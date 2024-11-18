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
use Bitrix\Main\Web\Uri;
use Rover\AmoCRM\Component\ListBase;
use Rover\AmoCRM\Dependence\DependenceBuilder;
use Rover\AmoCRM\Directory\Entity\Profile;
use Rover\AmoCRM\Directory\Model\LinkModel;
use Rover\AmoCRM\Directory\Model\ProfileModel;
use Rover\AmoCRM\Directory\Repository\ProfileRepository;
use Rover\AmoCRM\Event\IblockElementCreate;
use Rover\AmoCRM\Event\IblockElementUpdate;
use Rover\AmoCRM\Event\PostEvent;
use Rover\AmoCRM\Event\WebForm;
use Rover\AmoCRM\EventContext;
use Rover\AmoCRM\Factory\Directory\EventFactory;
use Rover\AmoCRM\Model\Source;
use Rover\AmoCRM\Options;
use Rover\AmoCRM\Service\ExceptionService;
use Rover\AmoCRM\Service\HitCache;
use Rover\AmoCRM\Service\Message;

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
class RoverAmoCrmResultList extends ListBase
{
    const GRID_ID        = 'amocrm_preset_elements_';
    const ACTION__EXPORT = 'export';

    /**
     * @param $arParams
     * @return mixed
     * @author Pavel Shulaev (https://rover-it.me)
     */
    public function onPrepareComponentParams($arParams)
    {
        $arParams['PROFILE_ID'] = intval($arParams['PROFILE_ID']);

        return $arParams;
    }

    /**
     * @throws Main\ArgumentNullException
     * @throws Main\LoaderException
     * @throws Main\SystemException
     * @author Pavel Shulaev (https://rover-it.me)
     */
    protected function checkParams()
    {
        parent::checkParams();

        if (empty($this->arParams['PROFILE_ID'])) {
            throw new Main\ArgumentNullException('PROFILE_ID');
        }
    }

    /**
     * @return Profile|mixed
     * @throws Main\ArgumentNullException
     * @throws Main\ArgumentOutOfRangeException
     * @throws Main\LoaderException
     * @throws Main\SystemException
     * @author Pavel Shulaev (https://rover-it.me)
     */
    protected function getProfile(): Profile
    {
        return ProfileRepository::getById($this->arParams['PROFILE_ID']);
    }

    /**
     * @param Source $mainSource
     * @return array
     * @throws Main\ArgumentException
     * @throws Main\ArgumentNullException
     * @throws Main\ArgumentOutOfRangeException
     * @throws Main\LoaderException
     * @throws Main\ObjectPropertyException
     * @throws Main\SystemException
     * @author Pavel Shulaev (https://rover-it.me)
     */
    protected function getActionPanel(Source $mainSource): array
    {
        $profiles     = ProfileRepository::getList([
            'filter' => ['=' . ProfileModel::UF_ACTIVE => true]
        ]);
        $presetsList  = [];
        $settingsList = [];

        /** @var Profile $profile */
        foreach ($profiles as $profile) {
            $source = $profile->getSource();

            $presetsList[] = [
                'ICONCLASS' => 'view',
                'TEXT'      => $profile->getName() . ' [' . $source->getTypeLabel() . '] (' . $source->getResultsCount() . ')',
                'ONCLICK'   => "jsUtils.Redirect(arguments, '/bitrix/admin/rover-acrm__result-list.php?profile_id=" . $profile->getId() . "&lang=" . LANGUAGE_ID . "')"
            ];

            if ($source === $mainSource) {
                $settingsList[] = [
                    'ICONCLASS' => 'edit',
                    'TEXT'      => $profile->getName() . ' [' . implode(', ', $profile->getAllowedSitesIds()) . ']',
                    'ONCLICK'   => "jsUtils.Redirect(arguments, '/bitrix/admin/rover-acrm__profile-element.php?profile_id=" . $profile->getId() . "&lang=" . LANGUAGE_ID . "')"
                ];
            }
        }

        $result = [
            [
                'TEXT'  => Loc::getMessage('rover-ape__action-back', ['#cnt#' => ProfileModel::getCount()]),
                'TITLE' => Loc::getMessage('rover-ape__action-back_title'),
                'LINK'  => '/bitrix/admin/rover-acrm__profile-list.php?lang=' . LANGUAGE_ID,
                'ICON'  => 'btn-list'
            ],
            [
                'TEXT'  => Loc::getMessage('rover-ape__action-settings'),
                'TITLE' => Loc::getMessage('rover-ape__action-settings_title'),
                //'LINK'  => '/bitrix/admin/rover-acrm__profile-element.php?profile_id=' . $this->arParams['ID']  . "&lang=" . LANGUAGE_ID,
                'ICON'  => 'btn-settings',
                'MENU'  => $settingsList
            ],
            [
                'TEXT'  => Loc::getMessage('rover-ape__action-presets'),
                'TITLE' => Loc::getMessage('rover-ape__action-presets_title'),
                'MENU'  => $presetsList,
                'ICON'  => 'btn-new'
            ]
        ];

        if ($this->arParams['READ_ONLY'] != 'Y') {
            $result[] = [
                'SEPARATOR' => true
            ];
            $result[] = [
                'TEXT'  => Loc::getMessage('rover-ape__action-module-settings'),
                'TITLE' => Loc::getMessage('rover-ape__action-module-settings_title'),
                'LINK'  => '/bitrix/admin/settings.php?lang=' . LANGUAGE_ID . '&mid=rover.amocrm&mid_menu=1',
                'ICON'  => 'btn-settings'
            ];
        }

        return $result;

    }

