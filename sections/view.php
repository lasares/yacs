<?php
/**
 * view one section
 *
 * @todo index presentation of articles (agnes)
 *
 * The main panel has following elements:
 * - top icons, if any --set in sub-section
 * - the section itself, with details, introduction, and main text.
 * - gadget boxes, if any --set in sub-sections
 * - list of sub-sections.
 * - list of related articles (from this section, or from sub-sections)
 * - list of files, if option 'with_files'
 * - list of comments, if option 'with_comments'
 * - list of related links
 * - list of inactive sub-section, for associates
 * - bottom icons, if any --set in sub-section
 *
 * If the section has been configured to not display sub-sections,
 * no command to create a sub-section is displayed.
 *
 * The extra panel has following elements:
 * - A navigation box for flashy news (#news, #scrolling_news, or #rotating_news)
 * - A contextual menu to switch to other sections in the neighbour
 * - twin pages, if any
 * - Up to 6 articles in extra boxes --set in sub-section
 * - Extra boxes listing articles of some sub-sections
 * - categories attached to this section
 * - Links to rss feeds related to this section
 * - Bookmarklet to post to this section
 * - Related feeding servers, if any
 * - Means to reference this page, into a sidebar box
 * - The top popular referrals, if any
 *
 * Several HTTP headers, or &lt;meta&gt; attributes of the displayed page, are set dynamically here
 * to help advanced web usage. This includes:
 * - a link to a RDF description of this page (e.g., '&lt;link rel="meta" href="http://127.0.0.1/yacs/sections/describe.php/4310" title="rdf" type="application/rdf+xml" /&gt;')
 * - a link to a RSS feed for this section (e.g., '&lt;link rel="alternate" href="http://127.0.0.1/yacs/sections/feed.php/4038" title="RSS" type="application/rss+xml" /&gt;')
 * - a rdf section implementing the [link=trackback]http://www.movabletype.org/docs/mttrackback.html[/link] interface
 * - a [link=pingback]http://www.hixie.ch/specs/pingback/pingback[/link] link (e.g., '&lt;link rel="pingback" href="http://here/yacs/services/pingback.php" /&gt;')
 *
 * @link http://www.movabletype.org/docs/mttrackback.html TrackBack Technical Specification
 * @link http://www.hixie.ch/specs/pingback/pingback Pingback specification
 *
 * Meta information also includes:
 * - page description, which is a copy of the introduction, if any, or the default general description parameter
 * - page author, who is the original creator
 *
 * How to customize this page?
 *
 * Well, there is so much you can do:
 * - use a special bullet for a given section by posting the adequate image to this section
 * - edit the section to use a skin variant, or another skin - see [script]sections/edit.php[/script]
 * - create a new layout for articles, to change the rendering of listed pages - see the table below
 *
 * You can select among following layouts for sub-sections:
 * [table]
 * Sections layout|Script loaded
 * [body]
 * 'compact'|[script]sections/layout_sections_as_compact.php[/script]
 * 'decorated'|[script]sections/layout_sections.php[/script]
 * 'folded'|[script]sections/layout_sections_as_folded.php[/script]
 * 'freemind'|[script]codes/codes.php[/script]
 * 'inline'|[script]sections/layout_sections_as_inline.php[/script]
 * 'jive'|[script]sections/layout_sections_as_jive.php[/script]
 * 'map' (also default value)|[script]sections/layout_sections_as_yahoo.php[/script]
 * 'none'|All sections are shown only to associates an editors, as a compact set of special sections
 * 'yabb'|[script]sections/layout_sections_as_yabb.php[/script]
 * custom|[script]sections/layout_sections_as_custom.php[/script] (to load a customized layout)
 * [/table]
 *
 * To create a custom layout for sections, create a script that implement the Layout interface
 * (look into [script]shared/layout.php[/script]) and save it into the directory ##sections##
 * with the name prefix ##layout_sections_as_##. Then edit the section to manually configure the layout.
 *
 * For example, for the custom layout ##foo## for sub-sections,
 * YACS will attempt to load the script ##sections/layout_sections_as_foo.php##.
 * Edit the section to manually configure the layout ##foo## for sub-sections.
 *
 * You can select among following layouts for articles:
 * [table]
 * Articles layout|Script loaded
 * [body]
 * 'alistapart'|[script]articles/layout_articles_as_alistapart.php[/script]
 * 'boxesandarrows'|[script]articles/layout_articles_as_boxesandarrows.php[/script]
 * 'compact'|[script]articles/layout_articles_as_compact.php[/script]
 * 'daily'|[script]articles/layout_articles_as_daily.php[/script]
 * 'decorated' (also default value)|[script]articles/layout_articles.php[/script]
 * 'jive'|[script]articles/layout_articles_as_jive.php[/script]
 * 'manual'|[script]articles/layout_articles_as_manual.php[/script]
 * 'map'|[script]articles/layout_articles_as_yahoo.php[/script]
 * 'none'|No articles are shown
 * 'table'|[script]articles/layout_articles_as_table.php[/script]
 * 'wiki'|[script]articles/layout_articles.php[/script]
 * 'yabb'|[script]articles/layout_articles_as_yabb.php[/script]
 * custom|[script]articles/layout_articles_as_custom.php[/script] (to load a customized layout)
 * [/table]
 *
 * For example, for the custom layout ##bar## for articles,
 * YACS will attempt to load the script ##articles/layout_articles_as_bar.php##.
 * Edit the section to manually configure the layout ##bar## for content.
 *
 * @link http://www.boxesandarrows.com/ Boxes and Arrows
 * @link http://www.jivesoftware.com/products/forums/  Jive Forums
 * @link http://www.php.net/manual/en/index.php PHP Manual
 * @link http://www.yabbforum.com/ Yet Another Bulletin Board
 *
 * The caching strategy for section rendering is aiming to save on database
 * requests. Since this script udates $context['page_details'], $context['text'],
 * and $context['extra'], each of these is cached separately.
 * The caching topic is the reference of this section (e.g;, 'section:678').
 * Cache entries are purged directly either when the page is modified, or when
 * some object attached to it triggers the Section::touch() function.
 *
 * Restrictions apply on this page:
 * - associates and editors are allowed to move forward
 * - permission is denied if the anchor is not viewable
 * - access is restricted ('active' field == 'R'), but the surfer is an authenticated member
 * - public access is allowed ('active' field == 'Y')
 * - permission denied is the default
 *
 * Accept following invocations:
 * - view.php/12 (view the first page of the section document)
 * - view.php?id=12 (view the first page of the section document)
 * - view.php/12/sections/1 (view the page 1 of the list of related sections)
 * - view.php?id=12&sections=1 (view the page 1 of the list of related sections)
 * - view.php/12/articles/3 (view the page 3 of the list of related articles)
 * - view.php?id=12&articles=3 (view the page 3 of the list of related articles)
 * - view.php/12/comments/1 (view the page 1 of the list of related comments)
 * - view.php?id=12&comments=1 (view the page 1 of the list of related comments)
 * - view.php/12/files/2 (view the page 2 of the list of related files)
 * - view.php?id=12&files=2 (view the page 2 of the list of related files)
 * - view.php/12/links/1 (view the page 1 of the list of related links)
 * - view.php?id=12&links=1 (view the page 1 of the list of related links)
 *
 * If this section, or one of its anchor, specifies a specific skin (option keyword '[code]skin_xyz[/code]'),
 * or a specific variant (option keyword '[code]variant_xyz[/code]'), they are used instead default values.
 *
 * @author Bernard Paques [email]bernard.paques@bigfoot.com[/email]
 * @author GnapZ
 * @tester Fw_crocodile
 * @tester Christophe Battarel [email]christophe.battarel@altairis.fr[/email]
 * @tester Elrik
 * @tester Viviane Zaniroli
 * @tester Fernand Le Chien
 * @tester Lucrecius
 * @tester Agnes
 * @tester Guillaume Perez
 * @tester Olivier
 * @tester Cloubech
 * @tester Le_ffrench
 * @reference
 * @license http://www.gnu.org/copyleft/lesser.txt GNU Lesser General Public License
 */

// common definitions and initial processing
include_once '../shared/global.php';
include_once '../categories/categories.php';
include_once '../comments/comments.php';		// attached comments and notes
include_once '../files/files.php';				// attached files
include_once '../links/links.php';				// related pages
include_once '../overlays/overlay.php';
include_once '../servers/servers.php';
include_once '../versions/versions.php';		// back in history

// look for the id
$id = NULL;
if(isset($_REQUEST['id']))
	$id = $_REQUEST['id'];
elseif(isset($context['arguments'][0]))
	$id = $context['arguments'][0];
$id = strip_tags($id);

// encode ISO-8859-1 argument, if any
if(isset($_SERVER['HTTP_ACCEPT_CHARSET']) && preg_match('/^iso-8859-1/i', $_SERVER['HTTP_ACCEPT_CHARSET']))
	$id = utf8_encode($id);

// page within a page
$page = 1;
if(isset($_REQUEST['pages']))
	$page = $_REQUEST['pages'];
$page = strip_tags($page);

// no follow-up page yet
$zoom_type = '';
$zoom_index = 1;

// view.php?id=12&sections=2
if(isset($_REQUEST['sections']) && ($zoom_index = $_REQUEST['sections']))
	$zoom_type = 'sections';

// view.php?id=12&articles=2
elseif(isset($_REQUEST['articles']) && ($zoom_index = $_REQUEST['articles']))
	$zoom_type = 'articles';

// view.php?id=12&comments=2
elseif(isset($_REQUEST['comments']) && ($zoom_index = $_REQUEST['comments']))
	$zoom_type = 'comments';

// view.php?id=12&files=2
elseif(isset($_REQUEST['files']) && ($zoom_index = $_REQUEST['files']))
	$zoom_type = 'files';

// view.php?id=12&links=2
elseif(isset($_REQUEST['links']) && ($zoom_index = $_REQUEST['links']))
	$zoom_type = 'links';

