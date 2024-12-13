<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(\Bitrix\Main\Loader::includeModule('aspro.allcorp3')){
	if(!isset($arParams['CACHE_TIME'])){
		$arParams['CACHE_TIME'] = 86400;
	}

	$codeBlock = 'INSTAGRAMM';
	$indexType = CAllcorp3::GetFrontParametrValue('INDEX_TYPE');
	$blockType = CAllcorp3::GetFrontParametrValue($indexType.'_'.$codeBlock.'_TEMPLATE');

	if($arParams['WIDE'] === 'FROM_THEME'){
		$arParams['WIDE'] = CAllcorp3::GetFrontParametrValue($indexType.'_'.$codeBlock.'_WIDE_'.$blockType);
	}
	if($arParams['ITEMS_OFFSET'] === 'FROM_THEME'){
		$arParams['ITEMS_OFFSET'] = CAllcorp3::GetFrontParametrValue($indexType.'_'.$codeBlock.'_ITEMS_OFFSET_'.$blockType);
	}
	if($arParams['LINES_COUNT'] === 'FROM_THEME'){
		$arParams['LINES_COUNT'] = CAllcorp3::GetFrontParametrValue($indexType.'_'.$codeBlock.'_LINES_COUNT_'.$blockType);
	}
	if($arParams['ELEMENTS_ROW'] === 'FROM_THEME'){
		$arParams['ELEMENTS_ROW'] = CAllcorp3::GetFrontParametrValue($indexType.'_'.$codeBlock.'_ELEMENTS_COUNT_'.$blockType);
	}
	if($arParams['SHOW_TITLE'] === 'FROM_THEME'){
		$arParams['SHOW_TITLE'] = CAllcorp3::GetFrontParametrValue('SHOW_TITLE_'.$codeBlock.'_'.$indexType);
	}
	if($arParams['TITLE_POSITION'] === 'FROM_THEME'){
		$arParams['TITLE_POSITION'] = CAllcorp3::GetFrontParametrValue('TITLE_POSITION_'.$codeBlock.'_'.$indexType);
	}

	$arParams['SHOW_TITLE'] = $arParams['SHOW_TITLE'] == 'Y';
	$arParams['PAGE_ELEMENT_COUNT'] = $arParams['ELEMENTS_ROW'] ? $arParams['ELEMENTS_ROW'] : 7;


	if($arParams['LINES_COUNT']){
		$arParams['PAGE_ELEMENT_COUNT'] *= $arParams['LINES_COUNT'];
	}

	$arResult['TOKEN'] = CAllcorp3::GetFrontParametrValue('API_TOKEN_INSTAGRAMM');
	$arParams['TITLE'] = CAllcorp3::GetFrontParametrValue('INSTAGRAMM_TITLE_BLOCK');
	$arParams['RIGHT_TITLE'] = CAllcorp3::GetFrontParametrValue('INSTAGRAMM_TITLE_ALL_BLOCK');
	$arResult['TEXT_LENGTH'] = CAllcorp3::GetFrontParametrValue('INSTAGRAMM_TEXT_LENGTH');	

	if(!is_object($GLOBALS['USER'])){
		$GLOBALS['USER'] = new CUser();
	}

	if(
		$this->startResultCache(
			$arParams['CACHE_TIME'],
			array(
				($arParams['CACHE_GROUPS'] === 'N'? false: $GLOBALS['USER']->GetGroups()),
				$arResult
			)
		)
	){
		$obInstagram = new CInstargramAllcorp3($arResult['TOKEN'], $arParams['PAGE_ELEMENT_COUNT']);

		$arData = $obInstagram->getInstagramPosts();
		//$arUser = $obInstagram->getInstagramUser();

		if($arData){
			if($arData['error']['message']){
				$arResult['ERROR'] = $arData['error']['message'];
			}
			elseif($arData['data']){
				$arResult['ITEMS'] = array_slice($arData['data'], 0, $arParams['PAGE_ELEMENT_COUNT']);
				$arResult['USER']['username'] = $arData['data'][0]['username'];

				$arParams['RIGHT_LINK'] = 'https://www.instagram.com/'.$arResult['USER']['username'].'/';
				$arParams['RIGHT_LINK_EXTERNAL'] = true;
			}

		}

		if($arResult['ERROR']){
			$this->AbortResultCache();
			?>
			<?if($GLOBALS['USER']->IsAdmin()):?>
				<div class="content_wrapper_block">
					<div class="maxwidth-theme" style="padding-top: 20px;">
						<div class="alert alert-danger">
							<strong>Error: </strong><?=$arResult['ERROR']?>
						</div>
					</div>
				</div>
			<?endif;?>
			<?
		}

		$this->IncludeComponentTemplate();
	}

}
else{
	return;
}
?>