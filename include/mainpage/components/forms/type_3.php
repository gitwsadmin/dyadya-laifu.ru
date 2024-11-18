<?
$indexPageOptions = $GLOBALS['arTheme']['INDEX_TYPE']['SUB_PARAMS'][ $GLOBALS['arTheme']['INDEX_TYPE']['VALUE'] ];
$blockOptions = $indexPageOptions['FORMS'];
$blockTemplateOptions = $blockOptions['TEMPLATE']['LIST'][ $blockOptions['TEMPLATE']['VALUE'] ];
?>

<?$APPLICATION->IncludeComponent(
	"aspro:form.allcorp3", 
	"form-list", 
	array(
		"IBLOCK_TYPE" => "aspro_allcorp3_form",
		"IBLOCK_ID" => "aspro_allcorp3_callback",
		"AJAX_MODE" => "Y",
		"AJAX_OPTION_JUMP" => "N",
		"AJAX_OPTION_STYLE" => "N",
		"AJAX_OPTION_HISTORY" => "N",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "100000",
		"AJAX_OPTION_ADDITIONAL" => "",
		"SEND_BUTTON_NAME" => "Отправить сообщение",
		"SEND_BUTTON_CLASS" => "btn btn-default btn-lg",
		"DISPLAY_CLOSE_BUTTON" => "Y",
		"CLOSE_BUTTON_NAME" => "Отправить еще",
		"COMPONENT_TEMPLATE" => "form-list",
		"WEBFORM_ID" => "aspro_allcorp3_callback",
		"CACHE_GROUPS" => "Y",
		"POSITION_IMAGE" => "RIGHT",
		"NO_IMAGE" => $blockTemplateOptions["ADDITIONAL_OPTIONS"]["NO_IMAGE"]["VALUE"],
		"COMPOSITE_FRAME_MODE" => "A",
		"COMPOSITE_FRAME_TYPE" => "AUTO",
		"SUBTITLE" => "Контакты",
		"TITLE" => "Оставьте заявку",
		"TYPE_BLOCK" => "COMPACT",
		"SHOW_PREVIEW_TEXT" => "Y"
	),
	false
);?>
