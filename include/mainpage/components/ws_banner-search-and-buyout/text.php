<div class="mainpage-blocks ws-index-banner ws-index-banner__search-and-buyout">
    <div class="ws-index-banner">

        <div class="ws-index-banner__image">
            <img id="ws-index-banner__search-and-buyout" src=""
                 alt="Поиск и выкуп оборудования" loading="lazy">
        </div>

    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        let imgElement = document.getElementById('ws-index-banner__search-and-buyout');

        if (window.innerWidth <= 767) {
            imgElement.src = "<?=getMediaItemByName(collectionId: 11, itemName: '02-оборудование.png')['PATH']?>";
        } else {
            imgElement.src = "<?=getMediaItemByName(collectionId: 10, itemName: '02-оборудование.png')['PATH']?>";
        }
    });
</script>