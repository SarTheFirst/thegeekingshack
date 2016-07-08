<?php
/**********************************************************************************
* Subs-CustomBBCodeAdmin.php - Admin Subs of the Custom BBCode Manager mod
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
* Functions dealing with listing the BBcodes for the user:
*********************************************************************************/
function CBBC_get_count()
{
	global $smcFunc;
	isAllowedTo('manage_custom_bbcodes');

	$request = $smcFunc['db_query']('', '
		SELECT COUNT(*)
		FROM {db_prefix}bbcodes',
		array(
		)
	);
	list ($total_bbc) = $smcFunc['db_fetch_row']($request);
	$smcFunc['db_free_result']($request);

	return $total_bbc;
}

function CBBC_get_data($start, $items_per_page, $sort)
{
	global $smcFunc, $settings, $scripturl, $context;
	isAllowedTo('manage_custom_bbcodes');

	$request = $smcFunc['db_query']('', '
		SELECT id, enabled, tag, ctype
		FROM {db_prefix}bbcodes
		ORDER BY {raw:sort}
		LIMIT {int:start}, {int:per_page}',
		array(
			'sort' => $sort,
			'start' => $start,
			'per_page' => $items_per_page,
		)
	);
	$bbcode = array();
	while ($row = $smcFunc['db_fetch_assoc']($request))
	{
		if (file_exists( CBBC_get_button_path($row['tag']) ))
 			$row['button'] = '<center><a href="' . $scripturl . '?action=admin;area=' . $_GET['area'] . ';sa=custombbc;activity=edit;tag=' . $row['id'] . ';' . $context['session_var'] . '=' . $context['session_id'] . '"><img src="' . CBBC_get_button_URL($row['tag']) . '" alt="" width=23 height=22 /></a></center>';
		else
			$row['button'] = '';
		$row['form'] = CBBC_get_readable($row['ctype'], $row['tag']);
		$row['tag'] = '<a href="' . $scripturl . '?action=admin;area=' . $_GET['area'] . ';sa=custombbc;activity=edit;tag=' . $row['id'] . ';' . $context['session_var'] . '=' . $context['session_id'] . '">' . ($row['tag_name'] = $row['tag']) . '</a>';
		$bbcode[] = $row;
	}
	$smcFunc['db_free_result']($request);

	return $bbcode;
}

function CBBC_get_actions($row)
{
	global $scripturl, $txt, $context;
	return '<a href="' . $scripturl . '?action=admin;area=' . $_GET['area'] . ';sa=custombbc;activity=edit;tag=' . $row['id'] . ';' . $context['session_var'] . '=' . $context['session_id'] . '" class="button">' . $txt['List_modify'] . '</a>&nbsp;&nbsp;' .
		'<a href="' . $scripturl . '?action=admin;area=' . $_GET['area'] . ';sa=custombbc;activity=delete;tag=' . $row['id'] . ';' . $context['session_var'] . '=' . $context['session_id'] . '" onclick="return confirm(\'' . $txt['List_delete_bbcode'] . '\');" class="button">' . $txt['List_delete'] . '</a>';
}

function CBBC_get_enabled($row)
{
	global $scripturl, $txt, $context;
	return '<center><a href="' . $scripturl . '?action=admin;area=' . $_GET['area'] . ';sa=custombbc;activity=' . ($row['enabled'] ? 'disable' : 'enable') . ';tag=' . $row['id'] . ';' . $context['session_var'] . '=' . $context['session_id'] . '" class="button">' . ($row['enabled'] ? $txt['List_disable'] : $txt['List_enable']) . '</a></center>';
}

