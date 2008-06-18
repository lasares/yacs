<?php
/**
 * Transform some text containing UBB-like code sequences.
 *
 * @todo &#91;files] - most recent files, in a compact list
 * @todo &#91;files=section:&lt;id>] - files attached in the given section
 * @todo &#91;links] - most recent links, in a compact list
 * @todo &#91;links=section:&lt;id>] - links attached in the given section
 * @todo code [label|link]
 * @todo for [read, add hits aside
 * @todo add a code to build sparklines out of tables requests http://sourceforge.net/projects/sparkline/
 * @todo add a code to link images with clickable maps
 * @todo replace marquee with our own customizable scroller
 * @todo WiKi rendering for lists
 *
 * This module uses the Skin class for the actual rendering.
 *
 * Basic codes, demonstrated into [link]codes/basic.php[/link]:
 * - **...** - wiki bold text
 * - &#91;b]...[/b] - bold text
 * - //...// - italics
 * - &#91;i]...[/i] - italics
 * - __...__ - underlined
 * - &#91;u]...[/u] - underlined
 * - ##...## - monospace
 * - &#91;code]...[/code] - a short sample of fixed-size text (e.g. a file name)
 * - &#91;color]...[/color] - change font color
 * - &#91;tiny]...[/tiny] - tiny size
 * - &#91;small]...[/small] - small size
 * - &#91;big]...[/big] - big size
 * - &#91;huge]...[/huge] - huge size
 * - &#91;subscript]...[/subscript] - subscript
 * - &#91;superscript]...[/superscript] - superscript
 * - ++...++ - inserted
 * - &#91;inserted]...[/inserted] - inserted
 * - --...-- - deleted
 * - &#91;deleted]...[/deleted] - deleted
 * - &#91;flag]...[/flag] - draw attention
 * - &#91;style=sans-serif]...[/style] - use a sans-serif font
 * - &#91;style=serif]...[/style] - use a serif font
 * - &#91;style=cursive]...[/style] - mimic hand writing
 * - &#91;style=comic]...[/style] - make it funny
 * - &#91;style=fantasy]...[/style] - guess what will appear
 * - &#91;style=my_style]...[/style] - translated to &lt;span class="my_style"&gt;...&lt;/span&gt;
 *
 * @see codes/basic.php
 *
 * Block codes, demonstrated in [link]codes/blocks.php[/link]:
 * - &#91;indent]...[/indent] - shift text to the right
 * - &#91;center]...[/center] - some centered text
 * - &#91;right]...[/right] - some right-aligned text
 * - &#91;decorated]...[/decorated] - some pretty paragraphs
 * - &#91;caution]...[/caution] - a warning paragraph
 * - &#91;note]...[/note] - a noticeable paragraph
 * - &#91;php]...[/php] - a snippet of php
 * - &#91;snippet]...[/snippet] - a snippet of fixed font data
 * - &#91;quote]...[/quote] - a block of quoted text
 * - &#91;folder]...[/folder] - click to view its content, or to fold it away
 * - &#91;folder=foo bar]...[/folder] - with title 'foo bar'
 * - &#91;sidebar]...[/sidebar] - a nice box aside
 * - &#91;sidebar=foo bar]...[/sidebar] - with title 'foo bar'
 * - &#91;scroller]...[/scroller] - some scrolling text
 *
 * @see codes/blocks.php
 *
 * List codes, demonstrated in [link]codes/lists.php[/link]:
 * - &#91;*] - for simple lists
 * - &#91;list]...[/list] - bulleted list
 * - &#91;list=1]...[/list] - numbered list, use numbers
 * - &#91;list=a]...[/list] - numbered list, use letters
 * - &#91;list=A]...[/list] - numbered list, use capital letters
 * - &#91;list=i]...[/list] - numbered list, use roman numbers
 * - &#91;list=I]...[/list] - numbered list, use upper case roman numbers
 *
 * @see codes/lists.php
 *
 * Codes for links, demonstrated in [link]codes/links.php[/link]:
 * - &lt;url&gt; - &lt;a href="url">url&lt;/a> or &lt;a href="url" class="external">url&lt;/a>
 * - &#91;link]&lt;url&gt;[/link] - &lt;a href="url">url&lt;/a> or &lt;a href="url" class="external">url&lt;/a>
 * - &#91;link=&lt;label&gt;]&lt;url&gt;[/link] - &lt;a href="url">label&lt;/a> or &lt;a href="url" class="external">label&lt;/a>
 * - &#91;url]&lt;url&gt;[/url] - deprecated by &#91;link]
 * - &#91;url=&lt;url&gt;]&lt;label&gt;[/url] - deprecated by &#91;link]
 * - &#91;button=&lt;label&gt;]&lt;url&gt;[/button] - build simple buttons with css
 * - &lt;address&gt; - &lt;a href="mailto:address" class="email">address&lt;/a>
 * - &#91;email]&lt;address&gt;[/email] - &lt;a href="mailto:address" class="email">address&lt;/a>
 * - &#91;email=&lt;name&gt;]&lt;address&gt;[/email] - &lt;a href="mailto:address" class="email">name&lt;/a>
 * - &#91;go=&lt;name&gt;, &lt;label&gt;] - trigger the selector on 'name'
 * - &#91;article=&lt;id>] - use article title as link label
 * - &#91;article=&lt;id>, foo bar] - with label 'foo bar'
 * - &#91;next=&lt;id>] - shortcut to next article
 * - &#91;next=&lt;id>, foo bar] - with label 'foo bar'
 * - &#91;previous=&lt;id>] - shortcut to previous article
 * - &#91;previous=&lt;id>, foo bar] - with label 'foo bar'
 * - &#91;section=&lt;id>] - use section title as link label
 * - &#91;section=&lt;id>, foo bar] - with label 'foo bar'
 * - &#91;category=&lt;id>] - use category title as link label
 * - &#91;category=&lt;id>, foo bar] - with label 'foo bar'
 * - &#91;decision=&lt;id>] - use decision id in link label
 * - &#91;decision=&lt;id>, foo bar] - with label 'foo bar'
 * - &#91;user=&lt;id>] - use nick name as link label
 * - &#91;user=&lt;id>, foo bar] - with label 'foo bar'
 * - &#91;server=&lt;id>] - use server title as link label
 * - &#91;server=&lt;id>, foo bar] - with label 'foo bar'
 * - &#91;file=&lt;id>] - use file title as link label
 * - &#91;file=&lt;id>, foo bar] - with label 'foo bar'
 * - &#91;download=&lt;id>] - a link to download a file
 * - &#91;download=&lt;id>, foo bar] - with label 'foo bar'
 * - &#91;comment=&lt;id>] - use comment id in link label
 * - &#91;comment=&lt;id>, foo bar] - with label 'foo bar'
 * - &#91;script]&lt;path/script.php&gt;[/script] - to the phpDoc page for script 'path/script.php'
 * - &#91;search] - a search form
 * - &#91;search=&lt;word&gt;] - hit Enter to search for 'word'
 * - &#91;login] - a login form (to be used in menus)
 * - &#91;action=&lt;id>] - use action title as link label
 * - &#91;action=&lt;id>, foo bar] - with label 'foo bar'
 * - &#91;wikipedia=&lt;keyword] - search Wikipedia
 * - &#91;wikipedia=&lt;keyword, foo bar] - search Wikipedia, with label 'foo bar'
 *
 * @see codes/links.php
 *
 * Titles and questions, demonstrated in [link]codes/titles.php[/link]:
 * - &#91;toc] - table of contents
 * - ==...== - a level 1 headline
 * - &#91;title]...[/title] - a level 1 headline, put in the table of contents
 * - ===...=== - a level 2 headline
 * - &#91;subtitle]...[/subtitle] - a level 2 headline
 * - &#91;header1]...[/header1] - a level 1 headline
 * - &#91;header2]...[/header2] - a level 2 headline
 * - &#91;header3]...[/header3] - a level 3 headline
 * - &#91;header4]...[/header4] - a level 4 headline
 * - &#91;header5]...[/header5] - a level 5 headline
 * - &#91;toq] - the table of questions for this page
 * - &#91;question]...[/question] - a question-title
 * - &#91;question] - a simple question
 * - &#91;answer] - some answer in a FAQ
 *
 * @see codes/titles.php
 *
 * Tables, demonstrated in [link]codes/tables.php[/link]:
 * - &#91;table]...[/table] - one simple table
 * - &#91;table=grid]...[/table] - add a grid
 * - &#91;table].[body].[/table] - a table with headers
 * - &#91;csv]...[/csv] - import some data from Excel
 * - &#91;csv=;]...[/csv] - import some data from Excel
 *
 * @see codes/tables.php
 *
 * Live codes, demonstrated in [link]codes/live.php[/link]:
 * - &#91;cloud] - the tags used at this site
 * - &#91;cloud=12] - maximum count of tags used at this site
 * - &#91;locations=all] - newest locations
 * - &#91;locations=users] - map user locations on Google maps
 * - &#91;location=latitude, longitude, label] - to build a dynamic map
 * - &#91;collections] - list available collections
 * - &#91;published] - most recent published pages, in a compact list
 * - &#91;published=section:&lt;id>] - articles published most recently in the given section
 * - &#91;published=category:&lt;id>] - articles published most recently in the given category
 * - &#91;published=user:&lt;id>] - articles published most recently created by given user
 * - &#91;read] - most read articles, in a compact list
 * - &#91;read=section:&lt;id>] - articles of fame in the given section
 * - &#91;edited] - most recent edited pages, in a compact list
 * - &#91;edited=section:&lt;id>] - articles edited most recently in the given section
 * - &#91;edited=category:&lt;id>] - articles edited most recently in the given category
 * - &#91;edited=user:&lt;id>] - articles edited most recently created by given user
 * - &#91;commented] - most fresh threads, in a compact list
 * - &#91;commented=section:&lt;id>] - articles commented most recently in the given section
 * - &#91;contributed] - most contributed articles, in a compact list
 * - &#91;contributed=section:&lt;id>] - most contributed articles in the given section
 * - &#91;freemind] - a Freemind map of site content
 * - &#91;freemind=section:&lt;id>] - a Freemind map of a section and its content
 * - &#91;freemind=section:&lt;id>, width, height] - a Freemind map of a section and its content
 *
 * @see codes/live.php
 *
 * Miscellaneous codes, demonstrated in [link]codes/misc.php[/link]:
 * - &#91;hint=&lt;help popup]...[/hint] - &lt;acronym tite="help popup">...&lt;/acronym>
 * - &#91;nl] - new line
 * - ----... - line break
 * - &#91;---] or &#91;___] - horizontal rule
 * - &#91;new] - something new
 * - &#91;updated] - something updated
 * - &#91;popular] - people love it
 * - &#91;be] - country flag
 * - &#91;ca] - country flag
 * - &#91;ch] - country flag
 * - &#91;de] - country flag
 * - &#91;en] - country flag
 * - &#91;es] - country flag
 * - &#91;fr] - country flag
 * - &#91;gb] - country flag
 * - &#91;gr] - country flag
 * - &#91;it] - country flag
 * - &#91;pt] - country flag
 * - &#91;us] - country flag
 *
 * @see codes/misc.php
 *
 * In-line elements:
 * - &#91;flash=&lt;id>, width, height, flashparams] - play a Flash object
 * - &#91;flash=&lt;id>, window] - play a Flash object in a separate window
 * - &#91;freemind=&lt;id>] - a Freemind map out of given file
 * - &#91;sound=&lt;id>] - play a sound
 * - &#91;image=&lt;id>] - an inline image
 * - &#91;image=&lt;id>,left] - a left-aligned image
 * - &#91;image=&lt;id>,center] - a centered image
 * - &#91;image=&lt;id>,right] - a right-aligned image
 * - &#91;image]src[/image]
 * - &#91;image=&lt;alt>]src[/image]
 * - &#91;images=&lt;id1>, &lt;id2>, ...] - a stack of images
 * - &#91;img]src[/img] (deprecated)
 * - &#91;img=&lt;alt>]src[/img] (deprecated)
 * - &#91;table=&lt;id>] - an inline table
 * - &#91;location=&lt;id>] - embed a map
 * - &#91;location=&lt;id>, foo bar] - with label 'foo bar'
 * - &#91;clear] - to introduce breaks after floating elements
 *
 * @link http://www.estvideo.com/dew/index/2005/02/16/370-player-flash-mp3-leger-comme-une-plume the dewplayer page
 *
 * Other codes:
 * - &#91;menu=label]url[/menu] -> one of the main menu command
 * - &#91;submenu=label]url[/submenu]	-> one of the second-level menu commands
 * - &#91;escape]...[/escape]
 * - &#91;anonymous]...[/anonymous] 	-> for non-logged people only
 * - &#91;restricted]...[/restricted]		-> for logged members only
 * - &#91;hidden]...[/hidden]		-> for associates only
 * - &#91;parameter=name]	-> value of one attribute of the global context
 *
 *
 * This script attempts to fight bbCode code injections by filtering strings to be used
 * as [code]src[/code] or as [code]href[/code] attributes (Thank you Mordread).
 *
 * @author Bernard Paques [email]bernard.paques@bigfoot.com[/email]
 * @author Mordread Wallas
 * @author GnapZ
 * @author Lasares
 * @tester Viviane Zaniroli
 * @tester Agnes
 * @tester Pat
 * @tester Guillaume Perez
 * @tester Fw_crocodile
 * @tester Lasares
 * @reference
 * @license http://www.gnu.org/copyleft/lesser.txt GNU Lesser General Public License
 */
