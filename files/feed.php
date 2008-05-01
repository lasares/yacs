<?php
/**
 * list new files in the RSS 2.0 format
 *
 * @todo derive this to links as well (pat)
 * @todo support Media RSS from yahoo http://search.yahoo.com/mrss
 *
 * This script gives the list of the newest published files,
 * with following information:
 * - title - the title or name of the file
 * - link - the absolute url to view the download page
 * - guid
 * - description - file description, if any
 * - pubDate - the date and time of file last modification
 * - dc:creator - the initial poster
 * - enclosure - a link to download the file
 *
 * Here is a sample item from a test feed:
 * [snippet]
 * <item>
 *		<title>40stars.gif</title>
 *		<link>http://127.0.0.1/yacs/files/view.php/19</link>
 *		<guid isPermaLink="true">http://127.0.0.1/yacs/files/view.php/19</guid>
 *		<description></description>
 *		<dc:creator>Bernard</dc:creator>
 *		<category>40stars.gif</category>
 *		<pubDate>Wed, 22 Dec 2004 23:13:48 GMT</pubDate>
 *		<enclosure url="http://127.0.0.1/yacs/files/article/396/40stars.gif" length="1153" type="application/download"/>
 *	</item>
 * [/snippet]
 *
 * If following features are enabled, this script will use them:
 * - compression - Through gzip, we have observed a shift from 3566 bytes to 881 bytes, meaning one Ethernet frame rather than three
 * - cache - Cache is supported through ETag and by setting Content-Length; Also, Cache-Control enables caching for some time, even through HTTPS
 *
 * Anonymous access is authorized, but only public files will be listed.
 *
 * @link http://blogs.law.harvard.edu/tech/rss RSS 2.0 Specification
 *
 * This feeder can be constrained to list files related to articles placed one single section.
 * Therefore, it becomes quite easy to implement podcasting or appcasting.
 * Create a dedicated section and populate it with articles and attached files.
 * Then use this script to fetch files only from the dedicated section.
 *
 * @link http://www.internetnews.com/dev-news/article.php/3431901 The RSS Enclosure Exposure
 * @link http://www.msmobiles.com/news.php/3232.html What is Podcasting?
 * @link http://en.wikipedia.org/wiki/Podcasting
 * @link http://www.marketingstudies.net/blogs/rss/archive/000270.html Appcasting
 *
 * Accept following invocations:
 * - feed.php
 * - feed.php/section/12
 * - feed.php?anchor=section:12
 *
 * @author Bernard Paques [email]bernard.paques@bigfoot.com[/email]
 * @author GnapZ
 * @reference
 * @license http://www.gnu.org/copyleft/lesser.txt GNU Lesser General Public License
 */

// common definitions and initial processing
include_once '../shared/global.php';
include_once 'files.php';

// look for the anchor as a string
$anchor = '';
if(isset($_REQUEST['anchor']))
	$anchor = $_REQUEST['anchor'];
elseif(isset($context['arguments'][0]) && isset($context['arguments'][1]))
	$anchor = $context['arguments'][0].':'.$context['arguments'][1];
$anchor = Anchors::get(strip_tags($anchor));

// no anchor, look for an article id
if(!$anchor) {
	$id = NULL;
	if(isset($_REQUEST['id']))
		$id = $_REQUEST['id'];
	elseif(isset($_REQUEST['section']))
		$id = $_REQUEST['section'];
	elseif(isset($context['arguments'][0]))
		$id = $context['arguments'][0];
	$id = strip_tags($id);
	$anchor = Anchors::get('section:'.$id);
}

// associates and editors can do what they want
if(Surfer::is_associate() || (is_object($anchor) && $anchor->is_editable()))
	$permitted = TRUE;

// the anchor has to be viewable by this surfer
elseif(is_object($anchor) && $anchor->is_viewable())
	$permitted = TRUE;

// no anchor -- show public comments
elseif(!is_object($anchor))
	$permitted = TRUE;

// the default is to disallow access
else
	$permitted = FALSE;

// load a skin
load_skin('files');

// path to this page
if(is_object($anchor) && $anchor->is_viewable())
	$context['path_bar'] = $anchor->get_path_bar();
else
	$context['path_bar'] = array( 'files/' => i18n::s('Files') );

// page title
$context['page_title'] = i18n::s('RSS feed');

// permission denied
if(!$permitted) {

	// anonymous users are invited to log in or to register
	if(!Surfer::is_logged()) {
		if(is_object($anchor))
			$link = $context['url_to_home'].$context['url_to_root'].'files/feed.php?anchor='.$anchor->get_reference();
		else
			$link = $context['url_to_home'].$context['url_to_root'].'files/feed.php';
		Safe::redirect($context['url_to_home'].$context['url_to_root'].'users/login.php?url='.htmlspecialchars(urlencode($link)));
	}

	// permission denied to authenticated user
	Safe::header('Status: 403 Forbidden', TRUE, 403);
	Skin::error(i18n::s('You are not allowed to perform this operation.'));

// display feed content
} else {

	// get the list from the cache, if possible
	if(is_object($anchor))
		$cache_id = 'files/feed.php?anchor='.$anchor->get_reference().'#channel';
	else
		$cache_id = 'files/feed.php#channel';
	if(!$text =& Cache::get($cache_id)) {

		// loads feeding parameters
		Safe::load('parameters/feeds.include.php');

		// set channel information
		$values = array();
		$values['channel'] = array();
		$values['channel']['title'] = sprintf(i18n::c('Files at %s'), $context['site_name']);
		$values['channel']['link'] = $context['url_to_home'].$context['url_to_root'].'files/';
		$values['channel']['description'] = i18n::c('Most recent public files');

		// the image for this channel
		if(isset($context['powered_by_image']) && $context['powered_by_image'])
			$values['channel']['image'] = $context['url_to_home'].$context['url_to_root'].$context['powered_by_image'];

		// list newest files
		if(is_object($anchor))
			$values['items'] = Files::list_by_date_for_anchor($anchor->get_reference(), 0, 50, 'feeds');
		else
			$values['items'] = Files::list_by_date(0, 50, 'feeds');

		// make a text
		include_once '../services/codec.php';
		include_once '../services/rss_codec.php';
		$result = rss_Codec::encode($values);
		$text = @$result[1];

		// save in cache for the next request
		Cache::put($cache_id, $text, 'files');
	}

	//
	// transfer to the user agent
	//

	// handle the output correctly
	render_raw('text/xml; charset='.$context['charset']);

	// suggest a name on download
	if(!headers_sent()) {
		if(is_object($anchor))
			$file_name = utf8::to_ascii($context['site_name'].'.files.'.str_replace(':', '.', $anchor->get_reference()).'.xml');
		else
			$file_name = utf8::to_ascii($context['site_name'].'.files.xml');
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