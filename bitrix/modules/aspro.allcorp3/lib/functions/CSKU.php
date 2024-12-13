<?
namespace Aspro\Allcorp3\Functions;

use Bitrix\Main\Application,
 Bitrix\Main\Localization\Loc,
 Bitrix\Main\IO\File,
 Bitrix\Main\IO\Directory,
 Bitrix\Main\Type\Collection,
 Bitrix\Main\Config\Option,
 Bitrix\Main\Loader,
 Bitrix\Highloadblock\HighloadBlockTable,
 Bitrix\Main,
 Bitrix\Iblock;

Loc::loadMessages(__FILE__);

if(!class_exists('CSKU'))
{
	/**
	 * Class for manipulate sku items
	 */
	class CSKU{
		/**
		 * @var string MODULE_ID solution CODE
		 * @var string linkCodeProp Code linked property
		 * @var number selectedItem ID selected item after sort
		 * @var array linkedProp All info linked property
		 * @var array treeProp Tree props by item
		 * @var array props All tree props form params
		 * @var array items All items
		 * @var array currentItem Selected item
		 * @var array config Params
		 */
		const MODULE_ID = \CAllcorp3::moduleID;

		public $props = [];
		public $items = [];
		public $linkedProp = [];
		public $treeProp = [];
		public $currentItem = [];
		public $config = [];
		public $linkCodeProp = '';
		private $selectedItem = 0;

		protected static $highLoadInclude = null;

		public function __construct(array $arOptions = [])
		{
			$arDefaultOptions = [
				'ORDER_VIEW' => false,
				'SHOW_ONE_CLICK_BUY' => 'N',
				'DISPLAY_COMPARE' => false,
				'USE_FAST_VIEW_PAGE_DETAIL' => 'NO',
				'LINK_SKU_PROP_CODE' => 'LINK_SKU',
				'SKU_SORT_FIELD' => 'sort',
				'SKU_SORT_ORDER' => 'asc',
				'SKU_SORT_FIELD2' => 'name',
				'SKU_SORT_ORDER2' => 'asc',
				'SKU_PROPERTY_CODE' => ['FILTER_PRICE', 'FORM_ORDER', 'PRICE_CURRENCY'],
				'SKU_TREE_PROPS' => [],
				'SHOW_SKU_DESCRIPTION' => 'N',
			];
			if ($arOptions['SKU_PROPERTY_CODE']) {
				$arOptions['SKU_PROPERTY_CODE'] = array_merge(
					$arDefaultOptions['SKU_PROPERTY_CODE'], 
					$arOptions['SKU_PROPERTY_CODE']
				);
			}
			$arConfig = array_merge($arDefaultOptions, $arOptions);
			
			$this->linkCodeProp = $arConfig['LINK_SKU_PROP_CODE'];
			$this->config = \array_intersect_key($arConfig, $arDefaultOptions);
		}

		/**
		 * Check visible and value in LINK_SKU property
		 * @param array $arProperties DISPLAY_PROPERTIES
		 * @return boolean
		 */
		private function checkItemLinkProp(array $arProperties = [])
		{
			$bCheck = false;
			if (
				$arProperties && 
				(
					isset($arProperties[$this->linkCodeProp]) && 
					\is_array($arProperties[$this->linkCodeProp]) && 
					$arProperties[$this->linkCodeProp]['VALUE']
				)
			) {
				$bCheck = true;
			}
			return $bCheck;
		}
		
		/**
		 * Set $this->linkedProp from LINK_SKU property
		 * @param array $arProperties DISPLAY_PROPERTIES
		 */
		public function setLinkedPropFromDisplayProps(array $arProperties = [])
		{
			if ($this->checkItemLinkProp($arProperties)) {
				$this->linkedProp = $arProperties[$this->linkCodeProp];
				unset($this->linkedProp['LINK_ELEMENT_VALUE']);
			} else {
				$this->linkedProp = [];
			}
		}
		
		/**
		 * Set $this->linkedProp from custom array
		 * @param array $arFields 
		 */
		public function setLinkedPropFromArray(array $arFields = [])
		{
			$this->linkedProp = $arFields;
		}

		/**
		 * Reset items variables 
		 * 
		 */
		private function resetItems()
		{
			$this->items = [];
			$this->currentItem = [];
			$this->treeProps = [];
			$this->matrix = [];
		}

		/**
		 * Get all items from sku iblock by property LINK_SKU
		 * @param array $arProperties DISPLAY_PROPERTIES
		 * @return array [['SECTION_ID' => ['ITEMS' => []]] | []
		 */
		public function getItemsByProperty()
		{
			$this->resetItems();
			if ($this->linkedProp && $this->linkedProp['VALUE']) {
				$arFilter = [
					'ID' => $this->linkedProp['VALUE'],
					'ACTIVE' => 'Y',
					'IBLOCK_ID' => $this->linkedProp['LINK_IBLOCK_ID']
				];
				$this->getItemsByFilter($arFilter);
				$this->getItemsProps();
				$this->getItemsIProps();
				$this->getMatrix();
			}
		}

		/**
		 * Get all items by filter
		 * @param array $arFilter
		 * @return array 
		 */
		public function getItemsByFilter(array $arFilter = [], array $arSelect = [])
		{
			if (!$arFilter['IBLOCK_ID']) {
				return [];
			}
			$arDefaultSelect = ['ID', 'IBLOCK_ID', 'NAME', 'SORT', 'PREVIEW_TEXT', 'DETAIL_TEXT', 'PREVIEW_PICTURE', 'DETAIL_PICTURE'];
			$iblockID = $arFilter['IBLOCK_ID'];
			$arSelect = array_merge($arDefaultSelect, $arSelect);
			$this->items = \CAllcorp3Cache::CIBlockElement_GetList(
				[
					$this->config['SKU_SORT_FIELD'] => $this->config['SKU_SORT_ORDER'],
					$this->config['SKU_SORT_FIELD2'] => $this->config['SKU_SORT_ORDER2'],
					"CACHE" => [
						"TAG" => \CAllcorp3Cache::GetIBlockCacheTag($iblockID),
						"GROUP" => 'ID',
						"MULTI" => 'N'
					]
				],
				$arFilter,
				false,
				false,
				$arSelect
			);
		}
		
		/**
		 * Get properties by loop items
		 * @return array 
		 */
		public function getItemsProps()
		{
			if ($this->items && $this->config['SKU_PROPERTY_CODE']) {
				$arProps = [];
				\CIBlockElement::GetPropertyValuesArray(
					$arProps,
					$this->linkedProp['LINK_IBLOCK_ID'],
					[
						'ID' => \array_keys($this->items)
					],
					[
						'CODE' => $this->config['SKU_PROPERTY_CODE']
					]
				);
				if ($arProps) {
					foreach ($arProps as $key => $arProp) {
						if ($this->items[$key]) {
							$this->items[$key]['DISPLAY_PROPERTIES'] = $arProp;
						}
					}
				}
			}
		}

		/**
		 * Get inherited properties
		 * @return array 
		 */
		public function getItemsIProps()
		{
			if ($this->items) {
				foreach ($this->items as $key => $arItem) {
					$ipropValues = new \Bitrix\Iblock\InheritedProperty\ElementValues($arItem['IBLOCK_ID'], $arItem['ID']);
					$this->items[$key]['IPROPERTY_VALUES'] = $ipropValues->getValues();
				}
			}
		}

		/**
		 * Get tree props value in item
		 * @return array 
		 */
		private function getMatrix()
		{
			if ($this->items) {
				
				$arMatrix = $arRow = [];
				$arPropsCode = array_keys($this->props);

				$arMatrixFields = array_fill_keys($arPropsCode, false);

				foreach ($this->items as $key => $arItem) {
					foreach ($arPropsCode as $codeProp) {
						$arCell = [
							'VALUE' => 0,
							'SORT' => PHP_INT_MAX,
							'NA' => true
						];
						if (isset($arItem['DISPLAY_PROPERTIES'][$codeProp])) {
							$arMatrixFields[$codeProp] = true;
							$arCell['NA'] = false;

							if ('directory' == $this->props[$codeProp]['USER_TYPE'])
							{
								$intValue = $this->props[$codeProp]['XML_MAP'][$arItem['DISPLAY_PROPERTIES'][$codeProp]['VALUE']];
								$arCell['VALUE'] = $intValue;
							}
							elseif ('L' == $this->props[$codeProp]['PROPERTY_TYPE'])
							{
								$arCell['VALUE'] = intval($arItem['DISPLAY_PROPERTIES'][$codeProp]['VALUE_ENUM_ID']);
							}
							elseif ('E' == $this->props[$codeProp]['PROPERTY_TYPE'])
							{
								$arCell['VALUE'] = intval($arItem['DISPLAY_PROPERTIES'][$codeProp]['VALUE']);
							}
							$arCell['SORT'] = $this->props[$codeProp]['VALUES'][$arCell['VALUE']]['SORT'];

						}
						$arRow[$codeProp] = $arCell;
					}
					$arMatrix[$key] = $arRow;

					\CIBlockPriceTools::clearProperties($this->items[$key]['DISPLAY_PROPERTIES'], \array_keys($this->props));
				}

				$this->matrix = [
					'ITEMS' => $arMatrix,
					'FIELDS' => $arMatrixFields
				];

				$this->setFormattedTreeProps();
			}
		}

		/**
		 * Set tree props with value by matrix
		 * @return array 
		 */
		private function setFormattedTreeProps()
		{
			$arPropSKU = $arItem['OFFERS_PROPS_JS'] = $arSortFields = [];
			
			foreach (array_keys($this->props) as $codeProp) {
				$boolExist = $this->matrix['FIELDS'][$codeProp];

				foreach ($this->matrix['ITEMS'] as $keyOffer => $arRow) {
					if ($boolExist) {
						if (!isset($this->items[$keyOffer]['TREE'])) {
							$this->items[$keyOffer]['TREE'] = [];
						}
						$this->items[$keyOffer]['TREE']['PROP_'.$this->props[$codeProp]['ID']] = $this->matrix['ITEMS'][$keyOffer][$codeProp]['VALUE'];
						$this->items[$keyOffer]['SKU_SORT_'.$codeProp] = $this->matrix['ITEMS'][$keyOffer][$codeProp]['SORT'];

						$arSortFields['SKU_SORT_'.$codeProp] = SORT_NUMERIC;

						$arPropSKU[$codeProp][$this->matrix['ITEMS'][$keyOffer][$codeProp]["VALUE"]] = $this->props[$codeProp]["VALUES"][$this->matrix['ITEMS'][$keyOffer][$codeProp]["VALUE"]];
						
					} else {
						unset($this->matrix['ITEMS'][$keyOffer][$codeProp]);
					}
				}
				if ($arPropSKU[$codeProp] && array_column($arPropSKU[$codeProp], 'ID')) {
					Collection::sortByColumn($arPropSKU[$codeProp], array("SORT" => array(SORT_NUMERIC, SORT_ASC), "NAME" => array(SORT_NUMERIC, SORT_ASC))); // sort sku prop values

					$this->treeProps[$codeProp] = array(
						"ID" => $this->props[$codeProp]["ID"],
						"CODE" => $this->props[$codeProp]["CODE"],
						"NAME" => $this->props[$codeProp]["NAME"],
						"SORT" => $this->props[$codeProp]["SORT"],
						"PROPERTY_TYPE" => $this->props[$codeProp]["PROPERTY_TYPE"],
						"USER_TYPE" => $this->props[$codeProp]["USER_TYPE"],
						"LINK_IBLOCK_ID" => $this->props[$codeProp]["LINK_IBLOCK_ID"],
						"SHOW_MODE" => $this->props[$codeProp]["SHOW_MODE"],
						"VALUES" => $arPropSKU[$codeProp]
					);
				}
			}

			$this->setActivePropsValue();
		}

		/**
		 * Set selected item ID
		 * @param number $number ID item 
		 */
		public function setSelectedItem($number)
		{
			if ((int)$number) {
				$this->selectedItem = $number;
			}
		}

		/**
		 * Set active class in props value by selected item
		 * @return array 
		 */
		private function setActivePropsValue()
		{
			$arFilter = [];
			$arCurrentOffer = current($this->items);
			if ($this->selectedItem && $this->items[$this->selectedItem]) {
				$arCurrentOffer = $this->items[$this->selectedItem];
			}
			$this->currentItem = $arCurrentOffer;
			
			foreach ($this->treeProps as $key => $arProp){
				$strName = 'PROP_'.$arProp['ID'];
				$arShowValues = $this->GetRowValues($arFilter, $strName);

				if (in_array($arCurrentOffer['TREE'][$strName], $arShowValues)) {
					$arFilter[$strName] = $arCurrentOffer['TREE'][$strName];
				} else {
					$arFilter[$strName] = $arShowValues[0];
				}

				$this->UpdateRow($arFilter[$strName], $arShowValues, $this->treeProps[$key]);

			}
			/*echo "<pre>";
			print_r($this->treeProps);
			echo "</pre>";*/
		}

		/**
		 * Get visible props value
		 * @param array $arFilter
		 * @param string $index Property code
		 */
		public static function GetCanBuy($arFilter, $arItem)
		{
			$boolSearch = false;
			$boolOneSearch = true;

			foreach ($arItem['OFFERS'] as $arOffer) {
				$boolOneSearch = true;
				foreach ($arFilter as $propName => $filter) {
					if ($filter !== $arOffer['TREE'][$propName]) {
						$boolOneSearch = false;
						break;
					}
				}

				if ($boolOneSearch) {
					$boolSearch = true;
					break;
				}
			}
			return $boolSearch;
		}

		/**
		 * Set active class in props value by selected item
		 * @param array $arFilter
		 * @param string $index Property code
		 * @return array 
		 */
		private function GetRowValues($arFilter, $index)
		{
			$arValues = [];
			$boolSearch = false;
			$boolOneSearch = true;

			if (!$arFilter) {
				if ($this->items) {
					foreach ($this->items as $arOffer) {
						if (!in_array($arOffer['TREE'][$index], $arValues)) {
							$arValues[] = $arOffer['TREE'][$index];
						}
					}
				}
				$boolSearch = true;
			} else {
				if ($this->items) {
					foreach ($this->items as $arOffer) {
						$boolOneSearch = true;
						foreach ($arFilter as $propName => $filter) {
							if ($filter !== $arOffer['TREE'][$propName]) {
								$boolOneSearch = false;
								break;
							}
						}
						if ($boolOneSearch) {
							if (!in_array($arOffer['TREE'][$index], $arValues)) {
								$arValues[] = $arOffer['TREE'][$index];
							}
							$boolSearch = true;
						}
					}
				}
			}
			return ($boolSearch ? $arValues : false);
		}

		/**
		 * Update classes in tree props value
		 * @param array $arFilter
		 * @param string $index Property code
		 * @return array 
		 */
		private function UpdateRow($arFilter, $arShowValues, &$arProp)
		{
			$isCurrent = false;
			$showI = 0;

			if ($arProp['VALUES']){
				foreach ($arProp['VALUES'] as $key => $arValue) {
					$value = $arValue['ID'];
					// $isCurrent = ($value === $arFilter && $value != 0);
					$isCurrent = ($value === $arFilter);

					/*if (in_array($value, $arCanBuyValues)) {
						$arProp['VALUES'][$key]['CLASS'] = ($isCurrent ? 'active' : '');
					} else {
						$arProp['VALUES'][$key]['CLASS'] = ($isCurrent ? 'active missing' : 'missing');
					}*/
					$arProp['VALUES'][$key]['CLASS'] = ($isCurrent ? 'active' : '');
					
					$arProp['VALUES'][$key]['STYLE'] = 'style="display: none"';

					if (in_array($value, $arShowValues)) {
						$arProp['VALUES'][$key]['STYLE'] = '';

						if ($value != 0) {
							++$showI;
						}
					}

					if ($isCurrent) {
						$arProp['VALUE'] = $arProp['VALUES'][$key]['NAME'];
					}
				}
				if (!$showI) {
					$arProp['STYLE'] = 'style="display: none"';
				} else {
					$arProp['STYLE'] = 'style=""';
				}
			}
		}

		/**
		 * Get tree props list by filter
		 * @param array $arFilter
		 * @param array $skuInfo [
		 * 	IBLOCK_ID => int,
		 * 	PRODUCT_IBLOCK_ID => int,
		 * 	SKU_PROPERTY_ID => int,
		 * 	VERSION => int
		 * ]
		 * @return array 
		 */
		public function getTreePropsByFilter(array $arFilter = [], array $skuInfo = [], array $defaultFields = [])
		{
			$requireFields = array(
				'ID',
				'UF_XML_ID',
				'UF_NAME',
			);
			if (!$defaultFields['PICT']) {
				$defaultFields['PICT'] = SITE_TEMPLATE_PATH.'/images/noimage.png';
			}
			$propertyIterator = Iblock\PropertyTable::getList([
				'select' => [
					'ID', 'IBLOCK_ID', 'CODE', 'NAME', 'SORT', 'LINK_IBLOCK_ID', 'PROPERTY_TYPE', 'USER_TYPE', 'USER_TYPE_SETTINGS', 'HINT'
				],
				'filter' => [
					array_merge(
						$arFilter,
						[
							'=PROPERTY_TYPE' => [
								Iblock\PropertyTable::TYPE_LIST,
								Iblock\PropertyTable::TYPE_ELEMENT,
								Iblock\PropertyTable::TYPE_STRING
							],
							'=ACTIVE' => 'Y', 
							'=MULTIPLE' => 'N'
						]
					)
				],
				'order' => [
					'SORT' => 'ASC', 'ID' => 'ASC'
				]
			]);
			while ($propInfo = $propertyIterator->fetch()) {
				$propInfo['ID'] = (int)$propInfo['ID'];
				if ($skuInfo && $propInfo['ID'] == $skuInfo['SKU_PROPERTY_ID'])
					continue;
				$propInfo['CODE'] = (string)$propInfo['CODE'];
				if ($propInfo['CODE'] === '')
					$propInfo['CODE'] = $propInfo['ID'];
				
				$propInfo['SORT'] = (int)$propInfo['SORT'];
				$propInfo['USER_TYPE'] = (string)$propInfo['USER_TYPE'];
				
				if ($propInfo['PROPERTY_TYPE'] == Iblock\PropertyTable::TYPE_STRING) {
					if ('directory' != $propInfo['USER_TYPE'])
						continue;
					$propInfo['USER_TYPE_SETTINGS'] = (string)$propInfo['USER_TYPE_SETTINGS'];
					if ($propInfo['USER_TYPE_SETTINGS'] == '')
						continue;
					$propInfo['USER_TYPE_SETTINGS'] = unserialize($propInfo['USER_TYPE_SETTINGS'], ['allowed_classes' => false]);
					if (!isset($propInfo['USER_TYPE_SETTINGS']['TABLE_NAME']) || empty($propInfo['USER_TYPE_SETTINGS']['TABLE_NAME']))
						continue;
					if (self::$highLoadInclude === null)
						self::$highLoadInclude = Loader::includeModule('highloadblock');
					if (!self::$highLoadInclude)
						continue;

					$highBlock = HighloadBlockTable::getList(array(
						'filter' => array('=TABLE_NAME' => $propInfo['USER_TYPE_SETTINGS']['TABLE_NAME'])
					))->fetch();
					if (!isset($highBlock['ID']))
						continue;

					$entity = HighloadBlockTable::compileEntity($highBlock);
					$fieldsList = $entity->getFields();
					if (empty($fieldsList))
						continue;

					$flag = true;
					foreach ($requireFields as $fieldCode) {
						if (!isset($fieldsList[$fieldCode]) || empty($fieldsList[$fieldCode])) {
							$flag = false;
							break;
						}
					}
					unset($fieldCode);
					if (!$flag)
						continue;
					$propInfo['USER_TYPE_SETTINGS']['FIELDS_MAP'] = $fieldsList;
					$propInfo['USER_TYPE_SETTINGS']['ENTITY'] = $entity;
				}
				switch ($propInfo['PROPERTY_TYPE']) {
					case Iblock\PropertyTable::TYPE_ELEMENT:
						$showMode = 'pict';
						break;
					case Iblock\PropertyTable::TYPE_LIST:
						$showMode = 'text';
						break;
					case Iblock\PropertyTable::TYPE_STRING:
						$showMode = (isset($fieldsList['UF_FILE']) ? 'pict' : 'text');
						break;
				}
				$propInfo['SHOW_MODE'] = $showMode;

				if ($showMode === 'pict') {
					$propInfo['DEFAULT_VALUES']['PICT'] = $defaultFields['PICT'];
				}

				$this->props[$propInfo['CODE']] = $propInfo;
			}
			unset($propInfo);

			if ($this->props) {
				$this->getPropsValue();
			}
		}
		
		/**
		 * Get value in props list
		 * @return array $this->props
		 */
		public function getPropsValue()
		{
			if (!$this->props) {
				return;
			}
			$useFilterValues = !empty($this->needPropValues) && is_array($this->needPropValues);
			foreach ($this->props as $key => $arProp) {
				$values = [];
				$valuesExist = false;
				$pictMode = ('pict' == $arProp['SHOW_MODE']);
				$needValuesExist = !empty($this->needPropValues[$arProp['ID']]) && is_array($this->needPropValues[$arProp['ID']]);

				if ($useFilterValues && !$needValuesExist)
                    continue;
				switch ($arProp['PROPERTY_TYPE']) {
					case Iblock\PropertyTable::TYPE_LIST:
						if ($needValuesExist) {
                            foreach (array_chunk($this->needPropValues[$arProp['ID']], 500) as $pageIds) {
                                $iterator = Iblock\PropertyEnumerationTable::getList(array(
                                    'select' => array('ID', 'VALUE', 'SORT'),
                                    'filter' => array('=PROPERTY_ID' => $arProp['ID'], '@ID' => $pageIds),
                                    'order' => array('SORT' => 'ASC', 'VALUE' => 'ASC')
                                ));
                                while ($row = $iterator->fetch()) {
                                    $row['ID'] = (int)$row['ID'];
                                    $values[$row['ID']] = array(
                                        'ID' => $row['ID'],
                                        'NAME' => $row['VALUE'],
                                        'SORT' => (int)$row['SORT'],
                                        'PICT' => false
                                    );
                                    $valuesExist = true;
                                }
                                unset($row, $iterator);
                            }
                            unset($pageIds);
                        } else {
							$iterator = Iblock\PropertyEnumerationTable::getList(array(
								'select' => array('ID', 'VALUE', 'SORT'),
								'filter' => array('=PROPERTY_ID' => $arProp['ID']),
								'order' => array('SORT' => 'ASC', 'VALUE' => 'ASC')
							));
							while ($row = $iterator->fetch()) {
								$row['ID'] = (int)$row['ID'];
								$values[$row['ID']] = array(
									'ID' => $row['ID'],
									'NAME' => $row['VALUE'],
									'SORT' => (int)$row['SORT'],
									'PICT' => false
								);
								$valuesExist = true;
							}
							unset($row, $iterator);
						}
						break;
					case Iblock\PropertyTable::TYPE_STRING:
						if (self::$highLoadInclude === null)
							self::$highLoadInclude = Loader::includeModule('highloadblock');
						if (!self::$highLoadInclude)
							continue 2;
						$xmlMap = array();
						$sortExist = isset($arProp['USER_TYPE_SETTINGS']['FIELDS_MAP']['UF_SORT']);

						$directorySelect = array('ID', 'UF_NAME', 'UF_XML_ID');
						$directoryOrder = array();
						if ($pictMode)
							$directorySelect[] = 'UF_FILE';
						if ($sortExist) {
							$directorySelect[] = 'UF_SORT';
							$directoryOrder['UF_SORT'] = 'ASC';
						}
						$directoryOrder['UF_NAME'] = 'ASC';
						$sortValue = 100;

						/** @var Main\Entity\Base $entity */
						$entity = $arProp['USER_TYPE_SETTINGS']['ENTITY'];
						if (!($entity instanceof Main\Entity\Base))
							continue 2;
						$entityDataClass = $entity->getDataClass();
						$entityGetList = array(
							'select' => $directorySelect,
							'order' => $directoryOrder
						);
						if ($needValuesExist) {
                            foreach (array_chunk($this->needPropValues[$arProp['ID']], 500) as $pageIds)
                            {
                                $entityGetList['filter'] = array('=UF_XML_ID' => $pageIds);
                                $iterator = $entityDataClass::getList($entityGetList);
                                while ($row = $iterator->fetch()) {
                                    $row['ID'] = (int)$row['ID'];
                                    $row['UF_SORT'] = ($sortExist ? (int)$row['UF_SORT'] : $sortValue);
                                    $sortValue += 100;

                                    if ($pictMode) {
                                        if (!empty($row['UF_FILE'])) {
                                            $arFile = \CFile::GetFileArray($row['UF_FILE']);
                                            if (!empty($arFile)) {
                                                $row['PICT'] = array(
                                                    'ID' => (int)$arFile['ID'],
                                                    'SRC' => $arFile['SRC'],
                                                    'WIDTH' => (int)$arFile['WIDTH'],
                                                    'HEIGHT' => (int)$arFile['HEIGHT']
                                                );
                                            }
                                        }
                                        if (empty($row['PICT']))
                                            $row['PICT'] = $arProp['DEFAULT_VALUES']['PICT'];
                                    }
                                    $values[$row['ID']] = array(
                                        'ID' => $row['ID'],
                                        'NAME' => $row['UF_NAME'],
                                        'SORT' => (int)$row['UF_SORT'],
                                        'XML_ID' => $row['UF_XML_ID'],
                                        'PICT' => ($pictMode ? $row['PICT'] : false)
                                    );
                                    $valuesExist = true;
                                    $xmlMap[$row['UF_XML_ID']] = $row['ID'];
                                }
                                unset($row, $iterator);
                            }
                            unset($pageIds);
                        } else {
							$iterator = $entityDataClass::getList($entityGetList);
							while ($row = $iterator->fetch()) {
								$row['ID'] = (int)$row['ID'];
								$row['UF_SORT'] = ($sortExist ? (int)$row['UF_SORT'] : $sortValue);
								$sortValue += 100;
	
								if ($pictMode) {
									if (!empty($row['UF_FILE'])) {
										$arFile = \CFile::GetFileArray($row['UF_FILE']);
										if (!empty($arFile))
										{
											$row['PICT'] = array(
												'ID' => (int)$arFile['ID'],
												'SRC' => $arFile['SRC'],
												'WIDTH' => (int)$arFile['WIDTH'],
												'HEIGHT' => (int)$arFile['HEIGHT']
											);
										}
									}
									if (empty($row['PICT']))
										$row['PICT'] = $arProp['DEFAULT_VALUES']['PICT'];
								}
								$values[$row['ID']] = array(
									'ID' => $row['ID'],
									'NAME' => $row['UF_NAME'],
									'SORT' => (int)$row['UF_SORT'],
									'XML_ID' => $row['UF_XML_ID'],
									'PICT' => ($pictMode ? $row['PICT'] : false)
								);
								$valuesExist = true;
								$xmlMap[$row['UF_XML_ID']] = $row['ID'];
							}
							unset($row, $iterator);
						}

						if ($valuesExist)
							$arProp['XML_MAP'] = $xmlMap;
					break;
				}
				if (!$valuesExist) {
					continue;
				}
				$arProp['VALUES'] = $values;
				$arProp['VALUES_COUNT'] = count($values);

				$this->props[$arProp['CODE']] = $arProp;
			}
		}
	}
}?>