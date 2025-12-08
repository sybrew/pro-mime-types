=== Pro Mime Types - Manage file media types ===
Contributors: Cybr
Donate link: https://github.com/sponsors/sybrew
Tags: attachment, image, mime types, upload, multisite
Requires at least: 5.3
Tested up to: 6.5
Requires PHP: 7.4.0
Stable tag: 2.1.1
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Pro Mime Types adds a nifty admin interface for allowing or blocking many file extensions for uploading media, documents, and other attachments.

== Description ==

Pro Mime Types adds a nifty (network) admin interface for allowing or blocking many file extensions for uploading media, documents, and other attachments.

It also shows you a list of all allowed MIME types on the site or network.

- When a MIME type is allowed, users who can upload files can now do so for that MIME type.
- When a MIME type is blocked, users see an error that the file isn't allowed for security reasons.

For WordPress Multisite networks, you can enable this plugin in network mode to control MIME types for the entire network.

### Features

* You can control many MIME types and extensions for upload via a modern interface.
* Pro Mime Types comes preconfigured by enabling many safe MIME types.
* View all allowed MIME types for the site (also those enabled by other plugins).
* Every MIME type comes with a security summary explaining why you should or shouldn't allow it. To view the summary, hover the mouse cursor over the big colored icon.
* Accessibility is at the forefront. For example, you can use full keyboard navigation, even for tooltips.
* Adds text, code, and miscellaneous file types to the Media Library for sorting.
* The Media Library gains support for more file types for sorting images, audio, video, documents, spreadsheets, and archives.

### Multisite support

This plugin can run in network mode, where all sites are allowed one set of MIME types. You can configure the allowed MIME types via the network administration UI.

Alternatively, Pro Mime Types can run in single-site mode, where every subsite has custom-allowed MIME types. Only the network administrator can assign these on a per-site basis.

== Installation ==

1. Install Pro Mime Types via the WordPress.org plugin directory or by uploading the files to your server.
1. Either Network Activate this plugin or activate it on a single site.
1. If you're on a Multisite network, you can set up the default options for the whole network via the Network Settings menu.
1. If you're on a Single Site installation, you can set up the default options via the Settings menu.
1. That's it! Enjoy!

== Screenshots ==

1. The settings user-interface of Pro Mime Types.
2. The enabled extensions interface of Pro Mime Types.

== Frequently Asked Questions ==

= I enabled a file type, but I still couldn't upload it! =

Not all PHP installations recognize MIME types the same way, making it difficult for us to test every file type.

Some file types are blocked by WordPress itself when the server lacks support for them, such as `.avif` and `.webp`.

