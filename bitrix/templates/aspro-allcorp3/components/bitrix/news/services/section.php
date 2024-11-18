<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
$this->setFrameMode(true);

global $arTheme, $APPLICATION;

$bShowLeftBlock = ($arTheme['LEFT_BLOCK_SERVICES_SECTIONS']['VALUE'] === 'Y' && !defined('ERROR_404'));
$APPLICATION->SetPageProperty('MENU', ($bShowLeftBlock ? 'Y' : 'N' ));

// get section items count and subsections
$arItemFilter = CAllcorp3::GetCurrentSectionElementFilter($arResult["VARIABLES"], $arParams);
$arSectionFilter = CAllcorp3::GetCurrentSectionFilter($arResult["VARIABLES"], $arParams);
$itemsCnt = CAllcorp3Cache::CIblockElement_GetList(array("CACHE" => array("TAG" => CAllcorp3Cache::GetIBlockCacheTag($arParams["IBLOCK_ID"]))), $arItemFilter, array());
$arSection = CAllcorp3Cache::CIblockSection_GetList(array("CACHE" => array("TAG" => CAllcorp3Cache::GetIBlockCacheTag($arParams["IBLOCK_ID"]), "MULTI" => "N")), $arSectionFilter, false, array('ID', 'NAME', 'DESCRIPTION', 'PICTURE', 'DETAIL_PICTURE', 'IBLOCK_ID', 'UF_TOP_SEO'));
$arSubSectionFilter = CAllcorp3::GetCurrentSectionSubSectionFilter($arResult["VARIABLES"], $arParams, $arSection['ID']);
$arSubSections = CAllcorp3Cache::CIblockSection_GetList(array("CACHE" => array("TAG" => CAllcorp3Cache::GetIBlockCacheTag($arParams["IBLOCK_ID"]), "MULTI" => "Y")), $arSubSectionFilter, false, array("ID", "DEPTH_LEVEL"));
?>
<?if(!$arSection && $arParams['SET_STATUS_404'] !== 'Y'):?>
	<div class="alert alert-warning"><?=GetMessage("SECTION_NOTFOUND")?></div>
<?elseif(!$arSection && $arParams['SET_STATUS_404'] === 'Y'):?>
	<?CAllcorp3::goto404Page();?>
<?else:?>
	<?
	CAllcorp3::AddMeta(
		array(
			'og:description' => $arSection['DESCRIPTION'],
			'og:image' => (($arSection['PICTURE'] || $arSection['DETAIL_PICTURE']) ? CFile::GetPath(($arSection['PICTURE'] ? $arSection['PICTURE'] : $arSection['DETAIL_PICTURE'])) : false),
		)
	);

	CAllcorp3::CheckComponentTemplatePageBlocksParams($arParams, __DIR__);
	?>
	
	<?// rss?>
	<?if($arParams['USE_RSS'] !== 'N'):?>
		<?$this->SetViewTarget('cowl_buttons');?>
		<?Aspro\Functions\CAsproAllcorp3::ShowRSSIcon(
			array(
				'URL' => $arResult['FOLDER'].$arResult['URL_TEMPLATES']['rss']
			)
		);?>
		<?$this->EndViewTarget();?>
	<?endif;?>
	
	<?if(!$arSubSections && !$itemsCnt):?>
		<div class="alert alert-warning"><?=GetMessage("SECTION_EMPTY")?></div>
	<?endif;?>

	<?if (
		$arSection['UF_TOP_SEO'] &&
		strpos($_SERVER['REQUEST_URI'], 'PAGEN') === false &&
		$arParams['SHOW_SECTION_DESCRIPTION'] != 'N'
	):?>
		<div class="text_before_items">
			<p><?=$arSection['UF_TOP_SEO'];?></p>
		</div>
	<?endif;?>

	<?if($arSubSections):?>
		<?// sections list?>
		<?$sViewElementTemplate = ($arParams["SECTION_TYPE_VIEW"] == "FROM_MODULE" ? $arTheme["SECTION_TYPE_VIEW_SERVICES"]["VALUE"] : $arParams["SECTION_TYPE_VIEW"]);?>
		
		<div class="section-wrapper-list">
			<?@include_once('page_blocks/'.$sViewElementTemplate.'.php');?>
		</div>
	<?endif;?>

	<?if (CAllcorp3::checkAjaxRequest()):?>
		<?$APPLICATION->RestartBuffer()?>
	<?endif;?>
	
	<?if(strlen($arParams["FILTER_NAME"])):?>
		<?$GLOBALS[$arParams["FILTER_NAME"]] = array_merge((array)$GLOBALS[$arParams["FILTER_NAME"]], $arItemFilter);?>
	<?else:?>
		<?$arParams["FILTER_NAME"] = "arrFilter";?>
		<?$GLOBALS[$arParams["FILTER_NAME"]] = $arItemFilter;?>
	<?endif;?>

	<?// section elements?>
	<?$sViewElementTemplate = ($arParams["SECTION_ELEMENTS_TYPE_VIEW"] == "FROM_MODULE" ? $arTheme["ELEMENTS_PAGE_SERVICES"]["VALUE"] : $arParams["SECTION_ELEMENTS_TYPE_VIEW"]);?>

	<?@include_once('page_blocks/'.$sViewElementTemplate.'.php');?>

	<?if (CAllcorp3::checkAjaxRequest()):?>
		<?die()?>
	<?endif;?>

	<?if (
		$arSection['DESCRIPTION'] && 
		strpos($_SERVER['REQUEST_URI'], 'PAGEN') === false &&
		$arParams['SHOW_SECTION_DESCRIPTION'] != 'N'
	):?>
		<div class="text_after_items">
			<?=$arSection['DESCRIPTION'];?>
		</div>
	<?endif;?>
<?endif;?>