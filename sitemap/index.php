<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Карта сайта");
?>
<div class="site_map_wrapper">
<?
$APPLICATION->IncludeComponent(
	"bitrix:main.map", 
	"main", 
	array(
		"LEVEL" => "3",
		"COL_NUM" => "2",
		"SHOW_DESCRIPTION" => "N",
		"SET_TITLE" => "Y",
		"CACHE_TIME" => "36000000",
		"COMPONENT_TEMPLATE" => "main",
		"CACHE_TYPE" => "A"
	),
	false
);
?>
</div>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>