<?
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true ) die();

//options from \Aspro\Functions\CAsproAllcorp3::showBlockHtml
$arOptions = $arConfig['PARAMS'];
?>
<?if(
    $arOptions &&
    is_array($arOptions)
):?>
    <?
    $type = $arOptions['TYPE'] ?: 'wide';
    $bTopContent = $type == 'TOP_CONTENT';
    $bTopOnHead = $type == 'TOP_ON_HEAD';
    ?>
    <div class="detail-image detail-image--<?=strtolower($type)?>">
        <?if (!$arOptions['TOP_IMG']):?>
            <a href="<?=$arOptions['URL']?>" class="fancybox" title="<?=$arOptions['TITLE'];?>">
        <?elseif ($bTopContent):?>
            <div class="maxwidth-theme">
        <?endif;?>

            <?if ($bTopOnHead):?>
                <div class="detail-image__fon" style="background: url(<?=$arOptions['URL']?>) center/cover no-repeat;"></div>
            <?else:?>
                <img src="<?=$arOptions['URL']?>" data-src="<?=$arOptions['URL']?>" class="img-responsive rounded-4" title="<?=$arOptions['TITLE'];?>" alt="<?=$arOptions['ALT'];?>" />
            <?endif;?>

        <?if (!$arOptions['TOP_IMG']):?>
            </a>
        <?elseif ($bTopContent):?>
            </div>
        <?endif;?>
    </div>
<?endif;?>