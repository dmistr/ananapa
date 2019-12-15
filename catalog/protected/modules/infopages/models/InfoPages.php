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

class InfoPages extends ParentModel {
	const STATUS_INACTIVE = 0;
	const STATUS_ACTIVE = 1;

	const MAIN_PAGE_ID = 1;

    const POSITION_BOTTOM = 1;
    const POSITION_TOP = 2;

    public static function getPositionList(){
        return array(
            self::POSITION_BOTTOM => tt('Bottom', 'infopages'),
            self::POSITION_TOP => tt('Top', 'infopages'),
        );
    }

	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	public function tableName() {
		return '{{infopages}}';
	}

	public function rules() {
		return array(
			array('title', 'i18nRequired'),
			array('title', 'i18nLength', 'max' => 255),
			array('active, widget, widget_data, widget_position', 'safe'),
			array($this->getI18nFieldSafe(), 'safe'),
			array('active', 'safe', 'on' => 'search'),
		);
	}

	public function relations(){
		return array(
			'menuPage' => array(self::HAS_MANY, 'Menu', 'pageId'),
			'menuPageOne' => array(self::HAS_ONE, 'Menu', 'pageId'),
		);
	}

	public function i18nFields(){
		return array(
			'title' => 'varchar(255) not null',
			'body' => 'text not null',
		);
	}

	public function seoFields() {
		return array(
			'fieldTitle' => 'title',
			'fieldDescription' => 'body'
		);
	}

	public function behaviors(){
		return array(
			'AutoTimestampBehavior' => array(
				'class' => 'zii.behaviors.CTimestampBehavior',
				'createAttribute' => 'date_created',
				'updateAttribute' => 'date_updated',
			),
		);
	}

	public function attributeLabels() {
		return array(
			'id' => 'ID',
			'active' => tc('Status'),
			'title' => tt('Page title'),
			'body' => tt('Page body'),
			'date_created' => tt('Creation date'),
			'widget' => tt('Widget', 'infopages'),
			'widget_position' => tt("Widget's position", 'infopages'),
		);
	}

	public function getUrl() {
		if(issetModule('seo') && param('genFirendlyUrl')){
			$seo = SeoFriendlyUrl::getForUrl($this->id, 'InfoPages');

			if($seo){
				$field = 'url_'.Yii::app()->language;
				if($seo->$field) {
					if($seo->direct_url){
						return Yii::app()->getBaseUrl(true) . '/' . $seo->$field . ( param('urlExtension') ? '.html' : '' );
					}
					return Yii::app()->createAbsoluteUrl('/infopages/main/view', array(
						'url' => $seo->$field . ( param('urlExtension') ? '.html' : '' ),
					));
				}
			}
		}

		return Yii::app()->createAbsoluteUrl('/infopages/main/view', array(
			'id' => $this->id,
		));
	}

	public static function getUrlById($id) {
		if(issetModule('seo') && param('genFirendlyUrl')){
			$seo = SeoFriendlyUrl::getForUrl($id, 'InfoPages');

			if($seo){
				$field = 'url_'.Yii::app()->language;
				if($seo->$field){
					if($seo->direct_url){
						return Yii::app()->getBaseUrl(true) . '/' . $seo->$field . ( param('urlExtension') ? '.html' : '' );
					}
					return Yii::app()->createAbsoluteUrl('/infopages/main/view', array(
						'url' => $seo->$field . ( param('urlExtension') ? '.html' : '' ),
					));
				}
			}
		}

		return Yii::app()->createAbsoluteUrl('/infopages/main/view', array(
			'id' => $id,
		));
	}

	public function search() {
		$criteria = new CDbCriteria;

        $titleField = 'title_'.Yii::app()->language;
		$criteria->compare($titleField, $this->$titleField, true);
        $bodyField = 'body_'.Yii::app()->language;
		$criteria->compare($bodyField, $this->$bodyField, true);

		$criteria->compare($this->getTableAlias().'.active', $this->active, true);

		return new CActiveDataProvider($this, array(
			'criteria' => $criteria,
			'sort' => array(
				'defaultOrder' => 'id DESC',
			),
			'pagination' => array(
				'pageSize' => param('adminPaginationPageSize', 20),
			),
		));
	}

	public static function getWidgetOptions($widget = null){
		$arrWidgets =  array(
			'' => tc('No'),
			'news' => tc('News'),
			'apartments' => tc('Listing'),
			'viewallonmap' => tc('Search for listings on the map'),
			'contactform' => tc('The form of the section "Contact Us"'),
			'randomapartments' => tc('Listing (random)'),
			'specialoffers' => tc('Special offers'),
		);

		if ($widget && array_key_exists($widget, $arrWidgets))
			return $arrWidgets[$widget];

		return $arrWidgets;
	}

	public static function getInfoPagesAddList() {
		$return = array();
		$result = InfoPages::model()->findAll('active = '.self::STATUS_ACTIVE);
		if ($result) {
			foreach($result as $item) {
				$return[$item->id] = $item->getStrByLang('title');
			}
		}

		return $return;
	}

	public function getTitle(){
		$return = CHtml::encode($this->getStrByLang('title'));

		if (Yii::app()->user->checkAccess('backend_access')) {
			$href = array();
			switch ($this->id) {
				case 2:
					$href = array('/news/backend/main/admin');
					break;
				case 4:
					$href = array('/articles/backend/main/admin');
					break;
			}
			if($href){
				$return .= ' ['.CHtml::link('Управление разделом', $href).']';
			}
		}

		return $return;
	}

