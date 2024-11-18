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
use AmoCRM\Exceptions\AmoCRMApiPageNotAvailableException;
use AmoCRM\Exceptions\AmoCRMMissedTokenException;
use AmoCRM\Exceptions\AmoCRMoAuthApiException;
use AmoCRM\Helpers\EntityTypesInterface;
use Bitrix\Main;
use Bitrix\Main\ArgumentNullException;
use Bitrix\Main\ArgumentOutOfRangeException;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\SystemException;
use Bitrix\Main\Web\Uri;
use Bitrix\Sale\Internals\StatusLangTable;
use Rover\AmoCRM\Component\ElementBase;
use Rover\AmoCRM\Config\Tabs;
use Rover\AmoCRM\Dependence\Atom\AgentsOnCron;
use Rover\AmoCRM\Dependence\Atom\Profile\Service;
use Rover\AmoCRM\Directory\Entity\Connection;
use Rover\AmoCRM\Directory\Entity\Profile;
use Rover\AmoCRM\Directory\Model\ConnectionModel;
use Rover\AmoCRM\Directory\Model\ProfileModel;
use Rover\AmoCRM\Directory\Repository\ConnectionRepository;
use Rover\AmoCRM\Directory\Repository\ProfileRepository;
use Rover\AmoCRM\Factory\SourceFactory;
use Rover\AmoCRM\Mapping;
use Rover\AmoCRM\Mapping\Companies;
use Rover\AmoCRM\Mapping\Contacts;
use Rover\AmoCRM\Mapping\Leads;
use Rover\AmoCRM\Mapping\Tasks;
use Rover\AmoCRM\Options;
use Rover\AmoCRM\Service\Duplicate;
use Rover\AmoCRM\Service\Duplicate\OptionsBuilder;
use Rover\AmoCRM\Service\ExceptionService;
use Rover\AmoCRM\Service\Message;
use Rover\AmoCRM\Service\Params;
use Rover\AmoCRM\Service\PathService;
use Rover\AmoCRM\Service\Tools;
use Rover\AmoCRM\Snippet\Mapping as MappingSnippet;
use Rover\AmoCRM\Snippet\Table as TableSnippet;

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
class RoverAmoCrmProfileElement extends ElementBase
{
    const FORM_ID = 'amocrm_preset_update';

