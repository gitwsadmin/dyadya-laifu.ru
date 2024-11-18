<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
//define global template name for body class
global $bodyDopClass, $USER, $bigBannersIndexClass;
if (strpos($bigBannersIndexClass, 'hidden') === false && $templateData['BANNERS_COUNT']) {
    if ($arParams['HEADER_OPACITY']) {
        $bodyDopClass .= ' header_opacity';

        $arOptions = [
            'PREFER_COLOR' => $templateData['CURRENT_BANNER_COLOR'] ?: ($_COOKIE['prefers-color-scheme'] ?? ''),
        ];
        if ($logoPosition = $APPLICATION->GetPageProperty('LOGO_POSITION')) {
            $arOptions['LOGO_POSITION'] = $logoPosition;
        }

        TSolution::setLogoColor($arOptions);
    }
    else if ($arParams['NO_OFFSET_BANNER'] && $arParams['NARROW_BANNER']) {
        $bodyDopClass .= ' header-no-border';
    }    
}

// for subscribe button in banner
if(isset($templateData['IS_SUBSCRIBE']) && $templateData['IS_SUBSCRIBE']){
    Bitrix\Main\Page\Frame::getInstance()->startDynamicWithID("banners-subscribe-".$arParams["IBLOCK_ID"]);
    
    $arSubscription = array();
    $email = '';
    $subscrId = '';
    if(CModule::IncludeModule("subscribe"))
    {
        //get current user subscription from cookies
        $arSubscription = CSubscription::GetUserSubscription();
    }
    if($arSubscription["ID"])
    {
        $email = $arSubscription["EMAIL"];
        $subscrId = $arSubscription["ID"];
    } else if( $USER->IsAuthorized() ){
        $email = $USER->GetEmail();
    }
    ?>
    <script type="text/javascript">
        $(document).ready(function() {
            try{
                $('.banners-big .subscribe-edit__form input[name=EMAIL]').val('<?=$email?>');
                $('.banners-big .subscribe-edit__form input[name=ID]').val('<?=$subscrId?>');
                $('.banners-big .subscribe-edit__form input[name=sessid]').val('<?=bitrix_sessid();?>');
                //$('.banners-big~input[name=sessid]').appendTo('.banners-big .subscribe-edit__form');
            }
            catch(e){
            }
        });
    </script>
<?
    Bitrix\Main\Page\Frame::getInstance()->finishDynamicWithID("banners-subscribe-".$arParams["IBLOCK_ID"], "");
}
?>
<?
$arExtensions = ['swiper', 'swiper_main_styles', 'top_banner'];
if ($templateData['HAS_VIDEO']) {
	$arExtensions[] = 'video_banner';
}
if ($arParams['HEADER_OPACITY'] && $templateData['BANNERS_COUNT']) {
    $arExtensions[] = 'header_opacity';
}
\TSolution\Extensions::init($arExtensions);
?>
