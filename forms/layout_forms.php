<?php
/**
 * layout forms
 *
 * This is the default layout for forms.
 *
 * @see forms/index.php
 * @see forms/forms.php
 *
 * @author Bernard Paques [email]bernard.paques@bigfoot.com[/email]
 * @reference
 * @license http://www.gnu.org/copyleft/lesser.txt GNU Lesser General Public License
 */
Class Layout_forms extends Layout_interface {

	/**
	 * list forms
	 *
	 * @param resource the SQL result
	 * @return array of resulting items, or NULL
	 *
	 * @see skins/layout.php
	**/
	function &layout(&$result) {
		global $context;

		// we return an array of ($url => $attributes)
		$items = array();

		// empty list
		if(!SQL::count($result))
			return $items;

		// flag forms updated recently
		if($context['site_revisit_after'] < 1)
			$context['site_revisit_after'] = 2;
		$dead_line = gmstrftime('%Y-%m-%d %H:%M:%S', mktime(0,0,0,date("m"),date("d")-$context['site_revisit_after'],date("Y")));
		$now = gmstrftime('%Y-%m-%d %H:%M:%S');

		// process all items in the list
		while($item =& SQL::fetch($result)) {

			// get the main anchor
			$anchor = Anchors::get($item['anchor']);

			// the url to view this item
			$url = Forms::get_url($item['id'], 'view', $item['title']);

			// reset the rendering engine between items
			Codes::initialize($url);

			// use the title to label the link
			$title = Codes::beautify_title($item['title']);

			// initialize variables
			$prefix = $suffix = $icon = '';

			// flag forms that are created or updated very recently
			if($item['edit_date'] >= $dead_line)
				$suffix .= UPDATED_FLAG;

			// signal restricted and private forms
			if($item['active'] == 'N')
				$prefix .= PRIVATE_FLAG;
			elseif($item['active'] == 'R')
				$prefix .= RESTRICTED_FLAG;

			// show details only to associates
			if(Surfer::is_associate()) {
				$details = array();

				if($item['nick_name'])
					$details[] = sprintf('{%s}', $item['nick_name']);

				// the main anchor link
				if(is_object($anchor))
					$details[] = sprintf(i18n::s('to %s'), Skin::build_link($anchor->get_url(), ucfirst($anchor->get_title()), 'section'));

				// the last action
				$details[] = sprintf(i18n::s('edited %s'), Skin::build_date($item['edit_date']));

				// edit command
				$details[] = Skin::build_link(Forms::get_url($item['id'], 'edit'), i18n::s('edit'), 'basic');

				// delete command
				$details[] = Skin::build_link(Forms::get_url($item['id'], 'delete'), i18n::s('delete'), 'basic');

				// append details to the suffix
				$suffix .= BR.Skin::finalize_list($details, 'menu');

			}

			// the introductory text
			if($item['introduction'])
				$suffix .= BR.Codes::beautify($item['introduction']);

			// display anchor thumbnail,if any
			if(is_object($anchor))
				$icon = $anchor->get_thumbnail_url();

			// list all components for this item
			$items[$url] = array($prefix, $title, $suffix, 'form', $icon);

		}

		// end of processing
		SQL::free($result);
		return $items;
	}

}

?>