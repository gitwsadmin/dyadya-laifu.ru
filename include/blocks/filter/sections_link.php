<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true ) die();?>

<?//options from \Aspro\Functions\CAsproAllcorp3::showBlockHtml?>
<?$arOptions = $arConfig['PARAMS'];?>
<?Aspro\Allcorp3\Functions\Extensions::init('filter_block');?>

<div class="head-block-wrapper dropdown-select overflow-block">
    <div class="bordered rounded-4 dropdown-select__list blocks_filter">
        <div class="line-block font_14 line-block--40 ">
            <?if ($arOptions['SECTION']):?>
                <a class="line-block__item head-block__item dark_link" href="<?=$arOptions['BASE_URL'];?>">
            <?else:?>
                <div class="line-block__item head-block__item head-block__item--active color-theme dark_link">
            <?endif;?>
                    <span class="head-block__item-title"><?=GetMessage($arOptions['ALL_ITEMS_LANG']);?></span>
            <?if ($arOptions['SECTION']):?>
                </a>
            <?else:?>
                </div>
            <?endif;?>
            <?foreach ($arOptions['ITEMS'] as $arItem):
                $bSelected = ($arOptions['SECTION'] && $arItem['ID'] == $arOptions['SECTION']['ID']);?>
                <?if ($bSelected):?>
                    <div class="line-block__item head-block__item head-block__item--active color-theme dark_link">
                <?else:?>
                    <a class="line-block__item head-block__item dark_link" href="<?=$arItem['SECTION_PAGE_URL'];?>"
                    >
                <?endif;?>
                        <span class="head-block__item-title"><?=$arItem['NAME'];?></span>
                <?if ($bSelected):?>
                    </div>
                <?else:?>
                    </a>
                <?endif;?>
            <?endforeach;?>
        </div>
    </div>
</div>