---
name: playground
description: >-
  Launches a local WordPress Playground site with Pro Mime Types mounted,
  captures the allowed MIME dump, and compares before/after plugin states.
  Use when testing regressions, reproducing bugs, changing the allow-list,
  upload checks, or the settings screen, or before calling that work done.
---
# Playground regression

Do not write PHPUnit. Do not use Local WP, Docker, or MySQL. The engine is wp-plugin-regression.

If launch, capture, harness, permalinks, or compare looks wrong, decide whether the cause is the plugin or the regression plumbing (Playground, engine, mounts, cookies, rewrites, mu-plugins, the harness). When the plumbing is at fault, say so in the chat. Fix or report that system. Do not work around it in plugin code. Never change Pro Mime Types so it fits a broken harness.

When **not** to boot: wrap, trailing-comma, minify-only, docs-only, copyright-only, makepot-only.

When to run:

- The allowed MIME list, upload checks, or the settings screen changes
- Support reproduction
- Before calling that work done

This plugin does not print a front-end head, sitemap, robots file, or schema block. Do not compare those, and do not treat a core head diff as a plugin failure.

PHP edits on the working-tree mount apply on the next request. Launch takes the next free port in `9001`–`9099`. `launch --pair` takes the next two consecutive ports (wordpress.org `--site=before`, working tree `--site=after`). Read the URL from launch output or `~/.wordpress-playground/tests/runs.json`. Do not assume port `9400` or that pair is always `9001`/`9002`. Restart a side after `--wp` / `--php` changes. Do not reuse persist across majors or `latest` vs `trunk`.

Captures are logged-out. Launch does not pass `--login`.

Do not `flush_rewrite_rules()` in a blueprint `runPHP` step. That writes incomplete rules and post permalinks fall through to the homepage. Set `permalink_structure` and `delete_option( 'rewrite_rules' )` so Playground can flush on a later `init`.

Playground 301s `/sitemap.xml` via a VFS mu-plugin (`sitemap-redirect.php`). The engine overwrites that file with a no-op until https://github.com/WordPress/wordpress-playground/issues/4325 is patched. Drop the overwrite when that issue lands. If every request 500s with a parse error in that file, the no-op was invalid PHP. Fix `lib/launch.js` in wp-plugin-regression; do not change the plugin.

Do not reuse a live Playground another chat started. `runs.json` may list a live pid that still belongs to that chat. Launch a fresh wipe on the next free port. `--keep` only for persist this chat created.

## Permission

Read `.local/playground/permission.txt` with an explicit path before cloning the engine. Grep/Glob cannot see `.local/`.

Never invent `True` or `False`. Never write this file unless the user has replied with the flag.

Format is always one line (`True` or `False`, never guessed):

```
PLAYGROUND=True|False
```

- If the file does not exist, prompt. If you cannot prompt and no permissions exist, skip Playground altogether and ask at the end of the chat.
- After the user replies with the flag, create the file from that reply.
- If the flag is `False`, do not prompt again, and do not clone or launch.
- If the flag is `True`, proceed. After a permitted import, keep that flag `True`.

The playground script reads this file. A missing file is a hard stop. A `False` flag skips even if the engine is already cloned.

## Engine

Public source of truth:

- https://github.com/theseoframework/wp-plugin-regression → `.local/playground/wp-plugin-regression`

If permission is `True` and `run.js` is missing, clone that repo and run `npm install` in it. Node 20.18 or newer. Do not copy the engine from elsewhere; clone the public repo above.

Override the path with `WP_PLUGIN_REGRESSION_DIR` only when the user supplies an existing engine location. Keep workstation-specific values in the local environment or ignored configuration, never in this public skill.

## Command

From the repo root:

```
node .claude/skills/playground/scripts/playground.js launch
node .claude/skills/playground/scripts/playground.js launch --pair
node .claude/skills/playground/scripts/playground.js stop
node .claude/skills/playground/scripts/playground.js stop --port=9001
node .claude/skills/playground/scripts/playground.js stop --all
node .claude/skills/playground/scripts/playground.js capture --label before
node .claude/skills/playground/scripts/playground.js capture --label after --feature=mimes
node .claude/skills/playground/scripts/playground.js compare --before before --after after --feature=mimes
node .claude/skills/playground/scripts/playground.js surfaces
node .claude/skills/playground/scripts/playground.js harness --action ping
node .claude/skills/playground/scripts/playground.js harness --json-file .local/playground/payload.json
```

Optional launch flags: `--wp=latest`, `--php=8.3`, `--site=default`, `--plugin=working|wporg`, `--port`, `--port-before`, `--port-after`, `--pair`, `--keep`, `--workers=<n|auto>`, `--extra-plugin-zip=<path>`. Launch picks the next free port in `9001`–`9099`. `--pair` picks the next two consecutive ports (`before` / `after`). Pin with `--port` or `--port-before` / `--port-after`. `--keep` reuses persist instead of wiping. `--workers` defaults to `max(6, os.cpus().length - 1)`. `--extra-plugin-zip` is repeatable. Extracts land in `~/.wordpress-playground/extra-plugins/`, never under this repo.

## WordPress and PHP versions

`--wp` is a Playground **build slug**, not a path to Core. Do not reuse persist across majors or `latest` vs `trunk`.

