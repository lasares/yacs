<?php
/**
 * upload a new file or update an existing one
 *
 * @todo if an image is uploaded as a file, also compute and use the thumbnail automatically
 * @todo do not change date if silent is checked
 * @todo allow for a move to another anchor (Lucrecius)
 * @todo if extension is not allowed, and if associates, add a shortcut to configuration panel for files
 *
 * If no anchor has been provided to host the file, this script will create one.
 * The title given for the file, or the file name, will be used as the page title.
 * On direct uploads the sender will have the opportunity to select in which section
 * the article has to be created.
 * By default the article will be posted in the first public section appearing at the site map.
 *
 * Also, fully qualified href can be provided instead of real files, to create shadow entries
 * linked to material posted elsewhere.
 * For example, you can use these href to list within YACS files that are available at anonymous FTP servers.
 *
 * On new file upload, the href field is always stripped.
 *
 * This script accepts following situations:
 * - If a file has been actually uploaded, then search in the database a record for this name and for this anchor.
 * - If no record has been found and if an id has been provided, seek the database for the related record.
 * - If a valid record has been found, then update it.
 * - Else create a new record in the database.
 *
 * An alternate link can be added to any file record, to better support P2P software (eMule, etc.)
 *
 * This script attempts to validate the new or updated article description against a standard PHP XML parser.
 * The objective is to spot malformed or unordered HTML and XHTML tags. No more, no less.
 *
 * Restrictions apply on this page:
 * - uploads can have been administratively disallowed
 * - anonymous (not-logged) surfer are invited to register to be able to post new files
 * - members can post new files, and modify their files afterwards, if submissions are allowed
 * - members can modify files from others, if parameter users_without_file_overloads != 'Y'
 * - associates and editors can do what they want
 *
 * @see users/configure.php
 *
 * The active field is used to control the publication of uploaded files
 * - members uploads are flagged as being restricted; only other members can access them
 * - associates can publish member uploads by changing the active field
 * - associates uploads are flagged according to the input form
 *
 * If the configuration enables it, associates can select to upload public files
 * into some FTP space. In this case the active flag will take the value 'X'.
 * Here are the rules used to take into account the fact that a file can not be transferred
 * from the web to the ftp or vice-versa:
 * - on first upload, an associate can select between X, A, R or N
 * - else if X was used, stick on it
 * - else an associate can select between A, R or N
 *
 * A button-based editor is used for the description field.
 * It's aiming to introduce most common [link=codes]codes/index.php[/link] supported by YACS.
 *
 * Accepted calls:
 * - edit.php upload a file and create an article to host it
 * - edit.php/&lt;type&gt;/&lt;id&gt;			upload a new file for the anchor
 * - edit.php?anchor=&lt;type&gt;:&lt;id&gt;	upload a new file for the anchor
 * - edit.php/&lt;id&gt;					modify an existing file
 * - edit.php?id=&lt;id&gt; 			modify an existing file
 *
 * If the anchor for this item specifies a specific skin (option keyword '[code]skin_xyz[/code]'),
 * or a specific variant (option keyword '[code]variant_xyz[/code]'), they are used instead default values.
 *
 * @author Bernard Paques [email]bernard.paques@bigfoot.com[/email]
 * @author Vincent No&euml;l
 * @author GnapZ
 * @author Christophe Battarel [email]christophe.battarel@altairis.fr[/email]
 * @tester Natice
 * @tester Vincent Weber
 * @tester Manuel L�pez Gallego
 * @reference
 * @license http://www.gnu.org/copyleft/lesser.txt GNU Lesser General Public License
 */

// common definitions and initial processing
include_once '../shared/global.php';

// the maximum size for uploads
$file_maximum_size = str_replace('M', '000000', Safe::get_cfg_var('upload_max_filesize'));
if(!$file_maximum_size || ($file_maximum_size > 50000000))
	$file_maximum_size = 5000000;

// look for the id
$id = NULL;
if(isset($_REQUEST['id']))
	$id = $_REQUEST['id'];
elseif(isset($context['arguments'][0]))
	$id = $context['arguments'][0];
$id = strip_tags($id);

// get the item from the database
include_once 'files.php';
$item =& Files::get($id);

