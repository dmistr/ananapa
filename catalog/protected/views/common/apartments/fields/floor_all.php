<?php
if ($data->canShowInView('floor_all')) {
    if ($data->floor || $data->floor_total) {
        if ($data->floor && $data->floor_total) {
            echo '<dt>' . tc('Floor') . ':</dt>';
            echo '<dd>' . Yii::t('module_apartments', '{n} floor of {total} total', array($data->floor, '{total}' => $data->floor_total)) . '</dd>';
        } else {
            if ($data->floor) {
                echo '<dt>' . tc('Floor') . ':</dt>';
                echo '<dd>' . $data->floor . '</dd>';
            }
            if ($data->floor_total) {
                echo '<dt>' . tt('Total number of floors', 'apartments') . ':</dt>';
                echo '<dd>' . $data->floor_total . '</dd>';
            }
        }
    }
}
?>