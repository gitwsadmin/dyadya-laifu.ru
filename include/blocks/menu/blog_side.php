<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true ) die();?>

<?//options from \Aspro\Functions\CAsproAllcorp3::showBlockHtml?>
<?$arOptions = $arConfig['PARAMS'];?>
<ul class="nav nav-list side-menu bordered rounded-4">
    <?foreach ($arOptions['SECTIONS'] as $arSection):?>
        <?if (isset($arSection['TEXT']) && $arSection['TEXT']):?>
            <li class="<?=($arSection['CURRENT'] ? 'active opened' : '')?><?=($arSection['CHILD'] ? ' child' : '')?>">
                <span class="bg-opacity-theme-parent-hover link-wrapper fill-theme-parent-all">
                    <a href="<?=$arSection['LINK'];?>" class="dark_link top-level-link link-with-flag<?=($arSection['CURRENT'] ? ' link--active' : '')?>">
                        <span class="side-menu__link-text"><?=$arSection['TEXT'];?></span>
                        <?if ($arSection['ELEMENT_COUNT']):?>
                            <span class="side-menu__link-count rounded-4 font_13"><?=$arSection['ELEMENT_COUNT'];?></span>
                        <?endif;?>
                        <?if ($arSection['CHILD']):?>
                            <?=CAllcorp3::showIconSvg("down menu-arrow bg-opacity-theme-target fill-theme-target", SITE_TEMPLATE_PATH.'/images/svg/Triangle_down.svg', '', '', true, false);?>
                        <?endif;?>
                    </a>
                    <?if ($arSection['CHILD']):?>
                        <span class="toggle_block"></span>
                    <?endif;?>
                </span>
                <?if ($arSection['CHILD']):?>
                    <div class="submenu-wrapper">
                        <ul class="submenu">
                            <?foreach ($arSection['CHILD'] as $arChild):?>
                                <li class="<?=($arChild['CURRENT'] ? 'active' : '')?>">
                                    <span class="bg-opacity-theme-parent-hover link-wrapper fill-theme-parent-all">
                                        <a href="<?=$arChild['LINK'];?>" class="dark_link font_14 sublink<?=($arChild['CURRENT'] ? ' link--active' : '')?>">
                                            <?=$arChild['TEXT'];?>
                                        </a>
                                    </span>
                                </li>
                            <?endforeach;?>
                        </ul>
                    </div>
                <?endif;?>
            </li>
        <?endif;?>
    <?endforeach;?>
</ul>
