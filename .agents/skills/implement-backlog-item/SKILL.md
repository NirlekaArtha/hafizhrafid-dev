---
name: implement-backlog-item
description: "Implement one task from docs/04-roadmap-backlog.md (IDs like P0-06, F1-14, F2-03, C-01) using the project's plan-first workflow. Use when the user gives a backlog ID or asks to build, add, or change a feature of this website."
---

# Implement a backlog item

Follow these steps in order. Do not skip the approval step.

## 1. Gather context
- Read the backlog row for the given ID and any items it depends on.
- Read the referenced sections in `docs/01`, `docs/02`, and `docs/03`.
- Read `docs/06-progress-log.md` for current state and earlier decisions.
- Inspect the existing code that will be touched. Check installed package versions in
  `composer.json` and `package.json`.

## 2. Post a plan and stop
Reply in Indonesian using this format, then wait for approval:

```
Tugas: <ID> <title>
Tujuan & kriteria selesai: ...
Pendekatan: ... (2-5 sentences)
File yang akan dibuat/diubah: ...
Alternatif: ... (trade-off singkat)
Risiko / hal yang belum jelas: ...
Pertanyaan: ... (hanya jika perlu)
Tes yang akan ditulis: ...
```

Do not write implementation code before the user approves. Trivial changes (one file, no behavior
change) may skip this step, but say so.

## 3. Implement
- Write the failing test first for core logic, then the smallest implementation that passes.
- Follow `AGENTS.md` and the rules in `.agents/rules/`.
- Keep the diff focused on this task. Note unrelated problems in the summary instead of fixing them.
- Ask before adding any dependency.

## 4. Verify
Run `composer lint`, `composer analyse`, `composer test`, and `npm run build` when frontend
files changed. Fix failures caused by your change. Report the real outcome of each command.
If a command could not run, say why.

## 5. Report and record
Reply in Indonesian:

```
Ringkasan: apa yang berubah dan kenapa
Verifikasi: hasil lint / analyse / test / build
Keputusan penting: ...
Hal yang perlu ditinjau manual: ...
Tindak lanjut: ...
```

Then append an entry to `docs/06-progress-log.md` and, if a decision changed, propose the update
to `docs/02-technical-design.md`. Suggest a Conventional Commit message. Do not commit or push
unless asked.
