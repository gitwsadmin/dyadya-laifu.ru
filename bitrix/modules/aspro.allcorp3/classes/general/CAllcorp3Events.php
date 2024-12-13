<?
use \Bitrix\Main\Localization\Loc,
	Bitrix\Main\Application,
	\Bitrix\Main\Config\Option,
	Bitrix\Main\IO\File,
	Bitrix\Main\Page\Asset,
	Aspro\Allcorp3\CRM;

Loc::loadMessages(__FILE__);

class CAllcorp3Events{
	const MODULE_ID = \CAllcorp3::moduleID;

	public static function OnFindSocialservicesUserHandler($arFields){
		// check for user with email
		if($arFields['EMAIL'])
		{
			$arUser = CUser::GetList($by = 'ID', $ord = 'ASC', array('EMAIL' => $arFields['EMAIL'], 'ACTIVE' => 'Y'), array('NAV_PARAMS' => array("nTopCount" => "1")))->fetch();
			if($arUser)
			{
				if($arFields['PERSONAL_PHOTO'])
				{

					/*if(!$arUser['PERSONAL_PHOTO'])
					{
						$arUpdateFields = Array(
							'PERSONAL_PHOTO' => $arFields['PERSONAL_PHOTO'],
						);
						$user->Update($arUser['ID'], $arUpdateFields);
					}
					else
					{*/
						$code = 'UF_'.strtoupper($arFields['EXTERNAL_AUTH_ID']);
						$arUserFieldUserImg = CUserTypeEntity::GetList(array(), array('ENTITY_ID' => 'USER', 'FIELD_NAME' => $code))->Fetch();
						if(!$arUserFieldUserImg)
						{
							$arFieldsUser = array(
								"FIELD_NAME" => $code,
								"USER_TYPE_ID" => "file",
								"XML_ID" => $code,
								"SORT" => 100,
								"MULTIPLE" => "N",
								"MANDATORY" => "N",
								"SHOW_FILTER" => "N",
								"SHOW_IN_LIST" => "Y",
								"EDIT_IN_LIST" => "Y",
								"IS_SEARCHABLE" => "N",
								"SETTINGS" => array(
									"DISPLAY" => "LIST",
									"LIST_HEIGHT" => 5,
								)
							);
							$arLangs = array(
								"EDIT_FORM_LABEL" => array(
									"ru" => $code,
									"en" => $code,
								),
								"LIST_COLUMN_LABEL" => array(
									"ru" => $code,
									"en" => $code,
								)
							);

							$ob = new CUserTypeEntity();
							$FIELD_ID = $ob->Add(array_merge($arFieldsUser, array('ENTITY_ID' => 'USER'), $arLangs));

						}
						$user = new CUser;
						$arUpdateFields = Array(
							$code => $arFields['PERSONAL_PHOTO'],
						);
						$user->Update($arUser['ID'], $arUpdateFields);
					//}
				}
				return $arUser['ID'];
			}
		}
		return false;
	}

	public static function OnRegionUpdateHandler($arFields){
		$arIBlock = CIBlock::GetList(array(), array("ID" => $arFields["IBLOCK_ID"]))->Fetch();
		if(\Bitrix\Main\Loader::includeModule(self::MODULE_ID))
		{
			if(isset(CAllcorp3Cache::$arIBlocks[$arIBlock['LID']]['aspro_allcorp3_regionality']['aspro_allcorp3_regions'][0]) && CAllcorp3Cache::$arIBlocks[$arIBlock['LID']]['aspro_allcorp3_regionality']['aspro_allcorp3_regions'][0])
				$iRegionIBlockID = CAllcorp3Cache::$arIBlocks[$arIBlock['LID']]['aspro_allcorp3_regionality']['aspro_allcorp3_regions'][0];
			else
				return;
			if($iRegionIBlockID == $arFields['IBLOCK_ID'])
			{
				$arSite = CSite::GetList($by, $sort, array("ACTIVE"=>"Y", "ID" =>  $arIBlock['LID']))->Fetch();
				$arSite['DIR'] = str_replace('//', '/', '/'.$arSite['DIR']);
				if(!strlen($arSite['DOC_ROOT'])){
					$arSite['DOC_ROOT'] = $_SERVER['DOCUMENT_ROOT'];
				}
				$arSite['DOC_ROOT'] = str_replace('//', '/', $arSite['DOC_ROOT'].'/');
				$siteDir = str_replace('//', '/', $arSite['DOC_ROOT'].$arSite['DIR']);

				$arProperty = CIBlockElement::GetProperty($arFields["IBLOCK_ID"], $arFields["ID"], "sort", "asc", array("CODE" => "MAIN_DOMAIN"))->Fetch();
				$xml_file = (isset($arFields["SITE_MAP"]) && $arFields["SITE_MAP"] ? $arFields["SITE_MAP"] : "sitemap.xml");
				if($arProperty["VALUE"])
				{
					if(file_exists($siteDir.'robots.txt'))
					{
						// @copy($siteDir.'robots.txt', $siteDir.'aspro_regions/robots/robots_'.$arProperty["VALUE"].'.txt');
						CopyDirFiles($siteDir.'robots.txt', $siteDir.'aspro_regions/robots/robots_'.$arProperty["VALUE"].'.txt', true, true);
						$arFile = file($siteDir.'aspro_regions/robots/robots_'.$arProperty["VALUE"].'.txt');
						$bHasHostRobots = $bHasHostSitemap = false;
						foreach($arFile as $key => $str)
						{
							if(strpos($str, "Host" ) !== false)
							{
								$arFile[$key] = "Host: ".(CMain::isHTTPS() ? "https://" : "http://").$arProperty["VALUE"]."\r\n";
								$bHasHostRobots = true;
							}
							if(strpos($str, "Sitemap" ) !== false)
							{
								$arFile[$key] = "Sitemap: ".(CMain::isHTTPS() ? "https://" : "http://").$arProperty["VALUE"]."/".$xml_file."\r\n";
								$bHasHostSitemap = true;
							}
						}

						if(!$bHasHostRobots)
							$arFile[] = "\r\nHost: ".(CMain::isHTTPS() ? "https://" : "http://").$arProperty["VALUE"];
						if(!$bHasHostSitemap && \Bitrix\Main\Loader::includeModule('seo') && file_exists($siteDir.$xml_file))
							$arFile[] = "\r\nSitemap: ".(CMain::isHTTPS() ? "https://" : "http://").$arProperty["VALUE"]."/".$xml_file;

						$strr = implode("", $arFile);
						file_put_contents($siteDir.'aspro_regions/robots/robots_'.$arProperty["VALUE"].'.txt', $strr);
					}
				}
			}
		}
	}

