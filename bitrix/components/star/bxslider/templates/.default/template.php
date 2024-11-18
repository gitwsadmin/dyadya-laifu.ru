<? 
	if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
	if ($arResult["JQUERY"] == "Y") CJSCore::Init(array('jquery'));
?>

<div class="star_bxslider_outer">
	<div id="star_bxslider" data-id="0" class="star_bxslider">
		<?foreach ($arResult["ITEMS"] as $item):?>
			<div class="star_bxslider_item">
				<?if($arResult["USE_FANCYBOX"] == "true"):?>
					<a class="star_bxslider_fancybox" href="<?=$item["PICTURE"]?>">
						<img src="<?=$item["PICTURE"]?>" title="<?=$item["NAME"]?>"/>
					</a>
				<?else:?>
					<?if (isset($item["LINK"]) && !empty($item["LINK"])):?><a href="<?=$item["LINK"]?>" <?=$arResult["NEW_WINDOW"]?>><?endif;?>
						<img src="<?=$item["PICTURE"]?>" title="<?=$item["NAME"]?>"/>
					<?if (isset($item["LINK"]) && !empty($item["LINK"])):?></a><?endif;?>
				<?endif;?>
			</div>
		<?endforeach;?>
	</div>
</div>

<script>
	sbs_id = 0;
	while(document.querySelector(".star_bxslider[data-id='"+sbs_id+"']")) sbs_id++;

	document.querySelector(".star_bxslider[data-id='0']").dataset.id = sbs_id;
	document.querySelector(".star_bxslider[data-id='"+sbs_id+"']").id = "star_bxslider_"+sbs_id;

	<?if($arResult["MIDDLE_HEIGHT"] == "true"):?>
		$("#star_bxslider_"+sbs_id).css("display", "flex");
		$("#star_bxslider_"+sbs_id).css("align-items", "center");
	<?endif;?>

	$("#star_bxslider_"+sbs_id).bxSlider({
		wrapperClass: 'star_bxslider_wrapper',
		infiniteLoop: <?=$arResult["INFINITE_LOOP"]?>,
		mode: '<?=$arResult["MODE"]?>',
		speed: <?=$arResult["SPEED"]?>,
		captions: <?=$arResult["CAPTIONS"]?>,
		adaptiveHeight: <?=$arResult["ADAPTIVE_HEIGHT"]?>,
		pager: <?=$arResult["PAGER"]?>,
		controls: <?=$arResult["CONTROLS"]?>,
		nextText: '',
		prevText: '',
		touchEnabled: false,
		auto: <?=$arResult["AUTO"]?>,
		pause: <?=$arResult["PAUSE"]*1000?>
		<?if ($arResult["BANNER_TYPE"] == "carousel"):?>,
			slideMargin: <?=$arResult["MARGIN"]?>,
			slideWidth: <?=$arResult["SLIDE_WIDTH"]?>,
			minSlides: <?=$arResult["COUNT_SLIDES"]?>,
			maxSlides: <?=$arResult["COUNT_SLIDES"]?>,
			moveSlides: <?=$arResult["COUNT_SLIDES"]?>
		<?endif;?>
	});

	<?if($arResult["USE_FANCYBOX"] == "true"):?>
		$("#star_bxslider_"+sbs_id + " .star_bxslider_fancybox").attr('rel', 'group' + sbs_id);
		$("#star_bxslider_"+sbs_id + " .star_bxslider_fancybox").fancybox({
			openEffect	: 'elastic',
    		closeEffect	: 'elastic'
		});
	<?endif;?>
</script>
