<?php
/**
 * Allcorp3 module
 * @copyright 2017 Aspro
 */

IncludeModuleLangFile(__FILE__);

class aspro_allcorp3 extends CModule {
	const solutionName	= 'allcorp3';
	const partnerName = 'aspro';
	const moduleClass = 'CAllcorp3';
	const moduleClassEvents = 'CAllcorp3Events';

	var $MODULE_ID = 'aspro.allcorp3';
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $MODULE_CSS;
	var $MODULE_GROUP_RIGHTS = 'Y';

	function __construct(){
		$arModuleVersion = array();

		$path = str_replace('\\', '/', __FILE__);
		$path = substr($path, 0, strlen($path) - strlen('/index.php'));
		include($path.'/version.php');

		$this->MODULE_VERSION = $arModuleVersion['VERSION'];
		$this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
		$this->MODULE_NAME = GetMessage('ALLCORP3_MODULE_NAME');
		$this->MODULE_DESCRIPTION = GetMessage('ALLCORP3_MODULE_DESC');
		$this->PARTNER_NAME = GetMessage('ALLCORP3_PARTNER');
		$this->PARTNER_URI = GetMessage('ALLCORP3_PARTNER_URI');
	}

	function checkValid(){
		return true;
	}

	function InstallDB($install_wizard = true){
		global $DB, $DBType, $APPLICATION;

		RegisterModule($this->MODULE_ID);
		COption::SetOptionString($this->MODULE_ID, 'GROUP_DEFAULT_RIGHT', $this->MODULE_GROUP_RIGHTS);

		if(preg_match('/.bitrixlabs.ru/', $_SERVER['HTTP_HOST'])){
			RegisterModuleDependences('main', 'OnBeforeProlog', $this->MODULE_ID, self::moduleClass, 'correctInstall');
		}

		if(CModule::IncludeModule($this->MODULE_ID)){
			$moduleClass = self::moduleClass;
			$instance = new $moduleClass();
			$instance::sendAsproBIAction('install');
		}

		return true;
	}

	function UnInstallDB($arParams = array()){
		global $DB, $DBType, $APPLICATION;

		if(CModule::IncludeModule($this->MODULE_ID)){
			$moduleClass = self::moduleClass;
			$instance = new $moduleClass();
			$instance::sendAsproBIAction('delete');
		}

		COption::RemoveOption($this->MODULE_ID, 'GROUP_DEFAULT_RIGHT');
		UnRegisterModule($this->MODULE_ID);

		return true;
	}

