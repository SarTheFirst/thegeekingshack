<?php
/**********************************************************************************
* CustomBBCodeAdmin.template.php - Custom BBCode Manager mod template file
***********************************************************************************
* This mod is licensed under the 2-clause BSD License, which can be found here:
*	http://opensource.org/licenses/BSD-2-Clause
*********************************************************************************
* This program is distributed in the hope that it is and will be useful, but
* WITHOUT ANY WARRANTIES; without even any implied warranty of MERCHANTABILITY
* or FITNESS FOR A PARTICULAR PURPOSE,
**********************************************************************************/
if (!defined('SMF'))
	die('Hacking attempt...');

function template_CustomBBCode_Browse()
{
	global $txt, $context, $scripturl, $forum_version;

	// Output any message we want to tell the user, plus the list of bbcodes:
	if (!empty($context['settings_message']))
		echo '
		<div class="information">', $context['settings_message'], '</div>';
	template_show_list('list_bbc');

	// Create the "Create New Tag" button, depending of what version SMF we are running:
	if (substr($forum_version, 0, 7) == 'SMF 2.1')
		echo '
		<div align="right">
			<a href="', $scripturl, '?action=admin;area=', $_GET['area'], ';sa=custombbc;activity=newtag;simple;sesc=' , $context['session_id'] , '" class="button">', $txt['List_simple'], '</a>
			<a href="', $scripturl, '?action=admin;area=', $_GET['area'], ';sa=custombbc;activity=newtag;newtag;sesc=' , $context['session_id'] , '" class="button">', $txt['List_add'], '</a>
		</div>';
	else
		echo '
		<form action="', $scripturl, '?action=admin;area=', $_GET['area'], ';sa=custombbc;activity=newtag;sesc=' , $context['session_id'] , '" name="restore" method="post" accept-charset="', $context['character_set'], '">
			<div align="right">
				<input type="submit" class="button_submit" name="simple" value="', $txt['List_simple'], '" />
				<input type="submit" class="button_submit" name="submit" value="', $txt['List_add'], '" />
			</div>
		</form>';
}

function template_callback_button_upload()
{
	global $txt, $forum_version, $boardurl, $context;
	
	// Figure out what we want to output first:
	$row = $context['CBBC_row'];
	$smf21 = (substr($forum_version, 0, 7) == 'SMF 2.1');
	$help = ($smf21 ? '<span class="generic_icons help" title="Help"></span>' : '<img src="' . $boardurl . '/Themes/default/images/helptopics.gif" class="icon" alt="Help">');
	
	// Output everything we need to here:
	echo '
						<p>', ($smf21 ? $txt['Edit_21_Upload_description'] : $txt['Edit_20_Upload_description']), '</p>
						<br />
						<dt>
							<a href="' . $boardurl . '/index.php?action=helpadmin;help=Edit_button_filename" onclick="return reqWin(this.href);" class="help">' . $help . '</a>
							<label for="file">', $txt['Edit_Filename'], '</label>
						</dt>
						<dd>
							<span id="file_span"><input type="file" name="file" id="file" size="40"></span>
							<input type="button"' . ($smf21 ? ' class="button_submit"' : '') . 'value="', $txt['upload_file'], '" onclick="uploadFile();" style="vertical-align: middle; float: none;" />
							<input type="button"' . ($smf21 ? ' class="button_submit"' : '') . 'value="', $txt['remove_file'], '" onclick="removeFile();" style="vertical-align: middle; float: none;" id="remove_button" />
							<input type="button"' . ($smf21 ? ' class="button_submit"' : '') . 'value="', $txt['clear_file'], '" onclick="clearFile();" style="vertical-align: middle; float: none;" />
						</dd>
						<dt id="tag_dt"' . (!$row['url_exists'] ? ' style="display: none;"' : '') . '>
							<label for="file">', $txt['Edit_Preview'], '</label>
						</dt>
						<dd id="tag_dd"' . (!$row['url_exists'] ? ' style="display: none;"' : '') . '>
							<img id="tag_img" style="cursor: pointer; background-image: url(&quot;' . $boardurl . '/Themes/default/images/bbc/bbc_bg.' . ($smf21 ? 'png' : 'gif') . '&quot;);" id="BBC_Button" src="' . $boardurl . '/Themes/default/images/bbc/' . $row['tag'] . '.' . ($smf21 ? 'png' : 'gif') . '_' . $row['last_update'] . '" align="middle" width="' . ($smf21 ? 16 : 23) . '" height="' . ($smf21 ? 16 : 22) . '" />
						</dd>';
}

?>