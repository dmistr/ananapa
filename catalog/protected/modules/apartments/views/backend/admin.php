<?php

// for modal apply paid service
$cs = Yii::app()->clientScript;
$cs->registerCoreScript('jquery.ui');
$cs->registerScriptFile($cs->getCoreScriptUrl(). '/jui/js/jquery-ui-i18n.min.js');
$cs->registerCssFile($cs->getCoreScriptUrl(). '/jui/css/base/jquery-ui.css');


$this->breadcrumbs=array(
	tt('Manage apartments'),
);

$this->menu = array(
	array('label'=>tt('Add apartment'), 'url'=>array('create')),
);
$this->adminTitle = tt('Manage apartments');

if(Yii::app()->user->hasFlash('mesIecsv')){
	echo "<div class='flash-success'>".Yii::app()->user->getFlash('mesIecsv')."</div>";
}

if (param('useUserads', 1)) {
	Yii::app()->clientScript->registerScriptFile(Yii::app()->theme->baseUrl . '/js/jquery.jeditable.js', CClientScript::POS_END);
	Yii::app()->clientScript->registerScript('editable_select', "
		function ajaxSetModerationStatus(elem, id, id_elem, owner_id, items){
			$('#editable_select-'+id_elem).editable('".Yii::app()->controller->createUrl("activate")."', {
				data   : items,
				type   : 'select',
				cancel : '".tc('Cancel')."',
				submit : '".tc('Ok')."',
				style  : 'inherit',
				submitdata : function() {
					return {id : id_elem};
				}
			});
		}
	",
	CClientScript::POS_HEAD);

}

$columns = array(
	array(
		'class'=>'CCheckBoxColumn',
		'id'=>'itemsSelected',
		'selectableRows' => '2',
		'htmlOptions' => array(
			'class'=>'center',
		),
	),
	array(
		'name' => 'id',
		'htmlOptions' => array(
			'class'=>'apartments_id_column',
		),
		'sortable' => false,
	),
        array(
		'name' => 'phone',
		'htmlOptions' => array(
			'class'=>'apartments_phone_column',
		),
		'sortable' => false,
	),
	array(
		'name' => 'active',
		'type' => 'raw',
		'value' => 'Yii::app()->controller->returnControllerStatusHtml($data, "apartments-grid", 1).
				(($data->deleted && !param("notDeleteListings", 0))  ? tt("Listing is deleted", "apartments") : "")',
		'htmlOptions' => array(
			//'style' => 'width: 150px;',
			'class'=>'apartments_status_column',
		),
		'sortable' => false,
		'filter' => Apartment::getModerationStatusArray(),
	),
	
	array(
		'name' => 'deleted',
		'type' => 'raw',
		'value' => 'Apartment::getApartmentsDeleted($data->deleted)',
		'htmlOptions' => array(
			'class'=>'apartments_status_column',
		),
		'sortable' => false,
		'filter' => Apartment::getApartmentsDeletedArray(),
		'visible' => param("notDeleteListings", 0),
	),
	array(
		'name' => 'type',
		'type' => 'raw',
		'value' => 'Apartment::getNameByType($data->type)',
		/*'htmlOptions' => array(
			'style' => 'width: 100px;',
		),*/
		'filter' => Apartment::getTypesArray(),//CHtml::dropDownList('Apartment[type_filter]', $currentType, Apartment::getTypesArray(true)),
		'sortable' => false,
	),
	array(
		'name' => 'price',
		'type' => 'raw',
		'value' => '$data->getPrettyPrice(false)',
		'htmlOptions' => array(
			'style' => 'width: 100px;',
		),
		'filter' => false,
		'sortable' => false,
	),
	array(
		'name' => 'obj_type_id',
		'type' => 'raw',
		'value' => '(isset($data->objType) && $data->objType) ? $data->objType->name : ""',
		/*'htmlOptions' => array(
			'style' => 'width: 100px;',
		),*/
		'filter' => Apartment::getObjTypesArray(),
		'sortable' => false,
	),
);
if (issetModule('location')) {
	$columns[]=array(
		'name' => 'loc_country',
		'value' => '($data->loc_country && isset($data->locCountry)) ? $data->locCountry->name : ""',
		'htmlOptions' => array(
			'style' => 'width: 150px;',
		),
		'sortable' => false,
		'filter' => Country::getCountriesArray(0, 1),
	);
	$columns[]=array(
		'name' => 'loc_region',
		'value' => '($data->loc_region && isset($data->locRegion)) ? $data->locRegion->name : ""',
		'htmlOptions' => array(
			'style' => 'width: 150px;',
		),
		'sortable' => false,
		'filter' => Region::getRegionsArray($model->loc_country, 0, 1),
	);
	$columns[]=array(
		'name' => 'loc_city',
		'value' => '($data->loc_city && isset($data->locCity)) ? $data->locCity->name : ""',
		'htmlOptions' => array(
			'style' => 'width: 150px;',
		),
		'sortable' => false,
		'filter' => City::getCitiesArray($model->loc_region, 0, 1),
	);
} else {
	$columns[]=array(
		'name' => 'city_id',
		'value' => '($data->city_id && isset($data->city)) ? $data->city->name : ""',
		'htmlOptions' => array(
			'style' => 'width: 150px;',
		),
		'sortable' => false,
		'filter' => ApartmentCity::getAllCity(),
	);
}



