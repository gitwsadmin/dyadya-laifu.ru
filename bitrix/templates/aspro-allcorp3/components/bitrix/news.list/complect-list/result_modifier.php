<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

$bShowImage = in_array('PREVIEW_PICTURE', $arParams['FIELD_CODE']);

foreach ($arResult['ITEMS'] as $key => &$arItem) {
	$arItem['DETAIL_PAGE_URL'] = $arParams['DETAIL_PAGE_URL']; // for link in cart

	$arImages = [];
	if ($bShowImage) {
		$arImages[] = is_array($arItem['FIELDS']['PREVIEW_PICTURE']) ? $arItem['FIELDS']['PREVIEW_PICTURE']['ID'] : $arItem['FIELDS']['PREVIEW_PICTURE'];
		if ($arItem['DISPLAY_PROPERTIES']['MORE_PHOTO']['VALUE']) {
			$arImages = array_merge($arImages, (array)$arItem['DISPLAY_PROPERTIES']['MORE_PHOTO']['VALUE']);
		}

		if ($arImages) {
			foreach ($arImages as $id) {
				if ($arImage = CFile::GetFileArray($id)) {
					$arImage['RESIZED_SRC'] = CFile::ResizeImageGet($id, ['width' => 62, 'height' => 62], BX_RESIZE_IMAGE_PROPORTIONAL_ALT)['src'];
					$arResult['ITEMS'][$key]['IMAGES'][] = $arImage;
				}
			}
			$arItem['PREVIEW_PICTURE_SRC'] = reset($arResult['ITEMS'][$key]['IMAGES'])['SRC'];
		}
	}
	if ($arItem['DISPLAY_PROPERTIES']['LINK_SERVICES']['VALUE']) {
		$arSelect = ["ID", "IBLOCK_ID", "NAME", "PREVIEW_PICTURE", "SORT", "DETAIL_PAGE_URL", "PROPERTY_FILTER_PRICE", "PROPERTY_FORM_ORDER"];
		if (in_array("PRICE", $arParams["PROPERTY_CODE"])) {
			$arSelect[] = "PROPERTY_PRICE";
		}
		if (in_array("PRICE_CURRENCY", $arParams["PROPERTY_CODE"])) {
			$arSelect[] = "PROPERTY_PRICE_CURRENCY";
		}
		if (in_array("PRICEOLD", $arParams["PROPERTY_CODE"])) {
			$arSelect[] = "PROPERTY_PRICEOLD";
		}
		$arItem['LINK_SERVICES'] = CAllcorp3Cache::CIBLockElement_GetList(
			array(
				'CACHE' => array(
					"TAG" => CAllcorp3Cache::GetIBlockCacheTag($arItem["PROPERTIES"]["LINK_SERVICES"]["LINK_IBLOCK_ID"])
				)
			),
			array(
				"IBLOCK_ID" => $arItem["PROPERTIES"]["LINK_SERVICES"]["LINK_IBLOCK_ID"],
				"ACTIVE" => "Y", 
				"ID" => $arItem["PROPERTIES"]["LINK_SERVICES"]["VALUE"],
				"!PROPERTY_FORM_ORDER" => false,
			),
			false,
			false,
			$arSelect
		);
		foreach ($arItem['LINK_SERVICES'] as $key2 => $arService) {
			if (isset($arService['PROPERTY_PRICE_VALUE'])) {
				$arItem['LINK_SERVICES'][$key2]['DISPLAY_PROPERTIES']['PRICE'] = [
					'VALUE' => $arService['PROPERTY_PRICE_VALUE']
				];
			}
			if (isset($arService['PROPERTY_PRICEOLD_VALUE'])) {
				$arItem['LINK_SERVICES'][$key2]['DISPLAY_PROPERTIES']['PRICEOLD'] = [
					'VALUE' => $arService['PROPERTY_PRICEOLD_VALUE']
				];
			}
			if (isset($arService['PROPERTY_ECONOMY_VALUE'])) {
				$arItem['LINK_SERVICES'][$key2]['DISPLAY_PROPERTIES']['ECONOMY'] = [
					'VALUE' => $arService['PROPERTY_ECONOMY_VALUE']
				];
			}
			if (isset($arService['PROPERTY_PRICE_CURRENCY_VALUE'])) {
				$arItem['LINK_SERVICES'][$key2]['DISPLAY_PROPERTIES']['PRICE_CURRENCY'] = [
					'VALUE' => $arService['PROPERTY_PRICE_CURRENCY_VALUE']
				];
			}
			$arItem['LINK_SERVICES'][$key2]['DISPLAY_PROPERTIES']['FILTER_PRICE'] = [
				'VALUE' => $arService['PROPERTY_FILTER_PRICE_VALUE']
			];

		}
	}
	if ($arItem['DISPLAY_PROPERTIES']['LINK_COMPLECT']['VALUE']) {
		$rsData = \Bitrix\Highloadblock\HighloadBlockTable::getList(
			array(
				'filter' => array(
					'=TABLE_NAME' => $arItem['DISPLAY_PROPERTIES']['LINK_COMPLECT']['USER_TYPE_SETTINGS']['TABLE_NAME']
				)
			)
		);
		if ($arData = $rsData->fetch()) {
			$entity = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($arData);
			$entityDataClass = $entity->getDataClass();
			$arFilter = array(
				'order' => ['UF_SORT' => 'ASC', 'ID' => 'DESC'],
				'filter' => array(
					'=UF_XML_ID' => $arItem['DISPLAY_PROPERTIES']['LINK_COMPLECT']['VALUE']
				)
			);
			$rsValues = $entityDataClass::getList($arFilter);
			while ($arValue = $rsValues->fetch()) {
				if (in_array("PRICE", $arParams["PROPERTY_CODE"])) {
					$arValue['PRICE'] = [
						'VALUE' => $arValue['UF_PRICE']
					];
				}
				if (in_array("PRICE_CURRENCY", $arParams["PROPERTY_CODE"]) && $arValue['UF_CURRENCY']) {
					$obEnum = new CUserFieldEnum;
					$arCurrency =$obEnum->GetList(array(), ['ID' => $arValue['UF_CURRENCY']])->Fetch();

					$arValue['PRICE_CURRENCY'] = [
						'VALUE' => $arCurrency['VALUE']
					];
				}
				if (in_array("PRICEOLD", $arParams["PROPERTY_CODE"])) {
					$arValue['PRICEOLD'] = [
						'VALUE' => $arValue['UF_PRICE_OLD']
					];
				}
				if (in_array("ECONOMY", $arParams["PROPERTY_CODE"])) {
					$arValue['ECONOMY'] = [
						'VALUE' => $arValue['UF_ECONOMY']
					];
				}
				$arValue['FILTER_PRICE'] = [
					'VALUE' => $arValue['UF_FILTER_PRICE']
				];
				$arResult['ITEMS'][$key]['LINK_COMPLECT'][] = $arValue;
			}
		}
	}
}
unset($arItem);
