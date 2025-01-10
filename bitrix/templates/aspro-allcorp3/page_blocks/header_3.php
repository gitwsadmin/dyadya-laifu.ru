<?php

include_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

try {
    $currencies = getCurrency();
} catch (Exception $e) {
}

global $APPLICATION;
$currentPage = $APPLICATION->GetCurPage();

$headerTitleText = <<<EOF
    <span class="ws-header__left-title-name">«Дядя Лайфу»</span>
    <span class="ws-header__left-title-subname">
        &ndash; это торговая компания с самым большим списком поставщиков
        <strong>производственного оборудования!</strong>
    </span>
EOF;

$headerTitleBlock = $currentPage === "/"
    ? "<h1 class='ws-header__left-title-h1'>$headerTitleText</h1>"
    : "<div class='ws-header__left-title-h1'>$headerTitleText</div>";
?>

<header id="header" class="ws-header">
    <div class="ws-header__block maxwidth-theme">

        <div class="ws-header__left">
            <div class="ws-header__left-sticker">
                <a href="/">
                    <img src="<?=getMediaItemByName(collectionId: 8, itemName: "01-дядя лаифу.png")['PATH']?>"
                         alt="Дядя Лайфу - карго доставка из Китая по всему СНГ">
                </a>
            </div>
            <div class="ws-header__left-title">
                <?=$headerTitleBlock?>
            </div>
        </div>

        <div class="ws-header__center">
            <div class="ws-header__center-top">

                <div class="ws-header__center-top-phone">
                    <div class="ws-header__center-top-phone-svg svg-phone"></div>
                    <div class="ws-header__center-top-phone-text">
                        <a href="tel:+74951470159">+7 495 147-01-59</a>
                    </div>
                </div>

                <div class="ws-header__center-top-currency">
                    <div class="ws-header__center-top-currency-image">
                        <img src="<?=getMediaItemByName(collectionId: 8, itemName: "02-cn.png")['PATH']?>"
                             alt="курс юаня" height="30">
                    </div>
                    <div class="ws-header__center-top-currency-text">
                        CNY <?=$currencies['CNY']?> RUB
                    </div>
                </div>

                <div class="ws-header__center-top-currency">
                    <div class="ws-header__center-top-currency-image">
                        <img src="<?=getMediaItemByName(collectionId: 8, itemName: "03-us.png")['PATH']?>"
                             alt="курс доллара" height="30">
                    </div>
                    <div class="ws-header__center-top-currency-text">
                        USD <?=$currencies['USD']?> RUB
                    </div>
                </div>

            </div>

            <div class="ws-header__center-menu">
                <ol>
                    <?php if ($currentPage !== "/") : ?>
                        <li>
                            <a href="/">Главная</a>
                        </li>
                    <?php endif; ?>
                    <li>
                        <a href="/catalog/">Каталог</a>
                    </li>
                    <li>
                        <a href="/services/">Услуги</a>
                    </li>
                    <li>
                        <a href="/articles/">Статьи</a>
                    </li>
                    <li>
                        <a href="/landings/">Обзоры</a>
                    </li>
                    <li>
                        <a href="/reviews/">Отзывы</a>
                    </li>
                    <li>
                        <a href="/faq/">Вопросы и ответы</a>
                    </li>
                    <li>
                        <a href="/company/">О компании</a>
                    </li>
                    <li>
                        <a href="/partners/">Партнеры</a>
                    </li>
                    <li>
                        <a href="/contacts/">Контакты</a>
                    </li>
                </ol>
            </div>
        </div>

        <div class="ws-header__right">
            <div class="ws-header__right-top">
                <div class="ws-header__right-top-search">
                    <div class="header-search banner-light-icon-fill fill-theme-hover color-theme-hover menu-light-icon-fill light-opacity-hover">
                        <div class="svg-search"></div>
                    </div>
                </div>
                <div class="ws-header__right-top-button">
                    <button class="btn animate-load has-ripple" data-event="jqm" data-param-id="7">
                        Подать заявку
                    </button>
                </div>
            </div>
            <div class="ws-header__right-bottom">
                <iframe src="https://yandex.ru/sprav/widget/rating-badge/150457494796?type=rating"
                        width="150" height="50" frameborder="0"></iframe>
            </div>
        </div>
    </div>

</header>