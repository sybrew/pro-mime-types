---
name: minify
description: >-
  Regenerates lib/admin.min.js and lib/admin.min.css via babel-tsf and
  cleancss-tsf. Use after editing unminified lib/admin.js or lib/admin.css, or
  when the user asks to minify. Do not hand-edit min files.
---
# Minify lib JS and CSS

Edit unminified sources only. Never write `*.min.js` or `*.min.css` by hand. Never search this repo for uglify, terser, or babel-minify. Do not create, edit, read, grep, search, or `git diff` `*.min.js` or `*.min.css`, including after minify exit code 0.

Sources in this plugin are `lib/admin.js` and `lib/admin.css`. The script points both engines at `lib/`. Each engine minifies its own type and skips files that already end in `.min.js` or `.min.css`.

## Permission

Read `.local/minify/permission.txt` with an explicit path before cloning engines. Grep/Glob cannot see `.local/`.

Never invent `True` or `False`. Never write this file unless the user has replied with both flags.

Format is always two lines (`True` or `False` per line, never guessed):

```
JS=True|False
CSS=True|False
```

- If the file does not exist, prompt. If you cannot prompt and no permissions exist, skip minification altogether and ask at the end of the chat.
- After the user replies with both flags, create the file from that reply. Always write both lines.
- If a flag is `False`, do not prompt again, and do not clone or minify that type.
- If a flag is `True`, proceed. After a permitted import, keep that flag `True`.

The minify script reads this file. A missing file is a hard stop. A `False` flag skips that type even if the engine is already cloned.

## Engines

Public source of truth:

- JS: https://github.com/theseoframework/babel-tsf → `.local/minify/babel-tsf`
- CSS: https://github.com/theseoframework/cleancss-tsf → `.local/minify/cleancss-tsf`

If permission is `True` and `run.js` is missing, clone that repo and run `npm install` in it. Do not copy engines from elsewhere; clone the public repos above.

Override paths with `BABEL_TSF_DIR` and `CLEANCSS_TSF_DIR` only when the user sets them.

## Command

From the repo root:

```powershell
node .claude/skills/minify/scripts/minify.js --js
node .claude/skills/minify/scripts/minify.js --css
node .claude/skills/minify/scripts/minify.js
```

`--js` and `--css` limit the run. No flags minifies both.

If the script exits non-zero, fix the failure. Do not hand-write min files as a fallback.
