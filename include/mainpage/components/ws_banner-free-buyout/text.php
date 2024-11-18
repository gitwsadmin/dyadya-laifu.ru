<div class="mainpage-blocks ws-index-banner ws-index-banner__free-buyout">
    <div class="ws-index-banner">

        <div class="ws-index-banner__image">
            <img id="ws-index-banner__free-buyout" src=""
                 alt="Бесплатный выкуп товара при отгрузке" loading="lazy">
        </div>

    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        let imgElement = document.getElementById('ws-index-banner__free-buyout');

        if (window.innerWidth <= 767) {
            imgElement.src = "<?=getMediaItemByName(collectionId: 11, itemName: '01-бесплатныи выкуп.png')['PATH']?>";
        } else {
            imgElement.src = "<?=getMediaItemByName(collectionId: 10, itemName: '01-бесплатныи выкуп.png')['PATH']?>";
        }
    });
</script>