	function InstallEvents(){
		RegisterModuleDependences('main', 'OnEndBufferContent', $this->MODULE_ID, self::moduleClassEvents, 'OnEndBufferContentHandler');
		RegisterModuleDependences('main', 'OnPageStart', $this->MODULE_ID, self::moduleClassEvents, 'OnPageStartHandler');
		RegisterModuleDependences('main', 'OnBeforeEventAdd', $this->MODULE_ID, self::moduleClassEvents, 'OnBeforeEventAddHandler');

		RegisterModuleDependences('main', 'OnBeforeUserRegister', $this->MODULE_ID, self::moduleClassEvents, 'OnBeforeUserUpdateHandler');
		RegisterModuleDependences('main', 'OnBeforeUserAdd', $this->MODULE_ID, self::moduleClassEvents, 'OnBeforeUserUpdateHandler');
		RegisterModuleDependences('main', 'OnBeforeUserUpdate', $this->MODULE_ID, self::moduleClassEvents, 'OnBeforeUserUpdateHandler');
		RegisterModuleDependences('main', 'OnAfterUserRegister', $this->MODULE_ID, self::moduleClassEvents, 'OnAfterUserRegisterHandler');
		RegisterModuleDependences('main', 'OnAdminContextMenuShow', $this->MODULE_ID, self::moduleClassEvents, 'OnAdminContextMenuShowHandler');
		RegisterModuleDependences('main', 'OnAfterUserLogin', $this->MODULE_ID, self::moduleClassEvents, 'OnAfterUserLoginHandler');

		RegisterModuleDependences('iblock', 'OnIBlockPropertyBuildList', $this->MODULE_ID, 'Aspro\Allcorp3\Property\ListUsersGroups', 'OnIBlockPropertyBuildList');
		RegisterModuleDependences('iblock', 'OnIBlockPropertyBuildList', $this->MODULE_ID, 'Aspro\Allcorp3\Property\ListWebForms', 'OnIBlockPropertyBuildList');
		RegisterModuleDependences('iblock', 'OnIBlockPropertyBuildList', $this->MODULE_ID, 'Aspro\Allcorp3\Property\CustomFilter', 'OnIBlockPropertyBuildList');
		RegisterModuleDependences('iblock', 'OnIBlockPropertyBuildList', $this->MODULE_ID, 'Aspro\Allcorp3\Property\ModalConditions', 'OnIBlockPropertyBuildList');
		RegisterModuleDependences('iblock', 'OnIBlockPropertyBuildList', $this->MODULE_ID, 'Aspro\Allcorp3\Property\ConditionType', 'OnIBlockPropertyBuildList');
		RegisterModuleDependences('iblock', 'OnIBlockPropertyBuildList', $this->MODULE_ID, 'Aspro\Allcorp3\Property\TariffItem', 'OnIBlockPropertyBuildList');

		RegisterModuleDependences('iblock', 'OnAfterIBlockAdd', $this->MODULE_ID, 'CAllcorp3Cache', 'ClearTagIBlock');
		RegisterModuleDependences('iblock', 'OnAfterIBlockUpdate', $this->MODULE_ID, 'CAllcorp3Cache', 'ClearTagIBlock');
		RegisterModuleDependences('iblock', 'OnBeforeIBlockDelete', $this->MODULE_ID, 'CAllcorp3Cache', "ClearTagIBlockBeforeDelete");

		RegisterModuleDependences('iblock', 'OnAfterIBlockElementAdd', $this->MODULE_ID, 'CAllcorp3Cache', 'ClearTagIBlockElement');
		RegisterModuleDependences('iblock', 'OnAfterIBlockElementUpdate', $this->MODULE_ID, 'CAllcorp3Cache', 'ClearTagIBlockElement');
		RegisterModuleDependences('iblock', 'OnAfterIBlockElementUpdate', $this->MODULE_ID, self::moduleClassEvents, 'OnRegionUpdateHandler');
		RegisterModuleDependences('iblock', 'OnAfterIBlockElementDelete', $this->MODULE_ID, 'CAllcorp3Cache', 'ClearTagIBlockElement');

		RegisterModuleDependences('iblock', 'OnAfterIBlockSectionAdd', $this->MODULE_ID, 'CAllcorp3Cache', 'ClearTagIBlockSection');
		RegisterModuleDependences('iblock', 'OnAfterIBlockSectionUpdate', $this->MODULE_ID, 'CAllcorp3Cache', 'ClearTagIBlockSection');
		RegisterModuleDependences('iblock', 'OnBeforeIBlockSectionDelete', $this->MODULE_ID, 'CAllcorp3Cache', 'ClearTagIBlockSectionBeforeDelete');

		RegisterModuleDependences('iblock', 'OnAfterIBlockPropertyAdd', $this->MODULE_ID, 'CAllcorp3', 'UpdateFormEvent');
		RegisterModuleDependences('iblock', 'OnAfterIBlockPropertyUpdate', $this->MODULE_ID, 'CAllcorp3', 'UpdateFormEvent');

		RegisterModuleDependences('socialservices', 'OnAfterSocServUserAdd', $this->MODULE_ID, self::moduleClassEvents, 'OnAfterSocServUserAddHandler');
		RegisterModuleDependences('socialservices', 'OnFindSocialservicesUser', $this->MODULE_ID, self::moduleClassEvents, 'OnFindSocialservicesUserHandler');
		
		RegisterModuleDependences('subscribe', 'OnBeforeSubscriptionAdd', $this->MODULE_ID, self::moduleClassEvents, 'OnBeforeSubscriptionAddHandler');

		RegisterModuleDependences('form', 'onBeforeResultAdd', $this->MODULE_ID, self::moduleClassEvents, 'onBeforeResultAddHandler');
		RegisterModuleDependences('form', 'onAfterResultAdd', $this->MODULE_ID, self::moduleClassEvents, 'onAfterResultAddHandler');

		RegisterModuleDependences('iblock', 'OnAfterIBlockElementAdd', $this->MODULE_ID, self::moduleClassEvents, 'OnAfterIBlockElementAddHandler');

		RegisterModuleDependences($this->MODULE_ID, 'OnAsproParameters', $this->MODULE_ID, self::moduleClassEvents, 'onAsproParametersHandler');

		return true;
	}

