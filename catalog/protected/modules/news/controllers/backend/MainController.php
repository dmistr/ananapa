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

class MainController extends ModuleAdminController{
	public $modelName = 'News';

	public function accessRules(){
		return array(
			array('allow',
				'expression'=> "Yii::app()->user->checkAccess('news_admin')",
			),
			array('deny',
				'users'=>array('*'),
			),
		);
	}

	public function actionCreate(){
		$model = new $this->modelName;

		$this->performAjaxValidation($model);

		if(isset($_POST[$this->modelName])){
			$model->newsImage = CUploadedFile::getInstance($model,'newsImage');
			$model->attributes=$_POST[$this->modelName];
			if($model->save()){
				$this->redirect(array('view','id'=>$model->id));
			}
		}

		$this->render('create', array('model'=>$model));
	}

	public function actionUpdate($id){
		$model = $this->loadModel($id);

		$this->performAjaxValidation($model);

		if(isset($_POST[$this->modelName])){
			$model->newsImage = CUploadedFile::getInstance($this->_model,'newsImage');
			$model->attributes=$_POST[$this->modelName];
			if($model->save()){
				$this->redirect(array('view','id'=>$model->id));
			}
		}

		$this->render('update', array('model'=>$model));
	}

    public function actionProduct(){

        //NewsProduct::getProductNews();
        Yii::app()->user->setState('menu_active', 'news.product');

        $model = NewsProduct::model();
      		$result = $model->getAllWithPagination();

      		$this->render('news_product', array(
      			'items' => $result['items'],
      			'pages' => $result['pages'],
      		));
    }

	public function actionDeleteImg() {
		$newsId = Yii::app()->request->getParam('id');
		$imageId = Yii::app()->request->getParam('imId');

		if ($newsId && $imageId) {
			$newsModel = News::model()->findByPk($newsId);
			if ($newsModel->image_id != $imageId)
				throw404();

			$newsModel->image_id = 0;
			$newsModel->update('image_id');

			$imageModel = NewsImage::model()->findByPk($imageId);
			$imageModel->delete();

			$this->redirect(array('/news/backend/main/update', 'id' => $newsId));
		}
		throw404();
	}
}