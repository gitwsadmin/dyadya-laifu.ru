<?
namespace Aspro\Allcorp3\Functions;

use Bitrix\Main\Application;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\IO\File;
use Bitrix\Main\IO\Directory;
use Bitrix\Main\Type\Collection;
use Bitrix\Main\Config\Option;
use Bitrix\Iblock;

Loc::loadMessages(__FILE__);

if(!class_exists('CSKUTemplate'))
{
	/**
	 * Class for show sku items
	 */
	class CSKUTemplate{
		/**
		 * @var string MODULE_ID solution
		 */
		const MODULE_ID = \CAllcorp3::moduleID;

		public $props = [];
		public $items = [];
		public $linkedProp = [];
		public $linkCodeProp = '';
		
		/**
		 * Show html sku tree props
		 * @param array $arProps 
		 */
		public static function showSkuPropsHtml(array $arProps = [])
		{
			foreach ($arProps as $code => $arProp) {
				\Aspro\Functions\CAsproAllcorp3::showBlockHtml([
					'TYPE' => 'SKU',
					'FILE' => 'sku/properties_in_'.$arProp['SHOW_MODE'].'.php',
					'PARAMS' => $arProp
				]);
			}
		}
	}
}?>