<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
	die();
}

use \Bitrix\Main\Localization\Loc;

$this->setFrameMode(true);

Loc::loadMessages(__FILE__);

$arItemFilter = CAllcorp3::GetIBlockAllElementsFilter($arParams);
$itemsCnt = CAllcorp3Cache::CIblockElement_GetList(['CACHE' => ['TAG' => CAllcorp3Cache::GetIBlockCacheTag($arParams['IBLOCK_ID'])]], $arItemFilter, []);
?>



<? if (!$itemsCnt): ?>
	<div class="alert alert-warning"><?= Loc::getMessage('FAQ__SECTION_EMPTY') ?></div>
<? else : ?>
	<?CAllcorp3::CheckComponentTemplatePageBlocksParams($arParams, __DIR__);?>
	<?// section elements?>
	<?@include_once('page_blocks/'.$arParams["SECTION_ELEMENTS_TYPE_VIEW"].'.php');?>
	<?if($arParams["SHOW_ASK_QUESTION_BLOCK"] == "Y"){
		include('include_bottom_block.php');
	}?>	
<? endif ?>

