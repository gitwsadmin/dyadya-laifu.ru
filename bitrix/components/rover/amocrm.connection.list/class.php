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

use AmoCRM\Exceptions\BadTypeException;
use Bitrix\Main;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\UI\AdminPageNavigation;
use Rover\AmoCRM\Component\ListBase;
use Rover\AmoCRM\Directory\Entity;
use Rover\AmoCRM\Directory\Entity\Connection;
use Rover\AmoCRM\Directory\Model\ConnectionModel;
use Rover\AmoCRM\Factory\Directory\ConnectionFactory;
use Rover\AmoCRM\Directory\Repository;
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
class RoverAmoCrmConnectionList extends ListBase
{
    const GRID_ID = 'amocrm_account_list';

    /**
     * @return string
     * @author Pavel Shulaev (https://rover-it.me)
     */
    protected function getRepositoryClass(): string
    {
        return Repository\ConnectionRepository::class;
    }

    /**
     * @author Pavel Shulaev (https://rover-it.me)
     */
    protected function getTitle(): string
    {
        return Loc::getMessage('rover-accl__title');
    }

    /**
     * @return array
     * @author Pavel Shulaev (https://rover-it.me)
     */
    protected function getEditableFields(): array
    {
        return [
            ConnectionModel::UF_NAME, ConnectionModel::UF_ACTIVE, ConnectionModel::UF_SORT
        ];
    }

    /**
     * @return array
     * @throws BadTypeException
     * @throws Main\ArgumentNullException
     * @throws Main\ArgumentOutOfRangeException
     * @throws Main\SystemException
     * @author Pavel Shulaev (https://rover-it.me)
     */
    protected function getRows(): array
    {
        $nav      = $this->getNavObject();
        $iterator = $this->getRowsIterator();
        $result   = [];

        while ($connectionData = $iterator->fetch()) {
            /** @var Connection $connection */
            $connection = ConnectionFactory::buildByData($connectionData);

            $row = [
                'id'      => $connectionData['ID'],
                'data'    => [
                    'ID'                            => $connectionData['ID'],
                    ConnectionModel::UF_ACTIVE      => $connectionData[ConnectionModel::UF_ACTIVE] ? 'Y' : 'N',
                    ConnectionModel::UF_NAME        => $connectionData[ConnectionModel::UF_NAME],
                    ConnectionModel::UF_BASE_DOMAIN => $connectionData[ConnectionModel::UF_BASE_DOMAIN] ?: '-',
                    ConnectionModel::UF_SORT        => $connectionData[ConnectionModel::UF_SORT],
                    'STATUS'                        => $connection->getStatus(),
                    'BUTTON'                        => '',
                ],
                'columns' => [
                    'STATUS'                   => $connection->getStatusPict(),
                    'BUTTON'                   => $this->arParams['READ_ONLY'] == 'Y'
                        ? '<span style="font-size: .85em; color: gray">' . Loc::getMessage('rover-accl__access-denied') . '</span>'
                        : $connection->getOAuthButton(),
                    ConnectionModel::UF_ACTIVE => $connectionData[ConnectionModel::UF_ACTIVE]
                        ? Loc::getMessage('rover-ac__yes')
                        : Loc::getMessage('rover-ac__no')
                ],
                'actions' => $this->getAvailableActions($connection, $nav,
                    Loc::getMessage('rover-accl__action-delete_confirm')),
            ];

            $result[] = $row;
        }

        return $result;
    }

    /**
     * @param Entity $entity
     * @param AdminPageNavigation $nav
     * @param string|null $confirm
     * @return array[]
     * @author Pavel Shulaev (https://rover-it.me)
     */
    protected function getAvailableActions(Entity $entity, AdminPageNavigation $nav, string $confirm = null): array
    {
        if ($this->arParams['READ_ONLY'] == 'Y') {
            return [
                [
                    'title'   => Loc::getMessage('rover-accl__action-read'),
                    'text'    => Loc::getMessage('rover-accl__action-read'),
                    'default' => true,
                    'onclick' => 'document.location.href="' . PathService::getConnectionElement($entity->getId()) . '"',
                ]
            ];
        }

        return [
            [
                'title'   => Loc::getMessage('rover-accl__action-update'),
                'text'    => Loc::getMessage('rover-accl__action-update'),
                'default' => true,
                'onclick' => 'document.location.href="' . PathService::getConnectionElement($entity->getId()) . '"',
            ], [
                'title'   => Loc::getMessage('rover-accl__action-remove'),
                'text'    => Loc::getMessage('rover-accl__action-remove'),
                'onclick' => 'if (window.confirm("' . ($confirm ?? Loc::getMessage('rover-aslb__action-remove-confirm')) . '")) {document.location.href="' . $this->arParams['LIST_URL'] . '?ID=' . $entity->getId() . '&lang=' . LANGUAGE_ID . '&' . $this->getActionButton() . '=delete&' . bitrix_sessid_get() . "&{$nav->getId()}=page-{$nav->getCurrentPage()}-size{$nav->getPageSize()}\"}",
            ]
        ];
    }

    /**
     * @return array[]
     * @throws Main\ArgumentNullException
     * @author Pavel Shulaev (https://rover-it.me)
     */
    protected function getColumns(): array
    {
        $fields = [
            [
                'id'      => 'ID',
                'name'    => 'ID',
                'default' => false,
                'sort'    => 'ID'
            ]
        ];

        foreach (
            [
                ConnectionModel::UF_NAME,
                ConnectionModel::UF_ACTIVE,
                ConnectionModel::UF_BASE_DOMAIN,
                ConnectionModel::UF_SORT
            ] as $field
        ) {
            $fields[] = [
                'id'       => $field,
                'name'     => ConnectionModel::getFieldL18n($field),
                'default'  => true,
                'sort'     => $field,
                'editable' => in_array($field, $this->getEditableFields()),
                "type"     => in_array($field, [ConnectionModel::UF_ACTIVE]) ? "checkbox" : 'text'
            ];
        }

        return array_merge($fields, [
            [
                'id'      => 'STATUS',
                'name'    => Loc::getMessage('rover-accl__header-STATUS'),
                'default' => true,
            ],
            [
                'id'      => 'BUTTON',
                'name'    => Loc::getMessage('rover-accl__header-BUTTON'),
                'default' => true,
            ]
        ]);
    }

    /**
     * @throws BadTypeException
     * @throws Main\ArgumentNullException
     * @throws Main\ArgumentOutOfRangeException
     * @throws Main\LoaderException
     * @throws Main\SystemException
     * @author Pavel Shulaev (https://rover-it.me)
     */
    protected function getResult()
    {
        $this->arResult['ROWS']       = $this->getRows();
        $this->arResult['COLUMNS']    = $this->getColumns();
        $this->arResult['NAV']        = $this->getNavObject();
        $this->arResult['SORT']       = $this->getGridSort();
        $this->arResult['PAGE_SIZES'] = $this->getPageSizes($this->arResult['NAV']);
    }
}