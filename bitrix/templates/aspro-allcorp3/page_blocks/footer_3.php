<?php

include_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

global $APPLICATION;
$currentPage = $APPLICATION->GetCurPage();

?>

<footer id="footer" class="ws-footer">
    <div class="ws-footer__row maxwidth-theme">

        <div class="ws-footer__left">
            <div class="ws-footer__left-head">
                <span class="fw-black">«Дядя Лайфу»</span> &ndash; это ваш надежный партнер!
            </div>

            <div class="ws-footer__left-menu">
                <ol>
                    <?php if ($currentPage !== "/") : ?>
                        <li>
                            <a href="/">Главная</a>
                        </li>
                    <?php endif; ?>
                    <li>
                        <b><a href="/catalog/">Каталог</a></b>
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
                        <a href="/company/">О компании</a>
                    </li>
                    <li>
                        <a href="/contacts/">Контакты</a>
                    </li>
                </ol>
            </div>

        </div>

        <div class="ws-footer__sticker">
            <img src="<?=getMediaItemByName(collectionId: 5, itemName: "04-хорошая работа.png")['PATH']?>"
                 alt="Дядя Лайфу - ваш надежный партнер">
        </div>

        <div class="ws-footer__right">

            <div class="ws-footer__right-phone-wrapper">
                <div class="ws-footer__right-phone-svg svg-phone"></div>
                <div class="ws-footer__right-phone-block">
                    <div class="ws-footer__right-phone-block-russia">
                        <a href="tel:+74951470159">+7 495 147-01-59</a>
                    </div>
                    <div class="ws-footer__right-phone-block-country">
                        Россия
                    </div>
                    <div class="ws-footer__right-phone-block-china">
                        <a href="tel:+8615024563093">+86 15 02456-30-93</a>
                    </div>
                    <div class="ws-footer__right-phone-block-country">
                        Китай
                    </div>
                </div>
            </div>

            <div class="ws-footer__right-email-wrapper">
                <div class="ws-footer__right-email-svg svg-email"></div>
                <div class="ws-footer__right-email-block">
                    <a href="&#109;&#97;&#105;&#108;&#116;&#111;&#58;&#105;&#110;&#102;&#111;&#64;&#100;&#121;&#97;&#100;&#121;&#97;&#45;&#108;&#97;&#105;&#102;&#117;&#46;&#114;&#117;">&#105;&#110;&#102;&#111;&#64;&#100;&#121;&#97;&#100;&#121;&#97;&#45;&#108;&#97;&#105;&#102;&#117;&#46;&#114;&#117;</a>
                </div>
            </div>

            <div class="ws-footer__right-address-wrapper">
                <div class="ws-footer__right-address-svg svg-address"></div>
                <div class="ws-footer__right-address-block">
                    义乌市大塘下一区城北路A48号店面国际物流 (来福叔叔)
                    Storefront International Logistics (Uncle Laifu), No. A48,
                    Chengbei Road, Next District, Datang, Yiwu City
                </div>
            </div>

        </div>
    </div>

    <div class="ws-footer__row ws-footer__addon maxwidth-theme">
        <div class="ws-footer__addon-year ">
            © 2012-<?=date('Y')?>
            Карго доставка от Дяди Лайфу
        </div>
        <div class="ws-footer__addon-rating">
            <iframe src="https://yandex.ru/sprav/widget/rating-badge/150457494796?type=rating"
                    width="150" height="50" frameborder="0"></iframe>
        </div>
        <div class="ws-footer__addon-policy">
            <a href="/include/licenses_detail.php">Политика конфиденциальности</a>
        </div>
    </div>
</footer>