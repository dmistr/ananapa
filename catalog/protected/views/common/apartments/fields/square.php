<?php
if ($data->canShowInView('square')) {
    echo '<dt>' . Yii::t('module_apartments', 'Total square') . ':</dt><dd>' . $data->square . ' ' . tc('site_square') . '</dd>';
}
?>