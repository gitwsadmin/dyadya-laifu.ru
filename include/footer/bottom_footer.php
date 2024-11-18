<?$APPLICATION->IncludeComponent("aspro:theme.allcorp3", "", array('SHOW_TEMPLATE' => 'Y'), false, ['HIDE_ICONS' => 'Y']);?>
<?\Aspro\Allcorp3\Functions\Extensions::init('logo_depend_banners');?>
<script>
if (typeof logo_depend_banners === 'function') {
    logo_depend_banners();
}
</script>
<?\Aspro\Allcorp3\Notice::showOnAuth();?>    
<?@include_once('bottom_footer_custom.php');?>