If you find an issue, please open a [support topic](https://wordpress.org/support/plugin/pro-mime-types/#new-topic-0) or [GitHub issue](https://github.com/sybrew/pro-mime-types/issues/new) and detail your website's [PHP version](https://wordpress.org/documentation/article/site-health-screen/#server) and image extension so we can start investigating the MIME type.

= Which file extensions are supported? ==

`avif`, `avifs`, `bmp`, `gif`, `heic`, `heif`, `ico`, `jpg`, `jpeg`, `jpe`, `jif`, `jfif`, `png`, `svg`, `tif`, `tiff`, `webp`,  `aac`, `ac3`, `aff`, `aif`, `aiff`, `flac`, `mid`, `midi`, `mka`, `mp1`, `mp2`, `mp3`, `m3a`, `m4a`, `m4b`, `ogg`, `oga`, `pcm`, `ra`, `ram`, `wav`, `wax`, `wma`,  `3g2`, `3gp2`, `3gp`, `3gpp`, `asf`, `asx`, `avi`, `divx`, `mkv`, `mov`, `qt`, `mp4`, `m4v`, `mpeg`, `mpg`, `mpe`, `mpv`, `vob`, `ogv`, `ogm`, `rm`, `webm`, `wm`, `wmv`, `wmx`,  `doc`, `docm`, `docx`, `dotm`, `dotx`, `epub`, `odt`, `oxps`, `pages`, `pdf`, `psd`, `ai`, `rtf`, `wri`, `wp`, `wpd`, `xcf`, `xps`,  `numbers`, `ods`, `xla`, `xls`, `xlt`, `xlw`, `xlam`, `xlsb`, `xlsm`, `xlsx`, `xltm`, `xltx`,  `key`, `odp`, `pot`, `pps`, `ppt`, `potm`, `potx`, `ppam`, `ppsm`, `ppsx`, `pptm`, `pptx`, `sldm`, `sldx`,  `csv`, `ics`, `md`, `rtx`, `tsv`, `txt`, `asc`, `c`, `cc`, `h`, `srt`, `vtt`,  `7z`, `cab`, `gz`, `gzip`, `img`, `2mg`, `smi`, `dmg`, `rar`, `tar`, `zip`,  `css`, `dfxp`, `htm`, `html`, `js`, `xml`, `php`,  `class`, `exe`, `mdb`, `mpp`, `odb`, `odc`, `odf`, `odg`, `onetoc`, `onetoc2`, `onetmp`, and `onepkg`.

= Which MIME types are supported? =

`image/avif`, `image/bmp`, `image/gif`, `image/heic`, `image/x-icon`, `image/jpeg`, `image/png`, `image/svg+xml`, `image/tiff`, `image/webp`, `audio/aac`, `audio/ac3`, `audio/aiff`, `audio/flac`, `audio/midi`, `audio/x-matroska`, `audio/mpeg`, `audio/ogg`, `audio/x-realaudio`, `audio/wav`, `audio/x-ms-wax`, `audio/x-ms-wma`, `audio/x-wav`, `video/3gpp2`, `video/3gpp`, `video/x-ms-asf`, `video/avi`, `video/divx`, `video/x-matroska`, `video/quicktime`, `video/mp4`, `video/mpeg`, `video/ogg`, `application/vnd.rn-realmedia`, `video/webm`, `video/x-ms-wm`, `video/x-ms-wmv`, `video/x-ms-wmx`, `application/msword`, `application/vnd.ms-word.document.macroEnabled.12`, `application/vnd.openxmlformats-officedocument.wordprocessingml.document`, `application/vnd.ms-word.template.macroEnabled.12`, `application/vnd.openxmlformats-officedocument.wordprocessingml.template`, `application/epub+zip`, `application/vnd.oasis.opendocument.text`, `application/oxps`, `application/vnd.apple.pages`, `application/pdf`, `image/vnd.adobe.photoshop`, `application/postscript`, `application/rtf`, `application/vnd.ms-write`, `application/wordperfect`, `image/x-xcf`, `application/vnd.ms-xpsdocument`, `application/vnd.apple.numbers`, `application/vnd.oasis.opendocument.spreadsheet`, `application/vnd.ms-excel`, `application/vnd.ms-excel.addin.macroEnabled.12`, `application/vnd.ms-excel.sheet.binary.macroEnabled.12`, `application/vnd.ms-excel.sheet.macroEnabled.12`, `application/vnd.openxmlformats-officedocument.spreadsheetml.sheet`, `application/vnd.ms-excel.template.macroEnabled.12`, `application/vnd.openxmlformats-officedocument.spreadsheetml.template`, `application/vnd.apple.keynote`, `application/vnd.oasis.opendocument.presentation`, `application/vnd.ms-powerpoint`, `application/vnd.ms-powerpoint.template.macroEnabled.12`, `application/vnd.openxmlformats-officedocument.presentationml.template`, `application/vnd.ms-powerpoint.addin.macroEnabled.12`, `application/vnd.ms-powerpoint.slideshow.macroEnabled.12`, `application/vnd.openxmlformats-officedocument.presentationml.slideshow`, `application/vnd.ms-powerpoint.presentation.macroEnabled.12`, `application/vnd.openxmlformats-officedocument.presentationml.presentation`, `application/vnd.ms-powerpoint.slide.macroEnabled.12`, `application/vnd.openxmlformats-officedocument.presentationml.slide`, `text/csv`, `text/calendar`, `text/markdown`, `text/richtext`, `text/tab-separated-values`, `text/plain`, `text/vtt`, `application/x-7z-compressed`, `application/vnd.ms-cab-compressed`, `application/x-gzip`, `application/x-apple-diskimage`, `application/rar`, `application/x-tar`, `application/zip`, `text/css`, `application/ttaf+xml`, `text/html`, `application/javascript`, `application/xhtml+xml`, `application/x-httpd-java`, `application/x-msdownload`, `application/vnd.ms-access`, `application/vnd.ms-project`, `application/vnd.oasis.opendocument.database`, `application/vnd.oasis.opendocument.chart`, `application/vnd.oasis.opendocument.formula`, `application/vnd.oasis.opendocument.graphics`, and `application/onenote`.

= I cannot enable PHP uploads? =

No. Direct PHP file uploads cannot be enabled via this plugin. You should use SFTP or FTP for that.

== Changelog ==

= 2.2.0 =

* Added: Support for EPUB files (`application/epub+zip`, extension `epub`).
* Added: Support for PCM audio files (`audio/x-wav`, extension `pcm`).
* Fixed: WordPress 6.8 started showing deprecation notices about text domains being loaded too early. We resolved this by loading the plugin at `init`, instead of `plugins_loaded`.

### Full Changelog

[Read the full changelog at GitHub](https://github.com/sybrew/pro-mime-types/blob/main/changelog.md).
