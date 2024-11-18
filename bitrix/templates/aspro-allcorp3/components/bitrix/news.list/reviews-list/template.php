<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true ) die();?>
<?$this->setFrameMode(true);?>
<?use \Bitrix\Main\Localization\Loc;?>
<?if($arResult['ITEMS']):?>
	<?
	$templateData['ITEMS'] = true;

	$bTextCentered = $arParams['TEXT_CENTER'] == 'Y';

	$bShowTitle = $arParams['TITLE'] && $arParams['FRONT_PAGE'] && $arParams['SHOW_TITLE'];
	$bShowTitleLink = $arParams['RIGHT_TITLE'] && $arParams['RIGHT_LINK'];

	$bShowRating = in_array('RATING', $arParams['PROPERTY_CODE']);
	$bHaveMore = count($arResult['ITEMS']) > $arParams['ELEMENT_IN_ROW'];
	$arParams['SHOW_NEXT'] = $arParams['SHOW_NEXT'] && $bHaveMore;

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

	$qntyItems = count($arResult['ITEMS']);

	global $arTheme;
	$slideshowSpeed = abs(intval($arTheme['PARTNERSBANNER_SLIDESSHOWSPEED']['VALUE']));
	$animationSpeed = abs(intval($arTheme['PARTNERSBANNER_ANIMATIONSPEED']['VALUE']));
	$bAnimation = (bool)$slideshowSpeed;

	$bTopNav = (!$arParams['TITLE_CENTER'] && $arParams['SHOW_TITLE']) && $arParams['NARROW'];

	$blockClasses = '';
	if($arParams['TEXT_CENTER']) {
		$blockClasses .= ' reviews-list--text-center';
	}
	if(!$arParams['ITEMS_OFFSET']) {
		$blockClasses .= ' reviews-list--items-close';
	}

	$owlClasses = ' owl-carousel--light owl-carousel--outer-dots owl-carousel--static-dots owl-carousel--dots-padding-top-20';
	if($bShowTitle) {
		$owlClasses .= ' owl-carousel--margin-top-64';
	}

	if(!array_key_exists('SLIDER_BUTTONS_BORDERED', $arParams) || $arParams['SLIDER_BUTTONS_BORDERED']){
		$owlClasses .= ' owl-carousel--buttons-bordered';
	}

	// if(!$bTopNav) {
		$owlClasses .= ' owl-carousel--button-wide';
		if($arParams['NARROW']) {
			$owlClasses .= ' owl-carousel--button-offset-half';
		} else {
			$owlClasses .= ' owl-carousel--button-offset-none';
		}
	// }
	if($bTopNav) {
		$owlClasses .= ' owl-carousel--buttons-size-36';
	} else if(!$arParams['NARROW']) {
		$owlClasses .= ' owl-carousel--buttons-size-48';
	}
	if($arParams['ITEMS_OFFSET'] && !$arParams['NARROW']) {
		$owlClasses .= ' owl-carousel--padding-left-32';
		$owlClasses .= ' owl-carousel--padding-right-32';
	}
	if($arParams['BORDER']) {
		$owlClasses .= ' owl-carousel--with-shadow';
		$owlClasses .= ' owl-carousel--after-offset-1';
	}
	if(!$arParams['ITEMS_OFFSET']){
		$owlClasses .= ' owl-carousel--no-gap';
	}
	if($arParams['SHOW_NEXT']) {
		$owlClasses .= ' owl-carousel--show-next';
		if($arParams['NARROW']) {
			$owlClasses .= ' owl-carousel--narrow';
		}
	}
	if(!$arParams['TEXT_CENTER']) {
		if($arParams['ELEMENT_IN_ROW'] > 1){
			$owlClasses .= ' owl-carousel--items-width-360-adaptive';
		}
		$owlClasses .= ' owl-carousel--wide-adaptive';
	}
	if(!$arParams['NARROW']) {
		$owlClasses .= ' owl-carousel--wide-view';
	}

	$itemClasses = '';
	if($arParams['BORDER']) {
		$itemClasses .= ' bordered';
	}
	if($arParams['ITEM_ROW']) {
		$itemClasses .= ' reviews-list__item--row';
	}
	if($arParams['ITEM_PADDING']) {
		$itemClasses .= ' reviews-list__item--padding-'.$arParams['ITEM_PADDING'];
	}
	if(!$arParams['ITEMS_OFFSET'] && $arParams['BORDER'] && ($arParams['ELEMENT_IN_ROW'] > 1 || $arParams['SHOW_NEXT']) ) {
		$itemClasses .= ' reviews-list__item--no-radius';
	}
	if($arParams['TEXT_CENTER']) {
		$itemClasses .= ' reviews-list__item--column';
		$itemClasses .= ' reviews-list__item--centered-vertical';
	}
	?>

	<div class="reviews-list <?=$blockClasses?>">
		<?=\Aspro\Functions\CAsproAllcorp3::showTitleBlock([
			'PATH' => 'reviews-list',
			'PARAMS' => $arParams,
		]);?>

		<?if($arParams['NARROW'] && !$arParams['SHOW_NEXT']):?>
			<div class="maxwidth-theme">
		<?endif;?>

	
		<div class="owl-carousel appear-block <?=$owlClasses?>" data-plugin-options='{"nav": true, "rewind": true, "dots": true, "dotsContainer": false, "loop": false, "autoplay": false, "marginMove": true, "margin": <?=$arParams['ITEMS_OFFSET'] ? '32' : ($arParams['BORDER'] ? '-1' : '0')?>, "responsive" : {"0": {"dots": <?=$bDots0?>, <?=($arParams['TEXT_CENTER'] ? '' : ($arParams['ELEMENT_IN_ROW'] > 1 ? '"autoWidth": true, "lightDrag": true,' : '"autoWidth": false, "lightDrag": false,'))?> "items": <?=$items0?> <?=($arParams['ITEMS_OFFSET'] ? ', "margin": 24' : '')?>}, "380": {"dots": <?=$bDots380?>, <?=($arParams['TEXT_CENTER'] ? '' : ($arParams['ELEMENT_IN_ROW'] > 1 ? '"autoWidth": true, "lightDrag": true,' : '"autoWidth": false, "lightDrag": false,'))?> "items": <?=$items380?> <?=($arParams['ITEMS_OFFSET'] ? ', "margin": 24' : '')?>}, "768": {"dots": <?=$bDots768?>, "autoWidth": false, "lightDrag": false, "items": <?=$items768?> }, "1200": {"dots": <?=$bDots1200?>, "items": <?=$items1200?>}}}'>
			<?
			$counter = 1;
			foreach($arResult['ITEMS'] as $i => $arItem):?>
				<?
				// edit/add/delete buttons for edit mode
				$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem['IBLOCK_ID'], 'ELEMENT_EDIT'));
				$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem['IBLOCK_ID'], 'ELEMENT_DELETE'), array('CONFIRM' => Loc::getMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));

				// preview image
				$bLogoImage = isset($arItem['FIELDS']['DETAIL_PICTURE']) && $arItem['DETAIL_PICTURE']['SRC'];
				$arImageTemp = $bLogoImage
							? $arItem['FIELDS']['DETAIL_PICTURE']
							: (
								(isset($arItem['FIELDS']['PREVIEW_PICTURE']) && $arItem['PREVIEW_PICTURE']['SRC'])
								? $arItem['FIELDS']['PREVIEW_PICTURE']
								: array()
							);
				$bImage = $arImageTemp;
				$nImageID = ($bImage ? $arImageTemp['ID'] : false);
				$arImage = $bImage ? 
							($bLogoImage 
								? CFile::GetFileArray($nImageID)
								: CFile::ResizeImageGet($nImageID, array('width' => 80, 'height' => 80), BX_RESIZE_IMAGE_EXACT, true)
				 			) : array();
				$imageSrc = $bImage ? 
								($bLogoImage
									? $arImage['SRC']
									: $arImage['src']
								) 
							: SITE_TEMPLATE_PATH.'/images/svg/noimage_staff.svg';

				?>
				<div class="reviews-list__item <?=$itemClasses?> <?=$counter == count($arResult['ITEMS']) ? 'reviews-list__item--last' : ''?> <?=$counter == 1 ? 'reviews-list__item--first' : ''?>" id="<?=$this->GetEditAreaId($arItem['ID']);?>">
					<div class="reviews-list__item-top-part <?=$arParams['TEXT_CENTER'] ? 'reviews-list__item-top-part--centered' : ''?> <?=$arParams['ITEM_ROW'] ? '' : 'reviews-list__item-top-part--centered-vertical'?> <?=$arParams['ITEM_TOP_PART_ROW'] ? 'reviews-list__item-top-part--row' : ''?> <?=$arParams['TOP_PART_COLUMN'] ? 'reviews-list__item-top-part--column' : ''?>">
						<div class="reviews-list__item-info-wrapper <?=$arParams['IMAGE_RIGHT'] ? 'reviews-list__item-info-wrapper--image-right' : ''?>">
							<?if($arParams['IMAGE'] && $imageSrc):?>
								<div class="reviews-list__item-image-wrapper <?=$bLogoImage ? 'reviews-list__item-image-wrapper--logo' : ''?> <?=$arParams['LOGO_CENTER'] ? 'reviews-list__item-image-wrapper--logo-center' : ''?> <?=($bImage ? '' : 'reviews-list__item-image--no-image')?> <?=$arParams['IMAGE_SIZE'] ? 'reviews-list__item-image-wrapper--image-'.$arParams['IMAGE_SIZE'] : ''?>">
									<?if($bLogoImage):?>
										<img alt="<?=$arItem['NAME']?>" title="<?=$arItem['NAME']?>" src="<?=$imageSrc?>" />
									<?else:?>
										<div class="reviews-list__item-image" style="background-image: url(<?=$imageSrc?>);"></div>
									<?endif;?>
								</div>
							<?endif;?>
							<div class="reviews-list__item-info <?=$arParams['RATING_RIGHT'] ? 'reviews-list__item-info--centered-vertical' : ''?>">
								<div class="reviews-list__item-text">
									<?if($arItem['DISPLAY_PROPERTIES']['POST']['VALUE'] || $arItem['DISPLAY_ACTIVE_FROM']) : ?>
										<div class="reviews-list__item-company font_13 color_999">
											<?= implode('<span class="reviews-list__separator">&mdash;</span>', array_filter([
												$arItem['PROPERTIES']['POST']['VALUE'] ? '<span>' . $arItem['PROPERTIES']['POST']['VALUE'] . '</span>' : null,
												$arItem['DISPLAY_ACTIVE_FROM'] ? '<span class="reviews-list__item-date-active ">' . $arItem['DISPLAY_ACTIVE_FROM'] . '</span>' : null
											])) ?>
										</div>
									<? endif; ?>
									<div class="reviews-list__item-title switcher-title <?=$arParams['NAME_LARGE'] ? ' font_22' : ' font_18'?>">
										<?=$arItem['NAME'];?>
									</div>
								</div>
								
								<?if($bShowRating && !$arParams['RATING_RIGHT']):?>
									<?$itemRating = $arItem['DISPLAY_PROPERTIES']['RATING']['VALUE'];?>
									<div class="rating reviews-list__rating">
										<?for($i = 0;$i < 5;$i++):?>
											<div class="rating__star">
												<?$svgClass = $i < $itemRating ? ' rating__star-svg rating__star-svg--filled' : ' rating__star-svg';?>
												<?=CAllcorp3::showIconSvg($svgClass, SITE_TEMPLATE_PATH.'/images/svg/rating_star_20.svg');?>
											</div>
										<?endfor;?>
									</div>
								<?endif;?>
							</div>
						</div>

						<?if($bShowRating && $arParams['RATING_RIGHT']):?>
							<?$itemRating = $arItem['DISPLAY_PROPERTIES']['RATING']['VALUE'];?>
							<div class="rating reviews-list__rating">
								<?for($i = 0;$i < 5;$i++):?>
									<div class="rating__star">
										<?$svgClass = $i < $itemRating ? ' rating__star-svg rating__star-svg--filled' : ' rating__star-svg';?>
										<?=CAllcorp3::showIconSvg($svgClass, SITE_TEMPLATE_PATH.'/images/svg/rating_star_20.svg');?>
									</div>
								<?endfor;?>
							</div>
						<?endif;?>
					</div>

					<?if(strlen($arItem['FIELDS']['PREVIEW_TEXT'])):?>
						<div class="reviews-list__item-preview-wrapper">
							<div class="reviews-list__item-preview font_15 font_large">
								<?if($arItem['PREVIEW_TEXT_TYPE'] == 'text'):?>
									<p><?=$arItem['FIELDS']['PREVIEW_TEXT'];?></p>
								<?else:?>
									<?=$arItem['FIELDS']['PREVIEW_TEXT'];?>
								<?endif;?>
							</div>
							<?if(strlen($arParams['PREVIEW_TRUNCATE_LEN']) && strlen($arItem['~PREVIEW_TEXT']) > $arParams['PREVIEW_TRUNCATE_LEN']):?>
								<div class="reviews-list__item-more">
									<span class="btn btn-default <?=$arParams['MORE_BTN_CLASS'] ? $arParams['MORE_BTN_CLASS'] : ''?> btn-transparent-border animate-load" data-event="jqm" data-param-id="<?=$arItem['ID'];?>" data-param-type="review" data-name="review"><?=Loc::getMessage('MORE');?></span>
								</div>
							<?endif;?>
						</div>
					<?endif;?>
				</div>
			<?
			$counter++;
			endforeach;?>
		</div>

		<?if($arParams['NARROW'] && !$arParams['SHOW_NEXT']):?>
			</div>
		<?endif;?>
		
	</div>
<?endif;?>