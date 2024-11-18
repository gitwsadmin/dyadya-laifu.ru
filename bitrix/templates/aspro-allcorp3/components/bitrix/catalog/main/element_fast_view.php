<?
global $arTheme, $APPLICATION;

//$APPLICATION->ShowHeadScripts();
$APPLICATION->ShowAjaxHead();

// cart
$bOrderViewBasket = (trim($arTheme['ORDER_VIEW']['VALUE']) === 'Y');

if($arSection){
	$arInherite = CAllcorp3::getSectionInheritedUF(array(
		'sectionId' => $arSection['ID'],
		'iblockId' => $arSection['IBLOCK_ID'],
		'select' => array(
			'UF_OFFERS_TYPE',
			'UF_PICTURE_RATIO',
		),
		'filter' => array(
			'GLOBAL_ACTIVE' => 'Y', 
		),
		'enums' => array(
			'UF_OFFERS_TYPE',
			'UF_PICTURE_RATIO',
		),
	));
}
$pictureRatioTmp = \Aspro\Functions\CAsproAllcorp3::getValueWithSection([
	'CODE' => 'CATALOG_PAGE_DETAIL_PICTURE_RATIO',
	'SECTION_VALUE' => $arInherite['UF_PICTURE_RATIO']
]);
$pictureRatio = $pictureRatioTmp ? $pictureRatioTmp : CAllcorp3::GetFrontParametrValue('ELEMENTS_IMG_TYPE');

$arParams['OID'] = 0;
if ($oidParam = CAllcorp3::GetFrontParametrValue('CATALOG_OID')) {
	$context=\Bitrix\Main\Context::getCurrent();
	$request=$context->getRequest();
	if ($oid = $request->getQuery($oidParam)) {
		$arParams['OID'] = $oid;
	}
}

$typeSKU = \Aspro\Functions\CAsproAllcorp3::getValueWithSection([
	'CODE' => 'CATALOG_PAGE_DETAIL_SKU',
	'SECTION_VALUE' => $arInherite['UF_OFFERS_TYPE']
]);
?>
<div class="product-container detail <?=$pictureRatio !== "normal" ? "ratio--".$pictureRatio : ""?>" itemscope itemtype="http://schema.org/Product">
	<?@include_once('page_blocks/'.$arTheme["USE_FAST_VIEW_PAGE_DETAIL"]["VALUE"].'.php');?>
</div>
<?
if($arRegion){
	$arTagSeoMarks = array();
	foreach($arRegion as $key => $value){
		if(strpos($key, 'PROPERTY_REGION_TAG') !== false && strpos($key, '_VALUE_ID') === false){
			$tag_name = str_replace(array('PROPERTY_', '_VALUE'), '', $key);
			$arTagSeoMarks['#'.$tag_name.'#'] = $key;
		}
	}

	if($arTagSeoMarks){
		CAllcorp3Regionality::addSeoMarks($arTagSeoMarks);
	}
}
?>