<?
namespace Aspro\Allcorp3\Components;

use Bitrix\Main\Loader,
    Bitrix\Main\Localization\Loc,
    Bitrix\Main\Config\Option,
    Bitrix\Main\SystemException,
    CAllcorp3 as Solution,
    Aspro\Allcorp3 as SolutionLibs;

if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
Loc::loadMessages(__FILE__);

class Eyed extends \CBitrixComponent {
    public function onPrepareComponentParams($arParams){
    	if(isset($arParams['CUSTOM_SITE_ID'])){
			$this->setSiteId($arParams['CUSTOM_SITE_ID']);
		}

		if(isset($arParams['CUSTOM_LANGUAGE_ID'])){
			$this->setLanguageId($arParams['CUSTOM_LANGUAGE_ID']);
		}

        return $arParams;
    }

    public function executeComponent(){
        $isAjax = $this->request->isPost() && $this->request['mode'] === 'ajax' && $this->request['action'] === 'getEyed';

        $this->addBodyClasses();

        if($isAjax){
			$GLOBALS['APPLICATION']->RestartBuffer();
		}

    	try{
    		$this->includeModules();

            $signer = new \Bitrix\Main\Security\Sign\Signer;
            $signedParams = $signer->sign(base64_encode(serialize($this->arParams)), str_replace(':', '.', $this->getName()));

            $this->arResult = array(
                'ENABLED' => SolutionLibs\Eyed::isEnabled(),
                'ACTIVE' => SolutionLibs\Eyed::isActive(),
                'IS_AJAX' => $isAjax,
                'SIGNED_PARAMS' => $signedParams,
                'COOKIE'=> array(
                    'ACTIVE' => SolutionLibs\Eyed::cookieActive,
                    'OPTIONS' => SolutionLibs\Eyed::cookieOptions,
                ),
                'OPTIONS' => SolutionLibs\Eyed::getOptions(),
            );

            if($isAjax){
                $GLOBALS['APPLICATION']->RestartBuffer();
            }

	        $this->includeComponentTemplate();
        }
        catch(SystemException $e){
            // echo $e->getMessage();
        }

        return $this->arResult;
    }

    public function addBodyClasses(){
        if(SolutionLibs\Eyed::isActive()){
            \Bitrix\Main\Data\StaticHtmlCache::getInstance()->markNonCacheable();

            $GLOBALS['bodyDopClass'] = ' eyed';
            $arOptions = SolutionLibs\Eyed::getOptions();

            // font size
            switch($arOptions['FONT-SIZE']){
                case 16:
                    $GLOBALS['bodyDopClass'] .= ' eyed--font-size--16';
                    break;
                case 20:
                    $GLOBALS['bodyDopClass'] .= ' eyed--font-size--20';
                    break;
                case 24:
                    $GLOBALS['bodyDopClass'] .= ' eyed--font-size--24';
                    break;
                default:
                    $GLOBALS['bodyDopClass'] .= ' eyed--font-size--16';
            }

            // color scheme
            switch($arOptions['COLOR-SCHEME']){
                case 'black':
                    $GLOBALS['bodyDopClass'] .= ' eyed--color-scheme--black';
                    break;
                case 'yellow':
                    $GLOBALS['bodyDopClass'] .= ' eyed--color-scheme--yellow';
                    break;
                case 'blue':
                    $GLOBALS['bodyDopClass'] .= ' eyed--color-scheme--blue';
                    break;
                default:
                    $GLOBALS['bodyDopClass'] .= ' eyed--color-scheme--black';
            }

            // images
            switch($arOptions['IMAGES']){
                case 0:
                    $GLOBALS['bodyDopClass'] .= ' eyed--images--off';
                    break;
                case 1:
                    $GLOBALS['bodyDopClass'] .= ' eyed--images--on';
                    break;
                default:
                    $GLOBALS['bodyDopClass'] .= ' eyed--images--on';
            }

            // speaker
            switch($arOptions['SPEAKER']){
                case 0:
                    $GLOBALS['bodyDopClass'] .= ' eyed--speaker--off';
                    break;
                case 1:
                    $GLOBALS['bodyDopClass'] .= ' eyed--speaker--on';
                    break;
                default:
                    $GLOBALS['bodyDopClass'] .= ' eyed--speaker--off';
            }
        }

        return $class;
    }

    protected function includeModules(){
        if(!Loader::includeModule(Solution::moduleID)){
            throw new SystemException(Loc::getMessage('EB_C_ERROR_MODULE_NOT_INSTALLED'));
        }
    }
}