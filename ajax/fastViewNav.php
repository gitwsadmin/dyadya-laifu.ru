<?
define('STATISTIC_SKIP_ACTIVITY_CHECK', 'true');
define('STOP_STATISTICS', true);
define('PUBLIC_AJAX_MODE', true);
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

$action = isset($_REQUEST['action']) ? trim($_REQUEST['action']) : 'prev';
$elementId = isset($_REQUEST['element']) ? intval($_REQUEST['element']) : false;
$iblockId = isset($_REQUEST['iblock']) ? intval($_REQUEST['iblock']) : false;
$sectionId = isset($_REQUEST['section']) ? intval($_REQUEST['section']) : false;
?>
<?if(
	$elementId > 0 &&
	strlen($action) &&
	\Bitrix\Main\Loader::includeModule('aspro.allcorp3')
):?>
	<?
	$arFilter = array();
	$filter = isset($_REQUEST['filter']) ? trim($_REQUEST['filter']) : '{}';
	if($filter){
		$arFilter = \Bitrix\Main\Web\Json::decode($filter);
	}
	
	if(
		$sectionId &&
		(
			!$arFilter ||
			(
				!$arFilter['SECTION_ID'] &&
				!$arFilter['SECTION_CODE']
			)
		)		
	){
		$arFilter['SECTION_ID'] = $sectionId;
	}
	
	$arFilter['INCLUDE_SUBSECTIONS'] = 'Y';
	$arFilter['ACTIVE'] = 'Y';
	$arFilter['GLOBAL_ACTIVE'] = 'Y';

	if(
		!isset($arFilter['IBLOCK_ID']) &&
		$iblockId
	){
		$arFilter['IBLOCK_ID'] = $iblockId;
	}

	$arOrder = array();
	$sort = isset($_REQUEST['sort']) ? trim($_REQUEST['sort']) : '{}';
	if($sort) {
		$sort = \Bitrix\Main\Web\Json::decode($sort);
		$arOrder = $sort;
	}
	$arOrder['CACHE'] = array('TAG' => CAllcorp3Cache::GetIBlockCacheTag($iblockId));

	$arNewItem = array();
	$arItems = CAllcorp3Cache::CIBlockElement_GetList($arOrder, $arFilter, false, false, array('DETAIL_PAGE_URL'));

	foreach($arItems as $i => $arItem){
		if($arItem['ID'] == $elementId){
			if($action === 'next'){
				if(isset($arItems[$i + 1])){
					$arNewItem = &$arItems[$i + 1];
				}
				else{
					$arNewItem = &$arItems[0];
				}
			}
			else{
				if(isset($arItems[$i - 1])){
					$arNewItem = &$arItems[$i - 1];
				}
				else{
					$arNewItem = &$arItems[count($arItems) - 1];
				}
			}

			break;
		}
	}

	$_GET['id'] = $_REQUEST['id'] = $arNewItem['ID'];
	$_GET['iblock_id'] = $_REQUEST['iblock_id'] = $arNewItem['IBLOCK_ID'];
	$_GET['item_href'] = $_REQUEST['item_href'] = $arNewItem['DETAIL_PAGE_URL'];

	include('fast_view.php');
	?>
<?endif;?>