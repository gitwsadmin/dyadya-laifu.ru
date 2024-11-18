<?
global $arTheme;
$bTariffsUseDetail = $GLOBALS['arTheme']['TARIFFS_USE_DETAIL']['VALUE'] === 'Y';

// items cache hack
$_GET['__SHOW_TARIFFS_DETAIL_LINK__'] = $bTariffsUseDetail;
?>
<?$APPLICATION->IncludeComponent(
	"bitrix:menu",
	"mobile",
	Array(
		"COMPONENT_TEMPLATE" => "mobile",
		"MENU_CACHE_TIME" => "3600000",
		"MENU_CACHE_TYPE" => "A",
		"MENU_CACHE_USE_GROUPS" => "N",
		"MENU_CACHE_GET_VARS" => array(
			"__SHOW_TARIFFS_DETAIL_LINK__",
		),
		"DELAY" => "N",
		"MAX_LEVEL" => "4",
		"ALLOW_MULTI_SELECT" => "Y",
		"ROOT_MENU_TYPE" => "top",
		"CHILD_MENU_TYPE" => "left",
		"USE_EXT" => "Y"
	)
);?>
<?
unset($_GET['__SHOW_TARIFFS_DETAIL_LINK__']);
?>