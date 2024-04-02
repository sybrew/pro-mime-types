=== Pro Mime Types - Manage file media types ===
Contributors: Cybr
Donate link: https://github.com/sponsors/sybrew
Tags: attachment, image, mime types, upload, multisite
Requires at least: 5.3
Tested up to: 6.2
Requires PHP: 7.4.0
Stable tag: 2.1.0
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Pro Mime Types adds a nifty (network) admin interface to allow or block many file extensions for media, document, and other attachment uploading.

== Description ==

Pro Mime Types allows you to enable or disable many MIME types uploads.

You can also see the list of all active MIME types on the site or network.

- When a MIME type is allowed: Users allowed to upload files can now do so for that MIME type.
- When a MIME type is disallowed: The user gets an error that the file isn't allowed for security reasons.

For WordPress Multisite networks, enable this plugin in network-mode to control MIME types for the entire network.

### Features

* Control many MIME types for upload via a modern interface.
* All assumed safe MIME types are enabled by default.
* All assumed unsafe MIME types have a tooltip with an explanation. Hover over the big colored icon.
* View all allowed MIME types for the site (also those enabled by other plugins).
* Sort through text, code, and miscellaneous file types via the Media Library,
* Sorting through images, audio, video, documents, spreadsheets, and archives now recognizes more types.
* For Multisite: This plugin can run in single-site mode, where every subsite has custom-allowed MIME types. Only the network administrator can assign these.
* For Multisite: This plugin can run in network mode, where all sites are allowed the same MIME types. You can configure the allowed MIME types via the network administration UI.

**Note:** Direct PHP file uploads cannot be enabled via this plugin. You should use FTP for that.

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

= Which file extensions are supported? ==

`bmp`, `gif`, `heic`, `heif`, `ico`, `jpg`, `jpeg`, `jpe`, `jif`, `jfif`, `png`, `svg`, `tif`, `tiff`, `webp`,  `aac`, `ac3`, `aff`, `aif`, `aiff`, `flac`, `mid`, `midi`, `mka`, `mp1`, `mp2`, `mp3`, `m3a`, `m4a`, `m4b`, `ogg`, `oga`, `ra`, `ram`, `wav`, `wax`, `wma`,  `3g2`, `3gp2`, `3gp`, `3gpp`, `asf`, `asx`, `avi`, `divx`, `mkv`, `mov`, `qt`, `mp4`, `m4v`, `mpeg`, `mpg`, `mpe`, `mpv`, `vob`, `ogv`, `ogm`, `rm`, `webm`, `wm`, `wmv`, `wmx`,  `doc`, `docm`, `docx`, `dotm`, `dotx`, `odt`, `oxps`, `pages`, `pdf`, `psd`, `ai`, `rtf`, `wri`, `wp`, `wpd`, `xcf`, `xps`,  `numbers`, `ods`, `xla`, `xls`, `xlt`, `xlw`, `xlam`, `xlsb`, `xlsm`, `xlsx`, `xltm`, `xltx`,  `key`, `odp`, `pot`, `pps`, `ppt`, `potm`, `potx`, `ppam`, `ppsm`, `ppsx`, `pptm`, `pptx`, `sldm`, `sldx`,  `csv`, `ics`, `md`, `rtx`, `tsv`, `txt`, `asc`, `c`, `cc`, `h`, `srt`, `vtt`,  `7z`, `cab`, `gz`, `gzip`, `img`, `2mg`, `smi`, `dmg`, `rar`, `tar`, `zip`,  `css`, `dfxp`, `htm`, `html`, `js`, `xml`, `php`,  `class`, `exe`, `mdb`, `mpp`, `odb`, `odc`, `odf`, `odg`, `onetoc`, `onetoc2`, `onetmp`, and `onepkg`.

= Which MIME types are supported? =

