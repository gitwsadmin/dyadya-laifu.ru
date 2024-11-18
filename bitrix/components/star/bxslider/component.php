<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

	$arResult["JQUERY"] = $arParams["JQUERY"];
	$arResult["BANNER_TYPE"] = $arParams["BANNER_TYPE"];
	$arResult["COUNT_SLIDES"] = intval($arParams["COUNT_SLIDES"]);
	$arResult["SLIDE_WIDTH"] = intval($arParams["SLIDE_WIDTH"]);
	$arResult["MARGIN"] = intval($arParams["MARGIN"]);
	$arResult["MODE"] = $arParams["MODE"];

	$arResult["ADAPTIVE_HEIGHT"] = $arParams["ADAPTIVE_HEIGHT"];
	
	if ($arResult["ADAPTIVE_HEIGHT"] == "false") {
		if ($arParams["MIDDLE_HEIGHT"] == "true") $arResult["MIDDLE_HEIGHT"] = "true";
		else $arResult["MIDDLE_HEIGHT"] = "false";
	} else {
		$arResult["MIDDLE_HEIGHT"] = "false";
	}

	$arResult["CONTROLS"] = $arParams["CONTROLS"];
	$arResult["PAGER"] = $arParams["PAGER"];
	$arResult["AUTO"] = $arParams["AUTO"];
	$arResult["SPEED"] = intval($arParams["SPEED"]);
	$arResult["PAUSE"] = intval($arParams["PAUSE"]);
	$arResult["INFINITE_LOOP"] = "true"; // TODO: add to settings?
	$arResult["USE_FANCYBOX"] = $arParams["USE_FANCYBOX"];
	if ($arParams["CAPTIONS"] == "true") $arResult["CAPTIONS"] = $arParams["CAPTIONS"];
	else $arResult["CAPTIONS"] = "false";

	if ($arResult["USE_FANCYBOX"] == "false") {
		if ($arParams["NEW_WINDOW"] == "N") $arResult["NEW_WINDOW"] = "";
		else $arResult["NEW_WINDOW"] = " target=\"_blank\"";
	} else {
		$arResult["NEW_WINDOW"] = "";
	}

	// Select elements from iblock
	$arResult["ITEMS"] = array();
	if ($arParams["DATA_TYPE"] == "FOLDER") {
		foreach (glob($_SERVER["DOCUMENT_ROOT"]."/".SITE_DIR.$arParams["FOLDER"]."/*.{jpg,jpeg,png}", GLOB_NOESCAPE | GLOB_BRACE) as $file) {
			if (!is_dir($file)) {
				$arResult["ITEMS"][] = array("PICTURE" => str_replace($_SERVER["DOCUMENT_ROOT"]."/".SITE_DIR, "", $file));
			}
		}
	} else {
		if(!CModule::IncludeModule("iblock") || !isset($arParams["IBLOCK_ID"])) return;
		
		$arOrder = Array($arParams["SORT_BY1"] => $arParams["SORT_ORDER1"]);
		$arSelect = Array("ID", "IBLOCK_ID", "NAME", "PREVIEW_PICTURE", "DETAIL_PICTURE", "PROPERTY_".$arParams["PROPERTY_CODE"]);
		$arFilter = Array("IBLOCK_ID" => intval($arParams["IBLOCK_ID"]), "ACTIVE"=>"Y");
		$res = CIBlockElement::GetList($arOrder, $arFilter, false, Array ("nTopCount" => intval($arParams["COUNT"])), $arSelect);
		while($ob = $res->GetNextElement()) {
			$item = $ob->GetFields();

			$img = CFile::GetFileArray($item[$arParams["IMAGE"]."_PICTURE"]);
			if (is_array($img) && isset($img["SRC"])) {
				$item["PICTURE"] = $img["SRC"];

				if ($arResult["USE_FANCYBOX"] == "false") {
					if (!empty($item["PROPERTY_".strtoupper($arParams["PROPERTY_CODE"])."_VALUE"])) $item["LINK"] = $item["PROPERTY_".strtoupper($arParams["PROPERTY_CODE"])."_VALUE"];
				}

				$arResult["ITEMS"][] = $item;
			}
		}
	}

	$this->IncludeComponentTemplate();
?>