	function UnInstallEvents(){
		UnRegisterModuleDependences('main', 'OnEndBufferContent', $this->MODULE_ID, self::moduleClassEvents, 'OnEndBufferContentHandler');
		UnRegisterModuleDependences('main', 'OnPageStart', $this->MODULE_ID, self::moduleClassEvents, 'OnPageStartHandler');
		UnRegisterModuleDependences('main', 'OnBeforeEventAdd', $this->MODULE_ID, self::moduleClassEvents, 'OnBeforeEventAddHandler');

		UnRegisterModuleDependences('main', 'OnBeforeUserRegister', $this->MODULE_ID, self::moduleClassEvents, 'OnBeforeUserUpdateHandler');
		UnRegisterModuleDependences('main', 'OnBeforeUserAdd', $this->MODULE_ID, self::moduleClassEvents, 'OnBeforeUserUpdateHandler');
		UnRegisterModuleDependences('main', 'OnBeforeUserUpdate', $this->MODULE_ID, self::moduleClassEvents, 'OnBeforeUserUpdateHandler');
		UnRegisterModuleDependences('main', 'OnAfterUserRegister', $this->MODULE_ID, self::moduleClassEvents, 'OnAfterUserRegisterHandler');
		UnRegisterModuleDependences('main', 'OnAdminContextMenuShow', $this->MODULE_ID, self::moduleClassEvents, 'OnAdminContextMenuShowHandler');
		UnRegisterModuleDependences('main', 'OnAfterUserLogin', $this->MODULE_ID, self::moduleClassEvents, 'OnAfterUserLoginHandler');

		UnRegisterModuleDependences('iblock', 'OnIBlockPropertyBuildList', $this->MODULE_ID, 'Aspro\Allcorp3\Property\ListUsersGroups', 'OnIBlockPropertyBuildList');
		UnRegisterModuleDependences('iblock', 'OnIBlockPropertyBuildList', $this->MODULE_ID, 'Aspro\Allcorp3\Property\ListWebForms', 'OnIBlockPropertyBuildList');
		UnRegisterModuleDependences('iblock', 'OnIBlockPropertyBuildList', $this->MODULE_ID, 'Aspro\Allcorp3\Property\CustomFilter', 'OnIBlockPropertyBuildList');
		UnRegisterModuleDependences('iblock', 'OnIBlockPropertyBuildList', $this->MODULE_ID, 'Aspro\Allcorp3\Property\ModalConditions', 'OnIBlockPropertyBuildList');
		UnRegisterModuleDependences('iblock', 'OnIBlockPropertyBuildList', $this->MODULE_ID, 'Aspro\Allcorp3\Property\ConditionType', 'OnIBlockPropertyBuildList');
		UnRegisterModuleDependences('iblock', 'OnIBlockPropertyBuildList', $this->MODULE_ID, 'Aspro\Allcorp3\Property\TariffItem', 'OnIBlockPropertyBuildList');

		UnRegisterModuleDependences('iblock', 'OnAfterIBlockAdd', $this->MODULE_ID, 'CAllcorp3Cache', 'ClearTagIBlock');
		UnRegisterModuleDependences('iblock', 'OnAfterIBlockUpdate', $this->MODULE_ID, 'CAllcorp3Cache', 'ClearTagIBlock');
		UnRegisterModuleDependences('iblock', 'OnBeforeIBlockDelete', $this->MODULE_ID, 'CAllcorp3Cache', "ClearTagIBlockBeforeDelete");

		UnRegisterModuleDependences('iblock', 'OnAfterIBlockElementAdd', $this->MODULE_ID, 'CAllcorp3Cache', 'ClearTagIBlockElement');
		UnRegisterModuleDependences('iblock', 'OnAfterIBlockElementUpdate', $this->MODULE_ID, 'CAllcorp3Cache', 'ClearTagIBlockElement');
		UnRegisterModuleDependences('iblock', 'OnAfterIBlockElementUpdate', $this->MODULE_ID, self::moduleClassEvents, 'OnRegionUpdateHandler');
		UnRegisterModuleDependences('iblock', 'OnAfterIBlockElementDelete', $this->MODULE_ID, 'CAllcorp3Cache', 'ClearTagIBlockElement');

		UnRegisterModuleDependences('iblock', 'OnAfterIBlockSectionAdd', $this->MODULE_ID, 'CAllcorp3Cache', 'ClearTagIBlockSection');
		UnRegisterModuleDependences('iblock', 'OnAfterIBlockSectionUpdate', $this->MODULE_ID, 'CAllcorp3Cache', 'ClearTagIBlockSection');
		UnRegisterModuleDependences('iblock', 'OnBeforeIBlockSectionDelete', $this->MODULE_ID, 'CAllcorp3Cache', 'ClearTagIBlockSectionBeforeDelete');

		UnRegisterModuleDependences('iblock', 'OnAfterIBlockPropertyAdd', $this->MODULE_ID, 'CAllcorp3', 'UpdateFormEvent');
		UnRegisterModuleDependences('iblock', 'OnAfterIBlockPropertyUpdate', $this->MODULE_ID, 'CAllcorp3', 'UpdateFormEvent');

		UnRegisterModuleDependences('socialservices', 'OnAfterSocServUserAdd', $this->MODULE_ID, self::moduleClassEvents, 'OnAfterSocServUserAddHandler');
		UnRegisterModuleDependences('socialservices', 'OnFindSocialservicesUser', $this->MODULE_ID, self::moduleClassEvents, 'OnFindSocialservicesUserHandler');
		
		UnRegisterModuleDependences('subscribe', 'OnBeforeSubscriptionAdd', $this->MODULE_ID, self::moduleClassEvents, 'OnBeforeSubscriptionAddHandler');

		UnRegisterModuleDependences('form', 'onBeforeResultAdd', $this->MODULE_ID, self::moduleClassEvents, 'onBeforeResultAddHandler');
		UnRegisterModuleDependences('form', 'onAfterResultAdd', $this->MODULE_ID, self::moduleClassEvents, 'onAfterResultAddHandler');

		UnRegisterModuleDependences('iblock', 'OnAfterIBlockElementAdd', $this->MODULE_ID, self::moduleClassEvents, 'OnAfterIBlockElementAddHandler');

		UnRegisterModuleDependences($this->MODULE_ID, 'OnAsproParameters', $this->MODULE_ID, self::moduleClassEvents, 'onAsproParametersHandler');

		return true;
	}

