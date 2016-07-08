<?php
/**********************************************************************************
* CustomBBCodeAdmin.english.php - Custom BBCode Manager mod english language file
***********************************************************************************
* This mod is licensed under the 2-clause BSD License, which can be found here:
*	http://opensource.org/licenses/BSD-2-Clause
***********************************************************************************
* This program is distributed in the hope that it is and will be useful, but
* WITHOUT ANY WARRANTIES; without even any implied warranty of MERCHANTABILITY
* or FITNESS FOR A PARTICULAR PURPOSE.
**********************************************************************************/

$txt['CustomBBCode_List_Title'] = 'Custom BBCodes';

// All strings used in the List Custom BBCodes template:
$txt['List_Header_Desc'] = 'Here you can manage all of your custom BBCodes.';
$txt['List_no_tags'] = 'No tags defined';
$txt['List_name'] = 'BBCode Usage';
$txt['List_tag'] = 'Tag name';
$txt['List_description'] = 'Tag Description for Button';
$txt['List_button'] = 'Button';	
$txt['List_modify'] = 'modify';
$txt['List_delete'] = 'delete';
$txt['List_enable'] = 'enable';
$txt['List_disable'] = 'disable';
$txt['List_actions'] = 'Actions';
$txt['List_add'] = 'Create New Tag';
$txt['List_delete_bbcode'] = 'Are you sure you want to delete this BBCode?';
$txt['List_simple'] = 'Create Simple Tag';
$txt['List_Status'] = 'Status';

// All strings used in the Edit Custom BBCode template:
$txt['Edit_title'] = 'Custom BBCode Settings';
$txt['Edit_name'] = 'BBCode';
$txt['Edit_description'] = $txt['List_description'];
$txt['Edit_type'] = 'Tag type';
$txt['Edit_tag'] = 'tag';
$txt['Edit_tag_format'] = 'Format bbcode will use:';
$txt['Edit_content'] = 'parsed content';
$txt['Edit_uncontent'] = 'unparsed content';
$txt['Edit_data'] = 'parsed content';
$txt['Edit_trim'] = 'Trim whitespace';
$txt['Edit_trim_no'] = '(no trim)';
$txt['Edit_trim_in'] = 'inside';
$txt['Edit_trim_out'] = 'outside';
$txt['Edit_trim_both'] = 'both';
$txt['Edit_block_level'] = 'Block level';
$txt['Edit_html'] = 'The HTML used for the tag:';
$txt['Edit_test_bbcode'] = 'Test';
$txt['Edit_button'] = 'Button for Tag';
$txt['Edit_remove'] = 'Remove Button';
$txt['Edit_remove_confirm'] = 'Are you sure you want to remove the button for this tag?';
$txt['Edit_show_button'] = 'Show Button for Tag';
$txt['Edit_Upload_Title'] = 'Upload Tag Button';
$txt['Edit_20_Upload_description'] = 'You can represent your new custom BBCode with a button on the Editor.  It must be a .GIF file that is 23x22 in size, with a transparent background.  It can be animated.  Maximum size is 10kb.';
$txt['Edit_21_Upload_description'] = 'You can represent your new custom BBCode with a button on the Editor.  It must be a .PNG file that is 16x16 in size, with a transparent background.  It can be animated.  Maximum size is 10kb.';
$txt['Edit_Filename'] = 'FileName:';
$txt['Edit_Upload'] = 'Upload';
$txt['Edit_test'] = 'Input Mask to test against:';
$txt['bbcode_exists'] = 'The bbcode you are attempting to insert already exists.';
$txt['upload_file'] = 'Upload';
$txt['remove_file'] = 'Remove';
$txt['clear_file'] = 'Clear';
$txt['Edit_Preview'] = 'Preview of BBCode button';
$txt['Edit_accept_urls'] = 'Assume <strong>{content}</strong> is a URLs?';

// DO NOT change the {content} or {option} text, as they are required by the mod!
$txt['Edit_subtext0'] = '{content} is replaced with the content of the tags.';
$txt['Edit_subtext1'] = '{content} (or $1) is replaced with the content of the tags.<br />{option1} (or $2) are replaced for 1st option.<br/>{option2} (or $3), {option3} (or $4), etc for more options.';
$txt['Edit_subtext2'] = '{option1} (or $1) are replaced for 1st option.<br/>{option2} (or $2), {option3} (or $3), etc for more options.';

// Description of the bbcode types...
$txt['parsed_content'] = 'Parsed Content';
$txt['unparsed_equals'] = 'Unparsed Equals';
$txt['parsed_equals'] = 'Parsed Equals';
$txt['unparsed_content'] = 'Unparsed Content';
$txt['closed'] = 'Closed';
$txt['unparsed_commas'] = 'Unparsed Commas';
$txt['unparsed_commas_content'] = 'Unparsed Commas Content';
$txt['unparsed_equals_content'] = 'Unparsed Equals Content';

// Version update information:
$txt['bbc_new_version'] = 'Custom BBCode Manager mod version %s is available for download!';
$txt['bbc_no_update'] = 'Your install of Custom BBCode Manager is up to date!';

// Upload error messages:
$txt['Edit_no_file_error'] = 'No file specified to upload!';
$txt['Edit_upload_error'] = 'An error occurred during the file upload.';
$txt['Edit_extension_error'] = 'Your button was rejected because it did not have a %s extension.';
$txt['Edit_mime_type_error'] = 'Your button was rejected because wrong mime type was detected.';
$txt['Edit_file_size_error'] = 'Your button was rejected because it was more than 10kb.';
$txt['Edit_width_error'] = 'Your button was rejected because it is not %d pixels in width.';
$txt['Edit_height_error'] = 'Your button was rejected because it is not %d pixels in height.';
$txt['Edit_single_content_error'] = 'You have more than one {content} marker in your replacement HTML.\nThe parser will only replace the {content} marker once, due to how the parser is built.\n\nContinue?';
$txt['Edit_no_content_error'] = 'You have at least one {content} marker in your replacement HTML.\nThe parser will not replace the {content} marker, due to how the parser is built.\n\nContinue?';

?>