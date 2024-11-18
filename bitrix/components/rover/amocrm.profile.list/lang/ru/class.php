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

$MESS['rover-apl__header-NAME']         = 'Название';
$MESS['rover-apl__header-CONNECTION']   = 'Подключение';
$MESS['rover-apl__header-TYPE']         = 'Источник';
$MESS['rover-apl__header-SITE']         = 'Сайт';
$MESS['rover-apl__header-MANAGER']      = 'Ответственный';
$MESS['rover-apl__header-ACTIVE']       = 'Акт.';
$MESS['rover-apl__header-RESPONSIBLE']  = 'Ответственный';
$MESS['rover-apl__header-UNSORTED']     = '«Неразобранное»';
$MESS['rover-apl__header-LEAD']         = 'Сделка';
$MESS['rover-apl__header-CONTACT']      = 'Контакт';
$MESS['rover-apl__header-COMPANY']      = 'Компания';
$MESS['rover-apl__header-TASK']         = 'Задача';
$MESS['rover-apl__header-ELEMENTS_CNT'] = 'Результаты';



$MESS['rover-apl__action-remove']                      = 'Удалить';
$MESS['rover-apl__action-remove_title']                = 'Удалить отмеченные элементы';
$MESS['rover-apl__action-remove_success']              = 'Элементы успешно удалены';
$MESS['rover-apl__action-confirm']                     = 'Подтвердите действие для отмеченных элементов';
$MESS['rover-apl__action-open']                        = 'Открыть';
$MESS['rover-apl__action-update']                      = 'Изменить';
$MESS['rover-apl__action-update_success']              = 'Элементы успешно изменены';
$MESS['rover-apl__action-copy']                        = 'Копировать';
$MESS['rover-apl__action-elements']                    = 'Результаты';
$MESS['rover-apl__title-results']                      = 'Перейти к результатам «#preset-name#»';
$MESS['rover-apl__title-settings']                     = 'Перейти к настройкам профиля «#preset-name#»';
$MESS['rover-apl__unavailable']                        = 'n/a';
$MESS['rover-apl__no']                                 = 'Нет';
$MESS['rover-apl__title-task-unavailable']             =
    'Создание задачи невозможно, т.к. включено создание «неразобранного»';
$MESS['rover-apl__action-cancel']                      = 'Отменить';
$MESS['rover-apl__action-add']                         = 'Добавить';
$MESS['rover-apl__action-add_title']                   = 'Добавить профиль интеграции';
$MESS['rover-apl__action-add_' . Iblock::getType()]    = 'Инфоблок';
$MESS['rover-apl__action-add_' . WebForm::getType()]   = 'Веб-форму';
$MESS['rover-apl__action-add_' . PostEvent::getType()] = 'Почтовое событие';

$MESS['rover-apl__action-connections']       = 'Подключения к amoCRM (#cnt#)';
$MESS['rover-apl__action-connections_title'] = 'Список подключений к amoCRM';
$MESS['rover-apl__action-settings']          = 'Настройки модуля';
$MESS['rover-apl__action-settings_title']    = 'Настройки модуля интеграции';
$MESS['rover-apl__title']                    = 'Список профилей интеграции с amoCRM';
$MESS['rover-apl__error_no_responsible']     = 'не найден';
$MESS['rover-apl__default_responsible']      = 'по умолчанию';
$MESS['rover-apl__no-connection']            = 'Отсутствует соединение с amoCRM';
$MESS['rover-apl__yes']                      = 'Да';
$MESS['rover-apl__no']                       = 'Нет';
$MESS['rover-apl__disabled']                 = 'откл.';
$MESS['rover-acpl__profile-has-errors']      = 'Есть ошибки настройки!';
