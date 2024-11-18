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

use Bitrix\Main\Grid\Panel\Snippet;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\UI\Extension;
use Rover\AmoCRM\Dependence\DependenceBuilder;
use Rover\AmoCRM\Model\Source;
use Rover\AmoCRM\Options;
use Rover\AmoCRM\Service\Message;
use Rover\AmoCRM\Service\PathService;

Loc::loadMessages(__FILE__);

$this->setFrameMode(false);

$extensions = ["popup"];
if (!Options::isJsDisabled()) {
    $extensions[] = "rover_amocrm.select2";
}

Extension::load($extensions);

try {
    Message::showAdmin();

    if (!DependenceBuilder::buildBase()->check()->isSuccess()) {
        return;
    }

    CUtil::InitJSCore(['popup']);

    $APPLICATION->IncludeComponent(
        "bitrix:main.interface.toolbar",
        "",
        [
            "BUTTONS" => $arResult['ACTION_PANEL']
        ],
        false,
        ['HIDE_ICONS' => 'Y']
    );

    $snippets = new Snippet();
    $actionPanel = [];
    if ($arParams['READ_ONLY'] != 'Y') {
        $actionPanel = [
            'GROUPS' => [
                'TYPE' => [
                    'ITEMS' => [
                        $snippets->getEditButton(),
                        $snippets->getRemoveButton()
                    ],
                ]
            ],
        ];
    }

    $APPLICATION->IncludeComponent(
        "bitrix:main.ui.grid",
        ".default",
        [
            "GRID_ID"                   => RoverAmoCrmProfileList::GRID_ID,
            'COLUMNS'                   => $arResult["COLUMNS"],
            'SHOW_ROW_CHECKBOXES'       => $arParams['READ_ONLY'] != 'Y',
            "NAV_OBJECT"                => $arResult["NAV"],
            'TOTAL_ROWS_COUNT'          => $arResult["NAV"]->getRecordCount(),
            'TOTAL_ROWS_COUNT_HTML'     => '<span class="main-grid-panel-content-title">' . Loc::getMessage('rover-acpl__total') . '</span> <span class="main-grid-panel-content-text">' . $arResult["NAV"]->getRecordCount() . '</span>',
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
            'ALLOW_SORT'                => true,
            'ALLOW_PIN_HEADER'          => true,
            'ACTION_PANEL'              => $actionPanel,
            'SORT'                      => $arResult['SORT']['sort'],
            'SORT_VARS'                 => $arResult['SORT']['vars'],
            "ROWS"                      => $arResult["ROWS"],
        ],
        $component, ["HIDE_ICONS" => "Y"]
    );

    $types = Source::getChildTypes();

    ?>
    <script type="text/javascript">
        BX.message({
            <?php foreach ($types as $type): ?>
            'rover_acpl__<?=$type?>_select': '<?=GetMessageJS("rover-acpl__" . $type . "_select")?>',
            'rover_acpl__<?=$type?>_empty': '<?=GetMessageJS("rover-acpl__" . $type . "_empty")?>',
            <?php endforeach; ?>
            'rover_acpl__button_close': '<?=GetMessageJS('rover-acpl__button_close')?>',
            'rover_acpl__button_add': '<?=GetMessageJS('rover-acpl__button_add')?>',
            'rover_acpl__profile_element_template': '<?=PathService::getProfileNewTemplate()?>',
            'rover_acpl__title': '<?=GetMessageJS('rover-acpl__title')?>',
            'rover_acpl__connection_empty': '<?=GetMessageJS('rover-acpl__connection_empty')?>',
            'rover_acpl__connection_select': '<?=GetMessageJS('rover-acpl__connection_select')?>',
        });

        var amoCrmPresetList;

        BX.ready(function () {

            var sourceTypes = <?=CUtil::PhpToJSObject($arResult['SOURCES_LIST'])?>;
            var connections = <?=CUtil::PhpToJSObject($arResult['CONNECTIONS_LIST'])?>;

            amoCrmPresetList = new AmoCrmPresetList(sourceTypes, connections);
        });
    </script><?php
} catch (\Exception $e) {
    ShowError($e->getMessage());
}