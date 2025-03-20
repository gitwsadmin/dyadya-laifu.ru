<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Loader;
use Bitrix\Iblock\SectionTable;

Loader::includeModule("iblock");

$menuLinks = [];
$iblockId = 43; // ID инфоблока каталога

// Получаем разделы первого уровня
$sections = SectionTable::getList([
    "filter" => [
        "IBLOCK_ID" => $iblockId,
        "ACTIVE" => "Y",
        "DEPTH_LEVEL" => 1 // Только первый уровень
    ],
    "select" => ["ID", "NAME", "CODE"],
    "order"  => ["SORT" => "ASC"]
]);

while ($section = $sections->fetch()) {
    $menuLinks[] = [
        $section["NAME"],           // Название раздела
        $section["CODE"], // URL раздела
        [],
        [],
        ""
    ];
}

$aMenuLinks = $menuLinks;
return $menuLinks;
?>
