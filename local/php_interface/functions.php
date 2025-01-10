<?php

use Bitrix\Main\Page\Asset;

function debug($var): void
{
    echo "<pre>";
    print_r($var);
    echo "</pre>";
}

/**
 * Канонические ссылки для всех страниц
 *
 * @param string $url
 *
 * @return string
 */
function getCanonicalUrl(string $url): string
{
    // Разбор URL на составляющие
    $parsedUrl = parse_url($url);
    $path = $parsedUrl['path'] ?? '';

    // Проверяем наличие /filter/ и удаляем его вместе с последующей частью URL
    $filterPos = strpos($path, '/filter/');
    if ($filterPos !== false) {
        $path = substr($path, 0, $filterPos);
    }

    // Убираем избыточные слеши в конце пути перед добавлением финального слеша и возвращаем каноническую ссылку
    return rtrim($path, '/') . '/';
}

/**
 * Получение списка элементов медиабиблиотеки по ID коллекции
 *
 * @param int $collectionId
 *
 * @return array
 */
function getMediaItemsByCollectionId(int $collectionId): array
{
    // Инициализация медиабиблиотеки
    CMedialib::Init();

    // Получение списка элементов медиабиблиотеки по коллекции
    $mediaItems = CMedialibItem::GetList(['arCollections' => [$collectionId]]);

    // Сортируем массив по NAME
    usort($mediaItems, static function ($a, $b) {
        return strcmp($a['NAME'], $b['NAME']);
    });

    return $mediaItems;
}

/**
 * Формирование HTML блока для списка изображений
 *
 * @param array $mediaItems
 * @param string $h3
 *
 * @return string
 */
function generateImagesHtmlBlock(array $mediaItems, string $h3): string
{
    // Подключение стилей и скриптов для fancybox
    Asset::getInstance()->addCss('/bitrix/templates/aspro-allcorp3/css/jquery.fancybox.min.css');
    Asset::getInstance()->addJs('/bitrix/templates/aspro-allcorp3/js/jquery.fancybox.min.js');

    $imagesBlock = '';

    // Формирование HTML блока для каждого изображения
    foreach ($mediaItems as $key => $mediaItem) {
        $pathToImage = $mediaItem['PATH'];
        $count = $key + 1;
        $alt = "$h3, фото $count";

        $imagesBlock .= <<<EOF
            <div class="ws-index-images-block__image">
                <a href="$pathToImage" data-fancybox="gallery">
                    <img loading="lazy" src="$pathToImage" alt="$alt" title="$alt">
                </a>
            </div>
        EOF;
    }

    return $imagesBlock;
}

/**
 * Вывод блока изображений из коллекции по ID
 *
 * @param int $collectionId
 * @param string $h3
 *
 * @return string
 */
function getImagesBlockByCollectionId(int $collectionId, string $h3): string
{
    $mediaItems = getMediaItemsByCollectionId($collectionId);

    return generateImagesHtmlBlock($mediaItems, $h3);
}


/**
 * Получение элемента медиабиблиотеки по ID или имени
 *
 * @param int $collectionId
 * @param int|null $itemId
 *
 * @return array|null
 */
function getMediaItemById(int $collectionId, int $itemId = null): ?array
{
    // Инициализация медиабиблиотеки
    CMedialib::Init();

    // Получение списка элементов медиабиблиотеки по коллекции
    $mediaItems = CMedialibItem::GetList(['arCollections' => [$collectionId]]);

    // Фильтрация по ID
    $filteredItems = array_filter($mediaItems, static function ($item) use ($itemId) {
        return (int)$item['ID'] === $itemId;
    });

    // Возвращаем первый найденный элемент или null, если ничего не найдено
    return !empty($filteredItems) ? array_shift($filteredItems) : null;
}

/**
 * Получение элемента медиабиблиотеки по имени
 *
 * @param int $collectionId
 * @param string|null $itemName
 *
 * @return array|null
 */
