<?php
/**********************************************************************************
* CustomBBCodeAdmin.php - PHP implementation of the Custom BBCode Manager mod
***********************************************************************************
* This mod is licensed under the 2-clause BSD License, which can be found here:
*	http://opensource.org/licenses/BSD-2-Clause
***********************************************************************************
* This program is distributed in the hope that it is and will be useful, but      *
* WITHOUT ANY WARRANTIES; without even any implied warranty of MERCHANTABILITY    *
* or FITNESS FOR A PARTICULAR PURPOSE.                                            *
**********************************************************************************/
if (!defined('SMF'))
	die('Hacking attempt...');

/**********************************************************************************
* CustomBBCode Admin hooks:
**********************************************************************************/
function CustomBBCodes_Admin(&$admin_areas)
{
	global $txt, $context;

	loadLanguage('CustomBBCodesAdmin');
	$custombbc = array($txt['CustomBBCode_List_Title'], 'manage_custom_bbcodes');
	if (isset($admin_areas['config']['areas']['featuresettings']['subsections']['bbc']))
		$admin_areas['config']['areas']['featuresettings']['subsections']['custombbc'] = $custombbc;
	else
		$admin_areas['layout']['areas']['postsettings']['subsections']['custombbc'] = $custombbc;
}

function CustomBBCodes_Modify_Features(&$subActions)
{
	$subActions['custombbc'] = 'CustomBBCodes_Controller';
}

function CustomBBCodes_Permissions(&$permissionGroups, &$permissionList, &$leftPermissionGroups, &$hiddenPermissions, &$relabelPermissions)
{
	global $context;
	$permissionList['membergroup']['manage_custom_bbcodes'] = array(false, 'maintenance', 'administrate');
	if (!allowedTo('manage_custom_bbcodes'))
		$context['illegal_permissions'][] = 'manage_custom_bbcodes';
	$context['non_guest_permissions'][] = 'manage_custom_bbcodes';
}

/**********************************************************************************
* CustomBBCode admin controller functions:
**********************************************************************************/
function CustomBBCodes_Controller()
{
	global $txt, $context, $sourcedir;

	// Load some basic stuff related to both the CustomBBCode edit and browse functions:
	isAllowedTo('manage_custom_bbcodes');
	loadTemplate('CustomBBCodesAdmin');
	require_once($sourcedir . '/Subs-CustomBBCodesAdmin.php');

	// Decide down what path to send the user:
	$subActions = array(
		// ======= Main UI functions: =======
		'browse' => 'CustomBBCodes_Browse',
		'newtag' => 'CustomBBCodes_Edit',
		'edit' => 'CustomBBCodes_Edit',
		'delete' => 'CustomBBCodes_Delete',
		'enable' => 'CustomBBCode_Enable',
		'disable' => 'CustomBBCode_Disable',
		'save' => 'CustomBBCode_Save',
		// ========= AJAX functions: =========
		'bbc_exists' => 'CustomBBCode_AJAX_Exists',
		'bbc_upload' => 'CustomBBCode_AJAX_Upload',
		'bbc_remove' => 'CustomBBCode_AJAX_Remove',
	);
	$_REQUEST['activity'] = isset($_REQUEST['activity']) && isset($subActions[$_REQUEST['activity']]) ? $_REQUEST['activity'] : 'browse';
	$tag = strtolower((isset($_REQUEST['tag']) ? $_REQUEST['tag'] : -1));
	$subActions[$_REQUEST['activity']]($tag);
}

