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

if (!defined('SMF'))
	die('Hacking attempt...');

// Adds admin areas.
function cb_admin_areas(&$admin_areas)
{
	global $txt;

	$admin_areas['config']['areas']['modsettings']['subsections']['cb'] = array($txt['cb']);
}

// Adds modify modifications.
function cb_modify_modifications(&$subActions)
{
	$subActions['cb'] = 'cbSettingsShow';
	$subActions['cbedit'] = 'cbSettingsEdit';
}

// Gets list of settings of position.
function cbGetListSettings($pos, $start, $items_per_page, $sort)
{
	if (empty($pos) || !is_numeric($start) || empty($items_per_page) || empty($sort))
		return false;

	$list = array();

	$cb_settings = cbGetSettings($pos, 'all');

	if (!empty($cb_settings))
	{
		// Applies $sort.
		$sort_array = explode(' ', $sort);
		if (1 == count($sort_array))
			$sort_array[] = 'ASC';

		$flag_order = 'DESC' == strtoupper($sort_array[1]) ? SORT_DESC : SORT_ASC;
		$cb_settings = cb_array_column_sort($cb_settings, $sort_array[0], $flag_order, SORT_REGULAR);

		// Reindex.
		$cb_settings2 = $cb_settings;
		$cb_settings = array();
		foreach ($cb_settings2 as $row)
			$cb_settings[] = $row;

		// Paginate.
		$lim = min(count($cb_settings), $start + $items_per_page);
		for ($i = $start; $i < $lim; $i++)
			$list[] = $cb_settings[$i];
	}

	return $list;
}

// Gets size of list of settings of position
function cbGetListSettingsSize($pos)
{
	if (empty($pos))
		return false;

	$cb_settings = cbGetSettings($pos, 'all');

	return count($cb_settings);
}

// Gets list of settings of above the header.
function cbGetListSettingsHeaderAbove($start, $items_per_page, $sort)
{
	return cbGetListSettings('header_above', $start, $items_per_page, $sort);
}

// Gets size of list of settings of above the header.
function cbGetListSettingsHeaderAboveSize()
{
	return cbGetListSettingsSize('header_above');
}

// Gets list of settings of header.
function cbGetListSettingsHeader($start, $items_per_page, $sort)
{
	return cbGetListSettings('header', $start, $items_per_page, $sort);
}

// Gets size of list of settings of header.
function cbGetListSettingsHeaderSize()
{
	return cbGetListSettingsSize('header');
}

// Gets list of settings of below the header.
function cbGetListSettingsHeaderBelow($start, $items_per_page, $sort)
{
	return cbGetListSettings('header_below', $start, $items_per_page, $sort);
}

// Gets size of list of settings of below the header.
function cbGetListSettingsHeaderBelowSize()
{
	return cbGetListSettingsSize('header_below');
}

// Gets list of settings of above the footer.
function cbGetListSettingsFooterAbove($start, $items_per_page, $sort)
{
	return cbGetListSettings('footer_above', $start, $items_per_page, $sort);
}

// Gets size of list of settings of above the footer.
function cbGetListSettingsFooterAboveSize()
{
	return cbGetListSettingsSize('footer_above');
}

// Gets list of settings of footer.
function cbGetListSettingsFooter($start, $items_per_page, $sort)
{
	return cbGetListSettings('footer', $start, $items_per_page, $sort);
}

// Gets size of list of settings of footer.
function cbGetListSettingsFooterSize()
{
	return cbGetListSettingsSize('footer');
}

// Gets list of settings of below the footer.
function cbGetListSettingsFooterBelow($start, $items_per_page, $sort)
{
	return cbGetListSettings('footer_below', $start, $items_per_page, $sort);
}

// Gets size of list of settings of below the footer.
function cbGetListSettingsFooterBelowSize()
{
	return cbGetListSettingsSize('footer_below');
}

