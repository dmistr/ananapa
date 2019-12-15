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
	public $modelName = 'Lang';

	public function accessRules(){
		return array(
			array('allow',
				'expression'=> "Yii::app()->user->checkAccess('all_lang_and_currency_admin')",
			),
			array('deny',
				'users'=>array('*'),
			),
		);
	}

    public function actionAdmin(){
   		$this->getMaxSorter();
		$this->getMinSorter();

		Yii::app()->user->setFlash('warning', Yii::t('module_lang','moduleAdminHelp',
			array('{link}'=>CHtml::link(tc('Currency'), array('/currency/backend/main/admin'))))
		);

   		parent::actionAdmin();
   	}

    public function actionIndex(){
        $this->redirect('admin');
    }

    public function actionView($id){
        $this->redirect('admin');
    }
/*
	public function actionCompare(){
		$sql = 'SELECT * FROM {{translate_message}} WHERE translation_de = ""';
		$result = Yii::app()->db->createCommand($sql)->queryAll();

		foreach ($result as $key=>$value) {
			$sql1 = 'SELECT translation_de FROM {{translate_message_de}} WHERE category = :category AND message = :message';
			$result1 = Yii::app()->db->createCommand($sql1)->queryScalar(array(':category'=>$value['category'], ':message'=>$value['message']));
			$result[$key]['translation_de'] = $result1;
			if (!$result1) {
				echo "<pre>";
				print_r($result[$key]);
				echo "</pre>";
			} else {
				$sql = 'UPDATE {{translate_message}} SET translation_de = :translation_de WHERE id="'.$value['id'].'"';
				Yii::app()->db->createCommand($sql)->execute(array(':translation_de' => $result1));
			}

		}


	}
*/
	public function actionSetDefault(){
        $id = (int) Yii::app()->request->getPost('id');
		$admin_mail = (int) Yii::app()->request->getPost('admin_mail');
        $model = Lang::model()->findByPk($id);
        $model->setDefault($admin_mail);

        Yii::app()->end();
    }

	public function actionActivate(){
        $id = (int) $_GET['id'];
        $action = $_GET['action'];
        if($id){
            $model = Lang::model()->findByPk($id);
            if(($model->main == 1 || $model->admin_mail == 1) && $action != 'activate'){
                Yii::app()->end();
            }
        }
        parent::actionActivate();
    }


    public function actionTest(){
        return false;

        @ini_set('max_execution_time', 3600);

        $sql = 'SELECT id, message, translation_en FROM {{translate_message}} WHERE status=0 OR status=1';
        $messages = Yii::app()->db->createCommand($sql)->queryAll();

        //$messages = array('The required fields are marked with an asterisk (<span class="required">*</span>).');

        $options = array(
            'fileTypes' => array('php'),
        );
        $files = CFileHelper::findFiles(realpath(Yii::getPathOfAlias('application')), $options);

        foreach($files as $file){
            $subject = file_get_contents($file);
            foreach($messages as $message){
                //$tmp = preg_match_all('@\''.preg_quote($message['message']).'\'@', $subject, $matches);

                $message['translation_en'] = str_replace('\'', '\\\'', $message['translation_en']);
                $count = 0;
                $tmp = preg_replace('@tc\(\''.preg_quote($message['message']).'\'\)@', 'tc(\''.$message['translation_en'].'\')', $subject, -1, $count);

                if($tmp !== NULL){
                    $subject = $tmp;
                }
                if($count){
                    $sql = 'UPDATE {{translate_message}} SET message=translation_en WHERE id="'.$message['id'].'"';
                    Yii::app()->db->createCommand($sql)->execute();
                }
                /*if($tmp > 0){
                    $sql = 'UPDATE {{translate_message}} SET status=4 WHERE id="'.$message['id'].'"';
                    Yii::app()->db->createCommand($sql)->execute();
                }*/
            }
            file_put_contents($file, $subject);
        }
    }

}
