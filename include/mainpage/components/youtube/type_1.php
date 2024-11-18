<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
{
	define("STATISTIC_SKIP_ACTIVITY_CHECK", "true");
	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
}
?>

<?
$APPLICATION->IncludeComponent(
	"aspro:wrapper.block.allcorp3", 
	"front_youtube", 
	array(
		"COMPONENT_TEMPLATE" => "front_youtube",
		"API_TOKEN_YOUTUBE" => "AIzaSyCE3_CwekuoxMBrFUKsOdT6eC4gbFIvxPg",
		"CHANNEL_ID_YOUTUBE" => "UCI4VRMQZTTvCkoaFbcpn9mA",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "86400",
		"CACHE_GROUPS" => "N",
		"TITLE" => "FROM_THEME",
		"SHOW_TITLE" => "FROM_THEME",
		"ITEMS_OFFSET" => "FROM_THEME",
		"TITLE_POSITION" => "FROM_THEME",
		"SORT_YOUTUBE" => "FROM_THEME",
		"PLAYLIST_ID_YOUTUBE" => "FROM_THEME",
		"COUNT_VIDEO_YOUTUBE" => "FROM_THEME",
		"COUNT_VIDEO_ON_LINE_YOUTUBE" => "FROM_THEME",
		"COMPOSITE_FRAME_MODE" => "A",
		"COMPOSITE_FRAME_TYPE" => "AUTO",
		"CACHE_FILTER" => "N",
		"SUBTITLE" => "FROM_THEME",
		"RIGHT_TITLE" => "FROM_THEME",
		"WIDE" => "FROM_THEME"
	),
	false
);?>