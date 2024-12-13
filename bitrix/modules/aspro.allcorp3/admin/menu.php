<?
use Bitrix\Main\Localization\Loc;

AddEventHandler('main', 'OnBuildGlobalMenu', 'OnBuildGlobalMenuHandlerAllcorp3');
function OnBuildGlobalMenuHandlerAllcorp3(&$arGlobalMenu, &$arModuleMenu){
	if(!defined('ALLCORP3_MENU_INCLUDED')){
		define('ALLCORP3_MENU_INCLUDED', true);

		IncludeModuleLangFile(__FILE__);
		$moduleID = 'aspro.allcorp3';

		$GLOBALS['APPLICATION']->SetAdditionalCss('/bitrix/css/'.$moduleID.'/menu.css');

		if($GLOBALS['APPLICATION']->GetGroupRight($moduleID) >= 'R'){
			$arGenerate = array(
				'text' => GetMessage('ALLCORP3_MENU_GENERATE_FILES_TEXT'),
				'title' => GetMessage('ALLCORP3_MENU_GENERATE_FILES_TITLE'),
				'sort' => 20,
				'icon' => 'seo_menu_icon',
				'page_icon' => 'pi_typography',
				'items_id' => 'gfiles',
				'items' => array(
					array(
						'text' => GetMessage('ALLCORP3_MENU_GENERATE_ROBOTS_TEXT'),
						'title' => GetMessage('ALLCORP3_MENU_GENERATE_ROBOTS_TITLE'),
						'sort' => 20,
						'url' => '/bitrix/admin/'.$moduleID.'_generate_robots.php?mid=main&lang='.LANGUAGE_ID,
						'icon' => '',
						'page_icon' => 'pi_typography',
						'items_id' => 'grobots',
					)
				)
			);

			if(\Bitrix\Main\Loader::includeModule('seo')){
				$arGenerate['items'][] = array(
					'text' => GetMessage('ALLCORP3_MENU_GENERATE_SITEMAP_TEXT'),
					'title' => GetMessage('ALLCORP3_MENU_GENERATE_SITEMAP_TITLE'),
					'sort' => 20,
					'url' => '/bitrix/admin/'.$moduleID.'_generate_sitemap.php?mid=main&lang='.LANGUAGE_ID,
					'icon' => '',
					'page_icon' => 'pi_typography',
					'items_id' => 'gsitemap',
				);
			}

			$arMenu = array(
				'menu_id' => 'global_menu_aspro_allcorp3',
				'text' => GetMessage('ALLCORP3_GLOBAL_MENU_TEXT'),
				'title' => GetMessage('ALLCORP3_GLOBAL_MENU_TITLE'),
				'sort' => 1000,
				'items_id' => 'global_menu_aspro_allcorp3_items',
				'icon' => 'imi_allcorp3',
				'items' => array(
					array(
						'text' => GetMessage('ALLCORP3_MENU_CONTROL_CENTER_TEXT'),
						'title' => GetMessage('ALLCORP3_MENU_CONTROL_CENTER_TITLE'),
						'sort' => 10,
						'url' => '/bitrix/admin/'.$moduleID.'_mc.php?lang='.LANGUAGE_ID,
						'icon' => 'imi_control_center',
						'page_icon' => 'pi_control_center',
						'items_id' => 'control_center',
					),
					array(
						'text' => GetMessage('ALLCORP3_MENU_TYPOGRAPHY_TEXT'),
						'title' => GetMessage('ALLCORP3_MENU_TYPOGRAPHY_TITLE'),
						'sort' => 20,
						'url' => '/bitrix/admin/'.$moduleID.'_options.php?mid=main&lang='.LANGUAGE_ID,
						'icon' => 'imi_typography',
						'page_icon' => 'pi_typography',
						'items_id' => 'main',
						'more_url' => array(
							'/bitrix/admin/'.$moduleID.'_options_tabs.php',
						),
					),
					array(
						'text' => Loc::getMessage('ALLCORP3_MENU_CRM_TEXT'),
						'title' => Loc::getMessage('ALLCORP3_MENU_CRM_TITLE'),
						'sort' => 30,
						'icon' => 'imi_marketing',
						'page_icon' => 'pi_typography',
						'items_id' => 'ncrm',
						"items" => array(
							array(
								'text' => GetMessage('ALLCORP3_MENU_ACLOUD_CRM_TEXT'),
								'title' => GetMessage('ALLCORP3_MENU_ACLOUD_CRM_TITLE'),
								'sort' => 20,
								'url' => '/bitrix/admin/'.$moduleID.'_crm_acloud.php?mid=main&lang='.LANGUAGE_ID,
								'icon' => '',
								'page_icon' => 'pi_typography',
								'items_id' => 'gsitemap',
							),
							array(
								'text' => Loc::getMessage('ALLCORP3_MENU_FLOWLU_CRM_TEXT'),
								'title' => Loc::getMessage('ALLCORP3_MENU_FLOWLU_CRM_TITLE'),
								'sort' => 20,
								'url' => '/bitrix/admin/'.$moduleID.'_crm_flowlu.php?lang='.urlencode(LANGUAGE_ID),
								'icon' => '',
								'page_icon' => 'pi_typography',
								'items_id' => 'crm_flowlu',
							),
							array(
								'text' => Loc::getMessage('ALLCORP3_MENU_AMO_CRM_TEXT'),
								'title' => Loc::getMessage('ALLCORP3_MENU_AMO_CRM_TITLE'),
								'sort' => 10,
								'url' => '/bitrix/admin/'.$moduleID.'_crm_amo.php?lang='.urlencode(LANGUAGE_ID),
								'icon' => '',
								'page_icon' => 'pi_typography',
								'items_id' => 'crm_amo',
							),
						)
					),
					array(
						'text' => GetMessage('ALLCORP3_MENU_DEVELOP_TEXT'),
						'title' => GetMessage('ALLCORP3_MENU_DEVELOP_TITLE'),
						'sort' => 20,
						'url' => '/bitrix/admin/'.$moduleID.'_develop.php?mid=main',
						'icon' => 'util_menu_icon',
						'page_icon' => 'pi_typography',
						'items_id' => 'develop',
					),
					$arGenerate
				),
			);

			// 		$arGenerate,
			// 		// array(
			// 		// 	'text' => Loc::getMessage('ASPRO_ALLCORP3_MENU_GS_TEXT'),
			// 		// 	'title' => Loc::getMessage('ASPRO_ALLCORP3_MENU_GS_TITLE'),
			// 		// 	'sort' => 1000,
			// 		// 	'url' => '/bitrix/admin/'.$moduleID.'_gs.php?lang='.urlencode(LANGUAGE_ID),
			// 		// 	'icon' => 'imi_gs',
			// 		// 	'page_icon' => 'pi_gs',
			// 		// 	'items_id' => 'gs',
			// 		// ),
			// 	),
			// );

			if (\Bitrix\Main\Loader::includeModule('form')) {
				$arMenu['items'][] = [
					'title' => GetMessage('ALLCORP3_MENU_GENERATE_FORM_TITLE'),
					'text' => GetMessage('ALLCORP3_MENU_GENERATE_FORM_TEXT'),
					'sort' => 30,
					'icon' => '',
					'page_icon' => '',
					'items_id' => 'form',
					'url' => '/bitrix/admin/'.$moduleID.'_generate_web_forms.php?mid=main',
				];
			}

			if(!isset($arGlobalMenu['global_menu_aspro'])){
				$arGlobalMenu['global_menu_aspro'] = array(
					'menu_id' => 'global_menu_aspro',
					'text' => GetMessage('ASPRO_ALLCORP3_GLOBAL_ASPRO_MENU_TEXT'),
					'title' => GetMessage('ASPRO_ALLCORP3_GLOBAL_ASPRO_MENU_TITLE'),
					'sort' => 1000,
					'items_id' => 'global_menu_aspro_items',
				);
			}

			$arGlobalMenu['global_menu_aspro']['items'][$moduleID] = $arMenu;
		}
	}
}
?>