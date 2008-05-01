<?php
/**
 * set an image as the icon of its anchor
 *
 * An icon is the main image displayed at some page.
 *
 * This page is to be used by associates and editors only, while they are editing images.
 * The script updates the database, then redirects to the anchor page.
 *
 * Accept following invocations:
 * - set_as_icon.php/12
 * - set_as_icon.php?id=12
 *
 * If the anchor for this item specifies a specific skin (option keyword '[code]skin_xyz[/code]'),
 * or a specific variant (option keyword '[code]variant_xyz[/code]'), they are used instead default values.
 *
 * @see articles/view.php
 * @see categories/view.php
 * @see sections/view.php
 * @see users/view.php
 *
 * @author Bernard Paques [email]bernard.paques@bigfoot.com[/email]
 * @author GnapZ
 * @reference
 * @license http://www.gnu.org/copyleft/lesser.txt GNU Lesser General Public License
 */

// common definitions and initial processing
include_once '../shared/global.php';
include_once 'images.php';

// look for the id
$id = NULL;
if(isset($_REQUEST['id']))
	$id = $_REQUEST['id'];
elseif(isset($context['arguments'][0]))
	$id = $context['arguments'][0];
$id = strip_tags($id);

// get the item from the database
$item =& Images::get($id);

// get the related anchor, if any
$anchor = NULL;
if(isset($item['anchor']) && $item['anchor'])
	$anchor = Anchors::get($item['anchor']);

// load the skin, maybe with a variant
load_skin('images', $anchor);

// clear the tab we are in, if any
if(is_object($anchor))
	$context['current_focus'] = $anchor->get_focus();

// the path to this page
if(is_object($anchor) && $anchor->is_viewable())
	$context['path_bar'] = $anchor->get_path_bar();
else
	$context['path_bar'] = array( 'index.php' => i18n::s('Images') );

// the title of the page
$context['page_title'] = i18n::s('Set an image as the page icon');

// not found
if(!$item['id'])
	Skin::error(i18n::s('No item has been found.'));

// no anchor
elseif(!is_object($anchor))
	Skin::error(i18n::s('No anchor has been found.'));

// operation is restricted to associates and editors
elseif(!Surfer::is_associate() && !$anchor->is_editable())
	Skin::error(i18n::s('You are not allowed to perform this operation.'));

// set this image as the anchor icon
else {

	// back to the anchor page if no error
	if(!($error = $anchor->touch('image:set_as_icon', $id)))
		Safe::redirect($context['url_to_home'].$context['url_to_root'].$anchor->get_url());

	Skin::error($error);
}

// failed operation
$context['text'] .= '<p>'.i18n::s('Operation has failed.').'</p>';

// render the skin
render_skin();

?>