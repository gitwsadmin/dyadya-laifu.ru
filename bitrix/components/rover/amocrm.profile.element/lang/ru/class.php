<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 14.06.2017
 * Time: 11:47
 *
 * @author Pavel Shulaev (https://rover-it.me)
 */

use AmoCRM\Helpers\EntityTypesInterface;
use Rover\AmoCRM\Config\Tabs;
use Rover\AmoCRM\Directory\Model\ProfileModel;
use Rover\AmoCRM\Service\Duplicate;

$MESS['rover-acpe__title']                    = 'Редактирование профиля интеграции «#name#»';
$MESS['rover-acpe__action-remove']            = 'Удалить';
$MESS['rover-acpe__action-remove_title']      = 'Удалить отмеченные элементы';
$MESS['rover-acpe__action-confirm']           = 'Подтвердите действие для отмеченных элементов';
$MESS['rover-acpe__action-update']            = 'Изменить';
$MESS['rover-acpe__action-elements']          = 'Результаты (#cnt#)';
$MESS['rover-acpe__action-elements_title']    = 'Результаты профиля';
$MESS['rover-acpe__action-cancel']            = 'Отменить';
$MESS['rover-acpe__action-back']              = 'К списку профилей (#cnt#)';
$MESS['rover-acpe__action-back_title']        = 'Вернуться к списку профилей интеграции';
$MESS['rover-acpe__action-connections']       = 'Подключения к amoCRM (#cnt#)';
$MESS['rover-acpe__action-connections_title'] = 'Список подключений к amoCRM';
$MESS['rover-acpe__action-settings']          = 'Настройки';
$MESS['rover-acpe__action-settings_title']    = 'Настройки модуля интеграции';
$MESS['rover-acpe__connection-unavailable']   = 'Подключение недоступно';
$MESS['rover-acpe__to-list']                  =
    '<a href="/bitrix/admin/rover-acrm__profile-list.php?lang=' . LANGUAGE_ID . '">Вернуться к списку</a>';
$MESS['rover-acpe__insert-placeholders']      =
    '&nbsp;<a href="#" onclick="RoverAmoCrmPlaceholder.openPopup(\'#elementId#\'); return false;">плейсхолдеры</a><div id="#elementId#_placeholders" style="display: none">#content#</div>';

$MESS['rover-acpe__header-fields-adv-all']                                = '(все)';
$MESS['rover-acpe__duplicate-filter']                                     = 'Фильтр дублей';
$MESS['rover-acpe__header-mapping']                                       = 'Маппинг полей';
$MESS['rover-acpe__header-duplicates']                                    = 'Контроль дублей';
$MESS['rover-acpe__duplicates-add-complex-note']                          =
    'Внутренний контроль дублей отключен. т.к. используется контроль дублей со стороны amoCRM';
$MESS['rover-acpe__duplicates-no-api-filter-note']                        =
    'Для контроля дублей необходимо подключить <a target="_blank" href="https://www.amocrm.ru/developers/content/crm_platform/filters-api">api фильтрации</a>. Для этого обратитесь в техподдержку амоСРМ на <a target="_blank" href="https://#base-domain#">вашем аккаунте</a>.<br> <span class="required">Внимание, услуга платная!</span>';
$MESS['rover-acpe__header-duplicates-' . EntityTypesInterface::CONTACTS]  = 'Контроль дублей нового контакта';
$MESS['rover-acpe__header-duplicates-' . EntityTypesInterface::COMPANIES] = 'Контроль дублей новой компании';
$MESS['rover-acpe__header-duplicates-' . EntityTypesInterface::LEADS]     = 'Контроль дублей новой сделки';

$MESS['rover-acpe__duplicate-control-label']                                    = 'Контролировать дубли';
$MESS['rover-acpe__duplicate-control-cron']                                     =
    'Включение этого функционала может <b>замедлить отклик сайта</b> в момент срабатывания события. Для ускорения отклика рекомендуется отложить <a target=\'_blank\' href=\'/bitrix/admin/settings.php?mid=rover.amocrm&lang=' . LANGUAGE_ID . '&tabControl_active_tab=agents\'>обработку новых событий на агента</a>. При этом агенты на сайте <b>обязательно</b> должны быть <a target="_blank" href="https://dev.1c-bitrix.ru/learning/course/?COURSE_ID=43&LESSON_ID=2943">переведены на cron</a>.';
$MESS['rover-acpe__duplicate-fields-label']                                     = 'Искать по полям';
$MESS['rover-acpe__duplicate-fields-help']                                      =
    'Должно быть выбрано хотя бы одно поле, иначе поиск осуществлён не будет';
$MESS['rover-acpe__duplicate-logic-label']                                      = 'Искать по совпадению';
$MESS['rover-acpe__duplicate-logic-' . Duplicate::LOGIC__AND . '_label']        = 'всех выбранных полей';
$MESS['rover-acpe__duplicate-logic-' . Duplicate::LOGIC__OR . '_label']         = 'любого из выбранных полей';
$MESS['rover-acpe__duplicate-action-label']                                     = 'Действие при обнаружении дубля';
$MESS['rover-acpe__duplicate-action-' . Duplicate::ACTION__ADD_NOTE . '_label'] =
    'Добавить примечание со ссылками на дубликаты';
$MESS['rover-acpe__duplicate-action-' . Duplicate::ACTION__COMBINE . '_label']  =
    'Обновить и использовать самый первый из найденных дубликатов';
$MESS['rover-acpe__duplicate-action-' . Duplicate::ACTION__SKIP . '_label']     =
    'Использовать самый первый из найденных дубликатов без обновления';
$MESS['rover-acpe__duplicate-no-api-error']                                     =
    'Данный функционал <b>отключен со стороны amoCRM</b>. Чтобы подключить его, напишите в службу поддержки вашего аккаунта: "<i>прошу подключить функционал фильтрации по API</i>".<br><br>Как только функционал будет подключен, здесь появятся настройки поиска дубликатов.';

$MESS['rover-acpe__duplicate-' . ProfileModel::DUPLICATE_STATUS . '_label']        = 'Искать в этапах';
$MESS['rover-acpe__duplicate-' . ProfileModel::DUPLICATE_STATUS . '_help']         =
    'Ограничивает область поиска дубликатов сделками в указанных воронках и этапах.';
$MESS['rover-acpe__duplicate-' . ProfileModel::DUPLICATE_UPDATE_NAME . '_label']   = 'Обновить название дубля';
$MESS['rover-acpe__duplicate-' . ProfileModel::DUPLICATE_UPDATE_NAME . '_help']    =
    'Работает, если выбрано действие «' . $MESS['rover-acpe__duplicate-action-' . Duplicate::ACTION__COMBINE . '_label'] . '»';
$MESS['rover-acpe__duplicate-' . ProfileModel::DUPLICATE_UPDATE_RESPONSIBLE . '_label']   =
    'Обновить ответственного пользователя дубля';
$MESS['rover-acpe__duplicate-' . ProfileModel::DUPLICATE_UPDATE_RESPONSIBLE . '_help']    =
    'Работает, если выбрано действие «' . $MESS['rover-acpe__duplicate-action-' . Duplicate::ACTION__COMBINE . '_label'] . '»';

$MESS['rover-acpe__duplicate-' . ProfileModel::DUPLICATE_UPDATE_STATUS . '_label'] = 'Обновить статус дубля';
$MESS['rover-acpe__duplicate-' . ProfileModel::DUPLICATE_UPDATE_STATUS . '_help']  =
    'Работает, если выбрано действие «' . $MESS['rover-acpe__duplicate-action-' . Duplicate::ACTION__COMBINE . '_label'] . '»';
$MESS['rover-acrm__duplicate-action-unsorted-help']                                =
    'При создании сделки в «неразобранном» будут добавлены примечания со ссылками на дубликаты.';

$MESS['rover-acpe__header-main']                                             = 'Общие настройки';
$MESS['rover-acpe__field-' . ProfileModel::UF_ACTIVE . '_help']              =
    'Включение/отключение текущего профиля интеграции';
$MESS['rover-acpe__field-' . ProfileModel::UF_NAME . '_help']                =
    'Источник: #type#<br>Подключение: #connection#';
$MESS['rover-acpe__field-' . ProfileModel::UF_SITES_IDS . '_help']           =
    'Если не выбран ни один сайт, профиль будет работать для всех';
$MESS['rover-acpe__field-' . ProfileModel::UF_RESPONSIBLE_LIST . '_help']    =
    '<ul><li>Пользователь будет назначен ответственным за создаваемые сделки, контакты и компании. Также ему будет ставиться создаваемая задача.</li><li>Если выбрано несколько пользователей, они будут назначаться по очереди.</li><li>Если сделка создается в «неразобранном», то ответственный назначается в Амо.</li></ul>';
$MESS['rover-acpe__field-' . ProfileModel::UF_COMMON_TAGS . '_help']         =
    'Должны быть указаны через запятую.<br>Будут добавлены к сделкам, контактам и компаниям.';
$MESS['rover-acpe__field-' . ProfileModel::UF_ADD_COMPLEX . '_help']         =
    '<ul><li>Необходима <a href="https://www.amocrm.ru/developers/content/crm_platform/duplication-control" target="_blank">дополнительная настройка для источника</a>.</li><li>Контроль дублей со стороны модуля <u>не осуществляется</u>.</li><li>Не работает, если сделка создается в «неразобранном».</li></ul>';
$MESS['rover-acpe__field-' . ProfileModel::UF_ADD_COMPLEX . '_no-lead-help'] = 'Необходимо включить создание сделки';
$MESS['rover-acpe__field-' . ProfileModel::UF_HIT_DUPLICATES . '_help']      =
    'Помогает избавиться от дублей в amoCRM, если на одном хите событие вызывается несколько раз.';

$MESS['rover-acpe__header-order']                                               = 'Заказ';
$MESS['rover-acpe__header-fields-bx-to-amo-order']                              =
    'Синхронизация в направлении интернет-магазин — amoCRM';
$MESS['rover-acpe__header-fields-amo-to-bx-order']                              =
    'Синхронизация в направлении amoCRM — интернет-магазин';
$MESS['rover-acpe__header-fields-statuses-mapping-order']                       = 'Маппинг статусов';
$MESS['rover-acpe__field-' . ProfileModel::UF_BX_AMO_STATUSES . '_help']        =
    'Синхронизируются согласно настройкам маппинга';
$MESS['rover-acpe__field-' . ProfileModel::UF_BX_AMO_PRODUCTS . '_help']        =
    'Товары будут переданы из заказа. Если в амо таких еще нет, то будут созданы новые.';
$MESS['rover-acpe__field-' . ProfileModel::UF_AMO_BX_STATUSES . '_help']        =
    'Синхронизируются согласно настройкам маппинга';
$MESS['rover-acpe__field-' . ProfileModel::UF_AMO_BX_PRODUCTS . '_help']        =
    'Синхронизация по количеству и наличию';
$MESS['rover-acpe__field-products_unsorted_help']                               =
    'Не добавляются при создании сделки в «неразобранном»';
$MESS['rover-acpe__field-products_disabled_help']                               = 'Товары отключены в аккаунте amoCRM';
$MESS['rover-acpe__field-' . Tabs::ORDER_STATUSES_MAPPING_CANCELLED . '_label'] = '[флаг отмены]';

$MESS['rover-acpe__header-' . EntityTypesInterface::LEADS]                      = 'Сделка';
$MESS['rover-acpe__field-create-' . EntityTypesInterface::LEADS]                = 'Создавать сделку';
$MESS['rover-acpe__field-create-' . EntityTypesInterface::LEADS . '_help']      =
    'Сделка будет привязана к контакту, в случае его создания.<br>При добавлении в «Неразобранное» сделка будет создаваться всегда, согласно своим настройкам.';
$MESS['rover-acpe__field-' . ProfileModel::UF_LEAD_STATUS_ID . '_label_multy']  = 'Воронка и этап';
$MESS['rover-acpe__field-' . ProfileModel::UF_LEAD_STATUS_ID . '_label_single'] = 'Этап';
$MESS['rover-acpe__field-contacts_id_label']                                    = 'Сделки привязанного контакта';
$MESS['rover-acpe__field-' . ProfileModel::UF_LEAD_VISITOR_UID . '_help']       =
    'Позволяет отслеживать посетителя в автоворонках. Подробнее в <a href="https://www.amocrm.ru/developers/content/digital_pipeline/site_visit" target="_blank">документации amoCRM</a>';

$MESS['rover-acpe__header-' . EntityTypesInterface::CONTACTS]       = 'Контакт';
$MESS['rover-acpe__field-create-' . EntityTypesInterface::CONTACTS] = 'Создавать контакт';

$MESS['rover-acpe__header-' . EntityTypesInterface::COMPANIES]       = 'Компания';
$MESS['rover-acpe__field-create-' . EntityTypesInterface::COMPANIES] = 'Создавать компанию';

$MESS['rover-acpe__header-' . EntityTypesInterface::TASKS]                 = 'Задача';
$MESS['rover-acpe__header-' . EntityTypesInterface::TASKS . '-disabled']   =
    ' (отключена, т.к. сделка создаётся в «неразобранном»)';
$MESS['rover-acpe__field-create-' . EntityTypesInterface::TASKS]           = 'Создавать задачу';
$MESS['rover-acpe__field-create-' . EntityTypesInterface::TASKS . '_help'] =
    'Если ни контакт, ни сделка, ни компания созданы не будут, задача так же не будет создана';
$MESS['rover-acpe__field-' . ProfileModel::UF_TASK_ELEMENT_TYPE . '_help'] =
    'Задача будет привязана к выбранной сущности в случае ее наличия. В противном случае будет выбрана первая из существующих: сделка, контакт, компания';
$MESS['rover-acpe__field-' . ProfileModel::UF_TASK_DEADLINE . '_help']     =
    'Пожалуйста, убедитесь, что часовые пояса Вашего сайта и амоСрм синхронизированы';
$MESS['rover-acpe__field-' . ProfileModel::UF_TASK_DEADLINE . '_now']      = 'В момент постановки задачи';
$MESS['rover-acpe__field-' . ProfileModel::UF_TASK_DEADLINE . '_one-hour'] = 'Через час';
$MESS['rover-acpe__field-' . ProfileModel::UF_TASK_DEADLINE . '_day-end']  = 'В 23:59 текущего дня';
$MESS['rover-acpe__entity-' . EntityTypesInterface::CONTACTS . '_label']   = 'контакт';
$MESS['rover-acpe__entity-' . EntityTypesInterface::LEADS . '_label']      = 'сделка';
$MESS['rover-acpe__entity-' . EntityTypesInterface::COMPANIES . '_label']  = 'компания';

$MESS['rover-acpe__all']     = '(все)';
$MESS['rover-acpe__default'] = '(по умолчанию)';