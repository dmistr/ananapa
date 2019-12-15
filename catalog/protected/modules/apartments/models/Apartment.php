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

class Apartment extends ParentModel {
	public $title;
	public $isAjaxLoadOnUpdate = false;

	public $metroStations;
	public $ownerEmail;
	public $ownerUsername;
	public $searchPaidService;
	private $_stationsTitle = 0;
	public $in_currency;

	const TYPE_RENT = 1;
	const TYPE_SALE = 2;
	const TYPE_RENTING = 3;
	const TYPE_BUY = 4;
	const TYPE_CHANGE = 5;
	const TYPE_DEFAULT = 1;

	private static $_type_arr;
	private static $_apartment_arr;
	private static $_apartment_del_arr;

	const PRICE_SALE = 1;
	const PRICE_PER_HOUR = 2;
	const PRICE_PER_DAY = 3;
	const PRICE_PER_WEEK = 4;
	const PRICE_PER_MONTH = 5;
	const PRICE_RENTING = 6;
	const PRICE_BUY = 7;
	const PRICE_CHANGE = 8;

	const STATUS_INACTIVE = 0;
	const STATUS_ACTIVE = 1;
	const STATUS_MODERATION = 2;
	const STATUS_DRAFT = 3;

	private static $_price_arr;

	public $videoUpload;
	public $video_file;
	public $video_html;

	public $panoramaFile;
	public $references;

	public $period_activity;

	public $oldStatus;
	private $fullTitleApartmentForChangeOwner;

	public static function model($className = __CLASS__) {
		return parent::model($className);
	}

	public function tableName() {
		return '{{apartment}}';
	}

	public function behaviors() {
		return array(
			'ERememberFiltersBehavior' => array(
				'class' => 'application.components.behaviors.ERememberFiltersBehavior',
				'defaults' => array(),
				'defaultStickOnClear' => false
			),
			'AutoTimestampBehavior' => array(
				'class' => 'zii.behaviors.CTimestampBehavior',
				'createAttribute' => 'date_created',
				'updateAttribute' => 'date_updated',
			),
		);
	}

	public function rules() {
		$video = ApartmentVideo::model();
		$panorama = ApartmentPanorama::model();
		$rules = array(
			array('price', 'priceValidator', 'except' => 'video_file, video_html, panorama'),
			array('title', 'i18nRequired', 'except' => 'video_file, video_html, panorama'),
			array('price, price_to, floor, floor_total, window_to, type, price_type, obj_type_id, city_id, activity_always, count_img', 'numerical', 'integerOnly' => true),
			array('square, land_square', 'numerical'),
			array('type', 'numerical', 'min' => 1),
			array('price_to', 'priceToValidator'),
			array('berths', 'length', 'max' => 255),
			array('title', 'i18nLength', 'max' => 255),
			array('lat, lng', 'length', 'max' => 25),
			array('phone', 'length', 'max' => 45),
			array('id', 'safe', 'on' => 'search'),
			array('floor', 'myFloorValidator'),
			array('is_price_poa', 'boolean'),
			array('in_currency, owner_active, num_of_rooms, is_special_offer, is_free_to, active, metroStations, note, period_activity', 'safe'),
			array($this->getI18nFieldSafe(), 'safe'),
			array('city_id, phone, owner_active, active, type, obj_type_id, ownerEmail, ownerUsername, searchPaidService, deleted', 'safe', 'on' => 'search'),

			array('video_html', 'checkHtmlCode' /*, 'on' => 'video_html'*/),
			array(
				'video_file', 'file',
				'types' => $video->supportExt,
				'maxSize' => $video->fileMaxSize,
				'allowEmpty' => true,
				//'on' => 'video_file',
			),
			array(
				'panoramaFile', 'file',
				'types' => $panorama->supportedExt,
				'maxSize' => $panorama->maxSize,
				'tooLarge' => Yii::t('module_apartments', 'The file was larger than {size}MB. Please upload a smaller file.', array('{size}' => $panorama->maxSizeMb)),
				'allowEmpty' => true,
				//'on' => 'panorama',
			),
		);

		if (issetModule('formeditor')) {

			$addRules = HFormEditor::getRulesForModel();
			$rules = CMap::mergeArray($rules, $addRules);
		}

		if (issetModule('location')) {
			$rules[] = array('loc_city, loc_region, loc_country', 'safe', 'on' => 'search');
			$rules[] = array('loc_city, loc_region, loc_country', 'numerical', 'integerOnly' => true);
		}

		return $rules;
	}

	public function requiredAdvanced($attribute, $params = null) {
		$isUpdate = Yii::app()->request->getPost('is_update');
		if (!$isUpdate && $this->canShowInForm($attribute) && $this->isEmpty($this->{$attribute})) {
			$this->addError($attribute, Yii::t('yii', '{attribute} cannot be blank.',
				array('{attribute}' => $this->getAttributeLabel($attribute))));
		}
	}

	public function checkHtmlCode() {
		if ($this->video_html) {
			$apartmentVideoModel = new ApartmentVideo;
			$return = $apartmentVideoModel->parseVideoHTML($this->video_html);

			if (is_array($return) && isset($return[1])) {
				if ($return[1] == 'error') {
					$this->addError('video_html', tt('incorrect_youtube_code', 'apartments'));
				}
			}
		}
	}

	public function priceValidator($attribute, $params) {
		if (!$this->is_price_poa) {
			if (!$this->price && !$this->price_to) {
				$this->addError('price', Yii::t('common', '{label} cannot be blank.', array('{label}' => $this->getAttributeLabel($attribute))));
			}
		}
	}

	public function priceToValidator()
	{
		if ($this->price_to && $this->price) {
			if ($this->price_to < $this->price) {
				$this->addError('price', tt('priceToValidatorText', 'apartments'));
			}
		}
	}

	public function i18nFields() {
		return array(
			'title' => 'text not null',
			'address' => 'varchar(255) not null',
			'description' => 'text not null',
			'description_near' => 'text not null',
			'exchange_to' => 'text not null'
		);
	}

	public function seoFields() {
		return array(
			'fieldTitle' => 'title',
			'fieldDescription' => 'description'
		);
	}

	public function currencyFields() {
		return array('price', 'price_to');
	}

	public function myFloorValidator($attribute, $params) {
		if ($this->floor && $this->floor_total) {
			if ($this->floor > $this->floor_total)
				$this->addError('floor', tt('validateFloorMoreTotal', 'apartments'));
		}
	}

	public function relations() {
		Yii::import('application.modules.apartmentObjType.models.ApartmentObjType');
		Yii::import('application.modules.apartmentCity.models.ApartmentCity');
		$relations = array(
			'objType' => array(self::BELONGS_TO, 'ApartmentObjType', 'obj_type_id'),

			'city' => array(self::BELONGS_TO, 'ApartmentCity', 'city_id'),

			'windowTo' => array(self::BELONGS_TO, 'WindowTo', 'window_to'),

			'images' => array(self::HAS_MANY, 'Images', 'id_object', 'order' => 'images.sorter'),

			/*'countImages' => array(self::STAT, 'Images', 'id_object'),*/

			'user' => array(self::BELONGS_TO, 'User', 'owner_id'),

			'video' => array(self::HAS_MANY, 'ApartmentVideo', 'apartment_id',
				'order' => 'video.id ASC',
			),
			'panorama' => array(self::HAS_MANY, 'ApartmentPanorama', 'apartment_id',
				'order' => 'panorama.id ASC',
			),
		);

		if (issetModule('bookingcalendar')) {
			//$bookingCalendar = new Bookingcalendar; // for publish assets
			$relations['bookingCalendar'] = array(self::HAS_MANY, 'Bookingcalendar', 'apartment_id');
		}
		if (issetModule('paidservices')) {
			$relations['paids'] = array(self::HAS_MANY, 'ApartmentPaid', 'apartment_id');
		}

		if (issetModule('location')) {
			$relations['locCountry'] = array(self::BELONGS_TO, 'Country', 'loc_country');
			$relations['locRegion'] = array(self::BELONGS_TO, 'Region', 'loc_region');
			$relations['locCity'] = array(self::BELONGS_TO, 'City', 'loc_city');
		}

		if (issetModule('seo')) {
			$relations['seo'] = array(self::HAS_ONE, 'SeoFriendlyUrl', 'model_id', 'on' => 'model_name="Apartment"');
			//$relations['seo'] = array(self::HAS_ONE, 'SeoFriendlyUrl', 'model_id', 'on' => 'model_name="Apartment"', 'group' => 'seo.model_id', 'order' => 'seo.id ASC');
		}

		return $relations;
	}

