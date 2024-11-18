<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 14.06.2017
 * Time: 11:47
 *
 * @author Pavel Shulaev (https://rover-it.me)
 */

use Rover\AmoCRM\Model\Source\Iblock;
use Rover\AmoCRM\Model\Source\PostEvent;
use Rover\AmoCRM\Model\Source\WebForm;

$MESS['rover-apl__header-NAME']         = 'Name';
$MESS['rover-apl__header-CONNECTION']   = 'Connection';
$MESS['rover-apl__header-TYPE']         = 'Source';
$MESS['rover-apl__header-SITE']         = 'Site';
$MESS['rover-apl__header-MANAGER']      = 'Responsible';
$MESS['rover-apl__header-ACTIVE']       = 'Active';
$MESS['rover-apl__header-UNSORTED']     = 'В«UnsortedВ»';
$MESS['rover-apl__header-RESPONSIBLE']  = 'Responsible';
$MESS['rover-apl__header-LEAD']         = 'Lead';
$MESS['rover-apl__header-CONTACT']      = 'Contact';
$MESS['rover-apl__header-COMPANY']      = 'Company';
$MESS['rover-apl__header-TASK']         = 'Task';
$MESS['rover-apl__header-ELEMENTS_CNT'] = 'Results';

$MESS['rover-apl__action-remove']                      = 'Remove';
$MESS['rover-apl__action-remove_title']                = 'Delete checked elements';
$MESS['rover-apl__action-remove_success']              = 'Elements successfully deleted';
$MESS['rover-apl__action-confirm']                     = 'Confirm action for checked elements';
$MESS['rover-apl__action-open']                        = 'Open';
$MESS['rover-apl__action-update']                      = 'Update';
$MESS['rover-apl__action-update_success']              = 'Elements successfully updated';
$MESS['rover-apl__action-copy']                        = 'Copy';
$MESS['rover-apl__action-elements']                    = 'Results';
$MESS['rover-apl__title-results']                      = 'Go to the results В«#preset-name#В»';
$MESS['rover-apl__title-settings']                     = 'Go to profile settings В«#preset-name#В»';
$MESS['rover-apl__unavailable']                        = 'n/a';
$MESS['rover-apl__no']                                 = 'No';
$MESS['rover-apl__title-task-unavailable']             =
    'Creating a task is impossible, because included the creation of "unsorted"';
$MESS['rover-apl__action-cancel']                      = 'Cancel';
$MESS['rover-apl__action-add']                         = 'Add';
$MESS['rover-apl__action-add_title']                   = 'Add integration profile';
$MESS['rover-apl__action-add_' . Iblock::getType()]    = 'Iblock';
$MESS['rover-apl__action-add_' . WebForm::getType()]   = 'Web form';
$MESS['rover-apl__action-add_' . PostEvent::getType()] = 'Post event';

$MESS['rover-apl__action-connections']       = 'AmoCRM connections (#cnt#)';
$MESS['rover-apl__action-connections_title'] = 'AmoCRM connections list';
$MESS['rover-apl__action-settings']          = 'Module settings';
$MESS['rover-apl__action-settings_title']    = 'Module settings';
$MESS['rover-apl__title']                    = 'List of profiles for integrating with amoCRM';
$MESS['rover-apl__error_no_responsible']     = 'not found';
$MESS['rover-apl__default_responsible']      = 'by default';
$MESS['rover-apl__no-connection']            = 'There is no connection with amoCRM';
$MESS['rover-apl__yes']                      = 'Yes';
$MESS['rover-apl__no']                       = 'No';
$MESS['rover-apl__disabled']                 = 'disabled';