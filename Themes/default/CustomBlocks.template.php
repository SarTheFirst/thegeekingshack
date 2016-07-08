<?php

/**
 * Custom blocks
 *
 * @package CustomBlocks
 * @version 2.4
 * @author davidhs
 * @copyright 2013-2015 by davidhs
 * @license Creative Commons Attribution 3.0 Unported License http://creativecommons.org/licenses/by/3.0/
 */

// Template for admin area settings (edit).
function template_cb_settings_edit()
{
	global $txt, $scripturl, $context, $settings;

	echo '
	<div id="admincenter">
		<form action="', $scripturl, '?action=admin;area=modsettings;sa=cbedit;pos=', $context['pos'], ';id=', $context['id'], $context['params_url'], ';', $context['session_var'], '=', $context['session_id'], '" method="post" accept-charset="', $context['character_set'], '">
			<div class="cat_bar">
				<h3 class="catbg">
					', $context['settings_title'], '
				</h3>
			</div>
			<div class="windowbg">
				<span class="topslice"><span></span></span>
				<div class="content">
					<dl class="settings">';

	if (!empty($context['id']))
	{
		echo '
						<dt>
							<label for="pos_new">', $txt['cb_position'], '</label>
						</dt>
						<dd>
							<select name="pos_new" id="pos_new">';

		foreach ($context['params']['pos_array'] as $k => $v)
			echo '
								<option value="', $k, '"', $context['pos'] == $k ? ' selected="selected"' : '', '>', $v, '</option>';

		echo '
							</select>
						</dd>';
	}

	echo '
						<dt>
							<label for="description">', $txt['cb_description'], '</label>
						</dt>
						<dd>
							<textarea name="description" id="description" rows="2" cols="30">', $context['params']['description'], '</textarea>
						</dd>
						<dt>
							<label for="cb_frame">', $txt['cb_frame'], '</label><br />
							<span class="smalltext">', $txt['cb_frame_desc'], '</span>
						</dt>
						<dd>
							<textarea name="frame" id="cb_frame" rows="4" cols="30" style="min-width: 90%;">', $context['params']['frame'], '</textarea>
						</dd>
						<dt>
							<label for="type">', $txt['cb_type'], '</label>
						</dt>
						<dd>
							<select name="type" id="type">';

	foreach ($context['params']['type_array'] as $k => $v)
		echo '
								<option value="', $k, '"', $context['params']['type'] == $k ? ' selected="selected"' : '', '>', $v, '</option>';

	echo '
							</select>
						</dd>
						<dt>
							<a href="', $scripturl, '?action=helpadmin;help=cb_content" onclick="return reqWin(this.href);" class="help"><img src="', $settings['images_url'], '/helptopics.gif" class="icon" alt="', $txt['help'], '" /></a>
							<label for="content">', $txt['cb_content'], '</label>', 'footer' == $context['pos'] ? '<br />
							<span class="smalltext">' . $txt['cb_content_footer_desc'] . '</span>' : '','
						</dt>
						<dd>
							<textarea name="content" id="content" rows="4" cols="30" style="min-width: 90%;">', $context['params']['content'], '</textarea>
						</dd>
						<dt>
							<label for="order">', $txt['cb_order'], '</label>
						</dt>
						<dd>
							<input type="text" name="order" id="order" value="', $context['params']['order'], '" size="5" class="input_text" />
						</dd>
						<dt>
							', $txt['cb_permissions'], '<br />
							<span class="smalltext">', $txt['cb_permissions_desc'], '</span>
						</dt>
						<dd>
							<table>
								<tr>
									<td>&nbsp;</td>';
	foreach ($context['params']['permissions_actions'] as $pa => $txt_pa)
		echo '
									<td align="center">', $txt_pa, '</td>';
	echo '
								</tr>';
	foreach ($context['params']['permissions_users'] as $pu => $txt_pu)
	{
		echo '
								<tr>
									<td align="left">', $txt_pu, '</td>';
		foreach ($context['params']['permissions_actions'] as $pa => $txt_pa)
		{
			$perm = $pu . ':' . $pa;
			$perm_id = $pu . '_' . $pa;
			echo '
									<td align="center"><input type="checkbox" name="permissions[]" id="permissions_' . $perm_id . '" value="' . $perm . '"' . (in_array($perm, $context['params']['permissions']) ? ' checked="checked"' : '') . ' class="input_check" />', '</td>';
		}
		echo '
								</tr>';
	}
	echo '
								<tr>
									<td align="left"><em>', $txt['check_all'], '</em></td>';
	foreach ($context['params']['permissions_actions'] as $pa => $txt_pa)
		echo '
									<td align="center"><input type="checkbox" name="check_all_permissions_', $pa, '" id="check_all_permissions_', $pa, '" value="" class="input_check" /></td>';
	echo '
								</tr>
							</table>
						</dd>
						<dt>
							<label for="active">', $txt['cb_active'], '</label><br />
							<span class="smalltext">', $txt['cb_active_desc'], '</span>
						</dt>
						<dd>
							<input type="checkbox" name="active" id="active" value="1"', $context['params']['active'] ? ' checked="checked"' : '', ' class="input_check" />
						</dd>
					</dl>
					<div class="righttext">
						<input type="submit" name="save" value="', $txt['save'], '" accesskey="s" class="button_submit" />
					</div>
				</div>
				<span class="botslice"><span></span></span>
			</div>
		</form>
	</div>
	<br class="clear" />
	<script type="text/javascript"><!-- // --><![CDATA[';

	foreach ($context['params']['permissions_actions'] as $pa => $txt_pa)
	{
		echo '

		document.getElementById("check_all_permissions_', $pa, '").onclick = function (event)
		{';
		foreach ($context['params']['permissions_users'] as $pu => $txt_pu)
		{
			$perm_id = $pu . '_' . $pa;
			echo '
			if (document.getElementById("permissions_', $perm_id, '"))
				document.getElementById("permissions_', $perm_id, '").checked = this.checked;';
		}
		echo '
		}';
	}

	echo '
	// ]]></script>';
}

// Template for admin area settings (show).
function template_cb_settings_show()
{
	global $txt, $scripturl, $context, $settings;

	echo '
	<div class="cat_bar">
		<h3 class="catbg">
			<a href="', $scripturl, '?action=helpadmin;help=cb" onclick="return reqWin(this.href);" class="help"><img src="', $settings['images_url'], '/helptopics.gif" class="icon" alt="', $txt['help'], '" /></a>
			', $context['settings_title'], '
		</h3>
	</div>';

	$pos_array = array('header_above', 'header', 'header_below', 'footer_above', 'footer', 'footer_below');
	foreach ($pos_array as $i => $pos)
	{
		if (!$i)
			echo '
	<br />';
		template_show_list('cb_settings_show_' . $pos);
	}
}

?>