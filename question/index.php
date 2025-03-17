<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");?>
<?$APPLICATION->SetTitle("Подать заявку");?>
<div class="page_question_form">
<?$APPLICATION->IncludeComponent(
	"bitrix:form", 
	"form-list", 
	array(
		"AJAX_MODE" => "Y",
		"AJAX_OPTION_ADDITIONAL" => "",
		"AJAX_OPTION_HISTORY" => "N",
		"AJAX_OPTION_JUMP" => "N",
		"AJAX_OPTION_STYLE" => "Y",
		"CACHE_TIME" => "3600",
		"CACHE_TYPE" => "A",
		"CHAIN_ITEM_LINK" => "",
		"CHAIN_ITEM_TEXT" => "",
		"EDIT_ADDITIONAL" => "N",
		"EDIT_STATUS" => "Y",
		"IGNORE_CUSTOM_TEMPLATE" => "N",
		"NAME_TEMPLATE" => "",
		"NOT_SHOW_FILTER" => array(
			0 => "",
			1 => "",
		),
		"NOT_SHOW_TABLE" => array(
			0 => "",
			1 => "",
		),
		"RESULT_ID" => $_REQUEST["RESULT_ID"],
		"SEF_MODE" => "N",
		"SHOW_ADDITIONAL" => "N",
		"SHOW_ANSWER_VALUE" => "N",
		"SHOW_EDIT_PAGE" => "N",
		"SHOW_LIST_PAGE" => "N",
		"SHOW_STATUS" => "Y",
		"SHOW_VIEW_PAGE" => "N",
		"START_PAGE" => "new",
		"SUCCESS_URL" => "/question/",
		"USE_EXTENDED_ERRORS" => "N",
		"WEB_FORM_ID" => "7",
		"COMPONENT_TEMPLATE" => "form-list",
		"VARIABLE_ALIASES" => array(
			"action" => "action",
		)
	),
	false
);?>
</div>
<script>
    // Проверяем, есть ли параметры в URL
    if (window.location.search) {
        // Получаем текущий URL без параметров
        const urlWithoutParams = window.location.origin + window.location.pathname;

        // Обновляем историю браузера, чтобы убрать параметры
        window.history.replaceState(null, null, urlWithoutParams);
    }
</script>


<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>


