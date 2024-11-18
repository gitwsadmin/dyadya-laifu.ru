<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */

/** @var CBitrixComponent $component */

use Bitrix\Main\Grid\Panel\Snippet;
use \Bitrix\Main\Localization\Loc;
use Rover\AmoCRM\Service\Message;

$APPLICATION->SetAdditionalCSS('/bitrix/css/main/grid/webform-button.css');
$APPLICATION->SetAdditionalCSS('/bitrix/js/ui/buttons/ui.buttons.css');
$APPLICATION->SetAdditionalCSS('/bitrix/js/ui/buttons/src/css/ui.buttons.css');

Loc::loadMessages(__FILE__);

$this->setFrameMode(false);

//CUtil::InitJSCore(array(/*'ajax' ,*/ 'popup'));

/*$APPLICATION->IncludeComponent(
    "bitrix:main.interface.toolbar",
    "",
    array(
        "BUTTONS"=> $arResult['ACTION_PANEL']
    ),
    false,
    array('HIDE_ICONS' => 'Y')
);*/

$snippets = new Snippet();

Message::showAdmin();

?>
    <div class="adm-toolbar-panel-container">
        <div class="adm-toolbar-panel-flexible-space"><? /*=Loc::getMessage('rover-accl__redirect-url', ['#url#' => Options::getRedirectUri()])*/ ?></div>
        <div class="adm-toolbar-panel-align-right">
            <a class="ui-btn ui-btn-light"
               href="/bitrix/admin/settings.php?lang=<?= LANGUAGE_ID ?>&mid=rover.amocrm&mid_menu=1">
                <?= Loc::getMessage('rover-accl__settings') ?>
            </a>
            <a class="ui-btn ui-btn-success" href="/bitrix/admin/rover-acrm__profile-list.php?lang=<?= LANGUAGE_ID ?>">
                <?= Loc::getMessage('rover-accl__integration-profiles') ?>
            </a>
            <?php if ($arParams['READ_ONLY'] != 'Y'): ?>
                <a class="ui-btn ui-btn-primary"
                   href="/bitrix/admin/rover-acrm__connection-element.php?lang=<?= LANGUAGE_ID ?>">
                    <?= Loc::getMessage('rover-accl__add') ?>
                </a>
            <?php endif; ?>
        </div>
    </div>
<?php

$actionPanel = [];
if ($arParams['READ_ONLY'] != 'Y') {
    $actionPanel = [
        'GROUPS' => [
            'TYPE' => [
                'ITEMS' => [
                    $snippets->getEditButton(),
                    $snippets->getRemoveButton(),
                ],
            ]
        ],
    ];
}

$APPLICATION->IncludeComponent(
    "bitrix:main.ui.grid",
    ".default",
    [
        "GRID_ID"                   => RoverAmoCrmConnectionList::GRID_ID,
        'COLUMNS'                   => $arResult["COLUMNS"],
        'SHOW_ROW_CHECKBOXES'       => $arParams['READ_ONLY'] != 'Y',
        "NAV_OBJECT"                => $arResult["NAV"],
        'TOTAL_ROWS_COUNT'          => $arResult["NAV"]->getRecordCount(),
        'TOTAL_ROWS_COUNT_HTML'     => '<span class="main-grid-panel-content-title">' . Loc::getMessage('rover-accl__total') . '</span> <span class="main-grid-panel-content-text">' . $arResult["NAV"]->getRecordCount() . '</span>',
        "AJAX_MODE"                 => "N",
        "AJAX_OPTION_JUMP"          => "N",
        "AJAX_OPTION_STYLE"         => "Y",
        "AJAX_OPTION_HISTORY"       => "N",
        'PAGE_SIZES'                => $arResult['PAGE_SIZES'],
        'SHOW_CHECK_ALL_CHECKBOXES' => $arParams['READ_ONLY'] != 'Y',
        'SHOW_ROW_ACTIONS_MENU'     => true,
        'SHOW_GRID_SETTINGS_MENU'   => true,
        'SHOW_NAVIGATION_PANEL'     => true,
        "ENABLE_COLLAPSIBLE_ROWS"   => true,
        'SHOW_ACTION_PANEL'         => true,
        'SHOW_PAGINATION'           => true,
        'SHOW_SELECTED_COUNTER'     => true,
        'SHOW_TOTAL_COUNTER'        => true,
        'SHOW_PAGESIZE'             => true,
        'ALLOW_COLUMNS_SORT'        => true,
        'ALLOW_COLUMNS_RESIZE'      => true,
        'ALLOW_HORIZONTAL_SCROLL'   => true,
        'ALLOW_SORT'                => true,
        'ALLOW_PIN_HEADER'          => true,
        'ACTION_PANEL'              => $actionPanel,
        'SORT'                      => $arResult['SORT']['sort'],
        'SORT_VARS'                 => $arResult['SORT']['vars'],
        "ROWS"                      => $arResult["ROWS"],
    ],
    $component, ["HIDE_ICONS" => "Y"]
);