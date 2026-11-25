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
- If the item changes public UI (any Blade view, Livewire view, CSS, or Tailwind change that
  visitors see; for example F1-01, F1-03, F1-05, F1-22, F1-34, F1-40 to F1-44, F1-46, F1-47,
  F2-04, F2-05, F2-08, F2-09, F2-10, F2-13, F3-01, F3-03, F3-04, F3-06 to F3-09), read
  `.agents/skills/antislop/SKILL.md` (the core filter) and `.agents/skills/antislop-ui/SKILL.md`
  before planning, and resolve the antislop mode as the core describes. Admin-only Filament work is
  excluded.
  If the item adds or edits user-facing text, also read `.agents/skills/antislop-copywriting/SKILL.md`.
- The antislop skills refer to `DESIGN.md` (and `antislop.md` for the core). In this repository,
  treat `docs/03-sitemap-wireframe-design.md` as `DESIGN.md`: its design tokens, palette, and wireframes
  are the source of truth for visual decisions.

## 2. Post a plan and stop
Reply in Indonesian using this format, then wait for approval:

```
Tugas: <ID> <title>
Tujuan & kriteria selesai: ...
Pendekatan: ... (2-5 sentences)
File yang akan dibuat/diubah: ...
Alternatif: ... (trade-off singkat)
Keputusan visual: ... (warna, radius, layout, motion beserta alasan; isi hanya untuk tugas UI)
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
For UI items, also run the "UI Skill Checklist" at the end of `antislop-ui` and the Delivery Gate in
the core `antislop`, and report the result
under "Hal yang perlu ditinjau manual".

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
