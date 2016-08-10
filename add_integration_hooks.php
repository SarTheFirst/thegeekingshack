<?php

/* Integration hook installation script */

if (file_exists(dirname(__FILE__) . '/SSI.php') && !defined('SMF'))
	require_once(dirname(__FILE__) . '/SSI.php');

elseif (!defined('SMF'))
	exit('<b>Error:</b> Cannot install - please verify you put this in the same place as SMF\'s index.php.');
		
$hooks = array(
	'integrate_pre_include' => '$sourcedir/staff_codes.php',
	'integrate_bbc_codes' => 'get_staff_codes'
);

foreach ($hooks as $hook => $function)
	add_integration_function($hook, $function, TRUE);

exit('Integration hooks successfully installed.');

?>