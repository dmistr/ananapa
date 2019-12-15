<?php
if ($data->canShowInView('land_square')) {
    echo '<dt>' . Yii::t('module_apartments', 'Land square') . ':</dt><dd>' . $data->land_square . ' ' . tc('site_land_square') . '</dd>';
}
?>