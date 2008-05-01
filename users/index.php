<?php
/**
 * handling members of this community
 *
 * @todo create buddy lists (CmputrAce)
 *
 * For a comprehensive description of user profiles, you should check the database abstraction script
 * at [script]users/users.php[/script].
 *
 * This page list users of this server, ranked by decreasing number of contributions and by
 * decreasing edition date. Therefore, it is likely
 * that the persons you are interested in are at located near the top of the list.
 *
 * Following restrictions apply:
 * - anonymous users can see only active user profiles (the 'active' field == 'Y')
 * - members can see active and restricted user profiles ('active field == 'Y' or 'R')
 * - associates can see all user profiles
 *
 * The main menu displays the total number of users.
 * It also has navigation links to page among profiles.
 * Commands are available to associates to either create a new user profile or to review noticeable user profiles.
 *
 * Contact shortcuts are included as well to registered Skype, Yahoo, MSN, etc...
 *
 * A list of most new users is displayed as a sidebox. Also, a shortcut to the search form has been added.
 *
 * A list of surfers who are present on the site is also displayed, as a side box.
 *
 *
 * This script secretely features a link to the main RSS feeder for this site, namely:
 * [code]&lt;link rel="alternate" href="http://.../yacs/feeds/rss_2.0.php" title="RSS" type="application/rss+xml" /&gt;[/code]
 *
 * The prefix hook is used to invoke any software extension bound as follows:
 * - id: 'users/index.php#prefix'
 * - type: 'include'
 * - parameters: none
 * Use this hook to include any text right before the main content.
 *
 * The suffix hook is used to invoke any software extension bound as follows:
 * - id: 'users/index.php#suffix'
 * - type: 'include'
 * - parameters: none
 * Use this hook to include any text right after the main content.
 *
 * Accept following invocations:
 * - index.php (view the 20 most contributing users)
 * - index.php/10 (view users 200 to 220, ranked by count of contributions)
 * - index.php?page=4 (view users 80 to 100, ranked by count of contributions)
 *
 * @author Bernard Paques [email]bernard.paques@bigfoot.com[/email]
 * @author GnapZ
 * @tester Fernand Le Chien
 * @reference
 * @license http://www.gnu.org/copyleft/lesser.txt GNU Lesser General Public License
 */

// common definitions and initial processing
include_once '../shared/global.php';
include_once '../locations/locations.php';

// which page should be displayed
$page = 1;
if(isset($_REQUEST['page']))
	$page = $_REQUEST['page'];
elseif(isset($context['arguments'][0]))
	$page = $context['arguments'][0];
$page = strip_tags($page);

// load the skin
load_skin('users');

// the maximum number of users per page
if(!defined('USERS_PER_PAGE'))
	define('USERS_PER_PAGE', 50);

// the title of the page
$context['page_title'] = i18n::s('People');

// count users in the database
$stats = Users::stat();
if($stats['count'])
	$context['page_menu'] = array_merge($context['page_menu'], array('_count' =>sprintf(i18n::ns('1 user', '%d users', $stats['count']), $stats['count'])));

// navigation commands for users, if necessary
if($stats['count'] > USERS_PER_PAGE) {
	$home = 'users/index.php';
	if($context['with_friendly_urls'] == 'Y')
		$prefix = $home.'/';
	elseif($context['with_friendly_urls'] == 'R')
		$prefix = $home.'/';
	else
		$prefix = $home.'?page=';
	$context['page_menu'] = array_merge($context['page_menu'], Skin::navigate($home, $prefix, $stats['count'], USERS_PER_PAGE, $page));
}

// map users on Google Maps
if($stats['count'] && isset($context['google_api_key']) && $context['google_api_key'])
	$context['page_menu'] = array_merge($context['page_menu'], array( Locations::get_url('users', 'map_on_google') => i18n::s('Map all users') ));

// associates may create new profiles
if(Surfer::is_associate())
	$context['page_menu'] = array_merge($context['page_menu'], array( 'users/edit.php' => i18n::s('Add a user') ));

