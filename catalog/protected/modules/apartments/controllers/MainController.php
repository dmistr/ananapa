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
	public $modelName = 'Apartment';

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

	public function actionIndex(){
		throw new CHttpException(404,tc('The requested page does not exist.'));
	}

	public function actionView($id = 0, $url = '', $printable = 0) {
		//$this->showSearchForm = false;
		$this->htmlPageId = 'viewlisting';

		$apartment = NULL;

        $seo = NULL;
		if( ($id || $url) && issetModule('seo') ){
			$url = $url ? $url : $id;
			$seo = SeoFriendlyUrl::getForView($url, $this->modelName);

			if($seo){
				$this->setSeo($seo);
				$id = $seo->model_id;
			}
		}

		if($id) {
			$apartment = Apartment::model()->with(array('windowTo', 'objType', 'city'))->findByPk($id);

            // избавляемся от дублей
            $apartmentUrl = $apartment->getUrl(false);
            if(!$printable && issetModule('seo') && $apartment->seo && Yii::app()->request->url != $apartmentUrl){
                $this->redirect($apartmentUrl, true, 301);
            }
		}

		if(!$apartment){
			throw404();
		}

		if (!in_array($apartment->type, Apartment::availableApTypesIds()) || !in_array($apartment->price_type, array_keys(Apartment::getPriceArray(Apartment::PRICE_SALE, true)))) {
			throw404();
		}

		if( $apartment->owner_id != 1 && $apartment->owner_active == Apartment::STATUS_INACTIVE) {
			if (!(isset(Yii::app()->user->id ) && Yii::app()->user->id == $apartment->owner_id) && !Yii::app()->user->checkAccess('backend_access')) {
				Yii::app()->user->setFlash('notice', tt('apartments_main_index_propertyNotAvailable', 'apartments'));
				throw404();
			}
		}

		if(($apartment->active == Apartment::STATUS_INACTIVE || $apartment->active == Apartment::STATUS_MODERATION)
		&& !Yii::app()->user->checkAccess('backend_access')
		&& !(isset(Yii::app()->user->id ) && Yii::app()->user->id == $apartment->owner_id)){
			Yii::app()->user->setFlash('notice', tt('apartments_main_index_propertyNotAvailable', 'apartments'));
			//$this->redirect(Yii::app()->homeUrl);
			throw404();
		}

		if($apartment->active == Apartment::STATUS_MODERATION && $apartment->owner_active == Apartment::STATUS_ACTIVE && $apartment->owner_id == Yii::app()->user->id){
			Yii::app()->user->setFlash('error', tc('Awaiting moderation'));
		}

		if($apartment->deleted){
			Yii::app()->user->setFlash('error', tt('Listing is deleted', 'apartments'));
		}

		$dateFree = CDateTimeParser::parse($apartment->is_free_to, 'yyyy-MM-dd');
		if($dateFree && $dateFree < (time()-60*60*24)){
			$apartment->is_special_offer = 0;
			$apartment->update(array('is_special_offer'));
		}


		if (!Yii::app()->request->isAjaxRequest) {
			$ipAddress = Yii::app()->request->userHostAddress;
			$userAgent = Yii::app()->request->userAgent;
			Apartment::setApartmentVisitCount($apartment, $ipAddress, $userAgent);
		}

		$lastNews = News::getLastNews();
		$lastArticles = Article::getLastArticles();

		#######################################################################
		# для соц. кнопок
		if ($apartment->getStrByLang("title"))
			Yii::app()->clientScript->registerMetaTag($apartment->getStrByLang("title"),null,null,array('property'=>'og:title'));

		if($apartment->getStrByLang("description"))
			Yii::app()->clientScript->registerMetaTag($apartment->getStrByLang("description"),null,null,array('property'=>'og:description'));

		Yii::app()->clientScript->registerMetaTag($apartment->getUrl(),null,null,array('property'=>'og:url'));

		if (Yii::app()->theme->name == 'atlas')
			$res = Images::getMainThumb(640, 400, $apartment->images);
		else
			$res = Images::getMainThumb(300, 200, $apartment->images);

		if (isset($res['thumbUrl']) && $res['thumbUrl']) {
			Yii::app()->clientScript->registerMetaTag($res['thumbUrl'],null,null,array('property'=>'og:image'));
			Yii::app()->clientScript->registerLinkTag('image_src', null, $res['thumbUrl']);
		}
		#######################################################################

		if ($printable) {
			$this->layout='//layouts/print';
			$this->render('view_print', array(
				'model' => $apartment,
			));
		} else {
			$this->render('view', array(
				'model' => $apartment,
				'statistics' => Apartment::getApartmentVisitCount($apartment),
				'lastNews' => $lastNews,
				'lastArticles' => $lastArticles,
			));
		}
	}

	public function actionGmap($id, $model = null){
		if($model === null){
			$model = $this->loadModel($id);
		}
		$result = CustomGMap::actionGmap($id, $model, $this->renderPartial('//../modules/apartments/views/backend/_marker', array('model' => $model), true), true);

		if($result){
			return $this->renderPartial('backend/_gmap', $result, true);
		}
		return '';
	}

	public function actionYmap($id, $model = null){
		if($model === null){
			$model = $this->loadModel($id);
		}
		$result = CustomYMap::init()->actionYmap($id, $model, $this->renderPartial('//../modules/apartments/views/backend/_marker', array('model' => $model), true));

		if($result){
			//return $this->renderPartial('backend/_ymap', $result, true);
		}
		return '';
	}

	public function actionOSmap($id, $model = null){
		if($model === null){
			$model = $this->loadModel($id);
		}
		$result = CustomOSMap::actionOSmap($id, $model, $this->renderPartial('//../modules/apartments/views/backend/_marker', array('model' => $model), true));

		if($result){
			return $this->renderPartial('backend/_osmap', $result, true);
		}
		return '';
	}

	public function actionGeneratePhone($id = null, $width=130, $font=3) {

		if ($id && param('useShowUserInfo')) {
			$apartmentInfo = Apartment::model()->findByPk($id, array('select' => 'owner_id, phone'));

            $phone = $apartmentInfo->phone;

			if (!$phone && $apartmentInfo->owner_id){
                $userInfo = User::model()->findByPk($apartmentInfo->owner_id, array('select' => 'phone'));
                $phone = $userInfo->phone;
            }

			if ($phone) {
				$image = imagecreate($width, 20);

				//$font = Yii::app()->theme->name != 'classic' ? 4 : $font;
				imagecolorallocate($image, 255, 255, 255);
				$textcolor = imagecolorallocate($image, 37, 75, 137); //Yii::getPathOfAlias('webroot.protected.modules.apartments.font').'/tahoma.ttf'

				imagettftext($image, 11, 0, 0, 14, $textcolor, Yii::getPathOfAlias('webroot.protected.modules.apartments.font').'/tahoma.ttf', $phone);

				header('Pragma: public');
				header('Expires: 0');
				header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
				header('Content-Transfer-Encoding: binary');
				header("Content-type: image/png");
				imagepng($image);
				//echo $image;
				imagedestroy($image);
			}
		}
	}

	public function actionAllListings() {
		$userId = (int) Yii::app()->request->getParam('id');
		if ($userId) {
			$this->userListingId = $userId;

			$data = HUser::getDataForListings($userId);

			// find count
			$apCount = Apartment::model()->count($data['criteria']);

			if(Yii::app()->request->isAjaxRequest){
				$this->renderPartial('_user_listings', array(
					'criteria' => $data['criteria'],
					'apCount' => $apCount,
					'username' => $data['userName'],
				), false, true);
			}else{
				$this->render('_user_listings', array(
					'criteria' => $data['criteria'],
					'apCount' => $apCount,
					'username' => $data['userName'],
				));
			}
		}
	}

	public function actionSendEmail($id, $isFancy = 0){
		$apartment = Apartment::model()->findByPk($id);

		if (!$apartment) {
			throw404();
		}

		if (!param('use_module_request_property'))
			throw404();

		$model = new SendMailForm;

		if(isset($_POST['SendMailForm'])){
			$model->attributes = $_POST['SendMailForm'];

			if(!Yii::app()->user->isGuest){
				$model->senderEmail = Yii::app()->user->email;
				$model->senderName = Yii::app()->user->username;
			}

			$model->ownerId = $apartment->user->id;
			$model->ownerEmail = $apartment->user->email;
			$model->ownerName = $apartment->user->username;

			$model->apartmentUrl = $apartment->getUrl();

			if($model->validate()){
				$notifier = new Notifier;
				$notifier->raiseEvent('onRequestProperty', $model, array('forceEmail' => $model->ownerEmail));

				Yii::app()->user->setFlash('success', tt('Thanks_for_request', 'apartments'));
				$model = new SendMailForm; // clear fields
			} else {
				$model->unsetAttributes(array('verifyCode'));
				Yii::app()->user->setFlash('error', tt('Error_send_request', 'apartments'));
			}
		}

		if($isFancy){
			//Yii::app()->clientscript->scriptMap['*.js'] = false;
			Yii::app()->clientscript->scriptMap['jquery.js'] = false;
			Yii::app()->clientscript->scriptMap['jquery.min.js'] = false;
			Yii::app()->clientscript->scriptMap['jquery-ui.min.js'] = false;

			$this->renderPartial('send_email', array(
				'apartment' => $apartment,
				'isFancy' => true,
				'model' => $model,
			), false, true);
		}
		else{
			$this->render('send_email', array(
				'apartment' => $apartment,
				'isFancy' => false,
				'model' => $model,
			));
		}
	}

	public function actionSavecoords($id){
		if(param('useGoogleMap', 1) || param('useYandexMap', 1) || param('useOSMMap', 1)){
			$apartment = $this->loadModel($id);
			if(isset($_POST['lat']) && isset($_POST['lng'])){
				$apartment->lat = $_POST['lat'];
				$apartment->lng = $_POST['lng'];
				$apartment->save(false);
			}
			Yii::app()->end();
		}
	}

	public function actionGetVideoFile() {
		$id = Yii::app()->request->getParam('id');
		$apId = Yii::app()->request->getParam('apId');

		if ($id && $apId) {
			$sql = 'SELECT video_file, video_html
					FROM {{apartment_video}}
					WHERE id = "'.$id.'"
					AND apartment_id = "'.$apId.'"';

			$result = Yii::app()->db->createCommand($sql)->queryRow();

			if ($result['video_file']) {
				$this->renderPartial('_video_file',
					array(
						'video'=>$result['video_file'],
						'apartment_id' => $apId,
						'id' => $id,
					), false, true
				);
			}
			elseif ($result['video_html']) {
				echo CHtml::decode($result['video_html']);
			}
		}
	}
}