Class Codes {

	/**
	 * beautify some text for final rendering
	 *
	 * This function is used to transform some text before sending it back to the browser.
	 * It actually performs following analysis:
	 * - implicit formatting
	 * - formatting codes
	 * - smileys
	 *
	 * If the keyword [escape][formatted][/escape] appears at the first line of text,
	 * or if options have the keyword ##formatted##, no implicit formatting is performed.
	 *
	 * If the keyword [escape][hardcoded][/escape] appears at the first line of text,
	 * or if options have the keyword ##hardcoded##, the only transformation is is new lines to breaks.
	 *
	 * If options feature the keyword ##compact##, then YACS codes that may
	 * generate big objects are removed, such as [escape][table]...[/table][/escape]
	 * and [escape][location][/escape].
	 *
	 * @param string the text to beautify
	 * @param string the set of options that apply to this text
	 * @return the beautified text
	 *
	 * @see articles/view.php
	 */
	function &beautify($text, $options='') {
		global $context;

		// save CPU cycles
		$text = trim($text);
		if(!$text)
			return $text;

		// profiling mode
		if($context['with_profile'] == 'Y')
			logger::profile('codes::beautify', 'start');

		//
		// looking for compact content
		//
		if(preg_match('/\bcompact\b/i', $options))
			$text = preg_replace(array('/\[table.+?\/table\]/', '/\[location.+?\]/'), '', $text);

		//
		// implicit formatting
		//

		// new lines will have to be checked
		$new_lines = 'proceed';

		// text is already formatted
		if(preg_match('/^\s*\[formatted\](.*)/is', $text, $matches)) {
			$new_lines = 'none';
			$text = $matches[1];

		// text is already formatted (through options)
		} elseif(preg_match('/\bformatted\b/i', $options))
			$new_lines = 'none';

		// newlines are hard coded
		elseif(preg_match('/^\s*\[hardcoded\](.*)/is', $text, $matches)) {
			$new_lines = 'hardcoded';
			$text = $matches[1];

		// newlines are hard coded (through options)
		} elseif(preg_match('/\bhardcoded\b/i', $options))
			$new_lines = 'hardcoded';

		// implicit formatting
		else
			$text =& Codes::beautify_implied($text, 'text');

		//
		// translate codes
		//

		// render codes
		$text =& Codes::render($text);

		// render smileys after codes, else it will break escaped strings
		if(is_callable(array('Smileys', 'render_smileys')))
			$text =& Smileys::render_smileys($text);

		// relocate images
		$text = str_replace('"skins/', '"'.$context['path_to_root'].'skins/', $text);

		//
		// adjust end of lines
		//

		// newlines are hard coded
		if($new_lines == 'hardcoded')
			$text = nl2br($text);

		// implicit formatting
		elseif($new_lines == 'proceed')
			$text =& Codes::beautify_implied($text, 'newlines');

		// profiling mode
		if($context['with_profile'] == 'Y')
			logger::profile('codes::beautify', 'stop');

		return $text;
	}

	/**
	 * beautify some text in the extra panel
	 *
	 * @param string the text to beautify
	 * @return the beautified text
	 *
	 * @see articles/view.php
	 */
	function &beautify_extra($text) {
		global $context;

		// regular rendering
		$text =& Codes::beautify($text);

		$search = array();
		$replace = array();

		// [box.extra=title]...[/box]
		$search[] = '/\[box\.(extra)=([^\]]+?)\](.*?)\[\/box\]/ise';
		$replace[] = "Skin::build_box(stripslashes('$2'), stripslashes('$3'), '$1')";

		// [box.navigation=title]...[/box]
		$search[] = '/\[box\.(navigation)=([^\]]+?)\](.*?)\[\/box\]/ise';
		$replace[] = "Skin::build_box(stripslashes('$2'), stripslashes('$3'), '$1')";

		// process all codes
		$text = preg_replace($search, $replace, $text);
		return $text;

	}

	/**
	 * render some basic formatting
	 *
	 * - suppress multiple newlines
	 * - render empty lines
	 * - render simple bulleted lines
	 * - make URL clickable (http://..., www.foo.bar, foo.bar@foo.com)
	 *
	 * Now this function looks for the keyword &#91;escape] in order
	 * to avoid for formatting pre-formatted areas.
	 *
	 * For example, if you type:
	 * [snippet]
	 * hello
	 * world
	 *
	 * how are
	 * you doing?
	 *
	 * - my first item
	 * - my second item
	 *
	 * > quoted from
	 * > a previous message
	 * [/snippet]
	 *
	 * This will be rendered visually in the browser as:
	 * [snippet]
	 * hello world
	 *
	 * how are you doing?
	 *
	 * - my first item
	 * - my second item
	 *
	 * > quoted from
	 * > a previous message
	 * [/snippet]
	 *
	 * @param string the text to transform
	 * @param sring either 'text' or 'newlines'
	 * @return the modified string
	 */
	function &beautify_implied($text, $variant='text') {

		// streamline newlines, even if this has been done elsewhere
		$text = str_replace(array("\r\n", "\r"), "\n", $text);

		// only change end of lines
		if($variant == 'newlines') {

			// formatting patterns
			$search = array(
				"|<br\s*/*>\n+|i",		/* don't insert additional \n after <br /> */
				"|\n\n+|i"				/* force an html space between paragraphs */
				);

			$replace = array(
				BR,
				BR.BR
				);

		// change everything, except new lines
		} else {

			// formatting patterns
			$search = array(
				"|</h1>\n+|i",			/* strip \n after title */
				"|</h2>\n+|i",
				"|</h3>\n+|i",
				"|</h4>\n+|i",
				"#^([a-z]+?)://([^ <>{}\r\n]+)#ie", /* make URL clickable */
				"#([\n\t ])([a-z]+?)://([^ <>{}\r\n]+)#ie", /* make URL clickable */
				"#([\n\t \(])www\.([a-z0-9\-]+)\.([a-z0-9_\-\.\~]+)((?:/[^,< \r\n\)]*)?)#ie",	/* web server */
				"/^(-|\*|�|\�)\s+(.+)$/im", /* lists hard-coded with -, *, �, or � -- no space ahead */
				"/\n[ \t]*(From|To|cc|bcc|Subject|Date):(\s*)/i",	/* common message headers */
				"|\n[ \t]*>(\s*)|i",		/* quoted by > */
				"|\n[ \t]*\|(\s*)|i",		/* quoted by | */
				"#([\n\t ])(mailto:|)([a-z0-9_\-\.\~]+?)@([a-z0-9_\-\.\~]{3}[a-z0-9_\-\.\~]+)([\n\t ]*)#ie" /* mail address*/
				);

			$replace = array(
				"</h1>",
				"</h2>",
				"</h3>",
				"</h4>",
				"Codes::render_link('$1://$2', '$1://$2')",
				"'$1'.Codes::render_link('$2://$3', '$2://$3')",
				"'$1'.Codes::render_link('http://www.$2.$3$4', 'www.$2.$3$4')",
				"<ul><li>$2</li></ul>",
				BR."$1:$2",
				BR.">$1",
				BR."|$1",
				"'$1'.Codes::render_link('mailto:$3@$4', '$3@$4', 'email').'$5'"
				);
		}

		// preserve escaped areas
		$text = str_replace(array('[escape]', '[/escape]', '[list]', '[/list]', '[php]', '[/php]', '[snippet]', '[/snippet]'),
			array('<escape>', '</escape>', '<list>', '</list>', '<php>', '</php>', '<snippet>', '</snippet>'), $text);

		// locate pre-formatted areas
		$areas = preg_split('/<(code|escape|list|php|snippet|pre)>(.*?)<\/\1>/is', trim($text), -1, PREG_SPLIT_DELIM_CAPTURE);

		// format only adequate areas
		$index = 0;
		$formatted = '';
		$inside = FALSE;
		$target = '';
		foreach($areas as $area) {

			switch($index%3) {
			case 0: // area to be formatted

				// do not rewrite tags
				$items = preg_split('/<(.+?)>/is', $area, -1, PREG_SPLIT_DELIM_CAPTURE);
				$where = 0;
				foreach($items as $item) {

					switch($where%2) {

					case 0: // outside a tag
						if($inside)
							$target .= $item;
						else
							$formatted .= preg_replace($search, $replace, $item);
						break;

					case 1: // inside a tag

						// inside or outside a link
						if($inside && preg_match('/^\/a/i', trim($item))) {
							$formatted .= preg_replace($search, $replace, $target).'<'.$item.'>';
							$target = '';
							$inside = FALSE;
						} elseif($inside)
							$target .= '<'.$item.'>';
						elseif(preg_match('/^a\s/i', trim($item))) {
							$formatted .= '<'.$item.'>';
							$inside = TRUE;
						} else
							$formatted .= '<'.$item.'>';
						break;

					}
					$where++;
				}
				break;

			case 1: // area boundary
				$tag = $area;
				break;

			case 2: // pre-formatted area - left unmodified

				// inside a link, or regular text
				if($inside)
					$target .= '<'.$tag.'>'.$area.'</'.$tag.'>';
				else
					$formatted .= '<'.$tag.'>'.$area.'</'.$tag.'>';
				break;

			}
			$index++;
		}

		// post-optimization
		if($variant == 'text')
			$formatted = preg_replace('#</ul>\n{0,1}<ul>#', '', $formatted);
		$formatted = preg_replace('#\n\n+<ul#', "\n<ul", $formatted);

		// restore escaped areas
		$formatted = str_replace(array('<escape>', '</escape>', '<list>', '</list>', '<php>', '</php>', '<snippet>', '</snippet>'),
			array('[escape]', '[/escape]', '[list]', '[/list]', '[php]', '[/php]', '[snippet]', '[/snippet]'), $formatted);

		return $formatted;
	}

	/**
	 * format a title
	 *
	 * New lines and images are the only things accepted in titles.
	 * The goal is to provide a faster service than beautify()
	 *
	 * @param string raw title
	 * @return string finalized title
	 */
	function &beautify_title($text) {

		// the only code transformed in titles
		$output = str_replace(array('[nl]', '[NL]'), '<br />', $text);

		// suppress pairing codes
		$output = preg_replace('/\[(.+?)\](.+?)\[\/(.+?)\]/s', '\\2', $output);

		// remove everything, except links, breaks and images, and selected tags
		$output = strip_tags($output, '<a><abbr><acronym><b><big><br><code><del><dfn><em><i><img><ins><q><small><span><strong><sub><sup><tt><u>');

		// return by reference
		return $output;
	}

	/**
	 * clean strings to be used in src or in href attributes
	 *
	 * @param the input string
	 * @return the safe string
	 */
	function &clean_href($text) {

		// suppress invalid chars
		$output = preg_replace(FORBIDDEN_CHARS_IN_URLS, '_', stripslashes($text));
		return $output;

	}

	/**
	 * get the value of one global parameter
	 *
	 * @param string name of the parameter
	 * @param mixed default value, if any
	 * @return the actual value of this parameter, else the default value, else ''
	 */
	function &get_parameter($name, $default='') {
		global $context;

		if(isset($context[$name])) {
			$output =& $context[$name];
			return $output;
		}

		$output = $default;
		return $output;
	}

	/**
	 * reset global variables used for rendering
	 *
	 * This function should be called between the processing of different articles in a loop
	 *
	 * @param string the target URL for this rendering (e.g., 'articles/view.php/123')
	 */
	function initialize($main_target=NULL) {
		global $context;

		global $codes_base;
		if($main_target)
			$codes_base = $context['url_to_root'].$main_target;
	}

	/**
	 * transform codes to html
	 *
	 * [php]
	 * // build the page
	 * $context['text'] .= ...
	 *
	 * // transform codes
	 * $context['text'] = Codes::render($context['text']);
	 *
	 * // final rendering
	 * render_skin();
	 * [/php]
	 *
	 * @param string the input string
	 * @return string the transformed string
	 */
	function &render($text) {
		global $context;

		// streamline newlines, even if this has been done elsewhere
		$text = str_replace(array("\r\n", "\r"), "\n", $text);

		// initialize only once
		static $pattern;
		if(!isset($pattern)) {

//			$pattern[] = ;
//			$replace[] = ;
//
//			$pattern[] = ;
//			$replace[] = ;
//
//			$pattern[] = ;
//			$replace[] = ;
//
//			$pattern[] = ;
//			$replace[] = ;
//
//			$pattern[] = ;
//			$replace[] = ;

			$pattern = array(
				'/\[escape\](.*?)\[\/escape\]/ise', 	// [escape]...[/escape] (before everything)
				'/\[php\](.*?)\[\/php\]/ise',			// [php]...[/php]
				'/\[snippet\](.*?)\[\/snippet\]/ise',	// [snippet]...[/snippet]
				'/(\[page\].*)$/is',				// [page] (provide only the first one)
				'/\[hidden\](.*?)\[\/hidden\]/ise', 	// [hidden]...[/hidden] (save some cycles if at the beginning)
				'/\[restricted\](.*?)\[\/restricted\]/ise', // [restricted]...[/restricted] (save some cycles if at the beginning)
				'/\[anonymous\](.*?)\[\/anonymous\]/ise', // [anonymous]...[/anonymous] (save some cycles if at the beginning)
				'/\[parameter=([^\]]+?)\]/ise', 			// [parameter=<name>]
				'/\[csv=(.)\](.*?)\[\/csv\]/ise',		// [csv=;]...[/csv] (before [table])
				'/\[csv\](.*?)\[\/csv\]/ise',			// [csv]...[/csv] (before [table])
				'/\[table=([^\]]+?)\](.*?)\[\/table\]/ise', // [table=variant]...[/table]
				'/\[table\](.*?)\[\/table\]/ise',		// [table]...[/table]
				'/( |\A)##(\S.*?)##(\W|\Z)/is',		// ##...##
				'/\[code\](.*?)\[\/code\]/is',			// [code]...[/code]
				'/\[indent\](.*?)\[\/indent\]/ise', 	// [indent]...[/indent]
				'/\[quote\](.*?)\[\/quote\]/ise',		// [quote]...[/quote]
				'/\[folder=([^\]]+?)\](.*?)\[\/folder\]\s*/ise',	// [folder=...]...[/folder]
				'/\[folder\](.*?)\[\/folder\]\s*/ise',	// [folder]...[/folder]
				'/\[sidebar=([^\]]+?)\](.*?)\[\/sidebar\]\s*/ise',	// [sidebar=...]...[/sidebar]
				'/\[sidebar\](.*?)\[\/sidebar\]\s*/ise',	// [sidebar]...[/sidebar]
				'/\[note\](.*?)\[\/note\]\s*/ise',		// [note]...[/note]
				'/\[caution\](.*?)\[\/caution\]\s*/ise', // [caution]...[/caution]
				'/\[search=([^\]]+?)\]/ise',				// [search=words]
				'/\[search\]/ise',						// [search]
				'/\[cloud=(\d+?)\]/ise',				// [cloud=12]
				'/\[cloud\]/ise',						// [cloud]
				'/\[collections\]/ise', 				// [collections]
				'/\[login=([^\]]+?)\]/ise', 				// [login=words]
				'/\[login\]/ise',						// [login]
				'/\[center\](.*?)\[\/center\]/ise', 	// [center]...[/center]
				'/\[right\](.*?)\[\/right\]/ise',		// [right]...[/right]
				'/\[decorated\](.*?)\[\/decorated\]/ise',// [decorated]...[/decorated]
				'/\[style=([^\]]+?)\](.*?)\[\/style\]/ise', // [style=variant]...[/style]
				'/\[hint=([^\]]+?)\](.*?)\[\/hint\]/is',	// [hint=help]...[/hint]
				'/\[caption\](.*?)\[\/caption\]/ise',	// [caption]...[/caption]
				'/\[tiny\](.*?)\[\/tiny\]/ise', 		// [tiny]...[/tiny]
				'/\[small\](.*?)\[\/small\]/ise',		// [small]...[/small]
				'/\[big\](.*?)\[\/big\]/ise',			// [big]...[/big]
				'/\[huge\](.*?)\[\/huge\]/ise', 		// [huge]...[/huge]
				'/\[subscript\](.*?)\[\/subscript\]/is',// [subscript]...[/subscript]
				'/\[superscript\](.*?)\[\/superscript\]/is',// [superscript]...[/superscript]
				'/( |\A)\+\+(\S.*?)\+\+(\W|\Z)/is',	// ++...++
				'/\[(---+|___+)\]\s*/ise',				// [---], [___] --- before inserted
				'/^-----*/me',							// ----
				'/\[inserted\](.*?)\[\/inserted\]/is',	// [inserted]...[/inserted]
				'/( |\A)--(\w.*?)--(\W|\Z)/ise',		// --...--
				'/\[deleted\](.*?)\[\/deleted\]/is',	// [deleted]...[/deleted]
				'/( |\A)\*\*(\S.*?)\*\*(\W|\Z)/is',	// **...**
				'/\[b\](.*?)\[\/b\]/is',				// [b]...[/b]
				'/( |\A)\/\/(\S.*?)\/\/(\W|\Z)/is',	// //...//
				'/\[i\](.*?)\[\/i\]/is',				// [i]...[/i]
				'/( |\A)__(\S.*?)__(\W|\Z)/is',		// __...__
				'/\[u\](.*?)\[\/u\]/is',				// [u]...[/u]
				'/\[color=([^\]]+?)\](.*?)\[\/color\]/is',	// [color=<color>]...[/color]
				'/\[new\]/ie',							// [new]
				'/\[updated\]/ie',						// [updated]
				'/\[popular\]/ie',						// [popular]
				'/\[flag=([^\]]+?)\]/ie',					// [flag=<flag>]
				'/\[flag\](.*?)\[\/flag\]/ise', 		// [flag]...[/flag]
				'/\[list\](.*?)\[\/list\]/ise', 		// [list]...[/list]
				'/\[list=([^\]]+?)\](.*?)\[\/list\]/ise',	// [list=1]...[/list]
				'/\n\n+[ \t]*\[\*\][ \t]*/ie',			// [*] (outside [list]...[/list])
				'/\n?[ \t]*\[\*\][ \t]*/ie',
				'/\[li\](.*?)\[\/li\]/is',				// [li]...[/li] (outside [list]...[/list])
				'/\[images=([^\]]+?)\]/ie', 				// [images=<ids>] (before other links)
				'/\[image\](.*?)\[\/image\]/ise',		// [image]src[/image]
				'/\[image=([^\]]+?)\](.*?)\[\/image\]/ise', // [image=alt]src[/image]
				'/\[img\](.*?)\[\/img\]/ise',			// [img]src[/img]
				'/\[img=([^\]]+?)\](.*?)\[\/img\]/ise', 	// [img=alt]src[/img]
				'/\[image=([^\]]+?)\]/ie',					// [image=<id>]
				'/\[image([^\]]+?)\]/ie',					// [image<id>] (deprecated)
				'/\[flash=([^\]]+?)\]/ie',					// [flash=<id>, <width>, <height>, <params>] or [flash=<id>, window]
				'/\[sound=([^\]]+?)\]/ie',					// [sound=<id>]
				'/\[go=([^\]]+?)\]/ie', 					// [go=<name>]
				'/\[article=([^\]]+?)\]/ie',				// [article=<id>] or [article=<id>, title]
				'/\[next=([^\]]+?)\]/ie',					// [next=<id>]
				'/\[previous=([^\]]+?)\]/ie',				// [previous=<id>]
				'/\[section=([^\]]+?)\]/ie',				// [section=<id>] or [section=<id>, title]
				'/\[category=([^\]]+?)\]/ie',				// [category=<id>] or [category=<id>, title]
				'/\[user=([^\]]+?)\]/ie',					// [user=<id>] or [user=<id>, title]
				'/\[server=([^\]]+?)\]/ie', 				// [server=<id>]
				'/\[file=([^\]]+?)\]/ie',					// [file=<id>] or [file=<id>, title]
				'/\[download=([^\]]+?)\]/ie',				// [download=<id>] or [download=<id>, title]
				'/\[action=([^\]]+?)\]/ie', 				// [action=<id>]
				'/\[comment=([^\]]+?)\]/ie',				// [comment=<id>] or [comment=<id>, title]
				'/\[decision=([^\]]+?)\]/ie',				// [decision=<id>] or [decision=<id>, title]
				'/\[url=([^\]]+?)\](.*?)\[\/url\]/ise', 	// [url=url]label[/url] (deprecated by [link])
				'/\[url\](.*?)\[\/url\]/ise',			// [url]url[/url] (deprecated by [link])
				'/\[link=([^\]]+?)\](.*?)\[\/link\]/ise',	// [link=label]url[/link]
				'/\[link\](.*?)\[\/link\]/ise', 		// [link]url[/link]
				'/\[button=([^\]]+?)\](.*?)\[\/button\]/ise',	// [button=label]url[/button]
				'/\[script\](.*?)\[\/script\]/ise', 	// [script]url[/script]
				'/\[menu\](.*?)\[\/menu\]\n*/ise',		// [menu]url[/menu]
				'/\[menu=([^\]]+?)\](.*?)\[\/menu\]\n{0,1}/ise',	// [menu=label]url[/menu]
				'/\[submenu\](.*?)\[\/submenu\]\n{0,1}/ise',	// [submenu]url[/submenu]
				'/\[submenu=([^\]]+?)\](.*?)\[\/submenu\]\n*/ise', // [submenu=label]url[/submenu]
				'/\[email=([^\]]+?)\](.*?)\[\/email\]/ise', // [email=label]url[/email]
				'/\[email\](.*?)\[\/email\]/ise',		// [email]url[/email]
				'/\[question\](.*?)\[\/question\]\n*/ise', // [question]...[/question]
				'/\[question\]/ise',					// [question]
				'/\[answer\]/ise',						// [answer]
				'/\[scroller\](.*?)\[\/scroller\]/ise', // [scroller]...[/scroller]
				'/\[toq\]\n*/ise',						// [toq] (table of questions)
				'/<p>\[title\](.*?)\[\/title\]<\/p>\n*/is', // a trick for FCKEditor
				'/\[title\](.*?)\[\/title\]\n*/is', 	// [title]...[/title]
				'/\[subtitle\](.*?)\[\/subtitle\]\n*/is', // [subtitle]...[/subtitle]
				'/\[(header[1-5])\](.*?)\[\/\1\]\n*/ise', // [header1]...[/header1] ... [header5]...[/header5]
				'/^======(\S.*?)======/me', 			// ======...====== level 5 headline
				'/^=====(\S.*?)=====/me',				// =====...===== level 4 headline
				'/^====(\S.*?)====/me', 				// ====...==== level 3 headline
				'/^===(\S.*?)===/me',					// ===...=== level 2 headline
				'/^==(\S.*?)==/me', 					// ==...== level 1 headline
				'/\[toc\]\n*/ise',						// [toc] (table of content)
				'/\[published\]\n*/ise',				// [published] (a compact list of recent publications)
				'/\[published=(.+?)\]\n*/ise',			// [published=section:4029] (a compact list of recent publications)
				'/\[read\]\n*/ise', 					// [read] (a compact list of hits)
				'/\[read=([^\]]+?)\]\n*/ise',				// [read=section:4029] (a compact list of hits)
				'/\[edited\]\n*/ise',					// [edited] (a compact list of recent updates)
				'/\[edited=([^\]]+?)\]\n*/ise', 			// [edited=section:4029] (a compact list of recent updates)
				'/\[commented\]\n*/ise',				// [commented] (a compact list of fresh threads)
				'/\[commented=([^\]]+?)\]\n*/ise',			// [commented=section:4029] (a compact list of fresh threads)
				'/\[contributed\]\n*/ise',				// [contributed] (a compact list of most active pages)
				'/\[contributed=([^\]]+?)\]\n*/ise',		// [contributed=section:4029] (a compact list of active pages)
				'/\[freemind\]\n*/ise', 				// [freemind] (a mind map of site content)
				'/\[freemind=([^\]]+?)\]\n*/ise',			// [freemind=section:4029] (a mind map of section content)
				'/\[news=([^\]]+?)\]/ise',				// [news=flash]
				'/\[table=([^\]]+?)\]/ise', 				// [table=<id>]
				'/\[locations=([^\]]+?)\]/ise', 			// [locations=<id>]
				'/\[location=([^\]]+?)\]/ise',				// [location=<id>]
				'/\[wikipedia=([^\]]+?)\]/ise', 			// [wikipedia=keyword] or [wikipedia=keyword, title]
				'/\[be\]/i',							// [be] belgian flag
				'/\[ca\]/i',							// [ca] canadian flag
				'/\[ch\]/i',							// [ch] swiss flag
				'/\[de\]/i',							// [de] german flag
				'/\[en\]/i',							// [en] english flag
				'/\[es\]/i',							// [es] spanish flag
				'/\[fr\]/i',							// [fr] french flag
				'/\[gb\]/i',							// [gb] gb flag
				'/\[gr\]/i',							// [gr] greek flag
				'/\[it\]/i',							// [it] italian flag
				'/\[pt\]/i',							// [pt] portuguese flag
				'/\[us\]/i',							// [pt] us flag
				'/\[clear\]\n*/i',						// [clear]
				'/\[nl\]\n*/si',						// [nl] (after tables)
				'/\[br\]/i' 							// [br] (deprecated by [nl])
			);
		}

		// initialize only once
		static $replace;
		if(!isset($replace)) {
			$replace = array(
				"Codes::render_escaped(stripslashes('$1'))",					// [escape]...[/escape]
				"Codes::render_pre(stripslashes('$1'), 'php')", 				// [php]...[/php]
				"Codes::render_pre(stripslashes('$1'), 'snippet')", 			// [snippet]...[/snippet]
				'', 															// [page]
				"Codes::render_hidden(stripslashes('$1'), 'hidden')",			// [hidden]...[/hidden]
				"Codes::render_hidden(stripslashes('$1'), 'restricted')",		// [restricted]...[/restricted]
				"Codes::render_hidden(stripslashes('$1'), 'anonymous')",		// [anonymous]...[/anonymous]
				"Codes::get_parameter('\\1')", 									// [parameter=<name>]
				"utf8::to_unicode(str_replace('$1', '|', utf8::from_unicode(stripslashes('$2'))))", // [csv=;]...[/csv]
				"str_replace(',', '|', stripslashes('$1'))",					// [csv]...[/csv]
				"Codes::render_table(stripslashes('$2'), '$1')",				// [table=variant]...[/table]
				"Codes::render_table(stripslashes('$1'), '')",					// [table]...[/table]
				'\\1<code>\\2</code>\\3',										// ##...##
				'<code>\\1</code>', 											// [code]...[/code]
				"Skin::build_block(stripslashes('$1'), 'indent')",				// [indent]...[indent]
				"Skin::build_block(stripslashes('$1'), 'quote')",				// [quote]...[/quote]
				"Skin::build_box(stripslashes('$1'), stripslashes('$2'), 'folder')",	// [folder=title]...[/folder]
				"Skin::build_box(NULL, stripslashes('$1'), 'folder')",			// [folder]...[/folder]
				"Skin::build_box(stripslashes('$1'), stripslashes('$2'), 'sidebar')",	// [sidebar=title]...[/sidebar]
				"Skin::build_box(NULL, stripslashes('$1'), 'sidebar')", 		// [sidebar]...[/sidebar]
				"Skin::build_block(stripslashes('$1'), 'note')",				// [note]...[/note]
				"Skin::build_block(stripslashes('$1'), 'caution')", 			// [caution]...[/caution]
				"Skin::build_block(stripslashes('$1'), 'search')",				// [search=<words>]
				"Skin::build_block(NULL, 'search')",							// [search]
				"Codes::render_cloud('$1')",									// [cloud=12]
				"Codes::render_cloud(20)",										// [cloud]
				"Codes::render_collections()",									// [collections]
				"Skin::build_block(stripslashes('$1'), 'login')",				// [login=<words>]
				"Skin::build_block(NULL, 'login')", 							// [login]
				"Skin::build_block(stripslashes('$1'), 'center')",				// [center]...[/center]
				"Skin::build_block(stripslashes('$1'), 'right')",				// [right]...[/right]
				"Skin::build_block(stripslashes('$1'), 'decorated')",			// [decorated]...[/decorated]
				"Skin::build_block(stripslashes('$2'), '$1')",					// [style=variant]...[/style]
				'<acronym title="\\1">\\2</acronym>',							// [hint=help]...[/hint]
				"Skin::build_block(stripslashes('$1'), 'caption')", 			// [caption]...[/caption]
				"Skin::build_block(stripslashes('$1'), 'tiny')",				// [tiny]...[/tiny]
				"Skin::build_block(stripslashes('$1'), 'small')",				// [small]...[/small]
				"Skin::build_block(stripslashes('$1'), 'big')", 				// [big]...[/big]
				"Skin::build_block(stripslashes('$1'), 'huge')",				// [huge]...[/huge]
				'<sub>\\1</sub>',												// [subscript]...[/subscript]
				'<sup>\\1</sup>',												// [superscript]...[/superscript]
				'\\1<ins>\\2</ins>\\3', 										// ++...++
				"HORIZONTAL_RULER", 											// [---], [___]
				"HORIZONTAL_RULER", 											// ----
				'<ins>\\1</ins>',												// [inserted]...[/inserted]
				"preg_match('/^(BEGIN|END)/', '\\2')?'\\1--\\2--\\3':'\\1<del>\\2</del>\\3'",	// --...-- take care of PKCS headers
				'<del>\\1</del>',												// [deleted]...[/deleted]
				'\\1<b>\\2</b>\\3', 											// **...**
				'<b>\\1</b>',													// [b]...[/b]
				'\\1<i>\\2</i>\\3', 											// //...//
				'<i>\\1</i>',													// [i]...[/i]
				'\\1<span style="text-decoration: underline">\\2</span>\\3',	// __...__
				'<span style="text-decoration: underline">\\1</span>',			// [u]...[/u]
				'<span style="color: \\1">\\2</span>',							// [color]...[/color]
				"NEW_FLAG", 													// [new]
				"UPDATED_FLAG", 												// [updated]
				"POPULAR_FLAG", 												// [popular]
				"Skin::build_flag('\\1')",										// [flag=....]
				"Skin::build_flag('\\1')",										// [flag]...[/flag]
				"Codes::render_list(stripslashes('$1'), NULL)", 				// [list]...[/list]
				"Codes::render_list(stripslashes('$2'), '$1')", 				// [list=?]...[/list]
				"BR.BR.BULLET_IMG.'&nbsp;'",									// standalone [*]
				"BR.BULLET_IMG.'&nbsp;'",
				'<li>\\1</li>', 												// [li]...[/li]
				"Codes::render_object('images', stripslashes('$1'))",			// [images=<ids>]
				"'<div class=\"external_image\"><img src=\"'.Codes::clean_href('$1').'\" alt=\"\" /></div>'",	// [image]src[/image]
				"'<div class=\"external_image\"><img src=\"'.Codes::clean_href('$2').'\" alt=\"'.Codes::clean_href('$1').'\" /></div>'", // [image=alt]src[/image]
				"'<div class=\"external_image\"><img src=\"'.Codes::clean_href('$1').'\" alt=\"\" /></div>'",	// [img]src[/img]
				"'<div class=\"external_image\"><img src=\"'.Codes::clean_href('$2').'\" alt=\"'.Codes::clean_href('$1').'\" /></div>'", // [img=alt]src[/img]
				"Codes::render_object('image', stripslashes('$1'))",			// [image=<id>]
				"Codes::render_object('image', stripslashes('$1'))",			// [image<id>] (deprecated)
				"Codes::render_object('flash', stripslashes('$1'))",			// [flash=<id>, <width>, <height>, <params>]
				"Codes::render_object('sound', stripslashes('$1'))",			// [sound=<id>]
				"Codes::render_object('go', stripslashes('$1'))",				// [go=<name>]
				"Codes::render_object('article', stripslashes('$1'))",			// [article=<id>]
				"Codes::render_object('next', stripslashes('$1'))", 			// [next=<id>]
				"Codes::render_object('previous', stripslashes('$1'))", 		// [previous=<id>]
				"Codes::render_object('section', stripslashes('$1'))",			// [section=<id>]
				"Codes::render_object('category', stripslashes('$1'))", 		// [category=<id>]
				"Codes::render_object('user', stripslashes('$1'))", 			// [user=<id>]
				"Codes::render_object('server', stripslashes('$1'))",			// [server=<id>]
				"Codes::render_object('file', stripslashes('$1'))", 			// [file=<id>] or [file=<id>, title]
				"Codes::render_object('download', stripslashes('$1'))", 		// [download=<id>] or [download=<id>, title]
				"Codes::render_object('action', stripslashes('$1'))",			// [action=<id>]
				"Codes::render_object('comment', stripslashes('$1'))",			// [comment=<id>] or [comment=<id>, title]
				"Codes::render_object('decision', stripslashes('$1'))", 		// [decision=<id>] or [decision=<id>, title]
				"Codes::render_link(Codes::clean_href('$1'), stripslashes('$2'))",		// [url=url]label[/link] (deprecated by [link])
				"Codes::render_link(Codes::clean_href('$1'), NULL)",			// [url]url[/url] (deprecated by [link])
				"Codes::render_link(Codes::clean_href('$2'), stripslashes('$1'))",		// [link=label]url[/link]
				"Codes::render_link(Codes::clean_href('$1'), NULL)",			// [link]url[/link]
				"Skin::build_link(Codes::clean_href('$2'), stripslashes('$1'), 'button')",	// [button=label]url[/button]
				"Skin::build_link(Codes::clean_href('$1'), stripslashes('$1'), 'script')",	// [script]url[/script]
				"Skin::build_link(Codes::clean_href('$1'), stripslashes('$1'), 'menu_1')",	// [menu]url[/menu]
				"Skin::build_link(Codes::clean_href('$2'), stripslashes('$1'), 'menu_1')",	// [menu=label]url[/menu]
				"Skin::build_link(Codes::clean_href('$1'), stripslashes('$1'), 'menu_2')",	// [submenu]url[/submenu]
				"Skin::build_link(Codes::clean_href('$2'), stripslashes('$1'), 'menu_2')",	// [submenu=label]url[/submenu]
				"Codes::render_email(Codes::clean_href('$2'), stripslashes('$1'))", // [email=label]url[/email]
				"Codes::render_email(Codes::clean_href('$1'), stripslashes('$1'))", // [email]url[/email]
				"Codes::render_title(stripslashes('$1'), 'question')",			// [question]...[/question]
				"QUESTION_FLAG",												// [question]
				"ANSWER_FLAG",													// [answer]
				"Codes::render_animated(stripslashes('$1'), 'scroller')",		// [scroller]...[/scroller]
				"Codes::render_table_of('questions')",							// [toq]
				'[header1]\\1[/header1]',										// a trick for FCKEditor
				'[header1]\\1[/header1]',										// [title]...[/title]
				'[header2]\\1[/header2]',										// [subtitle]...[/subtitle]
				"Codes::render_title(stripslashes('$2'), '$1')",				// [header1]...[/header1] ... [header5]...[/header5]
				"Codes::render_title(stripslashes('$1'), 'header5')",			// ======...====== level 5 header
				"Codes::render_title(stripslashes('$1'), 'header4')",			// =====...===== level 4 header
				"Codes::render_title(stripslashes('$1'), 'header3')",			// ====...==== level 3 header
				"Codes::render_title(stripslashes('$1'), 'header2')",			// ===...=== level 2 header
				"Codes::render_title(stripslashes('$1'), 'header1')",			// ==...== level 1 header
				"Codes::render_table_of('content')",							// [toc]
				"Codes::render_published('')",									// [published]
				"Codes::render_published('$1')",								// [published=section:4029]
				"Codes::render_read('')",										// [read]
				"Codes::render_read('$1')", 									// [read=section:4029]
				"Codes::render_updated('')",									// [edited]
				"Codes::render_updated('$1')",									// [edited=section:4029]
				"Codes::render_commented('')",									// [commented]
				"Codes::render_commented('$1')",								// [commented=section:4029]
				"Codes::render_contributed('')",								// [contributed]
				"Codes::render_contributed('$1')",								// [contributed=section:4029]
				"Codes::render_freemind('sections')",							// [freemind]
				"Codes::render_freemind('$1')", 								// [freemind=section:4029] or [freemind=123]
				"Codes::render_news('$1')", 									// [news=flash]
				"Codes::render_table('', '$1')",								// [table=<id>]
				"Codes::render_locations(stripslashes('$1'))",					// [locations=<id>]
				"Codes::render_location(stripslashes('$1'))",					// [location=<id>]
				"Codes::render_wikipedia(stripslashes('$1'))",					// [wikipedia=keyword] or [wikipedia=keyword, title]
				' <img src="'.$context['url_to_root'].'skins/images/flags/be.gif" alt=""'.EOT.' ', // [be] belgian flag
				' <img src="'.$context['url_to_root'].'skins/images/flags/ca.gif" alt=""'.EOT.' ', // [ca] canadian flag
				' <img src="'.$context['url_to_root'].'skins/images/flags/ch.gif" alt=""'.EOT.' ', // [ch] swiss flag
				' <img src="'.$context['url_to_root'].'skins/images/flags/de.gif" alt=""'.EOT.' ', // [de] german flag
				' <img src="'.$context['url_to_root'].'skins/images/flags/gb.gif" alt=""'.EOT.' ', // [en] english flag
				' <img src="'.$context['url_to_root'].'skins/images/flags/es.gif" alt=""'.EOT.' ', // [es] spanish flag
				' <img src="'.$context['url_to_root'].'skins/images/flags/fr.gif" alt=""'.EOT.' ', // [fr] french flag
				' <img src="'.$context['url_to_root'].'skins/images/flags/gb.gif" alt=""'.EOT.' ', // [gb] english flag
				' <img src="'.$context['url_to_root'].'skins/images/flags/gr.gif" alt=""'.EOT.' ', // [gr] greek flag
				' <img src="'.$context['url_to_root'].'skins/images/flags/it.gif" alt=""'.EOT.' ', // [it] italian flag
				' <img src="'.$context['url_to_root'].'skins/images/flags/pt.gif" alt=""'.EOT.' ', // [pt] portuguese flag
				' <img src="'.$context['url_to_root'].'skins/images/flags/us.gif" alt=""'.EOT.' ', // [us] us flag
				' <br style="clear: both;"'.EOT.' ',					// [clear]
				BR, 													// [nl]
				BR														// [br] (deprecated by [nl])
			);
		}

		// ensure we have enough time to execute
		Safe::set_time_limit(30);

		// do it globally
		$text = preg_replace($pattern, $replace, $text);

		// FCKEditor optimisation
		$text = str_replace("</pre>\n<pre>", "\n", $text);

		// done
		return $text;

	}

	/**
	 * render an animated block of text
	 *
	 * @param string the text
	 * @param string the variant
	 * @return string the rendered text
	**/
	function &render_animated($text, $variant) {
		global $context, $scroller_counter;

		$scroller_counter++;
		$output = '<marquee id="scroller_'.$scroller_counter.'">'.$text.'</marquee>';
		return $output;
	}

	/**
	 * render the cloud of tags
	 *
	 * @param string the number of items to list
	 * @return string the rendered text
	**/
	function &render_cloud($count=40) {
		global $context;

		// sanity check
		if(!(int)$count)
			$count = 40;

		// query the database and layout that stuff
		if(!$text = Members::list_categories_by_count_for_anchor(NULL, 0, $count, 'cloud'))
			$text = '<p>'.i18n::s('No item has been found.').'</p>';

		// we have an array to format
		if(is_array($text))
			$text =& Skin::build_list($text, '2-columns');

		// job done
		return $text;

	}

	/**
	 * list available collections
	 *
	 * @return string the rendered text
	**/
	function &render_collections() {
		global $context;

		// has one collection been defined?
		Safe::load('parameters/collections.include.php');
		if(!isset($context['collections']) || !is_array($context['collections'])) {
			$output = NULL;
			return $output;
		}

		// use attributes set for each collection
		$text = '';
		foreach($context['collections'] as $name => $attributes) {

			// retrieve collection information
			list($title, $path, $url, $introduction, $description, $prefix, $suffix, $visibility) = $attributes;

			// skip protected collections
			if(($visibility == 'N') && !Surfer::is_associate())
				continue;
			if(($visibility == 'R') && !Surfer::is_member())
				continue;

			// ensure we have a title for this collection
			if(!trim($title))
				$title = str_replace(array('.', '_', '%20'), ' ', $name);

			// build some hovering title
			$hover = ' title="'.encode_field(i18n::s('Access collection')." '".strip_tags($title)."'").'"';

			// signal restricted and private collections
			if($visibility == 'N')
				$title = PRIVATE_FLAG.$title;
			elseif($visibility == 'R')
				$title = RESTRICTED_FLAG.$title;

			// link to collection index page
			if($context['with_friendly_urls'] == 'Y')
				$link = 'collections/browse.php/'.rawurlencode($name);
			else
				$link = 'collections/browse.php?path='.urlencode($name);
			$text .= '<li><a href="'.$context['url_to_root'].$link.'"'.$hover.'>'.$title.'</a>';

			// add introduction text, if any
			if($introduction)
				$text .= ' - '.Codes::beautify($introduction);

			$text .= "</li>\n";
		}

		// finalize the list
		if($text)
			$text = '<ul class="collections">'."\n".$text."</ul>\n";

		// job done
		return $text;

	}

	/**
	 * render a compact list of fresh threads
	 *
	 * @param string the anchor (e.g. 'section:123')
	 * @return string the rendered text
	**/
	function &render_commented($anchor='') {
		global $context;

		// number of items to display
		$count = COMPACT_LIST_SIZE;
		if($position = strpos($anchor, ',')) {
			$count = (integer)trim(substr($anchor, $position+1));
			$anchor = substr($anchor, 0, $position);
		}
		if(!$count)
			$count = COMPACT_LIST_SIZE;

		// load the layout to use
		$layout = 'compact';

		// scope is limited to one section
		if($anchor) {

			// look at this level
			$anchors = array($anchor);

			// first level of depth
			$topics =& Sections::get_children_of_anchor($anchor, 'main');
			$anchors = array_merge($anchors, $topics);

			// second level of depth
			if(count($topics) && (count($anchors) < 50)) {
				$topics =& Sections::get_children_of_anchor($topics, 'main');
				$anchors = array_merge($anchors, $topics);
			}

			// third level of depth
			if(count($topics) && (count($anchors) < 50)) {
				$topics =& Sections::get_children_of_anchor($topics, 'main');
				$anchors = array_merge($anchors, $topics);
			}

			// query the database and layout that stuff
			include_once $context['path_to_root'].'comments/comments.php';
			if($text = Comments::list_threads_by_date_for_anchor($anchors, 0, $count, $layout)) {

				// we have an array to format
				if(is_array($text))
					$text =& Skin::build_list($text, 'compact');

			}

		// consider all threads
		} else {

			// query the database and layout that stuff
			include_once $context['path_to_root'].'comments/comments.php';
			if($text = Comments::list_threads_by_date(0, $count, $layout)) {

				// we have an array to format
				if(is_array($text))
					$text =& Skin::build_list($text, 'compact');

			}
		}

		// job done
		return $text;
	}

	/**
	 * render a compact list of most active threads
	 *
	 * @param string the anchor (e.g. 'section:123')
	 * @return string the rendered text
	**/
	function &render_contributed($anchor='') {
		global $context;

		// number of items to display
		$count = COMPACT_LIST_SIZE;
		if($position = strpos($anchor, ',')) {
			$count = (integer)trim(substr($anchor, $position+1));
			$anchor = substr($anchor, 0, $position);
		}
		if(!$count)
			$count = COMPACT_LIST_SIZE;

		// load the layout to use
		$layout = 'compact';

		// scope is limited to one section
		if($anchor) {

			// look at this level
			$anchors = array($anchor);

			// first level of depth
			$topics =& Sections::get_children_of_anchor($anchor, 'main');
			$anchors = array_merge($anchors, $topics);

			// second level of depth
			if(count($topics) && (count($anchors) < 50)) {
				$topics =& Sections::get_children_of_anchor($topics, 'main');
				$anchors = array_merge($anchors, $topics);
			}

			// third level of depth
			if(count($topics) && (count($anchors) < 50)) {
				$topics =& Sections::get_children_of_anchor($topics, 'main');
				$anchors = array_merge($anchors, $topics);
			}

			// query the database and layout that stuff
			include_once $context['path_to_root'].'comments/comments.php';
			if($text = Comments::list_threads_by_count_for_anchor($anchors, 0, $count, $layout)) {

				// we have an array to format
				if(is_array($text))
					$text =& Skin::build_list($text, 'compact');

			}

		// consider all threads
		} else {

			// query the database and layout that stuff
			include_once $context['path_to_root'].'comments/comments.php';
			if($text = Comments::list_threads_by_count(0, $count, $layout)) {

				// we have an array to format
				if(is_array($text))
					$text =& Skin::build_list($text, 'compact');

			}
		}

		// job done
		return $text;
	}

	/**
	 * render an email address
	 *
	 * @param string the address
	 * @param string the label
	 * @return string the rendered text
	**/
	function &render_email($address, $text) {
		// be sure to display something
		if(!$text)
			$text = $address;

		// return a complete anchor
		$output = Skin::build_link('mailto:'.$address, $text, 'email');
		return $output;
	}

	/**
	 * escape code sequences
	 *
	 * @param string the text
	 * @return string the rendered text
	**/
	function &render_escaped($text) {

		// replace strings --initialize only once
		static $from, $to;
		if(!isset($from)) {

			// chars or strings to be escaped
			$tags = array(
				'##' => '&#35;&#35;',
				'*' => '&#42;',
				'+' => '&#43;',
				'-' => '&#45;',
				'/' => '&#47;',
				':' => '&#58;',
				'=' => '&#61;',
				'[' => '&#91;',
				']' => '&#93;',
				'_' => '&#95;',
				'<' => '&#139;' // escape HTML as well
			);

			// initialize only once
			$from = array();
			$to = array();
			foreach($tags as $needle => $replace) {
				$from[] = $needle;
				$to[] = $replace;
			}
		}

		// do the job
		$text = str_replace($from, $to, $text);

		$output = '<code>'.nl2br($text).'</code>';
		return $output;
	}

	/**
	 * render an interactive Freemind map
	 *
	 * The id can be:
	 * - 'sections' - the entire content tree
	 * - 'section:123' - some branch of the content tree
	 * - '123' - the file with the provided id, if it is a Freemind map
	 * - 'http://link/to/a/map.mm' - has to reference this server
	 *
	 * The id can also include width and height of the target canvas, as in
	 * following examples:
	 * - '100%, 250px' - actual id is assumed to be 'sections'
	 * - 'section:4059, 100%, 250px'
	 *
	 * The Flash viewer is available at http://evamoraga.net/efectokiwano/mm/index.mm
	 *
	 * @link http://evamoraga.net/efectokiwano/mm/index.mm
	 *
	 * @param string id of the target map
	 * @return string the rendered string
	**/
	function &render_freemind($id) {
		global $context;

		// web reference to the target Freemind map file
		include_once $context['path_to_root'].'files/files.php';

		// process parameters
		$attributes = preg_split("/\s*,\s*/", $id, 3);
		switch(count($attributes)) {
		case 3: // id, width, height
			$id = $attributes[0];
			$width = $attributes[1];
			$height = $attributes[2];
			break;
		case 2: // width, height
			$id = 'sections';
			$width = $attributes[0];
			$height = $attributes[1];
			break;
		case 1: // id
			$id = $attributes[0];
			$width = isset($context['skins_freemind_canvas_width']) ? $context['skins_freemind_canvas_width'] : '100%';
			$height = isset($context['skins_freemind_canvas_height']) ? $context['skins_freemind_canvas_height'] : '500px';
			break;
		}

		// additional commands
		$menu = array();

		// web reference to site full content
		if($id == 'sections') {
			$target_href = $context['url_to_home'].$context['url_to_root'].Sections::get_url('all', 'freemind', utf8::to_ascii($context['site_name'].'.mm'));
			$menu = array_merge($menu, array(Sections::get_url('all', 'view_as_freemind', utf8::to_ascii($context['site_name'].'.mm')) => i18n::s('Full-size')));

		// content of one section
		} elseif(preg_match('/section:([0-9]+)/', $id, $matches)) {

			if(!$item =& Sections::get($matches[1])) {
				$text = '[freemind='.$id.']';
				return $text;
			}

			$target_href = $context['url_to_home'].$context['url_to_root'].Sections::get_url($item['id'], 'freemind', utf8::to_ascii($context['site_name'].' - '.strip_tags(Codes::beautify(trim($item['title']))).'.mm'));

			$menu = array_merge($menu, array(Sections::get_url($item['id'], 'view_as_freemind', utf8::to_ascii($context['site_name'].' - '.strip_tags(Codes::beautify(trim($item['title']))).'.mm')) => i18n::s('Full-size')));

		// direct reference to the target file
		} elseif(strpos($id, $context['url_to_home']) === 0) {
			$target_href = $id;

		// one file, as a freemind map
		} elseif(($item =& Files::get($id)) && isset($item['id'])) {

			// if we have an external reference, use it
			if(isset($item['file_href']) && $item['file_href']) {
				$target_href = $item['file_href'];

			// else redirect to ourself
			} else {

				// ensure a valid file name
				$file_name = utf8::to_ascii($item['file_name']);

				// where the file is
				$path = 'files/'.$context['virtual_path'].str_replace(':', '/', $item['anchor']).'/'.rawurlencode($item['file_name']);

				// map the file on the ftp server
				if($item['active'] == 'X') {
					Safe::load('parameters/files.include.php');
					$url_prefix = str_replace('//', '/', $context['files_url'].'/');

				// or map the file on the regular web space
				} else
					$url_prefix = $context['url_to_home'].$context['url_to_root'];


				// redirect to the actual file
				$target_href = $url_prefix.$path;
			}

		// no way to render this id
		} else {
			$text = '[freemind='.$id.']';
			return $text;
		}

		// allow several viewers to co-exist in the same page
		static $freemind_viewer_index;
		if(!isset($freemind_viewer_index))
			$freemind_viewer_index = 1;
		else
			$freemind_viewer_index++;

		// load flash player
		$url = $context['url_to_home'].$context['url_to_root'].'included/browser/visorFreemind.swf';

		// variables
		$flashvars = 'initLoadFile='.$target_href.'&openUrl=_self';

		$text = '<div id="freemind_viewer_'.$freemind_viewer_index.'">Flash plugin or Javascript are turned off. Activate both and reload to view the object</div>'."\n"
			.'<script type="text/javascript">// <![CDATA['."\n"
	        .'var params = {};'."\n"
	        .'params.base = "'.dirname($url).'/";'."\n"
	        .'params.quality = "high";'."\n"
	        .'params.wmode = "transparent";'."\n"
	        .'params.menu = "false";'."\n"
	        .'params.flashvars = "'.$flashvars.'";'."\n"
			.'swfobject.embedSWF("'.$url.'", "freemind_viewer_'.$freemind_viewer_index.'", "'.$width.'", "'.$height.'", "6", "'.$context['url_to_home'].$context['url_to_root'].'included/browser/expressinstall.swf", false, params);'."\n"

// 			// the following does not work under IE7...
// 			.'	var applet = document.createElement("embed");'."\n"
// //			.'	applet.setAttribute("classid", "clsid:8AD9C840-044E-11D1-B3E9-00805F499D93");'."\n"
// 			.'	applet.setAttribute("code", "freemind.main.FreeMindApplet.class");'."\n"
// 			.'	applet.setAttribute("archive", "'.$context['url_to_home'].$context['url_to_root'].'included/browser/freemindbrowser.jar");'."\n"
// 			.'	applet.setAttribute("type", "application/x-java-applet;version=1.4");'."\n"
// 			.'	applet.setAttribute("modes", "freemind.modes.browsemode.BrowseMode");'."\n"
// 			.'	applet.setAttribute("browsemode_initial_map", "'.$target_href.'");'."\n"
// 			.'	applet.setAttribute("initial_mode", "Browse");'."\n"
// 			.'	applet.setAttribute("selection_method", "selection_method_direct");'."\n"
// 			.'	applet.setAttribute("width", "'.$width.'");'."\n"
// 			.'	applet.setAttribute("height", "'.$height.'");'."\n"
// 			.'	applet.setAttribute("scriptable", "false");'."\n"
// 			.'	var handle = $("freemind_viewer_'.$freemind_viewer_index.'");'."\n"
// 			.'	handle.replaceChild(applet, handle.childNodes[0]);'."\n"
// 			.'}'."\n"
			.'// ]]></script>'."\n";

		// offer to download a copy of the map
		$menu = array_merge($menu, array($target_href => i18n::s('Browse this map with Freemind')));

		// display menu commands below the viewer
		if(count($menu))
			$text .= Skin::build_list($menu, 'menu_bar');

		// job done
		return $text;

	}


	/**
	 * render or not some text
	 *
	 * If variant = 'anonymous' and surfer is not logged, then display the block.
	 * If the surfer is an associate, then display the text.
	 * Else if the surfer is an authenticated member and variant = 'restricted', then display the text
	 * Else return an empty string
	 *
	 * @param string the text
	 * @param either 'anonymous', or 'restricted' or 'hidden'
	 * @return string the rendered text
	**/
	function &render_hidden($text, $variant) {

		// this block should only be visible from non-logged surfers
		if($variant == 'anonymous') {
			if(Surfer::is_logged())
				$text = '';
			return $text;
		}

		// associates may see everything else
		if(Surfer::is_associate())
			return $text;

		// this block is restricted to members
		if(Surfer::is_member() && ($variant == 'restricted'))
			return $text;

		// tough luck
		$text = '';
		return $text;
	}

	/**
	 * render a web link
	 *
	 * This function applies following transformations, in order to better classify links:
	 * - 'www.foo.bar' becomes 'http://www.foo.bar/'
	 * - 'anything@foo.bar' becomes 'mailto:anything@foo.bar'
	 *
	 * @param string the url as typed by the surfer
	 * @param string the related label, if any
	 * @return string the rendered text
	**/
	function &render_link($url, $label=NULL) {
		global $context;

		// remove leading and trailing spaces
		$url = trim($url);

		// rewrite links if necessary
		$from = array(
			'/^www\.([^\W\.]+?)\.([^\W]+?)/i',
			'/^([^:]+?)@([^\W\.]+?)\.([^\W]+?)/i'
			);

		$to = array(
			'http://\\0',
			'mailto:\\0'
			);

		$url = preg_replace($from, $to, $url);

		// let the rendering engine guess the type of this link
		if(is_callable(array('Skin', 'build_link')))
			$output =& Skin::build_link($url, $label, NULL);
		else
			$output = '<a href="'.$url.'">'.($label?$label:$url).'</a>';
		return $output;
	}

	/**
	 * render a list
	 *
	 * @param string the list content
	 * @param string the variant, if any
	 * @return string the rendered text
	**/
	function &render_list($content, $variant='') {
		global $context;

		if(!$content = trim($content)) {
			$output = NULL;
			return $output;
		}

		// preserve existing list, if any --coming from implied beautification
		if(preg_match('#^<ul>#', $content) && preg_match('#</ul>$#', $content))
			$items = preg_replace(array('#^<ul>#', '#</ul>$#'), '', $content);

		// split items
		else {
			$content = preg_replace(array("/<br \/>\n-/s", "/\n-/s", "/^-/", '/\[\*\]/'), '[*]', $content);
			$items = '<li>'.join('</li><li>', preg_split("/\[\*\]/s", $content, -1, PREG_SPLIT_NO_EMPTY)).'</li>';
		}

		// an ordinary bulleted list
		if(!$variant) {
			$output = '<ul>'.$items.'</ul>';
			return $output;

		// style a bulleted list, but ensure it's not numbered '1 incremental'
		} elseif($variant && (strlen($variant) > 1) && ($variant[1] != ' ')) {
			$output = '<ul class="'.$variant.'">'.$items.'</ul>';
			return $output;
		}

		// type has been deprecated, use styles
		$style = '';
		switch($variant) {
		case 'a':
			$style = 'style="list-style-type: lower-alpha"';
			break;

		case 'A':
			$style = 'style="list-style-type: upper-alpha"';
			break;

		case 'i':
			$style = 'style="list-style-type: lower-roman"';
			break;

		case 'I':
			$style = 'style="list-style-type: upper-roman"';
			break;

		default:
			$style = 'class="'.encode_field($variant).'"';
			break;

		}

		// a numbered list with style
		$output = '<ol '.$style.'>'.$items.'</ol>';
		return $output;
	}

	/**
	 * render a location
	 *
	 * @param string the id, with possible options or variant
	 * @return string the rendered text
	**/
	function &render_location($id) {
		global $context;

		// the required library
		include_once $context['path_to_root'].'locations/locations.php';

		// check all args
		$attributes = preg_split("/\s*,\s*/", $id, 3);

		// [location=latitude, longitude, label]
		if(count($attributes) === 3) {
			$item = array();
			$item['latitude'] = $attributes[0];
			$item['longitude'] = $attributes[1];
			$item['description'] = $attributes[2];

		// [location=id, label] or [location=id]
		} else {
			$id = $attributes[0];

			// a record is mandatory
			if(!$item =& Locations::get($id)) {
				if(Surfer::is_member()) {
					$output = '&#91;location='.$id.']';
					return $output;
				} else {
					$output = '';
					return $output;
				}
			}

			// build a small dynamic image if we cannot use Google maps
			if(!isset($context['google_api_key']) || !$context['google_api_key']) {
				$output = BR.'<img src="'.$context['url_to_root'].'locations/map_on_earth.php?id='.$item['id'].'" width="310" height="155" alt="'.$item['geo_position'].'"'.EOT.BR;
				return $output;
			}

			// use provided text, if any
			if(isset($attributes[1]))
				$item['description'] = $attributes[1].BR.$item['description'];

		}

		// map on Google
		$output =& Locations::map_on_google(array($item));
		return $output;

	}

	/**
	 * render several locations
	 *
	 * @param string 'all' or 'users'
	 * @return string the rendered text
	**/
	function &render_locations($id='all') {
		global $context;

		// the required library
		include_once $context['path_to_root'].'locations/locations.php';

		// get markers
		$items = array();
		switch($id) {
		case 'all':
			$items = Locations::list_by_date(0, 100, 'raw');
			break;

		case 'users':
			$items = Locations::list_users_by_date(0, 100, 'raw');
			break;

		default:
			if(Surfer::is_member()) {
				$output = '&#91;locations='.$id.']';
				return $output;
			} else {
				$output = '';
				return $output;
			}
		}

		// integrate with google maps
		$output =& Locations::map_on_google($items);
		return $output;

	}

	/**
	 * render some animated news
	 *
	 * We have replaced the old fat object by a lean, clean, and valid XHTML solution.
	 * However, as explained by Jeffrey Zeldmann in his book "designing with web standards",
	 * it may happen that this way of doing don't display correctly sometimes.
	 *
	 * @param string the variant - default is 'flash'
	 * @return string the rendered text
	**/
	function &render_news($variant) {
		global $context;

		switch($variant) {
		case 'flash':

			// sanity check
			if(!isset($context['root_flash_at_home']) || ($context['root_flash_at_home'] != 'Y'))
				$text = '';

			else {
				$url = $context['url_to_home'].$context['url_to_root'].'feeds/flash/slashdot.php';
				$flashvars = '';
				$text = '<div id="local_news" class="no_print">Flash plugin or Javascript are turned off. Activate both and reload to view the object</div>'."\n"
					.'<script type="text/javascript">// <![CDATA['."\n"
			        .'var params = {};'."\n"
			        .'params.base = "'.dirname($url).'/";'."\n"
			        .'params.quality = "high";'."\n"
			        .'params.wmode = "transparent";'."\n"
			        .'params.menu = "false";'."\n"
			        .'params.flashvars = "'.$flashvars.'";'."\n"
					.'swfobject.embedSWF("'.$url.'", "local_news", "80%", "50", "6", "'.$context['url_to_home'].$context['url_to_root'].'included/browser/expressinstall.swf", false, params);'."\n"
					.'// ]]></script>'."\n";
			}

			return $text;

		case 'dummy':
			$text = 'hello world';
			return $text;

		default:
			$text = '??'.$variant.'??';
			return $text;
		}
	}

	/**
	 * render a link to an object
	 *
	 * Following types are supported:
	 * - action - link to an action page
	 * - article - link to an article page
	 * - category - link to a category page
	 * - comment - link to a comment page
	 * - decision - link to a decision page
	 * - download - link to a download page
	 * - file - link to a file page
	 * - flash - display a file as a native flash object, or play a flash video
	 * - sound - launch dewplayer
	 * - go
	 * - image - display an in-line image
	 * - next - link to an article page
	 * - previous - link to an article page
	 * - section - link to a section page
	 * - server - link to a server page
	 * - user - link to a user page
	 *
	 * @param string the type
	 * @param string the id, with possible options or variant
	 * @return string the rendered text
	**/
	function &render_object($type, $id) {
		global $context;

		// depending on type
		switch($type) {

		// link to an action
		case 'action':
			include_once $context['path_to_root'].'actions/actions.php';

			// maybe an alternate title has been provided
			$attributes = preg_split("/\s*,\s*/", $id, 2);
			$id = $attributes[0];

			// ensure we have a label for this link
			if(isset($attributes[1])) {
				$text = $attributes[1];
				$type = 'basic'; // link is integrated in text
			} elseif(!$item =& Actions::get($id))
				$text = '';
			else
				$text =& Skin::strip($item['title']);
			if(!isset($text) || !$text)
				$text = sprintf(i18n::s('action %s'), $id);

			// make a link to the target page
			$url = Actions::get_url($id);

			// return a complete anchor
			$output =& Skin::build_link($url, $text, $type);
			return $output;

		// link to an article
		case 'article':

			// maybe an alternate title has been provided
			$attributes = preg_split("/\s*,\s*/", $id, 2);
			$id = $attributes[0];

			// load the record from the database
			if(!$item =& Articles::get($id))
				$output = sprintf(i18n::s('article %s'), $id);

			else {

				// ensure we have a label for this link
				if(isset($attributes[1])) {
					$text = $attributes[1];
					$type = 'basic'; // link is integrated in text
				} else
					$text = Skin::strip($item['title']);

				// make a link to the target page
				$url = Articles::get_url($item['id'], 'view', $item['title'], $item['nick_name']);

				// return a complete anchor
				$output =& Skin::build_link($url, $text, $type);
			}

			return $output;

		// link to a category
		case 'category':
			include_once $context['path_to_root'].'categories/categories.php';

			// maybe an alternate title has been provided
			$attributes = preg_split("/\s*,\s*/", $id, 2);
			$id = $attributes[0];

			// ensure we have a label for this link
			if(isset($attributes[1])) {
				$text = $attributes[1];
				$type = 'basic'; // link is integrated in text
			} elseif(!$item =& Categories::get($id))
				$text = '';
			else
				$text = Skin::strip($item['title']);
			if(!isset($text) || !$text)
				$text = sprintf(i18n::s('category %s'), $id);

			// make a link to the target page
			$url = Categories::get_url($id);

			// return a complete anchor
			$output =& Skin::build_link($url, $text, $type);
			return $output;

		// link to a comment
		case 'comment':
			include_once $context['path_to_root'].'comments/comments.php';

			// maybe an alternate title has been provided
			$attributes = preg_split("/\s*,\s*/", $id, 2);
			$id = $attributes[0];

			// ensure we have a label for this link
			if(isset($attributes[1])) {
				$text = $attributes[1];
				$type = 'basic'; // link is integrated in text
			}
			if(!isset($text) || !$text)
				$text = sprintf(i18n::s('comment %s'), $id);

			// make a link to the target page
			$url = Comments::get_url($id);

			// return a complete anchor
			$output =& Skin::build_link($url, $text, $type);
			return $output;

		// link to a decision
		case 'decision':
			include_once $context['path_to_root'].'decisions/decisions.php';

			// maybe an alternate title has been provided
			$attributes = preg_split("/\s*,\s*/", $id, 2);
			$id = $attributes[0];

			// ensure we have a label for this link
			if(isset($attributes[1])) {
				$text = $attributes[1];
				$type = 'basic'; // link is integrated in text
			}
			if(!isset($text) || !$text)
				$text = sprintf(i18n::s('decision %s'), $id);

			// make a link to the target page
			$url = Decisions::get_url($id);

			// return a complete anchor
			$output =& Skin::build_link($url, $text, $type);
			return $output;

		// link to a download
		case 'download':
			include_once $context['path_to_root'].'files/files.php';

			// maybe an alternate title has been provided
			$attributes = preg_split("/\s*,\s*/", $id, 2);
			$id = $attributes[0];

			// ensure we have a label for this link
			if(isset($attributes[1]))
				$text = $attributes[1];
			elseif(!$item =& Files::get($id))
				$text = '';
			else
				$text = Skin::strip( $item['title']?$item['title']:str_replace('_', ' ', $item['file_name']) );
			if(!isset($text) || !$text)
				$text = sprintf(i18n::s('file %s'), $id);

			// make a link to the target page
			if(isset($item['file_name']) && Files::is_stream($item['file_name']))
				$url = Files::get_url($id, 'stream', $item['file_name']);
			elseif(isset($item['file_name']))
				$url = Files::get_url($id, 'fetch', $item['file_name']);
			else
				$url = Files::get_url($id, 'fetch');

			// return a complete anchor
			$output =& Skin::build_link($url, $text, 'file');
			return $output;

		// link to a file
		case 'file':
			include_once $context['path_to_root'].'files/files.php';

			// maybe an alternate title has been provided
			$attributes = preg_split("/\s*,\s*/", $id, 2);
			$id = $attributes[0];

			// ensure we have a label for this link
			if(isset($attributes[1])) {
				$text = $attributes[1];
				$type = 'basic'; // link is integrated in text
			} elseif(!$item =& Files::get($id))
				$text = '';
			else
				$text = Skin::strip( $item['title']?$item['title']:str_replace('_', ' ', $item['file_name']) );
			if(!isset($text) || !$text)
				$text = sprintf(i18n::s('file %s'), $id);

			// make a link to the target page
			$url = Files::get_url($id, 'view', isset($item['file_name'])?$item['file_name']:'');

			// return a complete anchor
			$output =& Skin::build_link($url, $text, $type);
			return $output;

		// render a flash object
		case 'flash':
			include_once $context['path_to_root'].'files/files.php';

			// split parameters
			$attributes = preg_split("/\s*,\s*/", $id, 4);
			$id = $attributes[0];

			// get the file
			if(!$item =& Files::get($id)) {
				if(Surfer::is_associate())
					$output = '&#91;flash='.$id.']';
				else
					$output = '';
				return $output;
			}

			// stream in a separate page
			if(isset($attributes[1]) && preg_match('/window/i', $attributes[1])) {

				$output = '<a href="'.Files::get_url($id, 'stream', $item['file_name']).'" onclick="window.open(this.href); return false;" class="button"><span>'.i18n::s('Play in a separate window').'</span></a>';
				return $output;
			}

			// otherwise, we need at least the mandatory parameters
			if(@count($attributes) < 3) {
				$output = '&#91;flash=id, width, height]';
				return $output;
			}

			// object attributes
			$width = $attributes[1];
			$height = $attributes[2];
			$flashvars = '';
			if(isset($attributes[3]))
				$flashvars = $attributes[3];

			// where to get the file
			if(isset($item['file_href']) && $item['file_href'])
				$url = $item['file_href'];
			else
				$url = $context['url_to_home'].$context['url_to_root'].'files/'.str_replace(':', '/', $item['anchor']).'/'.rawurlencode($item['file_name']);

			// several ways to play flash
			switch(strtolower(substr(strrchr($url, '.'), 1))) {

			// native flash
			case 'swf':
				$output = '<div id="swf_'.$item['id'].'" class="no_print">Flash plugin or Javascript are turned off. Activate both and reload to view the object</div>'."\n"
					.'<script type="text/javascript">// <![CDATA['."\n"
			        .'var params = {};'."\n"
			        .'params.base = "'.dirname($url).'/";'."\n"
			        .'params.quality = "high";'."\n"
			        .'params.wmode = "transparent";'."\n"
			        .'params.menu = "false";'."\n"
			        .'params.flashvars = "'.$flashvars.'";'."\n"
					.'swfobject.embedSWF("'.$url.'", "swf_'.$item['id'].'", "'.$width.'", "'.$height.'", "6", "'.$context['url_to_home'].$context['url_to_root'].'included/browser/expressinstall.swf", false, params);'."\n"
					.'// ]]></script>'."\n";
				return $output;

			// stream a flash video
			case 'flv':

				// a flash player to stream a flash video
				$flvplayer_url = $context['url_to_root'].'included/browser/flvplayer.swf';

				// pass parameters to the player
				if($flashvars)
					$flashvars .= 'file='.$url.'&'.$flashvars;
				else
					$flashvars .= 'file='.$url;

				// the full object is built in Javascript
				$output = '<div id="flv_'.$item['id'].'" class="no_print">Flash plugin or Javascript are turned off. Activate both and reload to view the object</div>'."\n"
					.'<script type="text/javascript">// <![CDATA['."\n"
			        .'var params = {};'."\n"
			        .'params.base = "'.dirname($url).'/";'."\n"
			        .'params.quality = "high";'."\n"
			        .'params.wmode = "transparent";'."\n"
			        .'params.menu = "false";'."\n"
			        .'params.flashvars = "'.$flashvars.'";'."\n"
					.'swfobject.embedSWF("'.$flvplayer_url.'", "flv_'.$item['id'].'", "'.$width.'", "'.$height.'", "6", "'.$context['url_to_home'].$context['url_to_root'].'included/browser/expressinstall.swf", false, params);'."\n"
					.'// ]]></script>'."\n";
				return $output;

			// link to file page
			default:

				// link label
				$text = Skin::strip( $item['title']?$item['title']:str_replace('_', ' ', $item['file_name']) );
				if(!isset($text))
					$text = sprintf(i18n::s('file %s'), $id);

				// make a link to the target page
				$url = Files::get_url($id, 'view', $item['file_name']);

				// return a complete anchor
				$output =& Skin::build_link($url, $text, $type);
				return $output;

			}

		// invoke the selector
		case 'go':

			// extract the label, if any
			$attributes = preg_split("/\s*,\s*/", $id, 2);
			$name = $attributes[0];

			// ensure we have a label for this link
			if(isset($attributes[1])) {
				$text = $attributes[1];
				$type = 'basic'; // link is integrated in text
			} else
				$text = $name;

			// be cool with search engines
			if($context['with_friendly_urls'] == 'R')
				$url = 'go/'.rawurlencode($name);
			elseif($context['with_friendly_urls'] == 'Y')
				$url = 'go.php/'.rawurlencode($name);
			else
				$url = 'go.php?id='.urlencode($name);

			// return a complete anchor
			$output =& Skin::build_link($url, $text, $type);
			return $output;

		// embed an image
		case 'image':
			include_once $context['path_to_root'].'images/images.php';

			// get the variant, if any
			$attributes = preg_split("/\s*,\s*/", $id, 2);
			$id = $attributes[0];
			if(isset($attributes[1]))
				$variant = $attributes[1];
			else
				$variant = 'inline';

			// get the image record
			if(!$image =& Images::get($id)) {
				if(Surfer::is_member())
					$output = '&#91;image='.$id.']';
				else
					$output = '';
				return $output;
			}

			// a title for the image --do not force a title
			if(isset($image['title']))
				$title = $image['title'];
			else
				$title = '';

			// provide thumbnail if not defined, or forced, or for large images
			if(!$image['use_thumbnail']
				|| ($image['use_thumbnail'] == 'A')
				|| (($image['use_thumbnail'] == 'Y') && ($image['image_size'] > $context['thumbnail_threshold'])) ) {

				// not inline anymore, but thumbnail --preserve other variants
				if($variant == 'inline')
					$variant = 'thumbnail';

				// where to fetch the image file
				$href = Images::get_thumbnail_href($image);

				// to drive to plain image
				$link = Images::get_icon_href($image);

			// add an url, if any
			} elseif($image['link_url']) {

				// flag large images
				if($image['image_size'] > $context['thumbnail_threshold'])
					$variant = rtrim('large '.$variant);

				// where to fetch the image file
				$href = Images::get_icon_href($image);

				// transform local references, if any
				include_once $context['path_to_root'].'/links/links.php';
				$attributes = Links::transform_reference($image['link_url']);
				if($attributes[0])
					$link = $context['url_to_root'].$attributes[0];

				// direct use of this link
				else
					$link = $image['link_url'];

			// get the <img ... /> element
			} else {

				// do not append poor titles to inline images
				if($variant == 'inline')
					$title = '';

				// flag large images
				if($image['image_size'] > $context['thumbnail_threshold'])
					$variant = rtrim('large '.$variant);

				// where to fetch the image file
				$href = Images::get_icon_href($image);

				// no link
				$link = '';

			}

			// use the skin
			$output =& Skin::build_image($variant, $href, $title, $link);
			return $output;

		// embed a stack of images
		case 'images':
			include_once $context['path_to_root'].'images/images.php';

			// get the list of ids
			$ids = preg_split("/\s*,\s*/", $id);
			if(!count($ids)) {
				$output =  '&#91;images=id1, id2, ...]';
				return $output;
			}

			// build the list of images
			$items = array();
			foreach($ids as $id) {

				// get the image record
				if($image =& Images::get($id)) {

					// a title for the image --do not force a title
					if(isset($image['title']))
						$title = $image['title'];
					else
						$title = '';

					// provide thumbnail if not defined, or forced, or for large images
					$variant = 'inline';
					if(!$image['use_thumbnail']
						|| ($image['use_thumbnail'] == 'A')
						|| (($image['use_thumbnail'] == 'Y') && ($image['image_size'] > $context['thumbnail_threshold'])) ) {

						// not inline anymore, but thumbnail
						$variant = 'thumbnail';

						// where to fetch the image file
						$href = Images::get_thumbnail_href($image);

						// to drive to plain image
						$link = $context['url_to_root'].Images::get_url($id);

					// add an url, if any
					} elseif($image['link_url']) {

						// flag large images
						if($image['image_size'] > $context['thumbnail_threshold'])
							$variant = rtrim('large '.$variant);

						// where to fetch the image file
						$href = Images::get_icon_href($image);

						// transform local references, if any
						include_once $context['path_to_root'].'/links/links.php';
						$attributes = Links::transform_reference($image['link_url']);
						if($attributes[0])
							$link = $context['url_to_root'].$attributes[0];

						// direct use of this link
						else
							$link = $image['link_url'];

					// get the <img ... /> element
					} else {

						// flag large images
						if($image['image_size'] > $context['thumbnail_threshold'])
							$variant = rtrim('large '.$variant);

						// where to fetch the image file
						$href = Images::get_icon_href($image);

						// no link
						$link = '';

					}

					// use the skin
					$label =& Skin::build_image($variant, $href, $title, $link);

					// add item to the stack
					$items[ $href ]  = array('', $label, '', 'image', NULL);

				}

			}

			// format the list
			$output = '';
			if(count($items)) {

				// stack items
				$output = Skin::build_list($items, 'stack');

				// rotate items
				$output = Skin::rotate($output);
			}

			// done
			return $output;

		// link to the next article
		case 'next':

			// maybe an alternate title has been provided
			$attributes = preg_split("/\s*,\s*/", $id, 2);
			$id = $attributes[0];

			// ensure we have a label for this link
			if(isset($attributes[1])) {
				$text = $attributes[1];
				$type = 'basic'; // link is integrated in text
			} elseif(!$item =& Articles::get($id))
				$text = '';
			else
				$text = Skin::strip($item['title']);
			if(!isset($text) || !$text)
				$text = sprintf(i18n::s('article %s'), $id);

			// make a link to the target page
			$url = Articles::get_url($id);

			// return a complete anchor
			$output =& Skin::build_link($url, $text, $type);
			return $output;

		// link to the previous article
		case 'previous':

			// maybe an alternate title has been provided
			$attributes = preg_split("/\s*,\s*/", $id, 2);
			$id = $attributes[0];

			// ensure we have a label for this link
			if(isset($attributes[1])) {
				$text = $attributes[1];
				$type = 'basic'; // link is integrated in text
			} elseif(!$item =& Articles::get($id))
				$text = '';
			else
				$text = Skin::strip($item['title']);
			if(!isset($text) || !$text)
				$text = sprintf(i18n::s('article %s'), $id);

			// make a link to the target page
			$url = Articles::get_url($id);

			// return a complete anchor
			$output =& Skin::build_link($url, $text, $type);
			return $output;

		// link to a section
		case 'section':

			// maybe an alternate title has been provided
			$attributes = preg_split("/\s*,\s*/", $id, 2);
			$id = $attributes[0];

			// ensure we have a label for this link
			if(isset($attributes[1])) {
				$text = $attributes[1];
				$type = 'basic'; // link is integrated in text
			} elseif(!$item =& Sections::get($id))
				$text = '';
			else
				$text = Skin::strip($item['title']);
			if(!isset($text) || !$text)
				$text = sprintf(i18n::s('section %s'), $id);

			// make a link to the target page
			$url = Sections::get_url($id);

			// return a complete anchor
			$output =& Skin::build_link($url, $text, $type);
			return $output;

		// link to a server
		case 'server':
			include_once $context['path_to_root'].'servers/servers.php';

			// maybe an alternate title has been provided
			$attributes = preg_split("/\s*,\s*/", $id, 2);
			$id = $attributes[0];

			// ensure we have a label for this link
			if(isset($attributes[1])) {
				$text = $attributes[1];
				$type = 'basic'; // link is integrated in text
			} elseif(!$item =& Servers::get($id))
				$text = '';
			else
				$text = Skin::strip($item['title']);
			if(!isset($text) || !$text)
				$text = sprintf(i18n::s('server %s'), $id);

			// make a link to the target page
			$url = Servers::get_url($id);

			// return a complete anchor
			$output =& Skin::build_link($url, $text, $type);
			return $output;

		// render a sound object
		case 'sound':
			include_once $context['path_to_root'].'files/files.php';

			// maybe an alternate title has been provided
			$attributes = preg_split("/\s*,\s*/", $id, 2);
			$id = $attributes[0];
			$flashvars = '';
			if(isset($attributes[1]))
				$flashvars = $attributes[1];

			// get the file
			if(!$item =& Files::get($id)) {
				if(Surfer::is_associate())
					$output = '&#91;sound='.$id.']';
				else
					$output = '';
				return $output;
			}

			// where to get the file
			if(isset($item['file_href']) && $item['file_href'])
				$url = $item['file_href'];
			else
				$url = $context['url_to_root'].'files/'.str_replace(':', '/', $item['anchor']).'/'.rawurlencode($item['file_name']);

			// several ways to play flash
			switch(strtolower(substr(strrchr($url, '.'), 1))) {

			// stream a sound file
			case 'mp3':

				// a flash player to stream a sound
				$dewplayer_url = $context['url_to_root'].'included/browser/dewplayer.swf';
				if($flashvars)
					$flashvars = 'son='.$url.'&'.$flashvars;
				else
					$flashvars = 'son='.$url;

				$output = '<div id="sound_'.$item['id'].'" class="no_print">Flash plugin or Javascript are turned off. Activate both and reload to view the object</div>'."\n"
					.'<script type="text/javascript">// <![CDATA['."\n"
			        .'var params = {};'."\n"
			        .'params.base = "'.dirname($url).'/";'."\n"
			        .'params.quality = "high";'."\n"
			        .'params.wmode = "transparent";'."\n"
			        .'params.menu = "false";'."\n"
			        .'params.flashvars = "'.$flashvars.'";'."\n"
					.'swfobject.embedSWF("'.$dewplayer_url.'", "sound_'.$item['id'].'", "200", "20", "6", "'.$context['url_to_home'].$context['url_to_root'].'included/browser/expressinstall.swf", false, params);'."\n"
					.'// ]]></script>'."\n";
				return $output;

			// link to file page
			default:

				// link label
				$text = Skin::strip( $item['title']?$item['title']:str_replace('_', ' ', $item['file_name']) );
				if(!isset($text))
					$text = sprintf(i18n::s('file %s'), $id);

				// make a link to the target page
				$url = Files::get_url($id, 'view', $item['file_name']);

				// return a complete anchor
				$output =& Skin::build_link($url, $text, $type);
				return $output;

			}

		// link to a user
		case 'user':

			// maybe an alternate title has been provided
			$attributes = preg_split("/\s*,\s*/", $id, 2);
			$id = $attributes[0];

			// ensure we have a label for this link
			if(isset($attributes[1])) {
				$text = $attributes[1];
				$type = 'basic'; // link is integrated in text
			} elseif(!$item =& Users::get($id))
				$text = '';
			elseif(isset($item['nick_name']))
				$text = ucfirst($item['nick_name']);
			if(!isset($text) || !$text)
				$text = sprintf(i18n::s('user %s'), $id);

			// make a link to the target page
			$url = Users::get_url($id);

			// return a complete anchor
			$output =& Skin::build_link($url, $text, $type);
			return $output;

		// invalid type
		default:
			$output = '['.$type.']';
			return $output;

		}

	}

	/**
	 * render a block of code
	 *
	 * @param string the text
	 * @return string the rendered text
	**/
	function &render_pre($text, $variant='snippet') {

		// change new lines
		$text = trim(str_replace(array('<br><br>', '<br /><br />', '<br>', '<br\s*?/>'), array("\n\n", "\n\n", "\n", "\n"), str_replace("\r", '', $text)));

		// wrap long lines in code
		if($variant == 'php') {
			if($lines = split("\n", $text)) {
				$text = '';
				foreach($lines as $line)
					$text .= wordwrap($line, 75, "\n	", 1)."\n";
			}
		}

		// match some php code
		$explicit = FALSE;
		if(preg_match('/<\?php\s/', $text))
			$variant = 'php';
		elseif(($variant == 'php') && !preg_match('/<\?'.'php.+'.'\?'.'>/', $text)) {
			$text = '<?'.'php'."\n".$text."\n".'?'.'>';
			$explicit = TRUE;
		}

		// highlight php code, if any
		if($variant == 'php') {

			// handle newlines and indentations properly
			$text = str_replace(array("\n	 ", "\n<span", "\n</code", "\n</pre", "\n</span"), array("\n&nbsp;&nbsp;&nbsp;&nbsp;", '<span', '</code', '</pre', '</span'), Safe::highlight_string($text));

			// remove explicit php prefix and suffix -- dependant of highlight_string() evolution
			if($explicit)
				$text = preg_replace(array('/&lt;\?php<br\s*\/*>/', '/\?&gt;/'), '', $text);

		// or prevent html rendering
		} else
			$text = str_replace(array('<', "\n"), array('&lt;', '<br/>'), $text);

		// disable further codes and smilies transformations
		$search = array(	'[',		']',		':',		'//',	'<p>',	'</p>');
		$replace = array(	'&#91;',	'&#93;',	'&#58;',	'&#47;&#47;',	'', 	'');
		$output = '<pre>'.str_replace($search, $replace, $text).'</pre>';
		return $output;

	}

	/**
	 * render a compact list of recent publications
	 *
	 * The provided anchor can reference:
	 * - a section 'section:123'
	 * - a category 'category:456'
	 * - a user 'user:789'
	 * - nothing
	 *
	 * @param string the anchor (e.g. 'section:123')
	 * @return string the rendered text
	**/
	function &render_published($anchor='') {
		global $context;

		// number of items to display
		$count = COMPACT_LIST_SIZE;
		if($position = strpos($anchor, ',')) {
			$count = (integer)trim(substr($anchor, $position+1));
			$anchor = substr($anchor, 0, $position);
		}
		if(!$count)
			$count = COMPACT_LIST_SIZE;

		// load the layout to use
		$layout = 'compact';

		// sanity check
		$anchor = trim($anchor);

		// scope is limited to one section
		if(strpos($anchor, 'section:') === 0) {

			// look at this level
			$anchors = array($anchor);

			// first level of depth
			$topics =& Sections::get_children_of_anchor($anchor, 'main');
			$anchors = array_merge($anchors, $topics);

			// second level of depth
			if(count($topics) && (count($anchors) < 50)) {
				$topics =& Sections::get_children_of_anchor($topics, 'main');
				$anchors = array_merge($anchors, $topics);
			}

			// third level of depth
			if(count($topics) && (count($anchors) < 50)) {
				$topics =& Sections::get_children_of_anchor($topics, 'main');
				$anchors = array_merge($anchors, $topics);
			}

			// query the database and layout that stuff
			if($text = Articles::list_by_date_for_anchor($anchors, 0, $count, $layout)) {

				// we have an array to format
				if(is_array($text))
					$text =& Skin::build_list($text, 'compact');

			}

		// scope is limited to one category
		} elseif(strpos($anchor, 'category:') === 0) {

			// first level of depth
			$anchors = array();

			// get sections linked to this category
			if($topics =& Members::list_sections_by_title_for_anchor($anchor, 0, 50, 'raw')) {
				foreach($topics as $id => $not_used)
					$anchors = array_merge($anchors, array('section:'.$id));
			}

			// second level of depth
			if(count($topics) && (count($anchors) < 50)) {
				$topics =& Sections::get_children_of_anchor($anchors, 'main');
				$anchors = array_merge($anchors, $topics);
			}

			// third level of depth
			if(count($topics) && (count($anchors) < 50)) {
				$topics =& Sections::get_children_of_anchor($anchors, 'main');
				$anchors = array_merge($anchors, $topics);
			}

			// the category itself is an anchor
			$anchors[] = $anchor;

			// ensure anchors are referenced only once
			$anchors = array_unique($anchors);

			// query the database and layout that stuff
			if($text = Members::list_articles_by_date_for_anchor($anchors, 0, $count, $layout)) {

				// we have an array to format
				if(is_array($text))
					$text =& Skin::build_list($text, 'compact');

			}

		// scope is limited to one author
		} elseif(strpos($anchor, 'user:') === 0) {

			// query the database and layout that stuff
			if($text = Articles::list_by_date_for_author(str_replace('user:', '', $anchor), 0, $count, $layout)) {

				// we have an array to format
				if(is_array($text))
					$text =& Skin::build_list($text, 'compact');

			}
		// consider all pages
		} else {

			// query the database and layout that stuff
			if($text = Articles::list_by_date(0, $count, $layout)) {

				// we have an array to format
				if(is_array($text))
					$text =& Skin::build_list($text, 'compact');

			}
		}

		// job done
		return $text;
	}

	/**
	 * render a compact list of hits
	 *
	 * @param string the anchor (e.g. 'section:123')
	 * @return string the rendered text
	**/
	function &render_read($anchor='') {
		global $context;

		// number of items to display
		$count = COMPACT_LIST_SIZE;
		if($position = strpos($anchor, ',')) {
			$count = (integer)trim(substr($anchor, $position+1));
			$anchor = substr($anchor, 0, $position);
		}
		if(!$count)
			$count = COMPACT_LIST_SIZE;

		// load the layout to use
		$layout = 'compact';

		// scope is limited to one section
		if($anchor) {

			// look at this level
			$anchors = array($anchor);

			// first level of depth
			$topics =& Sections::get_children_of_anchor($anchor, 'main');
			$anchors = array_merge($anchors, $topics);

			// second level of depth
			if(count($topics) && (count($anchors) < 50)) {
				$topics =& Sections::get_children_of_anchor($topics, 'main');
				$anchors = array_merge($anchors, $topics);
			}

			// third level of depth
			if(count($topics) && (count($anchors) < 50)) {
				$topics =& Sections::get_children_of_anchor($topics, 'main');
				$anchors = array_merge($anchors, $topics);
			}

			// query the database and layout that stuff
			if($text = Articles::list_by_hits_for_anchor($anchors, 0, $count, $layout)) {

				// we have an array to format
				if(is_array($text))
					$text =& Skin::build_list($text, 'compact');

			}

		// consider all threads
		} else {

			// query the database and layout that stuff
			if($text = Articles::list_by_hits(0, $count, $layout)) {

				// we have an array to format
				if(is_array($text))
					$text =& Skin::build_list($text, 'compact');

			}
		}

		// job done
		return $text;
	}

	/**
	 * render a table
	 *
	 * @param string the table content
	 * @param string the variant, if any
	 * @return string the rendered text
	**/
	function render_table($content, $variant='') {
		global $context;

		// render an inline table
		if(!$content) {
			include_once $context['path_to_root'].'tables/tables.php';
			$output =& Tables::build($variant, 'inline');
			return $output;
		}

		// we are providing inline tables
		if($variant)
			$variant = 'inline '.$variant;
		else
			$variant = 'inline';

		// do we have headers to proceed?
		$in_body = !preg_match('/\[body\]/i', $content);

		// start at first line, except if headers have to be printed first
		if($in_body)
			$count = 1;
		else
			$count = 2;

		// split lines
		$rows = explode("\n", $content);
		if(!is_array($rows))
			return '';

		// one row per line - cells are separated by |, \t, or 2 spaces
		$text =& Skin::table_prefix($variant);
		foreach($rows as $row) {

			// skip blank lines
			if(!$row)
				continue;

			// header row
			if(!$in_body) {
				if(preg_match('/\[body\]/i', $row))
					$in_body = true;
				else
					$text .= Skin::table_row(preg_split("/([\|\t]|	)/", $row), 'header');

			// body row
			} else
				$text .= Skin::table_row(preg_split("/([\|\t]|	)/", $row), $count++);

		}

		// return the complete table
		$text .= Skin::table_suffix();
		return $text;
	}

	/**
	 * render a table of links
	 *
	 * @param string the variant
	 * @return string the rendered text
	**/
	function &render_table_of($variant) {
		global $codes_toc, $codes_toq;

		// list of questions for a FAQ
		if($variant == 'questions') {

			// to be rendered by css, using selector .toq_box ul, etc.
			$text = '<ul>'."\n";
			foreach($codes_toq as $link)
				$text .= '<li>'.$link.'</li>'."\n";
			$text .= '</ul>'."\n";

			$output =& Skin::build_box('', $text, 'toq');
			return $output;

		// list of titles
		} else {

			// to be rendered by css, using selector .toc_box ul, etc.
			// <ul>
			// <li>1. link</li> 		0 -> 1
			// <li>1. link				1 -> 1
			//		<ul>
			//		<li>2. link</li>	1 -> 2
			//		<li>2. link</li>	2 -> 2
			//		</ul></li>
			// <li>1. link</li> 		2 -> 1
			// </ul>
			$text ='';
			$previous_level = 0;
			foreach($codes_toc as $attributes) {
				list($level, $link) = $attributes;
				if($level > $previous_level)
					$text .= '<ul>'."\n";
				elseif($level < $previous_level)
					$text .= '</li></ul></li>'."\n";
				elseif($previous_level)
					$text .= '</li>'."\n";

				$text .= '<li>'.$link;

				$previous_level = $level;
			}

			while($previous_level-- > 0)
				$text .= '</li></ul>'."\n";

			$output =& Skin::build_box('', $text, 'toc');
			return $output;
		}
	}

	/**
	 * render a title, a sub-title, or a question
	 *
	 * @param string the text
	 * @param string the variant
	 * @return string the rendered text
	**/
	function &render_title($text, $variant) {
		global $codes_base, $codes_toc, $codes_toq, $context;

		// ensure we have a base reference to use
		if(!$codes_base)
			$codes_base = $context['self_url'];

		// remember questions
		if($variant == 'question') {
			$index = count($codes_toq)+1;
			$id = 'question_'.$index;
			$url = $codes_base.'#'.$id;
			$codes_toq[] = Skin::build_link($url, ucfirst($text), 'basic');
			$text = QUESTION_FLAG.$text;

		// remember level 1 titles ([title]...[/title] or [header1]...[/header1])
		} elseif($variant == 'header1') {
			$index = count($codes_toc)+1;
			$id = 'title_'.$index;
			$url = $codes_base.'#'.$id;
			$codes_toc[] = array(1, Skin::build_link($url, ucfirst($text), 'basic'));

		// remember level 2 titles ([subtitle]...[/subtitle] or [header2]...[/header2])
		} elseif($variant == 'header2') {
			$index = count($codes_toc)+1;
			$id = 'title_'.$index;
			$url = $codes_base.'#'.$id;
			$codes_toc[] = array(2, Skin::build_link($url, ucfirst($text), 'basic'));

		// remember level 3 titles
		} elseif($variant == 'header3') {
			$index = count($codes_toc)+1;
			$id = 'title_'.$index;
			$url = $codes_base.'#'.$id;
			$codes_toc[] = array(3, Skin::build_link($url, ucfirst($text), 'basic'));

		// remember level 4 titles
		} elseif($variant == 'header4') {
			$index = count($codes_toc)+1;
			$id = 'title_'.$index;
			$url = $codes_base.'#'.$id;
			$codes_toc[] = array(4, Skin::build_link($url, ucfirst($text), 'basic'));

		// remember level 5 titles
		} elseif($variant == 'header5') {
			$index = count($codes_toc)+1;
			$id = 'title_'.$index;
			$url = $codes_base.'#'.$id;
			$codes_toc[] = array(5, Skin::build_link($url, ucfirst($text), 'basic'));
		}

		// the rendered text
		$output =& Skin::build_block($text, $variant, $id);
		return $output;
	}

	/**
	 * render a compact list of recent modifications
	 *
	 * The provided anchor can reference:
	 * - a section 'section:123'
	 * - a category 'category:456'
	 * - a user 'user:789'
	 * - nothing
	 *
	 * @param string the anchor (e.g. 'section:123')
	 * @return string the rendered text
	**/
	function &render_updated($anchor='') {
		global $context;

		// number of items to display
		$count = COMPACT_LIST_SIZE;
		if($position = strpos($anchor, ',')) {
			$count = (integer)trim(substr($anchor, $position+1));
			$anchor = substr($anchor, 0, $position);
		}
		if(!$count)
			$count = COMPACT_LIST_SIZE;

		// load the layout to use
		$layout = 'compact';

		// sanity check
		$anchor = trim($anchor);

		// scope is limited to one section
		if(strpos($anchor, 'section:') === 0) {

			// look at this level
			$anchors = array($anchor);

			// first level of depth
			$topics =& Sections::get_children_of_anchor($anchor, 'main');
			$anchors = array_merge($anchors, $topics);

			// second level of depth
			if(count($topics) && (count($anchors) < 50)) {
				$topics =& Sections::get_children_of_anchor($topics, 'main');
				$anchors = array_merge($anchors, $topics);
			}

			// third level of depth
			if(count($topics) && (count($anchors) < 50)) {
				$topics =& Sections::get_children_of_anchor($topics, 'main');
				$anchors = array_merge($anchors, $topics);
			}

			// query the database and layout that stuff
			if($text = Articles::list_by_edition_date_for_anchor($anchors, 0, $count, $layout)) {

				// we have an array to format
				if(is_array($text))
					$text =& Skin::build_list($text, 'compact');

			}

		// scope is limited to one category
		} elseif(strpos($anchor, 'category:') === 0) {

			// first level of depth
			$anchors = array();

			// get sections linked to this category
			if($topics =& Members::list_sections_by_title_for_anchor($anchor, 0, 50, 'raw')) {
				foreach($topics as $id => $not_used)
					$anchors = array_merge($anchors, array('section:'.$id));
			}

			// second level of depth
			if(count($topics) && (count($anchors) < 50)) {
				$topics =& Sections::get_children_of_anchor($anchors, 'main');
				$anchors = array_merge($anchors, $topics);
			}

			// third level of depth
			if(count($topics) && (count($anchors) < 50)) {
				$topics =& Sections::get_children_of_anchor($anchors, 'main');
				$anchors = array_merge($anchors, $topics);
			}

			// the category itself is an anchor
			$anchors[] = $anchor;

			// ensure anchors are referenced only once
			$anchors = array_unique($anchors);

			// query the database and layout that stuff
			if($text = Members::list_articles_by_date_for_anchor($anchors, 0, $count, $layout)) {

				// we have an array to format
				if(is_array($text))
					$text =& Skin::build_list($text, 'compact');

			}

		// scope is limited to one author
		} elseif(strpos($anchor, 'user:') === 0) {

			// query the database and layout that stuff
			if($text = Articles::list_by_date_for_author(str_replace('user:', '', $anchor), 0, $count, $layout)) {

				// we have an array to format
				if(is_array($text))
					$text =& Skin::build_list($text, 'compact');

			}
		// consider all pages
		} else {

			// query the database and layout that stuff
			if($text = Articles::list_by('edition', 0, $count, $layout)) {

				// we have an array to format
				if(is_array($text))
					$text =& Skin::build_list($text, 'compact');

			}
		}

		// job done
		return $text;
	}

	/**
	 * render a link to Wikipedia
	 *
	 * @param string the id, with possible options or variant
	 * @return string the rendered text
	**/
	function &render_wikipedia($id) {
		global $context;

		// maybe an alternate title has been provided
		$attributes = preg_split("/\s*,\s*/", $id, 2);
		$id = $attributes[0];

		// ensure we have a label for this link
		if(isset($attributes[1]))
			$text = $attributes[1];
		else
			$text = '';

		// select the language
		$language = $context['preferred_language'];

		// take the navigator language if possible
		if (isset($context['language']) && $context['without_language_detection']=='N')
			$language = $context['language'];

		// make a link to the target page
		$url = 'http://'.$language.'.wikipedia.org/wiki/Special:Search?search='.preg_replace('[\s]', '_', $id);

		// return a complete anchor
		$output =& Skin::build_link($url, $text, 'wikipedia');
		return $output;

	}


	/**
	 * remove YACS codes from a string
	 *
	 * @param string embedding YACS codes
	 * @return a purged string
	 */
	function &strip($text) {
		global $context;

		// suppress pairing codes
		$text = preg_replace('/\[(.+?)\](.+?)\[\/(.+?)\]/s', '\\2', $text);

		// suppress bracketed words
		$text = trim(preg_replace('/\[(.+?)\]/s', ' ', $text));

		return $text;
	}
}

// load localized strings
if(is_callable(array('i18n', 'bind')))
	i18n::bind('codes');

?>