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

use Bitrix\Main\Loader;
use Bitrix\Iblock\SectionTable;

Loader::includeModule("iblock");

$iblockId = $iblock_id; // ID инфоблока каталога
$menuLinks = $aMenuLinks; // Твой массив меню

// Получаем все разделы инфоблока с их URL
$sections = SectionTable::getList([
    "filter" => ["IBLOCK_ID" => $iblockId, "ACTIVE" => "Y"],
    "select" => ["ID", "NAME", "CODE"],
    "order" => ["SORT" => "ASC"]
]);

$sectionLinks = [];
while ($section = $sections->fetch()) {
    $sectionLinks[$section["NAME"]] = $section["CODE"];
}

// Обновляем ссылки в массиве меню
foreach ($menuLinks as $key => $menuItem) {
    $sectionName = $menuItem[0]; // Имя раздела

    if (isset($sectionLinks[$sectionName])) {
        $menuLinks[$key][1] = '/landings/category/'.$sectionLinks[$sectionName]; // Подставляем корректную ссылку
    }
}
$aMenuLinks = $menuLinks;
// Теперь $menuLinks содержит правильные ссылки


//pr($aMenuLinks);
