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

$MESS['rover-acpe__title']                    = 'Editing an integration profile В«#name#В»';
$MESS['rover-acpe__action-remove']            = 'Remove';
$MESS['rover-acpe__action-remove_title']      = 'Remove checked elements';
$MESS['rover-acpe__action-confirm']           = 'Confirm action for checked elements';
$MESS['rover-acpe__action-update']            = 'Update';
$MESS['rover-acpe__action-elements']          = 'Results (#cnt#)';
$MESS['rover-acpe__action-elements_title']    = 'Profile\'s results';
$MESS['rover-acpe__action-cancel']            = 'Cancel';
$MESS['rover-acpe__action-back']              = 'To the list of profiles (#cnt#)';
$MESS['rover-acpe__action-back_title']        = 'Return to the list of integration profiles';
$MESS['rover-acpe__action-connections']       = 'AmoCRM connections (#cnt#)';
$MESS['rover-acpe__action-connections_title'] = 'AmoCRM connections list';
$MESS['rover-acpe__action-settings']          = 'Module settings';
$MESS['rover-acpe__action-settings_title']    = 'Module settings';
$MESS['rover-acpe__connection-unavailable']   = 'Connection unavailable';
$MESS['rover-acpe__to-list']                  =
    '<a href="/bitrix/admin/rover-acrm__profile-list.php?lang=' . LANGUAGE_ID . '">Back to list</a>';
$MESS['rover-acpe__insert-placeholders']      =
    '&nbsp;<a href="#" onclick="RoverAmoCrmPlaceholder.openPopup(\'#elementId#\'); return false;">placeholders</a><div id="#elementId#_placeholders" style="display: none">#content#</div>';

$MESS['rover-acpe__header-fields-adv-all']                                = '(all)';
$MESS['rover-acpe__duplicate-filter']                                     = 'Duplicate filter';
$MESS['rover-acpe__header-mapping']                                       = 'Fields mapping';
$MESS['rover-acpe__header-duplicates']                                    = 'Duplicate control';
$MESS['rover-acpe__duplicates-add-complex-note']                          =
    ' (disabled in case of amoCRM duplicate control)';
$MESS['rover-acpe__duplicates-no-api-filter-note']                        =
    'To control duplicates, you need to connect <a target="_blank" href="https://www.amocrm.ru/developers/content/crm_platform/filters-api">the filtering API</a>. To do this, contact amoSRM technical support <a target="_blank" href="https://#base-domain#">on your account</a>.<br> <span class="required">Attention, the service is paid!</span>';
$MESS['rover-acpe__header-duplicates-' . EntityTypesInterface::CONTACTS]  = 'Duplicates control';
$MESS['rover-acpe__header-duplicates-' . EntityTypesInterface::COMPANIES] = 'Duplicates control';
$MESS['rover-acpe__header-duplicates-' . EntityTypesInterface::LEADS]     = 'Duplicates control';

$MESS['rover-acpe__duplicate-control-label']                                    = 'Control duplicates';
$MESS['rover-acpe__duplicate-control-cron']                                     =
    'This function can slow down the response of the system. To solve this problem, it is recommended to defer integration to the agent. At the same time, agents on the site <u>must</ u> have to be <a target="_blank" href="https://dev.1c-bitrix.ru/learning/course/?COURSE_ID=43&LESSON_ID=2943">translated on cron</a>.';
$MESS['rover-acpe__duplicate-fields-label']                                     = 'Search by fields';
$MESS['rover-acpe__duplicate-fields-help']                                      = 'At least one field must be selected';
$MESS['rover-acpe__duplicate-logic-label']                                      = 'Search by match';
$MESS['rover-acpe__duplicate-logic-' . Duplicate::LOGIC__AND . '_label']        = 'of all selected fields';
$MESS['rover-acpe__duplicate-logic-' . Duplicate::LOGIC__OR . '_label']         = 'any of the selected fields';
$MESS['rover-acpe__duplicate-action-label']                                     = 'If a duplicate is found';
$MESS['rover-acpe__duplicate-action-' . Duplicate::ACTION__ADD_NOTE . '_label'] =
    'Add a note with reference to the original';