/**********************************************************************************
* CustomBBCode admin helper functions:
**********************************************************************************/
function CustomBBCodes_Browse($tag)
{
	global $modSettings, $txt, $scripturl, $sourcedir, $context;
	isAllowedTo('manage_custom_bbcodes');

	// Get latest version of the mod and display whether current mod is up-to-date:
	if (($file = cache_get_data('ila_mod_version', 86400)) == null)
	{
		$file = @file_get_contents('http://www.xptsp.com/tools/mod_version.php?url=Custom_BBCodes_Manager');
		cache_put_data('ila_mod_version', $file, 86400);
	}
	if (preg_match('#Custom_BBCodes_Manager_v(.+?)\.zip#i', $file, $version))
	{
		if (isset($modSettings['ila_version']) && $version[1] > $modSettings['ila_version'])
			$context['settings_message'] = '<strong>' . sprintf($txt['bbc_no_update'], $version[1]) . '</strong>';
		else
			$context['settings_message'] = '<strong>' . $txt['bbc_no_update'] . '</strong>';
	}

	// Build the array required for "createList" function:
	$list_options = array(
		'id' => 'list_bbc',
		'title' => $txt['CustomBBCode_List_Title'],
		'items_per_page' => 30,
		'base_href' => $scripturl . '?action=admin;area=' . $_GET['area'] . ';sa=custombbc',
		'default_sort_col' => 'tag',
		'get_items' => array(
			'function' => 'CBBC_get_data',
		),
		'get_count' => array(
			'function' => 'CBBC_get_count',
		),
		'no_items_label' => $txt['List_no_tags'],
		'columns' => array(
			'button' => array(
				'header' => array(
					'value' => $txt['List_button'],
				),
				'data' => array(
					'db' => 'button',
					'style' => 'width: 8%;',
				),
			),
			'tag' => array(
				'header' => array(
					'value' => $txt['List_tag'],
				),
				'data' => array(
					'db' => 'tag',
					'style' => 'width: 15%;',
				),
				'sort' =>  array(
					'default' => 'tag',
					'reverse' => 'tag DESC',
				),
			),
			'ctype' => array(
				'header' => array(
					'value' => $txt['Edit_type'],
				),
				'data' => array(
					'db' => 'ctype',
					'style' => 'width: 15%;',
				),
				'sort' =>  array(
					'default' => 'ctype',
					'reverse' => 'ctype DESC',
				),
			),
			'form' => array(
				'header' => array(
					'value' => $txt['List_name'],
				),
				'data' => array(
					'db' => 'form',
					'style' => 'width: 44%;',
				),
			),
			'status' => array(
				'header' => array(
					'value' => $txt['List_Status'],
				),
				'data' => array(
					'function' => 'CBBC_get_enabled',
					'style' => 'width: 7%; text-align: center;',
				),
				'sort' =>  array(
					'default' => 'enabled',
					'reverse' => 'enabled DESC',
				),
			),
			'actions' => array(
				'header' => array(
					'value' => $txt['List_actions'],
				),
				'data' => array(
					'function' => 'CBBC_get_actions',
					'style' => 'width: 13%; text-align: center;',
				),
			),
		),
	);

	// Let's build the list now:
	$context['page_title'] = $txt['CustomBBCode_List_Title'];
	$context[$context['admin_menu_name']]['tab_data']['tabs']['custombbc'] = array(
		'description' => $txt['List_Header_Desc'],
	);
	$context['sub_template'] = 'CustomBBCode_Browse';
	require_once($sourcedir . '/Subs-List.php');
	createList($list_options);
}