//$columns[]=array(
//    'name' => 'ownerUsername',
//    'htmlOptions' => array(
//        'style' => 'width: 150px;',
//    ),
//    'value' => 'isset($data->user->username) ? $data->user->username : ""'
//);


$columns[]=array(
	'header' => tc('Name'),
	'name' => 'title_'.Yii::app()->language,
	'type' => 'raw',
	'value' => 'CHtml::encode($data->{"title_".Yii::app()->language})',
	'sortable' => false,
);

if(issetModule('paidservices')){
	$columns[] = array(
		'name' => 'searchPaidService',
		'value'=>'$data->getPaidHtml(true, true)',
		'type'=>'raw',
		'htmlOptions' => array(
			'style' => 'width: 200px;',
		),
		'sortable' => false,
		'filter' => $paidServicesArray,
	);
}

$columns[] = array(
	'class'=>'bootstrap.widgets.TbButtonColumn',

	'template'=>'{up}{down}{change_owner}{view}{update}{clone}{delete}{restore}',
	'deleteConfirmation' => tc('Are you sure you want to delete this item?'),
	'htmlOptions' => array('class'=>'width135'),
	'buttons' => array(
		'change_owner' => array(
			'label' => '',
			'url'=>'Yii::app()->createUrl("/apartments/backend/main/choosenewowner", array("id" => $data->id))',
			//'options' => array('class'=>'icon-user tempModal', 'title' => tt('Set the owner of the listing', 'apartments')),
			'options' => array('class'=>'icon-user', 'title' => tt('Set the owner of the listing', 'apartments')),
			'visible' => ' Yii::app()->user->checkAccess("apartments_admin") ? true : false',
		),
		'delete' => array(
			'visible'=> '(!param("notDeleteListings", 0) || (param("notDeleteListings", 0) && !$data->deleted))'
		),
		'view' => array(
			'url'=>'$data->getUrl()',
			'options'=>array('target'=>'_blank'),
		),
		'up' => array(
			'label' => tc('Move an item up'),
			'imageUrl' => $url = Yii::app()->assetManager->publish(
				Yii::getPathOfAlias('zii.widgets.assets.gridview').'/up.gif'
			),
			'url'=>'Yii::app()->createUrl("/apartments/backend/main/move", array("id"=>$data->id, "direction" => "down", "catid" => "0"))',
			'options' => array('class'=>'infopages_arrow_image_up'),

			'visible' => '$data->sorter < "'.$maxSorter.'"',
			'click' => "js: function() { ajaxMoveRequest($(this).attr('href'), 'apartments-grid'); return false;}",
		),
		'clone' => array(
			'label' => tc('Clone'),
			'url' => 'Yii::app()->createUrl("/apartments/backend/main/clone", array("id" => $data->id))',
			'imageUrl' => Yii::app()->request->baseUrl. '/images/interface/copy_admin.png',
			'click' => "js: function() { ajaxMoveRequest($(this).attr('href'), 'apartments-grid'); return false;}",
		),
		'restore' => array(
			'label' => '',
			'url' => 'Yii::app()->createUrl("/apartments/backend/main/restore", array("id" => $data->id))',
			'options' => array('class'=>'icon-retweet', 'title' => tt('Restore', 'apartments')),
			'click' => "js: function() { ajaxMoveRequest($(this).attr('href'), 'apartments-grid'); return false;}",
			'visible' => '$data->deleted'
		),

		'down' => array(
			'label' => tc('Move an item down'),
			'imageUrl' => $url = Yii::app()->assetManager->publish(
				Yii::getPathOfAlias('zii.widgets.assets.gridview').'/down.gif'
			),
			'url'=>'Yii::app()->createUrl("/apartments/backend/main/move", array("id"=>$data->id, "direction" => "up", "catid" => "0"))',
			'options' => array('class'=>'infopages_arrow_image_down'),
			'visible' => '$data->sorter > "'.$minSorter.'"',
			'click' => "js: function() { ajaxMoveRequest($(this).attr('href'), 'apartments-grid'); return false;}",
		),
	),
);