// get the related anchor, if any
$anchor = NULL;
if(isset($item['anchor']))
	$anchor = Anchors::get($item['anchor']);
elseif(isset($_REQUEST['anchor']))
	$anchor = Anchors::get($_REQUEST['anchor']);
elseif(isset($context['arguments'][1]))
	$anchor = Anchors::get($context['arguments'][0].':'.$context['arguments'][1]);

// editors have associate-like capabilities
if(is_object($anchor) && $anchor->is_editable())
	Surfer::empower();

// do not accept new files if uploads have been disallowed
if(!isset($item['id']) && !Surfer::may_upload())
	$permitted = FALSE;

// associates and editors can upload new files
elseif(!isset($item['id']) && Surfer::is_empowered())
	$permitted = TRUE;

// associates and authenticated editors can modify files
elseif(isset($item['id']) && Surfer::is_empowered())
	$permitted = TRUE;

// the anchor has to be viewable by this surfer
elseif(is_object($anchor) && !$anchor->is_viewable())
	$permitted = FALSE;

// authenticated members are allowed to modify files from others
elseif(isset($item['id']) && Surfer::is_member() && (!isset($context['users_without_file_overloads']) || ($context['users_without_file_overloads'] != 'Y')))
	$permitted = TRUE;

// surfer created the item
elseif(isset($item['create_id']) && ($item['create_id'] == Surfer::get_id()))
	$permitted = TRUE;

// authenticated members can add files to existing pages
elseif(Surfer::is_member() && is_object($anchor))
	$permitted = TRUE;

// authenticated members can post new items if submission is allowed
elseif(Surfer::is_member() && !isset($item['id']) && (!isset($context['users_without_submission']) || ($context['users_without_submission'] != 'Y')))
	$permitted = TRUE;

// the default is to disallow access
else
	$permitted = FALSE;

// do not always show the edition form
$with_form = FALSE;

// load localized strings
i18n::bind('files');

// load the skin, maybe with a variant
load_skin('files', $anchor);

// clear the tab we are in, if any
if(is_object($anchor))
	$context['current_focus'] = $anchor->get_focus();

// the path to this page
if(is_object($anchor) && $anchor->is_viewable())
	$context['path_bar'] = $anchor->get_path_bar();
else
	$context['path_bar'] = array( 'files/' => i18n::s('Files') );

// the title of the page
$context['page_title'] = i18n::s('Upload a file');

// always validate input syntax
if(isset($_REQUEST['description']))
	validate($_REQUEST['description']);

