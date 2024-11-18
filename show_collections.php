<?php

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

// Проверка авторизации пользователя
global $USER;
if (!$USER->IsAuthorized()) {
    // Если пользователь не авторизован, перенаправить на страницу авторизации
    LocalRedirect("/bitrix/admin/");
}

// Проверка, что пользователь является администратором с ID 1
if ($USER->GetID() != 1) {
    // Если пользователь не администратор, перенаправить на главную страницу или выдать ошибку доступа
    die("Доступ запрещен");
}

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

CMedialib::Init();

$arCollections = CMedialibCollection::GetList(
    [
        'arOrder'  => ['ID' => 'ASC'],
        'arFilter' => ['ACTIVE' => 'Y'],
    ]
);

foreach ($arCollections as $arCollection) {
    debug($arCollection);

    $mediaItems = CMedialibItem::GetList(['arCollections' => [$arCollection['ID']]]);
    debug($mediaItems);
}


