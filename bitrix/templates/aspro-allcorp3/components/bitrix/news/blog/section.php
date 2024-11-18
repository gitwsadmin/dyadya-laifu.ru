<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
$this->setFrameMode(true);

global $arTheme, $APPLICATION;

$bShowLeftBlock = ($arTheme['BLOG_PAGE_LEFT_BLOCK']['VALUE'] === 'Y' && !defined('ERROR_404'));
$APPLICATION->SetPageProperty('MENU', ($bShowLeftBlock ? 'Y' : 'N' ));

// geting section items count and section [ID, NAME]
$arItemFilter = CAllcorp3::GetCurrentSectionElementFilter($arResult["VARIABLES"], $arParams);
$arSectionFilter = CAllcorp3::GetCurrentSectionFilter($arResult["VARIABLES"], $arParams);
$itemsCnt = CAllcorp3Cache::CIblockElement_GetList(array("CACHE" => array("TAG" => CAllcorp3Cache::GetIBlockCacheTag($arParams["IBLOCK_ID"]))), $arItemFilter, array());
$arSection = CAllcorp3Cache::CIblockSection_GetList(array("CACHE" => array("TAG" => CAllcorp3Cache::GetIBlockCacheTag($arParams["IBLOCK_ID"]), "MULTI" => "N")), $arSectionFilter, false, array('ID', 'NAME', 'DESCRIPTION', 'PICTURE', 'DETAIL_PICTURE', 'UF_TOP_SEO'), true);
?>
<?if(!$arSection && $arParams['SET_STATUS_404'] !== 'Y'):?>
	<?// get element start
	$arItemElementFilter = CAllcorp3::GetCurrentElementFilter($arResult['VARIABLES'], $arParams);
	if ($arItemElementFilter) {
		foreach ($arItemElementFilter as $key => $value) {
			if ($key == 'SECTION_CODE') {
				$arItemElementFilter['CODE'] = $value;
				$arResult['VARIABLES']['ELEMENT_CODE'] = $value;
				unset($arItemElementFilter[$key]);
				unset($arResult['VARIABLES']['SECTION_CODE']);
			}
			if ($key == 'SECTION_ID') {
				$arItemElementFilter['ID'] = $value;
				$arResult['VARIABLES']['ELEMENT_ID'] = $value;
				unset($arItemElementFilter[$key]);
				unset($arResult['VARIABLES']['SECTION_ID']);
			}
		}
		$arItemElementFilter['SECTION_ID'] = $arResult['VARIABLES']['SECTION_ID'] = 0;
	}
	if ($arParams['CACHE_GROUPS'] == 'Y') {
		$arItemElementFilter['CHECK_PERMISSIONS'] = 'Y';
		$arItemElementFilter['GROUPS'] = $GLOBALS["USER"]->GetGroups();
	}

	$arElement = CAllcorp3Cache::CIblockElement_GetList(array('CACHE' => array('TAG' => CAllcorp3Cache::GetIBlockCacheTag($arParams['IBLOCK_ID']), 'MULTI' => 'N')), $arItemElementFilter, false, false, array('ID', 'PREVIEW_TEXT', 'IBLOCK_SECTION_ID', 'PREVIEW_PICTURE', 'DETAIL_PICTURE'));
	if ($arElement) {
		include_once('detail.php');
		return ;
	}
	// get element end?>
	<div class="alert alert-warning"><?=GetMessage("SECTION_NOTFOUND")?></div>