// anyone can review some user profiles
$context['page_menu'] = array_merge($context['page_menu'], array( 'users/review.php' => i18n::s('Review user profiles') ));

// the prefix hook for the index of members
if(is_callable(array('Hooks', 'include_scripts')))
	$context['text'] .= Hooks::include_scripts('users/index.php#prefix');

// a search form for users
$context['text'] .= '<form action="'.$context['url_to_root'].'users/search.php" method="get">'
	.'<p>'
	.'<input type="text" name="search" size="40" value="'.encode_field(i18n::s('Look for some user')).'" onfocus="this.value=\'\'" maxlength="128"'.EOT
	.Skin::build_submit_button('&raquo;')
	.'</p>'
	."</form>\n";

// map users on Google Maps
if($stats['count'] && isset($context['google_api_key']) && $context['google_api_key'])
	$context['text'] .= '<p>'.Skin::build_link(Locations::get_url('users', 'map_on_google'), i18n::s('Map users at Google Maps')).'</p>';

// look up the database to find the list of users
$cache_id = 'users/index.php#text#'.$page;
if(!$text =& Cache::get($cache_id)) {

	// query the database and layout that stuff
	$offset = ($page - 1) * USERS_PER_PAGE;
	if(!$text = Users::list_by_posts($offset, USERS_PER_PAGE, 'full'))
		$text = '<p>'.i18n::s('No item has been found.').'</p>';

	// we have an array to format
	if(is_array($text))
		$text =& Skin::build_list($text, 'decorated');

	// cache this to speed subsequent queries
	Cache::put($cache_id, $text, 'users');
}
$context['text'] .= $text;

// also put a small menu at the bottom of the page
if(strlen($context['text']) > 1024)
	$context['text'] .= '<p>'.Skin::build_list($context['page_menu'], 'menu')."</p>\n";

// the suffix hook for the index of members
if(is_callable(array('Hooks', 'include_scripts')))
	$context['text'] .= Hooks::include_scripts('users/index.php#suffix');

// side bar with the list of present users --don't cache, this will change on each request anyway
include_once $context['path_to_root'].'users/visits.php';
if($items = Visits::list_users(0, COMPACT_LIST_SIZE, 'compact')) {

	// also mention the total number of present users
	$stat = Users::stat_present();
	if($stat['count'] > 1)
		$items = array_merge($items, array('_' => sprintf(i18n::ns('%d active now', '%d active now', $stat['count']), $stat['count'])));
	$context['extra'] .= Skin::build_box(i18n::s('Present users'), Skin::build_list($items, 'compact'), 'extra');
}

// page extra content
$cache_id = 'users/index.php#extra';
if(!$text =& Cache::get($cache_id)) {

	// side bar with the list of newest users
	if($items = Users::list_by_date(0, COMPACT_LIST_SIZE, 'compact'))
		$text .= Skin::build_box(i18n::s('Newest Members'), Skin::build_list($items, 'compact'), 'extra');

	// side boxes for related categories, if any
	include_once '../categories/categories.php';
	if($categories = Categories::list_by_date_for_display('user:index', 0, 7, 'raw')) {
		foreach($categories as $id => $attributes) {

			// link to the category page from the box title
			$label =& Skin::build_box_title(Skin::strip($attributes['title']), Categories::get_url($attributes['id'], 'view', $attributes['title']), i18n::s('View the category'));

			// box content
			if($items = Members::list_articles_by_date_for_anchor('category:'.$id, 0, COMPACT_LIST_SIZE, 'compact'))
				$text .= Skin::build_box($label, Skin::build_list($items, 'compact'), 'navigation')."\n";
		}
	}

	// cache, whatever change, for 5 minutes
	Cache::put($cache_id, $text, 'stable', 300);
}
$context['extra'] .= $text;

// referrals, if any
$context['extra'] .= Skin::build_referrals('users/index.php');

// a meta link to a feeding page
include_once '../feeds/feeds.php';
$context['page_header'] .= "\n".'<link rel="alternate" href="'.$context['url_to_root'].Feeds::get_url('rss').'" title="RSS" type="application/rss+xml"'.EOT;

// render the skin
render_skin();

?>