<?
global $arTheme;
$bTariffsUseDetail = $GLOBALS['arTheme']['TARIFFS_USE_DETAIL']['VALUE'] === 'Y';

// items cache hack
$_GET['__SHOW_TARIFFS_DETAIL_LINK__'] = $bTariffsUseDetail;
?>
<?$APPLICATION->IncludeComponent("bitrix:menu", "bottom", array(
    "ROOT_MENU_TYPE" => "bottom6",
    "MENU_CACHE_TYPE" => "A",
    "MENU_CACHE_TIME" => "3600000",
    "MENU_CACHE_USE_GROUPS" => "N",
    "MENU_CACHE_GET_VARS" => array(
        "__SHOW_TARIFFS_DETAIL_LINK__",
    ),
    "MAX_LEVEL" => "1",
    "CHILD_MENU_TYPE" => "left",
    "USE_EXT" => "N",
    "DELAY" => "N",
    "ALLOW_MULTI_SELECT" => "Y",
    'BOLD_ITEMS' => true,
    'ROW_ITEMS' => true,
    ),
    false
);?>
<?
unset($_GET['__SHOW_TARIFFS_DETAIL_LINK__']);
?>