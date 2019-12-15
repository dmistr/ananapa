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


class HApartment {
    public static function saveOther(Apartment $ad){
        if(ApartmentVideo::saveVideo($ad)){
            $ad->panoramaFile = CUploadedFile::getInstance($ad, 'panoramaFile');
            $ad->scenario = 'panorama';
            if(!$ad->validate()) {
                return false;
            }
        }

        $city = "";
        if (issetModule('location')) {
            $city .= $ad->locCountry ? $ad->locCountry->getStrByLang('name') : "";
            $city .= ($city && $ad->locCity) ? ", " : "";
            $city .= $ad->locCity ? $ad->locCity->getStrByLang('name') : "";
        } else
            $city = $ad->city ? $ad->city->getStrByLang('name') : "";

        // data
        if(($ad->address && $city) && (param('useGoogleMap', 1) || param('useYandexMap', 1) || param('useOSMMap', 1))){
            if (!$ad->lat && !$ad->lng) { # уже есть
                $coords = Geocoding::getCoordsByAddress($ad->address, $city);

                if(isset($coords['lat']) && isset($coords['lng'])){
                    $ad->lat = $coords['lat'];
                    $ad->lng = $coords['lng'];
                }
            }
        }

        return true;
    }

    public static function getRequestType(){
        $type = Yii::app()->getRequest()->getQuery('type');
        $existType = array_keys(Apartment::getTypesArray());
        if(!in_array($type, $existType)){
            $type = Apartment::TYPE_DEFAULT;
        }
        return $type;
    }

    /** Сохраняем данные выбранных справочников
     * @return array
     */
    public static function getCategoriesForUpdate(Apartment $ad)
    {
        if (isset($_POST['category']) && is_array($_POST['category'])) {
            $ad->references = Apartment::getCategories(null, $ad->type);
            foreach ($_POST['category'] as $cat => $categoryArray) {
                foreach ($categoryArray as $key => $value) {
                    $ad->references[$cat]['values'][$key]['selected'] = true;
                }
            }
        } else {
            $ad->references = Apartment::getCategories($ad->id, $ad->type);
        }

        return $ad->references;
    }


    public static function getModeShowList()
    {
        return array(
            'block' => tt('Display block', 'apartments'),
            'table' => tt('Display table', 'apartments'),
            'map' => tt('Display with a map', 'apartments'),
        );
    }


    public static function getPeriodActivityList()
    {
        // key for strtotime - http://php.net/manual/ru/function.strtotime.php
        return array(
            '+1 week' => tt('a week', 'apartments'),
            '+1 month' => tt('a month', 'apartments'),
            '+3 month' => tt('3 months', 'apartments'),
            '+6 month' => tt('6 months', 'apartments'),
            '+1 year' => tt('a year', 'apartments'),
            'always' => tt('always', 'apartments'),
        );
    }
}