	public function scopes() {
		return array(
			'onlyAuthOwner' => array(
				'condition' => $this->getTableAlias() . '. owner_id = ' . Yii::app()->user->id,
			),
			'notDeleted' => array(
				'condition' => $this->getTableAlias() . '. deleted = 0',
			),
			'withPhoto' => array(
				'condition' => 'count_img > 0',
			),
		);
	}

	public function getUrl($absolute = true)
	{
		if (issetModule('seo')) {
			return $this->getRelationUrl($absolute);
		} else {
			return self::getUrlById($this->id, $absolute);
		}
	}

	public function getRelationUrl($absolute = true) {
		$method = $absolute ? 'createAbsoluteUrl' : 'createUrl';

		if (issetModule('seo')) {
			$seo = $this->seo;
			if ($this->seo) {
				$field = 'url_' . Yii::app()->language;
				if($seo->$field) {
					return Yii::app()->{$method}('/apartments/main/view', array(
						'url' => $seo->$field . (param('urlExtension') ? '.html' : ''),
					));
				}
			}
		}

		return Yii::app()->{$method}('/apartments/main/view', array(
			'id' => $this->id,
		));
	}

	public static function getUrlById($id, $absolute = true) {
		$method = $absolute ? 'createAbsoluteUrl' : 'createUrl';
		if (issetModule('seo')) {
			$seo = SeoFriendlyUrl::getForUrl($id, 'Apartment');

			if ($seo) {
				$field = 'url_' . Yii::app()->language;
				return Yii::app()->{$method}('/apartments/main/view', array(
					'url' => $seo->$field . (param('urlExtension') ? '.html' : ''),
				));
			}
		}

		return Yii::app()->{$method}('/apartments/main/view', array(
			'id' => $id,
		));
	}

    public function attributeLabels()
    {
        return array(
            'id' => tt('ID', 'apartments'),
            'type' => tt('Type', 'apartments'),
            'price' => tt('Price', 'apartments'),
            'num_of_rooms' => tt('Number of rooms', 'apartments'),
            'floor' => tt('Floor', 'apartments'),
            'floor_total' => tt('Total number of floors', 'apartments'),
            'floor_all' => tt('Floor', 'apartments') . '/' . tt('Total number of floors', 'apartments'),
            'square' => tt('Square', 'apartments'),
            'land_square' => tt('Land square', 'apartments'),
            'window_to' => tt('Window to', 'apartments'),
            'title' => tt('Apartment title', 'apartments'),
            'description' => tt('Description', 'apartments'),
            'description_near' => tt('What is near?', 'apartments'),
            'metro_station' => tt('Metro station', 'apartments'),
            'address' => tt('Address', 'apartments'),
            'special_offer' => tt('Special offer', 'apartments'),
            'berths' => tt('Number of berths', 'apartments'),
            'active' => tt('Status', 'apartments'),
            'metroStations' => tt('Nearest metro stations', 'apartments'),
            'is_free_to' => tt('to', 'apartments'),
            'is_special_offer' => tt('Special offer', 'apartments'),
            'obj_type_id' => tt('Object type', 'apartments'),
            'city_id' => tt('City', 'apartments'),
            'city' => tt('City', 'apartments'),
            'owner_active' => tt('Status (owner)', 'apartments'),
            'ownerEmail' => tt('Owner email', 'apartments'),
            'ownerUsername' => tt('ownerUsername', 'apartments'),
			'searchPaidService' => tc('Paid services'),
            'exchange_to' => tt('Exchange to', 'apartments'),
            'is_price_poa' => tt('is_price_poa', 'apartments'),
            'video_file' => tt('video_file', 'apartments'),
            'video_html' => tt('video_html', 'apartments'),
            'references' => tc('References'),
            'loc_country' => tc('Country'),
            'locCountry' => tc('Country'),
            'loc_region' => tc('Region'),
            'locRegion' => tc('Region'),
            'loc_city' => tc('City'),
            'locCity' => tc('City'),
            'note' => tt('Note', 'apartments'),
            'phone' => tt('Owner phone', 'apartments'),
            'panoramaFile' => tc('A wide angle panorama-image or a ready SWF file of the panorama'),
            'period_activity' => tt("Period of listing's activity", 'apartments'),
			'deleted' => tt("Status (deleted)", 'apartments')
        );
    }

    public function getTitle()
    {
        return $this->getStrByLang('title');
    }

    public function search()
    {   
        $criteria = new CDbCriteria;
        $tmp = 'title_' . Yii::app()->language;

        $criteria->addCondition('t.phone LIKE "%' . $this->phone . '%"');

        $criteria->compare($this->getTableAlias() . '.id', $this->id);
        $criteria->compare($this->getTableAlias() . '.active', $this->active, true);       

        $criteria->addCondition($this->getTableAlias() . '.active<>:draft');
        $criteria->params[':draft'] = self::STATUS_DRAFT; 

        $criteria->compare($this->getTableAlias() . '.owner_active', $this->owner_active, true);
        if (issetModule('location')) {
            $criteria->compare('loc_country', $this->loc_country);
            $criteria->compare('loc_region', $this->loc_region);
            $criteria->compare('loc_city', $this->loc_city);
        } else
            $criteria->compare('city_id', $this->city_id);

        $criteria->compare($this->getTableAlias() . '.type', $this->type);
        $criteria->compare('obj_type_id', $this->obj_type_id);

		$criteria->compare('deleted', $this->deleted);

        $criteria->compare($tmp, $this->$tmp, true);

        if (issetModule('userads') && param('useModuleUserAds', 1)) {
            if ($this->ownerEmail) {
                $criteria->addCondition('email LIKE "%' . $this->ownerEmail . '%"');
            }
        }
        if ($this->ownerUsername) {
            $criteria->addCondition('username LIKE "%' . $this->ownerUsername . '%"');
        }

		if(issetModule('paidservices')) {
			if ($this->searchPaidService) {
				$sql = 'SELECT apartment_id FROM {{apartment_paid}} WHERE paid_id = '.(int) $this->searchPaidService.' AND status = '.ApartmentPaid::STATUS_ACTIVE.' AND date_end >= NOW()';
				$paidApartments = Yii::app()->db->createCommand($sql)->queryColumn();

				if (!$paidApartments)
					$paidApartments = array(0);

				$criteria->addInCondition($this->getTableAlias() . '.id', $paidApartments);
			}
		}

        $criteria->addInCondition($this->getTableAlias() . '.type', self::availableApTypesIds());

        $criteria->order = $this->getTableAlias() . '.sorter DESC';
        $criteria->with = array('city', 'user');

        return new CustomActiveDataProvider($this, array(
            'criteria' => $criteria,
            //'sort'=>array('defaultOrder'=>'sorter'),
            'pagination' => array(
                'pageSize' => param('adminPaginationPageSize', 20),
            ),
        ));
    }

    public function getPriceFrom()
    {
        if (isFree()) {
            return $this->price;
        }
        return round(Currency::convertFromDefault($this->price), param('round_price', 2));
    }

    public function getPriceTo()
    {
        if (isFree()) {
            return $this->price_to;
        }
        return round(Currency::convertFromDefault($this->price_to), param('round_price', 2));
    }

