<?php

/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 02.10.2015
 * Time: 19:12
 *
 * @author Pavel Shulaev (https://rover-it.me)
 */
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

use AmoCRM\Exceptions\AmoCRMApiException;
use AmoCRM\Exceptions\AmoCRMoAuthApiException;
use AmoCRM\Helpers\EntityTypesInterface;
use Bitrix\Main;
use Bitrix\Main\Localization\Loc;
use Rover\AmoCRM\Component\ListBase;
use Rover\AmoCRM\Dependence\Atom\Profile\Service;
use Rover\AmoCRM\Dependence\DependenceBuilder;
use Rover\AmoCRM\Directory\Entity\Connection;
use Rover\AmoCRM\Directory\Entity\Profile;
use Rover\AmoCRM\Directory\Model\ConnectionModel;
use Rover\AmoCRM\Directory\Model\ProfileModel;
use Rover\AmoCRM\Directory\Repository;
use Rover\AmoCRM\Directory\Repository\ConnectionRepository;
use Rover\AmoCRM\Directory\Repository\ProfileRepository;
use Rover\AmoCRM\Factory\Directory\ProfileFactory;
use Rover\AmoCRM\Model\Source;
use Rover\AmoCRM\Service\ExceptionService;
use Rover\AmoCRM\Service\Message;
use Rover\AmoCRM\Service\Params;
use Rover\AmoCRM\Service\PathService;

if (Main\Loader::includeSharewareModule('rover.amocrm') == Main\Loader::MODULE_DEMO_EXPIRED) {
    ShowMessage(Loc::getMessage('rover-ac__demo-expired'));

    return;
}

if (!Main\Loader::includeModule('rover.amocrm')) {
    ShowMessage('rover.amocrm module not found');

    return;
}

/**
 * Class RoverAmoCrmImport
 *
 * @author Pavel Shulaev (https://rover-it.me)
 */
class RoverAmoCrmProfileList extends ListBase
{
    const GRID_ID = 'amocrm_preset_list';

    /**
     * @return array
     * @author Pavel Shulaev (https://rover-it.me)
     */
    protected function getGridSort(): array
    {
        return $this->getGridOptions()->GetSorting([
            "sort" => ["ID" => "ASC"],
            "vars" => ["by" => "by", "order" => "order"]
        ]);
    }

    /**
     * @param Source $source
     * @return string
     * @throws Main\ArgumentNullException
     * @author Pavel Shulaev (https://rover-it.me)
     */
    protected function getSourceCell(Source $source): string
    {
        return "<a title='{$source->getName()}' href='" . PathService::getSourceEdit($source) . "}'>{$source->getName()}</a><br><span style=\"color: #999; font-size: .85em\">{$source->getTypeLabel()}</span>";
    }

    /**
     * @param Connection $connection
     * @param bool $showAvailable
     * @return string
     * @author Pavel Shulaev (https://rover-it.me)
     */
    protected function getConnectionCell(Connection $connection, bool $showAvailable = true): string
    {
        return '<div style="display: flex"><span>' . $connection->getStatusPict() . '</span>&nbsp;<span>'
            . '<a href="/bitrix/admin/rover-acrm__connection-element.php?ID=' . $connection->getId() . '&lang=' . LANGUAGE_ID . '">'
            . $connection->getName() . '</a><br><span style="color: #999; font-size: .85em">' . $connection->getBaseDomain('') . '</span>'
            . ($showAvailable && $connection->isAvailable() ? '' : ' <span style="color: #999; font-size: .85em">[' . Loc::getMessage('rover-apl__disabled') . ']</span>') . '</span></div>';
    }

    /**
     * @return array
     * @throws Main\ArgumentException
     * @throws Main\ArgumentNullException
     * @throws Main\ArgumentOutOfRangeException
     * @throws Main\LoaderException
     * @throws Main\NotImplementedException
     * @throws Main\ObjectPropertyException
     * @throws Main\SystemException
     * @throws AmoCRMApiException
     * @throws AmoCRMoAuthApiException
     * @author Pavel Shulaev (https://rover-it.me)
     */
    protected function getRows(): array
    {
        global $APPLICATION;

        $nav       = $this->getNavObject();
        $sort      = $this->getGridSort()['sort'];
        $sortBy    = key($sort);
        $sortOrder = $sort[$sortBy];
        if (!in_array($sortBy, array_keys(ProfileModel::getAdditionalUfParams()))) {
            $sortBy = 'ID';
        }

        $query = [
            'offset'      => $nav->getOffset(),
            'limit'       => $nav->getLimit(),
            'count_total' => true,
            'order'       => [$sortBy => $sortOrder]
        ];

        $profilesData = ProfileModel::getList($query);
        $nav->setRecordCount($profilesData->getCount());

        $curPage = $APPLICATION->GetCurPage(true);
        $curDir  = $this->getCurDir();
        $result  = [];

        /** @var Profile $profile */
        foreach ($profilesData as $profileData) {
            $profile   = ProfileFactory::buildByData($profileData);
            $profileId = $profile->getId();

            try {
                $connection      = $profile->getConnection();
                $statuses        = $connection->isAvailable() ? Params::getGroupedLeadStatuses($connection) : [];
                $amoUsers        = $connection->isAvailable() ? Params::getAmoUsers($connection) : [];
                $source          = $profile->getSource();
                $sites           = $profile->getAllowedSitesIds();
                $resultsCnt      = $source->getResultsCount();
                $profileName     = $profile->getName();
                $responsibleList = $profile->getResponsibleList();
                $leadCreate      = $profile->isLeadsEnabled();
                $unsortedEnabled = $connection->isAvailable() && $leadCreate && $profile->isUnsorted();

                if ($connection->isAvailable()) {
                    $responsiblePrint = [];
                    foreach ($responsibleList as $responsibleId) {
                        $responsible =
                            $amoUsers[$responsibleId] ?? '<span style="color: red">' . Loc::getMessage('rover-apl__error_no_responsible') . ' (' . $responsibleId . ')</span>';

                        $responsiblePrint[$responsibleId] = $responsibleId == $profile->getCurrentResponsibleId()
                            ? '<b>' . $responsible . '</b>'
                            : $responsible;
                    }

                    $responsibleColumn = count($responsiblePrint)
                        ? implode('<br>', $responsiblePrint)
                        : Loc::getMessage('rover-apl__default_responsible');
                } else {
                    $responsibleColumn = Loc::getMessage('rover-apl__unavailable');
                }

                $sitesColumns = [];
                foreach ($sites as $siteLid) {
                    $sitesColumns[] =
                        '<a href="/bitrix/admin/site_edit.php?lang=' . LANGUAGE_ID . '&LID=' . $siteLid . '">' . $siteLid . '</a>';
                }

                $row = [
                    'id'       => $profileId,
                    'data'     => [
                        'ID'                              => $profileId,
                        ProfileModel::UF_NAME             => $profileName,
                        ProfileModel::UF_SOURCE_ID        => $source->getTypeLabel(),
                        ProfileModel::UF_SITES_IDS        => implode(', ', $sites),
                        ProfileModel::UF_ACTIVE           => $profile->isActive() ? 'Y' : 'N',
                        ProfileModel::UF_RESPONSIBLE_LIST => $responsibleList,
                        //Profile::UF_AMO_ENTITIES        => $profile->getAmoEntities(),
                        EntityTypesInterface::LEADS       => $leadCreate ? 'Y' : 'N',
                        EntityTypesInterface::CONTACTS    => $profile->isContactsEnabled() ? 'Y' : 'N',
                        EntityTypesInterface::COMPANIES   => $profile->isCompaniesEnabled() ? 'Y' : 'N',
                        EntityTypesInterface::TASKS       => !$unsortedEnabled && $profile->isTasksEnabled() ? 'Y' : 'N',
                        'ELEMENTS_CNT'                    => $resultsCnt,
                        ProfileModel::UF_CONNECTION_ID    => $connection->getId()
                    ],
                    'columns'  => [
                        ProfileModel::UF_NAME             => "<a title='" . Loc::getMessage('rover-apl__title-settings',
                                ['#preset-name#' => $profileName]) . "' href='" . PathService::getProfileElement($profileId) . "'>$profileName</a>"
                            . ((new Service($profile))->check()
                                ->isSuccess() ? '' : '<br><small style="color: red">' . Loc::getMessage('rover-acpl__profile-has-errors') . '</small>'),
                        'ELEMENTS_CNT'                    => "<a title='" . Loc::getMessage('rover-apl__title-results',
                                ['#preset-name#' => $profileName]) . "' href='" . PathService::getProfileItems($profileId) . "'>$resultsCnt</a>",
                        ProfileModel::UF_SOURCE_ID        => $this->getSourceCell($source),
                        ProfileModel::UF_RESPONSIBLE_LIST => $responsibleColumn,
                        ProfileModel::UF_CONNECTION_ID    => $this->getConnectionCell($connection),
                        ProfileModel::UF_SITES_IDS        => implode(', ', $sitesColumns),
                    ],
                    'actions'  => $this->getAvailableActions($profile, $nav, $curDir, $curPage),
                    'editable' => $this->arParams['READ_ONLY'] != 'Y',
                    'source'   => $source
                ];

                if ($unsortedEnabled) {
                    $row['columns'][EntityTypesInterface::TASKS] = '<span style="color: #999; font-size: .85em" title="'
                        . Loc::getMessage('rover-apl__title-task-unavailable') . '">'
                        . Loc::getMessage('rover-apl__unavailable') . '</span>';
                    $row['data'][EntityTypesInterface::TASKS]    = '<span>' . $row['data']['TASK'] . '</span>';
                }

                if ($profile->isLeadsEnabled()) {
                    if ($statuses && $profile->getLeadPipelineId() && $profile->getLeadStatusId()) {
                        $leadStatus = $statuses[$profile->getLeadPipelineId()]['name'] . ' > '
                            . $statuses[$profile->getLeadPipelineId()]['options'][$profile->getLeadStatusId()];
                    } else {
                        $leadStatus = '';
                    }

                    $row['columns'][EntityTypesInterface::LEADS] = Loc::getMessage('rover-apl__yes')
                        . ($leadStatus ? '<br><span style="color: #999; font-size: .85em">'
                            . $leadStatus . '</span>' : '');
                    $row['data'][EntityTypesInterface::LEADS]    = '<span>' . $row['data']['LEAD'] . '</span>';
                }

                foreach ($row['data'] as $name => $value) {
                    $row['data']['~' . $name] = $value;
                }

            } catch (Exception $e) {
                $row = [
                    'id'   => null,
                    'data' => [
                        'ID'                              => $profileId,
                        ProfileModel::UF_NAME             => $profile->getName() . '<br><span style="color: red; font-size: .85em">Error: ' . $e->getMessage() . '</span>',
                        ProfileModel::UF_SOURCE_ID        => $this->getSourceCell($profile->getSource()),
                        ProfileModel::UF_SITES_IDS        => '-',
                        ProfileModel::UF_ACTIVE           => $profile->isActive() ? 'Y' : 'N',
                        ProfileModel::UF_RESPONSIBLE_LIST => '-',
                        EntityTypesInterface::LEADS       => '-',
                        EntityTypesInterface::CONTACTS    => '-',
                        EntityTypesInterface::COMPANIES   => '-',
                        EntityTypesInterface::TASKS       => '-',
                        'ELEMENTS_CNT'                    => '-',
                        ProfileModel::UF_CONNECTION_ID    => $this->getConnectionCell($connection, false),

                    ],

                    'actions'  => [
                        [
                            'TEXT'      => Loc::getMessage('rover-apl__action-remove'),
                            'ONCLICK'   => 'if(confirm("' . Loc::getMessage('rover-apl__action-confirm') . '")) window.location="' . $curPage . '?' . $this->getActionButton() . '=delete&ID=' . $profileId . '&' . bitrix_sessid_get() . "&lang=" . LANGUAGE_ID . '";',
                            "ICONCLASS" => "delete"
                        ]
                    ],
                    'editable' => false,
                    'source'   => null
                ];

                ExceptionService::handleException($e);
            }

            $result[] = $row;
        }

        return $result;
    }

    /**
     * @param Profile $profile
     * @param $nav
     * @param $curDir
     * @param $curPage
     * @return array[]
     * @author Pavel Shulaev (https://rover-it.me)
     */
    protected function getAvailableActions(Profile $profile, $nav, $curDir, $curPage): array
    {
        if ($this->arParams['READ_ONLY'] == 'Y') {
            return [
                [
                    'TEXT'      => Loc::getMessage('rover-apl__action-open'),
                    'ONCLICK'   => "jsUtils.Redirect(arguments, '" . $curDir . "rover-acrm__profile-element.php?profile_id=" . $profile->getId() . "&lang=" . LANGUAGE_ID . "')",
                    "ICONCLASS" => "edit",
                    'DEFAULT'   => true,
                ],
                [
                    'TEXT'      => Loc::getMessage('rover-apl__action-elements'),
                    'ONCLICK'   => "jsUtils.Redirect(arguments, '" . $curDir . "rover-acrm__result-list.php?profile_id=" . $profile->getId() . "&lang=" . LANGUAGE_ID . "')",
                    "ICONCLASS" => "view"
                ],
            ];
        }

        return [
            [
                'TEXT'      => Loc::getMessage('rover-apl__action-update'),
                'ONCLICK'   => "jsUtils.Redirect(arguments, '" . $curDir . "rover-acrm__profile-element.php?profile_id=" . $profile->getId() . "&lang=" . LANGUAGE_ID . "')",
                "ICONCLASS" => "edit",
                'DEFAULT'   => true,
            ],
            [
                'TEXT'      => Loc::getMessage('rover-apl__action-copy'),
                'ONCLICK'   => "jsUtils.Redirect(arguments, '" . $curPage . '?' . $this->getActionButton() . '=copy&ID=' . $profile->getId() . "&lang=" . LANGUAGE_ID . "&{$nav->getId()}=page-{$nav->getCurrentPage()}-size{$nav->getPageSize()}')",
                "ICONCLASS" => "copy"
            ],
            [
                'TEXT'      => Loc::getMessage('rover-apl__action-elements'),
                'ONCLICK'   => "jsUtils.Redirect(arguments, '" . $curDir . "rover-acrm__result-list.php?profile_id=" . $profile->getId() . "&lang=" . LANGUAGE_ID . "')",
                "ICONCLASS" => "view"
            ],
            [
                'SEPARATOR' => true,
            ],
            [
                'TEXT'      => Loc::getMessage('rover-apl__action-remove'),
                'ONCLICK'   => 'if(confirm("' . Loc::getMessage('rover-apl__action-confirm') . '")) window.location="' . $curPage . '?' . $this->getActionButton() . '=delete&ID=' . $profile->getId() . '&' . bitrix_sessid_get() . "&lang=" . LANGUAGE_ID . "&{$nav->getId()}=page-{$nav->getCurrentPage()}-size{$nav->getPageSize()}\";",
                "ICONCLASS" => "delete"
            ]
        ];
    }

    /**
     * @param $values
     * @return string
     * @author Pavel Shulaev (https://rover-it.me)
     */
    protected function getHtmlList($values): string
    {
        return $values
            ? '<ul style="padding-left: 0; /*list-style: inside*/"><li>' . implode('</li><li>', $values) . '</li></ul>'
            : '<small style="color: #777">-</span>';
    }

    /**
     * @param $array
     * @return mixed
     * @author Pavel Shulaev (https://rover-it.me)
     */
    protected function sort($array)
    {
        $sort  = $this->getGridSort();
        $sort  = $sort['sort'];
        $by    = key($sort);
        $order = $sort[$by];

        usort($array, function ($a, $b) use ($by, $order) {
            if (is_numeric($a['data'][$by]) && is_numeric($b['data'][$by])) {
                $result = $a['data'][$by] > $b['data'][$by]
                    ? 1 : ($a['data'][$by] < $b['data'][$by]
                        ? -1 : 0);
            } else {
                $result = strcasecmp($a['data'][$by], $b['data'][$by]);
            }

            if ($order == 'desc') {
                $result = $result * (-1);
            }

            return $result;
        });

        return $array;
    }

    /**
     * @return array
     * @author Pavel Shulaev (https://rover-it.me)
     */
    protected function getColumns(): array
    {
        /*$sites   = [];
        $sitesDB = Main\SiteTable::getList(['select' => ['LID', 'NAME']], ['cache' => ['ttl' => 3600]]);
        while ($site = $sitesDB->fetch())
            $sites[$site['LID']] = '[' . $site['LID'] . '] ' . $site['NAME'];
*/


        return [
            [
                'id'      => 'ID',
                'name'    => 'ID',
                'default' => false,
                'sort'    => 'ID'
            ],
            [
                'id'       => ProfileModel::UF_ACTIVE,
                'name'     => Loc::getMessage('rover-apl__header-ACTIVE'),
                'default'  => true,
                'sort'     => ProfileModel::UF_ACTIVE,
                'editable' => true,
                "type"     => "checkbox"
            ],
            [
                'id'       => ProfileModel::UF_NAME,
                'name'     => Loc::getMessage('rover-apl__header-NAME'),
                'default'  => true,
                'sort'     => ProfileModel::UF_NAME,
                'editable' => true,
            ],

            [
                'id'      => ProfileModel::UF_SOURCE_ID,
                'name'    => Loc::getMessage('rover-apl__header-TYPE'),
                'default' => true,
                'sort'    => ProfileModel::UF_SOURCE_ID
            ],
            [
                'id'       => ProfileModel::UF_CONNECTION_ID,
                'name'     => Loc::getMessage('rover-apl__header-CONNECTION'),
                'default'  => true,
                // 'sort'      => 'CONNECTION',
                'editable' => false,
            ],
            [
                'id'       => ProfileModel::UF_RESPONSIBLE_LIST,
                'name'     => Loc::getMessage('rover-apl__header-RESPONSIBLE'),
                'default'  => true,
                // не подходит, т.к. для каждого профиля может быть свой список отвественных
//                'editable' => [
//                    'items' => ['a' => 'b', 'c' => 'd'],
//                    'TYPE'  => 'MULTISELECT', // \Bitrix\Main\Grid\Editor\Types::MULTISELECT
//                ],
            ],
            [
                'id'      => ProfileModel::UF_SITES_IDS,
                'name'    => Loc::getMessage('rover-apl__header-SITE'),
                'default' => true,
                /* 'multiple'  => true,
                 'type'      => 'list',
                 // 'sort'      => 'TYPE'
                 'editable'  => [
                     'items' => $sites
                 ]*/
            ],
            [
                'id'       => EntityTypesInterface::LEADS,
                'name'     => Loc::getMessage('rover-apl__header-LEAD'),
                'default'  => true,
                //'sort'      => EntityTypesInterface::LEADS,
                'editable' => true,
                "type"     => "checkbox"
            ],
            /* array(
                 'id'        => ProfileModel::UF_AMO_ENTITIES,
                 'name'      => ProfileModel::getFieldL18n(ProfileModel::UF_AMO_ENTITIES),
                 'default'   => true,
                 //'sort'      => EntityTypesInterface::LEADS,
                // 'editable'  => true,
                 "type"      => "checkbox"
             ),*/
            [
                'id'       => EntityTypesInterface::CONTACTS,
                'name'     => Loc::getMessage('rover-apl__header-CONTACT'),
                'default'  => true,
                //'sort'      => EntityTypesInterface::CONTACTS,
                'editable' => true,
                "type"     => "checkbox"
            ],
            [
                'id'       => EntityTypesInterface::COMPANIES,
                'name'     => Loc::getMessage('rover-apl__header-COMPANY'),
                'default'  => true,
                //'sort'      => EntityTypesInterface::COMPANIES,
                'editable' => true,
                "type"     => "checkbox"
            ],
            [
                'id'       => EntityTypesInterface::TASKS,
                'name'     => Loc::getMessage('rover-apl__header-TASK'),
                'default'  => true,
                //'sort'      => EntityTypesInterface::TASKS,
                'editable' => true,
                "type"     => "checkbox"
            ],
            [
                'id'      => 'ELEMENTS_CNT',
                'name'    => Loc::getMessage('rover-apl__header-ELEMENTS_CNT'),
                'default' => true,
                //'sort'      => 'ELEMENTS_CNT'
            ],
        ];
    }

    /**
     * @return array
     * @throws Main\ArgumentNullException
     * @throws Main\LoaderException
     * @throws Main\SystemException
     * @throws ReflectionException
     * @author Pavel Shulaev (https://rover-it.me)
     */
    protected function getActionPanel(): array
    {
        $buttons = [];

        if ($this->arParams['READ_ONLY'] != 'Y') {
            $buttonAdd = [
                'TEXT'  => Loc::getMessage('rover-apl__action-add'),
                'TITLE' => Loc::getMessage('rover-apl__action-add_title'),
                'MENU'  => [],
                'ICON'  => 'btn-list'
            ];

            $classes = Source::getChildClasses();
            foreach ($classes as $sourceClassName) {
                /**
                 * @var Source $sourceClassName
                 */
                if (strlen($sourceClassName::$module)
                    && !Main\Loader::includeModule($sourceClassName::$module)) {
                    continue;
                }

                $buttonAdd['MENU'][] = [
                    'ICONCLASS' => 'add',
                    'TEXT'      => Loc::getMessage('rover-apl__action-add_' . $sourceClassName::getType()),
                    'ONCLICK'   => "amoCrmPresetList.popup('" . $sourceClassName::getType() . "')"
                ];
            }

            $connectionCount = ConnectionModel::getList(['count_total' => true, 'select' => ['ID']])->getCount();

            $buttons[] = $buttonAdd;
        }


        $buttons[] = [
            'TEXT'  => Loc::getMessage('rover-apl__action-connections', ['#cnt#' => $connectionCount]),
            'TITLE' => Loc::getMessage('rover-apl__action-connections_title'),
            'LINK'  => PathService::getConnectionList(),
            'ICON'  => 'btn-list'
        ];
        $buttons[] = ['SEPARATOR' => true];

        if ($this->arParams['READ_ONLY'] != 'Y') {
            $buttons[] = [
                'TEXT'  => Loc::getMessage('rover-apl__action-settings'),
                'TITLE' => Loc::getMessage('rover-apl__action-settings_title'),
                'LINK'  => PathService::getSettings(),
                'ICON'  => 'btn-settings'
            ];
        }

        return $buttons;
    }

    /**
     * @throws Main\ArgumentException
     * @throws Main\ArgumentNullException
     * @throws Main\ArgumentOutOfRangeException
     * @throws Main\Db\SqlQueryException
     * @throws Main\LoaderException
     * @throws Main\ObjectPropertyException
     * @throws Main\SystemException
     * @author Pavel Shulaev (https://rover-it.me)
     */
    protected function requestProcess()
    {
        if ($this->request->get('OK_MESSAGE')) {
            Message::addOk($this->request->get('OK_MESSAGE'));
        }

        if ($this->request->get('ERROR_MESSAGE')) {
            Message::addError($this->request->get('ERROR_MESSAGE'));
        }

        if ($this->arParams['READ_ONLY'] == 'Y') {
            return;
        }

        switch ($this->getRequestAction()) {
            case 'delete':
                $this->requestDelete();
                break;
            case 'edit':
                $this->requestEdit();
                break;
            case 'activate':
                $this->requestSetPresetActive(true);
                break;
            case 'deactivate':
                $this->requestSetPresetActive(false);
                break;
            case 'copy':
                $this->requestCopy();
                break;
        }
    }

    /**
     * @throws Main\ArgumentNullException
     * @throws Main\ArgumentOutOfRangeException
     * @throws Main\LoaderException
     * @throws Main\SystemException
     * @author Pavel Shulaev (https://rover-it.me)
     */
    protected function requestCopy()
    {
        $ids = $this->getRequestIds();

        if (!count($ids)) {
            return;
        }

        $profiles = ProfileRepository::getList(['filter' => ['=ID' => $ids]]);
        /** @var Profile $profile */
        foreach ($profiles as $profile) {
            $result = $profile->createDuplicate();
        }

        if ((count($ids) == 1) && $result->isSuccess()) {
            LocalRedirect($this->getCurDir() . "rover-acrm__profile-element.php?profile_id=" . $result->getId() . "&lang=" . LANGUAGE_ID);
        }
    }

    /**
     * @param bool $active
     * @throws Main\ArgumentNullException
     * @throws Main\ArgumentOutOfRangeException
     * @throws Main\LoaderException
     * @throws Main\SystemException
     * @author Pavel Shulaev (https://rover-it.me)
     */
    protected function requestSetPresetActive(bool $active)
    {
        $ids = $this->getRequestIds();
        if (!count($ids)) {
            return;
        }

        $profiles = ProfileRepository::getList(['filter' => ['=ID' => $ids]]);
        /** @var Profile $profile */
        foreach ($profiles as $profile) {
            $profile->setActive($active);

            Repository::save($profile);
        }
    }

    /**
     * @throws Main\ArgumentException
     * @throws Main\ArgumentNullException
     * @throws Main\ArgumentOutOfRangeException
     * @throws Main\LoaderException
     * @throws Main\ObjectPropertyException
     * @throws Main\SystemException
     * @author Pavel Shulaev (https://rover-it.me)
     */
    protected function requestEdit()
    {
        $fields = $this->request->get('FIELDS');

        foreach ($fields as $profileId => $field) {
            /** @var Profile $profile */
            $profile = ProfileRepository::getById($profileId);

            foreach ($field as $fieldName => $fieldValue) {

                switch ($fieldName) {
                    case ProfileModel::UF_NAME:
                        $profile->setName($fieldValue);
                        break;
                    case ProfileModel::UF_ACTIVE:
                        $profile->setActive($fieldValue == 'Y');
                        break;
                    case EntityTypesInterface::LEADS:
                        $profile->setLeadsEnabled($fieldValue == 'Y');
                        break;
                    case EntityTypesInterface::CONTACTS:
                        $profile->setContactsEnabled($fieldValue == 'Y');
                        break;
                    case EntityTypesInterface::COMPANIES:
                        $profile->setCompaniesEnabled($fieldValue == 'Y');
                        break;
                    case EntityTypesInterface::TASKS:
                        if (!$profile->isUnsorted()) {
                            $profile->setTasksEnabled($fieldValue == 'Y');
                        }
                        break;
                    case ProfileModel::UF_RESPONSIBLE_LIST:

                        $list = [];
                        foreach ($fieldValue as $responsibleData) {
                            $list[] = $responsibleData['VALUE'];
                        }

                        $profile->setResponsibleList($list);
                        break;
                }
            }

            Repository::save($profile);
        }

        Message::addOk(Loc::getMessage('rover-apl__action-update_success'));
    }

    /**
     * @return array|null|string
     * @author Pavel Shulaev (https://rover-it.me)
     */
    protected function getRequestIds()
    {
        $ids = $this->request->get('ID');
        if (!is_array($ids)) {
            $ids = [$ids];
        }

        return $ids;
    }

    /**
     * @throws Main\ArgumentNullException
     * @throws Main\ArgumentOutOfRangeException
     * @throws Main\SystemException
     * @author Pavel Shulaev (https://rover-it.me)
     */
    protected function requestDelete()
    {
        $ids = $this->getRequestIds();

        if (!count($ids)) {
            return;
        }

        try {
            foreach ($ids as $id) {
                ProfileModel::delete($id);
            }

            Message::addOk(Loc::getMessage('rover-apl__action-remove_success'));
        } catch (Exception $e) {
            ExceptionService::handleException($e);
        }
    }

    /**
     * @return array
     * @throws Main\ArgumentNullException
     * @throws ReflectionException
     * @author Pavel Shulaev (https://rover-it.me)
     */
    public function getSourcesList(): array
    {
        $sourceClasses = Source::getChildClasses();
        $result        = [];

        /** @var Source $className */
        foreach ($sourceClasses as $className) {
            try {
                $result[$className::getType()] = $className::getList();
            } catch (Exception $e) {
            }
        }

        return $result;
    }

    /**
     * @return array
     * @throws Main\ArgumentNullException
     * @throws Main\LoaderException
     * @throws Main\SystemException
     * @author Pavel Shulaev (https://rover-it.me)
     */
    protected function getConnectionsList(): array
    {
        $connections = ConnectionRepository::getList([
            'filter' => [
                '=' . ConnectionModel::UF_ACTIVE => true,
                '=STATUS'                        => [ConnectionModel::STATUS_EXPIRED, ConnectionModel::STATUS_OK]
            ],
        ]);

        $result = [];
        foreach ($connections as $connection) {
            $result[$connection->getCollection()->offsetGet('ID')]
                = $connection->getCollection()->offsetGet(ConnectionModel::UF_NAME)
                . ' (' . $connection->getCollection()->offsetGet(ConnectionModel::UF_BASE_DOMAIN) . ')';
        }

        return $result;
    }

    /**
     * @return void
     * @throws AmoCRMApiException
     * @throws AmoCRMoAuthApiException
     * @throws Main\ArgumentException
     * @throws Main\ArgumentNullException
     * @throws Main\ArgumentOutOfRangeException
     * @throws Main\LoaderException
     * @throws Main\NotImplementedException
     * @throws Main\ObjectPropertyException
     * @throws Main\SystemException
     * @throws ReflectionException
     * @author Pavel Shulaev (https://rover-it.me)
     */
    protected function getResult()
    {
        $this->arResult['ROWS']             = $this->getRows();
        $this->arResult['COLUMNS']          = $this->getColumns();
        $this->arResult['NAV']              = $this->getNavObject();
        $this->arResult['SORT']             = $this->getGridSort();
        $this->arResult['SOURCES_LIST']     = $this->getSourcesList();
        $this->arResult['ACTION_PANEL']     = $this->getActionPanel();
        $this->arResult['CONNECTIONS_LIST'] = $this->getConnectionsList();
        $this->arResult['PAGE_SIZES']       = $this->getPageSizes($this->arResult['NAV']);
    }

    /**
     * @return void
     * @throws Main\ArgumentException
     * @throws Main\ArgumentNullException
     * @throws Main\ArgumentOutOfRangeException
     * @throws Main\ObjectPropertyException
     * @throws Main\SystemException
     * @author Pavel Shulaev (https://rover-it.me)
     */
    public function executeComponent()
    {
        try {
            $this->setFrameMode(false);
            $this->checkParams();
            $this->setTitle();

            if (DependenceBuilder::buildBase()->check()->isSuccess()) {
                $this->requestProcess();
                $this->getResult();
            }

            $this->includeComponentTemplate();
        } catch (Exception $e) {
            ExceptionService::handleException($e, true);
        }
    }

    /**
     * @return string
     * @author Pavel Shulaev (https://rover-it.me)
     */
    protected function getRepositoryClass(): string
    {
        return ProfileRepository::class;// TODO: Implement getRepositoryClass() method.
    }

    /**
     * @return string
     * @author Pavel Shulaev (https://rover-it.me)
     */
    protected function getTitle(): string
    {
        return Loc::getMessage('rover-apl__title');
    }

    /**
     * @return array
     * @author Pavel Shulaev (https://rover-it.me)
     */
    protected function getEditableFields(): array
    {
        return []; //@TODO:
    }
}