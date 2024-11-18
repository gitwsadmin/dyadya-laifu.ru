<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}
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

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\UI\Extension;
use Rover\AmoCRM\Dependence\DependenceBuilder;
use Rover\AmoCRM\Options;
use Rover\AmoCRM\Service\Message;

Loc::loadMessages(__FILE__);

$this->setFrameMode(false);

Message::showAdmin();
Message::clear();

if (!DependenceBuilder::buildBase()->check()->isSuccess()) {
    return;
}

if (!Options::isJsDisabled()) {
    Extension::load(["rover_amocrm.select2"]);
}

$APPLICATION->IncludeComponent(
    "bitrix:main.interface.toolbar",
    ".default",
    [
        "BUTTONS" => $arResult['ACTION_PANEL']
    ],
    false,
    ['HIDE_ICONS' => 'Y']
);

$APPLICATION->IncludeComponent(
    "bitrix:main.interface.form",
    ".default",
    [
        "FORM_ID" => RoverAmoCrmProfileElement::FORM_ID,   //идентификатор формы
        "TABS"    => $arResult['TABS'],                  //описание вкладок формы
        "BUTTONS" => [                      //кнопки формы, возможны кастомные кнопки в виде html в "custom_html"
            "back_url"         => '/bitrix/admin/rover-acrm__profile-list.php?lang=' . LANGUAGE_ID,
            "custom_html"      => '',
            "standard_buttons" => $arParams['READ_ONLY'] != 'Y'
        ],
        "DATA"    => $arResult['DATA'],   //данные для редактировани
    ],
    false,
    ['HIDE_ICONS' => 'Y']
);


?>
<script>
    BX.message({
        rover_apu__placeholder_popup_header: '<?=GetMessageJS("rover-acpe__placeholder-popup-header")?>',
        rover_apu__placeholder_popup_close: '<?=GetMessageJS("rover-acpe__placeholder-popup-close")?>',
        rover_acpe__field_remove_title: '<?=GetMessageJS("rover-acpe__field-remove-title")?>',
        rover_acpe__field_add_title: '<?=GetMessageJS("rover-acpe__field-add-title")?>',

        rover_ac__event_init: '<?=GetMessageJS("rover-ac__event-init")?>',
    });
</script>
<style>
    #bx-admin-prefix .adm-detail-subtabs-block {
        white-space: normal;
    }
</style>