    public function getCurrency()
    {
        if (isFree()) {
            return param('siteCurrency', '$');
        } else {
            return Currency::getCurrentCurrencyName();
        }
    }

    public static function getFullInformation($apartmentId, $type = Apartment::TYPE_DEFAULT, $catId = null)
    {

        $addWhere = '';
        $addWhere .= (Apartment::TYPE_RENT == $type) ? ' AND reference_values.for_rent=1' : '';
        $addWhere .= (Apartment::TYPE_SALE == $type) ? ' AND reference_values.for_sale=1' : '';

        if ($catId)
            $addWhere .= ' AND reference_categories.id = ' . (int)$catId . ' ';

        $sql = '
			SELECT	style,
					reference_categories.title_' . Yii::app()->language . ' as category_title,
					reference_values.title_' . Yii::app()->language . ' as value,
					reference_categories.id as ref_id,
					reference_values.id as ref_value_id
			FROM	{{apartment_reference}} reference,
					{{apartment_reference_categories}} reference_categories,
					{{apartment_reference_values}} reference_values
			WHERE	reference.apartment_id = "' . intval($apartmentId) . '"
					AND reference.reference_id = reference_categories.id
					AND reference.reference_value_id = reference_values.id
					' . $addWhere . '
			ORDER BY reference_categories.sorter, reference_values.sorter';

        // Таблица apartment_reference меняется только при измении объявления (т.е. таблицы apartment)
        // Достаточно зависимости от apartment вместо apartment_reference
        $dependency = new CDbCacheDependency('
			SELECT MAX(val) FROM
				(SELECT MAX(date_updated) as val FROM {{apartment_reference_values}}
				UNION
				SELECT MAX(date_updated) as val FROM {{apartment_reference_categories}}
				UNION
				SELECT MAX(date_updated) as val FROM {{apartment}} WHERE id = "' . intval($apartmentId) . '") as t
		');

        $results = Yii::app()->db->cache(param('cachingTime', 1209600), $dependency)->createCommand($sql)->queryAll();

        $return = array();
        foreach ($results as $result) {
            if (!isset($return[$result['ref_id']])) {
                $return[$result['ref_id']]['title'] = $result['category_title'];
                $return[$result['ref_id']]['style'] = $result['style'];
            }
            $return[$result['ref_id']]['values'][$result['ref_value_id']] = $result['value'];
        }
        return $return;
    }

    public static function getCategories($id = null, $type = Apartment::TYPE_DEFAULT, $selected = array())
    {
        $addWhere = '';
        $addWhere .= (Apartment::TYPE_RENT == $type) ? ' AND reference_values.for_rent=1' : '';
        $addWhere .= (Apartment::TYPE_SALE == $type) ? ' AND reference_values.for_sale=1' : '';

        $sql = '
			SELECT	style,
					reference_values.title_' . Yii::app()->language . ' as value_title,
					reference_categories.title_' . Yii::app()->language . ' as category_title,
					reference_category_id, reference_values.id
			FROM	{{apartment_reference_values}} reference_values,
					{{apartment_reference_categories}} reference_categories
			WHERE	reference_category_id = reference_categories.id AND reference_categories.type=1
			' . $addWhere . '
			ORDER BY reference_categories.sorter, reference_values.sorter';

        $dependency = new CDbCacheDependency('
			SELECT MAX(val) FROM
				(SELECT MAX(date_updated) as val FROM {{apartment_reference_values}}
				UNION
				SELECT MAX(date_updated) as val FROM {{apartment_reference_categories}}) as t
		');

        $results = Yii::app()->db->cache(param('cachingTime', 1209600), $dependency)->createCommand($sql)->queryAll();

        $return = array();

        if ($id) {
            $selected = Apartment::getFullInformation($id, $type);
        } else {
            // При добавлении объявления
            if ($selected && count($selected)) {
                $tmp = array();
                foreach ($selected as $selKey => $selVal) {
                    $tmp[$selKey]['values'] = $selVal;
                }
                $selected = $tmp;
            }
        }
        if ($results) {
            foreach ($results as $result) {
                $return[$result['reference_category_id']]['title'] = $result['category_title'];
                $return[$result['reference_category_id']]['style'] = $result['style'];
                $return[$result['reference_category_id']]['values'][$result['id']]['title'] = $result['value_title'];
                if (isset($selected[$result['reference_category_id']]['values'][$result['id']])) {
                    $return[$result['reference_category_id']]['values'][$result['id']]['selected'] = true;
                } else {
                    $return[$result['reference_category_id']]['values'][$result['id']]['selected'] = false;
                }
            }
        }

        return $return;
    }

    public function afterFind(){
		$this->oldStatus = $this->active;
        if (!isFree()) {
            $this->in_currency = Currency::getDefaultCurrencyModel()->char_code;
        } else {
            $this->in_currency = param('siteCurrency', '$');
        }

        if ($this->activity_always) {
            $this->period_activity = 'always';
        } else {
            $this->period_activity = param('apartment_periodActivityDefault', 'always');
        }

        return parent::afterFind();
    }

    public function saveCategories()
    {
        $sql = 'DELETE FROM {{apartment_reference}} WHERE apartment_id="' . $this->id . '"';
        Yii::app()->db->createCommand($sql)->execute();

        if (isset($_POST['category'])) {
            foreach ($_POST['category'] as $catId => $value) {
                foreach ($value as $valId => $val) {
                    $sql = 'INSERT INTO {{apartment_reference}} (reference_id, reference_value_id, apartment_id)
						VALUES (:refId, :refValId, :apId) ';
                    $command = Yii::app()->db->createCommand($sql);
                    $command->bindValue(":refId", $catId, PDO::PARAM_INT);
                    $command->bindValue(":refValId", $valId, PDO::PARAM_INT);
                    $command->bindValue(":apId", $this->id, PDO::PARAM_INT);
                    $command->execute();
                }
            }
        }
    }

    public function beforeValidate() {
        Images::saveComments($this);
        return parent::beforeValidate();
    }

    public function beforeSave()
    {
        if (!$this->square) {
            $this->square = 0;
        }

        if (!$this->land_square) {
            $this->land_square = 0;
        }

        $userInfo = User::model()->findByPk($this->owner_id);

        // чистим поля где есть визуальный редактор http://www.elisdn.ru/blog/12/dpurifytextbehavior-ispolzuem-html-purifier-dlia-filtracii-dannih-v-yii
        $allWs = HFormEditor::getAllFields();

        $activeLangs = Lang::getActiveLangs(true);
        foreach($allWs as $row){
            if($row['type'] != FormDesigner::TYPE_TEXT_AREA_WS){
                continue;
            }
            if($row['is_i18n']){
                foreach($activeLangs as $lang){
                    $attr = $row['field'].'_'.$lang['name_iso'];
                    $this->{$attr} = purify($this->{$attr});
                }
            } else {
                $this->{$row['field']} = purify($this->{$row['field']});
            }
        }


        if ($this->isNewRecord) {
            $this->owner_id = $this->owner_id ? $this->owner_id : Yii::app()->user->id;

            if ($userInfo && in_array($userInfo->role, array(User::ROLE_MODERATOR, User::ROLE_ADMIN))) {
                $this->owner_active = self::STATUS_ACTIVE;
            }

            $maxSorter = Yii::app()->db->createCommand()
                ->select('MAX(sorter) as maxSorter')
                ->from($this->tableName())
                ->queryScalar();
            $this->sorter = $maxSorter + 1;

            if ($this->obj_type_id == 0) {
                $this->obj_type_id = Yii::app()->db->createCommand('SELECT MIN(id) FROM {{apartment_obj_type}}')->queryScalar();
            }
        }

        if (!isFree()) {
            $defaultCurrencyCharCode = Currency::getDefaultCurrencyModel()->char_code;

            if ($defaultCurrencyCharCode != $this->in_currency) {

                $this->price = (int)Currency::convert($this->price, $this->in_currency, $defaultCurrencyCharCode);

                if (isset($this->price_to) && $this->price_to) {
                    $this->price_to = (int)Currency::convert($this->price_to, $this->in_currency, $defaultCurrencyCharCode);
                }
            }
        }

        switch ($this->type) {
            case self::TYPE_SALE:
                $this->price_type = self::PRICE_SALE;
                break;

            case self::TYPE_BUY:
                $this->price_type = self::PRICE_BUY;
                break;

            case self::TYPE_RENTING:
                $this->price_type = self::PRICE_RENTING;
                break;

            case self::TYPE_CHANGE:
                $this->price_type = self::PRICE_CHANGE;
                break;
        }

        if (isset($_POST['set_period_activity']) && $_POST['set_period_activity'] == 1 && $this->period_activity) {
            $list = HApartment::getPeriodActivityList();
            if (isset($list[$this->period_activity])) {
                if ($this->period_activity == 'always') {
                    $this->activity_always = 1;
                } else {
                    $this->date_end_activity = date('Y-m-d', strtotime($this->period_activity, time()));
                    $this->activity_always = 0;
                }
            }
        }

        return parent::beforeSave();
    }

    public function afterSave(){
        if ($this->scenario == 'savecat') {
            $this->saveCategories();
            if ($this->metroStations) {
                $this->setMetroStations($this->metroStations);
            }
        }

        if ($this->panoramaFile) {
            $panorama = new ApartmentPanorama();
            $panorama->fileInstance = $this->panoramaFile;
            $panorama->apartment_id = $this->id;
            $panorama->save();
        }

        if (issetModule('seo') && param('genFirendlyUrl') && !$this->isAjaxLoadOnUpdate) {
            SeoFriendlyUrl::getAndCreateForModel($this);
        }

        if (issetModule('socialposting') && $this->active == self::STATUS_ACTIVE && $this->owner_active = self::STATUS_ACTIVE) {
            SocialpostingModel::preparePosting($this);
        }

		//if($this->isNewRecord) {
		if($this->getIsNewObject()){
			if (isset($this->user) && $this->user) {
				if ($this->user->role != User::ROLE_ADMIN && $this->user->role != User::ROLE_MODERATOR) {
					$notifier = new Notifier();
					$notifier->raiseEvent('onNewApartment', $this);
				}
			}
		}

        return parent::afterSave();
    }

	public function makeClone() {
		$old_id = $this->id;

		$this->id = null;
		$this->isNewRecord = true;

		$activeLangs = Lang::getActiveLangs();


		foreach($activeLangs as $lang){
			$field = 'title_'.$lang;
			$this->$field = utf8_substr(tt('Copy of', 'common', $lang). ' ' . $this->$field, 0 , 254);

		}
		if(Yii::app()->user->checkAccess('apartments_admin')) {
			$this->active = Apartment::STATUS_INACTIVE;
			$this->owner_active = Apartment::STATUS_ACTIVE;
		} else {
			if(param('useUseradsModeration', 1)){
				$this->active = Apartment::STATUS_MODERATION;
			}
			else {
				$this->active = Apartment::STATUS_ACTIVE;
			}
			$this->owner_active = Apartment::STATUS_INACTIVE;
		}


		$maxSorter = Yii::app()->db->createCommand()
			->select('MAX(sorter) as maxSorter')
			->from($this->tableName())
			->queryScalar();
		$this->sorter = $maxSorter + 1;

		$this->save();

		$sql = 'SELECT * FROM {{apartment_reference}} WHERE apartment_id="'.$old_id.'"';
		$result = Yii::app()->db->createCommand($sql)->queryAll();

		if($result){
			foreach($result as $res){
				$sql = 'INSERT INTO {{apartment_reference}} (reference_id, reference_value_id, apartment_id)
									VALUES (:refId, :refValId, :apId) ';
				$command = Yii::app()->db->createCommand($sql);
				$command->bindValue(":refId", $res['reference_id'], PDO::PARAM_INT);
				$command->bindValue(":refValId", $res['reference_value_id'], PDO::PARAM_INT);
				$command->bindValue(":apId", $this->id, PDO::PARAM_INT);
				$command->execute();

			}
		}

		$sql = 'SELECT * FROM {{apartment_video}} WHERE apartment_id="'.$old_id.'"';
		$result = Yii::app()->db->createCommand($sql)->queryAll();

		$oldPathVideo = Yii::getPathOfAlias('webroot.uploads.video').DIRECTORY_SEPARATOR.$old_id;
		$newPathVideo = Yii::getPathOfAlias('webroot.uploads.video').DIRECTORY_SEPARATOR.$this->id;

		if ($result && newFolder($newPathVideo)) {
			foreach ($result as $res) {
				$sql = 'INSERT INTO {{apartment_video}} (apartment_id, video_file, 	video_html, date_updated)
                            VALUES ("'.$this->id.'", "'.$res['video_file'].'", "'.$res['video_html'].'", NOW())';
				Yii::app()->db->createCommand($sql)->execute();

				if ($res['video_file'])
					copy($oldPathVideo.DIRECTORY_SEPARATOR.$res['video_file'], $newPathVideo.DIRECTORY_SEPARATOR.$res['video_file']);
			}
		}



		$oldPathUploads = Yii::getPathOfAlias('webroot.uploads.objects').DIRECTORY_SEPARATOR.$old_id;
		$newPathUploads = Yii::getPathOfAlias('webroot.uploads.objects').DIRECTORY_SEPARATOR.$this->id;

		if(newFolder($newPathUploads)) {
			$sql = 'SELECT * FROM {{apartment_panorama}} WHERE apartment_id="'.$old_id.'"';
			$result = Yii::app()->db->createCommand($sql)->queryAll();

			if ($result) {
				foreach ($result as $res) {
					$sql = 'INSERT INTO {{apartment_panorama}} (apartment_id, name, width, height, date_created)
                            VALUES ("'.$this->id.'", "'.$res['name'].'", "'.$res['width'].'", "'.$res['height'].'", NOW())';
					Yii::app()->db->createCommand($sql)->execute();

					copy($oldPathUploads.DIRECTORY_SEPARATOR.$res['name'], $newPathUploads.DIRECTORY_SEPARATOR.$res['name']);
				}
			}
			$oldPathImages = $oldPathUploads.DIRECTORY_SEPARATOR.Images::ORIGINAL_IMG_DIR;
			$newPathImages = $newPathUploads.DIRECTORY_SEPARATOR.Images::ORIGINAL_IMG_DIR;
			$newPathImagesModified = $newPathUploads.DIRECTORY_SEPARATOR.Images::MODIFIED_IMG_DIR;
			if(newFolder($newPathImages) && newFolder($newPathImagesModified)) {
				$sql = 'SELECT * FROM {{images}} WHERE id_object="'.$old_id.'"';
				$result = Yii::app()->db->createCommand($sql)->queryAll();

				if ($result) {
					foreach ($result as $res) {
						$sql = 'INSERT INTO {{images}} (id_object, id_owner, file_name, sorter, comment, is_main, date_created)
                            VALUES ("'.$this->id.'", "'.$res['id_owner'].'", "'.$res['file_name'].'", "'.$res['sorter'].'", "'.$res['comment'].'", "'.$res['is_main'].'", NOW())';
						Yii::app()->db->createCommand($sql)->execute();

						copy($oldPathImages.DIRECTORY_SEPARATOR.$res['file_name'], $newPathImages.DIRECTORY_SEPARATOR.$res['file_name']);
					}
				}

			}
		}





	}

    public function beforeDelete()
    {
		if (param('notDeleteListings', 0)) {
			$this->setAttribute('deleted', true);
			$this->update(array('deleted'));
			return false;
		}

        if (issetModule('seo')) {
            $sql = 'DELETE FROM {{seo_friendly_url}} WHERE model_id="' . $this->id . '" AND ( model_name = "Apartment" OR model_name = "UserAds" )';
            Yii::app()->db->createCommand($sql)->execute();
        }

        $sql = 'DELETE FROM {{apartment_reference}} WHERE apartment_id="' . $this->id . '"';
        Yii::app()->db->createCommand($sql)->execute();

        $sql = 'DELETE FROM {{comments}} WHERE model_id="' . $this->id . '" AND model_name="Apartment"';
        Yii::app()->db->createCommand($sql)->execute();

        $sql = 'DELETE FROM {{apartment_statistics}} WHERE apartment_id="' . $this->id . '"';
        Yii::app()->db->createCommand($sql)->execute();

        $sql = 'DELETE FROM {{apartment_complain}} WHERE apartment_id="' . $this->id . '"';
        Yii::app()->db->createCommand($sql)->execute();

        //Images::deleteByObjectId($this);
        Images::deleteDbByObjectId($this->id);

        $dir = Yii::getPathOfAlias('webroot.uploads.objects') . '/' . $this->id;
        rrmdir($dir);

        if (issetModule('metrostations')) {
            $sql = 'DELETE FROM {{apartment_metro}} WHERE id_apartment="' . $this->id . '"';
            Yii::app()->db->createCommand($sql)->execute();
        }

        //delete QR-code
        $qr_codes = glob(Yii::getPathOfAlias('webroot.uploads.qrcodes') . '/listing_' . $this->id . '-*.png');
        if (is_array($qr_codes) && count($qr_codes))
            array_map("unlink", $qr_codes);

        // delete video
        $sql = 'DELETE FROM {{apartment_video}} WHERE apartment_id="' . $this->id . '"';
        Yii::app()->db->createCommand($sql)->execute();

        $pathVideo = Yii::getPathOfAlias('webroot.uploads.video') . DIRECTORY_SEPARATOR . $this->id;
        rmrf($pathVideo);


        if (issetModule('bookingcalendar')) {
            $sql = 'DELETE FROM {{booking_calendar}} WHERE apartment_id="' . $this->id . '"';
            Yii::app()->db->createCommand($sql)->execute();
        }

        if (issetModule('comparisonList')) {
            $sql = 'DELETE FROM {{comparison_list}} WHERE apartment_id="' . $this->id . '"';
            Yii::app()->db->createCommand($sql)->execute();
        }

        Yii::app()->cache->flush();

        return parent::beforeDelete();
    }

    public function isValidApartment($id)
    {
        $sql = 'SELECT id FROM {{apartment}} WHERE id = :id';
        $command = Yii::app()->db->createCommand($sql);
        return $command->queryScalar(array(':id' => $id));
    }

    public static function getFullDependency($id)
    {
        return new CDbCacheDependency('
			SELECT MAX(val) FROM
				(SELECT MAX(date_updated) as val FROM {{comments}} WHERE model_id = "' . intval($id) . '" AND model_name="Apartment"
				UNION
				SELECT MAX(date_updated) as val FROM {{apartment}} WHERE id = "' . intval($id) . '"
				UNION
				SELECT MAX(date_updated) as val FROM {{apartment_window_to}}
				UNION
				SELECT MAX(date_updated) as val FROM {{images}}) as t
		');
    }

    public static function getImagesDependency()
    {
        return new CDbCacheDependency('
			SELECT MAX(val) FROM
				(SELECT MAX(date_updated) as val FROM {{apartment}}
				UNION
				SELECT MAX(date_updated) as val FROM {{images}}) as t
		');
    }

    public static function getDependency()
    {
        return new CDbCacheDependency('SELECT MAX(date_updated) FROM {{apartment}}');
    }

    public static function getExistsRooms()
    {
        $sql = 'SELECT DISTINCT num_of_rooms FROM {{apartment}} WHERE active=' . self::STATUS_ACTIVE . ' AND owner_active = ' . self::STATUS_ACTIVE . ' AND num_of_rooms > 0 ORDER BY num_of_rooms';
        return Yii::app()->db->cache(param('cachingTime', 1209600), self::getDependency())->createCommand($sql)->queryColumn();
    }

    public static function getObjTypesArray($with_all = false)
    {
        Yii::import('application.modules.apartmentObjType.models.ApartmentObjType');
        $objTypes = array();
        $objTypeModel = ApartmentObjType::model()->findAll(array(
            'order' => 'sorter'
        ));
        foreach ($objTypeModel as $type) {
            $objTypes[$type->id] = $type->name;
        }
        if ($with_all) {
            $objTypes[0] = tt('All object', 'apartments');
        }
        return $objTypes;
    }

    public static function getCityArray($with_all = false)
    {
        Yii::import('application.modules.apartmentCity.models.ApartmentCity');
        $cityArr = array();
        $cityModel = ApartmentCity::model()->findAll(array(
            'order' => 'sorter'
        ));
        foreach ($cityModel as $city) {
            $cityArr[$city->id] = $city->name;
        }
        if ($with_all) {
            $cityArr[0] = tt('All city', 'apartments');
        }
        return $cityArr;
    }

    public static function getTypesArray($withAll = false)
    {
        $types = array();

        if ($withAll) {
            $types[0] = tt('All', 'apartments');
        }

        if (param('useTypeRentHour', 1) || param('useTypeRentDay', 1) || param('useTypeRentWeek', 1) || param('useTypeRentMonth', 1)) {
            $types[self::TYPE_RENT] = tt('Rent', 'apartments');
        }
        if (param('useTypeSale', 1)) {
            $types[self::TYPE_SALE] = tt('Sale', 'apartments');
        }
        if (param('useTypeRenting', 1)) {
            $types[self::TYPE_RENTING] = tt('Rent a', 'apartments');
        }
        if (param('useTypeBuy', 1)) {
            $types[self::TYPE_BUY] = tt('Buy a', 'apartments');
        }
        if (param('useTypeChange', 1)) {
            $types[self::TYPE_CHANGE] = tt('Exchange', 'apartments');
        }
        return $types;
    }

    /** For Notifier
     * @return array
     */
    public static function getI18nTypesArray()
    {
        $types = array();

        self::fillI18nArray($types, 'current', Yii::app()->language);
        self::fillI18nArray($types, 'default', Lang::getDefaultLang());
        self::fillI18nArray($types, 'admin', Lang::getAdminMailLang());

        return $types;
    }

    private static function fillI18nArray(&$types, $field, $lang)
    {
		if (param('useTypeRentHour', 1) || param('useTypeRentDay', 1) || param('useTypeRentWeek', 1) || param('useTypeRentMonth', 1)) {
            $vs[self::TYPE_RENT] = 'Want rent property to smb';
        }
        if (param('useTypeSale', 1)) {
            $vs[self::TYPE_SALE] = 'Want sale';
        }
        if (param('useTypeRenting', 1)) {
            $vs[self::TYPE_RENTING] = 'Want rent property form smb';
        }
        if (param('useTypeBuy', 1)) {
            $vs[self::TYPE_BUY] = 'Want buy';
        }
        if (param('useTypeChange', 1)) {
            $vs[self::TYPE_CHANGE] = 'Want exchange';
        }

        foreach ($vs as $type => $langField) {
            $types[$type][$field] = tt($langField, 'apartments', $lang);
        }
    }

    public static function getTypesWantArray()
    {
        $types = array();

		if (param('useTypeRentHour', 1) || param('useTypeRentDay', 1) || param('useTypeRentWeek', 1) || param('useTypeRentMonth', 1)) {
            $types[self::TYPE_RENT] = tt('Want rent property to smb', 'apartments');
        }
        if (param('useTypeSale', 1)) {
            $types[self::TYPE_SALE] = tt('Want sale', 'apartments');
        }
        if (param('useTypeRenting', 1)) {
            $types[self::TYPE_RENTING] = tt('Want rent property form smb', 'apartments');
        }
        if (param('useTypeBuy', 1)) {
            $types[self::TYPE_BUY] = tt('Want buy', 'apartments');
        }
        if (param('useTypeChange', 1)) {
            $types[self::TYPE_CHANGE] = tt('Want exchange', 'apartments');
        }

        return $types;
    }

    public static function getNameByType($type)
    {
        if (!isset(self::$_type_arr)) {
            self::$_type_arr = self::getTypesArray();
        }

        if (!in_array($type, array_keys(self::$_type_arr))) {
            return self::$_type_arr[min(Apartment::availableApTypesIds())];
        }

        return self::$_type_arr[$type];
    }

    public static function getPriceArray($type, $all = false, $with_all = false) {
		if ($all) {
			$price = array(
				self::PRICE_SALE => tt('Sale price', 'apartments'),
				self::PRICE_PER_HOUR => tt('Price per hour', 'apartments'),
				self::PRICE_PER_DAY => tt('Price per day', 'apartments'),
				self::PRICE_PER_WEEK => tt('Price per week', 'apartments'),
				self::PRICE_PER_MONTH => tt('Price per month', 'apartments'),
				self::PRICE_RENTING => '',
				self::PRICE_BUY => '',
				self::PRICE_CHANGE => '',
			);

			if (!param('useTypeRentHour', 1) && array_key_exists(self::PRICE_PER_HOUR, $price))
				unset($price[self::PRICE_PER_HOUR]);

			if (!param('useTypeRentDay', 1) && array_key_exists(self::PRICE_PER_DAY, $price))
				unset($price[self::PRICE_PER_DAY]);

			if (!param('useTypeRentWeek', 1) && array_key_exists(self::PRICE_PER_WEEK, $price))
				unset($price[self::PRICE_PER_WEEK]);

			if (!param('useTypeRentMonth', 1) && array_key_exists(self::PRICE_PER_MONTH, $price))
				unset($price[self::PRICE_PER_MONTH]);

			return $price;
		}

		if ($type == self::TYPE_SALE) {
			$price = array(
				self::PRICE_SALE => tt('Sale price', 'apartments'),
			);
		}
		elseif ($type == self::TYPE_RENT) {
			$price = array(
				self::PRICE_PER_HOUR => tt('Price per hour', 'apartments'),
				self::PRICE_PER_DAY => tt('Price per day', 'apartments'),
				self::PRICE_PER_WEEK => tt('Price per week', 'apartments'),
				self::PRICE_PER_MONTH => tt('Price per month', 'apartments'),
			);

			if (!param('useTypeRentHour', 1) && array_key_exists(self::PRICE_PER_HOUR, $price))
				unset($price[self::PRICE_PER_HOUR]);

			if (!param('useTypeRentDay', 1) && array_key_exists(self::PRICE_PER_DAY, $price))
				unset($price[self::PRICE_PER_DAY]);

			if (!param('useTypeRentWeek', 1) && array_key_exists(self::PRICE_PER_WEEK, $price))
				unset($price[self::PRICE_PER_WEEK]);

			if (!param('useTypeRentMonth', 1) && array_key_exists(self::PRICE_PER_MONTH, $price))
				unset($price[self::PRICE_PER_MONTH]);
		}
		elseif ($type == self::TYPE_RENTING) {
			$price = array(
				self::PRICE_RENTING => '',
			);
		}
		elseif ($type == self::TYPE_BUY) {
			$price = array(
				self::PRICE_BUY => '',
			);
		}
		elseif ($type == self::TYPE_CHANGE) {
			$price = array(
				self::PRICE_CHANGE => '',
			);
		}

		if ($with_all) {
			$price[0] = tt('All');
		}
		return $price;
	}

	public static function getPriceName($price_type) {
		if (!isset(self::$_price_arr)) {
			self::$_price_arr = self::getPriceArray(NULL, true);
		}
		return self::$_price_arr[$price_type];
	}

	public function getPrettyPrice($showPriceType = true) {
		if (Yii::app()->theme->name == 'atlas') {
			if ($this->is_price_poa)
				return tt('is_price_poa', 'apartments');

			$tpl = Lang::getTemplateForPrice(Yii::app()->language);

			$price = $this->getPriceFrom();
			$priceTo = $this->getPriceTo();

			if ($this->isPriceFromTo()) {
				$priceFromTo = '';
				if ($price) {
					$data = array(
						'{CURRENCY}' => $this->getCurrency(),
						'{TEXT_FROM}' => tc('price_from'),
						'{PRICE}' => $this->setPretty($price),
					);
					$priceFromTo = strtr($tpl['from'], $data);
				}
				if ($priceTo) {
					$data = array(
						'{CURRENCY}' => $this->getCurrency(),
						'{TEXT_TO}' => tc('price_to'),
						'{PRICE}' => $this->setPretty($priceTo),
					);
					$priceFromTo .= strtr($tpl['to'], $data);
				}

				return $priceFromTo;
			}

			$data = array(
				'{CURRENCY}' => $this->getCurrency(),
				'{PRICE}' => $this->setPretty($price),
				'{TYPE}' => ($showPriceType) ? self::getPriceName($this->price_type) : '',
			);

			return strtr($tpl['default'], $data);
		} else {
			if ($this->is_price_poa)
				return tt('is_price_poa', 'apartments');

			$price = $this->getPriceFrom();
			$priceTo = $this->getPriceTo();
			if ($this->isPriceFromTo()) {
				$priceFromTo = '';
				if ($price)
					$priceFromTo = tc('price_from') . ' ' . $this->setPretty($price) . ' ' . $this->getCurrency();
				if ($priceTo)
					$priceFromTo .= $priceTo ? ' ' . tc('price_to') . ' ' . $this->setPretty($priceTo) . ' ' . $this->getCurrency() : '';

				return $priceFromTo;
			}
			return $this->setPretty($price) . ' ' . $this->getCurrency() . ' ' . self::getPriceName($this->price_type);
		}
	}

    public function isPriceFromTo()
    {
        return $this->type == self::TYPE_RENTING || $this->type == self::TYPE_BUY;
    }

	public function setPretty($price) {
		/*if (!param('usePrettyPrice', 1) || Yii::app()->language != 'ru') {
			return Apartment::priceFormat($price);
		}

		if (substr($price, -6) == "000000") {
			$priceStr = substr_replace($price, ' ' . tt('million', 'apartments'), -6);
		} elseif (substr($price, -5) == "00000" && strlen($price) >= 7) {
			$priceStr = substr_replace($price, '.', -6, 0);
			$priceStr = substr_replace($priceStr, ' ' . tt('million', 'apartments'), -5);
		} elseif (substr($price, -3) == "000") {
			$priceStr = substr_replace($price, ' ' . tt('thousand', 'apartments'), -3);
		} elseif (substr($price, -2) == "00" && strlen($price) >= 4) {
			$priceStr = substr_replace($price, '.', -3, 0);
			$priceStr = substr_replace($priceStr, ' ' . tt('thousand', 'apartments'), -2);
		} else {
			return Apartment::priceFormat($price);
		}

		return $priceStr;*/

		if (!param('usePrettyPrice', 1)) {
			return Apartment::priceFormat($price);
		}

		if (!is_numeric($price)) return false;

		$price = (0+str_replace(",", "", $price));

		if ($price > 1000000000000) $priceStr = round(($price/1000000000000), 9).' '.tt('trillion', 'apartments');
		elseif ($price > 1000000000) $priceStr = round(($price/1000000000), 8).' '.tt('billion', 'apartments');
		elseif ($price > 1000000) $priceStr = round(($price/1000000), 6).' '.tt('million', 'apartments');
		elseif ($price > 1000) $priceStr = round(($price/1000), 3).' '.tt('thousand', 'apartments');
		else return Apartment::priceFormat($price);

		return $priceStr;
	}

    public static function priceFormat($price)
    {
        if (is_numeric($price)) {
            $priceDecimalsPoint = (param('priceDecimalsPoint')) ? param('priceDecimalsPoint') : ' ';
            $priceThousandsSeparator = (param('priceThousandsSeparator')) ? param('priceThousandsSeparator') : ' ';

            return number_format($price, 0, $priceDecimalsPoint, $priceThousandsSeparator);
        }

        return $price;
    }

    public static function getApTypes()
    {
        $ownerActiveCond = '';
        if (param('useUserads'))
            $ownerActiveCond = ' AND owner_active = ' . self::STATUS_ACTIVE . ' ';

        $sql = 'SELECT DISTINCT price_type FROM {{apartment}} WHERE type IN (' . implode(',', Apartment::availableApTypesIds()) . ') AND active = ' . self::STATUS_ACTIVE . ' ' . $ownerActiveCond . '';
        return Yii::app()->db->cache(param('cachingTime', 1209600), self::getDependency())->createCommand($sql)->queryColumn();
    }

    public static function getSquareMinMax()
    {
        $ownerActiveCond = '';
        if (param('useUserads'))
            $ownerActiveCond = ' AND owner_active = ' . self::STATUS_ACTIVE . ' ';

        $sql = 'SELECT MIN(square) as square_min, MAX(square) as square_max FROM {{apartment}} WHERE price_type IN('.implode(",", array_keys(Apartment::getPriceArray(Apartment::PRICE_SALE, true))).') AND active = ' . self::STATUS_ACTIVE . ' ' . $ownerActiveCond . '';
        return Yii::app()->db->cache(param('cachingTime', 1209600), self::getDependency())->createCommand($sql)->queryRow();
    }

    public static function getPriceMinMax($objTypeId = 1, $all = false)
    {
        $ownerActiveCond = '';
        if (param('useUserads'))
            $ownerActiveCond = ' AND owner_active = ' . self::STATUS_ACTIVE . ' ';

        if ($all)
            $sql = 'SELECT MIN(price) as price_min, MAX(price) as price_max FROM {{apartment}} WHERE price_type IN('.implode(",", array_keys(Apartment::getPriceArray(Apartment::PRICE_SALE, true))).') AND active = ' . self::STATUS_ACTIVE . ' ' . $ownerActiveCond . ' AND is_price_poa = 0';
        else
            $sql = 'SELECT MIN(price) as price_min, MAX(price) as price_max FROM {{apartment}} WHERE price_type IN('.implode(",", array_keys(Apartment::getPriceArray(Apartment::PRICE_SALE, true))).') AND obj_type_id = "' . $objTypeId . '" AND active = ' . self::STATUS_ACTIVE . ' ' . $ownerActiveCond . ' AND is_price_poa = 0';

        return Yii::app()->db->cache(param('cachingTime', 1209600), self::getDependency())->createCommand($sql)->queryRow();
    }

    public static function getModerationStatusArray($withAll = false)
    {
        $status = array();
        if ($withAll) {
            $status[''] = tt('All', 'common');
        }

        $status[0] = CHtml::encode(tt('Inactive', 'common'));
        $status[1] = CHtml::encode(tt('Active', 'common'));
        $status[2] = CHtml::encode(tt('Awaiting moderation', 'common'));

        return $status;
    }

    public static function getRel($id, $lang)
    {
        $model = self::model()->resetScope()->findByPk($id);

        $title = 'title_' . $lang;
        $model->title = $model->$title;

        return $model;
    }

    public function getAddress()
    {
        return $this->getStrByLang('address');
    }

    public function getDescription()
    {
        return $this->getStrByLang('description');
    }

    public function getDescription_Near()
    {
        return $this->getStrByLang('description_near');
    }

    public static function getApartmentsStatusArray($withAll = false)
    {
        $status = array();
        if ($withAll) {
            $status[''] = Yii::t('common', 'All');
        }

        $status[0] = Yii::t('common', 'Inactive');
        $status[1] = Yii::t('common', 'Active');

        return $status;
    }

	public static function getApartmentsDeletedArray($withAll = false)
	{
		$status = array();
		if ($withAll) {
			$status[''] = Yii::t('common', 'All');
		}

		$status[0] = Yii::t('common', 'Active');
		$status[1] = Yii::t('common', 'Deleted');

		return $status;
	}

	public static function getApartmentsDeleted($status)
	{
		if (!isset(self::$_apartment_del_arr)) {
			self::$_apartment_del_arr = self::getApartmentsDeletedArray();
		}
		return self::$_apartment_del_arr[$status];
	}

    public static function getApartmentsStatus($status)
    {
        if (!isset(self::$_apartment_arr)) {
            self::$_apartment_arr = self::getApartmentsStatusArray();
        }
        return self::$_apartment_arr[$status];
    }

    public static function setApartmentVisitCount(Apartment $apartment, $ipAddress = '', $userAgent = '')
    {
        if (isset($apartment->id) && $apartment->id) {
            // total
            $apartment->visits += 1;
            $apartment->save(false, array('visits'));

            // today
            Yii::app()->db->createCommand()->insert('{{apartment_statistics}}', array(
                'apartment_id' => $apartment->id,
                'date_created' => new CDbExpression('NOW()'),
                'ip_address' => $ipAddress,
                'browser' => $userAgent,
            ));
        }
    }

    public static function getApartmentVisitCount(Apartment $apartment)
    {
        if (isset($apartment->id) && $apartment->id) {
            $statistics = array();

            $statistics['all'] = $apartment->visits;

            $statistics['today'] = Yii::app()->db->createCommand()
                ->select(array(new CDbExpression("COUNT(id) AS countToday")))
                ->from('{{apartment_statistics}}')
                ->where('apartment_id = "' . intval($apartment->id) . '" AND date(date_created)=date(now())')
                ->queryScalar();

			if ($statistics['all'] < $statistics['today'])
				$statistics['all'] = $statistics['today'];

            return $statistics;
        }
        return false;
    }

    public static function getCountModeration()
    {
        $sql = "SELECT COUNT(id) FROM {{apartment}} WHERE price_type IN (" . implode(',', array_keys(Apartment::getPriceArray(Apartment::PRICE_SALE, true))) . ") AND active=" . self::STATUS_MODERATION;
        return (int)Yii::app()->db->createCommand($sql)->queryScalar();
    }

    public function getUrlSendEmail()
    {
        return Yii::app()->createUrl('/apartments/main/sendEmail', array('id' => $this->id));
    }

    public function getObjType4table()
    {
        $str = $this->objType->getName();
        if ($this->num_of_rooms) {
            $str .= '<br/>' . Yii::t('module_apartments', '{n} rooms', $this->num_of_rooms);
        }
        return $str;
    }

    public function getRowCssClass()
    {
        if ($this->is_special_offer) {
            return 'special_offer_tr';
        }
        if ($this->date_up_search != '0000-00-00 00:00:00') {
            return 'up_in_search';
        }
        return '';
    }


    public function getMapIconUrl()
    {
        if (isset($this->objType) && $this->objType->icon_file) {
            $iconUrl = Yii::app()->getBaseUrl() . '/' . $this->objType->iconsMapPath . '/' . $this->objType->icon_file;
        } else {
            $iconUrl = Yii::app()->theme->baseUrl . "/images/house.png";
        }

        return $iconUrl;
    }

    public function getPaidHtml($withDateEnd = false, $withAddLink = false, $icon = false)
    {
        $content = '';
        $htmlArray = $issetPaids = array();

        # применённые платные услуги
        if (isset($this->paids)) {
            foreach ($this->paids as $apartmentPaid) {
                if (isset($apartmentPaid->paidService) && strtotime($apartmentPaid->date_end) > time()) {
                    $issetPaids[$apartmentPaid->paidService->id] = $apartmentPaid;
                }
            }
        }

        # все платные услуги
        $allPaidServices = PaidServices::model()->findAll('id != ' . PaidServices::ID_ADD_FUNDS . ' AND active = 1');
        if ($allPaidServices) {
            foreach ($allPaidServices as $service) {

                if (!Yii::app()->user->checkAccess('backend_access')) { # пользователь

                    if (array_key_exists($service->id, $issetPaids)) {
                        $html = '<div class="paid_row">' . CHtml::link(
                                $icon ? $service->getImageIcon(tc('is valid till') . ' ' . $issetPaids[$service->id]->date_end) : $service->name,
                                array('/paidservices/main/index',
                                    'id' => $this->id,
                                    'paid_id' => $service->id,
                                ),
                                array('class' => 'fancy'));
                        $html .= $withDateEnd ? '<span class="valid_till"> (' . tc('is valid till') . ' ' . $issetPaids[$service->id]->date_end . ')</span>' : '';
                        $html .= '</div>';
                    } else {
                        $html = '<div class="paid_row_no"><span class="boldText">' . CHtml::link(
                                $icon ? $service->getImageIcon() : $service->name,
                                array('/paidservices/main/index',
                                    'id' => $this->id,
                                    'paid_id' => $service->id,
                                ),
                                array('class' => 'fancy')) . '</span>';
                        $html .= '</div>';
                    }

                    if (isset($html) && $html) {
                        $htmlArray[] = $html;
                        unset($html);
                    }
                } else { # администратор
                    if (array_key_exists($service->id, $issetPaids) && $withDateEnd) {
                        $html = '<div class="paid_row"><span class="boldText">' . $service->name . '</span>';
                        $html .= $withDateEnd ? '<span class="valid_till"> (' . tc('is valid till') . ' ' . $issetPaids[$service->id]->date_end . ')</span>' : '';
                        $html .= '</div>';
                    }

                    if (isset($html) && $html) {
                        $htmlArray[] = $html;
                        unset($html);
                    }
                }
            }
        }

        if (count($htmlArray) > 0) {
            $content = implode('', $htmlArray);
        } else {
            $content = '<div class="paid_row">' . tc('No') . '</div>';
        }

        if (Yii::app()->user->checkAccess('backend_access') && $withAddLink) {
            $addUrl = Yii::app()->createUrl('/paidservices/backend/main/addPaid', array(
                'id' => $this->id,
                'withDate' => (int)$withDateEnd,
            ));

            $content .= CHtml::link(tc('Add'), $addUrl, array(
                'class' => 'tempModal boldText',
                'title' => tc('Apply a paid service to the listing')
            ));
        }

        return CHtml::tag('div', array('id' => 'paid_row_el_' . $this->id), $content);
    }

    public static function findAllWithCache($criteria)
    {
        return Apartment::model()
            ->cache(param('cachingTime', 1209600), Apartment::getImagesDependency())
            ->with(array('images', 'objType'))
            ->findAll($criteria);
    }

	public function canShowInView($field) {

		switch ($field) {
			case 'floor_all':
				if (!$this->floor && !$this->floor_total) {
					return false;
				}
				break;

			case 'phone':
				if (!$this->phone) {
					if (!isset($this->user) || !$this->user->phone) {
						return false;
					}
				}
				break;

			default:
				if (!isset($this->$field) || !$this->$field) {
					return false;
				}
		}

		if (issetModule('formdesigner')) {
			Yii::import('application.modules.formdesigner.models.*');
			return FormDesigner::canShow($field, $this);
		}

		return true;
	}

	public function canShowInForm($field) {
		if (issetModule('formdesigner')) {
			Yii::import('application.modules.formdesigner.models.*');
			return FormDesigner::canShow($field, $this);
		}
		return true;
	}

	public static function getTip($field) {
		if (issetModule('formdesigner')) {
			Yii::import('application.modules.formdesigner.models.*');
			return FormDesigner::getTipForm($field);
		}
		return '';
	}

	public function isOwner($orAdmin = false) {
		$isOwner = $this->owner_id == Yii::app()->user->id;
		if ($isOwner || ($orAdmin && Yii::app()->user->checkAccess('backend_access'))) {
			return true;
		}
		return false;
	}

	public function getEditUrl() {
		$editUrl = '';

		if (Yii::app()->user->checkAccess('backend_access')) {
			$editUrl = Yii::app()->createUrl('/apartments/backend/main/update', array('id' => $this->id));
		} elseif ($this->owner_id == Yii::app()->user->id && !$this->deleted) {
			$editUrl = Yii::app()->createUrl('/userads/main/update', array('id' => $this->id));
		}
		return $editUrl;
	}

	public static function availableApTypesIds() {
		$return = array();

		if (param('useTypeRentHour', 1) || param('useTypeRentDay', 1) || param('useTypeRentWeek', 1) || param('useTypeRentMonth', 1)) {
			$return[] = self::TYPE_RENT;
		}
		if (param('useTypeSale', 1)) {
			$return[] = self::TYPE_SALE;
		}
		if (param('useTypeRenting', 1)) {
			$return[] = self::TYPE_RENTING;
		}
		if (param('useTypeBuy', 1)) {
			$return[] = self::TYPE_BUY;
		}
		if (param('useTypeChange', 1)) {
			$return[] = self::TYPE_CHANGE;
		}

		return $return;
	}

	public function getAttributeLabel($attribute) {
		if (issetModule('formeditor')) {
			$label = FormDesigner::getLabelForm($attribute);

			return $label ? $label : parent::getAttributeLabel($attribute);
		}

		return parent::getAttributeLabel($attribute);
	}

	public static function returnMainThumbForGrid($data = null) {
		if ($data) {
			$res = Images::getMainThumb(60, 45, $data->images);
			return CHtml::image($res['thumbUrl'], $data->getStrByLang('title'), array(
				'title' => $data->getStrByLang('title'),
				'class' => 'apartment_type_img_small'
			));
		}
	}

	public function setDefaultType() {
		if (param('useTypeRentHour', 1) || param('useTypeRentDay', 1) || param('useTypeRentWeek', 1) || param('useTypeRentMonth', 1)) {
			$this->type = Apartment::TYPE_RENT;
		}
		elseif (param('useTypeSale', 1)) {
			$this->type = Apartment::TYPE_SALE;
		}
		elseif (param('useTypeRenting', 1)) {
			$this->type = Apartment::TYPE_RENTING;
		}
		elseif (param('useTypeBuy', 1)) {
			$this->type = Apartment::TYPE_BUY;
		}
		elseif (param('useTypeChange', 1)) {
			$this->type = Apartment::TYPE_CHANGE;
		}
		else
			$this->type = 0;
	}

	public function canSetPeriodActivity() {
		return $this->activity_always || time() >= strtotime($this->date_end_activity);
	}

	public function getDateEndActivityLongFormat() {
		return Yii::app()->dateFormatter->format(Yii::app()->locale->getDateFormat('long'), CDateTimeParser::parse($this->date_end_activity, 'yyyy-MM-dd'));
	}

	public function writeRating($id, $rating) {
		$sql = 'UPDATE {{apartment}} SET rating=:rating WHERE id=:id';
		Yii::app()->db->createCommand($sql)->execute(array(':rating' => $rating, ':id' => $id));
	}

	public function getSquareString() {
		if ($this->canShowInForm('square')) {
			return $this->square . " " . tc("site_square");
		}

		if ($this->canShowInForm('land_square')) {
			return $this->land_square . " " . tc("site_land_square");
		}

		return '';
	}

	public function getIsNewObject(){
		if($this->oldStatus == self::STATUS_DRAFT && $this->active == self::STATUS_ACTIVE){
			return true;
		} else {
			return false;
		}
	}

	public function getFullTitleApartmentForChangeOwner() {
		return $this->getStrByLang('title'). ' ( ID: '.$this->id.')';
	}
}