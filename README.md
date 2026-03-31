# dieserjonas.dev

Personal website and blog of [Jonas Doebertin](https://dieserjonas.dev/), a full-stack web developer based in Hamburg, Germany.

## What's on the site

The homepage serves as a hub for my work: a short intro, links to my latest notes (blog posts), and a showcase of current and past projects. There are also a few static pages for legal stuff and a colophon.

Notes cover a range of topics, from web development and self-hosting to 3D printing for model trains.

## Tech stack

The site is built with [Eleventy](https://www.11ty.dev/) (v3), a static site generator that turns Nunjucks templates and Markdown content into plain HTML. [Tailwind CSS](https://tailwindcss.com/) (v4) handles all the styling, processed at build time through PostCSS. Code blocks in blog posts get syntax highlighting at build time via [Shiki](https://shiki.style/), so there's no client-side JavaScript involved.

[Bun](https://bun.sh/) is used as the JavaScript runtime and package manager.

## Project structure

```
src/
  _data/           Site-wide data (name, description, URLs)
  _includes/
    layouts/       Page layouts (base, note, page)
    partials/      Reusable components (header, footer, icons)
  assets/css/      Tailwind CSS entry point and partials
  notes/           Blog posts (Markdown)
  projects/        Project descriptions (Markdown, rendered inline on homepage)
  index.njk        Homepage template
  sitemap.njk      Auto-generated sitemap
  *.md             Static pages (imprint, disclaimer, privacy, colophon)
public/            Static assets copied to the site root (favicons, images, manifest)
eleventy.config.js Eleventy configuration (collections, filters, CSS pipeline, Shiki)
```

## Local development

Make sure you have [Bun](https://bun.sh/) installed, then:

```bash
bun install
bun run dev
```

The site will be available at `http://localhost:8080/` with live reloading.

## Production build

```bash
bun run build
```

The output goes to `_site/`. This is what gets deployed.

## Deployment

Pushing to `main` triggers a [GitHub Actions workflow](.github/workflows/deploy.yml) that builds the site with Bun and deploys it to GitHub Pages at [dieserjonas.dev](https://dieserjonas.dev/).

## Adding a new note

Create a Markdown file in `src/notes/` with this frontmatter:

```yaml
---
title: Your note title
date: 2026-01-15
---
```

The note layout and URL (`/notes/your-file-name/`) are handled automatically. It will appear on the homepage sorted by date.

## License

Content is copyrighted. The site's source code is available for reference and inspiration.