$MESS['rover-acpe__duplicate-action-' . Duplicate::ACTION__COMBINE . '_label']  = 'Update original with duplicate data';
$MESS['rover-acpe__duplicate-action-' . Duplicate::ACTION__SKIP . '_label']     = 'Do not create duplicate';
$MESS['rover-acpe__duplicate-no-api-error']                                     =
    'This functionality is <b>disabled by amoCRM</b>. To enable it, write to your account support service: "<i>Please enable API filtering</i>".<br><br>As soon as the functionality is connected, the duplicate search settings will appear here.';

$MESS['rover-acpe__duplicate-' . ProfileModel::DUPLICATE_STATUS . '_label']             = 'Search in statuses';
$MESS['rover-acpe__duplicate-' . ProfileModel::DUPLICATE_STATUS . '_help']              =
    'Limits the scope of duplicate search by leads in selected statuses.';
$MESS['rover-acpe__duplicate-' . ProfileModel::DUPLICATE_UPDATE_NAME . '_label']        = 'Update duplicate name';
$MESS['rover-acpe__duplicate-' . ProfileModel::DUPLICATE_UPDATE_NAME . '_help']         =
    'Works if action В«' . $MESS['rover-acpe__duplicate-action-' . Duplicate::ACTION__COMBINE . '_label'] . 'В» is selected';
$MESS['rover-acpe__duplicate-' . ProfileModel::DUPLICATE_UPDATE_RESPONSIBLE . '_label'] =
    'Update duplicate responsible user';
$MESS['rover-acpe__duplicate-' . ProfileModel::DUPLICATE_UPDATE_RESPONSIBLE . '_help']  =
    'Works if action В«' . $MESS['rover-acpe__duplicate-action-' . Duplicate::ACTION__COMBINE . '_label'] . 'В» is selected';

$MESS['rover-acpe__duplicate-' . ProfileModel::DUPLICATE_UPDATE_STATUS . '_label']      = 'Update duplicate status';
$MESS['rover-acpe__duplicate-' . ProfileModel::DUPLICATE_UPDATE_STATUS . '_help']       =
    'Works if action В«' . $MESS['rover-acpe__duplicate-action-' . Duplicate::ACTION__COMBINE . '_label'] . 'В» is selected';
$MESS['rover-acrm__duplicate-action-unsorted-help']                                     =
    'When creating a lead in В«UnsortedВ», notes with links to duplicates will be added.';

$MESS['rover-acpe__header-main']                                             = 'Common settings';
$MESS['rover-acpe__field-' . ProfileModel::UF_ACTIVE . '_help']              =
    'Enable/disable the current integration profile';
$MESS['rover-acpe__field-' . ProfileModel::UF_NAME . '_help']                =
    'Source: #type#<br>Connection: #connection#';
$MESS['rover-acpe__field-' . ProfileModel::UF_SITES_IDS . '_help']           =
    'If is empty, profile works on all sites';
$MESS['rover-acpe__field-' . ProfileModel::UF_RESPONSIBLE_LIST . '_help']    =
    'This user will be appointed responsible for the created lead, contact and company. He will also be given the task to be created.<br>f several users are selected, they will be appointed in turn.';
$MESS['rover-acpe__field-' . ProfileModel::UF_COMMON_TAGS . '_help']         = 'adds to all created objects.';
$MESS['rover-acpe__field-' . ProfileModel::UF_ADD_COMPLEX . '_help']         =
    'Required <a href="https://www.amocrm.ru/developers/content/crm_platform/duplication-control" target="_blank">additional configuration for the source</a>.<br>Internal control of duplicates will be unavailable.';
$MESS['rover-acpe__field-' . ProfileModel::UF_ADD_COMPLEX . '_no-lead-help'] = 'Lead creation must be enabled';
$MESS['rover-acpe__field-' . ProfileModel::UF_HIT_DUPLICATES . '_help']      =
    'Helps you get rid of duplication when integrating a post event by sending a web form and in some other cases.';

$MESS['rover-acpe__header-order']                                               = 'Order';
$MESS['rover-acpe__header-fields-bx-to-amo-order']                              = 'Sync Bitrix to amoCRM';
$MESS['rover-acpe__header-fields-amo-to-bx-order']                              = 'Sync amoCRM to Bitrix';
$MESS['rover-acpe__header-fields-statuses-mapping-order']                       = 'Statuses Mapping';
$MESS['rover-acpe__field-' . ProfileModel::UF_BX_AMO_STATUSES . '_help']        =
    'Synchronized according to the mapping settings';
