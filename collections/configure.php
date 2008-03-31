<?php
/**
 * define collections and related parameters
 *
 * Use this script to modify following parameters:
 *
 * - [code]collection_names[][/code] - list of names
 *
 * - [code]collection_titles[][/code] - list of titles
 *
 * - [code]collection_paths[][/code] - list of paths to files (used internally to access files)
 *
 * - [code]collection_urls[][/code] - list of URLs to files (to be used by browsers to fetch files)
 *
 * - [code]collection_introductions[][/code] - list of introduction blocks of text (used in collection lists)
 *
 * - [code]collection_descriptions[][/code] - list of description blocks of text (used at collection index pages)
 *
 * - [code]collection_prefixes[][/code] - list of prefix blocks of text (inserted at each collection page)
 *
 * - [code]collection_suffixes[][/code] - list of suffix blocks of text (inserted at each collection page)
 *
 * - [code]collection_visibilities[][/code] - to control access to the collection
 *
 * Configuration information is saved into [code]parameters/collections.include.php[/code].
 * If YACS is prevented to write to the file, it displays parameters to allow for a manual update.
 *
 * The file [code]parameters/collections.include.php.bak[/code] can be used to restore
 * the active configuration before the last change.
 *
 * If the file [code]demo.flag[/code] exists, the script assumes that this instance
 * of YACS runs in demonstration mode.
 * In this mode the edit form is displayed, but parameters are not saved in the configuration file.
 *
 * @author Bernard Paques [email]bernard.paques@bigfoot.com[/email]
 * @author GnapZ
 * @tester Ghjmora
 * @reference
 * @license http://www.gnu.org/copyleft/lesser.txt GNU Lesser General Public License
 */

// common definitions and initial processing
include_once '../shared/global.php';

// load localized strings
i18n::bind('collections');

// load the skin
load_skin('collections');

// the path to this page
$context['path_bar'] = array( 'control/' => i18n::s('Control Panel') );

// the title of the page
$context['page_title'] = i18n::s('The configuration panel for collections');

// the back button
$context['page_menu'] = array_merge($context['page_menu'], array( 'collections/' => i18n::s('All collections') ));

// do it again
if(isset($_SERVER['REQUEST_METHOD']) && ($_SERVER['REQUEST_METHOD'] == 'POST'))
	$context['page_menu'] = array_merge($context['page_menu'], array( 'collections/configure.php' => i18n::s('Edit') ));

// anonymous users are invited to log in or to register
if(!Surfer::is_logged())
	Safe::redirect($context['url_to_home'].$context['url_to_root'].'users/login.php?url='.urlencode('collections/configure.php'));

