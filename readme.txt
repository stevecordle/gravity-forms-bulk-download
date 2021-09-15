=== Gravity Forms Bulk File Downloader ===
Contributors: stevecordle, kzeni
Tags: gravity forms, bulk download
Requires at least: 3.8
Tested up to: 5.2.2
Stable tag: 1.1.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This plugin will create a zip file of all the files uploaded, on submission of a multiple file upload form. It adds a Download All button to every entry with multiple files, also adds a new merge tag for notifications.

The merge tag is {download_all_files_zip}, it will spit out the url of the zip file created.

Ex:

<a href="{download_all_files_zip}" class="download-zip-link">Download a zip of all files uploaded</a>

== Description ==

This plugin will create a zip file of all the files uploaded, on submission of a multiple file upload form. It adds a Download All button to every entry with multiple files, also adds a new merge tag for notifications.

The merge tag is {download_all_files_zip}, it will spit out the url of the zip file created.

Ex:

<a href="{download_all_files_zip}" class="download-zip-link">Download a zip of all files uploaded</a>

== Installation ==

Upload/Activate the plugin.  It automatically creates zip files created with random md5 has for the name, to help with duplicate file names, when there are multiple file uploads on a gravity form that has the file upload (with multiple files activated).

It creates a new merge tag {download_all_files_zip}, which is just the link of the zip file.

== Frequently Asked Questions ==

= What is the merge tag I can use in notifications?

The merge tag is {download_all_files_zip}, it is also in the dropdown for merge tags.

= Where are the settings?

No settings necessary, it will create a zip file automatically when multiple files are uploaded, using gravity forms.

== Screenshots ==

1. This shows the entry list with the Download All button added to the right of (# files).
2. This shows the entry detail page where a Download All Files Zip button has been added at the bottom.
3. This shows the Notifications page where a new merge tag was added that can be used to display the link of the zip file {download_all_files_zip}

== Changelog ==

= 1.1.2 =
* 1.1.1 Missed another function needing to be set as static to avoid PHP issues.

= 1.1.1 =
* Prevent fatal error in newer PHP ("non-static method GFBulkDownload::__construct() cannot be called statically".)
* Other minor adjustments.

= 1.1 =
* Updated to work with current Gravity Forms versions.
* Made sure all intended functionality is working properly (submission email notification link, merge tags, output in entry listings & single entry details, bundling of uploaded files, etc.)
* Made it so zip files are deleted when the corresponding entry is deleted.
* Improved buttons to use standard class styling.

= 1.0 =
* Initial plugin creation.
