<?php
if ($data->canShowInView('window_to') && $data->windowTo->getTitle()) {
    echo '<dt>' . tt('window to') . ':</dt><dd>' . CHtml::encode($data->windowTo->getTitle()) . '</dd>';
}
?>