$MESS['rover-acpe__field-' . ProfileModel::UF_BX_AMO_PRODUCTS . '_help']        =
    'Products will be transferred from the order. If there are no such ones in AMO yet, then new ones will be created.';
$MESS['rover-acpe__field-' . ProfileModel::UF_AMO_BX_STATUSES . '_help']        =
    'Synchronized according to the mapping settings';
$MESS['rover-acpe__field-' . ProfileModel::UF_AMO_BX_PRODUCTS . '_help']        =
    'Synchronization by quantity and availability';
$MESS['rover-acpe__field-products_unsorted_help']                               =
    'Not added when creating a lead in "unsorted"';
$MESS['rover-acpe__field-products_disabled_help']                               = 'Disabled in amoCRM account';
$MESS['rover-acpe__field-' . Tabs::ORDER_STATUSES_MAPPING_CANCELLED . '_label'] = '[the cancel flag]';

$MESS['rover-acpe__header-' . EntityTypesInterface::LEADS]                      = 'Lead';
$MESS['rover-acpe__field-create-' . EntityTypesInterface::LEADS]                = 'Create lead';
$MESS['rover-acpe__field-create-' . EntityTypesInterface::LEADS . '_help']      =
    'The lead will be tied to the contact, if created.<br>When added to the "Unsorted", the lead will always be created, according to its settings.';
$MESS['rover-acpe__field-' . ProfileModel::UF_LEAD_STATUS_ID . '_label_multy']  = 'Pipeline and status';
$MESS['rover-acpe__field-' . ProfileModel::UF_LEAD_STATUS_ID . '_label_single'] = 'Status';
$MESS['rover-acpe__field-contacts_id_label']                                    = 'Contact\'s leads';
$MESS['rover-acpe__field-' . ProfileModel::UF_LEAD_VISITOR_UID . '_help']       =
    'Allows you to track the visitor in the autopipelines. Read more in <a href="https://www.amocrm.com/developers/content/digital_pipeline/site_visit" target="_blank">documentation of amoCRM</a>';

$MESS['rover-acpe__header-' . EntityTypesInterface::CONTACTS]       = 'Contact';
$MESS['rover-acpe__field-create-' . EntityTypesInterface::CONTACTS] = 'Create contact';

$MESS['rover-acpe__header-' . EntityTypesInterface::COMPANIES]       = 'Company';
$MESS['rover-acpe__field-create-' . EntityTypesInterface::COMPANIES] = 'Create company';

$MESS['rover-acpe__header-' . EntityTypesInterface::TASKS]                 = 'Task';
$MESS['rover-acpe__header-' . EntityTypesInterface::TASKS . '-disabled']   =
    'Task (disabled, because lead creates in "Unsorted" status")';
$MESS['rover-acpe__field-create-' . EntityTypesInterface::TASKS]           = 'Create task';
$MESS['rover-acpe__field-create-' . EntityTypesInterface::TASKS . '_help'] =
    'A contact or lead must be linked to the task. If neither a contact nor a deal is created, the task will not be created either.';
$MESS['rover-acpe__field-' . ProfileModel::UF_TASK_ELEMENT_TYPE . '_help'] =
    'The task will be bound to the selected entity if it exists. Otherwise, the first existing one will be chosen: lead, contact, company';
$MESS['rover-acpe__field-' . ProfileModel::UF_TASK_DEADLINE . '_help']     =
    'Please, make sure that the time zones of your site and amoCrm are synchronized';
$MESS['rover-acpe__field-' . ProfileModel::UF_TASK_DEADLINE . '_now']      = 'At the time of setting the task';
$MESS['rover-acpe__field-' . ProfileModel::UF_TASK_DEADLINE . '_one-hour'] = 'From one hour';
$MESS['rover-acpe__field-' . ProfileModel::UF_TASK_DEADLINE . '_day-end']  = 'At 11:59 pm the current day';
$MESS['rover-acpe__entity-' . EntityTypesInterface::CONTACTS . '_label']   = 'contact';
$MESS['rover-acpe__entity-' . EntityTypesInterface::LEADS . '_label']      = 'lead';
$MESS['rover-acpe__entity-' . EntityTypesInterface::COMPANIES . '_label']  = 'company';

$MESS['rover-acpe__all']     = '(all)';
$MESS['rover-acpe__default'] = '(by default)';