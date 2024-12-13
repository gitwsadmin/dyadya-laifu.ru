<?
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_before.php');
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_after.php');

global $APPLICATION;
IncludeModuleLangFile(__FILE__);

$moduleClass = "CAllcorp3";
$moduleID = "aspro.allcorp3";
$moduleName = "allcorp3";
$templateName = "aspro-allcorp3";

\Bitrix\Main\Loader::includeModule($moduleID);

use \Bitrix\Main\Config\Option,
    \Bitrix\Main\Localization\Loc;

$RIGHT = $APPLICATION->GetGroupRight($moduleID);
?>
<?if ($RIGHT >= "R"):?>
    <?
        $GLOBALS['APPLICATION']->SetAdditionalCss("/bitrix/css/".$moduleID."/style.css");
        $GLOBALS['APPLICATION']->SetTitle(Loc::getMessage("ALLCORP3_PAGE_TITLE"));

        $by = "id";
        $sort = "asc";

        $arSites = array();
        $db_res = CSite::GetList($by, $sort, array("ACTIVE"=>"Y"));
        while ($res = $db_res->Fetch()){
            $arSites[] = $res;
        }

        $arTabs = array();
        $bShowGenerate = false;
        foreach ($arSites as $key => $arSite){
            $arSite['DIR'] = str_replace('//', '/', '/'.$arSite['DIR']);
            if (!strlen($arSite['DOC_ROOT'])){
                $arSite['DOC_ROOT'] = $_SERVER['DOCUMENT_ROOT'];
            }
            $arSite['DOC_ROOT'] = str_replace('//', '/', $arSite['DOC_ROOT'].'/');
            $siteDir = str_replace('//', '/', $arSite['DOC_ROOT'].$arSite['DIR']);
            $optionsSiteID = $arSite["ID"];

            $arTabs[] = array(
                "DIV" => "edit".($key+1),
                "TAB" => Loc::getMessage("MAIN_OPTIONS_SITE_TITLE", array(
                    "#SITE_NAME#" => $arSite["NAME"], 
                    "#SITE_ID#" => $arSite["ID"]
                )),
                "ICON" => "settings",
                "PAGE_TYPE" => "site_settings",
                "SITE_ID" => $arSite["ID"],
                "SITE_DIR" => $arSite["DIR"],
                "SITE_DIR_FORMAT" => $siteDir,
                "LANGUAGE_ID" => $arSite["LANGUAGE_ID"],
            );
        }

        $tabControl = new CAdminTabControl("tabControl", $arTabs);

        $request = Bitrix\Main\Application::getInstance()->getContext()->getRequest();
        $postList = $request->getPostList()->toArray();

        if ($RIGHT >= "W" && isset($postList['generate']) && check_bitrix_sessid()) {
            $thematic = strtolower(Option::get($moduleID, 'THEMATIC', 'UNIVERSAL', $postList['SITE_ID']));

            require_once $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/classes/general/wizard.php";
            require_once $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/install/wizard_sol/utils.php";
            require_once $_SERVER['DOCUMENT_ROOT']."/bitrix/modules/".$moduleID."/install/wizards/aspro/".$moduleName."/site/services/form/lang/".$_REQUEST['LANGUAGE_ID']."/forms.php";
            include_once $_SERVER['DOCUMENT_ROOT']."/bitrix/modules/".$moduleID."/install/wizards/aspro/".$moduleName."/site/services/service_".$thematic.".php";

            if (isset($arServices) && isset($arServices['form']) && isset($arServices['form']['STAGES'])) {
                define('WIZARD_SITE_ID', $postList['SITE_ID']);
                define('WIZARD_SITE_EMAIL', $postList['SITE_EMAIL']);
                define('WIZARD_TEMPLATE_ID', $templateName);
                define('WIZARD_SERVICE_ABSOLUTE_PATH', $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.$moduleID.'/install/wizards/aspro/'.$moduleName.'/site/services/');

                $arForms = CForm::GetList($by = "s_id", $order = "desc", ['SITE' => $postList['SITE_ID']], $is_filtered);
                $arWebForms = [];

                while ($arForm = $arForms->Fetch()) {
                    $arWebForms[] = $arForm['SID'];
                }
                
                foreach ($arServices['form']['STAGES'] as $form) {
                    $fp = $_SERVER['DOCUMENT_ROOT']."/bitrix/modules/".$moduleID."/install/wizards/aspro/".$moduleName."/site/services/form/".$form;
    
                    if (file_exists($fp)) {
                        include_once $fp;
                    }
                }
            }
        }
        
        CJSCore::Init(array("jquery"));
   ?>
    <div class="adm-info-message"><?=Loc::getMessage("ALLCORP3_GENERATE_WEB_FORMS_INFO");?></div>
    <br>
    <br>
    <?if (!count($arTabs)):?>
		<div class="adm-info-message-wrap adm-info-message-red">
			<div class="adm-info-message">
				<div class="adm-info-message-title"><?=Loc::getMessage("ALLCORP3_NO_SITE_INSTALLED", array("#SESSION_ID#"=>bitrix_sessid_get()))?></div>
				<div class="adm-info-message-icon"></div>
			</div>
		</div>
	<?else:?>
        <?$tabControl->Begin();?>
        <form method="post" class="allcorp3_options allcorp3_options_generate_web_forms" enctype="multipart/form-data" action="<?=$APPLICATION->GetCurPage()?>?mid=<?=urlencode($mid)?>&amp;lang=<?=LANGUAGE_ID?>&<?=$tabControl->ActiveTabParam();?>">
            <?=bitrix_sessid_post();?>
            <?
            $activeTab = explode('=', $tabControl->ActiveTabParam())[1];
            ?>
            <?foreach ($arTabs as $key => $arTab):?>
                <?$tabControl->BeginNextTab();?>
                <tr id="<?=$arTab['DIV'];?>_fields">
                    <td>
                        <?if ($arTab["SITE_ID"]):?>
                            <input type="hidden" name="SITE_ID" value="<?=$arTab['SITE_ID'];?>"<?=$arTab['DIV'] === $activeTab ? '' : ' disabled';?> />
                            <input type="hidden" name="SITE_EMAIL" value="<?=$arSite['EMAIL'];?>"<?=$arTab['DIV'] === $activeTab ? '' : ' disabled';?>>
                            <input type="hidden" name="LANGUAGE_ID" value="<?=$arTab['LANGUAGE_ID'];?>"<?=$arTab['DIV'] === $activeTab ? '' : ' disabled';?>>

                            <input class="submit-btn adm-btn-save" type="submit" name="generate" value="<?=Loc::getMessage("ALLCORP3_GENERATE_WEB_FORMS");?>"<?=$arTab['DIV'] === $activeTab ? '' : ' disabled';?>>
                        <?endif;?>
                    </td>
                </tr>
                <?if ($RIGHT >= "W" && isset($postList['SITE_ID']) && $postList['SITE_ID'] === $arTab['SITE_ID'] && isset($arWebForms)):?>
                    <?
                    $arForms = CForm::GetList($by = "s_id", $order = "desc", ['SITE' => $postList['SITE_ID']], $is_filtered);
                    $arNewWebForms = [];
                    
                    while ($arForm = $arForms->Fetch()) {
                        if (!in_array($arForm['SID'], $arWebForms)){
                            $arNewWebForms[] = [
                                'SID' => $arForm['SID'],
                                'NAME' => $arForm['NAME'],
                            ];
                        }
                    }
                    ?>
                    <tr>
                        <td colspan="2">
                            <?if (count($arNewWebForms)):?>
                                <div class="adm-info-message"><?=Loc::getMessage("ALLCORP3_GENERATE_WEB_FORMS_INFO_SUCCESS");?></div>
                                <table width="500">
                                    <tr>
                                        <th><?=Loc::getMessage("ALLCORP3_GENERATE_WEB_FORMS_NAME");?></th>
                                        <th><?=Loc::getMessage("ALLCORP3_GENERATE_WEB_FORMS_SID");?></th>
                                    </tr>
                                    <?foreach ($arNewWebForms as $webForm):?>
                                        <tr>
                                            <td><?=$webForm['NAME'];?></td>
                                            <td><?=$webForm['SID'];?></td>
                                        </tr>
                                    <?endforeach;?>
                                </table>
                            <?else:?>
                                <div class="adm-info-message"><?=Loc::getMessage("ALLCORP3_GENERATE_WEB_FORMS_INFO_ACTUAL");?></div>
                            <?endif;?>
                        </td>
                    </tr>
                <?endif;?>
            <?endforeach;?>
        </form>
        <script>
            const $tabs = document.querySelectorAll('#tabControl_tabs .adm-detail-tab');
            const $form = document.querySelector('.allcorp3_options_generate_web_forms');
            const $formInputs = document.querySelectorAll('.allcorp3_options_generate_web_forms .adm-detail-content input')

            for (let i = 0; i < $tabs.length; i++) {
                $tabs[i].addEventListener('click', function(e){
                    const currentTab = e.target.id.split('tab_cont_')[1];
                    const action = new URL($form.action);
                    const $fields = document.querySelectorAll('#' + currentTab + '_fields input');

                    
                    for (let j = 0; j < $formInputs.length; j++) {
                        $formInputs[j].disabled = true;
                    }

                    for (let j = 0; j < $fields.length; j++) {
                        $fields[j].disabled = false;
                    }

                    action.searchParams.set('tabControl_active_tab', currentTab);
                    $form.action = action;
                });
            }
        </script>
        <?$tabControl->End();?>
    <?endif;?>
<?else:?>
    <?=CAdminMessage::ShowMessage(Loc::getMessage('NO_RIGHTS_FOR_VIEWING'));?>
<?endif;?>

<?require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_admin.php');?>