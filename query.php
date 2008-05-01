<?php
/**
 * send a query
 *
 * This script is to be used by anybody, including anonymous surfers,
 * to submit a query to the webmaster.
 *
 * What it actually does is to post an article into the '[code]queries[/code]' section.
 * Therefore, queries are ordinary articles to be handled by associates.
 *
 * On query submission:
 * - The web page displayed to the surfer displays a special link to bookmark the query page.
 * - An e-mail message is sent to the form submitter, for further reference
 * - A message is logged, site admins being notified of the query by e-mail
 *
 * For anonymous surfers, some user data is saved inside the page itself, including:
 * - surfer name
 * - surfer mail address
 *
 * On subsequent access to the query page, using page handle, these data is restored to surfer environment.
 * With this setup, anonymous surfers may interact with a given web page without registering first.
 *
 * YACS attempts to stop robots by generating a random string and by asking user to type it.
 *
 * @author Bernard Paques [email]bernard.paques@bigfoot.com[/email]
 * @tester fw_crocodile
 * @reference
 * @license http://www.gnu.org/copyleft/lesser.txt GNU Lesser General Public License
 */

// common definitions and initial processing
include_once 'shared/global.php';

// do not always show the edition form
$with_form = FALSE;

// load localized strings
i18n::bind('root');

// load the skin
load_skin('query');

// the title of the page
$context['page_title'] = i18n::s('We are here to help');

