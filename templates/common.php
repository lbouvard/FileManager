<?php
/**
 * Common information used on all clients templates.
 * Avoids the need to define all of this when creating a new template.
 *
 * @package		ProjectSend
 * @subpackage	Templates
 */

/**
 * Since the header.php file is shared between the back-end and the
 * templates, it's necessary to define the allowed levels, or else
 * the files list will not be available.
 */
$allowed_levels = array(9,8,7,0);

/**
 * Define a variable that will tell header.php if session_start()
 * needs to be called or not (since it is also called from
 * session_check.php
 */
$is_template = true;

/**
 * Loads a language file from the current template folder based on
 * the system options.
 */
$lang = SITE_LANG;
if(!isset($ld)) { $ld = 'cftp_admin'; }
require_once(ROOT_DIR.'/includes/classes/i18n.php');
I18n::LoadDomain(ROOT_DIR."/templates/".TEMPLATE_USE."/lang/{$lang}.mo", $ld);

$this_template = BASE_URI.'templates/'.TEMPLATE_USE.'/';

include_once(ROOT_DIR.'/templates/session_check.php');

/**
 * URI to the default template CSS file.
 */
$this_template_css = BASE_URI.'templates/'.TEMPLATE_USE.'/main.css';

$database->MySQLDB();

/**
 * Get all the client's information
 */
$client_info = get_client_by_username($this_user);

/**
 * Get the list of different groups the client belongs to.
 */
$sql_groups = $database->query("SELECT DISTINCT group_id FROM tbl_members WHERE client_id='".$client_info['id']."'");
$count_groups = mysql_num_rows($sql_groups);
if ($count_groups > 0) {
	while($row_groups = mysql_fetch_array($sql_groups)) {
		$groups_ids[] = $row_groups["group_id"];
	}
	$found_groups = implode(',',$groups_ids);
}

/**
 * Define the arrays so they can't be empty
 */
$found_all_files_array	= array();
$found_own_files_temp	= array();
$found_group_files_temp	= array();

/**
 * Get the client's own files
 * Construct the query first.
 */
$files_query = "SELECT id, file_id, client_id, group_id FROM tbl_files_relations WHERE (client_id = '".$client_info['id']."'";
if (!empty($found_groups)) {
	$files_query .= " OR group_id IN ($found_groups)";
}
$files_query .= ") AND hidden = '0'";

$files_sql = $database->query($files_query);
while ($row_files = mysql_fetch_array($files_sql)) {
	if (!is_null($row_files['client_id'])) {
		$found_all_files_array[]	= $row_files['file_id'];
		$found_own_files_temp[]		= $row_files['file_id'];
	}
	if (!is_null($row_files['group_id'])) {
		$found_all_files_array[]	= $row_files['file_id'];
		$found_group_files_temp[]	= $row_files['file_id'];
	}
}

$found_own_files_ids	= (!empty($found_own_files_temp)) ? implode(',', array_unique($found_own_files_temp)) : '';
$found_group_files_ids	= (!empty($found_group_files_temp)) ? implode(',', array_unique($found_group_files_temp)) : '';



/** Create the files list */
$my_files = array();

if (!empty($found_own_files_ids) || !empty($found_group_files_ids)) {
	$f = 0;
	$ids_to_search = implode(',', array_unique($found_all_files_array));
	$files_query = "SELECT * FROM tbl_files WHERE id IN ($ids_to_search)";

	/** Add the search terms */	
	if(isset($_POST['search']) && !empty($_POST['search'])) {
		$search_terms		= mysql_real_escape_string($_POST['search']);
		$files_query		.= " AND (filename LIKE '%$search_terms%' OR description LIKE '%$search_terms%')";
		$no_results_error	= 'search';
	}
	
	$sql_files = $database->query($files_query);
	while($data = mysql_fetch_array($sql_files)) {

		$add_file	= true;
		$expired	= false;

		/** Does it expire? */
		if ($data['expires'] == '1') {
			if (time() > strtotime($data['expiry_date'])) {
				if (EXPIRED_FILES_HIDE == '1') {
					$add_file = false;
				}
				$expired = true;
			}
		}

		/** Make the list of files */
		if ($add_file == true) {
			/*
			if (in_array($data['id'], $found_own_files_temp)) {
				$origin = 'own';
			}
			if (in_array($data['id'], $found_group_files_temp)) {
				$origin = 'group';
			}
			*/
			$my_files[$f] = array(
								//'origin'		=> $origin,
								'id'			=> $data['id'],
								'url'			=> $data['url'],
								'name'			=> $data['filename'],
								'description'	=> $data['description'],
								'timestamp'		=> $data['timestamp'],
								'expired'		=> $expired,
							);
			$f++;
		}
	}
	
}

// DEBUG
//echo '<pre>'; print_r($my_files); echo '</pre>';


/** Get the url for the logo from "Branding" */
$logo_file_info = generate_logo_url();
?>