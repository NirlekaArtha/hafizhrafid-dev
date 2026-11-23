---
trigger: glob
globs: "tests/**/*.php"
description: "Pest testing conventions and priorities for this project."
---

# Testing Rules

- Use Pest. Test names describe behavior, for example `it('counts a view only once per visitor per day')`.
- Prefer feature tests that go through HTTP/Livewire. Use unit tests for pure logic
  (`HtmlProcessor`, view hash, placeholder protection).
- Never call real external services. Use `Http::fake()`, `Queue::fake()`, and fake providers.
- Control time with `Carbon::setTestNow()`. Use factories, not hand-built rows.
- Write the failing test first when fixing a bug, and make sure it fails for the right reason.
- Never weaken or delete a test to make the suite pass. If a test is wrong, explain why.

## Priority coverage (from docs/02-technical-design.md section 12)
1. HTML sanitization with XSS payloads.
2. Locale resolution order and hreflang output.
3. Like idempotency and per-day view counting, including bot filtering.
4. Article publish/schedule rules and the "translation must be reviewed" rule.
5. Translation placeholder protection for `<pre>` and `<code>`.
6. Admin access control.
