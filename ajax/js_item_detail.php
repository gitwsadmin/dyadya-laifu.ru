<?define("STATISTIC_SKIP_ACTIVITY_CHECK", "true");
define('STOP_STATISTICS', true);
define('PUBLIC_AJAX_MODE', true);?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");?>

<?$context = \Bitrix\Main\Application::getInstance()->getContext();
$request = $context->getRequest();
$arPost = $request->getPostList()->toArray();

global $APPLICATION;
$arPost = $APPLICATION->ConvertCharsetArray($arPost, 'UTF-8', LANG_CHARSET);

if (!strlen($arPost["SITE_ID"])) {
	$arPost["SITE_ID"] = SITE_ID;
}

if($arPost["SITE_ID"]){
	$dbRes = CSite::GetByID($arPost["SITE_ID"]);
	$arSite = $dbRes->Fetch();
	$arSite['DIR'] = str_replace('//', '/', '/'.$arSite['DIR']);
}
?>
<?if($arPost["PARAMS"]):?>
	<?$arPropsTmp = array();
	foreach ($arPost as $key => $value) {
		if (strpos($key, 'PROP_') !== false) {
			$arPropsTmp[$key] = $value;
		}
	}
	$arSelectedProps = json_encode($arPropsTmp);

	if (!$arPost['ADD_PICT_PROP']) {
		$arPost['ADD_PICT_PROP'] = 'PHOTOS';
	}

	
	$obSKU = new \Aspro\Allcorp3\Functions\CSKU($arPost["PARAMS"]);
	$obSKU->getTreePropsByFilter([
		'=IBLOCK_ID' => $arPost['SKU_IBLOCK_ID'],
		'CODE' => $arPost["PARAMS"]['SKU_TREE_PROPS']
	]);

	/* get liked items */
	$arLinkedProp = [];
	$rsProp = CIBlockElement::GetProperty(
		$arPost['IBLOCK_ID'], 
		$arPost['ID'], 
		[
			"sort" => "asc"
		], 
		[
			"CODE"=>$arPost['PARAMS']['LINK_SKU_PROP_CODE']
		]
	);
	while ($arProp = $rsProp->fetch()) {
		if (!$arLinkedProp) {
			$arLinkedProp = [
				'VALUE' => [$arProp['VALUE']],
				'LINK_IBLOCK_ID' => $arProp['LINK_IBLOCK_ID']
			];
		} else {
			$arLinkedProp['VALUE'][] = $arProp['VALUE'];
		}
	}
	/* */

	/* get element */
	$arElement = \CAllcorp3Cache::CIBlockElement_GetList(
		[
			"CACHE" => [
				"TAG" => \CAllcorp3Cache::GetIBlockCacheTag($arPost['IBLOCK_ID']),
				'MULTI' => 'N'
			]
		],
		[
			'ID' => $arPost['ID'],
			'ACTIVE' => 'Y'
		],
		false,
		false,
		[
			'ID',
			'NAME',
			'IBLOCK_ID',
			'PREVIEW_TEXT',
			'PREVIEW_PICTURE',
			'DETAIL_TEXT',
			'DETAIL_PICTURE',
			'DETAIL_PAGE_URL',
			'PROPERTY_'.$arPost['ADD_PICT_PROP'],
			'PROPERTY_HIT',
			'PROPERTY_SALE_TEXT'
		]
	);

	if (
		($arElement['DETAIL_PICTURE'] && $arElement['PREVIEW_PICTURE']) ||
		(!$arElement['DETAIL_PICTURE'] && $arElement['PREVIEW_PICTURE'])
	) {
		$arElement['DETAIL_PICTURE'] = $arElement['PREVIEW_PICTURE'];
	}
	if ($arElement['PROPERTY_'.$arPost['ADD_PICT_PROP'].'_VALUE']) {
		$arElement['PROPERTIES'][$arPost['ADD_PICT_PROP']]['PROPERTY_TYPE'] = 'F';
		foreach ((array)$arElement['PROPERTY_'.$arPost['ADD_PICT_PROP'].'_VALUE'] as $value) {
			$arElement['PROPERTIES'][$arPost['ADD_PICT_PROP']]['VALUE'][] = $value;
		}
	}

	// site name postfix 
	$arElement['POSTFIX'] = '';
	if (
		$arSite && 
		CAllcorp3::GetFrontParametrValue('HIDE_SITE_NAME_TITLE') == 'N'
	) {
		$arElement['POSTFIX'] = ' - '.$arSite['SITE_NAME'];
	}
	/* */
	
	$obSKU->setLinkedPropFromArray($arLinkedProp);
	$obSKU->getItemsByProperty();

	$bChangeTitle = CAllcorp3::GetFrontParametrValue($arPost['IS_DETAIL'] === 'Y' ? 'CHANGE_TITLE_ITEM_DETAIL' : 'CHANGE_TITLE_ITEM_LIST') == 'Y';
	
	$arItems = [];
	foreach ($obSKU->items as $arItem) {
		if ($arElement['DETAIL_PAGE_URL']) {
			$arItem['DETAIL_PAGE_URL'] = $arElement['DETAIL_PAGE_URL'];
			if ($arPost["OID"]) {
				$arItem['DETAIL_PAGE_URL'] .= '?'.$arPost["OID"].'='.$arItem['ID'];
			}
		}

		$arItem['PRICES_HTML'] = \Aspro\Functions\CAsproAllcorp3::showPrice(
			array_merge(
				(array)$arPost['PRICE_PARAMS'],
				[
					'ITEM' => $arItem,
					'SHOW_SCHEMA' => false,
					'RETURN' => true,
					'CATALOG_IBLOCK_ID' => $arElement['IBLOCK_ID'], 
					'ITEM_ID' => $arElement['ID'],
				]
			)
		);		
		$arItem['BASKET_HTML'] = \Aspro\Functions\CAsproAllcorp3::showBasketButton(
			array_merge(
				(array)$arPost['BASKET_PARAMS'],
				[
					'ITEM' => $arItem,
					'PARAMS' => $obSKU->config,
					'ORDER_BTN' => ($arItem["DISPLAY_PROPERTIES"]["FORM_ORDER"]["VALUE_XML_ID"] == "YES"),
					'SHOW_COUNTER' => false,
					'RETURN' => true,
				]
			)
		);
		
		if ($arPost['IS_DETAIL'] === 'Y') {
			$pictureID = $arItem['DETAIL_PICTURE'] ?? $arItem['PREVIEW_PICTURE'];
		}
		else {
			$pictureID = $arItem['PREVIEW_PICTURE'] ?? $arItem['DETAIL_PICTURE'];
		}

		if ($pictureID) {
			$arItem['PICTURE_SRC'] = \CFile::GetPath($pictureID);
		}
		else {
			if ($arElement['PREVIEW_PICTURE']) {
				$arItem['PREVIEW_PICTURE'] = $arElement['PREVIEW_PICTURE'];
			}
		}

		if ($arPost['PARAMS']['SHOW_GALLERY'] === 'Y') {
			$arItem['GALLERY'] = \Aspro\Functions\CAsproAllcorp3::getSliderForItem([
				'TYPE' => 'catalog_block',
				'PROP_CODE' => $arPost['ADD_PICT_PROP'],
				// 'ADD_DETAIL_SLIDER' => false,
				'ITEM' => $arElement,
				'PARAMS' => $arParams,
			]);

			if ($pictureID) {
				$arPicture = \CFile::GetFileArray($pictureID);

				$alt = $arPicture['DESCRIPTION'] ?: $arPicture['ALT'] ?: ($bChangeTitle ? $arItem['NAME'] : $arElement['NAME']);
				$title = $arPicture['DESCRIPTION'] ?: $arPicture['TITLE'] ?: ($bChangeTitle ? $arItem['NAME'] : $arElement['NAME']);

				$arPicture['TITLE'] = $title;
				$arPicture['ALT'] = $alt;

				array_unshift($arItem['GALLERY'], $arPicture);
			}

			array_splice($arItem['GALLERY'], $arPost['MAX_GALLERY_ITEMS']);
			
			$arItem['GALLERY'] = \Aspro\Functions\CAsproAllcorp3::resizeImages($arItem['GALLERY']);
			$arItem['GALLERY_HTML'] = \Aspro\Functions\CAsproAllcorp3::showImage(
				array_merge(
					(array)$arPost['IMG_PARAMS'],
					[
						'ITEM' => $arItem,
						'PARAMS' => $arPost['PARAMS'],
						'RETURN' => true,
						'CATALOG_IBLOCK_ID' => $arElement['IBLOCK_ID'],
						'ITEM_ID' => $arElement['ID'],
					]
				)
			);
		} else {
			$arItem['ICONS_HTML'] = \Aspro\Functions\CAsproAllcorp3::showSideIcons([
				'ITEM' => $arItem,
				'PARAMS' => $arPost['PARAMS'],
				'RETURN' => true,
				'CATALOG_IBLOCK_ID' => $arElement['IBLOCK_ID'],
				'ITEM_ID' => $arElement['ID'],
			]);
		}

		$arItem['BASKET_JSON'] = \CAllcorp3::getDataItem($arItem, false);
		if ($arItem['DETAIL_PAGE_URL']) {
			$arItem['BASKET_JSON']['DETAIL_PAGE_URL'] = $arItem['DETAIL_PAGE_URL'];
		}
		$arItem['OFFER_PROP'] = \CAllcorp3::PrepareItemProps($arItem['DISPLAY_PROPERTIES']);

		$arItems[] = $arItem;
	}?>
	<script>
		GetRowValues = function(arFilter, index)
		{
			var i = 0,
				j,
				arValues = [],
				boolSearch = false,
				boolOneSearch = true;

			if (0 === arFilter.length) {
				for (i = 0; i < obOffers.length; i++) {
					if (!BX.util.in_array(obOffers[i].TREE[index], arValues))
						arValues[arValues.length] = obOffers[i].TREE[index];
				}
				boolSearch = true;
			} else {
				for (i = 0; i < obOffers.length; i++) {
					boolOneSearch = true;
					for (j in arFilter) {
						if (arFilter[j]) {
							if (arFilter[j].toString() !== obOffers[i].TREE[j]) {
								boolOneSearch = false;
								break;
							}
						}
					}

					if (boolOneSearch) {
						if (!BX.util.in_array(obOffers[i].TREE[index], arValues))
							arValues[arValues.length] = obOffers[i].TREE[index];
						boolSearch = true;
					}
				}
			}
			return (boolSearch ? arValues : false);
		};

		ChangeInfo = function()
		{
			var i = 0,
				j,
				index = -1,
				compareParams,
				selectedValues = {},
				boolOneSearch = true;

			if ($(containerClass).data('selected')) {
				selectedValues = $(containerClass).data('selected');
			}

			for (i = 0; i < obOffers.length; i++) {
				boolOneSearch = true;
				for (j in selectedValues) {
					if (selectedValues[j]) {
						if (selectedValues[j].toString() !== obOffers[i].TREE[j]) {
							boolOneSearch = false;
							break;
						}
					}
				}
				if (boolOneSearch) {
					index = i;
					break;
				}
			}

			if (-1 < index) {
				UpdateStatus(index, '.js-replace-status:first')
				UpdateStatus(index, '.js-popup-block-adaptive .js-replace-status:first')
				UpdateArticle(index, '.js-replace-article:first')
				UpdateArticle(index, '.js-popup-block-adaptive .js-replace-article:first')
				UpdatePrice(index, '.js-popup-price:first')
				UpdatePrice(index, '.js-popup-block-adaptive .js-popup-price:first')
				UpdateItemInfoForBasket(index, '[data-item]:first')
				UpdateItemInfoForBasket(index, '.js-popup-block-adaptive [data-item]:first')
				UpdateBtnBasket(index, '.js-replace-btns:first')
				UpdateBtnBasket(index, '.js-popup-block-adaptive .js-replace-btns:first')
				UpdateProps(index)
				UpdateImages(index)
				UpdateSideIcons(index)
				UpdateLink(index)
				UpdateTitle(index)
				UpdateDescription(index)
				setCompareItemsClass()
			}
		};

		UpdateLink = function (index)
		{
			const $titleLink = wrapper.find('.js-popup-title:first')
			if ($titleLink.length) {
				let url = $titleLink.attr('href');
				const $detailBlock = wrapper.find('.detail-block');
				if (arAsproOptions['OID']) {
					const re = new RegExp('(' + arAsproOptions['OID'] + '=)' + '(\\d+)');
					if (url && !$detailBlock.length) {
						const matches = url.match(re);
						if (matches && matches.length === 3) {
							url = url.replace(matches[2], obOffers[index]['ID'])
							$titleLink.attr('href', url)
							if (wrapper.find('.image-list__link').length) {
								wrapper.find('.image-list__link').attr('href', url)
							}
						}

						const $moreLink = wrapper.find('.js-replace-more')
						if ($moreLink.length) {
							$moreLink.attr('href', url)
						}
						const data = wrapper.find('[data-item]:first').data('item');
						if (data) {
							data['DETAIL_PAGE_URL'] = url;
							wrapper.find('[data-item]:first').data('item', data)
						}

					} else {
						let href = window.location.href
						let matches = href.match(re);
						if (matches && matches.length === 3) {
							href = href.replace(matches[2], obOffers[index]['ID'])
						} else {
							let queryString = location.search;
							if (queryString) {
								matches = queryString.match(re);
								if (!matches) {
									queryString += '&'+arAsproOptions['OID']+'='+obOffers[index]['ID']
								}
							} else {
								queryString = '?'+arAsproOptions['OID']+'='+obOffers[index]['ID']
							}
							href = location.pathname+queryString;
						}
						history.replaceState(null, null, href);
					}
				}
			}
		}
		UpdateStatus = function (index, selector)
		{
			const $status = wrapper.find(selector);
			if ($status.length) {
				let value = $status.data('value');
				let code = $status.data('code');
				let state = $status.data('state');
				$status.removeClass(state);
				if (obOffers[index]['DISPLAY_PROPERTIES']['STATUS']['VALUE']) {
					value = obOffers[index]['DISPLAY_PROPERTIES']['STATUS']['VALUE'];
					state = obOffers[index]['DISPLAY_PROPERTIES']['STATUS']['VALUE_XML_ID'];
				}
				$status.text(value);
				$status.data('state', state);
				$status.addClass(state);
			}
		}
		UpdateArticle = function (index, selector)
		{
			const $article = wrapper.find(selector);
			if ($article.length) {
				let value = $article.data('value');
				if (obOffers[index]['DISPLAY_PROPERTIES']['ARTICLE']['VALUE']) {
					value = obOffers[index]['DISPLAY_PROPERTIES']['ARTICLE']['VALUE'];
				}
				$article.text(value);
			}
		}
		UpdatePrice = function (index, selector)
		{
			const $price = wrapper.find(selector);
			if ($price.length) {
				$price.html(obOffers[index]['PRICES_HTML']);
			}
		}
		UpdateItemInfoForBasket = function (index, selector)
		{
			wrapper.find(selector)
			 .data('id', obOffers[index]['ID'])
			 .data('item', obOffers[index]['BASKET_JSON']);
		}
		UpdateBtnBasket = function (index, selector)
		{
			const $btn = wrapper.find(selector);
			if ($btn.length) {
				$btn.html(obOffers[index]['BASKET_HTML']);
				if (typeof setBasketItemsClasses === 'function') {
					setBasketItemsClasses();
				}
			}
		}
		UpdateTitle = function (index)
		{
			var block = wrapper,
				isDetail = block.hasClass('catalog-detail');

			if (isDetail) {
				if (arAsproOptions.THEME.CHANGE_TITLE_ITEM_DETAIL === 'Y') {
					let skuName = typeof obOffers[index].IPROPERTY_VALUES === 'object' && obOffers[index].IPROPERTY_VALUES.ELEMENT_PAGE_TITLE ? obOffers[index].IPROPERTY_VALUES.ELEMENT_PAGE_TITLE : obOffers[index].NAME;

					let skuWindowTitle = typeof obOffers[index].IPROPERTY_VALUES === 'object' && obOffers[index].IPROPERTY_VALUES.ELEMENT_META_TITLE ? obOffers[index].IPROPERTY_VALUES.ELEMENT_META_TITLE : obOffers[index].NAME;

					let bFastView = wrapper.closest('.fast_view_frame').length;

					if (bFastView) {
						wrapper.find('.js-popup-title').html(skuName);
					}
					else {
						$("h1").html(skuName);
						document.title = skuWindowTitle + "" + obElement.POSTFIX;
					}
				}
			}
			else {
				if (arAsproOptions.THEME.CHANGE_TITLE_ITEM_LIST === 'Y') {
					let skuName = typeof obOffers[index].IPROPERTY_VALUES === 'object' && obOffers[index].IPROPERTY_VALUES.ELEMENT_PAGE_TITLE ? obOffers[index].IPROPERTY_VALUES.ELEMENT_PAGE_TITLE : obOffers[index].NAME;

					let $titleLink = wrapper.find('.js-popup-title:first');
					if ($titleLink.length) {
						$titleLink.find('span').html(skuName);
					}
				}
			}
			
		}
		UpdateDescription = function (index)
		{
			var block = wrapper,
				isDetail = block.hasClass('catalog-detail'),
				$detailTextBlock = block.find('.catalog-detail__detailtext'),
				$previewTextBlock = block.find('.catalog-detail__previewtext .text-block');

			if (isDetail) {
				<?if($arPost['PARAMS']['SHOW_SKU_DESCRIPTION'] === 'Y'):?>
				if ($previewTextBlock.length) {
					if (obOffers[index].PREVIEW_TEXT.length) {
						$previewTextBlock.html(obOffers[index].PREVIEW_TEXT);
					} else if (obElement.PREVIEW_TEXT.length) {
						$previewTextBlock.html(obElement.PREVIEW_TEXT);
					}
				}

				if ($detailTextBlock.length) {
					if (obOffers[index].DETAIL_TEXT.length) {
						$detailTextBlock.html(obOffers[index].DETAIL_TEXT);
					} else if (obElement.DETAIL_TEXT.length) {
						$detailTextBlock.html(obElement.DETAIL_TEXT);
					}
				}
				<?endif;?>
			}
		}
		UpdateProps = function (index)
		{
			const $props = wrapper.find('.js-offers-prop:first')
			if ($props.length) {
				wrapper.find('.js-prop').remove();
				if (obOffers[index]['OFFER_PROP']) {
					if (!Object.keys(obOffers[index]['OFFER_PROP']).length) {
						return;
					}
					if (!window['propTemplate']) {
						let $clone = $props.clone()
						$clone.find('> *:not(:first-child)').remove()
						$clone.find('.js-prop-replace').removeClass('js-prop-replace').addClass('js-prop');
						$clone.find('.js-prop-title').text('#PROP_TITLE#')
						$clone.find('.js-prop-value').text('#PROP_VALUE#')
						$clone.find('.hint').remove()
						let cloneHtml = $clone.html()
						window['propTemplate'] = cloneHtml;
					}

					let html = '';
					for (let key in obOffers[index]['OFFER_PROP']) {
						let title = obOffers[index]['OFFER_PROP'][key]['NAME'];
						let value = obOffers[index]['OFFER_PROP'][key]['VALUE'];

						let str = window['propTemplate']
							.replace('#PROP_TITLE#', title)
							.replace('#PROP_VALUE#', value);

						html += str;
					}
					if (html) {
						$props[0].insertAdjacentHTML('beforeend', html);
					}
				}
			} 
		}
		UpdateImages = function (index)
		{
			if (wrapper.find('.js-detail-img').length) {
				UpdateDetailImages(index)
			} else {
				UpdateListImages(index)
			}
		}
		UpdateListImages = function (index)
		{
			let $img = wrapper.find('.js-replace-img')
			let $gallery = wrapper.find('.js-image-block')
			if ($gallery.length && obOffers[index]['GALLERY_HTML']) {
				let sticker = $gallery.find('.sticker')
				if (sticker.length) {
					sticker.appendTo(wrapper.find('.js-config-img'))
				}
				$gallery.html($(obOffers[index]['GALLERY_HTML']).find('.js-image-block').html())
				$gallery.prepend(sticker)
			} else if ($img.length) {
				let src = $img.data('js') ? $img.data('js') : $img.attr('src');
				if (obOffers[index]['PICTURE_SRC']) {
					src = obOffers[index]['PICTURE_SRC'];
				}
				$img.prop('src', src)
			}
		}
		UpdateDetailImages = function (index)
		{
			let $gallery = wrapper.find('.js-detail-img')
			let $galleryThmb = wrapper.find('.js-detail-img-thmb')
			if ($gallery.length && obOffers[index]['GALLERY']) {
				const countPhoto = obOffers[index]['GALLERY'].length
				let slideHtml = '';

				for (let i in obOffers[index]['GALLERY']) {
					const image = obOffers[index]['GALLERY'][i]
					const title = (image['TITLE'] ? image['TITLE'] : obOffers[index]['NAME'])
					const alt = (image['ALT'] ? image['ALT'] : obOffers[index]['NAME'])
					if (typeof(image) === 'object') {
						slideHtml+='<div id="photo-'+i+'" class="catalog-detail__gallery__item catalog-detail__gallery__item--big">'
							slideHtml+='<a href="'+image['SRC']+'" data-fancybox="gallery" class="catalog-detail__gallery__link popup_link fancy" title="'+title+'">'
								slideHtml+='<img class="catalog-detail__gallery__picture" src="'+image['SMALL']['src']+'" alt="'+alt+'" title="'+title+'" />'
							slideHtml+='</a>';
						slideHtml+='</div>';
					}
				}

				$gallery.html(slideHtml)

				if ($gallery.data('owl.carousel') !== undefined) {
					$gallery.addClass('destroyed');
					$gallery.data('owl.carousel').destroy();
				}

				if ($galleryThmb.length) {
					let slideThmbHtml = '';
					
					if (countPhoto > 1) {
						for (let i in obOffers[index]['GALLERY']) {
							const image = obOffers[index]['GALLERY'][i]
							const title = (image['TITLE'] ? image['TITLE'] : obOffers[index]['NAME'])
							const alt = (image['ALT'] ? image['ALT'] : obOffers[index]['NAME'])
							if (typeof(image) === 'object') {
								slideThmbHtml+='<div id="photo-'+i+'" class="catalog-detail__gallery__item catalog-detail__gallery__item--thmb">'
									slideThmbHtml+='<img class="catalog-detail__gallery__picture" src="'+image['THUMB']['src']+'" alt="'+alt+'" title="'+title+'" />'
								slideThmbHtml+='</div>';
							}
						}
						$galleryThmb.css({
							'max-width':Math.ceil(((countPhoto <= 3 ? countPhoto : 3) * (58 + 8)) - 8 + 48)
						});
					}
					$galleryThmb.attr('data-size', countPhoto)
					$galleryThmb.html(slideThmbHtml)
					
					if ($galleryThmb.data('owl.carousel') !== undefined) {
						$galleryThmb.addClass('destroyed');
						$galleryThmb.data('owl.carousel').destroy();
					}
				}

				wrapper.find('[itemprop="image"]').attr('href', obOffers[index]['GALLERY'][0]['SRC'])
				
				InitOwlSlider();
				InitFancyBox();
			}
		}
		UpdateSideIcons = function (index)
		{
			let $icons = wrapper.find('.js-replace-icons:first')
			if ($icons.length && obOffers[index]['ICONS_HTML']) {
				$icons.html($(obOffers[index]['ICONS_HTML']).html())
			}
		}
		UpdateRow = function(intNumber, activeID, showID, canBuyID)
		{
			var i = 0,
				showI = 0,
				value = '',
				countShow = 0,
				strNewLen = '',
				obData = {},
				obDataCont = {},
				pictMode = false,
				extShowMode = false,
				isCurrent = false,
				selectIndex = 0,
				obLeft = this.treeEnableArrow,
				obRight = this.treeEnableArrow,
				currentShowStart = 0,
				RowItems = null;

			if (-1 < intNumber && intNumber < $(containerClass + ' .sku-props__inner').length) {
				var activeClass = 'sku-props__value--active';

				RowItems = BX.findChildren($(containerClass + ' .sku-props__inner:eq('+intNumber+') .sku-props__values')[0], {'tag': 'div'}, false);

				if (!!RowItems && 0 < RowItems.length){
					countShow = showID.length;
					obData = {
						style: {},
						props: {
							disabled: '',
							selected: '',
						},
					};
					obDataCont = {
						style: {},
					};

					$titleNode = $(containerClass + ' .sku-props__inner:eq('+intNumber+') .sku-props__js-size')

					for (i = 0; i < RowItems.length; i++){
						let $item = RowItems[i].querySelector('.sku-props__value');
						let classList = $item.classList.value.replace(activeClass, '');
						
						let value = $item.getAttribute('data-onevalue');
						let title = $item.getAttribute('data-title');

						isCurrent = (value === activeID && value !=0);

						if (isCurrent) {
							classList += ' ' + activeClass;
							$titleNode.text(title)
						}

						obData.style.display = 'none';
						
						if (BX.util.in_array(value, showID)) {
							obData.style.display = '';
							
							if (isCurrent) {
								selectIndex = showI;
							}
							showI++;
						}
						$item.className = classList;

						BX.adjust(RowItems[i], obData);
					}
					// activeID is string, and can be '0' or ''
					if(!showI || activeID == 0) {
						obDataCont.style.display = 'none';
					} else {
						obDataCont.style.display = '';
					}
					BX.adjust($(containerClass + ' .sku-props__inner:eq('+intNumber+')')[0], obDataCont);
				}
			}
		};

		var strName = '',
			arShowValues = false,
			i, j,
			containerClass = '.sku-props.js-selected',
			arCanBuyValues = [],
			selectedValues = JSON.parse('<?=$arSelectedProps?>'),
			obOffers = <?=CUtil::PhpToJSObject($arItems, false, true)?>,
			obElement = <?=CUtil::PhpToJSObject($arElement, false, true)?>,
			strPropValue = '<?=$arPost['VALUE'];?>',
			depth = '<?=$arPost['DEPTH'];?>',
			wrapper = $(containerClass).closest('.js-popup-block'),
			arFilter = {},
			tmpFilter = [];			

		UpdateSKUInfoByProps = function(){
			for (i = 0; i < depth; i++) {
				strName = 'PROP_'+$(containerClass + ' .sku-props__inner:eq('+i+')').data('id');
				arFilter[strName] = selectedValues[strName].toString();
			}
			strName = 'PROP_'+$(containerClass + ' .sku-props__inner:eq('+depth+')').data('id');
	
			arShowValues = GetRowValues(arFilter, strName);
			if (arShowValues && BX.util.in_array(strPropValue, arShowValues)) {
				if ($(containerClass).data('selected')) {
					selectedValues = $(containerClass).data('selected');
				}
	
				arFilter[strName] = strPropValue;
				for (i = ++depth; i < $(containerClass + ' .sku-props__inner').length; i++) {
					strName = 'PROP_'+$(containerClass + ' .sku-props__inner:eq('+i+')').data('id');
					arShowValues = GetRowValues(arFilter, strName);
	
					if (!arShowValues) {
						break;
					}
	
					arCanBuyValues = arShowValues;
	
					if (selectedValues[strName] && BX.util.in_array(selectedValues[strName], arCanBuyValues)) {
						arFilter[strName] = selectedValues[strName].toString();
					} else {
						arFilter[strName] = arCanBuyValues[0];
					}
					UpdateRow(i, arFilter[strName], arShowValues, arCanBuyValues);
				}
				$(containerClass).data('selected', arFilter);
	
				ChangeInfo();
			}
		}
		UpdateSKUInfoByProps()
	</script>
<?endif;?>