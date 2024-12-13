<? 
namespace Aspro\Allcorp3\Functions;

use CAllcorp3 as Solution;

class Extensions {
    public static function register(){
		$arConfig = [
			'filter_block' => [
				'css' => SITE_TEMPLATE_PATH.'/css/filter_block.css',
			],
			'header_opacity' => [
				'css' => SITE_TEMPLATE_PATH.'/css/header_opacity.css',
			],
			'item_action' => [
				'js' => SITE_TEMPLATE_PATH.'/js/item-action.js',
			],
			'images_detail' => [
				'css' => SITE_TEMPLATE_PATH.'/css/images_detail.css',
			],
			'link_scroll' => [
				'js' => SITE_TEMPLATE_PATH.'/js/sectionLinkScroll.js',
			],
			'logo_depend_banners' => [
				'js' =>  SITE_TEMPLATE_PATH.'/js/logo_depend_banners.js',
			],
			'logo' => [
				'js' => SITE_TEMPLATE_PATH.'/js/logo.js',
			],
			'notice' => [
				'js' => '/bitrix/js/'.Solution::moduleID.'/notice.js',
				'css' => '/bitrix/css/'.Solution::moduleID.'/notice.css',
				'lang' => '/bitrix/modules/'.Solution::moduleID.'/lang/'.LANGUAGE_ID.'/lib/notice.php',
			],
			'swiper' => [
				'js' => SITE_TEMPLATE_PATH.'/vendor/js/carousel/swiper/swiper-bundle.min.js',
				'css' => [
					SITE_TEMPLATE_PATH.'/vendor/css/carousel/swiper/swiper-bundle.min.css',
					SITE_TEMPLATE_PATH.'/css/slider.swiper.min.css'
				],
				'rel' => [Solution::partnerName.'_swiper_init'],
			],
			'swiper_init' => [
				'js' => SITE_TEMPLATE_PATH.'/js/slider.swiper.min.js',
			],
			'tariffitem' => [
				'js' => '/bitrix/js/'.Solution::moduleID.'/property/tariffitem.js',
				'css' => '/bitrix/css/'.Solution::moduleID.'/property/tariffitem.css',
				'lang' => '/bitrix/modules/'.Solution::moduleID.'/lang/'.LANGUAGE_ID.'/property/tariffitem.php',
			],
		];

		foreach ($arConfig as $ext => $arExt) {
			\CJSCore::RegisterExt(Solution::partnerName.'_'.$ext, array_merge($arExt, ['skip_core' => true]));
		}
	}

	public static function init($arExtensions){
		$arExtensions = is_array($arExtensions) ? $arExtensions : (array)$arExtensions;

		if($arExtensions){
            
			$arExtensions = array_map(function($ext){
				return strpos($ext, Solution::partnerName) !== false ? $ext : Solution::partnerName.'_'.$ext;
			}, $arExtensions);

			\CJSCore::Init($arExtensions);
		}
	}
}
