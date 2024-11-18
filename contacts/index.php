<?php

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");

$APPLICATION->SetTitle("Наши контакты");

$APPLICATION->SetPageProperty(
    "title",
    "Контакты торговой компании Дядя Лайфу в России и в Китае"
);

$APPLICATION->SetPageProperty(
    "description",
    "Контакты офиса и склада транспортной компании Дядя Лайфу вы можете найти на данной странице. Склады находятся в Китае в городе Иу и городе Гуанчжоу."
);

CModule::IncludeModule("main");

// Фильтр для получения активных элементов инфоблока с ID 50
$filter = [
    "IBLOCK_ID" => 50,
    "ACTIVE"    => "Y",
];

// Получение элементов инфоблока, отсортированных по убыванию
$elementsResult = CIBlockElement::GetList(['SORT' => 'DESC'], $filter);

// Счетчик для элементов
$elementCount = 0;

// Обработка каждого элемента
while ($elementObject = $elementsResult->GetNextElement()) {
    // Получение полей элемента
    $elementFields = $elementObject->GetFields();

    // Получение свойств элемента
    $elementProperties = $elementObject->GetProperties();

    $name = $elementFields['NAME'];
    $imgSrc = CFile::GetFileArray($elementFields['DETAIL_PICTURE'])['SRC'];
    $imgAlt = "Компания Дядя Лайфу - $name";

    ?>

    <div class="contacts-detail mb-90" itemscope itemtype="http://schema.org/Organization">

        <h2 itemprop="name"><?=$name?></h2>

        <div class="contact-property contact-property--image">
            <img id="contact_image_<?=$elementCount?>" src="" alt="<?=$imgAlt?>" title="<?=$imgAlt?>">
        </div>

        <div class="contacts-detail__properties font-18">

            <div class="contacts__col">
                <div class="bx-map-view-layout bx-yandex-view-layout">
                    <div class="bx-yandex-view-map">
                        <div class="bx-yandex-map" id="BX_YMAP_MAP_<?=$elementCount?>"
                             data-coords="<?=$elementProperties['MAP']['VALUE']?>"
                             style="height: 300px; width: 100%">загрузка карты...
                        </div>
                    </div>
                </div>
            </div>

            <div class="contacts__col contacts-detail__property-wrapper">

                <div class="contacts-detail__property">
                    <div class="contact-property contact-property--address">
                        <div class="contact-property__label font_13 color_999">Адрес</div>
                        <div itemprop="address" class="contact-property__value color_333">
                            <?=$elementFields['PREVIEW_TEXT']?>
                        </div>
                    </div>
                </div>

                <div class="contacts-detail__property">
                    <div class="contact-property contact-property--schedule">
                        <div class="contact-property__label font_13 color_999">Режим работы</div>
                        <div class="contact-property__value color_333">
                            <?=$elementProperties['TIME']['VALUE'][0]?>
                        </div>
                    </div>
                </div>

                <div class="contacts-detail__property">
                    <div class="contact-property__label font_13 color_999">Телефон</div>
                    <div class="contact-property__value dark_link" itemprop="telephone">
                        <a href="tel:<?=$elementProperties['PHONE']['VALUE'][0]?>">
                            <?=$elementProperties['PHONE']['VALUE'][0]?>
                        </a>
                    </div>
                </div>

                <div class="contacts-detail__btn-wrapper">
                    <span class="btn btn-default bg-theme-target border-theme-target animate-load"
                          data-event="jqm" data-param-id="aspro_allcorp3_question" data-name="question">
                        Свяжитесь с нами
                    </span>
                </div>

            </div>

        </div>

    </div>

    <?php

    // Инкремент счетчика элементов
    $elementCount++;
}
?>

    <script>

        document.addEventListener("DOMContentLoaded", function () {
            let imgElement01 = document.getElementById('contact_image_0');
            let imgElement02 = document.getElementById('contact_image_1');

            if (window.innerWidth <= 767) {
                imgElement01.src = "<?=getMediaItemByName(collectionId: 46, itemName: 'москва_736х506.jpg')['PATH']?>";
                imgElement02.src = "<?=getMediaItemByName(collectionId: 46, itemName: 'иу_736х506.jpg')['PATH']?>";
            } else {
                imgElement01.src = "<?=getMediaItemByName(collectionId: 46, itemName: 'москва_1920х580.jpg')['PATH']?>";
                imgElement02.src = "<?=getMediaItemByName(collectionId: 46, itemName: 'иу_1920х580.jpg')['PATH']?>";
            }
        });

        let script = document.createElement('script');

        script.src = 'https://api-maps.yandex.ru/2.0/?load=package.full&mode=release&lang=ru-RU&wizard=bitrix';

        (document.head || document.documentElement).appendChild(script);

        script.onload = function () {
            this.parentNode.removeChild(script);
        };

        function init_MAP_mF8Ev4() {
            $('.bx-yandex-map').each(function () {
                $(this).html('');

                let coords = $(this).data('coords').split(','),
                    map = new ymaps.Map(this, {
                        center: coords,
                        zoom: 10,
                        type: 'yandex#map'
                    });

                map.controls.add('zoomControl');
                map.controls.add('smallZoomControl');
                map.controls.add('typeSelector');

                map.behaviors.enable("dblClickZoom");
                map.behaviors.enable("drag");

                if (map.behaviors.isEnabled("rightMouseButtonMagnifier")) {
                    map.behaviors.disable("rightMouseButtonMagnifier");
                }

                if (map.behaviors.isEnabled("scrollZoom")) {
                    map.behaviors.disable("scrollZoom");
                }

                map.geoObjects.add(new ymaps.Placemark(coords, {}, {
                    preset: 'islands#dotIcon',
                }));
            });
        }

        (function bx_ymaps_waiter() {
            if (typeof ymaps !== 'undefined' && typeof $ !== 'undefined') {
                ymaps.ready(init_MAP_mF8Ev4);
            } else {
                setTimeout(bx_ymaps_waiter, 100);
            }
        })();


    </script>

<?php

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php");