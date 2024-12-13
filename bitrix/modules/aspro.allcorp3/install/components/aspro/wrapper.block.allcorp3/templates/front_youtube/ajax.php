<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
$codeBlock = 'YOUTUBE';
$indexType = CAllcorp3::GetFrontParametrValue('INDEX_TYPE');
$blockType = CAllcorp3::GetFrontParametrValue($indexType.'_'.$codeBlock.'_TEMPLATE');


if($arParams['WIDE'] === 'FROM_THEME'){
	$arParams['WIDE'] = CAllcorp3::GetFrontParametrValue($indexType.'_'.$codeBlock.'_WIDE_'.$blockType);
}

if($arParams['ITEMS_OFFSET'] === 'FROM_THEME'){
	$arParams['ITEMS_OFFSET'] = CAllcorp3::GetFrontParametrValue($indexType.'_'.$codeBlock.'_ITEMS_OFFSET_'.$blockType);
}

if($arParams['COUNT_VIDEO_ON_LINE_YOUTUBE'] === 'FROM_THEME'){
	$arParams['COUNT_VIDEO_ON_LINE_YOUTUBE'] = CAllcorp3::GetFrontParametrValue($indexType.'_'.$codeBlock.'_ELEMENTS_COUNT_'.$blockType);
} elseif($arParams['COUNT_VIDEO_ON_LINE_YOUTUBE'] === 'FROM_SETTINGS_YOUTUBE') {
	$arParams['COUNT_VIDEO_ON_LINE_YOUTUBE'] = CAllcorp3::GetFrontParametrValue('COUNT_VIDEO_ON_LINE_'.$codeBlock);
}

if($arParams['SHOW_TITLE'] === 'FROM_THEME'){
	$arParams['SHOW_TITLE'] = CAllcorp3::GetFrontParametrValue('SHOW_TITLE_'.$codeBlock.'_'.$indexType);
}

if($arParams['TITLE_POSITION'] === 'FROM_THEME'){
	$arParams['TITLE_POSITION'] = CAllcorp3::GetFrontParametrValue('TITLE_POSITION_'.$codeBlock.'_'.$indexType);
}

$arParams['RIGHT_TITLE'] = CAllcorp3::GetFrontParametrValue('YOTUBE_TITLE_ALL_BLOCK');

foreach($arParams as $code => $vauel) {
	if ( $vauel === 'FROM_THEME' && strpos($code, "~") === false ) {
		$arParams[$code] = CAllcorp3::GetFrontParametrValue($code);
	}
}
$arParams['RIGHT_LINK_EXTERNAL'] = true;
?>

<?$APPLICATION->IncludeComponent(
	"aspro:youtube",
	"main",
	Array(
		"API_TOKEN_YOUTUBE" => $arParams['API_TOKEN_YOUTUBE'],
		"CHANNEL_ID_YOUTUBE" => $arParams['CHANNEL_ID_YOUTUBE'],
		"SORT_YOUTUBE" => $arParams['SORT_YOUTUBE'],
		"PLAYLIST_ID_YOUTUBE" => $arParams['PLAYLIST_ID_YOUTUBE'],
		"COUNT_VIDEO_YOUTUBE" => $arParams['COUNT_VIDEO_YOUTUBE'],
		"COUNT_VIDEO_ON_LINE_YOUTUBE" => $arParams['COUNT_VIDEO_ON_LINE_YOUTUBE'],
		"TITLE" => $arParams['TITLE'],
		"SHOW_TITLE" => $arParams['SHOW_TITLE']==="Y",
		"ITEMS_OFFSET" => $arParams['ITEMS_OFFSET'],
		"TITLE_POSITION" => $arParams['TITLE_POSITION'],
		"SUBTITLE" => $arParams['SUBTITLE'],
		"RIGHT_TITLE" =>  $arParams["RIGHT_TITLE"],
		"WIDE" =>  $arParams["WIDE"],
		"COMPOSITE_FRAME_MODE" => $arParams['COMPOSITE_FRAME_MODE'],
		"COMPOSITE_FRAME_TYPE" => $arParams['COMPOSITE_FRAME_TYPE'],
		"CACHE_TYPE" => $arParams['CACHE_TYPE'],
		"CACHE_TIME" => $arParams['CACHE_TIME'],
		"CACHE_GROUPS" => $arParams['CACHE_GROUPS'],
		"RIGHT_LINK_EXTERNAL" => $arParams['RIGHT_LINK_EXTERNAL'],
	)
);?>