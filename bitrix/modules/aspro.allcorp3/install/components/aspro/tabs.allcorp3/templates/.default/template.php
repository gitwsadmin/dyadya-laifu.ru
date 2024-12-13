<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
/** @var CBitrixComponentTemplate $this */
/** @var array $arParams */
/** @var array $arResult */
/** @global CDatabase $DB */

$this->setFrameMode(true);
if ($arResult["TABS"]):?>
	<?
	$arTab=array();
	$arParams['SET_TITLE'] = 'N';
	$arTmp = reset($arResult["TABS"]);
	$arParams["FILTER_HIT_PROP"] = $arTmp["CODE"];
	$arParamsTmp = urlencode(serialize($arParams));
	?>
	<div class="element-list <?=$blockClasses?> <?=$templateName?>-template">
		<?$navHtml = '';?>
		<?if (count($arResult["TABS"]) > 1):?>
			<?ob_start();?>
				<div class="tab-nav-wrapper">
					<div class="tab-nav font_14" data-template="<?=$arParams['TYPE_TEMPLATE']?>">
						<?$i = 0;?>
						<?foreach ($arResult["TABS"] as $key => $arItem):?>
							<div class="tab-nav__item  bg-opacity-theme-hover bg-theme-active bg-theme-hover-active color-theme-hover-no-active <?=(!$i ? ' active clicked' : '');?>" data-code="<?=$key;?>"><?=$arItem['TITLE']?></div>
							<?++$i;?>
						<?endforeach;?>
					</div>
				</div>
			<?$navHtml = ob_get_clean();?>
		<?endif;?>
		
		<?=\Aspro\Functions\CAsproAllcorp3::showTitleBlock([
			'PATH' => 'elements-list',
			'PARAMS' => $arParams,
			'CENTER_BLOCK' => $navHtml
		]);?>

		<?if($arParams['NARROW']):?>
			<div class="maxwidth-theme">
		<?elseif($arParams['ITEMS_OFFSET']):?>
			<div class="maxwidth-theme maxwidth-theme--no-maxwidth">
		<?endif;?>
			<span id="js-request-data" class='request-data' data-value='<?=$arParamsTmp?>'></span>
			<div class="js-tabs-ajax">
				<?$i = 0;?>
				<?foreach ($arResult["TABS"] as $key => $arItem):?>
					<div class="tab-content-block <?=(!$i ? 'active ' : 'loading-state');?>" data-code="<?=$key?>" data-filter="<?=($arItem["FILTER"] ? urlencode(serialize($arItem["FILTER"])) : '');?>">
						<?if ($_REQUEST['AJAX_REQUEST'] == 'Y') {
							$APPLICATION->RestartBuffer();
						}?>
						<?if (!$i) {
							if ($arItem["FILTER"]) {
								$GLOBALS[$arParams["FILTER_NAME"]] = $arItem["FILTER"];
							}

							include(str_replace("//", "/", $_SERVER["DOCUMENT_ROOT"].SITE_DIR."include/mainpage/comp_catalog_ajax.php"));
						}?>
						<?if ($_REQUEST['AJAX_REQUEST'] == 'Y') {
							CAllcorp3::checkRestartBuffer(true, 'catalog_tab');
						}?>
					</div>
					<?++$i;?>
				<?endforeach;?>
			</div>
		<?if($arParams['NARROW'] || $arParams['ITEMS_OFFSET']):?>
		</div>
		<?endif;?>
	</div>
	<script>try{window.tabsInitOnReady();}catch(e){console.log(e);}</script>
<?endif;?>