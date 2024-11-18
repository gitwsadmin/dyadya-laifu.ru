<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();?>
<?$APPLICATION->IncludeComponent(
	"bitrix:map.yandex.view",
	"map",
	array(
		"API_KEY" => \Bitrix\Main\Config\Option::get("fileman","yandex_map_api_key",""),
		"COMPONENT_TEMPLATE" => "map",
		"COMPOSITE_FRAME_MODE" => "A",
		"COMPOSITE_FRAME_TYPE" => "AUTO",
		"CONTROLS" => array(
			0 => "ZOOM",
			1 => "SMALLZOOM",
			2 => "TYPECONTROL",
		),
		"INIT_MAP_TYPE" => "MAP",
		"KEY" => "AKk9BlwBAAAAcf5CSgMAHyfAq9knnHW9nsNrwnKOBpJ8-FUAAAAAAAAAAABE8lP1ifeROCbNOEGuF0oRi1P0xQ==",
		"MAP_DATA" => "a:4:{s:10:\"yandex_lat\";d:55.75461952078772;s:10:\"yandex_lon\";d:37.62022412333199;s:12:\"yandex_scale\";i:15;s:10:\"PLACEMARKS\";a:1:{i:0;a:3:{s:3:\"LON\";d:37.620438700053;s:3:\"LAT\";d:55.753445723095;s:4:\"TEXT\";s:8:\"Компания\";}}}",
		"MAP_HEIGHT" => "550px",
		"MAP_ID" => "",
		"MAP_WIDTH" => "100%",
		"OPTIONS" => array(
			0 => "ENABLE_DBLCLICK_ZOOM",
			1 => "ENABLE_DRAGGING",
		),
		"USE_REGION_DATA" => "Y"
	),
	false
);?>