<?php
/**
 * ProjectSend system constants
 *
 * This file includes the most basic system options that cannot be
 * changed through the web interface, such as the version number,
 * php directives and the user and password length values.
 *
 * @package ProjectSend 
 * @subpackage Core
 */

/**
 * Current version.
 * Updated only when releasing a new downloadable complete version.
 */
define('CURRENT_VERSION', 'r582');

/**
 * Fix for including external files when on HTTPS.
 * Contribution by Scott Wright on
 * http://code.google.com/p/clients-oriented-ftp/issues/detail?id=230
 */
define('PROTOCOL', empty($_SERVER['HTTPS'])? 'http' : 'https');

/**
 * Turn off reporting of PHP errors, warnings and notices.
 * On a development environment, it should be set to E_ALL for
 * complete debugging.
 *
 * @link http://www.php.net/manual/en/function.error-reporting.php
 */
error_reporting(0);

define('GLOBAL_TIME_LIMIT', 240*60);
define('UPLOAD_TIME_LIMIT', 120*60);
@set_time_limit(GLOBAL_TIME_LIMIT);

/**
 * Define the RSS url to use on the home news list.
 */
define('NEWS_FEED_URI','http://www.projectsend.org/feed/');

/**
 * Define the Feed from where to take the latest version
 * number.
 */
define('UPDATES_FEED_URI','http://projectsend.org/updates/versions.xml');

/**
 * Include the personal configuration file
 * It must be created before installing ProjectSend.
 *
 * @see sys.config.sample.php
 */
if(file_exists(ROOT_DIR.'/includes/sys.config.php')) {
	include(ROOT_DIR.'/includes/sys.config.php');
}
else {
	echo '<h1>Missing a required file</h1>';
	echo "<p>The system couldn't find the configuration file <strong>sys.config.php</strong> that should be located on the <strong>includes</strong> folder.</p>
	<p>This file contains the database connection information, as well as the language and other important settings.</p>
	<p>If this is the first time you are trying to run ProjectSend, you can edit the sample file <strong>includes/sys.config.sample.php</strong> to create your own configuration information.<br />
		Then make sure to rename it to sys.config.php</p>";
	exit;
}

/**
 * Define the tables names
 */
if (!defined('TABLES_PREFIX')) {
	define('TABLES_PREFIX', 'tbl_');
}
define('TABLE_FILES', TABLES_PREFIX . 'files');
define('TABLE_FILES_RELATIONS', TABLES_PREFIX . 'files_relations');
define('TABLE_NOTIFICATIONS', TABLES_PREFIX . 'notifications');
define('TABLE_OPTIONS', TABLES_PREFIX . 'options');
define('TABLE_USERS', TABLES_PREFIX . 'users');
define('TABLE_GROUPS', TABLES_PREFIX . 'groups');
define('TABLE_MEMBERS', TABLES_PREFIX . 'members');
define('TABLE_FOLDERS', TABLES_PREFIX . 'folders');
define('TABLE_LOG', TABLES_PREFIX . 'actions_log');
$current_tables = array(TABLE_FILES,TABLE_OPTIONS,TABLE_USERS);
//$current_tables = array(TABLE_FILES,TABLE_FILES_RELATIONS,TABLE_OPTIONS,TABLE_USERS,TABLE_GROUPS,TABLE_MEMBERS,TABLE_FOLDERS,TABLE_LOG);

/**
 * This values affect both validation methods (client and server side)
 * and also the maxlength value of the form fields.
 */
define('MIN_USER_CHARS', 5);
define('MAX_USER_CHARS', 60);
define('MIN_PASS_CHARS', 5);
define('MAX_PASS_CHARS', 60);

/*
 * Cookie expiration time (in seconds).
 * Set by default to 30 days (60*60*24*30).
 */
define('COOKIE_EXP_TIME', 60*60*24*30);

/**
 * Define the folder where uploaded files will reside
 */
define('UPLOADED_FILES_FOLDER', ROOT_DIR.'/upload/files/');
define('UPLOADED_FILES_URL', '/upload/files/');

/**
 * Define the folder where the uploaded files are stored before
 * being assigned to any client.
 *
 * Also, this is the folder where files are searched for when
 * using the Import from FTP feature.
 *
 * @ Deprecated
 */
define('USER_UPLOADS_TEMP_FOLDER', ROOT_DIR.'/upload/temp');
define('CLIENT_UPLOADS_TEMP_FOLDER', ROOT_DIR.'/upload/temp');

/**
 * Define the system name, and the information that will be used
 * on the footer blocks.
 *
 */
define('SYSTEM_URI','http://www.projectsend.org/');
define('SYSTEM_URI_LABEL','ProjectSend on Google Code');
define('DONATIONS_URL','http://www.projectsend.org/donations/');
/** Previously cFTP */
define('SYSTEM_NAME','ProjectSend');

define('LOGO_FOLDER',ROOT_DIR.'/img/custom/logo/');
define('LOGO_THUMB_FOLDER',ROOT_DIR.'/img/custom/thumbs/');

/**
 * Current system language
 *
 * @see sys.config.sample.php
 */
$lang = SITE_LANG;
define('I18N_DEFAULT_DOMAIN', 'cftp_admin');
require_once(ROOT_DIR.'/includes/classes/i18n.php');
I18n::LoadDomain(ROOT_DIR."/lang/{$lang}.mo", 'cftp_admin' );

/** System User Roles names */
$user_role_9_name = __('System Administrator','cftp_admin');
$user_role_8_name = __('Account Manager','cftp_admin');
$user_role_7_name = __('Uploader','cftp_admin');
$user_role_0_name = __('Client','cftp_admin');
define('USER_ROLE_LVL_9', $user_role_9_name);
define('USER_ROLE_LVL_8', $user_role_8_name);
define('USER_ROLE_LVL_7', $user_role_7_name);
define('USER_ROLE_LVL_0', $user_role_0_name);

/** phpass */
define('HASH_COST_LOG2', 8);
define('HASH_PORTABLE', false);
?>