<?php defined('ABSPATH') or die("No direct access allowed.");
/**
 * Plugin Name: Gravity Forms Bulk Download
 * Plugin URI: http://stevecordle.net
 * Description: Bulk download files for file upload forms using Gravity Forms
 * Version: 1.1
 * Author: Steve Cordle
 * Network: true
 * Author URI: http://stevecordle.net/
 * */

require_once __DIR__ . '/vendor/autoload.php';

// Start up the plugin after GravityForms is loaded.
add_action( 'gform_loaded', array( 'GFBulkDownload', '__construct' ), 5 );

if (!class_exists("GFBulkDownload")) {

	class GFBulkDownload {

		protected $name = 'Gravity Forms Bulk Download';
		protected $version = '1.1';

		public function __construct() {
			add_action("gform_entry_created",			array($this, 'afterSubmission'), 1, 2);
			add_action("gform_pre_submission_filter",	array($this, 'preSubmissionFilter'), 10, 1);
			add_action("gform_entries_column_filter",	array($this, 'change_column_data'), 10, 5);
			add_filter('gform_custom_merge_tags',		array($this, 'custom_merge_tags'), 10, 4);
			add_filter('gform_replace_merge_tags',		array($this, 'replace_download_link'), 10, 7);
			add_action("gform_entry_detail",			array($this, 'addMetaToDetails'), 10, 2);
			add_filter('gform_notification',			array($this, 'append_before_email_sent'), 10, 3);
			add_action('gform_delete_entry',			array($this, 'delete_entry_file_zip'), 10, 1);
		}

		function append_before_email_sent( $notification, $form, $entry ) {
			$site = trailingslashit(get_bloginfo('url'));
			$zip_path = gform_get_meta($entry['id'], 'zip_path');
			if(!empty($zip_path)){
				$url = $site.$zip_path;
				$notification['message'] .= "\n\n <a href='{$url}' >Download All Files in Zip</a> \n\n";
			}
			return $notification;
		}

		public function addStyles(){

		}

		public function addScripts(){

		}

		function addMetaToDetails($form, $entry){
			$zip = gform_get_meta($entry['id'], 'zip_path');

			if(!empty($zip)){
				$site_url = trailingslashit(get_bloginfo('url'));
				$url = $site_url.$zip;
				echo	"<table cellspacing='0' class='widefat fixed entry-detail-view'>
							<tr>
								<td colspan='2' class='entry-view-field-name' >Zip File Download Link</td>
							</tr>
							<tr>
								<td colspan='2' class='entry-view-field-value lastrow'>
									<a class='button' style='margin-left:0;' href='{$url}'> Download All Files Zip <i class='fa fa-download'> </i></a>
								</td>
							</tr>
						</table>";
			}
		}

		public function preSubmissionFilter($form) {

			//Get upload path for form
			$form_upload_dir = GFFormsModel::get_upload_path($form['id']);

			//strip everything up to wp-content/....   so we can build a url
			$form_upload_dir_formatted = stristr($form_upload_dir, 'wp-content/');

			//decode json and remove slashes to get list of files
			$files_for_zip = json_decode(stripslashes($_POST['gform_uploaded_files']), true);

			if(!is_null($files_for_zip)){
				//reset the array pointer
				reset($files_for_zip);

				// get the key of the first element (This is due to the format of the array  )
				$key = key($files_for_zip);
				$zipArray = $files_for_zip[$key];

				$zip = new ZipArchive();
				$filename = 'files_' . md5(rand(0123456, 7890123)) . '.zip';
				$file_path = trailingslashit($form_upload_dir) . $filename;
				$zip->open($file_path, ZipArchive::CREATE);
				foreach($zipArray as $file_info){
					$zip->addFile($form_upload_dir . '/tmp/' . $file_info['temp_filename'], $file_info['uploaded_filename']);
				}
				$zip->close();

				//Set the zip key for the $form object to the url for the zip file we created, minus the domainname so we can pass it to the entry
				$form['zip'] = trailingslashit($form_upload_dir_formatted) . $filename;
			}
			return $form;
		}

		public function afterSubmission($entry, $form) {
			//update the Entry with a zip_file meta field with a link to the zip file that was created.
			if (isset($form['zip'])) {
				gform_update_meta($entry['id'], 'zip_path', $form['zip']);
			}
		}

		public function delete_entry_file_zip( $entry_id ) {
			//getting entry object
			$entry = GFAPI::get_entry( $entry_id );
			$zip = gform_get_meta($entry['id'], 'zip_path');
			//if entry is associated with a file zip, delete it so zip files aren't left on the server after the entry was deleted
			if(!empty($zip)){
				wp_delete_file($zip);
			}
		}

		function change_column_data($value, $form_id, $field_id, $lead, $query_string) {
			$form = GFFormsModel::get_form_meta($form_id);
			$zip_path = gform_get_meta($lead['id'], 'zip_path');
			$site_url = get_bloginfo('url');
			$full_url = trailingslashit($site_url) . $zip_path;
			foreach ($form['fields'] as $field) {
				if ($field['type'] == 'fileupload' && $field['multipleFiles'] == "1") {
					$upload_field_id = $field['id'];
				}
			}
			if ($upload_field_id == $field_id) {
				if (!empty($zip_path)) {
					return $value . " <a class='button' href='$full_url'> Download All <i class='fa fa-download'> </i></a>";
				}
			}
			return $value;
		}

		function custom_merge_tags($merge_tags, $form_id, $fields, $element_id) {
			$merge_tags[] = array('label' => 'Download All Files (Zip)', 'tag' => '{download_all_files_zip}');
			return $merge_tags;
		}

		function replace_download_link($text, $form, $entry, $url_encode, $esc_html, $nl2br, $format) {
			$custom_merge_tag = '{download_all_files_zip}';
			if (strpos($text, $custom_merge_tag) === false) {
				return $text;
			} else {
				$zip_path = gform_get_meta($entry['id'], 'zip_path');
				$full_url = trailingslashit(get_bloginfo('url')) . $zip_path;
				return str_replace($custom_merge_tag, $full_url, $text);
			}
		}

	}

	new GFBulkDownload();
}
