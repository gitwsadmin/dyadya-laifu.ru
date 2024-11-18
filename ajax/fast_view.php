<?
define('STATISTIC_SKIP_ACTIVITY_CHECK', 'true');
define('STOP_STATISTICS', true);
define('PUBLIC_AJAX_MODE', true);
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

$iblockId = isset($_REQUEST['iblock_id']) ? intval($_REQUEST['iblock_id']) : false;
$id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : false;
?>
<?if(
	$iblockId > 0 && 
	$id > 0 &&
	\Bitrix\Main\Loader::includeModule('aspro.allcorp3')
):?>
	<?
	global $APPLICATION, $arRegion, $arTheme;
	$arRegion = CAllcorp3Regionality::getCurrentRegion();
	$arTheme = CAllcorp3::GetFrontParametrsValues(SITE_ID);
	$url = htmlspecialcharsbx(urldecode($_GET['item_href']));
	?>
	<script data-skip-moving="true">
		var objUrl = parseUrlQuery(),
			add_url = '<?=(strpos($url, '?') !== false ? '&' : '?')?>FAST_VIEW=Y';

		if('clear_cache' in objUrl){
			if(objUrl.clear_cache == 'Y'){
				add_url += '&clear_cache=Y';
			}
		}

		if(!$('.fast_view_frame .form.sending').length){
			$('.fast_view_frame').addClass('loading-state');
		}

		BX.ajax({
			url: '<?=$url?>'+add_url,
			method: 'POST',
			data: BX.ajax.prepareData({'FAST_VIEW':'Y'}),
			dataType: 'html',
			processData: true,
			start: true,
			headers: [{'name': 'X-Requested-With', 'value': 'XMLHttpRequest'}],
			onfailure: function(data) {
				alert('Error connecting server');
			},
			onsuccess: function(html){
				var ob = BX.processHTML(html);
				BX.ajax.processScripts(ob.SCRIPT);

				// wait while not loaded template`s css
				var timer = setInterval(function(){
					if($('.fast_view_frame.popup .jqmClose.top-close').css('z-index') == 2){
						clearInterval(timer);
						BX('fast_view_item').innerHTML = ob.HTML;

						$('#fast_view_item').closest('.form').addClass('init');
						$('.fast_view_frame').removeClass('loading-state');

						initCountdown();
						setBasketItemsClasses();
						setCompareItemsClass();
						
						InitFancyBoxVideo();
						InitOwlSlider();

						$('#fast_view_item .animate-load').click(function(){
							$(this).parent().addClass('loadings');
						});

						$('#fast_view_item .counter_block input[type=text]').numeric({allow:"."});

						$('.navigation-wrapper-fast-view .fast-view-nav').removeClass('noAjax');

						$(window).scroll();
					}
				}, 100);
			}
		});

		$(document).on('click', '.jqmClose', function(e){
			e.preventDefault();
			$(this).closest('.jqmWindow').jqmHide();
		});
	</script>
	<div id="fast_view_item"></div>
<?endif;?>