function getMediaItemByName(int $collectionId, string $itemName = null): ?array
{
    // Инициализация медиабиблиотеки
    CMedialib::Init();

    // Получение списка элементов медиабиблиотеки по коллекции
    $mediaItems = CMedialibItem::GetList(['arCollections' => [$collectionId]]);

    // Фильтрация по имени
    $filteredItems = array_filter($mediaItems, static function ($item) use ($itemName) {
        return $itemName !== null && $item['NAME'] === $itemName;
    });

    // Возвращаем первый найденный элемент или null, если ничего не найдено
    return !empty($filteredItems) ? array_shift($filteredItems) : null;
}

function get_currency()
{
    // формируем 2 даты - "завтра" и несколько дней назад
    $curDate = date('d/m/Y', mktime(0, 0, 0, date("n"), date("j") + 1, date("Y")));
    $curDate2 = date('d/m/Y', mktime(0, 0, 0, date("n"), date("j") - 10, date("Y")));

    // xml с сайта ЦБ РФ
    $currencyXML_CNY = file_get_contents(
        'http://www.cbr.ru/scripts/XML_dynamic.asp?date_req1=' . $curDate2 . '&date_req2=' . $curDate . '&VAL_NM_RQ=R01375'
    );
    // убираем переносы строк
    $currencyXML_CNY = str_replace(["\r\n", "\n", "\r"], '', $currencyXML_CNY);

    // регулярное выражение получает все значения тегов <Value> в массив $arValues
    preg_match_all('#<Value>(.*?)</Value>#', $currencyXML_CNY, $arValues, PREG_PATTERN_ORDER);
    // если совпадений больше одного, т.е. в xml были данные как минимум за 2 дня и можно отследить динамику

    if (count($arValues[1]) >= 2) {
        $arValues[1] = array_reverse($arValues[1]);
        $kurs_cny = number_format(str_replace(",", ".", $arValues[1][0]), 2, '.', ' ');
    }

    $arValues = [];

    // xml с сайта ЦБ РФ
    $currencyXML_USD = file_get_contents(
        'http://www.cbr.ru/scripts/XML_dynamic.asp?date_req1=' . $curDate2 . '&date_req2=' . $curDate . '&VAL_NM_RQ=R01235'
    );
    // убираем переносы строк
    $currencyXML_USD = str_replace(["\r\n", "\n", "\r"], '', $currencyXML_USD);

    // регулярное выражение получает все значения тегов <Value> в массив $arValues
    preg_match_all('#<Value>(.*?)</Value>#', $currencyXML_USD, $arValues, PREG_PATTERN_ORDER);
    // если совпадений больше одного, т.е. в xml были данные как минимум за 2 дня и можно отследить динамику

    if (count($arValues[1]) >= 2) {
        $arValues[1] = array_reverse($arValues[1]);

        $kurs_usd = number_format(str_replace(",", ".", $arValues[1][0]), 2, '.', ' ');
    }

    $res = [
        'kursCYN' => $kurs_cny,
        'kursUSD' => $kurs_usd,
    ];

    return $res;
}

/**
 * Основная функция для получения курсов валют.
 * Обновляет данные раз в сутки и сохраняет их в файл.
 *
 * @return array Ассоциативный массив с курсами валют для USD и CNY.
 * @throws Exception если не удалось получить или разобрать курсы валют.
 */
function getCurrency(): array
{
    $filePath = $_SERVER["DOCUMENT_ROOT"] . '/currency_rates.json';
    $currencyRates = [];

    if (shouldUpdateRates($filePath)) {
        $currencyRates = fetchCurrencyRatesFromCbr();
//        $currencyRates = fetchCurrencyRatesFromApi();
//        $url = "https://www.zhivagobank.ru/";
//        $content = fetchContent($url);
//        $content = fetchContentByCurl($url);

//        $currencyRates = parseCurrencyRates($content);
        saveRatesToFile($currencyRates, $filePath);
    } else {
        $currencyRates = readRatesFromFile($filePath);
    }

    return $currencyRates;
}

/**
 * Получает содержимое с указанного URL.
 *
 * @param string $url URL для получения содержимого.
 *
 * @return string Полученное содержимое.
 * @throws RuntimeException если URL недействителен или содержимое не может быть получено.
 */