	function InstallPublic(){
	}

	function InstallFiles(){
		CopyDirFiles(__DIR__.'/admin/', $_SERVER['DOCUMENT_ROOT'].'/bitrix/admin', true);
		CopyDirFiles(__DIR__.'/components/', $_SERVER['DOCUMENT_ROOT'].'/bitrix/components', true, true);
		CopyDirFiles(__DIR__.'/wizards/', $_SERVER['DOCUMENT_ROOT'].'/bitrix/wizards', true, true);

		CopyDirFiles(__DIR__.'/css/', $_SERVER['DOCUMENT_ROOT'].'/bitrix/css/'.self::partnerName.'.'.self::solutionName, true, true);
		CopyDirFiles(__DIR__.'/js/', $_SERVER['DOCUMENT_ROOT'].'/bitrix/js/'.self::partnerName.'.'.self::solutionName, true, true);
		CopyDirFiles(__DIR__.'/images/', $_SERVER['DOCUMENT_ROOT'].'/bitrix/images/'.self::partnerName.'.'.self::solutionName, true, true);
		
		CopyDirFiles(__DIR__.'/tools/', $_SERVER['DOCUMENT_ROOT'].'/bitrix/tools/'.self::partnerName.'.'.self::solutionName, true, true);

		$this->InstallGadget();

		return true;
	}

	function UnInstallFiles(){
		DeleteDirFiles(__DIR__.'/admin/', $_SERVER['DOCUMENT_ROOT'].'/bitrix/admin');
		DeleteDirFilesEx('/bitrix/wizards/'.self::partnerName.'/'.self::solutionName.'/');
		DeleteDirFilesEx('/bitrix/css/'.self::partnerName.'.'.self::solutionName.'/');
		DeleteDirFilesEx('/bitrix/js/'.self::partnerName.'.'.self::solutionName.'/');
		DeleteDirFilesEx('/bitrix/images/'.self::partnerName.'.'.self::solutionName.'/');

		DeleteDirFilesEx('/bitrix/tools/'.self::partnerName.'.'.self::solutionName.'/');

		$this->UnInstallComponents();
		$this->UnInstallGadget();

		return true;
	}

