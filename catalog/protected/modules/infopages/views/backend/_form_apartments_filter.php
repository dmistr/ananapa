<div id="apartments_filter" style="display: none;" class="well">
	<h4><?php echo tt('Filter for listings\' list') ?></h4>
	<?php
	if(issetModule('location')){ ?>
		<div class="">
			<div class=""><?php echo tc('Country') ?>:</div>
			<?php
			echo CHtml::dropDownList(
				'filter[country_id]',
				$this->getFilterValue('country_id'),
				Country::getCountriesArray(2),
				array('class' => 'searchField', 'id' => 'country',
					'ajax' => array(
						'type'=>'GET',
						'url'=>$this->createUrl('/location/main/getRegions'),
						'data'=>'js:"country="+$("#country").val()+"&type=2"',
						'success'=>'function(result){
							$("#region").html(result);
							$("#region").change();
						}'
					)
				)
			);

			?>
		</div>

		<div class="">
			<div class=""><?php echo tc('Region') ?>:</div>
			<?php
			echo CHtml::dropDownList(
				'filter[region_id]',
				$this->getFilterValue('region_id'),
				Region::getRegionsArray($this->getFilterValue('country_id'), 2),
				array('class' => 'searchField', 'id' => 'region',
					'ajax' => array(
						'type'=>'GET',
						'url'=>$this->createUrl('/location/main/getCities'),
						'data'=>'js:"region="+$("#region").val()+"&type=0"',
						'success'=>'function(result){
							$("#ap_city").html(result);
						}'
					)
				)
			);

			?>
		</div>
	<?php

	$cities = City::getCitiesArray($this->getFilterValue('region_id'));
	}

	$objTypes = CArray::merge(array(0 => ''), ApartmentObjType::getList());
	//$typeList = CArray::merge(array(0 => ''), Apartment::getTypesArray());
	$dataTypes = SearchForm::apTypes();

	$dataTypes['propertyType'][0] = '';
	$typeList = $dataTypes['propertyType'];

	$roomItems = array(
		'0' => '',
		'1' => 1,
		'2' => 2,
		'3' => 3,
		'4' => Yii::t('common', '4 and more'),
	);
	?>

	<div class="">
		<div class=""><?php echo Yii::t('common', 'City') ?>:</div>
		<?php
		$cities = (isset($cities) && count($cities)) ? $cities : CArray::merge(array(0 => tc('select city')), ApartmentCity::getAllCity());

		echo CHtml::dropDownList(
			'filter[city_id]',
			$this->getFilterValue('city_id'),
			$cities,
			array('class' => ' searchField', 'id' => 'ap_city') //, 'multiple' => 'multiple'
		);
		?>
	</div>

	<div class="rowold">
		<div class=""><?php echo tc('Type') ?>:</div>
		<?php echo CHtml::dropDownList('filter[type]', $this->getFilterValue('type'), $typeList); ?>
	</div>

	<div class="rowold">
		<div class=""><?php echo tc('Property type') ?>:</div>
		<?php echo CHtml::dropDownList('filter[obj_type_id]', $this->getFilterValue('obj_type_id'), $objTypes); ?>
	</div>

	<?php if (!(issetModule('selecttoslider') && param('useRoomSlider') == 1)) :?>
		<div class="rowold">
			<div class=""><?php echo tc('Number of rooms') ?>:</div>
			<?php
			echo CHtml::dropDownList('filter[rooms]', $this->getFilterValue('rooms'), $roomItems);
			?>
		</div>
	<?php endif?>

	<?php if (isset($addedFields) && $addedFields && count($addedFields)) :?>
		<?php foreach($addedFields as $adField):?>
			<div class="rowold">
				<div class=""><?php echo $adField['label']; ?>:</div>
				<?php if (isset($adField['listData']) && $adField['listData']):?>
					<?php echo CHtml::dropDownList('filter['.$adField['field'].']', $this->getFilterValue($adField['field']), CMap::mergeArray(array("" => ""), $adField['listData'])); ?>
				<?php else: ?>
					<?php echo CHtml::textField('filter['.$adField['field'].']', $this->getFilterValue($adField['field'])); ?>
				<?php endif;?>
			</div>
		<?php endforeach;?>
	<?php endif;?>
</div>