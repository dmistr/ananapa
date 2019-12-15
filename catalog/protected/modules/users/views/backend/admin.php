<?php
$this->breadcrumbs=array(
	Yii::t('common', 'User managment'),
);

$this->menu=array(
	array('label'=>tt('Add user'), 'url'=>array('/users/backend/main/create')),
);

$this->adminTitle = Yii::t('common', 'User managment');

$columns = array(
	array(
		'class'=>'CCheckBoxColumn',
		'id'=>'itemsSelected',
		'selectableRows' => '2',
		'htmlOptions' => array(
			'class'=>'center',
		),
		'disabled' => '$data->id == 1',
	),
	array(
		'name' => 'active',
		'header' => tt('Status'),
		'type' => 'raw',
		'value' => 'Yii::app()->controller->returnStatusHtml($data, "user-grid", 1, 1)',
		'headerHtmlOptions' => array(
			'class'=>'infopages_status_column',
		),
		'filter' => array(0 => tt('Inactive'), 1 => tt('Active')),
	),
	array(
		'name' => 'type',
		'value' => '$data->getTypeName()',
		'filter' => User::getTypeList(),
	),
	array(
		'name' => 'role',
		'value' => '$data->getRoleName()',
		'filter' => User::$roles,
	),
	array(
		'name' => 'username',
		'header' => tt('User name'),
	),
	'phone',
	'email',
	array(
		'header' => '',
		'value' => 'HUser::getLinkForRecover($data)',
		'type' => 'raw'
	),
	array(
		'name'=>'date_created',
		'type'=>'raw',
		'filter'=>$this->widget('zii.widgets.jui.CJuiDatePicker', array(
			'model'=>$model,
			'attribute'=>'date_created',
			'language' => Yii::app()->controller->datePickerLang,
			'options' => array(
				'showAnim'=>'fold',
				'dateFormat'=> 'yy-mm-dd',
				'changeMonth' => 'true',
				'changeYear'=>'true',
				'showButtonPanel' => 'true',
			),
		),true),
		'htmlOptions' => array('style' => 'width:130px;'),
	),
);

if(issetModule('paidservices')) {
	//$columns[] = 'balance';
}

if(issetModule('tariffPlans') && issetModule('paidservices') && Yii::app()->user->checkAccess('tariff_plans_admin')){
	// for modal apply
	$cs = Yii::app()->clientScript;
	$cs->registerCoreScript('jquery.ui');
	$cs->registerScriptFile($cs->getCoreScriptUrl(). '/jui/js/jquery-ui-i18n.min.js');
	$cs->registerCssFile($cs->getCoreScriptUrl(). '/jui/css/base/jquery-ui.css');

	$columns[] = array(
		'header' => tc('Tariff Plans'),
		'value'=>'TariffPlans::getTariffPlansHtml(true, true, $data)',
		'type'=>'raw',
		'htmlOptions' => array(
			'style' => 'width: 200px;',
		),
	);
}

$columns[] = array(
	'class'=>'bootstrap.widgets.TbButtonColumn',
	'template'=>'{view}{update}{delete}{send}',
	'deleteConfirmation' => tc('Are you sure you want to delete this item?'),
	'buttons' => array(
		'view' => array(
			'visible' => '$data->role != "admin"',
		),
		'delete' => array(
			'visible' => '$data->role != "admin"',
		),
		'send' => array(
			'label' => '',
			'url'=>'Yii::app()->createUrl("/messages/backend/main/read", array("id" => $data->id))',
			'options' => array('class'=>'icon-envelope', 'title' => tt('Message', 'messages')),
			'visible' => '(issetModule("messages") && Yii::app()->user->checkAccess("messages_admin") && $data->id != Yii::app()->user->id && $data->role == "registered" && $data->active == 1) ? true : false',
		),
	)
);

$this->widget('CustomGridView', array(
	'id'=>'user-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'afterAjaxUpdate' => 'function(){$("a[rel=\'tooltip\']").tooltip(); $("div.tooltip-arrow").remove(); $("div.tooltip-inner").remove(); jQuery("#News_date_created").datepicker(jQuery.extend(jQuery.datepicker.regional["'.Yii::app()->controller->datePickerLang.'"],{"showAnim":"fold","dateFormat":"yy-mm-dd","changeMonth":"true","showButtonPanel":"true","changeYear":"true"}));}',
	'columns'=>$columns
));

$this->renderPartial('//site/admin-select-items', array(
	'url' => '/users/backend/main/itemsSelected',
	'id' => 'user-grid',
	'model' => $model,
	'options' => array(
		'activate' => Yii::t('common', 'Activate'),
		'deactivate' => Yii::t('common', 'Deactivate'),
		'delete' => Yii::t('common', 'Delete')
	),
));

