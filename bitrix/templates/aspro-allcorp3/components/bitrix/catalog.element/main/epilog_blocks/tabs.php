<?use \Bitrix\Main\Localization\Loc;
// добавим таб комплектация
$res = CIBlockElement::GetProperty($arResult['IBLOCK_ID'], $arResult['ID'], [], ["CODE" => "komplektaciya"]);
if ($prop = $res->Fetch()) {
    if (isset($prop["VALUE"]) && $prop["VALUE"] != '') {
        $komplektaciya = $prop["VALUE"];
        $APPLICATION->AddViewContent('komplektaciya', $komplektaciya['TEXT']);
        $arTabOrder[100] = 'komplektaciya';
        $arParams['T_KOMPLEKTACIYA'] = 'Комплектация';
    }
}
// добавим таб допонительно
$res1 = CIBlockElement::GetProperty($arResult['IBLOCK_ID'], $arResult['ID'], [], ["CODE" => "DOPOLNITELNO_TEXT"]);
if ($prop1 = $res1->Fetch()) {
    if (isset($prop1["VALUE"]) && $prop1["VALUE"] != '') {
        $dopolnitelno = $prop1["VALUE"];
        $APPLICATION->AddViewContent('dopolnitelno', $dopolnitelno['TEXT']);
    }
}
//show tabs block
$countTab = 0;
foreach ($arTabOrder as $iTab => $tabCode) {
    include $tabCode.'.php';

    $bShowVar = 'bShow_'.$tabCode;
    if (isset($$bShowVar) && $$bShowVar) {
        $countTab++;
    } else {
        unset($arTabOrder[$iTab]);
    }
}?>
<?if($countTab):?>
    <div class="detail-block ordered-block tabs-block">
        <?if($countTab > 1):?>
            <div class="tabs tabs-history arrow_scroll">
                <ul class="nav nav-tabs font_14 font_weight--600">
                    <?$iTab = 0;?>
                    <?foreach($arTabOrder as $tabCode):?>
                        <?$upperTabCode = mb_strtoupper($tabCode);?>
                        <li class="bordered rounded-4 <?=(!($iTab++) ? 'active' : '')?>"><a href="#<?=$tabCode?>" data-toggle="tab"><?=$arParams['T_'.$upperTabCode]?></a></li>
                    <?endforeach;?>
                </ul>
            </div>
        <?endif;?>
        <div class="tab-content<?=($iTab < 2 ? ' not_tabs' : '')?>">
            <?$iTab = 0;?>
            <?foreach($arTabOrder as $tabCode):?>
                <?include $tabCode.'.php';?>
            <?endforeach;?>
        </div>
    </div>
<?endif;?>