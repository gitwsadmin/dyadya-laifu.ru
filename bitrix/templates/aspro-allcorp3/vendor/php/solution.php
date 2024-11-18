<?
namespace {
    if (!defined('VENDOR_PARTNER_NAME')) {
        /** @const Aspro partner name */
        define('VENDOR_PARTNER_NAME', 'aspro');
    }
    
    if (!defined('VENDOR_SOLUTION_NAME')) {
        /** @const Aspro solution name */
        define('VENDOR_SOLUTION_NAME', 'allcorp3');
    }
    
    if (!defined('VENDOR_MODULE_ID')) {
        /** @const Aspro module id */
        define('VENDOR_MODULE_ID', 'aspro.allcorp3');
    }
    
    foreach ([
        'CAllcorp3' => 'TSolution',
        'CAllcorp3Events' => 'TSolution\Events',
        'CAllcorp3Cache' => 'TSolution\Cache',
        'CAllcorp3Regionality' => 'TSolution\Regionality',
        'CAllcorp3Condition' => 'TSolution\Condition',
        'CInstargramAllcorp3' => 'TSolution\Instagram',
        'CVKAllcorp3' => 'TSolution\VK',
        'Aspro\Functions\CAsproAllcorp3' => 'TSolution\Functions',
        'Aspro\Functions\CAsproAllcorp3ReCaptcha' => 'TSolution\ReCaptcha',
        'Aspro\Allcorp3\Functions\Extensions' => 'TSolution\Extensions',
        'Aspro\Allcorp3\Functions\CSKU' => 'TSolution\SKU',
        'Aspro\Allcorp3\Property\CustomFilter' => 'TSolution\Property\CustomFilter',
        'Aspro\Allcorp3\Notice' => 'TSolution\Notice',
        'Aspro\Allcorp3\Banner\Transparency' => 'TSolution\Banner\Transparency',
        'Aspro\Allcorp3\Video\Iframe' => 'TSolution\Video\Iframe',
    ] as $original => $alias) {
        if (!class_exists($alias)) {
            class_alias($original, $alias);
        }    
    }

    // these alias declarations for IDE only
    if (false) {
        class TSolution extends CAllcorp3 {}
    }
}

// these alias declarations for IDE only
namespace TSolution {
    if (false) {
        class Events extends \CAllcorp3Events {}

        class Cache extends \CAllcorp3Cache {}

        class Regionality extends \CAllcorp3Regionality {}

        class Condition extends \CAllcorp3Condition {}

        class Instagram extends \CInstargramAllcorp3 {}

        class Functions extends \Aspro\Functions\CAsproAllcorp3 {}

        class Extensions extends \Aspro\Allcorp3\Functions\Extensions {}

        class SKU extends \Aspro\Allcorp3\Functions\CSKU {}

        class Notice extends \Aspro\Allcorp3\Notice {}
    }
}

namespace TSolution\Banner {
    if (false) {
        class Transparency extends \Aspro\Allcorp3\Banner\Transparency {}
    }
}

namespace TSolution\Video {
    if (false) {
        class Iframe extends \Aspro\Allcorp3\Video\Iframe {}
    }
}