<div class="mainpage-blocks ws-index-banner ws-index-banner__moscow-delivery-point">
    <div class="ws-index-banner">

        <div class="ws-index-banner__image">
            <img id="ws-index-banner__moscow-delivery-point" src=""
                 alt="Наш пункт выдачи в Москве" loading="lazy">
        </div>

    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        let imgElement = document.getElementById('ws-index-banner__moscow-delivery-point');

        if (window.innerWidth <= 767) {
            imgElement.src = "<?=getMediaItemByName(collectionId: 11, itemName: '04-пункт выдачи.jpg')['PATH']?>";
        } else {
            imgElement.src = "<?=getMediaItemByName(collectionId: 10, itemName: '04-пункт выдачи.jpg')['PATH']?>";
        }
    });
</script>