	public function getBody(){
		return $this->getStrByLang('body');
	}

	public function beforeSave(){
        if($this->widget == 'apartments' && isset($_POST['filter'])){
            $this->widget_data = CJSON::encode($_POST['filter']);
        }

		return parent::beforeSave();
	}


	public function afterSave() {
		if(issetModule('seo') && param('genFirendlyUrl')){
			SeoFriendlyUrl::getAndCreateForModel($this);
		}
		return parent::afterSave();
	}

	public function beforeDelete() {
		if(issetModule('seo') && param('genFirendlyUrl')){
			$sql = 'DELETE FROM {{seo_friendly_url}} WHERE model_id="'.$this->id.'" AND model_name = "InfoPages"';
			Yii::app()->db->createCommand($sql)->execute();
		}

		return parent::beforeDelete();
	}

	private $_filter;

	public function getCriteriaForAdList(){
		$criteria = new CDbCriteria();
		if($this->widget_data){
			$this->_filter = CJSON::decode($this->widget_data);

			if(issetModule('location')){
				$this->setForCriteria($criteria, 'country_id', 'loc_country');
				$this->setForCriteria($criteria, 'region_id', 'loc_region');
				$this->setForCriteria($criteria, 'city_id', 'loc_city');

				if(isset($this->_filter['country_id']) && $this->_filter['country_id'])
					Yii::app()->controller->selectedCountry = $this->_filter['country_id'];
				if(isset($this->_filter['region_id']) && $this->_filter['region_id'])
					Yii::app()->controller->selectedRegion = $this->_filter['region_id'];
				if(isset($this->_filter['country_id']) && $this->_filter['country_id'])
					Yii::app()->controller->selectedCity = $this->_filter['city_id'];
			} else {
				$this->setForCriteria($criteria, 'city_id', 'city_id');

				if(isset($this->_filter['city_id']) && $this->_filter['city_id'])
					Yii::app()->controller->selectedCity = $this->_filter['city_id'];
			}

			$this->setForCriteria($criteria, 'type', 't.price_type');
			$this->setForCriteria($criteria, 'obj_type_id', 't.obj_type_id');
			if (!(issetModule('selecttoslider') && param('useRoomSlider') == 1))
				$this->setForCriteria($criteria, 'rooms', 't.num_of_rooms');

			if(isset($this->_filter['type']) && $this->_filter['type'])
				Yii::app()->controller->apType = $this->_filter['type'];
			if(isset($this->_filter['obj_type_id']) && $this->_filter['obj_type_id'])
				Yii::app()->controller->objType = $this->_filter['obj_type_id'];
			if(isset($this->_filter['rooms']) && $this->_filter['rooms'])
				Yii::app()->controller->roomsCount = $this->_filter['rooms'];

			# new fields
			$newFieldsAll = InfoPages::getAddedFields();
			if ($newFieldsAll) {
				foreach($newFieldsAll as $field) {
					$this->setForCriteria($criteria, $field['field'], 't.'.$field['field'], true, $field);

					if(isset($this->_filter[$field['field']]) && $this->_filter[$field['field']])
						Yii::app()->controller->newFields[$field['field']] = $this->_filter[$field['field']];
				}
			}
		}

		//deb($criteria);

		return $criteria;
	}

	private function setForCriteria($criteria, $key, $field, $isNewField = false, $newFieldArr = array()){
		if(isset($this->_filter[$key]) && $this->_filter[$key]){
			if ($isNewField && count($newFieldArr)) {
				switch($newFieldArr['compare_type']){
					case FormDesigner::COMPARE_EQUAL:
						$criteria->compare($field, $this->_filter[$key]);
						break;

					case FormDesigner::COMPARE_LIKE:
						$criteria->compare($field, $this->_filter[$key], true);
						break;

					case FormDesigner::COMPARE_FROM:
						$value = intval($this->_filter[$key]);
						$criteria->compare($field, ">={$value}");
						break;

					case FormDesigner::COMPARE_TO:
						$value = intval($this->_filter[$key]);
						$criteria->compare($field, "<={$value}");
						break;
				}
			}
			else {
				if ($key == 'rooms') {
					if($this->_filter[$key] == 4) {
						$criteria->addCondition($field.' >= '.$this->_filter[$key]);
					} else {
						$criteria->addCondition($field.' = '.$this->_filter[$key]);
					}
				}
				else {
					$criteria->compare($field, $this->_filter[$key]);
				}
			}
		}
	}

	public static function getAddedFields() {
		$addedFields = null;

		if (issetModule('formdesigner')) {
			$newFieldsAll = FormDesigner::getNewFields();
			if ($newFieldsAll && count($newFieldsAll)) {
				foreach($newFieldsAll as $key => $field){
					$addedFields[$key]['field'] = $field->field;
					$addedFields[$key]['type'] = $field->type;
					$addedFields[$key]['compare_type'] = $field->compare_type;
					$addedFields[$key]['label'] = $field->getStrByLang('label');

					if ($field->type == FormDesigner::TYPE_REFERENCE) {
						$addedFields[$key]['listData'] = FormDesigner::getListByCategoryID($field->reference_id);
					}
				}
			}
		}

		return $addedFields;
	}
}