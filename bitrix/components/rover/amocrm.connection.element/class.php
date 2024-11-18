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

use Bitrix\Main;
use Bitrix\Main\Localization\Loc;
use Rover\AmoCRM\Component\ElementBase;
use Rover\AmoCRM\Directory\Entity\Connection;
use Rover\AmoCRM\Directory\Model\ConnectionModel;
use Rover\AmoCRM\Directory\Model\ProfileModel;
use Rover\AmoCRM\Directory\Repository\ConnectionRepository;
use Rover\AmoCRM\Options;
use Rover\AmoCRM\Service\ExceptionService;
use Rover\AmoCRM\Service\Message;
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
class RoverAmoCrmConnectionElement extends ElementBase
{
    const FORM_ID = 'amocrm_connection_element';

    protected Connection $connection;

    /**
     * @return string
     * @author Pavel Shulaev (https://rover-it.me)
     */
    protected function getRepositoryClass(): string
    {
        return ConnectionRepository::class;
    }

    /**
     * @return array[]
     * @throws Main\ArgumentNullException
     * @throws Main\LoaderException
     * @throws Main\SystemException
     * @author Pavel Shulaev (https://rover-it.me)
     */
    protected function getActionPanel(): array
    {
        $result = [
            [
                'TEXT'  => Loc::getMessage('rover-acce__action-back', ['#cnt#' => ConnectionModel::getCount()]),
                'TITLE' => Loc::getMessage('rover-acce__action-back_title'),
                'LINK'  => PathService::getConnectionList(),
                'ICON'  => 'btn-list'
            ],
            [
                'TEXT'  => Loc::getMessage('rover-acae__integration-profiles', ['#cnt#' => ProfileModel::getCount()]),
                'TITLE' => Loc::getMessage('rover-acae__integration-profiles_title'),
                'LINK'  => PathService::getProfileList(),
                'ICON'  => 'btn-list'
            ]
        ];

        if ($this->arParams['READ_ONLY'] != 'Y') {
            $result[] = ['SEPARATOR' => true];
            $result[] = [
                'TEXT'  => Loc::getMessage('rover-acce__action-settings'),
                'TITLE' => Loc::getMessage('rover-acce__action-settings_title'),
                'LINK'  => PathService::getSettings(),
                'ICON'  => 'btn-settings'
            ];
        }

        return $result;
    }

    /**
     * @return array
     * @throws Main\ArgumentNullException
     * @throws Main\ArgumentOutOfRangeException
     * @throws Main\LoaderException
     * @throws Main\SystemException
     * @author Pavel Shulaev (https://rover-it.me)
     */
    protected function getData(): array
    {
        if ($this->arParams['ID']) {
            return $this->getConnection()->getCollection()->getCollection();
        }

        return $this->getPostData();
    }

    /**
     * @return array
     * @author Pavel Shulaev (https://rover-it.me)
     */
    protected function getPostData(): array
    {
        return [
            ConnectionModel::UF_NAME          => $_POST[ConnectionModel::UF_NAME] ?: Loc::getMessage('rover-acce__name-default'),
            ConnectionModel::UF_ACTIVE        => $_POST[ConnectionModel::UF_ACTIVE] ?: 'Y',
            ConnectionModel::UF_SORT          => $_POST[ConnectionModel::UF_SORT] ?: 100,
            ConnectionModel::UF_CLIENT_SECRET => $_POST[ConnectionModel::UF_CLIENT_SECRET] ?: '',
            ConnectionModel::UF_CLIENT_UUID   => $_POST[ConnectionModel::UF_CLIENT_UUID] ?: '',
        ];
    }

    /**
     * @return Connection|null
     * @throws Main\ArgumentNullException
     * @throws Main\ArgumentOutOfRangeException
     * @throws Main\LoaderException
     * @throws Main\SystemException
     * @author Pavel Shulaev (https://rover-it.me)
     */
    protected function getConnection(): ?Connection
    {
        if (empty($this->arParams['ID'])) {
            throw new Main\ArgumentNullException('ID');
        }

        if (!isset($this->connection)) {
            if (!$this->connection = ConnectionRepository::getById($this->arParams['ID'])) {
                throw new Main\ArgumentOutOfRangeException('ID');
            }

            $this->connection->getCollection()->offsetSet(ConnectionModel::UF_ACTIVE,
                $this->connection->getCollection()->offsetGet(ConnectionModel::UF_ACTIVE) ? 'Y' : 'N'
            );
        }

        return $this->connection;
    }

    /**
     * @throws Main\ArgumentNullException
     * @throws Main\ArgumentOutOfRangeException
     * @throws Main\LoaderException
     * @throws Main\SystemException
     * @author Pavel Shulaev (https://rover-it.me)
     */
    protected function getResult()
    {
        $this->arResult['DATA']         = $this->getData();
        $this->arResult['TABS']         = $this->getTabs();
        $this->arResult['ACTION_PANEL'] = $this->getActionPanel();
    }

