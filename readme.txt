=== Pro Mime Types ===
Contributors: Cybr
Donate link: https://github.com/sponsors/sybrew
Tags: mimes, mime types, types, multisite, network, upload, attachment, security, images, video, pdf
Requires at least: 5.3
Tested up to: 6.2
Requires PHP: 7.4.0
Stable tag: 2.0.0
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Pro Mime Types enables you to allow or block MIME types for media files and other attachment uploads through a nifty (network) admin menu.

== Description ==

Pro Mime Types allows you to enable or disable many MIME types uploads.

You can also see the list of all active MIME types on the site or network.

- When a MIME type is allowed: Users allowed to upload files can now do so for that MIME type.
- When a MIME type is disallowed: The user gets an error that the file isn't allowed for security reasons.

For WordPress Multisite networks, enable this plugin in network-mode control MIME types on the entire network.

= Features =

* Control many MIME types for upload via a modern interface.
* All assumed safe MIME types are enabled by default.
* All assumed unsafe MIME types have a tooltip with an explanation. Hover over the big colored icon.
* View all allowed MIME types for the site (also those enabled by other plugins).
* Sort through text, code, and miscellaneous file types via the Media Library,
* Sorting through images, audio, video, documents, spreadsheets, and archives now recognizes more types.
* For Multisite: This plugin can run in single-site mode, where every subsite has custom-allowed MIME types. Only the network administrator can assign these.
* For Multisite: This plugin can run in network mode, where all sites are allowed the same MIME types. You can configure the allowed MIME types via the network administration UI.

**Note:** PHP file type support cannot be enabled to protect you.

== Installation ==

1. Install Pro Mime Types either via the WordPress.org plugin directory or by uploading the files to your server.
1. Either Network Activate this plugin or activate it on a single site.
1. If you're on a MultiSite network, you can set up the default options for the whole network in your Network Settings menu.
1. If you're on a Single Site installation, you can set up the default options within the Settings menu.
1. That's it! Enjoy!

== Frequently Asked Questions ==

= I enabled a file type, but I still couldn't upload it =

Not all PHP installations recognize MIME types the same way, making it difficult for us to test every file type.

If you find an issue, please open a [support topic](https://wordpress.org/support/plugin/pro-mime-types/#new-topic-0) or [GitHub issue](https://github.com/sybrew/pro-mime-types/issues/new) and detail your website's [PHP version](https://wordpress.org/documentation/article/site-health-screen/#server) and image extension so we can start investigating the MIME type.

== Changelog ==

= 2.0.0 =

After eight years without updates (yet still working with the latest version of WordPress), Pro Mime Types got rewritten from the ground up. Now it's written by a senior PHP developer (me) instead of a noob (also me).

* Security: Resolved a security vulnerability (CSRF) due to missing nonces, where a rogue actor could enable and disable MIME-type support after tricking an admin into clicking a rogue link. Props Nguyen Xuan Chien via PatchStack.
* Added: You can now submit translations for this plugin via WordPress.org.
* Added: `application/postscript`, `application/vnd.ms-cab-compressed`, `application/x-apple-diskimage`, `audio/aac`, `audio/ac3`, `audio/aiff`, `audio/flac`, `image/heic`, and `image/webp` are now supported MIME types.
* Added: `.2mg`, `.aac`, `.ac3`, `.aff`, `.ai`, `.aif`, `.aiff`, `.cab`, `.dmg`, `.flac`, `.heic`, `.img`, `.jfif`, `.jif`, `.md`, `.mp1`, `.mp2`, `.mpeg`, `.ogm`, `.smi`, `.vob`, `.xcf`, and `.xml` are now supported extensions.
* Added: Extensive type detection for the image and video MIME types mentioned above. WordPress uses this for shortcodes and file extension conversion.
* Added: This plugin can now be used on a WordPress Multisite network in single-site mode.
	* However, network administrative capabilities are still required to change MIME-type support. Regular site administrators cannot enable MIME-type support on a network for your security.
* Added: You can now filter in custom MIME types via filter `pmt_supported_mime_types`. That filter must be registered before `plugins_loaded`.
* Added: The plugin options are saved when installing the plugin. This prevents extra database lookups on every request when the options are left as default.
* Added: Now allows uploading of file types even if WordPress fails to deduct them.
* Added: You can now sort through text, code, and miscellaneous file types in the Media Library.
* Changed: The "allowed MIME types" list now shows the actual values recognized by WordPress instead of inferring from the options set with this plugin.
* Changed: The plugin translation domain is now `pro-mime-types`, from `promimetypes`.
* Changed: Now requires WP 5.3 or later because it adds a test for the following PHP requirement.
* Changed: Now requires PHP 7.4 or later.
* Changed: The plugin and all its files now use license GPLv3, from "GPLv2 or later."
* Improved: All MIME type options are now stored in a single row, comma separated by "extension regex," instead of using 90 rows (one for each type).
	* After updating, all options will be migrated. It would be best if you did not downgrade, or you'll get 90 extraneous settings in your database.
	* Note: During the upgrade, if the plugin operates in single-site mode on a network, then **only** the first site that receives a request will get migrated the options; all other sites will use the default settings. This plugin has too small of a reach to consider this edge case. Moreover, the settings weren't always reachable in single-site mode, anyway.
* Improved: Overall performance by implementing better coding standards.
* Removed: Pro Sites support and all related settings; that plugin has been abandoned.
* Removed: Shortcode `superadmin_showmimetypes`.
* Removed: All old functions, filters, actions, callbacks, and globals.
* Removed: Support for `.swf` and `.flv` file extensions. Shockwave Flash is long gone.
* Removed: Support for `.dv` (DeltaVision) and `.sea` (Compact Pro) file sorting in WordPress's Media Library. I couldn't find a valid MIME type.
* Removed: Support for `.php` (PHP Hypertext Processor) file sorting in WordPress's Media Library. Consider using FTP.
	* Implementing this via Pro Mime Types would allow the uploading of PHP files, which we will refuse to implement.

= 1.0.7 =
* Fixed: Fatal Error on attachment call.
* Confirmed: WP 4.3.1 support.

= 1.0.6 =
* Fixed: Wrong call in ext2type filter.

= 1.0.5 =
* Added: Default MIME options (enabled for safe, disabled for the rest) (effective only before the first save has been made).
* Added: Default MIME Types are directly active on the first activation.
* Added: single-site compatibility.
* Added: Extra option saving sanitation.
* Removed: Pro Sites information on non-multisite installations.
* Cleaned: HTML.
* Cleaned: PHP code.
* Compatibility: PHP7 & WP 4.3.0 tested.

= 1.0.4 =
* Fixed PHP warning.

= 1.0.3 =
* Made the shortcodes conforming to the WordPress coding standards.

= 1.0.2 =
* Now uses Object Cache to determine Pro Site level, updates every 4 hours.

= 1.0.1 =
* Fixed PHP notice.
* Loaded global variable $promimes within 'init' instead of 'wp'.

= 1.0.0 =
* Initial Release.
