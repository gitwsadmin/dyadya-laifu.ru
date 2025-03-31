<div class="mainpage-blocks ws-index-catalog-wrapper">
    <div class="maxwidth-theme">
        <div class="ws-index-catalog">
            <?php
            $catalogItems = [
                [
                    'name'        => 'Квадроциклы',
                    'image'       => 'include/mainpage/components/ws_catalog/quad.png',
                    'link' => '/landings/category/kvadrocikli/',
                    'description' => "",
                ],
                [
                    'name'        => 'Гидроциклы',
                    'image'       => 'include/mainpage/components/ws_catalog/hydro.png',
                    'link' => '/landings/category/gidrotsikly/',
                    'description' => "",
                ],
                [
                    'name'        => 'Питбайки',
                    'image'       => 'include/mainpage/components/ws_catalog/pit.png',
                    'link' => '/landings/category/pitbayki/',
                    'description' => "",
                ],
            ];

            foreach ($catalogItems as $catalogItem) {
                $name = $catalogItem['name'];
                $description = $catalogItem['description'];

                echo <<<EOF
                        <div class="ws-index-catalog__item_catalog">
                            <div class="ws-index-catalog__item-image">
                                <a href="{$catalogItem['link']}">
                                    <img src="{$catalogItem['image']}" alt="$name" title="$name">
                                </a>
                            </div>
                            <div class="ws-index-catalog__item-title_catalog" >
                               <a href="{$catalogItem['link']}">$name</a> 
                            </div>
                        </div>
                    EOF;
            }
            ?>
        </div>
    </div>
