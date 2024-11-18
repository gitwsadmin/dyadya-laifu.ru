<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
{
	define("STATISTIC_SKIP_ACTIVITY_CHECK", "true");
	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
}?>
<?$APPLICATION->IncludeComponent(
	"aspro:wrapper.block.allcorp3", 
	"front_vk", 
	array(
		"COMPONENT_TEMPLATE" => "front_vk",
		"API_TOKEN_VK" => "FROM_THEME",
		"GROUP_ID_VK" => "FROM_THEME",
		"VK_TITLE_BLOCK" => "FROM_THEME",
		"VK_TITLE_ALL_BLOCK" => "FROM_THEME",
		"VK_TEXT_LENGTH" => "FROM_THEME",
		"SHOW_TITLE" => "FROM_THEME",
		"TITLE_POSITION" => "FROM_THEME",
		"LINE_ELEMENT_COUNT" => "FROM_THEME",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "100",
		"ITEMS_OFFSET" => "FROM_THEME",
		"CACHE_GROUPS" => "N",
		"COMPOSITE_FRAME_MODE" => "A",
		"COMPOSITE_FRAME_TYPE" => "AUTO",
		"WIDE" => "FROM_THEME",
		"CACHE_FILTER" => "N"
	),
	false
);?>