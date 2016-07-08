<?php
/**********************************************************************************
* CustomBBCodeAdmin.Dutch.php - Custom BBCode Manager mod Dutch language file
***********************************************************************************
* This mod is licensed under the 2-clause BSD License, which can be found here:
*	http://opensource.org/licenses/BSD-2-Clause
***********************************************************************************
* This program is distributed in the hope that it is and will be useful, but
* WITHOUT ANY WARRANTIES; without even any implied warranty of MERCHANTABILITY
* or FITNESS FOR A PARTICULAR PURPOSE.
**********************************************************************************/

$txt['CustomBBCode_List_Title'] = 'Aangepaste BBCodes';

// All strings used in the List Custom BBCodes template:
$txt['List_Header_Desc'] = 'Hier kunt U al Uw eigen BBCodes beheren.';
$txt['List_no_tags'] = 'geen tags toegewezen';
$txt['List_name'] = 'BBCode Gebruik';
$txt['List_tag'] = 'Tag Naam';
$txt['List_description'] = 'Tag beschrijving knop';
$txt['List_button'] = 'knop';	
$txt['List_modify'] = 'Aanpassen';
$txt['List_delete'] = 'verwijderen';
$txt['List_enable'] = 'Inschakelen';
$txt['List_disable'] = 'Uitschakelen';
$txt['List_actions'] = 'Toepassingen';
$txt['List_add'] = 'Maak een nieuwe Tag';
$txt['List_delete_bbcode'] = 'weet U zeker dat U deze BBCode wilt verwijderen?';
//$txt['List_simple'] = 'Create Simple Tag';
//$txt['List_Status'] = 'Status';

// All strings used in the Edit Custom BBCode template:
$txt['Edit_title'] = 'Eigen BBCode Instellingen';
$txt['Edit_name'] = 'BBCode';
$txt['Edit_description'] = $txt['List_description'];
$txt['Edit_type'] = 'Tag tiepe';
$txt['Edit_tag'] = 'tag';
//$txt['Edit_tag_format'] = 'Format bbcode will use:';
$txt['Edit_content'] = 'geparseerde Inhoud';
$txt['Edit_uncontent'] = 'ongeparseerde Inhoud';
$txt['Edit_data'] = 'geparseerde Inhoud';
$txt['Edit_trim'] = 'Trim witruimte';
$txt['Edit_trim_no'] = '(geen trim)';
$txt['Edit_trim_in'] = 'Binnen';
$txt['Edit_trim_out'] = 'Buiten';
$txt['Edit_trim_both'] = 'Beide';
$txt['Edit_block_level'] = 'Blokkeer level';
$txt['Edit_html'] = 'De HTML gebruikt voor de tag:';
$txt['Edit_test_bbcode'] = 'Test';
$txt['Edit_button'] = 'Knop voor Tag';
$txt['Edit_remove'] = 'Verwijder knop';
$txt['Edit_remove_confirm'] = 'Weet U zeker dat U deze knop voor de Tag wilt verwijderen?';
$txt['Edit_show_button'] = 'Laat knop zien voor deze Tag';
$txt['Edit_Upload_Title'] = 'Upload Tag knop';
$txt['Edit_20_Upload_description'] = 'U kunt Uw nieuwe BBCode laten weergeven met een knop in de Editor. Dit moet een .GIF afbeelding zijn van 23x22px met een transparante achtergrond. Het mag een animati zijn. Maximale groote is 10kb.';
$txt['Edit_21_Upload_description'] = 'U kunt Uw nieuwe BBCode laten weergeven met een knop in de Editor. Dit moet een .PNG afbeelding zijn van 16x16px met een transparante achtergrond. Het mag een animati zijn. Maximale groote is 10kb.';
$txt['Edit_Filename'] = 'Bestandsnaam:';
$txt['Edit_Upload'] = 'Upload';
$txt['Edit_test'] = 'Input Mask to test against:';
$txt['bbcode_exists'] = 'De BBCode die U probeerd toe te voegen bestaad al.';
//$txt['upload_file'] = 'Upload';
//$txt['remove_file'] = 'Remove';
//$txt['clear_file'] = 'Clear';
//$txt['Edit_Preview'] = 'Preview of BBCode button';
//$txt['Edit_accept_urls'] = 'Assume <strong>{content}</strong> is a URLs?';

// DO NOT change the {content} or {option} text, as they are required by the mod!
$txt['Edit_subtext0'] = '{content} Word vervangen met de inhoud van de Tags.';
$txt['Edit_subtext1'] = '{content} (or $1) Word vervangen met de inhoud van de Tags.<br />{option1} (or $2) Worden vervangen voor de eerste optie.<br/>{option2} (or $3), {option3} (or $4), etc voor meer opties.';
$txt['Edit_subtext2'] = '{option1} (or $1) Word vevangen voor eerste optie.<br/>{option2} (or $2), {option3} (or $3), etc voor meer opties.';

// Description of the bbcode types...
$txt['parsed_content'] = 'Geparseerde Inhoud';
$txt['unparsed_equals'] = 'Ongeparseerde Gelijken';
$txt['parsed_equals'] = 'Geparseerde Gelijken';
$txt['unparsed_content'] = 'Ongeparseerde Inhoud';
$txt['closed'] = 'Gesloten';
$txt['unparsed_commas'] = 'Ongeparseerde Commas';
$txt['unparsed_commas_content'] = 'Ongeparseerde Commas Inhoud';
$txt['unparsed_equals_content'] = 'Ongeparseerde Gelijken Inhoud';

// Version update information:
//$txt['bbc_new_version'] = 'Custom BBCode Manager mod version %s is available for download!';
//$txt['bbc_no_update'] = 'Your install of Custom BBCode Manager is up to date!';

// Upload error messages:
//$txt['Edit_no_file_error'] = 'No file specified to upload!';
//$txt['Edit_upload_error'] = 'An error occurred during the file upload.';
//$txt['Edit_extension_error'] = 'Your button was rejected because it did not have a %s extension.';
//$txt['Edit_mime_type_error'] = 'Your button was rejected because wrong mime type was detected.';
//$txt['Edit_file_size_error'] = 'Your button was rejected because it was more than 10kb.';
//$txt['Edit_width_error'] = 'Your button was rejected because it is not %d pixels in width.';
//$txt['Edit_height_error'] = 'Your button was rejected because it is not %d pixels in height.';
//$txt['Edit_single_content_error'] = 'You have more than one {content} marker in your replacement HTML.\nThe parser will only replace the {content} marker once, due to how the parser is built.\n\nContinue?';
//$txt['Edit_no_content_error'] = 'You have at least one {content} marker in your replacement HTML.\nThe parser will not replace the {content} marker, due to how the parser is built.\n\nContinue?';

?>