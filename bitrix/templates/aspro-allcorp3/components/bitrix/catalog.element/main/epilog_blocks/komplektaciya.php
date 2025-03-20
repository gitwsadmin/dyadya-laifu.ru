<?
use \Bitrix\Main\Localization\Loc;

$bTab = isset($tabCode) && $tabCode === 'komplektaciya';
?>
<?//show komplektaciya block?>
<?if($arParams["T_KOMPLEKTACIYA"]):?>
    <?if($bTab):?>
        <?if(!isset($bShow_komplektaciya)):?>
            <?$bShow_komplektaciya = true;?>
        <?else:?>
            <div class="tab-pane <?=(!($iTab++) ? 'active' : '')?>" id="komplektaciya">
                <?$APPLICATION->ShowViewContent('komplektaciya')?>
            </div>
        <?endif;?>
    <?else:?>
        <div class="detail-block ordered-block komplektaciya">
            <div class="ordered-block__title switcher-title font_22"><?=$arParams["T_KOMPLEKTACIYA"]?></div>
            <?$APPLICATION->ShowViewContent('komplektaciya')?>
        </div>
    <?endif;?>
<?endif;?>
