<?
if (!defined('ALLCORP3_MODULE_ID')) {
	define('ALLCORP3_MODULE_ID', 'aspro.allcorp3');
}

use \Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

if (!class_exists('CAllcorp3Regionality')) {
	class CAllcorp3Regionality
	{
		public static $arSeoMarks = array(
			'#REGION_NAME#' => 'NAME',
			'#REGION_ID#' => 'ID',
			'#REGION_NAME_DECLINE_RP#' => 'PROPERTY_REGION_NAME_DECLINE_RP_VALUE',
			'#REGION_NAME_DECLINE_PP#' => 'PROPERTY_REGION_NAME_DECLINE_PP_VALUE',
			'#REGION_NAME_DECLINE_TP#' => 'PROPERTY_REGION_NAME_DECLINE_TP_VALUE',
		);

		public static function checkUseRegionality()
		{
			if(\Bitrix\Main\Loader::includeModule(ALLCORP3_MODULE_ID))
			{
				return CAllcorp3::GetFrontParametrValue('USE_REGIONALITY');
			}
			return 'N';
		}

		public static function getRegionIBlockID()
		{
			static $iRegionIBlockID;
			if($iRegionIBlockID === NULL)
			{
				if(isset(CAllcorp3Cache::$arIBlocks[SITE_ID]['aspro_allcorp3_regionality']['aspro_allcorp3_regions'][0]) && CAllcorp3Cache::$arIBlocks[SITE_ID]['aspro_allcorp3_regionality']['aspro_allcorp3_regions'][0])
				{
					$iRegionIBlockID = CAllcorp3Cache::$arIBlocks[SITE_ID]['aspro_allcorp3_regionality']['aspro_allcorp3_regions'][0];
				}
				else
				{
					return;
				}
			}
			return $iRegionIBlockID;
		}

		public static function addSeoMarks($arMarks = array())
		{
			self::$arSeoMarks = array_merge(self::$arSeoMarks, $arMarks);
		}

		public static function replaceSeoMarks()
		{
			global $APPLICATION, $arSite, $arRegion;

			$page_title = $APPLICATION->GetTitle();
			$page_seo_title = ((strlen($APPLICATION->GetPageProperty('title')) > 1) ? $APPLICATION->GetPageProperty('title') : $page_title);

			if($arRegion && $page_title)
			{
				foreach(CAllcorp3Regionality::$arSeoMarks as $mark => $field)
				{
					if(strpos($page_title, $mark) !== false)
						$page_title = str_replace($mark, $arRegion[$field], $page_title);
					if(strpos($page_seo_title, $mark) !== false)
						$page_seo_title = str_replace($mark, $arRegion[$field], $page_seo_title);
				}
				if(!CAllcorp3::IsMainPage())
				{
					$bShowSiteName = (\Bitrix\Main\Config\Option::get(ALLCORP3_MODULE_ID, "HIDE_SITE_NAME_TITLE", "N") == "N");
					$sPostfix = ($bShowSiteName ? ' - '.$arSite['SITE_NAME'] : '');

					$APPLICATION->SetPageProperty("title", $page_seo_title.$sPostfix);
					$APPLICATION->SetTitle($page_title);
				}
				else
				{
					if(!empty($page_seo_title))
						$APPLICATION->SetPageProperty("title", $page_seo_title);
					else
						$APPLICATION->SetPageProperty("title", $arSite['SITE_NAME']);

					if(!empty($page_title))
						$APPLICATION->SetTitle($title);
					else
						$APPLICATION->SetTitle($arSite['SITE_NAME']);
				}
			}
			return true;
		}

		public static function getRegions()
		{
			static $arRegions;

			if($arRegions === NULL)
			{
				$arRegions = array();
				if($iRegionIBlockID = self::getRegionIBlockID())
				{

					if(self::checkUseRegionality() == 'N')
						return false;

					$cache = new CPHPCache();
					$cache_time = 86400;
					$cache_path = __CLASS__.'/'.__FUNCTION__;

					$cache_id = 'aspro_allcorp3_regions'.$iRegionIBlockID;
					if(\Bitrix\Main\Config\Option::get('main', 'component_cache_on', 'Y') == 'Y' && $cache->InitCache($cache_time, $cache_id, $cache_path))
					{
						$res = $cache->GetVars();
						$arRegions = $res['arRegions'];
					}
					else
					{
						// get all items
						$arMainProps = array('DEFAULT', 'DOMAINS', 'COMPANY_TITLE', 'MAIN_DOMAIN', 'FAVORIT_LOCATION', 'PHONES', 'PRICES_LINK', 'LOCATION_LINK', 'STORES_LINK', 'REGION_NAME_DECLINE_RP', 'REGION_NAME_DECLINE_PP', 'REGION_NAME_DECLINE_TP', 'SORT_REGION_PRICE', 'ADDRESS', 'EMAIL', 'SHCEDULE');
						$arFilter = array('ACTIVE' => 'Y', 'IBLOCK_ID' => $iRegionIBlockID);
						$arSelect = array('ID', 'CODE', 'NAME', 'IBLOCK_ID', 'IBLOCK_SECTION_ID', 'DETAIL_TEXT', 'PREVIEW_TEXT');
						foreach($arMainProps as $code)
						{
							$arSelect[] = 'PROPERTY_'.$code;
						}

						// property code need start REGION_TAG_ for auto add for cache
						$arProps = array();
						$rsProperty = CIBlockProperty::GetList(array(), array_merge($arFilter, array('CODE' => 'REGION_TAG_%')));
						while($arProp = $rsProperty->Fetch())
						{
							$arSelect[] = 'PROPERTY_'.$arProp['CODE'];
						}

						foreach(GetModuleEvents(ALLCORP3_MODULE_ID, 'OnAsproRegionalityAddSelectFieldsAndProps', true) as $arEvent) // event for add to select in region getlist elements
							ExecuteModuleEventEx($arEvent, array(&$arSelect));

						// $arItems = CAllcorp3Cache::CIBLockElement_GetList(array('SORT' => 'ASC', 'NAME' => 'ASC', 'CACHE' => array('TAG' => CAllcorp3Cache::GetIBlockCacheTag($iRegionIBlockID), 'GROUP' => 'ID')), $arFilter, false, false, $arSelect);

						$arItems = array();
						$dbRes = \CIBLockElement::GetList(array('SORT' => 'ASC', 'NAME' => 'ASC'), $arFilter, false, false, array('ID', 'NAME', 'IBLOCK_ID', 'IBLOCK_SECTION_ID', 'DETAIL_TEXT'));
						while($ob = $dbRes->GetNextElement()){
							$arFields = $ob->GetFields();
							$arProps = $ob->GetProperties();
							$arItem = array();
							foreach($arFields as $code => $value){
								if(in_array($code, $arSelect)){
									$arItem[$code] = $value;
								}
							}
							foreach($arProps as $code => $arProperty){
								if(in_array('PROPERTY_'.$code, $arSelect)){
									$arItem['PROPERTY_'.$code.'_VALUE'] = $arProperty['~VALUE'];
									if(isset($arProperty['WITH_DESCRIPTION']) && $arProperty['WITH_DESCRIPTION'] == "Y"){
									    $arItem['PROPERTY_'.$code.'_DESCRIPTION'] = $arProperty['~DESCRIPTION'];
									}
								}
							}
							$arItems[$arItem['ID']] = $arItem;
						}

						foreach(GetModuleEvents(ALLCORP3_MODULE_ID, 'OnAsproRegionalityGetElements', true) as $arEvent) // event for manipulation with region elements
							ExecuteModuleEventEx($arEvent, array(&$arItems));

						if($arItems)
						{
							foreach($arItems as $key => $arItem)
							{
								if (
									(!isset($arItem['PROPERTY_MAIN_DOMAIN']) || !$arItem['PROPERTY_MAIN_DOMAIN']) 
									&& (isset($arItem['PROPERTY_DEFAULT_VALUE']) && $arItem['PROPERTY_DEFAULT_VALUE'] === 'Y')
								) {
									$arItems[$key]['PROPERTY_MAIN_DOMAIN'] = $_SERVER['HTTP_HOST'];
								}

								//domains props
								if(!is_array($arItem['PROPERTY_DOMAINS_VALUE']))
									$arItem['PROPERTY_DOMAINS_VALUE'] = (array)$arItem['PROPERTY_DOMAINS_VALUE'];
								$arItems[$key]['LIST_DOMAINS'] = array_merge((array)$arItem['PROPERTY_MAIN_DOMAIN_VALUE'], $arItem['PROPERTY_DOMAINS_VALUE']);
								unset($arItems[$key]['PROPERTY_DOMAINS_VALUE']);
								unset($arItems[$key]['PROPERTY_DOMAINS_VALUE_ID']);

								//email props
								if(!is_array($arItem['PROPERTY_EMAIL_VALUE']))
									$arItems[$key]['PROPERTY_EMAIL_VALUE'] = (array)$arItem['PROPERTY_EMAIL_VALUE'];

								//phones props
								if(!is_array($arItem['PROPERTY_PHONES_VALUE']))
									$arItem['PROPERTY_PHONES_VALUE'] = (array)$arItem['PROPERTY_PHONES_VALUE'];
								$arItems[$key]['PHONES'] = $arItem['PROPERTY_PHONES_VALUE'];
								unset($arItems[$key]['PROPERTY_PHONES_VALUE']);
								unset($arItems[$key]['PROPERTY_PHONES_VALUE_ID']);
							}
							$arRegions = $arItems;
							unset($arItems);

							$cache->StartDataCache($cache_time, $cache_id, $cache_path);

							global $CACHE_MANAGER;
							$CACHE_MANAGER->StartTagCache($cache_path);
							$CACHE_MANAGER->RegisterTag($cache_id);
							$CACHE_MANAGER->EndTagCache();

							$cache->EndDataCache(
								array(
									"arRegions" => $arRegions
								)
							);
						}
						else
						{
							return;
						}
					}
				}
				else
				{
					return;
				}
			}
			return $arRegions;
		}

		public static function getRealRegionByIP()
		{
			static $arRegion;

			if(!isset($arRegion)){
				$arRegion = false;

				if($arRegions = self::getRegions()){
					// get ip
					$ip = $_SERVER["REMOTE_ADDR"];
					if(!empty($_SERVER["HTTP_X_REAL_IP"])){
						$ip = $_SERVER["HTTP_X_REAL_IP"];
					}

					// get city
					$city = false;
					if(class_exists('\Bitrix\Main\Service\GeoIp\Manager')){
						if(!isset($_SESSION['GEOIP']['cityName']) || !$_SESSION['GEOIP']['cityName'])
						{
							// by bitrix api
							$obBitrixGeoIPResult = \Bitrix\Main\Service\GeoIp\Manager::getDataResult($ip, 'ru');
							if($obBitrixGeoIPResult){
								if($obResult = $obBitrixGeoIPResult->getGeoData()){
									$_SESSION['GEOIP'] = get_object_vars($obResult);
									$city = isset($_SESSION['GEOIP']['cityName']) && $_SESSION['GEOIP']['cityName'] ? $_SESSION['GEOIP']['cityName'] : '';
								}
							}
						}
						else
							$city = isset($_SESSION['GEOIP']['cityName']) && $_SESSION['GEOIP']['cityName'] ? $_SESSION['GEOIP']['cityName'] : '';
					}
					if(!$city){
						if(\Bitrix\Main\Loader::includeModule('altasib.geoip')){
							// by altasib api
							if($arData = ALX_GeoIP::GetAddr($ip)){
								$city = isset($arData['city']) && $arData['city'] ? $arData['city'] : '';
							}
						}
					}

					// search by city name
					if($city){
						foreach($arRegions as $key => $arItem){
							if($city === $arItem['NAME']){
								$arRegion = $arItem;
							}
						}
					}
				}
			}

			return $arRegion;
		}

		public static function getCurrentRegion()
		{
			static $arRegion;

			if(!isset($arRegion)){
				$arRegion = false;


				if($arRegions = self::getRegions()){
					global $arTheme;

					if(!$arTheme){
						$arTheme = CAllcorp3::GetFrontParametrsValues(SITE_ID);
					}

					// search current region
					if($arTheme['REGIONALITY_TYPE'] === 'ONE_DOMAIN'){
						// search by cookie value
						if(isset($_COOKIE['current_region']) && $_COOKIE['current_region']){
							if(isset($arRegions[$_COOKIE['current_region']]) && $arRegions[$_COOKIE['current_region']]){
								return $arRegion = $arRegions[$_COOKIE['current_region']];
							}
						}
					}

					// search by domain name
					if(!$arRegion){
						if($arTheme['REGIONALITY_TYPE'] !== 'ONE_DOMAIN'){
							foreach($arRegions as $arItem){
								if(in_array($_SERVER['SERVER_NAME'], $arItem['LIST_DOMAINS']) || in_array($_SERVER['HTTP_HOST'], $arItem['LIST_DOMAINS'])){
									$arRegion = $arItem;
									break;
								}
							}
						}
					}

					// region not finded, set default
					if(!$arRegion){
						foreach($arRegions as $arItem){
							if($arItem['PROPERTY_DEFAULT_VALUE'] === 'Y'){
								$arRegion = $arItem;
								break;
							}
						}
					}

					// region not finded, set first region
					if(!$arRegion){
						$arRegion = reset($arRegions);
					}
				}
			}

			return $arRegion;
		}
	}
}