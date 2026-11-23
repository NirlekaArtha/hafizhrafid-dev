---
trigger: model_decision
description: "Apply when handling user input, HTML content, file uploads, authentication, cookies, rate limiting, visitor identity, view/like counters, contact forms, logging, or secrets."
---

# Security and Privacy Rules

## Content and HTML
- HTML from CKEditor is sanitized on save with an explicit allow-list (HTMLPurifier). Store the
  sanitized result in `body_html`.
- Never print unsanitized HTML with `{!! !!}`. The only allowed raw output is the pre-processed
  `body_rendered` of an article.
- Every sanitizer change needs a test with XSS payloads.

## Input, uploads, and forms
- Validate everything server-side. Limit upload MIME types and sizes. Regenerate file names.
- Public endpoints (contact, like, view, login, translation trigger) must have rate limiting.
- The contact form uses a honeypot plus rate limiting. Add Cloudflare Turnstile only if abuse appears.
- Mass assignment: explicit `$fillable` only.

## Visitor identity (no fingerprinting)
- Likes: first-party cookie `vid` (random UUID, `HttpOnly`, `Secure`, `SameSite=Lax`, 1 year).
  Likes are idempotent per `(article_id, visitor_uuid)`.
- Views: `visitor_hash = HMAC_SHA256(IP | User-Agent | article_id, daily_salt)` with
  `daily_salt = HMAC_SHA256(UTC date, APP_KEY)`. Count at most one view per visitor per article
  per day. Filter bots.
- Never store raw IP addresses. Store `ip_hash` only where needed (for example contact messages).
- Never add fingerprinting libraries or third-party trackers.

## Authentication and authorization
- Social login only (Google, GitHub). No passwords.
- Filament panel access is limited to the `admin` role. Admin routes are `noindex` and throttled.
- Check authorization in Policies, not in views.

## Secrets and logging
- Secrets live in `.env` or CI secrets. Never commit them. Keep `.env.example` free of real values.
- Send only article text to the translation provider. Never send secrets, user data, or
  environment details.
- Do not log tokens, cookies, emails, or full request bodies.

## HTTP hardening
- Keep the Content Security Policy strict. Allow only sources the site actually uses.
- Set security headers: `X-Content-Type-Options`, `Referrer-Policy`, `Permissions-Policy`, HSTS.
