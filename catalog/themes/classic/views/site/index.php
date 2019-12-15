<?php if (isset($newsIndex) && $newsIndex) : ?>
	<div class="clear"></div>
<br>
<hr>
	<div class="last-news-index">
<!--	     <p class="title"><?php echo tt('News', 'news');?></p>     -->
		<?php foreach($newsIndex as $news) : ?>
			<div class="last-news-item">			
				<div class="last-news-title">
					<?php echo CHtml::link(truncateText($news->getStrByLang('title'), 8), $news->getUrl());?>
				</div>
			</div>
		<?php endforeach;?>
	</div>
<hr>
                <div class="clear"></div>  
<?php endif;?>

<?php
if($page){
	if (isset($page->page)) {

		if ($page->page->widget && $page->page->widget_position == InfoPages::POSITION_TOP){
			echo '<div>';
			Yii::import('application.modules.'.$page->page->widget.'.components.*');
			if($page->page->widget == 'contactform'){
				$this->widget('ContactformWidget', array('page' => 'index'));
			} else {
				$this->widget(ucfirst($page->page->widget).'Widget');
			}
			echo '</div><div class="clear"></div>';
		}

		if($page->page->body){
			echo $page->page->body;
		}

		if ($page->page->widget && $page->page->widget_position == InfoPages::POSITION_BOTTOM){
			echo '<div class="clear"></div><div>';
			Yii::import('application.modules.'.$page->page->widget.'.components.*');
			if($page->page->widget == 'contactform'){
				$this->widget('ContactformWidget', array('page' => 'index'));
			} else {
				$this->widget(ucfirst($page->page->widget).'Widget');
			}
			echo '</div>';
		}
	}
}