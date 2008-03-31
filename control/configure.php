<?php
/**
 * change YACS main parameters (database, language, etc.)
 *
 * Use this script to change database and other essential parameters of this server.
 *
 * Access to this page is reserved to associates.
 * Moreover, the current surfer is considered as an associate in any of following situations:
 * - there is no configuration file and no switch file
 * - there is no connection to the database and no switch file
 *
 * Configuration information is saved into [code]parameters/control.include.php[/code].
 * If YACS is prevented to write to the file, it displays parameters to allow for a manual update.
 *
 * The file [code]parameters/control.include.php.bak[/code] can be used to restore
 * the active configuration before the last change.
 *
 * If the file [code]demo.flag[/code] exists, the script assumes that this instance
 * of YACS runs in demonstration mode.
 * In this mode the edit form is not displayed at all to protect database passwords.
 *
 *
 * Database parameters:
 *
 * [*] [code]database_server[/code] - database host name or IP address
 *
 * [*] [code]database_user[/code] - account to use to connect to the database
 *
 * [*] [code]database_password[/code] - to authenticate to the database server
 *
 * [*] [code]database[/code] - the name of the database to use
 *
 * [*] [code]table_prefix[/code] - the prefix for all tables used for this YACS instance
 *
 * [*] [code]users_database_server[/code] - only for user records - database host name or IP address
 *
 * [*] [code]users_database_user[/code] - only for user records - account to use to connect to the database
 *
 * [*] [code]users_database_password[/code] - only for user records - to authenticate to the database server
 *
 * [*] [code]users_database[/code] - only for user records - the name of the database to use
 *
 * [*] [code]users_table_prefix[/code] - only for user records - the prefix for the users table
 *
 *
 * System parameters:
 *
 * [*] [code]directory_mask[/code] - Default mask to be used for mkdir.
 *
 * [*] [code]file_mask[/code] - Default mask to be used for files.
 *
 * [*] [code]url_to_root[/code] - The absolute path to YACS scripts.
 * The default value is '[code]/yacs/[/code]'.
 *
 * [*] [code]preferred_language[/code] - The two-letter ISO code representing the language
 * to be used for auto-generated text, such as background e-mail message, etc.
 * Default value is '[code]en[/code]'.
 *
 * [*] [code]without_language_detection[/code] - By default YACS attempts to localize its interface
 * depending on browser data. In some situations you may prefer to turn this parameter to 'Y', and
 * to stick with the preferred language only.
 *
 * [*] [code]with_compression[/code] - By default page content is transferred 'as-is' to user agents.
 * If this parameter is set explicitly to 'Y', YACS will attempt to compress every HTML and XML content.
 * You should depart from the default mode if the HTTP service does not feature compression.
 *
 * [*] [code]without_cache[/code] - Normally YACS uses partial caching techniques to speed up page rendering.
 * However sometimes it can be useful to disable this feature.
 *
 * [*] [code]with_cron[/code] - By default YACS attempts to add background
 * processing to page rendering. If you have explicitly configured your server
 * to trigger [script]cron.php[/script] instead, for example through crontab,
 * you can set the parameter [code]with_cron[/code] to 'Y' to smooth response
 * times of YACS.
 *
 * [*] [code]cron_host[/code] - Because crontabs are shared at most hosting
 * providers, [script]cron.php[/script] believes it runs at ##localhost##, where
 * the software expects a real host name. The parameter overcomes this situation
 * and can even be set as a virtual host of your choice.
 *
 * [*] [code]with_debug[/code] - By default YACS runs in production mode.
 * However, at development site you can turn this parameter to 'Y' to get more information.
 * For example, PHP is reconfigured to display all messages, including notices and warnings.
 *
 *
 * Inbound HTTP parameters:
 *
 * [*] [code]with_ajax_comet[/code] - By default YACS maintains short-lived
 * HTTP connections, and AJAX updates rely on client-based polling cycles.
 * Set this parameter to 'Y' to actually activate the COMET architecture, and
 * to let the server propagate fresh information as soon as possible.
 * This setting will generate long-lived HTTP connections, and therefore consume
 * more system resources on server side.
 *
 * [*] [code]with_friendly_urls[/code] - By default pages are referenced using query strings
 * (e.g;, [code]articles/view.php?id=123[/code]). In numerous cases you can activate friendly URLS
 * to let surfers and spiders reference your pages more easily (e.g;, [code]articles/view.php/123[/code]).
 *
 * [*] [code]without_http_cache[/code] - By default YACS handles [code]ETag[/code] and [code]Last-Modified[/code]
 * HTTP headers to help proxies and browsers cache provided information.
 * However sometimes it can be useful to disable this feature.
 *
 *
 * Outbound HTTP parameters:
 *
 * [*] [code]without_outbound_http[/code] - By default YACS connects to other web servers.
 * Change this parameter to 'Y' to disable this feature.
 *
 * [*] [code]proxy_server[/code] - if the infrastructure requires a proxy for external HTTP requests
 *
 * [*] [code]proxy_user[/code] - the account to be used against the proxy
 *
 * [*] [code]proxy_password[/code] - authentication data
 *
 *
 * Outbound e-mail parameters:
 *
 * [*] [code]with_email[/code] - the sending of e-mail messages has to be explicitly activated.
 * By default YACS does not send messages.
 *
 * [*] [code]mail_smtp_server[/code] - host name or IP address of the server that will process our SMTP requests.
 * There is no default value.
 *
 * [*] [code]mail_encoding[/code] - either '8bit' or 'base64'.
 * The default value is 'base64'.
 *
 * [*] [code]mail_from[/code] - the account used to send messages
 * There is no default value.
 *
 * [*] [code]mail_logger_recipient[/code] - one address, or a comma-separated list of addresses,
 * that will receive event messages. There is no default value.
 *
 * [*] [code]mail_pop3_server[/code], [code]mail_pop3_user[/code],
 * and [code]mail_pop3_password[/code] - data used for POP3 authentication
 * before SMTP sending. There is no default values.
 * These are required at ovh for SMTP to take place.
 *
 * [*] [code]debug_mail[/code] - if set to 'Y',
 * save titles and recipients of sent messages into [code]temporary/debug.txt[/code]
 *
 *
 * Skin selection:
 *
 * [*] [code]skin[/code] - while YACS is able to support several skins, one of them will be privileged.
 * Default value is '[code]joi[/code]'.
 *
 *
 * Password of last resort:
 *
 * [*] [code]password_of_last_resort[/code] - A pass phrase to be authenticated as an associate.
 * This parameter cannot be set through a web form.
 * The only way to set a password of last resort is to edit manually the main
 * configuration file, [code]parameters/control.include.php[/code].
 * To suppress the password of last resort you can simply use this
 * configuration panel, which will rewrite the configuration file.
 *
 *
 * @author Bernard Paques [email]bernard.paques@bigfoot.com[/email]
 * @author GnapZ
 * @tester Jan Boen
 * @tester Kedare
 * @tester Timster
 * @reference
 * @license http://www.gnu.org/copyleft/lesser.txt GNU Lesser General Public License
 */