	public static function OnAfterSocServUserAddHandler( $arFields ){
		if($arFields["EMAIL"]){
			global $USER;
			$userEmail=$USER->GetEmail();
			$email=(is_null($userEmail) ? $arFields["EMAIL"] : $userEmail );
			//$resUser = CUser::GetList(($by="ID"), ($order="asc"), array("=EMAIL" => $arFields["EMAIL"]), array("FIELDS" => array("ID")));
			$resUser = CUser::GetList(($by="ID"), ($order="asc"), array("=EMAIL" => $email), array("FIELDS" => array("ID")));
			$arUserAlreadyExist = $resUser->Fetch();

			if($arUserAlreadyExist["ID"]){
				\Bitrix\Main\Loader::includeModule('socialservices');
				global $USER;
				if($resUser->SelectedRowsCount()>1){
					CSocServAuthDB::Update($arFields["ID"], array("USER_ID" => $arUserAlreadyExist["ID"], "CAN_DELETE" => "Y"));
					CUser::Delete($arFields["USER_ID"]);
					$USER->Authorize($arUserAlreadyExist["ID"]);
				}else{
					$def_group = COption::GetOptionString("main", "new_user_registration_def_group", "");
					if($def_group!=""){
						$GROUP_ID = explode(",", $def_group);
						$arPolicy = $USER->GetGroupPolicy($GROUP_ID);
					}else{
						$arPolicy = $USER->GetGroupPolicy(array());
					}
					$password_min_length = (int)$arPolicy["PASSWORD_LENGTH"];
					if($password_min_length <= 0)
						$password_min_length = 6;
					$password_chars = array(
						"abcdefghijklnmopqrstuvwxyz",
						"ABCDEFGHIJKLNMOPQRSTUVWXYZ",
						"0123456789",
					);
					if($arPolicy["PASSWORD_PUNCTUATION"] === "Y")
						$password_chars[] = ",.<>/?;:'\"[]{}\|`~!@#\$%^&*()-_+=";
					$NEW_PASSWORD = $NEW_PASSWORD_CONFIRM = randString($password_min_length+2, $password_chars);

					$user = new CUser;
					$arFieldsUser = Array(
					  "NAME"              => $arFields["NAME"],
					  "LAST_NAME"         => $arFields["LAST_NAME"],
					  "EMAIL"             => $arFields["EMAIL"],
					  "LOGIN"             => $arFields["EMAIL"],
					  "GROUP_ID"          => $GROUP_ID,
					  "PASSWORD"          => $NEW_PASSWORD,
					  "CONFIRM_PASSWORD"  => $NEW_PASSWORD_CONFIRM,
					);
					unset($arFields["LOGIN"]);
					unset($arFields["PASSWORD"]);
					unset($arFields["EXTERNAL_AUTH_ID"]);
					unset($arFields["XML_ID"]);
					$arAddFields = array();
					$arAddFields = array_merge($arFieldsUser, $arFields);
					if(isset($arAddFields["PERSONAL_PHOTO"]) && $arAddFields["PERSONAL_PHOTO"])
					{
						$arPic = CFile::MakeFileArray($arFields["PERSONAL_PHOTO"]);
						$arAddFields["PERSONAL_PHOTO"] = $arPic;
					}

					//if($arUserAlreadyExist["ID"]!=$arFields["USER_ID"]){
						$ID = $user->Add($arAddFields);
						//$ID = $user->Add($arFieldsUser);
						CSocServAuthDB::Update($arFields["ID"], array("USER_ID" => $ID, "CAN_DELETE" => "Y"));
						CUser::Delete($arFields["USER_ID"]);
						$USER->Authorize($ID);
					//}
				}
			}
		}
	}

