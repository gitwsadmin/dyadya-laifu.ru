<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?$this->setFrameMode(true);?>
<?use \Bitrix\Main\Localization\Loc,
	  \Bitrix\Main\Web\Json;?>
<?if($arResult["ITEMS"]):?>
	<?
	$templateData['ITEMS'] = true;

	$bHasBottomPager = $arParams["DISPLAY_BOTTOM_PAGER"] == "Y" && $arResult["NAV_STRING"];
	$bUseSchema = !(isset($arParams["NO_USE_SHCEMA_ORG"]) && $arParams["NO_USE_SHCEMA_ORG"] == "Y");
	$bAjax = $arParams["AJAX_REQUEST"]=="Y";
	$bMobileScrolledItems = $arParams['MOBILE_SCROLLED'];
	$bSlider = $arParams['SLIDER'] === true || $arParams['SLIDER'] === 'Y';

	$bShowProps = $arParams['SHOW_PROPS'] == 'Y';

	if ($bShowProps) {
		$arParams['SHOW_GALLERY'] = 'N';
	}

	$bOrderViewBasket = $arParams['ORDER_VIEW'];
	$basketURL = (strlen(trim($arTheme['ORDER_VIEW']['DEPENDENT_PARAMS']['URL_BASKET_SECTION']['VALUE'])) ? trim($arTheme['ORDER_VIEW']['DEPENDENT_PARAMS']['URL_BASKET_SECTION']['VALUE']) : '');

	if($bSlider){
		$bDots1200 = $arParams['DOTS_1200'] === 'Y' ? 1 : 0;
		if($arParams['ITEM_1200']) {
			$items1200 = intval($arParams['ITEM_1200']);
		}
		else{
			$items1200 = $arParams['ELEMENT_IN_ROW'] ? $arParams['ELEMENT_IN_ROW'] : 1;
		}

		$bDots768 = $arParams['DOTS_768'] === 'Y' ? 1 : 0;
		if($arParams['ITEM_768']) {
			$items768 = intval($arParams['ITEM_768']);
		}
		else{
			$items768 = 
				$arParams['ELEMENT_IN_ROW'] > 1 ? 2 : 1;
		}

		$bDots380 = $arParams['DOTS_380'] === 'Y' ? 1 : 0;
		if($arParams['ITEM_380']) {
				$items380 = intval($arParams['ITEM_380']);
			}
			else{
				$items380 = 1;
			}

		$bDots0 = $arParams['DOTS_0'] === 'Y' ? 1 : 0;
		if($arParams['ITEM_0']) {
			$items0 = intval($arParams['ITEM_0']);
		}
		else{
			$items0 = 1;
		}

		$owlClasses = ' owl-carousel--light owl-carousel--outer-dots owl-carousel--button-wide owl-carousel--items-'.$arParams['ELEMENT_IN_ROW'];
		if($arParams['NARROW']) {
			$owlClasses .= ' owl-carousel--button-offset-half';
		}
		else{
			$owlClasses .= ' owl-carousel--button-offset-none';
		}

		if(!array_key_exists('SLIDER_BUTTONS_BORDERED', $arParams) || $arParams['SLIDER_BUTTONS_BORDERED']){
			$owlClasses .= ' owl-carousel--buttons-bordered';
		}
		
		if($arParams['ITEMS_OFFSET'] && !$arParams['NARROW']) {
			$owlClasses .= ' owl-carousel--padding-left-32';
			$owlClasses .= ' owl-carousel--padding-right-32';
		}
		if($arParams['SHOW_NEXT']) {
			$owlClasses .= ' owl-carousel--show-next';
			if($arParams['NARROW']) {
				$owlClasses .= ' owl-carousel--narrow';
			}
		}
		if(!$arParams['NARROW']) {
			$owlClasses .= ' owl-carousel--wide-view';
		}
		if($arParams['BORDER']) {
			$owlClasses .= ' owl-carousel--after-offset-1';
		}
		if(!$arParams['ITEMS_OFFSET']){
			$owlClasses .= ' owl-carousel--no-gap';
		}
		if ($arParams['ITEM_HOVER_SHADOW'] !== false){
			$owlClasses .= ' owl-carousel--with-shadow';
		}
	}
	else{
		$gridClass .= ' grid-list--items-2-991 grid-list--items-'.$arParams['ELEMENT_IN_ROW'];
	
		if($bMobileScrolledItems){
			$gridClass .= ' mobile-scrolled mobile-scrolled--items-2 mobile-offset';
		}
		else {
			$gridClass .= ' grid-list--compact';
		}
	
		if (!$arParams['ITEMS_OFFSET']) {
			$gridClass .= ' grid-list--no-gap';
		}
		elseif ($arParams['GRID_GAP']) {
			$gridClass .= ' grid-list--gap-'.$arParams['GRID_GAP'];
		}
		if ($arParams['NARROW']) {
			$gridClass .= ' grid-list--items-'.$arParams['ELEMENT_IN_ROW'];
		} else {
			$gridClass .= ' grid-list--items-'.$arParams['ELEMENT_IN_ROW'].'-wide';
		}
	}

	$itemClass = ' bordered side-icons-hover bg-theme-parent-hover border-theme-parent-hover js-popup-block';
	if ($arParams['ITEM_HOVER_SHADOW'] !== false){
		$itemClass .= ' shadow-hovered shadow-hovered-f600 shadow-no-border-hovered';
	}
	if ($arParams['ITEMS_OFFSET']) {
		$itemClass .= ' rounded-4';
	}
	if ($arParams['TEXT_CENTER']) {
		$itemClass .= ' catalog-block__item--centered';
	}

	$bBottomButtons = (isset($arParams['POSITION_BTNS']) && $arParams['POSITION_BTNS'] == '4');
	?>
	<?if(!$bAjax):?>
	<div class="catalog-items <?=$templateName;?>_template <?=$arParams['IS_COMPACT_SLIDER'] ? 'compact-catalog-slider' : ''?> <?=$arParams['PICTURE_RATIO'] && $arParams['PICTURE_RATIO'] !== "normal" ? "ratio--".$arParams['PICTURE_RATIO'] : "" ?>">
		<div class="fast_view_params" data-params="<?=urlencode(serialize($arTransferParams));?>"></div>
		<?if ($arResult['SKU_CONFIG']):?><div class="js-sku-config" data-value='<?=str_replace('\'', '"', CUtil::PhpToJSObject($arResult['SKU_CONFIG'], false, true))?>'></div><?endif;?>
		<div class="catalog-block" <?if ($bUseSchema):?>itemscope itemtype="http://schema.org/ItemList"<?endif;?> >
			<?if($bSlider):?>
				<div class="js_append ajax_load owl-carousel appear-block <?=$owlClasses?>" data-plugin-options='{"nav": true, "rewind": true, "dots": true, "dotsContainer": false, "loop": false, "autoplay": false, "marginMove": true, "margin": <?=($arParams['ITEMS_OFFSET'] ? ($arParams['GRID_GAP'] ? $arParams['GRID_GAP'] : "32") : ($arParams['BORDER'] ? "-1" : "0"))?>, "responsive" : {"0": {"autoWidth": false, "lightDrag": false, "dots": <?=$bDots0?>, "items": <?=$items0?>}, "380": {"autoWidth": false, "lightDrag": false, "dots": <?=$bDots380?>, "items": <?=$items380?>}, "768": {"autoWidth": false, "lightDrag": false, "dots": <?=$bDots768?>, "items": <?=$items768?>}, "1200": {"autoWidth": false, "lightDrag": false, "dots": <?=$bDots1200?>, "items": <?=$items1200?>} }}'>
			<?else:?>
				<div class="js_append ajax_load block grid-list <?=$gridClass?>">
			<?endif;?>
	<?endif;?>
		<?foreach($arResult["ITEMS"] as $arItem){?>
			<?$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_EDIT"));
			$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BCS_ELEMENT_DELETE_CONFIRM')));

			$item_id = $arItem["ID"];

			// if (isset($arParams['ID_FOR_TABS']) && $arParams['ID_FOR_TABS'] == 'Y') {
			// 	$arItem["strMainID"] = $this->GetEditAreaId($arItem['ID'])."_".$arParams["FILTER_HIT_PROP"];
			// } else {
			// 	$arItem["strMainID"] = $this->GetEditAreaId($arItem['ID']);
			// }
			$arItem["strMainID"] = $this->GetEditAreaId($arItem['ID']);

			// use order button?
			$bOrderButton = ($arItem["PROPERTIES"]["FORM_ORDER"]["VALUE_XML_ID"] == "YES");
			$dataItem = ($bOrderViewBasket ? CAllcorp3::getDataItem($arItem) : false);

			$article = $arItem['DISPLAY_PROPERTIES']['ARTICLE']['VALUE'];
			$status = $arItem['DISPLAY_PROPERTIES']['STATUS']['VALUE'];
			$statusCode = $arItem['DISPLAY_PROPERTIES']['STATUS']['VALUE_XML_ID'];

			/* sku replace start */
			$arCurrentOffer = $arItem['SKU']['CURRENT'];

			if ($arCurrentOffer) {
				$arItem['PARENT_IMG'] = '';
				if ($arItem['PREVIEW_PICTURE']) {
					$arItem['PARENT_IMG'] = $arItem['PREVIEW_PICTURE'];
				} elseif ($arItem['DETAIL_PICTURE']) {
					$arItem['PARENT_IMG'] = $arItem['DETAIL_PICTURE'];
				}

				$oid = \Bitrix\Main\Config\Option::get('aspro.allcorp3', 'CATALOG_OID', 'oid');
				if ($oid) {
					$arItem['DETAIL_PAGE_URL'].= '?'.$oid.'='.$arCurrentOffer['ID'];
					$arCurrentOffer['DETAIL_PAGE_URL'] = $arItem['DETAIL_PAGE_URL'];
				}
				if ($arParams['SHOW_GALLERY'] === 'Y') {
					$pictureID = $arCurrentOffer['PREVIEW_PICTURE'] ?? $arCurrentOffer['DETAIL_PICTURE'];
					if ($pictureID) {
						$arPicture = \CFile::GetFileArray($pictureID);

						$alt = $arParams['CHANGE_TITLE_ITEM_LIST'] === 'Y' ? $arCurrentOffer['NAME'] : $arItem['NAME'];
						$title = $arParams['CHANGE_TITLE_ITEM_LIST'] === 'Y' ? $arCurrentOffer['NAME'] : $arItem['NAME'];
						if ($arResult['ALT_TITLE_GET'] == 'SEO') {
							$alt = $arPicture['ALT'] ?: $alt;
							$title = $arPicture['TITLE'] ?: $title;
						}
						else {
							$alt = $arPicture['DESCRIPTION'] ?: $arPicture['ALT'] ?: $alt;
							$title = $arPicture['DESCRIPTION'] ?: $arPicture['TITLE'] ?: $title;
						}

						$arPicture['TITLE'] = $title;
						$arPicture['ALT'] = $alt;

						array_unshift($arItem['GALLERY'], $arPicture);
						array_splice($arItem['GALLERY'], $arParams['MAX_GALLERY_ITEMS']);
					}
				} else {
					if ($arCurrentOffer['PREVIEW_PICTURE'] || $arCurrentOffer['DETAIL_PICTURE']) {
						if ($arCurrentOffer['PREVIEW_PICTURE']) {
							$arItem['PREVIEW_PICTURE'] = $arCurrentOffer['PREVIEW_PICTURE'];
						} elseif ($arCurrentOffer['DETAIL_PICTURE']) {
							$arItem['PREVIEW_PICTURE'] = $arCurrentOffer['DETAIL_PICTURE'];
						}
					}
					if (!$arCurrentOffer['PREVIEW_PICTURE'] && !$arCurrentOffer['DETAIL_PICTURE']) {
						if ($arItem['PREVIEW_PICTURE']) {
							$arCurrentOffer['PREVIEW_PICTURE'] = $arItem['PREVIEW_PICTURE'];
						} elseif ($arItem['DETAIL_PICTURE']) {
							$arCurrentOffer['PREVIEW_PICTURE'] = $arItem['DETAIL_PICTURE'];
						}
					}
				}

				if ($arCurrentOffer["DISPLAY_PROPERTIES"]["ARTICLE"]["VALUE"]) {
					$article = $arCurrentOffer['DISPLAY_PROPERTIES']['ARTICLE']['VALUE'];
				}
				if ($arCurrentOffer["DISPLAY_PROPERTIES"]["STATUS"]["VALUE"]) {
					$status = $arCurrentOffer['DISPLAY_PROPERTIES']['STATUS']['VALUE'];
					$statusCode = $arCurrentOffer['DISPLAY_PROPERTIES']['STATUS']['VALUE_XML_ID'];
				}

				$arItem["DISPLAY_PROPERTIES"]["FORM_ORDER"] = $arCurrentOffer["DISPLAY_PROPERTIES"]["FORM_ORDER"];
				$arItem["DISPLAY_PROPERTIES"]["PRICE"] = $arCurrentOffer["DISPLAY_PROPERTIES"]["PRICE"];

				$arItem['OFFER_PROP'] = CAllcorp3::PrepareItemProps($arCurrentOffer['DISPLAY_PROPERTIES']);
				
				$dataItem = ($bOrderViewBasket ? CAllcorp3::getDataItem($arCurrentOffer) : false);
			
				if ($arParams['CHANGE_TITLE_ITEM_LIST'] === 'Y') {
					$arItem['NAME'] = $arCurrentOffer['IPROPERTY_VALUES']['ELEMENT_PAGE_TITLE'] ?? $arCurrentOffer['NAME'];
					$arItem['IPROPERTY_VALUES']['ELEMENT_PAGE_TITLE'] = $arItem['NAME'];
				}
			}

			$elementName = ((isset($arItem['IPROPERTY_VALUES']['ELEMENT_PAGE_TITLE']) && $arItem['IPROPERTY_VALUES']['ELEMENT_PAGE_TITLE']) ? $arItem['IPROPERTY_VALUES']['ELEMENT_PAGE_TITLE'] : $arItem['NAME']);

			$bOrderButton = ($arItem["DISPLAY_PROPERTIES"]["FORM_ORDER"]["VALUE_XML_ID"] == "YES");
			/* sku replace end */
			?>

			<?ob_start();?>
				<?if ($arParams["SHOW_DISCOUNT_TIME"]=="Y" && $arParams['SHOW_COUNTER_LIST'] != 'N' && $arItem['DISPLAY_PROPERTIES']['DATE_COUNTER']['VALUE']):?>
					<?\Aspro\Functions\CAsproAllcorp3::showDiscountCounter([
						'TYPE' => 'list-image',
						'ICONS' => true,
						'DATE' => $arItem['DISPLAY_PROPERTIES']['DATE_COUNTER']['VALUE'],
						'ITEM' => $arItem
					]);?>
				<?endif;?>
			<?$itemDiscount = ob_get_clean();?>
			
			<div class="catalog-block__wrapper grid-list__item grid-list-border-outer <?=($bSlider ? 'height-100' : '')?> <?=($arCurrentOffer ? 'has-offers' : '');?>" data-hovered="false">
				<div class="catalog-block__item <?=$itemClass?>" id="<?=$arItem["strMainID"]?>">
					<div class="catalog-block__inner flexbox height-100" <?if ($bUseSchema):?>itemprop="itemListElement" itemscope="" itemtype="http://schema.org/Product"<?endif;?>>
						<?if ($bUseSchema):?>
							<?/*<meta itemprop="position" content="<?=(++$positionProduct)?>" />*/?>
							<meta itemprop="description" content="<?=htmlspecialcharsbx(strip_tags($arItem['PREVIEW_TEXT'] ?: $arItem['NAME']))?>" />
						<?endif;?>
						<?$arImgConfig = [
							'TYPE' => 'catalog_block',
							'ADDITIONAL_IMG_CLASS' => 'js-replace-img',
							'ADDITIONAL_WRAPPER_CLASS' => ($arParams['IMG_CORNER'] == 'Y' ? 'catalog-block__item--img-corner' : ''),
						];?>
						<div class="js-config-img" data-img-config='<?=str_replace('\'', '"', CUtil::PhpToJSObject($arImgConfig, false, true))?>'></div>
						
						<?if ($bShowProps && $arItem['SHOW_PROPERTIES']):?>
							<div class="properties-absolute-outer <?=($arParams['IMG_CORNER'] == 'Y' ? 'properties-absolute-margin' : '')?>">
								<div class="properties properties_absolute srollbar-custom scroll-deferred">
									<div class="properties__container js-offers-prop">
										<?if ($arItem['SHOW_PROPERTIES']):?>
											<?foreach($arItem['SHOW_PROPERTIES'] as $arProp):?>
												<div class="properties__item properties--mt-10 js-prop-replace">
													<div class="properties__title font_sxs muted js-prop-title">
														<div class="hint hint--first">
															<?/*if($arProp["HINT"] && $arParams["SHOW_HINTS"]=="Y"):?>
																<span class="hint__icon rounded bg-theme-hover border-theme-hover bordered"><i>?</i></span><div class="tooltip tooltip--manual"><?=$arProp["HINT"]?></div>
															<?endif;*/?>
															<?=$arProp['NAME']?>
														</div>
													</div>

													<div class="properties__value font_sm darken js-prop-value">
														<?
														if(is_array($arProp["DISPLAY_VALUE"])) { foreach($arProp["DISPLAY_VALUE"] as $key => $value) { if ($arProp["DISPLAY_VALUE"][$key+1]) {echo $value.", ";} else {echo $value;} }}
														else { echo $arProp["DISPLAY_VALUE"]; }
														?>
													</div>
												</div>
											<?endforeach;?>
										<?endif;?>
										<?if ($arItem['OFFER_PROP']):?>
											<?foreach($arItem['OFFER_PROP'] as $arProp):?>
												<div class="properties__item properties--mt-10 js-prop">
													<div class="properties__title font_sxs muted">
														<div class="hint hint--first">
															<?=$arProp['NAME']?>
														</div>
													</div>
													<div class="properties__value font_sm darken">
														<?
														if(is_array($arProp["VALUE"])) { foreach($arProp["VALUE"] as $key => $value) { if ($arProp["VALUE"][$key+1]) {echo $value.", ";} else {echo $value;} }}
														else { echo $arProp["VALUE"]; }
														?>
													</div>
												</div>
											<?endforeach;?>
										<?endif;?>
									</div>
								</div>
							</div>
						<?endif;?>

						<?if ($bShowProps && $arItem['SHOW_PROPERTIES']):?>
						<div class="image-list-outer <?=($arParams['IMG_CORNER'] == 'Y' ? '' : 'image-list-outer_overflow')?>">
						<?endif;?>
							<?=\Aspro\Functions\CAsproAllcorp3::showImage(
								array_merge(
									[
										'ITEM' => $arItem,
										'PARAMS' => $arParams,
										'CONTENT' => $itemDiscount,
									],
									$arImgConfig
								)
							)?>
						<?if ($bShowProps && $arItem['SHOW_PROPERTIES']):?>
						</div>
						<?endif;?>
						

						<?if ($bUseSchema):?>
							<meta itemprop="name" content="<?=$arItem['NAME']?>">
							<link itemprop="url" href="<?=$arItem['DETAIL_PAGE_URL']?>">
						<?endif;?>
						<div 
							class="catalog-block__info flex-1 flexbox flexbox--justify-beetwen" 
							data-id="<?=($arCurrentOffer ? $arCurrentOffer['ID'] : $arItem['ID'])?>"
							<?if ($bUseSchema):?>itemprop="offers" itemscope itemtype="http://schema.org/Offer"<?endif;?>
							<?=($bOrderViewBasket ? ' data-item="'.$dataItem.'"' : '')?>
						>
							<div class="catalog-block__info-top">
								<?// element section?>
								<?if ($arItem['SECTIONS']):?>
									<div class="catalog-block__info-section color_999 font_12 linecamp-2"><?=implode('/', $arItem['SECTIONS'])?></div>
								<?endif;?>
								<div class="catalog-block__info-inner">
									<?// element title?>
									<div class="catalog-block__info-title linecamp-4 height-auto-t600 font_16">
										<?if ($bUseSchema):?>
											<link itemprop="url" href="<?=$arItem['DETAIL_PAGE_URL']?>">
										<?endif;?>
										<a href="<?=$arItem["DETAIL_PAGE_URL"]?>" class="dark_link switcher-title js-popup-title"><span><?=$elementName;?></span></a>
									</div>
									<?if (strlen($status) || strlen($article)):?>
										<div class="catalog-block__info-tech compact-hidden-t600">
											<div class="line-block line-block--20 flexbox--wrap js-popup-info">
												<?// element status?>
												<?if (strlen($status)):?>
													<div class="line-block__item font_13">
														<?if ($bUseSchema):?>
															<?=\Aspro\Functions\CAsproAllcorp3::showSchemaAvailabilityMeta($statusCode);?>
														<?endif;?>
														<span 
															 class="status-icon <?=$statusCode?> js-replace-status" 
															 data-state="<?=$statusCode?>"
															 data-code="<?=$arItem['DISPLAY_PROPERTIES']['STATUS']['VALUE_XML_ID']?>" 
															 data-value="<?=$arItem['DISPLAY_PROPERTIES']['STATUS']['VALUE']?>"
															><?=$status?></span>
													</div>
												<?endif;?>
												<?// element article?>
												<?if (strlen($article)):?>
													<div class="line-block__item font_13 color_999">
														<span class="article"><?=GetMessage('S_ARTICLE')?>&nbsp;<span 
															 class="js-replace-article"
															 data-value="<?=$arItem['DISPLAY_PROPERTIES']['ARTICLE']['VALUE']?>"
															><?=$article?></span></span>
													</div>
												<?endif;?>
											</div>
										</div>
									<?endif;?>
								</div>
							</div>
							<div class="catalog-block__info-bottom">
								<div class="line-block line-block--20 flexbox--wrap flexbox--justify-beetwen <?=(!$bBottomButtons ? '' : 'catalog-block--bottom-btn');?>">
									<?// element price?>
									
									<?if (isset($arItem['PROPERTIES']['PRICE']) && strlen($arItem['PROPERTIES']['PRICE']['VALUE'])) {?>
										<div class="line-block__item catalog-block__info-bottom--margined js-popup-price <?=(!$bBottomButtons ? 'catalog-block__info-price' : '');?>">
											<?=\Aspro\Functions\CAsproAllcorp3::showPrice([
												'ITEM' => ($arCurrentOffer ? $arCurrentOffer : $arItem),
												'PARAMS' => $arParams,
												'SHOW_SCHEMA' => $bUseSchema,
												'BASKET' => $bOrderViewBasket,
											]);?>
										</div>
									<?}?>
									<?// element btns?>
									<?$arBtnConfig = [
										'BASKET_URL' => $basketURL,
										'BASKET' => $bOrderViewBasket,
										'ORDER_BTN' => $bOrderButton,
										'BTN_CLASS_MORE' => 'bg-theme-target border-theme-target',
										'SHOW_COUNTER' => false,
										'CATALOG_IBLOCK_ID' => $arItem['IBLOCK_ID'],
										'ITEM_ID' => $arItem['ID'],
									];?>
									<div class="line-block__item catalog-block__info-bottom--margined <?=(!$bBottomButtons ? 'catalog-block__info-btn' : '');?> <?=($arCurrentOffer ? 'hide-600' : '');?>">
										<div class="js-replace-btns js-config-btns" data-btn-config='<?=str_replace('\'', '"', CUtil::PhpToJSObject($arBtnConfig, false, true))?>'>
											<?=\Aspro\Functions\CAsproAllcorp3::showBasketButton(
												array_merge(
													$arBtnConfig, 
													[
														'ITEM' => ($arCurrentOffer ? $arCurrentOffer : $arItem),
														'PARAMS' => $arParams,
													]
												)
											);?>
										</div>
									</div>
								</div>
								<?if ($arCurrentOffer):?>
									<div class="visible-600">
										<?=\Aspro\Functions\CAsproAllcorp3::showBasketButton([
											'ORDER_BTN' => false,
											'ITEM' => $arCurrentOffer
										]);?>
									</div>
								<?endif;?>
							</div>
						</div>
						<?if ($arItem['SKU']['PROPS']):?>
							<div class="catalog-block__offers hide-600">
								<div 
								class="sku-props sku-props--list"
								data-site-id="<?=SITE_ID;?>"
								data-item-id="<?=$arItem['ID'];?>"
								data-iblockid="<?=$arItem['IBLOCK_ID'];?>"
								data-offer-id="<?=$arCurrentOffer['ID'];?>"
								data-offer-iblockid="<?=$arCurrentOffer['IBLOCK_ID'];?>"
								>
									<div class="line-block line-block--flex-wrap line-block--40 line-block--align-normal">
										<?=\Aspro\Allcorp3\Functions\CSKUTemplate::showSkuPropsHtml($arItem['SKU']['PROPS'])?>
									</div>
								</div>
							</div>
						<?endif;?>

					</div>
				</div>

			</div>
		<?}?>

		<?if(!$bSlider):?>
			<?if ($bHasBottomPager && $bMobileScrolledItems):?>
				<?if($bAjax):?>
					<div class="wrap_nav bottom_nav_wrapper">
				<?endif;?>

					<?$bHasNav = (strpos($arResult["NAV_STRING"], 'more_text_ajax') !== false);?>
					<div class="bottom_nav mobile_slider <?=($bHasNav ? '' : ' hidden-nav');?>" data-parent=".catalog-block" data-append=".grid-list" <?=($bAjax ? "style='display: none; '" : "");?>>
						<?=$arResult["NAV_STRING"]?>
					</div>

				<?if($bAjax):?>
					</div>
				<?endif;?>
			<?endif;?>
		<?endif;?>

	<?if(!$bAjax):?>
			</div> <?//.js_append ajax_load block grid-list?>
	<?endif;?>

		<?if($bAjax):?>
			<div class="wrap_nav bottom_nav_wrapper">
		<?endif;?>

		<?if($arParams['IS_CATALOG_PAGE'] == 'Y' && $arParams['SECTION_COUNT_ELEMENTS'] == 'Y'):?>
			<?if((int)$arResult['NAV_RESULT']->NavRecordCount > 0):?>
				<?$this->SetViewTarget("more_text_title");?>
					<span class="element-count-wrapper"><span class="element-count color_999 rounded-4"><?=$arResult['NAV_RESULT']->NavRecordCount;?></span></span>
				<?$this->EndViewTarget();?>
			<?endif;?>
		<?endif;?>

		<div class="bottom_nav_wrapper nav-compact">
			<div class="bottom_nav <?=($bMobileScrolledItems ? 'hide-600' : '');?>" <?=($arParams['AJAX_REQUEST'] == "Y" ? "style='display: none; '" : "");?> data-parent=".catalog-block" data-append=".grid-list">
				<?if($arParams['DISPLAY_BOTTOM_PAGER']):?>
					<?=$arResult['NAV_STRING']?>
				<?endif;?>
			</div>
		</div>	

	<?if($bAjax):?>
		</div>
	<?endif;?>

	<?if(!$bAjax):?>
		</div> <?//.catalog-block?>
	</div> <?//.catalog-items?>
	<?endif;?>

	<script>
		if (typeof initCountdown === "function") initCountdown();
		if (typeof setBasketItemsClasses === "function") setBasketItemsClasses();
		if (typeof setCompareItemsClass === "function") setCompareItemsClass();
	</script>
<?elseif($arParams['IS_CATALOG_PAGE'] == 'Y'):?>
	<div class="no_goods catalog_block_view">
		<div class="no_products">
			<div class="wrap_text_empty">
				<?if($_REQUEST["set_filter"]){?>
					<?$APPLICATION->IncludeFile(SITE_DIR."include/section_no_products_filter.php", Array(), Array("MODE" => "html",  "NAME" => GetMessage('EMPTY_CATALOG_DESCR')));?>
				<?}else{?>
					<?$APPLICATION->IncludeFile(SITE_DIR."include/section_no_products.php", Array(), Array("MODE" => "html",  "NAME" => GetMessage('EMPTY_CATALOG_DESCR')));?>
				<?}?>
			</div>
		</div>
	</div>
<?endif;?>