include_once '../shared/global.php';

// if we have changed the url to root, consider it right now
if(isset($_REQUEST['url_to_root']))
	$context['url_to_root'] = preg_replace(FORBIDDEN_CHARS_IN_URLS, '_', strip_tags($_REQUEST['url_to_root']));

// stop hackers
if(isset($_REQUEST['value']))
	$_REQUEST['value'] = preg_replace(FORBIDDEN_STRINGS_IN_PATHS, '_', strip_tags($_REQUEST['value']));

// if we are changing the skin
if(isset($_REQUEST['parameter']) && ($_REQUEST['parameter'] == 'skin') && isset($_REQUEST['value']) && Surfer::is_associate())
	$context['skin'] = 'skins/'.basename($_REQUEST['value']);

// load localized strings
i18n::bind('control');

// load the skin
load_skin('control');

// if no skin has been defined yet, we are in HTML
if(!defined('BR'))
	define('BR', '<br>');
if(!defined('EOT'))
	define('EOT', '>');

// if no configuration file or if no database
$connection = FALSE;
if(isset($context['database_server']) && isset($context['database_user']) && isset($context['database_password']) && isset($context['database']))
	$connection =& SQL::connect($context['database_server'], $context['database_user'], $context['database_password'], $context['database']);
if(!file_exists('../parameters/control.include.php') || !$connection ) {

	// consider the current surfer as an associate, but only on first installation
	if(!Surfer::is_associate() && !file_exists('../parameters/switch.on') && !file_exists('../parameters/switch.off')) {
		$fields = array();
		$fields['id'] = 1;
		$fields['nick_name'] = 'admin';
		$fields['email'] = '';
		$fields['capability'] = 'A';
		Surfer::set($fields);
		Skin::error(i18n::s('You are considered temporarily as an associate, with specific rights on this server. Please do not close your browser until the end of the configuration.'));
	}
}

// the path to this page
$context['path_bar'] = array( 'control/' => i18n::s('Control Panel') );

// the title of the page
$context['page_title'] = i18n::s('Main Configuration Panel');

// not first installation
if(class_exists('Skin') && (file_exists('../parameters/switch.on') || file_exists('../parameters/switch.off'))) {

	// do it again if necessary
	if(isset($_SERVER['REQUEST_METHOD']) && ($_SERVER['REQUEST_METHOD'] == 'POST'))
		$context['page_menu'] = array_merge($context['page_menu'], array( 'control/configure.php' => i18n::s('Edit') ));

	// back to the control panel
	$context['page_menu'] = array_merge($context['page_menu'], array( 'control/' => i18n::s('Control Panel') ));

	// select another skin visually
	$context['page_menu'] = array_merge($context['page_menu'], array( 'skins/' => i18n::s('Select another skin') ));

}

