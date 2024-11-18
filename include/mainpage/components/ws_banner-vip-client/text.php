<div class="mainpage-blocks ws-index-banner ws-index-banner__vip-client">
    <div class="ws-index-banner">

        <div class="ws-index-banner__image">
            <img id="ws-index-banner__vip-client" src=""
                 alt="Станьте ВИП клиентом в компании Дядя Лайфу" loading="lazy">
        </div>

    </div>
</div>

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