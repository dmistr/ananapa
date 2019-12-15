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
    public $layout='//layouts/usercpanel';
	public $htmlPageId = 'userads';

	public $modelName = 'UserAds';
	public $photoUpload = false;

	public function init() {
		// если админ - делаем редирект на просмотр в админку
		if(Yii::app()->user->checkAccess('apartments_admin')){
			$this->redirect($this->createAbsoluteUrl('/apartments/backend/main/admin'));
		}
		if (!param('useUserads')) {
			throw404();
		}
		parent::init();
	}

	public function accessRules(){
		return array(
			array(
				'allow',
                'expression' => 'param("useUserads") && Yii::app()->user->checkAccess("registered")',
			),
			array(
				'deny',
				'users'=>array('*'),
			),
		);
	}

	public function actionIndex(){
		$model = new $this->modelName('search');

        Yii::app()->user->setState('searchUrl', NULL);

		$model->unsetAttributes();  // clear any default values
		if(isset($_GET[$this->modelName])){
			$model->attributes = $_GET[$this->modelName];
		}

        if(Yii::app()->request->isAjaxRequest){
            $this->renderPartial('index',array(
                'model'=>$model,
            ), false, true);
        } else {
            $this->render('index',array(
                'model'=>$model,
            ));
        }
	}

	public function actionActivate(){

		if(isset($_GET['id']) && isset($_GET['action'])){
			$action = Yii::app()->request->getQuery('action');;
			$model = $this->loadModelUserAd($_GET['id']);
            $model->scenario = 'update_status';

			if($model){
				if (issetModule('tariffPlans') && issetModule('paidservices') && $action == 'activate') {
					TariffPlans::checkAllowUserActivateAd(Yii::app()->user->id, false, '>=');
				}

				$model->owner_active = ($action == 'activate'?1:0);
				$model->update(array('owner_active'));
			}
		}
		if(!Yii::app()->request->isAjaxRequest){
			$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('index'));
		}
	}


	public function actionCreate(){
		$this->setActiveMenu('add_ad');

		$this->modelName = 'Apartment';
		$model = new $this->modelName;

		$user = User::model()->findByPk(Yii::app()->user->id);
		if (preg_match("/null\.io/i", $user->email)) {
			Yii::app()->user->setFlash('error', tt('You can not add listings till you specify your valid email.', 'socialauth'));
			$this->redirect(array('/usercpanel/main/index', 'from' => 'userads'));
		}
		elseif (!$user->phone) {
			Yii::app()->user->setFlash('error', tt('You can not add listings till you specify your phone number.', 'socialauth'));
			$this->redirect(array('/usercpanel/main/index', 'from' => 'userads'));
		}


		$model->active = Apartment::STATUS_DRAFT;
		$model->owner_active = Apartment::STATUS_ACTIVE;

		if (issetModule('tariffPlans') && issetModule('paidservices')) {
			$return = TariffPlans::checkAllowUserActivateAd(Yii::app()->user->id, true, '>=');

			if ($return === false) {
				$model->owner_active = Apartment::STATUS_INACTIVE;
			}
		}

		$model->setDefaultType();
		$model->save(false);

		$this->redirect(array('update', 'id' => $model->id));
	}

	public function loadModelUserAd($id) {
		$model = $this->loadModel($id);
		if($model->owner_id != Yii::app()->user->id || $model->deleted){
			throw404();
		}
		return $model;
	}

	public function actionUpdate($id){
		$this->setActiveMenu('add_ad');

		$model = $this->loadModelUserAd($id);
		if(issetModule('bookingcalendar')) {
			$model = $model->with(array('bookingCalendar'));
		}

		$this->performAjaxValidation($model);

		if(isset($_GET['type'])){
			$model->type = HApartment::getRequestType();
		}

		if(isset($_POST[$this->modelName])){
			$originalActive = $model->active;
			$model->attributes=$_POST[$this->modelName];

			if ($model->type != Apartment::TYPE_BUY && $model->type != Apartment::TYPE_RENTING) {
				// video, panorama, lat, lon
				HApartment::saveOther($model);
			}

			$model->scenario = 'savecat';
			$model->owner_active = Apartment::STATUS_ACTIVE;

			if (issetModule('tariffPlans') && issetModule('paidservices')) {
				$return = TariffPlans::checkAllowUserActivateAd(Yii::app()->user->id, true, '>=');

				if ($return === false) {
					$model->owner_active = Apartment::STATUS_INACTIVE;
				}
			}

			$isUpdate = Yii::app()->request->getPost('is_update');
			$model->isAjaxLoadOnUpdate = $isUpdate;

			if($isUpdate){
				$model->save(false);
			}
			elseif($model->validate()) {
				if(param('useUseradsModeration', 1)){
					$model->active = Apartment::STATUS_MODERATION;
				}
				else {
					$model->active = Apartment::STATUS_ACTIVE;
				}

				if($model->save(false)){
					if ($model->owner_active == Apartment::STATUS_INACTIVE)
						$this->redirect(array('/usercpanel/main/index'));
					else
						$this->redirect(array('/apartments/main/view','id'=>$model->id));
				}
			}
			else {
				$model->active = $originalActive;
			}
		}

		HApartment::getCategoriesForUpdate($model);

		if($model->active == Apartment::STATUS_DRAFT){
			Yii::app()->user->setState('menu_active', 'apartments.create');
			$this->render('create', array(
				'model' => $model,
				'supportvideoext' => ApartmentVideo::model()->supportExt,
				'supportvideomaxsize' => ApartmentVideo::model()->fileMaxSize,
			));
			return;
		}

		$this->render('update',
			array(
				'model'=>$model,
				'supportvideoext' => ApartmentVideo::model()->supportExt,
				'supportvideomaxsize' => ApartmentVideo::model()->fileMaxSize,
			)
		);
	}

	public function actionDelete($id){
		if(Yii::app()->request->isPostRequest){
			// we only allow deletion via POST request
			$this->loadModelUserAd($id)->delete();
			// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
			if(!isset($_GET['ajax']))
				$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('index'));
		}
		else
			throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
	}

	public function actionClone($id) {
		if (param("enableUserAdsCopy",0)) {
			$this->loadModelUserAd($id)->makeClone();
			// if AJAX request (triggered by deletion via grid view), we should not redirect the browser
			if (!Yii::app()->request->isAjaxRequest) {
				$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('index'));
			}
		} else
			throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
	}

	public function actionGmap($id){
		$model = $this->loadModelUserAd($id);

		$result = CustomGMap::actionGmap($id, $model, $this->renderPartial('//../modules/apartments/views/backend/_marker', array('model' => $model), true));
		if($result){
			return $this->renderPartial('//../modules/apartments/views/backend/_gmap', $result, true);
		}
	}

	public function actionYmap($id){
		$model = $this->loadModelUserAd($id);

		$result = CustomYMap::init()->actionYmap($id, $model, $this->renderPartial('//../modules/apartments/views/backend/_marker', array('model' => $model), true));
		if($result){
			return $this->renderPartial('//../modules/apartments/views/backend/_ymap', $result, true);
		}
	}

	public function actionOSmap($id){
		$model = $this->loadModelUserAd($id);

		$result = CustomOSMap::actionOsmap($id, $model, $this->renderPartial('//../modules/apartments/views/backend/_marker', array('model' => $model), true));
		if($result){
			return $this->renderPartial('//../modules/apartments/views/backend/_osmap', $result, true);
		}
	}

	public function actionSavecoords($id){
		if(param('useGoogleMap', 1) || param('useYandexMap', 1) || param('useOSMMap', 1)){
			$apartment = $this->loadModelUserAd($id);
			if(isset($_POST['lat']) && isset($_POST['lng'])){
				$apartment->lat = floatval($_POST['lat']);
				$apartment->lng = floatval($_POST['lng']);
				$apartment->update(array('lat', 'lng'));
			}
			Yii::app()->end();
		}
	}

	public function actionView($id = 0, $url = ''){
		$this->redirect(array('/apartments/main/view', 'id' => $id));
	}
}