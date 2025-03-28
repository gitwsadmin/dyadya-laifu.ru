<?php

$contentArray = [
    [
        'id'          => '01',
        'title'       => 'Бесплатное страхование груза',
//        'image'       => getMediaItemByName(17, "01-Бесплатное страхование груза.jpg")['PATH'],
        'alt'         => 'Бесплатное страхование груза',
        'description' => "
            <p>
                Мы заботимся о безопасности ваших поставок, поэтому предоставляем бесплатное страхование грузов на весь маршрут перевозки. 
                Это означает, что ваш товар защищён от возможных рисков, включая повреждение, потерю или кражу.
            </p>
        ",
    ],
    [
        'id'          => '02',
        'title'       => 'Собственные склады в Китае',
//        'image'       => getMediaItemByName(17, "02-Собственные склады.jpg")['PATH'],
        'alt'         => 'Собственные склады в Китае',
        'description' => "
            <p>
                Мы обеспечиваем надёжное хранение и обработку ваших грузов на собственных складах в Китае и России. 
                Это позволяет оптимизировать логистику, снизить расходы и ускорить доставку.
            </p>
        ",
    ],
    [
        'id'          => '03',
        'title'       => 'Отслеживание грузов',
//        'image'       => getMediaItemByName(17, "03-Соблюдение сроков доставки.jpg")['PATH'],
        'alt'         => 'Отслеживание грузов',
        'description' => "
            <p>
                Мы обеспечиваем полную прозрачность доставки благодаря системе отслеживания грузов в реальном времени. 
                Вы всегда знаете, где находится ваш товар и на каком этапе перевозки он находится.
            </p>
        ",
    ],
    [
        'id'          => '04',
        'title'       => 'Оперативный расчёт доставки',
//        'image'       => getMediaItemByName(17, "04-Быстрыи просчет стоимости доставки.jpg")['PATH'],
        'alt'         => 'Оперативный расчёт доставки',
        'description' => "
            <p>
                Мы понимаем, как важно заранее знать стоимость и сроки перевозки, поэтому предлагаем оперативный расчёт доставки для вашего груза. 
                Оставьте заявку, и мы быстро рассчитаем доставку с учётом всех ваших потребностей.
            </p>
        ",
    ],
];

$contentBlocks = '';

foreach ($contentArray as $contentItem) {
    $contentBlocks .= <<<EOF
        <div class="ws-index-why__item">
            <div class="ws-index-why__title">
                {$contentItem['title']}
            </div>
            <div class="ws-index-why__content">
                <div class="ws-index-why__image">
                    <img id="ws-index-why__image-{$contentItem['id']}" src="" alt="{$contentItem['alt']}">
                </div>

                <div class="ws-index-why__description">
                    {$contentItem['description']}
                </div>
            </div>
        </div>
    EOF;
}

?>

<div class="mainpage-blocks ws-index-why-wrapper">
    <div class="maxwidth-theme">

        <div class="text-center">
            <h3 class="h3-heading">Наши преимущества</h3>
        </div>

        <div class="ws-index-why">
            <?=$contentBlocks?>
        </div>

    </div>
</div>


<script>
    document.addEventListener("DOMContentLoaded", function () {
        let imgElement01 = document.getElementById('ws-index-why__image-01');
        let imgElement02 = document.getElementById('ws-index-why__image-02');
        let imgElement03 = document.getElementById('ws-index-why__image-03');
        let imgElement04 = document.getElementById('ws-index-why__image-04');

        if (window.innerWidth <= 767) {
            imgElement01.src = "<?=getMediaItemByName(collectionId: 18, itemName: '01-Бесплатное страхование груза.jpg')['PATH']?>";
            imgElement02.src = "<?=getMediaItemByName(collectionId: 18, itemName: '02-Собственные склады.jpg')['PATH']?>";
            imgElement03.src = "<?=getMediaItemByName(collectionId: 18, itemName: '03-Соблюдение сроков доставки.jpg')['PATH']?>";
            imgElement04.src = "<?=getMediaItemByName(collectionId: 18, itemName: '04-Быстрыи просчет стоимости доставки.jpg')['PATH']?>";
        } else {
            imgElement01.src = "<?=getMediaItemByName(collectionId: 19, itemName: '01-Бесплатное страхование груза.jpg')['PATH']?>";
            imgElement02.src = "<?=getMediaItemByName(collectionId: 19, itemName: '02-Собственные склады.jpg')['PATH']?>";
            imgElement03.src = "<?=getMediaItemByName(collectionId: 19, itemName: '03-Соблюдение сроков доставки.jpg')['PATH']?>";
            imgElement04.src = "<?=getMediaItemByName(collectionId: 19, itemName: '04-Быстрыи просчет стоимости доставки.jpg')['PATH']?>";
        }
    });
</script>