// ensure we have an associate
if(!Surfer::is_associate()) {
	Safe::header('Status: 403 Forbidden', TRUE, 403);
	Skin::error(i18n::s('You are not allowed to perform this operation.'));

	// forward to the control panel
	$menu = array('control/' => i18n::s('Back to the control panel'));
	$context['text'] .= Skin::build_list($menu, 'menu_bar');

// nothing more in demo mode
} elseif(file_exists($context['path_to_root'].'parameters/demo.flag')) {

	// remind the surfer
	$context['text'] .= '<p>'.i18n::s('This instance of YACS runs in demonstration mode. For security reasons configuration parameters cannot be displayed nor changed in this mode.')."</p>\n";

// display the input form, except if there is only one parameter to change
} elseif(isset($_SERVER['REQUEST_METHOD']) && ($_SERVER['REQUEST_METHOD'] != 'POST') && (!isset($_REQUEST['parameter']) || !isset($_REQUEST['value'])) ) {

	// the user form
	$context['text'] .= '<form method="post" action="'.$context['script_url'].'" id="main_form"><div>'."\n";

	//
	// database parameters
	//
	$context['text'] .= Skin::build_block(i18n::s('Database parameters'), 'title');
	$fields = array();

	$context['text'] .= '<p>'.sprintf(i18n::s('Following parameters may be provided by your Internet Service Provider (ISP), or by some database manager. If you manage your own server, the database should have been created before moving forward. You may check the %s file for further information.'), '<a href="../'.i18n::s('readme.txt').'">'.i18n::s('readme.txt').'</a>')."</p>\n";

	// host name
	$label = i18n::s('Database server name');
	if(!isset($context['database_server']) || !$context['database_server'])
		$context['database_server'] = 'localhost';
	$input = '<input type="text" name="database_server" size="45" value="'.encode_field($context['database_server']).'" maxlength="255" />';
	$fields[] = array($label, $input);

	// database account
	$label = i18n::s('Login name');
	if(!isset($context['database_user']) || !$context['database_user'])
		$context['database_user'] = 'root';
	$input = '<input type="text" name="database_user" size="45" value="'.encode_field($context['database_user']).'" maxlength="255" />';
	$fields[] = array($label, $input);

	// related password
	$label = i18n::s('Login password');
	if(!isset($context['database_password']))
		$context['database_password'] = '';
	$input = '<input type="password" name="database_password" size="45" value="'.encode_field($context['database_password']).'" maxlength="255" />';
	$fields[] = array($label, $input);

	// database name
	$label = i18n::s('Database name');
	if(!isset($context['database']) || !$context['database'])
		$context['database'] = 'yacs';
	$input = '<input type="text" name="database" size="45" value="'.encode_field($context['database']).'" maxlength="255" />';
	$fields[] = array($label, $input);

	// prefix for table names
	$label = i18n::s('Prefix for table names');
	if(!isset($context['table_prefix']) || !$context['table_prefix'])
		$context['table_prefix'] = 'yacs_';
	$input = '<input type="text" name="table_prefix" size="45" value="'.encode_field($context['table_prefix']).'" maxlength="255" />';
	$fields[] = array($label, $input);

	// build the form
	$context['text'] .= Skin::build_form($fields);
	$fields = array();

	//
	// separate access for user records
	//
	$text = '<p>'.i18n::s('To share user information among several YACS servers configure below parameters specific to the table of users. Else keep fields empty.')."</p>\n";

	// secondary host name
	$label = i18n::s('Database server that hosts user information');
	if(!isset($context['users_database_server']) || !$context['users_database_server'])
		$context['users_database_server'] = '';
	$input = '<input type="text" name="users_database_server" size="45" value="'.encode_field($context['users_database_server']).'" maxlength="255" />';
	$fields[] = array($label, $input);

	// secondary account
	$label = i18n::s('Login name');
	if(!isset($context['users_database_user']) || !$context['users_database_user'])
		$context['users_database_user'] = '';
	$input = '<input type="text" name="users_database_user" size="45" value="'.encode_field($context['users_database_user']).'" maxlength="255" />';
	$fields[] = array($label, $input);

	// related password
	$label = i18n::s('Login password');
	if(!isset($context['users_database_password']))
		$context['users_database_password'] = '';
	$input = '<input type="password" name="users_database_password" size="45" value="'.encode_field($context['users_database_password']).'" maxlength="255" />';
	$fields[] = array($label, $input);

	// database name
	$label = i18n::s('Name of database that contains user information');
	if(!isset($context['users_database']) || !$context['users_database'])
		$context['users_database'] = '';
	$input = '<input type="text" name="users_database" size="45" value="'.encode_field($context['users_database']).'" maxlength="255" />';
	$fields[] = array($label, $input);

	// prefix for table names
	$label = i18n::s('Prefix for the users table');
	if(!isset($context['users_table_prefix']) || !$context['users_table_prefix'])
		$context['users_table_prefix'] = '';
	$input = '<input type="text" name="users_table_prefix" size="45" value="'.encode_field($context['users_table_prefix']).'" maxlength="255" />';
	$fields[] = array($label, $input);

	// build the form
	$text .= Skin::build_form($fields);
	$fields = array();

	// in a folded box
	$context['text'] .= Skin::build_box(i18n::s('Custom storage of user information'), $text, 'folder');

	//
	// system parameters
	//
	$context['text'] .= Skin::build_block(i18n::s('System parameters'), 'title');

	// splash message
	$context['text'] .= '<p>'.i18n::s('If you do not know what following parameters mean, please use default values for safety.')."</p>\n";

	// url to root -- see shared/global.php to understand the usage of 'url_to_root_parameter'
	$label = i18n::s('Path (URL) to root directory');
	if(!isset($context['url_to_root_parameter']))
		$context['url_to_root_parameter'] = '';
	$input = '<input type="text" name="url_to_root_parameter" size="45" value="'.encode_field($context['url_to_root_parameter']).'" maxlength="255" />';
	$fields[] = array($label, $input);

	// preferred language for messages generated by YACS
	$label = i18n::s('Community language');
	if(!isset($context['preferred_language']))
		$context['preferred_language'] = 'en';
	$input = '';
	$locales = i18n::list_locales();
	foreach($locales as $locale => $text) {
		if($input)
			$input .= BR;
		$checked = '';
		if($context['preferred_language'] == $locale)
			$checked = ' checked="checked"';
		$input .= '<input type="radio" name="preferred_language" value="'.$locale.'"'.$checked.'/> '.$text;
	}
	$fields[] = array($label, $input);

	// language detection
	$label = i18n::s('Language detection');
	$input = '<input type="radio" name="without_language_detection" value="N"';
	if(!isset($context['without_language_detection']) || ($context['without_language_detection'] != 'Y'))
		$input .= ' checked="checked"';
	$input .= EOT.' '.i18n::s('Attempt to adapt the interface to the language indicated by the browser.');
	$input .= BR.'<input type="radio" name="without_language_detection" value="Y"';
	if(isset($context['without_language_detection']) && ($context['without_language_detection'] == 'Y'))
		$input .= ' checked="checked"';
	$input .= EOT.' '.i18n::s('Stick to the preferred language selected above.');
	$fields[] = array($label, $input);

	// content compression
	$label = i18n::s('Page compression');
	$input = '<input type="radio" name="with_compression" value="N"';
	if(!isset($context['with_compression']) || ($context['with_compression'] != 'Y'))
		$input .= ' checked="checked"';
	$input .= EOT.' '.i18n::s('Do not try to compress transmitted data. The web engine already does it.');
	$input .= BR.'<input type="radio" name="with_compression" value="Y"';
	if(isset($context['with_compression']) && ($context['with_compression'] == 'Y'))
		$input .= ' checked="checked"';
	$input .= EOT.' '.i18n::s('Compress content (gzip) transmitted to user agents when possible.');
	$fields[] = array($label, $input);

	// rendering cache
	$label = i18n::s('Rendering cache');
	$input = '<input type="radio" name="without_cache" value="N"';
	if(!isset($context['without_cache']) || ($context['without_cache'] != 'Y'))
		$input .= ' checked="checked"';
	$input .= EOT.' '.i18n::s('Cache computed elements to speed up rendering.');
	$input .= BR.'<input type="radio" name="without_cache" value="Y"';
	if(isset($context['without_cache']) && ($context['without_cache'] == 'Y'))
		$input .= ' checked="checked"';
	$input .= EOT.' '.i18n::s('Compute all page elements.');
	$fields[] = array($label, $input);

	// with_cron and cron_host
	$label = i18n::s('Background processing');
	$input = '<input type="radio" name="with_cron" value="N"';
	if(!isset($context['with_cron']) || ($context['with_cron'] != 'Y'))
		$input .= ' checked="checked"';
	$input .= EOT.' '.i18n::s('Add background processing at the end of page rendering.');
	$input .= BR.'<input type="radio" name="with_cron" value="Y"';
	if(isset($context['with_cron']) && ($context['with_cron'] == 'Y'))
		$input .= ' checked="checked"';
	$input .= EOT.' '.i18n::s('The server launches cron.php on its own, as virtual host').' <input type="text" name="cron_host" value="'.encode_field(isset($context['cron_host'])?$context['cron_host']:$context['host_name']).'" size="20"/>';
	$fields[] = array($label, $input);

	// file_mask and directory_mask
	$label = i18n::s('Default masks');
	$input = i18n::s('Mask for files').' <input type="text" name="file_mask" size="5" value="'.encode_field(sprintf('0%o', $context['file_mask'])).'" maxlength="5" /> '
		.i18n::s('Mask for directories').' <input type="text" name="directory_mask" size="5" value="'.encode_field(sprintf('0%o', $context['directory_mask'])).'" maxlength="5" />';
	$fields[] = array($label, $input);

	// debug
	$label = i18n::s('Verbosity');
	$input = '<input type="radio" name="with_debug" value="N"';
	if($context['with_debug'] != 'Y')
		$input .= ' checked="checked"';
	$input .= EOT.' '.i18n::s('Verbosity should be kept to a minimum (normal operation).');
	$input .= BR.'<input type="radio" name="with_debug" value="Y"';
	if($context['with_debug'] == 'Y')
		$input .= ' checked="checked"';
	$input .= EOT.' '.i18n::s('Provide as much information as possible (development server).');
	$fields[] = array($label, $input);

	// build the form
	$context['text'] .= Skin::build_form($fields);
	$fields = array();

	//
	// inbound HTTP parameters
	//

	// not on first installation
	if(file_exists('../parameters/control.include.php')) {

		$context['text'] .= Skin::build_block(i18n::s('Inbound HTTP parameters'), 'title');

		// splash message
		$context['text'] .= '<p>'.i18n::s('If you do not know what following parameters mean, please use default values for safety.')."</p>\n";

		// friendly urls
		$label = i18n::s('URL generation');
		$input = '<input type="radio" name="with_friendly_urls" value="N"';
		if($context['with_friendly_urls'] != 'Y')
			$input .= ' checked="checked"';
		$input .= EOT.' '.i18n::s('This system does not support the mapping of args in the URL.').' (<code>articles/view.php?id=123</code>)';
		$input .= BR.'<input type="radio" name="with_friendly_urls" value="Y"';
		if($context['with_friendly_urls'] == 'Y')
			$input .= ' checked="checked"';
		$input .= EOT.' '.i18n::s('Help search engines to index more pages.').' (<code>articles/view.php/123</code>)'
			.' ('.Skin::build_link('control/test.php/123/456', i18n::s('test link'), 'external').')';
		$input .= BR.'<input type="radio" name="with_friendly_urls" value="R"';
		if($context['with_friendly_urls'] == 'R')
			$input .= ' checked="checked"';
		$input .= EOT.' '.i18n::s('Rewriting rules have been activated (in <code>.htaccess</code>) to support pretty references.').' (<code>article-123</code>)'
			.' ('.Skin::build_link('rewrite_test/123', i18n::s('test link'), 'external').')';
		$fields[] = array($label, $input);

		// web cache
		$label = i18n::s('Web cache');
		$input = '<input type="radio" name="without_http_cache" value="N"';
		if(!isset($context['without_http_cache']) || ($context['without_http_cache'] != 'Y'))
			$input .= ' checked="checked"';
		$input .= EOT.' '.i18n::s('Set HTTP headers to enable private caching and to ask for every page revalidation.');
		$input .= BR.'<input type="radio" name="without_http_cache" value="Y"';
		if(isset($context['without_http_cache']) && ($context['without_http_cache'] == 'Y'))
			$input .= ' checked="checked"';
		$input .= EOT.' '.i18n::s('No cache management. Default settings of the PHP engine apply.');
		$fields[] = array($label, $input);

		// AJAX COMET
		$label = i18n::s('AJAX acceleration');
		$input = '<input type="radio" name="with_ajax_comet" value="N"';
		if(!isset($context['with_ajax_comet']) || ($context['with_ajax_comet'] != 'Y'))
			$input .= ' checked="checked"';
		$input .= EOT.' '.i18n::s('Workstations poll the server periodically to get fresh information.');
		$input .= BR.'<input type="radio" name="with_ajax_comet" value="Y"';
		if(isset($context['with_ajax_comet']) && ($context['with_ajax_comet'] == 'Y'))
			$input .= ' checked="checked"';
		$input .= EOT.' '.i18n::s('The server pushes fresh information as soon as possible, using AJAX COMET principles. This setting will require more TCP resources than the previous one.');
		$fields[] = array($label, $input);

		// build the form
		$context['text'] .= Skin::build_form($fields);
		$fields = array();

	}

	//
	// outbound HTTP parameters
	//

	// not on first installation
	if(file_exists('../parameters/control.include.php')) {

		$context['text'] .= Skin::build_block(i18n::s('Outbound HTTP parameters'), 'title');

		// without outbound http
		$label = i18n::s('Global switch');
		$input = '<input type="radio" name="without_outbound_http" value="N"';
		if(!isset($context['without_outbound_http']) || ($context['without_outbound_http'] != 'Y'))
			$input .= ' checked="checked"';
		$input .= EOT.' '.i18n::s('This server uses the web for syndication, for pings or for other activities.');
		$input .= BR.'<input type="radio" name="without_outbound_http" value="Y"';
		if(isset($context['without_outbound_http']) && ($context['without_outbound_http'] == 'Y'))
			$input .= ' checked="checked"';
		$input .= EOT.' '.i18n::s('Prevent this server to connect to others.');
		$fields[] = array($label, $input);

		// the proxy host
		$label = i18n::s('Proxy address or name');
		if(!isset($context['proxy_server']))
			$context['proxy_server'] = '';
		$input = '<input type="text" name="proxy_server" size="45" value="'.encode_field($context['proxy_server']).'" maxlength="255" />';
		$fields[] = array($label, $input);

		// the proxy user name
		$label = i18n::s('Proxy account');
		if(!isset($context['proxy_user']))
			$context['proxy_user'] = '';
		$input = '<input type="text" name="proxy_user" size="45" value="'.encode_field($context['proxy_user']).'" maxlength="255" />';
		$fields[] = array($label, $input);

		// the proxy password
		$label = i18n::s('Proxy password');
		if(!isset($context['proxy_password']))
			$context['proxy_password'] = '';
		$input = '<input type="password" name="proxy_password" size="45" value="'.encode_field($context['proxy_password']).'" maxlength="255" />';
		$fields[] = array($label, $input);

		// build the form
		$context['text'] .= Skin::build_form($fields);
		$fields = array();

	}

	//
	// outbound mail parameters
	//

	// not on first installation
	if(file_exists('../parameters/control.include.php')) {

		$context['text'] .= Skin::build_block(i18n::s('Outbound mail parameters'), 'title');

		// splash message
		$context['text'] .= '<p>'.i18n::s('If you do not know what following parameters mean, please use default values for safety.')."</p>\n";

		// with mail
		$label = i18n::s('Global switch');
		$input = '<input type="radio" name="with_email" value="N"';
		if(!isset($context['with_email']) || ($context['with_email'] != 'Y'))
			$input .= ' checked="checked"';
		$input .= EOT.' '.i18n::s('This system is not configured to send e-mail messages.');
		$input .= BR.'<input type="radio" name="with_email" value="Y"';
		if(isset($context['with_email']) && ($context['with_email'] == 'Y'))
			$input .= ' checked="checked"';
		$input .= EOT.' '.i18n::s('Use below parameters to handle electronic mail messages.');
		$fields[] = array($label, $input);

		// smtp server
		$label = i18n::s('SMTP server (if blank, use php.ini)');
		if(!isset($context['mail_smtp_server']))
			$context['mail_smtp_server'] = '';
		$input = '<input type="text" name="mail_smtp_server" size="45" value="'.encode_field($context['mail_smtp_server']).'" maxlength="255" />';
		$fields[] = array($label, $input);

		// mail encoding
		$label = i18n::s('Messages encoding');
		$input = '<input type="radio" name="mail_encoding" value="base64"';
		if(!isset($context['mail_encoding']) || ($context['mail_encoding'] != '8bit'))
			$input .= ' checked="checked"';
		$input .= EOT.' '.i18n::s('Transform messages using base64 encoding to ensure that only 7-bit ASCII entities are transmitted.');
		$input .= BR.'<input type="radio" name="mail_encoding" value="8bit"';
		if(isset($context['mail_encoding']) && ($context['mail_encoding'] == '8bit'))
			$input .= ' checked="checked"';
		$input .= EOT.' '.i18n::s('Do not transform bytes and assume proper transmission of 8-bit entities end-to-end.');
		$fields[] = array($label, $input);

		// pop3 server
		$label = i18n::s('POP3 server used for authentication');
		if(!isset($context['mail_pop3_server']))
			$context['mail_pop3_server'] = '';
		$input = '<input type="text" name="mail_pop3_server" size="45" value="'.encode_field($context['mail_pop3_server']).'" maxlength="255" />';
		$fields[] = array($label, $input);

		// pop3 user name
		$label = i18n::s('POP3 account');
		if(!isset($context['mail_pop3_user']))
			$context['mail_pop3_user'] = '';
		$input = '<input type="text" name="mail_pop3_user" size="45" value="'.encode_field($context['mail_pop3_user']).'" maxlength="255" />';
		$fields[] = array($label, $input);

		// pop3 password
		$label = i18n::s('POP3 password');
		if(!isset($context['mail_pop3_password']))
			$context['mail_pop3_password'] = '';
		$input = '<input type="password" name="mail_pop3_password" size="45" value="'.encode_field($context['mail_pop3_password']).'" maxlength="255" />';
		$fields[] = array($label, $input);

		// source address
		$label = i18n::s('Source address for electronic mail (From:)');
		if(!isset($context['mail_from']))
			$context['mail_from'] = '';
		$input = '<input type="text" name="mail_from" size="45" value="'.encode_field($context['mail_from']).'" maxlength="255" />';
		$fields[] = array($label, $input);

		// target recipients for logged events
		$label = i18n::s('Recipients of system events');
		if(!isset($context['mail_logger_recipient']))
			$context['mail_logger_recipient'] = '';
		$input = '<input type="text" name="mail_logger_recipient" size="45" value="'.encode_field($context['mail_logger_recipient']).'" maxlength="255" />';
		$fields[] = array($label, $input);

		// debug mail
		$label = i18n::s('Debug mail services');
		$checked = '';
		if(isset($context['debug_mail']) && ($context['debug_mail'] == 'Y'))
			$checked = ' checked="checked" ';
		$input = '<input type="checkbox" name="debug_mail" value="Y" '.$checked.'/> '.i18n::s('List messages sent electronically in the file temporary/debug.txt. Use this option only for troubleshooting.');
		$fields[] = array($label, $input);

		// build the form
		$context['text'] .= Skin::build_form($fields);
		$fields = array();

	}

	//
	// about the skin
	//

	// on first installation, use the default skin
	if(!file_exists('../parameters/control.include.php'))
		$context['text'] .= '<input type="hidden" name="skin" value="'.encode_field($context['skin']).'" >'."\n";

	// else let the user select his preferred skin
	else {

		$context['text'] .= Skin::build_block(i18n::s('Skin of the server'), 'title');

		$context['text'] .= '<p>'.i18n::s('Please select a skin. This can be changed and configured later on.')."</p>\n";

		// list skins available on this system
		$context['text'] .= '<p>';
		if($dir = Safe::opendir("../skins")) {

			// valid skins have a template.php file
			while(($file = Safe::readdir($dir)) !== FALSE) {
				if($file == '.' || $file == '..' || !is_dir('../skins/'.$file))
					continue;
				if(!file_exists('../skins/'.$file.'/template.php'))
					continue;

				// set a default skin
				if(!$context['skin'])
					$context['skin'] = 'skins/'.$file;

				$checked = '';
				if($context['skin'] == 'skins/'.$file)
					$checked = ' checked="checked"';
				$skins[] = '<input type="radio" name="skin" value="skins/'.encode_field($file).'"'.$checked.EOT.' '.$file.BR."\n";

			}
			Safe::closedir($dir);
			if(@count($skins)) {
				sort($skins);
				foreach($skins as $skin)
					$context['text'] .= $skin;
			}
		}
		$context['text'] .= '</p>';

	}

	//
	// the submit button
	//
	$context['text'] .= Skin::build_box(i18n::s('Save parameters'), '<p>'.Skin::build_submit_button(i18n::s('Save'), i18n::s('Press [s] to submit data'), 's', 'confirmed').'</p>', 'section');

	// end of the form
	$context['text'] .= '</div></form>';

// save updated parameters
} else {

	// there is only one parameter to change
	if(isset($_REQUEST['parameter']) && isset($_REQUEST['value'])) {

		// set it
		$context[ $_REQUEST['parameter'] ] = $_REQUEST['value'];

		// return to the skins index if we are coming from there
		if($_REQUEST['parameter'] == 'skin') {
			$context['followup_label'] = i18n::s('All skins');
			$context['followup_link'] = 'skins/';
		}

		// move the updated configuration to the request
		$_REQUEST = $context;
	}

	// backup the old version
	Safe::unlink($context['path_to_root'].'parameters/control.include.php.bak');
	Safe::rename($context['path_to_root'].'parameters/control.include.php', $context['path_to_root'].'parameters/control.include.php.bak');

	// ensure we have default values
	if(!isset($_REQUEST['debug_mail']))
		$_REQUEST['debug_mail'] = 'N';

	// masks are octal
	if($_REQUEST['directory_mask'] < '0700')
		$_REQUEST['directory_mask'] = '0755';
	if($_REQUEST['file_mask'] < '0600')
		$_REQUEST['file_mask'] = '0644';

	// build the new configuration file
	$content = '<?php'."\n"
		.'// This file has been created by the configuration script control/configure.php'."\n"
		.'// on '.gmdate("F j, Y, g:i a").' GMT, for '.Surfer::get_name().'. Please do not modify it manually.'."\n"
		.'global $context;'."\n";
	if(isset($_REQUEST['cron_host']))
		$content .= '$context[\'cron_host\']=\''.addcslashes($_REQUEST['cron_host'], "\\'")."';\n";
	if(isset($_REQUEST['database_server']))
		$content .= '$context[\'database_server\']=\''.addcslashes($_REQUEST['database_server'], "\\'")."';\n";
	if(isset($_REQUEST['database_user']))
		$content .= '$context[\'database_user\']=\''.addcslashes($_REQUEST['database_user'], "\\'")."';\n";
	if(isset($_REQUEST['database_password']))
		$content .= '$context[\'database_password\']=\''.addcslashes($_REQUEST['database_password'], "\\'")."';\n";
	if(isset($_REQUEST['database']))
		$content .= '$context[\'database\']=\''.addcslashes($_REQUEST['database'], "\\'")."';\n";
	$content .= '$context[\'directory_mask\']='.$_REQUEST['directory_mask'].";\n";
	$content .= '$context[\'file_mask\']='.$_REQUEST['file_mask'].";\n";
	if(isset($_REQUEST['table_prefix']))
		$content .= '$context[\'table_prefix\']=\''.addcslashes($_REQUEST['table_prefix'], "\\'")."';\n";
	if(isset($_REQUEST['users_database_server']))
		$content .= '$context[\'users_database_server\']=\''.addcslashes($_REQUEST['users_database_server'], "\\'")."';\n";
	if(isset($_REQUEST['users_database_user']))
		$content .= '$context[\'users_database_user\']=\''.addcslashes($_REQUEST['users_database_user'], "\\'")."';\n";
	if(isset($_REQUEST['users_database_password']))
		$content .= '$context[\'users_database_password\']=\''.addcslashes($_REQUEST['users_database_password'], "\\'")."';\n";
	if(isset($_REQUEST['users_database']))
		$content .= '$context[\'users_database\']=\''.addcslashes($_REQUEST['users_database'], "\\'")."';\n";
	if(isset($_REQUEST['users_table_prefix']))
		$content .= '$context[\'users_table_prefix\']=\''.addcslashes($_REQUEST['users_table_prefix'], "\\'")."';\n";
	if(isset($_REQUEST['mail_smtp_server']))
		$content .= '$context[\'mail_smtp_server\']=\''.addcslashes($_REQUEST['mail_smtp_server'], "\\'")."';\n";
	if(isset($_REQUEST['mail_encoding']))
		$content .= '$context[\'mail_encoding\']=\''.addcslashes($_REQUEST['mail_encoding'], "\\'")."';\n";
	if(isset($_REQUEST['mail_from']))
		$content .= '$context[\'mail_from\']=\''.addcslashes($_REQUEST['mail_from'], "\\'")."';\n";
	if(isset($_REQUEST['mail_logger_recipient']))
		$content .= '$context[\'mail_logger_recipient\']=\''.addcslashes($_REQUEST['mail_logger_recipient'], "\\'")."';\n";
	if(isset($_REQUEST['mail_pop3_server']))
		$content .= '$context[\'mail_pop3_server\']=\''.addcslashes($_REQUEST['mail_pop3_server'], "\\'")."';\n";
	if(isset($_REQUEST['mail_pop3_user']))
		$content .= '$context[\'mail_pop3_user\']=\''.addcslashes($_REQUEST['mail_pop3_user'], "\\'")."';\n";
	if(isset($_REQUEST['mail_pop3_password']))
		$content .= '$context[\'mail_pop3_password\']=\''.addcslashes($_REQUEST['mail_pop3_password'], "\\'")."';\n";
	if(isset($_REQUEST['proxy_server']))
		$content .= '$context[\'proxy_server\']=\''.addcslashes($_REQUEST['proxy_server'], "\\'")."';\n";
	if(isset($_REQUEST['proxy_user']))
		$content .= '$context[\'proxy_user\']=\''.addcslashes($_REQUEST['proxy_user'], "\\'")."';\n";
	if(isset($_REQUEST['proxy_password']))
		$content .= '$context[\'proxy_password\']=\''.addcslashes($_REQUEST['proxy_password'], "\\'")."';\n";
	if(isset($_REQUEST['debug_mail']))
		$content .= '$context[\'debug_mail\']=\''.addcslashes($_REQUEST['debug_mail'], "\\'")."';\n";
	if(isset($_REQUEST['preferred_language']))
		$content .= '$context[\'preferred_language\']=\''.addcslashes($_REQUEST['preferred_language'], "\\'")."';\n";
	if(isset($_REQUEST['skin']))
		$content .= '$context[\'skin\']=\''.addcslashes($_REQUEST['skin'], "\\'")."';\n";
	if(isset($_REQUEST['url_to_root_parameter']))
		$content .= '$context[\'url_to_root\']=\''.addcslashes($_REQUEST['url_to_root_parameter'], "\\'")."';\n";
	if(isset($_REQUEST['with_ajax_comet']))
		$content .= '$context[\'with_ajax_comet\']=\''.addcslashes($_REQUEST['with_ajax_comet'], "\\'")."';\n";
	if(isset($_REQUEST['with_compression']))
		$content .= '$context[\'with_compression\']=\''.addcslashes($_REQUEST['with_compression'], "\\'")."';\n";
	if(isset($_REQUEST['with_cron']))
		$content .= '$context[\'with_cron\']=\''.addcslashes($_REQUEST['with_cron'], "\\'")."';\n";
	if(isset($_REQUEST['with_debug']))
		$content .= '$context[\'with_debug\']=\''.addcslashes($_REQUEST['with_debug'], "\\'")."';\n";
	if(isset($_REQUEST['with_email']))
		$content .= '$context[\'with_email\']=\''.addcslashes($_REQUEST['with_email'], "\\'")."';\n";
	if(isset($_REQUEST['with_friendly_urls']))
		$content .= '$context[\'with_friendly_urls\']=\''.addcslashes($_REQUEST['with_friendly_urls'], "\\'")."';\n";
	if(isset($_REQUEST['without_cache']))
		$content .= '$context[\'without_cache\']=\''.addcslashes($_REQUEST['without_cache'], "\\'")."';\n";
	if(isset($_REQUEST['without_http_cache']))
		$content .= '$context[\'without_http_cache\']=\''.addcslashes($_REQUEST['without_http_cache'], "\\'")."';\n";
	if(isset($_REQUEST['without_language_detection']))
		$content .= '$context[\'without_language_detection\']=\''.addcslashes($_REQUEST['without_language_detection'], "\\'")."';\n";
	if(isset($_REQUEST['without_outbound_http']))
		$content .= '$context[\'without_outbound_http\']=\''.addcslashes($_REQUEST['without_outbound_http'], "\\'")."';\n";
	$content .= '?>'."\n";

	// silently attempt to create the database if it does not exist
	if($handle =& SQL::connect($_REQUEST['database_server'], $_REQUEST['database_user'], $_REQUEST['database_password'], $_REQUEST['database'])) {
		$query = 'CREATE DATABASE IF NOT EXISTS '.SQL::escape($_REQUEST['database']);
		SQL::query($query, TRUE);
	}

	// alert the end user if we are not able to connect to the database
	if(!$handle =& SQL::connect($_REQUEST['database_server'], $_REQUEST['database_user'], $_REQUEST['database_password'], $_REQUEST['database'])) {

		Skin::error(i18n::s('ERROR: Unsuccessful connection to the database. Please check lines below and <a href="configure.php">edit parameters</a> again.'));

	// update the parameters file
	} elseif(!Safe::file_put_contents('parameters/control.include.php', $content)) {

		Skin::error(sprintf(i18n::s('ERROR: Impossible to write to the file %s. The configuration has not been saved.'), 'parameters/control.include.php'));

		// allow for a manual update
		$context['text'] .= '<p style="text-decoration: blink;">'.sprintf(i18n::s('To actually change the configuration, please copy and paste following lines by yourself in file %s.'), 'parameters/control.include.php')."</p>\n";

	// job done
	} else {

		$context['text'] .= '<p>'.sprintf(i18n::s('The following configuration has been saved into the file %s.'), 'parameters/control.include.php')."</p>\n";

		// first installation
		if(!file_exists('../parameters/switch.on') && !file_exists('../parameters/switch.off'))
			$context['text'] .= '<p>'.i18n::s('Review provided information if you wish, then click on the button at the bottom of the page to move forward.')."</a></p>\n";

		// purge the cache
		Cache::clear();

		// remember the change
		$label = sprintf(i18n::c('%s has been updated'), 'parameters/control.include.php');
		$description = $context['url_to_home'].$context['url_to_root'].'control/configure.php';
		Logger::remember('control/configure.php', $label, $description);

	}

	// display updated parameters
	if(is_callable(array('skin', 'build_box')))
		$context['text'] .= Skin::build_box(i18n::s('Configuration parameters'), Safe::highlight_string($content), 'folder');
	else
		$context['text'] .= Safe::highlight_string($content);

	// first installation
	if(!file_exists('../parameters/switch.on') && !file_exists('../parameters/switch.off')) {

		// some css files may have to be slightly updated
		if(($context['url_to_root_parameter'] != '/yacs/') && ($skins = Safe::opendir('../skins'))) {

			// valid skins have a template.php file
			while(($skin = Safe::readdir($skins)) !== FALSE) {
				if($skin == '.' || $skin == '..' || !is_dir('../skins/'.$skin))
					continue;
				if(!file_exists('../skins/'.$skin.'/template.php'))
					continue;

				// look for css files
				if($files = Safe::opendir('../skins/'.$skin)) {
					while(($file = Safe::readdir($files)) !== FALSE) {
						if(!preg_match('/\.css$/i', $file))
							continue;

						// change this css file
						if($content = Safe::file_get_contents('../skins/'.$skin.'/'.$file)) {
							$content = str_replace('/yacs/', $context['url_to_root_parameter'], $content);
							Safe::file_put_contents('skins/'.$skin.'/'.$file, $content);
						}
					}
					Safe::closedir($files);
				}
			}
			Safe::closedir($skins);
		}

		// look for software extensions
		$context['text'] .= '<form method="get" action="scan.php" id="main_form">'."\n"
			.'<p class="assistant_bar">'.Skin::build_submit_button(i18n::s('Look for software extensions'), NULL, 's', 'confirmed').'</p>'."\n"
			.'</form>'."\n";

		// this may take several minutes
		$context['text'] .= '<p>'.i18n::s('When you will click on the button the server will be immediately requested to proceed. However, because of the so many things to do on the back-end, you may have to wait for minutes before getting a response displayed. Thank you for your patience.')."</p>\n";

	// the followup link, if any
	} elseif(isset($context['followup_link'])) {

		// ensure we have a label
		if(!isset($context['followup_label']))
			$context['followup_label'] = i18n::s('Next step');

		$context['text'] .= '<form method="get" action="'.$context['url_to_root'].$context['followup_link'].'" id="main_form">'."\n"
			.'<p class="assistant_bar">'.Skin::build_submit_button($context['followup_label']).'</p>'."\n"
			.'</form>'."\n";

	// ordinary follow-up commands
	} else {

		// what's next?
		$context['text'] .= '<p>'.i18n::s('What do you want to do now?')."</p>\n";

		// follow-up menu
		$menu = array();

		// offer to change it again
		$menu = array_merge($menu, array( 'control/configure.php' => i18n::s('Configure again') ));

		// back to the control panel
		$menu = array_merge($menu, array( 'control/' => i18n::s('Go to the Control Panel') ));

		// display follow-up commands
		$context['text'] .= Skin::build_list($menu, 'menu_bar');

	}
}

// render the skin
render_skin();

?>