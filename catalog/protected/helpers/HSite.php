<?php
	/* * ********************************************************************************************
	 *								Open Real Estate
	 *								----------------
	 * 	version				:	V1.12.0
	 * 	copyright			:	(c) 2015 Monoray
	 * 							http://monoray.net
	 *							http://monoray.ru
	 *
	 * 	website				:	http://open-real-estate.info/en
	 *
	 * 	contact us			:	http://open-real-estate.info/en/contact-us
	 *
	 * 	license:			:	http://open-real-estate.info/en/license
	 * 							http://open-real-estate.info/ru/license
	 *
	 * This file is part of Open Real Estate
	 *
	 * ********************************************************************************************* */

class HSite {
    public static function registerMainAssets(){
        $cs = Yii::app()->clientScript;
        //$cs->coreScriptPosition = CClientScript::POS_BEGIN;
        $baseThemeUrl = Yii::app()->theme->baseUrl;

        $cs->registerCoreScript('jquery');
        $cs->registerCoreScript('jquery.ui');
        $cs->registerCoreScript('rating');
        $cs->registerCssFile($baseThemeUrl . '/css/ui/jquery-ui.multiselect.css');
        $cs->registerCssFile($baseThemeUrl . '/css/redmond/jquery-ui-1.7.1.custom.css');
        $cs->registerCssFile($baseThemeUrl . '/css/ui.slider.extras.css');
        $cs->registerScriptFile($baseThemeUrl . '/js/jquery.multiselect.min.js', CClientScript::POS_BEGIN);
        $cs->registerCssFile($baseThemeUrl . '/css/ui/jquery-ui.multiselect.css');
        $cs->registerScriptFile($baseThemeUrl . '/js/jquery.dropdownPlain.js', CClientScript::POS_BEGIN);
        $cs->registerScriptFile($baseThemeUrl . '/js/common.js', CClientScript::POS_BEGIN);
        $cs->registerScriptFile($baseThemeUrl . '/js/habra_alert.js', CClientScript::POS_END);
        $cs->registerScriptFile($baseThemeUrl . '/js/jquery.cookie.js', CClientScript::POS_END);
        $cs->registerScriptFile($baseThemeUrl . '/js/scrollto.js', CClientScript::POS_END);
        $cs->registerCssFile($baseThemeUrl . '/css/form.css', 'screen, projection');

        // superfish menu
        $cs->registerCssFile($baseThemeUrl . '/js/superfish/css/superfish.css', 'screen');
        $cs->registerScriptFile($baseThemeUrl . '/js/superfish/js/superfish.js', CClientScript::POS_END);

        if(Yii::app()->theme->name == 'atlas'){
            $cs->registerCssFile($baseThemeUrl . '/css/rating/rating.css');
            $colorTheme = Themes::getParam('color_theme');
            if($colorTheme){
                $cs->registerCssFile($baseThemeUrl . '/css/colors/'.$colorTheme);
            }
            $cs->registerScriptFile($baseThemeUrl . '/js/jquery.easing.1.3.js', CClientScript::POS_BEGIN);
            $cs->registerScript('initizlize-superfish-menu', '
			$("#sf-menu-id").superfish( {hoverClass: "sfHover", delay: 100, animationOut: {opacity:"hide"}, animation: {opacity:"show"}, cssArrows: false, dropShadows: false, speed: "fast", speedOut: 1 });
		', CClientScript::POS_READY);
        }

        //deb(Yii::app()->theme->name); exit;
        if(Yii::app()->theme->name == 'classic'){
            $cs->registerCssFile($cs->getCoreScriptUrl().'/rating/jquery.rating.css');
            $cs->registerCssFile($baseThemeUrl.'/js/superfish/css/superfish-vertical.css', 'screen');
            $cs->registerScriptFile($baseThemeUrl.'/js/superfish/js/hoverIntent.js', CClientScript::POS_HEAD);

            $cs->registerScript('initizlize-superfish-menu', '
			$("#sf-menu-id").superfish( {delay: 100, autoArrows: false, dropShadows: false, pathClass: "overideThisToUse", speed: "fast" });
		', CClientScript::POS_READY);
        }
    }
}