// permission denied
if(!$permitted) {

	// anonymous users are invited to log in or to register
	if(!Surfer::is_logged()) {

		if(isset($item['id']))
			$link = Files::get_url($item['id'], 'edit');
		elseif(isset($_REQUEST['anchor']))
			$link = 'files/edit.php?anchor='.urlencode(strip_tags($_REQUEST['anchor']));
		else
			$link = 'files/edit.php';

		Safe::redirect($context['url_to_home'].$context['url_to_root'].'users/login.php?url='.urlencode($link));
	}

	// permission denied to authenticated user
	Safe::header('Status: 403 Forbidden', TRUE, 403);
	Skin::error(i18n::s('You are not allowed to perform this operation.'));

// maybe posts are not allowed here
} elseif(is_object($anchor) && $anchor->has_option('locked') && !Surfer::is_associate()) {
	Safe::header('Status: 403 Forbidden', TRUE, 403);
	if(isset($item['id']))
		Skin::error(i18n::s('This page has been locked. It cannot be modified anymore.'));
	else
		Skin::error(i18n::s('Posts are not allowed here.'));

// extension is not allowed
} elseif(isset($_FILES['upload']['name']) && $_FILES['upload']['name'] && !Files::is_authorized($_FILES['upload']['name'])) {
	Safe::header('Status: 403 Forbidden', TRUE, 403);
	Skin::error(i18n::s('This type of file is not allowed.'));

// an error occured
} elseif(count($context['error'])) {
	$item = $_REQUEST;
	$with_form = TRUE;

// change editor
} elseif(isset($_REQUEST['preferred_editor']) && $_REQUEST['preferred_editor'] && ($_REQUEST['preferred_editor'] != $_SESSION['surfer_editor'])) {
	$_SESSION['surfer_editor'] = $_REQUEST['preferred_editor'];
	$item = $_REQUEST;
	$with_form = TRUE;

// process uploaded data
} elseif(isset($_SERVER['REQUEST_METHOD']) && ($_SERVER['REQUEST_METHOD'] == 'POST')) {

	// remember the previous version
	if(isset($item['id'])) {
		include_once '../versions/versions.php';
		Versions::save($item, 'file:'.$item['id']);
	}

	// a file has been uploaded
	if(isset($_FILES['upload']['name']) && $_FILES['upload']['name'] && ($_FILES['upload']['name'] != 'none')) {

		// access the temporary uploaded file
		$file_upload = $_FILES['upload']['tmp_name'];

		// $_FILES transcoding to utf8 is not automatic
		$_FILES['upload']['name'] = utf8::to_unicode($_FILES['upload']['name']);

		// enhance file name
		$file_name = $_FILES['upload']['name'];
		$file_extension = '';
		$position = strrpos($_FILES['upload']['name'], '.');
		if($position !== FALSE) {
			$file_name = substr($_FILES['upload']['name'], 0, $position);
			$file_extension = strtolower(substr($_FILES['upload']['name'], $position+1));
		}
		$_FILES['upload']['name'] = str_replace(array('.', '_', '%20'), ' ', $file_name);
		if($file_extension)
			$_FILES['upload']['name'] .= '.'.$file_extension;

		// ensure we have a file name
		$file_name = utf8::to_ascii($_FILES['upload']['name']);
		$_REQUEST['file_name'] = $file_name;

		// create an anchor if none has been provided
		if(!is_object($anchor)) {

			// set the title
			$fields['title'] = ucfirst(strip_tags($_REQUEST['title']));

			// most of time, it is more pertinent to move the description to the article itself
			$fields['description'] = $_REQUEST['description'];
			$_REQUEST['description'] = '';

			// use the provided section
			if($_REQUEST['section'])
				$fields['anchor'] = $_REQUEST['section'];

			// or select the default section
			else
				$fields['anchor'] = 'section:'.Sections::get_default();

			// create a hosting article for this file
			if($article_id = Articles::post($fields)) {
				$anchor = Anchors::get('article:'.$article_id);
				$_REQUEST['anchor'] = $anchor->get_reference();

				// purge section cache
				if($section = Anchors::get($fields['anchor']))
					$section->touch('article:create', $article_id, TRUE);
			}
			$fields = array();
		}

		// maybe this file has already been uploaded for this anchor
		if(isset($_REQUEST['anchor']) && ($match =& Files::get_by_anchor_and_name($_REQUEST['anchor'], $file_name))) {

			// if yes, switch to the matching record (and forget the record fetched previously, if any)
			$_REQUEST['id'] = $match['id'];
			$item = $match;
		}

		// uploads are not allowed
		if(!Surfer::may_upload())
			Skin::error(i18n::s('You are not allowed to perform this operation.'));

		// size exceeds php.ini settings -- UPLOAD_ERR_INI_SIZE
		elseif(isset($_FILES['upload']['error']) && ($_FILES['upload']['error'] == 1))
			Skin::error(i18n::s('The size of this file is over server limit (php.ini).'));

		// size exceeds form limit -- UPLOAD_ERR_FORM_SIZE
		elseif(isset($_FILES['upload']['error']) && ($_FILES['upload']['error'] == 2))
			Skin::error(i18n::s('The size of this file is over form limit.'));

		// partial transfer -- UPLOAD_ERR_PARTIAL
		elseif(isset($_FILES['upload']['error']) && ($_FILES['upload']['error'] == 3))
			Skin::error(i18n::s('File transfer has been interrupted.'));

		// no file -- UPLOAD_ERR_NO_FILE
		elseif(isset($_FILES['upload']['error']) && ($_FILES['upload']['error'] == 4))
			Skin::error(i18n::s('No file has been transferred.'));

		// zero bytes transmitted
		elseif(!$_FILES['upload']['size'])
			Skin::error(sprintf(i18n::s('It is likely file size goes beyond the limit displayed in upload form. Nothing has been transmitted for %s.'), $_FILES['upload']['name']));

		// an anchor is mandatory to put the file in the file system
		elseif(!is_object($anchor))
			Skin::error(i18n::s('No anchor has been found.'));

		// check provided upload name
		elseif(!Safe::is_uploaded_file($file_upload))
			Skin::error(i18n::s('Possible file attack.'));

		// put the file into the anonymous ftp space
		elseif(isset($_REQUEST['active']) && ($_REQUEST['active'] == 'X')) {
			Safe::load('parameters/files.include.php');

			// create folders
			$file_path = str_replace('//', '/', $context['files_path'].'/files');
			Safe::mkdir($file_path);
			$file_path .= '/'.str_replace(':', '/', $anchor->get_reference());
			Safe::mkdir(dirname($file_path));
			Safe::mkdir($file_path);
			$file_path .= '/';

			// move the uploaded file
			if(!Safe::move_uploaded_file($file_upload, $file_path.$file_name))
				Skin::error(sprintf(i18n::s('Impossible to move the upload file to %s.'), $file_path.$file_name));

			// this will be filtered by umask anyway
			else
				Safe::chmod($file_path.$file_name, $context['file_mask']);

		// put the file into the regular web space
		} else {

			// create folders
			$file_path = 'files/'.$context['virtual_path'].str_replace(':', '/', $anchor->get_reference());
			Safe::make_path($file_path);

			// make an absolute path
			$file_path = $context['path_to_root'].$file_path.'/';

			// move the uploaded file
			if(!Safe::move_uploaded_file($file_upload, $file_path.$file_name))
				Skin::error(sprintf(i18n::s('Impossible to move the upload file to %s.'), $file_path.$file_name));

			// this will be filtered by umask anyway
			else
				Safe::chmod($file_path.$file_name, $context['file_mask']);

		}

		// remember file size
		$_REQUEST['file_size'] = $_FILES['upload']['size'];

		// silently delete the previous file if the name has changed
		if($item['file_name'] && $file_name && ($item['file_name'] != $file_name) && isset($file_path))
			Safe::unlink($file_path.$item['file_name']);

		// we have a real file, not a reference
		$_REQUEST['file_href'] = '';

	// we are posting a reference
	} elseif(isset($_REQUEST['file_href']) && $_REQUEST['file_href']) {

		// protect from hackers
		$_REQUEST['file_href'] = preg_replace(FORBIDDEN_CHARS_IN_URLS, '_', $_REQUEST['file_href']);

		// ensure we have a title
		if(!$_REQUEST['title'])
			$_REQUEST['title'] = str_replace('%20', ' ', basename($_REQUEST['file_href']));

		// ensure we have a file name
		$_REQUEST['file_name'] = utf8::to_ascii(str_replace('%20', ' ', basename($_REQUEST['file_href'])));

		// create an anchor if none has been provided
		if(!is_object($anchor)) {

			// set the title
			$fields['title'] = ucfirst(strip_tags($_REQUEST['title']));

			// most of time, it is more pertinent to move the description to the article itself
			$fields['description'] = $_REQUEST['description'];
			$_REQUEST['description'] = '';

			// use the provided section, if any
			if($_REQUEST['section'])
				$fields['anchor'] = $_REQUEST['section'];

			// select the default section
			else
				$fields['anchor'] = 'section:'.Sections::get_default();

			// create a hosting article for this file
			if($article_id = Articles::post($fields)) {
				$anchor = Anchors::get('article:'.$article_id);
				$_REQUEST['anchor'] = $anchor->get_reference();

				// purge section cache
				if($section = Anchors::get($fields['anchor']))
					$section->touch('article:create', $article_id, TRUE);
			}
			$fields = array();
		}

	// nothing has been posted
	} elseif(!isset($_REQUEST['id'])) {
		Skin::error(i18n::s('No file has been transferred. Check maximum file size.'));

	}

	// make the file name searchable on initial post
	if(!isset($_REQUEST['id']) && isset($_REQUEST['file_name']))
		$_REQUEST['keywords'] .= ' '.str_replace(array('%20', '_', '.', '-'), ' ', $_REQUEST['file_name']);

	// limit access rights based on parent heritage
	if(is_object($anchor))
		$_REQUEST['active'] = $anchor->ceil_rights(isset($_REQUEST['active_set']) ? $_REQUEST['active_set'] : 'Y');

	// hook to index binary files

	// an error has already been encoutered
	if(count($context['error'])) {
		$item = $_REQUEST;

	// do not show the form, since browser may have not transmitted anchor information

	// update the record in the database
	} elseif(!$id = Files::post($_REQUEST)) {
		$item = $_REQUEST;
		$with_form = TRUE;

	// reward the poster for new posts
	} elseif(!isset($_REQUEST['id'])) {

		// always touch the related anchor on new posts
		$anchor->touch('file:create', $id);

		// increment the post counter of the surfer
		Users::increment_posts(Surfer::get_id());

		// thanks
		$context['page_title'] = i18n::s('Thank you very much for your contribution');

		// show file attributes
		$attributes = array();
		if($_REQUEST['file_name'])
			$attributes[] = $_REQUEST['file_name'];
		if($_REQUEST['file_size'])
			$attributes[] = $_REQUEST['file_size'].' bytes';
		if(is_array($attributes))
			$context['text'] .= '<p>'.implode(BR, $attributes)."</p>\n";

		// the action
		$context['text'] .= '<p>'.i18n::s('The upload has been successfully recorded.').'</p>';

		// splash message
		$context['text'] .= '<p>'.i18n::s('What do you want to do now?').'</p>';

		// follow-up commands -- do not use #files, because of thread layout, etc.
		$menu = array();
		if(is_object($anchor))
			$menu = array_merge($menu, array($anchor->get_url() => i18n::s('View the main page')));
		$menu = array_merge($menu, array(Files::get_url($id, 'view', $_REQUEST['file_name']) => i18n::s('Check the download page for this file')));
		if(Surfer::may_upload())
			$menu = array_merge($menu, array('images/edit.php?anchor='.urlencode('file:'.$id) => i18n::s('Add an image')));
		if(is_object($anchor) && Surfer::may_upload())
			$menu = array_merge($menu, array('files/edit.php?anchor='.$anchor->get_reference() => i18n::s('Upload another file')));
		$context['text'] .= Skin::build_list($menu, 'menu_bar');

		// log the submission of a new file by a non-associate
		if(!Surfer::is_associate() && is_object($anchor)) {
			$label = sprintf(i18n::c('New file in %s'), strip_tags($anchor->get_title()));
			$description = sprintf(i18n::c('%s at %s'), $_REQUEST['file_name'], $context['url_to_home'].$context['url_to_root'].Files::get_url($id));
			Logger::notify('files/edit.php', $label, $description);
		}

	// forward to the updated page
	} else {

		// increment the post counter of the surfer
		Users::increment_posts(Surfer::get_id());

		// touch the related anchor
		$anchor->touch('file:update', $_REQUEST['id'], isset($_REQUEST['silent']) && ($_REQUEST['silent'] == 'Y'));

		// forward to the anchor page -- do not use #files, because of thread layout, etc.
		Safe::redirect($context['url_to_home'].$context['url_to_root'].$anchor->get_url());

	}

// display the form on GET
} else
	$with_form = TRUE;