    /**
     * @author Pavel Shulaev (https://rover-it.me)
     */
    public function onIncludeComponentLang(): void
    {
        $this->includeComponentLang(basename(__FILE__));

        Loc::loadMessages(__FILE__);
        Loc::loadMessages($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/rover.amocrm/lib/config/tabs.php'); // for tabs
    }

    /**
     * @return string
     * @author Pavel Shulaev (https://rover-it.me)
     */
    protected function getRepositoryClass(): string
    {
        return ProfileRepository::class;
    }

    /**
     * @param bool $status
     * @return string
     * @author Pavel Shulaev (https://rover-it.me)
     */
    protected function getStatusLabel(bool $status): string
    {
        return $status
            ? (Tools::isUtf8() ? ' ✔' : ' [+]')
            : (Tools::isUtf8() ? ' ✘' : ' [-]');
    }

    /**
     * @param $arParams
     * @return mixed
     * @author Pavel Shulaev (https://rover-it.me)
     */
    public function onPrepareComponentParams($arParams): array
    {
        $arParams['ID'] = intval($arParams['ID']);

        return $arParams;
    }

    /**
     * @return void
     * @throws AmoCRMApiException
     * @throws AmoCRMMissedTokenException
     * @throws AmoCRMoAuthApiException
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     * @throws Main\ArgumentException
     * @throws Main\LoaderException
     * @throws Main\NotImplementedException
     * @throws Main\ObjectPropertyException
     * @throws SystemException
     * @throws \AmoCRM\Exceptions\InvalidArgumentException
     * @author Pavel Shulaev (https://rover-it.me)
     */
    protected function requestAddProfile()
    {
        if (empty($this->request->get('connection_id'))) {
            throw new Main\ArgumentNullException('connection_id');
        }

        /** @var Connection $connection */
        if (!$connection = ConnectionRepository::getById($this->request->get('connection_id'))) {
            throw new Main\ArgumentOutOfRangeException('connection_id');
        }

        if (!$connection->isAvailable()) {
            throw new SystemException('Connection unavailable');
        }

        // try to create preset
        $source =
            SourceFactory::buildByTypeId($this->request->get('source_type'), intval($this->request->get('source_id')));
        $result = ProfileModel::addBySource($source, $connection);
        if (!$result->isSuccess()) {
            throw new SystemException(implode('<br>', $result->getErrorMessages()));
        }

        $uri = new Uri($this->request->getRequestUri());

        $uri->deleteParams(['source_type', 'source_id', 'connection_id']);
        $uri->addParams(["profile_id" => $result->getId()]);

        LocalRedirect($uri->getUri());
    }

    /**
     * @return array
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     * @throws Main\ArgumentException
     * @throws Main\LoaderException
     * @throws SystemException
     * @author Pavel Shulaev (https://rover-it.me)
     */
    protected function getActionPanel(): array
    {
        $result = [
            [
                'TEXT'  => Loc::getMessage('rover-acpe__action-back', ['#cnt#' => ProfileModel::getCount()]),
                'TITLE' => Loc::getMessage('rover-acpe__action-back_title'),
                'LINK'  => PathService::getProfileList(),
                'ICON'  => 'btn-list'
            ],
            [
                'TEXT'  => Loc::getMessage('rover-acpe__action-elements',
                    ['#cnt#' => $this->getProfile()->getResultsCount()]),
                'TITLE' => Loc::getMessage('rover-acpe__action-elements_title'),
                'LINK'  => PathService::getProfileItems($this->arParams['ID']),
                'ICON'  => 'btn-list'
            ],
            [
                'TEXT'  => Loc::getMessage('rover-acpe__action-connections', ['#cnt#' => ConnectionModel::getCount()]),
                'TITLE' => Loc::getMessage('rover-acpe__action-connections_title'),
                'LINK'  => PathService::getConnectionList(),
                'ICON'  => 'btn-list'
            ],
        ];

        if ($this->arParams['READ_ONLY'] != 'Y') {
            $result[] = ['SEPARATOR' => true];
            $result[] = [
                'TEXT'  => Loc::getMessage('rover-acpe__action-settings'),
                'TITLE' => Loc::getMessage('rover-acpe__action-settings_title'),
                'LINK'  => PathService::getSettings(),
                'ICON'  => 'btn-settings'
            ];
        }

        return $result;
    }

    /**
     * @param $text
     * @return string
     * @throws ArgumentNullException
     * @author Pavel Shulaev (https://rover-it.me)
     */
    protected function getHelp($text): string
    {
        $text = trim($text);
        if (!strlen($text)) {
            throw new ArgumentNullException('text');
        }

        return '<br><small style="color: #777;">' . $text . '</small>';
    }

    /**
     * @param $text
     * @return string
     * @throws ArgumentNullException
     * @author Pavel Shulaev (https://rover-it.me)
     */
    protected function getPostInput($text): string
    {
        $text = trim($text);
        if (!strlen($text)) {
            throw new ArgumentNullException('text');
        }

        return 'b">' . $text . '<a a="b';
    }

    /**
     * @param string $id
     * @param array $params
     * @param string|null $name
     * @param string|null $help
     * @param null $value
     * @return array
     * @throws ArgumentNullException
     * @author Pavel Shulaev (https://rover-it.me)
     */
    public function getCheckbox(
        string $id,
        array $params = [],
        string $name = null,
        string $help = null,
        $value = null
    ): array
    {
        $id = trim($id);
        if (!mb_strlen($id)) {
            throw new ArgumentNullException('id');
        }

        $field = [
            'id'       => $id,
            'name'     => $name ?? ProfileModel::getFieldL18n($id),
            'required' => false,
            'type'     => 'checkbox',
        ];

        if (!is_null($value)) {
            $field['value'] = $value;
        }

        if (count($params)) {
            $field['params'] = $params;
        } else {
            if (is_null($help)) {
                if (mb_strlen(Loc::getMessage('rover-acpe__field-' . $id . '_help'))) {
                    $help = Loc::getMessage('rover-acpe__field-' . $id . '_help');
                } // @TODO: delete
                elseif (mb_strlen(Loc::getMessage($id . '_help'))) {
                    $help = Loc::getMessage($id . '_help');
                }
            }

            if (!is_null($help)) {
                $field['params']['a'] = $this->getPostInput($this->getHelp($help));
            }
        }

        return $field;
    }

    /**
     * @param $entityType
     * @param array $params
     * @return string
     * @throws AmoCRMApiException
     * @throws AmoCRMMissedTokenException
     * @throws AmoCRMoAuthApiException
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     * @throws Main\ArgumentException
     * @throws Main\LoaderException
     * @throws Main\ObjectPropertyException
     * @throws SystemException
     * @author Pavel Shulaev (https://rover-it.me)
     */
    protected function getDuplicateControlRow($entityType, array $params = []): string
    {
        $entityType = trim($entityType);
        if (!mb_strlen($entityType)) {
            throw new ArgumentNullException('type');
        }

        $profile = $this->getProfile();

        return TableSnippet::getCheckBoxRow([
                'name'     => ProfileModel::UF_DUPLICATE_DATA . "[$entityType][" . ProfileModel::DUPLICATE_ACTIVE . ']',
                'checked'  => $profile->isDuplicateControlActive($entityType),
                'disabled' => !$profile->getConnection()->isApiFilterEnabled()
            ] + $params,
            Loc::getMessage('rover-acpe__duplicate-control-label')
        );
    }

    /**
     * @param string $entityType
     * @param array $options
     * @param array $params
     * @return string
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     * @throws Main\LoaderException
     * @throws SystemException
     * @author Pavel Shulaev (https://rover-it.me)
     */
    protected function getDuplicateFieldsRow(string $entityType, array $options, array $params = []): string
    {
        $entityType = trim($entityType);
        if (!mb_strlen($entityType)) {
            throw new ArgumentNullException('entityType');
        }

        return TableSnippet::getSelectRow([
                'options'  => $options,
                'selected' => $this->getProfile()
                        ->getDuplicateData()[$entityType][ProfileModel::DUPLICATE_FIELDS] ?? null,
                'params'   => [
                    'name'     => ProfileModel::UF_DUPLICATE_DATA . "[$entityType][" . ProfileModel::DUPLICATE_FIELDS . '][]',
                    'id'       => ProfileModel::UF_DUPLICATE_DATA . "[$entityType][" . ProfileModel::DUPLICATE_FIELDS . ']',
                    'multiple' => true,
                    'size'     => min(6, count($options))
                ]
            ] + $params,
            Loc::getMessage('rover-acpe__duplicate-fields-label'),
            Loc::getMessage('rover-acpe__duplicate-fields-help')
        );
    }

    /**
     * @param string $entityType
     * @param array $params
     * @return string
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     * @throws Main\LoaderException
     * @throws SystemException
     * @author Pavel Shulaev (https://rover-it.me)
     */
    protected function getDuplicateLogicRow(string $entityType, array $params = []): string
    {
        $entityType = trim($entityType);
        if (!mb_strlen($entityType)) {
            throw new ArgumentNullException('entityType');
        }

        return TableSnippet::getSelectRow([
                'options'  => [
                    Duplicate::LOGIC__AND => Loc::getMessage('rover-acpe__duplicate-logic-' . Duplicate::LOGIC__AND . '_label'),
                    Duplicate::LOGIC__OR  => Loc::getMessage('rover-acpe__duplicate-logic-' . Duplicate::LOGIC__OR . '_label'),
                ],
                'selected' => $this->getProfile()
                        ->getDuplicateData()[$entityType][ProfileModel::DUPLICATE_LOGIC] ?? null,
                'params'   => [
                    'name' => ProfileModel::UF_DUPLICATE_DATA . "[$entityType][" . ProfileModel::DUPLICATE_LOGIC . ']',
                    'id'   => ProfileModel::UF_DUPLICATE_DATA . "[$entityType][" . ProfileModel::DUPLICATE_LOGIC . ']',
                ]
            ] + $params, Loc::getMessage('rover-acpe__duplicate-logic-label'));
    }

    /**
     * @param string $entityType
     * @param array $params
     * @return string
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     * @throws Main\LoaderException
     * @throws SystemException
     * @author Pavel Shulaev (https://rover-it.me)
     */
    public function getDuplicateActionRow(string $entityType, array $params = []): string
    {
        $entityType = trim($entityType);
        if (!mb_strlen($entityType)) {
            throw new ArgumentNullException('entityType');
        }

        $help = $this->getProfile()->isUnsorted()
            ? Loc::getMessage('rover-acrm__duplicate-action-unsorted-help')
            : '';

        $actions = [
            Duplicate::ACTION__ADD_NOTE => Loc::getMessage('rover-acpe__duplicate-action-' . Duplicate::ACTION__ADD_NOTE . '_label'),
            Duplicate::ACTION__COMBINE  => Loc::getMessage('rover-acpe__duplicate-action-' . Duplicate::ACTION__COMBINE . '_label'),
            Duplicate::ACTION__SKIP     => Loc::getMessage('rover-acpe__duplicate-action-' . Duplicate::ACTION__SKIP . '_label'),
        ];

        return TableSnippet::getSelectRow([
                'options'  => $actions,
                'selected' => $this->getProfile()
                        ->getDuplicateData()[$entityType][ProfileModel::DUPLICATE_ACTION] ?? null,
                'params'   => [
                    'name' => ProfileModel::UF_DUPLICATE_DATA . "[$entityType][" . ProfileModel::DUPLICATE_ACTION . ']',
                    'id'   => ProfileModel::UF_DUPLICATE_DATA . "[$entityType][" . ProfileModel::DUPLICATE_ACTION . ']',
                ]
            ] + $params, Loc::getMessage('rover-acpe__duplicate-action-label'), $help);
    }

    /**
     * @param string $id
     * @param string $name
     * @param array $options
     * @param bool $isMultiple
     * @param string $help
     * @param bool $required
     * @param int $size
     * @param bool $disabled
     * @param string|null $value
     * @return array
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     * @throws Main\LoaderException
     * @throws SystemException
     * @author Pavel Shulaev (https://rover-it.me)
     */
    protected function getSelect(
        string $id,
        string $name,
        array $options = [],
        bool $isMultiple = false,
        string $help = '',
        bool $required = false,
        int $size = 4,
        bool $disabled = false,
        string $value = null
    ): array
    {
        $id = trim($id);
        if (!strlen($id)) {
            throw new ArgumentNullException('id');
        }

        $config = [
            'options'  => $options,
            'params'   => [
                'id'   => $id,
                'name' => $id
            ],
            'selected' => $value ?? $this->getProfile()->getCollection()->offsetGet($id),
            'help'     => $help,
        ];

        if ($isMultiple) {
            $config['params']['multiple'] = 'multiple';
            $config['params']['size']     = min($size, count($options));
            $config['params']['name']     .= '[]';
        }

        if ($disabled) {
            $config['params']['disabled'] = 'disabled';
        }

        if ($required) {
            $config['params']['required'] = 'required';
        }

        $select = $this->getSelectInput($config);

        return [
            'id'       => $id . ($isMultiple ? '[]' : ''),
            'name'     => $name,
            'required' => $required,
            'type'     => 'custom',
            'value'    => $select
        ];
    }

    /**
     * @return array[]
     * @throws AmoCRMApiException
     * @throws AmoCRMoAuthApiException
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     * @throws Main\ArgumentException
     * @throws Main\LoaderException
     * @throws Main\ObjectPropertyException
     * @throws ReflectionException
     * @throws SystemException
     * @author Pavel Shulaev (https://rover-it.me)
     */
    protected function getTabs(): array
    {
        $tabs = [$this->getMainTab()];

        if ($this->getProfile()->getSource()->isOrder()) {
            $tabs[] = $this->getOrderTab();
        }

        return array_merge($tabs, [
            $this->getLeadTab(),
            $this->getContactCompanyTab(EntityTypesInterface::CONTACTS),
            $this->getContactCompanyTab(EntityTypesInterface::COMPANIES),
            $this->getTaskTab()
        ]);
    }

    /**
     * @param $name
     * @param $options
     * @param $groupName
     * @return string
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     * @throws Main\LoaderException
     * @throws SystemException
     * @author Pavel Shulaev (https://rover-it.me)
     */
    protected function getSelectGroupInput($name, $options, $groupName): string
    {
        // change group script
        $html        = '';
        $optionsId   = crc32(serialize($options));
        $resultItems = [];

        // for keeping sort
        foreach ($options as $itemId => $itemValue) {
            $item = [
                'id'   => $itemId,
                'name' => $itemValue['name']
            ];

            if (isset($itemValue['options'])) {
                $itemOptions = [];
                foreach ($itemValue['options'] as $optionId => $optionName) {
                    $itemOptions[] = ['id' => $optionId, 'name' => $optionName];
                }

                $item['options'] = $itemOptions;
            }

            $resultItems[] = $item;
        }

        $html .= '
			<script type="text/javascript">
                function OnType_' . $optionsId . '_Changed(typeSelect, selectID)
                {
                    var items       = ' . CUtil::PhpToJSObject($resultItems) . ';
                    var selected    = BX(selectID), options;
                  
                    if(!!selected)
                    {
                        for(var i=selected.length-1; i >= 0; i--){
                            selected.remove(i);
                        }
                        
                        // search selected group
                        for(var k in items)
                        {
                            if ((items[k]["id"] == typeSelect.value)
                                && (items[k]["options"]))
                            {
                                options = items[k]["options"];
                            }
                        }
                        
                        if (!!options) {
                            for(var j in options)
                            {
                                var newOption = new Option(options[j]["name"], options[j]["id"], false, false);
                                selected.options.add(newOption);
                            }
                        }
                    }
                }
			</script>
			';

        $groupValue = $this->getProfile()->getCollection()->offsetGet($groupName);
        if (empty($groupValue)) {
            $groupValue = array_key_first($options);
        }

        $onChangeGroup = 'OnType_' . $optionsId . '_Changed(this, \'' . CUtil::JSEscape($name) . '\');';

        $html .= '<select name="' . $groupName . '" id="' . $groupName . '" onchange="' . htmlspecialcharsbx($onChangeGroup) . '">' . "\n";

        foreach ($options as $key => $optionValue) {
            $html .= '<option value="' . htmlspecialcharsbx($key) . '"' . ($groupValue == $key ? ' selected' : '') . '>'
                . htmlspecialcharsEx($optionValue['name'] ?? $key)
                . '</option>' . "\n";
        }

        $html .= "</select>\n";
        $html .= "&nbsp;\n";
        $html .= '<select name="' . $name . '" id="' . $name . '">' . "\n";

        $value = $this->getProfile()->getCollection()->offsetGet($name);

        if (!is_null($groupValue)) {
            foreach ($options[$groupValue]['options'] as $key => $optionValue2) {
                $html .= '<option value="' . htmlspecialcharsbx($key) . '"' . ($key == $value ? ' selected' : '') . '>'
                    . htmlspecialcharsEx($optionValue2)
                    . '</option>' . "\n";
            }
        }

        $html .= "</select>\n";

        return $html;
    }

    /**
     * @return array
     * @throws AmoCRMApiException
     * @throws AmoCRMoAuthApiException
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     * @throws Main\ArgumentException
     * @throws Main\LoaderException
     * @throws Main\ObjectPropertyException
     * @throws SystemException
     * @author Pavel Shulaev (https://rover-it.me)
     */
    protected function getMainTab(): array
    {
        $profile         = $this->getProfile();
        $source          = $profile->getSource();
        $connection      = $profile->getConnection();
        $sites           = ['' => Loc::getMessage('rover-acpe__all')] + Params::getSites();
        $responsibleList = ['' => Loc::getMessage('rover-acpe__default')] + Params::getAmoUsers($connection);


        $fields = [
            $this->getCheckbox(ProfileModel::UF_ACTIVE),
            [
                'id'     => ProfileModel::UF_NAME,
                'name'   => ProfileModel::getFieldL18n(ProfileModel::UF_NAME),
                //'required'  => true,
                'type'   => 'text',
                'params' => [
                    'size' => 50,
                    'a'    => $this->getPostInput($this->getHelp(
                        Loc::getMessage('rover-acpe__field-' . ProfileModel::UF_NAME . '_help', [
                            '#type#'       => "<a title='{$source->getName()}' href='" . PathService::getSourceEdit($source) . "'>{$source->getName()}</a> ({$source->getTypeLabel()})",
                            '#connection#' => "<a title='{$connection->getName()}' href='" . PathService::getConnectionElement($connection->getId()) . "'>{$connection->getName()}</a> ({$connection->getBaseDomain('')})"
                        ]))
                    )
                ]
            ],
            $this->getSelect(ProfileModel::UF_RESPONSIBLE_LIST,
                ProfileModel::getFieldL18n(ProfileModel::UF_RESPONSIBLE_LIST),
                $responsibleList, true,
                Loc::getMessage('rover-acpe__field-' . ProfileModel::UF_RESPONSIBLE_LIST . '_help')),
            $this->getSelect(ProfileModel::UF_SITES_IDS, ProfileModel::getFieldL18n(ProfileModel::UF_SITES_IDS),
                $sites, true,
                Loc::getMessage('rover-acpe__field-' . ProfileModel::UF_SITES_IDS . '_help')),
            [
                'id'       => ProfileModel::UF_COMMON_TAGS,
                'name'     => ProfileModel::getFieldL18n(ProfileModel::UF_COMMON_TAGS),
                'required' => false,
                'type'     => 'text',
                'params'   => [
                    'size' => 50,
                    'id'   => ProfileModel::UF_COMMON_TAGS,
                    'a'    => $this->getPostInput($this->getProfile()
                            ->getMapping(Leads::class)
                            ->getPlaceholder()
                            ->getButton(ProfileModel::UF_COMMON_TAGS)
                        . $this->getHelp(Loc::getMessage('rover-acpe__field-' . ProfileModel::UF_COMMON_TAGS . '_help')))
                ]
            ],
        ];

        $addComplexAllowed   = Options::isAllowAddComplex();
        $addComplexAvailable = in_array(EntityTypesInterface::LEADS, $this->getProfile()->getAmoEntities());

        if ($addComplexAllowed) {
            $fields[] = $this->getCheckbox(ProfileModel::UF_ADD_COMPLEX, $addComplexAvailable
                ? [] : [
                    'disabled' => 'disabled',
                    'a'        => $this->getPostInput($this->getHelp(Loc::getMessage('rover-acpe__field-' . ProfileModel::UF_ADD_COMPLEX . '_no-lead-help')))
                ]);
        }
        $fields[] = $this->getCheckbox(ProfileModel::UF_HIT_DUPLICATES);

        return [
            'id'     => 'header-main',
            'name'   => Loc::getMessage('rover-acpe__header-main'),
            'title'  => Loc::getMessage('rover-acpe__header-main'),
            'fields' => $fields
        ];
    }

    /**
     * @return array
     * @throws AmoCRMApiException
     * @throws AmoCRMoAuthApiException
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     * @throws Main\ArgumentException
     * @throws Main\LoaderException
     * @throws Main\ObjectPropertyException
     * @throws SystemException
     * @author Pavel Shulaev (https://rover-it.me)
     */
    protected function getOrderTab(): array
    {
        $profile    = $this->getProfile();
        $connection = $profile->getConnection();

        $orderStatuses = StatusLangTable::getList([
            'filter' => ['=LID' => LANGUAGE_ID, '=STATUS.TYPE' => 'O'],
            'order'  => ['STATUS.SORT' => 'ASC'],
            'select' => ['STATUS_ID', 'NAME'],
            'cache'  => ['ttl' => 3600]
        ]);
        $leadStatuses  = [' ' => Loc::getMessage('rover-ac__not-set')] + Params::getLeadPipelinesStatuses($connection);

        $bxToAmoProductsParams = $amoToBxProductsParams = [];

        if ($this->getProfile()->isUnsorted() || !$this->getProfile()->getConnection()->isCatalogElementsEnabled()) {
            $bxToAmoProductsParams['disabled'] = $amoToBxProductsParams['disabled'] = 'disabled';
            $catalogElementsHelp               = !$this->getProfile()->getConnection()->isCatalogElementsEnabled()
                ? Loc::getMessage('rover-acpe__field-products_disabled_help')
                : Loc::getMessage('rover-acpe__field-products_unsorted_help');

            $bxToAmoProductsParams['a'] =
            $amoToBxProductsParams['a'] = $this->getPostInput($this->getHelp($catalogElementsHelp));
        }

        $count = $this->getProfile()->isAmoBxStatuses()
            + $this->getProfile()->isBxAmoStatuses()
            + $this->getProfile()->isAmoBxProducts()
            + $this->getProfile()->isBxAmoProducts();

        if ($count == 4) {
            $status = Tools::isUtf8() ? ' ✔' : ' [+]';
        } elseif ($count == 0) {
            $status = Tools::isUtf8() ? ' ✘' : ' [-]';
        } else {
            $status = Tools::isUtf8() ? ' ♦' : ' [*]';
        }

        $tab = [
            'id'     => 'header-order',
            'name'   => Loc::getMessage('rover-acpe__header-order') . $status,
            'title'  => Loc::getMessage('rover-acpe__header-order'),
            'fields' => [
                [
                    'id'   => 'fields-settings-order-bx-to-amo',
                    'name' => Loc::getMessage('rover-acpe__header-fields-bx-to-amo-order'),
                    'type' => 'section',
                ],
                $this->getCheckbox(ProfileModel::UF_BX_AMO_STATUSES),
                $this->getCheckbox(ProfileModel::UF_BX_AMO_PRODUCTS, $bxToAmoProductsParams),
                [
                    'id'   => 'fields-settings-order-amo-to-bx',
                    'name' => Loc::getMessage('rover-acpe__header-fields-amo-to-bx-order'),
                    'type' => 'section',
                ],
                $this->getCheckbox(ProfileModel::UF_AMO_BX_STATUSES),
                $this->getCheckbox(ProfileModel::UF_AMO_BX_PRODUCTS, $amoToBxProductsParams),
                [
                    'id'   => 'fields-settings-order-statuses-mapping',
                    'name' => Loc::getMessage('rover-acpe__header-fields-statuses-mapping-order'),
                    'type' => 'section',
                ]
            ]
        ];

        while ($orderStatus = $orderStatuses->fetch()) {
            $tab['fields'][] = [
                'id'       => ProfileModel::UF_MAPPING_DATA . '[' . Tabs::ORDER_STATUSES_MAPPING . '-' . $orderStatus['STATUS_ID'] . ']',
                'name'     => $orderStatus['NAME'],
                'required' => false,
                'type'     => 'select',
                'items'    => $leadStatuses,
                'value'    => $this->getProfile()
                        ->getMappingData()[Tabs::ORDER_STATUSES_MAPPING . '-' . $orderStatus['STATUS_ID']] ?? null
            ];
        }

        $tab['fields'][] = [
            'id'       => ProfileModel::UF_MAPPING_DATA . '[' . Tabs::ORDER_STATUSES_MAPPING_CANCELLED . ']',
            'name'     => Loc::getMessage('rover-acpe__field-' . Tabs::ORDER_STATUSES_MAPPING_CANCELLED . '_label'),
            'required' => false,
            'type'     => 'select',
            'items'    => $leadStatuses,
            'value'    => $this->getProfile()->getMappingData()[Tabs::ORDER_STATUSES_MAPPING_CANCELLED] ?? null
        ];

        return $tab;
    }

    /**
     * @throws AmoCRMApiException
     * @throws AmoCRMoAuthApiException
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     * @throws Main\ArgumentException
     * @throws Main\LoaderException
     * @throws Main\ObjectPropertyException
     * @throws SystemException
     * @author Pavel Shulaev (https://rover-it.me)
     */
    protected function getLeadTab(): array
    {
        $profile    = $this->getProfile();
        $connection = $profile->getConnection();

        if ($connection->isMultiPipeline()) {
            $pipelineField = [
                'id'    => ProfileModel::UF_LEAD_STATUS_ID,
                'name'  => Loc::getMessage('rover-acpe__field-' . ProfileModel::UF_LEAD_STATUS_ID . '_label_multy'),
                'type'  => 'custom',
                'value' => $this->getSelectGroupInput(ProfileModel::UF_LEAD_STATUS_ID,
                    Params::getGroupedLeadStatuses($connection), ProfileModel::UF_LEAD_PIPELINE_ID)
            ];
        } else {
            $pipelineField = $this->getSelect(
                ProfileModel::UF_LEAD_STATUS_ID,
                Loc::getMessage('rover-acpe__field-' . ProfileModel::UF_LEAD_STATUS_ID . '_label_single'),
                Params::getLeadStatuses($connection)
            );

            // add hidden pipeline id
            $pipelines              = Params::getLeadPipelines($connection);
            $pipelineField['value'] .= '<input type="hidden" name="' . ProfileModel::UF_LEAD_PIPELINE_ID . '" value="' . key($pipelines) . '">';
        }

        return [
            'id'     => 'header-' . EntityTypesInterface::LEADS,
            'name'   => Loc::getMessage('rover-acpe__header-' . EntityTypesInterface::LEADS)
                . $this->getStatusLabel($this->getProfile()->isLeadsEnabled()),
            'title'  => Loc::getMessage('rover-acpe__header-' . EntityTypesInterface::LEADS)
                . ($this->getProfile()->isLeadsEnabled() ? '' : ' ' . Loc::getMessage('rover-acpe__disabled')),
            'fields' => [
                $this->getCheckbox(ProfileModel::UF_AMO_ENTITIES . '[' . EntityTypesInterface::LEADS . ']', [],
                    Loc::getMessage('rover-acpe__field-create-' . EntityTypesInterface::LEADS),
                    Loc::getMessage('rover-acpe__field-create-' . EntityTypesInterface::LEADS . '_help'),
                    $this->getProfile()->isLeadsEnabled()
                ),
                $pipelineField,
                $this->getCheckbox(ProfileModel::UF_LEAD_VISITOR_UID),
                [
                    'id'      => 'mapping-fields-subtabs_' . Leads::getUFName(),
                    'type'    => 'custom',
                    'colspan' => true,
                    'value'   => $this->getEntitySubTabs($this->getProfile()->getMapping(Leads::class),
                        OptionsBuilder::getInstance($this->getProfile())
                            ->buildByEntityType(EntityTypesInterface::LEADS))
                ],
            ]
        ];
    }

    /**
     * @param Mapping $mapping
     * @param array $availableDuplicateFields
     * @return false|string
     * @throws AmoCRMApiException
     * @throws AmoCRMMissedTokenException
     * @throws AmoCRMoAuthApiException
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     * @throws Main\ArgumentException
     * @throws Main\LoaderException
     * @throws Main\NotImplementedException
     * @throws Main\ObjectPropertyException
     * @throws SystemException
     * @author Pavel Shulaev (https://rover-it.me)
     */
    protected function getEntitySubTabs(Mapping $mapping, array $availableDuplicateFields)
    {
        $entityType = $mapping->getAmoEntity();
        $profile    = $this->getProfile();
        $connection = $profile->getConnection();

        $enabled = $profile->isDuplicateControlActive($entityType)
            && $connection->isApiFilterEnabled()
            && !$profile->canAddComplex();

        $duplicateNote = '';
        if ($profile->canAddComplex()) {
            $duplicateNote = Loc::getMessage('rover-acpe__duplicates-add-complex-note');
        } elseif (!$connection->isApiFilterEnabled()) {
            $duplicateNote = Loc::getMessage('rover-acpe__duplicates-no-api-filter-note',
                ['#base-domain#' => $connection->getBaseDomain()]);
        }

        $initTabs = [
            [
                "DIV"   => "opt_site_mapping_" . $mapping::getUFName(),
                "TAB"   => Loc::getMessage('rover-acpe__header-mapping'),
                'TITLE' => ProfileModel::getFieldL18n($mapping::getUFName())
            ],
            [
                "DIV"   => "opt_site_duplicates_" . $mapping::getUFName(),
                "TAB"   => Loc::getMessage('rover-acpe__header-duplicates')
                    . ($enabled ? ' &#10004;' : ' &#10006;'),
                'TITLE' => Loc::getMessage('rover-acpe__header-duplicates-' . $entityType)
            ],
        ];

        ob_start();

        $subTabControl = new CAdminViewTabControl("subTabControl_mapping_fields_" . $entityType, $initTabs);
        $subTabControl->Begin();

        // mapping

        $subTabControl->BeginNextTab();
        echo '<table class="adm-detail-content-table edit-table bx-edit-table"><tbody>';
        echo '<tr><td colspan="2">' . MappingSnippet::getTable($mapping, null, true) . '</td></tr>';
        echo '</tbody></table>';

        // duplicates
        $subTabControl->BeginNextTab();

        if ($duplicateNote) {
            echo '<div class="adm-info-message">' . $duplicateNote . '</div>';
        } else {
            echo '<table class="adm-detail-content-table edit-table bx-edit-table"><tbody>';

            if (!AgentsOnCron::isAgentsOnCron()) {
                echo TableSnippet::getMessageRow(Loc::getMessage('rover-acpe__duplicate-control-cron'));
            }

            echo $this->getDuplicateControlRow($entityType);
            echo $this->getDuplicateActionRow($entityType);

            echo TableSnippet::getCheckBoxRow([
                'name'    => ProfileModel::UF_DUPLICATE_DATA . '[' . $entityType . '][' . ProfileModel::DUPLICATE_UPDATE_RESPONSIBLE . ']',
                'checked' => $profile->isDuplicateUpdateResponsible($entityType),
            ],
                Loc::getMessage('rover-acpe__duplicate-' . ProfileModel::DUPLICATE_UPDATE_RESPONSIBLE . '_label'),
                Loc::getMessage('rover-acpe__duplicate-' . ProfileModel::DUPLICATE_UPDATE_RESPONSIBLE . '_help')
            );

            if ($entityType == EntityTypesInterface::LEADS):

                echo TableSnippet::getCheckBoxRow([
                    'name'    => ProfileModel::UF_DUPLICATE_DATA . '[' . EntityTypesInterface::LEADS . '][' . ProfileModel::DUPLICATE_UPDATE_NAME . ']',
                    'checked' => $profile->isLeadDuplicateUpdateName(),
                ],
                    Loc::getMessage('rover-acpe__duplicate-' . ProfileModel::DUPLICATE_UPDATE_NAME . '_label'),
                    Loc::getMessage('rover-acpe__duplicate-' . ProfileModel::DUPLICATE_UPDATE_NAME . '_help')
                );

                echo TableSnippet::getCheckBoxRow([
                    'name'    => ProfileModel::UF_DUPLICATE_DATA . '[' . EntityTypesInterface::LEADS . '][' . ProfileModel::DUPLICATE_UPDATE_STATUS . ']',
                    'checked' => $profile->isLeadDuplicateUpdateStatus(),
                ],
                    Loc::getMessage('rover-acpe__duplicate-' . ProfileModel::DUPLICATE_UPDATE_STATUS . '_label'),
                    Loc::getMessage('rover-acpe__duplicate-' . ProfileModel::DUPLICATE_UPDATE_STATUS . '_help')
                );

            endif;

            echo TableSnippet::getSectionRow(Loc::getMessage('rover-acpe__duplicate-filter'));

            if ($entityType == EntityTypesInterface::LEADS):

                $allStatuses =
                    ['' => Loc::getMessage('rover-acpe__header-fields-adv-all')] + Params::getLeadPipelinesStatuses($connection);

                echo TableSnippet::getSelectRow([
                    'options'  => $allStatuses,
                    'selected' => $profile->getDuplicateData()[EntityTypesInterface::LEADS][ProfileModel::DUPLICATE_STATUS] ?? null,
                    'params'   => [
                        'name'     => ProfileModel::UF_DUPLICATE_DATA . '[' . EntityTypesInterface::LEADS . '][' . ProfileModel::DUPLICATE_STATUS . '][]',
                        'id'       => ProfileModel::UF_DUPLICATE_DATA . '[' . EntityTypesInterface::LEADS . '][' . ProfileModel::DUPLICATE_STATUS . ']',
                        'multiple' => true,
                        'size'     => min(6, count($allStatuses))
                    ],
                ],
                    Loc::getMessage('rover-acpe__duplicate-' . ProfileModel::DUPLICATE_STATUS . '_label'),
                    Loc::getMessage('rover-acpe__duplicate-' . ProfileModel::DUPLICATE_STATUS . '_help')
                );
                // @TODO: LEAD_DUPLICATE_PIPELINE
            endif;

            echo $this->getDuplicateFieldsRow($entityType, $availableDuplicateFields);
            echo $this->getDuplicateLogicRow($entityType);

            echo '</tbody></table>';
        }

        $subTabControl->End();

        return ob_get_clean();
    }

    /**
     * @param $entityType
     * @return array
     * @throws AmoCRMApiException
     * @throws AmoCRMMissedTokenException
     * @throws AmoCRMoAuthApiException
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     * @throws Main\ArgumentException
     * @throws Main\LoaderException
     * @throws Main\NotImplementedException
     * @throws Main\ObjectPropertyException
     * @throws SystemException
     * @throws AmoCRMApiPageNotAvailableException
     * @throws \AmoCRM\Exceptions\InvalidArgumentException
     * @author Pavel Shulaev (https://rover-it.me)
     */
    protected function getContactCompanyTab($entityType): array
    {
        if ($entityType == EntityTypesInterface::CONTACTS) {
            $mappingClass = Contacts::class;
        } elseif ($entityType == EntityTypesInterface::COMPANIES) {
            $mappingClass = Companies::class;
        } else {
            throw new ArgumentOutOfRangeException('restType');
        }

        return [
            'id'     => 'header-' . $entityType,
            'name'   => Loc::getMessage('rover-acpe__header-' . $entityType)
                . $this->getStatusLabel($this->getProfile()->isAmoEntityEnabled($entityType)),
            'title'  => Loc::getMessage('rover-acpe__header-' . $entityType),
            'fields' => [
                $this->getCheckbox(ProfileModel::UF_AMO_ENTITIES . '[' . $entityType . ']', [],
                    Loc::getMessage('rover-acpe__field-create-' . $entityType),
                    Loc::getMessage('rover-acpe__field-create-' . $entityType . '_help'),
                    $this->getProfile()->isAmoEntityEnabled($entityType)
                ),
                [
                    'id'      => 'mapping-fields-subtabs_' . $mappingClass::getUFName(),
                    'type'    => 'custom',
                    'colspan' => true,
                    'value'   => $this->getEntitySubTabs($this->getProfile()->getMapping($mappingClass),
                        OptionsBuilder::getInstance($this->getProfile())->buildByEntityType($entityType))
                ],
            ]
        ];
    }

    /**
     * @return array
     * @throws AmoCRMApiException
     * @throws AmoCRMoAuthApiException
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     * @throws Main\ArgumentException
     * @throws Main\LoaderException
     * @throws Main\ObjectPropertyException
     * @throws SystemException
     * @author Pavel Shulaev (https://rover-it.me)
     */
    protected function getTaskTab(): array
    {
        $profile      = $this->getProfile();
        $connection   = $profile->getConnection();
        $elementTypes = [
            EntityTypesInterface::CONTACTS  => Loc::getMessage('rover-acpe__entity-' . EntityTypesInterface::CONTACTS . '_label'),
            EntityTypesInterface::COMPANIES => Loc::getMessage('rover-acpe__entity-' . EntityTypesInterface::COMPANIES . '_label'),
            EntityTypesInterface::LEADS     => Loc::getMessage('rover-acpe__entity-' . EntityTypesInterface::LEADS . '_label')
        ];

        $enabled = $this->getProfile()->isTasksEnabled() && !$profile->isUnsorted();

        return [
            'id'     => 'tasks_header',
            'name'   => Loc::getMessage('rover-acpe__header-' . EntityTypesInterface::TASKS)
                . $this->getStatusLabel($enabled),
            'title'  => Loc::getMessage('rover-acpe__header-' . EntityTypesInterface::TASKS)
                . ($profile->isUnsorted() ? Loc::getMessage('rover-acpe__header-' . EntityTypesInterface::TASKS . '-disabled') : ''),
            'fields' => [
                $this->getCheckbox(ProfileModel::UF_AMO_ENTITIES . '[' . EntityTypesInterface::TASKS . ']', [],
                    Loc::getMessage('rover-acpe__field-create-' . EntityTypesInterface::TASKS),
                    Loc::getMessage('rover-acpe__field-create-' . EntityTypesInterface::TASKS . '_help'),
                    $this->getProfile()->isTasksEnabled()
                ),
                $this->getSelect(ProfileModel::UF_TASK_ELEMENT_TYPE,
                    ProfileModel::getFieldL18n(ProfileModel::UF_TASK_ELEMENT_TYPE),
                    $elementTypes, false,
                    Loc::getMessage('rover-acpe__field-' . ProfileModel::UF_TASK_ELEMENT_TYPE . '_help')),
                [
                    'id'       => ProfileModel::UF_TASK_TYPE,
                    'name'     => ProfileModel::getFieldL18n(ProfileModel::UF_TASK_TYPE),
                    'required' => false,
                    'type'     => 'select',
                    'items'    => Params::getTaskTypes($connection),
                ],
                [
                    'id'       => ProfileModel::UF_TASK_DEADLINE,
                    'name'     => ProfileModel::getFieldL18n(ProfileModel::UF_TASK_DEADLINE),
                    'required' => false,
                    'type'     => 'select',
                    'items'    => [
                        ProfileModel::TASK_DEADLINE__NOW      => Loc::getMessage('rover-acpe__field-' . ProfileModel::UF_TASK_DEADLINE . '_now'),
                        ProfileModel::TASK_DEADLINE__ONE_HOUR => Loc::getMessage('rover-acpe__field-' . ProfileModel::UF_TASK_DEADLINE . '_one-hour'),
                        ProfileModel::TASK_DEADLINE__DAY_END  => Loc::getMessage('rover-acpe__field-' . ProfileModel::UF_TASK_DEADLINE . '_day-end'),
                    ],
                ],
                [
                    'id'   => Tasks::getUFName() . '-section',
                    'name' => ProfileModel::getFieldL18n(Tasks::getUFName()),
                    'type' => 'section'
                ],
                [
                    'id'       => Tasks::getUFName(),
                    'name'     => ProfileModel::getFieldL18n(Tasks::getUFName()),
                    'required' => false,
                    'type'     => 'custom',
                    'colspan'  => true,
                    'value'    => MappingSnippet::getTable($this->getProfile()->getMapping(Tasks::class), null, true)
                ],
            ]
        ];
    }

    /**
     * @return void
     * @throws AmoCRMApiException
     * @throws AmoCRMMissedTokenException
     * @throws AmoCRMoAuthApiException
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     * @throws Main\ArgumentException
     * @throws Main\LoaderException
     * @throws Main\NotImplementedException
     * @throws Main\ObjectPropertyException
     * @throws SystemException
     * @throws \AmoCRM\Exceptions\InvalidArgumentException
     * @throws ReflectionException
     * @author Pavel Shulaev (https://rover-it.me)
     */
    protected function checkRequest()
    {
        if ($this->request->get('ok_message')) {
            Message::addOk($this->request->get('ok_message'));
        }

        if ($this->request->get('error_message')) {
            Message::addError($this->request->get('error_message'));
        }

        if ($this->arParams['READ_ONLY'] == 'Y') {
            return;
        }

        if ($this->request->get('connection_id') && empty($this->arParams['ID'])) {
            $this->requestAddProfile();
        }

        if (empty($this->arParams['ID'])) {
            throw new Main\ArgumentNullException('ID');
        }

        if (!$this->getProfile()->getConnection()->isAvailable()) {
            throw new Main\SystemException(Loc::getMessage('rover-acpe__connection-unavailable'));
        }

        if ($this->request->isPost()
            && ($this->request->getPost('apply') || $this->request->getPost('save'))
            && check_bitrix_sessid()) {
            $this->requestUpdateProfile();
        }
    }

    /**
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     * @throws Main\LoaderException
     * @throws Main\NotImplementedException
     * @throws SystemException
     * @throws ReflectionException
     * @author Pavel Shulaev (https://rover-it.me)
     */
    protected function requestUpdateProfile(): void
    {
        $skipChecking = [];
        if ($this->arParams['ID']) {
            $skipChecking[] = ProfileModel::UF_CONNECTION_ID;
        }

        if (!self::checkRequestFields(ProfileModel::class, $skipChecking)) {
            return;
        }

        $data = $this->getPostData();

        unset($data[ProfileModel::UF_CONNECTION_ID]);    // do not update connection
        unset($data[ProfileModel::UF_SOURCE_TYPE]);      // do not update source type
        unset($data[ProfileModel::UF_SOURCE_ID]);        // do not update source id

        ProfileModel::checkMandatoryData($data,
            [ProfileModel::UF_CONNECTION_ID, ProfileModel::UF_SOURCE_TYPE, ProfileModel::UF_SOURCE_ID]);

        unset($data[ProfileModel::UF_CONNECTION_ID]); // do not update connection
        $result = ProfileModel::update($this->arParams['ID'], $data);

        if ($result->isSuccess()) {
            if ($this->request->getPost('apply')) {
                LocalRedirect(PathService::getProfileElement($result->getId()));
            } else {
                LocalRedirect(PathService::getProfileList());
            }
        } else {
            Message::addError(implode('<br>', $result->getErrorMessages()));
        }
    }

    /**
     * @return array
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     * @throws Main\LoaderException
     * @throws Main\NotImplementedException
     * @throws ReflectionException
     * @throws SystemException
     * @author Pavel Shulaev (https://rover-it.me)
     */
    protected function getPostData(): array
    {
        $postList = $this->request->getPostList();
        $params   = ProfileModel::getAdditionalUfParams();
        $result   = [];

        foreach ($params as $fieldParam) {

            if (($fieldParam['FIELD_NAME'] == ProfileModel::UF_DUPLICATE_DATA)
                && empty($postList->get($fieldParam['FIELD_NAME']))) {
                continue;
            }

            if (in_array($fieldParam['FIELD_NAME'], [ProfileModel::UF_AMO_ENTITIES])) {
                $value = [];
                foreach ($postList->get($fieldParam['FIELD_NAME']) as $type => $checkBoxValue) {
                    if ($checkBoxValue == 'Y') {
                        $value[] = $type;
                    }
                }
            } else {
                $value = $postList->get($fieldParam['FIELD_NAME']);

                if (is_array($value) && $fieldParam['MULTIPLE'] != 'Y') {
                    $value = serialize($value);
                }
                $mappingClassName = Mapping::getClassNameByUfName($fieldParam['FIELD_NAME']);

                if ($mappingClassName) {
                    $value = $mappingClassName::encode($value);
                } else {
                    if ($fieldParam['USER_TYPE_ID'] == 'boolean') {
                        if (!in_array($value, ['Y', 'N'])) {
                            continue;
                        }

                        $value = $value == 'Y';
                    } elseif (is_null($value) && isset($fieldParam['SETTINGS']['DEFAULT_VALUE'])) {
                        $value = $fieldParam['SETTINGS']['DEFAULT_VALUE'];
                    }
                }
            }

            $result[$fieldParam['FIELD_NAME']] = $value;
        }

        return $result;
    }

    /**
     * @return array
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     * @throws Main\LoaderException
     * @throws Main\NotImplementedException
     * @throws SystemException
     * @throws ReflectionException
     * @author Pavel Shulaev (https://rover-it.me)
     */
    protected function getData(): array
    {
        $data = $this->arParams['ID']
            ? $this->getProfile()->getCollection()->getCollection()
            : $this->getPostData();

        return self::prepareUfData($data, ProfileModel::getAdditionalUfParams());
    }

    /**
     * @throws AmoCRMApiException
     * @throws AmoCRMoAuthApiException
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     * @throws Main\ArgumentException
     * @throws Main\LoaderException
     * @throws Main\ObjectPropertyException
     * @throws ReflectionException
     * @throws SystemException
     * @author Pavel Shulaev (https://rover-it.me)
     */
    public function getResult(): void
    {
        $this->arResult['ACTION_PANEL'] = $this->getActionPanel();
        $this->arResult['TABS']         = $this->getTabs();
        $this->arResult['DATA']         = $this->getData();

        $profileServiceResult = (new Service($this->getProfile()))->check();
        if (!$profileServiceResult->isSuccess()) {
            Message::addError($profileServiceResult->getErrorMessages());
        }
    }

    /**
     * @return Profile
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     * @throws Main\LoaderException
     * @throws SystemException
     * @author Pavel Shulaev (https://rover-it.me)
     */
    protected function getProfile(): ?Profile
    {
        return ProfileRepository::getById($this->arParams['ID']);
    }

    /**
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     * @throws Main\ArgumentException
     * @throws SystemException
     * @throws Main\LoaderException
     * @author Pavel Shulaev (https://rover-it.me)
     */
    protected function setTitle(): void
    {
        global $APPLICATION;
        $APPLICATION->SetTitle(Loc::getMessage('rover-acpe__title', ['#name#' => $this->getProfile()->getName()]));
    }

    /**
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     * @throws Main\ArgumentException
     * @throws Main\ObjectPropertyException
     * @throws SystemException
     * @author Pavel Shulaev (https://rover-it.me)
     */
    public function executeComponent(): void
    {
        try {
            $this->setFrameMode(false);
            $this->checkParams();
            $this->checkRequest();
            $this->getResult();
            $this->checkRights();
            $this->setTitle();

            $this->includeComponentTemplate();
        } catch (Exception $e) {
            ExceptionService::handleException($e, true);
            echo Loc::getMessage('rover-acpe__to-list');
        }
    }
}