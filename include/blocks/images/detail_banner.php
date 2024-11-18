<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

//options from \Aspro\Functions\CAsproAllcorp3::showBlockHtml
$arOptions = $arConfig['PARAMS'];

if (
    $arOptions &&
    is_array($arOptions)
):

    $sMoreText = \Bitrix\Main\Config\Option::get(
        \CAllcorp3::moduleID,
        'EXPRESSION_READ_MORE_OFFERS_DEFAULT',
        GetMessage('TO_ALL'),
        SITE_ID
    );

    $arOptions['COLOR'] = $arOptions['COLOR'] === 'dark' ?: 'light';
    $arOptions['TEXT'] = is_array($arOptions['TEXT']) ? $arOptions['TEXT'] : [];
    $arOptions['BUTTONS'] = is_array($arOptions['BUTTONS']) ? $arOptions['BUTTONS'] : [];
    $arOptions['ATTR'] = is_array($arOptions['ATTR']) ? $arOptions['ATTR'] : [];
    ?>
    <div class="banners-big banners-big--detail swipeignore banners-big--nothigh banners-big--normal banners-big--adaptive-1">
        <div class="maxwidth-banner ">
            <div class="banners-big__wrapper">
                <div class="banners-big__item-wrapper">
                    <div class="box banners-big__item banners-big__depend-height banners-big__depend-padding banners-big__item--<?=$arOptions['COLOR']?>"
                         style="background-image: url(<?=$arOptions['PICTURES']['BG']['SRC']?>); opacity: 1;"
                         data-color="<?=$arOptions['COLOR']?>">
                        <div class="maxwidth-theme pos-static">
                            <div class="banners-big__inner">
                                <div class="banners-big__text banners-big__text--wide1  banners-big__depend-height animated delay06 duration08 fadeInUp">
                                    <?php if ($arOptions['TEXT']['TOP']): ?>
                                        <div class="banners-big__top-text banners-big__top-text--small"><?=$arOptions['TEXT']['TOP']?></div>
                                    <?php endif; ?>

                                    <div class="banners-big__title banners-big__title--small">
                                        <h1 class="switcher-title" id="pagetitle">
                                            <?=htmlspecialcharsback($arOptions['TITLE'])?>
                                        </h1>
                                    </div>

                                    <?php if ($arOptions['TEXT']['PREVIEW']): ?>
                                        <div class="banners-big__text-wrapper ">
                                            <div class="banners-big__text-block banners-big__text-block--small banners-big__text-block--margin-top-more">
                                                <?php if ($arOptions['TEXT']['PREVIEW']['TYPE'] == 'text'): ?>
                                                    <p><?=$arOptions['TEXT']['PREVIEW']['VALUE']?></p>
                                                <?php else: ?>
                                                    <?=$arOptions['TEXT']['PREVIEW']['VALUE']?>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                    <?php if ($arOptions['BUTTONS']): ?>
                                        <div class="banners-big__buttons ">
                                            <?php foreach ($arOptions['BUTTONS'] as $arButton): ?>
                                                <?php
                                                if (!is_array($arButton)) {
                                                    continue;
                                                }

                                                $arButton['TITLE'] = strlen($arButton['TITLE'])
                                                    ? $arButton['TITLE']
                                                    : $sMoreText;

                                                $arButton['CLASS'] = $arButton['CLASS'] ?? 'btn btn-default';

                                                $arButton['ATTR'] = is_array($arButton['ATTR'])
                                                    ? $arButton['ATTR']
                                                    : [];
                                                ?>

                                                <div class="banners-big__buttons-item">
                                                    <?php if ($arButton['TYPE'] === 'link' && $arButton['LINK']): ?>
                                                        <a
                                                                href="<?=$arButton['LINK'];?>"
                                                                class="<?=$arButton['CLASS'];?>"
                                                            <?=!empty($arButton['ATTR']) ? implode(
                                                                ' ',
                                                                $arButton['ATTR']
                                                            ) : null;?>
                                                        ><?=$arButton['TITLE'];?></a>
                                                    <?php else: ?>
                                                        <?php
// 23.07.2024 [Roman Brovin]
// убрал <?!empty($arButton['ATTR']) ? implode(' ', $arButton['ATTR']) : null;
// и добавил data-event="jqm" data-param-id="7" data-name="question", чтобы открывалась форма
                                                        ?>
                                                        <span data-event="jqm" data-param-id="7" data-name="question"
                                                              class="<?=$arButton['CLASS']?>">
                                                            <?=$arButton['TITLE']?>
                                                        </span>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="banners-big__img-wrapper banners-big__depend-height banners-big__img-wrapper--back-right animated delay09 duration08 fadeInUp">
                                    <img class="plaxy banners-big__img" src="<?=$arOptions['PICTURES']['IMG']['SRC']?>"
                                         alt="<?=htmlspecialchars($arOptions['ATTR']['ALT'])?>"
                                         title="<?=htmlspecialchars($arOptions['ATTR']['TITLE'])?>">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>