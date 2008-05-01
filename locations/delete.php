<?php
/**
 * delete a location
 *
 * This script calls for confirmation, then actually deletes the link.
 * It updates the database, then redirects to the anchor page.
 *
 * Restrictions apply on this page:
 * - associates and authenticated editors are allowed to move forward
 * - permission is denied if the anchor is not viewable by this surfer
 * - permission is granted if the anchor is the profile of this member
 * - authenticated users may suppress their own posts
 * - else permission is denied
 *
 * Accept following invocations:
 * - delete.php/12
 * - delete.php?id=12
 *
 * If the anchor for this item specifies a specific skin (option keyword '[code]skin_xyz[/code]'),
 * or a specific variant (option keyword '[code]variant_xyz[/code]'), they are used instead default values.
 *
 * @author Bernard Paques [email]bernard.paques@bigfoot.com[/email]
 * @author GnapZ
 * @reference
 * @license http://www.gnu.org/copyleft/lesser.txt GNU Lesser General Public License
 */

// common definitions and initial processing
include_once '../shared/global.php';
include_once 'locations.php';

// look for the id
$id = NULL;
if(isset($_REQUEST['id']))
	$id = $_REQUEST['id'];
elseif(isset($context['arguments'][0]))
	$id = $context['arguments'][0];
$id = strip_tags($id);

// get the item from the database
$item =& Locations::get($id);

// get the related anchor, if any
$anchor = NULL;
if(isset($item['anchor']) && $item['anchor'])
	$anchor = Anchors::get($item['anchor']);

// associates and authenticated editors can do what they want
if(Surfer::is_associate() || (Surfer::is_member() && is_object($anchor) && $anchor->is_editable()))
	$permitted = TRUE;

// the anchor has to be viewable by this surfer
elseif(is_object($anchor) && !$anchor->is_viewable())
	$permitted = FALSE;

// the item is anchored to the profile of this member
elseif(Surfer::is_member() && !strcmp($item['anchor'], 'user:'.Surfer::get_id()))
	$permitted = TRUE;

// authenticated surfers may suppress their own posts --no create_id yet...
elseif(isset($item['edit_id']) && Surfer::is_creator($item['edit_id']))
	$permitted = TRUE;

// the default is to deny access
else
	$permitted = FALSE;

// load the skin, mabe with a variant
load_skin('locations', $anchor);

// the path to this page
if(is_object($anchor))
	$context['path_bar'] = $anchor->get_path_bar();
else
	$context['path_bar'] = array( 'locations/' => i18n::s('Locations') );

// the title of the page
$context['page_title'] = i18n::s('Delete a location');

// not found
if(!$item['id']) {
	Safe::header('Status: 404 Not Found', TRUE, 404);
	Skin::error(i18n::s('No item has the provided id.'));

// permission denied
} elseif(!$permitted) {

	// anonymous users are invited to log in
	if(!Surfer::is_logged())
		Safe::redirect($context['url_to_home'].$context['url_to_root'].'users/login.php?url='.urlencode(Locations::get_url($item['id'], 'delete')));

	// permission denied to authenticated user
	Safe::header('Status: 403 Forbidden', TRUE, 403);
	Skin::error(i18n::s('You are not allowed to perform this operation.'));

// deletion is confirmed
} elseif(isset($_REQUEST['confirm']) && ($_REQUEST['confirm'] == 'yes')) {

	// touch the related anchor before actual deletion, since the location has to be accessible at that time
	if(is_object($anchor))
		$anchor->touch('location:delete', $id, TRUE);

	// if no error, back to the anchor or to the index page
	if(Locations::delete($id)) {
		if(is_object($anchor))
			Safe::redirect($context['url_to_home'].$context['url_to_root'].$anchor->get_url());
		else
			Safe::redirect($context['url_to_home'].$context['url_to_root'].'users/');
	}

// deletion has to be confirmed
} elseif(isset($_SERVER['REQUEST_METHOD']) && ($_SERVER['REQUEST_METHOD'] == 'POST')) {

	Safe::header('Status: 403 Forbidden', TRUE, 403);
	Skin::error(i18n::s('The deletion has not been confirmed.'));

// ask for confirmation
} else {

	// the submit button
	$context['text'] .= '<form method="post" action="'.$context['script_url'].'" id="main_form"><p>'."\n"
		.Skin::build_submit_button(i18n::s('Yes, I want to suppress this location'), NULL, NULL, 'confirmed')."\n"
		.'<input type="hidden" name="id" value="'.$item['id'].'" />'."\n"
		.'<input type="hidden" name="confirm" value="yes" />'."\n"
		.'</p></form>'."\n";

	// set the focus
	$context['text'] .= '<script type="text/javascript">// <![CDATA['."\n"
		.'// set the focus on first form field'."\n"
		.'document.getElementById("confirmed").focus();'."\n"
		.'// ]]></script>'."\n";

	// the geo_place_name of the location
	$context['text'] .= Skin::build_block($item['geo_place_name'], 'title');

	// display the full text
	if($item['description']) {

		// beautify the text
		$text = Codes::beautify($item['description']);

		// show the description
		$context['text'] .= '<p></p>'.$text."<p></p>\n";
	}

	// information on uploader
	if(Surfer::is_member() && $item['edit_name'])
		$details[] = sprintf(i18n::s('edited by %s %s'), Users::get_link($item['edit_name'], $item['edit_address'], $item['edit_id']), Skin::build_date($item['edit_date']));

	// the complete details
	$context['text'] .= ucfirst(implode(', ', $details)).BR."\n";

}

// render the skin
render_skin();

?>