// view.php/12/files/2
elseif(isset($context['arguments'][1]) && isset($context['arguments'][2])) {
	$zoom_type = $context['arguments'][1];
	$zoom_index = $context['arguments'][2];
}

// get the item from the database
$item =& Sections::get($id);

// get the related overlay, if any
$overlay = NULL;
if(isset($item['overlay']))
	$overlay = Overlay::bind($item['overlay']);

// get the overlay for content of this section, if any
$content_overlay = NULL;
if(isset($item['content_overlay']))
	$content_overlay = Overlay::bind($item['content_overlay']);

// get the related anchor, if any
$anchor = NULL;
if(isset($item['anchor']) && $item['anchor'])
	$anchor = Anchors::get($item['anchor']);

// editors can do what they want on items anchored here
if((isset($item['id']) && Sections::is_assigned($item['id']) && Surfer::is_member()) || (is_object($anchor) && $anchor->is_editable()))
	Surfer::empower();

// readers have additional rights
elseif((isset($item['id']) && Sections::is_assigned($item['id']) && Surfer::is_logged()) || (is_object($anchor) && $anchor->is_assigned()))
	Surfer::empower('S');

//
// is this surfer allowed to browse the page?
//

// associates, editors and readers can read this page
if(Surfer::is_empowered('S'))
	$permitted = TRUE;

// the anchor has to be viewable by this surfer
elseif(is_object($anchor) && !$anchor->is_viewable())
	$permitted = FALSE;

// access is restricted to authenticated surfer
elseif(isset($item['active']) && ($item['active'] == 'R') && Surfer::is_logged())
	$permitted = TRUE;

// public access is allowed
elseif(isset($item['active']) && $item['active'] == 'Y')
	$permitted = TRUE;

// the default is to disallow access
else
	$permitted = FALSE;

// is the article on user watch list?
$in_watch_list = FALSE;
if(Surfer::is_logged() && isset($item['id']))
	$in_watch_list = Members::check('section:'.$item['id'], 'user:'.Surfer::get_id());

// has this page some versions?
$has_versions = FALSE;
if(isset($item['id']) && !$zoom_type && Surfer::is_empowered() && Surfer::is_logged() && Versions::count_for_anchor('section:'.$item['id']))
	$has_versions = TRUE;

// use a specific script to render the article in replacement of the standard one --also protect from hackers
if(isset($item['options']) && preg_match('/\bview_as_[a-zA-Z0-9_\.]+?\b/i', $item['options'], $matches) && is_readable($matches[0].'.php')) {
	include $matches[0].'.php';
	return;
} elseif(is_object($anchor) && ($viewer = $anchor->has_option('view_as')) && is_readable('view_as_'.$viewer.'.php')) {
	$name = 'view_as_'.$viewer.'.php';
	include $name;
	return;
}

// load the skin, maybe with a variant
load_skin('sections', $anchor, isset($item['options']) ? $item['options'] : '');

// clear the tab we are in
$context['current_focus'] = array();
if(is_object($anchor))
	$context['current_focus'] = $anchor->get_focus();
if(isset($item['id']))
	$context['current_focus'][] = 'section:'.$item['id'];

// path to this page
if(is_object($anchor) && $anchor->is_viewable())
	$context['path_bar'] = $anchor->get_path_bar();

// page title
if(isset($item['index_title']) && $item['index_title'])
	$context['page_title'] = $item['index_title'];
elseif(isset($item['title']) && $item['title'])
	$context['page_title'] = $item['title'];
else
	$context['page_title'] = i18n::s('No title has been provided.');

// insert page family, if any
if(isset($item['family']) && $item['family'])
	$context['page_title'] = FAMILY_PREFIX.'<span id="family">'.$item['family'].'</span> '.FAMILY_SUFFIX.$context['page_title']."\n";

// page language, if any
if(isset($item['language']) && $item['language'] && ($item['language'] != 'none'))
	$context['page_language'] = $item['language'];

// watch command is provided to logged surfers
if(isset($item['id']) && !$zoom_type && $permitted && Surfer::is_logged()) {

	$link = Users::get_url('section:'.$item['id'], 'track');

	if($in_watch_list)
		$label = i18n::s('Forget');
	else
		$label = i18n::s('Watch');

	Skin::define_img('WATCH_TOOL_IMG', $context['skin'].'/icons/tools/watch.gif');
	$context['page_menu'] = array_merge($context['page_menu'], array( $link => array('', WATCH_TOOL_IMG.$label, '', 'basic', '', i18n::s('Manage your watch list'))));
}

// the command to post a new page
if(isset($item['id']) && !$zoom_type && $permitted && Articles::are_allowed($anchor, $item)) {

	$url = 'articles/edit.php?anchor='.urlencode('section:'.$item['id']);

	if($item['articles_layout'] == 'jive')
		$label = i18n::s('Start a new topic');
	elseif($item['articles_layout'] == 'yabb')
		$label = i18n::s('Start a new topic');
	else
		$label = i18n::s('Add a page');

	Skin::define_img('NEW_ARTICLE_IMG', $context['skin'].'/icons/articles/new.gif');
	$context['page_menu'] = array_merge($context['page_menu'], array( $url => array('', NEW_ARTICLE_IMG.$label, '', 'basic', '', i18n::s('Add new content to this section')) ));

}

// the command to create a new poll, if no overlay nor template has been defined for content of this section
if(isset($item['id']) && !$zoom_type && $permitted && (!isset($item['content_overlay']) || !trim($item['content_overlay'])) && (!isset($item['articles_templates']) || !trim($item['articles_templates'])) && (!is_object($anchor) || !$anchor->get_templates_for('article')) && Articles::are_allowed($anchor, $item)) {

	$url = 'articles/edit.php?anchor='.urlencode('section:'.$item['id']).'&amp;variant=poll';
	$label = i18n::s('Add a poll');
	Skin::define_img('POLL_IMG', $context['skin'].'/icons/articles/poll.gif');
	$context['page_menu'] = array_merge($context['page_menu'], array( $url => array('', POLL_IMG.$label, '', 'basic', '', i18n::s('Add new content to this section')) ));
}

// associates can create a sub-section, if sub sections are allowed
if(isset($item['id']) && !$zoom_type && $permitted
	&& (!isset($item['sections_layout']) || ($item['sections_layout'] != 'none'))
	&& Surfer::is_empowered()) {

	Skin::define_img('NEW_SECTION_IMG', $context['skin'].'/icons/sections/new.gif');
	$context['page_menu'] = array_merge($context['page_menu'], array( 'sections/edit.php?anchor='.urlencode('section:'.$item['id']) => array('', NEW_SECTION_IMG.i18n::s('Add a section'), '', 'basic', '', i18n::s('Add a section')) ));

}

// additional commands for associates and editors do not appear on follow-up pages
if(isset($item['id']) && !$zoom_type && Surfer::is_empowered()) {

	// modify this page
	Skin::define_img('EDIT_SECTION_IMG', $context['skin'].'/icons/sections/edit.gif');
	$context['page_menu'] = array_merge($context['page_menu'], array( Sections::get_url($item['id'], 'edit') => array('', EDIT_SECTION_IMG.i18n::s('Edit'), '', 'basic', '', i18n::s('Update the content of this page')) ));

	// access previous versions, if any
	if($has_versions && Surfer::is_logged()) {
		Skin::define_img('HISTORY_TOOL_IMG', $context['skin'].'/icons/tools/history.gif');
		$context['page_menu'] = array_merge($context['page_menu'], array( Versions::get_url('section:'.$item['id'], 'list') => array('', HISTORY_TOOL_IMG.i18n::s('History'), '', 'basic', '', i18n::s('Previous versions of this page')) ));
	}

	// post an image
	if(Surfer::may_upload()) {
		Skin::define_img('IMAGE_TOOL_IMG', $context['skin'].'/icons/tools/image.gif');
		$context['page_menu'] = array_merge($context['page_menu'], array( 'images/edit.php?anchor='.urlencode('section:'.$item['id']) => IMAGE_TOOL_IMG.i18n::s('Add an image') ));
	}

	// bulk operations
	$context['page_menu'] = array_merge($context['page_menu'], array( Sections::get_url($item['id'], 'bulk') => i18n::s('Bulk') ));

	// lock the page
	if(!isset($item['locked']) || ($item['locked'] == 'N'))
		$context['page_menu'] = array_merge($context['page_menu'], array( Sections::get_url($item['id'], 'lock') => i18n::s('Lock') ));
	else
		$context['page_menu'] = array_merge($context['page_menu'], array( Sections::get_url($item['id'], 'lock') => i18n::s('Unlock') ));

	// delete the section
	Skin::define_img('DELETE_SECTION_IMG', $context['skin'].'/icons/sections/delete.gif');
	$context['page_menu'] = array_merge($context['page_menu'], array( Sections::get_url($item['id'], 'delete') => DELETE_SECTION_IMG.i18n::s('Delete') ));

}

// add a file
if(isset($item['id']) && !$zoom_type && $permitted && Files::are_allowed($anchor, $item, TRUE))
	$context['page_menu'] = array_merge($context['page_menu'], array( 'files/edit.php?anchor='.urlencode('section:'.$item['id']) => FILE_TOOL_IMG.i18n::s('Upload a file') ));

// add a link
if(isset($item['id']) && !$zoom_type && $permitted && Links::are_allowed($anchor, $item, TRUE))
	$context['page_menu'] = array_merge($context['page_menu'], array( 'links/edit.php?anchor='.urlencode('section:'.$item['id']) => LINK_TOOL_IMG.i18n::s('Add a link') ));

// print
if(isset($item['id']) && !$zoom_type && $permitted && Surfer::is_logged()) {
	Skin::define_img('PRINT_TOOL_IMG', $context['skin'].'/icons/tools/print.gif');
	$context['page_menu'] = array_merge($context['page_menu'], array( Sections::get_url($item['id'], 'print') => array('', PRINT_TOOL_IMG.i18n::s('Print'), '', 'basic', '', i18n::s('Get a paper copy of this page.')) ));
}