// Admin area settings (edit).
function cbSettingsEdit($return_config = false)
{
	global $txt, $context, $smcFunc, $sourcedir, $modSettings;

	if ($return_config)
		return array();

	loadTemplate('CustomBlocks');

	// Sort out the context!
	$context['pos'] = isset($_GET['pos']) ? $_GET['pos'] : 'header';
	$context['id'] = isset($_GET['id']) ? (int) $_GET['id'] : 0;
	$context['settings_title'] = $context['id'] ? $txt['cb_edit_block'] : $txt['cb_add_' . $context['pos'] . '_title'];
	$start = isset($_GET[$context['pos'] . '_start']) ? (int) $_GET[$context['pos'] . '_start'] : 0;
	$sort = isset($_GET[$context['pos'] . '_sort']) ? $_GET[$context['pos'] . '_sort'] : 'order';
	$context['params_url'] = ';' . $context['pos'] . '_start=' . $start . ';' . $context['pos'] . '_sort=' . $sort . (isset($_GET[$context['pos'] . '_desc']) ? ';' . $context['pos'] . '_desc' : '');
	$context[$context['admin_menu_name']]['current_subsection'] = 'cb';
	$context['sub_template'] = 'cb_settings_edit';

	if ($context['id'])
		$context['params'] = cbGetSettings($context['pos'], $context['id']);

	// Setup the default values as needed.
	if (empty($context['params']))
		$context['params'] = cbGetSettings($context['pos']);

	// Others data.
	$context['params']['frame'] = $smcFunc['htmlspecialchars']($context['params']['frame'], ENT_QUOTES);
	$context['params']['content'] = $smcFunc['htmlspecialchars']($context['params']['content'], ENT_QUOTES);
	$context['params']['permissions'] = !empty($context['params']['permissions']) ? explode(',', $context['params']['permissions']) : array();

	$pos_array = array('header_above', 'header', 'header_below', 'footer_above', 'footer', 'footer_below');
	$context['params']['pos_array'] = array();
	foreach ($pos_array as $pos)
		$context['params']['pos_array'][$pos] = $txt['cb_pos_' . $pos];

	$type_array = array('html', 'bbc', 'php');
	$context['params']['type_array'] = array();
	foreach ($type_array as $type)
		$context['params']['type_array'][$type] = $txt['cb_type_' . $type];

	$context['params']['permissions_users'] = array(
		'guest' => $txt['guest'],
		'user' => $txt['user'],
		'localmod' => $txt['cb_local_moderator'],
		'globalmod' => $txt['cb_global_moderator'],
		'admin' => $txt['cb_administrator'],
	);
	$context['params']['permissions_actions'] = array(
		'view' => $txt['view'],
	);

	// Are we saving?
	if (isset($_POST['save']))
	{
		checkSession('get');

		// Validate inputs.
		$_POST['description'] = $smcFunc['htmlspecialchars']($_POST['description'], ENT_QUOTES);

		$pos_new = $context['id'] && $context['pos'] != $_POST['pos_new'] ? $_POST['pos_new'] : '';
		$order = isset($_POST['order']) ? (int) $_POST['order'] : 0;
		$permissions = !empty($_POST['permissions']) ? implode(',', $_POST['permissions']) : '';
		$active = isset($_POST['active']) ? 1 : 0;

		// Do the insertion/updates.
		if (!empty($pos_new))
		{
			cbDeleteSettings('cb\\_' . $context['pos'] . '\\_' . $context['id'] . '\\_%');

			if (!empty($modSettings['cb_' . $context['pos'] . '_ids']))
			{
				$ids = explode(',', $modSettings['cb_' . $context['pos'] . '_ids']);
				$key = array_search($context['id'], $ids);
				if (is_numeric($key))
					unset($ids[$key]);

				$setArray = array();
				$setArray['cb_' . $context['pos'] . '_ids'] = implode(',', $ids);
				cbUpdateSettings($setArray);
			}


			$context['id'] = 0;
			$context['pos'] = $pos_new;
		}

		$params = array_keys(cbGetSettings($context['pos']));

		if ($context['id'])
			$id2 = $context['id'];
		else
		{
			$id_new = 1;
			$ids = empty($modSettings['cb_' . $context['pos'] . '_ids']) ? array() : explode(',', $modSettings['cb_' . $context['pos'] . '_ids']);
			while (in_array($id_new, $ids))
				$id_new++;
			$id2 = $id_new;
		}

		$setArray = array();
		if (!$context['id'])
		{
			$ids[] = $id_new;
			sort($ids);
			$setArray['cb_' . $context['pos'] . '_ids'] = implode(',', $ids);
		}
		$pos_id2 = $context['pos'] . '_' . $id2;
		foreach ($params as $param)
			$setArray['cb_' . $pos_id2 . '_' . $param] = in_array($param, array('order', 'permissions', 'active')) ? $$param : $_POST[$param];
		cbUpdateSettings($setArray);
	}
	// Deleting?
	elseif (isset($_GET['delete']) && $context['id'])
	{
		checkSession('get');

		cbDeleteSettings('cb\\_' . $context['pos'] . '\\_' . $context['id'] . '\\_%');

		if (!empty($modSettings['cb_' . $context['pos'] . '_ids']))
		{
			$ids = explode(',', $modSettings['cb_' . $context['pos'] . '_ids']);
			$key = array_search($context['id'], $ids);
			if (is_numeric($key))
				unset($ids[$key]);

			$setArray = array();
			$setArray['cb_' . $context['pos'] . '_ids'] = implode(',', $ids);
			cbUpdateSettings($setArray);
		}
	}

	if (isset($_GET['delete']) || isset($_POST['save']))
	{
		checkSession('get');
		redirectexit('action=admin;area=modsettings;sa=cb' . $context['params_url']);
	}
}

