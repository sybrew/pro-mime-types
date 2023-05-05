=== Pro Mime Types ===
Contributors: Cybr
Tags: mime, mimetypes, types, multisite, single, upload, wpmu, prosites, pro, sites
Requires at least: 3.1.0
Tested up to: 4.3.1
Stable tag: 1.0.7
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Pro Mime Types allows you to set allowed upload mime types through a nifty network admin menu and considers WPMUdev's Pro Sites!

== Description ==

= Pro Mime Types =

**This plugin works on both WordPress MultiSite and Single Sites**

This plugin allows you to:

* Set allowed Mime types
* If allowed, also set minimum Pro Sites level (WPMUdev plugin)

Within the network admin menu under Settings you can allow or disallow various mime types.
You can also see the list of all active Mime Types on the network.

> <strong>About Pro Mime Types</strong><br>
> This plugin allows you to set available mime types for upload in the Media Library
>
> When a Mime Type is allowed:
> Any user can upload the file
>
> When a Mime Type is disallowed:
> The user gets an error that the file isn't allowed because of security reasons.

*For the extra Pro Sites functionality you'll need the Pro Sites plugin from WPMUdev, get it here: [Pro Sites by WPMUdev]*

[Pro Sites by WPMUdev]: https://premium.wpmudev.org/project/pro-sites/
	"Pro Sites"

== Installation ==

1. Install Pro Mime Types either via the WordPress.org plugin directory, or by uploading the files to your server.
1. Either Network Activate this plugin or activate it on a single site.
1. If you're on a MultiSite network, you can set up the default options for the whole network in your Network Settings menu.
1. If you're on a Single Site installation, you can set up the default options within the Settings menu.
1. That's it! Enjoy!

== Changelog ==

= 1.0.7 =
* Fixed: Fatal Error on attachment call by ID on front end.
* Confirmed: WP 4.3.1 support

= 1.0.6 =
* Fixed: Wrong call in ext2type filter

= 1.0.5 =
* Added: Default mime options (enabled for safe, disabled for the rest) (effective only before the first save has been made)
* Added: Default Mime Types are directly active on first activation
* Added: single-site compatibility
* Added: Extra option saving sanitation
* Removed: Pro Sites information on non-multisite installations
* Fixed: Unlikely XSS vulnerability in admin area
* Cleaned: HTML
* Cleaned: PHP code
* Compatibility: PHP7 & WP 4.3.0 tested

= 1.0.4 =
* Fixed PHP warning

= 1.0.3 =
* Made the shortcodes conforming to the WordPress coding standards

= 1.0.2 =
* Now uses Object Cache to determine Pro Site level, updates every 4 hours

= 1.0.1 =
* Fixed PHP notice
* Loaded global variable $promimes within 'init' instead of 'wp'

= 1.0.0 =
* Initial Release
