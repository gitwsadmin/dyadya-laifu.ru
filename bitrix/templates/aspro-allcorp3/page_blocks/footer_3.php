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
                        <a href="/landings/">Обзоры</a>
                    </li>
                    <li>
                        <a href="/services/">Услуги</a>
                    </li>
                    <li>
                        <a href="/articles/">Статьи</a>
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
                    <p>Адрес офиса: Room 8222, Yunji Building,No.719,Danxi North Road,Yiwu City,Zhejiang Province,China</p>
                    <p>Адрес склада: No.566 Chunhua Road, Yiwu City, Zhejiang Province, China, Uncle Lai Fu International Logistics, Beloved Park</p>
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

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <button class="social-toggle btn"><i class="fas fa-comment-dots"></i></button>
    <div class="social-menu">
        <a href="https://wa.me/79152075496?text=Здравствуйте%2C+у+меня+есть+вопрос...&type=phone_number" target="_blank">
            <img src="<?=SITE_TEMPLATE_PATH?>/page_blocks/footer_soc_svg/whatsapp.svg" alt="WhatsApp">
        </a>
        <a href="https://t.me/+x4E2BFFQb09iYTI1" target="_blank">
            <img src="<?=SITE_TEMPLATE_PATH?>/page_blocks/footer_soc_svg/Telegram.svg" alt="Telegram">
        </a>
        <a href="https://vk.com/kargo_dyadyalaifu" target="_blank">
            <img src="<?=SITE_TEMPLATE_PATH?>/page_blocks/footer_soc_svg/VK.svg" alt="VK">
        </a>
        <a href="https://www.youtube.com/@cargo-china" target="_blank">
            <img src="<?=SITE_TEMPLATE_PATH?>/page_blocks/footer_soc_svg/YouTube.svg" alt="YouTube">
        </a>
        <a href="https://www.avito.ru/brands/ws_electro" target="_blank">
            <img src="<?=SITE_TEMPLATE_PATH?>/page_blocks/footer_soc_svg/Avito.png" alt="Avito">
        </a>
    </div>

</footer>