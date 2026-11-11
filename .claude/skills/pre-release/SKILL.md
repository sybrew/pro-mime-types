---
name: pre-release
description: >-
  Runs the Pro Mime Types pre-release checklist. Sets the release version,
  Stable tag, and changelog headings, captures the MIME dump, and flicks the
  Allow/Disallow selects the changelog names. Use when preparing a release,
  shipping a WordPress.org update, or when the user asks for the pre-release
  checklist.
---

# Pre-release

Run when cutting a release, or when the user asks for this checklist. Do not strip `-dev` or move `Stable tag` during ordinary development.

Follow the [Playground](../playground/SKILL.md), [copyright](../copyright/SKILL.md), [minify](../minify/SKILL.md), and [Makepot](../makepot/SKILL.md) skills for their respective checks. Do not tag, commit, or push.

## Version headers

The release version is the `Version:` header in `promimetypes.php`. `const VERSION` matches that header.

On the release commit, set these to that version, with no `-dev-{number}` suffix:

- Plugin header `Version:`
- `const VERSION`
- `readme.txt` `Stable tag:`
- The release section heading in `changelog.md`, which is `= {version} =`
- The same heading in the current changelog section of `readme.txt`

`Stable tag:` is the version WordPress.org offers for download. It is a version number, never `trunk`. Leaving it on the previous release means the new version is not offered.

Until that commit, `Version:` and `const VERSION` keep their `-dev-{number}` suffix and `Stable tag:` stays on the previous release. Do not add bullets under a heading that is already released. The new release gets its own `= {version} =` section.

`Requires at least`, `Tested up to`, and `Requires PHP` in the plugin header match the same lines in `readme.txt`. Bump `Tested up to` only when this checklist's Playground run used that WordPress version.

If `inc/upgrade.php` gained a migration for a new stored settings format, `DB_VERSION` in `promimetypes.php` is that new integer. Otherwise leave `DB_VERSION`.

This plugin has no Upgrade Notice section. Do not add one.

## MIME catalog

`launch --pair`. wordpress.org is before, the working tree is after. This repository has no baseline capture.

Capture both labels with no `--feature` and no `--types`. The catalog is one plain-text entry, `/?pmt-regression=mimes`. It prints the plugin version, network mode, database version, stored settings string, and each allowed extension regex with its MIME type. It does not print a front-end head, sitemap, robots file, or schema block. Do not treat a missing SEO artifact as a failure.

`compare` the two bundles with no `--feature`. A PHP diagnostic is a blocker. A diff the changelog does not describe is a blocker. The version line changing because wordpress.org is still the previous release is expected; say so. If `Stable tag` was never updated, say so.

If `lib/admin.js` or `lib/admin.css` changed, run the minify skill before the settings-screen check below.

## Changelog controls

Read this version's changelog section. List every select, field, or tab it names. Skip bullets that name no control.

On the after site, use the [Playground skill's Playwright path](../playground/SKILL.md). Do not type a password.

Open `wp-admin/options-general.php?page=pro-mime-types`. Each supported type has an Allow/Disallow select named `pro_mime_types_settings[{option}]`. The Allowed Types tab (`&tab=allowed-types`) is read-only. Flick each select the changelog names, save with the page's Save Changes button, and confirm the effect. Use `capture --paths` or `capture --feature=mimes` when the effect is the allow-list. Set the select back when a later check needs the default.

A control you cannot find is a failed item. If Playwright is not connected, say so. Do not claim the controls were flicked.

## Translation template

Run the [Makepot skill](../makepot/SKILL.md) last, after the release header and Stable tag are final and the other checks are complete. Compare the generated message strings with the previous template and verify `language/pro-mime-types.pot` uses the release version in its header.

## Report

List each item as pass or fail: the release version, `const VERSION`, `Stable tag`, the compare summary, and each control you flicked with the effect you saw.
