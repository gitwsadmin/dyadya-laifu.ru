<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();?>
<?$this->setFrameMode(true);?>
<?if($arResult['ITEMS']['HEADER_WIDE_MENU']['ITEMS']):?>
	<?$slideshowSpeed = 0;?>
	<div class="side_banners">
		<div class="owl-carousel-hover loading-state owl-carousel--light owl-carousel--buttons-transparent owl-carousel--buttons-size-30 owl-carousel--buttons-right-minus-11 owl-carousel--buttons-bottom-minus-48" data-plugin-options='{"nav": true, "loop": true, <?=($slideshowSpeed > 0 ? '"autoplay": true, "autoplayTimeout": '.$slideshowSpeed.',' : '')?> "items": 1}'>
			<?foreach($arResult['ITEMS']['HEADER_WIDE_MENU']['ITEMS'] as $arItem) {
				$picture = $arItem['PREVIEW_PICTURE']['SRC'] ? $arItem['PREVIEW_PICTURE']['SRC'] : $arItem['DETAIL_PICTURE']['SRC'];
				$date = $arItem['DISPLAY_ACTIVE_FROM'] ? $arItem['DISPLAY_ACTIVE_FROM'] : '';
				$date = strtolower($date);
				$link = $arItem['PROPERTIES']['LINKIMG']['VALUE'];
				?>
				<div class="side_banners__item">
					<?if($link):?>
						<a class="dark_link" href="<?=$link?>">
					<?endif;?>
						<div class="side_banners__item-img switcher-border-radius" style="background: url(<?=$picture?>) no-repeat center;"></div>
					<?if($link):?>
						</a>
					<?endif;?>
					<div class="side_banners__item-name font_15 switcher-title">
						<?if($link):?>
							<a class="dark_link" href="<?=$link?>">
						<?endif;?>
								<?=$arItem['NAME']?>
						<?if($link):?>
							</a>
						<?endif;?>
					</div>
					<?if($date):?>
						<div class="side_banners__item-date font_13"><?=$date?></div>
					<?endif;?>
				</div>
			<?}?>
		</div>
		<?
		$dotsClasses = ' owl-carousel__dots--small owl-carousel__dots--relative owl-carousel__dots--offset-top-30';
		echo \Aspro\Functions\CAsproAllcorp3::getDotsHTML(count($arResult['ITEMS']['HEADER_WIDE_MENU']['ITEMS']), $dotsClasses);?>
	</div>
<?endif;?>