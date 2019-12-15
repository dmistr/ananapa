<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "https://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php
/*$nameRFC3066 = 'ru-ru';
$allLangs = Lang::getActiveLangs(true);
if ($allLangs) {
	$nameRFC3066 = (array_key_exists(Yii::app()->language, $allLangs) && array_key_exists('name_rfc3066', $allLangs[Yii::app()->language])) ? $allLangs[Yii::app()->language]['name_rfc3066'] : 'ru-ru';
}
$nameRFC3066 = utf8_strtolower($nameRFC3066);
*/
$cs = Yii::app()->clientScript;
$baseUrl = Yii::app()->baseUrl;
$baseThemeUrl = Yii::app()->theme->baseUrl;
?>

<html xmlns="https://www.w3.org/1999/xhtml" xml:lang="<?php echo Yii::app()->language;?>" lang="<?php echo Yii::app()->language;?>">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

	<title><?php echo CHtml::encode($this->seoTitle ? $this->seoTitle : $this->pageTitle); ?>. город-курорт Анапа</title>
	<meta name="description" content="<?php echo CHtml::encode($this->seoDescription ? $this->seoDescription : $this->pageDescription); ?>" />
	<meta name="keywords" content="<?php echo CHtml::encode($this->seoKeywords ? $this->seoKeywords : $this->pageKeywords); ?>" />
	<link href='https://fonts.googleapis.com/css?family=Roboto:400,300,700,500&subset=latin,cyrillic-ext,greek-ext,greek,vietnamese,latin-ext,cyrillic' rel='stylesheet' type='text/css'>

	<link rel="stylesheet" type="text/css" href="<?php echo $baseThemeUrl; ?>/css/screen.css" media="screen, projection" />
	<link rel="stylesheet" type="text/css" href="<?php echo $baseThemeUrl; ?>/css/print.css" media="print" />
	<!--<link rel="stylesheet" type="text/css" href="<?php echo $baseThemeUrl; ?>/css/form.css" />-->
	<link media="screen, projection" type="text/css" href="<?php echo $baseThemeUrl; ?>/css/styles.css" rel="stylesheet" />

	<!--[if IE]> <link href="<?php echo $baseThemeUrl; ?>/css/ie.css" rel="stylesheet" type="text/css"> <![endif]-->

	<link rel="apple-touch-icon" sizes="57x57" href="/images/apple-icon-57x57.png">
        <link rel="apple-touch-icon" sizes="60x60" href="/images/apple-icon-60x60.png">
        <link rel="apple-touch-icon" sizes="72x72" href="/images/apple-icon-72x72.png">
        <link rel="apple-touch-icon" sizes="76x76" href="/images/apple-icon-76x76.png">
        <link rel="apple-touch-icon" sizes="114x114" href="/images/apple-icon-114x114.png">
        <link rel="apple-touch-icon" sizes="120x120" href="/images/apple-icon-120x120.png">
        <link rel="apple-touch-icon" sizes="144x144" href="/images/apple-icon-144x144.png">
        <link rel="apple-touch-icon" sizes="152x152" href="/images/apple-icon-152x152.png">
        <link rel="apple-touch-icon" sizes="180x180" href="/images/apple-icon-180x180.png">
        <link rel="icon" type="image/png" sizes="192x192"  href="/android-icon-192x192.png">
        <link rel="icon" type="image/png" sizes="32x32" href="/images/favicon-32x32.png">
        <link rel="icon" type="image/png" sizes="96x96" href="/images/favicon-96x96.png">
        <link rel="icon" type="image/png" sizes="16x16" href="/images/favicon-16x16.png">
        <link rel="manifest" href="/images/manifest.json">
        <meta name="msapplication-TileColor" content="#ffffff">
        <meta name="msapplication-TileImage" content="/images/ms-icon-144x144.png">
        <meta name="theme-color" content="#ffffff">
        
	<?php
    HSite::registerMainAssets();

	if(Yii::app()->user->checkAccess('backend_access')){
		?><link rel="stylesheet" type="text/css" href="<?php echo $baseThemeUrl; ?>/css/tooltip/tipTip.css" /><?php
	}
	?>
</head>

