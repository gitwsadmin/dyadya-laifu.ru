<?php
use AmoCRM\Helpers\EntityTypesInterface;

$MESS['rover-ape__header-' . EntityTypesInterface::LEADS]       = 'Lead';
$MESS['rover-ape__header-' . EntityTypesInterface::CONTACTS]    = 'Contact';
$MESS['rover-ape__header-' . EntityTypesInterface::COMPANIES]   = 'Company';
$MESS['rover-ape__header-' . EntityTypesInterface::TASKS]       = 'Task';

$MESS['rover-ape__export-success']          = 'Elements successfully exported';
$MESS['rover-ape__export-success-2']        = 'Items have been successfully added to the integration queue';

$MESS['rover-ape__action-remove']           = 'Remove';
$MESS['rover-ape__action-export']           = 'Send to amoCRM';
$MESS['rover-ape__action-remove_title']     = 'Remove selected elements';
$MESS['rover-ape__action-confirm']          = 'Confirm action for checked elements';
$MESS['rover-ape__action-elements']         = 'Elements';
$MESS['rover-ape__action-cancel']           = 'Cancel';
$MESS['rover-ape__action-presets']          = 'Go to the results';
$MESS['rover-ape__action-presets_title']    = 'Go to the results of another integration profile';
$MESS['rover-ape__action-back']             = 'To the list of profiles (#cnt#)';
$MESS['rover-ape__action-back_title']       = 'Return to the list of integration profiles';
$MESS['rover-ape__title']                   = 'Results for source В«#name#В» [#type#]';
$MESS['rover-ape__action-settings']         = 'Setting the integration profiles';
$MESS['rover-ape__action-settings_title']   = 'Setting the integration profiles';
$MESS['rover-ape__action-module-settings']         = 'Module settings';
$MESS['rover-ape__action-module-settings_title']   = 'Module settings';
$MESS['rover-ape__no-connection']           = 'There is no connection with amoCRM';
$MESS['rover-ape__datetime_created']        = 'Created';
$MESS["rover-ape__action-export-disabled"]  = "To transfer the results, you must enable the integration in <a href='/bitrix/admin/settings.php?lang=" . LANGUAGE_ID . "&mid=rover.amocrm&mid_menu=1'>the module settings</a>";