`image/bmp`, `image/gif`, `image/heic`, `image/x-icon`, `image/jpeg`, `image/png`, `image/svg+xml`, `image/tiff`, `image/webp`, `audio/aac`, `audio/ac3`, `audio/aiff`, `audio/flac`, `audio/midi`, `audio/x-matroska`, `audio/mpeg`, `audio/ogg`, `audio/x-realaudio`, `audio/wav`, `audio/x-ms-wax`, `audio/x-ms-wma`, `video/3gpp2`, `video/3gpp`, `video/x-ms-asf`, `video/avi`, `video/divx`, `video/x-matroska`, `video/quicktime`, `video/mp4`, `video/mpeg`, `video/ogg`, `application/vnd.rn-realmedia`, `video/webm`, `video/x-ms-wm`, `video/x-ms-wmv`, `video/x-ms-wmx`, `application/msword`, `application/vnd.ms-word.document.macroEnabled.12`, `application/vnd.openxmlformats-officedocument.wordprocessingml.document`, `application/vnd.ms-word.template.macroEnabled.12`, `application/vnd.openxmlformats-officedocument.wordprocessingml.template`, `application/vnd.oasis.opendocument.text`, `application/oxps`, `application/vnd.apple.pages`, `application/pdf`, `image/vnd.adobe.photoshop`, `application/postscript`, `application/rtf`, `application/vnd.ms-write`, `application/wordperfect`, `image/x-xcf`, `application/vnd.ms-xpsdocument`, `application/vnd.apple.numbers`, `application/vnd.oasis.opendocument.spreadsheet`, `application/vnd.ms-excel`, `application/vnd.ms-excel.addin.macroEnabled.12`, `application/vnd.ms-excel.sheet.binary.macroEnabled.12`, `application/vnd.ms-excel.sheet.macroEnabled.12`, `application/vnd.openxmlformats-officedocument.spreadsheetml.sheet`, `application/vnd.ms-excel.template.macroEnabled.12`, `application/vnd.openxmlformats-officedocument.spreadsheetml.template`, `application/vnd.apple.keynote`, `application/vnd.oasis.opendocument.presentation`, `application/vnd.ms-powerpoint`, `application/vnd.ms-powerpoint.template.macroEnabled.12`, `application/vnd.openxmlformats-officedocument.presentationml.template`, `application/vnd.ms-powerpoint.addin.macroEnabled.12`, `application/vnd.ms-powerpoint.slideshow.macroEnabled.12`, `application/vnd.openxmlformats-officedocument.presentationml.slideshow`, `application/vnd.ms-powerpoint.presentation.macroEnabled.12`, `application/vnd.openxmlformats-officedocument.presentationml.presentation`, `application/vnd.ms-powerpoint.slide.macroEnabled.12`, `application/vnd.openxmlformats-officedocument.presentationml.slide`, `text/csv`, `text/calendar`, `text/markdown`, `text/richtext`, `text/tab-separated-values`, `text/plain`, `text/vtt`, `application/x-7z-compressed`, `application/vnd.ms-cab-compressed`, `application/x-gzip`, `application/x-apple-diskimage`, `application/rar`, `application/x-tar`, `application/zip`, `text/css`, `application/ttaf+xml`, `text/html`, `application/javascript`, `application/xhtml+xml`, `application/x-httpd-java`, `application/x-msdownload`, `application/vnd.ms-access`, `application/vnd.ms-project`, `application/vnd.oasis.opendocument.database`, `application/vnd.oasis.opendocument.chart`, `application/vnd.oasis.opendocument.formula`, `application/vnd.oasis.opendocument.graphics`, and `application/onenote`.

== Changelog ==

= 2.0.2 =

* Upgrade: TODO The stored settings will now convert from regex-based to key-based.
* Added: AVIF is now supported (image/avif, .avif|avifs). You require WP 6.5 or later to prevent corruption of the upload.
* Added: The settings link has been added to Pro Mime Types's listing on the plugin activation page.
	* In network mode, only the network administrator will see this and it will be accessible from any subsite.
* Changed: constant `Pro_Mime_Types\SUPPORTED_MIME_TYPES` is now an associative array (with index keys), instead of a sequential one.
	* Filter `pmt_supported_mime_types` is directly affected by this change, but using sequential values is still possible, though they may not be stored properly.
* Fixed: .exe is now assigned the MIME type PHP recognizes, instead of what Windows does.
	Should be `application/x-dosexec` instead of `application/x-msdownload`.
* TODO inspect https://github.com/WordPress/wordpress-develop/pull/1232
* TODO upgrade copyright year.

= 2.0.1 =

* Changed: `.svg` was inattentively marked as a "safe" file extension, which it's not. Consider using the [Safe SVG plugin](https://wordpress.org/plugins/safe-svg/) to sanitize SVG uploads (you must still allow them via Pro Mime Types). If you do not use SVGs, you should disallow them from being uploaded.
* Improved: The plugin is faster now since it memoizes repeated translations.
* Updated: New translations are available.

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
