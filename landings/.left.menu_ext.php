<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}
$aMenuLinksExt = [];

if ($arMenuParametrs = CAllcorp3::GetDirMenuParametrs(__DIR__)) {
    $iblock_id = CAllcorp3Cache::$arIBlocks[SITE_ID]['aspro_allcorp3_catalog']['aspro_allcorp3_landing'][0];
    $arExtParams = [
        'IBLOCK_ID'      => $iblock_id,
        'MENU_PARAMS'    => $arMenuParametrs,
        'SECTION_FILTER' => [],    // custom filter for sections (through array_merge)
        'SECTION_SELECT' => [],    // custom select for sections (through array_merge)
        'ELEMENT_FILTER' => [],    // custom filter for elements (through array_merge)
        'ELEMENT_SELECT' => [],    // custom select for elements (through array_merge)
        'MENU_TYPE'      => 'catalog',
    ];
    CAllcorp3::getMenuChildsExt($arExtParams, $aMenuLinksExt);
}

$aMenuLinks = array_merge($aMenuLinks, $aMenuLinksExt);