function CBBC_get_readable($ctype, $tag)
{
	global $txt;

	// Translate the ctype variable into human-readable form:
	switch ($ctype)
	{
		case 'unparsed_equals':
			return '[' . $tag . '=xyz]' . $txt['Edit_uncontent'] . '[/' . $tag . ']';
		case 'parsed_equals':
			return '[' . $tag . '=' . $txt['Edit_data'] . ']' . $txt['Edit_content'] . '[/' . $tag . ']';
		case 'unparsed_content':
			return '[' . $tag . ']' . $txt['Edit_uncontent'] . '[/' . $tag . ']';
		case 'closed':
			return '[' . $tag . '], [' . $tag . '/], [' . $tag . ' /]';
		case 'unparsed_commas':
			return '[' . $tag . '=1,2,3]' . $txt['Edit_content'] . '[/' . $tag . ']';
		case 'unparsed_commas_content':
			return '[' . $tag . '=1,2,3]' . $txt['Edit_uncontent'] . '[/' . $tag . ']';
		case 'unparsed_equals_content':
			return '[' . $tag . '=...]' . $txt['Edit_uncontent'] . '[/' . $tag . ']';
		case 'parsed_content':
		default:
			return '[' . $tag . ']' . $txt['Edit_content'] . '[/' . $tag . ']';
	}
}

/**********************************************************************************
* Adminstrative functions dealing with the BBcodes:
*********************************************************************************/
function CBBC_get_row($id, $decode = false)
{
	global $smcFunc;
	isAllowedTo('manage_custom_bbcodes');

	$request = $smcFunc['db_query']('', '
		SELECT *
		FROM {db_prefix}bbcodes
		WHERE id = {int:id}',
		array(
			'id' => (int) $id,
		)
	);
	$row = $smcFunc['db_fetch_assoc']($request);
	$smcFunc['db_free_result']($request);
	if (!empty($row))
	{
		$row['tag'] = stripslashes($row['tag']);
		$row['description'] = stripslashes($row['description']);
		$row['content'] = stripslashes($row['content']);
		$row['before'] = stripslashes($row['before']);
		$row['after'] = stripslashes($row['after']);
		$row['trim'] = stripslashes($row['trim']);
		$row['ctype'] = stripslashes($row['ctype']);
	}
	return $row;
}

function CBBC_remove_tag($id)
{
	global $smcFunc;
	isAllowedTo('manage_custom_bbcodes');

	// Retrieve the tag name and remove the image button from the server:
	$row = CBBC_get_row($id);
	if (isset($row['tag']))
		CBBC_remove_button_image($row['tag']);

	// Delete the entry from the database:
	$smcFunc['db_query']('', '
		DELETE FROM {db_prefix}bbcodes
		WHERE id = {int:id}',
		array(
			'id' => (int) $id,
		)
	);

	// Empty out the cached arrays so that it updates properly:
	cache_put_data('bbcodes_custom', null, 86400);
	cache_put_data('bbcodes_buttons', null, 86400);
}

function CBBC_update_tag($id, $new)
{
	global $smcFunc;
	isAllowedTo('manage_custom_bbcodes');

	$row = CBBC_get_row((int) $id);
	if (!isset($row['id']) && $row['id'] != ((int) $id))
		return;
	$row = array(
		'id' => (int) $id,
		'content' => isset($new['content']) ? $new['content'] : $row['content'],
		'before' => isset($new['before']) ? $new['before'] : $row['before'],
		'after' => isset($new['after']) ? $new['after'] : $row['after'],
		'enabled' => (int) isset($new['enabled']) ? $new['enabled'] : $row['enabled'],
		'tag' => isset($new['tag']) ? $new['tag'] : $row['tag'],
		'description' => isset($new['description']) ? $new['description'] : $row['description'],
		'block_level' => (int) isset($new['block_level']) ? $new['block_level'] : $row['block_level'],
		'trim' => isset($new['trim']) ? $new['trim'] : $row['trim'],
		'ctype' => isset($new['ctype']) ? $new['ctype'] : $row['ctype'],
		'button' => (int) isset($new['button']) ? $new['button'] : $row['button'],
		'accept_urls' => (int) isset($new['accept_urls']) ? $new['accept_urls'] : $row['accept_urls'],
	);
	return CBBC_replace_tag($row);
}

