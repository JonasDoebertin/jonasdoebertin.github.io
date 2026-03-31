# Project: dieserjonas.dev

Personal website and blog built with Eleventy v3, Tailwind CSS v4, and Bun.

## Commands

- `bun run dev` starts the dev server at http://localhost:8080/ with live reload
- `bun run build` creates a production build in `_site/` (sets `NODE_ENV=production`)

## Architecture

Static site generator (Eleventy) with Nunjucks templates and Markdown content. No client-side JavaScript. CSS is processed at build time through PostCSS with `@tailwindcss/postcss`. Syntax highlighting is handled at build time by Shiki (one-dark-pro theme) via `@shikijs/markdown-it`.

### Key directories

- `src/` is the Eleventy input directory
- `src/_includes/layouts/` has three layouts: `base.njk` (HTML shell), `note.njk` (blog posts), `page.njk` (static pages)
- `src/_includes/partials/icons/` contains SVG icons as Nunjucks macros (use `{% from "partials/icons/foo.njk" import icon %}`)
- `src/notes/` blog posts, auto-assigned `layouts/note.njk` via `notes.json` directory data file
- `src/projects/` project descriptions, `permalink: false` (rendered inline on the homepage, never as standalone pages)
- `src/assets/css/main.css` is the single CSS entry point; partials prefixed with `_` are imported by PostCSS and excluded from Eleventy output
- `public/` contains static assets (favicons, images, manifest) copied to the site root
- `_data/site.js` holds global data (siteName, baseUrl, production flag)

### Collections

- **notes**: glob `src/notes/*.md`, sorted by date descending. Frontmatter: `title`, `date`
- **projects**: glob `src/projects/*.md`, sorted by `priority` ascending. Frontmatter: `title`, `status` (`current`|`legacy`), `priority` (integer), `links` (array of `{title, url}`)

### Filters

- `dateDisplay(date, format)` with formats: `"MMMM YYYY"`, `"YYYY-MM-DD"`, `"YYYY-MM"`

### Content conventions

- `markdownTemplateEngine: false` so Nunjucks expressions in Markdown are not evaluated (avoids conflicts with `{{ }}` in code examples)
- Notes use `<p class="lead">` for the intro paragraph (styled by Tailwind Typography's `.lead` class)
- Frontmatter `noindex: true` excludes a page from the sitemap and adds a `noindex` robots meta tag
- Static pages (imprint, disclaimer, privacy, colophon) set their layout explicitly in frontmatter

### CSS details

- Brand color: `oklch(0.56 0.2562 35.78)` defined as `--color-brand` in `@theme`
- `@source "../.."` in `main.css` ensures Tailwind scans all templates in `src/`
- Headings get a decorative horizontal line via `_headings.css` (opt out with class `no-decor`)
- External links, `mailto:`, and `tel:` links get a `\21A3` arrow suffix via `_links.css`
- Links are underlined by default, no underline on hover (WCAG 1.4.1 compliance)
- `.no-decoration` class suppresses both underline and arrow on links

### Accessibility

- Skip-to-content link targeting `#main-content`
- SVG icons use `aria-hidden="true"` (parent links carry the accessible label)
- Brand color passes WCAG AA contrast (4.9:1 against white)
- `text-zinc-500` for muted text (4.8:1 against white)
- Syntax highlighting CSS includes `forced-colors` media query fallback

## Deployment

GitHub Actions workflow (`.github/workflows/deploy.yml`) runs on push to `main`. Uses `oven-sh/setup-bun@v2`, builds with `NODE_ENV=production`, deploys `_site/` to GitHub Pages at dieserjonas.dev.
