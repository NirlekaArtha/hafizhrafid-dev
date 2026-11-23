---
trigger: model_decision
description: "Apply when working on locale routing, language detection, translations, hreflang, SEO meta tags, sitemap, article translation, or the content rendering pipeline."
---

# Internationalization and Content Rules

## Routing and detection
- Public URLs are prefixed with `/{locale}` (`id` or `en`). Segment names are identical in both
  languages (`/portfolio`, `/blog`). Slugs are per language.
- Locale resolution order: URL prefix, `locale` cookie, `Accept-Language` header, `CF-IPCountry`
  header (ID maps to `id`, otherwise `en`), default `en`.
- Redirect `/` to `/{locale}` for regular visitors only. Never force-redirect crawlers.
- Emit `hreflang` (and `x-default`) only for translation pairs that are both published.

## UI strings and translatable data
- UI strings live in `lang/id` and `lang/en`. No hard-coded user-facing text in Blade or PHP.
- Categories, projects, and profile data use translatable JSON columns.
- Articles use one row per language, linked by `translation_group_id`, with `UNIQUE (locale, slug)`.

## Article translation pipeline
- A translation is always created as a draft with `is_machine_translated = true`.
  It cannot be published until reviewed (`reviewed_at` set).
- Never translate the content of `<pre>`, `<code>`, URLs, or identifiers. Protect them with
  placeholders and restore them afterwards. Cover this with unit tests.
- The translation provider sits behind `TranslatorInterface`.
- Show the label "translated with AI assistance, reviewed by the author" while
  `is_machine_translated` is true.
- If the language counterpart does not exist, the language switcher links to the blog index of
  that language, never to a 404.

## Content rendering pipeline
- On save: sanitize HTML, then process it (heading anchors, table of contents, syntax highlighting,
  image attributes, `rel="noopener"` on external links) and store the result in `body_rendered`
  and `toc`. Do not do this work on every request.
- Headings in articles are H2 to H4. The page template owns the single `<h1>`.
- Keep a revision (max 20) on every save.