function CBBC_replace_tag($data)
{
	global $smcFunc;
	isAllowedTo('manage_custom_bbcodes');

	// Insert the information:
	$smcFunc['db_insert']('replace',
		'{db_prefix}bbcodes',
		array(
			'id' => 'int',
			'content' => 'text',
			'before' => 'text',
			'after' => 'text',
			'enabled' => 'int',
			'tag' => 'text',
			'block_level' => 'int',
			'trim' => 'text',
			'ctype' => 'text',
			'button' => 'int',
			'description' => 'text',
			'last_update' => 'int',
			'accept_urls' => 'int',
		),
		array(
			(int) $data['id'],
			isset($data['content']) ? addslashes($data['content']) : '',
			isset($data['before']) ? addslashes($data['before']) : '',
			isset($data['after']) ? addslashes($data['after']) : '',
			(int) isset($data['enabled']) ? $data['enabled'] : 1,
			isset($data['tag']) ? addslashes($smcFunc['strtolower']($data['tag'])) : '',
			(int) isset($data['block_level']) ? $data['block_level'] : 0,
			isset($data['trim']) ? addslashes($data['trim']) : '',
			isset($data['ctype']) ? addslashes($data['ctype']) : '',
			(int) isset($data['button']) ? $data['button'] : 0,
			isset($data['description']) ? addslashes($data['description']) : '',
			$time = time(),
			(int) isset($data['accept_urls']) ? $data['accept_urls'] : 0,
		),
		array('id')
	);

	// Empty out the cached arrays so that it updates properly:
	cache_put_data('bbcodes_custom', null, 86400);
	cache_put_data('bbcodes_buttons', null, 86400);
	return $time;
}

function CBBC_get_max_id()
{
	global $smcFunc;
	isAllowedTo('manage_custom_bbcodes');

	$request = $smcFunc['db_query']('', '
		SELECT MAX(id) AS max_id
		FROM {db_prefix}bbcodes',
		array()
	);
	$row = $smcFunc['db_fetch_assoc']($request);
	$smcFunc['db_free_result']($request);
	return $row['max_id'];
}

/**********************************************************************************
* Adminstrative functions dealing with the BBcodes button images:
*********************************************************************************/
function CBBC_copy_button_image($tag)
{
	global $smcFunc, $boarddir, $modSettings, $forum_version, $txt, $context;
	isAllowedTo('manage_custom_bbcodes');

	// Make sure that the file being uploaded mets expections:
	$ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
	if (!empty($_FILES['file']['tmp_name']))
		$file = getimagesize($_FILES['file']['tmp_name']);
	$smf21 = (substr($forum_version, 0, 7) == 'SMF 2.1');
	$extension = ($smf21 ? 'png' : 'gif');
	$checks = array(
		'upload' => array($_FILES['file']['error'] == 0, 0),
		'extension' => array(strtolower($ext) == $extension, strtoupper($extension)),
		'mime_type' => array($file['mime'] == 'image/' . $ext, 0),
		'file_size' => array($_FILES['file']['size'] <= 10240, 0),
		'width' => array($file[0] == ($smf21 ? 16 : 23), ($smf21 ? 16 : 23)),
		'height' => array($file[1] == ($smf21 ? 16 : 22), ($smf21 ? 16 : 22)),
	);
	foreach ($checks as $error_type => $data)
	{
		if (!$data[0])
		{
			$context['settings_message'] = sprintf($txt['Edit_' . $error_type . '_error'], $data[1]);
			return false;
		}
	}

	// Copy the image to the images/bbc folders in each theme:
	$request = $smcFunc['db_query']('', '
		SELECT value
		FROM {db_prefix}themes
		WHERE variable = {string:name}
			AND id_member = 0
			AND id_theme IN ({raw:known})',
		array(
			'name' => 'theme_dir',
			'known' => $modSettings['knownThemes'],
		)
	);
	$dest = $boarddir . '/Themes/default/images/bbc/' . $tag . '.' . $extension;
	move_uploaded_file($_FILES['file']['tmp_name'], $dest);
	while ($row = $smcFunc['db_fetch_assoc']($request))
		copy($dest, $row['value'] . '/images/bbc/' . $tag . '.' . $extension);
	$smcFunc['db_free_result']($request);
	return $time;
}

