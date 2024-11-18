<?
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

$indexPageOptions = $GLOBALS['arTheme']['INDEX_TYPE']['SUB_PARAMS'][$GLOBALS['arTheme']['INDEX_TYPE']['VALUE']];
$blockOptions = $indexPageOptions['GALLERY'];
$blockTemplateOptions = $blockOptions['TEMPLATE']['LIST'][$blockOptions['TEMPLATE']['VALUE']];

$galleryId = false;
$iblock_id = CAllcorp3Cache::$arIBlocks[SITE_ID]['aspro_allcorp3_content']['aspro_allcorp3_gallery'][0];
if($iblock_id){
	if(
		$arMainGallery = CAllcorp3Cache::CIBlockElement_GetList(
			array(
				'SORT' => 'ASC',
				'ID' => 'DESC',
				'CACHE' => array(
					'TAG' => CAllcorp3Cache::GetIBlockCacheTag($iblock_id),
					'MULTI' => 'N'
				)
			),
			array(
				'IBLOCK_ID' => $iblock_id,
				'ACTIVE' => 'Y',
				'PROPERTY_MAIN_ALBUM_VALUE' => 'Y',
				'PROPERTY_SHOW_ON_INDEX_PAGE_VALUE' => 'Y',
				'INCLUDE_SUBSECTIONS' => 'Y',
			),
			false,
			false,
			array('ID')
		)
	){
		$galleryId = $arMainGallery['ID'];
	}
}
?>
<?if($galleryId):?>
	<?$APPLICATION->IncludeComponent(
		"bitrix:news.detail", 
		"gallery-item", 
		array(
			"DISPLAY_DATE" => "N",
			"DISPLAY_NAME" => "Y",
			"DISPLAY_PICTURE" => "Y",
			"DISPLAY_PREVIEW_TEXT" => "Y",
			"USE_SHARE" => "N",
			"SHARE_HIDE" => "N",
			"SHARE_TEMPLATE" => "",
			"SHARE_HANDLERS" => array(
				0 => "delicious",
			),
			"SHARE_SHORTEN_URL_LOGIN" => "",
			"SHARE_SHORTEN_URL_KEY" => "",
			"AJAX_MODE" => "N",
			"IBLOCK_TYPE" => "aspro_allcorp3_content",
			"IBLOCK_ID" => "19",
			"ELEMENT_ID" => $galleryId,
			"ELEMENT_CODE" => "",
			"CHECK_DATES" => "Y",
			"FIELD_CODE" => array(
				0 => "ID",
				1 => "NAME",
				2 => "PREVIEW_TEXT",
				3 => "PREVIEW_PICTURE",
				4 => "",
			),
			"PROPERTY_CODE" => array(
				0 => "",
				1 => "PHOTOS",
				2 => "",
			),
			"IBLOCK_URL" => "",
			"DETAIL_URL" => "",
			"SET_TITLE" => "N",
			"SET_CANONICAL_URL" => "N",
			"SET_BROWSER_TITLE" => "N",
			"BROWSER_TITLE" => "-",
			"SET_META_KEYWORDS" => "N",
			"META_KEYWORDS" => "-",
			"SET_META_DESCRIPTION" => "N",
			"META_DESCRIPTION" => "-",
			"SET_STATUS_404" => "N",
			"SET_LAST_MODIFIED" => "N",
			"INCLUDE_IBLOCK_INTO_CHAIN" => "N",
			"ADD_SECTIONS_CHAIN" => "N",
			"ADD_ELEMENT_CHAIN" => "N",
			"ACTIVE_DATE_FORMAT" => "d.m.Y",
			"USE_PERMISSIONS" => "N",
			"GROUP_PERMISSIONS" => array(
				0 => "",
			),
			"CACHE_TYPE" => "A",
			"CACHE_TIME" => "3600000",
			"CACHE_FILTER" => "Y",
			"CACHE_GROUPS" => "Y",
			"DISPLAY_TOP_PAGER" => "N",
			"DISPLAY_BOTTOM_PAGER" => "N",
			"PAGER_TITLE" => "",
			"PAGER_TEMPLATE" => "ajax",
			"PAGER_SHOW_ALL" => "N",
			"PAGER_BASE_LINK_ENABLE" => "N",
			"SHOW_404" => "N",
			"MESSAGE_404" => "",
			"STRICT_SECTION_CHECK" => "Y",
			"PAGER_BASE_LINK" => "",
			"PAGER_PARAMS_NAME" => "arrPager",
			"AJAX_OPTION_JUMP" => "N",
			"AJAX_OPTION_STYLE" => "N",
			"AJAX_OPTION_HISTORY" => "N",
			"TEMPLATE_VIEW" => $blockOptions["TEMPLATE"]["VALUE"],
			"NARROW" => true,
			"SHOW_TITLE" => $blockOptions["INDEX_BLOCK_OPTIONS"]["BOTTOM"]["SHOW_TITLE"]["VALUE"]=="Y",
			"TITLE_POSITION" => $blockOptions["INDEX_BLOCK_OPTIONS"]["BOTTOM"]["TITLE_POSITION"]["VALUE"],
			"RIGHT_TITLE" => "Вcя галерея",
			"RIGHT_LINK" => "gallery/",
			"CHECK_REQUEST_BLOCK" => CAllcorp3::checkRequestBlock("gallery"),
			"IS_AJAX" => CAllcorp3::checkAjaxRequest(),
			"SUBTITLE" => "Галерея",
			"SHOW_PREVIEW_TEXT" => "Y",
			"COMPOSITE_FRAME_MODE" => "A",
			"COMPOSITE_FRAME_TYPE" => "AUTO",
			"COMPONENT_TEMPLATE" => "gallery-item",
			"AJAX_OPTION_ADDITIONAL" => "",
		),
		false
	);?>
<?endif;?>