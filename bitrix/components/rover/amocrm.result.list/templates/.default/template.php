<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
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

use Bitrix\Main\Grid\Panel\Actions;
use Bitrix\Main\Grid\Panel\Snippet;
use Bitrix\Main\Grid\Panel\Types;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Page\Asset;
use Bitrix\Main\UI\Extension;
use Bitrix\Main\Web\Json;
use Rover\AmoCRM\Dependence\DependenceBuilder;
use Rover\AmoCRM\Directory\Model\ProfileModel;
use Rover\AmoCRM\Options;
use Rover\AmoCRM\Service\Message;
use Rover\AmoCRM\Service\PathService;

Loc::loadMessages(__FILE__);

$this->setFrameMode(false);

if (!DependenceBuilder::buildBase()->check()->isSuccess()) {
    return;
}

Extension::load(["ui.progressbar", "ui.alerts"]);
Asset::getInstance()->addJs('/bitrix/js/rover_amocrm/events.js');

Message::showAdmin();

?>
<div id="progress-bar-container"></div>
<div class="adm-info-message-wrap">
    <div class="adm-info-message">
        <?= Loc::getMessage('rover-acr__note',
            ['#path#' => PathService::getProfileElement($arParams['PROFILE_ID'])]); ?>
    </div>
</div>
<?php

$APPLICATION->IncludeComponent(
    "bitrix:main.interface.toolbar",
    "",
    [
        "BUTTONS" => $arResult['ACTION_PANEL']
    ],
    false,
    ['HIDE_ICONS' => 'Y']
);

$actionPanel = [];
if ($arParams['READ_ONLY'] != 'Y') {

    $snippets = new Snippet();

    $actionPanel = [
        'GROUPS' => [
            'TYPE' => [
                'ITEMS' => [
                    [
                        'TYPE'     => Types::BUTTON,
                        'ID'       => "rover-ac__export-items",
                        'CLASS'    => "apply",
                        'TEXT'     => Loc::getMessage("rover-acr__action_mass-export"),
                        'ONCHANGE' => [
                            [
                                'ACTION' => Actions::CALLBACK,
                                'DATA'   => [
                                    [
                                        'JS' => "roverAmoCRMExportItems('" . $arResult['GRID_ID'] . "', " . $arParams['PROFILE_ID'] . ", " . (Options::isAgentHandleNew() ? 'true' : 'false') . ");",
                                    ]
                                ]
                            ]
                        ]
                    ],
                    $snippets->getForAllCheckbox()
                ],
            ]
        ],
    ];
}

$APPLICATION->IncludeComponent(
    "bitrix:main.ui.grid",
    ".default",
    [
        "GRID_ID"                   => $arResult['GRID_ID'],
        'COLUMNS'                   => $arResult["COLUMNS"],
        'SHOW_ROW_CHECKBOXES'       => $arParams['READ_ONLY'] != 'Y',
        "NAV_OBJECT"                => $arResult["NAV"],
        'TOTAL_ROWS_COUNT'          => $arResult["NAV"]->getRecordCount(),
        'TOTAL_ROWS_COUNT_HTML'     => '<span class="main-grid-panel-content-title">' . Loc::getMessage('rover-ape__total') . '</span> <span class="main-grid-panel-content-text">' . $arResult["NAV"]->getRecordCount() . '</span>',
        "AJAX_MODE"                 => "Y",
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
        'ALLOW_SORT'                => false, // @TODO: true
        'ALLOW_PIN_HEADER'          => true,
        'ACTION_PANEL'              => $actionPanel,
        'SORT'                      => $arResult['SORT']['sort'],
        'SORT_VARS'                 => $arResult['SORT']['vars'],
        "ROWS"                      => $arResult["ROWS"],
    ],
    $component, ["HIDE_ICONS" => "Y"]
);

?>
<div id="rover-acr__params"
     data-entities-ids='<?= Json::encode($arResult['ALL_IDS']) ?>'
     data-event-type='<?= $arResult['EVENT_TYPE'] ?>'
     data-source-type='<?=$arResult['PROFILE'][ProfileModel::UF_SOURCE_TYPE]?>'
     data-source-id='<?=$arResult['PROFILE'][ProfileModel::UF_SOURCE_ID]?>'
></div>

<script>
    BX.message({
        rover_acr__js_initialization: '<?=GetMessageJS("rover-acr__js-initialization")?>',
        rover_acr__js_export_title: '<?=GetMessageJS("rover-acr__js-export-title")?>',
        rover_acr__js_added_to_queue: '<?=GetMessageJS("rover-acr__js-added-to-queue")?>',
        rover_acr__js_export_complete: '<?=GetMessageJS("rover-acr__js-export-complete")?>',
    });
</script>