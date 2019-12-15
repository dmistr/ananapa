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

class ModuleUserController extends Controller{
	public $metroStations;
	public $userListingId;

	public $cityActive;

	public $layout='//layouts/inner';
	public $params = array();
	private $_model;
	public $modelName;

	public function getViewPath($checkTheme=true){
		if($checkTheme && ($theme=Yii::app()->getTheme())!==null){
			if (is_dir($theme->getViewPath().DIRECTORY_SEPARATOR.'modules'.DIRECTORY_SEPARATOR.$this->getModule($this->id)->getName().DIRECTORY_SEPARATOR.'views'))
				return $theme->getViewPath().DIRECTORY_SEPARATOR.'modules'.DIRECTORY_SEPARATOR.$this->getModule($this->id)->getName().DIRECTORY_SEPARATOR.'views';
		}
		return Yii::getPathOfAlias('application.modules.'.$this->getModule($this->id)->getName().'.views');
	}

	public function beginWidget($className,$properties=array()){
		if($className == 'CustomForm'){
			$className = 'CActiveForm';
		}
		if($className == 'CustomGridView'){
			$className = 'CGridView';
		}
		return parent::beginWidget($className,$properties);
	}

	public function widget($className,$properties=array(),$captureOutput=false){
		if($className == 'bootstrap.widgets.TbButton'){
			if(isset($properties['htmlOptions'])){
				return CHtml::submitButton($properties['label'], $properties['htmlOptions']);
			} else {
				return CHtml::submitButton($properties['label']);
			}
		}

	    return parent::widget($className,$properties,$captureOutput);
	}

	public function filters(){
		return array(
			'accessControl', // perform access control for CRUD operations
			array(
				'ESetReturnUrlFilter + index, view, create, update, bookingform, complain, mainform, add, edit',
			),
		);
	}

	public function accessRules(){
		return array(
			array('allow',
				'roles'=>array('guest'),
			),
		);
	}

	public function init(){
		parent::init();
		//$this->metroStations = SearchForm::stationsInit();
		$this->cityActive = SearchForm::cityInit();
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

		$this->render('view',array(
			'model'=>$model,
		));
	}

	public function actionIndex(){
		$dataProvider=new CActiveDataProvider($this->modelName);
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
	}

	public function loadModel($id = null, $resetScope = 0) {
		if($this->_model===null) {
			if($id == null){
				if(isset($_GET['id'])) {
					$model = new $this->modelName;
					if($resetScope){
						$this->_model=$model->resetScope()->findByPk($_GET['id']);
					}else{
						$this->_model=$model->findByPk($_GET['id']);
					}
				}
			}
			else{
				$model = new $this->modelName;
				if($resetScope){
					$this->_model=$model->resetScope()->findByPk($id);
				}else{
					$this->_model=$model->findByPk($id);
				}
			}

			if($this->_model===null){
				throw new CHttpException(404,tc('The requested page does not exist.'));
			}
		}
		return $this->_model;
	}

	public function loadModelWith($with) {
		if($this->_model===null) {
			if(isset($_GET['id'])) {
				$model = new $this->modelName;
				$this->_model = $model->with($with)->findByPk($_GET['id']); //findByPk($_GET['id']);
			}
			if($this->_model===null){
				throw new CHttpException(404,tc('The requested page does not exist.'));
			}
		}
		return $this->_model;
	}

