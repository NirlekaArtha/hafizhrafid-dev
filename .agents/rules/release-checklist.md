---
trigger: manual
description: "Release checklist. Mention explicitly with @ before tagging a release."
---

# Release Checklist

Before tagging a release (`v0.1.0`, `v0.2.0`, ...):

- [ ] CI is green on `main` (lint, static analysis, tests, build).
- [ ] Lighthouse CI thresholds pass (mobile at least 95) on home, blog index, one article.
- [ ] Migrations reviewed: no destructive change without an expand-and-contract plan.
- [ ] `.env.example` matches the variables the code reads. No secrets in the repo history.
- [ ] `composer audit` and `npm audit` reviewed.
- [ ] Sitemap, `robots.txt`, hreflang, canonical verified on the staging or production URL.
- [ ] Privacy page matches the cookies and data actually used.
- [ ] Backup ran and a restore was tested recently.
- [ ] README, docs, and `docs/06-progress-log.md` updated. Changelog or release notes written.
- [ ] Deploy executed, queue worker and scheduler restarted, smoke test done.
