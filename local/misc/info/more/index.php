<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Возможности");
?>

<p>В решении поддерживается множество UI элементов, которые Вы с легкостью можете использовать для развития сайта и добавления нового функционала.</p>

<div class="row"> 						
	<div class="col-md-3 col-sm-6 col-xs-12"> 							
		<div class="more_wrapper">
			<a href="<?=SITE_DIR;?>info/more/typograpy/" class="bordered shadow-no-border-hovered"  data-toggle="tooltip" title="" data-original-title="Можно использовать Tooltip!">
				<?=CAllCorp3::showSpriteIconSvg(SITE_TEMPLATE_PATH.'/images/svg/more_info.svg#decoration', 'fill-theme svg-inline-more_icon');?>
				<div class="title color-theme-hover">
					Оформление
				</div>
			</a>
		</div>
	</div>
	<div class="col-md-3 col-sm-6 col-xs-12"> 
		<div class="more_wrapper">
			<a href="<?=SITE_DIR;?>info/more/buttons/" class="bordered shadow-no-border-hovered"  data-toggle="tooltip" title="" data-original-title="Можно использовать Tooltip!">
				<?=CAllCorp3::showSpriteIconSvg(SITE_TEMPLATE_PATH.'/images/svg/more_info.svg#buttons', 'fill-theme svg-inline-more_icon');?>
				<div class="title color-theme-hover">
					Кнопки
				</div>
			</a>
		</div>							
	</div>
	<div class="col-md-3 col-sm-6 col-xs-12"> 
		<div class="more_wrapper">
			<a href="<?=SITE_DIR;?>info/more/icons/" class="bordered shadow-no-border-hovered"  data-toggle="tooltip" title="" data-original-title="Можно использовать Tooltip!">
				<?=CAllCorp3::showSpriteIconSvg(SITE_TEMPLATE_PATH.'/images/svg/more_info.svg#icons', 'fill-theme svg-inline-more_icon');?>
				<div class="title color-theme-hover">
					Иконки
				</div>
			</a>
		</div>								
	</div>
	<div class="col-md-3 col-sm-6 col-xs-12"> 
		<div class="more_wrapper">
			<a href="<?=SITE_DIR;?>info/more/elements/" class="bordered shadow-no-border-hovered"  data-toggle="tooltip" title="" data-original-title="Можно использовать Tooltip!">
				<?=CAllCorp3::showSpriteIconSvg(SITE_TEMPLATE_PATH.'/images/svg/more_info.svg#elements', 'fill-theme svg-inline-more_icon');?>
				<div class="title color-theme-hover">
					Элементы
				</div>
			</a>
		</div>								
	</div>
</div>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>