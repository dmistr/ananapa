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

class MainController extends ModuleUserController {
	public $roomsCountMin;
	public $roomsCountMax;
	public $floorCount;
	public $floorCountMin;
	public $floorCountMax;
	public $squareCount;
	public $squareCountMin;
	public $squareCountMax;
	public $price;
	public $priceSlider = array();
	public $metroStations;
	public $selectedStations;
	public $sApId;
    public $landSquare;
	public $term;
    public $bStart;
    public $bEnd;

    // with photo
	public $wp;
    // owner type
	public $ot;

	public function actionIndex(){
        $href = Yii::app()->getBaseUrl(true).'/'.Yii::app()->request->getPathInfo();
        Yii::app()->clientScript->registerLinkTag('canonical', null, $href);
        unset($href);

		$criteria = new CDbCriteria;
		$criteria->addCondition('active = ' . Apartment::STATUS_ACTIVE);
		if(param('useUserads')) {
			$criteria->addCondition('owner_active = ' . Apartment::STATUS_ACTIVE);
		}

		if(Yii::app()->request->isAjaxRequest) {
			$this->renderPartial('index', array(
				'criteria' => $criteria,
				'apCount' => null,
			), false, true);
		} else {
			$this->render('index', array(
				'criteria' => $criteria,
				'apCount' => null,
			));
		}
	}

	public function getExistRooms(){
		return Apartment::getExistsRooms();
	}

