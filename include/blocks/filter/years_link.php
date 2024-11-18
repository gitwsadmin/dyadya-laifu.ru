<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true ) die();?>

<?//options from \Aspro\Functions\CAsproAllcorp3::showBlockHtml?>
<?$arOptions = $arConfig['PARAMS'];?>
<?Aspro\Allcorp3\Functions\Extensions::init('filter_block');?>

<div class="head-block-wrapper dropdown-select cursor_default overflow-block">
    <div class="bordered rounded-4 dropdown-select__list blocks_filter">
        <div class="line-block font_14 line-block--gap line-block--gap-40 ">
            <?if ($arOptions['HAS_YEAR']):?>
                <a class="line-block__item head-block__item dark_link" href="<?=$GLOBALS['APPLICATION']->GetCurPageParam('', array('year'));?>">
            <?else:?>
                <div class="line-block__item head-block__item head-block__item--active color-theme dark_link">
            <?endif;?>
                    <span class="head-block__item-title"><?=GetMessage($arOptions['ALL_ITEMS_LANG']);?></span>
            <?if ($arOptions['HAS_YEAR']):?>
                </a>
            <?else:?>
                </div>
            <?endif;?>
            <?foreach ($arOptions['ITEMS'] as $value):
                $bSelected = ($arOptions['HAS_YEAR'] && $value == $arOptions['YEAR']);?>
                <?if ($bSelected):?>
                    <div class="line-block__item head-block__item head-block__item--active color-theme dark_link">
                <?else:?>
                    <a 
                        class="line-block__item head-block__item dark_link"
                        href="<?=$GLOBALS['APPLICATION']->GetCurPageParam('year='.$value, array('year'));?>"
                    >
                <?endif;?>
                        <span class="head-block__item-title"><?=$value;?></span>
                <?if ($bSelected):?>
                    </div>
                <?else:?>
                     </a>
                <?endif;?>
            <?endforeach;?>
        </div>
    </div>
</div>