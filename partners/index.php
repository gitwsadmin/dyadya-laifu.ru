<?php

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");

$APPLICATION->SetPageProperty("MENU", "N");
$APPLICATION->SetTitle("Партнеры компании Дядя Лайфу");

$APPLICATION->SetPageProperty(
    "title",
    "Партнеры нашей компании"
);

$APPLICATION->SetPageProperty(
    "description",
    "Партнеры компании Дядя Лайфу"
);

use Bitrix\Main\Page\Asset;

Asset::getInstance()->addCss('/bitrix/templates/aspro-allcorp3/css/jquery.fancybox.min.css');
Asset::getInstance()->addJs('/bitrix/templates/aspro-allcorp3/js/jquery.fancybox.min.js');

?>

<div class="partners">

    <div class="partners__wrapper partners__wrapper-prolog">
        <div class="partners__description">

            <h2>Делегируй - Фулфилмент для маркетплейсов</h2>
            <p>Полный цикл обслуживания продавцов маркетплейсов: от упаковки до доставки до складов</p>

            <div class="mb-20">
                <a rel="nofollow" href="https://dele-ff.ru/">
                    https://dele-ff.ru
                </a>
            </div>

            <ul class="list-svg">
                <li>
                    <strong>Складские услуги:</strong> Приемка, хранение, комплектация.
                </li>
                <li>
                    <strong>Подготовка товаров:</strong> По стандартам маркетплейса. Упаковка, маркировка, комплектация, проверка на
                    брак.
                </li>
                <li>
                    <strong>Логистика:</strong> Забор грузов по всей Москве, доставка до складов маркетплейсов.
                </li>
                <li>
                    <strong>Дополнительные услуги:</strong> Ведение кабинетов, услуги Байера и забор ваших выкупов с ПВЗ
                </li>
                <li>
                    Получи 10% скидку, на первую отгрузку при чеке от 13.000, у наших партнеров! Назови по телефону
                    промокод "Дядя Лайфу".
                </li>
            </ul>
        </div>
        <div class="partners__image partners__image-prolog">
            <a href="<?=getMediaItemByName(collectionId: 22, itemName: "dele-ff.ru.png")['PATH']?>"
               data-fancybox="partners">
                <img src="<?=getMediaItemByName(collectionId: 22, itemName: "dele-ff.ru.png")['PATH']?>"
                     alt="Делегируй - партнер Дяди Лайфу" loading="lazy">
            </a>
        </div>

    </div>


</div>

<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php");
?>