	public function actionMainsearch($rss = null){
        $countAjax = Yii::app()->request->getParam('countAjax');

        $href = Yii::app()->getBaseUrl(true).'/'.Yii::app()->request->getPathInfo();
        Yii::app()->clientScript->registerLinkTag('canonical', null, $href);
        unset($href);

		if(Yii::app()->request->getParam('currency')) {
			setCurrency();
			$this->redirect(array('mainsearch'));
		}

		$criteria = new CDbCriteria;
		$criteria->addCondition('t.active = ' . Apartment::STATUS_ACTIVE);
		$criteria->addCondition('t.deleted = 0');
		if(param('useUserads')) {
			$criteria->addCondition('t.owner_active = ' . Apartment::STATUS_ACTIVE);
		}

		$criteria->addInCondition('t.type', Apartment::availableApTypesIds());
		$criteria->addInCondition('t.price_type', array_keys(Apartment::getPriceArray(Apartment::PRICE_SALE, true)));

		$this->sApId = (int) Yii::app()->request->getParam('sApId');
		if ($this->sApId) {
			$criteria->addCondition('id = :sApId');
			$criteria->params[':sApId'] = $this->sApId;

			$apCount = Apartment::model()->count($criteria);
            if($countAjax && Yii::app()->request->isAjaxRequest){
                $this->echoAjaxCount($apCount);
            }

			if ($apCount) {
				$apartmentModel = Apartment::model()->findByPk($this->sApId);
				Yii::app()->controller->redirect($apartmentModel->getUrl());
				Yii::app()->end();
			}
		}

		// rooms
		if(issetModule('selecttoslider') && param('useRoomSlider') == 1) {
			$roomsMin = Yii::app()->request->getParam('room_min');
			$roomsMax = Yii::app()->request->getParam('room_max');

			if($roomsMin || $roomsMax) {
				$criteria->addCondition('num_of_rooms >= :roomsMin AND num_of_rooms <= :roomsMax');
				$criteria->params[':roomsMin'] = $roomsMin;
				$criteria->params[':roomsMax'] = $roomsMax;

				$this->roomsCountMin = $roomsMin;
				$this->roomsCountMax = $roomsMax;
			}
		} else {
			$rooms = Yii::app()->request->getParam('rooms');
			if($rooms) {
				if($rooms == 4) {
					$criteria->addCondition('num_of_rooms >= :rooms');
				} else {
					$criteria->addCondition('num_of_rooms = :rooms');
				}
				$criteria->params[':rooms'] = $rooms;

				$this->roomsCount = $rooms;
			}
		}

        $this->bStart = Yii::app()->request->getParam('b_start');
        $this->bEnd = Yii::app()->request->getParam('b_end');
        if($this->bStart){
            $dateStart = Yii::app()->dateFormatter->format('yyyy-MM-dd', CDateTimeParser::parse($this->bStart, Booking::getYiiDateFormat()));
            if($this->bEnd){
                $dateEnd = Yii::app()->dateFormatter->format('yyyy-MM-dd', CDateTimeParser::parse($this->bEnd, Booking::getYiiDateFormat()));
            }else{
                $dateEnd = $dateStart;
            }

            if($dateStart && $dateEnd){
                $criteria->addCondition('t.id NOT IN (
                    SELECT DISTINCT b.apartment_id
                        FROM {{booking_calendar}} AS b
                        WHERE b.date_start BETWEEN :b_start AND :b_end
                            OR :b_start BETWEEN b.date_start AND b.date_end
                )');
                $criteria->params['b_start'] = $dateStart;
                $criteria->params['b_end'] = $dateEnd;
            }
        }

		// floor
		if(issetModule('selecttoslider') && param('useFloorSlider') == 1) {
			$floorMin = Yii::app()->request->getParam('floor_min');
			$floorMax = Yii::app()->request->getParam('floor_max');

			if($floorMin || $floorMax) {
				$criteria->addCondition('floor >= :floorMin AND floor <= :floorMax');
				$criteria->params[':floorMin'] = $floorMin;
				$criteria->params[':floorMax'] = $floorMax;

				$this->floorCountMin = $floorMin;
				$this->floorCountMax = $floorMax;
			}
		} else {
			$floor = Yii::app()->request->getParam('floor');
			if($floor) {
				$criteria->addCondition('floor = :floor');
				$criteria->params[':floor'] = $floor;

				$this->floorCount = $floor;
			}
		}

		// square
		if(issetModule('selecttoslider') && param('useSquareSlider') == 1) {
			$squareMin = Yii::app()->request->getParam('square_min');
			$squareMax = Yii::app()->request->getParam('square_max');

			if($squareMin || $squareMax) {
				$criteria->addCondition('square >= :squareMin AND square <= :squareMax');
				$criteria->params[':squareMin'] = $squareMin;
				$criteria->params[':squareMax'] = $squareMax;

				$this->squareCountMin = $squareMin;
				$this->squareCountMax = $squareMax;
			}
		} else {
			$square = Yii::app()->request->getParam('square');
			if($square) {
				$criteria->addCondition('square <= :square');
				$criteria->params[':square'] = $square;

				$this->squareCount = $square;
			}
		}

		$landSquare = Yii::app()->request->getParam('land_square');
		if($landSquare) {
			$criteria->addCondition('land_square <= :land_square');
			$criteria->params[':land_square'] = $landSquare;

			$this->landSquare = $landSquare;
		}

		$this->selectedCity = Yii::app()->request->getParam('city', array());
		if(isset($this->selectedCity[0]) && $this->selectedCity[0] == 0){
			$this->selectedCity = array();
		}

		$this->wp = Yii::app()->request->getParam('wp');
		if($this->wp){
			$criteria->addCondition('count_img > 0');
		}

		$this->ot = Yii::app()->request->getParam('ot');
		if($this->ot){
			$criteria->join = 'INNER JOIN {{users}} AS u ON u.id = t.owner_id';
			//$criteria->select = 'u.type';
			if($this->ot == 1){
				$ownerTypes = array(
					User::TYPE_PRIVATE_PERSON,
					User::TYPE_ADMIN
				);
			}
			if($this->ot == 2){
				$ownerTypes = array(
					User::TYPE_AGENT,
					User::TYPE_AGENCY
				);
			}
			/*if($this->ot == 3){
				$ownerTypes = array(
					User::TYPE_AGENCY,
					User::TYPE_ADMIN
				);
			}*/

			if (isset($ownerTypes) && $ownerTypes)
				$criteria->compare('u.type', $ownerTypes);
		}

        if (issetModule('location')) {
			$country = Yii::app()->request->getParam('country');
			if($country) {
				$this->selectedCountry = $country;
				$criteria->compare('loc_country', $country);
			}

			$region = Yii::app()->request->getParam('region');
			if($region) {
				$this->selectedRegion = $region;
				$criteria->compare('loc_region', $region);
			}

            if($this->selectedCity) {
                $criteria->compare('t.loc_city', $this->selectedCity);
            }
		} else {
			if($this->selectedCity) {
				$criteria->compare('t.city_id', $this->selectedCity);
			}
		}

		$this->objType = Yii::app()->request->getParam('objType');
		if($this->objType) {
			$criteria->compare('obj_type_id', $this->objType);
		}

		// type
		$this->apType = Yii::app()->request->getParam('apType');
		if($this->apType) {
			$criteria->addCondition('price_type = :apType');
			$criteria->params[':apType'] = $this->apType;
		}

		$type = Yii::app()->request->getParam('type');
		if($type) {
			$criteria->addCondition('type = :type');
			$criteria->params[':type'] = $type;
		}

		// price
		if(issetModule('selecttoslider') && param('usePriceSlider') == 1) {
			$priceMin = Yii::app()->request->getParam("price_min");
			$priceMax = Yii::app()->request->getParam("price_max");

			if($priceMin || $priceMax) {
				$criteria->addCondition('(price >= :priceMin AND price <= :priceMax) OR (is_price_poa = 1)');

				if(issetModule('currency')){
					$criteria->params[':priceMin'] = floor(Currency::convertToDefault($priceMin));
					$criteria->params[':priceMax'] = ceil(Currency::convertToDefault($priceMax));
				} else {
					$criteria->params[':priceMin'] = $priceMin;
					$criteria->params[':priceMax'] = $priceMax;
				}

				$this->priceSlider["min"] = $priceMin;
				$this->priceSlider["max"] = $priceMax;
			}
		} else {
			$price = Yii::app()->request->getParam('price');

			if(issetModule('currency')){
				$priceDefault = ceil(Currency::convertToDefault($price));
			} else {
				$priceDefault = $price;
			}

			if($priceDefault) {
				$criteria->addCondition('(price <= :price) OR (is_price_poa = 1)');
				$criteria->params[':price'] = $priceDefault;

				$this->price = $price;
			}
		}

		// ключевые слова
		$term = Yii::app()->request->getParam('term');
		$doTermSearch = Yii::app()->request->getParam('do-term-search');

		if ($term && $doTermSearch == 1) {
			$term = utf8_substr($term, 0, 50);
			$term = cleanPostData($term);

			if ($term && utf8_strlen($term) >= $this->minLengthSearch) {
				$this->term = $term;

				$words = explode(' ', $term);
				foreach($words as $key=>$value){
					if(mb_strlen($value, "UTF-8") < $this->minLengthSearch ){
						unset($words[$key]);
					}
				}

				if (count($words) > 1) {
                    $cleanWords = array();
                    foreach($words as $word){
                        if(utf8_strlen($word) >= $this->minLengthSearch){
                            $cleanWords[] = $word;
                        }
                    }

					$searchString = '+'.implode('* +', $cleanWords).'* '; # https://dev.mysql.com/doc/refman/5.5/en/fulltext-boolean.html

					$sql = 'SELECT id
					FROM {{apartment}}
					WHERE MATCH
						(title_'.Yii::app()->language.', description_'.Yii::app()->language.', description_near_'.Yii::app()->language.', address_'.Yii::app()->language.')
						AGAINST ("'.$searchString.'" IN BOOLEAN MODE)';
				}
				else {
					$sql = 'SELECT id
					FROM {{apartment}}
					WHERE MATCH
						(title_'.Yii::app()->language.', description_'.Yii::app()->language.', description_near_'.Yii::app()->language.', address_'.Yii::app()->language.')
						AGAINST ("*'.$term.'*" IN BOOLEAN MODE)';
				}

				$resTerm = Yii::app()->db->createCommand($sql)->queryAll();
				if (is_array($resTerm) && count($resTerm) > 0) {
					$resTerm = CHtml::listData($resTerm, 'id', 'relevance');

					$criteria->addInCondition('t.id', array_keys($resTerm));
				}
				else {
					$criteria->addInCondition('t.id', array(0)); # ничего не найдено
				}
			}
		}

		// поиск объявлений владельца
		$this->userListingId = Yii::app()->request->getParam('userListingId');
		if($this->userListingId) {
			$criteria->addCondition('owner_id = :userListingId');
			$criteria->params[':userListingId'] = $this->userListingId;
		}

		$filterName = null;
		// Поиск по справочникам - клик в просмотре профиля анкеты
		if(param('useReferenceLinkInView')) {
			if(Yii::app()->request->getQuery('serviceId', false)) {
				$serviceId = Yii::app()->request->getQuery('serviceId', false);
				if($serviceId) {
					$serviceIdArray = explode('-', $serviceId);
					if(is_array($serviceIdArray) && count($serviceIdArray) > 0) {
						$value = (int)$serviceIdArray[0];

						$sql = 'SELECT DISTINCT apartment_id FROM {{apartment_reference}} WHERE reference_value_id = ' . $value;
						$apartmentIds = Yii::app()->db->cache(param('cachingTime', 1209600), Apartment::getDependency())->createCommand($sql)->queryColumn();
						//$apartmentIds = Yii::app()->db->createCommand($sql)->queryColumn();
						$criteria->addInCondition('t.id', $apartmentIds);


						Yii::app()->getModule('referencevalues');

						$sql = 'SELECT title_' . Yii::app()->language . ' FROM {{apartment_reference_values}} WHERE id = ' . $value;
						$filterName = Yii::app()->db->cache(param('cachingTime', 1209600), ReferenceValues::getDependency())->createCommand($sql)->queryScalar();

						if($filterName) {
							$filterName = CHtml::encode($filterName);
						}
					}
				}
			}
		}

		if(issetModule('formeditor')){
			$newFieldsAll = FormDesigner::getNewFields();
			foreach($newFieldsAll as $field){
				$value = CHtml::encode(Yii::app()->request->getParam($field->field));
				if(!$value){
					continue;
				}
				$fieldString = $field->field;

				$this->newFields[$fieldString] = $value;

				switch($field->compare_type){
					case FormDesigner::COMPARE_EQUAL:
						$criteria->compare($fieldString, $value);
						break;

					case FormDesigner::COMPARE_LIKE:
						$criteria->compare($fieldString, $value, true);
						break;

					case FormDesigner::COMPARE_FROM:
						$value = intval($value);
						$criteria->compare($fieldString, ">={$value}");
						break;

					case FormDesigner::COMPARE_TO:
						$value = intval($value);
						$criteria->compare($fieldString, "<={$value}");
						break;
				}
			}
		}

		if($rss && issetModule('rss')) {
			$this->widget('application.modules.rss.components.RssWidget', array(
				'criteria' => $criteria,
			));
		}

		// find count
		$apCount = Apartment::model()->count($criteria);

        if($countAjax && Yii::app()->request->isAjaxRequest){
            $this->echoAjaxCount($apCount);
        }

        $searchParams = $_GET;
        if(isset($searchParams['is_ajax'])){
            unset($searchParams['is_ajax']);
        }
        Yii::app()->user->setState('searchUrl', Yii::app()->createUrl('/search', $searchParams));
        unset($searchParams);

		if(Yii::app()->request->isAjaxRequest) {
//			$modeListShow = User::getModeListShow();
//			if ($modeListShow == 'table') {
//				# нужны скрипты и стили, поэтому processOutput установлен в true только для table
//				$this->renderPartial('index', array(
//					'criteria' => $criteria,
//					'apCount' => $apCount,
//					'filterName' => $filterName,
//				), false, true);
//			}
//			else {
				$this->renderPartial('index', array(
					'criteria' => $criteria,
					'apCount' => $apCount,
					'filterName' => $filterName,
				));
//			}
		} else {
			$this->render('index', array(
				'criteria' => $criteria,
				'apCount' => $apCount,
				'filterName' => $filterName,
			));
		}
	}