	function InstallGadget(){
		CopyDirFiles(__DIR__.'/gadgets/', $_SERVER['DOCUMENT_ROOT'].'/bitrix/gadgets/', true, true);

		$gadget_id = strtoupper(self::solutionName);
		$gdid = $gadget_id."@".rand();
		if(class_exists('CUserOptions')){
			$arUserOptions = CUserOptions::GetOption('intranet', '~gadgets_admin_index', false, false);
			if(is_array($arUserOptions) && isset($arUserOptions[0])){
				foreach($arUserOptions[0]['GADGETS'] as $tempid => $tempgadget){
					$p = strpos($tempid, '@');
					$gadget_id_tmp = ($p === false ? $tempid : substr($tempid, 0, $p));

					if($gadget_id_tmp == $gadget_id){
						return false;
					}
					if($tempgadget['COLUMN'] == 0){
						++$arUserOptions[0]['GADGETS'][$tempid]['ROW'];
					}
				}
				$arUserOptions[0]['GADGETS'][$gdid] = array('COLUMN' => 0, 'ROW' => 0);
				CUserOptions::SetOption('intranet', '~gadgets_admin_index', $arUserOptions, false, false);
			}
		}

		return true;
	}

	function UnInstallGadget(){
		$gadget_id = strtoupper(self::solutionName);
		if(class_exists('CUserOptions')){
			$arUserOptions = CUserOptions::GetOption('intranet', '~gadgets_admin_index', false, false);
			if(is_array($arUserOptions) && isset($arUserOptions[0])){
				foreach($arUserOptions[0]['GADGETS'] as $tempid => $tempgadget){
					$p = strpos($tempid, '@');
					$gadget_id_tmp = ($p === false ? $tempid : substr($tempid, 0, $p));

					if($gadget_id_tmp == $gadget_id){
						unset($arUserOptions[0]['GADGETS'][$tempid]);
					}
				}
				CUserOptions::SetOption('intranet', '~gadgets_admin_index', $arUserOptions, false, false);
			}
		}

		DeleteDirFilesEx('/bitrix/gadgets/'.self::partnerName.'/'.self::solutionName.'/');

		return true;
	}
	function UnInstallComponents(){
		DeleteDirFilesEx('/bitrix/components/'.self::partnerName.'/auth.'.self::solutionName.'/');
		DeleteDirFilesEx('/bitrix/components/'.self::partnerName.'/basket.'.self::solutionName.'/');
		DeleteDirFilesEx('/bitrix/components/'.self::partnerName.'/catalog.section.list.'.self::solutionName.'/');
		DeleteDirFilesEx('/bitrix/components/'.self::partnerName.'/com.banners.'.self::solutionName.'/');
		DeleteDirFilesEx('/bitrix/components/'.self::partnerName.'/form.'.self::solutionName.'/');
		DeleteDirFilesEx('/bitrix/components/'.self::partnerName.'/instargam.'.self::solutionName.'/');
		DeleteDirFilesEx('/bitrix/components/'.self::partnerName.'/marketing.popup.'.self::solutionName.'/');
		DeleteDirFilesEx('/bitrix/components/'.self::partnerName.'/regionality.list.'.self::solutionName.'/');
		DeleteDirFilesEx('/bitrix/components/'.self::partnerName.'/social.info.'.self::solutionName.'/');
		DeleteDirFilesEx('/bitrix/components/'.self::partnerName.'/tabs.'.self::solutionName.'/');
		DeleteDirFilesEx('/bitrix/components/'.self::partnerName.'/theme.'.self::solutionName.'/');
		DeleteDirFilesEx('/bitrix/components/'.self::partnerName.'/wrapper.block.'.self::solutionName.'/');

		return true;
	}

	function DoInstall(){
		global $APPLICATION, $step;

		$this->InstallFiles();
		$this->InstallDB(false);
		$this->InstallEvents();
		$this->InstallPublic();

		$APPLICATION->IncludeAdminFile(GetMessage('ALLCORP3_INSTALL_TITLE'), $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.$this->MODULE_ID.'/install/step.php');
	}

	function DoUninstall(){
		global $APPLICATION, $step;

		$this->UnInstallDB();
		$this->UnInstallFiles();
		$this->UnInstallEvents();
		$APPLICATION->IncludeAdminFile(GetMessage('ALLCORP3_UNINSTALL_TITLE'), $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.$this->MODULE_ID.'/install/unstep.php');
	}
}