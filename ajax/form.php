<?
define("STATISTIC_SKIP_ACTIVITY_CHECK", "true");
define('STOP_STATISTICS', true);
define('PUBLIC_AJAX_MODE', true);
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

$form_id = isset($_REQUEST["form_id"]) ? $_REQUEST["form_id"] : false;

if(\Bitrix\Main\Loader::includeModule('aspro.allcorp3'))
	$arTheme = CAllcorp3::GetFrontParametrsValues(SITE_ID);
$id = (isset($_REQUEST["id"]) ? $_REQUEST["id"] : false);

$isCallBack = (CAllcorp3::GetFrontParametrValue('USE_BITRIX_FORM') == 'N') && ($id == CAllcorp3Cache::$arIBlocks[SITE_ID]["aspro_allcorp3_form"]["aspro_allcorp3_callback"][0]);
$successMessage = ($isCallBack ? "<p>Наш менеджер перезвонит вам в ближайшее время.</p><p>Спасибо за ваше обращение!</p>" : "Ваше сообщение отправлено!");
?>
<span class="jqmClose top-close stroke-theme-hover" onclick="window.b24form = false;" title="<?=\Bitrix\Main\Localization\Loc::getMessage('CLOSE_BLOCK');?>"><?=CAllcorp3::showIconSvg('', SITE_TEMPLATE_PATH.'/images/svg/Close.svg')?></span>
<?if($form_id == 'fast_view'):?>
	<?include('fast_view.php');?>
<?elseif($form_id == 'fast_view_sale'):?>
	<?include('fast_view_sale.php');?>
<?elseif($form_id == 'marketing'):?>
	<?include('marketing.php');?>
<?elseif($form_id == 'city_chooser'):?>
	<?include_once('city_chooser.php');?>
<?elseif(isset($_REQUEST['type']) && $_REQUEST['type'] == 'review'):?>
	<?include_once('review.php');?>
<?elseif(isset($_REQUEST['type']) && $_REQUEST['type'] == 'auth'):?>
	<?include_once('auth.php');?>
<?elseif(isset($_REQUEST['type']) && $_REQUEST['type'] == 'subscribe'):?>
	<?include('subscribe.php');?>
<?elseif($id):?>
	<?$APPLICATION->IncludeComponent(
		"aspro:form.allcorp3", "popup",
		Array(
			"IBLOCK_TYPE" => "aspro_allcorp3_form",
			"IBLOCK_ID" => $id,
			"AJAX_MODE" => "Y",
			"AJAX_OPTION_JUMP" => "N",
			"AJAX_OPTION_STYLE" => "N",
			"AJAX_OPTION_HISTORY" => "N",
			"CACHE_TYPE" => "A",
			"CACHE_TIME" => "100000",
			"AJAX_OPTION_ADDITIONAL" => "",
			"SUCCESS_MESSAGE" => $successMessage,
			"SEND_BUTTON_NAME" => "Отправить",
			"SEND_BUTTON_CLASS" => "btn btn-default",
			"DISPLAY_CLOSE_BUTTON" => "Y",
			"POPUP" => "Y",
			"CLOSE_BUTTON_NAME" => "Закрыть",
			"CLOSE_BUTTON_CLASS" => "jqmClose btn btn-default bottom-close"
		)
	);?>
<?else:?>
	<div style="padding: 40px 40px 15px 40px;">
		<div class="alert alert-danger">
			<?=\Bitrix\Main\Localization\Loc::getMessage('ERROR_ID_FORM');?>
		</div>
	</div>
<?endif;?>