// Admin area settings (show).
function cbSettingsShow($return_config = false)
{
	global $txt, $scripturl, $context, $sourcedir;

	$pos_array = array(
		'header_above' => 'HeaderAbove',
		'header' => 'Header',
		'header_below' => 'HeaderBelow',
		'footer_above' => 'FooterAbove',
		'footer' => 'Footer',
		'footer_below' => 'FooterBelow',
	);

	if ($return_config)
	{
		$cb_settings = cbGetSettings('');
		$config_vars = array();
		foreach ($pos_array as $pos => $infix)
		{
			foreach ($cb_settings as $param => $v)
				$config_vars[] = array('text', 'cb_' . $pos . '_' . $param);
		}

		return $config_vars;
	}

	loadTemplate('CustomBlocks');
	require_once ($sourcedir . '/Subs-List.php');

	$context['sub_template'] = 'cb_settings_show';
	$context['settings_title'] = $txt['cb'];

	// Headers & footers list.
	foreach ($pos_array as $pos => $infix)
	{
		$start = isset($_REQUEST[$pos . '_start']) ? (int) $_REQUEST[$pos . '_start'] : 0;
		$sort = isset($_REQUEST[$pos . '_sort']) ? $_REQUEST[$pos . '_sort'] : 'order';
		$params_url = ';' . $pos . '_start=' . $start . ';' . $pos . '_sort=' . $sort . (isset($_REQUEST[$pos . '_desc']) ? ';' . $pos . '_desc' : '');

		$listOptions = array(
			'id' => 'cb_settings_show_' . $pos,
			'title' => $txt['cb_' . $pos],
			'base_href' => $scripturl . '?action=admin;area=modsettings;sa=cb',
			'default_sort_col' => 'order',
			'request_vars' => array(
				'sort' => $pos . '_sort',
				'desc' => $pos . '_desc',
			),
			'start_var_name' => $pos . '_start',
			'no_items_label' => $txt['cb_none_' . $pos],
			'items_per_page' => 25,
			'get_items' => array(
				'function' => 'cbGetListSettings' . $infix,
			),
			'get_count' => array(
				'function' => 'cbGetListSettings' . $infix . 'Size',
			),
			'columns' => array(
				'order' => array(
					'header' => array(
						'value' => $txt['cb_order'],
						'style' => 'width: 5%;',
					),
					'data' => array(
						'db' => 'order',
						'style' => 'text-align: center;',
					),
					'sort' => array(
						'default' => 'order',
						'reverse' => 'order DESC',
					),
				),
				'description' => array(
					'header' => array(
						'value' => $txt['cb_description'],
						'style' => 'text-align: left;',
					),
					'data' => array(
						'function' => create_function('$rowData', '
							return nl2br($rowData[\'description\']);
						'),
					),
					'sort' => array(
						'default' => 'description',
						'reverse' => 'description DESC',
					),
				),
				'type' => array(
					'header' => array(
						'value' => $txt['cb_type'],
						'style' => 'width: 30%; text-align: left;',
					),
					'data' => array(
						'function' => create_function('$rowData', '
							global $txt;

							$textKey = \'cb_type_\' . $rowData[\'type\'];
							return isset($txt[$textKey]) ? $txt[$textKey] : $textKey;
						'),
					),
					'sort' => array(
						'default' => 'type',
						'reverse' => 'type DESC',
					),
				),
				'active' => array(
					'header' => array(
						'value' => $txt['cb_active'],
						'style' => 'width: 8%;',
					),
					'data' => array(
						'function' => create_function('$rowData', '
							global $txt;

							return $rowData[\'active\'] ? $txt[\'yes\'] : $txt[\'no\'];
						'),
						'style' => 'text-align: center;',
					),
					'sort' => array(
						'default' => 'active DESC',
						'reverse' => 'active',
					),
				),
				'option_modify' => array(
					'header' => array(
						'style' => 'width: 10%;',
					),
					'data' => array(
						'sprintf' => array(
							'format' => '<a href="' . $scripturl . '?action=admin;area=modsettings;sa=cbedit;pos=' . $pos . ';id=%1$s' . $params_url . '">' . $txt['modify'] . '</a>',
							'params' => array(
								'id' => false,
							),
						),
						'style' => 'text-align: center;',
					),
				),
				'option_delete' => array(
					'header' => array(
						'style' => 'width: 10%;',
					),
					'data' => array(
						'sprintf' => array(
							'format' => '<a href="' . $scripturl . '?action=admin;area=modsettings;sa=cbedit;pos=' . $pos . ';delete=1;id=%1$s' . $params_url . ';' . $context['session_var'] . '=' . $context['session_id'] . '" onclick="return confirm(\'' .$txt['cb_edit_delete_' . $pos . '_sure'] . '\');">' . $txt['delete'] . '</a>',
							'params' => array(
								'id' => false,
							),
						),
						'style' => 'text-align: center;',
					),
				),
			),
			'form' => array(
				'href' => $scripturl . '?action=admin;area=modsettings;sa=cbedit;pos=' . $pos . $params_url,
				'name' => 'cb' . $infix,
			),
			'additional_rows' => array(
				array(
					'position' => 'after_title',
					'value' => $txt['cb_' . $pos . '_desc'],
				),
				array(
					'position' => 'below_table_data',
					'value' => '<input type="submit" name="new" value="' . $txt['cb_make_new_' . $pos] . '" class="button_submit" />',
					'style' => 'text-align: right;',
				),
			),
		);
		createList($listOptions);
	}
}

// Updates settings.
function cbUpdateSettings($setArray)
{
	global $smcFunc;

	if (empty($setArray))
		return false;

	$replaceArray = array();
	foreach ($setArray as $variable => $value)
		$replaceArray[] = array($variable, $value);

	$smcFunc['db_insert']('replace',
		'{db_prefix}settings',
		array('variable' => 'string-255', 'value' => 'string-65534'),
		$replaceArray,
		array('variable')
	);

	// Kill the cache - it needs redoing now, but we won't bother ourselves with that here.
	cache_put_data('modSettings', null, 90);
}

// Deletes settings.
function cbDeleteSettings($filter)
{
	global $smcFunc;

	if (empty($filter))
		return false;

	$smcFunc['db_query']('', '
		DELETE FROM {db_prefix}settings
		WHERE variable LIKE {string:filter}',
		array(
			'filter' => $filter,
		)
	);

	// Kill the cache - it needs redoing now, but we won't bother ourselves with that here.
	cache_put_data('modSettings', null, 90);
}

/**
 * array_column_sort
 *
 * function to sort an "arrow of rows" by its columns
 * exracts the columns to be sorted and then
 * uses eval to flexibly apply the standard
 * array_multisort function
 *
 * uses a temporary copy of the array whith "_" prefixed to the keys
 * this makes sure that array_multisort is working with an associative
 * array with string type keys, which in turn ensures that the keys
 * will be preserved.
 *
 * TODO: find a way of modifying the keys of $array directly, without using
 * a copy of the array.
 *
 * flexible syntax:
 * $new_array = array_column_sort($array [, 'col1' [, SORT_FLAG [, SORT_FLAG]]]...);
 *
 * original code credited to Ichier (www.ichier.de) here:
 * http://uk.php.net/manual/en/function.array-multisort.php
 *
 * prefixing array indeces with "_" idea credit to steve at mg-rover dot org, also here:
 * http://uk.php.net/manual/en/function.array-multisort.php
 *
 */
function cb_array_column_sort() {
	$args = func_get_args();
	$array = array_shift($args);

	// make a temporary copy of array for which will fix the
	// keys to be strings, so that array_multisort() doesn't
	// destroy them
	$array_mod = array();
	foreach ($array as $key => $value) {
		$array_mod['_' . $key] = $value;
	}

	$i = 0;
	$multi_sort_line = "return array_multisort(";
	foreach ($args as $arg) {
		$i++;
		if (is_string($arg)) {
			foreach ($array_mod as $row_key => $row) {
				$sort_array[$i][] = $row[$arg];
			}
		} else {
			$sort_array[$i] = $arg;
		}
		$multi_sort_line .= "\$sort_array[" . $i . "], ";
	}
	$multi_sort_line .= "\$array_mod);";

	eval($multi_sort_line);

	// now copy $array_mod back into $array, stripping off the "_"
	// that we added earlier.
	$array = array();
	foreach ($array_mod as $key => $value) {
		$array[substr($key, 1)] = $value;
	}
	return $array;
}

?>