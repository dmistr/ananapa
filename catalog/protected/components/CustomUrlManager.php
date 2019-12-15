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

class CustomUrlManager extends CUrlManager {
	private $langRoute;
    private $isInstalled = false;

	public function init() {
		$langs = Lang::getActiveLangs();
		$defaultLang = Lang::getDefaultLang();
        $this->isInstalled = oreInstall::isInstalled();

		$keyDefault = array_search($defaultLang, $langs);
		if($keyDefault !== false && $this->isInstalled){
			unset($langs[$keyDefault]);
		}

		//$countLangs = count($langs);

		$this->langRoute = '<lang:'.implode('|',$langs).'>';

		$rules = array(
			'sitemap.xml'=>'sitemap/main/viewxml',
			'yandex_export_feed.xml'=>'yandexRealty/main/viewfeed',
			'version'=>'/site/version',
			'site/uploadimage/' => 'site/uploadimage/',
			'site/activation' => 'site/activation',
			'min/serve/g/' => 'min/serve/',
			'rss' => 'quicksearch/main/mainsearch/rss/1',
			//'/property/'=>'quicksearch/main/mainsearch',
			'<module:\w+>/backend/<controller:\w+>/<action:\w+>'=>'<module>/backend/<controller>/<action>', // CGridView ajax
		);

		$rulesLang = array(
			'/' => 'site/index',
			'/login' => 'site/login',
			'/admin' => 'site/login',
			'/administrator' => 'site/login',
			'/register' => 'site/register',
			'/recover' => 'site/recover',
			'/logout' => 'site/logout',
			'/site/activation' => 'site/activation',
			'/sell'=>'quicksearch/main/mainsearch/type/2',
			'/rent'=>'quicksearch/main/mainsearch/type/1',
			'/site/uploadimage/' => 'site/uploadimage/',
			'/min/serve/g/' => 'min/serve/',
			'/news'=>'news/main/index',
			'/news/<id:\d+>'=>'news/main/view',
			'/news/<url:[-a-zA-Z0-9_+\.]{1,255}>'=>'news/main/view',
			'/faq'=>'articles/main/index',
			'/faq/<id:\d+>'=>'articles/main/view',
			'/faq/<url:[-a-zA-Z0-9_+\.]{1,255}>'=>'articles/main/view',
			'/contact-us'=>'contactform/main/index',
			'/specialoffers'=>'specialoffers/main/index',
			'/sitemap'=>'sitemap/main/index',
			'/reviews'=>'reviews/main/index',
			'/reviews/add'=>'reviews/main/add',
			'/guestad/add'=>'guestad/main/create',
			'/page/<id:\d+>'=>'infopages/main/view',
			'/page/<url:[-a-zA-Z0-9_+\.]{1,255}>'=>'infopages/main/view',
			'/search' => 'quicksearch/main/mainsearch',
			'/comparisonList' => 'comparisonList/main/index',
			'/complain/add' => 'apartmentsComplain/main/complain',
			'/booking/add' => 'booking/main/bookingform',
			'/booking/request' => 'booking/main/mainform',
			'/usercpanel' => 'usercpanel/main/index',
			'/usercpanel/data' => 'usercpanel/main/data',
			'/usercpanel/changepwd' => 'usercpanel/main/changepassword',
			'/usercpanel/tariffplans' => 'tariffPlans/main/index',
			'/usercpanel/payments' => 'usercpanel/main/payments',
			'/usercpanel/balance' => 'usercpanel/main/balance',
			'/usercpanel/bookingtable' => 'bookingtable/main/index',
			'/userads/create' => 'userads/main/create',
			'/userads/edit' => 'userads/main/update',
			'/userads/delete' => 'userads/main/delete',
			'/users/viewall' => 'users/main/search',
			'/users/alllistings' => 'apartments/main/alllistings',
			'/apartments/sendEmail' => 'apartments/main/sendEmail',
			'/mailbox' => 'messages/main/index',
			'/mailbox/send' => 'messages/main/sendform',
			'/mailbox/read' => 'messages/main/read',
			'/mailbox/delete' => 'messages/main/delete',
			'/messages/downloadFile' => 'messages/main/downloadFile',
			'/service-<serviceId:\d+>' => 'quicksearch/main/mainsearch',
			'/property/<id:\d+>'=>'apartments/main/view',
			'/property/<url:[-a-zA-Z0-9_+\.]{1,255}>'=>'apartments/main/view',
			'/<controller:(quicksearch|specialoffers)>/main/index' => '<controller>/main/index',
			'/<_m>/<_c>/<_a>*' => '<_m>/<_c>/<_a>',
			'/<_c>/<_a>*' => '<_c>/<_a>',
			'/<_c>' => '<_c>',
		);

		foreach($rulesLang as $key => $rule){
			if($langs && $this->langRoute){
				$rules[$this->langRoute . $key] = $rule;
			}
           	$rules[$key] = array($rule, 'defaultParams' => array('lang' => $defaultLang));
        }

		if($langs && $this->langRoute){
			$rules[$this->langRoute] = '';
		}

		$this->addRules($rules);

		if($this->isInstalled){
			$modules = Yii::app()->getModules();

			$paramModules = ConfigurationModel::getModulesList();
			foreach($paramModules as $module){
				if(isset($modules[$module]) && !param('module_enabled_'.$module)){
					$modules[$module]['enabled'] = false;
				}
			}

			Yii::app()->setModules($modules);
		}
		return parent::init();
	}

	private $parseReady = false;

	public function parseUrl($request) {
		if(issetModule('seo') && $this->parseReady === false && oreInstall::isInstalled()){
			if (preg_match('#^([\w-]+)#i', $request->pathInfo, $matches)) {
				$activeLangs = Lang::getActiveLangs();
				$arr = array();
				foreach($activeLangs as $lang){
					$arr[] = 'url_'.$lang.' = :alias';
				}
				$condition = '('.implode(' OR ', $arr).')';

				$seo = SeoFriendlyUrl::model()->find(array(
					'condition' => 'direct_url = 1 AND '.$condition,
					'params' => array('alias'=>$matches[1])
				));

				if ($seo !== null) {
					foreach($activeLangs as $lang){
						$field = 'url_'.$lang;

                        if($seo->$field == $matches[1]){

                            setLangCookie($lang);
                            Yii::app()->setLanguage($lang);
                            //$_GET['lang'] = $lang;
                        }
					}
					$_GET['url'] = $matches[1];
					//$_GET['id'] = $seo->model_id;

					//Yii::app()->controller->seo = $seo;
					return 'infopages/main/view';
				}
			}

			$this->parseReady = true;
		}

		return parent::parseUrl($request);
	}

    public function createUrl($route, $params = array(), $ampersand = '&') {
        if ($route != 'min/serve' && $route != 'site/uploadimage') {
            $langs = Lang::getActiveLangs();
            $countLangs = count($langs);
            $defaultLang = Lang::getDefaultLang();

			if(isset($params['lang']) && $params['lang'] == $defaultLang && $this->isInstalled){
				if(!param('useBootstrap')){
					unset($params['lang']);
				}
			} else if (Yii::app()->language != $defaultLang && !isFree() && empty($params['lang']) && $countLangs > 1) {
				$params['lang'] = Yii::app()->language;
			}

			if(!$this->isInstalled && $countLangs == 1 && $route != 'install'){
				$params['lang'] = Yii::app()->language;
			}

			if(!$this->isInstalled && $countLangs > 1 && !isset($params['lang']) && $route != 'install'){
				$params['lang'] = Yii::app()->language;
			}
        }

        return parent::createUrl($route, $params, $ampersand);
    }
}