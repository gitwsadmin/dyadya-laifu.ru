<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<? $this->setFrameMode(true); ?>
<? use \Bitrix\Main\Localization\Loc; ?>
<? if ($arResult['ITEMS']): ?>
	<?
	$templateData['ITEMS'] = true;

	$bTextCentered = $arParams['TEXT_CENTER'] == 'Y';

	$bShowTitle = $arParams['TITLE'] && $arParams['FRONT_PAGE'] && $arParams['SHOW_TITLE'];
	$bShowTitleLink = $arParams['RIGHT_TITLE'] && $arParams['RIGHT_LINK'];
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

	$blockClasses = '';

	if (!$arParams['ITEMS_OFFSET']) {
		$blockClasses .= ' staff-list--items-close';
	}
	if ($arParams['ITEMS_OFFSET']) {
		$blockClasses .= ' staff-list--items-offset';
	}

	$itemClasses = '';
	if ($arParams['BORDER']) {
		$itemClasses .= ' bordered';
	}
	if (!$arParams['ITEMS_OFFSET'] && ($arParams['ELEMENT_IN_ROW'] > 1 || $arParams['SHOW_NEXT'])) {
		$itemClasses .= ' staff-list__item--no-radius';
	}
	if ($arParams['TEXT_POSITION'] == 'BOTTOM' || $arParams['TEXT_POSITION'] == 'BOTTOM_RELATIVE') {
		$itemClasses .= ' staff-list__item--scroll-text-hover';
	}
	if ($arParams['TEXT_POSITION'] == 'BOTTOM') {
		$itemClasses .= ' staff-list__item--dark-text-hover';
	}
	if ($arParams['ITEM_HOVER_SHADOW']) {
		$itemClasses .= ' staff-list__item--shadow';
	}
	if ($arParams['ITEM_ROW']) {
		$itemClasses .= ' staff-list__item--row';
	}
	if ($arParams['ITEM_ROW_REVERSE']) {
		$itemClasses .= ' staff-list__item--row-reverse';
	}
	if ($arParams['ITEM_FLEX']) {
		$itemClasses .= ' staff-list__item--flex';
	}

	$bottomClass = '';
	$owPluginOptions = '';
	if ($arParams['TYPE_VIEW'] == 'VIEW1' || $arParams['TYPE_VIEW'] == 'DETAIL') {
		$blockClasses .= ' staff-list--view1';
		$itemClasses .= ' staff-list__item--scroll-text-hover';
		$owlClasses2 = 'owl-carousel--outer-dots';
		if($arParams['TYPE_VIEW'] == 'VIEW1'){
			$owlClasses2 .= ' owl-carousel--view1';
		}
		else{
			$owlClasses2 .= ' owl-carousel--detail owl-carousel--light owl-carousel--button-wide owl-carousel--button-offset-half';
		}

		$owPluginOptions = '"dotsContainer": false,';
		if ($arParams['NARROW'] && $arParams['SHOW_NEXT']) {
			$owPluginOptions = '"dotsContainer": "#carousel-dots_staff", "navContainer": "#carousel-navigation_staff",';
		}
		if($arParams['NARROW'] && !$arParams['SHOW_NEXT']) {
			$owlClasses2 .= ' owl-carousel--nav-offset';
		}
		if (!$arParams['NARROW']) {
			$owlClasses2 .= ' owl-carousel--nav-arrow-lg';
		}
	}

	if ($arParams['TYPE_VIEW'] == 'VIEW2') {
		$blockClasses .= ' staff-list--view2';
		$itemClasses .= '';
		$owlClasses2 = 'owl-carousel--view1 owl-carousel--outer-dots';

		$owPluginOptions = '"dotsContainer": false,';
		if ($arParams['NARROW'] && $arParams['SHOW_NEXT']) {
			$owPluginOptions = '"dotsContainer": "#carousel-dots_staff", "navContainer": "#carousel-navigation_staff",';
		}
		if($arParams['NARROW'] && !$arParams['SHOW_NEXT']) {
			$owlClasses2 .= ' owl-carousel--nav-offset';
		}

		if($arParams['ITEMS_OFFSET']) {
			$owlClasses2 .= ' owl-carousel--shadow';
		} elseif($arParams['SHOW_NEXT']) {
			$owlClasses2 .= ' owl-carousel--shadow';
		} else {
			$owlClasses2 .= ' owl-carousel--shadow-offset';
		}

		if ($arParams['NARROW']) {
		} else {
			$owlClasses2 .= ' owl-carousel--nav-arrow-lg';
		}
	}

	if ($arParams['TYPE_VIEW'] == 'VIEW3') {
		$blockClasses .= ' staff-list--view3';
		$itemClasses .= '';
		$owlClasses2 = 'owl-carousel--view1 owl-carousel--outer-dots';
		$bottomClass = 'btn btn-default btn-transparent-border animate-load has-ripple';

		$owPluginOptions = '"dotsContainer": false,';
		if ($arParams['NARROW'] && $arParams['SHOW_NEXT']) {
			$owPluginOptions = '"dotsContainer": "#carousel-dots_staff", "navContainer": "#carousel-navigation_staff",';
		}
		if($arParams['NARROW'] && !$arParams['SHOW_NEXT']) {
			$owlClasses2 .= ' owl-carousel--nav-offset';
		}

		if($arParams['ITEMS_OFFSET']) {
			$owlClasses2 .= ' owl-carousel--shadow';
		} elseif($arParams['SHOW_NEXT']) {
			$owlClasses2 .= ' owl-carousel--shadow';
		} else {
			$owlClasses2 .= ' owl-carousel--shadow-offset';
		}

		if ($arParams['NARROW']) {
		} else {
			$owlClasses2 .= ' owl-carousel--nav-arrow-lg';
		}
	}

	if ($arParams['TYPE_VIEW'] == 'VIEW4') {
		$blockClasses .= ' staff-list--view4';
		$itemClasses .= ' staff-list__item--scroll-text-hover';
		$owlClasses2 = 'owl-carousel--view1 owl-carousel--outer-dots';

		$owPluginOptions = '"dotsContainer": false,';
		if ($arParams['NARROW'] && $arParams['SHOW_NEXT']) {
			$owPluginOptions = '"dotsContainer": "#carousel-dots_staff", "navContainer": "#carousel-navigation_staff",';
		}
		if($arParams['NARROW'] && !$arParams['SHOW_NEXT']) {
			$owlClasses2 .= ' owl-carousel--nav-offset';
		}

		if($arParams['ITEMS_OFFSET']) {
			$owlClasses2 .= ' owl-carousel--shadow';
		} elseif($arParams['SHOW_NEXT']) {
			$owlClasses2 .= ' owl-carousel--shadow';
		} else {
			$owlClasses2 .= ' owl-carousel--shadow-offset';
		}

		if ($arParams['NARROW']) {
		} else {
			$owlClasses2 .= ' owl-carousel--nav-arrow-lg';
		}
	}
	?>

	<div class="staff-list <?= $blockClasses ?>">
		<?=\Aspro\Functions\CAsproAllcorp3::showTitleBlock([
			'PATH' => 'staff-list',
			'PARAMS' => $arParams,
		]);?>

		<? if ($arParams['NARROW'] && !$arParams['SHOW_NEXT']): ?>
		<div class="maxwidth-theme staff-carousel-wrapper staff-carousel-wrapper--narrow">
		<? endif; ?>
			<div class="staff-carousel">
				<? if ($arParams['NARROW'] && $arParams['SHOW_NEXT']): ?>
				<div class="owl-carousel-view1__wrapper" data-carousel-without-hiding>
				<? endif; ?>
					<div id="carousel_staff" class="owl-carousel appear-block <? /*=$owlClasses*/ ?> <?= $owlClasses2 ?>"
						 data-plugin-options='{<?= $owPluginOptions ?> "nav": true, "rewind": true, "dots": true, "loop": false, "autoplay": false, "marginMove": true, "margin": <?=($arParams['ITEMS_OFFSET'] ? '32' : '0')?>, "responsive" : {"0": {"autoWidth": true, "lightDrag": false, "items": <?=$items0?>, "dots": <?=$bDots0?>, "margin": <?=($arParams['ITEMS_OFFSET'] ? '24' : '0')?>}, "380": {"autoWidth": true, "lightDrag": false, "items": <?=$items380?>, "dots": <?=$bDots380?>, "margin": <?=($arParams['ITEMS_OFFSET'] ? '24' : '0')?>}, "768": {"autoWidth": false, "lightDrag": false, "dots": <?=$bDots768?>, "items": <?=$items768?> }, "1200": {"items": <?=$items1200?>, "dots": <?=$bDots1200?>} }}'>
						<?
						$counter = 1;
						foreach ($arResult['ITEMS'] as $i => $arItem):?>
							<?
							// edit/add/delete buttons for edit mode
							$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem['IBLOCK_ID'], 'ELEMENT_EDIT'));
							$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem['IBLOCK_ID'], 'ELEMENT_DELETE'), array('CONFIRM' => Loc::getMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));

							// preview image
							$bImage = (isset($arItem['FIELDS']['PREVIEW_PICTURE']) && $arItem['PREVIEW_PICTURE']['SRC']);
							$nImageID = ($bImage ? (is_array($arItem['FIELDS']['PREVIEW_PICTURE']) ? $arItem['FIELDS']['PREVIEW_PICTURE']['ID'] : $arItem['FIELDS']['PREVIEW_PICTURE']) : "");
							$imageSrc = ($bImage ? CFile::getPath($nImageID) : SITE_TEMPLATE_PATH . '/images/svg/noimage_staff.svg');

							?>
							<div class="staff-list__item <?= $itemClasses ?> <?= $counter == count($arResult['ITEMS']) ? 'staff-list__item--last' : '' ?> <?= $counter == 1 ? 'staff-list__item--first' : '' ?>"
								 id="<?= $this->GetEditAreaId($arItem['ID']); ?>">
								<? if ($imageSrc): ?>
									<div class="staff-list__item-image-wrapper">
										<div class="staff-list__item-image"
											 style="background-image: url(<?= $imageSrc ?>);"></div>
										<a class="staff-list__item-link" href="<?= $arItem['DETAIL_PAGE_URL'] ?>"></a>

										<? if ($arParams['TYPE_VIEW'] == 'VIEW2'): ?>
											<? if ($arItem['PROPERTIES']['SEND_MESS']['VALUE_XML_ID'] == 'Y'): ?>
												<div class="staff-list__item-button--on-image">
													<div class=" btn btn-default <?= $arParams['BUTTON_SIZE'] ? $arParams['BUTTON_SIZE'] : '' ?> animate-load"
														 data-event="jqm"
														 data-name="staff"
														 data-autoload-staff="<?= CAllcorp3::formatJsName($arItem['NAME']) ?>"
														 data-autoload-staff_email_hidden="<?= CAllcorp3::formatJsName($arItem['DISPLAY_PROPERTIES']['EMAIL']['VALUE']) ?>"
														 data-param-id="<?= CAllcorp3::getFormID("aspro_allcorp3_callstaff"); ?>"
													>
														<?= GetMessage('SEND_MESSAGE') ?>
													</div>
												</div>
											<? endif; ?>
										<? endif; ?>
									</div>
								<? endif; ?>

								<? if ($arParams['TYPE_VIEW'] == 'VIEW1' || $arParams['TYPE_VIEW'] == 'DETAIL' || $arParams['TYPE_VIEW'] == 'VIEW4'): ?>
									<div class="staff-list__item-additional-text-wrapper <?= 'staff-list__item-additional-text-wrapper--' . $arParams['ADDITIONAL_TEXT_POSITION'] ?>">
										<? if ($arItem['DISPLAY_PROPERTIES']['POST']['VALUE']): ?>
											<div class="staff-list__item-company font_13 <?= $arParams['ADDITIONAL_TEXT_COLOR'] == 'DARK' ? 'color_333 opacity_5' : 'color_light--opacity' ?>">
												<?
												$arItem['PROPERTIES']['POST']['VALUE'] = mb_strtoupper(mb_substr($arItem['PROPERTIES']['POST']['VALUE'], 0, 1)) . mb_substr($arItem['PROPERTIES']['POST']['VALUE'], 1);
												echo($arItem['PROPERTIES']['POST']['VALUE']);
												?>
											</div>
										<? endif; ?>
										<div class="staff-list__item-title switcher-title font_<?= $arParams['NAME_SIZE'] ?>  <?= $arParams['ADDITIONAL_TEXT_COLOR'] == 'DARK' ? 'color_333' : 'color_light' ?>">
											<?= $arItem['NAME']; ?>
										</div>
									</div>
								<? endif; ?>

								<div class="staff-list__item-text-wrapper">
									<div class="staff-list__item-text-top-part">
										<? if ($arItem['DISPLAY_PROPERTIES']['POST']['VALUE']): ?>
											<div class="staff-list__item-company font_13 color_333 opacity_5">
												<?
												$arItem['PROPERTIES']['POST']['VALUE'] = mb_strtoupper(mb_substr($arItem['PROPERTIES']['POST']['VALUE'], 0, 1)) . mb_substr($arItem['PROPERTIES']['POST']['VALUE'], 1);
												echo($arItem['PROPERTIES']['POST']['VALUE']);
												?>
											</div>
										<? endif; ?>
										<a class="dark_link" href="<?= $arItem['DETAIL_PAGE_URL'] ?>">
											<div class="staff-list__item-title switcher-title font_<?= $arParams['NAME_SIZE'] ?>">
												<?= $arItem['NAME']; ?>
											</div>
										</a>

										<? if (strlen($arItem['FIELDS']['PREVIEW_TEXT'])): ?>
											<div class="staff-list__item-preview-wrapper">
												<div class="staff-list__item-preview font_<?= $arParams['PREVIEW_SIZE'] ? $arParams['PREVIEW_SIZE'] : '13' ?> color_666">
													<?= $arItem['FIELDS']['PREVIEW_TEXT']; ?>
												</div>
											</div>
										<? endif; ?>

										<?
										$phone = $arItem['DISPLAY_PROPERTIES']['PHONE']['VALUE'];
										$phoneFormatted = $phone ? preg_replace('/[^\d]/', '', $phone) : '';

										$email = $arItem['DISPLAY_PROPERTIES']['EMAIL']['VALUE'];

										if ($phone || $email || $arItem['SOCIAL_INFO']):?>
											<div class="staff-list__info-wrapper">
												<? if ($phone || $email): ?>
													<div class="staff-list__item-props">
														<? if ($phone): ?>
															<div class="staff-list__item-prop">
																<div class="staff-list__item-prop-title font_13 color_999">
																	<?= GetMessage('PHONE') ?>
																</div>
																<a rel="nofollow" href="tel:+<?= $phoneFormatted ?>"
																   class="staff-list__item-phone font_14 dark_link">
																	<?= $phone ?>
																</a>
															</div>
														<? endif; ?>

														<? if ($email): ?>
															<div class="staff-list__item-prop">
																<div class="staff-list__item-prop-title font_13 color_999">
																	<?= GetMessage('EMAIL') ?>
																</div>
																<a rel="nofollow" href="mailto:<?= $email ?>"
																   class="staff-list__item-email font_14 dark_link">
																	<?= $email ?>
																</a>
															</div>
														<? endif; ?>
													</div>
												<? endif; ?>

												<? if ($arItem['SOCIAL_INFO']): ?>
													<div class="staff-list__item-socials">
														<? foreach ($arItem['SOCIAL_INFO'] as $arSoc): ?>
															<a class="staff-list__item-social fill-theme-hover"
															   rel="nofollow" href="<?= $arSoc['VALUE'] ?>">
																<?= CAllcorp3::showIconSvg('', $arSoc['PATH']); ?>
															</a>
														<? endforeach; ?>
													</div>
												<? endif; ?>
											</div>
										<? endif; ?>
									</div>

									<div class="staff-list__item-text-bottom-part">
										<? if ($arItem['PROPERTIES']['SEND_MESS']['VALUE_XML_ID'] == 'Y'): ?>
											<div class="staff-list__item-button">
												<div class="btn btn-default <?= $bottomClass ?> <?= $arParams['BUTTON_SIZE'] ? $arParams['BUTTON_SIZE'] : '' ?> animate-load"
													 data-event="jqm"
													 data-name="staff"
													 data-autoload-staff="<?= CAllcorp3::formatJsName($arItem['NAME']) ?>"
													 data-autoload-staff_email_hidden="<?= CAllcorp3::formatJsName($arItem['DISPLAY_PROPERTIES']['EMAIL']['VALUE']) ?>"
													 data-param-id="<?= CAllcorp3::getFormID("aspro_allcorp3_callstaff"); ?>"
												>
													<?= GetMessage('SEND_MESSAGE') ?>
												</div>
											</div>
										<? endif; ?>
									</div>
								</div>
							</div>
							<?
							$counter++;
						endforeach; ?>
					</div>

				<? if ($arParams['NARROW'] && $arParams['SHOW_NEXT']): ?>
					<div class="maxwidth-theme" data-carousel-without-hiding-role="maxwidth"></div>
					<div id="carousel-navigation_staff"
						 class="owl-navigation-outer owl-navigation-outer--hidden-sm" data-carousel-without-hiding-role="navigation"></div>
					<div id="carousel-dots_staff" class="owl-navigation-outer-dots <?= $arParams['TYPE_VIEW'] != 'VIEW1' ? 'owl-navigation-outer-dots--position1' : '' ?>"></div>
				</div>
				<? endif ?>
			</div>

			<? if ($arParams['RESPONSIVE_DOTS']) {
				$dotsClasses = '';
				$dotsClasses .= ' owl-carousel__dots--line';
				$dotsClasses .= ' owl-carousel__dots--bottom-minus-16';
				$dotsClasses .= ' owl-carousel__dots--line-small';
				\Aspro\Functions\CAsproAllcorp3::getDotsHTML(count($arResult['ITEMS']), $dotsClasses);
			} ?>

			<? if ($arParams['NARROW'] && !$arParams['SHOW_NEXT']): ?>
		</div>
	<? endif; ?>

	</div>
<? endif; ?>