</div>
<?/*?>
    <div class="mainpage-blocks ws-index-catalog-wrapper">
    <div class="maxwidth-theme">

        <div class="text-center">
            <h3 class="h3-heading">Обзоры</h3>
        </div>

        <div>
            <div class="ws-index-catalog__tagline">
                <a rel="nofollow" href="https://t.me/dyadyalaifu/2">
                    В Китае производят большое количество квадроциклов, один из ведущих брендов - <strong>CFMOTO</strong>
                </a>
            </div>

            <div class="ws-index-catalog">
                <?php
                $catalogItems = [
                    [
                        'name'        => 'Квадроцикл CFORCE 450L EPS',
//                        'image'       => getMediaItemByName(23, "01.jpg")['PATH'],
                        'image'       => 'include/mainpage/components/ws_catalog/2.jpg',
//                        'youtube'     => 'https://www.youtube.com/watch?v=WE4yE22q26c',
                        'link' => '/articles/obshchie/obzor-na-kvadrotsikly-cfmoto-cforce-serii-450l/',
                        'description' => "
                        <ol>
                            <li><strong>Рабочий объем:</strong> 400 куб. см</li>
                            <li><strong>Мощность:</strong> 22,5 кВт (31 л.с.)</li>
                            <li><strong>Крутящий момент:</strong> 33 Нм</li>
                            <li><strong>Зажигание:</strong> ECU</li>
                            <li><strong>Стартер:</strong> Электрический</li>
                            <li><strong>Трансмиссия:</strong> Вариатор + КПП (L-H-N-R-P)</li>
                        </ol><p align='center'><a title='Информация про CFORCE 450L EPS' style='color: salmon; font-weight:bold; text-decoration:underline;' href='/articles/obshchie/obzor-na-kvadrotsikly-cfmoto-cforce-serii-450l/'>Обзор и больше информации</a></p>",
                    ],
                    [
                        'name'        => 'Квадроцикл CFORCE 625 TOURING',
//                        'image'       => getMediaItemByName(24, "01.jpg")['PATH'],
                        'image'       => 'include/mainpage/components/ws_catalog/3.jpg',
//                        'youtube'     => 'https://www.youtube.com/watch?v=WE4yE22q26c',
                        'link' => '/landings/kvadrocikl-cforce-625-touring/',
                        'description' => "
                        <ol>
                            <li><strong>Рабочий объем:</strong> 600 куб. см</li>
                            <li><strong>Мощность:</strong> 33 кВт (45 л.с.)</li>
                            <li><strong>Крутящий момент:</strong> 51 Нм</li>
                            <li><strong>Зажигание:</strong> ECU</li>
                            <li><strong>Стартер:</strong> Электрический</li>
                            <li><strong>Трансмиссия:</strong> Вариатор с понижающей передачей</li>
                        </ol><p align='center'><a title='Информация про CFORCE 625 TOURING' style='color: salmon; font-weight:bold; text-decoration:underline;' href='/landings/kvadrocikl-cforce-625-touring/'>Обзор и больше информации</a></p>",
                    ],
                    [
                        'name'        => 'Квадроцикл CFMOTO 520L EPS',
//                        'image'       => getMediaItemByName(25, "01.jpg")['PATH'],
                        'image'       => 'include/mainpage/components/ws_catalog/1.jpg',
//                        'youtube'     => 'https://www.youtube.com/watch?v=WE4yE22q26c',
                        'link' => '/landings/kvadrocicle-520-eps/',
                        'description' => "
                        <ol>
                            <li><strong>Рабочий объем:</strong> 495 куб. см</li>
                            <li><strong>Мощность:</strong> 28 кВт (38 л.с.)</li>
                            <li><strong>Крутящий момент:</strong> 46 Нм</li>
                            <li><strong>Зажигание:</strong> ECU</li>
                            <li><strong>Стартер:</strong> Электрический</li>
                            <li><strong>Трансмиссия:</strong> Автоматическая (вариатор), с понижающей передачей</li>
                        </ol><p align='center'><a title='Информация про CFMOTO CFORCE 520L EPS' style='color: salmon; font-weight:bold; text-decoration:underline;' href='/landings/kvadrocicle-520-eps/'>Обзор и больше информации</a></p>",
                    ],
                ];

                foreach ($catalogItems as $catalogItem) {
                    $name = $catalogItem['name'];
                    $description = $catalogItem['description'];

                    echo <<<EOF
                        <div class="ws-index-catalog__item">
                            <div class="ws-index-catalog__item-image">
                                <a href="{$catalogItem['link']}">
                                    <img src="{$catalogItem['image']}" alt="$name" title="$name">
                                </a>
                            </div>
                            <div class="ws-index-catalog__item-title" data-event="jqm" data-param-id="7">
                                $name
                            </div>
                            <div class="ws-index-catalog__item-description">
                                $description
                            </div>
                        </div>
                    EOF;
                }
                ?>
            </div>
        </div>

        <div class="mt-120">
            <div class="ws-index-catalog__tagline">
                <a rel="nofollow" href="https://t.me/dyadyalaifu/6">
                    Привозим мини-экскаваторы <strong>RIPPA</strong> по невероятно низким ценам с прямой поставкой из
                    Китая!
                    <br>Надежная техника и отличное предложение!
                </a>
            </div>

            <div class="ws-index-catalog">
                <?php
                $catalogItems = [
                    [
                        'name'        => 'Мини-экскаватор RIPPA R319',
                        'image'       => getMediaItemByName(28, "01.jpg")['PATH'],
                        'youtube'     => 'https://www.youtube.com/watch?v=lz1v9Y_81-o',
                        'description' => "
                        <ol>
                            <li><strong>Общий вес:</strong> 980 кг</li>
                            <li><strong>Габариты:</strong> 2550х930х2050 мм</li>
                            <li><strong>Марка и модель двигателя:</strong> Ligong 192F</li>
                            <li><strong>Мощность:</strong> 9,2 кВт</li>
                            <li><strong>Скорость движения:</strong> 0-3.5 км/ч</li>
                            <li><strong>Угол подъема:</strong> 35°</li>
                        </ol><p align='center'><a title='Информация про мини-экскаватор RIPPA R319' style='color: salmon; font-weight:bold; text-decoration:underline;' href='/landings/rippa-r319/'>Обзор и больше информации</a></p><p align='center'><a title='Сравнение мини-экскаватор RIPPA R319 с конкурентом' style='color: salmon; font-weight:bold; text-decoration:underline;' href='/landings/mini-stroitelnaya-technika/'>Сравнение с конкурентом</a></p>",
                    ],
                    [
                        'name'        => 'Мини-экскаватор NDI322',
                        'image'       => getMediaItemByName(29, "01.jpg")['PATH'],
                        'youtube'     => 'https://www.youtube.com/watch?v=lz1v9Y_81-o',
                        'description' => "
                         <ol>
                            <li><strong>Общий вес:</strong> 1500 кг</li>
                            <li><strong>Габариты:</strong> 2824х930х2275 мм</li>
                            <li><strong>Марка и модель двигателя:</strong> Кubоtа D722</li>
                            <li><strong>Мощность:</strong> 10,2 кВт</li>
                            <li><strong>Скорость движения:</strong> 0-1,5 км/ч</li>
                            <li><strong>Угол подъема:</strong> 30°</li>
                        </ol><p align='center'><a title='Информация про мини-экскаватор RIPPA NDI322' style='color: salmon; font-weight:bold; text-decoration:underline;' href='/landings/mini-ekskavator-rippa-ndi322/'>Обзор и больше информации</a></p>",
                    ],
                ];

                foreach ($catalogItems as $catalogItem) {
                    $name = $catalogItem['name'];
                    $description = $catalogItem['description'];

                    echo <<<EOF
                        <div class="ws-index-catalog__item">
                            <div class="ws-index-catalog__item-image">
                                <a href="{$catalogItem['youtube']}">
                                    <img src="{$catalogItem['image']}" alt="$name" title="$name">
                                </a>
                            </div>
                            <div class="ws-index-catalog__item-title" data-event="jqm" data-param-id="7">
                                $name
                            </div>
                            <div class="ws-index-catalog__item-description">
                                $description
                            </div>
                        </div>
                    EOF;
                }
                ?>
            </div>
        </div>

    </div>
</div>
<?*/?>