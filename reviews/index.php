<?php

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");

$APPLICATION->SetTitle("Отзывы клиентов");

$APPLICATION->SetPageProperty(
    "title",
    "Отзывы клиентов о компании Дядя Лайфу +7 495 147-01-59"
);

$APPLICATION->SetPageProperty(
    "description",
    "Здесь представлены все самые свежие отзывы клиентов о компании Дядя Лайфу"
);

use Bitrix\Main\Page\Asset;

Asset::getInstance()->addCss('/bitrix/templates/aspro-allcorp3/css/jquery.fancybox.min.css');
Asset::getInstance()->addJs('/bitrix/templates/aspro-allcorp3/js/jquery.fancybox.min.js');

?>

    <div class="reviews">
        <div class="reviews__description">
<p align="center"><a href="/reviews-ya/"><u>Отзывы о нас на Яндекс Картах</u></a></p>
            Мы ценим каждый отзыв и благодарны за возможность улучшать наш сервис благодаря вашей обратной связи.
            Все отзывы написаны самими клиентами в свободной форме, что позволяет передать подлинные эмоции и
            впечатления от сотрудничества с нами.
        </div>

        <div class="reviews__image-wrapper">
            <?php
            $arrImages = getMediaItemsByCollectionId(30);

            foreach ($arrImages as $key => $value) {
                $key++;
                $imgAlt = "Отзывы о компании Дядя Лайфу, фото $key";
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
            <div>Больше отзывов о жизни нашей компании вы можете найти в телеграм-канале</div>
            <div class="reviews__telegram-ico mt-10">
                <img src="<?=getMediaItemByName(collectionId: 2, itemName: "telegram.png")['PATH']?>"
                     alt="telegram reviews">
            </div>
            <div class="reviews__telegram-link">
                <a href="https://t.me/dyadyalaifu">@dyadyalaifu</a>
            </div>
        </div>

    </div>

<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php");
?>