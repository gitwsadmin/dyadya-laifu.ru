<?php

$contentArray = [
    [
        'id'          => '01',
        'title'       => 'Бесплатное страхование груза',
//        'image'       => getMediaItemByName(17, "01-Бесплатное страхование груза.jpg")['PATH'],
        'alt'         => 'Бесплатное страхование груза',
        'description' => "
            <p>
                Мы понимаем, насколько важно для вас быть уверенными в сохранности ваших товаров во время
                транспортировки. Именно поэтому наша компания предлагает
                <strong>бесплатное страхование груза</strong>.
            </p>
            <p>
                Это означает, что ваши товары защищены от любых непредвиденных обстоятельств, таких как
                повреждения или потеря, без дополнительных затрат для вас.
            </p>
            <p>
                Наши страховые партнеры обеспечивают быстрое и справедливое урегулирование любых страховых
                случаев, предоставляя вам полный покой и уверенность в безопасности ваших грузов.
            </p>
        ",
    ],
    [
        'id'          => '02',
        'title'       => 'Собственные склады',
//        'image'       => getMediaItemByName(17, "02-Собственные склады.jpg")['PATH'],
        'alt'         => 'Собственные склады',
        'description' => "
            <p>
                Мы владеем и управляем собственными складами <strong>на территории Китая и России</strong>. 
                Это позволяет нам контролировать каждый этап хранения и обработки ваших товаров.
            </p>
            <p>
                Наши склады оборудованы современной техникой и системами безопасности, что гарантирует
                надежное хранение и защиту ваших товаров от повреждений и краж.
            </p>
            <p>
                Наличие собственных складов также обеспечивает гибкость и оперативность в логистических
                операциях, что способствует <strong>своевременной и эффективной доставке</strong>.
            </p>
        ",
    ],
    [
        'id'          => '03',
        'title'       => 'Соблюдение сроков доставки',
//        'image'       => getMediaItemByName(17, "03-Соблюдение сроков доставки.jpg")['PATH'],
        'alt'         => 'Соблюдение сроков доставки',
        'description' => "
            <p>
                Соблюдение сроков доставки &ndash; это <strong>наш приоритет</strong>. Мы понимаем, что своевременная
                доставка критически важна для вашего бизнеса.
            </p>
            <p>
                Наша команда <strong>опытных логистов</strong> тщательно планирует маршруты и координирует все этапы
                транспортировки, чтобы обеспечить прибытие грузов в оговоренные сроки.
            </p>
            <p>
                Мы используем передовые технологии и системы отслеживания, чтобы минимизировать задержки и
                предоставлять вам актуальную информацию о статусе доставки в режиме реального времени.
            </p>
        ",
    ],
    [
        'id'          => '04',
        'title'       => 'Быстрый просчет доставки',
//        'image'       => getMediaItemByName(17, "04-Быстрыи просчет стоимости доставки.jpg")['PATH'],
        'alt'         => 'Просчет стоимости доставки',
        'description' => "
            <p>
                Мы знаем, что оперативность и точность в расчетах важны для принятия решений. Наша компания предлагает
                <strong>быстрый и удобный</strong> сервис для расчета стоимости доставки.
            </p>
            <p>
                Используя наш онлайн-калькулятор, вы можете получить точную оценку затрат на перевозку ваших товаров
                за считанные минуты.
            </p>
            <p>
                Это позволяет вам планировать бюджет и оптимизировать логистические расходы без лишних задержек.
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
            <h3 class="h3-heading">Почему мы</h3>
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