    public function echoAjaxCount($apCount){
//        if($apCount > 0){
//            $buttonLabel = Yii::t('common', '{n} listings', array($apCount, '{n}' => $apCount));
//        } else {
//            $buttonLabel = tc('Search');
//        }
        echo CJSON::encode(array(
            'count' => $apCount,
            'string' => Yii::t('common', '{n} listings', array($apCount, '{n}' => $apCount)),
        ));
        Yii::app()->end();
    }

    public function actionLoadForm(){
        if(!Yii::app()->request->isAjaxRequest){
            throw404();
        }

        $this->objType = Yii::app()->request->getParam('obj_type_id');
        $isInner = Yii::app()->request->getParam('is_inner');

        $roomsMin = Yii::app()->request->getParam('room_min');
        $roomsMax = Yii::app()->request->getParam('room_max');
        if($roomsMin || $roomsMax) {
            $this->roomsCountMin = $roomsMin;
            $this->roomsCountMax = $roomsMax;
        }

        $this->sApId = (int) Yii::app()->request->getParam('sApId');

        $this->bStart = Yii::app()->request->getParam('b_start');
        $this->bEnd = Yii::app()->request->getParam('b_end');

        $floorMin = Yii::app()->request->getParam('floor_min');
        $floorMax = Yii::app()->request->getParam('floor_max');
        if($floorMin || $floorMax) {
            $this->floorCountMin = $floorMin;
            $this->floorCountMax = $floorMax;
        }

        $floor = Yii::app()->request->getParam('floor');
        if($floor) {
            $this->floorCount = $floor;
        }

        $this->wp = Yii::app()->request->getParam('wp');
        $this->ot = Yii::app()->request->getParam('ot');

        if(issetModule('selecttoslider') && param('useSquareSlider') == 1) {
            $squareMin = Yii::app()->request->getParam('square_min');
            $squareMax = Yii::app()->request->getParam('square_max');

            if($squareMin || $squareMax) {
                $this->squareCountMin = $squareMin;
                $this->squareCountMax = $squareMax;
            }
        } else {
            $square = Yii::app()->request->getParam('square');
            if($square) {
                $this->squareCount = $square;
            }
        }

        $this->selectedCity = Yii::app()->request->getParam('city', array());
        if(isset($this->selectedCity[0]) && $this->selectedCity[0] == 0){
            $this->selectedCity = array();
        }

        if (issetModule('location')) {
            $country = Yii::app()->request->getParam('country');
            if($country) {
                $this->selectedCountry = $country;
            }

            $region = Yii::app()->request->getParam('region');
            if($region) {
                $this->selectedRegion = $region;
            }
        }

        $this->objType = Yii::app()->request->getParam('objType');
        $this->apType = Yii::app()->request->getParam('apType');


/*        if(issetModule('selecttoslider') && param('usePriceSlider') == 1) {
            $priceMin = Yii::app()->request->getParam("price_min");
            $priceMax = Yii::app()->request->getParam("price_max");

            if($priceMin || $priceMax) {
                $this->priceSlider["min"] = $priceMin;
                $this->priceSlider["max"] = $priceMax;
            }
        } else {
            $price = Yii::app()->request->getParam('price');

            if(issetModule('currency')){
                $priceDefault = ceil(Currency::convertToDefault($price));
            } else {
                $priceDefault = $price;
            }

            if($priceDefault) {
                $this->price = $price;
            }
        }*/

        if(issetModule('formeditor')){
            $newFieldsAll = FormDesigner::getNewFields();
            foreach($newFieldsAll as $field){
                $value = CHtml::encode(Yii::app()->request->getParam($field->field));
                if(!$value){
                    continue;
                }
                $fieldString = $field->field;
                $this->newFields[$fieldString] = $value;
            }
        }

        $compact = Yii::app()->request->getParam('compact', 0);

        HAjax::jsonOk('', array(
            'html' => $this->renderPartial('//site/_search_form', array('isInner' => $isInner, 'compact' => $compact), true),
            'sliderRangeFields' => SearchForm::getSliderRangeFields(),
            'cityField' => SearchForm::getCityField(),
            'countFiled' => SearchForm::getCountFiled(),
            'compact' => $compact,
        ));
    }

}