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
	public $modelName = 'News';
	public $showSearchForm = false;

	public function actionIndex(){
		$newsPage = Menu::model()->findByPk(Menu::NEWS_ID);
		if ($newsPage) {
			if ($newsPage->active == 0) {
				throw404();
			}
		}

		$model = new $this->modelName;
		$result = $model->getAllWithPagination();

		$this->render('index', array(
			'items' => $result['items'],
			'pages' => $result['pages'],
		));
	}
}