<?php

use \Bitrix\Main\Localization\Loc;

?>
<? if ($templateData['GOODS'] && $templateData['GOODS']['IBLOCK_ID'] && $templateData['GOODS']['VALUE']): ?>
	<?
	$GLOBALS['arrGoodsFilter'] = ['ID' => $templateData['GOODS']['VALUE']];
	$GLOBALS['arrGoodsFilter'] = array_merge($GLOBALS['arrGoodsFilter'], (array)$GLOBALS['arRegionLink']);
	?>
	<?
	$bCheckAjaxBlock = CAllcorp3::checkRequestBlock("goods-list-inner");
	$isAjax = (CAllcorp3::checkAjaxRequest() && $bCheckAjaxBlock ) ? 'Y' : 'N';
	?>
	<? ob_start(); ?>
	<?/* $APPLICATION->IncludeComponent(
		"bitrix:catalog.section",
		"catalog_block",
		array(
			"IS_CATALOG_PAGE" => "N",
			"DETAIL_URL" => $arResult["FOLDER"] . $arResult["URL_TEMPLATES"]["element"],
			"IBLOCK_ID" => $templateData['GOODS']['IBLOCK_ID'],
			"CACHE_TYPE" => "A",
			"CACHE_TIME" => "36000000",
			"CACHE_FILTER" => "Y",
			"CACHE_GROUPS" => "N",
			"FILTER_NAME" => "arrGoodsFilter",
			"HIT_PROP" => "HIT",
			"IBLOCK_TYPE" => "aspro_allcorp3_catalog",
			"PAGE_ELEMENT_COUNT" => "20",
			"PROPERTY_CODE" => array(
				0 => "STATUS",
				1 => "ARTICLE",
				2 => "PRICE",
				3 => "PRICEOLD",
				4 => "ECONOMY",
				5 => "DATE_COUNTER",
				6 => "INCLUDE_TEXT",
			),
			"ELEMENT_SORT_FIELD" => "SORT",
			"ELEMENT_SORT_ORDER" => "ASC",
			"ELEMENT_SORT_FIELD2" => "ID",
			"ELEMENT_SORT_ORDER2" => "DESC",
			"FIELD_CODE" => array(
				0 => "NAME",
				1 => "PREVIEW_TEXT",
				2 => "PREVIEW_PICTURE",
				3 => "",
			),
			"ELEMENTS_TABLE_TYPE_VIEW" => "FROM_MODULE",
			"SHOW_SECTION" => "Y",
			"LINE_ELEMENT_COUNT" => "3",
			"SHOW_DISCOUNT_TIME" => "Y",
			"SHOW_OLD_PRICE" => "Y",
			"SHOW_PREVIEW_TEXT" => "N",
			"SHOW_DISCOUNT_PRICE" => "Y",
			"SHOW_GALLERY" => "Y",
			"ADD_PICT_PROP" => "PHOTOS",
			"MAX_GALLERY_ITEMS" => '',
			"DISPLAY_TOP_PAGER" => "N",
			"DISPLAY_BOTTOM_PAGER" => "N",
			"PAGER_TITLE" => "",
			"PAGER_TEMPLATE" => ".default",
			"PAGER_SHOW_ALWAYS" => "N",
			"PAGER_DESC_NUMBERING" => "N",
			"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
			"PAGER_SHOW_ALL" => "N",
			"INCLUDE_SUBSECTIONS" => "Y",
			"SHOW_ALL_WO_SECTION" => "Y",
			"SECTION_COUNT_ELEMENTS" => '',
			"IS_CATALOG_PAGE" => 'Y',
			"META_KEYWORDS" => '',
			"META_DESCRIPTION" => '',
			"BROWSER_TITLE" => '',
			"ADD_SECTIONS_CHAIN" => "N",
			"DISPLAY_COMPARE" => 'Y',
			"SHOW_ONE_CLINK_BUY" => 'Y',
			"ELEMENT_IN_ROW" => "3",
			"POSITION_BTNS" => "3",
			"AJAX_REQUEST" => 'N',
			"TEXT_CENTER" => false,
			"IMG_CORNER" => false,
			"ORDER_VIEW" => true,
			"GRID_GAP" => "0",
			"ROW_VIEW" => true,
			"SLIDER" => true,
			"SLIDER_BUTTONS_BORDERED" => false,
			"IS_COMPACT_SLIDER" => false,
			"BORDER" => true,
			"ITEM_HOVER_SHADOW" => false,
			"DARK_HOVER" => false,
			"ROUNDED" => true,
			"ROUNDED_IMAGE" => true,
			"ITEM_PADDING" => true,
			"ELEMENTS_ROW" => 1,
			"MAXWIDTH_WRAP" => false,
			"MOBILE_SCROLLED" => false,
			"ITEM_0" => "2",
			"NARROW" => 'Y',
			"ITEMS_OFFSET" => false,
			"IMAGES" => "PICTURE",
			"IMAGE_POSITION" => "LEFT",
			"SHOW_PREVIEW" => true,
			"SHOW_TITLE" => false,
			"TITLE_POSITION" => "",
			"TITLE" => "",
			"RIGHT_TITLE" => "",
			"RIGHT_LINK" => "",
			"CHECK_REQUEST_BLOCK" => CAllcorp3::checkRequestBlock("goods-list-inner"),
			"IS_AJAX" => CAllcorp3::checkAjaxRequest(),
			"NAME_SIZE" => "18",
			"SUBTITLE" => "",
			"SHOW_PREVIEW_TEXT" => "N",
		),
		$component, array('HIDE_ICONS' => 'Y')
	); */?>
		<?\Aspro\Functions\CAsproAllcorp3::showBlockHtml([
			'FILE' => '/detail_linked_goods.php',
			'PARAMS' => array_merge(
				$arParams,
				array(
					'ORDER_VIEW' => $bOrderViewBasket,
					'CHECK_REQUEST_BLOCK' => $bCheckAjaxBlock,
					'IS_AJAX' => $isAjax,
					'ELEMENT_IN_ROW' => $APPLICATION->GetProperty('MENU') === 'Y' ? 4 : 5,
				)
			)
		]);?>
	<? $html = trim(ob_get_clean()); ?>
	<? if ($html && strpos($html, 'error') === false): ?>
		<div class="detail-block ordered-block">
			<div class="ordered-block__title switcher-title font_22"><?= $arParams['T_GOODS'] ?: Loc::getMessage('EPILOG_BLOCK__ITEMS') ?></div>
			<div class="ajax-pagination-wrapper" data-class="goods-list-inner">
				<?if ($isAjax === 'Y'):?>
					<?$APPLICATION->RestartBuffer();?>
				<?endif;?>
				<?= $html ?>
				<?if ($isAjax === 'Y'):?>
					<?die();?>
				<?endif;?>
			</div>
		</div>
	<? endif; ?>
<? endif; ?>