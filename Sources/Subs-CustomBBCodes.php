<?php
/**********************************************************************************
* Subs-CustomBBCode.php - Subs of the Custom BBCode Manager mod
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
* CustomBBCode hooks defining the new bbcodes
**********************************************************************************/
function CustomBBCodes_BBCodes(&$codes)
{
	global $smcFunc;

	if (($bbcodes = cache_get_data('bbcodes_custom', 86400)) == null)
	{
		// Build the subforum tree array:
		$request = $smcFunc['db_query']('', '
			SELECT *
			FROM {db_prefix}bbcodes
			WHERE enabled = {int:enabled}',
			array(
				'enabled' => 1
			)
		);
		$bbcodes = array();
		while ($row = $smcFunc['db_fetch_assoc']($request))
		{
			// Strip unnecessary fields from the resulting array:
			if ($row['ctype'] != 'parsed_content')
				$row['type'] = $row['ctype'];
			if ($row['trim'] == 'none')
				unset($row['trim']);
			$row['block_level'] = ($row['block_level'] ? true : false);
			if (!$row['block_level'])
				unset($row['block_level']);
			switch($row['ctype'])
			{
				case 'unparsed_content':
				case 'closed':
				case 'unparsed_commas_content':
				case 'unparsed_equals_content':
					unset($row['before']);
					unset($row['after']);
					$row['content'] = stripslashes($row['content']);
					break;

				case 'parsed_equals':
				case 'unparsed_equals':
				case 'parsed_content':
				case 'unparsed_commas':
				default:
					unset($row['content']);
					$row['before'] = stripslashes($row['before']);
					$row['after'] = stripslashes($row['after']);
					break;
			}
			unset($row['ctype']);

			// If the "accept_urls" option is checked, add a validation function:
			if (!empty($row['accept_urls']))
				$row['validate'] = 'CustomBBCodes_URL_Validate';

			// Add the new BBCode to the array:
			$bbcodes[] = $row;
		}
		$smcFunc['db_free_result']($request);

		// Stuff this array into the cache for future use:
		cache_put_data('bbcodes_custom', $bbcodes, 86400);
	}
	$codes = array_merge($codes, $bbcodes);
}

function CustomBBCodes_URL_Validate(&$tag, &$data, &$disabled)
{
	$data = strtr($data, array('<br>' => ''));
	if (strpos($data, 'http://') !== 0 && strpos($data, 'https://') !== 0)
		$data = 'http://' . $data;
}

/**********************************************************************************
* CustomBBCode hooks defining the new bbcodes buttons
**********************************************************************************/
function CustomBBCodes_Buttons(&$buttons)
{
	global $smcFunc, $forum_version, $boarddir;

	if (($cached = cache_get_data('bbcodes_buttons', 86400)) == null)
	{
		$request = $smcFunc['db_query']('', '
			SELECT tag, ctype, description, last_update
			FROM {db_prefix}bbcodes
			WHERE enabled = {int:enabled}
				AND button = {int:button}',
			array(
				'enabled' => 1,
				'button' => 1,
			)
		);

		// Build the Custom BBcode array:
		$cached = array();
		$ext = (substr($forum_version, 0, 7) == 'SMF 2.1' ? 'png' : 'gif');
		while ($row = $smcFunc['db_fetch_assoc']($request))
		{
			// Skip this custom bbcode if the button isn't present on the system:
			$tag = stripslashes($row['tag']);
			if (!file_exists($boarddir . '/Themes/default/images/bbc/' . $tag . '.' . $ext))
				continue;
	
			// Process the bbcode button tag:
			$tmp = array(
				'image' => $tag . '_' . $row['last_update'],
				'code' => $tag,
				'description' => stripslashes(empty($row['description']) ? $tag : $row['description']),
			);
			switch($row['ctype'])
			{
				case 'parsed_content':
				case 'unparsed_content':
					$tmp['before'] = '[' . $tag . ']';
					$tmp['after'] = '[/' . $tag . ']';
					break;

				case 'closed':
					$tmp['before'] = '[' . $tag . ']';
					$tmp['after'] = '';
					break;
					
				case 'parsed_equals':
				case 'unparsed_commas':
				case 'unparsed_commas_content':
				case 'unparsed_equals':
				case 'unparsed_equals_content':
				default:
					$tmp['before'] = '[' . $tag . '=';
					$tmp['after'] = '][/' . $tag . ']';
			}

			$cached[] = $tmp;
		}
		$smcFunc['db_free_result']($request);

		// Stuff this array into the cache for future use:
		cache_put_data('bbcodes_buttons', $cached, 86400);
	}
	$buttons[0] = array_merge($buttons[0], $cached);
}

/**********************************************************************************
* CustomBBCode hook defining any needed css for the bbcode buttons:
**********************************************************************************/
function CustomBBCodes_LoadTheme()
{
}

/**********************************************************************************
* CustomBBCode support function for Help area:
**********************************************************************************/
function CBBC_Spoiler($title, $hidden)
{
	global $txt;
	return '<div style="padding: 3px; font-size: 1em;"><div style="border-bottom: 1px solid #5873B0; margin-bottom: 3px; font-size: 0.8em; font-weight: bold; display: block;"><span onClick="if (this.parentNode.parentNode.getElementsByTagName(\'div\')[1].getElementsByTagName(\'div\')[0].style.display != \'\') {  this.parentNode.parentNode.getElementsByTagName(\'div\')[1].getElementsByTagName(\'div\')[0].style.display = \'\'; this.innerHTML = \'<b>' . $title . ': </b><a href=\\\'#\\\' onClick=\\\'return false;\\\'>' . strtoupper($txt["debug_hide"]) . '</a>\'; } else { this.parentNode.parentNode.getElementsByTagName(\'div\')[1].getElementsByTagName(\'div\')[0].style.display = \'none\'; this.innerHTML = \'<b>' . $title . ': </b><a href=\\\'#\\\' onClick=\\\'return false;\\\'>' . strtoupper($txt["debug_show"]) . '</a>\'; }" /><b>' . $title . ': </b><a href="#" onClick="return false;">' . strtoupper($txt["debug_show"]) . '</a></span></div><div class="quotecontent"><div style="display: none;">' . $hidden . '</div></div></div>';
}

?>