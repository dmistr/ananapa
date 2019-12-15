<div class="<?php echo $divClass; ?>">
    <span class="search"><div class="<?php echo $textClass; ?>"><?php echo tc('Booking'); ?>:</div></span>
    <div class="search">
        <?php
        $bStart = isset($this->bStart) ? $this->bStart : HDate::formatForDatePicker(time());
        $bEnd = isset($this->bEnd) ? $this->bEnd : null;

        $datePickerLang = Yii::app()->language;
        if ($datePickerLang == 'en')
            $datePickerLang = 'en-GB';

        echo tc('Booking from').':&nbsp;';
        $this->widget('application.extensions.FJuiDatePicker', array(
            'name'=>'b_start',
            'value' => $bStart,
            'range' => 'eval_period',
            'language' => $datePickerLang,
            'options'=>array(
                'showAnim'=>'fold',
                'dateFormat'=>Booking::getJsDateFormat(),
                'minDate'=>'new Date()',
                //'maxDate'=>'+12M',
            ),
            'htmlOptions' => array(
                'class' => 'width80'
            ),
        ));

        echo ' '.tc('to').':&nbsp;';
        $this->widget('application.extensions.FJuiDatePicker', array(
            'name' => 'b_end',
            'value' => $bEnd,
            'range' => 'eval_period',
            'language' => $datePickerLang,
            'options'=>array(
                'showAnim'=>'fold',
                'dateFormat'=>Booking::getJsDateFormat(),
                'minDate'=>'new Date()',
            ),
            'htmlOptions' => array(
                'class' => 'width80'
            ),
        ));
        ?>
    </div>
</div>