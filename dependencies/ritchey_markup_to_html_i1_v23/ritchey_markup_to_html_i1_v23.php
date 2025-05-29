<?php
# Meta
/*
Name: Ritchey Markup To HTML i1 v23
Description: Convert text (marked using a custom markup language) to HTML. Returns "TRUE" on success. Returns "FALSE" on failure.
Notes:
- Optional arguments can be "NULL" to skip them in which case they will use default values.
- The HTML document produced does not follow common design practices, because it is intended for viewing as a document, not for serving as a website.
Arguments: 'source_file' (required) is the file to read from. 'destination_file' (required) the path of where to write the HTML file. 'css_file' (optional) is a path to a css file import into the HTML. 'preserve_empty_lines' (optional) specifies whether to preserve empty lines, or ignore them. 'overwrite' (optional) specifies whether it's okay to write over the destination_file, if it already exists. 'display_errors' (optional) indicates if errors should be displayed.
Arguments (Script Friendly): source_file:file:required,destination_file:file:required,css_file:file:optional,preserve_empty_lines:bool:optional,overwrite:bool:optional,display_errors:bool:optional
*/
# Content
if (function_exists('ritchey_markup_to_html_i1_v23') === FALSE){
function ritchey_markup_to_html_i1_v23($source_file, $destination_file, $css_file = NULL, $preserve_empty_lines = NULL, $overwrite = NULL, $display_errors = NULL){
	$errors = array();
	$location = realpath(dirname(__FILE__));
	if (@is_file($source_file) === FALSE){
		$errors[] = "source_file";
	}
	if (@is_dir(@dirname($destination_file)) === FALSE){
		$errors[] = 'destination_file';
	} else if (@is_file($destination_file) !== FALSE){
		if ($overwrite !== TRUE){
			$errors[] = "destination_file";
		}
	}
	if ($css_file === NULL){
		$css_file = "{$location}/custom_dependencies/ritchey-general-theme-v2.css";
	} else if (@is_file($css_file) === TRUE){
		// Do nothing
	} else {
		$errors[] = "css_file";
	}
	if ($overwrite === NULL){
		$overwrite = FALSE;
	} else if ($overwrite === TRUE){
		// Do nothing
	} else if ($overwrite === FALSE){
		// Do nothing
	} else {
		$errors[] = "overwrite";
	}
	if ($preserve_empty_lines === NULL){
		$preserve_empty_lines = TRUE;
	} else if ($preserve_empty_lines === TRUE){
		// Do nothing
	} else if ($preserve_empty_lines === FALSE){
		// Do nothing
	} else {
		$errors[] = "preserve_empty_lines";
	}
	if ($display_errors === NULL){
		$display_errors = FALSE;
	} else if ($display_errors === TRUE){
		// Do nothing
	} else if ($display_errors === FALSE){
		// Do nothing
	} else {
		$errors[] = "display_errors";
	}
	## Task
	if (@empty($errors) === TRUE){
		### Import text as an array of individual lines
		$data = array();
		$handle = @fopen($source_file, 'r');
		while (@feof($handle) !== TRUE) {
			// Get line from file
			$line = @fgets($handle);
			$line = rtrim($line, "\n\r\v");
			$data[] = $line;
		}
		@fclose($handle);
		### Process lines for encapsulating elements, and then for sub elements. Because it's done on a line by line basis, there's no concept of sections, or multi-line elements (e.g. lists), which impacts how styling is done. The idea is that things are normally all styled one way. However, a unique id specific to that item can be used to overrule this, but it must be done on a case by case basis.
		$line = 0;
		$switch1 = FALSE;
		$marker_md5 = 'd41d8cd98f00b204e9800998ecf8427e'; // This is an empty string MD5 checksum. It needs to be set, since there might not be an h2 to build a marker from.
		foreach ($data as &$value){
			$md5 = hash('md5', $value);
			$line++;
			#### Process encapsulating elements
			$html_div_format = array(0 => "<div", 'outter_ids' => "", 1 => ">", 2 => "<div", 'extra_ids' => "", 3 => ">", 4 => "<div", 'inner_ids' => "", 5 => ">", 'content' => "", 6 => "</div>", 7 => "</div>", 8 => "</div>");
			// Remove lines that start with '//'
			if (substr($value, 0, 2) === '//' and $switch1 === FALSE){
				$value = '';
			// Remove empty lines
			} else if (trim($value) === '' and $switch1 === FALSE){
				if ($preserve_empty_lines === TRUE){
					$formatted_value = $html_div_format;
					$formatted_value['content'] = '';
					$formatted_value['outter_ids'] = " id='outter_{$line}' class='outter_empty_line' data-md5='outter_{$md5}' data-marker-md5='{$marker_md5}'";
					$formatted_value['extra_ids'] = " id='extra_{$line}' class='extra_empty_line' data-md5='extra_{$md5}' data-marker-md5='{$marker_md5}'";
					$formatted_value['inner_ids'] = " id='inner_{$line}' class='inner_empty_line' data-md5='inner_{$md5}' data-marker-md5='{$marker_md5}'";
					$value = implode($formatted_value);
				} else {
					$value = '';
				}
			// Add heading elements
			} else if (substr($value, 0, 1) === '#'  and $switch1 === FALSE){
				// Process level 1 headings
				if(substr($value, 0, 1) === '#' and substr($value, 1, 1) === ' '){
					$formatted_value = $html_div_format;
					$formatted_value['content'] = trim(substr($value, 2));
					$formatted_value['outter_ids'] = " id='outter_{$line}' class='outter_h1' data-md5='outter_{$md5}' data-marker-md5='{$marker_md5}'";
					$formatted_value['extra_ids'] = " id='extra_{$line}' class='extra_h1' data-md5='extra_{$md5}' data-marker-md5='{$marker_md5}'";
					$formatted_value['inner_ids'] = " id='inner_{$line}' class='inner_h1' data-md5='inner_{$md5}' data-marker-md5='{$marker_md5}'";
					$value = implode($formatted_value);
				}
				// Process level 2 headings
				if(substr($value, 0, 2) === '##' and substr($value, 2, 1) === ' '){
					$marker_md5 = $md5; // This only needs to be done for h2s.
					$formatted_value = $html_div_format;
					$formatted_value['content'] = trim(substr($value, 3));
					$formatted_value['outter_ids'] = " id='outter_{$line}' class='outter_h2' data-md5='outter_{$md5}' data-marker-md5='{$marker_md5}'";
					$formatted_value['extra_ids'] = " id='extra_{$line}' class='extra_h2' data-md5='extra_{$md5}' data-marker-md5='{$marker_md5}'";
					$formatted_value['inner_ids'] = " id='inner_{$line}' class='inner_h2' data-md5='inner_{$md5}' data-marker-md5='{$marker_md5}'";
					$value = implode($formatted_value);
				}
				// Process level 3 headings
				if(substr($value, 0, 3) === '###' and substr($value, 3, 1) === ' '){
					$formatted_value = $html_div_format;
					$formatted_value['content'] = trim(substr($value, 4));
					$formatted_value['outter_ids'] = " id='outter_{$line}' class='outter_h3' data-md5='outter_{$md5}' data-marker-md5='{$marker_md5}'";
					$formatted_value['extra_ids'] = " id='extra_{$line}' class='extra_h3' data-md5='extra_{$md5}' data-marker-md5='{$marker_md5}'";
					$formatted_value['inner_ids'] = " id='inner_{$line}' class='inner_h3' data-md5='inner_{$md5}' data-marker-md5='{$marker_md5}'";
					$value = implode($formatted_value);
				}
				// Process level 4 headings
				if(substr($value, 0, 4) === '####' and substr($value, 4, 1) === ' '){
					$formatted_value = $html_div_format;
					$formatted_value['content'] = trim(substr($value, 5));
					$formatted_value['outter_ids'] = " id='outter_{$line}' class='outter_h4' data-md5='outter_{$md5}' data-marker-md5='{$marker_md5}'";
					$formatted_value['extra_ids'] = " id='extra_{$line}' class='extra_h4' data-md5='extra_{$md5}' data-marker-md5='{$marker_md5}'";
					$formatted_value['inner_ids'] = " id='inner_{$line}' class='inner_h4' data-md5='inner_{$md5}' data-marker-md5='{$marker_md5}'";
					$value = implode($formatted_value);
				}
				// Process level 5 headings
				if(substr($value, 0, 5) === '#####' and substr($value, 5, 1) === ' '){
					$formatted_value = $html_div_format;
					$formatted_value['content'] = trim(substr($value, 6));
					$formatted_value['outter_ids'] = " id='outter_{$line}' class='outter_h5' data-md5='outter_{$md5}' data-marker-md5='{$marker_md5}'";
					$formatted_value['extra_ids'] = " id='extra_{$line}' class='extra_h5' data-md5='extra_{$md5}' data-marker-md5='{$marker_md5}'";
					$formatted_value['inner_ids'] = " id='inner_{$line}' class='inner_h5' data-md5='inner_{$md5}' data-marker-md5='{$marker_md5}'";
					$value = implode($formatted_value);
				}
				// Process level 6 headings
				if (substr($value, 0, 6) === '######' and substr($value, 6, 1) === ' '){
					$formatted_value = $html_div_format;
					$formatted_value['content'] = trim(substr($value, 7));
					$formatted_value['outter_ids'] = " id='outter_{$line}' class='outter_h6' data-md5='outter_{$md5}' data-marker-md5='{$marker_md5}'";
					$formatted_value['extra_ids'] = " id='extra_{$line}' class='extra_h6' data-md5='extra_{$md5}' data-marker-md5='{$marker_md5}'";
					$formatted_value['inner_ids'] = " id='inner_{$line}' class='inner_h6' data-md5='inner_{$md5}' data-marker-md5='{$marker_md5}'";
					$value = implode($formatted_value);
				}
			// Process attached subheadings
			} else if (substr($value, 0, 2) === '| '){
				$formatted_value = $html_div_format;
				$formatted_value['content'] = trim(substr($value, 2));
				$formatted_value['outter_ids'] = " id='outter_{$line}' class='outter_ah' data-md5='outter_{$md5}' data-marker-md5='{$marker_md5}'";
				$formatted_value['extra_ids'] = " id='extra_{$line}' class='extra_ah' data-md5='extra_{$md5}' data-marker-md5='{$marker_md5}'";
				$formatted_value['inner_ids'] = " id='inner_{$line}' class='inner_ah' data-md5='inner_{$md5}' data-marker-md5='{$marker_md5}'";
				$value = implode($formatted_value);
			// Add list labels
			} else if (substr(strrev($value), 0, 1) === ':' and $switch1 === FALSE){
				// Process top-level list labels
				require_once $location . '/custom_dependencies/ritchey_check_if_string_is_all_caps_i1_v1/ritchey_check_if_string_is_all_caps_i1_v1.php';
				if (ritchey_check_if_string_is_all_caps_i1_v1(substr($value, 0, 1), FALSE, FALSE, FALSE, FALSE) === TRUE){
					$formatted_value = $html_div_format;
					$formatted_value['content'] = ucwords(strtolower(substr($value, 0, -1)));
					$formatted_value['outter_ids'] = " id='outter_{$line}' class='outter_ll1' data-md5='outter_{$md5}' data-marker-md5='{$marker_md5}'";
					$formatted_value['extra_ids'] = " id='extra_{$line}' class='extra_ll1' data-md5='extra_{$md5}' data-marker-md5='{$marker_md5}'";
					$formatted_value['inner_ids'] = " id='inner_{$line}' class='inner_ll1' data-md5='inner_{$md5}' data-marker-md5='{$marker_md5}'";
					$value = implode($formatted_value);
				}
				// Process sub-level list labels
				if (ritchey_check_if_string_is_all_caps_i1_v1(substr(ltrim($value), 2, 1), FALSE, FALSE, FALSE, FALSE) === TRUE){
					$formatted_value = $html_div_format;
					$formatted_value['content'] = "<span class='list_dot'></span>" . "<span class='list_value'>" . ucwords(strtolower(substr(ltrim($value), 2, -1))) . "</span>";
					$lln = strpos($value, '-') + 2;
					$formatted_value['outter_ids'] = " id='outter_{$line}' class='outter_ll{$lln}' data-md5='outter_{$md5}' data-marker-md5='{$marker_md5}'";
					$formatted_value['extra_ids'] = " id='extra_{$line}' class='extra_ll{$lln}' data-md5='extra_{$md5}' data-marker-md5='{$marker_md5}'";
					$formatted_value['inner_ids'] = " id='inner_{$line}' class='inner_ll{$lln}' data-md5='inner_{$md5}' data-marker-md5='{$marker_md5}'";
					$value = implode($formatted_value);
				}
			// Add list entries (non-label)
			} else if (substr(ltrim($value), 0, 1) === '-' and $value !== '---' and $switch1 === FALSE){
				$formatted_value = $html_div_format;
				$formatted_value['content'] = "<span class='list_dot'></span>" . "<span class='list_value'>" . substr(ltrim($value), 2) . "</span>";
				$len = strpos($value, '-') + 2;
				$formatted_value['outter_ids'] = " id='outter_{$line}' class='outter_le{$len}' data-md5='outter_{$md5}' data-marker-md5='{$marker_md5}'";
				$formatted_value['extra_ids'] = " id='extra_{$line}' class='extra_le{$len}' data-md5='extra_{$md5}' data-marker-md5='{$marker_md5}'";
				$formatted_value['inner_ids'] = " id='inner_{$line}' class='inner_le{$len}' data-md5='inner_{$md5}' data-marker-md5='{$marker_md5}'";
				$value = implode($formatted_value);
			// Add Labels
			} else if (strpos($value, ':') !== FALSE and ctype_upper(preg_replace("/[^[:alpha:]]/", "", substr($value, 0, strpos($value, ':')))) === TRUE  and $switch1 === FALSE){
				$formatted_value = $html_div_format;
				$value = explode(':', $value, 2);
				$formatted_value['content'] = "<span class='field'>" . ucwords(strtolower($value[0])) . "</span>" . substr($value[1], 1);
				$formatted_value['outter_ids'] = " id='outter_{$line}' class='outter_f' data-md5='outter_{$md5}' data-marker-md5='{$marker_md5}'";
				$formatted_value['extra_ids'] = " id='extra_{$line}' class='extra_f' data-md5='extra_{$md5}' data-marker-md5='{$marker_md5}'";
				$formatted_value['inner_ids'] = " id='inner_{$line}' class='inner_f' data-md5='inner_{$md5}' data-marker-md5='{$marker_md5}'";
				$value = implode($formatted_value);
			// Add tags
			} else if (substr($value, 0, 1) === '[' and substr($value, -1) === ']'  and $switch1 === FALSE){
				$formatted_value = $html_div_format;
				$value = explode(']', str_replace('[', '', $value));
				$value = array_filter($value);
				foreach ($value as &$item){
					$item = '<div class="tag">' . $item . '</div>';
				}
				unset($item);
				$formatted_value['content'] = "<div class='tag_label'></div>" . implode($value);
				$formatted_value['outter_ids'] = " id='outter_{$line}' class='outter_t' data-md5='outter_{$md5}' data-marker-md5='{$marker_md5}'";
				$formatted_value['extra_ids'] = " id='extra_{$line}' class='extra_t' data-md5='extra_{$md5}' data-marker-md5='{$marker_md5}'";
				$formatted_value['inner_ids'] = " id='inner_{$line}' class='inner_t' data-md5='inner_{$md5}' data-marker-md5='{$marker_md5}'";
				$value = implode($formatted_value);
			// Add flat-lists
			} else if (strpos($value, ' | ') !== FALSE  and $switch1 === FALSE){
				$formatted_value = $html_div_format;
				$value = explode(' | ', $value);
				$value = array_filter($value);
				foreach ($value as &$item){
					$item = '<div class="fl_entry">' . $item . '</div>';
				}
				unset($item);
				$formatted_value['content'] = implode($value);
				$formatted_value['outter_ids'] = " id='outter_{$line}' class='outter_fl' data-md5='outter_{$md5}' data-marker-md5='{$marker_md5}'";
				$formatted_value['extra_ids'] = " id='extra_{$line}' class='extra_fl' data-md5='extra_{$md5}' data-marker-md5='{$marker_md5}'";
				$formatted_value['inner_ids'] = " id='inner_{$line}' class='inner_fl' data-md5='inner_{$md5}' data-marker-md5='{$marker_md5}'";
				$value = implode($formatted_value);
			// Add blockquotes hold
			} else if (trim($value) === '"'){
				if ($switch1 === 'blockquote'){
					$switch1 = FALSE;
					$flag_number = '2';
				} else if ($switch1 === FALSE){
					$switch1 = 'blockquote';
					$flag_number = '1';
				} else {
					// Do nothing
				}
				$formatted_value = $html_div_format;
				$formatted_value['content'] = '';
				$formatted_value['outter_ids'] = " id='outter_{$line}' class='outter_blockquote_flag{$flag_number} 02fc208ce1cb8a08bb5b18ed8a2b6141879d0900bd3f269bb417228f79e0c0be_no_processing' data-md5='outter_{$md5}' data-marker-md5='{$marker_md5}'";
				$formatted_value['extra_ids'] = " id='extra_{$line}' class='extra_blockquote_flag{$flag_number} 02fc208ce1cb8a08bb5b18ed8a2b6141879d0900bd3f269bb417228f79e0c0be_no_processing' data-md5='extra_{$md5}' data-marker-md5='{$marker_md5}'";
				$formatted_value['inner_ids'] = " id='inner_{$line}' class='inner_blockquote_flag{$flag_number}' data-md5='inner_{$md5}' data-marker-md5='{$marker_md5}'";
				$value = implode($formatted_value);
			// Add blockquotes 
			} else if ($switch1 === 'blockquote'){
				$formatted_value = $html_div_format;
				$formatted_value['content'] = "<div class='blockquote_wrapper'>" . $value . "</div>";
				$formatted_value['outter_ids'] = " id='outter_{$line}' class='outter_blockquote 02fc208ce1cb8a08bb5b18ed8a2b6141879d0900bd3f269bb417228f79e0c0be_no_processing' data-md5='outter_{$md5}' data-marker-md5='{$marker_md5}'";
				$formatted_value['extra_ids'] = " id='extra_{$line}' class='extra_blockquote 02fc208ce1cb8a08bb5b18ed8a2b6141879d0900bd3f269bb417228f79e0c0be_no_processing' data-md5='extra_{$md5}' data-marker-md5='{$marker_md5}'";
				$formatted_value['inner_ids'] = " id='inner_{$line}' class='inner_blockquote' data-md5='inner_{$md5}' data-marker-md5='{$marker_md5}'";
				$value = implode($formatted_value);
			// Add blockmessage hold
			} else if (trim($value) === '='){
				if ($switch1 === 'blockmessage'){
					$switch1 = FALSE;
					$flag_number = '2';
				} else if ($switch1 === FALSE){
					$switch1 = 'blockmessage';
					$flag_number = '1';
				} else {
					// Do nothing
				}
				$formatted_value = $html_div_format;
				$formatted_value['content'] = '';
				$formatted_value['outter_ids'] = " id='outter_{$line}' class='outter_blockmessage_flag{$flag_number} 02fc208ce1cb8a08bb5b18ed8a2b6141879d0900bd3f269bb417228f79e0c0be_no_processing' data-md5='outter_{$md5}' data-marker-md5='{$marker_md5}'";
				$formatted_value['extra_ids'] = " id='extra_{$line}' class='extra_blockmessage_flag{$flag_number} 02fc208ce1cb8a08bb5b18ed8a2b6141879d0900bd3f269bb417228f79e0c0be_no_processing' data-md5='extra_{$md5}' data-marker-md5='{$marker_md5}'";
				$formatted_value['inner_ids'] = " id='inner_{$line}' class='inner_blockmessage_flag{$flag_number}' data-md5='inner_{$md5}' data-marker-md5='{$marker_md5}'";
				$value = implode($formatted_value);
			// Add blockmessages
			} else if ($switch1 === 'blockmessage'){
				$formatted_value = $html_div_format;
				$formatted_value['content'] = "<div class='blockmessage_wrapper'>" . $value . "</div>";
				$formatted_value['outter_ids'] = " id='outter_{$line}' class='outter_blockmessage 02fc208ce1cb8a08bb5b18ed8a2b6141879d0900bd3f269bb417228f79e0c0be_no_processing' data-md5='outter_{$md5}' data-marker-md5='{$marker_md5}'";
				$formatted_value['extra_ids'] = " id='extra_{$line}' class='extra_blockmessage 02fc208ce1cb8a08bb5b18ed8a2b6141879d0900bd3f269bb417228f79e0c0be_no_processing' data-md5='extra_{$md5}' data-marker-md5='{$marker_md5}'";
				$formatted_value['inner_ids'] = " id='inner_{$line}' class='inner_blockmessage' data-md5='inner_{$md5}' data-marker-md5='{$marker_md5}'";
				$value = implode($formatted_value);
			// Add separator
			} else if ($value === '---'){
				$formatted_value = $html_div_format;
				$formatted_value['content'] = '';
				$formatted_value['outter_ids'] = " id='outter_{$line}' class='outter_separator' data-md5='outter_{$md5}' data-marker-md5='{$marker_md5}'";
				$formatted_value['extra_ids'] = " id='extra_{$line}' class='extra_separator' data-md5='extra_{$md5}' data-marker-md5='{$marker_md5}'";
				$formatted_value['inner_ids'] = " id='inner_{$line}' class='inner_separator' data-md5='inner_{$md5}' data-marker-md5='{$marker_md5}'";
				$value = implode($formatted_value);
			} else {
				// Add a default wrapper.
				$formatted_value = $html_div_format;
				$formatted_value['content'] = $value;
				$formatted_value['outter_ids'] = " id='outter_{$line}' class='outter_default' data-md5='outter_{$md5}' data-marker-md5='{$marker_md5}'";
				$formatted_value['extra_ids'] = " id='extra_{$line}' class='extra_default' data-md5='extra_{$md5}' data-marker-md5='{$marker_md5}'";
				$formatted_value['inner_ids'] = " id='inner_{$line}' class='inner_default' data-md5='inner_{$md5}' data-marker-md5='{$marker_md5}'";
				$value = implode($formatted_value);
			}
			#### Process sub elements. Keep in mind, they are inside elements now. Also keep in mind that holds (e.g. for blockquotes) need to be re-detected by checking for "02fc208ce1cb8a08bb5b18ed8a2b6141879d0900bd3f269bb417228f79e0c0be_no_processing" within the string.
			// Replace tabs
			if (strpos($value, "\t") !== FALSE and strpos($value, '02fc208ce1cb8a08bb5b18ed8a2b6141879d0900bd3f269bb417228f79e0c0be_no_processing') === FALSE){
				$value = preg_replace('/[\t]/', "<span class='tab'></span>", $value);
			}
			// Replace links
			if (preg_match('/\{[^\}]+\}\(https?:\/\/[^\)]+\)/', $value) === 1 and strpos($value, '02fc208ce1cb8a08bb5b18ed8a2b6141879d0900bd3f269bb417228f79e0c0be_no_processing') === FALSE) {
				$pattern = '/\{[^\}]+\}\(https?:\/\/[^\)]+\)/';
				$replacement = function ($matches) {
					$parts = explode('}(', $matches[0]);
					$matches[0] = "<a href='" . substr($parts[1], 0, -1) . "'>" . substr($parts[0], 1) . "</a>";
					return $matches[0];
				};
				$value = preg_replace_callback($pattern, $replacement, $value);
			}
			// Replace bold, italic, or underlined text
			if (preg_match('/\{[^}]+\}\((Bold|Italics|Underlined)\)/', $value) === 1 and strpos($value, '02fc208ce1cb8a08bb5b18ed8a2b6141879d0900bd3f269bb417228f79e0c0be_no_processing') === FALSE) {
				$pattern = '/\{[^}]+\}\((Bold|Italics|Underlined)\)/';
				$replacement = function ($matches) {
					$parts = explode('}(', $matches[0]);	
					$uuid = preg_replace("/[^A-Za-z0-9 ]/", '', substr($parts[1], 0, -1));
					$uuid = preg_replace("/ /", '_', $uuid);
					$uuid = trim($uuid);
					$uuid = strtolower($uuid);
					$matches[0] = "<span class='" . 'text_style_' . $uuid . "'>" . substr($parts[0], 1) . '</span>';
					return $matches[0];
				};
				$value = preg_replace_callback($pattern, $replacement, $value);
			}
			// Replace PNG Images
			if (preg_match('/\([a-z0-9_]+\.png\)/', $value) === 1 and strpos($value, '02fc208ce1cb8a08bb5b18ed8a2b6141879d0900bd3f269bb417228f79e0c0be_no_processing') === FALSE) {
				$pattern = '/\([a-z0-9_]+\.png\)/';
				$replacement = function ($matches) use ($source_file){
					$resource_location = dirname($source_file);
					$image_file = $resource_location . '/' . substr($matches[0], 1, -1);
					if (is_file($image_file) === TRUE){
						$image_data = base64_encode(file_get_contents($image_file));
						$matches[0] = "<img class='image' src='data:image/png;charset=utf-8;base64,{$image_data}'>";
					}
					return $matches[0];
				};
				$value = preg_replace_callback($pattern, $replacement, $value);
			}
			// Replace JPEG Images
			if (preg_match('/\([a-z0-9_]+\.jpg\)/', $value) === 1 and strpos($value, '02fc208ce1cb8a08bb5b18ed8a2b6141879d0900bd3f269bb417228f79e0c0be_no_processing') === FALSE) {
				$pattern = '/\([a-z0-9_]+\.jpg\)/';
				$replacement = function ($matches) use ($source_file){
					$resource_location = dirname($source_file);
					$image_file = $resource_location . '/' . substr($matches[0], 1, -1);
					if (is_file($image_file) === TRUE){
						$image_data = base64_encode(file_get_contents($image_file));
						$matches[0] = "<img class='image' src='data:image/jpeg;charset=utf-8;base64,{$image_data}'>";
					}
					return $matches[0];
				};
				$value = preg_replace_callback($pattern, $replacement, $value);
			}
			// Replace comments styled as "(COMMENT)" (e.g. "(EXPIRED)", "(PREFERRED)"). THIS COULD BE IMPROVED TO EXCLUDE ANYTHING THAT IS ONLY NUMBERS SO THAT PHONE NUMBER AREA CODES AREN'T CAPTURED.
			if (preg_match('/\([^\p{Ll}]*\)/u', $value) === 1 and strpos($value, '02fc208ce1cb8a08bb5b18ed8a2b6141879d0900bd3f269bb417228f79e0c0be_no_processing') === FALSE) {
				$pattern = '/\([^\p{Ll}]*\)/u';
				$replacement = function ($matches) {
					$uuid = preg_replace("/[^A-Za-z0-9 ]/", '', $matches[0]);
					$uuid = preg_replace("/ /", '_', $uuid);
					$uuid = trim($uuid);
					$uuid = strtolower($uuid);
					$matches[0] = "<span class='" . 'comment_' . $uuid . "'>" . ucwords(strtolower($matches[0])) . '</span>';
					return $matches[0];
				};
				$value = preg_replace_callback($pattern, $replacement, $value);
			}
		}
		unset($value);
		$data = array_filter($data);
		### Add HTML document aspects such as the top/bottom of the page, and import the CSS style data.
		$data[] = "<div class='page_heading'>&#8706; </div>";
		$data[] = "<div class='page_footer'>&#8706; </div>";
		$css = file_get_contents($css_file);
		$title = hash('sha256', implode($data));
		$part1 = <<<HEREDOC
<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{$title}</title>
<style>
{$css}
</style>
</head>
<body>
HEREDOC;
		$data = implode(PHP_EOL, $data);
		$part2 = <<<HEREDOC
</body>
</html>
HEREDOC;
		$data = $part1 . PHP_EOL . $data . PHP_EOL . $part2;
		file_put_contents($destination_file, $data);
	}
	result:
	## Display Errors
	if ($display_errors === TRUE){
		if (@empty($errors) === FALSE){
			$message = @implode(", ", $errors);
			if (function_exists('ritchey_markup_to_html_i1_v23_format_error') === FALSE){
				function ritchey_markup_to_html_i1_v23_format_error($errno, $errstr){
					echo $errstr;
				}
			}
			set_error_handler("ritchey_markup_to_html_i1_v23_format_error");
			trigger_error($message, E_USER_ERROR);
		}
	}
	## Return
	if (@empty($errors) === TRUE){
		return TRUE;
	} else {
		return FALSE;
	}
}
}
?>