	public static function OnBeforeUserUpdateHandler(&$arFields){
		$bTmpUser = false;
		$bAdminSection = (defined('ADMIN_SECTION') && ADMIN_SECTION === true);

		if(strlen($arFields['NAME']))
			$arFields['NAME'] = trim($arFields['NAME']);

		if($bAdminSection)
	    {
	    	// include CMainPage
	        require_once($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/main/include/mainpage.php");
	        
			// get site_id by host
			$mainPage = new \CMainPage;
	        $siteID = $mainPage->GetSiteByHost();
	        if (!$siteID) {
	            $siteID = "s1";
			}
	    }
		$siteID = SITE_ID;
		$sChangeLogin = COption::GetOptionString(\CAllcorp3::moduleID, 'LOGIN_EQUAL_EMAIL', 'N', $siteID);

		if(strlen($arFields['NAME']) && !strlen($arFields['LAST_NAME']) && !strlen($arFields['SECOND_NAME']))
		{
			if($siteID == 'ru')
				$siteID = 's1';
			if($bAdminSection)
				$bOneFIO = COption::GetOptionString(\CAllcorp3::moduleID, 'PERSONAL_ONEFIO', 'Y', $siteID);
			else
			{
				$arFrontParametrs = CAllcorp3::GetFrontParametrsValues($siteID);
				$bOneFIO = $arFrontParametrs['PERSONAL_ONEFIO'] !== 'N';
			}

			if($bOneFIO)
			{
				$arName = explode(' ', $arFields['NAME']);
				if($arName)
				{
					$arFields['NAME'] = '';
					$arFields['SECOND_NAME'] = '';
					foreach($arName as $i => $name)
					{
						if(!$i)
						{
							$arFields['LAST_NAME'] = $name;
						}
						else
						{
							if(!strlen($arFields['NAME']))
								$arFields['NAME'] = $name;

							elseif(!strlen($arFields['SECOND_NAME']))
								$arFields['SECOND_NAME'] = $name;

						}
					}
				}
			}
		}
		if(strlen($arFields["EMAIL"]))
		{
			if(!$bAdminSection)
			{
				$arFrontParametrs = CAllcorp3::GetFrontParametrsValues($siteID);
				$sChangeLogin = $arFrontParametrs['LOGIN_EQUAL_EMAIL'];
			}
			if($sChangeLogin != "N")
			{
				$bEmailError = false;

				if(\Bitrix\Main\Config\Option::get('main', 'new_user_email_uniq_check', 'N') == 'Y')
				{
					$rsUser = CUser::GetList($by = "ID", $order = "ASC", array("=EMAIL" => $arFields["EMAIL"], "!ID" => $arFields["ID"]));
					if(!$bEmailError = intval($rsUser->SelectedRowsCount()) > 0)
					{
						$rsUser = CUser::GetList($by = "ID", $order = "ASC", array("LOGIN_EQUAL" => $arFields["EMAIL"], "!ID" => $arFields["ID"]));
						$bEmailError = intval($rsUser->SelectedRowsCount()) > 0;
					}
				}

				if($bEmailError){
					global $APPLICATION;
					$APPLICATION->throwException(Loc::getMessage("EMAIL_IS_ALREADY_EXISTS", array("#EMAIL#" => $arFields["EMAIL"])));
					return false;
				}
				else{
					// !admin
					if (!isset($GLOBALS["USER"]) || !is_object($GLOBALS["USER"])){
						$bTmpUser = True;
						$GLOBALS["USER"] = new \CUser;
					}

					if($bAdminSection)
					{
						if(isset($arFields['ID']) && $arFields['ID'])
						{
							if(!in_array(1, CUser::GetUserGroup($arFields['ID'])))
								$arFields['LOGIN'] = $arFields['EMAIL'];
						}
						elseif(isset($arFields['GROUP_ID']) && $arFields['GROUP_ID'])
						{
							$arUserGroups = array();
							$arTmpGroups = (array)$arFields['GROUP_ID'];
							foreach($arTmpGroups as $arGroup)
							{
								if(is_array($arGroup))
									$arUserGroups[] = $arGroup['GROUP_ID'];
								else
									$arUserGroups[] = $arGroup;
							}

							if(count(array_intersect($arUserGroups, array(1)))<=0)
								$arFields['LOGIN'] = $arFields['EMAIL'];
						}
						else
							$arFields['LOGIN'] = $arFields['EMAIL'];
					}
					else
					{
						if(!$GLOBALS['USER']->IsAdmin())
							$arFields["LOGIN"] = $arFields["EMAIL"];
					}
				}
			}
		}

		if ($bTmpUser)
			unset($GLOBALS["USER"]);

		return $arFields;
	}

	public static function OnAfterUserRegisterHandler($arFields){

	}

	public static function OnBeforeEventAddHandler(&$event, &$lid, &$arFields, &$message_id){
		if(\Bitrix\Main\Loader::includeModule(self::MODULE_ID))
		{
			if($arCurrentRegion = CAllcorp3Regionality::getCurrentRegion())
			{
				$arFields['REGION_ID'] = $arCurrentRegion['ID'];
				$arFields['REGION_MAIN_DOMAIN'] = $arCurrentRegion['PROPERTY_MAIN_DOMAIN_VALUE'];
				$arFields['REGION_MAIN_DOMAIN_RAW'] = (CMain::IsHTTPS() ? 'https://' : 'http://').$arCurrentRegion['PROPERTY_MAIN_DOMAIN_VALUE'];
				$arFields['REGION_ADDRESS'] = $arCurrentRegion['PROPERTY_ADDRESS_VALUE']['TEXT'];
				$arFields['REGION_EMAIL'] = implode(', ', $arCurrentRegion['PROPERTY_EMAIL_VALUE']);
				$arFields['REGION_PHONE'] = implode(', ', $arCurrentRegion['PHONES']);

				$arTagSeoMarks = array();
				foreach($arCurrentRegion as $key => $value)
				{
					if(strpos($key, 'PROPERTY_REGION_TAG') !== false && strpos($key, '_VALUE_ID') === false)
					{
						$tag_name = str_replace(array('PROPERTY_', '_VALUE'), '', $key);
						$arTagSeoMarks['#'.$tag_name.'#'] = $key;
					}
				}

				if($arTagSeoMarks)
					CAllcorp3Regionality::addSeoMarks($arTagSeoMarks);

				foreach(CAllcorp3Regionality::$arSeoMarks as $mark => $field)
				{
					$mark = str_replace('#', '', $mark);
					if(is_array($arCurrentRegion[$field]))
						$arFields[$mark] = $arCurrentRegion[$field]['TEXT'];
					else
						$arFields[$mark] = $arCurrentRegion[$field];
				}
			}
		}
	}

	public static function OnEndBufferContentHandler(&$content)
	{
		if(!defined('ADMIN_SECTION'))
		{			
			global $SECTION_BNR_CONTENT, $arRegion;
			if(preg_match_all('/<\s*link\s+[^\>]*rel\s*=\s*[\'"](canonical|next|prev)[\'"][^\>]*>/i'.BX_UTF_PCRE_MODIFIER, $content, $arMatches)){

			    $links = implode(
				'',
				array_map(
				    function($match){
					if(preg_match('/href\s*=\s*[\'"]([^\'"]*)[\'"]/i'.BX_UTF_PCRE_MODIFIER, $match, $arMatch)){
					    return preg_replace('/href\s*=\s*[\'"]([^\'"]*)[\'"]/i'.BX_UTF_PCRE_MODIFIER, 'href="'.(preg_replace('/(http[s]*:\/\/|^)([^\/]*[\/]?)(.*)/i'.BX_UTF_PCRE_MODIFIER, (CMain::IsHTTPS() ? 'https://' : 'http://').$_SERVER['SERVER_NAME'].'/${3}', $arMatch[1])).'"', $match);
					}

					return $match;
				    },
				    array_values($arMatches[0])
				)
			    );

			    $content = preg_replace(
				array(
				    '/<\s*link\s+[^\>]*rel\s*=\s*[\'"](canonical|next|prev)[\'"][^\>]*>/i'.BX_UTF_PCRE_MODIFIER,
				    '/<\s*head(\s+[^\>]*|)>/i'.BX_UTF_PCRE_MODIFIER,
				),
				array(
				    '',
				    '${0}'.$links,
				),
				$content
			    );
			}

			//replace text/javascript for html5 validation w3c
			$content = str_replace(' type="text/javascript"', '', $content);
			$content = str_replace(' type=\'text/javascript\'', '', $content);
			$content = str_replace(' type="text/css"', '', $content);
			$content = str_replace(' type=\'text/css\'', '', $content);
			$content = str_replace(' charset="utf-8"', '', $content);

			if(defined('ASPRO_USE_ONENDBUFFERCONTENT_HANDLER') && ASPRO_USE_ONENDBUFFERCONTENT_HANDLER == 'Y')
			{
				if($SECTION_BNR_CONTENT)
				{
					/*$start = strpos($content, '<!--title_content-->');
					if($start>0)
					{
						$end = strpos($content, '<!--end-title_content-->');

						if(($end>0) && ($end>$start))
						{
							if(defined("BX_UTF") && BX_UTF === true && !CAllcorp3::checkVersionModule('20.100.0', 'main'))
								$content = CAllcorp3::utf8_substr_replace($content, "", $start, $end-$start);
							else
								$content = substr_replace($content, "", $start, $end-$start);
						}
					}*/
					$pattern = '/<!--title_content-->(.*?)<!--end-title_content-->/ism';
					$content = preg_replace($pattern, '', $content);
					$content = str_replace("body class=\"", "body class=\"with_banners ", $content);
				}

				if (defined('ASPRO_PAGE_WO_TITLE') && ASPRO_PAGE_WO_TITLE) {
					$pattern = '/<!--h1_content-->(.*?)<!--\/h1_content-->/ism';
					$content = preg_replace($pattern, '', $content);
					$content = str_replace("body class=\"", "body class=\"block-wo-title ", $content);
				}

			}
			foreach(CAllcorp3Regionality::$arSeoMarks as $mark => $field)
			{
				if(strpos($content, $mark) !== false)
				{
					if($arRegion)
					{
						if(is_array($arRegion[$field]))
							$content = str_replace($mark, $arRegion[$field]['TEXT'], $content);
						else
							$content = str_replace($mark, $arRegion[$field], $content);
					}
					else
						$content = str_replace($mark, '', $content);
				}
			}

			// lazyload
			global $arTheme;
			if($GLOBALS['_USE_LAZY_LOAD_ALLCORP3_']){
				if((strpos($_SERVER['REQUEST_URI'], '/bitrix/components/') === false && strpos($_SERVER['REQUEST_URI'], '/bitrix/tools/') === false && strpos($_SERVER['REQUEST_URI'], '/bitrix/admin/') === false)){
					// add lazyload attribyte for each <img> that does not contain data-src
					$tmpContent = preg_replace('/<img ((?![^>]*\bdata-src\b)[^>]*>)/i'.BX_UTF_PCRE_MODIFIER, '<img data-lazyload ${1}', $content);
					if(isset($tmpContent)){
						$content = $tmpContent;
						$content = preg_replace('/(<img data-lazyload (?![^>]*\bsrcset\b)[^>]*)src=/i'.BX_UTF_PCRE_MODIFIER, '${1} src="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==" data-src=', $content);
					}

					$arTags = array(
						'div',
						'a',
						'li',
						'span',
						'tr',
						'td',
					);
					$sTags = implode('|', $arTags);
					$bgPatterns = array(
						'/<('.$sTags.')((?![^>]*\bdata-src\b)[^>]*background-image:\s*url\s*)/i'.BX_UTF_PCRE_MODIFIER,
						'/<('.$sTags.')((?![^>]*\bdata-src\b)[^>]*background:.*?url\s*)/i'.BX_UTF_PCRE_MODIFIER,
					);
					$tmpContent = preg_replace($bgPatterns, '<${1} data-lazyload ${2}', $content);
					if(isset($tmpContent)){
						$content = $tmpContent;
						$bgPattern = '/
						(						# group 1 = tag content part before attr background-image
							< 					# open tag
							('.$sTags.')\s+ 	# group 2 = tag name 
							data-lazyload\s+ 	# attr data-lazyload
							[^>]*
						)
						background-image:\s*url\s*
						\(						# open (
							\s*					# any spaces
							[\'"]?				# \' or "
							(					# group 3 = value of url without quotes and spaces
								[^\)\'"\s]*
							)
							[\'"]?				# \' or "
							\s*					# any spaces
						\)						# close )
						(						# group 4 = tag content part after attr background-image
							[^>]*
						)
						/ix'.BX_UTF_PCRE_MODIFIER;
						$tmpContent = preg_replace($bgPattern, '${1}background-image:url(data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==)${4} data-bg="${3}"', $content);

						if(isset($tmpContent)){
							$content = $tmpContent;
							$content = preg_replace('/(<\w+ data-lazyload [^>]*?)class=([\'"])(?![^>]*\blazyload\b)/is'.BX_UTF_PCRE_MODIFIER, '${1}class=${2}lazyload ', $content);
							$content = preg_replace('/<\w+ data-lazyload (?![^>]*\bclass\s*=\s*[\'\"]\b)(?![^>]*\blazyload\b)/is'.BX_UTF_PCRE_MODIFIER, '${0}class="lazyload " ', $content);
						}
					}

					$content = str_replace(' data-lazyload ', ' ', $content);
				}
			}

			//process recaptcha
			if(\Aspro\Functions\CAsproAllcorp3ReCaptcha::checkRecaptchaActive())
			{
				$count = 0;
				$contentReplace = preg_replace_callback(
					'!(<img\s[^>]*?src[^>]*?=[^>]*?)(\/bitrix\/tools\/captcha\.php\?(captcha_code|captcha_sid)=[0-9a-z]+)([^>]*?>)!',
					function ($arImage)
					{
						//replace src and style
						$arImage = array(
							'tag' => $arImage[1],
							'src' => $arImage[2],
							'tail' => $arImage[4],
						);

						return \Aspro\Functions\CAsproAllcorp3ReCaptcha::callbackReplaceImage($arImage);
					},
					$content,
					-1,
					$count
				);

				if($count <= 0 || !$contentReplace)
					return;

				$content = $contentReplace;
				unset($contentReplace);

				$captcha_public_key = \Aspro\Functions\CAsproAllcorp3ReCaptcha::getPublicKey();

				$ind = 0;
				while ($ind++ <= $count)
				{
					$uniqueId = randString(4);
					$content = preg_replace(
						'!<input\s[^>]*?name[^>]*?=[^>]*?captcha_word[^>]*?>!',
						"<div id='recaptcha-$uniqueId'
						class='g-recaptcha'
						data-sitekey='$captcha_public_key'></div>
					<script type='text/javascript' data-skip-moving='true'>
						if(typeof renderRecaptchaById !== 'undefined')
							renderRecaptchaById('recaptcha-$uniqueId');
					</script>", $content, 1
					);
				}

				$arSearchMessages = array(
					Loc::getMessage('FORM_CAPRCHE_TITLE_RECAPTCHA'),
					Loc::getMessage('FORM_CAPRCHE_TITLE_RECAPTCHA2'),
					Loc::getMessage('FORM_CAPRCHE_TITLE_RECAPTCHA3')
				);

				$content = str_replace($arSearchMessages, Loc::getMessage('FORM_GENERAL_RECAPTCHA'), $content);
			}


		}
	}

	public static function onBeforeResultAddHandler($WEB_FORM_ID, &$arFields, &$arrVALUES){
		if(!defined('ADMIN_SECTION') && isset($_REQUEST['aspro_allcorp3_form_validate']))
		{
			global $APPLICATION;
			$arTheme = CAllcorp3::GetFrontParametrsValues(SITE_ID);

			if($arrVALUES['nspm'] && !isset($arrVALUES['captcha_sid']))
		    	$APPLICATION->ThrowException(Loc::getMessage('ERROR_FORM_CAPTCHA'));

		  	if($arTheme['SHOW_LICENCE'] == 'Y' && ((!isset($arrVALUES['licenses_popup']) || !$arrVALUES['licenses_popup']) && (!isset($arrVALUES['licenses_inline']) || !$arrVALUES['licenses_inline'])))
		    	$APPLICATION->ThrowException(Loc::getMessage('ERROR_FORM_LICENSE'));
		}
	}

	public static function OnPageStartHandler()
	{

		// current region
		global $arRegion;
		if(!$arRegion){
			$arRegion = CAllcorp3Regionality::getCurrentRegion();
		}

		if(defined("ADMIN_SECTION") || !\Aspro\Functions\CAsproAllcorp3ReCaptcha::checkRecaptchaActive())
			return;

		// remove captcha_word from request
		if(isset($_REQUEST['captcha_word'])){
			$_REQUEST['captcha_word'] = $_POST['captcha_word'] = '';
		}

		$captcha_public_key = \Aspro\Functions\CAsproAllcorp3ReCaptcha::getPublicKey();
		$captcha_version = \Aspro\Functions\CAsproAllcorp3ReCaptcha::getVersion();
		$assets = Asset::getInstance();

		if($captcha_version == 3){
			$arCaptchaProp = array(
				'recaptchaColor' => '',
				'recaptchaLogoShow' => '',
				'recaptchaSize' => '',
				'recaptchaBadge' => '',
				'recaptchaLang' => LANGUAGE_ID,
			);
		}
		else{
			$arCaptchaProp = array(
				'recaptchaColor' => strtolower(Option::get(self::MODULE_ID, 'GOOGLE_RECAPTCHA_COLOR', 'LIGHT')),
				'recaptchaLogoShow' => strtolower(Option::get(self::MODULE_ID, 'GOOGLE_RECAPTCHA_SHOW_LOGO', 'Y')),
				'recaptchaSize' => strtolower(Option::get(self::MODULE_ID, 'GOOGLE_RECAPTCHA_SIZE', 'NORMAL')),
				'recaptchaBadge' => strtolower(Option::get(self::MODULE_ID, 'GOOGLE_RECAPTCHA_BADGE', 'BOTTOMRIGHT')),
				'recaptchaLang' => LANGUAGE_ID,
			);
		}

		//add global object asproRecaptcha
		$scripts = "<script type='text/javascript' data-skip-moving='true'>";
		$scripts .= "window['asproRecaptcha'] = {params: ".\CUtil::PhpToJsObject($arCaptchaProp).",key: '".$captcha_public_key."',ver: '".$captcha_version."'};";
		$scripts .= "</script>";
		$assets->addString($scripts);

		//add scripts
		$scriptsDir = $_SERVER['DOCUMENT_ROOT'].'/bitrix/js/'.self::MODULE_ID.'/captcha/';
		$scriptsPath = File::isFileExists($scriptsDir.'recaptcha.min.js')? $scriptsDir.'recaptcha.min.js' : $scriptsDir.'recaptcha.js';
		$scriptCode = File::getFileContents($scriptsPath);
		$scripts = "<script type='text/javascript' data-skip-moving='true'>".$scriptCode."</script>";
		$assets->addString($scripts);

		$scriptsPath = File::isFileExists($scriptsDir . 'replacescript.min.js') ? $scriptsDir . 'replacescript.min.js' : $scriptsDir . 'replacescript.js';
		$scriptCode = File::getFileContents($scriptsPath);
		$scripts = "<script type='text/javascript' data-skip-moving='true'>".$scriptCode."</script>";
		$assets->addString($scripts);

		//process post request
		$application = Application::getInstance();
		$request = $application->getContext()->getRequest();
		$arPostData = $request->getPostList()->toArray();

		$needReInit = false;

		if($arPostData['g-recaptcha-response'])
		{
			if($code = \Aspro\Functions\CAsproAllcorp3ReCaptcha::getCodeByPostList($arPostData))
			{
				$_REQUEST['captcha_word'] = $_POST['captcha_word'] = $code;
				$needReInit = true;
			}
		}

		foreach($arPostData as $key => $arPost)
		{
			if(!is_array($arPost) || !$arPost['g-recaptcha-response'])
				continue;

			if($code = \Aspro\Functions\CAsproAllcorp3ReCaptcha::getCodeByPostList($arPost))
			{
				$_REQUEST[$key]['captcha_word'] = $_POST[$key]['captcha_word'] = $code;
				$needReInit = true;
			}
		}
		if($needReInit)
		{
			\Aspro\Functions\CAsproAllcorp3ReCaptcha::reInitContext($application, $request);
		}
	}

	public static function OnBeforePrologHandler(){

	}

	public static function OnBeforeSubscriptionAddHandler(&$arFields){
		if(!defined('ADMIN_SECTION'))
		{
			global $APPLICATION;
			$arTheme = CAllcorp3::GetFrontParametrsValues(SITE_ID);

			if($arTheme['SHOW_LICENCE'] == 'Y' && !isset($_REQUEST['licenses_subscribe']))
			{
				$APPLICATION->ThrowException(\Bitrix\Main\Localization\Loc::getMessage('ERROR_FORM_LICENSE'));
				return false;
			}
		}
	}

	public static function onAfterResultAddHandler($WEB_FORM_ID, $RESULT_ID){
		Aspro\Functions\CAsproAllcorp3::addFormResultToIBlock($WEB_FORM_ID, $RESULT_ID);

		$acloud = CRM\Acloud\Connection::getInstance(SITE_ID);
		if ($acloud->forms_autosend) {
			try {
				CRM\Helper::sendFormResult($WEB_FORM_ID, $RESULT_ID, $acloud);
			}
			catch (\Exception $e) {
			}
		}

		if (CRM\Flowlu\Connection::getInstance(SITE_ID)->forms_autosend) {
			\Aspro\Functions\CAsproAllcorp3::sendLeadCrmFromForm($WEB_FORM_ID, $RESULT_ID, 'FLOWLU', SITE_ID, false, false);
		}

		if (CRM\Amocrm\Connection::getInstance(SITE_ID)->forms_autosend) {
			\Aspro\Functions\CAsproAllcorp3::sendLeadCrmFromForm($WEB_FORM_ID, $RESULT_ID, 'AMO_CRM', SITE_ID, false, false);
		}
	}

	public static function OnAfterIBlockElementAddHandler($arFields){
		if (defined('ADMIN_SECTION') && ADMIN_SECTION === true) return;

		if ($arFields['IBLOCK_ID'] != \CAllcorp3Cache::$arIBlocks[SITE_ID]["aspro_allcorp3_form"]["aspro_allcorp3_feedback"][0]) {
			return;
		}

		if (!$arFields['PROPERTY_VALUES']['NAME']) return;

		$PROP = [];
		if ($arFields['PROPERTY_VALUES']) {
			if ($arFields['PROPERTY_VALUES']['POST']) {
				$PROP['POST'] = $arFields['PROPERTY_VALUES']['POST'];
			}
			if ($arFields['PROPERTY_VALUES']['RATING']) {
				$arRating = \CIBlockPropertyEnum::GetList(
					[], 
					[
						"IBLOCK_ID" => \CAllcorp3Cache::$arIBlocks[SITE_ID]["aspro_allcorp3_content"]["aspro_allcorp3_reviews"][0], 
						"CODE" => "RATING", 
						"VALUE" => $arFields['PROPERTY_VALUES']['RATING']
					]
				)->Fetch();
				if ($arRating['ID']) {
					$PROP['RATING'] = $arRating['ID'];
				}
			}
			if ($arFields['PROPERTY_VALUES']['EMAIL']) {
				$PROP['EMAIL'] = $arFields['PROPERTY_VALUES']['EMAIL'];
			}
			if ($arFields['PROPERTY_VALUES']['FILES']) {
				$PROP['DOCUMENTS'] = $arFields['PROPERTY_VALUES']['FILES'];
			}
			
		}
		$arData = [
			"PROPERTY_VALUES"=> $PROP,
			"NAME"=> $arFields['PROPERTY_VALUES']['NAME'],
			"PREVIEW_TEXT"=> $arFields['PROPERTY_VALUES']['MESSAGE']['VALUE']['TEXT'],
		];
		if ($arFields['PROPERTY_VALUES']['PHOTO']) {
			$arData['PREVIEW_PICTURE'] = $arFields['PROPERTY_VALUES']['PHOTO'][0];
		}
		
		\Aspro\Functions\CAsproAllcorp3::sendDataToIBlock($arData);
	}

	public static function onAsproParametersHandler(&$arParams){
		$arNewOptions = \Aspro\Functions\CAsproAllcorp3::getCustomBlocks();
		if ($arNewOptions) {
			$currentIndexType = Option::get(self::MODULE_ID, 'INDEX_TYPE', 'index1');
			$indexOptions = $arParams['INDEX_PAGE']['OPTIONS']['INDEX_TYPE']['SUB_PARAMS'][$currentIndexType];
			
			$arParams['INDEX_PAGE']['OPTIONS']['INDEX_TYPE']['SUB_PARAMS'][$currentIndexType] = array_merge($indexOptions, $arNewOptions);
		}
	}
	public static function OnAdminContextMenuShowHandler(&$items){
		if(
			$_SERVER['REQUEST_METHOD'] === 'GET' &&
			(
				$GLOBALS['APPLICATION']->GetCurPage() === '/bitrix/admin/form_result_edit.php' ||
				$GLOBALS['APPLICATION']->GetCurPage() === '/bitrix/admin/form_result_view.php'
			) &&
			isset($_REQUEST['RESULT_ID']) &&
			isset($_REQUEST['WEB_FORM_ID'])
		) {
			// only for second buttons row
			if (in_array('btn_new', array_column($items, 'ICON'))) {
				$formId = intval($_REQUEST['WEB_FORM_ID']);
				$resultId = intval($_REQUEST['RESULT_ID']);
				if(
					$formId > 0 &&
					$resultId > 0
				){
					if (\Bitrix\Main\Loader::includeModule('form')) {
						\CFormResult::GetDataByID($resultId, array(), $arResultFields, $arAnswers);
	
						if ($arResultFields) {
							$arSites = CForm::GetSiteArray($formId);
	
							if ($arSites) {
								$arMenuItem = array(
									'TEXT' => $title = Loc::getMessage('CRM_SEND'),
									'TITLE' => $title,
									'MENU' => [],
									'ASPRO_CRM' => 'Y',
								);

								$arSubMenuItem = [];
		
								foreach ($arSites as $siteId) {
									$arSendingResult = CRM\Helper::getSendingFormResult($resultId, $siteId);
			
									$acloud = CRM\Acloud\Connection::getInstance($siteId);
									if ($acloud->active) {
										$leadId = intval(isset($arSendingResult['ACLOUD']) ? (is_array($arSendingResult['ACLOUD']) ? $arSendingResult['ACLOUD'][$acloud->domain] : $arSendingResult['ACLOUD']) : 0);
										$bSended = $leadId > 0;
										if ($bSended) {
											$url = CRM\Acloud\Lead::getUrl($leadId);
											$url = $url ? $acloud->domain.$url : $leadId;
	
											$title = Loc::getMessage(
												'CRM_OPEN_LEAD',
												[
													'#CRM_DOMAIN#' => $acloud->domain,
													'#LEAD_ID#' => $leadId,
												]
											);
	
											if (count($arSites) > 1) {
												$title .= ' ('.$siteId.')';
											}
	
											$arSubMenuItem = [
												'TEXT' => $title,
												'TITLE' => $title,
												'ONCLICK' => 'window.open(\''.$url.'\'); return false;',
											];
										} else {
											$matches = (array)$acloud->forms_matches[$formId];

											if ($matches) {
												$title = Loc::getMessage(
													'CRM_SEND_ORDER',
													[
														'#CRM_DOMAIN#' => $acloud->domain,
													]
												);
		
												if (count($arSites) > 1) {
													$title .= ' ('.$siteId.')';
												}
		
												$arSubMenuItem = [
													'TEXT' => $title,
													'TITLE' => $title,
													'LINK' => 'javascript: BX.ajax({
														url: \'/bitrix/admin/aspro.allcorp3_crm_acloud.php?SendCrm=Y&FORM_ID='.$formId.'&RESULT_ID='.$resultId.'&SITE_ID='.$siteId.'&sessid='.bitrix_sessid().'\',
														method: \'POST\',
														dataType: \'json\',
														async: false,
														start: true,
														cache: false,
														onsuccess: function(data) {
															if (
																typeof data === \'object\' &&
																data &&
																(
																	\'error\' in data ||
																	\'response\' in data
																)
															){
																if(\'error\' in data){
																	alert(data.error);
																}
																else if(\'response\' in data){
																	location.reload();
																}
																
																return true;
															}
														},
														onfailure: function() {
															alert(\'error\');
														},
													});',
												];
											}
										}
			
										if ($arSubMenuItem) {
											$bFinded = false;
											foreach ($items as &$item) {
												if (
													is_array($item) &&
													is_array($item['MENU']) &&
													array_key_exists('ASPRO_CRM', $item)
												) {
													$item['MENU'][] = $arSubMenuItem;
													$bFinded = true;
				
													break;
												}
											}
											unset($item);
				
											if (!$bFinded) {
												$arMenuItem['MENU'][] = $arSubMenuItem;
												$items[] = $arMenuItem;
											}
										}
									}
								}
							}
						}
					}
				}
			}
		} 
	}
	public static function OnAfterUserLoginHandler(&$items){
		if(
			!defined('ADMIN_SECTION') &&
			$items['USER_ID']
		) {
			\Aspro\Allcorp3\Notice::setAuthFlag();
 		}
	}

}
?>