// not found -- help web crawlers
if(!isset($item['id'])) {
	Safe::header('Status: 404 Not Found', TRUE, 404);
	Skin::error(i18n::s('No item has the provided id.'));

// permission denied
} elseif(!$permitted) {

	// anonymous users are invited to log in or to register
	if(!Surfer::is_logged())
		Safe::redirect($context['url_to_home'].$context['url_to_root'].'users/login.php?url='.urlencode(Sections::get_url($item['id'])));

	// permission denied to authenticated user
	Safe::header('Status: 403 Forbidden', TRUE, 403);
	Skin::error(i18n::s('You are not allowed to perform this operation.'));

// display the section
} else {

	// remember surfer visit
	Surfer::click('section:'.$item['id'], $item['active']);

	// increment silently the hits counter if not associate, nor creator -- editors are taken into account
	if(Surfer::is_associate())
		;
	elseif(Surfer::get_id() && isset($item['create_id']) && (Surfer::get_id() == $item['create_id']))
		;
	elseif(!$zoom_type) {
		$item['hits'] = isset($item['hits'])?($item['hits']+1):1;
		Sections::increment_hits($item['id']);
	}

	// initialize the rendering engine
	Codes::initialize(Sections::get_url($item['id'], 'view', $item['title']));

	//
	// set page image -- $context['page_image']
	//

	// the section or the anchor icon, if any
	if(isset($item['icon_url']) && $item['icon_url'])
		$context['page_image'] = $item['icon_url'];
	elseif(is_object($anchor))
		$context['page_image'] = $anchor->get_icon_url();

	//
	// set page meta_information -- $context['page_header'], etc.
	//

	// add meta information, if any
	if(isset($item['meta']) && $item['meta'])
		$context['page_header'] .= $item['meta'];

	// a meta link to a feeding page
	$context['page_header'] .= "\n".'<link rel="alternate" href="'.$context['url_to_root'].Sections::get_url($item['id'], 'feed').'" title="RSS" type="application/rss+xml"'.EOT;

	// a meta link to a description page (actually, rdf)
	$context['page_header'] .= "\n".'<link rel="meta" href="'.$context['url_to_root'].Sections::get_url($item['id'], 'describe').'" title="Meta Information" type="application/rdf+xml"'.EOT;

	// implement the pingback interface
	$context['page_header'] .= "\n".'<link rel="pingback" href="'.$context['url_to_root'].'services/ping.php"'.EOT;

	// implement the trackback interface
	$permanent_link = $context['url_to_home'].$context['url_to_root'].Sections::get_url($item['id']);
	if($context['with_friendly_urls'] == 'Y')
		$trackback_link = $context['url_to_home'].$context['url_to_root'].'links/trackback.php/section/'.$item['id'];
	else
		$trackback_link = $context['url_to_home'].$context['url_to_root'].'links/trackback.php?anchor=section:'.$item['id'];
	$context['page_header'] .= "\n".'<!--'
		."\n".'<rdf:RDF xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"'
		."\n".' 		xmlns:dc="http://purl.org/dc/elements/1.1/"'
		."\n".' 		xmlns:trackback="http://madskills.com/public/xml/rss/module/trackback/">'
		."\n".'<rdf:Description'
		."\n".' trackback:ping="'.$trackback_link.'"'
		."\n".' dc:identifier="'.$permanent_link.'"'
		."\n".' rdf:about="'.$permanent_link.'" />'
		."\n".'</rdf:RDF>'
		."\n".'-->';

	// a meta link to our blogging interface
	$context['page_header'] .= "\n".'<link rel="EditURI" href="'.$context['url_to_home'].$context['url_to_root'].'services/describe.php" title="RSD" type="application/rsd+xml"'.EOT;

	// set specific headers
	if(isset($item['introduction']) && $item['introduction'])
		$context['page_description'] = $item['introduction'];
	if(isset($item['create_name']) && $item['create_name'])
		$context['page_author'] = $item['create_name'];

	//
	// before page title -- $context['prefix']
	//

	$cache_id = 'sections/view.php?id='.$item['id'].'#prefix';
	if(!$text =& Cache::get($cache_id)) {

		// top icons
		if(!$zoom_type && ($anchors =& Sections::get_anchors_for_anchor('section:'.$item['id'], 'icon_top')) && ($items = Articles::list_by_date_for_anchor($anchors, 0, 12, 'thumbnails'))) {

			// generate HTML
			if(is_array($items))
				$content =& Skin::build_list($items, 'compact');
			else
				$content = (string)$items;

			// insert thumbnails before page title
			$text .= Skin::build_box('', '<br class="images_prefix"'.EOT.$content.'<br class="images_suffix"'.EOT, 'header1', 'top_icons');

		}

		// cache, whatever change, for 5 minutes
		Cache::put($cache_id, $text, 'stable', 300);
	}
	$context['prefix'] .= $text;

	//
	// set page details -- $context['page_details']
	//

	// do not mention details at follow-up pages
	if(!$zoom_type) {

		// details
		$context['page_details'] = '<p class="details">';
		$details = array();

		// additional details for associates and editors
		if(Surfer::is_empowered()) {

			// the nick name
			if($item['nick_name'])
				$details[] = '"'.$item['nick_name'].'"';

			// the creator of this section
			if($item['create_date'])
				$details[] = sprintf(i18n::s('posted by %s %s'), Users::get_link($item['create_name'], $item['create_address'], $item['create_id']), Skin::build_date($item['create_date']));

			// hide last edition if done by creator, and if less than 24 hours between creation and last edition
			if($item['create_date'] && ($item['create_id'] == $item['edit_id'])
					&& (SQL::strtotime($item['create_date'])+24*60*60 >= SQL::strtotime($item['edit_date'])))
				;

			// the last edition of this section
			else {

				if($item['edit_action'])
					$action = get_action_label($item['edit_action']);
				else
					$action = i18n::s('edited');

				$details[] = sprintf(i18n::s('%s by %s %s'), $action, Users::get_link($item['edit_name'], $item['edit_address'], $item['edit_id']), Skin::build_date($item['edit_date']));
			}

			// the number of hits
			if($item['hits'] > 1)
				$details[] = sprintf(i18n::s('%d hits'), $item['hits']);

		}

		// rank for this section
		if(Surfer::is_empowered() && Surfer::is_logged() && (intval($item['rank']) != 10000))
			$details[] = '{'.$item['rank'].'}';

		// inline details
		if(count($details))
			$context['page_details'] .= ucfirst(implode(', ', $details)).BR."\n";

		// one detail per line
		$details = array();

		// restricted to logged members
		if($item['active'] == 'R')
			$details[] = RESTRICTED_FLAG.' '.i18n::s('Access is restricted to authenticated members');

		// restricted to associates
		if($item['active'] == 'N')
			$details[] = PRIVATE_FLAG.' '.i18n::s('Access is restricted to associates and editors');

		// index panel
		if(Surfer::is_empowered() && Surfer::is_logged()) {

			// at the parent index page
			if($item['anchor']) {

				if(isset($item['index_panel']) && ($item['index_panel'] == 'extra'))
					$details[] = i18n::s('Is displayed at the parent section page among other extra boxes.');
				elseif(isset($item['index_panel']) && ($item['index_panel'] == 'extra_boxes'))
					$details[] = i18n::s('Topmost articles are displayed at the parent section page in distinct extra boxes.');
				elseif(isset($item['index_panel']) && ($item['index_panel'] == 'gadget'))
					$details[] = i18n::s('Is displayed in the middle of the parent section page, among other gadget boxes.');
				elseif(isset($item['index_panel']) && ($item['index_panel'] == 'gadget_boxes'))
					$details[] = i18n::s('First articles are displayed at the parent section page in distinct gadget boxes.');
				elseif(isset($item['index_panel']) && ($item['index_panel'] == 'icon_bottom'))
					$details[] = i18n::s('Article thumbnails are displayed at the bottom of the parent section page.');
				elseif(isset($item['index_panel']) && ($item['index_panel'] == 'icon_top'))
					$details[] = i18n::s('Article thumbnails are displayed at the top of the parent section page.');
				elseif(isset($item['index_panel']) && ($item['index_panel'] == 'news'))
					$details[] = i18n::s('Articles are listed at the parent section page, in the area reserved to flashy news.');

			// at the site map
			} else {

				if(isset($item['index_map']) && ($item['index_map'] == 'N'))
					$details[] = i18n::s('Is not publicly listed at the Site Map. Is listed with special sections, but only to associates.');
			}

		}

		// home panel
		if(Surfer::is_empowered() && Surfer::is_logged()) {
			if(isset($item['home_panel']) && ($item['home_panel'] == 'extra'))
				$details[] = i18n::s('Is displayed at the front page, among other extra boxes.');
			elseif(isset($item['home_panel']) && ($item['home_panel'] == 'extra_boxes'))
				$details[] = i18n::s('First articles are displayed at the front page in distinct extra boxes.');
			elseif(isset($item['home_panel']) && ($item['home_panel'] == 'gadget'))
				$details[] = i18n::s('Is displayed in the middle of the front page, among other gadget boxes.');
			elseif(isset($item['home_panel']) && ($item['home_panel'] == 'gadget_boxes'))
				$details[] = i18n::s('First articles are displayed at the front page in distinct gadget boxes.');
			elseif(isset($item['home_panel']) && ($item['home_panel'] == 'icon_bottom'))
				$details[] = i18n::s('Article thumbnails are displayed at the bottom of the front page.');
			elseif(isset($item['home_panel']) && ($item['home_panel'] == 'icon_top'))
				$details[] = i18n::s('Article thumbnails are displayed at the top of the front page.');
			elseif(isset($item['home_panel']) && ($item['home_panel'] == 'news'))
				$details[] = i18n::s('Articles are listed at the front page, in the area reserved to recent news.');
		}

		// signal sections to be activated
		$now = gmstrftime('%Y-%m-%d %H:%M:%S');
		if(Surfer::is_empowered() && Surfer::is_logged() && ($item['activation_date'] > $now))
			$details[] = DRAFT_FLAG.' '.sprintf(i18n::s('Section will be activated %s'), Skin::build_date($item['activation_date']));

		// expired section
		if(Surfer::is_empowered() && Surfer::is_logged() && ($item['expiry_date'] > NULL_DATE) && ($item['expiry_date'] <= $now))
			$details[] = EXPIRED_FLAG.' '.sprintf(i18n::s('Section has expired %s'), Skin::build_date($item['expiry_date']));

		// locked section
		if(Surfer::is_empowered() && Surfer::is_logged() && ($item['locked'] ==  'Y') )
			$details[] = LOCKED_FLAG.' '.i18n::s('Section is locked');

		// section editors and readers
		if(Surfer::is_empowered() && Surfer::is_logged()) {
			if($items = Members::list_editors_by_name_for_member('section:'.$item['id'], 0, 50, 'compact'))
				$details[] = sprintf(i18n::s('Editors: %s'), Skin::build_list($items, 'comma'));

			if($items = Members::list_readers_by_name_for_member('section:'.$item['id'], 0, 50, 'compact'))
				$details[] = sprintf(i18n::s('Readers: %s'), Skin::build_list($items, 'comma'));
		}

		// display details, if any
		if(count($details))
			$context['page_details'] .= ucfirst(implode(BR."\n", $details));

		// no more details
		$context['page_details'] .= "</p>\n";

	}

	//
	// update main panel -- $context['text']
	//

	// cache varies on $zoom_type and $zoom_index, or on $page
	if($zoom_type)
		$cache_id = 'sections/view.php?id='.$item['id'].'#text#'.$zoom_type.'#'.$zoom_index;
	else
		$cache_id = 'sections/view.php?id='.$item['id'].'#text#'.$page;
	if(!$text =& Cache::get($cache_id)) {

		// everything that comes after main text should be put in $bottom_text or in $bottom_menu
		$bottom_menu = array();
		$bottom_text = '';

		// display very few things if we are on a follow-up page (comments, files, etc.)
		if($zoom_type) {

			if(isset($item['introduction']) && $item['introduction'])
				$text .= Codes::beautify($item['introduction'], $item['options'])."<p> </p>\n";
			else
				$text .= Skin::cap(Codes::beautify($item['description'], $item['options']), 50)."<p> </p>\n";

		// else expose full details
		} else {

			// only at the first page
			if($page == 1) {

				// the introduction text, if any
				if(isset($item['introduction']) && trim($item['introduction']))
					$text .= Skin::build_block($item['introduction'], 'introduction');
				else
					$text .= BR;

				// get text related to the overlay, if any
				if(is_object($overlay))
					$text .= $overlay->get_text('view', $item);

			}

			// the beautified description, which is the actual page body
			if(trim($item['description'])) {

				// from 1..n to 0..n-1
				$page -= 1;

				// provide only the requested page
				$pages = preg_split('/\s*\[page\]\s*/is', $item['description']);
				if($page > count($pages))
					$page = count($pages)-1;
				if($page < 0)
					$page = 0;
				$description = $pages[ $page ];

				// if there are several pages, remove toc and toq codes
				if(count($pages) > 1)
					$description = preg_replace('/\s*\[(toc|toq)\]\s*/is', '', $description);

				// beautify the target page
				$description = Codes::beautify($description, $item['options'])."\n";

				// use adequate label
				if(is_object($overlay) && ($label = $overlay->get_label('description')))
					$text .= Skin::build_block($label, 'title').'<p>'.$description."</p>\n";
				else
					$text .= $description."\n";

				// if there are several pages, add navigation commands to browse them
				if(count($pages) > 1) {
					$page_menu = array( '_' => i18n::s('Pages') );
					$home = Sections::get_url($item['id'], 'view', $item['title']);
					$prefix = Sections::get_url($item['id'], 'navigate', 'pages');
					$page_menu = array_merge($page_menu, Skin::navigate($home, $prefix, count($pages), 1, $page+1));

					$text .= Skin::build_list($page_menu, 'menu_bar');
				}

			}

			// mask bottom commands, except on large content
			if(strlen($text) > 512) {

				// bookmark command
				if(isset($item['create_id']) && Surfer::get_id() && !Surfer::is_creator($item['create_id']) && !$in_watch_list) {

					$link = Users::get_url('article:'.$item['id'], 'track');

					Skin::define_img('WATCH_TOOL_IMG', $context['skin'].'/icons/tools/watch.gif');
					$bottom_menu = array_merge($bottom_menu, array($link => array('', WATCH_TOOL_IMG.i18n::s('Watch'), '', 'basic', '', i18n::s('Manage your watch list')) ));
				}

				// add explicit modification commands, if allowed to do so
				if(isset($item['id']) && !$zoom_type && Surfer::is_empowered() && Surfer::is_logged()) {

					// change this page
					Skin::define_img('EDIT_SECTION_IMG', $context['skin'].'/icons/sections/edit.gif');
					$bottom_menu = array_merge($bottom_menu, array( Sections::get_url($item['id'], 'edit') => array('', EDIT_SECTION_IMG.i18n::s('Edit'), '', 'basic', i18n::s('Update the content of this page')) ));

				}

			}

		}

		//
		// gadget boxes
		//

		// gadget boxes are featured only at the main index page
		if(!$zoom_type) {

			// all boxes
			$content = '';

			// one gadget box per article, from sub-sections
			if($anchors =& Sections::get_anchors_for_anchor('section:'.$item['id'], 'gadget_boxes')) {

				// up to 6 articles to be displayed as gadget boxes
				if($items = Articles::list_by_edition_date_for_anchor($anchors, 0, 7, 'boxes')) {
					foreach($items as $title => $attributes)
						$content .= Skin::build_box($title, $attributes['content'], 'gadget', $attributes['id'])."\n";
				}
			}

			// one gadget box per section, from sub-sections
			if($anchors =& Sections::get_anchors_for_anchor('section:'.$item['id'], 'gadget')) {

				// one box per section
				foreach($anchors as $anchor) {
					// sanity check
					if(!$section = Anchors::get($anchor))
						continue;

					$box = array( 'title' => '', 'list' => array(), 'text' => '');

					// link to the section page from box title
					$box['title'] =& Skin::build_box_title($section->get_title(), $section->get_url(), i18n::s('View the section'));

					// add sub-sections, if any
					if($related = Sections::list_by_title_for_anchor($anchor, 0, COMPACT_LIST_SIZE+1, 'compact')) {
						foreach($related as $url => $label) {
							if(is_array($label))
								$label = $label[0].' '.$label[1];
							$box['list'] = array_merge($box['list'], array($url => array('', $label, '', 'basic')));
						}
					}

					// list matching articles
					if((COMPACT_LIST_SIZE >= count($box['list'])) && ($items = Articles::list_by_edition_date_for_anchor($anchor, 0, COMPACT_LIST_SIZE+1 - count($box['list']), 'compact')))
						$box['list'] = array_merge($box['list'], $items);

					// add matching links, if any
					if((COMPACT_LIST_SIZE >= count($box['list'])) && ($items = Links::list_by_date_for_anchor($anchor, 0, COMPACT_LIST_SIZE+1 - count($box['list']), 'compact')))
						$box['list'] = array_merge($box['list'], $items);

					// more at the section page
					if(count($box['list']) > COMPACT_LIST_SIZE) {
						@array_splice($box['list'], COMPACT_LIST_SIZE);

						// link to the section page
						$box['list'] = array_merge($box['list'], array($section->get_url() => i18n::s('More pages').MORE_IMG));
					}

					// render the html for the box
					if(count($box['list']))
						$box['text'] =& Skin::build_list($box['list'], 'compact');

					// give a chance to associates to populate empty sections
					elseif(Surfer::is_empowered())
						$box['text'] = Skin::build_link($section->get_url(), i18n::s('View the section'), 'shortcut');

					// append a box
					if($box['text'])
						$content .= Skin::build_box($box['title'], $box['text'], 'gadget');

				}

			}

			// leverage CSS
			if($content)
				$content = '<p id="gadgets_prefix"> </p>'."\n".$content.'<p id="gadgets_suffix"> </p>'."\n";

			// add after main menu bar
			$bottom_text .= $content;
		}

		//
		// sub-sections, if any
		//

		// the list of related sections if not at another follow-up page
		if(!$zoom_type || ($zoom_type == 'sections')) {

			// display sub-sections as a Freemind map, except to search engines
			if(isset($item['sections_layout']) && ($item['sections_layout'] == 'freemind') && !Surfer::is_crawler()) {
				$bottom_text .= Codes::render_freemind('section:'.$item['id'].', 100%, 400px');

			// use a regular layout
			} elseif(!isset($item['sections_layout']) || ($item['sections_layout'] != 'none')) {

				// select a layout
				if(!isset($item['sections_layout']) || !$item['sections_layout']) {
					include_once 'layout_sections.php';
					$layout =& new Layout_sections();
				} elseif($item['sections_layout'] == 'decorated') {
					include_once 'layout_sections.php';
					$layout =& new Layout_sections();
				} elseif($item['sections_layout'] == 'map') {
					include_once 'layout_sections_as_yahoo.php';
					$layout =& new Layout_sections_as_yahoo();
				} elseif(is_readable($context['path_to_root'].'sections/layout_sections_as_'.$item['sections_layout'].'.php')) {
					$name = 'layout_sections_as_'.$item['sections_layout'];
					include_once $name.'.php';
					$layout =& new $name;
				} else {

					// useful warning for associates
					if(Surfer::is_associate())
						Skin::error(sprintf(i18n::s('Warning: No script exists for the customized layout %s'), $item['sections_layout']));

					include_once '../sections/layout_sections.php';
					$layout =& new Layout_sections();
				}

				// the maximum number of sections per page
				if(isset($item['sections_count']) && ($item['sections_count'] > 1))
					$items_per_page = $item['sections_count'];
				elseif(is_object($layout))
					$items_per_page = $layout->items_per_page();
				else
					$items_per_page = SECTIONS_PER_PAGE;

				// build a complete box
				$box = array('bar' => array(), 'text' => '');

				// count the number of subsections
				if($count = Sections::count_for_anchor('section:'.$item['id'])) {
					if($count > $items_per_page)
						$box['bar'] = array('_count' => sprintf(i18n::ns('1 section', '%d sections', $count), $count));

					// list items by title
					$offset = ($zoom_index - 1) * $items_per_page;
					$items = Sections::list_by_title_for_anchor('section:'.$item['id'], $offset, $items_per_page, $layout);

					// navigation commands for sections
					$home = Sections::get_url($item['id'], 'view', $item['title']);
					$prefix = Sections::get_url($item['id'], 'navigate', 'sections');
					$box['bar'] = array_merge($box['bar'],
						Skin::navigate($home, $prefix, $count, $items_per_page, $zoom_index));

					// some sub sections have been attached to this page
					if(@count($items) > 0) {

						// the command to post a new section, for associates
						$url = 'sections/edit.php?anchor='.urlencode('section:'.$item['id']);
						if(Surfer::is_empowered())
							$box['bar'] = array_merge($box['bar'], array( $url => i18n::s('Add a section') ));
					}

					// actually render the html for the section
					if(is_array($items) && is_string($item['sections_layout']) && ($item['sections_layout'] == 'compact'))
						$box['text'] .= Skin::build_list($items, 'compact');
					elseif(is_array($items) && is_string($layout) && ($layout == 'decorated'))
						$box['text'] .= Skin::build_list($items, 'decorated');
					elseif(is_array($items))
						$box['text'] .= Skin::build_list($items, '2-columns');
					elseif(is_string($items))
						$box['text'] .= $items;

				}

				// build a box
				if($box['text']) {

					// show commands
					if(count($box['bar']) && ($context['skin_variant'] != 'mobile')) {

						// append the menu bar at the end
						$box['text'] .= Skin::build_list($box['bar'], 'menu_bar');

						// don't repeat commands before the box
						$box['bar'] = array();

					}

					// insert a full box
					$box['text'] =& Skin::build_box('', $box['text'], 'header1', 'sub-sections');
				}

				// integrate commands in bottom menu
				if(count($box['bar']))
					$bottom_menu = array_merge($bottom_menu, $box['bar']);

				// there is some box content
				if(trim($box['text']))
					$bottom_text .= $box['text'];

			}
		}

		//
		// articles related to this section, or to sub-sections
		//

		// the list of related articles if not at another follow-up page
		if( (!$zoom_type || ($zoom_type == 'articles'))
			&& (!isset($item['articles_layout']) || ($item['articles_layout'] != 'none')) ) {

			// this is a slideshow
			if(preg_match('/\bwith_slideshow\b/i', $item['options'])) {

				// explain what we are talking about
				$description = '<p>'.sprintf(i18n::s('Content of this section has been designed as an interactive on-line presentation. Navigate using the keyboard or a pointing device as usual. Use letter C to display control, and letter B to switch to/from a black screen. Based on the %s technology.'), Skin::build_link('http://www.meyerweb.com/eric/tools/s5/', i18n::s('S5'), 'external')).'</p>';

				// the label
				Skin::define_img('PLAY_IMG', $context['skin'].'/icons/files/play.gif');
				$label = PLAY_IMG.' '.sprintf(i18n::s('Play %s'), str_replace('_', ' ', $item['title']));

				// hovering the link
				$title = i18n::s('Start the show');

				// use a definition list to enable customization of the download box
				$bottom_text .= '<dl class="download">'
					.'<dt>'.Skin::build_link(Sections::get_url($item['id'], 'slideshow'), $label, 'basic', $title).'</dt>'
					.'<dd>'.$description.'</dd></dl>'."\n";

			}

			// only associates and editors can list pages of a slideshow
			if(Surfer::is_empowered() || !preg_match('/\bwith_slideshow\b/i', $item['options'])) {

				// delegate rendering to the overlay, where applicable
				if(isset($content_overlay) && is_object($content_overlay) && is_callable(array($content_overlay, 'render_articles_for_anchor'))) {
					$bottom_text .= $content_overlay->render_articles_for_anchor('section:'.$item['id'], $zoom_index);

				// regular rendering
				} else {

					// select a layout
					if(!isset($item['articles_layout']) || !$item['articles_layout']) {
						include_once '../articles/layout_articles.php';
						$layout =& new Layout_articles();
					} elseif($item['articles_layout'] == 'decorated') {
						include_once '../articles/layout_articles.php';
						$layout =& new Layout_articles();
					} elseif($item['articles_layout'] == 'map') {
						include_once '../articles/layout_articles_as_yahoo.php';
						$layout =& new Layout_articles_as_yahoo();
					} elseif($item['articles_layout'] == 'wiki') {
						include_once '../articles/layout_articles.php';
						$layout =& new Layout_articles();
					} elseif(is_readable($context['path_to_root'].'articles/layout_articles_as_'.$item['articles_layout'].'.php')) {
						$name = 'layout_articles_as_'.$item['articles_layout'];
						include_once $context['path_to_root'].'articles/'.$name.'.php';
						$layout =& new $name;
					} else {

						// useful warning for associates
						if(Surfer::is_associate())
							Skin::error(sprintf(i18n::s('Warning: No script exists for the customized layout %s'), $item['articles_layout']));

						include_once '../articles/layout_articles.php';
						$layout =& new Layout_articles();
					}

					// avoid links to this page
					if(is_object($layout) && is_callable(array($layout, 'set_variant')))
						$layout->set_variant('section:'.$item['id']);

					// the maximum number of articles per page
					if(is_object($layout))
						$items_per_page = $layout->items_per_page();
					else
						$items_per_page = ARTICLES_PER_PAGE;

					// create a box
					$box = array('bar' => array(), 'prefix_bar' => array(), 'text' => '');

					// list articles by date (default) or by title (option 'articles_by_title')
					$offset = ($zoom_index - 1) * $items_per_page;
					if(preg_match('/\barticles_by_title\b/i', $item['options']))
						$items =& Articles::list_for_anchor_by('title', 'section:'.$item['id'], $offset, $items_per_page, $layout);
					elseif(preg_match('/\barticles_by_publication\b/i', $item['options']))
						$items =& Articles::list_for_anchor_by('publication', 'section:'.$item['id'], $offset, $items_per_page, $layout);
					elseif(preg_match('/\barticles_by_rating\b/i', $item['options']))
						$items =& Articles::list_for_anchor_by('rating', 'section:'.$item['id'], $offset, $items_per_page, $layout);
					elseif(preg_match('/\barticles_by_reverse_rank\b/i', $item['options']))
						$items =& Articles::list_for_anchor_by('reverse_rank', 'section:'.$item['id'], $offset, $items_per_page, $layout);
					else
						$items =& Articles::list_for_anchor('section:'.$item['id'], $offset, $items_per_page, $layout);

					// no navigation bar with alistapart
					if(!isset($item['articles_layout']) || ($item['articles_layout'] != 'alistapart')) {

						// count the number of articles in this section
						if($count = Articles::count_for_anchor('section:'.$item['id'])) {
							if($count > $items_per_page)
								$box['bar'] = array_merge($box['bar'], array('_count' => sprintf(i18n::ns('1 page', '%d pages', $count), $count)));

							// navigation commands for articles
							$home = Sections::get_url($item['id'], 'view', $item['title']);
							$prefix = Sections::get_url($item['id'], 'navigate', 'articles');
							$box['bar'] = array_merge($box['bar'],
								Skin::navigate($home, $prefix, $count, $items_per_page, $zoom_index));

						}

						// some articles have been attached to this page
						if(@count($items) > 0) {

							// the command to post a new page
							if(Articles::are_allowed($anchor, $item)) {

								Skin::define_img('NEW_THREAD_IMG', $context['skin'].'/icons/articles/new_thread.gif');
								$url = 'articles/edit.php?anchor='.urlencode('section:'.$item['id']);
								if($item['articles_layout'] == 'jive')
									$label = NEW_THREAD_IMG.' '.i18n::s('Start a new topic');
								elseif($item['articles_layout'] == 'yabb')
									$label = NEW_THREAD_IMG.' '.i18n::s('Start a new topic');
								else
									$label = i18n::s('Add a page');
								$box['bar'] = array_merge($box['bar'], array( $url => $label ));

							}

							// the command to create a new poll, if no overlay nor template has been defined for content of this section
							if((!isset($item['content_overlay']) || !trim($item['content_overlay'])) && (!isset($item['articles_templates']) || !trim($item['articles_templates'])) && (!is_object($anchor) || !$anchor->get_templates_for('article')) && Articles::are_allowed($anchor, $item)) {

								$url = 'articles/edit.php?anchor='.urlencode('section:'.$item['id']).'&amp;variant=poll';
								Skin::define_img('POLL_IMG', $context['skin'].'/icons/articles/poll.gif');
								$label = i18n::s('Add a poll');
								$box['bar'] = array_merge($box['bar'], array( $url => POLL_IMG.$label ));

							}
						}
					}

					// actually render the html for the box
					if(is_array($items) && isset($item['articles_layout']) && ($item['articles_layout'] == 'compact'))
						$box['text'] .= Skin::build_list($items, 'compact');
					elseif(is_array($items))
						$box['text'] .= Skin::build_list($items, 'decorated');
					elseif(is_string($items))
						$box['text'] .= $items;

					// part of the main content
					if($box['text']) {

						// add a title in case of complex page
						$title = '';
						if(preg_match('/<h2>|<h3>|class="section_box"/', $context['text'].$bottom_text))
							$title = i18n::s('Recent pages');

						// show commands
						if(@count($box['bar']) && ($context['skin_variant'] != 'mobile')) {

							// append the menu bar at the end
							$box['text'] .= Skin::build_list($box['bar'], 'menu_bar');

							// commands before the box
							$box['bar'] = $box['prefix_bar'];

						}

						// insert a full box
						$box['text'] =& Skin::build_box($title, $box['text'], 'header1', 'articles');

					}

					// integrate commands in bottom menu
					if(count($box['bar']))
						$bottom_menu = array_merge($bottom_menu, $box['bar']);

					// there is some box content
					if(trim($box['text']))
						$bottom_text .= $box['text'];

				}

				// newest articles of main sub-sections, if not at another follow-up page
				if(!$zoom_type && !preg_match('/\bno_new_articles\b/i', $item['options'])) {

					// select a layout
					if(!isset($item['articles_layout']) || !$item['articles_layout']) {
						include_once '../articles/layout_articles.php';
						$layout =& new Layout_articles();
					} elseif($item['articles_layout'] == 'decorated') {
						include_once '../articles/layout_articles.php';
						$layout =& new Layout_articles();
					} elseif($item['articles_layout'] == 'map') {
						include_once '../articles/layout_articles_as_yahoo.php';
						$layout =& new Layout_articles_as_yahoo();
					} elseif($item['articles_layout'] == 'wiki') {
						include_once '../articles/layout_articles.php';
						$layout =& new Layout_articles();
					} elseif(is_readable($context['path_to_root'].'articles/layout_articles_as_'.$item['articles_layout'].'.php')) {
						$name = 'layout_articles_as_'.$item['articles_layout'];
						include_once $context['path_to_root'].'articles/'.$name.'.php';
						$layout =& new $name;
					} else {

						// useful warning for associates
						if(Surfer::is_associate())
							Skin::error(sprintf(i18n::s('Warning: No script exists for the customized layout %s'), $item['articles_layout']));

						include_once '../articles/layout_articles.php';
						$layout =& new Layout_articles();
					}

					// avoid links to this page
					if(is_object($layout) && is_callable(array($layout, 'set_variant')))
						$layout->set_variant('section:'.$item['id']);

					// the maximum number of articles per page
					if(is_object($layout))
						$items_per_page = $layout->items_per_page();
					else
						$items_per_page = ARTICLES_PER_PAGE;

					// sub-sections targeting the main area
					if($anchors =& Sections::get_anchors_for_anchor('section:'.$item['id'], 'main')) {

						// use ordering options set for the section
						if(preg_match('/\barticles_by_title\b/i', $item['options']))
							$items =& Articles::list_for_anchor_by('title', $anchors, 0, $items_per_page, $layout);
						elseif(preg_match('/\barticles_by_publication\b/i', $item['options']))
							$items =& Articles::list_for_anchor_by('publication', $anchors, 0, $items_per_page, $layout);
						elseif(preg_match('/\barticles_by_rating\b/i', $item['options']))
							$items =& Articles::list_for_anchor_by('rating', $anchors, 0, $items_per_page, $layout);
						else
							$items =& Articles::list_for_anchor($anchors, 0, $items_per_page, $layout);

						// actually render the html for the section
						$content = '';
						if(is_array($items) && is_string($item['articles_layout']) && ($item['articles_layout'] == 'compact'))
							$content .= Skin::build_list($items, 'compact');
						elseif(is_array($items))
							$content .= Skin::build_list($items, 'decorated');
						elseif(is_string($items))
							$content .= $items;

						// part of the main content
						if($content) {

							// add a title in case of complex page
//							$title = '';
//							if(preg_match('/<h2>|<h3>|class="section_box"/', $context['text'].$bottom_text))
								$title = i18n::s('What is new?');

							// update the final page
							$bottom_text .= Skin::build_box($title, $content, 'header1', 'what_is_new');

						}
					}
				}

			}

		// show hidden articles to associates and editors
		} elseif( (!$zoom_type || ($zoom_type == 'articles'))
			&& isset($item['articles_layout']) && ($item['articles_layout'] == 'none')
			&& Surfer::is_empowered() ) {

			// make a compact list
			include_once '../articles/layout_articles_as_compact.php';
			$layout =& new Layout_articles_as_compact();

			// avoid links to this page
			if(is_object($layout) && is_callable(array($layout, 'set_variant')))
				$layout->set_variant('section:'.$item['id']);

			// the maximum number of articles per page
			if(is_object($layout))
				$items_per_page = $layout->items_per_page();
			else
				$items_per_page = ARTICLES_PER_PAGE;

			// list articles by date (default) or by title (option 'articles_by_title')
			$offset = ($zoom_index - 1) * $items_per_page;
			if(preg_match('/\barticles_by_title\b/i', $item['options']))
				$items =& Articles::list_for_anchor_by('title', 'section:'.$item['id'], $offset, $items_per_page, $layout);
			elseif(preg_match('/\barticles_by_publication\b/i', $item['options']))
				$items =& Articles::list_for_anchor_by('publication', 'section:'.$item['id'], $offset, $items_per_page, $layout);
			elseif(preg_match('/\barticles_by_rating\b/i', $item['options']))
				$items =& Articles::list_for_anchor_by('rating', 'section:'.$item['id'], $offset, $items_per_page, $layout);
			elseif(preg_match('/\barticles_by_reverse_rank\b/i', $item['options']))
				$items =& Articles::list_for_anchor_by('reverse_rank', 'section:'.$item['id'], $offset, $items_per_page, $layout);
			else
				$items =& Articles::list_for_anchor('section:'.$item['id'], $offset, $items_per_page, $layout);

			// actually render the html for the box
			$content = '';
			if(is_array($items))
				$content = Skin::build_list($items, 'compact');
			else
				$content = $items;

			// make a complete box
			if($content)
				$bottom_text .= Skin::build_box(i18n::s('Hidden pages'), $content, 'header1', 'articles');
		}

		//
		// files attached to this section
		//

		// the list of related files if not at another follow-up page
		if(!$zoom_type || ($zoom_type == 'files')) {

			// build a complete box
			$box = array('bar' => array(), 'text' => '');

			// count the number of files in this section
			if($count = Files::count_for_anchor('section:'.$item['id'])) {
				$box['bar'] = array_merge($box['bar'], array('_count' => sprintf(i18n::ns('1 file', '%d files', $count), $count)));

				// list files by date (default) or by title (option 'files_by_title')
				$offset = ($zoom_index - 1) * FILES_PER_PAGE;
				if(preg_match('/\bfiles_by_title\b/i', $item['options']))
					$items = Files::list_by_title_for_anchor('section:'.$item['id'], $offset, FILES_PER_PAGE);
				else
					$items = Files::list_by_date_for_anchor('section:'.$item['id'], $offset, FILES_PER_PAGE);

				// actually render the html
				if(is_array($items))
					$box['text'] .= Skin::build_list($items, 'decorated');
				elseif(is_string($items))
					$box['text'] .= $items;

				// navigation commands for files
				$home = Sections::get_url($item['id'], 'view', $item['title']);
				$prefix = Sections::get_url($item['id'], 'navigate', 'files');
				$box['bar'] = array_merge($box['bar'],
					Skin::navigate($home, $prefix, $count, FILES_PER_PAGE, $zoom_index));

			}

			// the command to post a new file -- check 'with_files' option
			if(Files::are_allowed($anchor, $item, TRUE)) {
				$url = 'files/edit.php?anchor='.urlencode('section:'.$item['id']);
				$box['bar'] = array_merge($box['bar'], array( $url => FILE_TOOL_IMG.i18n::s('Upload a file') ));

			}

			// build a box
			if($box['text']) {

				// show commands
				if(count($box['bar']) && ($context['skin_variant'] != 'mobile')) {

					// append the menu bar at the end
					$box['text'] .= Skin::build_list($box['bar'], 'menu_bar');

				}

				// don't repeat commands before the box
				$box['bar'] = array();

				// insert a full box
				$box['text'] =& Skin::build_box(i18n::s('Files'), $box['text'], 'header1', 'files');
			}

			// integrate commands in bottom menu
			if(count($box['bar']))
				$bottom_menu = array_merge($bottom_menu, $box['bar']);

			// there is some box content
			if(trim($box['text']))
				$bottom_text .= $box['text'];

		}

		//
		// attached comments
		//

		// the list of related comments if not at another follow-up page
		if(!$zoom_type || ($zoom_type == 'comments')) {

			// title label
			if(is_object($anchor) && $anchor->is_viewable())
				$title_label = ucfirst($anchor->get_label('comments', 'count_many'));
			else
				$title_label = i18n::s('Your comments');

			// layout is defined in options
			if($item['articles_layout'] == 'boxesandarrows') {
				include_once '../comments/layout_comments_as_boxesandarrows.php';
				$layout =& new Layout_comments_as_boxesandarrows();

			} elseif($item['articles_layout'] == 'daily') {
				include_once '../comments/layout_comments_as_daily.php';
				$layout =& new Layout_comments_as_daily();

			} elseif($item['articles_layout'] == 'jive') {
				include_once '../comments/layout_comments_as_jive.php';
				$layout =& new Layout_comments_as_jive();

			} elseif($item['articles_layout'] == 'manual') {
				include_once '../comments/layout_comments_as_manual.php';
				$layout =& new Layout_comments_as_manual();

			} elseif($item['articles_layout'] == 'yabb') {
				include_once '../comments/layout_comments_as_yabb.php';
				$layout =& new Layout_comments_as_yabb();

			} else {
				include_once '../comments/layout_comments.php';
				$layout =& new Layout_comments();
			}

			// the maximum number of comments per page
			if(is_object($layout))
				$items_per_page = $layout->items_per_page();
			else
				$items_per_page = COMMENTS_PER_PAGE;

			// the first comment to list
			$offset = ($zoom_index - 1) * $items_per_page;
			if(is_object($layout) && method_exists($layout, 'set_offset'))
				$layout->set_offset($offset);

			// build a complete box
			$box = array('bar' => array(), 'text' => '');

			// a navigation bar for these comments
			if($zoom_type && ($zoom_type == 'comments'))
				$link = '_count';
			if($count = Comments::count_for_anchor('section:'.$item['id'])) {
				if($count > $items_per_page)
					$box['bar'] = array_merge($box['bar'], array($link => sprintf(i18n::s('%d comments'), $count)));

				// list comments by date
				$items = Comments::list_by_date_for_anchor('section:'.$item['id'], $offset, $items_per_page, $layout);

				// actually render the html
				if(is_array($items))
					$box['text'] .= Skin::build_list($items, 'rows');
				elseif(is_string($items))
					$box['text'] .= $items;

				// navigation commands for comments
				$prefix = Comments::get_url('section:'.$item['id'], 'navigate');
				$box['bar'] = array_merge($box['bar'],
					Skin::navigate(NULL, $prefix, $count, $items_per_page, $zoom_index, FALSE, TRUE));

			}

			// new comments are allowed -- check option 'with_comments'
			if(Comments::are_allowed($anchor, $item, TRUE))
				$box['bar'] = array_merge($box['bar'], array( Comments::get_url('section:'.$item['id'], 'comment') => array('', COMMENT_TOOL_IMG.i18n::s('Add a comment'), '', 'basic', '', i18n::s('Express yourself, and say what you think.'))));

			// build a box
			if($box['text']) {

				// put a title if there are other titles or if more than 2048 chars
				$title = '';
				if(preg_match('/(<h1|<h2|<h3|<table|\[title|\[subtitle)/i', $context['text'].$bottom_text) || (strlen($context['text'].$bottom_text) > 2048))
					$title = $title_label;

				// show commands
				if(count($box['bar']) && ($context['skin_variant'] != 'mobile')) {

					// shortcut to last comment in page
					if(is_object($layout) && ($stats['count'] > 7)) {
						$box['text'] = Skin::build_list(array('#last_comment' => i18n::s('Page bottom')), 'menu_bar').$box['text'];
						$box['text'] .= '<span id="last_comment" />';
					}

					// append the menu bar at the end
					$box['text'] .= Skin::build_list($box['bar'], 'menu_bar');

					// don't repeat commands before the box
					$box['bar'] = array();

				}

				// insert a full box
				$box['text'] =& Skin::build_box($title, $box['text'], 'header1', 'comments');
			}

			// integrate commands in bottom menu
			if(count($box['bar']))
				$bottom_menu = array_merge($bottom_menu, $box['bar']);

			// there is some box content
			if(trim($box['text']))
				$bottom_text .= $box['text'];

		}

		//
		// links attached to this section
		//

		// the list of related links if not at another follow-up page
		if(!$zoom_type || ($zoom_type == 'links')) {

			// build a complete box
			$box = array('bar' => array(), 'text' => '');

			// a navigation bar for these links
			if($count = Links::count_for_anchor('section:'.$item['id'])) {
				if($count > LINKS_PER_PAGE)
					$box['bar'] = array_merge($box['bar'], array('_count' => sprintf(i18n::ns('1 link', '%d links', $count), $count)));

				// list links by date (default) or by title (option 'links_by_title')
				$offset = ($zoom_index - 1) * LINKS_PER_PAGE;
				if(preg_match('/\blinks_by_title\b/i', $item['options']))
					$items = Links::list_by_title_for_anchor('section:'.$item['id'], $offset, LINKS_PER_PAGE, 'no_anchor');
				else
					$items = Links::list_by_date_for_anchor('section:'.$item['id'], $offset, LINKS_PER_PAGE, 'no_anchor');

				// actually render the html
				if(is_array($items))
					$box['text'] .= Skin::build_list($items, 'rows');
				elseif(is_string($items))
					$box['text'] .= $items;

				// navigation commands for links
				$home = Sections::get_url($item['id'], 'view', $item['title']);
				$prefix = Sections::get_url($item['id'], 'navigate', 'links');
				$box['bar'] = array_merge($box['bar'],
					Skin::navigate($home, $prefix, $count, LINKS_PER_PAGE, $zoom_index));

			}

			// new links are allowed -- check option 'with_links'
			if(Links::are_allowed($anchor, $item, TRUE)) {

				// the command to post a new link
				Skin::define_img('NEW_LINK_IMG', $context['skin'].'/icons/links/new.gif');
				$url = 'links/edit.php?anchor='.urlencode('section:'.$item['id']);
				$box['bar'] = array_merge($box['bar'], array( $url => NEW_LINK_IMG.i18n::s('Add a link') ));

			}

			// build a box
			if($box['text']) {

				// show commands
				if(count($box['bar']) && ($context['skin_variant'] != 'mobile')) {

					// append the menu bar at the end
					$box['text'] .= Skin::build_list($box['bar'], 'menu_bar');

					// don't repeat commands before the box
					$box['bar'] = array();

				}

				// insert a full box
				$box['text'] =& Skin::build_box(i18n::s('See also'), $box['text'], 'header1', 'links');

			}

			// integrate commands in bottom menu
			if(count($box['bar']))
				$bottom_menu = array_merge($bottom_menu, $box['bar']);

			// there is some box content
			if(trim($box['text']))
				$bottom_text .= $box['text'];

		}

		//
		// inactive sub sections
		//

		// associates may list special sections as well
		if(!$zoom_type && Surfer::is_empowered()) {

			// no special item yet
			$items = array();

			// if sub-sections are rendered by Freemind applet, also provide regular links to empowered surfers
			if(isset($item['sections_layout']) && ($item['sections_layout'] == 'freemind'))
				$items = Sections::list_by_title_for_anchor('section:'.$item['id'], 0, 50, 'compact');

			// append inactive sections, if any
			$items = array_merge($items, Sections::list_inactive_by_title_for_anchor('section:'.$item['id'], 0, 50, 'compact'));

			// we have an array to format
			if(count($items)) {
				$content =& Skin::build_list($items, 'compact');

				// displayed as another box
				$bottom_text .= Skin::build_box(i18n::s('Special sections'), $content, 'header1', 'special_sections');

			}
		}

		//
		// trailer information
		//

		// insert bottom menu, if any
		if(count($bottom_menu))
			$text .= Skin::build_list($bottom_menu, 'menu_bar');

		// insert text collected so far
		$text .= $bottom_text;

		// add trailer information from this item, if any
		if(isset($item['trailer']) && trim($item['trailer']))
			$text .= Codes::beautify($item['trailer']);

		//
		// save in cache
		//

		Cache::put($cache_id, $text, 'section:'.$item['id']);

	}

	// update the main content panel
	$context['text'] .= $text;

	//
	// after everything else -- $context['suffix']
	//

	$cache_id = 'sections/view.php?id='.$item['id'].'#suffix';
	if(!$text =& Cache::get($cache_id)) {

		// bottom icons
		if(!$zoom_type && ($anchors =& Sections::get_anchors_for_anchor('section:'.$item['id'], 'icon_bottom')) && ($items = Articles::list_by_date_for_anchor($anchors, 0, 12, 'thumbnails'))) {

			// generate HTML
			if(is_array($items))
				$text =& Skin::build_list($items, 'compact');
			else
				$text = (string)$items;

			// make a box with a frame of images
			$text .= Skin::build_box('', '<br class="images_prefix"'.EOT.$text.'<br class="images_suffix"'.EOT, 'header1', 'bottom_icons');

		}

		// cache, whatever change, for 5 minutes
		Cache::put($cache_id, $text, 'stable', 300);
	}
	$context['suffix'] .= $text;

	//
	// populate the extra panel
	//

	// cache content
	$cache_id = 'sections/view.php?id='.$item['id'].'#extra';
	if(!$text =& Cache::get($cache_id)) {

		// show creator profile, if required to do so
		if(preg_match('/\bwith_creator_profile\b/', $item['options']) && ($poster = Users::get($item['create_id'])) && ($section = Anchors::get('section:'.$item['id'])))
			$text .= $section->get_user_profile($poster, 'extra');

		// show news -- set in sections/edit.php
		if($item['index_news'] != 'none') {

			// news from sub-sections where index_panel == 'news'
			if($anchors =& Sections::get_anchors_for_anchor('section:'.$item['id'], 'news')) {

				// build a complete box
				$box['bar'] = array();
				$box['text'] = '';

				// set in sections/edit.php
				if($item['index_news_count'] < 1)
					$item['index_news_count'] = 7;

				// list articles by date
				$items = Articles::list_by_date_for_anchor($anchors, 0, $item['index_news_count'], 'news');

				// render html
				if(is_array($items))
					$box['text'] .= Skin::build_list($items, 'news');
				elseif(is_string($items))
					$box['text'] .= $items;

				// we do have something to display
				if($box['text']) {

					// animate the text if required to do so
					if($item['index_news'] == 'scroll') {
						$box['text'] = Skin::scroll($box['text']);
						$box['id'] = 'scrolling_news';
					} elseif($item['index_news'] == 'rotate') {
						$box['text'] = Skin::rotate($box['text']);
						$box['id'] = 'rotating_news';
					} else
						$box['id'] = 'news';

					// make an extra box -- the css id is either #news, #scrolling_news or #rotating_news
					$text .= Skin::build_box(i18n::s('In the news'), $box['text'], 'extra', $box['id']);
				}
			}
		}

		// the contextual menu, in a navigation box, if this has not been disabled
		if( (!is_object($anchor) || !$anchor->has_option('no_contextual_menu'))
			&& (!isset($item['options']) || !preg_match('/\bno_contextual_menu\b/i', $item['options']))
			&& isset($context['current_focus']) && ($menu =& Skin::build_contextual_menu($context['current_focus']))) {

			// use title from topmost level
			if(count($context['current_focus']) && ($anchor = Anchors::get($context['current_focus'][0]))) {
				$box_title = $anchor->get_title();
				$box_url = $anchor->get_url();

			// generic title
			} else {
				$box_title = i18n::s('Navigation');
				$box_url = '';
			}

			// in a navigation box
			$box_popup = '';
			$text .= Skin::build_box($box_title, $menu, 'navigation', 'contextual_menu', $box_url, $box_popup)."\n";
		}

		// twin pages
		if(isset($item['nick_name']) && $item['nick_name']) {

			// build a complete box
			$box['text'] = '';

			// list pages with same name
			$items = Sections::list_for_name($item['nick_name'], $item['id'], 'compact');

			// actually render the html for the section
			if(is_array($items))
				$box['text'] .= Skin::build_list($items, 'compact');
			if($box['text'])
				$text .= Skin::build_box(i18n::s('Related'), $box['text'], 'navigation', 'twins');

		}


		// add extra information from this item, if any
		if(isset($item['extra']) && $item['extra'])
			$text .= Codes::beautify($item['extra']);

		// one extra box per article, from sub-sections
		if($anchors =& Sections::get_anchors_for_anchor('section:'.$item['id'], 'extra_boxes')) {

			// the maximum number of boxes is a global parameter
			if(!isset($context['site_extra_maximum']) || !$context['site_extra_maximum'])
				$context['site_extra_maximum'] = 7;

			// articles to be displayed as extra boxes
			if($items = Articles::list_by_date_for_anchor($anchors, 0, $context['site_extra_maximum'], 'boxes')) {
				foreach($items as $title => $attributes)
					$text .= Skin::build_box($title, $attributes['content'], 'extra', $attributes['id'])."\n";
			}

		}

		// one extra box per section, from sub-sections
		if($anchors =& Sections::get_anchors_for_anchor('section:'.$item['id'], 'extra')) {

			// one box per section
			foreach($anchors as $anchor) {
				$box = array();

				// sanity check
				if(!$section = Anchors::get($anchor))
					continue;

				// link to the section page from box title
				$box['title'] =& Skin::build_box_title($section->get_title(), $section->get_url(), i18n::s('View the section'));

				// build a compact list
				$box['list'] = array();

				// list matching articles
				if($items = Articles::list_by_edition_date_for_anchor($anchor, 0, COMPACT_LIST_SIZE+1, 'compact'))
					$box['list'] = array_merge($box['list'], $items);

				// add matching links, if any
				if((COMPACT_LIST_SIZE >= count($box['list'])) && ($items = Links::list_by_date_for_anchor($anchor, 0, COMPACT_LIST_SIZE - count($box['list']), 'compact')))
					$box['list'] = array_merge($box['list'], $items);

				// add matching sections, if any
				if((COMPACT_LIST_SIZE >= count($box['list'])) && ($items = Sections::list_by_title_for_anchor($anchor, 0, COMPACT_LIST_SIZE - count($box['list']), 'compact')))
					$box['list'] = array_merge($box['list'], $items);

				// more at the section page
				if(count($box['list']) > COMPACT_LIST_SIZE) {
					@array_splice($box['list'], COMPACT_LIST_SIZE);

					// link to the section page
					$box['list'] = array_merge($box['list'], array($section->get_url() => i18n::s('More pages').MORE_IMG));
				}

				// render the html for the box
				if(count($box['list']))
					$box['text'] =& Skin::build_list($box['list'], 'compact');

				// give a chance to associates to populate empty sections
				elseif(Surfer::is_empowered())
					$box['text'] = Skin::build_link($section->get_url(), i18n::s('View the section'), 'shortcut');

				// append a box
				if($box['text'])
					$text .= Skin::build_box($box['title'], $box['text'], 'navigation');

			}
		}

		// categories attached to this section
		if(!$zoom_type || ($zoom_type == 'categories')) {

			// build a complete box
			$box['bar'] = array();
			$box['text'] = '';

			// list categories by title
			$offset = ($zoom_index - 1) * CATEGORIES_PER_PAGE;
			$items = Members::list_categories_by_title_for_member('section:'.$item['id'], $offset, CATEGORIES_PER_PAGE, 'sidebar');

			// the command to change categories assignments
			if(Categories::are_allowed($anchor, $item))
				$items = array_merge($items, array( Categories::get_url('section:'.$item['id'], 'select') => i18n::s('Assign categories') ));

			// actually render the html for the section
			if(@count($box['bar']))
				$box['text'] .= Skin::build_list($box['bar'], 'menu_bar');
			if(is_array($items))
				$box['text'] .= Skin::build_list($items, 'compact');
			if($box['text'])
				$text .= Skin::build_box(i18n::s('See also'), $box['text'], 'navigation', 'categories');

		}

		// get news from rss
		if(isset($item['id']) && (!isset($context['skins_general_without_feed']) || ($context['skins_general_without_feed'] != 'Y')) ) {

			$content = Skin::build_link($context['url_to_home'].$context['url_to_root'].Sections::get_url($item['id'], 'feed'), i18n::s('recent pages'), 'xml')
				.BR.Skin::build_link($context['url_to_home'].$context['url_to_root'].Files::get_url('section:'.$item['id'], 'feed'), i18n::s('podcasted files'), 'xml');

			// public aggregators
			if(!isset($context['without_internet_visibility']) || ($context['without_internet_visibility'] != 'Y'))
				$content .= BR.join(BR, Skin::build_subscribers($context['url_to_home'].$context['url_to_root'].Sections::get_url($item['id'], 'feed'), $item['title']));

			$text .= Skin::build_box(i18n::s('Stay tuned'), $content, 'extra', 'feeds');
		}

		// offer bookmarklets if submissions are allowed
		if(isset($item['id']) && Surfer::is_member() && (!isset($context['pages_without_bookmarklets']) || ($context['pages_without_bookmarklets'] != 'Y'))
			 && (Surfer::is_empowered() || (is_object($anchor) && $anchor->is_editable())
				|| ( (Surfer::is_member() || (isset($item['options']) && preg_match('/\banonymous_edit\b/i', $item['content_options'])) )
					&& (!isset($item['active']) || ($item['active'] != 'N'))
					&& (!isset($item['locked']) || ($item['locked'] != 'Y'))
					&& (!isset($context['users_without_submission']) || ($context['users_without_submission'] != 'Y'))) ) ) {

			// accessible bookmarklets
			$bookmarklets = array();

			// blogging bookmarklet uses YACS codes
			$bookmarklet = "javascript:function findFrame(f){var i;try{isThere=f.document.selection.createRange().text;}catch(e){isThere='';}if(isThere==''){for(i=0;i&lt;f.frames.length;i++){findFrame(f.frames[i]);}}else{s=isThere}return s}"
				."var s='';"
				."d=document;"
				."s=d.selection?findFrame(window):window.getSelection();"
				."window.location='".$context['url_to_home'].$context['url_to_root']."articles/edit.php?"
					."blogid=".$item['id']
					."&amp;title='+escape(d.title)+'"
					."&amp;text='+escape('%22'+s+'%22%5Bnl]-- %5Blink='+d.title+']'+d.location+'%5B/link]')+'"
					."&amp;source='+escape(d.location);";
			$bookmarklets[] = '<a href="'.$bookmarklet.'">'.sprintf(i18n::s('Blog at %s'), $item['title']).'</a>';

			// bookmark bookmarklet, if links are allowed
			if(isset($item['options']) && preg_match('/\bwith_links\b/i', $item['options'])) {
				$bookmarklet = "javascript:function findFrame(f){var i;try{isThere=f.document.selection.createRange().text;}catch(e){isThere='';}if(isThere==''){for(i=0;i&lt;f.frames.length;i++){findFrame(f.frames[i]);}}else{s=isThere}return s}"
					."var s='';"
					."d=document;"
					."s=d.selection?findFrame(window):window.getSelection();"
					."window.location='".$context['url_to_home'].$context['url_to_root']."links/edit.php?"
						."link='+escape(d.location)+'"
						."&amp;anchor='+escape('section:".$item['id']."')+'"
						."&amp;title='+escape(d.title)+'"
						."&amp;text='+escape(s);";

				if($item['nick_name'] == 'bookmarks')
					$name = strip_tags($context['site_name']);
				else
					$name = strip_tags($item['title']);
				$bookmarklets[] = '<a href="'.$bookmarklet.'">'.sprintf(i18n::s('Bookmark at %s'), $name).'</a>';
			}

			$label = i18n::ns('Bookmark following link to contribute here:', 'Bookmark following links to contribute here:', count($bookmarklets))."\n<ul>".'<li>'.implode('</li><li>', $bookmarklets).'</li></ul>'."\n";

			// an extra box
			$text .= Skin::build_box(i18n::s('Bookmarklets to contribute'), $label, 'extra', 'bookmarklets');
		}

		// list feeding servers, if any
		if($content = Servers::list_by_date_for_anchor('section:'.$item['id'])) {
			if(is_array($content))
				$content =& Skin::build_list($content, 'compact');
			$text .= Skin::build_box(i18n::s('Related servers'), $content, 'navigation', 'servers');
		}

		// how to reference this page
		if(Surfer::is_member() && !$zoom_type && (!isset($context['pages_without_reference']) || ($context['pages_without_reference'] != 'Y')) ) {

			// box content
			$label = sprintf(i18n::s('Here, use code %s'), '<code>[section='.$item['id'].']</code>')."\n"
				.BR.sprintf(i18n::s('Elsewhere, bookmark the %s'), Skin::build_link(Sections::get_url($item['id'], 'view', $item['title'], $item['nick_name']), i18n::s('full link')))."\n";

			// in a sidebar box
			$text .= Skin::build_box(i18n::s('Reference this page'), $label, 'navigation', 'reference');

		}

		// download content
		if(Surfer::is_member() && !$zoom_type && (!isset($context['pages_without_freemind']) || ($context['pages_without_freemind'] != 'Y')) ) {

			// box content
			$content = Skin::build_link(Sections::get_url($item['id'], 'freemind', utf8::to_ascii($context['site_name'].' - '.strip_tags(Codes::beautify_title(trim($item['title']))).'.mm')), i18n::s('Freemind map'), 'basic');

			// in a sidebar box
			$text .= Skin::build_box(i18n::s('Download'), Codes::beautify($content), 'navigation');

		}

		// referrals, if any
		if(!$zoom_type && (Surfer::is_empowered() || (isset($context['with_referrals']) && ($context['with_referrals'] == 'Y')))) {

			// box content
			include_once '../agents/referrals.php';
			if($content = Referrals::list_by_hits_for_url($context['url_to_root_parameter'].Sections::get_url($item['id'])))
				$text .= Skin::build_box(i18n::s('Referrals'), $content, 'navigation', 'referrals');

		}

		//
		// save in cache
		//

		Cache::put($cache_id, $text, 'section:'.$item['id']);

	}

	// update the extra panel
	$context['extra'] = $text.$context['extra'];

}

// render the skin
render_skin();

?>