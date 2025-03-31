<?
namespace Aspro\Functions;

use \Bitrix\Main\Application,
	\Bitrix\Main\Web\DOM\Document,
	\Bitrix\Main\Localization\Loc,
	\Bitrix\Main\Web\DOM\CssParser,
	\Bitrix\Main\Text\HtmlFilter,
	\Bitrix\Main\IO\File,
	\Bitrix\Main\IO\Directory,
	\Bitrix\Main\Config\Option,
	\Bitrix\Main\Web\Json,
	\Aspro\Functions\CAsproAllcorp3CRM,
	CAllcorp3 as Solution;

Loc::loadMessages(__FILE__);

if (!class_exists("CAsproAllcorp3")) {
	class CAsproAllcorp3
	{
		const MODULE_ID = \CAllcorp3::moduleID;

		/*public static function OnAsproShowPageTypeHandler($arTheme, &$arMainPageOrder){
			$arMainPageOrder[] = 'NEW_BLOCK';
		}*/

		/*public static function OnAsproParametersHandler(&$arParameters){
			//add new option with value
			$arParameters['TEST'] = array(
				'TITLE' => 'Test group',
				'OPTIONS' => array(
					'THEME_SWITCHER' => array(
						'TITLE' => 'Test title',
						'TYPE' => 'checkbox',
						'DEFAULT' => 'Y',
						'THEME' => 'N',
					)
				)
			);
			//add new value in exist option
			$arParameters['INDEX_PAGE']['OPTIONS']['INDEX_TYPE']['LIST']['index_test'] = 'index_test';
			$arParameters['INDEX_PAGE']['OPTIONS']['INDEX_TYPE']['SUB_PARAMS']['index4']['TEST'] = array(
				'TITLE' => 'Test title',
				'TYPE' => 'checkbox',
				'DEFAULT' => 'Y',
				'THEME' => 'N',
				'ONE_ROW' => 'Y',
				'SMALL_TOGGLE' => 'Y',
			);
		}*/

		//log to file
		public static function set_log($type="log", $path="log_file", $arMess=array())
		{
			$root = $_SERVER['DOCUMENT_ROOT'].'/upload/logs/'.self::MODULE_ID.'/'.$type.'/';
			if(!file_exists($root) || !is_dir($root))
				mkdir( $root, 0700, true );

			$path_date = $root.date('Y-m').'/';
			if(!file_exists($path_date) || !is_dir($path_date))
				mkdir( $path_date, 0700 );

			file_put_contents($path_date.$path.'.log', date('d-m-Y H-i-s', time()+\CTimeZone::GetOffset())."\n".print_r($arMess, true)."\n", LOCK_EX | FILE_APPEND);
		}

		protected static function _getAllFormFieldsHTML($WEB_FORM_ID, $RESULT_ID, $arAnswers)
		{
			global $APPLICATION;

			$strResult = "";

			$w = \CFormField::GetList($WEB_FORM_ID, "ALL", $by, $order, array("ACTIVE" => "Y"), $is_filtered);
			while ($wr=$w->Fetch())
			{
				$answer = "";
				$answer_raw = '';
				if (is_array($arAnswers[$wr["SID"]]))
				{
					$bHasDiffTypes = false;
					$lastType = '';
					foreach ($arAnswers[$wr['SID']] as $arrA)
					{
						if ($lastType == '') $lastType = $arrA['FIELD_TYPE'];
						elseif ($arrA['FIELD_TYPE'] != $lastType)
						{
							$bHasDiffTypes = true;
							break;
						}
					}

					foreach($arAnswers[$wr["SID"]] as $arrA)
					{
						if ($wr['ADDITIONAL'] == 'Y')
						{
							$arrA['FIELD_TYPE'] = $wr['FIELD_TYPE'];
						}

						$USER_TEXT_EXIST = (strlen(trim($arrA["USER_TEXT"]))>0);
						$ANSWER_TEXT_EXIST = (strlen(trim($arrA["ANSWER_TEXT"]))>0);
						$ANSWER_VALUE_EXIST = (strlen(trim($arrA["ANSWER_VALUE"]))>0);
						$USER_FILE_EXIST = (intval($arrA["USER_FILE_ID"])>0);

						if (
							$bHasDiffTypes
							&&
							!$USER_TEXT_EXIST
							&&
							(
								$arrA['FIELD_TYPE'] == 'text'
								||
								$arrA['FIELD_TYPE'] == 'textarea'
							)
						)
							continue;

						if (strlen(trim($answer))>0) $answer .= "<br />";
						if (strlen(trim($answer_raw))>0) $answer_raw .= ",";

						if ($ANSWER_TEXT_EXIST)
							$answer .= $arrA["ANSWER_TEXT"].': ';

						switch ($arrA['FIELD_TYPE'])
						{
							case 'text':
							case 'textarea':
							case 'hidden':
							case 'date':
							case 'password':

								if ($USER_TEXT_EXIST)
								{
									$answer .= htmlspecialcharsbx(trim($arrA["USER_TEXT"]));
									$answer_raw .= htmlspecialcharsbx(trim($arrA["USER_TEXT"]));
								}

							break;

							case 'email':
							case 'url':

								if ($USER_TEXT_EXIST)
								{
									$answer .= '<a href="'.($arrA['FIELD_TYPE'] == 'email' ? 'mailto:' : '').trim($arrA["USER_TEXT"]).'">'.htmlspecialcharsbx(trim($arrA["USER_TEXT"])).'</a>';
									$answer_raw .= htmlspecialcharsbx(trim($arrA["USER_TEXT"]));
								}

							break;

							case 'checkbox':
							case 'multiselect':
							case 'radio':
							case 'dropdown':

								if ($ANSWER_TEXT_EXIST)
								{
									$answer = htmlspecialcharsbx(substr($answer, 0, -2).' ');
									$answer_raw .= htmlspecialcharsbx($arrA['ANSWER_TEXT']);
								}

								if ($ANSWER_VALUE_EXIST)
								{
									$answer .= '('.htmlspecialcharsbx($arrA['ANSWER_VALUE']).') ';
									if (!$ANSWER_TEXT_EXIST)
										$answer_raw .= htmlspecialcharsbx($arrA['ANSWER_VALUE']);
								}

								if (!$ANSWER_VALUE_EXIST && !$ANSWER_TEXT_EXIST)
									$answer_raw .= $arrA['ANSWER_ID'];

								$answer .= '['.$arrA['ANSWER_ID'].']';

							break;

							case 'file':
							case 'image':

								if ($USER_FILE_EXIST)
								{
									$f = \CFile::GetByID($arrA["USER_FILE_ID"]);
									if ($fr = $f->Fetch())
									{
										$file_size = \CFile::FormatSize($fr["FILE_SIZE"]);
										$url = ($APPLICATION->IsHTTPS() ? "https://" : "http://").$_SERVER["HTTP_HOST"]. "/bitrix/tools/form_show_file.php?rid=".$RESULT_ID. "&hash=".$arrA["USER_FILE_HASH"]."&lang=".LANGUAGE_ID;

										if ($arrA["USER_FILE_IS_IMAGE"]=="Y")
										{
											$answer .= "<a href=\"$url\">".htmlspecialcharsbx($arrA["USER_FILE_NAME"])."</a> [".$fr["WIDTH"]." x ".$fr["HEIGHT"]."] (".$file_size.")";
										}
										else
										{
											$answer .= "<a href=\"$url&action=download\">".htmlspecialcharsbx($arrA["USER_FILE_NAME"])."</a> (".$file_size.")";
										}

										$answer_raw .= htmlspecialcharsbx($arrA['USER_FILE_NAME']);
									}
								}

							break;
						}
					}
				}

				$strResult .= $wr["TITLE"].":<br />".(strlen($answer)<=0 ? " " : $answer)."<br /><br />";
			}

			return $strResult;
		}

		protected static function _getAllFormFields($WEB_FORM_ID, $RESULT_ID, $arAnswers)
		{
			global $APPLICATION;

			$strResult = "";

			$w = \CFormField::GetList($WEB_FORM_ID, "ALL", $by, $order, array("ACTIVE" => "Y"), $is_filtered);
			while ($wr=$w->Fetch())
			{
				$answer = "";
				$answer_raw = '';
				if (is_array($arAnswers[$wr["SID"]]))
				{
					$bHasDiffTypes = false;
					$lastType = '';
					foreach ($arAnswers[$wr['SID']] as $arrA)
					{
						if ($lastType == '') $lastType = $arrA['FIELD_TYPE'];
						elseif ($arrA['FIELD_TYPE'] != $lastType)
						{
							$bHasDiffTypes = true;
							break;
						}
					}

					foreach($arAnswers[$wr["SID"]] as $arrA)
					{
						if ($wr['ADDITIONAL'] == 'Y')
						{
							$arrA['FIELD_TYPE'] = $wr['FIELD_TYPE'];
						}

						$USER_TEXT_EXIST = (strlen(trim($arrA["USER_TEXT"]))>0);
						$ANSWER_TEXT_EXIST = (strlen(trim($arrA["ANSWER_TEXT"]))>0);
						$ANSWER_VALUE_EXIST = (strlen(trim($arrA["ANSWER_VALUE"]))>0);
						$USER_FILE_EXIST = (intval($arrA["USER_FILE_ID"])>0);

						if (
							$bHasDiffTypes
							&& !$USER_TEXT_EXIST
							&& (
								$arrA['FIELD_TYPE'] == 'text'
								||
								$arrA['FIELD_TYPE'] == 'textarea'
							)
						)
						{
							continue;
						}

						if (strlen(trim($answer)) > 0)
							$answer .= "\n";
						if (strlen(trim($answer_raw)) > 0)
							$answer_raw .= ",";

						if ($ANSWER_TEXT_EXIST)
							$answer .= $arrA["ANSWER_TEXT"].': ';

						switch ($arrA['FIELD_TYPE'])
						{
							case 'text':
							case 'textarea':
							case 'email':
							case 'url':
							case 'hidden':
							case 'date':
							case 'password':

								if ($USER_TEXT_EXIST)
								{
									$answer .= trim($arrA["USER_TEXT"]);
									$answer_raw .= trim($arrA["USER_TEXT"]);
								}

							break;

							case 'checkbox':
							case 'multiselect':
							case 'radio':
							case 'dropdown':

								if ($ANSWER_TEXT_EXIST)
								{
									$answer = substr($answer, 0, -2).' ';
									$answer_raw .= $arrA['ANSWER_TEXT'];
								}

								if ($ANSWER_VALUE_EXIST)
								{
									$answer .= '('.$arrA['ANSWER_VALUE'].') ';
									if (!$ANSWER_TEXT_EXIST)
									{
										$answer_raw .= $arrA['ANSWER_VALUE'];
									}
								}

								if (!$ANSWER_VALUE_EXIST && !$ANSWER_TEXT_EXIST)
								{
									$answer_raw .= $arrA['ANSWER_ID'];
								}

								$answer .= '['.$arrA['ANSWER_ID'].']';

							break;

							case 'file':
							case 'image':

								if ($USER_FILE_EXIST)
								{
									$f = \CFile::GetByID($arrA["USER_FILE_ID"]);
									if ($fr = $f->Fetch())
									{
										$file_size = \CFile::FormatSize($fr["FILE_SIZE"]);
										$url = ($APPLICATION->IsHTTPS() ? "https://" : "http://").$_SERVER["HTTP_HOST"]. "/bitrix/tools/form_show_file.php?rid=".$RESULT_ID. "&hash=".$arrA["USER_FILE_HASH"]."&action=download&lang=".LANGUAGE_ID;

										if ($arrA["USER_FILE_IS_IMAGE"]=="Y")
										{
											$answer .= $arrA["USER_FILE_NAME"]." [".$fr["WIDTH"]." x ".$fr["HEIGHT"]."] (".$file_size.")\n".$url;
										}
										else
										{
											$answer .= $arrA["USER_FILE_NAME"]." (".$file_size.")\n".$url."&action=download";
										}
									}

									$answer_raw .= $arrA['USER_FILE_NAME'];
								}

							break;
						}
					}
				}

				$strResult .= $wr["TITLE"].":\r\n".(strlen($answer)<=0 ? " " : $answer)."\r\n\r\n";
			}

			return $strResult;
		}

		public static function prepareArray($arFields = array(), $arReplace = array(), $stamp = '_leads')
		{
			$arTmpFields = array();
			if($arFields && $arReplace)
			{
				foreach($arFields as $key => $value)
				{
					$key = str_replace($stamp, '', $key);
					if(in_array($key, $arReplace))
						$arTmpFields[$key] = $value;
				}
				// $arTmpFields = self::prepareArray($arFields, array('name', 'tags', 'budget'), '_leads');
			}
			return $arTmpFields;
		}

		public static function sendLeadCrmFromForm(
			$WEB_FORM_ID,
			$RESULT_ID,
			$TYPE = 'ALL',
			$SITE_ID = SITE_ID,
			$CURL = false,
			$DECODE = false
		){
			$bIntegrationFlowlu = CAsproAllcorp3CRM::isEnabledIntegration('FLOWLU', $SITE_ID);
			$bIntegrationAcloud = CAsproAllcorp3CRM::isEnabledIntegration('ACLOUD', $SITE_ID);
			$bIntegrationAmoCrm = CAsproAllcorp3CRM::isEnabledIntegration('AMO_CRM', $SITE_ID);

			if($bIntegrationFlowlu || $bIntegrationAmoCrm || $bIntegrationAcloud)
			{
				$arAllMatchValues = array();

				$arMatchValuesFlowlu = unserialize(Option::get(self::MODULE_ID, 'FLOWLU_CRM_FIELDS_MATCH_'.$WEB_FORM_ID, '', $SITE_ID));
				$arMatchValuesAcloud = unserialize(Option::get(self::MODULE_ID, 'ACLOUD_CRM_FIELDS_MATCH_'.$WEB_FORM_ID, '', $SITE_ID));
				$arMatchValuesAmoCrm = unserialize(Option::get(self::MODULE_ID, 'AMO_CRM_FIELDS_MATCH_'.$WEB_FORM_ID, '', $SITE_ID));

				//flowlu
				if($bIntegrationFlowlu && ($TYPE == 'ALL' || $TYPE == 'FLOWLU'))
					$arAllMatchValues['FLOWLU'] = $arMatchValuesFlowlu;
					
				//acloud
				if($bIntegrationAcloud && ($TYPE == 'ALL' || $TYPE == 'ACLOUD'))
					$arAllMatchValues['ACLOUD'] = $arMatchValuesAcloud;
				//amocrm
				if($bIntegrationAmoCrm && ($TYPE == 'ALL' || $TYPE == 'AMO_CRM'))
					$arAllMatchValues['AMO_CRM'] = $arMatchValuesAmoCrm;

				if($arAllMatchValues)
				{
					//get fields
					\CForm::GetResultAnswerArray(
						$WEB_FORM_ID,
						$arrColumns,
						$arrAnswers,
						$arrAnswersVarname,
						array("RESULT_ID" => $RESULT_ID)
					);

					//get form
					\CFormResult::GetDataByID($RESULT_ID, array(), $arResultFields, $arAnswers);
				}

				if($arAllMatchValues)
				{
					$arPostFields = array();

					//fill main fieds
					foreach($arAllMatchValues as $crm => $arFields)
					{
						foreach($arFields as $key => $id)
						{
							switch($id)
							{
								case 'RESULT_ID':
									$arPostFields[$crm][$key] = $arResultFields['ID'];
								break;
								case 'FORM_SID':
									$arPostFields[$crm][$key] = $arResultFields['SID'];
								break;
								case 'FORM_NAME':
									$arPostFields[$crm][$key] = $arResultFields['NAME'];
								break;
								case 'SITE_ID':
									$arPostFields[$crm][$key] = $SITE_ID;
								break;
								case 'FORM_ALL':
									$arPostFields[$crm][$key] = self::_getAllFormFields($WEB_FORM_ID, $RESULT_ID, $arAnswers);
								break;
								case 'FORM_ALL_HTML':
									$arPostFields[$crm][$key] = self::_getAllFormFieldsHTML($WEB_FORM_ID, $RESULT_ID, $arAnswers);
								break;
							}
						}
					}

					//fill form fieds
					foreach($arAllMatchValues as $crm => $arFields)
					{
						foreach($arFields as $key => $id)
						{
							if($arrAnswers[$RESULT_ID][$id])
							{
								$bCanPushCrm = true;

								$arAnswer = reset($arrAnswers[$RESULT_ID][$id]);

								$arPostFields[$crm][$key] = (isset($arAnswer['USER_TEXT']) && $arAnswer['USER_TEXT'] ? $arAnswer['USER_TEXT'] : $arAnswer['ANSWER_TEXT']);
							}
						}
					}

					if($arPostFields)
					{
						$arHeaders = array();

						if($crm === 'AMO_CRM'){
							$arOAuth = array();
							$arConfig = array(
								'type' => 'AMO_CRM',
								'siteId' => $SITE_ID,
							);
							CAsproAllcorp3CRM::restore(
								$arOAuth,
								$arConfig
							);

							CAsproAllcorp3CRM::updateOAuth(
								$arOAuth,
								$arConfig
							);

							CAsproAllcorp3CRM::save(
								$arOAuth,
								$arConfig
							);

							$arHeaders = array(
								'Authorization' => 'Bearer '.$arOAuth['accessToken']
							);
						}

						foreach($arPostFields as $crm => $arFields)
						{
							if($crm == 'FLOWLU')
							{
								$url = str_replace('#DOMAIN#', Option::get(self::MODULE_ID, 'DOMAIN_'.$crm, '', $SITE_ID), CAsproAllcorp3CRM::FLOWLU_PATH);
								$arFields['api_key'] = Option::get(self::MODULE_ID, 'TOKEN_FLOWLU', '', $SITE_ID);
								$arFields['ref'] = 'form:aspro-allcorp3';
								$arFields['ref_id'] = $WEB_FORM_ID.'_'.$RESULT_ID;
								$name_field = 'name';
							}
							elseif($crm == 'ACLOUD')
							{
								$url = str_replace('#DOMAIN#', Option::get(self::MODULE_ID, 'DOMAIN_'.$crm, '', $SITE_ID), CAsproAllcorp3CRM::ACLOUD_PATH);
								$arFields['api_key'] = Option::get(self::MODULE_ID, 'TOKEN_ACLOUD', '', $SITE_ID);
								$arFields['ref'] = 'form:aspro-allcorp3';
								$arFields['ref_id'] = $WEB_FORM_ID.'_'.$RESULT_ID;
								$name_field = 'name';
							}
							else
							{
								$name_field = 'name_leads';
								$url = str_replace('#DOMAIN#', Option::get(self::MODULE_ID, 'DOMAIN_'.$crm, '', $SITE_ID), CAsproAllcorp3CRM::AMO_CRM_PATH);
								if(!$arFields['tags_leads'])
									$arFields['tags_leads'] = Option::get(self::MODULE_ID, 'TAGS_AMO_CRM_TITLE', '', $SITE_ID);
							}

							if(!$arFields[$name_field])
								$arFields[$name_field] = Option::get(self::MODULE_ID, 'LEAD_NAME_'.$crm.'_TITLE', \Bitrix\Main\Localization\Loc::getMessage('ALLCORP3_MODULE_LEAD_NAME_'.$crm), $SITE_ID);

							$smCrmName = strtolower(str_replace('_', '', $crm));
							//log to file form request
							if(Option::get(self::MODULE_ID, 'USE_LOG_'.$crm, 'N', $SITE_ID) == 'Y')
							{
								self::set_log('crm', $smCrmName.'_create_lead_request', $arFields);
							}

							//convert all to UTF8 encoding for send to flowlu
							// foreach($arFields as $key => $value)
							// {
							// 	$arFields[$key] = iconv(LANG_CHARSET, 'UTF-8', $value);
							// }

							$arFieldsLead = $arFields;

							if($crm == 'AMO_CRM')
							{
								$arFieldsLeadTmp = $arFields;
								$arCustomFields = unserialize(Option::get(self::MODULE_ID, 'CUSTOM_FIELD_AMO_CRM', '', $SITE_ID));
								//prepare array
								$arFieldsLeadTmp = self::prepareArray($arFields, array('name', 'tags', 'price', 'budget'), '_leads');
								if($arCustomFields && $arCustomFields['leads'])
								{
									foreach($arCustomFields['leads'] as $key => $arProp)
									{
										if($arFields[$key.'_leads'])
										{
											$arFieldsLeadTmp['custom_fields'][] = array(
												'id' => $key,
												'values' => array(
													array(
														'value' => $arFields[$key.'_leads']
													)
												)
											);
										}
										elseif(isset($arProp['ENUMS']) && $arProp['ENUMS'])
										{
											foreach($arProp['ENUMS'] as $key2 => $value)
											{
												if($arFields[$key.'_'.$key2.'_leads'])
												{
													$arFieldsLeadTmp['custom_fields'][] = array(
														'id' => $key,
														'values' => array(
															array(
																'value' => $arFields[$key.'_'.$key2.'_leads'],
																'enum' => $value
															)
														)
													);
												}
											}
										}
									}
								}

								$arFieldsLead = array(
									'request' => array(
										'leads' => array(
											'add' => array(
												$arFieldsLeadTmp
											)
										)
									)
								);
							}

							$result = CAsproAllcorp3CRM::query($url, CAsproAllcorp3CRM::$arCrmMethods[$crm]["CREATE_LEAD"], $arFieldsLead, $arHeaders, $CURL, $DECODE);
							$arCrmResult = Json::decode($result);
							unset($arFieldsLead);

							if(isset($arCrmResult['response']))
							{
								if($crm == 'AMO_CRM' && $arCrmResult['response']['leads']) // create contact and company for amocrm
								{
									$arLead = reset($arCrmResult['response']['leads']['add']);
									$leadID = $arLead['id'];

									//add notes
									if($arFields['notes_leads'])
									{
										$arFieldsNote = array(
											'request' => array(
												'notes' => array(
													'add' => array(
														array(
															'element_id' => $leadID,
															'element_type' => 2,
															'note_type' => 4,
															'text' => $arFields['notes_leads']
														),
													)
												)
											)
										);
										$resultNote = CAsproAllcorp3CRM::query($url, CAsproAllcorp3CRM::$arCrmMethods[$crm]["CREATE_NOTES"], $arFieldsNote, $arHeaders, $CURL, $DECODE);

										unset($arFieldsNote);
										unset($resultNote);
									}

									//add company
									$company_id = 0;
									if($arCustomFields && $arCustomFields['companies'])
									{
										//prepare array
										$arFieldsCompanyTmp = self::prepareArray($arFields, array('name', 'tags'), '_companies');
										$arFieldsCompanyTmp['linked_leads_id'] = array($leadID);

										foreach($arCustomFields['companies'] as $key => $arProp)
										{
											if($arFields[$key.'_companies'])
											{
												$arFieldsCompanyTmp['custom_fields'][] = array(
													'id' => $key,
													'values' => array(
														array(
															'value' => $arFields[$key.'_companies']
														)
													)
												);
											}
											elseif(isset($arProp['ENUMS']) && $arProp['ENUMS'])
											{
												foreach($arProp['ENUMS'] as $key2 => $value)
												{
													if($arFields[$key.'_'.$key2.'_companies'])
													{
														$arFieldsCompanyTmp['custom_fields'][] = array(
															'id' => $key,
															'values' => array(
																array(
																	'value' => $arFields[$key.'_'.$key2.'_companies'],
																	'enum' => $value
																)
															)
														);
													}
												}
											}
										}
										$arFieldsCompany = array(
											'request' => array(
												'contacts' => array(
													'add' => array(
														$arFieldsCompanyTmp
													)
												)
											)
										);

										$resultCompany = CAsproAllcorp3CRM::query($url, CAsproAllcorp3CRM::$arCrmMethods[$crm]["CREATE_COMPANY"], $arFieldsCompany, $arHeaders, $CURL, $DECODE);
										$resultCompany = Json::decode($resultCompany);

										if(isset($resultCompany['response']['contacts']['add'][0]['id']))
											$company_id = $resultCompany['response']['contacts']['add'][0]['id'];

										//log to file crm response
										if(Option::get(self::MODULE_ID, 'USE_LOG_'.$crm, 'N', $SITE_ID) == 'Y')
										{
											self::set_log('crm', $smCrmName.'_create_company_response', $resultCompany);
										}

										unset($arFieldsCompany);
										unset($resultCompany);
									}

									//add contact
									$arFieldsContactTmp = self::prepareArray($arFields, array('name', 'tags'), '_contacts');
									$arFieldsContactTmp['linked_leads_id'] = array($leadID);

									if($company_id)
										$arFieldsContactTmp['linked_company_id'] = $company_id;

									if($arCustomFields && $arCustomFields['contacts'])
									{
										foreach($arCustomFields['contacts'] as $key => $arProp)
										{
											if($arFields[$key.'_contacts'])
											{
												$arFieldsContactTmp['custom_fields'][] = array(
													'id' => $key,
													'values' => array(
														array(
															'value' => $arFields[$key.'_contacts']
														)
													)
												);
											}
											elseif(isset($arProp['ENUMS']) && $arProp['ENUMS'])
											{
												foreach($arProp['ENUMS'] as $key2 => $value)
												{
													if($arFields[$key.'_'.$key2.'_contacts'])
													{
														$arFieldsContactTmp['custom_fields'][] = array(
															'id' => $key,
															'values' => array(
																array(
																	'value' => $arFields[$key.'_'.$key2.'_contacts'],
																	'enum' => $value
																)
															)
														);
													}
												}
											}
										}
									}

									$arFieldsContact = array(
										'request' => array(
											'contacts' => array(
												'add' => array(
													$arFieldsContactTmp
												)
											)
										)
									);

									$resultContact = CAsproAllcorp3CRM::query($url, CAsproAllcorp3CRM::$arCrmMethods['AMO_CRM']['CREATE_CONTACT'], $arFieldsContact, $arHeaders, $CURL, $DECODE);

									//log to file crm response
									if(Option::get(self::MODULE_ID, 'USE_LOG_'.$crm, 'N', $SITE_ID) == 'Y')
									{
										self::set_log('crm', $smCrmName.'_create_contact_response', Json::decode($resultContact));
									}

									unset($arFieldsContact);
									unset($resultContact);

								}

								if((isset($arCrmResult['response']['id']) && $arCrmResult['response']['id']) || (isset($arCrmResult['response']['leads']) && $leadID))
								{
									$arFormResultOption = unserialize(Option::get(self::MODULE_ID, 'CRM_SEND_FORM_'.$RESULT_ID, '', $SITE_ID));
									if(!isset($arFormResultOption['FLOWLU']) && (isset($arCrmResult['response']['id']) && $arCrmResult['response']['id']))
										$arFormResultOption['FLOWLU'] = $arCrmResult['response']['id'];
									if(!isset($arFormResultOption['ACLOUD']) && (isset($arCrmResult['response']['id']) && $arCrmResult['response']['id']))
										$arFormResultOption['ACLOUD'] = $arCrmResult['response']['id'];
									if(!isset($arFormResultOption['AMO_CRM']) && (isset($arCrmResult['response']['leads']) && $leadID))
										$arFormResultOption['AMO_CRM'] = $leadID;
									Option::set(self::MODULE_ID, 'CRM_SEND_FORM_'.$RESULT_ID, serialize($arFormResultOption), $SITE_ID);
								}
							}

							//log to file crm response
							if(Option::get(self::MODULE_ID, 'USE_LOG_'.$crm, 'N', $SITE_ID) == 'Y')
							{
								self::set_log('crm', $smCrmName.'_create_lead_response', $arCrmResult);
							}
						}
					}
				}
			}
			return $result;
		}

		public static function addFormResultToIBlock($WEB_FORM_ID, $RESULT_ID){
			$bAdminSection = (defined('ADMIN_SECTION') && ADMIN_SECTION === true);
			if(!$bAdminSection)
			{
				//check REVIEW form
				$rsForm = \CForm::GetByID($WEB_FORM_ID);
				$arForm = $rsForm->Fetch();
				if($arForm && strpos($arForm['SID'], str_replace('.', '_', self::MODULE_ID).'_feedback') !== false)
				{
					\CForm::GetResultAnswerArray(
							$WEB_FORM_ID,
							$arrColumns,
							$arrAnswers,
							$arrAnswersVarname,
							array("RESULT_ID" => $RESULT_ID)
						);
					\CFormResult::GetDataByID($RESULT_ID, array(), $arResultFields, $arAnswers);

					if($arrAnswersVarname)
					{
						$PROP = array(
							'EMAIL' => $arrAnswersVarname[$RESULT_ID]['EMAIL'][0]['USER_TEXT'],
							'POST' => $arrAnswersVarname[$RESULT_ID]['POST'][0]['USER_TEXT'],
							'RATING' => $arrAnswersVarname[$RESULT_ID]['RATING'][0]['USER_TEXT'],
						);
						if ($arrAnswersVarname[$RESULT_ID]['POST'][0]['USER_TEXT']) {
							$PROP['POST'] = $arrAnswersVarname[$RESULT_ID]['POST'][0]['USER_TEXT'];
						}
						if ($arrAnswersVarname[$RESULT_ID]['RATING'][0]['USER_TEXT']) {
							$arRating = \CIBlockPropertyEnum::GetList(
								[],
								[
									"IBLOCK_ID" => $iblockID,
									"CODE" => "RATING",
									"VALUE" => $arrAnswersVarname[$RESULT_ID]['RATING'][0]['USER_TEXT']
								]
							)->Fetch();
							if ($arRating['ID']) {
								$PROP['RATING'] = $arRating['ID'];
							}
						}
						if ($arrAnswersVarname[$RESULT_ID]['EMAIL'][0]['USER_TEXT']) {
							$PROP['EMAIL'] = $arrAnswersVarname[$RESULT_ID]['EMAIL'][0]['USER_TEXT'];
						}
						if ($arrAnswersVarname[$RESULT_ID]['FILE'][0]['USER_FILE_ID']) {
							$PROP['DOCUMENTS'] = \CFile::MakeFileArray($arrAnswersVarname[$RESULT_ID]['FILE'][0]['USER_FILE_ID']);
						}
						$arData = [
							"PROPERTY_VALUES"=> $PROP,
							"NAME"=> $arrAnswersVarname[$RESULT_ID]['NAME'][0]['USER_TEXT'],
							"PREVIEW_TEXT"=> $arrAnswersVarname[$RESULT_ID]['MESSAGE'][0]['USER_TEXT'],
						];
						if ($arrAnswersVarname[$RESULT_ID]['PHOTO'][0]['USER_FILE_ID']) {
							$arData['PREVIEW_PICTURE'] = \CFile::MakeFileArray($arrAnswersVarname[$RESULT_ID]['PHOTO'][0]['USER_FILE_ID']);
						}
						self::sendDataToIBlock($arData);
					}
				}
			}
		}

		public static function sendDataToIBlock($arFields){
			$el = new \CIBlockElement;

			$arData = array_merge(
				[
					"IBLOCK_ID" => \CAllcorp3Cache::$arIBlocks[SITE_ID]["aspro_allcorp3_content"]["aspro_allcorp3_reviews"][0],
					"ACTIVE"=> "N",
				],
				$arFields
			);

			$el->Add($arData);
		}

		public static function showPrice($arOptions = [])
		{
			$arDefaultOptions = [
				'TYPE' => 'catalog-block',
				'WRAPPER_CLASS' => '',
				'TO_LINE' => false,
				'WIDE_BLOCK' => false,
				'SHOW_SCHEMA' => true,
				'PRICE_BLOCK_CLASS' => 'color_333',
				'PRICE_FONT' => 17,
				'PRICEOLD_FONT' => 13,
				'RETURN' => false,
				'PRICES' => [],
				'ITEM' => [],
				'PARAMS' => []
			];
			$arConfig = array_merge($arDefaultOptions, $arOptions);

			if ($handler = self::getCustomFunc(__FUNCTION__)) {
				return call_user_func_array($handler, [$arConfig]);
			}?>

			<?
			$arParams = $arConfig['PARAMS'];
			$arItem = $arConfig['ITEM'];
			$bLinePrices = $arConfig['TO_LINE'];
			$bShowSchema = $arConfig['SHOW_SCHEMA'];
			$bWideBlock = $arConfig['WIDE_BLOCK'];

			$price = isset($arItem['PRICE']) && $arItem['PRICE']['VALUE']
				? $arItem['PRICE']
				: $arItem['DISPLAY_PROPERTIES']['PRICE'];
			$priceOld = isset($arItem['PRICEOLD']) && $arItem['PRICEOLD']['VALUE']
				? $arItem['PRICEOLD']
				: $arItem['DISPLAY_PROPERTIES']['PRICEOLD'];
			$priceEconomy = isset($arItem['ECONOMY']) && $arItem['ECONOMY']['VALUE']
				? $arItem['ECONOMY']
				: $arItem['DISPLAY_PROPERTIES']['ECONOMY'];
			$priceCurrency = isset($arItem['PRICE_CURRENCY']) && $arItem['PRICE_CURRENCY']
				? $arItem['PRICE_CURRENCY']
				: $arItem['DISPLAY_PROPERTIES']['PRICE_CURRENCY'];
			$priceFilter = isset($arItem['FILTER_PRICE']) && $arItem['FILTER_PRICE']['VALUE']
				? $arItem['FILTER_PRICE']
				: $arItem['DISPLAY_PROPERTIES']['FILTER_PRICE'];

			if (!$priceCurrency) {
				$priceCurrency = $arItem['PROPERTIES']['PRICE_CURRENCY'];
			}
			if (!$priceFilter) {
				$priceFilter = $arItem['PROPERTIES']['FILTER_PRICE'];
			}

			if ($arConfig['PRICES']) {
				$price['VALUE'] = $arConfig['PRICES']['PRICE'];
				$priceOld['VALUE'] = $arConfig['PRICES']['PRICE_OLD'];
				$priceCurrency['VALUE'] = $arConfig['PRICES']['PRICE_CURRENCY'];
			}

			$bUseCurrency = $priceCurrency['VALUE'];
			?>
			<?ob_start();?>
				<?if(strlen($price['VALUE'])):?>
					<?if(strlen($arConfig['WRAPPER_CLASS'])):?>
						<div class="<?=$arConfig['WRAPPER_CLASS']?>">
					<?endif;?>

					<div class="price<?=($bLinePrices ? '  price--inline' : '')?> <?=$arConfig['PRICE_BLOCK_CLASS'];?>">
						<div class="price__new">
							<span class="price__new-val font_<?=$arConfig['PRICE_FONT'];?>" <?if($bShowSchema):?>itemprop="price" content="<?=($arParams['SHOW_PRICE'] ? $price['VALUE'] : $priceFilter['VALUE']);?>"<?endif;?>>
								<?if ($bUseCurrency) {
									$price['VALUE'] = str_replace('#CURRENCY#',$priceCurrency["VALUE"], $price['VALUE']);
								}?>
								<?=\CAllcorp3::FormatPriceShema($price['VALUE'], ($arParams['SHOW_PRICE'] ? false : $bShowSchema), $arItem['PROPERTIES'] ?: $arItem['DISPLAY_PROPERTIES'] ?: $arItem)?>
							</span>
						</div>
						<?if($priceOld['VALUE']):?>
							<div class="price__old">
								<?if($bWideBlock):?>
									<?=GetMessage('PRICE_DISCOUNT')?>
								<?endif;?>
								<?if ($bUseCurrency) {
									$priceOld['VALUE'] = str_replace('#CURRENCY#',$priceCurrency["VALUE"], $priceOld['VALUE']);
								}?>
								<span class="price__old-val font_<?=$arConfig['PRICEOLD_FONT'];?> color_999"><?=$priceOld['VALUE']?></span>
							</div>
						<?endif;?>
						<?if($arItem['DISPLAY_PROPERTIES']['ECONOMY']['VALUE']):?>
							<div class="price__economy rounded-3">
								<?if($bWideBlock):?>
									<?=GetMessage('PRICE_ECONOMY')?>
								<?endif;?>
								<?if ($bUseCurrency) {
									$priceEconomy['VALUE'] = str_replace('#CURRENCY#',$priceCurrency["VALUE"], $priceEconomy['VALUE']);
								}?>
								<span class="price__economy-val font_11"><?=$priceEconomy['VALUE']?></span>
							</div>
						<?endif;?>
					</div>

					<?if(strlen($arConfig['WRAPPER_CLASS'])):?>
						</div>
					<?endif;?>
				<?endif;?>
			<?$html = ob_get_contents();
			ob_end_clean();

			// event for manipulation
			foreach (GetModuleEvents(self::MODULE_ID, 'OnAspro'.ucfirst(__FUNCTION__), true) as $arEvent) {
				ExecuteModuleEventEx($arEvent, array($arConfig, &$html));
			}
			if ($arConfig['RETURN']) {
				return $html;
			} else {
				echo $html;
			}
			?>
		<?}

		public static function showSchemaAvailabilityMeta($status = "")
		{
		    if (!empty($status)) {
				switch ($status) {
					case "instock": return '<link itemprop="availability" href="http://schema.org/InStock">';
					case "nostock": return '<link itemprop="availability" href="http://schema.org/OutOfStock">';
					case "order": return '<link itemprop="availability" href="http://schema.org/PreOrder">';
					case "pending": return '<link itemprop="availability" href="http://schema.org/PreSale">';
				}
		    }
		}

		public static function showHeaderBlock($options)
		{
			$bRestart = $options['AJAX_BLOCK'] == $options['PARAM_NAME'];
			if (
				!$bRestart && 
				$options['IS_AJAX'] && 
				(
					$options['AJAX_BLOCK'] != 'HEADER_MAIN_PART' && 
					$options['AJAX_BLOCK'] != 'HEADER_TOP_PART' && 
					$options['AJAX_BLOCK'] != 'HEADER_TOPEST_PART' && 
					$options['AJAX_BLOCK'] != 'HEADER_FIXED_TOP_PART'
				)
			) {
				return false;
			}

			global $APPLICATION;

			if($options['IS_AJAX'] && $bRestart) {
				if ($options['BLOCK_TYPE'] === 'THEME_SELECTOR' || $options['BLOCK_TYPE'] === 'HEADER_RIGHT_BLOCK') {
					$APPLICATION->ShowAjaxHead();
				} else {
					$APPLICATION->restartBuffer();
				}
			}?>
			<div class="<?=$options['WRAPPER'] ? $options['WRAPPER'] : ''?> <?=$options['VISIBLE'] ? '' : 'hidden'?>" data-ajax-load-block="<?=$options['PARAM_NAME']?>">
				<?if($options['INNER_WRAPPER']):?>
					<div class="<?=$options['INNER_WRAPPER']?>">
				<?endif;?>
				<?if($options['VISIBLE']):?>

					<?
					switch($options['BLOCK_TYPE']) {
						case 'SLOGAN':?>
							<?if(\CAllcorp3::checkContentFile(SITE_DIR.'include/header/header-text.php')):?>
								<div class="slogan font_sm">
									<div class="slogan__text banner-light-text menu-light-text">
										<?$APPLICATION->IncludeFile(SITE_DIR."include/header/header-text.php", array(), array(
												"MODE" => "html",
												"NAME" => "Text in title",
												"TEMPLATE" => "include_area.php",
											)
										);?>
									</div>
								</div>
							<?endif;?>
							<?break;
						case 'SEARCH':?>
							<?if($options['TYPE'] == 'LINE'):?>
								<div class="">
									<?include $_SERVER['DOCUMENT_ROOT'].SITE_DIR.'/include/header/search.title.php';?>
								</div>
							<?else:?>
								<div class="header-search banner-light-icon-fill fill-theme-hover color-theme-hover menu-light-icon-fill light-opacity-hover" title="<?=GetMessage("SEARCH_TITLE")?>">
									<?=\CAllcorp3::showIconSvg(" header-search__icon", SITE_TEMPLATE_PATH."/images/svg/Search_black.svg");?>
									<?if($options['MESSAGE']):?>
										<span class="header-search__name header__icon-name menu-light-text banner-light-text">
											<?=$options['MESSAGE']?>
										</span>
									<?endif;?>
								</div>
							<?endif;?>
						<?break;
						case 'ADDRESS':?>
							<div class="<?=$options['NO_ICON'] ? '' : 'icon-block--with_icon'?>">
								<?
								\CAllcorp3::showAddress(
									array(
										'CLASS' => 'address',
										'NO_LIGHT' => false,
									)
								);?>
							</div>
						<?break;
						case 'SOCIAL':?>
							<?include $_SERVER['DOCUMENT_ROOT'].SITE_DIR.'include/header/social.info.php';?>
						<?break;
						case 'PHONE':?>
							<div class="<?=$options['NO_ICON'] ? '' : 'icon-block--with_icon'?> <?=$options['ONLY_ICON'] ? 'icon-block--only_icon' : ''?>">
								<div class="phones">
									<?//check phone text?>
									<div class="phones__phones-wrapper">
										<?$svg = $options['ONLY_ICON'] ? 'Phone_big.svg' : 'Phone_black.svg';?>
										<?\CAllcorp3::ShowHeaderPhones('phones__inner--big', $svg, $options['ONLY_ICON'], $options['DROPDOWN_TOP'], $options['DROPDOWN_CALLBACK'], $options['DROPDOWN_EMAIL'],$options['DROPDOWN_SOCIAL'], $options['DROPDOWN_ADDRESS'],$options['DROPDOWN_SCHEDULE']);?>
									</div>

									<?if($options['CALLBACK']):?>
										<div class="phones__callback light-opacity-hover animate-load colored banner-light-text menu-light-text <?=$options['CALLBACK_CLASS'] ? $options['CALLBACK_CLASS'] : ''?>" data-event="jqm" data-param-id="<?=\CAllcorp3::getFormID("aspro_allcorp3_callback");?>" data-name="callback">
											<?=$options['MESSAGE']?>
										</div>
									<?endif;?>
								</div>
							</div>
						<?break;
						case 'LANG':?>
							<div class="<?=$options['NO_ICON'] ? '' : 'icon-block--with_icon'?> <?=$options['ONLY_ICON'] ? 'icon-block--only_icon' : ''?>">
								<?include $_SERVER['DOCUMENT_ROOT'].SITE_DIR.'/include/header/site.selector.php';?>
							</div>
						<?break;
						case 'MEGA_MENU':?>
							<div class="burger light-opacity-hover fill-theme-hover banner-light-icon-fill menu-light-icon-fill fill-dark-light-block">
								<?=\CAllcorp3::showIconSvg("burger", SITE_TEMPLATE_PATH."/images/svg/Burger_big_white.svg");?>
							</div>
						<?break;
						case 'BUTTON':?>
							<div class="header-button">
								<?include $_SERVER['DOCUMENT_ROOT'].SITE_DIR.'/include/header/express.button.php';?>
							</div>
						<?break;
						case 'TOPEST_BUTTON':?>
							<div class="btn-header-topest btn btn-default">
							COVID-19
							</div>
						<?break;
						case 'CABINET':?>
							<div class="header-cabinet">
								<?$arCabinetParams = $options['CABINET_PARAMS'] ? $options['CABINET_PARAMS'] : array();?>
								<?=\CAllcorp3::showCabinetLink($arCabinetParams);?>
							</div>
						<?break;
						case 'COMPARE':?>
							<div class="header-compare js-compare-block-wrapper">
								<?=\CAllcorp3::showCompareLink($options['CLASS_LINK'], $options['CLASS_ICON'], $options['MESSAGE']);?>
							</div>
							<?break;
						case 'BASKET':?>
							<div class="header-cart">
								<?=\CAllcorp3::showBasketLink('', '', $options['MESSAGE']);?>
							</div>
						<?break;
						case 'EYED':?>
							<?
							$bEyedActive = \Aspro\Allcorp3\Eyed::isActive();
							$titleEyed = $bEyedActive ? Loc::getMessage("EA_T_NORMAL_VERSION_SHORT") : Loc::getMessage("EA_T_EYED_VERSION");
							?>
							<div class="header-eyed eyed-toggle <?=($bEyedActive ? 'eyed-toggle--off' : 'eyed-toggle--on')?> banner-light-icon-fill fill-theme-hover color-theme-hover menu-light-icon-fill light-opacity-hover" title="<?=$titleEyed?>">
								<?=\CAllcorp3::showIconSvg(" header-eyed__icon", SITE_TEMPLATE_PATH."/images/svg/Eyed_black.svg");?>
								<?if($options['MESSAGE']):?>
									<span class="header-eyed__name header__icon-name menu-light-text banner-light-text"><?=$titleEyed?></span>
								<?endif;?>
							</div>
						<?break;
						case 'HEADER_RIGHT_BLOCK':?>
							<?if ($options['ITEMS']) {
								foreach ($options['ITEMS'] as $arOption) {
									self::showHeaderBlock($arOption);
								}
							}?>
						<?break;
						case 'THEME_SELECTOR':?>
							<div class="header-theme-selector">
								<?include $_SERVER['DOCUMENT_ROOT'].SITE_DIR.'/include/header/theme.selector.php';?>
							</div>
						<?break;
					}?>

				<?endif;?>
				<?if($options['INNER_WRAPPER']):?>
					</div>
				<?endif;?>

			</div>


			<?if($options['IS_AJAX'] && $bRestart) {
				die();
			}
		}

		public static function showFooterBlock($options)
		{
			$bRestart = $options['AJAX_BLOCK'] == $options['PARAM_NAME'];
			$bRightIcon = $options['POSITION_ICON'] === 'RIGHT';
			if(!$bRestart && $options['IS_AJAX'] && ($options['AJAX_BLOCK'] != 'FOOTER_MAIN_PART' && $options['AJAX_BLOCK'] != 'FOOTER_TOP_PART' && $options['AJAX_BLOCK'] != 'FOOTER_TOPEST_PART'))
				return false;

			global $APPLICATION;

			if($options['IS_AJAX'] && $bRestart) {
				$APPLICATION->restartBuffer();
			}?>

			<div class="<?=$options['WRAPPER'] ? $options['WRAPPER'] : ''?> <?=$options['VISIBLE'] ? '' : 'hidden'?>" data-ajax-load-block="<?=$options['PARAM_NAME']?>" data-ajax-check-visible="<?=($options['PARAM_CHECK_VISIBLE'] ? $options['PARAM_CHECK_VISIBLE'] : '');?>">
				<?if($options['INNER_WRAPPER']):?>
					<div class="<?=$options['INNER_WRAPPER']?>">
				<?endif;?>

				<?if($options['VISIBLE']):?>
					<?
					switch($options['BLOCK_TYPE']) {
						case 'SUBSCRIBE':?>
							<?if (\Bitrix\Main\ModuleManager::isModuleInstalled("subscribe")):?>
								<?if ($options['COMPACT']):?>
									<div class="stroke-theme-parent-all color-theme-parent-all">
										<div class="icon-block icon-block--with_icon" data-event="jqm" data-param-type="subscribe" data-name="subscribe">

											<?if ($options['BTN_CLASS']):?>
												<div class="<?=$options['BTN_CLASS']?>">
											<?endif;?>

												<div class="subscribe icon-block__wrapper <?=($bRightIcon ? 'flexbox--direction-row-reverse' : '');?>">
													<span class="icon-block__icon icon-block__only-icon <?=($bRightIcon ? 'icon-block__icon--right' : '');?> stroke-theme-target banner-light-icon-fill menu-light-icon-fill light-opacity-hover"><?=\CAllcorp3::showIconSvg('subscribe', SITE_TEMPLATE_PATH.'/images/svg/Subscribe_sm.svg');?></span>
													<div class="subscribe__text color-theme-target icon-block__name font_<?=($options['FONT_SIZE'] ? $options['FONT_SIZE'] : 13);?> color_999"><?=Loc::getMessage('SUBSCRIBE');?></div>
												</div>

											<?if ($options['BTN_CLASS']):?>
											</div>
											<?endif;?>

										</div>
									</div>
								<?elseif (\CAllcorp3::checkContentFile(SITE_DIR.'include/footer/subscribe.php')):?>
									<?include $_SERVER['DOCUMENT_ROOT'].SITE_DIR.'include/footer/subscribe.php';?>
								<?endif;?>
							<?endif;?>
							<?break;
						case 'ADDRESS':?>
							<div class="<?=$options['NO_ICON'] ? '' : 'icon-block--with_icon'?>">
								<?
								\CAllcorp3::showAddress(
									array(
										'CLASS' => 'address',
										'FONT_SIZE' => '15',
									)
								);?>
							</div>
						<?break;
						case 'EMAIL':?>
							<div class="<?=$options['NO_ICON'] ? '' : 'icon-block--with_icon'?>">
								<?
								\CAllcorp3::showEmail(
									array(
										'CLASS' => 'footer__email font_14',
										'LINK_CLASS' => 'dark_link',
									)
								);?>
							</div>
						<?break;
						case 'SOCIAL':?>
							<?include $_SERVER['DOCUMENT_ROOT'].SITE_DIR.'include/footer/social.info.php';?>
						<?break;
						case 'PHONE':?>
							<div class="<?=$options['NO_ICON'] ? '' : 'icon-block--with_icon'?> <?=$options['ONLY_ICON'] ? 'icon-block--only_icon' : ''?>">
								<div class="phones">
									<?//check phone text?>
									<div class="phones__phones-wrapper">
										<?$svg = $options['ONLY_ICON'] ? 'Phone_big.svg' : 'Phone_black.svg';?>
										<?\CAllcorp3::ShowHeaderPhones('phones__inner--big', $svg, $options['ONLY_ICON'], $options['DROPDOWN_TOP'],$options['DROPDOWN_CALLBACK'], $options['DROPDOWN_EMAIL'],$options['DROPDOWN_SOCIAL'], $options['DROPDOWN_ADDRESS'],$options['DROPDOWN_SCHEDULE']);?>
									</div>

									<?if($options['CALLBACK']):?>
										<div class="phones__callback light-opacity-hover animate-load colored banner-light-text menu-light-text <?=$options['CALLBACK_CLASS'] ? $options['CALLBACK_CLASS'] : ''?>" data-event="jqm" data-param-id="<?=\CAllcorp3::getFormID("aspro_allcorp3_callback");?>" data-name="callback">
											<?=$options['MESSAGE']?>
										</div>
									<?endif;?>
								</div>
							</div>
						<?break;
						case 'LANG':?>
							<div class="<?=$options['NO_ICON'] ? '' : 'icon-block--with_icon'?> <?=$options['ONLY_ICON'] ? 'icon-block--only_icon' : ''?>">
								<?include $_SERVER['DOCUMENT_ROOT'].SITE_DIR.'/include/header/site.selector.php';?>
							</div>
						<?break;
						case 'PAY_SYSTEMS':?>
							<?if(\CAllcorp3::checkContentFile(SITE_DIR.'include/footer/pay_system_icons.php')):?>
								<?$APPLICATION->IncludeFile(SITE_DIR."include/footer/pay_system_icons.php", Array(), Array(
									"MODE" => "php",
									"NAME" => "Payment systems",
									"TEMPLATE" => "include_area.php",
								)
								);?>
							<?endif;?>
						<?break;
						case 'DEVELOPER':?>
							<?include $_SERVER['DOCUMENT_ROOT'].SITE_DIR.'include/footer/developer.php';?>
						<?break;
						case 'EYED':?>
							<?
							$bEyedActive = \Aspro\Allcorp3\Eyed::isActive();
							$titleEyed = $bEyedActive ? Loc::getMessage("EA_T_NORMAL_VERSION_SHORT") : Loc::getMessage("EA_T_EYED_VERSION");
							?>
							<div class="footer__eyed eyed-toggle <?=($bEyedActive ? 'eyed-toggle--off' : 'eyed-toggle--on')?> font_13 color_999 pointer" title="<?=$titleEyed?>">
								<span class="footer-eyed__name color-theme-target menu-light-text"><?=$titleEyed?></span>
							</div>
						<?break;
						case 'SITEMAP':?>
							<?include $_SERVER['DOCUMENT_ROOT'].SITE_DIR.'include/footer/sitemap.php';?>
						<?break;
						case 'FOOTER_ALL_BLOCK':?>
							<?if ($options['ITEMS']) {
								foreach ($options['ITEMS'] as $arOption) {
									self::showFooterBlock($arOption);
								}
							}?>
						<?break;
					}?>

				<?endif;?>

				<?if($options['INNER_WRAPPER']):?>
					</div>
				<?endif;?>

			</div>


			<?if($options['IS_AJAX'] && $bRestart) {
				die();
			}
		}

		public static function showMobileHeaderBlock($options)
		{
			$bRestart = $options['AJAX_BLOCK'] == $options['PARAM_NAME'];
			if(
				!$bRestart &&
				$options['IS_AJAX'] &&
				(
					$options['AJAX_BLOCK'] != 'HEADER_MOBILE_MAIN_PART' &&
					$options['AJAX_BLOCK'] != 'HEADER_MOBILE_TOGGLE_PHONE' &&
					$options['AJAX_BLOCK'] != 'HEADER_MOBILE_TOGGLE_SEARCH' &&
					$options['AJAX_BLOCK'] != 'HEADER_MOBILE_TOGGLE_PERSONAL' &&
					$options['AJAX_BLOCK'] != 'HEADER_MOBILE_TOGGLE_COMPARE' &&
					$options['AJAX_BLOCK'] != 'HEADER_MOBILE_TOGGLE_CART'
				)
			){
				return false;
			}

			if(!$options['VISIBLE']){
				return false;
			}

			global $APPLICATION;

			$class = ($options['WRAPPER'] ? $options['WRAPPER'] : '');
			$class .= ($options['VISIBLE'] ? '' : ' hidden');

			if($options['IS_AJAX'] && $bRestart) {
				$APPLICATION->restartBuffer();
			}
			?>
			<div <?=($class ? 'class="'.$class.'"' : '')?> data-ajax-load-block="<?=$options['PARAM_NAME']?>">
				<?
				switch($options['BLOCK_TYPE']) {
					case 'SEARCH':?>
						<div class="header-search banner-light-icon-fill fill-theme-hover color-theme-hover menu-light-icon-fill light-opacity-hover" title="<?=GetMessage("SEARCH_TITLE")?>">
							<?=\CAllcorp3::showIconSvg(" header-search__icon", SITE_TEMPLATE_PATH."/images/svg/Search_black.svg");?>
						</div>
					<?break;
					case 'PHONE':?>
						<div class="icon-block--with_icon icon-block--only_icon">
							<div class="phones">
								<div class="phones__phones-wrapper">
									<?\CAllcorp3::ShowHeaderMobilePhones(
										array('CALLBACK' => $options['CALLBACK'])
									);?>
								</div>
							</div>
						</div>
					<?break;
					case 'BURGER':?>
						<div class="burger light-opacity-hover fill-theme-hover banner-light-icon-fill menu-light-icon-fill fill-dark-light-block">
							<?=\CAllcorp3::showIconSvg('burger', SITE_TEMPLATE_PATH."/images/svg/Burger_big_white.svg");?>
						</div>
					<?break;
					case 'CABINET':?>
						<div class="header-cabinet">
							<?$arCabinetParams = $options['CABINET_PARAMS'] ? $options['CABINET_PARAMS'] : array();?>
							<?=\CAllcorp3::showCabinetLink($arCabinetParams);?>
						</div>
					<?break;
					case 'COMPARE':?>
						<div class="header-compare js-compare-block-wrapper">
							<?=\CAllcorp3::showCompareLink($options['CLASS_LINK'], $options['CLASS_ICON'], $options['MESSAGE']);?>
						</div>
						<?break;
					case 'BASKET':?>
						<div class="header-cart">
							<?=\CAllcorp3::showBasketLink('', '', $options['MESSAGE']);?>
						</div>
					<?break;
				}?>
			</div>
			<?
			if($options['IS_AJAX'] && $bRestart) {
				die();
			}
		}

		public static function showMobileMenuBlock($options)
		{
			$bRestart = $options['AJAX_BLOCK'] == $options['PARAM_NAME'];
			if(
				!$bRestart &&
				$options['IS_AJAX'] &&
				(
					$options['AJAX_BLOCK'] != 'MOBILE_MENU_MAIN_PART' &&
					$options['AJAX_BLOCK'] != 'MOBILE_MENU_TOGGLE_CONTACTS' &&
					$options['AJAX_BLOCK'] != 'MOBILE_MENU_TOGGLE_SOCIAL' &&
					$options['AJAX_BLOCK'] != 'MOBILE_MENU_TOGGLE_LANG' &&
					$options['AJAX_BLOCK'] != 'MOBILE_MENU_TOGGLE_REGION' &&
					$options['AJAX_BLOCK'] != 'MOBILE_MENU_TOGGLE_PERSONAL' &&
					$options['AJAX_BLOCK'] != 'MOBILE_MENU_TOGGLE_CART' &&
					$options['AJAX_BLOCK'] != 'MOBILE_MENU_TOGGLE_COMPARE' &&
					$options['AJAX_BLOCK'] != 'MOBILE_MENU_TOGGLE_BUTTON'
				)
			){
				return false;
			}

			if(!$options['VISIBLE']){
				return false;
			}

			global $APPLICATION;

			$class = ($options['WRAPPER'] ? $options['WRAPPER'] : '');
			$class .= ($options['VISIBLE'] ? '' : ' hidden');

			if($options['IS_AJAX'] && $bRestart) {
				if ($options['BLOCK_TYPE'] === 'THEME_SELECTOR') {
					$APPLICATION->ShowAjaxHead();
				} else {
					$APPLICATION->restartBuffer();
				}
			}
			?>
			<div <?=($class ? 'class="'.$class.'"' : '')?> data-ajax-load-block="<?=$options['PARAM_NAME']?>">
				<?
				switch($options['BLOCK_TYPE']) {
					case 'CONTACTS':?>
						<div class="mobilemenu__menu mobilemenu__menu--contacts">
							<ul class="mobilemenu__menu-list">
								<?if($options['PHONES']):?>
									<?\CAllcorp3::ShowMobileMenuPhones(
										array('CALLBACK' => $options['CALLBACK'])
									);?>
								<?endif;?>

								<?if($options['ADDRESS']):?>
									<?ob_start();?>
									<?\CAllcorp3::showAddress(
										array(
											'CLASS' => 'bg-opacity-theme-parent-hover fill-theme-parent-all color-theme-parent-all icon-block--with_icon',
											'SHOW_SVG' => true,
											'CLASS_SVG' => 'address mobilemenu__menu-item-svg fill-theme-target',
											'SVG_NAME' => 'Address_big.svg',
											'TITLE' => '',
											'TITLE_CLASS' => '',
											'FONT_SIZE' => '15',
											'WRAPPER' => '',
											'NO_LIGHT' => true,
											'LARGE' => false,
										)
									);?>
									<?$addressHtml = trim(ob_get_clean());?>
									<?if(strlen($addressHtml)):?>
										<li class="mobilemenu__menu-item mobilemenu__menu-item--with-icon"><?=$addressHtml?></li>
									<?endif;?>
								<?endif;?>

								<?if($options['EMAIL']):?>
									<?ob_start();?>
									<?\CAllcorp3::showEmail(
										array(
											'CLASS' => 'link-wrapper bg-opacity-theme-parent-hover fill-theme-parent-all color-theme-parent-all',
											'SHOW_SVG' => true,
											'CLASS_SVG' => 'email mobilemenu__menu-item-svg fill-theme-target',
											'SVG_NAME' => 'Email_big.svg',
											'TITLE' => '',
											'TITLE_CLASS' => '',
											'LINK_CLASS' => 'dark_link font_15',
											'WRAPPER' => '',
										)
									);?>
									<?$emailHtml = trim(ob_get_clean());?>
									<?if(strlen($emailHtml)):?>
										<li class="mobilemenu__menu-item mobilemenu__menu-item--with-icon"><?=$emailHtml?></li>
									<?endif;?>
								<?endif;?>

								<?if($options['SCHEDULE']):?>
									<?ob_start();?>
									<?\CAllcorp3::showSchedule(
										array(
											'CLASS' => 'font_15 bg-opacity-theme-parent-hover fill-theme-parent-all color-theme-parent-all',
											'SHOW_SVG' => true,
											'CLASS_SVG' => 'schedule mobilemenu__menu-item-svg fill-theme-target',
											'TITLE' => '',
											'TITLE_CLASS' => '',
											'FONT_SIZE' => '15',
											'WRAPPER' => '',
										)
									);?>
									<?$scheduleHtml = trim(ob_get_clean());?>
									<?if(strlen($scheduleHtml)):?>
										<li class="mobilemenu__menu-item mobilemenu__menu-item--with-icon"><?=$scheduleHtml?></li>
									<?endif;?>
								<?endif;?>
							</ul>
						</div>
						<?break;
					case 'SOCIAL':?>
						<?include $_SERVER['DOCUMENT_ROOT'].SITE_DIR.'include/header/mobile-social.info.php';?>
						<?break;
					case 'LANG':?>
						<?include $_SERVER['DOCUMENT_ROOT'].SITE_DIR.'/include/header/site.selector.php';?>
						<?break;
					case 'REGION':?>
						<?\CAllcorp3::ShowMobileMenuRegions();?>
						<?break;
					case 'CABINET':?>
						<?\CAllcorp3::ShowMobileMenuCabinet();?>
					<?break;
					case 'COMPARE':?>
						<?\CAllcorp3::ShowMobileMenuCompare();?>
					<?break;
					case 'BASKET':?>
						<?\CAllcorp3::ShowMobileMenuBasket();?>
					<?break;
					case 'BUTTON':?>
						<div class="mobilemenu__button">
							<?include $_SERVER['DOCUMENT_ROOT'].SITE_DIR.'/include/header/mobile-express.button.php';?>
						</div>
					<?break;
					case 'THEME_SELECTOR':?>
						<div class="header-theme-selector">
							<?include $_SERVER['DOCUMENT_ROOT'].SITE_DIR.'/include/header/theme.selector.php';?>
						</div>
					<?break;
				}?>
			</div>
			<?
			if($options['IS_AJAX'] && $bRestart) {
				die();
			}
		}

		public static function showBasketButton($arOptions = [])
		{
			$arDefaultOptions = [
				'TYPE' => 'catalog-block',
				'WRAPPER' => false,
				'WRAPPER_CLASS' => '',
				'BASKET' => false,
				'DETAIL_PAGE' => false,
				'ORDER_BTN' => false,
				'ONE_CLICK_BUY' => false,
				'QUESTION_BTN' => false,
				'DISPLAY_COMPARE' => false,
				'INFO_BTN_ICONS' => false,
				'SHOW_COUNTER' => true,
				'RETURN' => false,
				'JS_CLASS' => false,
				'BASKET_URL' => SITE_DIR.'cart/',
				'BTN_CLASS' => 'btn-md btn-transparent-border',
				'BTN_IN_CART_CLASS' => 'btn-md',
				'BTN_CLASS_MORE' => '',
				'BTN_CALLBACK_CLASS' => 'btn-sm btn-transparent-border',
				'BTN_OCB_CLASS' => 'btn-sm btn-transparent-border',
				'TO_ORDER_TEXT' => '',
				'ORDER_FORM_ID' => 'aspro_allcorp3_order_product',
				'ITEM' => [],
				'PARAMS' => []
			];
			$arConfig = array_merge($arDefaultOptions, $arOptions);

			if ($handler = self::getCustomFunc(__FUNCTION__)) {
				return call_user_func_array($handler, [$arConfig]);
			}

			$arParams = $arConfig['PARAMS'];
			$arItem = $arConfig['ITEM'];
			$bOrderViewBasket = isset($arConfig['BASKET']) && !empty($arConfig['BASKET']) && $arConfig['BASKET'] !== 'N' && $arConfig['BASKET'] !== 'false';
			$bOrderButton = $arConfig['ORDER_BTN'];
			$bDetail = $arConfig['DETAIL_PAGE'] === true;
			// $bDetail = $arConfig['BASKET_URL'];

			$sOrderText = Option::get(self::MODULE_ID, 'EXPRESSION_ORDER_BUTTON', GetMessage('TO_ORDER'), SITE_ID);
			$sMoreText = Option::get(self::MODULE_ID, 'EXPRESSION_READ_MORE_OFFERS_DEFAULT', GetMessage('TO_ALL'), SITE_ID);
			$sToCartText = Option::get(self::MODULE_ID, 'EXPRESSION_ADDTOBASKET_BUTTON_DEFAULT', GetMessage('BUTTON_TO_CART'), SITE_ID);
			$sInCartText = Option::get(self::MODULE_ID, 'EXPRESSION_ADDEDTOBASKET_BUTTON_DEFAULT', GetMessage('BUTTON_IN_CART'), SITE_ID);

			if ($arConfig['TO_ORDER_TEXT']) {
				$sOrderText = $arConfig['TO_ORDER_TEXT'];
			}

			if ($arConfig['TO_CART_TEXT']) {
				$sToCartText = $arConfig['TO_CART_TEXT'];
			}

			$arConfig['ONE_CLICK_BUY'] = $arConfig['ONE_CLICK_BUY'] === true || $arConfig['ONE_CLICK_BUY'] === 'true';
			$arConfig['QUESTION_BTN'] = $arConfig['QUESTION_BTN'] === true || $arConfig['QUESTION_BTN'] === 'true';
			$arConfig['DISPLAY_COMPARE'] = $arConfig['DISPLAY_COMPARE'] === true || $arConfig['DISPLAY_COMPARE'] === 'true';
			?>

			<?$html = '';?>
			<?ob_start();?>
			<?if ($arConfig['JS_CLASS']):?>
				<div class="<?=$arConfig['JS_CLASS'];?>">
			<?endif;?>
			<?if ($arConfig['WRAPPER']):?>
				<div class="footer-button btn-actions<?=($arConfig['INFO_BTN_ICONS'] ? ' btn-actions--with-icons' : '');?> <?=$arConfig['WRAPPER_CLASS'];?>">
			<?endif;?>

				<?if($bOrderButton):?>
					<?// element order button?>
					<?if(!$bOrderViewBasket || $bOrderViewBasket === 'false'):?>
						<span class="buy_block btn-actions__inner">
							<span class="buttons">
								<span class="btn btn-default <?=$arConfig['BTN_CLASS']?> <?=$arConfig['BTN_CLASS_MORE']?> animate-load" data-event="jqm" data-param-id="<?=\CAllcorp3::getFormID($arConfig['ORDER_FORM_ID']);?>" data-autoload-product="<?=\CAllcorp3::formatJsName($arItem["NAME"]);?>" data-autoload-service="<?=\CAllcorp3::formatJsName($arItem["NAME"]);?>" data-autoload-project="<?=\CAllcorp3::formatJsName($arItem["NAME"]);?>" data-autoload-news="<?=\CAllcorp3::formatJsName($arItem["NAME"]);?>" data-autoload-sale="<?=\CAllcorp3::formatJsName($arItem["NAME"]);?>" data-name="order_product_<?=$arItem['ID'];?>">
									<?=$sOrderText;?>
								</span>
							</span>
							<?if ($arConfig['INFO_BTN_ICONS']):?>
								<?if ($arConfig['DISPLAY_COMPARE']):?>
									<?self::showSideIcons([
										'TYPE' => $arConfig['TYPE'],
										'ITEM' => $arItem,
										'PARAMS' => $arParams,
										'DOP_CLASS' => 'static side-icons--line side-icons--lg',
									])?>
								<?endif;?>
							<?else:?>
								<?if (
									$arConfig['QUESTION_BTN'] ||
									$arConfig['DISPLAY_COMPARE']
								):?>
									<div class="info-buttons flexbox flexbox--direction-row">
										<?if ($arConfig['QUESTION_BTN']):?>
											<div class="info-buttons__item info-buttons__item--question flex-1">
												<a
												href="javascript:void(0)"
												rel="nofollow"
												class="btn btn-default <?=$arConfig['BTN_CALLBACK_CLASS']?> btn-wide animate-load"
												data-param-id="<?=\CAllcorp3::getFormID("aspro_allcorp3_question");?>"
												data-name="question"
												data-event="jqm"
												title="<?=Loc::getMessage('QUESTION_FORM_TITLE')?>"
												data-autoload-need_product="<?=\CAllcorp3::formatJsName($arItem['NAME'])?>"
												>
													<span class="fill-use-fff">
														<?=\CAllcorp3::showSpriteIconSvg(SITE_TEMPLATE_PATH."/images/svg/catalog/item_icons.svg#question", "compare", ['WIDTH' => 11,'HEIGHT' => 14]);?>
														<span class="info-buttons__item-text"><?=Loc::getMessage('QUESTION_FORM_TITLE')?></span>
													</span>
												</a>
											</div>
										<?endif;?>
										<?if ($arConfig['DISPLAY_COMPARE']):?>
											<?$arTransferData = [
												'ID' => (isset($arConfig['ITEM_ID']) && $arItem['ID'] !== $arConfig['ITEM_ID'] ? $arConfig['ITEM_ID'] : $arItem['ID']),
												'IBLOCK_ID' => $arConfig['CATALOG_IBLOCK_ID'],
											];?>
											<div class="info-buttons__item info-buttons__item--question side-icons__item--compare <?=(!$arConfig['QUESTION_BTN'] ? 'flex-1' : '');?>">
												<a
												href="javascript:void(0)"
												rel="nofollow"
												class="btn btn-default <?=$arConfig['BTN_CALLBACK_CLASS']?> <?=($arConfig['QUESTION_BTN'] ? '' : 'btn-wide')?> js-item-action"
												data-action="compare"
												data-id="<?=$arTransferData['ID']?>"
												data-item_compare='<?=str_replace('\'', '"', \CUtil::PhpToJsObject($arTransferData))?>' 
												title="<?=Loc::getMessage('COMPARE_ITEM')?>" 
												data-title="<?=Loc::getMessage('COMPARE_ITEM')?>" 
												data-title_compared="<?=Loc::getMessage('COMPARE_ITEM_REMOVE')?>"
												>
													<span class="fill-use-fff">
														<?=\CAllcorp3::showSpriteIconSvg(SITE_TEMPLATE_PATH."/images/svg/catalog/item_icons.svg#compare_small", "compare", ['WIDTH' => 14,'HEIGHT' => 14]);?>
														<?if (!$arConfig['QUESTION_BTN']):?>
															<span class="info-buttons__item-text"><?=Loc::getMessage('COMPARE_ITEM')?></span>
														<?endif;?>
													</span>
												</a>
											</div>
										<?endif;?>
									</div>
								<?endif;?>
							<?endif;?>
						</span>
					<?// element buy block?>
					<?else:?>
						<div class="buy_block btn-actions__inner">
							<?if ($arConfig['SHOW_COUNTER']):?>
								<div class="counter">
									<div class="wrap">
										<span class="minus ctrl bgtransition"></span>
										<div class="input"><input type="text" value="1" class="count" maxlength="20" /></div>
										<span class="plus ctrl bgtransition"></span>
									</div>
								</div>
							<?endif;?>
							<div class="buttons">
								<span class="btn btn-default <?=$arConfig['BTN_CLASS']?> <?=$arConfig['BTN_CLASS_MORE']?> to_cart animate-load" data-quantity="1">
									<span>
										<?=$sToCartText?>
									</span>
								</span>
								<div class="btn btn-default in_cart <?=$arConfig['BTN_IN_CART_CLASS']?>">
									<div class="counter js-ajax">
										<span class="counter__action counter__action--minus"></span>
										<div class="counter__count-wrapper">
											<input type="text" value="1" class="counter__count" maxlength="6">
										</div>
										<span class="counter__action counter__action--plus"></span>
									</div>
								</div>
							</div>
							<?if (
								$arConfig['ONE_CLICK_BUY'] ||
								$arConfig['QUESTION_BTN'] ||
								$arConfig['DISPLAY_COMPARE']
							):?>
								<?if ($arConfig['INFO_BTN_ICONS']):?>
									<?self::showSideIcons([
										'TYPE' => $arConfig['TYPE'],
										'ITEM' => $arItem,
										'PARAMS' => $arParams,
										'DOP_CLASS' => 'static side-icons--line side-icons--lg',
										'CATALOG_IBLOCK_ID' => $arConfig['CATALOG_IBLOCK_ID'],
										'ITEM_ID' => $arConfig['ITEM_ID'],
									])?>
								<?else:?>
									<div class="info-buttons <?=($arConfig['ONE_CLICK_BUY'] || $arConfig['QUESTION_BTN'] ? 'flexbox flexbox--direction-row' : '')?>">
										<?if ($arConfig['ONE_CLICK_BUY']):?>
											<?$price = ($arItem['DISPLAY_PROPERTIES']['PRICE']['VALUE'] ? $arItem['DISPLAY_PROPERTIES']['PRICE']['VALUE'] : $arItem['PROPERTIES']['PRICE']['VALUE'])?>
											<div class="info-buttons__item info-buttons__item--ocb flex-1">
												<span
												class="btn btn-default <?=$arConfig['BTN_OCB_CLASS']?> animate-load"
												data-param-id="<?=\CAllcorp3::getFormID("aspro_allcorp3_quick_buy");?>"
												data-name="ocb"
												data-event="jqm"
												data-autoload-product_name="<?=\CAllcorp3::formatJsName($arItem['NAME'])?>"
												data-autoload-product_price="<?=\CAllcorp3::formatJsName($price)?>"
												>
													<span>
														<?=Loc::getMessage('ONE_CLICK_BUY')?>
													</span>
												</span>
											</div>
										<?endif;?>
										<?if ($arConfig['QUESTION_BTN']):?>
											<div class="info-buttons__item info-buttons__item--question <?=(!$arConfig['ONE_CLICK_BUY'] ? 'flex-1' : '');?>">
												<a
												href="javascript:void(0)"
												rel="nofollow"
												class="btn btn-default <?=$arConfig['BTN_CALLBACK_CLASS']?> <?=($arConfig['ONE_CLICK_BUY'] ? '' : 'btn-wide')?> animate-load"
												data-param-id="<?=\CAllcorp3::getFormID("aspro_allcorp3_question");?>"
												data-name="question"
												data-event="jqm"
												title="<?=Loc::getMessage('QUESTION_FORM_TITLE')?>"
												data-autoload-need_product="<?=\CAllcorp3::formatJsName($arItem['NAME'])?>"
												>
													<span class="fill-use-fff">
														<?//=($arConfig['ONE_CLICK_BUY'] ? '?' : Loc::getMessage('QUESTION_FORM_TITLE'))?>
														<?=\CAllcorp3::showSpriteIconSvg(SITE_TEMPLATE_PATH."/images/svg/catalog/item_icons.svg#question", "compare", ['WIDTH' => 11,'HEIGHT' => 14]);?>
														<?if (!$arConfig['ONE_CLICK_BUY']):?>
															<span class="info-buttons__item-text"><?=Loc::getMessage('QUESTION_FORM_TITLE')?></span>
														<?endif;?>
													</span>
												</a>
											</div>
										<?endif;?>
										<?if ($arConfig['DISPLAY_COMPARE']):?>
											<?$arTransferData = [
												'ID' => (isset($arConfig['ITEM_ID']) && $arItem['ID'] !== $arConfig['ITEM_ID'] ? $arConfig['ITEM_ID'] : $arItem['ID']),
												'IBLOCK_ID' => $arConfig['CATALOG_IBLOCK_ID'],
											];?>
											<div class="info-buttons__item info-buttons__item--question side-icons__item--compare">
												<a
												href="javascript:void(0)"
												rel="nofollow"
												class="btn btn-default <?=$arConfig['BTN_CALLBACK_CLASS']?> <?=($arConfig['ONE_CLICK_BUY'] || $arConfig['QUESTION_BTN'] ? '' : 'btn-wide')?> js-item-action"
												data-action="compare"
												data-id="<?=$arTransferData['ID'];?>"
												data-item_compare='<?=str_replace('\'', '"', \CUtil::PhpToJsObject($arTransferData))?>' 
												title="<?=Loc::getMessage('COMPARE_ITEM')?>" 
												data-title="<?=Loc::getMessage('COMPARE_ITEM')?>" 
												data-title_compared="<?=Loc::getMessage('COMPARE_ITEM_REMOVE')?>"
												>
													<span class="fill-use-fff">
														<?=\CAllcorp3::showSpriteIconSvg(SITE_TEMPLATE_PATH."/images/svg/catalog/item_icons.svg#compare_small", "compare", ['WIDTH' => 14,'HEIGHT' => 14]);?>
														<?if (!$arConfig['ONE_CLICK_BUY'] && !$arConfig['QUESTION_BTN']):?>
															<span class="info-buttons__item-text"><?=Loc::getMessage('COMPARE_ITEM')?></span>
														<?endif;?>
													</span>
												</a>
											</div>
										<?endif;?>
									</div>
								<?endif;?>
							<?endif;?>
						</div>
					<?endif;?>
				<?else:?>
					<?if (!$bDetail):?>
						<?// element more block?>
						<a href="<?=$arItem['DETAIL_PAGE_URL']?>" class="btn btn-default btn-actions__inner btn-wide <?=$arConfig['BTN_CLASS']?> <?=$arConfig['BTN_CLASS_MORE']?> js-replace-more">
							<?=$sMoreText;?>
						</a>
					<?endif;?>
				<?endif;?>
			<?if ($arConfig['WRAPPER']):?>
			</div>
			<?endif;?>
			<?if ($arConfig['JS_CLASS']):?>
			</div>
			<?endif;?>
			<?$html = ob_get_contents();
			ob_end_clean();

			// event for manipulation
			foreach (GetModuleEvents(self::MODULE_ID, 'OnAspro'.ucfirst(__FUNCTION__), true) as $arEvent) {
				ExecuteModuleEventEx($arEvent, array($arConfig, &$html));
			}
			if ($arConfig['RETURN']) {
				return $html;
			} else {
				echo $html;
			}
			?>
		<?}
		/*
		 Remove all nondigits symbols
		 @param $price
		 @return cleared price
		*/
		public static function clearPriceFromString($price = 0) {
			if ($price) {
				$price = str_replace('&#8381;','', $price); // replace RUB symbol
				return (int)preg_replace('/\D/', '', $price);
			}
		}

		public static function getDotsHTML($count, $dotsClasses = '', $animationSpeed = false)
		{
			$animationSpeed = $animationSpeed ? 'style="animation-duration: '.($animationSpeed / 1000).'s;"' : '';
			?>
			<div class="owl-carousel__dots <?=$dotsClasses?>">

				<?for($i = 0;$i < $count;$i++) {?>
					<div class='owl-carousel__dot border-theme-active <?=$animationSpeed ? '' : 'bg-theme-active'?>'>
						<?if($i == 0):?>
							<div class="owl-carousel__dot-scroll"></div>
						<?endif;?>
						<div class="owl-carousel__dot-pie bg-theme-active-child owl-carousel__dot-spinner" <?=$animationSpeed?>></div>
						<div class="owl-carousel__dot-pie bg-theme-active-child owl-carousel__dot-left-side" <?=$animationSpeed?>></div>
						<div class="owl-carousel__dot-pie bg-theme-active-child owl-carousel__dot-right-side" <?=$animationSpeed?>></div>
					</div>
				<?}?>

			</div>
		<?}

		public static function getItemMapHtml($arOptions)
		{
			if ($handler = self::getCustomFunc(__FUNCTION__)) {
				return call_user_func_array($handler, [$arOptions]);
			}

			$arItem = $arOptions['ITEM'];
			$arParams = $arOptions['PARAMS'];
			$bShowQuestionBtn = ($arOptions['SHOW_QUESTION_BTN'] === 'Y');
			$bShowSocial = ($arOptions['SHOW_SOCIAL'] === 'Y');
			$bShowClose = ($arOptions['SHOW_CLOSE'] === 'Y');
			$bShowTitle = ($arOptions['SHOW_TITLE'] === 'Y' && $arParams['TITLE']);

			$btnClass = (isset($arParams['BTN_CLASS']) && $arParams['BTN_CLASS'] ? $arParams['BTN_CLASS'] : 'font_14');

			// $html = '<div>';
				if ($bShowTitle) {
					$html .= '<div class="map-detail-items__item-subtitle font_13 color_999">'.$arParams['TITLE'].'</div>';
				}
				$html .= '<div class="map-detail-items__item-title switcher-title font_18 color_333 font_large">'.(strlen($arItem['URL']) ? '<a class="dark_link" href="'.$arItem['URL'].'">' : '').$arItem['NAME'].(strlen($arItem['URL']) ? '</a>' : '').'</div>';
				if ($arItem['METRO'] || $arItem['SCHEDULE'] || $arItem['EMAIL'] || $arItem['PHONE'] ) {
					$html .= '<div class="map-detail-items__item-props">';
					if ($arItem['METRO']) {
						$html .= '<div class="map-detail-items__item-property">';
							$html .= '<div class="map-detail-items__item-property-title font_13 muted">'.$arItem['DISPLAY_PROPERTIES']['METRO']['NAME'].'</div>';
							$html .= '<div class="map-detail-items__item-property-value color_333">'.$arItem['METRO_HTML'].'</div>';
						$html .= '</div>';
					}
					if ($arItem['SCHEDULE']) {
						$html .= '<div class="map-detail-items__item-property">';
							$html .= '<div class="map-detail-items__item-property-title font_13 muted">'.$arItem['DISPLAY_PROPERTIES']['SCHEDULE']['NAME'].'</div>';
							$html .= '<div class="map-detail-items__item-property-value color_333">'.$arItem['SCHEDULE'].'</div>';
						$html .= '</div>';
					}
					if ($arItem['PHONE']) {
						$html .= '<div class="map-detail-items__item-property">';
							$html .= '<div class="map-detail-items__item-property-title font_13 muted">'.$arItem['DISPLAY_PROPERTIES']['PHONE']['NAME'].'</div>';
							$html .= '<div class="map-detail-items__item-property-value color_333">'.$arItem['PHONE_HTML'].'</div>';
						$html .= '</div>';
					}
					if ($arItem['EMAIL']) {
						$html .= '<div class="map-detail-items__item-property">';
							$html .= '<div class="map-detail-items__item-property-title font_13 muted">'.$arItem['DISPLAY_PROPERTIES']['EMAIL']['NAME'].'</div>';
							if($arItem['EMAIL_HTML']) {
								$html .= '<div class="map-detail-items__item-property-value color_333">'.$arItem['EMAIL_HTML'].'</div>';
							} else {
								$html .= '<div class="map-detail-items__item-property-value color_333"><a class="dark_link" href="mailto:' . $arItem['EMAIL'] . '">' . $arItem['EMAIL'] . '</a></div>';
							}
						$html .= '</div>';
					}
					$html .= '</div>';
				}
				if ($arItem['SOCIAL_INFO'] && $bShowSocial) {
					$html .= '<div class="social-list social-list--mt-30">';
						foreach ($arItem['SOCIAL_INFO'] as $arSoc) {
							$html .= '<a class="social-list__item fill-theme-hover" rel="nofollow" href="'.$arSoc['VALUE'].'">';
								$html .= \CAllcorp3::showIconSvg('', $arSoc['PATH']);
							$html .= '</a>';
						}
					$html .= '</div>';
				}
				if ($bShowClose) {
					$html .= '<div class="map-detail-items__item-svg muted fill-theme-hover">';
						$html .= '<svg class="map-detail-items__item-close fill-999" width="14" height="14" viewBox="0 0 14 14"><path data-name="Rounded Rectangle 568 copy 16" class="cls-1" d="M1009.4,953l5.32,5.315a0.987,0.987,0,0,1,0,1.4,1,1,0,0,1-1.41,0L1008,954.4l-5.32,5.315a0.991,0.991,0,0,1-1.4-1.4L1006.6,953l-5.32-5.315a0.991,0.991,0,0,1,1.4-1.4l5.32,5.315,5.31-5.315a1,1,0,0,1,1.41,0,0.987,0.987,0,0,1,0,1.4Z" transform="translate(-1001 -946)"></path></svg>';
					$html .= '</div>';
				}
				if ($bShowQuestionBtn) {
					$html .= '<div class="map-detail-items__item-buttons">';
						$html .= '<span class="btn btn-default  btn-transparent-border animate-load '.$btnClass.'" data-event="jqm" data-param-id="'.\CAllcorp3::getFormID("aspro_allcorp3_question").'" data-name="question">'.GetMessage('SEND_MESSAGE_BUTTON').'</span>';
					$html .= '</div>';
				}
			// $html .= '</div>';

			foreach(GetModuleEvents(self::MODULE_ID, 'onGetItemMapHtml', true) as $arEvent) // event for manipulation map item block
				ExecuteModuleEventEx($arEvent, array($arOptions, &$html));

			return $html;
		}

		public static function prepareShopListArray($arShops)
		{
			$arFormatShops=array();

			$arPlacemarks = array();

			if (is_array($arShops)) {
				foreach ($arShops as $i => $arShop) {
					$arShop['GPS_S'] = false;
					$arShop['GPS_N'] = false;
					if ($arStoreMap = explode(',', $arShop['MAP'])) {
						$arShop['GPS_S'] = $arStoreMap[0];
						$arShop['GPS_N'] = $arStoreMap[1];
					}

					if ($arShop['GPS_S'] && $arShop['GPS_N']) {
						$mapLAT += $arShop['GPS_S'];
						$mapLON += $arShop['GPS_N'];

						$html = self::getItemMapHtml([
							'ITEM' => $arShop,
							'SHOW_QUESTION_BTN' => 'Y'
						]);

						$arPlacemarks[] = array(
							"ID" => $arShop["ID"],
							"LAT" => $arShop['GPS_S'],
							"LON" => $arShop['GPS_N'],
							"TEXT" => $html,
							//"HTML" => '<div class="title">'.(strlen($arShop['URL']) ? '<a href="'.$arShop['URL'].'">' : '').$arShop["ADDRESS"].(strlen($arShop['URL']) ? '</a>' : '').'</div><div class="info-content">'.($arShop['METRO'] ? $arShop['METRO_PLACEMARK_HTML'] : '').(strlen($arShop['SCHEDULE']) ? '<div class="schedule">'.$arShop['SCHEDULE'].'</div>' : '').$str_phones.(strlen($arShop['EMAIL']) ? '<div class="email"><a rel="nofollow" href="mailto:'.$arShop['EMAIL'].'">'.$arShop['EMAIL'].'</a></div>' : '').'</div>'.(strlen($arShop['URL']) ? '<a rel="nofollow" class="button" href="'.$arShop['URL'].'"><span>'.GetMessage('DETAIL').'</span></a>' : '')
						);
					}
					$arShops[$i] = $arShop;
				}
			}
			$arFormatShops["SHOPS"]=$arShops;
			$arFormatShops["PLACEMARKS"]=$arPlacemarks;
			$arFormatShops["POINTS"]=array(
				"LAT" => $mapLAT,
				"LON" => $mapLON,
			);

			return $arFormatShops;
		}

		public static function showDiscountCounter($arOptions = [])
		{
			$arDefaultOptions = [
				'WRAPPER' => false,
				'WRAPPER_CLASS' => '',
				'ICONS' => false,
				'IS_COMPACT' => true,
				'TYPE' => 'type-1',
				'DATE' => $arOptions['ITEM']['ACTIVE_TO'],
				'ITEM' => []
			];
			$arConfig = array_merge($arDefaultOptions, $arOptions);

			if ($handler = self::getCustomFunc(__FUNCTION__)) {
				return call_user_func_array($handler, [$arConfig]);
			}?>

			<?
			$bShowDiscont = ($arConfig['DATE'] && time() <= strtotime($arConfig['DATE']));
			?>

			<?ob_start();?>
				<?if ($arConfig['ITEM'] && $bShowDiscont):?>
					<?if ($arConfig['WRAPPER']):?>
						<div class="countdown_wrapper <?=$arConfig['WRAPPER_CLASS']?>">
					<?endif;?>

					<div class="countdown countdown-<?=$arConfig['TYPE']?><?=($arConfig['ICONS'] ? ' countdown--icons' : '');?><?=($arConfig['IS_COMPACT'] ? ' compact' : '');?>">

						<div class="countdown__inner bordered rounded-3">
							<span class="countdown__active-to hidden"><?=$arConfig['DATE'];?></span>
							<?if ($arConfig['ICONS']):?>
								<span class="countdown__icon countdown__item">
									<?=\CAllcorp3::showIconSvg('', SITE_TEMPLATE_PATH.'/images/svg/catalog/Discount.svg');?>
								</span>
							<?endif;?>
							<span class="countdown__items"><span class="countdown__item">0</span><span class="countdown__item">0</span><span class="countdown__item">0</span><span class="countdown__item">0</span></span>
						</div>

					</div>

					<?if ($arConfig['WRAPPER']):?>
						</div>
					<?endif;?>
				<?endif;?>
			<?$html = ob_get_contents();
			ob_end_clean();

			// event for manipulation
			foreach (GetModuleEvents(self::MODULE_ID, 'OnAspro'.ucfirst(__FUNCTION__), true) as $arEvent) {
				ExecuteModuleEventEx($arEvent, array($arConfig, &$html));
			}

			echo $html;?>
		<?}

		public static function showTitleInLeftBlock($arOptions = [])
		{
			global $APPLICATION;
			$arDefaultOptions = [
				'WRAPPER_CLASS' => 'flexbox--w34-f992',
				'STICKY_CLASS' => 'flexbox--mb20-t991',
				'VISIBLE' => true,
				'PATH' => 'sale-list',
				'POSITION' => 'TOP',
				'PARAMS' => [],
				'ITEM' => []
			];
			$arConfig = array_merge($arDefaultOptions, $arOptions);

			if ($handler = self::getCustomFunc(__FUNCTION__)) {
				return call_user_func_array($handler, [$arConfig]);
			}?>

			<?
			$bShowTitle = $arConfig['PARAMS']['TITLE'];
			$bShowTitleLink = $arConfig['PARAMS']['RIGHT_TITLE'] && $arConfig['PARAMS']['RIGHT_LINK'];
			?>
			<?ob_start();?>
				<?if ($bShowTitle && $arConfig['VISIBLE']):?>

					<?if ($arConfig['WRAPPER_CLASS']):?>
						<div class="<?=$arConfig['WRAPPER_CLASS']?>">
					<?endif?>

					<div class="sticky-block <?=$arConfig['STICKY_CLASS']?>">
						<?
						$path = SITE_DIR.'/include/mainpage/text-under-title/'.$arConfig['PATH'].'.php';
						$bShowContent = \CAllcorp3::checkContentFile($path) && ($arConfig['PARAMS']['SHOW_PREVIEW_TEXT'] !== 'N' && $arConfig['PARAMS']['SHOW_PREVIEW_TEXT'] !== false);
						?>
						<?if ($arConfig['PARAMS']['SUBTITLE']):?>
							<div class="index-block__subtitle index-block__subtitle--margined-f992"><?=$arConfig['PARAMS']['SUBTITLE']?></div>
						<?endif;?>

						<?if($bShowTitle):?>
							<h3 class="index-block__title switcher-title<?=($bShowContent ? ' line' : '')?>"><?=$arConfig['PARAMS']['TITLE']?></h3>
						<?endif;?>

						<?if($bShowContent):?>
							<div class="index-block__preview font-24 line-height-28">
								<?$APPLICATION->IncludeFile($path, Array(), Array("MODE" => "html", "TEMPLATE" => "include_area.php", "NAME" => GetMessage("SECTIONS_INCLUDE_TEXT")));?>
							</div>
						<?endif;?>

						<?if ($bShowTitleLink):?>
							<a href="<?=preg_replace('/(?<!:)[\/]{2,}/', '/', SITE_DIR.$arConfig['PARAMS']['RIGHT_LINK'])?>" class="btn btn-transparent-border btn-lg index-block__btn"><?=$arConfig['PARAMS']['RIGHT_TITLE'] ;?></a>
						<?endif;?>
					</div>

					<?if ($arConfig['WRAPPER_CLASS']):?>
						</div>
					<?endif?>

				<?endif?>
			<?$html = ob_get_contents();
			ob_end_clean();

			// event for manipulation
			foreach (GetModuleEvents(self::MODULE_ID, 'OnAspro'.ucfirst(__FUNCTION__), true) as $arEvent) {
				ExecuteModuleEventEx($arEvent, array($arConfig, &$html));
			}

			echo $html;
		}

		public static function showTitleBlock($arOptions = [])
		{
			global $APPLICATION;
			$arDefaultOptions = [
				'WRAPPER' => true,
				'VISIBLE' => true,
				'PATH' => 'sale-list',
				'POSITION' => 'TOP',
				'CENTER_BLOCK' => '',
				'PARAMS' => [],
				'ITEM' => []
			];
			$arConfig = array_merge($arDefaultOptions, $arOptions);

			if ($handler = self::getCustomFunc(__FUNCTION__)) {
				return call_user_func_array($handler, [$arConfig]);
			}?>

			<?
			$bShowTitle = $arConfig['PARAMS']['TITLE'] && $arConfig['PARAMS']['SHOW_TITLE'];
			$bShowTitleLink = $arConfig['PARAMS']['RIGHT_TITLE'] && $arConfig['PARAMS']['RIGHT_LINK'];
			$bTitleCenter = $arConfig['PARAMS']['TITLE_POSITION'] === 'CENTERED';
			?>

			<?ob_start();?>
				<?if($bShowTitle && $arConfig['VISIBLE']):?>
					<?if($arConfig['WRAPPER']):?>
					<div class="maxwidth-theme">
					<?endif?>
						<?
						$path = SITE_DIR.'/include/mainpage/text-under-title/'.$arConfig['PATH'].'.php';
						$bShowContent = \CAllcorp3::checkContentFile($path) && ($arConfig['PARAMS']['SHOW_PREVIEW_TEXT'] !== 'N' && $arConfig['PARAMS']['SHOW_PREVIEW_TEXT'] !== false);
						$bTitleLeft = $arConfig['PARAMS']['TITLE_POSITION'] === 'LEFT' && $bShowContent;
						?>

						<?ob_start()?>
							<?if($bShowTitleLink):?>
								<?$bExternalLink = $arConfig['PARAMS']['RIGHT_LINK_EXTERNAL']?>
								<?if ($bExternalLink):?>
									<a class="index-block__link dark_link stroke-theme-hover right_link_block" href="<?=$arConfig['PARAMS']['RIGHT_LINK']?>" target="_blank" rel="nofollow">
								<?else:?>
									<a class="index-block__link dark_link stroke-theme-hover right_link_block" href="<?=preg_replace('/(?<!:)[\/]{2,}/', '/', SITE_DIR.$arConfig['PARAMS']['RIGHT_LINK'])?>" title="<?=Loc::getMessage('REDIRECT_SECTION')?>">
								<?endif;?>
									<?=$arConfig['PARAMS']['RIGHT_TITLE'];?>
									<span class="index-block__arrow"><?=\CAllcorp3::showIconSvg(' stroke_999', SITE_TEMPLATE_PATH.'/images/svg/Arrow_map.svg');?></span>
								</a>
							<?endif;?>
						<?$htmlLinkAll = ob_get_clean()?>

						<?if ($bTitleLeft):?>
							<div class="index-block__space-wrapper flexbox flexbox--direction-row flexbox--justify-beetwen index-block--mb-59">
								<div class="index-block__part--left <?=$arConfig['LEFT_PART_CLASS']?>">	
						<?endif;?>

						<?if ($arConfig['PARAMS']['SUBTITLE']):?>
							<div class="index-block__subtitle<?=$bTitleCenter ? ' text-center' : ''?><?=$bTitleLeft ? ' index-block__subtitle--mb-9' : ''?>"><?=$arConfig['PARAMS']['SUBTITLE']?></div>
						<?endif;?>
						<div class="index-block__title-wrapper <?=$arConfig['CENTER_BLOCK'] ? 'index-block__title-wrapper--with-center-block' : ''?> <?=$bTitleCenter ? 'index-block__title-wrapper--centered' : ''?> <?=($bShowContent ? '' : 'index-block__title-wrapper--mb-52');?>">
							<?if($bShowTitle):?>
								<div class="index-block__part--left">
									<h3 class="index-block__title switcher-title<?=($bShowContent ? ' line' : '')?>"><?=$arConfig['PARAMS']['TITLE']?></h3>
								</div>

								<?if ($arConfig['CENTER_BLOCK']):?>
									<div class="index-block__part--center">
										<?=$arConfig['CENTER_BLOCK'];?>
									</div>
								<?endif;?>

								<div class="index-block__part--right<?=(!$bTitleLeft ? '' : ' visible-t991')?>">
									<?=$htmlLinkAll;?>
								</div>
							<?endif;?>
						</div>

						<?if ($bTitleLeft):?>
							</div>
							<div class="index-block__part--right flexbox--direction-column">
						<?endif;?>

						<?if($bShowContent):?>
							<div class="index-block__preview <?=$bTitleCenter ? 'index-block__preview--centered' : ''?> <?=$bTitleLeft ? 'index-block__preview--right' : ' index-block__preview--mb-66'?>">
								<?$APPLICATION->IncludeFile($path, Array(), Array("MODE" => "html", "TEMPLATE" => "include_area.php", "NAME" => GetMessage("SECTIONS_INCLUDE_TEXT")));?>
								<?if ($bTitleLeft):?>
									<div class="hidden-xs hidden-sm"><?=$htmlLinkAll;?></div>
								<?endif;?>
							</div>
						<?endif;?>

						<?if ($bTitleLeft):?>
								</div>
							</div>
						<?endif;?>
					<?if($arConfig['WRAPPER']):?>
					</div>
					<?endif;?>
				<?endif;?>
			<?$html = ob_get_contents();
			ob_end_clean();

			// event for manipulation
			foreach (GetModuleEvents(self::MODULE_ID, 'OnAspro'.ucfirst(__FUNCTION__), true) as $arEvent) {
				ExecuteModuleEventEx($arEvent, array($arConfig, &$html));
			}

			echo $html;?>
		<?}

		public static function showStickers($arOptions = [])
		{
			global $APPLICATION;
			$arDefaultOptions = [
				'TYPE' => '',
				'WRAPPER' => '',
				'ITEM' => [],
				'PARAMS' => [],
			];
			$arConfig = array_merge($arDefaultOptions, $arOptions);

			if ($handler = self::getCustomFunc(__FUNCTION__)) {
				return call_user_func_array($handler, [$arConfig]);
			}

			$arParams = $arConfig['PARAMS'];
			$arItem = $arConfig['ITEM'];
			if($arItem):?>
				<?ob_start();?>

				<?$prop = ($arParams["STIKERS_PROP"] ? $arParams["STIKERS_PROP"] : "HIT");?>
				<?$saleSticker = ($arParams["SALE_STIKER"] ? $arParams["SALE_STIKER"] : "SALE_TEXT");?>

				<?if($arItem["PROPERTIES"][$prop]['VALUE_XML_ID'] ||
					$arItem["PROPERTIES"][$saleSticker]["VALUE"]):?>
					<?if($arConfig['WRAPPER']):?>
						<div class="<?=$arConfig['WRAPPER']?>">
					<?endif;?>
					<div class="sticker sticker--upper">

						<?foreach($arItem["PROPERTIES"][$prop]['VALUE_XML_ID'] as $key => $class):?>
							<div><div class="sticker__item sticker_item--<?=strtolower($class);?> font_9"><?=$arItem['PROPERTIES']['HIT']['VALUE'][$key]?></div></div>
						<?endforeach;?>

						<?if($arItem["PROPERTIES"][$saleSticker]["VALUE"]):?>
							<div><div class="sticker__item sticker_item--sale-text font_9"><?=$arItem["PROPERTIES"][$saleSticker]["VALUE"];?></div></div>
						<?endif;?>
					</div>
					<?if($arConfig['WRAPPER']):?>
						</div>
					<?endif;?>
				<?endif;?>
				<?$html = ob_get_contents();
				ob_end_clean();

				// event for manipulation
				foreach (GetModuleEvents(self::MODULE_ID, 'OnAspro'.ucfirst(__FUNCTION__), true) as $arEvent) {
					ExecuteModuleEventEx($arEvent, array($arConfig, &$html));
				}

				echo trim($html);?>
			<?endif;?>
		<?}

		public static function showBlockHtml($arOptions = [])
		{
			global $APPLICATION;
			$arDefaultOptions = [
				'TYPE' => '',
				'BASE_PATH' => SITE_DIR.'/include/blocks/',
				'FILE' => '',
				'PARAMS' => []
			];
			$arConfig = array_merge($arDefaultOptions, $arOptions);

			if ($handler = self::getCustomFunc(__FUNCTION__)) {
				return call_user_func_array($handler, [$arConfig]);
			}

			if ($arConfig['FILE']) {
				$path = str_replace('//', '/', $_SERVER['DOCUMENT_ROOT'].$arConfig['BASE_PATH'].$arConfig['FILE']);
				if (file_exists($path)) {
					$customFile = str_replace('.php', '_custom.php', $arConfig['FILE']);
					$customPath = str_replace('//', '/', $_SERVER['DOCUMENT_ROOT'].$arConfig['BASE_PATH'].$customFile);
					if (file_exists($customPath)) {
						include($customPath);
					} else {
						include($path);
					}
				}
			}
		}

		public static function getItemsYear($arOptions = [])
		{
			global $APPLICATION;
			$arDefaultOptions = [
				'TYPE' => '',
				'PARAMS' => [],
				'FILTER' => []
			];
			$arConfig = array_merge($arDefaultOptions, $arOptions);

			if ($handler = self::getCustomFunc(__FUNCTION__)) {
				return call_user_func_array($handler, [$arConfig]);
			}

			$arYears = [];

			if ($arConfig['FILTER'] && $arConfig['PARAMS']['IBLOCK_ID']) {
				$arItems = \CAllcorp3Cache::CIBlockElement_GetList(
					[
						'SORT' => 'ASC',
						'NAME' => 'ASC',
						'CACHE' => [
							'TAG' => \CAllcorp3Cache::GetIBlockCacheTag($arConfig['PARAMS']['IBLOCK_ID'])
						]
					],
					$arConfig['FILTER'],
					false,
					false,
					['ID', 'NAME', 'ACTIVE_FROM']
				);
				if ($arItems) {
					foreach ($arItems as $arItem) {
						if ($arItem['ACTIVE_FROM']) {
							if ($arDateTime = \ParseDateTime($arItem['ACTIVE_FROM'], FORMAT_DATETIME)) {
								$arYears[$arDateTime['YYYY']] = $arDateTime['YYYY'];
							}
						}
					}
					if ($arYears && count($arYears) > 1) {
						rsort($arYears);
					} else {
						$arYears = [];
					}
				}
			}
			return $arYears;
		}

		public static function getSectionsForMenu($arOptions = [])
		{
			global $APPLICATION;
			$arDefaultOptions = [
				'TYPE' => '',
				'PARAMS' => [],
			];
			$arConfig = array_merge($arDefaultOptions, $arOptions);

			if ($handler = self::getCustomFunc(__FUNCTION__)) {
				return call_user_func_array($handler, [$arConfig]);
			}

			$arExtParams = array(
				'IBLOCK_ID' => $arConfig['PARAMS']['IBLOCK_ID'],
				'MENU_PARAMS' => array(
					'MENU_SHOW_SECTIONS' => 'Y'
				),
				'SECTION_FILTER' => array(),	// custom filter for sections (through array_merge)
				'SECTION_SELECT' => array(),	// custom select for sections (through array_merge)
				'ELEMENT_FILTER' => array(),	// custom filter for elements (through array_merge)
				'ELEMENT_SELECT' => array(),	// custom select for elements (through array_merge)
				'MENU_TYPE' => 'sections-menu',
			);
			\CAllcorp3::getMenuChildsExt($arExtParams, $aMenuLinksExt, true);
			$arSections = \CAllcorp3::getChilds($aMenuLinksExt);

			return $arSections;
		}

		public static function getSectionsWithElementCount($arOptions = [])
		{
			global $APPLICATION;
			$arDefaultOptions = [
				'TYPE' => '',
				'SECTION' => [],
				'PARAMS' => [],
				'FILTER' => []
			];
			$arConfig = array_merge($arDefaultOptions, $arOptions);

			if ($handler = self::getCustomFunc(__FUNCTION__)) {
				return call_user_func_array($handler, [$arConfig]);
			}

			$arResult = [
				'TAGS' => [],
				'SECTIONS' => []
			];

			if ($arConfig['FILTER'] && $arConfig['PARAMS']['IBLOCK_ID']) {

				/* get menu items*/
				$arResult['SECTIONS'] = self::getSectionsForMenu([
					'PARAMS' => $arConfig['PARAMS']
				]);
				/* */

				/* remove useless filter params */
				if (
					isset($arConfig['FILTER']['CODE']) ||
					isset($arConfig['FILTER']['SECTION_CODE'])
				) {
					unset($arConfig['FILTER']['CODE']);
					unset($arConfig['FILTER']['SECTION_CODE']);
				}
				if (
					isset($arConfig['FILTER']['ID']) ||
					isset($arConfig['FILTER']['SECTION_ID'])
				) {
					unset($arConfig['FILTER']['ID']);
					unset($arConfig['FILTER']['SECTION_ID']);
				}
				/* */

				/* get tags and items count */
				if ($arResult['SECTIONS']) {

					if ($arConfig['SECTION']) {
						$cur_page = $GLOBALS['APPLICATION']->GetCurPage(true);
						$cur_page_no_index = $GLOBALS['APPLICATION']->GetCurPage(false);
					}

					foreach ($arResult['SECTIONS'] as $key => $arSection) {
						$arElements = \CAllcorp3Cache::CIBlockElement_GetList(
							array(
								'CACHE' => array(
									'TAG' => \CAllcorp3Cache::GetIBlockCacheTag($arConfig['PARAMS']['IBLOCK_ID']),
									'MULTI' => 'Y'
								)
							),
							array_merge(
								$arConfig['FILTER'],
								array(
									"SECTION_ID" => $arSection["PARAMS"]["ID"],
									"INCLUDE_SUBSECTIONS" => "Y"
								)
							),
							false,
							false,
							array('ID', 'TAGS', 'IBLOCK_SECTION_ID')
						);
						if (!$arElements) {
							unset($arResult['SECTIONS'][$key]);
						} else {
							foreach ($arElements as $arElement) {
								if ($arElement['TAGS']) {
									if ($arConfig['SECTION'] && $arConfig['SECTION']['ID']) {
										if ($arElement['IBLOCK_SECTION_ID'] == $arConfig['SECTION']['ID']) {
											$arResult['TAGS'][] = explode(',', $arElement['TAGS']);
										}
									} else {
										$arResult['TAGS'][] = explode(',', $arElement['TAGS']);
									}
								}
							}
							$arResult['SECTIONS'][$key]['ELEMENT_COUNT'] = count($arElements);
							if ($arConfig['SECTION']) {
								$arResult['SECTIONS'][$key]['CURRENT'] = \CMenu::IsItemSelected($arSection['LINK'], $cur_page, $cur_page_no_index);
								if ($arSection['CHILD']) {
									foreach ($arSection['CHILD'] as $key2 => $arChild) {
										if (\CMenu::IsItemSelected($arChild['LINK'], $cur_page, $cur_page_no_index)) {
											$arResult['SECTIONS'][$key]['CHILD'][$key2]['CURRENT'] = 'darken bold';
											$arResult['SECTIONS'][$key]['CURRENT'] = true;
										}
									}
								}
							}
						}
					}
				} else {
					$arElements = \CAllcorp3Cache::CIBlockElement_GetList(
						array(
							'CACHE' => array(
								'TAG' => \CAllcorp3Cache::GetIBlockCacheTag($arConfig['PARAMS']['IBLOCK_ID']),
								'MULTI' => 'Y'
							)
						),
						$arItemFilter,
						false,
						false,
						array('ID', 'TAGS')
					);

					foreach ($arElements as $arElement) {
						if ($arElement['TAGS']) {
							$arResult['TAGS'][] = explode(',', $arElement['TAGS']);
						}
					}
				}
			}
			return $arResult;
		}

		public static function showSideIcons($arOptions = [])
		{
			global $APPLICATION;
			$arDefaultOptions = [
				'TYPE' => '',
				'DOP_CLASS' => '',
				'RETURN' => false,
				'ITEM' => [],
				'PARAMS' => [],
			];
			$arConfig = array_merge($arDefaultOptions, $arOptions);

			if ($handler = self::getCustomFunc(__FUNCTION__)) {
				return call_user_func_array($handler, [$arConfig]);
			}

			$arParams = $arConfig['PARAMS'];
			$arItem = $arConfig['ITEM'];
			
			$arParams['DISPLAY_COMPARE'] = $arParams['DISPLAY_COMPARE'] === true || $arParams['DISPLAY_COMPARE'] === 'true';
			$arParams['ORDER_VIEW'] = $arParams['ORDER_VIEW'] === true || $arParams['ORDER_VIEW'] === 'true' ;
			$bOrderButton = ($arItem["DISPLAY_PROPERTIES"]["FORM_ORDER"]["VALUE_XML_ID"] == "YES");
			$bOrderViewBasket = $arParams['ORDER_VIEW'];
			if($arItem):?>
				<?ob_start();?>
					<div class="side-icons js-replace-icons <?=$arConfig['DOP_CLASS'];?>">
						<?if ($arParams['USE_FAST_VIEW_PAGE_DETAIL'] != 'NO'):?>
							<?$sFastOrderText = $arParams['EXPRESSION_FOR_FAST_VIEW'];?>
							<div class="side-icons__item side-icons__item--fast-view bordered rounded-4">
								<a href="javascript:void(0)" rel="nofollow" data-event="jqm" title="<?=$sFastOrderText?>" data-name="fast_view" data-param-form_id="fast_view" data-param-iblock_id="<?=$arItem['IBLOCK_ID']?>" data-param-id="<?=$arItem['ID']?>" data-param-item_href="<?=urlencode($arItem['DETAIL_PAGE_URL'])?>">
									<?=\CAllcorp3::showIconSvg("side-search", SITE_TEMPLATE_PATH."/images/svg/catalog/Fancy_side.svg");?>
								</a>
							</div>
						<?endif;?>
						<?if ($arParams['SHOW_ONE_CLICK_BUY'] != 'N' && $bOrderButton && $bOrderViewBasket):?>
							<?$price = ($arItem['DISPLAY_PROPERTIES']['PRICE']['VALUE'] ? $arItem['DISPLAY_PROPERTIES']['PRICE']['VALUE'] : $arItem['PROPERTIES']['PRICE']['VALUE'])?>
							<div class="side-icons__item side-icons__item--ocb bordered rounded-4">
								<a href="javascript:void(0)" rel="nofollow" class="ocb" data-param-id="<?=\CAllcorp3::getFormID("aspro_allcorp3_quick_buy");?>" data-name="ocb" data-event="jqm" title="<?=Loc::getMessage('ONE_CLICK_BUY')?>" data-autoload-product_name="<?=\CAllcorp3::formatJsName($arItem['NAME'])?>" data-autoload-product_price="<?=\CAllcorp3::formatJsName($price)?>">
									<?=\CAllcorp3::showIconSvg("side-ocb", SITE_TEMPLATE_PATH."/images/svg/catalog/one_click.svg");?>
								</a>
							</div>
						<?endif;?>
						<?if ($arParams['DISPLAY_COMPARE']):?>
							<div class="side-icons__item side-icons__item--compare side-icons__item--fill bordered rounded-4">
								<?$arTransferData = [
									'ID' => (isset($arConfig['ITEM_ID']) && $arItem['ID'] !== $arConfig['ITEM_ID'] ? $arConfig['ITEM_ID'] : $arItem['ID']),
									'IBLOCK_ID' => $arConfig['CATALOG_IBLOCK_ID'] ?? $arItem['IBLOCK_ID'],
								];?>
								<a href="javascript:void(0)" rel="nofollow" class="js-item-action" data-action="compare" data-id="<?=$arTransferData['ID'];?>" data-item_compare='<?=str_replace('\'', '"', \CUtil::PhpToJsObject($arTransferData))?>' title="<?=Loc::getMessage('COMPARE_ITEM')?>" data-title="<?=Loc::getMessage('COMPARE_ITEM')?>" data-title_compared="<?=Loc::getMessage('COMPARE_ITEM_REMOVE')?>">
									<?=\CAllcorp3::showSpriteIconSvg(SITE_TEMPLATE_PATH."/images/svg/catalog/item_icons.svg#compare_small", "compare", [
										'WIDTH' => 14,
										'HEIGHT' => 14,
									]);?>
								</a>
							</div>
						<?endif;?>
					</div>
				<?$html = ob_get_contents();
				ob_end_clean();

				// event for manipulation
				foreach (GetModuleEvents(self::MODULE_ID, 'OnAspro'.ucfirst(__FUNCTION__), true) as $arEvent) {
					ExecuteModuleEventEx($arEvent, array($arConfig, &$html));
				}
				if ($arConfig['RETURN']) {
					return $html;
				} else {
					echo $html;
				}
				?>
			<?endif;?>
		<?}

		public static function showImage($arOptions = [])
		{
			global $APPLICATION;
			$arDefaultOptions = [
				'TYPE' => '',
				'CONTENT' => '',
				'WRAP_LINK' => true,
				'ADDITIONAL_WRAPPER_CLASS' => '',
				'ADDITIONAL_IMG_CLASS' => '',
				'RETURN' => false,
				'ITEM' => [],
				'PARAMS' => [],
				'STICKY' => false,
			];
			$arConfig = array_merge($arDefaultOptions, $arOptions);

			if ($handler = self::getCustomFunc(__FUNCTION__)) {
				return call_user_func_array($handler, [$arConfig]);
			}

			$arParams = $arConfig['PARAMS'];
			$arItem = $arConfig['ITEM'];
			?>
			<?ob_start();?>
				<div class="image-list <?=$arConfig['ADDITIONAL_WRAPPER_CLASS'];?>">
					<div class="image-list-wrapper js-image-block<?=($arConfig['STICKY'] ? ' sticky-block' : '')?>">
						<?self::showSideIcons([
							'TYPE' => $arConfig['TYPE'],
							'ITEM' => $arItem,
							'PARAMS' => $arParams,
							'CATALOG_IBLOCK_ID' => $arConfig['CATALOG_IBLOCK_ID'],
							'ITEM_ID' => $arConfig['ITEM_ID']
						]);?>
						<?self::showStickers([
							'TYPE' => $arConfig['TYPE'],
							'ITEM' => $arItem,
							'PARAMS' => $arParams,
						]);?>
						<?if($arParams['SHOW_GALLERY'] != 'N'):?>
							<?self::showSectionGallery([
								'TYPE' => $arConfig['TYPE'],
								'ADDITIONAL_IMG_CLASS' => $arConfig['ADDITIONAL_IMG_CLASS'],
								'ITEM' => $arItem,
								'PARAMS' => $arParams,
							]);?>
						<?else:?>
							<?self::showImg([
								'TYPE' => $arConfig['TYPE'],
								'WRAP_LINK' => $arConfig['WRAP_LINK'],
								'ADDITIONAL_IMG_CLASS' => $arConfig['ADDITIONAL_IMG_CLASS'],
								'ITEM' => $arItem,
								'PARAMS' => $arParams,
							]);?>
						<?endif;?>
					</div>
					<?if ($arConfig['CONTENT']):?>
						<?=$arConfig['CONTENT'];?>
					<?endif;?>
				</div>
			<?$html = ob_get_contents();
			ob_end_clean();

			// event for manipulation
			foreach (GetModuleEvents(self::MODULE_ID, 'OnAspro'.ucfirst(__FUNCTION__), true) as $arEvent) {
				ExecuteModuleEventEx($arEvent, array($arConfig, &$html));
			}
			if ($arConfig['RETURN']) {
				return $html;
			} else {
				echo $html;
			}?>
		<?}

		public static function showImg($arOptions = [])
		{
			global $APPLICATION;
			$arDefaultOptions = [
				'TYPE' => '',
				'WRAP_LINK' => true,
				'ADDITIONAL_IMG_CLASS' => '',
				'ITEM' => [],
				'PARAMS' => [],
			];
			$arConfig = array_merge($arDefaultOptions, $arOptions);

			if ($handler = self::getCustomFunc(__FUNCTION__)) {
				return call_user_func_array($handler, [$arConfig]);
			}

			$arParams = $arConfig['PARAMS'];
			$arItem = $arConfig['ITEM'];
			$dopClassImg = $arConfig['ADDITIONAL_IMG_CLASS'];
			$bHasParentImg = (isset($arItem['PARENT_IMG']) && $arItem['PARENT_IMG']);
			?>

			<?if($arItem):?>
				<?ob_start();?>

				<?
				$jsImgSrc = '';
				if ($bHasParentImg) {
					$arItem['PARENT_IMG'] = is_array($arItem['PARENT_IMG'])
						? $arItem['PARENT_IMG']['SRC']
						: \CFile::GetPath($arItem['PARENT_IMG']);
					$jsImgSrc = 'data-js="'.$arItem['PARENT_IMG'].'"';
				}
				?>

				<?if($arConfig['WRAP_LINK']):?>
					<?/*if($arConfig['ZOOM']):?>
						<a href="javascript:void(0)" rel="nofollow" class="image-list__link fancy-js">
					<?else:*/?>
						<a href="<?=$arItem["DETAIL_PAGE_URL"]?>" class="image-list__link">
					<?//endif;?>
				<?endif;?>
					<?
					$a_alt = (is_array($arItem["PREVIEW_PICTURE"]) && strlen($arItem["PREVIEW_PICTURE"]['DESCRIPTION']) ? $arItem["PREVIEW_PICTURE"]['DESCRIPTION'] : ($arItem['SELECTED_SKU_IPROPERTY_VALUES'] ? ($arItem["SELECTED_SKU_IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_ALT"] ? $arItem["SELECTED_SKU_IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_ALT"] : $arItem["NAME"]) : ($arItem["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_ALT"] ? $arItem["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_ALT"] : $arItem["NAME"])));

					$a_title = (is_array($arItem["PREVIEW_PICTURE"]) && strlen($arItem["PREVIEW_PICTURE"]['DESCRIPTION']) ? $arItem["PREVIEW_PICTURE"]['DESCRIPTION'] : ($arItem['SELECTED_SKU_IPROPERTY_VALUES'] ? ($arItem["SELECTED_SKU_IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_TITLE"] ? $arItem["SELECTED_SKU_IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_TITLE"] : $arItem["NAME"]) : ($arItem["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_TITLE"] ? $arItem["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_TITLE"] : $arItem["NAME"])));
					?>

					<?if (!empty($arItem["PREVIEW_PICTURE"]) ):?>
						<?
						$src = is_array($arItem["PREVIEW_PICTURE"]) ? $arItem["PREVIEW_PICTURE"]["SRC"] : \CFile::GetPath($arItem["PREVIEW_PICTURE"]);
						if ($arItem["DETAIL_PICTURE"]) {
							if (isset($arItem["DETAIL_PICTURE"]["SRC"])) {
								$bigSrc = $arItem["DETAIL_PICTURE"]["SRC"];
							} else {
								$bigSrc = \CFile::GetPath($arItem["DETAIL_PICTURE"]);
							}
						} else {
							$bigSrc = $src;
						}
						?>
						<img class="img-responsive <?=$dopClassImg;?>" src="<?=$src;?>" data-big="<?=$bigSrc?>" <?=$jsImgSrc;?> alt="<?=$a_alt;?>" title="<?=$a_title;?>" />
					<?elseif (!empty($arItem["DETAIL_PICTURE"])):?>
						<?if(isset($arItem["DETAIL_PICTURE"]["src"])):?>
							<?$img["src"] = $arItem["DETAIL_PICTURE"]["src"]?>
						<?else:?>
							<?$img = \CFile::ResizeImageGet($arItem["DETAIL_PICTURE"], array( "width" => 350, "height" => 350 ), BX_RESIZE_IMAGE_PROPORTIONAL,true );?>
						<?endif;?>
						<img class="img-responsive <?=$dopClassImg;?>" src="<?=$img["src"]?>" <?=$jsImgSrc;?> alt="<?=$a_alt;?>" title="<?=$a_title;?>" />
					<?else:?>
						<img class="img-responsive <?=$dopClassImg;?>" src="<?=SITE_TEMPLATE_PATH.'/images/svg/noimage_product.svg';?>" alt="<?=$a_alt;?>" title="<?=$a_title;?>" />
					<?endif;?>
				<?if($arConfig['WRAP_LINK']):?>
					</a>
				<?endif;?>

				<?$html = ob_get_contents();
				ob_end_clean();

				// event for manipulation
				foreach (GetModuleEvents(self::MODULE_ID, 'OnAspro'.ucfirst(__FUNCTION__), true) as $arEvent) {
					ExecuteModuleEventEx($arEvent, array($arConfig, &$html));
				}

				echo $html;?>
			<?endif;?>
		<?}

		public static function showSectionGallery($arOptions = [])
		{
			global $APPLICATION;
			$arDefaultOptions = [
				'TYPE' => '',
				'WRAP_LINK' => true,
				'RETURN' => false,
				'ZOOM' => true,
				'ADDITIONAL_IMG_CLASS' => '',
				'RESIZE' => [
					'WIDTH' => 2000,
					'HEIGHT' => 2000,
				],
				'ITEM' => [],
				'PARAMS' => [],
			];
			$arConfig = array_merge($arDefaultOptions, $arOptions);

			if ($handler = self::getCustomFunc(__FUNCTION__)) {
				return call_user_func_array($handler, [$arConfig]);
			}

			$arParams = $arConfig['PARAMS'];
			$arItem = $arConfig['ITEM'];
			$key = $arParams['GALLERY_KEY'] ? $arParams['GALLERY_KEY'] : 'GALLERY';
			$bReturn = $arConfig['RETURN'];
			$arResize = $arConfig['RESIZE'];
			$dopClassImg = $arConfig['ADDITIONAL_IMG_CLASS'];
			$bHasParentImg = (isset($arItem['PARENT_IMG']) && $arItem['PARENT_IMG']);

			if($arItem):?>
				<?ob_start();?>

					<?
					$jsImgSrc = '';
					if ($bHasParentImg) {
						$arItem['PARENT_IMG'] = is_array($arItem['PARENT_IMG'])
							? $arItem['PARENT_IMG']['SRC']
							: \CFile::GetPath($arItem['PARENT_IMG']);
						$jsImgSrc = 'data-js="'.$arItem['PARENT_IMG'].'"';
					}
					?>

					<?if($arItem[$key]):?>
						<?$count = count($arItem[$key]);?>
						<?if($arConfig['WRAP_LINK']):?>
							<?/*if($arConfig['ZOOM']):?>
								<a href="javascript:void(0)" rel="nofollow" class="image-list__link fancy-js">
							<?else:*/?>
								<a href="<?=$arItem["DETAIL_PAGE_URL"]?>" class="image-list__link">
							<?//endif;?>
						<?endif;?>
							<span class="section-gallery-wrapper js-replace-gallery flexbox">
								<?foreach($arItem[$key] as $i => $arGalleryItem):?>
									<?
									if($arResize) {
										$resizeImage = \CFile::ResizeImageGet($arGalleryItem["ID"], array("width" => $arResize['WIDTH'], "height" => $arResize['HEIGHT']), BX_RESIZE_IMAGE_PROPORTIONAL, true, array());
										$arGalleryItem['SRC'] = $resizeImage['src'];
										$arGalleryItem['HEIGHT'] = $resizeImage['height'];
										$arGalleryItem['WIDTH'] = $resizeImage['width'];
									}else{
										$arGalleryItem['SRC'] = \CFile::GetPath($arGalleryItem['ID']);
									}
									?>
									<span class="section-gallery-wrapper__item<?=(!$i ? ' active' : '');?>">
										<span class="section-gallery-wrapper__item-nav<?=($count > 1 ? ' ' : ' section-gallery-wrapper__item_hidden ');?>"></span>
										<img class="img-responsive <?=$dopClassImg?>" src="<?=$arGalleryItem["SRC"];?>" <?=$jsImgSrc;?> data-big="<?=$arGalleryItem["SRC"]?>" alt="<?=$arGalleryItem["ALT"];?>" title="<?=$arGalleryItem["TITLE"];?>" />
									</span>
								<?endforeach;?>
							</span>
						<?if($arConfig['WRAP_LINK']):?>
							</a>
						<?endif;?>
						<?if ($count > 1):?>
							<span class="section-gallery-nav hide-600">
								<span class="section-gallery-nav__wrapper">
									<?foreach($arItem[$key] as $i => $arGalleryItem):?>
										<span class="section-gallery-nav__item bg-theme-hover bg-theme-active<?=(!$i ? ' active' : '');?>"></span>
									<?endforeach;?>
								</span>
							</span>
						<?endif;?>
					<?else:?>
						<a href="<?=$arItem["DETAIL_PAGE_URL"]?>" class="image-list__link"><img class="img-responsive <?=$dopClassImg?>" src="<?=SITE_TEMPLATE_PATH.'/images/svg/noimage_product.svg';?>" alt="<?=$arItem["NAME"]?>" title="<?=$arItem["NAME"]?>" /></a>
					<?endif;?>
				<?$html = ob_get_contents();
				ob_end_clean();

				// event for manipulation
				foreach (GetModuleEvents(self::MODULE_ID, 'OnAspro'.ucfirst(__FUNCTION__), true) as $arEvent) {
					ExecuteModuleEventEx($arEvent, array($arConfig, &$html));
				}

				if(!$bReturn)
					echo $html;
				else
					return $html?>
			<?endif;?>
		<?}

		public static function getSliderForItem($arOptions = [])
		{
			global $APPLICATION;
			$arDefaultOptions = [
				'TYPE' => '',
				'PROP_CODE' => 'MORE_PHOTO',
				'ENCODE' => true,
				'ADD_DETAIL_SLIDER' => true,
				'ITEM' => [],
				'PARAMS' => [],
			];
			$arConfig = array_merge($arDefaultOptions, $arOptions);

			if ($handler = self::getCustomFunc(__FUNCTION__)) {
				return call_user_func_array($handler, [$arConfig]);
			}

			$arParams = $arConfig['PARAMS'];
			$arItem = $arConfig['ITEM'];

			$encode = ($arConfig['ENCODE'] === true);
			$addDetailToSlider = ($arConfig['ADD_DETAIL_SLIDER'] === true);
			$result = array();

			if (!empty($arItem) && is_array($arItem)) {

				if (
					'' != $arConfig['PROP_CODE'] &&
					isset($arItem['PROPERTIES'][$arConfig['PROP_CODE']]) &&
					'F' == $arItem['PROPERTIES'][$arConfig['PROP_CODE']]['PROPERTY_TYPE']
				) {
					if ('MORE_PHOTO' == $arConfig['PROP_CODE']  && isset($arItem['MORE_PHOTO']) && !empty($arItem['MORE_PHOTO'])) {
						foreach ($arItem['MORE_PHOTO'] as $onePhoto) {
							$alt = ($onePhoto["DESCRIPTION"] ? $onePhoto["DESCRIPTION"] : ($arItem['IPROPERTY_VALUES']['ELEMENT_DETAIL_PICTURE_FILE_ALT'] ? $arItem['IPROPERTY_VALUES']['ELEMENT_DETAIL_PICTURE_FILE_ALT'] : $arItem["NAME"]));
							$title = ($onePhoto["DESCRIPTION"] ? $onePhoto["DESCRIPTION"] : ($arItem['IPROPERTY_VALUES']['ELEMENT_DETAIL_PICTURE_FILE_TITLE'] ? $arItem['IPROPERTY_VALUES']['ELEMENT_DETAIL_PICTURE_FILE_TITLE'] : $arItem["NAME"]));
							if ($arItem['ALT_TITLE_GET'] == 'SEO') {
								$alt = ($arItem['IPROPERTY_VALUES']['ELEMENT_DETAIL_PICTURE_FILE_ALT'] ? $arItem['IPROPERTY_VALUES']['ELEMENT_DETAIL_PICTURE_FILE_ALT'] : $arItem["NAME"]);
								$title = ($arItem['IPROPERTY_VALUES']['ELEMENT_DETAIL_PICTURE_FILE_TITLE'] ? $arItem['IPROPERTY_VALUES']['ELEMENT_DETAIL_PICTURE_FILE_TITLE'] : $arItem["NAME"]);
							}
							$result[] = array(
								'ID' => (int)$onePhoto['ID'],
								'SRC' => ($encode ? \CHTTP::urnEncode($onePhoto['SRC'], 'utf-8') : $onePhoto['SRC']),
								'WIDTH' => (int)$onePhoto['WIDTH'],
								'HEIGHT' => (int)$onePhoto['HEIGHT'],
								'ALT' => $alt,
								'TITLE' => $title
							);
						}
						unset($onePhoto);
					} else {
						if (
							isset($arItem['DISPLAY_PROPERTIES'][$arConfig['PROP_CODE']]['FILE_VALUE']) &&
							!empty($arItem['DISPLAY_PROPERTIES'][$arConfig['PROP_CODE']]['FILE_VALUE'])
						) {
							$fileValues = (
							isset($arItem['DISPLAY_PROPERTIES'][$arConfig['PROP_CODE']]['FILE_VALUE']['ID']) ?
								array(0 => $arItem['DISPLAY_PROPERTIES'][$arConfig['PROP_CODE']]['FILE_VALUE']) :
								$arItem['DISPLAY_PROPERTIES'][$arConfig['PROP_CODE']]['FILE_VALUE']
							);
							foreach ($fileValues as &$oneFileValue) {
								$alt = ($oneFileValue["DESCRIPTION"] ? $oneFileValue["DESCRIPTION"] : ($arItem['IPROPERTY_VALUES']['ELEMENT_DETAIL_PICTURE_FILE_ALT'] ? $arItem['IPROPERTY_VALUES']['ELEMENT_DETAIL_PICTURE_FILE_ALT'] : $arItem["NAME"]));
								$title = ($oneFileValue["DESCRIPTION"] ? $oneFileValue["DESCRIPTION"] : ($arItem['IPROPERTY_VALUES']['ELEMENT_DETAIL_PICTURE_FILE_TITLE'] ? $arItem['IPROPERTY_VALUES']['ELEMENT_DETAIL_PICTURE_FILE_TITLE'] : $arItem["NAME"]));
								if($arItem['ALT_TITLE_GET'] == 'SEO')
								{
									$alt = ($arItem['IPROPERTY_VALUES']['ELEMENT_DETAIL_PICTURE_FILE_ALT'] ? $arItem['IPROPERTY_VALUES']['ELEMENT_DETAIL_PICTURE_FILE_ALT'] : $arItem["NAME"]);
									$title = ($arItem['IPROPERTY_VALUES']['ELEMENT_DETAIL_PICTURE_FILE_TITLE'] ? $arItem['IPROPERTY_VALUES']['ELEMENT_DETAIL_PICTURE_FILE_TITLE'] : $arItem["NAME"]);
								}
								$result[] = array(
									'ID' => (int)$oneFileValue['ID'],
									'SRC' => ($encode ? \CHTTP::urnEncode($oneFileValue['SRC'], 'utf-8') : $oneFileValue['SRC']),
									'WIDTH' => (int)$oneFileValue['WIDTH'],
									'HEIGHT' => (int)$oneFileValue['HEIGHT'],
									'ALT' => $alt,
									'TITLE' => $title
								);
							}
							if (isset($oneFileValue))
								unset($oneFileValue);
						} else {
							$propValues = $arItem['PROPERTIES'][$arConfig['PROP_CODE']]['VALUE'];
							if (!is_array($propValues))
								$propValues = array($propValues);

							foreach ($propValues as &$oneValue) {
								$oneFileValue = \CFile::GetFileArray($oneValue);
								if (isset($oneFileValue['ID'])) {
									$alt = ($oneFileValue["DESCRIPTION"] ? $oneFileValue["DESCRIPTION"] : ($arItem['IPROPERTY_VALUES']['ELEMENT_DETAIL_PICTURE_FILE_ALT'] ? $arItem['IPROPERTY_VALUES']['ELEMENT_DETAIL_PICTURE_FILE_ALT'] : $arItem["NAME"]));
									$title = ($oneFileValue["DESCRIPTION"] ? $oneFileValue["DESCRIPTION"] : ($arItem['IPROPERTY_VALUES']['ELEMENT_DETAIL_PICTURE_FILE_TITLE'] ? $arItem['IPROPERTY_VALUES']['ELEMENT_DETAIL_PICTURE_FILE_TITLE'] : $arItem["NAME"]));
									if($arItem['ALT_TITLE_GET'] == 'SEO')
									{
										$alt = ($arItem['IPROPERTY_VALUES']['ELEMENT_DETAIL_PICTURE_FILE_ALT'] ? $arItem['IPROPERTY_VALUES']['ELEMENT_DETAIL_PICTURE_FILE_ALT'] : $arItem["NAME"]);
										$title = ($arItem['IPROPERTY_VALUES']['ELEMENT_DETAIL_PICTURE_FILE_TITLE'] ? $arItem['IPROPERTY_VALUES']['ELEMENT_DETAIL_PICTURE_FILE_TITLE'] : $arItem["NAME"]);
									}
									$result[] = array(
										'ID' => (int)$oneFileValue['ID'],
										'SRC' => ($encode ? \CHTTP::urnEncode($oneFileValue['SRC'], 'utf-8') : $oneFileValue['SRC']),
										'WIDTH' => (int)$oneFileValue['WIDTH'],
										'HEIGHT' => (int)$oneFileValue['HEIGHT'],
										'ALT' => $alt,
										'TITLE' => $title
									);
								}
							}
							if (isset($oneValue)) {
								unset($oneValue);
							}
						}
					}
				}
				if ($addDetailToSlider || empty($result)) {

					if (!empty($arItem['DETAIL_PICTURE'])) {
						if (!is_array($arItem['DETAIL_PICTURE'])) {
							$arItem['DETAIL_PICTURE'] = \CFile::GetFileArray($arItem['DETAIL_PICTURE']);
						}

						if (isset($arItem['DETAIL_PICTURE']['ID'])) {
							$alt = ($arItem['DETAIL_PICTURE']['DESCRIPTION'] ? $arItem['DETAIL_PICTURE']['DESCRIPTION'] : ($arItem['DETAIL_PICTURE']['ALT'] ? $arItem['DETAIL_PICTURE']['ALT'] : $arItem['NAME'] ));
							$title = ($arItem['DETAIL_PICTURE']['DESCRIPTION'] ? $arItem['DETAIL_PICTURE']['DESCRIPTION'] : ($arItem['DETAIL_PICTURE']['TITLE'] ? $arItem['DETAIL_PICTURE']['TITLE'] : $arItem['NAME'] ));
							if ($arItem['ALT_TITLE_GET'] == 'SEO') {
								$alt = ($arItem['DETAIL_PICTURE']['ALT'] ? $arItem['DETAIL_PICTURE']['ALT'] : $arItem['NAME'] );
								$title = ($arItem['DETAIL_PICTURE']['TITLE'] ? $arItem['DETAIL_PICTURE']['TITLE'] : $arItem['NAME'] );
							}
							$detailPictIds = array_column($result, 'ID');
							if (!in_array((int)$arItem['DETAIL_PICTURE']['ID'], $detailPictIds)) {
								array_unshift(
									$result,
									array(
										'ID' => (int)$arItem['DETAIL_PICTURE']['ID'],
										'SRC' => ($encode ? \CHTTP::urnEncode($arItem['DETAIL_PICTURE']['SRC'], 'utf-8') : $arItem['DETAIL_PICTURE']['SRC']),
										'WIDTH' => (int)$arItem['DETAIL_PICTURE']['WIDTH'],
										'HEIGHT' => (int)$arItem['DETAIL_PICTURE']['HEIGHT'],
										'ALT' => $alt,
										'TITLE' => $title
									)
								);
							}
						} elseif ($arItem['PICTURE']) {
							array_unshift(
								$result,
								array(
									'SRC' => $arItem['PICTURE'],
									'ALT' => $arItem['NAME'],
									'TITLE' => $arItem['NAME']
								)
							);
						}
					}
				}
			}

			// event for manipulation
			foreach (GetModuleEvents(self::MODULE_ID, 'OnAspro'.ucfirst(__FUNCTION__), true) as $arEvent) {
				ExecuteModuleEventEx($arEvent, array($arConfig, &$result));
			}

			return $result;
		}
		public static function resizeImages(array $arImages = [])
		{
			if (!count($arImages)) return $arImages;
			
			foreach($arImages as $i => $arImage){
				$arImages[$i] = array_merge(
					$arImage, array(
						"BIG" => array('src' => \CFile::GetPath($arImage["ID"]), 'width'=>$arImage['WIDTH'], 'height'=>$arImage['HEIGHT']),
						"SMALL" => \CFile::ResizeImageGet($arImage["ID"], array("width" => 500, "height" => 500), BX_RESIZE_IMAGE_PROPORTIONAL, true, array()),
						"THUMB" => \CFile::ResizeImageGet($arImage["ID"], array("width" => 50, "height" => 50), BX_RESIZE_IMAGE_PROPORTIONAL, true, array()),
					)
				);
			}
			return $arImages;
			
		}

		public static function getCustomFunc($method = '')
		{
			$className = end(explode('\\', __CLASS__));
			$methodCall = $method.$className;
			$classCall = __CLASS__.'Custom';
			$handler = [$classCall, $methodCall];
			if (method_exists($classCall, $methodCall) && is_callable($handler)) {
				return $handler;
			}
			return false;
		}

		public static function showBottomPanel() {
			global $arTheme, $APPLICATION, $arBasketPrices, $compare_items;

			\Bitrix\Main\Page\Frame::getInstance()->startDynamicWithID('bottom-panel-block');

			if ($arTheme['BOTTOM_ICONS_PANEL']['VALUE'] == 'Y') {
				$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH.'/css/bottom-icons-panel.css');

				$iblockID = \CAllcorp3Cache::$arIBlocks[SITE_ID]['aspro_allcorp3_content']['aspro_allcorp3_bottom_icons'][0];

				/*custom functions call*/
				if ($handler = self::getCustomFunc(__FUNCTION__)) {
					$arParams = [
						'IBLOCK_ID' => $iblockID
					];
					call_user_func_array($handler, [$arParams]);
					return;
				}
				/**/

				if ($iblockID) {
					ob_start();
					$arFilter = [
						'IBLOCK_ID' => $iblockID,
						'ACTIVE' => 'Y'
					];
					$arItems = \CAllcorp3Cache::CIBLockElement_GetList(array('SORT' => 'ASC', 'ID' => 'ASC', 'CACHE' => array('TAG' => \CAllcorp3Cache::GetIBlockCacheTag($iblockID))), $arFilter, false, false, array('ID', 'NAME', 'IBLOCK_ID', 'PROPERTY_IMG', 'PROPERTY_TYPE', 'PROPERTY_URL', 'PROPERTY_SHOW_TEXT'));
					?>
					<?if($arItems):?>
						<div class="bottom-icons-panel swipeignore">
							<div class="bottom-icons-panel__content">
								<?foreach($arItems as $arItem):?>
									<?
									$title = $arItem['NAME'];

									$url = trim($arItem['PROPERTY_URL_VALUE']);
									if(strlen($url)){
										if(strpos($url, '#'.'SITE_DIR'.'#') !== false){
											$url = str_replace('#'.'SITE_DIR'.'#', SITE_DIR, $url);
										}

										$url = '/'.ltrim($url, '/');
									}

									$bActive = strlen($url) && ($APPLICATION->GetCurPage() === $url || (\CSite::InDir($url) && $url != SITE_DIR));
									$bShowText = ($arItem['PROPERTY_SHOW_TEXT_VALUE'] == 'Y');
									$bBasket = $bRegion = false;
									$counterClass = "";
									$wrapCounterClass = "";
									$bShowCounter = false;

									if($arItem['PROPERTY_TYPE_VALUE']){
										$arProperty = \CIBlockPropertyEnum::GetByID($arItem['PROPERTY_TYPE_ENUM_ID']);

										if($bBasket = $arProperty['XML_ID'] === 'basket'){
											if($arTheme['ORDER_VIEW']['VALUE'] !== 'Y'){
												continue;
											}

											$arBasketItems = \CAllcorp3::getBasketItems();

											$summ = $count = 0;
											if($arBasketItems){
												foreach($arBasketItems as $arBasketItem){
													if(
														!($arBasketItem['ID']) ||
														!strlen($arBasketItem['NAME'])
													){
														continue;
													}

													++$count;

													if(strlen(trim($arBasketItem['PROPERTY_PRICE_VALUE']))){
														$summ += floatval(str_replace(' ', '', $arBasketItem['PROPERTY_FILTER_PRICE_VALUE'])) * $arBasketItem['QUANTITY'];
													}
												}
											}

											$bEmptyBasket = !$count;
											$title = $bEmptyBasket ? GetMessage('EMPTY_BASKET') : GetMessage('TITLE_BASKET', array('#SUMM#' => \CAllcorp3::FormatSumm($summ, 1)));
										}
										elseif(
											$arProperty['XML_ID'] === 'cabinet'
										){
											if($arTheme['CABINET']['VALUE'] !== 'Y'){
												continue;
											}
										}
										elseif(
											$arProperty['XML_ID'] === 'region'
										){
											if($arTheme['USE_REGIONALITY']['VALUE'] !== 'Y'){
												continue;
											}

											$bRegion = true;
										}
										elseif(
											$arProperty['XML_ID'] === 'tariffs'
										){
											if($arTheme['TARIFFS_USE_DETAIL']['VALUE'] !== 'Y'){
												continue;
											}
										}elseif(
											$arProperty['XML_ID'] === 'compare'
										){
											if($arTheme['CATALOG_COMPARE']['VALUE'] !== 'Y'){
												continue;
											}
											
											$wrapCounterClass = " js-compare-block";
											$counterClass = " icon-count--compare";
											$bShowCounter = true;
											
											$arCompareItems = \CAllcorp3::checkCompareItems();
											$count = count($arCompareItems);

											if($count){
												$wrapCounterClass .= ' icon-block-with-counter--count';
											}
											
										}
									}
									?>
									<?if(strlen($url)):?>
										<a href="<?=htmlspecialcharsbx($url)?>"
									<?else:?>
										<span
									<?endif;?>
										class="bottom-icons-panel__content-link fill-theme-parent<?=($arItem['PROPERTY_TYPE_VALUE'] ? ' bottom-icons-panel__content-link--'.$arProperty['XML_ID'] : '')?><?=($bActive ? ' bottom-icons-panel__content-link--active' : ' dark_link')?><?=($bBasket ? ' basket' : '')?> bottom-icons-panel__content-link--with-counter" 
										title="<?=htmlspecialcharsbx($title)?>"
										<?=($bRegion ? ' data-event="jqm" data-name="city_chooser" data-param-form_id="city_chooser"' : '')?>
									>
										<?if($arItem['PROPERTY_IMG_VALUE']):?>
											<span class="icon-block-with-counter__inner fill-theme-hover fill-theme-target<?=($bShowText ? ' bottom-icons-panel__content-picture-wrapper--mb-3' : '')?><?=($bBasket ? ' js-basket-block'.($bEmptyBasket ? ' header-cart__inner--empty' : '') : '')?>">
												<?
												$arImg = \CFile::ResizeImageGet($arItem['PROPERTY_IMG_VALUE'], array('width' => 60, 'height' => 60), BX_RESIZE_IMAGE_PROPORTIONAL_ALT);?>
												<?if(is_array($arImg)):?>
													<?if(strpos($arImg["src"], ".svg") !== false):?>
														<?=\CAllcorp3::showIconSvg("cat_icons light-ignore", $arImg["src"]);?>
													<?else:?>
														<img class="bottom-icons-panel__content-picture lazyload" src="<?=$arImg["src"]?>" data-src="<?=$arImg["src"]?>" alt="<?=$arItem['NAME']?>" title="<?=$arItem['NAME']?>" />
													<?endif;?>
												<?endif;?>
												<?if($bBasket):?>
													<span class="header-cart__count bg-more-theme count<?=($bEmptyBasket ? ' empted' : '')?>"><?=$count?></span>
												<?elseif($bShowCounter):?>
													<span class="<?= $wrapCounterClass; ?>">
														<span class="icon-count bg-more-theme count<?= $counterClass; ?>"><?=$count?></span>
													</span>
												<?endif;?>
											</span>
										<?endif;?>
										<?if($bShowText):?>
											<span class="bottom-icons-panel__content-text font_10 bottom-icons-panel__content-link--display--block"><?=$arItem['NAME'];?></span>
										<?endif;?>
									<?if(strlen($url)):?>
										</a>
									<?else:?>
										</span>
									<?endif;?>
								<?endforeach;?>
							</div>
						</div>
					<?endif;?>
					<?
					$html = ob_get_contents();
					ob_end_clean();

					foreach(GetModuleEvents(self::MODULE_ID, 'OnAsproShowBottomPanel', true) as $arEvent) // event for manipulation item
						ExecuteModuleEventEx($arEvent, array($iblockID, $arItems, &$html));

					echo $html;
				}
			}

			\Bitrix\Main\Page\Frame::getInstance()->finishDynamicWithID('bottom-panel-block', '');
		}

		public static function getValueWithSection($arOption = [])
		{
			$value = '';
			if ($arOption['CODE']) {
				global $arMergeOptions;

				$value = \CAllcorp3::GetFrontParametrValue($arOption['CODE']);

				if ($arOption['CUSTOM_VALUE']) {
					$value = $arOption['CUSTOM_VALUE'];
				}

				if ($arOption['SECTION_VALUE']) {
					$value = $arOption['SECTION_VALUE'];
					if ($_SESSION['THEME'][SITE_ID][$arOption['CODE']]) {
						$value = $_SESSION['THEME'][SITE_ID][$arOption['CODE']];
					}
				}
				$arMergeOptions[$arOption['CODE']] = $value;
			}
			return $value;
		}

		public static function declOfNum($number, $titles)
		{
			$cases = array (2, 0, 1, 1, 1, 2);
			return $number." ".$titles[ ($number%100>4 && $number%100<20)? 2 : $cases[min($number%10, 5)] ];
		}

		public static function checkActiveFilterPage($sefUrl = '')
		{
			$arParseStr = [];
			if ($sefUrl) {
				global $APPLICATION;
				if (isset($sefUrl) && strripos($sefUrl, "#SMART_FILTER_PATH#")) {

					$isSmartFilter = str_replace("#SMART_FILTER_PATH#", "(.*?)", $sefUrl);
					$isSmartFilter = preg_replace('/^#[a-zA-Z_]+#/i', "", $isSmartFilter);
					$isSmartFilter = str_replace("/", "\/", $isSmartFilter);
					preg_match("/".$isSmartFilter."/i", $APPLICATION->GetCurPage(), $arParseStr);
				}
			}
			return $arParseStr;
		}

		public static function showShareBlock(array $arOptions){
			$class = $arOptions['CLASS'];
			ob_start();
			?>
				<?global $APPLICATION;?>
				<div class="share fill-theme-hover hover-block <?=$class?>">
					<div class="shares-block hover-block__item">
						<?=\CAllcorp3::showIconSvg('down colored_theme_hover_bg-el-svg', SITE_TEMPLATE_PATH.'/images/svg/share.svg', '', '', true, false);?>
						<?$GLOBALS['APPLICATION']->IncludeFile(SITE_DIR.'include/share_buttons.php', Array(), Array("MODE" => "html", "NAME" => GetMessage('CT_BCE_CATALOG_SOC_BUTTON')));?>
					</div>
				</div>
			<?
			$html = ob_get_contents();
			ob_end_clean();

			// event for manipulation
			foreach (GetModuleEvents(self::MODULE_ID, 'OnAspro'.ucfirst(__FUNCTION__), true) as $arEvent) {
				ExecuteModuleEventEx($arEvent, array($arOptions, &$html));
			}

			echo $html;
		}

		public static function showRSSIcon(array $arOptions){
			$url = $arOptions['URL'];
//			$GLOBALS['APPLICATION']->AddHeadString('<link rel="alternate" type="application/rss+xml" title="rss" href="'.$url.'" />');
			ob_start();
			?>
			<div class="rss fill-theme-hover"><a href="<?=$url?>" title="RSS" target="_blank"><?=\CAllcorp3::showIconSvg('print', SITE_TEMPLATE_PATH.'/images/svg/rss.svg', '', 'colored_theme_hover_bg-el-svg')?></a></div>
			<?
			$html = ob_get_contents();
			ob_end_clean();

			// event for manipulation
			foreach (GetModuleEvents(self::MODULE_ID, 'OnAspro'.ucfirst(__FUNCTION__), true) as $arEvent) {
				ExecuteModuleEventEx($arEvent, array($arOptions, &$html));
			}

			echo $html;
		}

		public static function showBackUrl(array $arOptions){
			$url = $arOptions['URL'];
			$text = $arOptions['TEXT'];
			ob_start();
			?>
			<a class="back-url font_14 dark_link stroke-theme-parent-all colored_theme_hover_bg-block animate-arrow-hover color-theme-parent-all" href="<?=$url?>">
				<span class="back-url-icon arrow-all stroke-theme-target">
					<?=\CAllcorp3::showIconSvg(' arrow-all__item-arrow', SITE_TEMPLATE_PATH.'/images/svg/Arrow_map.svg');?>
					<span class="arrow-all__item-line colored_theme_hover_bg-el"></span>
				</span>
				<span class="back-url-text"><?=$text?></span>
			</a>
			<?
			$html = ob_get_contents();
			ob_end_clean();

			// event for manipulation
			foreach (GetModuleEvents(self::MODULE_ID, 'OnAspro'.ucfirst(__FUNCTION__), true) as $arEvent) {
				ExecuteModuleEventEx($arEvent, array($arOptions, &$html));
			}

			echo $html;
		}

		public static function getCustomBlocks($siteID = SITE_ID) {
			$arNewOptions = [];
			$customBlocksIblock = \CAllcorp3Cache::$arIBlocks[$siteID]['aspro_allcorp3_mainblocks']['aspro_allcorp3_mainblocks'][0];

			if(!$customBlocksIblock)
				return;

			$arSectionFilter = array(
				'IBLOCK_ID' => $customBlocksIblock,
				'ACTIVE' => 'Y',
			);
			$arSectionSelect = array(
				'ID',
				'NAME',
				'CODE',
			);
			$arSections = \CAllcorp3Cache::CIblockSection_GetList(
				array(
					'SORT' => 'ASC',
					'ID' => 'ASC',
					"CACHE" => array(
						"TAG" => \CAllcorp3Cache::GetIBlockCacheTag($customBlocksIblock)
					)
				),
				$arSectionFilter,
				false,
				$arSectionSelect
			);
			if ($arSections) {
				$arSectionsID = array();
				foreach ($arSections as $arSection) {
					$arSectionsID[] = $arSection['ID'];
				}

				$arElementFilter = array(
					'IBLOCK_ID' => $customBlocksIblock,
					'ACTIVE' => 'Y',
					'IBLOCK_SECTION_ID' => $arSectionsID,
				);
				$arElementSelect = array(
					'ID',
					'NAME',
					'CODE',
					'PREVIEW_PICTURE',
					'IBLOCK_SECTION_ID',
				);
				$arElements = \CAllcorp3Cache::CIblockElement_GetList(
					array(
						'SORT' => 'ASC',
						'ID' => 'ASC',
						"CACHE" => array(
							"TAG" => \CAllcorp3Cache::GetIBlockCacheTag($customBlocksIblock),
							"MULTI" => "Y",
							'GROUP' => array('IBLOCK_SECTION_ID')
						)
					),
					$arElementFilter,
					false,
					false,
					$arElementSelect
				);

				foreach ($arSections as $arSection) {
					if ($arElements[$arSection['ID']]) {
						$arElementsTemplate = array(
							'TITLE' => Loc::getMessage('CUSTOM_BLOCK_SECTION').$arSection['NAME'],
							'TYPE' => 'selectbox',
							'IS_ROW' => 'Y',
							'LIST' => array(),
							'PREVIEW' => array(
								'SCROLL_BLOCK' => '.'.strtoupper($arSection['CODE']),
								'URL' => '',
							),
						);
						$currentTemplate = '';
						foreach ($arElements[$arSection['ID']] as $arElement) {
							if (!$currentTemplate) {
								$currentTemplate = $arElement['CODE'];
							}
							$src = '';
							if ($arElement['PREVIEW_PICTURE']) {
								$img = \CFile::ResizeImageGet(
									$arElement['PREVIEW_PICTURE'],
									array( "width" => 200, "height" => 200 ),
									BX_RESIZE_IMAGE_PROPORTIONAL,
									true
								);
								$src = $img['src'];
							}
							$arElementsTemplate['LIST'][$arElement['CODE']] = array(
								'TITLE' => $arElement['NAME'],
								'IMG' => $src,
								'ROW_CLASS' => 'col-md-4',
								'POSITION_BLOCK' => 'block',
								'IN_BLOCK' => 'Y',
							);
						}


						$newOptionCode = strtoupper($arSection['CODE']);
						$arNewOptions[$newOptionCode] = array(
							'TITLE' => $arSection['NAME'],
							'THEME' => 'Y',
							'TYPE' => 'checkbox',
							'DEFAULT' => 'Y',
							'ONE_ROW' => 'Y',
							'SMALL_TOGGLE' => 'Y',
							'FON' => 'N',
							'TEMPLATE' => $arElementsTemplate,
							'INDEX_BLOCK_OPTIONS' => array(
								'TOP' => array(
									'DELIMITER' => 'Y',
									'FON' => 'Y',
								),
								'BOTTOM' => array(
									'TOP_OFFSET' => array(
										'TITLE' => GetMessage('TOP_OFFSET'),
										'TYPE' => 'selectbox',
										'LIST' => array(
											'0' => '0',
											'40' => '40',
											'80' => '80',
											'130' => '130',
										),
										'DEFAULT' => '80',
									),
									'BOTTOM_OFFSET' => array(
										'TITLE' => GetMessage('BOTTOM_OFFSET'),
										'TYPE' => 'selectbox',
										'LIST' => array(
											'0' => '0',
											'40' => '40',
											'80' => '80',
											'130' => '130',
										),
										'DEFAULT' => '80',
									),
									/*'SHOW_TITLE' => array(
										'TITLE' => GetMessage('SHOW_TITLE'),
										'TYPE' => 'checkbox',
										'DEFAULT' => 'N',
									),
									'TITLE_POSITION' => array(
										'TITLE' => GetMessage('TITLE_POSITION'),
										'TYPE' => 'selectbox',
										'LIST' => array(
											'NORMAL' => GetMessage('TITLE_POSITION_NORMAL'),
											'CENTERED' => GetMessage('TITLE_POSITION_CENTER'),
											'LEFT' => GetMessage('TITLE_POSITION_LEFT'),
										),
										'DEFAULT' => 'NORMAL',
									),*/
								)
							)
						);
						$arNewOptions[$newOptionCode]['TEMPLATE']['DEFAULT'] = $currentTemplate;
					}
				}
			}
			return $arNewOptions;
		}

		public static function getSolutionOptions($arFrontParametrs = [], $SITE_ID = SITE_ID) {
			$arResult = [];
			foreach (\CAllcorp3::$arParametrsList as $blockCode => $arBlock) {
				foreach ($arBlock['OPTIONS'] as $optionCode => $arOption) {
					$arResult[$optionCode] = $arOption;
					$arResult[$optionCode]['VALUE'] = $arFrontParametrs[$optionCode];
					$arResult[$optionCode]['TYPE_BLOCK'] = $blockCode;

					if ($arResult[$optionCode]['LIST']) {
						foreach ($arResult[$optionCode]['LIST'] as $key => $arListOption) {
							if (isset($arListOption['ADDITIONAL_OPTIONS']) && $arListOption['ADDITIONAL_OPTIONS']) {
								foreach ($arListOption['ADDITIONAL_OPTIONS'] as $key2 => $arListOption2) {
									if ($arListOption2['LIST']) {
										$bMulti = $arListOption2['TYPE'] == 'multiselectbox';
										if ($bMulti) {
											$arFrontParametrs[$key2.'_'.$key] = explode(',', $arFrontParametrs[$key2.'_'.$key]);
										}
										foreach ($arListOption2['LIST'] as $key3 => $arListOption3) {
											if (!is_array($arListOption3)) {
												$arResult[$optionCode]['LIST'][$key]['ADDITIONAL_OPTIONS'][$key2]['LIST'][$key3] = array('TITLE' => $arListOption3);
											}

											if ($bMulti) {
												if ( in_array($key3, $arFrontParametrs[$key2.'_'.$key]) ) {
													$arResult[$optionCode]['LIST'][$key]['ADDITIONAL_OPTIONS'][$key2]['LIST'][$key3]['CURRENT'] = 'Y';
													$arResult[$optionCode]['LIST'][$key]['ADDITIONAL_OPTIONS'][$key2]['VALUE'] = $arFrontParametrs[$key2.'_'.$key];
												}
											} else {
												if ($key3 == $arFrontParametrs[$key2.'_'.$key]) {
													$arResult[$optionCode]['LIST'][$key]['ADDITIONAL_OPTIONS'][$key2]['LIST'][$key3]['CURRENT'] = 'Y';
													$arResult[$optionCode]['LIST'][$key]['ADDITIONAL_OPTIONS'][$key2]['VALUE'] = $arFrontParametrs[$key2.'_'.$key];
												}
											}
										}
										if ($bMulti) {
											$arResult[$optionCode]['LIST'][$key]['ADDITIONAL_OPTIONS'][$key2]['VALUE'] = implode(',', $arResult[$optionCode]['LIST'][$key]['ADDITIONAL_OPTIONS'][$key2]['VALUE']);
										}
									} elseif ($arListOption2['TYPE'] == 'checkbox') {
										$arResult[$optionCode]['LIST'][$key]['ADDITIONAL_OPTIONS'][$key2]['VALUE'] = $arFrontParametrs[$key2.'_'.$key];
									}
								}
							}

							if(isset($arListOption['TOGGLE_OPTIONS']) && $arListOption['TOGGLE_OPTIONS'])
							{
								foreach($arListOption['TOGGLE_OPTIONS']['OPTIONS'] as $key2 => $arListOption2)
								{
									if($arListOption2['LIST'])
									{
										$bMulti = $arListOption2['TYPE'] == 'multiselectbox';
										if($bMulti) {
											$arFrontParametrs[$key2.'_'.$key] = explode(',', $arFrontParametrs[$key2.'_'.$key]);
										}
										foreach($arListOption2['LIST'] as $key3 => $arListOption3)
										{
											if(!is_array($arListOption3))
												$arResult[$optionCode]['LIST'][$key]['TOGGLE_OPTIONS'][$key2]['LIST'][$key3] = array('TITLE' => $arListOption3);

											if($bMulti) {
												if( in_array($key3, $arFrontParametrs[$key2.'_'.$key]) )
												{
													$arResult[$optionCode]['LIST'][$key]['TOGGLE_OPTIONS'][$key2]['LIST'][$key3]['CURRENT'] = 'Y';
													$arResult[$optionCode]['LIST'][$key]['TOGGLE_OPTIONS'][$key2]['VALUE'] = $arFrontParametrs[$key2.'_'.$key];
												}
											} else {
												if($key3 == $arFrontParametrs[$key2.'_'.$key])
												{
													$arResult[$optionCode]['LIST'][$key]['TOGGLE_OPTIONS'][$key2]['LIST'][$key3]['CURRENT'] = 'Y';
													$arResult[$optionCode]['LIST'][$key]['TOGGLE_OPTIONS'][$key2]['VALUE'] = $arFrontParametrs[$key2.'_'.$key];
												}
											}
										}
										if($bMulti) {
											$arResult[$optionCode]['LIST'][$key]['TOGGLE_OPTIONS'][$key2]['VALUE'] = implode(',', $arResult[$optionCode]['LIST'][$key]['TOGGLE_OPTIONS'][$key2]['VALUE']);
										}
									}
									elseif($arListOption2['TYPE'] == 'checkbox')
									{
										$arResult[$optionCode]['LIST'][$key]['TOGGLE_OPTIONS']['OPTIONS'][$key2]['VALUE'] = $arFrontParametrs[$key2.'_'.$key];
									}

									if($arListOption2['ADDITIONAL_OPTIONS'] && is_array($arListOption2['ADDITIONAL_OPTIONS']))
									{
										foreach($arListOption2['ADDITIONAL_OPTIONS'] as $key3 => $arListOption3)
										{
											if($arListOption3['LIST'])
											{
												foreach($arListOption3['LIST'] as $key3 => $arListOption3)
												{
													$arDefaultValues[$key3.'_'.$key] = $arListOption3['DEFAULT'];
													$arValues[$key3.'_'.$key] = Option::get(self::MODULE_ID, $key3.'_'.$key, $arListOption3['DEFAULT'], $SITE_ID);
												}
											}
											elseif($arListOption3['TYPE'] == 'checkbox')
											{
												$arResult[$optionCode]['LIST'][$key]['TOGGLE_OPTIONS']['OPTIONS'][$key2]['ADDITIONAL_OPTIONS'][$key3]['VALUE'] = $arFrontParametrs[$key3.'_'.$key];
											}
										}
									}
								}
							}
						}
					}

					if (
						isset($arResult[$optionCode]['SUB_PARAMS']) && $arResult[$optionCode]['SUB_PARAMS']
					) { //nested params
						if ($arResult[$optionCode]['LIST']) {
							foreach ($arResult[$optionCode]['LIST'] as $key => $arListOption) {
								if ($arResult[$optionCode]['SUB_PARAMS'][$key]) {
									foreach ($arResult[$optionCode]['SUB_PARAMS'][$key] as $key2 => $arSubOptions) {
										//show special options for index components
										if (isset($arSubOptions['INDEX_BLOCK_OPTIONS'])) {
											if (isset($arSubOptions['INDEX_BLOCK_OPTIONS']['TOP']) && $arSubOptions['INDEX_BLOCK_OPTIONS']['TOP']) {
												foreach ($arSubOptions['INDEX_BLOCK_OPTIONS']['TOP'] as $topOptionKey => $topOption) {
													$code_tmp = $topOptionKey.'_'.$key2.'_'.$key;
													$arResult['INDEX_BLOCK_OPTIONS'][$code_tmp] = $arFrontParametrs[$code_tmp];
													$arResult[$optionCode]['SUB_PARAMS'][$key][$key2]['INDEX_BLOCK_OPTIONS']['TOP'][$topOptionKey] = $arFrontParametrs[$code_tmp];
												}
											}
											if (isset($arSubOptions['INDEX_BLOCK_OPTIONS']['BOTTOM']) && $arSubOptions['INDEX_BLOCK_OPTIONS']['BOTTOM']) {
												foreach ($arSubOptions['INDEX_BLOCK_OPTIONS']['BOTTOM'] as $bottomOptionKey => $bottomOption) {
													$code_tmp = $bottomOptionKey.'_'.$key2.'_'.$key;
													$arResult['INDEX_BLOCK_OPTIONS'][$code_tmp] = isset($arFrontParametrs[$code_tmp]) ? $arFrontParametrs[$code_tmp] : false;
													$arResult[$optionCode]['SUB_PARAMS'][$key][$key2]['INDEX_BLOCK_OPTIONS']['BOTTOM'][$bottomOptionKey]['VALUE'] = $arResult['INDEX_BLOCK_OPTIONS'][$code_tmp];

													if (!strlen($arResult['INDEX_BLOCK_OPTIONS'][$code_tmp]) && $bottomOption['DEFAULT']) {
														$arResult['INDEX_BLOCK_OPTIONS'][$code_tmp] = $bottomOption['DEFAULT'];
														$arResult[$optionCode]['SUB_PARAMS'][$key][$key2]['INDEX_BLOCK_OPTIONS']['BOTTOM'][$bottomOptionKey]['VALUE'] = $bottomOption['DEFAULT'];
													}
												}
											}
										}

										//show template index components
										if (isset($arSubOptions['TEMPLATE']) && $arSubOptions['TEMPLATE']) {
											$code_tmp = $key.'_'.$key2.'_TEMPLATE';
											$arResult['TEMPLATE_PARAMS'][$key][$code_tmp] = $arSubOptions['TEMPLATE'];
											$arResult['TEMPLATE_PARAMS'][$key][$code_tmp]['ACTIVE'] = $arFrontParametrs[$key.'_'.$key2];
											foreach ($arResult['TEMPLATE_PARAMS'][$key][$code_tmp]['LIST'] as $keyTemplate => $template) {
												if ($arFrontParametrs[$code_tmp] == $keyTemplate) {
													$arResult['TEMPLATE_PARAMS'][$key][$code_tmp]['LIST'][$keyTemplate]['CURRENT'] = 'Y';
													$arResult['TEMPLATE_PARAMS'][$key][$code_tmp]['VALUE'] = $keyTemplate;
													$arResult[$optionCode]['SUB_PARAMS'][$key][$key2]['TEMPLATE']['VALUE'] = $arFrontParametrs[$code_tmp];
												}

												if ($template['ADDITIONAL_OPTIONS']) {
													foreach ($template['ADDITIONAL_OPTIONS'] as $keyS2 => $arListOption2) {
														$currentVal = $arFrontParametrs[$key.'_'.$key2.'_'.$keyS2.'_'.$keyTemplate];
														$currentVal = $currentVal ? $currentVal : $arResult[$optionCode]['SUB_PARAMS'][$key][$key2]['TEMPLATE']['LIST'][$keyTemplate]['ADDITIONAL_OPTIONS'][$keyS2]['DEFAULT'];

														$arResult[$optionCode]['SUB_PARAMS'][$key][$key2]['TEMPLATE']['LIST'][$keyTemplate]['ADDITIONAL_OPTIONS'][$keyS2]['VALUE'] = $currentVal;
														$arResult['TEMPLATE_PARAMS'][$key][$code_tmp]['LIST'][$keyTemplate]['ADDITIONAL_OPTIONS'][$keyS2]['VALUE'] = $currentVal;

														if ($arListOption2['LIST']) {
															foreach ($arListOption2['LIST'] as $keyS3 => $arListOption3) {
																;
															}
														} elseif($arListOption2['TYPE'] == 'checkbox') {
															$arResult['TEMPLATE_PARAMS'][$key][$code_tmp]['LIST'][$keyTemplate]['ADDITIONAL_OPTIONS'][$keyS2]['VALUE'] = $currentVal;
														}
													}
												}

												if ($template['TOGGLE_OPTIONS']) {
													foreach ($template['TOGGLE_OPTIONS']['OPTIONS'] as $keyS2 => $arListOption2) {
														if ($arListOption2['LIST']) {
															foreach ($arListOption2['LIST'] as $keyS3 => $arListOption3) {
																$arResult[$optionCode]['SUB_PARAMS'][$key][$key2]['TEMPLATE']['LIST'][$keyTemplate]['TOGGLE_OPTIONS'][$keyS2]['LIST'][$keyS3] = $arListOption2['DEFAULT'];
															}
														} elseif($arListOption2['TYPE'] == 'checkbox') {
															$arResult[$optionCode]['SUB_PARAMS'][$key][$key2]['TEMPLATE']['LIST'][$keyTemplate]['TOGGLE_OPTIONS'][$keyS2]['VALUE'] = $arFrontParametrs[$key.'_'.$key2.'_'.$keyS2.'_'.$keyTemplate];
															$arResult['TEMPLATE_PARAMS'][$key][$code_tmp]['LIST'][$keyTemplate]['TOGGLE_OPTIONS'][$keyS2]['VALUE'] = $arFrontParametrs[$key.'_'.$key2.'_'.$keyS2.'_'.$keyTemplate];
														}
													}
												}
											}
										}

										if ($arResult[$optionCode]['SUB_PARAMS'][$key][$key2]['TYPE'] == 'selectbox') {
											foreach ($arResult[$optionCode]['SUB_PARAMS'][$key][$key2]['LIST'] as $key3 => $value) {
												if($arFrontParametrs[$key.'_'.$key2] == $value)
													$arResult[$optionCode]['SUB_PARAMS'][$key][$key2]['LIST'][$key3]['CURRENT'] = 'Y';
											}
										} else {
											$arResult[$optionCode]['SUB_PARAMS'][$key][$key2]['VALUE'] = $arFrontParametrs[$key.'_'.$key2];
										}
									}

									//sort order prop for main page
									$param = 'SORT_ORDER_'.$optionCode.'_'.$key;
									$arResult[$param] = $arFrontParametrs[$param];
								}
							}
						}
					}

					if (isset($arResult[$optionCode]['DEPENDENT_PARAMS']) && $arResult[$optionCode]['DEPENDENT_PARAMS']) { //dependent params
						foreach ($arResult[$optionCode]['DEPENDENT_PARAMS'] as $key => $arListOption) {
							$arResult[$optionCode]['DEPENDENT_PARAMS'][$key]['VALUE'] = $arFrontParametrs[$key];
							if (isset($arListOption['LIST']) && isset($arListOption['LIST'])) {
								foreach ($arListOption['LIST'] as $variantCode => $variant) {
									if (!is_array($variant)) {
										$arResult[$optionCode]['DEPENDENT_PARAMS'][$key]['LIST'][$variantCode] = array('TITLE' => $variant);
									}
									if ($arFrontParametrs[$key] == $variantCode) {
										$arResult[$optionCode]['DEPENDENT_PARAMS'][$key]['LIST'][$variantCode]['CURRENT'] = 'Y';
									}

									if (is_array($variant) && $variant['TOGGLE_OPTIONS']) {
										foreach ($variant['TOGGLE_OPTIONS']['OPTIONS'] as $key2 => $arListOption2) {
											if ($arListOption2['LIST']) {
												$bMulti = $arListOption2['TYPE'] == 'multiselectbox';
												if ($bMulti) {
													$arFrontParametrs[$key2.'_'.$key] = explode(',', $arFrontParametrs[$key2.'_'.$key]);
												}
												foreach ($arListOption2['LIST'] as $key3 => $arListOption3) {
													if (!is_array($arListOption3)) {
														$arResult[$optionCode]['DEPENDENT_PARAMS'][$key]['LIST'][$variantCode]['TOGGLE_OPTIONS'][$key2]['LIST'][$key3] = array('TITLE' => $arListOption3);
													}

													if ($bMulti) {
														if (in_array($key3, $arFrontParametrs[$key2.'_'.$key])) {
															$arResult[$optionCode]['DEPENDENT_PARAMS'][$key]['LIST'][$variantCode]['TOGGLE_OPTIONS'][$key2]['LIST'][$key3]['CURRENT'] = 'Y';
															$arResult[$optionCode]['DEPENDENT_PARAMS'][$key]['LIST'][$variantCode]['TOGGLE_OPTIONS'][$key2]['VALUE'] = $arFrontParametrs[$key2.'_'.$variantCode];
														}
													} else {
														if ($key3 == $arFrontParametrs[$key2.'_'.$key]) {
															$arResult[$optionCode]['DEPENDENT_PARAMS'][$key]['LIST'][$variantCode]['TOGGLE_OPTIONS'][$key2]['LIST'][$key3]['CURRENT'] = 'Y';
															$arResult[$optionCode]['DEPENDENT_PARAMS'][$key]['LIST'][$variantCode]['TOGGLE_OPTIONS'][$key2]['VALUE'] = $arFrontParametrs[$key2.'_'.$variantCode];
														}
													}
												}
												if ($bMulti) {
													$arResult[$optionCode]['DEPENDENT_PARAMS'][$key]['LIST'][$variantCode]['TOGGLE_OPTIONS'][$key2]['VALUE'] = implode(',', $arResult[$optionCode]['DEPENDENT_PARAMS'][$key]['LIST'][$variantCode]['TOGGLE_OPTIONS'][$key2]['VALUE']);
												}
											} elseif($arListOption2['TYPE'] == 'checkbox') {
												$arResult[$optionCode]['DEPENDENT_PARAMS'][$key]['LIST'][$variantCode]['TOGGLE_OPTIONS']['OPTIONS'][$key2]['VALUE'] = $arFrontParametrs[$key2.'_'.$variantCode];
											}

											if ($arListOption2['ADDITIONAL_OPTIONS'] && is_array($arListOption2['ADDITIONAL_OPTIONS'])) {
												foreach ($arListOption2['ADDITIONAL_OPTIONS'] as $key3 => $arListOption3) {
													if ($arListOption3['LIST']) {
														foreach($arListOption3['LIST'] as $key3 => $arListOption3)
														{
															$arDefaultValues[$key3.'_'.$variantCode] = $arListOption3['DEFAULT'];
															$arValues[$key3.'_'.$variantCode] = Option::get(self::MODULE_ID, $key3.'_'.$variantCode, $arListOption3['DEFAULT'], $SITE_ID);
														}
													} elseif ($arListOption3['TYPE'] == 'checkbox') {
														$arResult[$optionCode]['DEPENDENT_PARAMS'][$key]['LIST'][$variantCode]['TOGGLE_OPTIONS']['OPTIONS'][$key2]['ADDITIONAL_OPTIONS'][$key3]['VALUE'] = $arFrontParametrs[$key3.'_'.$variantCode];
													}
												}
											}
										}
									}
								}
							}
						}
					}

					// CURRENT for compatibility with old versions
					if ($arResult[$optionCode]['LIST']) {
						$bMulti = $arResult[$optionCode]['TYPE'] == 'multiselectbox';
						if ($bMulti) {
							$arValue = explode(',', $arResult[$optionCode]['VALUE']);
						}
						foreach ($arResult[$optionCode]['LIST'] as $variantCode => $variantTitle) {
							if (!is_array($variantTitle)) {
								$arResult[$optionCode]['LIST'][$variantCode] = array('TITLE' => $variantTitle);
							}

							if ($bMulti) {
								if (in_array($variantCode, $arValue)) {
									$arResult[$optionCode]['LIST'][$variantCode]['CURRENT'] = 'Y';
								}
							} else {
								if ($arResult[$optionCode]['VALUE'] == $variantCode) {
									$arResult[$optionCode]['LIST'][$variantCode]['CURRENT'] = 'Y';
								}
							}
						}
					}
				}
			}

			if ($arResult) {
				$arGroups = $arGroups2 = array();
				foreach ($arResult as $optionCode => $arOption) {
					if ((isset($arOption['GROUP']) && $arOption['GROUP'])) { //set groups option
						$arGroups[$arOption['GROUP']]['TITLE'] = GetMessage($arOption['GROUP']);
						$arGroups[$arOption['GROUP']]['THEME'] = $arOption['THEME'];
						$arGroups[$arOption['GROUP']]['GROUPS_EXT'] = 'Y';
						$arGroups[$arOption['GROUP']]['TYPE_BLOCK'] = $arOption['TYPE_BLOCK'];
						$arGroups[$arOption['GROUP']]['OPTIONS'][$optionCode] = $arOption;
						unset($arResult[$optionCode]);

						if(isset($arOption['GROUP_HINT']) && $arOption['GROUP_HINT']) //set group hint
							$arGroups[$arOption['GROUP']]['HINT'] = $arOption['GROUP_HINT'];
					} elseif((isset($arOption['TAB_GROUP_BLOCK']) && $arOption['TAB_GROUP_BLOCK'])) {
						$arGroups2['TABS'][$arOption['TYPE_BLOCK']][$arOption['TAB_GROUP_BLOCK']]['OPTIONS'][$optionCode] = $arOption;
					}
				}

				if ($arGroups) {
					$arResult = array_merge($arResult, $arGroups);
				}
				if ($arGroups2) {
					$arResult = array_merge($arResult, $arGroups2);
				}
			}

			return $arResult;
		}

		public static function getCountSites($bFromStatic = true){
			static $cacheCount;

			if(!isset($cacheCount)){
				$cacheCount = 0;
			}

			$countSites =& $cacheCount;

			if(!$bFromStatic){
				$countSites = array();
			}

			if(!$countSites){
				$strSites = \CAllcorp3::GetFrontParametrValue('SITES_SHOW_IN_SELECTOR');
				$arSites = explode(',', $strSites);
				$countSites = count($arSites);
			}

			return $countSites;
		}

		public static function getShowSites($bFromStatic = true){
			static $cacheSites;

			if(!isset($cacheSites)){
				$cacheSites = array();
			}

			$arSites =& $cacheSites;

			if(!$bFromStatic){
				$arSites = array();
			}

			if(!$arSites){
				$strSites = \CAllcorp3::GetFrontParametrValue('SITES_SHOW_IN_SELECTOR');
				$arSites = explode(',', $strSites);
				$arSites = array_diff($arSites, array(''));
			}

			return $arSites;
		}

		public static function afterHeaderAction(){
			global $arMergeOptions, $sideMenuHeader;
			?>
			<script data-skip-moving="true">if(typeof topMenuAction !== 'undefined') topMenuAction()</script>
			<?if( !$sideMenuHeader && !isset($_COOKIE['side_menu']) ){
				$arAdminOptions = \CAllcorp3::GetBackParametrsValues(SITE_ID);
				if($arAdminOptions['THEME_SWITCHER'] === 'Y'){
					$arMergeOptions['SIDE_MENU'] = $arAdminOptions['SIDE_MENU'];
				}
			}
		}

		public static function getCrossLinkedItems(array $arItem, array $arLinkProperties, array $arCrossLinkProperties = array()){
			static $arDetailPageShowProps;

			$arLinkedIDs = array();

			if($arLinkProperties){
				$linkProperty = isset($arLinkProperties[0]) ? $arLinkProperties[0] : '';
				$filterLinkProperty = isset($arLinkProperties[1]) ? $arLinkProperties[1] : '';
			}

			if($arCrossLinkProperties){
				$crossLinkProperty = isset($arCrossLinkProperties[0]) ? $arCrossLinkProperties[0] : '';
				$crossFilterLinkProperty = isset($arCrossLinkProperties[1]) ? $arCrossLinkProperties[1] : '';
			}

			if(
				is_array($arItem) &&
				array_key_exists('DISPLAY_PROPERTIES', $arItem)
			){
				$id = $arItem['ID'];
				$iblockId = $arItem['IBLOCK_ID'];

				// get display properties
				if(!isset($arDetailPageShowProps)){
					$arDetailPageShowProps = \Bitrix\Iblock\Model\PropertyFeature::getDetailPageShowProperties(
						$arParams['IBLOCK_ID'],
						array('CODE' => 'Y')
					);
					if($arDetailPageShowProps === null){
						$arDetailPageShowProps = array();
					}
				}

				// LINK_#PROPERTY_CODE#
				if(
					strlen($linkProperty) &&
					array_key_exists($linkProperty, $arItem['PROPERTIES']) &&
					is_array($arItem['PROPERTIES'][$linkProperty])
				){
					$linkIblockId = $arItem['PROPERTIES'][$linkProperty]['LINK_IBLOCK_ID'];
					if($arItem['DISPLAY_PROPERTIES'][$linkProperty]['VALUE']){
						$arLinkedIDs = $arItem['DISPLAY_PROPERTIES'][$linkProperty]['VALUE'];
					}
				}

				// LINK_#PROPERTY_CODE#_FILTER
				if(
					strlen($filterLinkProperty) &&
					array_key_exists($filterLinkProperty, $arItem['PROPERTIES']) &&
					is_array($arItem['PROPERTIES'][$filterLinkProperty])
				){
					$filterLinkIblockId = $arItem['PROPERTIES'][$filterLinkProperty]['USER_TYPE_SETTINGS']['IBLOCK_ID'];

					if($filterLinkIblockId){
						try{
							$arTmpFilter = Json::decode($arItem['DISPLAY_PROPERTIES'][$filterLinkProperty]['~VALUE']);
						}
						catch(\Exception $e){
							$arTmpFilter = array();
						}

						if(
							array_key_exists('CHILDREN', $arTmpFilter) &&
							$arTmpFilter['CHILDREN']
						){
							// unset result
							$arLinkedIDs = array();

							$cond = new \CAllcorp3Condition();
							try{
								$arFilter = $cond->parseCondition($arTmpFilter, array());
							}
							catch(\Exception $e){
								$arFilter = array();
							}

							if(
								$arFilter &&
								$filterLinkIblockId
							){
								$arFilter = array(
									'LOGIC' => 'AND',
									array(
										'IBLOCK_ID' => $filterLinkIblockId['IBLOCK_ID'],
									),
									array(
										$arFilter
									),
								);

								$arLinkedIDs = \CAllCorp3Cache::CIBLockElement_GetList(
									array(
										'CACHE' => array(
											'TAG' => \CAllCorp3Cache::GetIBlockCacheTag($filterLinkIblockId),
											'RESULT' => array('ID'),
											'MULTI' => 'Y',
										)
									),
									$arFilter,
									false,
									false,
									array('ID')
								);
							}
						}

						if(
							!$linkIblockId &&
							$filterLinkIblockId
						){
							$linkIblockId = $filterLinkIblockId;
						}
					}
				}

				if(
					$id &&
					$iblockId &&
					$linkIblockId
				){
					if(
						array_key_exists($linkProperty, $arItem['DISPLAY_PROPERTIES']) ||
						array_key_exists($filterLinkProperty, $arItem['DISPLAY_PROPERTIES']) ||
						in_array($linkProperty, $arDetailPageShowProps) ||
						in_array($filterLinkProperty, $arDetailPageShowProps)
					){
						$arCrossLinkedItemsIDs = array();

						// CROSS LINK_#PROPERTY_CODE#
						if(strlen($crossLinkProperty)){
							$arCrossLinkedItemsIDs = \CAllCorp3Cache::CIBLockElement_GetList(
								array(
									'CACHE' => array(
										'TAG' => \CAllCorp3Cache::GetIBlockCacheTag($linkIblockId),
										'RESULT' => array('ID'),
										'MULTI' => 'Y',
									)
								),
								array(
									'IBLOCK_ID' => $linkIblockId,
									'PROPERTY_'.$crossLinkProperty => $id,
								),
								false,
								false,
								array('ID')
							);
						}

						// CROSS LINK_#PROPERTY_CODE#_FILTER
						if(strlen($crossFilterLinkProperty)){
							$arCrossFilterLinkedItems = \CAllCorp3Cache::CIBLockElement_GetList(
								array(
									'CACHE' => array(
										'TAG' => \CAllCorp3Cache::GetIBlockCacheTag($linkIblockId),
										'MULTI' => 'Y',
									)
								),
								array(
									'IBLOCK_ID' => $linkIblockId,
									array(
										'LOGIC' => 'AND',
										array(
											'!PROPERTY_'.$crossFilterLinkProperty => false,
										),
										array(
											'!PROPERTY_'.$crossFilterLinkProperty => '[]',
										),
									)
								),
								false,
								false,
								array('ID', 'IBLOCK_ID', 'PROPERTY_'.$crossFilterLinkProperty)
							);

							if($arCrossFilterLinkedItems){
								foreach($arCrossFilterLinkedItems as $arCrossLinkedItem){
									try{
										$arTmpFilter = Json::decode($arCrossLinkedItem['PROPERTY_'.$crossFilterLinkProperty.'_VALUE']);
									}
									catch(\Exception $e){
										$arTmpFilter = array();
									}

									if(
										array_key_exists('CHILDREN', $arTmpFilter) &&
										$arTmpFilter['CHILDREN']
									){
										$p = array_search($arCrossLinkedItem['ID'], $arCrossLinkedItemsIDs);
										if($p !== false){
											// unset from result
											unset($arCrossLinkedItemsIDs[$p]);
										}
									}

									$cond = new \CAllcorp3Condition();
									try{
										$arFilter = $cond->parseCondition($arTmpFilter, array());
									}
									catch(\Exception $e){
										$arFilter = array();
									}

									if($arFilter){
										$arFilter = array(
											'LOGIC' => 'AND',
											array(
												'IBLOCK_ID' => $iblockId,
												'ID' => $id,
											),
											$arFilter
										);

										if(\CAllCorp3Cache::CIBLockElement_GetList(
											array(
												'CACHE' => array(
													'TAG' => \CAllCorp3Cache::GetIBlockCacheTag($iblockId),
													'GROUP' => array('ID'),
												)
											),
											$arFilter,
											array()
										)){
											$arCrossLinkedItemsIDs[] = $arCrossLinkedItem['ID'];
										}
									}
								}
							}
						}

						if($arCrossLinkedItemsIDs){
							$arLinkedIDs = array_merge($arLinkedIDs, $arCrossLinkedItemsIDs);
						}
					}
				}
			}

			return array(
				'IBLOCK_ID' => $linkIblockId,
				'VALUE' => array_unique($arLinkedIDs),
			);
		}

		public static function checkProperty($nameDownloadFile, $urlDownloadFile){
			if($urlDownloadFile){
				
				if(!$nameDownloadFile){
					$nameDownloadFile = GetMessage('NAME_DOWNLOAD_FILE');
				}
				ob_start();
				?>
				<div class="download fill-theme-hover"><a download href="<?=$urlDownloadFile?>" title="<?=$nameDownloadFile?>"><?=\CAllcorp3::showIconSvg('download', SITE_TEMPLATE_PATH.'/images/svg/file_download.svg', '', 'colored_theme_hover_bg-el-svg')?><span class="font_13 title"><?=$nameDownloadFile;?></span></a></div>
				<?
				$html = ob_get_contents();
				ob_end_clean();
				return $html;
			}
		}
		public static function getGridClassByCount($arBreakPoints = [], $elementInRow) {

			$strClassGrid = "";
			if($elementInRow > 2) {

				natsort($arBreakPoints);
				$arBreakPoints = array_values($arBreakPoints);
			
				$arClass = [];
				for($i = count($arBreakPoints)-1; $i >= 0; $i--){
					$arClass[] = ' grid-list--items-'. ($elementInRow--) .'-'.$arBreakPoints[$i];
				}
				
				$strClassGrid = implode(' ', $arClass); 
			}

			$strClassGrid .= ' grid-list--items-2-601';

			return $strClassGrid;
		}

		/**
		 * Replace catalog and sku props 'LIST_PROPERTY_CODE', 'SKU_IBLOCK_ID', 'SKU_PROPERTY_CODE', 'SKU_TREE_PROPS'
		 * @param $arParams array
		 * @return $arParams array
		 */
		public static function replacePropsParams(&$arParams, $arReplaceCode = ['PROPERTY_CODE' => 'LIST_PROPERTY_CODE'])
		{
			// /* COMMON */ TODO: implement into params for list and detail
			// $arParams["SHOW_HINTS"] = Solution::GetFrontParametrValue('SHOW_HINTS');

			/* CATALOG_PROPS */
			$arParams[$arReplaceCode["PROPERTY_CODE"]] = explode(',', Solution::GetFrontParametrValue('CATALOG_PROPERTY_CODE'));

			/* SKU */
			$arParams["SKU_IBLOCK_ID"] = Solution::GetFrontParametrValue('CATALOG_SKU_IBLOCK_ID');
			$arParams["SKU_PROPERTY_CODE"] = explode(',', Solution::GetFrontParametrValue('CATALOG_SKU_PROPERTY_CODE'));
			$arParams["SKU_TREE_PROPS"] = explode(',', Solution::GetFrontParametrValue('CATALOG_SKU_TREE_PROPERTY_CODE'));
		}

		/**
		 * cleaning text from the symbol. 
		 * 
		 */
		public static function replaceString($text):string {
			$regexp = "/[\W_]/s";
			return preg_replace($regexp, "", $text);
		}
		public static function getFiles($path) {
			$arFiles = [];
			$dir_iterator = new \RecursiveDirectoryIterator($path);
			$iterator = new \RecursiveIteratorIterator($dir_iterator, \RecursiveIteratorIterator::CHILD_FIRST);

			foreach ($iterator as $file) {
				if ($file->isFile()) {
				$arFiles[] = $file;
				}
			}
			return $arFiles;
		}

		/**
		 * Get path from directory by element_code|element_id and section path from detail_page_url.
		 * Comparing the cleaned path with the files in the folder along the path SEF_FOLDER.DIR.
		 * The priority is set as follows: 1 element_code, 2 full path. 3 section_code
		 * @param array $arOptions 
		 * * RESULT => array,
		 * * DIRECTORY => [
		 * * * SEF_FOLDER => string,
		 * * * DIR => string
		 * * ]
		 */
		public static function getPathFromDirectory(array $arOptions):string {

			$arResult= $arOptions['RESULT'];
			$sefFolder = $arOptions['DIRECTORY']['SEF_FOLDER'];
			$dir = $arOptions['DIRECTORY']['DIR'];
			$fullPath = $_SERVER['DOCUMENT_ROOT'].$sefFolder.$dir;
			if (!file_exists($fullPath)) return false;
			$url = str_replace([$sefFolder, $arResult['CODE']], '', $arResult['DETAIL_PAGE_URL']);
			$sectionPath = self::replaceString($url);
			$arSectionsCode = array_map('self::replaceString', array_filter(explode("/", $url)));
			$arFiles = self::getFiles($fullPath) ;
			$dopsFile = '';
			$arMatchFiles = [];
			$elementCode = self::replaceString($arResult['CODE']);
			$elementId = $arResult['ID'];

			foreach ($arFiles as $key => $file) {
				$file = self::replaceString($file->getBaseName($file->getExtension()));

				if ($elementCode === $file || $elementId === $file ) {
					$arMatchFiles[0] = $arFiles[$key];
				} elseif ($sectionPath.$elementCode === $file || $sectionPath.$elementId === $file) {
					$arMatchFiles[1] = $arFiles[$key];
				} elseif ($sectionPath === $file) {
					$arMatchFiles[2] = $arFiles[$key];
				} else {
					if (in_array($file, $arSectionsCode)) {
						$arMatchFiles[3] = $arFiles[$key];
					}
				}
			}
			if ($arMatchFiles) {
				ksort($arMatchFiles);
				$dopsFile = str_replace($_SERVER['DOCUMENT_ROOT'], '', reset($arMatchFiles));
			}
			return $dopsFile;
		}
	}
}?>