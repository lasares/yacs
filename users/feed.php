<?php
/**
 * list articles in the RSS 2.0 format for some user
 *
 * List up to ten fresh pages for one user only.
 * At the moment, this script gives the list of the ten newest published articles,
 * with following information:
 * - title - the title of the article
 * - url - the absolute url to fetch the article
 * - time - the date and time of article last modification
 * - author - the last contributor to the article
 * - section - the label of the section from where the article is originated
 * - image - the absolute url to fetch a related image, if any
 *
 * If following features are enabled, this script will use them:
 * - compression - Through gzip, we have observed a shift from 3566 bytes to 881 bytes, meaning one Ethernet frame rather than three
 * - cache - Cache is supported through ETag and by setting Content-Length; Also, Cache-Control enables caching for some time, even through HTTPS
 *
 * Restrictions apply on this page:
 * - associates are allowed to move forward
 * - access is restricted ('active' field == 'R'), but the surfer is an authenticated member
 * - public access is allowed ('active' field == 'Y')
 * - permission denied is the default
 *
 * Accept following invocations:
 * - feed.php (show my own feed if I am logged)
 * - feed.php/12
 * - feed.php?id=12
 *
 * @link http://blogs.law.harvard.edu/tech/rss RSS 2.0 Specification
 *
 * @author Bernard Paques [email]bernard.paques@bigfoot.com[/email]
 * @author GnapZ
 * @tester Pat
 * @reference
 * @license http://www.gnu.org/copyleft/lesser.txt GNU Lesser General Public License
 */

// common definitions and initial processing
include_once '../shared/global.php';

// look for the id
$id = NULL;
if(isset($_REQUEST['id']))
	$id = $_REQUEST['id'];
elseif(isset($context['arguments'][0]))
	$id = $context['arguments'][0];
elseif(Surfer::is_logged())
	$id = Surfer::get_id();
$id = strip_tags($id);

// get the item from the database
$item =& Users::get($id);

// associates can do what they want
if(Surfer::is_associate())
	$permitted = TRUE;

// access is restricted to authenticated member
elseif(($item['active'] == 'R') && Surfer::is_member())
	$permitted = TRUE;

// public access is allowed
elseif($item['active'] == 'Y')
	$permitted = TRUE;

// the default is to disallow access
else
	$permitted = FALSE;

// load the skin
load_skin('users');

// path to this page
$context['path_bar'] = array( 'users/' => i18n::s('People') );

// page title
$context['page_title'] = i18n::s('RSS feed');

// not found
if(!isset($item['id'])) {
	Safe::header('Status: 404 Not Found', TRUE, 404);
	Skin::error(i18n::s('No item has the provided id.'));

// permission denied
} elseif(!$permitted) {

	// anonymous users are invited to log in or to register
	if(!Surfer::is_logged())
		Safe::redirect($context['url_to_home'].$context['url_to_root'].'users/login.php?url='.urlencode(Users::get_url($item['id'], 'feed')));

	// permission denied to authenticated user
	Safe::header('Status: 403 Forbidden', TRUE, 403);
	Skin::error(i18n::s('You are not allowed to perform this operation.'));

// send the list of recent contributions by this user
} else {

	// get the list from the cache, if possible
	$cache_id = 'users/feed.php?id='.$item['id'].'#channel';
	if(!$text =& Cache::get($cache_id)) {

		// loads feeding parameters
		Safe::load('parameters/feeds.include.php');

		// structured data
		$values = array();
		$values['channel'] = array();

		// set channel information
		$values['channel']['title'] = $item['full_name'] ? $item['full_name'] : $item['nick_name'];
		$values['channel']['link'] = $context['url_to_home'].$context['url_to_root'].Users::get_url($item['id']);
		$values['channel']['description'] = $item['introduction'];

		// the image for this channel
		if(isset($context['powered_by_image']) && $context['powered_by_image'])
			$values['channel']['image'] = $context['url_to_home'].$context['url_to_root'].$context['powered_by_image'];

		// the list of newest pages
		$values['items'] = (array)Articles::list_by_date_for_author($item['id'], 0, 50, 'feeds');

		// make a text
		include_once '../services/codec.php';
		include_once '../services/rss_codec.php';
		$result = rss_Codec::encode($values);
		$status = @$result[0];
		$text = @$result[1];

		// save in cache for the next request
		Cache::put($cache_id, $text, 'articles');
	}

	//
	// transfer to the user agent
	//

	// handle the output correctly
	render_raw('text/xml; charset='.$context['charset']);

	// suggest a name on download
	if(!headers_sent()) {
		$file_name = utf8::to_ascii($context['site_name'].'.section.'.$item['id'].'.rss.xml');
		Safe::header('Content-Disposition: inline; filename="'.$file_name.'"');
	}

	// enable 30-minute caching (30*60 = 1800), even through https, to help IE6 on download
	if(!headers_sent()) {
		Safe::header('Expires: '.gmdate("D, d M Y H:i:s", time() + 1800).' GMT');
		Safe::header("Cache-Control: max-age=1800, public");
		Safe::header("Pragma: ");
	}

	// strong validation
	if((!isset($context['without_http_cache']) || ($context['without_http_cache'] != 'Y')) && !headers_sent()) {

		// generate some strong validator
		$etag = '"'.md5($text).'"';
		Safe::header('ETag: '.$etag);

		// validate the content if hash is ok
		if(isset($_SERVER['HTTP_IF_NONE_MATCH']) && is_array($if_none_match = explode(',', str_replace('\"', '"', $_SERVER['HTTP_IF_NONE_MATCH'])))) {
			foreach($if_none_match as $target) {
				if(trim($target) == $etag) {
					Safe::header('Status: 304 Not Modified', TRUE, 304);
					return;
				}
			}
		}
	}

	// actual transmission except on a HEAD request
	if(isset($_SERVER['REQUEST_METHOD']) && ($_SERVER['REQUEST_METHOD'] != 'HEAD'))
		echo $text;

	// the post-processing hook, then exit
	finalize_page(TRUE);

}

// render the skin
render_skin();

?>