<?elseif (!$arSection && $arParams['SET_STATUS_404'] === 'Y'):?>
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

	<?
	// edit/add/delete buttons for edit mode
	$arSectionButtons = CIBlock::GetPanelButtons($arSection['IBLOCK_ID'], 0, $arSection['ID'], array('SESSID' => false, 'CATALOG' => true));
	$this->AddEditAction($arSection['ID'], $arSectionButtons['edit']['edit_section']['ACTION_URL'], CIBlock::GetArrayByID($arSection['IBLOCK_ID'], 'SECTION_EDIT'));
	$this->AddDeleteAction($arSection['ID'], $arSectionButtons['edit']['delete_section']['ACTION_URL'], CIBlock::GetArrayByID($arSection['IBLOCK_ID'], 'SECTION_DELETE'), array('CONFIRM' => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
	?>
	<div class="main-section-wrapper" id="<?=$this->GetEditAreaId($arSection['ID'])?>">
		
		<?// show filter?>
		<?include('include/filter.php')?>

		<?if($arSection['UF_TOP_SEO'] && strpos($_SERVER['REQUEST_URI'], 'PAGEN') === false):?>
			<div class="text_before_items">
				<p><?=$arSection['UF_TOP_SEO'];?></p>
			</div>
		<?endif;?>

		<?// get SECTIONS and TAG for right block?>
		<?$arSideInfo = \Aspro\Functions\CAsproAllcorp3::getSectionsWithElementCount([
			'FILTER' => $arItemFilter,
			'SECTION' => $arSection,
			'PARAMS' => $arParams
		])?>

		<?$this->__component->__template->SetViewTarget('under_sidebar_content');?>
			<?if(count($arSideInfo['SECTIONS']) > 1):?>
				<?\Aspro\Functions\CAsproAllcorp3::showBlockHtml([
					'FILE' => '/menu/blog_side.php',
					'PARAMS' => [
						'SECTIONS' => array_merge(
							[
								[
									'TEXT' => GetMessage('ALL_ARTICLES'),
									'LINK' => $arParams['SEF_FOLDER']
								]
							], 
							$arSideInfo['SECTIONS']
						),
					]
				])?>
			<?endif;?>

			<?if($arSideInfo['TAGS']):?>
				<?$APPLICATION->IncludeComponent(
					"bitrix:search.tags.cloud",
					"main",
					Array(
						"CACHE_TIME" => "86400",
						"CACHE_TYPE" => "A",
						"CHECK_DATES" => "Y",
						"COLOR_NEW" => "3E74E6",
						"COLOR_OLD" => "C0C0C0",
						"COLOR_TYPE" => "N",
						"FILTER_NAME" => "",
						"FONT_MAX" => "50",
						"FONT_MIN" => "10",
						"PAGE_ELEMENTS" => "150",
						"TAGS_ELEMENT" => $arSideInfo['TAGS'],
						"FILTER_NAME" => "",
						"PERIOD" => "",
						"PERIOD_NEW_TAGS" => "",
						"SHOW_CHAIN" => "N",
						"SORT" => "NAME",
						"TAGS_INHERIT" => "Y",
						"URL_SEARCH" => SITE_DIR."search/index.php",
						"WIDTH" => "100%",
						"arrFILTER" => array("iblock_aspro_allcorp3_content"),
						"arrFILTER_iblock_aspro_allcorp3_content" => array($arParams["IBLOCK_ID"])
					), $component
				);?>
			<?endif;?>
		<?$this->__component->__template->EndViewTarget();?>

		<?if (CAllcorp3::checkAjaxRequest()):?>
			<?$APPLICATION->RestartBuffer()?>
		<?endif;?>

		<?// section elements?>
		<?$sViewElementsTemplate = ($arParams["SECTION_ELEMENTS_TYPE_VIEW"] == "FROM_MODULE" ? $arTheme["BLOG_PAGE"]["VALUE"] : $arParams["SECTION_ELEMENTS_TYPE_VIEW"]);?>
		<?@include_once('page_blocks/'.$sViewElementsTemplate.'.php');?>

		<?if (CAllcorp3::checkAjaxRequest()):?>
			<?die()?>
		<?endif;?>

		<?if($arSection['DESCRIPTION'] && strpos($_SERVER['REQUEST_URI'], 'PAGEN') === false):?>
			<div class="text_after_items">
				<?=$arSection['DESCRIPTION'];?>
			</div>
		<?endif;?>
		
	</div>
<?endif;?>