	protected function afterRender($view, &$output) {
		eval(base64_decode('aWYgKGlzRnJlZSgpKSB7CgkkdXJsID0gJ2h0dHA6Ly9vcGVuLXJlYWwtZXN0YXRlLmluZm8vZW4vJzsKCSR0ZXh0ID0gJ1Bvd2VyZWQgYnknOwoJaWYgKFlpaTo6YXBwKCktPmxhbmd1YWdlID09ICdydScgfHwgWWlpOjphcHAoKS0+bGFuZ3VhZ2UgPT0gJ3VrJykgewoJCSR1cmwgPSAnaHR0cDovL29wZW4tcmVhbC1lc3RhdGUuaW5mby9ydS8nOwoJCSR0ZXh0ID0gJ9Cg0LDQsdC+0YLQsNC10YIg0L3QsCc7Cgl9CgoJaWYgKFlpaTo6YXBwKCktPnRoZW1lICYmIGlzc2V0KFlpaTo6YXBwKCktPnRoZW1lLT5uYW1lKSAmJiBZaWk6OmFwcCgpLT50aGVtZS0+bmFtZSA9PSAnYXRsYXMnKSB7CgkJcHJlZ19tYXRjaF9hbGwgKCcjPGRpdiBjbGFzcz0iY29weXJpZ2h0Ij4oLiopPC9kaXY+I2lzVScsICRvdXRwdXQsICRtYXRjaGVzICk7CgkJaWYgKCBpc3NldCggJG1hdGNoZXNbMV1bMF0gKSAmJiAhZW1wdHkoICRtYXRjaGVzWzFdWzBdICkgKSB7CgkJCSRpbnNlcnQ9JzxwIHN0eWxlPSJmbG9hdDogbGVmdDsgbWFyZ2luOiAyN3B4IDAgMCAxNXB4OyBwYWRkaW5nOiAwOyBjb2xvcjogI0ZGRjsiPicuJHRleHQuJyA8YSBocmVmPSInLiR1cmwuJyIgdGFyZ2V0PSJfYmxhbmsiPk9wZW4gUmVhbCBFc3RhdGU8L2E+PC9wPic7CgkJCSRvdXRwdXQ9c3RyX3JlcGxhY2UoJG1hdGNoZXNbMF1bMF0sICRtYXRjaGVzWzBdWzBdLiRpbnNlcnQsICRvdXRwdXQpOwoJCX0KCQllbHNlIHsKCQkJJGluc2VydD0nPGRpdiBpZD0iZm9vdGVyIj48ZGl2IGNsYXNzPSJ3cmFwcGVyIj48ZGl2IGNsYXNzPSJjb3B5cmlnaHQiPiZjb3B5OyZuYnNwOycuQ0h0bWw6OmVuY29kZShZaWk6OmFwcCgpLT5uYW1lKS4nLCAnLmRhdGUoJ1knKTsnPHAgc3R5bGU9ImZsb2F0OiBsZWZ0OyBtYXJnaW46IDI3cHggMCAwIDE1cHg7IHBhZGRpbmc6IDA7IGNvbG9yOiAjRkZGOyI+Jy4kdGV4dC4nIDxhIGhyZWY9IicuJHVybC4nIiB0YXJnZXQ9Il9ibGFuayI+T3BlbiBSZWFsIEVzdGF0ZTwvYT48L3A+PC9kaXY+PC9kaXY+PC9kaXY+PC9kaXY+JzsKCQkJJG91dHB1dD1zdHJfcmVwbGFjZSgnPGRpdiBpZD0ibG9hZGluZyInLCAkaW5zZXJ0Lic8ZGl2IGlkPSJsb2FkaW5nIicsICRvdXRwdXQpOwoJCX0KCX0KCWVsc2UgewoJCXByZWdfbWF0Y2hfYWxsICgnIzxwIGNsYXNzPSJzbG9nYW4iPiguKik8L3A+I2lzVScsICRvdXRwdXQsICRtYXRjaGVzICk7CgkJaWYgKCBpc3NldCggJG1hdGNoZXNbMV1bMF0gKSAmJiAhZW1wdHkoICRtYXRjaGVzWzFdWzBdICkgKSB7CgkJCSRpbnNlcnQ9JzxwIHN0eWxlPSJ0ZXh0LWFsaWduOiBjZW50ZXI7IG1hcmdpbjogMDsgcGFkZGluZzogMDsiPicuJHRleHQuJyA8YSBocmVmPSInLiR1cmwuJyIgdGFyZ2V0PSJfYmxhbmsiPk9wZW4gUmVhbCBFc3RhdGU8L2E+PC9wPic7CgkJCSRvdXRwdXQ9c3RyX3JlcGxhY2UoJG1hdGNoZXNbMF1bMF0sICRtYXRjaGVzWzBdWzBdLiRpbnNlcnQsICRvdXRwdXQpOwoJCX0KCQllbHNlIHsKCQkJJGluc2VydD0nPGRpdiBjbGFzcz0iZm9vdGVyIj48cCBzdHlsZT0idGV4dC1hbGlnbjogY2VudGVyOyBtYXJnaW46IDA7IHBhZGRpbmc6IDA7Ij4nLiR0ZXh0LicgPGEgaHJlZj0iJy4kdXJsLiciIHRhcmdldD0iX2JsYW5rIj5PcGVuIFJlYWwgRXN0YXRlPC9hPjwvcD48L3A+PC9kaXY+JzsKCQkJJG91dHB1dD1zdHJfcmVwbGFjZSgnPGRpdiBpZD0ibG9hZGluZyInLCAkaW5zZXJ0Lic8ZGl2IGlkPSJsb2FkaW5nIicsICRvdXRwdXQpOwoJCX0KCX0KCXVuc2V0KCR1cmwpOwoJdW5zZXQoJHRleHQpOwoJdW5zZXQoJG1hdGNoZXMpOwoJdW5zZXQoJGluc2VydCk7Cn0='));
	}


	protected function performAjaxValidation($model){
		if(isset($_POST['ajax']) && $_POST['ajax']===$this->modelName.'-form'){
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}

	public function sliderImages(){
		$dependency = new CDbCacheDependency('SELECT MAX(date_updated) FROM {{slider}}');
		$sql = 'SELECT url FROM {{slider}} ORDER BY sorter';
		$items = Yii::app()->db->cache(param('cachingTime', 1209600), $dependency)->createCommand($sql)->queryColumn();
		return $this->renderPartial('_slider_image', array('items' => $items), true);
	}
}