Slugs the CLI accepts: `latest` (default gold), `beta`, `trunk` (`nightly` is the same), a hosted major/minor (`7.0`, `6.9`, `6.9.1`), a beta/RC (`6.8-RC1`), or a zip URL. PHP is `--php` (`7.4`–`8.5`, default `8.3`). This plugin requires PHP 7.4.

Do not write sites, extra-plugin extracts, or plugin zips under `.local/playground/` (that tree is inside the synced repo). Persist is `~/.wordpress-playground/tests/pro-mime-types/<version>/<site>/`. Off-site plugin zips extract to `~/.wordpress-playground/extra-plugins/`. Launch wipes the site folder unless `--keep`. Captures stay in `.local/playground/captures/`.

This consumer is a single-plugin repo. `plugin.json` omits `dir`, `activate`, `extraPlugins`, and `extraMounts`. Do not add them here. Mount another plugin with `launch --extra-plugin-zip`.

`--wp=trunk` is the prebuilt WordPress/WordPress nightly. It is not `wordpress-develop`. Do not mount `wordpress-develop/src` (or its `build/`) as `/wordpress`.

Use `--wp` / `--php` only when the change can be version-sensitive. Not a full matrix on every edit.

## A/B previous vs current

The working tree is mounted live. PHP edits apply on the next request. There is no previous plugin left on disk after you edit. This repository has no baseline capture. Do not invent one.

1. **Live side-by-side (preferred).** `launch --pair`. wordpress.org is `--site=before`, working tree is `--site=after`, on the next two free consecutive ports. `capture --label before` and `capture --label after` select those sites. `compare` diffs the JSON bundles and does not need a live server. Open the URLs from launch output or `runs.json`. `stop` kills this workspace's runs and leaves other consumers alone. `stop --port=<port>` kills one. `stop --all` stops every run in `runs.json`. Use `--all` only when the user asks to purge every workspace. Frame requests use the port of the run `--label` already selected. An older engine throws `Multiple Playground runs` while two sites are up; pass `--port` from the launch line with that engine.
2. **This session, one server, not yet edited.** `launch`, `capture --label before`, edit, `capture --label after`, `compare`. With one live site, a label that is not a site name still captures that site, and the bundle file uses the label.

Same `--feature` (or `--types`) on both captures. `surfaces` first. Do not git checkout, stash, or mount another Core tree to fake previous. Unminified `lib/admin.js` or `lib/admin.css` edits need the minify skill before a Playwright check of the settings screen. The MIME dump does not include those files.

The allow-list A/B is HTTP `capture` / `compare`. The settings screen is Playwright MCP against the live Playground URL after `launch`. The engine does not drive a browser. Cursor’s built-in Browser Automation conflicts with Playwright MCP; use the MCP. Default captures stay logged-out.

Playwright MCP must load `.cursor/skills/playground/playwright.mcp.json` through `--config` in the MCP client's user configuration. Keep any client-required absolute path in that private configuration. The repository config strips Chrome's `--disable-blink-features=AutomationControlled` flag and sends `X-PMT-Playground-Admin: 1`. For the settings screen, open `http://127.0.0.1:<port>/wp-admin/options-general.php?page=pro-mime-types`. The Allowed Types tab adds `&tab=allowed-types`. Do not fill `admin` / `password`. `pmt-playwright-admin.php` calls `wp_set_auth_cookie()` for the `admin` user when that header is present. That is the login. Do not add `.cursor/permissions.json`. Do not pass Playground `--login` or a blueprint `login` step. Front-end requests stay logged-out. Restart the Playwright MCP server after changing the user configuration or repository config.

Mutate the live site by writing mu-plugins under `~/.wordpress-playground/tests/pro-mime-types/<version>/<site>/mu-plugins/`, or `harness --json-file <path>`. Do not pass `--json "{...}"` from PowerShell; it strips the quotes. Do not add `eval`. The settings option is the string `pro_mime_types_settings`. The harness `option` action writes it with `update_option`.

## What capture stores

`plugin.json` has one feature, `mimes`, and one entry, `/?pmt-regression=mimes`. `pmt-regression.php` is mounted on both the wordpress.org site and the working tree. It accepts the `blog` frame, then prints plain text:

```
version: 2.2.0
network: 0
db: 2100
settings: avif,gif,jpg
allowed: gif image/gif
allowed: jpg|jpeg|jpe|jif|jfif image/jpeg
```

Those values come from the mounted plugin. A missing function prints an empty field instead of a PHP fatal. `allowed:` lines are sorted by extension regex.

`capture --feature=mimes` compares `settings:`, `db:`, and `allowed:` lines, plus status. The version and network lines show on a full compare (no `--feature`). A version-only diff during `-dev` work is expected.

One capture writes `.local/playground/captures/<label>.json`. Do not hand-edit it. `compare --feature` needs that entry in both bundles.

On allow-list work:

1. `surfaces` — the `mimes` feature and its entry.
2. `capture --label after --feature=mimes` and `compare --before before --after after --feature=mimes`.

`--paths` still appends extra URLs. A full compare diffs the whole dump.

`stop` waits for the site SQLite file to unlock. Default launch wipes persist (`--keep` to reuse). If SQLite stays locked, retry stop, then launch.

If the script exits non-zero, fix the failure.
