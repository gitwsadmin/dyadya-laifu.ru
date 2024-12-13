<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();?>
<?$this->setFrameMode(true);?>
<?if($arResult['ITEMS']['HEADER_WIDE_MENU']['ITEMS']):?>
	<?$slideshowSpeed = 0;?>
	<div class="bottom-banners">
		<div class="owl-carousel-hover loading-state owl-carousel--light owl-carousel--buttons-gray owl-carousel--buttons-transparent owl-carousel--buttons-size-30 owl-carousel--buttons-right-minus-11 owl-carousel--buttons-bottom-minus-10" data-plugin-options='{"nav": true, "rewind" : true, "loop": true, <?=($slideshowSpeed > 0 ? '"autoplay": true, "autoplayTimeout": '.$slideshowSpeed.',' : '')?> "items": 1}'>
			<?foreach($arResult['ITEMS']['HEADER_WIDE_MENU']['ITEMS'] as $arItem) {
				$picture = $arItem['PREVIEW_PICTURE']['SRC'] ? $arItem['PREVIEW_PICTURE']['SRC'] : $arItem['DETAIL_PICTURE']['SRC'];
				$date = $arItem['DISPLAY_ACTIVE_FROM'] ? $arItem['DISPLAY_ACTIVE_FROM'] : '';
				$date = strtolower($date);
				$link = $arItem['PROPERTIES']['LINKIMG']['VALUE'];
				$topText = $arItem['PROPERTIES']['TOP_TEXT']['VALUE'];
				?>
				<div class="bottom-banners__item">
					<?if($picture):?>
						<?if($link):?>
							<a href="<?=$link?>">
						<?endif;?>
							<div class="bottom-banners__item-img switcher-border-radius" style="background: url(<?=$picture?>) no-repeat center;"></div>
						<?if($link):?>
							</a>
						<?endif;?>
					<?endif;?>
					<div class="bottom-banners__item-text">
						<?if($topText):?>
							<div class="bottom-banners__item-top-text font_13 color_999">
								<?=$topText;?>
							</div>
						<?endif;?>
						<div class="bottom-banners__item-name font_15 switcher-title">
							<?if($link):?>
								<a class="dark_link" href="<?=$link?>">
							<?endif;?>
									<?=$arItem['NAME']?>
							<?if($link):?>
								</a>
							<?endif;?>
						</div>
						<?if($date):?>
							<div class="bottom-banners__item-date font_13"><?=$date?></div>
						<?endif;?>
					</div>
				</div>
			<?}?>
		</div>
		<?
		$dotsClasses = ' owl-carousel__dots--small owl-carousel__dots--offset-top-30 owl-carousel__dots--right-40 owl-carousel__dots--top-10';
		echo \Aspro\Functions\CAsproAllcorp3::getDotsHTML(count($arResult['ITEMS']['HEADER_WIDE_MENU']['ITEMS']), $dotsClasses);?>
	</div>
<?endif;?>