<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true ) die();?>

<?//options from \Aspro\Functions\CAsproAllcorp3::showBlockHtml?>
<?$arOptions = $arConfig['PARAMS'];?>

<?$bHasCurrentItem = (isset($arOptions['VALUE']) && $arOptions['VALUE']);?>
<div class="line-block__item sku-props__inner" <?=$arOptions['STYLE']?> data-id="<?=$arOptions['ID']?>">
    <div class="sku-props__item">
        <?if (isset($arOptions['NAME']) && $arOptions['NAME']):?>
            <div class="sku-props__title color_666 font_12">
                <?=$arOptions['NAME']?>: <span class="sku-props__js-size"><?=($bHasCurrentItem ? $arOptions['VALUE'] : "&mdash;");?></span>
            </div>
        <?endif;?>
        <div class="line-block line-block--flex-wrap sku-props__values ">
            <?foreach ($arOptions['VALUES'] as $key => $arItem):?>
                <div class="line-block__item" <?=$arItem['STYLE']?>>
                    <div class="sku-props__value rounded-4 font_13 bordered <?=($arItem['CLASS'] === 'active' ? 'sku-props__value--active' : '');?>" data-onevalue="<?=$arItem['ID'];?>" data-title="<?=$arItem['NAME']?>"><?=$arItem['NAME']?></div>
                </div>
            <?endforeach;?>
        </div>
    </div>
</div>
