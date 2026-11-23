---
trigger: glob
globs: "resources/**/*.php, resources/**/*.css, resources/**/*.js"
description: "Frontend, accessibility, and performance rules for Blade, Tailwind, Alpine, and Livewire views."
---

# Frontend Rules

- Pages are server-rendered. Do not add JavaScript libraries beyond Alpine and Livewire without asking.
- CKEditor code lives only in the separate `admin.js` bundle. Never import it in `app.js`.
- Use the design tokens from `docs/03-sitemap-wireframe-design.md` as CSS variables. Support light
  and dark themes. No hard-coded colors in components.
- Use semantic HTML: one `<h1>` per page, ordered headings, landmarks, a "skip to content" link.
- Images: always `alt`, `width`, `height`, `loading="lazy"` (except the LCP image), WebP via the
  media library. No inline `style` attributes.
- Fonts are self-hosted with `font-display: swap`.
- Interactive elements need visible focus states and a minimum 44px touch target.
  Icon-only buttons need `aria-label`.
- Respect `prefers-reduced-motion`. No heavy animations.
- Extract a Blade component when a markup pattern appears three or more times.
- Performance target: Lighthouse mobile at least 95 on home, blog index, and article pages.
  Do not add anything that would break this without raising it first.
