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

$MESS['rover-apl__presets']    = 'Профиль интеграции';
$MESS['rover-apl__show']       = 'Показать';
$MESS['rover-apl__all']        = 'Всего';
$MESS['rover-apl__check']      = 'выбрать';
$MESS['rover-apl__activate']   = 'активировать';
$MESS['rover-apl__deactivate'] = 'деактивировать';
$MESS['rover-apl__delete']     = 'удалить';

$MESS['rover-acpl__title']                               = 'Добавление нового профиля интеграции';
$MESS['rover-acpl__connection_select']                   = 'Выберите подключение';
$MESS['rover-acpl__' . Iblock::getType() . '_select']    = 'Выберите инфоблок';
$MESS['rover-acpl__' . WebForm::getType() . '_select']   = 'Выберите веб-форму';
$MESS['rover-acpl__' . PostEvent::getType() . '_select'] = 'Выберите почтовое событие';
$MESS['rover-acpl__connection_empty']                    =
    'Не найдено ни одного доступного подключения к amoCRM. <a target="_blank" href="' . PathService::getConnectionList() . '">Перейти к списку</a>.';
$MESS['rover-acpl__' . Iblock::getType() . '_empty']     =
    'Не найдено ни одного инфоблока. <a target="_blank" href="' . PathService::getProfileList() . '">Перейти к списку</a>';
$MESS['rover-acpl__' . WebForm::getType() . '_empty']    =
    'Не найдено ни одной веб-формы. <a target="_blank" href="' . PathService::getProfileList() . '">Перейти к списку</a>';
$MESS['rover-acpl__' . PostEvent::getType() . '_empty']  =
    'Не найдено ни одонго почтового события. <a target="_blank" href="' . PathService::getProfileList() . '">Перейти к списку</a>';
$MESS['rover-acpl__button_close']                        = 'Закрыть';
$MESS['rover-acpl__button_add']                          = 'Создать';
$MESS['rover-acpl__total']                               = 'Всего:';