function fetchContentByCurl(string $url): string
{
    if (!filter_var($url, FILTER_VALIDATE_URL)) {
        throw new RuntimeException("Недействительный URL: $url");
    }

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_FAILONERROR, true);

    $content = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if (curl_errno($ch)) {
        $errorMessage = curl_error($ch);
        curl_close($ch);
        throw new RuntimeException("Ошибка cURL: $errorMessage");
    }

    curl_close($ch);

    if ($httpCode !== 200) {
        throw new RuntimeException("Не удалось получить содержимое с $url (HTTP код: $httpCode)");
    }

    return $content;
}

/**
 * Получает курсы валют с API ExchangeRate-API.
 *
 * @return array Ассоциативный массив с курсами валют для USD и CNY.
 * @throws Exception если не удалось получить или разобрать курсы валют.
 */
function fetchCurrencyRatesFromApi(): array
{
    $apiKey = '841079e74811cc1c6f9ee311';  // Замените на ваш API ключ
    $url = "https://v6.exchangerate-api.com/v6/$apiKey/latest/USD";

    $response = file_get_contents($url);
    if ($response === false) {
        throw new \RuntimeException("Не удалось получить данные с API.");
    }

    $data = json_decode($response, true, 512, JSON_THROW_ON_ERROR);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new \RuntimeException("Ошибка при разборе JSON ответа: " . json_last_error_msg());
    }

    if (!isset($data['conversion_rates']['USD'], $data['conversion_rates']['CNY'])) {
        throw new \RuntimeException("Курсы валют не найдены в ответе API.");
    }

    return [
        'USD' => number_format($data['conversion_rates']['USD'], 2, '.', ' '),
        'CNY' => number_format($data['conversion_rates']['CNY'], 2, '.', ' '),
    ];
}

/**
 * Проверяет, нужно ли обновлять курсы валют.
 *
 * @param string $filePath Путь к файлу с курсами валют.
 *
 * @return bool Возвращает true, если данные нужно обновить, иначе false.
 */
function shouldUpdateRates(string $filePath): bool
{
    if (!file_exists($filePath)) {
//        debug('true');
        return true;
    }

//    debug('false');

    $fileModificationTime = filemtime($filePath);
    $currentTime = time();

    // Обновляем данные, если прошло больше суток с момента последнего обновления
    return ($currentTime - $fileModificationTime) > 86400;
}

/**
 * Сохраняет курсы валют в файл.
 *
 * @param array $currencyRates Ассоциативный массив с курсами валют для USD и CNY.
 * @param string $filePath Путь к файлу для сохранения.
 *
 * @return void
 * @throws JsonException
 */
function saveRatesToFile(array $currencyRates, string $filePath): void
{
    $jsonContent = json_encode($currencyRates, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    file_put_contents($filePath, $jsonContent);
}

/**
 * Читает курсы валют из файла.
 *
 * @param string $filePath Путь к файлу с курсами валют.
 *
 * @return array Ассоциативный массив с курсами валют для USD и CNY.
 * @throws Exception если не удалось прочитать или разобрать файл с курсами.
 */
function readRatesFromFile(string $filePath): array
{
    $jsonContent = file_get_contents($filePath);
    if ($jsonContent === false) {
        throw new \RuntimeException("Не удалось прочитать файл с курсами: $filePath");
    }

    $currencyRates = json_decode($jsonContent, true, 512, JSON_THROW_ON_ERROR);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new \RuntimeException("Ошибка при разборе JSON из файла: " . json_last_error_msg());
    }

    return $currencyRates;
}

/**
 * Получает курсы валют с сайта ЦБ РФ.
 *
 * @return array Ассоциативный массив с курсами валют для USD и CNY.
 * @throws Exception если не удалось получить или разобрать курсы валют.
 */
function fetchCurrencyRatesFromCbr(): array
{
    $currentDate = date('d/m/Y', strtotime('+1 day'));
    $startDate = date('d/m/Y', strtotime('-10 days'));

    $cnyRate = fetchCurrencyRate('R01375', $startDate, $currentDate, 'CNY');
    $usdRate = fetchCurrencyRate('R01235', $startDate, $currentDate, 'USD');

    return [
        'USD' => $usdRate,
        'CNY' => $cnyRate,
    ];
}

/**
 * Получает курс валюты с сайта ЦБ РФ по коду валюты.
 *
 * @param string $currencyCode Код валюты.
 * @param string $startDate Дата начала периода.
 * @param string $endDate Дата конца периода.
 * @param string $currencyName Название валюты (для сообщений об ошибках).
 *
 * @return string Курс валюты.
 * @throws Exception если не удалось получить или разобрать курсы валют.
 */
