<?php

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");

$APPLICATION->SetPageProperty("MENU_SHOW_ELEMENTS", "Y");
$APPLICATION->SetPageProperty("MENU_SHOW_SECTIONS", "Y");
$APPLICATION->SetPageProperty("MENU", "N");

$APPLICATION->SetTitle("О компании Дядя Лайфу");

$APPLICATION->SetPageProperty(
    "title",
    "О компании Дядя Лайфу - комплексные услуги по организации грузовых перевозок всеми видами транспорта +7 495 147-01-59"
);

$APPLICATION->SetPageProperty(
    "description",
    "Компания «Дядя Лайфу» предлагает надежные и качественные услуги по организации грузовых перевозок всеми видами транспорта, включая авиационные, автомобильные и железнодорожные перевозки, экспресс-доставку, а также складские услуги в Китае. Мы гарантируем оптимальные маршруты, формирование сборных партий, упаковку и проверку товара, отслеживание груза. Наши преимущества: собственные склады и транспорт в Китае и России, личный менеджер в Китае, работа без предоплаты и онлайн-платежи."
);

use Bitrix\Main\Page\Asset;

Asset::getInstance()->addCss('/bitrix/templates/aspro-allcorp3/css/jquery.fancybox.min.css');
Asset::getInstance()->addJs('/bitrix/templates/aspro-allcorp3/js/jquery.fancybox.min.js');

?>

<div class="company">
    <p class="company__tagline">
        Стремясь обеспечить максимальное удобство и выгоду компания «Дядя Лайфу» гарантирует надежность и
        качество оказываемых услуг!
    </p>

    <div class="company__wrapper company__wrapper-prolog">
        <div class="company__description">
            <h2>О компании</h2>

            <p class="fw-bold">
                Компания «Дядя Лайфу» предлагает комплексные услуги по организации грузовых перевозок всеми видами
                транспорта.
            </p>

            <h3>Наши услуги включают:</h3>
            <ul class="list-svg">
                <li>
                    <strong>Грузовые перевозки:</strong> Авиационные, автомобильные и железнодорожные перевозки, а также
                    экспресс-доставка
                </li>
                <li>
                    <strong>Складские услуги:</strong> Хранение товара на наших складах в Китае
                </li>
                <li>
                    <strong>Оптимизация маршрутов:</strong> Подбор самых выгодных маршрутов для обеспечения разумных
                    тарифов и оптимальных сроков доставки
                </li>
            </ul>

            <h3>Наши преимущества:</h3>
            <ul class="list-svg">
                <li>
                    <strong>Собственные склады и транспорт</strong> на территории Китая и России
                </li>
                <li>
                    <strong>Личный менеджер:</strong> Удаленный помощник в Китае, говорящий на русском языке
                </li>
                <li>
                    <strong>Работа без предоплаты:</strong> Перечисление денег по договору только после проверки
                    полученного груза
                </li>
                <li>
                    <strong>Онлайн-платежи:</strong> Перечисление денег поставщикам товаров и грузов в режиме онлайн
                </li>
            </ul>

            <h3>Дополнительные услуги:</h3>
            <ul class="list-svg">
                <li>Формирование сборных партий грузов</li>
                <li>Упаковка товара</li>
                <li>Перевозка в контейнерах</li>
                <li>Проверка количества и качества товара</li>
                <li>Отслеживание груза в пути</li>
            </ul>
        </div>
        <div class="company__image company__image-prolog">
            <a href="<?=getMediaItemByName(collectionId: 20, itemName: "01.jpg")['PATH']?>"
               data-fancybox="company">
                <img src="<?=getMediaItemByName(collectionId: 20, itemName: "01.jpg")['PATH']?>"
                     alt="О компании Дядя Лайфу" title="О компании Дядя Лайфу" loading="lazy">
            </a>
        </div>

    </div>

    <h2 class="mt-40">Свидетельство на товарный знак</h2>

    <div class="company__wrapper company__wrapper-license">
        <?php
        $arrImages = getMediaItemsByCollectionId(21);

        foreach ($arrImages as $key => $value) {
            $key++;
            $imgAlt = "Свидетельство на товарный знак, фото $key";

            echo <<<EOF
                <div class="company__image company__image-license">
                    <a href="{$value['PATH']}" data-fancybox="license">
                        <img title="$imgAlt" alt="$imgAlt" src="{$value['PATH']}" loading="lazy">
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