function CustomBBCodes_Edit($tag, $err = false)
{
	global $txt, $scripturl, $context, $smcFunc, $settings, $modSettings;
	global $forum_version, $sourcedir, $settings, $boardurl;

	// Make sure that we are allowed to do this thing:
	isAllowedTo('manage_custom_bbcodes');
	checkSession('request');

	// Get the tag from the database.  If no tag, set the fields to default...
	if ($tag != -1)
		$row = CBBC_get_row($tag);
	if (empty($row))
		$row = array(
			'id' => -1,
			'enabled' => 1,
			'button' => 0,
			'tag' => '',
			'description' => '',
			'block_level' => 0,
			'trim' => 'none',
			'ctype' => 'parsed_content',
			'before' => '',
			'after' => '',
			'content' => '',
			'accept_urls' => 0,
		);
	$row['last_update'] = (empty($row['last_update']) ? time() : $row['last_update']);

	// Let's put the entire HTML replacement code back together so the user understands it better:
	$row['type'] = $row['ctype'];
	switch($row['ctype'])
	{
		case 'closed':
			$row['html'] = $row['content'];
			break;

		case 'unparsed_content':
			 $row['html'] = str_replace('$1', '{content}', $row['content']);
			break;

		case 'unparsed_commas_content':
		case 'unparsed_equals_content':
			$search = array('$1', '$2', '$3', '$4', '$5', '$6', '$7', '$8', '$9');
			$replace = array('{content}', '{option1}', '{option2}', '{option3}', '{option4}', '{option5}', '{option6}', '{option7}', '{option8}');
			$row['html'] = str_replace($search, $replace, $row['content']);
			break;

		case 'parsed_equals':
		case 'unparsed_equals':
		case 'parsed_content':
		case 'unparsed_commas':
		default:
			$search = array('$1', '$2', '$3', '$4', '$5', '$6', '$7', '$8');
			$replace = array('{option1}', '{option2}', '{option3}', '{option4}', '{option5}', '{option6}', '{option7}', '{option8}');
			$row['html'] = str_replace($search, $replace, $row['before']) . '{content}' . str_replace($search, $replace, $row['after']);
			break;
	}

	// Let's get the path to the button image used by the editor:
	$row['image'] = CBBC_get_button_URL($row['tag']);
	$row['url_exists'] = file_exists( CBBC_get_button_path($row['tag']) );

	// Temporarily transfer the entire row to the $modSettings array:
	foreach ($row as $id => $element)
		$modSettings['Edit_' . $id] = $element;
	$modSettings['Edit_name'] = $row['tag'];
	$modSettings['Edit_show_button'] = $row['button'];

	// Let's get all the Javascript that we need for the admin page:
	CBBC_javascript($row);

	// Assemble the options available in this mod:
	$config_vars = array(
		array('text', 'Edit_name', 'javascript' => ' onchange="AlreadyExists();"', 'postinput' => $context['CBBC_Javascript']),
		array('text', 'Edit_description', 'size' => 60),
		'',
		'a' => array('select', 'Edit_type', 'javascript' => ' onchange="Process();"', array(
			'closed' => $txt['closed'],
			'parsed_content' => $txt['parsed_content'],
			'unparsed_content' => $txt['unparsed_content'],
			'parsed_equals' => $txt['parsed_equals'],
			'unparsed_equals' => $txt['unparsed_equals'],
			'unparsed_commas' => $txt['unparsed_commas'],
			'unparsed_commas_content' => $txt['unparsed_commas_content'],
			'unparsed_equals_content' => $txt['unparsed_equals_content'],
		)),
		'b' => array('text', 'Edit_tag_format', 'size' => 60, 'javascript' => ' disabled="disabled"'),
		array('large_text', 'Edit_html', 'force_div_id' => 'htmlDiv'),
		920 => '',
		'c' => array('check', 'Edit_block_level'),
		'd' => array('select', 'Edit_trim', array(
			'none' => $txt['Edit_trim_no'],
			'inside' => $txt['Edit_trim_in'],
			'outside' => $txt['Edit_trim_out'],
			'both' => $txt['Edit_trim_both'],
		)),
		'e' => array('check', 'Edit_accept_urls'),
		//'',	<== This is an empty space!  Do not remove!
		'f' => array('title', 'Edit_button'),
		'g' => array('callback', 'button_upload'),
		'h' => array('check', 'Edit_show_button', 'force_div_id' => 'show_button'),
	);

	// Let's remove most of the fields for new simple bbcodes:
	if (isset($_REQUEST['simple']))
	{
		foreach ($config_vars as $id => $field)
			if (!is_int($id))
				unset($config_vars[$id]);
		unset($config_vars[ substr($forum_version, 0, 7) == 'SMF 2.0' ? 920 : -1]);
	}

	// Load required stuff in order to make this work right:
	require_once($sourcedir . '/ManagePermissions.php');
	require_once($sourcedir . '/ManageServer.php');

	// Display the template with the information about the requested tag:
	$context['sub_template'] = 'show_settings';
	$context['settings_title'] = $txt['Edit_title'];
	$context['post_url'] = $context['CBBC_xml'] . ';activity=save;tag=' . $tag . ';' . (isset($_GET['simple']) ? 'simple;' : '') . $context['session_var'] . '=' . $context['session_id'];
	$context['force_form_onsubmit'] = 'return BeforeSubmit();';
	$context['CBBC_row'] = $row;
	$context['CBBC_multipart'] = true;

	// Prepare everything for showing it to the user:
	prepareDBSettingContext($config_vars);
}