function fetchCurrencyRate(string $currencyCode, string $startDate, string $endDate, string $currencyName): string
{
    $url = 'http://www.cbr.ru/scripts/XML_dynamic.asp?date_req1=' . $startDate . '&date_req2=' . $endDate . '&VAL_NM_RQ=' . $currencyCode;
    $currencyXML = fetchContent($url, $currencyName);

    $currencyXML = str_replace(["\r\n", "\n", "\r"], '', $currencyXML);
    preg_match_all('#<Value>(.*?)</Value>#', $currencyXML, $values, PREG_PATTERN_ORDER);

    if (count($values[1]) < 1) {
        throw new RuntimeException("Недостаточно данных для $currencyName.");
    }

    $values[1] = array_reverse($values[1]);
    $latestRate = number_format(str_replace(",", ".", $values[1][0]), 2, '.', ' ');

    return $latestRate;
}

///**
// * Получает содержимое с указанного URL.
// *
// * @param string $url URL для получения содержимого.
// *
// * @return string Полученное содержимое.
// * @throws Exception если URL недействителен или содержимое не может быть получено.
// */
//function fetchContent(string $url): string
//{
//    if (!filter_var($url, FILTER_VALIDATE_URL)) {
//        throw new \RuntimeException("Недействительный URL: $url");
//    }
//
//    $content = file_get_contents($url);
//    if ($content === false) {
//        throw new \RuntimeException("Не удалось получить содержимое с $url");
//    }
//
//    return $content;
//}

/**
 * Получает содержимое с указанного URL.
 *
 * @param string $url URL для получения содержимого.
 * @param string $currencyName Название валюты (для сообщений об ошибках).
 *
 * @return string Полученное содержимое.
 * @throws RuntimeException если URL недействителен или содержимое не может быть получено.
 */
function fetchContent(string $url, string $currencyName): string
{
    if (!filter_var($url, FILTER_VALIDATE_URL)) {
        throw new RuntimeException("Недействительный URL: $url");
    }

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_FAILONERROR, true);

    $content = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if (curl_errno($ch)) {
        $errorMessage = curl_error($ch);
        curl_close($ch);
        throw new RuntimeException("Ошибка cURL: $errorMessage");
    }

    curl_close($ch);

    if ($httpCode !== 200) {
        throw new RuntimeException("Не удалось получить содержимое с $url (HTTP код: $httpCode) для $currencyName");
    }

    return $content;
}

/**
 * Парсит курсы валют из полученного содержимого.
 *
 * @param string $content Полученное содержимое с URL.
 *
 * @return array Ассоциативный массив с курсами валют для USD и CNY.
 * @throws Exception если не удалось разобрать курсы валют.
 */
function parseCurrencyRates(string $content): array
{
    preg_match('~<table class="currency-main">(.*?)</table>~s', $content, $tableMatches);
    if (empty($tableMatches)) {
        throw new \RuntimeException("Таблица валют не найдена в содержимом.");
    }

    preg_match_all('~<tr>(.*?)</tr>~s', $tableMatches[1], $rowMatches);
    if (empty($rowMatches[0][1]) || empty($rowMatches[0][3])) {
        throw new \RuntimeException("Строки валют не найдены в таблице.");
    }

    $usdRate = extractRateFromRow($rowMatches[0][1]);
    $cnyRate = extractRateFromRow($rowMatches[0][3]);

    return [
        'USD' => $usdRate,
        'CNY' => $cnyRate,
    ];
}

/**
 * Извлекает курс валюты из строки таблицы.
 *
 * @param string $row HTML содержимое строки таблицы.
 *
 * @return string Извлеченный и обрезанный курс валюты.
 * @throws Exception если не удалось извлечь курс валюты.
 */
function extractRateFromRow(string $row): string
{
    preg_match_all('~<td>(.*?)</td>~s', $row, $cellMatches);
    if (empty($cellMatches[1][1])) {
        throw new \RuntimeException("Курс валюты не найден в строке.");
    }

    return trim($cellMatches[1][1]);
}