// display the form
if($with_form) {

	// file has been assigned
	if(isset($item['assign_id']) && $item['assign_id']) {

		// surfer is the owner
		if(Surfer::is_member() && (Surfer::get_id() == $item['assign_id'])) {
			$context['text'] .= Skin::build_block(sprintf(i18n::s('This file has been assigned to you %s, and you are encouraged to %s as soon as possible.'), Skin::build_date($item['assign_date']), i18n::s('upload an updated version')), 'note');

		// file has been assigned to another surfer
		} else {
			$context['text'] .= Skin::build_block(sprintf(i18n::s('This file has been assigned to %s %s, and it is likely that an updated version will be made available soon.'), Users::get_link($item['assign_name'], $item['assign_address'], $item['assign_id']), Skin::build_date($item['assign_date']))
				.' '.i18n::s('You are encouraged to wait for a fresher version to be available before moving forward.'), 'caution');
		}

	}

	// the form to edit a file
	$context['text'] .= '<form method="post" enctype="multipart/form-data" action="'.$context['script_url'].'" id="main_form"><div>';

	// this is a direct upload, we need to select a section
	if(!$anchor) {

		// a splash message for new users
		$context['text'] .= Skin::build_block(i18n::s('This script will create a brand new page for the uploaded file. If you would like to attach a file to an existing page, browse the target page instead and use the adequate command from the menu below page title.'), 'caution')."\n";

		$label = i18n::s('Section');
		$input = '<select name="section">'.Sections::get_options().'</select>';
		$hint = i18n::s('Please carefully select a section for your file');
		$fields[] = array($label, $input, $hint);
	}

	// reference the anchor page
	if(is_object($anchor) && $anchor->is_viewable())
		$context['text'] .= '<p>'.Skin::build_link($anchor->get_url(), $anchor->get_title(), 'basic')."</p>\n";

	// last edition
	if(isset($item['edit_id']) && $item['edit_id'])
		$context['text'] .= '<p>'.sprintf(i18n::s('Previous edition by %s %s'), Users::get_link($item['edit_name'], $item['edit_address'], $item['edit_id']), Skin::build_date($item['edit_date'])).'</p>'."\n";

	// the title
	$label = i18n::s('Title');
	$input = '<input type="text" name="title" size="45" value="'.encode_field(isset($item['title'])?$item['title']:'').'" maxlength="255" accesskey="t" />';
	$hint = i18n::s('Please type a meaningful and sortable title to help your peers');
	$fields[] = array($label, $input, $hint);

	// the file itself
	$label = i18n::s('File');
	$input = '';

	// a member is creating a new file entry
	if(!isset($item['id']) && Surfer::is_member()) {

		// several options to consider
		$input = '<dl>';

		// the upload entry requires rights to upload
		if(Surfer::may_upload()) {

			// an upload entry
			$input .= '<dt><input type="radio" name="file_type" value="upload" checked="checked" />'.i18n::s('Upload a file').'</dt>'
				.'<dd><input type="hidden" name="MAX_FILE_SIZE" value="'.$file_maximum_size.'" />'
				.'<input type="file" name="upload" id="upload" size="30" />';
			$size_hint = preg_replace('/000$/', 'k', preg_replace('/000000$/', 'M', $file_maximum_size));
			$input .= ' ('.sprintf(i18n::s('&lt;&nbsp;%s&nbsp;bytes'), $size_hint).')</dd>'."\n";

			// or
			$input .= '<dt>'.i18n::s('or').'</dt>';

		}

		// a reference
		$input .= '<dt><input type="radio" name="file_type" value="href" />'.i18n::s('Share an existing reference (ftp://, http://, ...)').'</dt>'
			.'<dd><input type="text" name="file_href" size="45" value="'.encode_field(isset($item['file_href'])?$item['file_href']:'').'" maxlength="255" />';
		$input .= BR.i18n::s('File size')
			.' <input type="text" name="file_size" size="12" value="'.encode_field(isset($item['file_size'])?$item['file_size']:'').'" maxlength="12" /> '.i18n::s('bytes')
			.'</dd>'."\n";

		$input .= '</dl>';

	// an anonymous surfer is creating a new file entry
	} elseif(!isset($item['id'])) {

		// the upload entry requires rights to upload
		if(Surfer::may_upload()) {

			// an upload entry
			$input = '<input type="hidden" name="file_type" value="upload" />'
				.'<input type="hidden" name="MAX_FILE_SIZE" value="'.$file_maximum_size.'" />'
				.'<input type="file" name="upload" id="upload" size="30" />';
			$size_hint = preg_replace('/000$/', 'k', preg_replace('/000000$/', 'M', $file_maximum_size));
			$input .= ' ('.sprintf(i18n::s('&lt;&nbsp;%s&nbsp;bytes'), $size_hint).')'."\n";

		}

	// update an existing entry
	} elseif(!isset($item['file_href']) || !$item['file_href']) {

		// file name
		if(isset($item['file_name']) && $item['file_name'])
			$input .= $item['file_name'].BR;

		// file uploader
		if(isset($item['create_name']))
			$input .= sprintf(i18n::s('posted by %s %s'), Users::get_link($item['create_name'], $item['create_address'], $item['create_id']), Skin::build_date($item['create_date'])).BR;

		$other_details = array();

		// file size
		if(isset($item['file_size']) && ($item['file_size'] > 1))
			$other_details[] = sprintf(i18n::s('%d bytes'), $item['file_size']);

		// hits
		if($item['hits'] > 1)
			$other_details[] = sprintf(i18n::s('%d downloads'), $item['hits']);

		if(count($other_details))
			$input .= join(', ', $other_details).BR;

		// the upload entry requires rights to upload
		if(Surfer::may_upload()) {

			// refresh the file
			$size_hint = preg_replace('/000$/', 'k', preg_replace('/000000$/', 'M', $file_maximum_size));
			$input .= '<input type="hidden" name="MAX_FILE_SIZE" value="'.$file_maximum_size.'" />'
				.'<input type="file" name="upload" id="upload" size="30" />'
				.' ('.sprintf(i18n::s('&lt;&nbsp;%s&nbsp;bytes'), $size_hint).')'."\n"
				.'<p class="tiny">'.i18n::s('Refresh from a local file').'</p>'."\n";

		}

	// we can refresh the reference
	} else {

		$input .= '<p class="tiny">'.i18n::s('Existing reference (ftp://, http://, ...)')
			.BR.'<input type="text" name="file_href" id="upload" size="45" value="'.encode_field($item['file_href']).'" maxlength="255" />';
		$input .= BR.i18n::s('File size')
			.' <input type="text" name="file_size" size="5" value="'.encode_field($item['file_size']).'" maxlength="12" /> '.i18n::s('bytes').'</p>'."\n";

	}

	// a complex field
	$fields[] = array($label, $input);

	// the description
	$label = i18n::s('Description');

	// use the editor if possible
	$input = Surfer::get_editor('description', isset($item['description'])?$item['description']:'');
	$fields[] = array($label, $input);

	// build the form
	$context['text'] .= Skin::build_form($fields);
	$fields = array();

	// the source
	$label = i18n::s('Source');
	$input = '<input type="text" name="source" size="45" value="'.encode_field(isset($item['source'])?$item['source']:'').'" maxlength="255" accesskey="u" />';
	$hint = i18n::s('If you have get this file from outside source, please describe it here');
	$fields[] = array($label, $input, $hint);

	// alternate href
	$label = i18n::s('Alternate link');
	$input = '<input type="text" name="alternate_href" size="45" value="'.encode_field(isset($item['alternate_href'])?$item['alternate_href']:'').'" maxlength="255" />';
	$hint = i18n::s('Paste here complicated peer-to-peer href (ed2k, torrent, etc.)');
	$fields[] = array($label, $input, $hint);

	// keywords
	$label = i18n::s('Keywords');
	$input = '<input type="text" name="keywords" size="45" value="'.encode_field(isset($item['keywords'])?$item['keywords']:'').'" maxlength="255" accesskey="o" />';
	$hint = i18n::s('As this field may be searched by surfers, please choose adequate searchable words');
	$fields[] = array($label, $input, $hint);

	// associates may change the active flag: eXternal/public, Yes/public, Restricted/logged, No/associates --we don't care about inheritance, to enable security changes afterwards
	if(Surfer::is_empowered() && Surfer::is_member()) {
		$label = i18n::s('Visibility');
		$input = '';
		Safe::load('parameters/files.include.php');

		// from a public ftp site
		if(isset($item['active_set']) && (($item['active_set'] == 'X') || ((!isset($item['id'])) && isset($context['files_on_ftp']) && ($context['files_on_ftp'] == 'Y')))) {
			$input .= '<input type="radio" name="active_set" value="X"';
			if($item['active_set'] == 'X')
				$input .= ' checked="checked"';
			$input .= EOT.' '.i18n::s('File can be downloaded from the anonymous ftp service').BR;
		}

		// or from this server
		if(!isset($item['active_set']) || ($item['active_set'] != 'X')) {
			$input .= '<input type="radio" name="active_set" value="Y" accesskey="v"';
			if(!isset($item['active_set']) || ($item['active_set'] == 'Y'))
				$input .= ' checked="checked"';
			$input .= EOT.' '.i18n::s('Anyone may download this file')
				.BR.'<input type="radio" name="active_set" value="R"';
			if(isset($item['active_set']) && ($item['active_set'] == 'R'))
				$input .= ' checked="checked"';
			$input .= EOT.' '.i18n::s('Access is restricted to authenticated members')
				.BR.'<input type="radio" name="active_set" value="N"';
			if(isset($item['active_set']) && ($item['active_set'] == 'N'))
				$input .= ' checked="checked"';
			$input .= EOT.' '.i18n::s('Access is restricted to associates and editors')."\n";
		}
		$fields[] = array($label, $input);
	}

	// the icon url may be set after the page has been created
	if(isset($item['id']) && Surfer::is_empowered() && Surfer::is_member()) {
		$label = i18n::s('Icon URL');
		$value = '';
		if(isset($item['icon_url']) && $item['icon_url'])
			$value = $item['icon_url'];
		$input = '<input type="text" name="icon_url" size="55" value="'.encode_field($value).'" maxlength="255" />';
		$hint = i18n::s('You can click on the Set as icon link in the list of images below, if any');
		$fields[] = array($label, $input, $hint);
	}

	// the thumbnail url may be set after the page has been created
	if(isset($item['id']) && Surfer::is_empowered() && Surfer::is_member()) {
		$label = i18n::s('Thumbnail URL');
		$input = '<input type="text" name="thumbnail_url" size="55" value="'.encode_field(isset($item['thumbnail_url']) ? $item['thumbnail_url'] : '').'" maxlength="255" />';
		$hint = i18n::s('You can click on the Set as thumbnail link in the list of images below, if any');
		$fields[] = array($label, $input, $hint);
	}

	// add a folded box
	$context['text'] .= Skin::build_box(i18n::s('Advanced options'), Skin::build_form($fields), 'folder');
	$fields = array();

	// associates may decide to not stamp changes, but only for changes
	if(isset($item['id']) && Surfer::is_associate() && isset($anchor)) {
		$context['text'] .= '<p><input type="checkbox" name="silent" value="Y" /> '.i18n::s('Do not change modification date of the related page.').'</p>';
	}

	// the submit button
	$context['text'] .= '<p>'.Skin::build_submit_button(i18n::s('Submit'), i18n::s('Press [s] to submit data'), 's').'</p>'."\n";

	// transmit the id as a hidden field
	if(isset($item['id']) && $item['id'])
		$context['text'] .= '<input type="hidden" name="id" value="'.$item['id'].'" />';

	// other hidden fields
	if(is_object($anchor))
		$context['text'] .= '<input type="hidden" name="anchor" value="'.$anchor->get_reference().'" />';

	// end of the form
	$context['text'] .= '</div></form>';

	// the script used for form handling at the browser
	$context['text'] .= '<script type="text/javascript">// <![CDATA['."\n"
		.'// set the focus on first form field'."\n"
		.'document.getElementById("upload").focus();'."\n"
		.'// ]]></script>'."\n";

	// general help on this form
	$help = '<p>'.i18n::s('Please set a meaningful title to be used instead of the file name in lists of files.').'</p>'
		.'<p>'.i18n::s('Also, take the time to describe your post. This field is fully indexed for searches.').'</p>'
		.'<p>'.sprintf(i18n::s('%s and %s are available to beautify your post.'), Skin::build_link('codes/', i18n::s('YACS codes'), 'help'), Skin::build_link('smileys/', i18n::s('smileys'), 'help')).'</p>'
		.'<p>'.i18n::s('Lastly, indicate the original source of the file if you know it, either with a name or, better, with a web address.').'</p>';
	$context['extra'] .= Skin::build_box(i18n::s('Help'), $help, 'navigation', 'help');

	// if we are editing an existing item
	if(isset($item['id'])) {

		// related images
		$context['text'] .= Skin::build_block(i18n::s('Images'), 'title');

		// the menu to post a new image
		if(Surfer::may_upload()) {
			$menu = array( 'images/edit.php?anchor='.urlencode('file:'.$item['id']) => i18n::s('Add an image') );
			$context['text'] .= Skin::build_list($menu, 'menu_bar');
		}

		// the list of images
		include_once '../images/images.php';
		if($items = Images::list_by_date_for_anchor('file:'.$item['id'], 0, 50, NULL)) {
			$context['text'] .= '<p>'.i18n::s('Click on links to insert images in the main field.')."</p>\n";
			$context['text'] .= Skin::build_list($items, 'decorated');
		}
	}

}

// render the skin
render_skin();

?>