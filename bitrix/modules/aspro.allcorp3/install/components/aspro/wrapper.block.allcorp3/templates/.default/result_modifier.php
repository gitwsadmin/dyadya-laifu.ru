<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();?>
<?if ($arParams['CODE_BLOCK']) {
	$codeBlock = $arParams['CODE_BLOCK'];
	$indexType = CAllcorp3::GetFrontParametrValue('INDEX_TYPE');
	$blockType = CAllcorp3::GetFrontParametrValue($indexType.'_'.$codeBlock.'_TEMPLATE');

	if ($arParams['WIDE'] === 'FROM_THEME') {
		$arParams['WIDE'] = CAllcorp3::GetFrontParametrValue($indexType.'_'.$codeBlock.'_WIDE_'.$blockType);
	}

	if ($arParams['OFFSET'] === 'FROM_THEME') {
		$arParams['OFFSET'] = CAllcorp3::GetFrontParametrValue($indexType.'_'.$codeBlock.'_OFFSET_'.$blockType);
	}
}
?>