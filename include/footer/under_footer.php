<?@include_once('under_footer_custom.php');?>

<!-- marketnig popups -->
<?$APPLICATION->IncludeComponent(
	"aspro:marketing.popup.allcorp3", 
	".default", 
	array(),
	false, array('HIDE_ICONS' => 'Y')
);?>
<!-- /marketnig popups -->

<div class="bx_areas"><?CAllcorp3::ShowPageType('bottom_counter');?></div>
<?CAllcorp3::SetMeta();?>
<?CAllcorp3::ShowPageType('search_title_component');?>
<?CAllcorp3::ShowPageType('basket_component');?>
<?Aspro\Functions\CAsproAllcorp3::showBottomPanel();?>
<?CAllcorp3::AjaxAuth();?>
<div id="popup_iframe_wrapper"></div>