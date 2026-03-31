---
title: "One Pi-hole for everything: ad-blocking at home and on the go with Tailscale"
date: 2026-03-29
---

<p class="lead">I wanted one Pi-hole instance that blocks ads on my phone when I'm out with the family and on my TV at home. Sounds simple enough, right? It took me way too long to figure this out, mostly because nobody seems to have covered this exact setup yet. So, here's my approach.</p>

The idea: run Pi-hole in Docker on my NAS, connect it to my Tailscale network so all my Tailnet devices can use it wherever they are, and also make it available on my local network for devices that can't run Tailscale (like my TV). One Pi-hole, two networks, zero (or: way less) ads.

## What you'll need

Before we get into it, here's what I'm assuming you already have set up:

- A machine that can run Docker (I'm using Unraid on a UGREEN NAS, but anything works)
- A way to manage containers ([Dockhand](https://dockhand.pro/) in my case, but you could use Portainer, SSH with docker compose or whatever you prefer)
- A Tailscale account with a tailnet already set up
- A Tailscale auth key (you can generate one in the admin console under _Settings > Keys_)

If you've got those things, you're good to follow along.

## The thing that didn't work: DockTail

My first attempt used [DockTail](https://docktail.org/), which is a tool that makes it really easy to expose Docker containers on your Tailscale network. You slap some labels on your container and DockTail handles the rest. For web UIs this works beautifully. I had Pi-hole's admin panel accessible on my tailnet within minutes.

Then I tried to actually use it as a DNS server. I set the Pi-hole's Tailnet IP as my nameserver in the Tailscale admin console and... nothing. DNS resolution just stopped working on all my Tailnet devices.

A quick test confirmed it:

```bash
dig @<pihole-tailnet-ip> google.com

;; connection timed out; no servers could be reached
```

The web UI worked fine over HTTPS. But DNS runs on port 53, and crucially, it uses UDP. DockTail only supports HTTP, HTTPS, and TCP protocols. No UDP. That's just a limitation of how _Tailscale Serve_ works under the hood.

So DockTail was out. I needed Pi-hole to have a real, full network presence on the tailnet, not just proxied HTTP.

## The sidecar approach

The solution is to run a Tailscale container as a "sidecar" next to Pi-hole. The Tailscale container joins your tailnet and gets its own IP address. Pi-hole then shares that container's network stack, which means Pi-hole is directly on the tailnet with all ports available, including UDP 53.

Here's the Tailscale part of the Docker Compose config:

```yaml
services:
  tailscale:
    container_name: pihole-tailscale
    image: tailscale/tailscale:latest
    restart: unless-stopped
    environment:
      TS_AUTHKEY: ${TS_AUTHKEY}
      TS_ACCEPT_DNS: false
      TS_STATE_DIR: /var/lib/tailscale
      TS_USERSPACE: false
      TS_HOSTNAME: pihole
    volumes:
      - /mnt/user/appdata/tailscale-pihole:/var/lib/tailscale
    cap_add:
      - NET_ADMIN
      - NET_RAW
    devices:
      - /dev/net/tun:/dev/net/tun
```

A few things worth explaining here:

`TS_ACCEPT_DNS: false` is the most important one. When you eventually tell Tailscale to use Pi-hole as the DNS server for your entire tailnet, every device on that tailnet will send DNS queries to Pi-hole. If the Tailscale sidecar *also* tries to use the tailnet's DNS (which is... itself), you get a circular dependency and everything breaks. Setting this to `false` makes the sidecar ignore the tailnet's DNS settings and use the host's DNS instead.

`TS_AUTHKEY` is pulled from a `.env` file. Don't hardcode your auth key in the compose file. I learned this the hard way when I accidentally pasted mine into a chat. Had to revoke it and generate a new one.

The volume mount stores Tailscale's state so the sidecar remembers its identity across restarts. I'm using Unraid's default `appdata` share here to avoid needing a dedicated share, but a named Docker volume or any local directory mount would work just as well. Just use whatever fits your host setup.

The `NET_ADMIN`, `NET_RAW` capabilities and the `/dev/net/tun` device are required for Tailscale to set up its network interface. Explaining what each of these does is a bit beyond the scope of this post. Just make sure they're there.

## Pi-hole configuration

Now for Pi-hole itself. The key line is `network_mode: service:tailscale`, which tells Docker to use the Tailscale container's network stack instead of creating its own. This single line is what makes Pi-hole reachable on the tailnet.

```yaml
  pihole:
    container_name: pihole
    image: pihole/pihole:latest
    restart: unless-stopped
    network_mode: service:tailscale
    environment:
      PIHOLE_UID: 99
      PIHOLE_GID: 100
      TZ: Europe/Berlin
      FTLCONF_webserver_api_password: ${ADMIN_PASSWORD}
      FTLCONF_dns_listeningMode: ALL
      FTLCONF_dns_upstreams: |-
        9.9.9.9
        149.112.112.112
        2620:fe::fe
        2620:fe::9
    volumes:
      - /mnt/user/pihole-data:/etc/pihole
```

`PIHOLE_UID` and `PIHOLE_GID` set the user and group IDs that Pi-hole runs as inside the container. I'm using `99` and `100` here because that's Unraid's `nobody` user, which is the standard for Docker containers on Unraid. If you're not on Unraid, you can either drop these entirely or set them to match a user on your host system.

`FTLCONF_dns_listeningMode: ALL` is easy to miss. By default, Pi-hole only listens on certain interfaces. Since the Tailscale network interface isn't one it would normally expect, you need to tell it to listen on everything. Without this, Pi-hole simply ignores DNS queries coming in from the tailnet.

For upstream DNS I'm using Quad9 (`9.9.9.9` and `149.112.112.112`). The important thing is that these are public DNS servers that Pi-hole can reach directly. If your upstream DNS somehow routed through Tailscale, and Tailscale is using Pi-hole for DNS, you'd be right back at that circular dependency problem. Public resolvers don't have this issue.

## Using Pi-hole on your tailnet

With both containers running, Pi-hole should show up as a machine called "pihole" in your Tailscale admin console. Now you need to tell your tailnet to actually use it for DNS.

Go to the Tailscale admin console, then DNS settings. Add your Pi-hole's Tailnet IP (you can find it on the machines page) as a global nameserver. Enable "Override local DNS" so devices actually use it instead of their local DNS. Keep MagicDNS enabled so `*.ts.net` names still work.

Test it:

```bash
dig @<your-pihole-tailnet-ip> google.com
```

You should get an answer back with actual IP addresses. If you do, it's working. Your phone on cellular data, your laptop at a coffee shop, any device on your Tailnet is now using your Pi-hole for DNS.

## What about the TV?

This is where it gets good. My TV can't run Tailscale. Neither can my smart speakers or most IoT devices. But they're all on my local network, and I want them to use Pi-hole too.

The trick is to expose Pi-hole's DNS port on the host machine's local network. Since Pi-hole shares the Tailscale container's network stack, we need to add port mappings to the Tailscale service. Add these lines to the `tailscale` service in your compose file:

```yaml
    ports:
      - 53:53/tcp
      - 53:53/udp
```

This maps port 53 (DNS) from inside the container to port 53 on your host machine. So if your server's local IP is `192.168.x.x`, any device on your network can now use it as a DNS server.

Test it:

```bash
dig @<your-host-lan-ip> google.com
```

If that works, the last step is making it automatic. In your router's settings, find the DHCP configuration and set your server's LAN IP as the DNS server. On a Fritzbox, this is under _Home Network > Network > Network Settings > IPv4 Configuration_. Every device that gets its IP from the router will now automatically use Pi-hole for DNS. No per-device configuration needed.

## Seeing hostnames instead of IPs

One more thing that bugged me: Pi-hole's dashboard was showing all connected clients as bare IP addresses. Not very helpful when you're trying to figure out which device is making all those queries.

Pi-hole can resolve those IPs to hostnames using conditional forwarding. You can configure this entirely through environment variables by adding `FTLCONF_dns_revServers` to the Pi-hole container:

```yaml
      FTLCONF_dns_revServers: |-
        true,192.168.106.0/24,192.168.106.1,fritz.box
        true,100.64.0.0/10,100.100.100.100
```

The first line tells Pi-hole to ask the Fritzbox (`192.168.106.1` in my case) for reverse DNS lookups on local network IPs. Your router knows which hostname belongs to which IP because it hands out the DHCP leases.

The second line is for Tailnet clients. Their IPs are in the `100.64.0.0/10` range, and Tailscale runs a MagicDNS resolver at `100.100.100.100` that knows all the device names on your Tailnet. This works because the Pi-hole container shares the Tailscale sidecar's network and can reach that address.

## A few more Pi-hole settings worth knowing about

The final config below includes a few extra environment variables I haven't mentioned yet.

`FTLCONF_dns_domainNeeded: true` tells Pi-hole to not forward queries for plain hostnames (like `laptop` instead of `laptop.local`) to the upstream DNS servers. These are local names that your upstream resolver can't answer anyway, so there's no point sending them out.

The three `specialDomains` settings handle some edge cases around encrypted DNS. Some browsers and devices try to bypass your DNS server by using DNS-over-HTTPS or private relay services. `mozillaCanary` tells Firefox that it shouldn't use its built-in DNS-over-HTTPS. `iCloudPrivateRelay` does the same for Apple's iCloud Private Relay. `designatedResolver` handles the more general Discovery of Designated Resolvers (DDR) protocol. With all three set to `true`, Pi-hole signals to these services that you're intentionally running your own DNS and they should respect that.

## The complete docker-compose.yml

Here's the full config for reference. Create a `.env` file next to it with your `TS_AUTHKEY` and `ADMIN_PASSWORD`.

```yaml
services:

  tailscale:
    container_name: pihole-tailscale
    image: tailscale/tailscale:latest
    restart: unless-stopped
    ports:
      - 53:53/tcp
      - 53:53/udp
    environment:
      TS_AUTHKEY: ${TS_AUTHKEY}
      TS_ACCEPT_DNS: false
      TS_STATE_DIR: /var/lib/tailscale
      TS_USERSPACE: false
      TS_HOSTNAME: pihole
    volumes:
      - /mnt/user/appdata/tailscale-pihole:/var/lib/tailscale
    cap_add:
      - NET_ADMIN
      - NET_RAW
    devices:
      - /dev/net/tun:/dev/net/tun

  pihole:
    container_name: pihole
    image: pihole/pihole:latest
    restart: unless-stopped
    network_mode: service:tailscale
    environment:
      PIHOLE_UID: 99
      PIHOLE_GID: 100
      TZ: Europe/Berlin
      FTLCONF_webserver_api_password: ${ADMIN_PASSWORD}
      FTLCONF_dns_listeningMode: ALL
      FTLCONF_dns_domainNeeded: true
      FTLCONF_dns_upstreams: |-
        9.9.9.9
        149.112.112.112
        2620:fe::fe
        2620:fe::9
      FTLCONF_dns_revServers: |-
        true,192.168.106.0/24,192.168.106.1,fritz.box
        true,100.64.0.0/10,100.100.100.100
      FTLCONF_dns_specialDomains_mozillaCanary: true
      FTLCONF_dns_specialDomains_iCloudPrivateRelay: true
      FTLCONF_dns_specialDomains_designatedResolver: true
    volumes:
      - /mnt/user/pihole-data:/etc/pihole
```

And that's it. One Pi-hole, accessible from anywhere on your tailnet and from every device on your local network. If you want to take it further, look into Pi-hole's group management for per-device blocklists, or add some community blocklists beyond the defaults.