// only associates can proceed
elseif(!Surfer::is_associate()) {
	Safe::header('Status: 403 Forbidden', TRUE, 403);
	Skin::error(i18n::s('You are not allowed to perform this operation.'));

// display the input form
} elseif(isset($_SERVER['REQUEST_METHOD']) && ($_SERVER['REQUEST_METHOD'] != 'POST')) {

	// load current parameters, if any
	Safe::load('parameters/collections.include.php');

	// the form
	$context['text'] .= '<form method="post" action="'.$context['script_url'].'" id="main_form"><div>';

	// the splash message
	$context['text'] .= '<p>'.i18n::s('Below is the list of shared directories of files.')."</p>\n";

	// collections list
	$context['text'] .= Skin::build_block(i18n::s('Collections'), 'title');

	// list existing collections
	if(isset($context['collections']) && is_array($context['collections'])) {

		foreach($context['collections'] as $name => $attributes) {

			list($title, $path, $url, $introduction, $description, $prefix, $suffix, $visibility) = $attributes;

			$label = i18n::s('Collection nick name');
			$input = '<input type="text" name="collection_names['.$name.']" size="32" value="'.encode_field($name).'" maxlength="32" />';
			$hint = i18n::s('Delete to suppress');
			$fields[] = array($label, $input, $hint);

			$label = i18n::s('Visibility');
			$input = '<input type="radio" name="collection_visibilities['.$name.']" value="Y"';
			if(!isset($visibility) || !$visibility || ($visibility == 'Y'))
				$input .= ' checked="checked"';
			$input .= EOT.' '.i18n::s('Anyone may browse this collection');
			$input .= BR.'<input type="radio" name="collection_visibilities['.$name.']" value="R"';
			if(isset($visibility) && ($visibility == 'R'))
				$input .= ' checked="checked"';
			$input .= EOT.' '.i18n::s('Access is restricted to authenticated members');
			$input .= BR.'<input type="radio" name="collection_visibilities['.$name.']" value="N"';
			if(isset($visibility) && ($visibility == 'N'))
				$input .= ' checked="checked"';
			$input .= EOT.' '.i18n::s('Access is restricted to associates');
			$fields[] = array($label, $input);

			$label = i18n::s('Label');
			$input = '<input type="text" name="collection_titles['.$name.']" size="45" value="'.encode_field($title).'" maxlength="255" />';
			$hint = i18n::s('Please provide a meaningful title.');
			$fields[] = array($label, $input, $hint);

			$label = i18n::s('Path prefix');
			$input = '<input type="text" name="collection_paths['.$name.']" size="45" value="'.encode_field($path).'" maxlength="255" />';
			$hint = sprintf(i18n::s('Local access to files; YACS installation directory is at "%s"'), $context['path_to_root']);
			$fields[] = array($label, $input, $hint);

			$label = i18n::s('URL prefix');
			$input = '<input type="text" name="collection_urls['.$name.']" size="45" value="'.encode_field($url).'" maxlength="255" />';
			$hint = i18n::s('The ftp:// or http:// address used to access the collection');
			$fields[] = array($label, $input, $hint);

			$label = i18n::s('Introduction');
			$input = '<textarea name="collection_introductions['.$name.']" cols="40" rows="2">'.encode_field($introduction).'</textarea>';
			$hint = i18n::s('To be used at the front page and on the collections index page');
			$fields[] = array($label, $input, $hint);

			$label = i18n::s('Description');
			$input = '<textarea name="collection_descriptions['.$name.']" cols="40" rows="3">'.encode_field($description).'</textarea>';
			$hint = i18n::s('To be inserted on the index page of this collection');
			$fields[] = array($label, $input, $hint);

			$label = i18n::s('Prefix');
			$input = '<textarea name="collection_prefixes['.$name.']" cols="40" rows="2">'.encode_field($prefix).'</textarea>';
			$hint = i18n::s('Inserted on top of pages for this collection');
			$fields[] = array($label, $input, $hint);

			$label = i18n::s('Suffix');
			$input = '<textarea name="collection_suffixes['.$name.']" cols="40" rows="2">'.encode_field($suffix).'</textarea>';
			$hint = i18n::s('Inserted at bottom of pages for this collection');
			$fields[] = array($label, $input, $hint);

			// add a folded box
			$context['text'] .= Skin::build_box(sprintf(i18n::s('Collection: %s'), ($title?$title:$name)), Skin::build_form($fields), 'folder');
			$fields = array();

		}
	}

	// append one remote collection
	$label = i18n::s('Collection nick name');
	$input = '<input type="text" name="collection_names[]" size="32" maxlength="32" />';
	$hint = i18n::s('Use a short nick name');
	$fields[] = array($label, $input, $hint);

	$label = i18n::s('Visibility');
	$input = '<input type="radio" name="collection_visibilities[]" value="Y" checked="checked"';
	$input .= EOT.' '.i18n::s('Anyone may browse this collection');
	$input .= BR.'<input type="radio" name="collection_visibilities[]" value="R"';
	$input .= EOT.' '.i18n::s('Access is restricted to authenticated members');
	$input .= BR.'<input type="radio" name="collection_visibilities[]" value="N"';
	$input .= EOT.' '.i18n::s('Access is restricted to associates');
	$fields[] = array($label, $input);

	$label = i18n::s('Label');
	$input = '<input type="text" name="collection_titles[]" size="45" maxlength="255" />';
	$hint = i18n::s('Please provide a meaningful title.');
	$fields[] = array($label, $input, $hint);

	$label = i18n::s('Path prefix');
	$input = '<input type="text" name="collection_paths[]" size="45" maxlength="255" />';
	$hint = sprintf(i18n::s('Local access to files; YACS installation directory is at "%s"'), $context['path_to_root']);
	$fields[] = array($label, $input, $hint);

	$label = i18n::s('URL prefix');
	$input = '<input type="text" name="collection_urls[]" size="45" maxlength="255" />';
	$hint = i18n::s('The ftp:// or http:// address used to access the collection');
	$fields[] = array($label, $input, $hint);

	$label = i18n::s('Introduction');
	$input = '<textarea name="collection_introductions[]" cols="40" rows="2"></textarea>';
	$hint = i18n::s('To be used at the front page and on the collections index page');
	$fields[] = array($label, $input, $hint);

	$label = i18n::s('Description');
	$input = '<textarea name="collection_descriptions[]" cols="40" rows="3"></textarea>';
	$hint = i18n::s('To be inserted on the index page of this collection');
	$fields[] = array($label, $input, $hint);

	$label = i18n::s('Prefix');
	$input = '<textarea name="collection_prefixes[]" cols="40" rows="2"></textarea>';
	$hint = i18n::s('Inserted on top of pages for this collection');
	$fields[] = array($label, $input, $hint);

	$label = i18n::s('Suffix');
	$input = '<textarea name="collection_suffixes[]" cols="40" rows="2"></textarea>';
	$hint = i18n::s('Inserted at bottom of pages for this collection');
	$fields[] = array($label, $input, $hint);

	// add a folded box
	$context['text'] .= Skin::build_box(i18n::s('Add a collection'), Skin::build_form($fields), 'folder');
	$fields = array();

	// the submit button
	$context['text'] .= Skin::build_box(i18n::s('Save parameters'), '<p>'.Skin::build_submit_button(i18n::s('Submit'), i18n::s('Press [s] to submit data'), 's').'</p>'."\n", 'section');

	// end of the form
	$context['text'] .= '</div></form>';

	// general help on this form
	$help = '<p>'.i18n::s('The Collection Nick Name is prepended to the path of each file of the collection. Therefore it has to be as short (typically, one word) and as meaningful as possible.').'</p>'
		.'<p>'.i18n::s('The Label is the actual title for this collection. It is used as a page title on the collection index page, and in the path list at the top of each subsequent page for the collection.').'</p>'
		.'<p>'.i18n::s('The Path Prefix is prepended by the server to access every directory and file of the collection. You should indicate here the location of the collection in the local file system.').'</p>'
		.'<p>'.i18n::s('The URL Prefix is inserted in links used to download files of the collection. You should indicate here the access method (e.g., ftp) and path used to access files remotely, from a web browser.').'</p>'
		.'<p>'.i18n::s('The Introduction is used only on the main index page of all collections, while the Description is inserted in the index page of this collection.').'</p>'
		.'<p>'.i18n::s('The Prefix and Suffix are inserted respectively at the beginning and end of each page of the collection, except the index.').'</p>';
	$context['extra'] .= Skin::build_box(i18n::s('Help'), $help, 'navigation', 'help');

// no modifications in demo mode
} elseif(file_exists($context['path_to_root'].'parameters/demo.flag')) {

	// remind the surfer
	$context['text'] .= '<p>'.i18n::s('This instance of YACS runs in demonstration mode. For security reasons configuration parameters cannot be changed in this mode.').'</p>';

// save updated parameters
} else {

	// backup the old version
	Safe::unlink($context['path_to_root'].'parameters/collections.include.php.bak');
	Safe::rename($context['path_to_root'].'parameters/collections.include.php', $context['path_to_root'].'parameters/collections.include.php.bak');

	// build the new configuration file
	$content = '<?php'."\n"
		.'// This file has been created by the configuration script collections/configure.php'."\n"
		.'// on '.gmdate("F j, Y, g:i a").' GMT, for '.Surfer::get_name().'. Please do not modify it manually.'."\n"
		.'global $context;'."\n";
	foreach($_REQUEST['collection_urls'] as $index => $value) {
		$name	= addcslashes($_REQUEST['collection_names'][$index], "\\'");
		$title	= addcslashes($_REQUEST['collection_titles'][$index], "\\'");
		$path	= addcslashes($_REQUEST['collection_paths'][$index], "\\'");
		$url	= addcslashes($_REQUEST['collection_urls'][$index], "\\'");
		$introduction	= addcslashes($_REQUEST['collection_introductions'][$index], "\\'");
		$description	= addcslashes($_REQUEST['collection_descriptions'][$index], "\\'");
		$prefix = addcslashes($_REQUEST['collection_prefixes'][$index], "\\'");
		$suffix = addcslashes($_REQUEST['collection_suffixes'][$index], "\\'");
		$visibility = addcslashes($_REQUEST['collection_visibilities'][$index], "\\'");
		if($name && $path && $url) {
			$content .= '$context[\'collections\'][\''.$name.'\']=array(\''.$title.'\', \''
				.$path.'\', \''.$url.'\', \''
				.$introduction.'\', \''.$description.'\', \''
				.$prefix.'\', \''.$suffix.'\', \''.$visibility."');\n";
		}
	}
	$content .= '?>'."\n";

	// update the parameters file
	if(!Safe::file_put_contents('parameters/collections.include.php', $content)) {

		Skin::error(sprintf(i18n::s('ERROR: Impossible to write to the file %s. The configuration has not been saved.'), 'parameters/collections.include.php'));

		// allow for a manual update
		$context['text'] .= '<p style="text-decoration: blink;">'.sprintf(i18n::s('To actually change the configuration, please copy and paste following lines by yourself in file %s.'), 'parameters/collections.include.php')."</p>\n";

	// job done
	} else {

		$context['text'] .= '<p>'.sprintf(i18n::s('The following configuration has been saved into the file %s.'), 'parameters/collections.include.php')."</p>\n";

		// purge the cache
		Cache::clear();

		// remember the change
		$label = sprintf(i18n::c('%s has been updated'), 'parameters/collections.include.php');
		$description = $context['url_to_home'].$context['url_to_root'].'collections/configure.php';
		Logger::remember('collections/configure.php', $label, $description);

	}

	// display updated parameters
	$context['text'] .= Skin::build_box(i18n::s('Configuration parameters'), Safe::highlight_string($content), 'folder');

	// what's next?
	$context['text'] .= '<p>'.i18n::s('What do you want to do now?')."</p>\n";

	// follow-up commands
	$menu = array();

	// offer to change it again
	$menu = array_merge($menu, array( 'collections/configure.php' => i18n::s('Configure again') ));

	// back to the control panel
	$menu = array_merge($menu, array( 'control/' => i18n::s('Go to the Control Panel') ));

	// display follow-up commands
	$context['text'] .= Skin::build_list($menu, 'menu_bar');

}

// render the skin
render_skin();

?>