<?php

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");

$APPLICATION->SetTitle("Галерея фотографий");

$APPLICATION->SetPageProperty(
    "title",
    "Галерея фотографий компании Дядя Лайфу"
);

$APPLICATION->SetPageProperty(
    "description",
    "Все фотографии компании Дядя Лайфу"
);

use Bitrix\Main\Page\Asset;

Asset::getInstance()->addCss('/bitrix/templates/aspro-allcorp3/css/jquery.fancybox.min.css');
Asset::getInstance()->addJs('/bitrix/templates/aspro-allcorp3/js/jquery.fancybox.min.js');

?>

<div class="gallery">
    <div class="gallery__image-wrapper">
        <?php
        $arrImages = getMediaItemsByCollectionId(15);

        foreach ($arrImages as $key => $value) {
            $key++;
            $imgAlt = "Отгрузка товаров компании Дядя Лайфу, фото $key";
            $path = $value['PATH'];

            echo <<<EOF
                <div class="gallery__image-block">
                    <a href="$path" data-fancybox="gallery">
                        <img title="$imgAlt" alt="$imgAlt" src="$path" loading="lazy">
                    </a>
                </div>
            EOF;
        }
        ?>

        <?php
        $arrImages = getMediaItemsByCollectionId(13);

        foreach ($arrImages as $key => $value) {
            $key++;
            $imgAlt = "Сотрудники компании Дядя Лайфу, фото $key";
            $path = $value['PATH'];

            echo <<<EOF
                <div class="gallery__image-block">
                    <a href="$path" data-fancybox="gallery">
                        <img title="$imgAlt" alt="$imgAlt" src="$path" loading="lazy">
                    </a>
                </div>
            EOF;
        }
        ?>

        <?php
        $arrImages = getMediaItemsByCollectionId(14);

        foreach ($arrImages as $key => $value) {
            $key++;
            $imgAlt = "Склад компании Дядя Лайфу, фото $key";
            $path = $value['PATH'];

            echo <<<EOF
                <div class="gallery__image-block">
                    <a href="$path" data-fancybox="gallery">
                        <img title="$imgAlt" alt="$imgAlt" src="$path" loading="lazy">
                    </a>
                </div>
            EOF;
        }
        ?>
    </div>
</div>

<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php");
?>

