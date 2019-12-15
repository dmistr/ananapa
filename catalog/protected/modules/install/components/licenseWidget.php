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

class licenseWidget extends CWidget
{
    public $autoOpen = true;

    public function run() {

        $license = @file_get_contents('http://re.monoray.ru/license.php?host='.$_SERVER['HTTP_HOST']);

        if ($license){
            echo $license;
        }else{
            $this->render('application.modules.install.views.license');
        }
    }
}