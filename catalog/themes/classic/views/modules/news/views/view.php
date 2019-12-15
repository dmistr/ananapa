<?php
	$this->pageTitle .= ' - '.NewsModule::t('News').' - '.CHtml::encode($model->getStrByLang('title'));
	$this->breadcrumbs=array(
		NewsModule::t('News') => array('/news/main/index'),
		truncateText(CHtml::encode($model->getStrByLang('title')), 10),
	);
?>

<h2><?php echo CHtml::encode($model->getStrByLang('title'));?></h2>
<span class="date"><?php echo NewsModule::t('Created on').' '.$model->dateCreated; ?></span>



<?php
	echo $model->body;
?>
<div class="clear"></div>

<?php if(param('enableCommentsForNews', 1)){ ?>
<div id="comments">
	<?php
		$this->widget('application.modules.comments.components.commentListWidget', array(
			'model' => $model,
			'url' => $model->getUrl(),
			'showRating' => false,
		));
	?>
</div>
<?php } ?>