    /**
     * @return array[]
     * @throws Main\ArgumentException
     * @throws Main\ArgumentNullException
     * @throws Main\ObjectPropertyException
     * @throws Main\SystemException
     * @author Pavel Shulaev (https://rover-it.me)
     */
    protected function getTabs(): array
    {
        $redirectUri = Options::getRedirectUri();
        $redirectUri = $redirectUri
            ? '<b id="redirect-uri">' . $redirectUri . '</b>&nbsp <span onclick="copyTextFromBlock(\'redirect-uri\', this);"><a href="#" onclick="return false">' . Loc::getMessage('rover-acce__copy-link') . '</a></span><br>' . $this->getHelp(Loc::getMessage('rover-acce__copy-link_help'))
            : '-';

        return [
            [
                'id'     => 'main',
                'name'   => Loc::getMessage('rover-acce__tab-name'),
                'title'  => Loc::getMessage('rover-acce__tab-title'),
                'icon'   => '',
                'fields' => [
                    [
                        'id'       => 'link',
                        'name'     => Loc::getMessage('rover-acce__header-link'),
                        'required' => false,
                        'type'     => 'custom',
                        'value'    => $redirectUri
                    ],
                    [
                        'id'       => ConnectionModel::UF_NAME,
                        'name'     => ConnectionModel::getFieldL18n(ConnectionModel::UF_NAME),
                        'required' => true,
                    ],
                    [
                        'id'       => ConnectionModel::UF_ACTIVE,
                        'name'     => ConnectionModel::getFieldL18n(ConnectionModel::UF_ACTIVE),
                        'required' => false,
                        'type'     => 'checkbox'
                    ],
                    [
                        'id'       => ConnectionModel::UF_CLIENT_SECRET,
                        'name'     => ConnectionModel::getFieldL18n(ConnectionModel::UF_CLIENT_SECRET),
                        'required' => true,
                    ],
                    [
                        'id'       => ConnectionModel::UF_CLIENT_UUID,
                        'name'     => ConnectionModel::getFieldL18n(ConnectionModel::UF_CLIENT_UUID),
                        'required' => true,
                    ],
                    [
                        'id'       => ConnectionModel::UF_SORT,
                        'name'     => ConnectionModel::getFieldL18n(ConnectionModel::UF_SORT),
                        'required' => false,
                        'type'     => 'integer'
                    ],
                ]
            ]
        ];
    }

    /**
     * @author Pavel Shulaev (https://rover-it.me)
     */
    protected function setTitle()
    {
        global $APPLICATION;
        if (isset($this->arResult['DATA']['ID'])) {
            $title = Loc::getMessage('rover-acce__edit-title',
                ['#name#' => $this->arResult['DATA'][ConnectionModel::UF_NAME]]);
        } else {
            $title = Loc::getMessage('rover-acce__add-title');
        }

        $APPLICATION->SetTitle($title);
    }

    /**
     * @return void
     * @throws Main\ArgumentNullException
     * @throws Main\LoaderException
     * @throws Main\SystemException
     * @author Pavel Shulaev (https://rover-it.me)
     */
    protected function checkRequest(): void
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

        //Form submitted
        if ($this->request->isPost() && check_bitrix_sessid()) {
            $data = [
                ConnectionModel::UF_NAME          => $_POST[ConnectionModel::UF_NAME],
                ConnectionModel::UF_CLIENT_SECRET => $_POST[ConnectionModel::UF_CLIENT_SECRET],
                ConnectionModel::UF_CLIENT_UUID   => $_POST[ConnectionModel::UF_CLIENT_UUID],
                ConnectionModel::UF_ACTIVE        => $_POST[ConnectionModel::UF_ACTIVE] != 'N',
                ConnectionModel::UF_SORT          => $_POST[ConnectionModel::UF_SORT] ?: 500,
            ];

            /** @var Main\Result $result */
            $result = $this->arParams['ID']
                ? ConnectionModel::update($this->arParams['ID'], $data)
                : ConnectionModel::add($data);

            if ($result->isSuccess()) {
                if (isset($_POST['apply'])) {
                    LocalRedirect(PathService::getConnectionElement($result->getId()));
                } else {
                    LocalRedirect(PathService::getConnectionList());
                }

            } else {
                Message::addError(implode('<br>', $result->getErrorMessages()));
            }
        }
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
            $this->checkRequest();
            $this->getResult();
            $this->includeComponentTemplate();
            $this->setTitle();

        } catch (Exception $e) {
            ExceptionService::handleException($e, true);
        }
    }

}