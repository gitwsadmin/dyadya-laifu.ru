<?
global $arTheme;
$wideMenuParams = array(
	'CONTENT_TYPE' => $arTheme['WIDE_MENU_CONTENT']['VALUE'],
	'CHILDS_TYPE' => $arTheme['WIDE_MENU_CONTENT']['DEPENDENT_PARAMS']['CHILDS_VIEW']['VALUE'],
	'IMG_POSITION' => $arTheme['IMAGES_WIDE_MENU_POSITION']['VALUE'],
	'IMAGES' => $arTheme['IMAGES_WIDE_MENU']['VALUE'],
);
$bTariffsUseDetail = $GLOBALS['arTheme']['TARIFFS_USE_DETAIL']['VALUE'] === 'Y';

// items cache hack
$_GET['__SHOW_TARIFFS_DETAIL_LINK__'] = $bTariffsUseDetail;
?>
<?$APPLICATION->IncludeComponent(
	"bitrix:menu",
	"left_catalog",
	Array(
		"ALLOW_MULTI_SELECT" => "N",
		"CHILD_MENU_TYPE" => "left",
		"COMPONENT_TEMPLATE" => "top",
		"COUNT_ITEM" => "6",
		"DELAY" => "N",
		"MAX_LEVEL" => CAllcorp3::GetFrontParametrValue('MAX_DEPTH_MENU'),
		"MENU_CACHE_GET_VARS" => array(
			"__SHOW_TARIFFS_DETAIL_LINK__",
		),
		"MENU_CACHE_TIME" => "3600000",
		"MENU_CACHE_TYPE" => "A",
		"MENU_CACHE_USE_GROUPS" => "N",
		"ROOT_MENU_TYPE" => "top",
		"USE_EXT" => "Y",
		"WIDE_MENU_PARAMS" => $wideMenuParams,
	)
);?>
<?
unset($_GET['__SHOW_TARIFFS_DETAIL_LINK__']);
?>