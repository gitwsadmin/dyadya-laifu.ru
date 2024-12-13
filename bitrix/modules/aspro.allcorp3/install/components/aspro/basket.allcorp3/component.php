<?
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

if (!CModule::IncludeModule('iblock')) {
	ShowError(GetMessage('IBLOCK_MODULE_NOT_INSTALLED'));
	return;
}
if (!CModule::IncludeModule('aspro.allcorp3')) {
	ShowError(GetMessage('ASPRO_ALLCORP3_MODULE_NOT_INSTALLED'));
	return;
}

$arModuleOptions = CAllcorp3::GetFrontParametrsValues(SITE_ID);
$bUseBasket = ($arModuleOptions['ORDER_VIEW'] === 'Y');

$arParams['PATH_TO_BASKET'] = $pageBasket = trim($arParams['PATH_TO_BASKET'] ?? $arModuleOptions['BASKET_PAGE_URL'] ?? '');
$arParams['PATH_TO_ORDER'] = $pageOrder = trim($arParams['PATH_TO_ORDER'] ?? $arModuleOptions['ORDER_PAGE_URL'] ?? '');
$arParams['PATH_TO_CATALOG'] = $pageCatalog = trim($arParams['PATH_TO_CATALOG'] ?? $arModuleOptions['CATALOG_PAGE_URL'] ?? '');
$arParams['SHOW_BASKET_PRINT'] = $arParams['SHOW_BASKET_PRINT'] ?? $arModuleOptions['SHOW_BASKET_PRINT'];

$isBasketPage = CAllcorp3::IsBasketPage($pageBasket);
$isOrderPage = CAllcorp3::IsOrderPage($pageOrder);

if(!$bUseBasket){
	if($arParams['SHOW_404'] !== 'N'){
		CAllcorp3::goto404Page();

		return;
	}
}

global $USER;
$userID = $USER->GetID();
$userID = ($userID > 0 ? $userID : 0);

$arResult = array(
	'ITEMS' => array(),
	'ITEMS_COUNT' => 0,
	'ITEMS_SUMM' => 0,
	'ITEMS_SUMM_WD' => 0,
	'USE_BASKET' => $bUseBasket ? 'Y' : 'N',
	'IS_BASKET_PAGE' => $isBasketPage ? 'Y' : 'N',
	'IS_ORDER_PAGE' => $isOrderPage ? 'Y' : 'N',
	'USER_ID' => $userID,
	'PAY_SYSTEM' => $arModuleOptions['PAY_SYSTEM'],
);

if($bUseBasket){
	if(
		$arParams['HIDE_ON_CART_PAGE'] !== 'Y' ||
		(
			!$isBasketPage &&
			!$isOrderPage
		)
	){
		if(
			!isset($_SESSION[SITE_ID][$userID]['BASKET_ITEMS']) ||
			!is_array($_SESSION[SITE_ID][$userID]['BASKET_ITEMS'])
		) {
			$_SESSION[SITE_ID][$userID]['BASKET_ITEMS'] = array();
		}
		
		$arSessionItems = $_SESSION[SITE_ID][$userID]['BASKET_ITEMS'];
		$summ = $summ_wd = 0;
		
		foreach($arSessionItems as $arItem){
			if(
				!($arItem['ID']) ||
				!strlen($arItem['NAME'])
			){
				continue;
			}
		
			$arItem['PICTURE'] = (isset($arItem['PREVIEW_PICTURE']) ? $arItem['PREVIEW_PICTURE'] : (isset($arItem['DETAIL_PICTURE']) ? $arItem['DETAIL_PICTURE'] : '')) ?: '';
			if($arItem['PICTURE']){
				$arItem['PICTURE'] = CFile::GetFileArray($arItem['PICTURE']);
				$arItem['PICTURE']['IMAGE_70'] = CFile::ResizeImageGet($arItem['PICTURE']['ID'], array('width' => 70, 'height' => 70), BX_RESIZE_IMAGE_PROPORTIONAL_ALT, true );
				$arItem['PICTURE']['IMAGE_110'] = CFile::ResizeImageGet($arItem['PICTURE']['ID'], array('width' => 110, 'height' => 110), BX_RESIZE_IMAGE_PROPORTIONAL_ALT, true );
			}
		
			$arItem['PROPERTY_STATUS'] = CIBlockPropertyEnum::GetByID($arItem['PROPERTY_STATUS_VALUE']);
		
			if (strlen(trim($arItem['PROPERTY_PRICE_VALUE']))) {
				if ($arItem['PROPERTY_PRICE_CURRENCY_VALUE']) {
					$arItem['PROPERTY_PRICE_VALUE'] = str_replace('#CURRENCY#', $arItem['PROPERTY_PRICE_CURRENCY_VALUE'], $arItem['PROPERTY_PRICE_VALUE']);
				}
		
				$arItem['PROPERTIES']['PRICE']['VALUE'] = $arItem['PROPERTY_PRICE_VALUE'];
		
				$arItem['SUMM'] = CAllcorp3::FormatSumm($arItem['PROPERTY_FILTER_PRICE_VALUE'], $arItem['QUANTITY']);
				$summ += floatval(str_replace(' ', '', $arItem['PROPERTY_FILTER_PRICE_VALUE'])) * $arItem['QUANTITY'];
			}
		
			if (strlen(trim($arItem['PROPERTY_PRICEOLD_VALUE']))) {
				if ($arItem['PROPERTY_PRICE_CURRENCY_VALUE']) {
					$arItem['PROPERTY_PRICEOLD_VALUE'] = str_replace('#CURRENCY#', $arItem['PROPERTY_PRICE_CURRENCY_VALUE'], $arItem['PROPERTY_PRICEOLD_VALUE']);
				}

				$arItem['PROPERTIES']['PRICEOLD']['VALUE'] = $arItem['PROPERTY_PRICEOLD_VALUE'];

				$arItem['SUMM_WD'] = CAllcorp3::FormatSumm($arItem['PROPERTY_PRICEOLD_VALUE'], $arItem['QUANTITY']);
			}

			$summ_wd += strlen(trim($arItem['PROPERTY_PRICEOLD_VALUE'])) ? (floatval(str_replace(' ', '', $arItem['PROPERTY_FILTER_PRICE_VALUE'])) * $arItem['QUANTITY']) : (strlen(trim($arItem['PROPERTY_PRICEOLD_VALUE'])) ? (floatval(str_replace(' ', '', $arItem['PROPERTY_PRICEOLD_VALUE'])) * $arItem['QUANTITY']) : 0);
		
			$arResult['ITEMS'][$arItem['ID']] = $arItem;
		}
		
		$arResult['ITEMS_SUMM'] = CAllcorp3::FormatSumm($summ, 1);
		$arResult['ITEMS_SUMM_RAW'] = $summ;
		$arResult['ITEMS_SUMM_WD'] = CAllcorp3::FormatSumm($summ_wd, 1);
		$arResult['ITEMS_SUMM_WD_RAW'] = $summ_wd;
		$arResult['ITEMS_COUNT'] = count($arResult['ITEMS']);
	}
}

$this->IncludeComponentTemplate();