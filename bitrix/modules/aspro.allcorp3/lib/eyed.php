<?
namespace Aspro\Allcorp3;

use	CAllcorp3 as Solution;

class Eyed {
	const cookieActive = 'ALLCORP3_EYE_VERSION_VALUE';
	const cookieOptions = 'ALLCORP3_EYE_VERSION_OPTIONS';

	public static function isEnabled(){
		static $result;

		if (!isset($result)) {
			$headerType = Solution::GetFrontParametrValue('HEADER_TYPE', SITE_ID);
			$bShowHeaderEyed = Solution::GetFrontParametrValue('HEADER_TOGGLE_EYED_'.$headerType, SITE_ID) === 'Y';

			if (!$bShowHeaderEyed) {
				$headerFixedType = Solution::GetFrontParametrValue('HEADER_FIXED', SITE_ID);
				$bShowHeaderFixedEyed = Solution::GetFrontParametrValue('HEADER_FIXED_TOGGLE_EYED_'.$headerFixedType, SITE_ID) === 'Y';

				if (!$bShowHeaderFixedEyed) {
					$footerType = Solution::GetFrontParametrValue('FOOTER_TYPE', SITE_ID);
					$bShowFooterEyed = Solution::GetFrontParametrValue('FOOTER_TOGGLE_EYED_'.$footerType, SITE_ID) === 'Y';

					$result = $bShowFooterEyed;
				}
			}

			$result = true;
		}

		return $result;
	}

	public static function isActive(){
		return
			isset($_COOKIE[self::cookieActive]) &&
			$_COOKIE[self::cookieActive] === 'Y' &&
			self::isEnabled();
	}

	public static function getCookie(){
        return (isset($_COOKIE[self::cookieOptions]) && $_COOKIE[self::cookieOptions]) ? $_COOKIE[self::cookieOptions] : '{}';
    }

    public static function getOptions(){
		$cookieOptions = self::getCookie();
		$arCookieOptions = json_decode($cookieOptions, true);
		$arCookieOptions = is_array($arCookieOptions) ? $arCookieOptions : array();

		$arOptions = array(
			'FONT-SIZE' => in_array($arCookieOptions['FONT-SIZE'], array(16, 20, 24)) ? $arCookieOptions['FONT-SIZE'] : 16,
			'COLOR-SCHEME' => in_array($arCookieOptions['COLOR-SCHEME'], array('black', 'yellow', 'blue')) ? $arCookieOptions['COLOR-SCHEME'] : 'black',
			'IMAGES' => strlen($arCookieOptions['IMAGES']) ? ($arCookieOptions['IMAGES'] ? 1 : 0) : 1,
			'SPEAKER' => strlen($arCookieOptions['SPEAKER']) ? ($arCookieOptions['SPEAKER'] ? 1 : 0) : 1,
		);

		return $arOptions;
    }
}