    /**
     * @return string
     * @author Pavel Shulaev (https://rover-it.me)
     */
    protected function getGridId(): string
    {
        return self::GRID_ID . $this->arParams['PROFILE_ID'];
    }

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
     * @throws Main\ArgumentNullException
     * @throws Main\ArgumentOutOfRangeException
     * @throws Main\SystemException|Main\LoaderException
     * @author Pavel Shulaev (https://rover-it.me)
     */
    protected function requestActions(Source $source)
    {
        if (mb_strlen($this->request->get('OK_MESSAGE'))) {
            Message::addOk($this->request->get('OK_MESSAGE'));
        }

        if ($this->arParams['READ_ONLY'] == 'Y') {
            return;
        }

        $action = $this->getRequestAction();
        switch ($action) {
            case self::ACTION__EXPORT:
                $this->requestExport($source);
                break;
        }
    }

    /**
     * @param Source $source
     * @throws Main\ArgumentNullException
     * @throws Main\ArgumentOutOfRangeException
     * @throws Main\SystemException|Main\LoaderException
     * @author Pavel Shulaev (https://rover-it.me)
     */
    protected function requestExport(Source $source)
    {
        $elementsIds = $this->request->get('ID');
        if (empty($elementsIds)) {
            return;
        }

        if (!Options::isEnabled()) {
            Message::addError(Loc::getMessage(Loc::getMessage("rover-ape__action-export-disabled")));

            return;
        }

        if (!is_array($elementsIds)) {
            $elementsIds = [$elementsIds];
        }

        /** @TODO: refactor */
        switch ($source::getType()) {
            case Source\WebForm::getType():
                $eventType = WebForm::getTypeStatic();
                break;
            case Source\PostEvent::getType():
                $eventType = PostEvent::getTypeStatic();
                break;
            case Source\Iblock::getType():
                $eventType = IblockElementUpdate::getTypeStatic();
                break;
            default:
                return;
        }

        foreach ($elementsIds as $elementId) {
            try {
                $event = EventFactory::buildBySourceEntityId($source, $eventType, $elementId);

                EventContext::addStatic($event);

            } catch (\Exception $e) {
                Message::addException($e);
            }
        }

        if (empty(Message::getErrors())) {
            $uri = new Uri($this->request->getRequestUri());

            $uri->deleteParams(["ID", $this->getActionButton()]);
            $uri->addParams([
                "OK_MESSAGE" => Options::isAgentHandleNew()
                    ? Loc::getMessage('rover-ape__export-success-2')
                    : Loc::getMessage('rover-ape__export-success-1')
            ]);

            LocalRedirect($uri->getUri());
        }
    }

    /**
     * @return string|null
     * @throws Main\ArgumentNullException
     * @throws Main\ArgumentOutOfRangeException
     * @throws Main\LoaderException
     * @throws Main\SystemException
     * @author Pavel Shulaev (https://rover-it.me)
     */
    private function getEventType(): ?string
    {
        $profile = $this->getProfile();
        switch ($profile->getSourceType()) {
            case Source\Iblock::getType():
                return IblockElementCreate::getType();
            case Source\PostEvent::getType():
                return PostEvent::getType();
            case Source\WebForm::getType():
                return WebForm::getType();
        }

        return null;
    }

    /**
     * @throws AmoCRMApiException
     * @throws AmoCRMoAuthApiException
     * @throws Main\ArgumentException
     * @throws Main\ArgumentNullException
     * @throws Main\ArgumentOutOfRangeException
     * @throws Main\LoaderException
     * @throws Main\ObjectPropertyException
     * @throws Main\SystemException
     * @author Pavel Shulaev (https://rover-it.me)
     */
    public function getResult(Source $source)
    {


        $this->arResult['NAV']          = $this->getNavObject();
        $this->arResult['ROWS']         = $this->getRows($source);
        $this->arResult['COLUMNS']      = $this->getColumns($source);
        $this->arResult['ACTION_PANEL'] = $this->getActionPanel($source);

        $this->arResult['GRID_ID']    = $this->getGridId();
        $this->arResult['SORT']       = $this->getGridSort();
        $this->arResult['PAGE_SIZES'] = $this->getPageSizes($this->arResult['NAV']);
        $this->arResult['ALL_IDS']    = $this->getAllIds($source);
        $this->arResult['EVENT_TYPE'] = $this->getEventType();
        $this->arResult['PROFILE']    = $this->getProfile()->getCollection()->getCollection();
    }

    /**
     * @param Source $source
     * @return array
     * @author Pavel Shulaev (https://rover-it.me)
     */
    private function getAllIds(Source $source): array
    {
        $elements = $source->getResultsData(['select' => ['ID']]);

        return array_keys($elements);
    }

    /**
     * @param Source $source
     * @return array
     * @throws AmoCRMApiException
     * @throws AmoCRMoAuthApiException
     * @throws Main\ArgumentException
     * @throws Main\ArgumentNullException
     * @throws Main\ArgumentOutOfRangeException
     * @throws Main\LoaderException
     * @throws Main\ObjectPropertyException
     * @throws Main\SystemException
     * @author Pavel Shulaev (https://rover-it.me)
     */
    protected function getRows(Source $source): array
    {
        global $APPLICATION;

        $curPage    = $APPLICATION->GetCurPage(true);
        $elements   = $this->getElements($source);
        $rows       = [];
        $links      = $this->getLinks();
        $baseDomain = $this->getProfile()->getConnection()->getBaseDomain('');

        foreach ($elements as $elementId => $element) {
            $linkedLeadId    = $links[$elementId][EntityTypesInterface::LEADS] ?? null;
            $linkedContactId = $links[$elementId][EntityTypesInterface::CONTACTS] ?? null;
            $linkedCompanyId = $links[$elementId][EntityTypesInterface::COMPANIES] ?? null;
            $linkedTaskId    = $links[$elementId][EntityTypesInterface::TASKS] ?? null;

            $data = [
                'ID'                                      => $elementId,
                'DATE_CREATE'                             => $source->getResultDateCreate($elementId),
                'LINK_' . EntityTypesInterface::LEADS     => $linkedLeadId,
                'LINK_' . EntityTypesInterface::CONTACTS  => $linkedContactId,
                'LINK_' . EntityTypesInterface::COMPANIES => $linkedCompanyId,
                'LINK_' . EntityTypesInterface::TASKS     => $linkedTaskId,
            ];

            foreach ($element as $field => $value) {
                $data['FIELD_' . $field] = $value;
            }

            $rows[] = [
                'id'       => $elementId,
                'data'     => $data,
                'columns'  => [
                    'LINK_' . EntityTypesInterface::LEADS     => $linkedLeadId
                        ? '<a target="_blank" href="https://' . $baseDomain . '/leads/detail/' . $linkedLeadId . '">' . $linkedLeadId . '</a>' : '',
                    'LINK_' . EntityTypesInterface::CONTACTS  => $linkedContactId
                        ? '<a target="_blank" href="https://' . $baseDomain . '/contacts/detail/' . $linkedContactId . '">' . $linkedContactId . '</a>' : '',
                    'LINK_' . EntityTypesInterface::COMPANIES => $linkedCompanyId
                        ? '<a target="_blank" href="https://' . $baseDomain . '/companies/detail/' . $linkedCompanyId . '">' . $linkedCompanyId . '</a>' : '',
                    // 'LINK_' . EntityTypesInterface::TASKS => $linkedTaskId
                    //      ? '<a href="https://' . $baseDomain .  '/tasks/detail/' . $linkedTaskId . '">' . $linkedTaskId . '</a>' : '',
                ],
                'actions'  => $this->arParams['READ_ONLY'] == 'Y' ? [] : [
                    [
                        'TEXT'      => Loc::getMessage('rover-ape__action-export'),
                        'ONCLICK'   => "jsUtils.Redirect(arguments, '" . $curPage
                            . "?profile_id=" . $this->arParams['PROFILE_ID']
                            . "&ID=" . $elementId
                            . "&lang=" . LANGUAGE_ID
                            . "&" . $this->getActionButton() . "=" . self::ACTION__EXPORT . "')",
                        "ICONCLASS" => "copy",
                        //'DEFAULT'   => true
                    ],
                ],
                'editable' => $this->arParams['READ_ONLY'] != 'Y',
            ];
        }

        return $rows;
    }

    /**
     * @param Source $source
     * @return mixed
     * @throws Main\ArgumentNullException
     * @author Pavel Shulaev (https://rover-it.me)
     */
    protected function getElements(Source $source)
    {
        $cacheId = HitCache::genId(__METHOD__, [$source->getId()]);
        if (!HitCache::exists($cacheId)) {
            $sort     = $this->getGridSort();
            $elements = $source->getResultsData(['order' => $sort['sort']], $this->arResult['NAV']);

            HitCache::set($cacheId, $elements);
        }

        return HitCache::get($cacheId);
    }

    /**
     * @param Source $source
     * @return array
     * @author Pavel Shulaev (https://rover-it.me)
     */
    protected function getColumns(Source $source): array
    {
        $labels = $source->getLabelMap();
        $result = [
            [
                'id'      => 'ID',
                'name'    => 'ID',
                'default' => false,
                'sort'    => 'ID',
            ]
        ];

        foreach ($labels as $code => $name) {
            $result[] = [
                'id'      => 'FIELD_' . $code,
                'name'    => $name,
                'default' => true,
                //'sort'      => $code
            ];
        }

        $result[] = [
            'id'      => 'DATE_CREATE',
            'name'    => Loc::getMessage('rover-ape__datetime_created'),
            'default' => true,
            //'type'      => 'date'
        ];

        $result[] = [
            'id'      => 'LINK_' . EntityTypesInterface::LEADS,
            'name'    => Loc::getMessage('rover-ape__header-' . EntityTypesInterface::LEADS),
            'default' => true,
            //'type'      => 'date'
        ];

        $result[] = [
            'id'      => 'LINK_' . EntityTypesInterface::CONTACTS,
            'name'    => Loc::getMessage('rover-ape__header-' . EntityTypesInterface::CONTACTS),
            'default' => true,
            //'type'      => 'date'
        ];

        $result[] = [
            'id'      => 'LINK_' . EntityTypesInterface::COMPANIES,
            'name'    => Loc::getMessage('rover-ape__header-' . EntityTypesInterface::COMPANIES),
            'default' => true,
            //'type'      => 'date'
        ];

        $result[] = [
            'id'      => 'LINK_' . EntityTypesInterface::TASKS,
            'name'    => Loc::getMessage('rover-ape__header-' . EntityTypesInterface::TASKS),
            'default' => true,
            //'type'      => 'date'
        ];

        return $result;
    }

    /**
     * @return array
     * @throws Main\ArgumentException
     * @throws Main\ArgumentNullException
     * @throws Main\ArgumentOutOfRangeException
     * @throws Main\LoaderException
     * @throws Main\ObjectPropertyException
     * @throws Main\SystemException
     * @throws AmoCRMApiException
     * @throws AmoCRMoAuthApiException
     * @author Pavel Shulaev (https://rover-it.me)
     */
    protected function getLinks(): array
    {
        $profile = $this->getProfile();

        $query = [
            'filter' => [
                '=' . LinkModel::UF_BX_ENTITY_ID   => array_keys($this->getElements($profile->getSource())),
                '=' . LinkModel::UF_BX_ENTITY_TYPE => $profile->getSourceType(),
                '=' . LinkModel::UF_ACCOUNT_ID     => $profile->getConnection()->getAccountId(),
            ],
            'select' => [LinkModel::UF_BX_ENTITY_ID, LinkModel::UF_AMO_ENTITY_TYPE, LinkModel::UF_AMO_ENTITY_ID]
        ];

        $links   = [];
        $dbLinks = LinkModel::getList($query);
        while ($link = $dbLinks->fetch()) {
            $links[$link[LinkModel::UF_BX_ENTITY_ID]][$link[LinkModel::UF_AMO_ENTITY_TYPE]] =
                $link[LinkModel::UF_AMO_ENTITY_ID];
        }

        return $links;
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

            if (DependenceBuilder::buildBase()->check()->isSuccess()) {
                $source = $this->getProfile()->getSource();
                $this->requestActions($source);
                $this->getResult($source);
                $this->checkRights();
            }

            $this->includeComponentTemplate();
            $this->setTitle();
        } catch (Throwable $e) {
            ExceptionService::handleException($e, true);
        }
    }

    protected function getRepositoryClass(): string
    {
        return ProfileRepository::class;
    }

    /**
     * @return string
     * @throws Main\ArgumentNullException
     * @throws Main\ArgumentOutOfRangeException
     * @throws Main\LoaderException
     * @throws Main\SystemException
     * @author Pavel Shulaev (https://rover-it.me)
     */
    protected function getTitle(): string
    {
        $source = $this->getProfile()->getSource();

        return Loc::getMessage('rover-ape__title', [
            '#name#' => $source->getName(),
            '#type#' => $source->getTypeLabel(),
        ]);
    }

    protected function getEditableFields(): array
    {
        return [];// TODO: Implement getEditableFields() method.
    }
}