<?php
// Version: 2.0; Modifications

// BEGIN MOD CustomBlocks
$txt['cb'] = 'Custom blocks';
$txt['cb_desc'] = 'Administration panel for the &quot;Custom blocks&quot; MOD';
$txt['cb_position'] = 'Position';
$txt['cb_description'] = 'Description';
$txt['cb_frame'] = 'Frame';
$txt['cb_frame_desc'] = 'Attributes for <tt>DIV</tt> tag which skirts the content.<br />
Example: <tt>style=&quot;float: left; width: 250px; height: 100px;&quot;</tt>';
$txt['cb_type'] = 'Type';
$txt['cb_type_html'] = 'HTML';
$txt['cb_type_bbc'] = 'BBC';
$txt['cb_type_php'] = 'PHP';
$txt['cb_content'] = 'Content';
$txt['cb_content_footer_desc'] = 'One block of forum footer should contain the copyright.';
$txt['cb_order'] = 'Order';
$txt['cb_permissions'] = 'Permissions';
$txt['cb_permissions_desc'] = 'Actions that a user can perform over this block, subject to the permissions of the forum.';
$txt['cb_local_moderator'] = 'Local moderator';
$txt['cb_global_moderator'] = 'Global moderator';
$txt['cb_administrator'] = 'Administrator';
$txt['cb_active'] = 'Active';
$txt['cb_active_desc'] = 'If not selected this block will not be shown to anyone.';
$txt['cb_edit_block'] = 'Edit block';
$cb_pos_array = array(
	'header_above' => array(
		'above the forum header',
		'will be displayed above the forum header',
		'above the forum header',
		'Above the forum header',
	),
	'header' => array(
		'in the forum header',
		'will replace all the forum header',
		'of the forum header',
		'In the forum header',
	),
	'header_below' => array(
		'below the forum header',
		'will be displayed below the forum header',
		'below the forum header',
		'Below the forum header',
	),
	'footer_above' => array(
		'above the forum footer',
		'will be displayed above the forum footer',
		'above the forum footer',
		'Above the forum footer',
	),
	'footer' => array(
		'in the forum footer',
		'will replace all the forum footer. One block should contain the copyright',
		'of the forum footer',
		'In the forum footer. One block should contain the copyright',
	),
	'footer_below' => array(
		'below the forum footer',
		'will displayed below the forum footer',
		'below the forum footer',
		'Below the forum footer',
	),
);
foreach ($cb_pos_array as $cb_pos => $cb_text)
{
	$txt['cb_' . $cb_pos] = 'Blocks ' . $cb_text[0];
	$txt['cb_' . $cb_pos . '_desc'] = 'The blocks ' . $cb_text[1] . '.';
	$txt['cb_none_' . $cb_pos] = 'You have not created any blocks ' . $cb_text[0] . ' yet!';
	$txt['cb_make_new_' . $cb_pos] = 'New block ' . $cb_text[0];
	$txt['cb_add_' . $cb_pos . '_title'] = 'Add block ' . $cb_text[0];
	$txt['cb_edit_delete_' . $cb_pos . '_sure'] = 'Are you sure you wish to delete this block ' . $cb_text[2] . '?';
	$txt['cb_pos_' . $cb_pos] = $cb_text[3];
}
// END MOD CustomBlocks

?>