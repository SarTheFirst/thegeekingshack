<?php

/**
 * Custom blocks
 *
 * @package CustomBlocks
 * @version 2.3
 * @author davidhs
 * @copyright 2013-2014 by davidhs
 * @license Creative Commons Attribution 3.0 Unported License http://creativecommons.org/licenses/by/3.0/
 */

if (!defined('SMF'))
	die('Hacking attempt...');

// Gets blocks of position.
function cbGetBlocks($pos)
{
	global $modSettings;

	$blocks = array();

	if (!empty($modSettings['cb_' . $pos . '_ids']))
	{
		$ids = explode(',', $modSettings['cb_' . $pos . '_ids']);
		$copyright = false;

		foreach ($ids as $i => $id)
		{
			// Get settings.
			$cb_settings = cbGetSettings($pos, $id);
			if (empty($cb_settings))
				continue;

			extract($cb_settings);

			// Validate active and permissions.
			if (!$active || !in_array(cbGetTypeUser() . ':view', explode(',', $permissions)))
				continue;

			// Formating $order.
			$order = (100 * $order) + $i;

			// Get block.
			$blocks[$order] = $cb_settings;
			if ('footer' == $pos && !$copyright)
				$copyright = !(false === strpos($content, '{COPYRIGHT}'));
		}

		if (!empty($blocks))
		{
			ksort($blocks);

			if ('footer' == $pos && !$copyright)
				array_unshift($blocks, array(
					'frame' => 'style="float: center;"',
					'type' => 'html',
					'content' => '{COPYRIGHT}',
				));
		}
	}

	return $blocks;
}

// Gets settings of position.
function cbGetSettings($pos, $id = null)
{
	global $modSettings;

	$default_settings = array(
		'description' => '',
		'frame' => '',
		'type' => 'html',
		'content' => '',
		'order' => '0',
		'permissions' => '',
		'active' => '1',
	);

	$ids = !empty($modSettings['cb_' . $pos . '_ids']) ? explode(',', $modSettings['cb_' . $pos . '_ids']) : array();

	$cb_settings = array();
	if (empty($id))
		$cb_settings = $default_settings;
	elseif (is_numeric($id))
	{
		if (in_array($id, $ids))
		{
			$pos_id = $pos . '_' . $id;

			$cb_settings['id'] = $id;
			foreach ($default_settings as $param => $v)
				$cb_settings[$param] = isset($modSettings['cb_' . $pos_id . '_' . $param]) ? $modSettings['cb_' . $pos_id . '_' . $param] : $v;
		}
	}
	elseif (!empty($ids)) // $id = all.
	{
		foreach ($ids as $id)
			$cb_settings[] = cbGetSettings($pos, $id);
	}

	return $cb_settings;
}

// Gets type of user.
function cbGetTypeUser()
{
	global $context;

	if ($context['user']['is_admin'])
		$user = 'admin';
	elseif (allowedTo('moderate_forum'))
		$user = 'globalmod';
	elseif ($context['user']['is_mod'])
		$user = 'localmod';
	elseif ($context['user']['is_logged'])
		$user = 'user';
	elseif ($context['user']['is_guest'])
		$user = 'guest';

	return $user;
}

// Returns HTML code with blocks of position.
function cbParseBlocks($pos)
{
	global $settings, $boardurl, $scripturl;

	$text = '';

	$blocks = cbGetBlocks($pos);
	if (!empty($blocks))
	{
		ob_start();
		theme_copyright();
		$copyright = ob_get_contents();
		ob_end_clean();

		$trans = array(
			'{COPYRIGHT}' => $copyright,
			'{BOARDURL}' => $boardurl,
			'{SCRIPTURL}' => $scripturl,
			'{IMAGES_URL}' => $settings['images_url'],
			'{DEFAULT_IMAGES_URL}' => $settings['default_images_url'],
		);
		$trans_bbc1 = array(
			'{BOARDURL}' => $boardurl,
			'{SCRIPTURL}' => $scripturl,
			'{IMAGES_URL}' => $settings['images_url'],
			'{DEFAULT_IMAGES_URL}' => $settings['default_images_url'],
		);
		$trans_bbc2 = array(
			'{COPYRIGHT}' => $copyright,
		);

		$text = '
				<div style="clear:left;">';
		foreach ($blocks as $block)
		{
			extract($block);
			$content = strtr($content, 'bbc' == $type ? $trans_bbc1 : $trans);

			$text .= '
					';

			if (!empty($frame))
				$text .= '<div ' . $frame . '>
					';

			switch ($type)
			{
				case 'bbc':
				{
					$content = parse_bbc(censorText($content));
					$text .= strtr($content, $trans_bbc2);
					break;
				}
				case 'php':
				{
					$content = trim($content);

					if ('<?php' == substr($content, 0, 5))
						$content = substr($content, 5);
					if ('?>' == substr($content, -2))
						$content = substr($content, 0, -2);

					ob_start();
					eval($content);
					$text .= ob_get_contents();
					ob_end_clean();
					break;
				}
				case 'html':
				default:
				{
					$text .= $content;
					break;
				}
			}

			if (!empty($frame))
				$text .= '
					</div>';
		}

		$text .= '
				</div>';
	}

	return $text;
}

?>