function CBBC_remove_button_image($tag)
{
	global $smcFunc, $boarddir, $modSettings, $forum_version;
	isAllowedTo('manage_custom_bbcodes');

	$request = $smcFunc['db_query']('', '
		SELECT value
		FROM {db_prefix}themes
		WHERE variable = {string:name}
			AND id_member = 0
			AND id_theme IN ({raw:known})',
		array(
			'name' => 'theme_dir',
			'known' => $modSettings['knownThemes'],
		)
	);
	$ext = (substr($forum_version, 0, 7) == 'SMF 2.1' ? 'png' : 'gif');
	while ($row = $smcFunc['db_fetch_assoc']($request))
		@unlink($row['value'] . '/images/bbc/' . $tag . '.' . $ext);
	$smcFunc['db_free_result']($request);
}

function CBBC_get_button_URL($tag)
{
	global $settings, $forum_version;
	return $settings['images_url'] . '/bbc/' . $tag . '.' . (substr($forum_version, 0, 7) == 'SMF 2.1' ? 'png' : 'gif');
}

function CBBC_get_button_path($tag)
{
	global $settings, $forum_version;
	return $settings['theme_dir'] . '/images/bbc/' . $tag . '.' . (substr($forum_version, 0, 7) == 'SMF 2.1' ? 'png' : 'gif');
}

/**********************************************************************************
* Function to test to see if the bbcode already exists:
*********************************************************************************/
function CBBC_bbcode_exists($tag)
{
	global $smcFunc, $modSettings;
	isAllowedTo('manage_custom_bbcodes');

	// Little bit of sanity checking, if you please:
	if (empty($tag))
		return false;

	// Is this bbcode is already defined by SMF itself or another mod?
	// OLD CODE => remove_integration_function('integrate_bbc_codes', 'CustomBBCodes_BBCodes');
	$current = explode(',', ($old = $modSettings['integrate_bbc_codes']));
	$modSettings['integrate_bbc_codes'] = implode(',', array_diff($current, array('CustomBBCodes_BBCodes')));
	$temp = parse_bbc(false);
	// OLD CODE => add_integration_function('integrate_bbc_codes', 'CustomBBCodes_BBCodes');
	$modSettings['integrate_bbc_codes'] = $old;
	$tags = array();
	foreach ($temp as $tagname)
		$tags[] = $tagname['tag'];
	$tags = array_unique($tags);
	if (in_array(strtolower($tag), $tags))
		return true;
}

