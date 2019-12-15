<?php
if ($data->canShowInView('address')) {
	$adressFull = '';

	if (issetModule('location')) {
		if ($data->locCountry || $data->locRegion || $data->locCity)
			$adressFull = ' ';

		if ($data->locCountry) {
			$adressFull .= $data->locCountry->getStrByLang('name');
		}
		if ($data->locRegion) {
			if ($data->locCountry)
				$adressFull .= ',&nbsp;';
			$adressFull .= $data->locRegion->getStrByLang('name');
		}
		if ($data->locCity) {
			if ($data->locCountry || $data->locRegion)
				$adressFull .= ',&nbsp;';
			$adressFull .= $data->locCity->getStrByLang('name');
		}
	} else {
		if (isset($data->city) && isset($data->city->name)) {
			$cityName = $data->city->name;
			if ($cityName) {
				$adressFull = ' ' . $cityName;
			}
		}
	}
	$adress = CHtml::encode($data->getStrByLang('address'));


	if ($adress) {
		if (issetModule('tariffPlans') && issetModule('paidservices') && ($data->owner_id != Yii::app()->user->id)) {
			if (Yii::app()->user->isGuest) {
				$defaultTariffInfo = TariffPlans::getFullTariffInfoById(TariffPlans::DEFAULT_TARIFF_PLAN_ID);

				if (!$defaultTariffInfo['showAddress']) {
					$adressFull = Yii::t('module_tariffPlans', 'Please <a href="{n}">login</a> to view', Yii::app()->controller->createUrl('/site/login'));
				}
				else {
					$adressFull .= ', ' . $adress;
				}
			}
			else {
				if (TariffPlans::checkAllowShowAddress())
					$adressFull .= ', ' . $adress;
				else
					$adressFull = ' '.Yii::t('module_tariffPlans', 'Please <a href="{n}">change the tariff plan</a> to view', Yii::app()->controller->createUrl('/tariffPlans/main/index'));
			}
		}
		else {
			$adressFull .= ', ' . $adress;
		}
	}

	if ($adressFull) {
		echo '<dt>' . tt('Address') . ':</dt><dd>' . $adressFull . '</dd>';
	}
}
?>