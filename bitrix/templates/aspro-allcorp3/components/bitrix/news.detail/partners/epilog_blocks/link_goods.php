<?php

use \Bitrix\Main\Localization\Loc;

?>
<? if($arParams['SHOW_LINK_GOODS'] == 'Y' && 
	$arParams['LINK_GOODS_IBLOCK_ID'] > 0 && 
	in_array('items', $GLOBALS["SHOW_TYPE_ITEMS"]) 
): ?>

	<?
	$bCheckAjaxBlock = CAllcorp3::checkRequestBlock("goods-list-inner");
	$isAjax = (CAllcorp3::checkAjaxRequest() && $bCheckAjaxBlock ) ? 'Y' : 'N';
	?>

	<? ob_start(); ?>
		<?
		$GLOBALS['arrGoodsFilter'] = array(
			'PROPERTY_' . $arParams['LINK_GOODS_PROP_CODE'] => $arResult['ID'],
			'SECTION_GLOBAL_ACTIVE' => 'Y',
			'ACTIVE' => 'Y',
		);
		$GLOBALS['arrGoodsFilter'] = array_merge($GLOBALS['arrGoodsFilter'], (array)$GLOBALS['arRegionLink']);
		?>
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
			<div class="ordered-block__title switcher-title font_22">
				<?=
				$arParams['T_LINK_GOODS']
					? str_replace('#NAME#', $arResult['NAME'], $arParams['T_LINK_GOODS'])
					: Loc::getMessage('EPILOG_BLOCK__GOODS', [
						'#NAME#' => $arResult['NAME'],
					])
				?>
			</div>
					
			
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