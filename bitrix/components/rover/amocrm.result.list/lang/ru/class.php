<?php

use AmoCRM\Helpers\EntityTypesInterface;

$MESS['rover-ape__header-' . EntityTypesInterface::LEADS]       = 'Сделка';
$MESS['rover-ape__header-' . EntityTypesInterface::CONTACTS]    = 'Контакт';
$MESS['rover-ape__header-' . EntityTypesInterface::COMPANIES]   = 'Компания';
$MESS['rover-ape__header-' . EntityTypesInterface::TASKS]       = 'Задача';

$MESS['rover-ape__export-success-1']  = 'Элементы успешно перенесены в amoCRM';
$MESS['rover-ape__export-success-2']  = 'Элементы успешно добавлены в очередь на интеграцию';

$MESS['rover-ape__action-remove']           = 'Удалить';
$MESS['rover-ape__action-export']           = 'Отправить в amoCRM';
$MESS['rover-ape__action-remove_title']     = 'Удалить отмеченные элементы';
$MESS['rover-ape__action-confirm']          = 'Подтвердите действие для отмеченных элементов';
$MESS['rover-ape__action-elements']         = 'Элементы';
$MESS['rover-ape__action-cancel']           = 'Отменить';
$MESS['rover-ape__action-presets']          = 'Перейти к результатам';
$MESS['rover-ape__action-presets_title']    = 'Перейти к результатам другого профиля интеграции';
$MESS['rover-ape__action-back']             = 'К списку профилей (#cnt#)';
$MESS['rover-ape__action-back_title']       = 'Вернуться к списку профилей интеграции';
$MESS['rover-ape__title']                   = 'Результаты для источника «#name#» [#type#]';
$MESS['rover-ape__action-settings']         = 'Настройка профилей интеграции источника';
$MESS['rover-ape__action-settings_title']   = 'Настройка профилей интеграции источника';
$MESS['rover-ape__action-module-settings']         = 'Настройки';
$MESS['rover-ape__action-module-settings_title']   = 'Настройки модуля интеграции';
$MESS['rover-ape__no-connection']           = 'Отсутствует соединение с amoCRM';
$MESS['rover-ape__datetime_created']       = 'Создан';
$MESS["rover-ape__action-export-disabled"] = "Для переноса результатов необходимо включить интеграцию в <a href='/bitrix/admin/settings.php?lang=" . LANGUAGE_ID . "&mid=rover.amocrm&mid_menu=1'>настройках модуля</a>";