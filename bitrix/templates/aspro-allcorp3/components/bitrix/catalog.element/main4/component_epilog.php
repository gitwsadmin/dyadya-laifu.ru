<?
use \Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);
global $arTheme, $APPLICATION;

CJSCore::Init('aspro_fancybox');

$arExtensions = [];

// top banner
\TSolution\Banner\Transparency::setHeaderClasses($templateData);

define('ASPRO_PAGE_WO_TITLE', true); // remove h1 in page_title

// can order?
$bOrderViewBasket = $templateData["ORDER"];

// use tabs?
if($arParams['USE_DETAIL_TABS'] === 'Y'){
	$bUseDetailTabs = true;
}
elseif($arParams['USE_DETAIL_TABS'] === 'N'){
	$bUseDetailTabs = false;
}
else{
	$bUseDetailTabs = $arTheme['USE_DETAIL_TABS']['VALUE'] != 'N';
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
<div class="catalog-detail__bottom-info">
	<?include_once 'epilog_blocks/tizers.php';?>
	<?foreach($arBlockOrder as $blockCode):?>
		<?include 'epilog_blocks/'.$blockCode.'.php';?>
	<?endforeach;?>
</div>

<?
if ($arParams['DISPLAY_COMPARE'] || $arParams['ORDER_VIEW']) {
	$arExtensions[] = 'item_action';
}

if ($arExtensions) {
	\Aspro\Allcorp3\Functions\Extensions::init($arExtensions);
}
?>