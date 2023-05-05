<?php
//* Creating global $promimes variable and array
add_action( 'init', 'pmt_promimes' );

function pmt_promimes($promimes) {
	global $promimes;

	$promimes = array(
		//* array(mimetype,mimename,dangerzone,reason),
		//* dangerzone: 0 = safe, 1 = unwanted, 2 = likely dangerous, 3 = dangerous
		//* TODO: section this into named chunks (e.g. image, video, audio) with adding another dimension.
		
		// Image formats.
		array('jpg|jpeg|jpe','image/jpeg',0,''),
		array('gif','image/gif',0,''),
		array('png','image/png',0,''),
		array('bmp','image/bmp',0,''),
		array('tif|tiff','image/tiff',0,''),
		array('ico','image/x-icon',0,''),
		array('svg','image/svg+xml',0,''),
		
		// Video formats.
		array('asf|asx','video/x-ms-asf',0,''),
		array('wmv','video/x-ms-wmv',0,''),
		array('wmx','video/x-ms-wmx',0,''),
		array('wm','video/x-ms-wm',0,''),
		array('avi','video/avi',0,''),
		array('divx','video/divx',0,''),
		array('flv','video/x-flv',0,''),
		array('mov|qt','video/quicktime',0,''),
		array('mpeg|mpg|mpe','video/mpeg',0,''),
		array('mp4|m4v','video/mp4',0,''),
		array('ogv','video/ogg',0,''),
		array('webm','video/webm',0,''),
		array('mkv','video/x-matroska',0,''),
		array('3gp|3gpp','video/3gpp',0,''), // Can also be audio
		array('3g2|3gp2','video/3gpp2',0,''), // Can also be audio
		
		// Text formats.
		array('txt|asc|c|cc|h|srt','text/plain',0,''),
		array('csv','text/csv',0,''),
		array('tsv','text/tab-separated-values',0,''),
		array('ics','text/calendar',0,''),
		array('rtx','text/richtext',0,''),
		array('css','text/css',3,__('@import and behaviour: rules in CSS can be executed in browser.', 'promimetypes')),
		array('htm|html','text/html',3,__('Can run in iframes through shortcodes. Can import javascript. Can import CSS.', 'promimetypes')),
		array('vtt','text/vtt',0,''),
		array('dfxp','application/ttaf+xml',3,__('Can potentionally import.', 'promimetypes')),
		
		// Audio formats.
		array('mp3|m4a|m4b','audio/mpeg',0,''),
		array('ra|ram','audio/x-realaudio',0,''),
		array('wav','audio/wav',0,''),
		array('ogg|oga','audio/ogg',0,''),
		array('mid|midi','audio/midi',0,''),
		array('wma','audio/x-ms-wma',0,''),
		array('wax','audio/x-ms-wax',0,''),
		array('mka','audio/x-matroska',0,''),
		
		// Misc application formats.
		array('rtf','application/rtf',0,''),
		array('js','application/javascript',3,__('Can execute code in browser.', 'promimetypes')),
		array('pdf','application/pdf',1,__('PDF files are run in browsers and can potentionally contain virusses.', 'promimetypes')),
		array('swf','application/x-shockwave-flash',2,__('Same as pdf.', 'promimetypes')),
		array('class','application/java',3,__('Can be executed on many servers.', 'promimetypes')),
		array('tar','application/x-tar',2,__('Compressed file format, can contain unwanted stuff.', 'promimetypes')),
		array('zip','application/zip',2,__('Compressed file format, can contain unwanted stuff.', 'promimetypes')),
		array('gz|gzip','application/x-gzip',3,__('Compressed file format, can contain unwanted stuff. Executes in browser.', 'promimetypes')),
		array('rar','application/rar',2,__('Compressed file format, can contain unwanted stuff.', 'promimetypes')),
		array('7z','application/x-7z-compressed',2,__('Compressed file format, can contain unwanted stuff.', 'promimetypes')),
		array('exe','application/x-msdownload',3,__('Don\'t even bother.', 'promimetypes')),
		array('psd','application/octet-stream',0,''),
		
		// MS Office formats.
		array('doc','application/msword',1,__('Can contain macros'), 'promimetypes'),
		array('pot|pps|ppt','application/vnd.ms-powerpoint',1,__('Can contain macros', 'promimetypes')),
		array('wri','application/vnd.ms-write',1,__('Can contain macros', 'promimetypes')),
		array('xla|xls|xlt|xlw','application/vnd.ms-excel',1,__('Can contain macros', 'promimetypes')),
		array('mdb','application/vnd.ms-access',1,__('Can contain macros', 'promimetypes')),
		array('mpp','application/vnd.ms-project',1,__('Can contain macros', 'promimetypes')),
		array('docx','application/vnd.openxmlformats-officedocument.wordprocessingml.document',1,__('XML file format.', 'promimetypes')),
		array('docm','application/vnd.ms-word.document.macroEnabled.12',0,''),
		array('dotx','application/vnd.openxmlformats-officedocument.wordprocessingml.template',1,__('XML file format.', 'promimetypes')),
		array('dotm','application/vnd.ms-word.template.macroEnabled.12',0,''),
		array('xlsx','application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',1,__('XML file format.', 'promimetypes')),
		array('xlsm','application/vnd.ms-excel.sheet.macroEnabled.12',0,''),
		array('xlsb','application/vnd.ms-excel.sheet.binary.macroEnabled.12',0,''),
		array('xltx','application/vnd.openxmlformats-officedocument.spreadsheetml.template',1,__('XML file format.', 'promimetypes')),
		array('xltm','application/vnd.ms-excel.template.macroEnabled.12',0,''),
		array('xlam','application/vnd.ms-excel.addin.macroEnabled.12',0,''),
		array('pptx','application/vnd.openxmlformats-officedocument.presentationml.presentation',1,__('XML file format.', 'promimetypes')),
		array('pptm','application/vnd.ms-powerpoint.presentation.macroEnabled.12',0,''),
		array('ppsx','application/vnd.openxmlformats-officedocument.presentationml.slideshow',1,__('XML file format.', 'promimetypes')),
		array('ppsm','application/vnd.ms-powerpoint.slideshow.macroEnabled.12',0,''),
		array('potx','application/vnd.openxmlformats-officedocument.presentationml.template',1,__('XML file format.', 'promimetypes')),
		array('potm','application/vnd.ms-powerpoint.template.macroEnabled.12',0,''),
		array('ppam','application/vnd.ms-powerpoint.addin.macroEnabled.12',0,''),
		array('sldx','application/vnd.openxmlformats-officedocument.presentationml.slide',1,__('XML file format.', 'promimetypes')),
		array('sldm','application/vnd.ms-powerpoint.slide.macroEnabled.12',0,''),
		array('onetoc|onetoc2|onetmp|onepkg','application/onenote',0,''),
		array('oxps','application/oxps',0,''),
		array('xps','application/vnd.ms-xpsdocument',0,''),
		
		// OpenOffice formats.
		array('odt','application/vnd.oasis.opendocument.text',0,''),
		array('odp','application/vnd.oasis.opendocument.presentation',0,''),
		array('ods','application/vnd.oasis.opendocument.spreadsheet',0,''),
		array('odg','application/vnd.oasis.opendocument.graphics',0,''),
		array('odc','application/vnd.oasis.opendocument.chart',0,''),
		array('odb','application/vnd.oasis.opendocument.database',0,''),
		array('odf','application/vnd.oasis.opendocument.formula',0,''),
		
		// WordPerfect formats.
		array('wp|wpd','application/wordperfect',0,''),
		
		// iWork formats.
		array('key','application/vnd.apple.keynote',0,''),
		array('numbers','application/vnd.apple.numbers',0,''),
		array('pages','application/vnd.apple.pages',0,''),
	);
}