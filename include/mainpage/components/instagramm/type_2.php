<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
{
	define("STATISTIC_SKIP_ACTIVITY_CHECK", "true");
	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
}?>
<?$APPLICATION->IncludeComponent(
	"aspro:wrapper.block.allcorp3", 
	"front_instagramm", 
	array(
		"COMPONENT_TEMPLATE" => "front_instagramm",
		"WIDE" => "FROM_THEME",
		"ITEMS_OFFSET" => "FROM_THEME",
		"LINES_COUNT" => "FROM_THEME",
		"ELEMENTS_ROW" => "FROM_THEME",
		"SHOW_TITLE" => "FROM_THEME",
		"TITLE_POSITION" => "FROM_THEME",
		"SHOW_PREVIEW_TEXT" => "Y",
		"TYPE_BLOCK" => "WITH_BIG_BLOCK",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "86400",
		"CACHE_GROUPS" => "N",
		"COMPOSITE_FRAME_MODE" => "A",
		"COMPOSITE_FRAME_TYPE" => "AUTO",
		"SUBTITLE" => "Публикации"
	),
	false
);?>