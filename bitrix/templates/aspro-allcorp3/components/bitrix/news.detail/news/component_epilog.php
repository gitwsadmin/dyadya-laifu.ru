<?
use \Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);
global $arTheme, $APPLICATION;

CJSCore::Init('aspro_fancybox');

$arExtensions = [];

// top banner
\TSolution\Banner\Transparency::setHeaderClasses($templateData);

// can order?
$bOrderViewBasket = $templateData["ORDER"];

// use tabs?
if($arParams['USE_DETAIL_TABS'] === 'Y'){
	$bUseDetailTabs = true;
}
else{
	$bUseDetailTabs = false;
}

if ($arTheme['SHOW_PROJECTS_MAP_DETAIL']['VALUE'] == 'N') {
	unset($templateData['MAP']);
}

// blocks order
if(
	!$bUseDetailTabs &&
	array_key_exists('DETAIL_BLOCKS_ALL_ORDER', $arParams) &&
	$arParams["DETAIL_BLOCKS_ALL_ORDER"]
){
	$arBlockOrder = explode(",", $arParams["DETAIL_BLOCKS_ALL_ORDER"]);
}
else{
	$arBlockOrder = explode(",", $arParams["DETAIL_BLOCKS_ORDER"]);
	$arTabOrder = explode(",", $arParams["DETAIL_BLOCKS_TAB_ORDER"]);
}
?>
<div class="services-detail__bottom-info">
	<?foreach($arBlockOrder as $blockCode):?>
		<?include 'epilog_blocks/'.$blockCode.'.php';?>
	<?endforeach;?>
	<?include 'epilog_blocks/tags.php';?>
</div>

<?
if ($arExtensions) {
	\Aspro\Allcorp3\Functions\Extensions::init($arExtensions);
}
?>