<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */

$this->setFrameMode(false);

use Bitrix\Main\Localization\Loc;
use Rover\AmoCRM\Service\Message;

Loc::loadMessages(__FILE__);

Message::showAdmin();

?>
<div class="acrmce">
    <div class="adm-info-message-wrap" style="position: relative; top: -15px;">
        <div class="adm-info-message">
            <?=Loc::getMessage('rover-acce__note');?>
        </div>
    </div>
    <?$APPLICATION->IncludeComponent(
        "bitrix:main.interface.toolbar",
        "",
        array(
            "BUTTONS"=> $arResult['ACTION_PANEL']
        ),
        false,
        array('HIDE_ICONS' => 'Y')
    );?><?$APPLICATION->IncludeComponent(
        "bitrix:main.interface.form",
        "",
        array(
            "FORM_ID"   => RoverAmoCrmConnectionElement::FORM_ID,
            "TABS"      => $arResult['TABS'],
            "BUTTONS"   => array(
                "back_url"          => '/bitrix/admin/rover-acrm__connection-list.php?lang=' . LANGUAGE_ID,
                "custom_html"       => "",
                "standard_buttons"  => $arParams['READ_ONLY'] != 'Y'
            ),
            "DATA"      => $arResult["DATA"],
        ),
        $component, array("HIDE_ICONS" => "Y")
    );
    ?>
</div>