<body>
<!-- Yandex.Metrika counter -->
<script type="text/javascript">
(function (d, w, c) {
    (w[c] = w[c] || []).push(function() {
        try {
            w.yaCounter29868764 = new Ya.Metrika({id:29868764,
                    webvisor:true,
                    clickmap:true,
                    trackLinks:true,
                    accurateTrackBounce:true});
        } catch(e) { }
    });

    var n = d.getElementsByTagName("script")[0],
        s = d.createElement("script"),
        f = function () { n.parentNode.insertBefore(s, n); };
    s.type = "text/javascript";
    s.async = true;
    s.src = (d.location.protocol == "https:" ? "https:" : "http:") + "//mc.yandex.ru/metrika/watch.js";

    if (w.opera == "[object Opera]") {
        d.addEventListener("DOMContentLoaded", f, false);
    } else { f(); }
})(document, window, "yandex_metrika_callbacks");
</script>
<noscript><div><img src="//mc.yandex.ru/watch/29868764" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
<!-- /Yandex.Metrika counter -->
<!-- Rating@Mail.ru counter -->
<script type="text/javascript">
var _tmr = _tmr || [];
_tmr.push({id: "2653019", type: "pageView", start: (new Date()).getTime()});
(function (d, w, id) {
  if (d.getElementById(id)) return;
  var ts = d.createElement("script"); ts.type = "text/javascript"; ts.async = true; ts.id = id;
  ts.src = (d.location.protocol == "https:" ? "https:" : "http:") + "//top-fwz1.mail.ru/js/code.js";
  var f = function () {var s = d.getElementsByTagName("script")[0]; s.parentNode.insertBefore(ts, s);};
  if (w.opera == "[object Opera]") { d.addEventListener("DOMContentLoaded", f, false); } else { f(); }
})(document, window, "topmailru-code");
</script><noscript><div style="position:absolute;left:-10000px;">
<img src="//top-fwz1.mail.ru/counter?id=2653019;js=na" style="border:0;" height="1" width="1" alt="Рейтинг@Mail.ru" />
</div></noscript>
<!-- //Rating@Mail.ru counter -->
	<?php if (demo()) :?>
		<?php $this->renderPartial('//site/ads-block', array()); ?>
	<?php endif; ?>

	<div id="container" <?php echo (demo()) ? 'style="padding-top: 40px;"' : '';?> >
		<noscript><div class="noscript"><?php echo Yii::t('common', 'Allow javascript in your browser for comfortable use site.'); ?></div></noscript>
		<div class="logo">
			
				<a href=//ananapa.ru /a> <img width="960" height="120" alt="<?php echo CHtml::encode($this->pageDescription); ?>" src="<?php echo $baseThemeUrl; ?>/images/s10.png" id="logo"  alt="Каталог недвижимости города Анапа" title="НОВАЯ ЖИЗНЬ!"/>
			</a>
		</div>
