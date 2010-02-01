<?php
/**
 * layout articles to display related rights of the surfer
 *
 * This layout has been designed to help users to understand their rights on listed pages.
 *
 * Final rendering is a table with following columns:
 * - item - including the title, which is also a clickable link to the page, tags, etc
 * - update - dates of last modification
 * - owner - with a checkbox if the surfer is an owner of this item
 * - editor - with a checkbox if the surfer is an editor of this item
 * - watcher - with a checkbox if the surfer is a watcher of this item
 *
 * @author Bernard Paques
 * @reference
 * @license http://www.gnu.org/copyleft/lesser.txt GNU Lesser General Public License
 */
Class Layout_articles_as_rights extends Layout_interface {

	/**
	 * the preferred number of items for this layout
	 *
	 * @return 50
	 *
	 * @see skins/layout.php
	 */
	function items_per_page() {
		return 50;
	}

	/**
	 * list articles as rows in a table
	 *
	 * @param resource the SQL result
	 * @return string the rendered text
	**/
	function &layout(&$result) {
		global $context;

		// we return some text
		$text = '';

		// empty list
		if(!SQL::count($result))
			return $text;

		// we list pages for one surfer
		// sanity check
		if(!isset($this->layout_variant))
			$this->layout_variant = Surfer::get_id();

		// flag articles updated recently
		$now = gmstrftime('%Y-%m-%d %H:%M:%S');
		if($context['site_revisit_after'] < 1)
			$context['site_revisit_after'] = 2;
		$dead_line = gmstrftime('%Y-%m-%d %H:%M:%S', mktime(0,0,0,date("m"),date("d")-$context['site_revisit_after'],date("Y")));

		// build a list of articles
		Skin::define_img('CHECKED_IMG', 'ajax/accept.png', '*');
		$rows = array();
		include_once $context['path_to_root'].'comments/comments.php';
		include_once $context['path_to_root'].'links/links.php';
		include_once $context['path_to_root'].'overlays/overlay.php';
		while($item =& SQL::fetch($result)) {

			// get the related overlay
			$overlay = Overlay::load($item);

			// get the anchor
			$anchor =& Anchors::get($item['anchor']);

			// the url to view this item
			$url =& Articles::get_permalink($item);

			// reset everything
			$summary = $update = $owner = $editor = $watcher = '';

			// signal articles to be published
			if(!isset($item['publish_date']) || ($item['publish_date'] <= NULL_DATE) || ($item['publish_date'] > gmstrftime('%Y-%m-%d %H:%M:%S')))
				$summary .= DRAFT_FLAG;

			// signal restricted and private articles
			if($item['active'] == 'N')
				$summary .= PRIVATE_FLAG.' ';
			elseif($item['active'] == 'R')
				$summary .= RESTRICTED_FLAG.' ';

			// indicate the id in the hovering popup
			$hover = i18n::s('View the page');
			if(Surfer::is_member())
				$hover .= ' [article='.$item['id'].']';

			// use the title to label the link
			if(is_object($overlay))
				$label = Codes::beautify_title($overlay->get_text('title', $item));
			else
				$label = Codes::beautify_title($item['title']);

			// use the title as a link to the page
			$summary .= Skin::build_link($url, $label, 'basic', $hover);

			// flag articles updated recently
			if(($item['expiry_date'] > NULL_DATE) && ($item['expiry_date'] <= $now))
				$summary .= EXPIRED_FLAG.' ';
			elseif($item['create_date'] >= $dead_line)
				$summary .= NEW_FLAG.' ';
			elseif($item['edit_date'] >= $dead_line)
				$summary .= UPDATED_FLAG.' ';

			// insert overlay data, if any
			if(is_object($overlay))
				$summary .= $overlay->get_text('list', $item);

			// attachment details
			$details = array();

			// signal locked articles
			if(isset($item['locked']) && ($item['locked'] == 'Y'))
				$details[] = LOCKED_FLAG;

			// info on related files
			if($count = Files::count_for_anchor('article:'.$item['id'], TRUE)) {
				Skin::define_img('FILES_LIST_IMG', 'files/list.gif');
				$details[] = FILES_LIST_IMG.sprintf(i18n::ns('%d file', '%d files', $count), $count);
			}

			// info on related links
			if($count = Links::count_for_anchor('article:'.$item['id'], TRUE)) {
				Skin::define_img('LINKS_LIST_IMG', 'links/list.gif');
				$details[] = LINKS_LIST_IMG.sprintf(i18n::ns('%d link', '%d links', $count), $count);
			}

			// comments
			if($count = Comments::count_for_anchor('article:'.$item['id'], TRUE)) {
				Skin::define_img('COMMENTS_LIST_IMG', 'comments/list.gif');
				$details[] = Skin::build_link(Comments::get_url('article:'.$item['id'], 'list'), COMMENTS_LIST_IMG.sprintf(i18n::ns('%d comment', '%d comments', $count), $count));
			}

			// combine in-line details
			if(count($details))
				$summary .= ' <span class="details">'.trim(implode(' ', $details)).'</span>';

			// display all tags
			if($item['tags'])
				$summary .= BR.'<span class="tags">'.Skin::build_tags($item['tags'], 'article:'.$item['id']).'</span>';

			// poster name
			if(isset($context['with_author_information']) && ($context['with_author_information'] == 'Y')) {
				if($item['create_name'])
					$author = Users::get_link($item['create_name'], $item['create_address'], $item['create_id']);
				else
					$author = Users::get_link($item['edit_name'], $item['edit_address'], $item['edit_id']);
			}

			// dates
			$update = '<span class="details">'.join(BR, Articles::build_dates($anchor, $item)).'</span>';

			// watcher
			if(Articles::is_watched($item['id'], $this->layout_variant))
				$watcher = CHECKED_IMG;

			// editor
			if(Articles::is_editable($anchor, $item, $this->layout_variant))
				$editor = CHECKED_IMG;

			// owner
			if(Articles::is_owned($anchor, $item, $this->layout_variant))
				$owner = CHECKED_IMG;

			// this is another row of the output
			$cells = array($summary, $update, $watcher, $editor, $owner);

			// append this row
			$rows[] = $cells;

		}

		// end of processing
		SQL::free($result);

		// headers
		$headers = array(i18n::s('Page'), i18n::s('Dates'), i18n::s('Watcher'), i18n::s('Editor'), i18n::s('Owner'));

		// return a sortable table
		$text .= Skin::table($headers, $rows, 'grid');
		return $text;
	}
}

?>