function CustomBBCode_Save($tag)
{
	isAllowedTo('manage_custom_bbcodes');
	checkSession('request');

	// Populate the data fields with information supplied:
	$row = array(
		'id' => ($tag == -1 ? CBBC_get_max_id() + 1 : $tag),
		'tag' => (isset($_POST['Edit_name']) ? strtolower($_POST['Edit_name']) : ''),
		'content' => '',
		'before' => '',
		'after' => '',
		'enabled' => (isset($_POST['enabled']) ? (int) $_POST['enabled'] : 1),
		'block_level' => (isset($_POST['Edit_block_level']) ? 1 : 0),
		'trim' => (isset($_POST['Edit_trim']) ? $_POST['Edit_trim'] : 'none'),
		'ctype' => (isset($_POST['Edit_type']) ? $_POST['Edit_type'] : 'parsed_content'),
		'button' => (isset($_POST['Edit_show_button']) ? $_POST['Edit_show_button'] : 0),
		'description' => (isset($_POST['Edit_description']) ? $_POST['Edit_description'] : ''),
		'accept_urls' => (isset($_POST['Edit_accept_urls']) ? $_POST['Edit_accept_urls'] : 0),
	);

	// Make sure that the bbcode doesn't exist.  If it does, error out....
	if (CBBC_bbcode_exists($row['tag']))
		fatal_lang_error('bbcode_exists', false);

	// Are we trying to upload a new bbcode button image?
	if (!empty($_FILES['file']['tmp_name']))
	{
		if (CBBC_copy_button_image($row['tag']))
			$row['button'] = 1;
		else
			return CustomBBCodes_Edit($tag);
	}

	// Break the given HTML string into a more understandable form for SMF....
	$text = isset($_POST['Edit_html']) ? $_POST['Edit_html'] : '';
	switch($row['ctype'])
	{
		case 'closed':
			break;

		case 'unparsed_content':
			$row['content'] = str_replace('$1', '{content}', $text);
			break;

		case 'unparsed_commas_content':
		case 'unparsed_equals_content':
			$search = array('{content}', '{option1}', '{option2}', '{option3}', '{option4}', '{option5}', '{option6}', '{option7}', '{option8}');
			$replace = array('$1', '$2', '$3', '$4', '$5', '$6', '$7', '$8', '$9');
			$row['content'] = str_replace($search, $replace, $text);
			break;

		case 'parsed_equals':
		case 'parsed_content':
		case 'unparsed_equals':
		case 'unparsed_commas':
		default:
			$search = array('{option1}', '{option2}', '{option3}', '{option4}', '{option5}', '{option6}', '{option7}', '{option8}');
			$replace = array('$1', '$2', '$3', '$4', '$5', '$6', '$7', '$8');
			$pos = strpos($text, '{content}');
			if ($pos === false)
				$row['before'] = str_replace($search, $replace, $text);
			else
			{
				$row['before'] = str_replace($search, $replace, substr($text, 0, $pos));
				$row['after'] = str_replace($search, $replace, substr($text, $pos + 9));
			}
			break;
	}

	// Insert the information, then return to the CustomBBCodes listing page:
	CBBC_replace_tag($row);
	redirectexit('action=admin;area=' . $_GET['area'] . ';sa=custombbc');
}

function CustomBBCodes_Delete($tag)
{
	isAllowedTo('manage_custom_bbcodes');
	checkSession('get');
	CBBC_remove_tag((int) $tag);
	redirectexit('action=admin;area=' . $_GET['area'] . ';sa=custombbc');
}

function CustomBBCode_Enable($tag)
{
	isAllowedTo('manage_custom_bbcodes');
	checkSession('get');
	CBBC_update_tag((int) $tag, array('enabled' => 1));
	redirectexit('action=admin;area=' . $_GET['area'] . ';sa=custombbc');
}

function CustomBBCode_Disable($tag)
{
	isAllowedTo('manage_custom_bbcodes');
	checkSession('get');
	CBBC_update_tag((int) $tag, array('enabled' => 0));
	redirectexit('action=admin;area=' . $_GET['area'] . ';sa=custombbc');
}

/**********************************************************************************
* CustomBBCode XML functions for Custom BBCode Manager page:
**********************************************************************************/
function CustomBBCode_AJAX_Exists($tag)
{
	isAllowedTo('manage_custom_bbcodes');
	die( CBBC_bbcode_exists(isset($_GET['tag']) ? $_GET['tag'] : false) ? 'Invalid' : 'OK' );
}

function CustomBBCode_AJAX_Upload($tag)
{
	global $context;
	isAllowedTo('manage_custom_bbcodes');
	$result = CBBC_copy_button_image($tag);
	if (is_int($result) && !empty($_POST['id']))
		CBBC_update_tag($_POST['id'], array( 'button' => 1, 'last_update' => $time = time() ) );
	die( is_int($result) ? $result : $context['settings_message'] );
}

function CustomBBCode_AJAX_Remove($tag)
{
	isAllowedTo('manage_custom_bbcodes');
	CBBC_remove_button_image(isset($_GET['tag']) ? $_GET['tag'] : false);
	if (!empty($_POST['id']))
		CBBC_update_tag($_POST['id'], array( 'button' => 0, 'last_update' => $time = time() ) );
	die('OK');
}

?>