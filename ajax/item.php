<?define("STATISTIC_SKIP_ACTIVITY_CHECK", "true");?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

if (!\Bitrix\Main\Loader::includeModule("iblock") || !\Bitrix\Main\Loader::includeModule("aspro.allcorp3")) {
	return \Bitrix\Main\Web\Json::encode(['error' => 'Error include module']);
}

$request = Bitrix\Main\Application::getInstance()->getContext()->getRequest();
$request->addFilter(new \Bitrix\Main\Web\PostDecodeFilter);

$action = $request->get('action');
$type = $request->get('type');
if (check_bitrix_sessid() && $action) {
	$arItems = [];
	switch ($action) {
		case 'compare':
			$iblockID = $request->get('IBLOCK_ID');
			if($type === "multiple") {
				$arItems  =  $request->get('items');
				if($iblockID && $arItems) {
					foreach($arItems as $arItem){
						$_SESSION["CATALOG_COMPARE_LIST"][$iblockID]["ITEMS"][$arItem["ID"]] = \CIBlockElement::GetByID($arItem["ID"])->Fetch();
					}
					$arItems = array_keys($_SESSION["CATALOG_COMPARE_LIST"][$iblockID]["ITEMS"]);
				} else {
					die(\Bitrix\Main\Web\Json::encode(['error' => 'Not enought parameters']));
				}
			} else {
				$itemID = $request->get('ID');
				if ($iblockID && $itemID) {
					if(
						!empty($_SESSION["CATALOG_COMPARE_LIST"])
						&& !empty($_SESSION["CATALOG_COMPARE_LIST"][$iblockID])
						&& array_key_exists($itemID, $_SESSION["CATALOG_COMPARE_LIST"][$iblockID]["ITEMS"])
					) {
						unset($_SESSION["CATALOG_COMPARE_LIST"][$iblockID]["ITEMS"][$itemID]);
					} else {
						$_SESSION["CATALOG_COMPARE_LIST"][$iblockID]["ITEMS"][$itemID] = \CIBlockElement::GetByID($itemID)->Fetch();
					}
					$arItems = array_keys($_SESSION["CATALOG_COMPARE_LIST"][$iblockID]["ITEMS"]);
				} else {
					die(\Bitrix\Main\Web\Json::encode(['error' => 'Not enought parameters']));
				}
			}
			
			break;
	}
	die(\Bitrix\Main\Web\Json::encode(['status' => 'OK', 'items' => $arItems]));
}
die(\Bitrix\Main\Web\Json::encode(['error' => 'Not enought parameters']));