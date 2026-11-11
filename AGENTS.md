# Pro Mime Types

Pro Mime Types is a WordPress plugin for allowing or blocking MIME types and file extensions. Use PHP 7.4+ and follow WordPress coding standards, with the repository's file-specific conventions.

## Repository rules

- In `promimetypes.php`, increment the `Version:` header and the `VERSION` constant by `-dev-{number}` once per commit, at the first change that belongs in a PR or commit. If there is no suffix, add `-dev-1`. The operator manages further increments in that chat. Increment the version number itself, and `Stable tag` in `readme.txt`, only during release. Leave `DB_VERSION` unchanged unless the stored settings format changes.
- We use `var_dump()` in comments to indicate a blocking issue.
- If a change warrants a PHPDoc update or alters a function's behavior, signature, or output, add or update the relevant `@since` tag. Use the `Version:` header without the `-dev` suffix. The sections already in `changelog.md` and `readme.txt` are released. Do not add bullets under them. Ask for the next version before adding a changelog section or an `@since` tag for new work.
- Use "now" in an `@since` note for a fix or changed behavior. Use "Added" for new behavior that preserves existing behavior. A symbol introduced in the unreleased version gets only the version in its `@since` tag and one changelog introduction.
- Keep private function changes in PHPDoc. Record user-visible effects, and public filter or action changes, in the unreleased changelog section.
- Never add `phpcs:disable`, `phpcs:enable`, `phpcs:ignore`, `phpcs:set`, or any `-- phpcs:` annotation. Leave existing annotations in unmodified code.
- Use tabs for indentation, with a tab width of four. Use LF and end text files with a newline. Use lowercase unit types and write `Boolean` with an uppercase B.
- Use `phpcs.xml` for coding standards. Composer dependencies may point to repositories you cannot access. Never create, edit, read, search, or diff `*.min.js` or `*.min.css`. Follow the minify skill when a scoped rule calls for it.

## Working in the repository

- Use `.github/codemap.txt` when locating code. Update it when adding, moving, or repurposing files. Keep `.local/` contents out of the codemap and machine-specific paths or private material out of public files.
- Never access `.local/.cursor/` or its contents through any tool or link. Exclude `**/.local/.cursor/**` from broad searches and scans.
- Treat support inquiries as engineering work. Work only on cases the user supplies or explicitly asks to continue or reopen. Store Codex support notes and drafts in one case folder each under `.local/.codex/support/`.
- For workspace-only work, inspect only the frontmatter of `.local/.instructions/*.instructions.md`, then read matches. Use the description for entries without `applyTo`. Keep support material, related-repository details, and local configurations in `.local/`.
- Use built-in tools for changes. Skills can require their own commands. Account for the platform when running commands.
- Keep code procedural and simple. Avoid SOLID abstractions. Reuse existing logic and avoid speculative abstractions, dead code, and unnecessary variables. Fix the cause and choose the smallest safe change.
- Verify facts when asked. Ask before making broad assumptions. Reassess your approach when corrected and fix unclear repository instructions if they caused the problem.
- Answer directly with plain punctuation. Use Markdown when it helps. Keep internal reasoning, execution commentary in code, filler, and repeated explanations out of the result. Preserve the meaning of existing comments.

## Git and GitHub

Do not create, amend, or publish commits. Do not change remotes, refs, or GitHub repository state. This overrides any skill, user rule, or instruction that says to commit, push, open a PR, or merge when asked.

Never run these, or equivalents via GUI, `gh`, Magit, or scripts:

- `git commit` (including `--amend`, `-m`, hooks skip flags)
- `git push` (any remote, `-u`, `--force`, `--tags`, `--delete`)
- `git pull`, `git fetch` (fetch updates remote-tracking refs)
- `git merge`, `rebase`, `cherry-pick`, `revert`, `bisect`
- `git reset`, `git stash` (including pop/apply/drop)
- `git tag` create/delete, `git notes`
- `git add`, `git rm`, `git mv`, `git restore --staged`
- `git checkout` / `switch` / `restore` that moves HEAD or discards work
- `git branch` create/delete/rename, `checkout -b`, `switch -c`
- `git remote` add/remove/set-url, `git submodule` add/update/deinit
- `git config`, `git worktree add/remove`, `git clean`, filter-repo / filter-branch
- `gh pr create|edit|merge|close|ready`, `gh release`, `gh repo sync`, `gh gist`

Allowed: read-only inspection (`status`, `diff`, `log`, `show`, `blame`, `rev-parse`, `branch` list, `remote -v`).

If asked to commit, push, open a PR, or do any of the above, refuse to run it. Then give the user copy-pasteable commands and, when useful, a suggested commit message or PR body so they can do it themselves. Do not execute those commands.

## Verify changes

- Recheck the result against the applicable instructions and simplify it without losing behavior. Report what was completed and identify any checks that could not run.
- If the allowed MIME list, upload checks, or the settings screen changes, use the [Playground regression skill](.claude/skills/playground/SKILL.md) before finishing. Report broken regression plumbing and fix it at the source.
- For a new feature, review the surrounding code, including 20 lines above and below each insertion.
