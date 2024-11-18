<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 14.06.2017
 * Time: 13:15
 *
 * @author Pavel Shulaev (https://rover-it.me)
 */

use Rover\AmoCRM\Model\Source\Iblock;
use Rover\AmoCRM\Model\Source\PostEvent;
use Rover\AmoCRM\Model\Source\WebForm;
use Rover\AmoCRM\Service\PathService;

$MESS['rover-apl__presets']    = 'Integration profile';
$MESS['rover-apl__show']       = 'Show';
$MESS['rover-apl__all']        = 'All';
$MESS['rover-apl__check']      = 'check';
$MESS['rover-apl__activate']   = 'activate';
$MESS['rover-apl__deactivate'] = 'deactivate';
$MESS['rover-apl__delete']     = 'remove';

$MESS['rover-acpl__title']                               = 'Creating new integration profile';
$MESS['rover-acpl__connection_select']                   = 'Choose connection';
$MESS['rover-acpl__' . Iblock::getType() . '_select']    = 'Choose infoblock';
$MESS['rover-acpl__' . WebForm::getType() . '_select']   = 'Choose web form';
$MESS['rover-acpl__' . PostEvent::getType() . '_select'] = 'Choose post event';
$MESS['rover-acpl__connection_empty']                    =
    'No connections found.<br>You can add it <a target="_blank" href="' . PathService::getConnectionList() . '">here</a>';
$MESS['rover-acpl__' . Iblock::getType() . '_empty']     =
    'No infoblocks found.<br>You can add it <a target="_blank" href="' . PathService::getProfileList() . '">here</a>';
$MESS['rover-acpl__' . WebForm::getType() . '_empty']    =
    'No web forms found.<br>You can add it <a target="_blank" href="' . PathService::getProfileList() . '">here</a>';
$MESS['rover-acpl__' . PostEvent::getType() . '_empty']  =
    'No post events forms found.<br>You can add it <a target="_blank" href="' . PathService::getProfileList() . '">here</a>';
$MESS['rover-acpl__button_close']                        = 'Close';
$MESS['rover-acpl__button_add']                          = 'Add';
$MESS['rover-acpl__total']                               = 'Total:';