<?php

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetPageProperty("title", "Отзывы клиентов с Яндекс Карт о компании Дядя Лайфу Карго");
$APPLICATION->SetPageProperty("keywords", "отзывы дядя лайфу, отзывы карго, отзывы доставка из китая");
$APPLICATION->SetPageProperty("description", "Реальные отзывы и оценки клиентов с Яндекс Карт о компании Дядя Лайфу Карго");

$APPLICATION->SetTitle("Отзывы с Яндекс Карт");


use Bitrix\Main\Page\Asset;

Asset::getInstance()->addCss('/bitrix/templates/aspro-allcorp3/css/jquery.fancybox.min.css');
Asset::getInstance()->addJs('/bitrix/templates/aspro-allcorp3/js/jquery.fancybox.min.js');

?>


    <div class="reviews">
        <div class="reviews__description">
<p align="center"><a href="/reviews/"><u>Отзывы о нас в Telegram</u></a></p>
            Все отзывы написаны нашими клиентами на площадке Яндекс Карты. Мы ценим каждый ваш отзыв и оценку. Благодарим за выбор нашей компании.
        </div>

        <div class="reviews__image-wrapper">
            <?php
            $arrImages = getMediaItemsByCollectionId(105);

            foreach ($arrImages as $key => $value) {
                $key++;
                $imgAlt = "Отзывы о компании Дядя Лайфу Карго на Яндекс Картах, фото $key";
                $path = $value['PATH'];

                echo <<<EOF
                    <div class="reviews__image-block">
                        <a href="$path" data-fancybox="gallery">
                            <img title="$imgAlt" alt="$imgAlt" src="$path" loading="lazy">
                        </a>
                    </div>
                EOF;
            }
            ?>
        </div>

        <div class="reviews__feedback">
            <div>Если вы хотите оставить отзыв, то всегда можно это сделать на нашей странице Яндекс Карты</div>
            

            <div class="reviews__telegram-link">
                <a target="_blank" href="https://yandex.ru/maps/org/dyadya_layfu_kargo/150457494796/">Дядя Лайфу Карго</a>
            </div>

        </div>

    </div>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>