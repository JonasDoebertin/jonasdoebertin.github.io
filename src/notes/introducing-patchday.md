---
title: "I rebuilt my monthly patch day around one question: is this update safe?"
description: "I got tired of reading changelogs across every service I self-host. So I built Patchday to tell me what changed, what's risky, and how far behind I am."
date: 2026-06-05
---

<p class="lead">I self-host more than I probably should. Over the years, the little stack of services running on my hardware grew from a fun weekend project into something I rely on. Right now that's Immich, Borg UI, Pi-hole, Paperless-ngx, Mealie, Dockhand, and NoteDiscovery, all tied together over Tailscale and running on an Unraid-driven UGREEN NAS. Somewhere along the way, keeping that stack healthy turned into a second hobby I never signed up for.</p>

The hard part was never the setup. It was the updates.

Every service moves at its own pace. One ships a quiet bugfix on Tuesday, another drops a major version that silently changes a config format, a third sits untouched for three months and then releases two breaking versions back to back. Multiply that across the whole stack and "staying current" stops being a task you finish. It turns into background noise you learn to tune out, right up until the moment you can't.

I have a routine for this. The first Friday of every month is my patch day: the evening I sit down and bring everything up to date. And every patch day put me in the same spot. Either I update everything blindly and hope nothing important changed, or I read through every project's changelog one by one to find out. The first option is fast and reckless. The second is careful, miserable, and still error prone, because by the fourth changelog your eyes glaze over and you skim right past the paragraph that matters.

That's not a hypothetical. I once skimmed past a breaking change in Immich, updated anyway, and got to spend the evening restoring its library from an offsite backup. Everything came back fine. But it was the kind of evening that leaves you sitting there afterward thinking that a single line of warning would have saved the whole mess.

I did that dance for a few months before deciding it was a problem worth actually solving.

## What Patchday does

Patchday watches the software you run and tells you, in plain language, what's actually worth knowing.

You add the projects you care about. Patchday keeps an eye on their releases, and when something new ships, it does the reading for you. Instead of a wall of changelogs, you get a short briefing: what changed, what might break, the rough migration steps, and how risky the jump looks, flagged from low all the way up to critical. You can also tell Patchday which version you're currently running, so you can see at a glance how far behind you've drifted on each project.

The goal is simple. I want to glance at one place on patch day and know whether tonight is a two-minute round of updates or a "block out the evening" round, without reading a single changelog myself unless I want to.

If you self-host, you already feel the gap this fills. If you don't, picture having to manually check thirty different websites every week to find out whether any of the apps on your phone had an important update, then reading the fine print on each one. That's roughly the job Patchday takes off your plate.

## Why I built it

I looked around first. There are pieces out there: update notifiers, dependency trackers, dashboards. But most of them answer "is there an update?" The question I actually have on patch day is "should I take this update, and what am I in for if I do?" That's a different problem, and it's the one I wanted to solve well.

The Immich evening was the tipping point. After that, "I should really keep better track of this" became "nobody's built the tool I want, so I'll build it." The name pretty much wrote itself.

## Why I'm building it in the open

Here's the part I want to be upfront about: Patchday isn't finished.

I could have built it quietly for another six months and then unveiled a polished thing. I decided not to. Patchday is open right now. You can sign up and start using it today, for free, while it's still being built. There's a limit on how many projects you can track on the free tier for now, but it's enough to put it to real work on the stack you actually run.

I'm doing this for a selfish reason and an honest one.

The selfish reason: I build better when real people are using the thing. A tool that watches your services, with your messy real-world mix of well-documented and barely-documented projects, turns up problems I'd never find on my own NAS. Early users make the product sharper than any roadmap I could write alone.

The honest reason is simpler. I'd rather you watch it grow than be sold a finished story. You'll hit rough edges. Some briefings will be better than others. A feature you want might not exist yet. When that happens, I want to hear about it. Every piece of feedback goes straight into deciding what gets built next, and it will keep doing that until Patchday is the tool I set out to make. Building in the open, to me, just means not pretending otherwise.

## Try it

If any of this sounds familiar, if a changelog has ever cost you an evening you wanted back, I'd love for you to try Patchday and tell me what you think.

You can start at [patchday.dev](https://patchday.dev). Add a few of the projects you run, see what the briefings look like for software you actually know, and then tell me where it falls short. The honest, unfiltered "this annoyed me" notes are the most useful thing you can send me right now.

I'll be sharing more here as it takes shape: the decisions, the things I get wrong, and where it's headed.
