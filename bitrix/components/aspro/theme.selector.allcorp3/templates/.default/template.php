<?
use Bitrix\Main\Localization\Loc,
	CAllcorp3 as Solution,
	Aspro\Allcorp3\Components\WizardSolutionLight\Template;

if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
Loc::loadMessages(__FILE__);

$this->setFrameMode(true);

$arResult['TEMPLATE'] = $this->{'__name'};
?>
<div id="theme-selector--<?=$arResult['RAND']?>" class="theme-selector" title="<?=Loc::getMessage('TS_T_'.$arResult['COLOR'])?>">
	<div class="theme-selector__inner">
		<div class="theme-selector__items menu-light-icon-fill banner-light-icon-fill fill-use-888 fill-theme-use-svg-hover">
			<div class="theme-selector__item theme-selector__item--light<?=($arResult['COLOR'] === 'light' ? ' current' : '')?>"
				<?=$arResult['COLOR'] !== 'light' ? 'style="display: none"' : '';?>
			>
				<div class="theme-selector__item-icon"><?=Solution::showSpriteIconSvg($this->{'__folder'}.'/images/svg/icons.svg#light-16-16', 'light-16-16', ['WIDTH' => 18, 'HEIGHT' => 18]);?></div>
			</div>
			<div class="theme-selector__item theme-selector__item--dark<?=($arResult['COLOR'] === 'dark' ? ' current' : '')?>"
				<?=$arResult['COLOR'] !== 'dark' ? 'style="display: none"' : '';?>
			>
				<div class="theme-selector__item-icon"><?=Solution::showSpriteIconSvg($this->{'__folder'}.'/images/svg/icons.svg#dark-14-14', 'dark-14-14', ['WIDTH' => 18, 'HEIGHT' => 18]);?></div>
			</div>
		</div>
	</div>
	<script>
	BX.message({
		TS_T_light: '<?=GetMessageJS('TS_T_light')?>',
		TS_T_dark: '<?=GetMessageJS('TS_T_dark')?>',
	});

	new JThemeSelector(
		'<?=$arResult['RAND']?>', 
		<?=CUtil::PhpToJSObject($arParams, false, true)?>, <?=CUtil::PhpToJSObject($arResult, false, true)?>
	);
	</script>
</div>