$this->widget('CustomGridView', array(
	'id'=>'apartments-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'afterAjaxUpdate' => 'function(){$("a[rel=\'tooltip\']").tooltip(); $("div.tooltip-arrow").remove(); $("div.tooltip-inner").remove(); reInstallSortable();}',
	'rowCssClassExpression'=>'"items[]_{$data->id}"',
	'columns'=>$columns
));

$options = array(
	'activate' => Yii::t('common', 'Activate'),
	'deactivate' => Yii::t('common', 'Deactivate'),
	'delete' => Yii::t('common', 'Delete'),
	'clone' => Yii::t('common', 'Clone')
);

if(Apartment::model()->countByAttributes(array('deleted'=>1)))
	$options['restore'] = tt('Restore', 'apartments');

$this->renderPartial('//site/admin-select-items', array(
	'url' => '/apartments/backend/main/itemsSelected',
	'id' => 'apartments-grid',
	'model' => $model,
	'options' => $options,
));
?>

<?php

$csrf_token_name = Yii::app()->request->csrfTokenName;
$csrf_token = Yii::app()->request->csrfToken;

$cs = Yii::app()->getClientScript();
$cs->registerCoreScript('jquery.ui');

$str_js = "
		var fixHelper = function(e, ui) {
			ui.children().each(function() {
				$(this).width($(this).width());
			});
			return ui;
		};

		function reInstallSortable(id, data) {
			installSortable();
		}

		function updateGrid() {
			$.fn.yiiGridView.update('apartments-grid');
		}

		function installSortable() {
			$('#apartments-grid table.items tbody').sortable({
				forcePlaceholderSize: true,
				forceHelperSize: true,
				items: 'tr',
				update : function () {
					serial = $('#apartments-grid table.items tbody').sortable('serialize', {key: 'items[]', attribute: 'class'}) + '&{$csrf_token_name}={$csrf_token}';
					$.ajax({
						'url': '" . $this->createUrl('/apartments/backend/main/sortitems') . "',
						'type': 'post',
						'data': serial,
						'success': function(data){
							updateGrid();
						},
						'error': function(request, status, error){
							alert('We are unable to set the sort order at this time.  Please try again in a few minutes.');
						}
					});
				},
				helper: fixHelper
			}).disableSelection();
		}

		installSortable();
";

$cs->registerScript('sortable-project', $str_js);