// post a new query
if(isset($_SERVER['REQUEST_METHOD']) && ($_SERVER['REQUEST_METHOD'] == 'POST')) {

	// protect from hackers
	if(isset($_REQUEST['edit_name']))
		$_REQUEST['edit_name'] = preg_replace(FORBIDDEN_CHARS_IN_NAMES, '_', $_REQUEST['edit_name']);
	if(isset($_REQUEST['edit_address']))
		$_REQUEST['edit_address'] = preg_replace(FORBIDDEN_CHARS_IN_URLS, '_', $_REQUEST['edit_address']);

	// track anonymous surfers
	Surfer::track($_REQUEST);

	// this is the exact copy of what end users has typed
	$item = $_REQUEST;

	// get a section for queries
	if(!$anchor = Anchors::get('section:queries')) {
		$fields = array();
		$fields['nick_name'] = 'queries';
		$fields['title'] =& i18n::c('Queries');
		$fields['introduction'] =& i18n::c('Submitted to the webmaster by any surfers');
		$fields['description'] =& i18n::c('<p>This section has been created automatically on query submission. It\'s aiming to capture feedback directly from surfers. It is highly recommended to delete pages below after their processing. Of course you can edit submitted queries to assign them to other sections if necessary.</p>');
		$fields['locked'] = 'Y'; // no direct contributions
		$fields['active_set'] = 'N'; // for associates only
		$fields['home_panel'] = 'none'; // content is not pushed at the front page
		$fields['index_map'] = 'N'; // this is a special section
		$fields['sections_layout'] = 'none'; // prevent creation of sub-sections

		// reference the new section
		if($new_id = Sections::post($fields)) {
			$context['text'] .= '<p>'.sprintf(i18n::s('A section \'%s\' has been created.'), $fields['nick_name'])."</p>\n";

			$anchor = Anchors::get('section:'.$new_id);
		}
	}
	$_REQUEST['anchor'] = $anchor->get_reference();

	// from form fields to record columns
	if(!isset($_REQUEST['edit_id']))
		$_REQUEST['edit_id']	= Surfer::get_id();
	$_REQUEST['create_address'] = $_REQUEST['edit_address'];
	$_REQUEST['create_name'] = $_REQUEST['edit_name'];
	if(!$_REQUEST['create_name'])
		$_REQUEST['create_name'] = $_REQUEST['create_address'];
	if(!$_REQUEST['create_name'])
		$_REQUEST['create_name'] =& i18n::c('(anonymous)');

	// always auto-publish queries
	$_REQUEST['publish_date']	= gmstrftime('%Y-%m-%d %H:%M:%S');
	if(isset($_REQUEST['edit_id']))
		$_REQUEST['publish_id'] 	= $_REQUEST['edit_id'];
	$_REQUEST['publish_address'] = $_REQUEST['edit_address'];
	$_REQUEST['publish_name']	= $_REQUEST['edit_name'];

	// show e-mail address of anonymous surfer
	if($_REQUEST['edit_address'] && !Surfer::is_logged())
		$_REQUEST['description'] = '<p>'.sprintf(i18n::c('Sent by %s'), '[email='.($_REQUEST['edit_name']?$_REQUEST['edit_name']:i18n::c('e-mail')).']'.$_REQUEST['edit_address'].'[/email]')."</p>\n"
			.$_REQUEST['description'];

	// stop robots
	if(Surfer::may_be_a_robot()) {
		Skin::error(i18n::s('Please prove you are not a robot.'));
		$with_form = TRUE;

	// display the form on error
	} elseif(!$query_id = Articles::post($_REQUEST)) {
		$with_form = TRUE;

	// post-processing
	} else {

		// message to the query poster
		$context['page_title'] = i18n::s('Your query has been registered');

		// use the secret handle to access the query
		$link = '';
		$status = '';
		if(($item =& Articles::get($query_id)) && $item['handle']) {

			// build credentials --see users/login.php
			$credentials = array();
			$credentials[0] = 'edit';
			$credentials[1] = 'article:'.$item['id'];
			$credentials[2] = sprintf('%u', crc32($item['create_name'].':'.$item['handle']));

			// the secret link
			$link = $context['url_to_home'].$context['url_to_root'].Users::get_url($credentials, 'credentials');

			$status = i18n::s('<p>You can check the status of your query at the following address:</p>')
				.'<p>'.Skin::build_link($link, $link, 'basic', i18n::s('The permanent address for your query')).'</p>';

		}

		$context['text'] .= i18n::s('<p>Your query will now be reviewed by one of the associates of this community. It is likely that this will be done within the next 24 hours at the latest.</p>');
		$context['text'] .= $status;

		// follow-up commands
		$context['text'] .= '<p>'.i18n::s('Where do you want to go now?').'</p>';
		$menu = array();
		$menu = array_merge($menu, array($context['url_to_root'] => i18n::s('Front page')));
		$menu = array_merge($menu, array('articles/' => i18n::s('All pages')));
		$menu = array_merge($menu, array('sections/' => i18n::s('Site map')));
		$menu = array_merge($menu, array('search.php' => i18n::s('Search')));
		$menu = array_merge($menu, array('help.php' => i18n::s('Help index')));
		$context['text'] .= Skin::build_list($menu, 'menu_bar');

		// send a confirmation message to the surfer
		if(isset($_REQUEST['edit_address']) && preg_match('/.+@.+/', $_REQUEST['edit_address']) && $link) {

			// message recipient
			$to = $_REQUEST['edit_address'];

			// message subject
			$subject = sprintf(i18n::s('Your query: %s'), strip_tags($_REQUEST['title']));

			// message body
			$message = sprintf(i18n::s("Your query will now be reviewed by one of the associates of this community. It is likely that this will be done within the next 24 hours at the latest.\n\nYou can check the status of your query at the following address:\n\n%s\n\nWe would like to thank you for your interest in our web site."), $link);

			// actual post - don't stop on error
			include_once $context['path_to_root'].'shared/mailer.php';
			Mailer::notify($to, $subject, $message);

		}

		// touch the related anchor
		if(is_object($anchor) && isset($item['id']))
			$anchor->touch('article:create', $item['id'], TRUE);

		// get the article back
		$article = Anchors::get('article:'.$query_id);

		// log the query submission
		if(is_object($article)) {
			$label = sprintf(i18n::c('New query: %s'), strip_tags($article->get_title()));
			$description = $context['url_to_home'].$context['url_to_root'].$article->get_url()
				."\n\n".$article->get_teaser('basic');
			Logger::notify('query.php', $label, $description);
		}

	}

// display the form on GET
} else
	$with_form = TRUE;