/**********************************************************************************
* Function to put Javascript into the admin panel page:
*********************************************************************************/
function CBBC_javascript(&$row, $simple = false)
{
	global $context, $txt, $forum_version, $scripturl, $settings, $boardurl;

	// << SMF 2.1 ONLY >> Add CSS for the new tag's button so we can display it properly:
	if (($smf21 = substr($forum_version, 0, 7) == 'SMF 2.1'))
	{
		loadCSSFile('jquery.sceditor.css');
		addInlineJavascript('
	<style type="text/css">
		.sceditor-button-tag div {
			background: url("' . $row['image'] . '_' . $row['last_update'] . '");
		}
	</style>');
	}

	// Handle valid/invalid icons depending on which version of SMF we are running:
	$context['CBBC_Javascript'] = ($smf21 ?
		// NEXT 2 LINES ARE SMF 2.1 ONLY!
		'<span id="Tag_Valid" style="display: none" class="generic_icons valid"></span>' .
		'<span id="Tag_Invalid" style="display: none" class="generic_icons delete"></span>' :
		//=====================================================
		// NEXT 2 LINES ARE SMF 2.0 ONLY!
		'<span id="Tag_Valid" style="display: none"><img src="' . $settings['images_url'] . '/icons/field_valid.gif" class="icon" /></span>' .
		'<span id="Tag_Invalid" style="display: none"><img src="' . $settings['images_url'] . '/icons/quick_remove.gif" class="icon" /></span>');

	// We need/want some javascript for our admin page:
	$context['CBBC_xml'] = $scripturl . '?action=admin;area=' . $_GET['area'] . ';sa=custombbc;id=' . $row['id'];
	$context['settings_post_javascript'] = '
		function _(el){
			return document.getElementById(el);
		}
		function AlreadyExists()
		{' . (!$simple ? '
			Process();' : '') . '
			var tag = _("Edit_name").value.toLowerCase();
			_("Edit_name").value = tag;
			if (tag == "")
				return Tag_Valid("Same", tag);
			if (tag == "' . $row['tag'] . '")
				return Tag_Valid("Same", tag);
			var ajax = new XMLHttpRequest();
			ajax.onreadystatechange = function()
			{
				if (ajax.readyState == 4 && ajax.status == 200)
					Tag_Valid(ajax.responseText, tag);
			};
			ajax.open("GET", "' . $context['CBBC_xml'] . ';activity=bbc_exists;tag=" + tag, true);
			ajax.send();
		}
		function Tag_Valid(response, tag)
		{
			var good = (response == "Invalid");
			_("Tag_Valid").style.display = (good ? "none" : (response == "Same" ? "none" : ""));
			_("Tag_Invalid").style.display = (good ? "" : "none");
			if (good)
			{
				alert("' . $txt['bbcode_exists'] . '");
				_("Edit_name").focus();
			}
			UpdateIcon(tag);
		}
		function occurrences(string, subString, allowOverlapping) 
		{
			string += "";
			subString += "";
			if (subString.length <= 0) return (string.length + 1);

			var n = 0,
				pos = 0,
				step = allowOverlapping ? 1 : subString.length;

			while (true) {
				pos = string.indexOf(subString, pos);
				if (pos >= 0) {
					++n;
					pos += step;
				} else break;
			}
			return n;
		}		
		function BeforeSubmit()
		{
			if (_("Tag_Invalid").style.display == "")
			{
				alert("' . $txt['bbcode_exists'] . '");
				_("Edit_name").focus();
				return false;
			}
			var ttype = "parsed_content";
			if (_("Edit_type") != null)
				ttype = _("Edit_type").value;
			var count = occurrences(_("Edit_html").value, "{content}");
			if ((ttype == "parsed_content" || ttype == "unparsed_equals" || ttype == "parsed_equals") && (count > 1))
			{
				_("Edit_html").focus();
				return confirm("' . $txt['Edit_single_content_error'] . '");
			}
			if ((ttype == "closed") && (count > 0))
			{
				_("Edit_html").focus();
				return confirm("' . $txt['Edit_no_content_error'] . '");
			}
		}';

	// In normal mode, we need a bunch of Javascript to help our user!  So include it ONLY for normal mode!
	if (!$simple)
	{
		$context['settings_post_javascript'] .= '
		function Process()
		{
			<!-- Gather all the information we need -->
			var pre = "<a id=\"setting_Edit_name\" href=\"' . $scripturl . '?action=helpadmin;help=Edit_name\" onclick=\"return reqOverlayDiv(this.href);\">' .
				($smf21 ? '<span class=\"generic_icons help\" title=\"Help\"></span><a id=\"setting_Edit_name\">' :
					'<img src=\"' . $boardurl . '/Themes/default/images/helptopics.gif\" class=\"icon\" alt=\"Help\">') .
				($smf21 ? '</span> ' : '</a>') . '<label for=\"Edit_name\">' . $txt['Edit_html'] . '<br /><span class=\"smalltext\">";
			var post = "</span></label>' . ($smf21 ? '</span>' : '') . '";
			var tag = _("Edit_name").value;
			tag = (tag == "" ? "tag" : tag);
			var format = _("Edit_tag_format");
			var htmlDiv = _("htmlDiv");

			<!-- Make the necessary changes to the UI -->
			switch( _("Edit_type").selectedIndex )
			{
			case 0: <!-- parsed_content -->
				format.value = "[" + tag + "]' . $txt['Edit_content'] . '[/" + tag + "]";
				htmlDiv.innerHTML = pre + "' . $txt['Edit_subtext0'] . '" + post;
				break;

			case 1: <!-- unparsed_equals -->
				format.value = "[" + tag + "=xyz]' . $txt['Edit_content'] . '[/" + tag + "]";
				htmlDiv.innerHTML = pre + "' . $txt['Edit_subtext2'] . '" + post;
				break;

			case 2: <!-- parsed_equals -->
				format.value = "[" + tag + "=' . $txt['Edit_data'] . ']' . $txt['Edit_content'] . '[/" + tag + "]";
				htmlDiv.innerHTML = pre + "' . $txt['Edit_subtext2'] . '" + post;
				break;

			case 3: <!-- unparsed_content -->
				format.value = "[" + tag + "]' . $txt['Edit_uncontent'] . '[/" + tag + "]";
				htmlDiv.innerHTML = pre + "' . $txt['Edit_subtext0'] . '" + post;
				break;

			case 4: <!-- closed -->
				format.value = "[" + tag + "]";
				htmlDiv.innerHTML = pre + post;
				break;

			case 5: <!-- unparsed_commas -->
				format.value = "[" + tag + "=1,2,3]' . $txt['Edit_content'] . '[/" + tag + "]";
				htmlDiv.innerHTML = pre + "' . $txt['Edit_subtext2'] . '" + post;
				break;

			case 6: <!-- unparsed_commas_content -->
				format.value = "[" + tag + "=1,2,3]' . $txt['Edit_uncontent'] . '[/" + tag + "]";
				htmlDiv.innerHTML = pre + "' . $txt['Edit_subtext1'] . '" + post;
				break;

			case 7: <!-- unparsed_equals_content -->
				format.value = "[" + tag + "=...]' . $txt['Edit_uncontent'] . '[/" + tag + "]";
				htmlDiv.innerHTML = pre + "' . $txt['Edit_subtext1'] . '" + post;
				break;
			}
		}
		function clearFile()
		{
			_("file_span").innerHTML = _("file_span").innerHTML;
		}
		function uploadFile()
		{
			var tag = _("Edit_name").value.toLowerCase();
			_("Edit_name").value = tag;
			var file = _("file").files[0];
			var formdata = new FormData();
			formdata.append("file", file);
			var ajax = new XMLHttpRequest();
			ajax.onreadystatechange = function()
			{
				if (ajax.readyState == 4 && ajax.status == 200)
					updateIcon(ajax.responseText, true);
			};
			ajax.open("POST", "' . $context['CBBC_xml'] . ';activity=bbc_upload;tag=" + tag, true);
			ajax.send(formdata);
		}
		function removeFile()
		{
			var tag = _("Edit_name").value.toLowerCase();
			_("Edit_name").value = tag;
			var ajax = new XMLHttpRequest();
			ajax.onreadystatechange = function()
			{
				if (ajax.readyState == 4 && ajax.status == 200)
					updateIcon(ajax.responseText, false);
			};
			ajax.open("GET", "' . $context['CBBC_xml'] . ';activity=bbc_remove;tag=" + tag, true);
			ajax.send();
		}
		function updateIcon(response, show)
		{
			if (response % 1 !== 0)
				alert(response);
			else
			{
				var tag = _("Edit_name").value.toLowerCase();
				_("tag_img").src = "' . $boardurl . '/Themes/default/images/bbc/' . $row['tag'] . '.' . ($smf21 ? 'png' : 'gif') . '_" + response;
				_("tag_dt").style.display = (show ? "" : "none");
				_("tag_dd").style.display = (show ? "" : "none");
				_("Edit_show_button").disabled = (show ? "" : "disabled");
				_("Edit_show_button").checked = show;
				_("show_button").style.display = (show ? "" : "none");
				_("show_button_dd").style.display = (show ? "" : "none");
				_("file_span").innerHTML = _("file_span").innerHTML;
				_("remove_button").style.display = (show ? "" : "none");
			}
		}
		updateIcon(' . $row['last_update'] . ', ' . ($row['url_exists'] ? 'true' : 'false') . ');';
	}

	// Finish the Javascript stuff:
	$context['settings_post_javascript'] .= '
		AlreadyExists();';
}

?>