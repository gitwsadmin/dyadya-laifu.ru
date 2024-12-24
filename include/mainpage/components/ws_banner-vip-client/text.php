<?php

use Bitrix\Main\Loader;
use Bitrix\Iblock\ElementTable;

Loader::includeModule('iblock');

// Задаем параметры
$elementId = 742;
$iblockId = 17;

// Получаем элемент
$res = CIBlockElement::GetByID($elementId);
if ($element = $res->GetNext()) {
    // ID детальной картинки
    $detailPictureId = $element['DETAIL_PICTURE'];
    $previewPictureId = $element['PREVIEW_PICTURE'];
    $text = $element['NAME'];

    // Получение путей к картинкам
    $detailPicture = CFile::GetPath($detailPictureId);
    $previewPicture = CFile::GetPath($previewPictureId);

}
?>

<div class="mainpage-blocks ws-index-banner ws-index-banner__vip-client">
    <div class="ws-index-banner">

        <div class="ws-index-banner__image">
            <img id="ws-index-banner__vip-client" src=""
                 alt="<?=$text?>" loading="lazy">
        </div>

    </div>
</div>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            let imgElement = document.getElementById('ws-index-banner__vip-client');

            if (window.innerWidth <= 767) {
                imgElement.src = "<?=$previewPicture?>";
            } else {
                imgElement.src = "<?=$detailPicture?>";
            }
        });
    </script>
<?/*?>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        let imgElement = document.getElementById('ws-index-banner__vip-client');

        if (window.innerWidth <= 767) {
            imgElement.src = "<?=getMediaItemByName(collectionId: 11, itemName: '05-карго компания.png')['PATH']?>";
        } else {
            imgElement.src = "<?=getMediaItemByName(collectionId: 10, itemName: '05-карго компания.jpg')['PATH']?>";
        }
    });
</script>
<?*/?>