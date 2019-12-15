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

class MainController extends ModuleUserController{
	public $modelName = 'InfoPages';
	public function actions() {
		$return = array();
		if (param('useJQuerySimpleCaptcha', 0)) {
			$return['captcha'] = array(
				'class' => 'jQuerySimpleCCaptchaAction',
				'backColor' => 0xFFFFFF,
			);
		}
		else {
			$return['captcha'] = array(
				'class' => 'MathCCaptchaAction',
				'backColor' => 0xFFFFFF,
		);
	}

		return $return;
	}
	public function filters() {
		return array(
			'accessControl', // perform access control for CRUD operations
			array(
				'ESetReturnUrlFilter + index, view, create, update, bookingform, complain, mainform, add, edit',
			),
		);
	}

	public function accessRules(){
		return array(
			array(
				'allow',
				'actions' => array('view', 'captcha'),
				'users'=>array('*'),
			),
			array('deny',
				'users' => array('*'),
			),
		);
	}

	public function actionView($id = 0, $url = ''){
		if($url && issetModule('seo')){
			$seo = SeoFriendlyUrl::getForView($url, $this->modelName);

			if(!$seo){
				throw404();
			}

			$this->setSeo($seo);

			$id = $seo->model_id;
		}

		$model = $this->loadModel($id, 1);

		if (!$model->active)
			throw404();

		if($model->id == 4) { //User Agreement
			$field = 'body_'.Yii::app()->language;
			$model->$field = str_replace('{site_domain}', IdnaConvert::checkDecode(Yii::app()->getBaseUrl(true)), $model->$field);
			$model->$field = str_replace('{site_title}', CHtml::encode(Yii::app()->name), $model->$field);
		}

		$this->showSearchForm = ($model->widget && $model->widget == 'apartments') ? true : false;

		if(Yii::app()->request->isAjaxRequest) {
			$this->renderPartial('view', array(
				'model'=>$model,
			));
		}
		else {
			$this->render('view',array(
				'model'=>$model,
			));
		}
	}

}