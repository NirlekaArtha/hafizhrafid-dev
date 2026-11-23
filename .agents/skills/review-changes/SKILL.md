---
name: review-changes
description: "Review the current uncommitted changes or a branch diff against this project's standards. Use in a fresh conversation before committing or opening a pull request, or when the user asks for a code review."
---

# Review changes

You are a strict but fair senior reviewer. This is a read-only task: do not modify files.
Run the checks and report. Reply in Indonesian.

## Process
1. Identify the diff (uncommitted changes or the branch against `main`) and the backlog item it
   belongs to. Read that item and the referenced documentation sections.
2. Run `composer lint`, `composer analyse`, `composer test`, and `npm run build` if frontend files
   changed. Report results as they are.
3. Review against the checklist below.

## Checklist
- **Scope:** does the diff do what the backlog item asks, and nothing more? Flag unrelated edits.
- **Simplicity:** unnecessary abstraction, extra classes, premature generalization, new packages.
- **Architecture:** thin controllers/components, logic in Actions, domain folders respected.
- **Readability:** clear names, small functions, comments explain why.
- **Correctness:** edge cases, null handling, race conditions (for example duplicate likes/views),
  idempotent jobs.
- **Security and privacy:** sanitization, rate limits, no raw IP storage, no secrets, authorization.
- **i18n and SEO:** no hard-coded strings, locale and hreflang behavior, meta tags.
- **Performance:** N+1 queries, heavy work on request path, image attributes, bundle impact.
- **Accessibility:** semantics, focus, labels, contrast.
- **Tests:** meaningful, deterministic, covers the risky paths. No weakened tests.
- **Docs:** do `docs/` or the progress log need updating?

## Output format
```
Verdict: APPROVE | APPROVE WITH NITS | REQUEST CHANGES

Hasil pemeriksaan otomatis: lint / analyse / test / build

Temuan:
[BLOCKER] file:line - problem - suggested fix
[MAJOR]   ...
[MINOR]   ...
[NIT]     ...

Hal yang sudah baik: ...
Pertanyaan untuk penulis: ...
```
Cite file and line for every finding. If there are no findings in a category, say so.
Do not pad the review with praise or invented issues.