// display the form
if($with_form) {

	// splash message
	$context['text'] .= i18n::s('<p>Please fill out the form and it will be sent automatically to the site managers. Be as precise as possible, and mention your e-mail address to let us a chance to contact you back.</p>')."\n";

	// the form to send a query
	$context['text'] .= '<form method="post" action="'.$context['script_url'].'" onsubmit="return validateDocumentPost(this)" id="main_form"><div>';

	// surfer name
	if(!isset($item['edit_name']))
		$item['edit_name'] = Surfer::get_name();
	$label = i18n::s('Your name').' *';
	$input = '<input type="text" name="edit_name" id="edit_name" size="45" value="'.encode_field($item['edit_name']).'" maxlength="255" />';
	$hint = i18n::s('Let us a chance to know who you are');
	$fields[] = array($label, $input, $hint);

	// surfer address
	if(!isset($item['edit_address']))
		$item['edit_address'] = Surfer::get_email_address();
	$label = i18n::s('Your e-mail address').' *';
	$input = '<input type="text" name="edit_address" size="45" value="'.encode_field($item['edit_address']).'" maxlength="255" />';
	$hint = i18n::s('To be alerted during the processing of your request');
	$fields[] = array($label, $input, $hint);

	// stop robots
	if($field = Surfer::get_robot_stopper())
		$fields[] = $field;

	// the title
	if(!isset($item['title']))
		$item['title'] = '';
	$label = i18n::s('Query object').' *';
	$input = '<textarea name="title" rows="2" cols="50">'.encode_field($item['title']).'</textarea>';
	$hint = i18n::s('The main object of your query');
	$fields[] = array($label, $input, $hint);

	// the description
	if(!isset($item['description']))
		$item['description'] = '';
	$label = i18n::s('Details of your request');
	$input = '<textarea name="description" rows="20" cols="50">'.encode_field($item['description']).'</textarea>';
	$hint = i18n::s('Please mention any reference information required to process the request');
	$fields[] = array($label, $input, $hint);

	// build the form
	$context['text'] .= Skin::build_form($fields);

	// bottom commands
	$menu = array();

	// the submit button
	$menu[] = Skin::build_submit_button(i18n::s('Submit'), i18n::s('Press [s] to submit data'), 's');

	// step back
	if(isset($_SERVER['HTTP_REFERER']))
		$menu[] = Skin::build_link($_SERVER['HTTP_REFERER'], i18n::s('Cancel'), 'span');

	// display the menu
	$context['text'] .= Skin::finalize_list($menu, 'menu_bar');

	// end of the form
	$context['text'] .= '</div></form>';

	// append the script used for data checking on the browser
	$context['page_footer'] .= '<script type="text/javascript">// <![CDATA['."\n"
		.'// check that main fields are not empty'."\n"
		.'func'.'tion validateDocumentPost(container) {'."\n"
		."\n"
		.'	// edit_name is mandatory'."\n"
		.'	if(!container.edit_name.value) {'."\n"
		.'		alert("'.i18n::s('Please give your first and last names').'");'."\n"
		.'		Yacs.stopWorking();'."\n"
		.'		return false;'."\n"
		.'	}'."\n"
		."\n"
		.'	// edit_address is mandatory'."\n"
		.'	if(!container.edit_address.value) {'."\n"
		.'		alert("'.i18n::s('Please give your e-mail address').'");'."\n"
		.'		Yacs.stopWorking();'."\n"
		.'		return false;'."\n"
		.'	}'."\n"
		."\n"
		.'	// title is mandatory'."\n"
		.'	if(!container.title.value) {'."\n"
		.'		alert("'.i18n::s('Please provide a meaningful title.').'");'."\n"
		.'		Yacs.stopWorking();'."\n"
		.'		return false;'."\n"
		.'	}'."\n"
		."\n"
		.'	if(container.description.value.length > 64000){'."\n"
		.'		alert("'.i18n::s('The description should not exceed 64000 characters.').'");'."\n"
		.'		Yacs.stopWorking();'."\n"
		.'		return false;'."\n"
		.'	}'."\n"
		."\n"
		.'	// successful check'."\n"
		.'	return true;'."\n"
		.'}'."\n"
		."\n"
		.'// set the focus on first form field'."\n"
		.'document.getElementById("edit_name").focus();'."\n"
		.'// ]]></script>'."\n";

	// general help on this form
	$text = i18n::s('<p>Use this form to submit any kind of request you can have, from simple suggestions to complex questions.</p><p>Hearty discussion and unpopular viewpoints are welcome, but please keep queries civil. Flaming, trolling, and smarmy queries are discouraged and may be deleted. In fact, we reserve the right to delete any post for any reason. Don\'t make us do it.</p>');
	if(Surfer::is_associate())
		$text .= '<p>'.i18n::s('If you paste some existing HTML content and want to avoid the implicit formatting insert the code <code>[formatted]</code> at the very beginning of the description field.');
	else
		$text .= '<p>'.i18n::s('Most HTML tags are removed.');
	$text .= ' '.sprintf(i18n::s('You can use %s to beautify your post'), Skin::build_link('codes/', i18n::s('YACS codes'), 'help')).'.</p>';

	// locate mandatory fields
	$text .= '<p>'.i18n::s('Mandatory fields are marked with a *').'</p>';

	$context['extra'] .= Skin::build_box(i18n::s('Help'), $text, 'navigation', 'help');

}

// render the skin
render_skin();

?>