<?php
echo '<dl class="ap-descr">';
if ($data->deleted)
	echo '<dt></dt><dd class="deleted">' .  tt('Listing is deleted', 'apartments') . '</dd>';
echo '<dt>' . tt('Apartment ID') . ':</dt><dd>' . $data->id . '</dd>';
$rows = HFormEditor::getGeneralFields();
HFormEditor::renderViewRows($rows, $data);
echo '</dl>';
echo '<div class="clear"></div>';
