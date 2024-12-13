<?
namespace Aspro\Allcorp3;
use Aspro\Allcorp3\Functions\Extensions;

class Notice {
    const ON_AUTH_SESSION_FLAG = 'ASPRO_ALLCORP3_SUCCESSFUL_AUTHORIZATION';

    public static function showOnAuth() {
        if ($_SESSION[self::ON_AUTH_SESSION_FLAG] == 'Y') {
            if (is_object($GLOBALS['USER'])) {
                $arUser = [
                    'id' => $GLOBALS['USER']->GetID(),
                    'login' => $GLOBALS['USER']->GetLogin(),
                    'fullname' => $GLOBALS['USER']->GetFullName(),
                ];

                Extensions::init('notice');
                ?>
                <script>
                BX.ready(function(){
                    JNoticeSurface.get().onAuth(<?=\CUtil::PhpToJSObject($arUser, false)?>);
                });
                </script>
                <?
            }

            unset($_SESSION[self::ON_AUTH_SESSION_FLAG]);
        }
    }

    public static function setAuthFlag() {
        $_SESSION[self::ON_AUTH_SESSION_FLAG] = 'Y';
    }
}