<a align="right"></a>
		<?php
		if(!isFree()){
			$languages = Lang::getActiveLangs(true);
			if(count($languages) > 1){
				$this->widget('application.modules.lang.components.langSelectorWidget', array( 'type' => 'links', 'languages' => $languages ));
			}
			if(count(Currency::getActiveCurrency()) >1){
				$this->widget('application.modules.currency.components.currencySelectorWidget');
			}
		}
		?>

		<div id="user-cpanel"  class="menu_item">
			<?php
			   if(!isset($adminView)){
					$this->widget('zii.widgets.CMenu',array(
						
					));
				} else {
					$this->widget('zii.widgets.CMenu',array(
						'id' => 'dropDownNav',
						'items'=>CMap::mergeArray($this->aData['topMenuItems'], array(array('label' => Yii::t('common', 'Logout'), 'url'=>array('/site/logout')))),
						'htmlOptions' => array('class' => 'dropDownNav adminTopNav'),
					));
				}
			?>

		</div>
		<?php
		if(!isset($adminView)){
		?>
			<div id="search" class="menu_item">
				<?php
				if (param('useYandexShare', 0))
					$this->widget('application.extensions.YandexShareApi', array(
						'services' => param('yaShareServices', 'yazakladki,moikrug,linkedin,vkontakte,facebook,twitter,odnoklassniki')
					));
				if (param('useInternalShare', 1))
					$this->widget('ext.sharebox.EShareBox', array(
						'url' => Yii::app()->getRequest()->getHostInfo().Yii::app()->request->url,
						'title'=> CHtml::encode($this->seoTitle ? $this->seoTitle : $this->pageTitle),
						'iconSize' => 16,
						'include' => explode(',', param('intenalServices', 'vk,facebook,twitter,google-plus')),
					));

					/*$this->widget('zii.widgets.CMenu',array(
						'id' => 'dropDownNav',
						'items'=>$this->aData['topMenuItems'],
						'htmlOptions' => array('class' => 'dropDownNav'),
					));*/

					$this->widget('CustomMenu',array(
						'id' => 'sf-menu-id',
						'items' => $this->aData['topMenuItems'],
						'htmlOptions' => array('class' => 'sf-menu'),
						'encodeLabel' => false,
						'activateParents' => true,
					));
				?>
			</div>
		<?php
		} else {
			echo '<hr />';
			?>

			<div class="admin-top-menu">
				<?php
				$this->widget('zii.widgets.CMenu', array(
					'items'=>$this->aData['adminMenuItems'],
					'encodeLabel' => false,
					'submenuHtmlOptions' => array('class' => 'admin-submenu'),
					'htmlOptions' => array('class' => 'adminMainNav')
				));
				?>
			</div>
		<?php
		}
		?>

		<div class="content">
			<?php echo $content; ?>
			<div class="clear"></div>
		</div>

		<?php
			if(issetModule('advertising')) {
				$this->renderPartial('//modules/advertising/views/advert-bottom');
			}
		?>

		<div class="footer">
			<?php echo getGA(); ?>
			<?php echo getJivo(); ?>
			<p class="slogan">&nbsp;&nbsp;&nbsp;&copy;&nbsp;<?php echo CHtml::encode(Yii::app()->name).', '.date('Y'); ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;+7(928)401-5994&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;+7(900)242-9222</p>
			<!-- <?php echo param('version_name').' '.param('version'); ?> -->
		</div>
	</div>

	<div id="loading" style="display:none;"><?php echo Yii::t('common', 'Loading content...'); ?></div>
	<?php
    $cs->registerScript('main-vars', '
		var BASE_URL = '.CJavaScript::encode(Yii::app()->baseUrl).';
        var CHANGE_SEARCH_URL = '.CJavaScript::encode(Yii::app()->createUrl('/quicksearch/main/mainsearch/countAjax/1')).';
		var params = {
			change_search_ajax: '.param("change_search_ajax", 1).'
		}
	', CClientScript::POS_HEAD, array(), true);

    $this->renderPartial('//layouts/_common');

	$this->widget('application.modules.fancybox.EFancyBox', array(
		'target'=>'a.fancy',
		'config'=>array(
				'ajax' => array('data'=>"isFancy=true"),
				'titlePosition' => 'inside',
				'onClosed' => 'js:function(){
					var capClick = $("#yw0_button");
					if(typeof capClick !== "undefined")	capClick.click();
				}'
			),
		)
	);
//var capClick = $("#yw0_button");alert(capClick);
	if(Yii::app()->user->checkAccess('apartments_admin')){
		$cs->registerScriptFile($baseThemeUrl.'/js/tooltip/jquery.tipTip.minified.js', CClientScript::POS_HEAD);
		$cs->registerScript('adminMenuToolTip', '
			$(function(){
				$(".adminMainNavItem").tipTip({maxWidth: "auto", edgeOffset: 10, delay: 200});
			});
		', CClientScript::POS_READY);
		?>

		<div class="admin-menu-small <?php echo demo() ? 'admin-menu-small-demo' : '';?> ">
			<a href="<?php echo $baseUrl; ?>/apartments/backend/main/admin">
				<img src="<?php echo $baseThemeUrl; ?>/images/adminmenu/administrator.png" alt="<?php echo Yii::t('common','Administration'); ?>" title="<?php echo Yii::t('common','Administration'); ?>" class="adminMainNavItem" />
			</a>
		</div>
	<?php } ?>

<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-57411013-3', 'auto');
  ga('send', 'pageview');

</script>
<script src="http://ananapa.ru/catalog/themes/classic/js/jquery.cookie.js" type="text/javascript"></script>
<script type="text/javascript" src="http://ananapa.ru/catalog/themes/classic/js/pop_up.js"></script>
<link href="http://ananapa.ru/catalog/themes/classic/css/pop_up.css" type="text/css" rel="stylesheet" />
<div id="popup_dream-farm_window" class="popup_dream-farm">
<div class="policymessage">
<strong>С политикой конфиденциальности, с использованием данным сайтом cookies, а также со всеми пунктами пользовательского соглашения -  соглашаюсь</strong>

 
<p><a class="moreinfo" href="http://ananapa.ru/catalog/page/4">ПРОЧИТАТЬ СОГЛАШЕНИЕ</a></p>
<a href="javascript:void(0);" id="popup_dream-farm_no-thanks" title="Закрыть" alt="Закрыть" class="no-thanks">ПОДТВЕРДИТЬ</a>
</div>
</div>
</div>
<script type="text/javascript">
jQuery(document).ready(function() {
	window.setTimeout('showPopup("dream-farm", false);', 1);
});
</script>
</body>
</html>