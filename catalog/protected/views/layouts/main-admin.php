<?php $baseUrl = Yii::app()->request->baseUrl; ?>
<?php $baseThemeUrl = Yii::app()->theme->baseUrl; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<meta name="language" content="en"/>

	<title><?php echo CHtml::encode($this->pageTitle); ?></title>
	<meta name="description" content="<?php echo CHtml::encode($this->pageDescription); ?>"/>
	<meta name="keywords" content="<?php echo CHtml::encode($this->pageKeywords); ?>"/>
	<link href='http://fonts.googleapis.com/css?family=Roboto:400,300,700,500&subset=latin,cyrillic-ext,greek-ext,greek,vietnamese,latin-ext,cyrillic' rel='stylesheet' type='text/css'>

	<link media="screen, projection" type="text/css" href="<?php echo $baseUrl; ?>/common/css/admin-styles.css" rel="stylesheet"/>

	<!--[if IE]>
	<link href="<?php echo $baseUrl; ?>/common/css/ie.css" rel="stylesheet" type="text/css"> <![endif]-->
	<link rel="icon" href="<?php echo $baseUrl; ?>/favicon.ico" type="image/x-icon"/>
	<link rel="shortcut icon" href="<?php echo $baseUrl; ?>/favicon.ico" type="image/x-icon"/>

	<?php
		Yii::app()->bootstrap->registerAllCss();
		Yii::app()->bootstrap->registerCoreScripts();
		Yii::app()->clientScript->registerScriptFile($baseThemeUrl . '/js/scrollto.js', CClientScript::POS_END);

        $this->renderPartial('//layouts/_common');
	?>
</head>

<body id="top">
<div id="fb-root"></div>

<?php

	if(isFree()) {
		$rightItems = array(
			array('label' => tc('Log out'), 'url' => $baseUrl . '/site/logout'),
		);
	} else {
		$rightItems = array(
			array('label' => tc('Language'), 'url' => '#', 'items' => Lang::getAdminMenuLangs()),
			array('label' => tc('Currency'), 'url' => '#', 'items' => Currency::getActiveCurrencyArray(4)),
			array('label' => tc('Log out'), 'url' => $baseUrl . '/site/logout'),
		);
	}

	$this->widget('bootstrap.widgets.TbNavbar', array(
		'fixed' => 'top',
		'brand' => '<img alt="' . CHtml::encode($this->pageDescription) . '" src="' . $baseThemeUrl . '/images/pages/logo-open-re-admin.png" id="logo">',
		'brandUrl' => $baseUrl . '/',
		'collapse' => false, // requires bootstrap-responsive.css
		'items' => array(
			array(
				'class' => 'bootstrap.widgets.TbMenu',
				'items' => array(
					array('label' => tc('Control panel'), 'url' => '#', 'active' => true),
					array('label' => tc('Menu'), 'url' => '#', 'items' => $this->infoPages),
				),
				'encodeLabel' => false,
			),
			//'<form class="navbar-search pull-left" action=""><input type="text" class="search-query span2" placeholder="Search"></form>',

			array(
				'class' => 'bootstrap.widgets.TbMenu',
				'htmlOptions' => array('class' => 'pull-right'),
				'items' => $rightItems,
			),
		),
	));

	$countApartmentModeration = Apartment::getCountModeration();
	$bageListings = ($countApartmentModeration > 0) ? "&nbsp<span class=\"badge\">{$countApartmentModeration}</span>" : '';

	$bagePayments = '';
	if(issetModule('payment')){
		$countPaymentWait = Payments::getCountWait();
		$bagePayments = ($countPaymentWait > 0) ? "&nbsp<span class=\"badge\">{$countPaymentWait}</span>" : '';
	}

	$bageComments = '';
	if(issetModule('comments')){
		$countCommentPending = Comment::getCountPending();
		$bageComments = ($countCommentPending > 0) ? "&nbsp<span class=\"badge\">{$countCommentPending}</span>" : '';
	}

	$bageComplain = '';
	if(issetModule('apartmentsComplain')){
		$countComplainPending = ApartmentsComplain::getCountPending();
		$bageComplain = ($countComplainPending > 0) ? "&nbsp<span class=\"badge\">{$countComplainPending}</span>" : '';
	}

	$bageReviews = '';
	if(issetModule('reviews')){
			$countReviewsPending = Reviews::getCountModeration();
			$bageReviews = ($countReviewsPending > 0) ? "&nbsp<span class=\"badge\">{$countReviewsPending}</span>" : '';
	}

	$bageBooking = '';
	if (issetModule('bookingtable')) {
		$countNewPending = Bookingtable::getCountNew();
		$bageBooking = ($countNewPending > 0) ? "&nbsp<span class=\"badge\">{$countNewPending}</span>" : '';
	}

	$bageMessages = '';
	if (issetModule('messages')) {
		$countMessagesUnread = Messages::getCountUnread(Yii::app()->user->id);
		$bageMessages = ($countMessagesUnread > 0) ? "&nbsp<span class=\"badge\">{$countMessagesUnread}</span>" : '';
	}
?>

<div class="bootnavbar-delimiter"></div>
<div class="container-fluid">
    <div class="row-fluid">
        <div class="span3">
            <div class="well sidebar-nav">
				<?php
					$this->widget('bootstrap.widgets.TbMenu', array(
					'type' => 'list',
					'encodeLabel' => false,
					'items' => array(
						array('label' => tc('Listings'), 'visible' => (Yii::app()->user->checkAccess('apartments_admin') || Yii::app()->user->checkAccess('comments_admin') || Yii::app()->user->checkAccess('apartmentsComplain_admin') )),
						array('label' => tc('Listings') . $bageListings, 'icon' => 'icon-list-alt', 'url' => $baseUrl . '/apartments/backend/main/admin', 'active' => isActive('apartments'), 'visible' => Yii::app()->user->checkAccess('apartments_admin')),
						array('label' => tc('List your property'), 'icon' => 'icon-plus-sign', 'url' => $baseUrl . '/apartments/backend/main/create', 'active' => isActive('apartments.create'), 'visible' => Yii::app()->user->checkAccess('apartments_admin')),
						array('label' => tc('Comments') . $bageComments, 'icon' => 'icon-list-alt', 'url' => $baseUrl . '/comments/backend/main/admin', 'active' => isActive('comments'), 'visible' => Yii::app()->user->checkAccess('comments_admin')),
						array('label' => tc('Complains') . $bageComplain, 'icon' => 'icon-list-alt', 'url' => $baseUrl . '/apartmentsComplain/backend/main/admin', 'active' => isActive('apartmentsComplain'), 'visible' => issetModule('apartmentsComplain') && Yii::app()->user->checkAccess('apartmentsComplain_admin')),
						array('label' => tt('Booking apartment', 'booking') . $bageBooking, 'icon' => 'icon-file', 'url' => $baseUrl . '/bookingtable/backend/main/admin', 'active' => isActive('bookingtable'), 'visible' => issetModule('bookingtable') && Yii::app()->user->checkAccess('bookingtable_admin')),

						array('label' => tc('Users'), 'visible' => Yii::app()->user->checkAccess('users_admin')),
						array('label' => tc('Users'), 'icon' => 'icon-list-alt', 'url' => $baseUrl . '/users/backend/main/admin', 'active' => isActive('users'), 'visible' => Yii::app()->user->checkAccess('users_admin')),
						//array('label' => tt('Add user', 'users'), 'icon' => 'icon-plus-sign', 'url' => $baseUrl . '/users/backend/main/create', 'active' => isActive('users.create'), 'visible' => Yii::app()->user->checkAccess('users_admin')),

						array('label' => tt('Reviews', 'reviews'), 'visible' => Yii::app()->user->checkAccess('reviews_admin')),
						array('label' => tt('Reviews_management', 'reviews') . $bageReviews, 'icon' => 'icon-list-alt', 'url' => $baseUrl . '/reviews/backend/main/admin', 'active' => isActive('reviews'), 'visible' => Yii::app()->user->checkAccess('reviews_admin')),
						array('label' => tt('Add_feedback', 'reviews'), 'icon' => 'icon-plus-sign', 'url' => $baseUrl . '/reviews/backend/main/create', 'active' => isActive('reviews.create'), 'visible' => Yii::app()->user->checkAccess('reviews_admin')),

						array('label' => tt('Clients', 'clients'), 'visible' => Yii::app()->user->checkAccess('clients_admin')),
						array('label' => tt('Clients', 'clients'), 'icon' => 'icon-list-alt', 'url' => $baseUrl . '/clients/backend/main/admin', 'active' => isActive('clients'), 'visible' => Yii::app()->user->checkAccess('clients_admin')),

						array('label' => tc('Content'), 'visible' => (Yii::app()->user->checkAccess('news_admin') || Yii::app()->user->checkAccess('articles_admin') || Yii::app()->user->checkAccess('menumanager_admin')) ),
						array('label' => tc('News'), 'icon' => 'icon-file', 'url' => $baseUrl . '/news/backend/main/admin', 'active' => isActive('news'), 'visible' => Yii::app()->user->checkAccess('news_admin')),
						array('label' => tc('Q&As'), 'icon' => 'icon-file', 'url' => $baseUrl . '/articles/backend/main/admin', 'active' => isActive('articles'), 'visible' => Yii::app()->user->checkAccess('articles_admin')),
						array('label' => tc('Top menu items'), 'icon' => 'icon-file', 'url' => $baseUrl . '/menumanager/backend/main/admin', 'active' => isActive('menumanager'), 'visible' => Yii::app()->user->checkAccess('menumanager_admin')),
						array('label' => tc('Info pages'), 'icon' => 'icon-file', 'url' => $baseUrl . '/infopages/backend/main/admin', 'active' => isActive('infopages'), 'visible' => Yii::app()->user->checkAccess('infopages_admin')),

						array('label' => tt('Messages', 'messages'), 'visible' => issetModule('messages') && Yii::app()->user->checkAccess('messages_admin')),
						array('label' => tt('Messages', 'messages') . $bageMessages, 'icon' => 'icon-list-alt', 'url' => $baseUrl . '/messages/backend/main/admin', 'active' => (isActive('messages') || isActive('messages.read')), 'visible' => issetModule('messages') && Yii::app()->user->checkAccess('messages_admin')),
						array('label' => tt('Mailing messages', 'messages'), 'icon' => 'icon-list-alt', 'url' => $baseUrl . '/messages/backend/mailing/admin', 'active' => isActive('messages.mailing'), 'visible' => issetModule('messages') && Yii::app()->user->checkAccess('messages_admin')),

						array('label' => tc('Payments'), 'visible' => issetModule('payment') && (Yii::app()->user->checkAccess('payment_admin') || Yii::app()->user->checkAccess('paidservices_admin'))),
						array('label' => tc('Tariff Plans'), 'icon' => 'icon-shopping-cart', 'url' => $baseUrl . '/tariffPlans/backend/main/admin', 'active' => isActive('tariffPlans'), 'visible' => issetModule('tariffPlans') && issetModule('paidservices') && Yii::app()->user->checkAccess('tariff_plans_admin')),
						array('label' => tc('Paid services'), 'icon' => 'icon-shopping-cart', 'url' => $baseUrl . '/paidservices/backend/main/admin', 'active' => isActive('paidservices'), 'visible' => issetModule('payment') && Yii::app()->user->checkAccess('paidservices_admin')),
						array('label' => tc('Manage payments') . $bagePayments, 'icon' => 'icon-shopping-cart', 'url' => $baseUrl . '/payment/backend/main/admin', 'active' => isActive('payment'), 'visible' => issetModule('payment') && Yii::app()->user->checkAccess('payment_admin')),
						array('label' => tc('Payment systems'), 'icon' => 'icon-wrench', 'url' => $baseUrl . '/payment/backend/paysystem/admin', 'active' => isActive('payment.paysystem'), 'visible' => issetModule('payment') && Yii::app()->user->checkAccess('payment_admin')),

						array('label' => tc('References'), 'visible' => Yii::app()->user->checkAccess('all_reference_admin')),
						array('label' => tc('Categories of references'), 'icon' => 'icon-asterisk', 'url' => $baseUrl . '/referencecategories/backend/main/admin', 'active' => isActive('referencecategories'), 'visible' => Yii::app()->user->checkAccess('all_reference_admin')),
						array('label' => tc('Values of references'), 'icon' => 'icon-asterisk', 'url' => $baseUrl . '/referencevalues/backend/main/admin', 'active' => isActive('referencevalues'), 'visible' => Yii::app()->user->checkAccess('all_reference_admin')),
						array('label' => tc('Reference "View:"'), 'icon' => 'icon-asterisk', 'url' => $baseUrl . '/windowto/backend/main/admin', 'active' => isActive('windowto'), 'visible' => Yii::app()->user->checkAccess('all_reference_admin')),
						array('label' => tc('Reference "Check-in"'), 'icon' => 'icon-asterisk', 'url' => $baseUrl . '/timesin/backend/main/admin', 'active' => isActive('timesin'), 'visible' => Yii::app()->user->checkAccess('all_reference_admin')),
						array('label' => tc('Reference "Check-out"'), 'icon' => 'icon-asterisk', 'url' => $baseUrl . '/timesout/backend/main/admin', 'active' => isActive('timesout'), 'visible' => Yii::app()->user->checkAccess('all_reference_admin')),
						array('label' => tc('Reference "Property types"'), 'icon' => 'icon-asterisk', 'url' => $baseUrl . '/apartmentObjType/backend/main/admin', 'active' => isActive('apartmentObjType'), 'visible' => Yii::app()->user->checkAccess('all_reference_admin')),
						array('label' => tc('Reference "City/Cities"'), 'icon' => 'icon-asterisk', 'url' => $baseUrl . '/apartmentCity/backend/main/admin', 'active' => isActive('apartmentCity'), 'visible' => (!issetModule('location') && Yii::app()->user->checkAccess('all_reference_admin'))),

						array('label' => tc('Blockip'), 'visible' => Yii::app()->user->checkAccess('blockip_admin')),
						array('label' => tc('Blockip'), 'icon' => 'icon-list-alt', 'url' => $baseUrl . '/blockIp/backend/main/admin', 'active' => isActive('blockIp'), 'visible' => Yii::app()->user->checkAccess('blockip_admin')),

						array('label' => tc('Settings'), 'visible' => Yii::app()->user->checkAccess('all_settings_admin')),
						array('label' => tc('Settings'), 'icon' => 'icon-wrench', 'url' => $baseUrl . '/configuration/backend/main/admin', 'active' => isActive('configuration'), 'visible' => Yii::app()->user->checkAccess('all_settings_admin')),
						array('label' => tc('Manage modules'), 'icon' => 'icon-wrench', 'url' => $baseUrl . '/modules/backend/main/admin', 'active' => isActive('modules'), 'visible' => Yii::app()->user->checkAccess('all_settings_admin')),
						array('label' => tc('Images'), 'icon' => 'icon-wrench', 'url' => $baseUrl . '/images/backend/main/index', 'active' => isActive('images'), 'visible' => Yii::app()->user->checkAccess('all_settings_admin')),
						array('label' => tc('Change admin password'), 'icon' => 'icon-wrench', 'url' => $baseUrl . '/adminpass/backend/main/index', 'active' => isActive('adminpass'), 'visible' => Yii::app()->user->checkAccess('all_settings_admin')),
						array('label' => tc('Site service '), 'icon' => 'icon-wrench', 'url' => $baseUrl . '/service/backend/main/admin', 'active' => isActive('service'), 'visible' => issetModule('service'), 'visible' => Yii::app()->user->checkAccess('all_settings_admin')),
						array('label' => tc('Authentication services'), 'icon' => 'icon-wrench', 'url' => $baseUrl . '/socialauth/backend/main/admin', 'active' => isActive('socialauth'), 'visible' => issetModule('socialauth'), 'visible' => Yii::app()->user->checkAccess('all_settings_admin')),
						array('label' => tc('Manage themes'), 'icon' => 'icon-wrench', 'url' => $baseUrl . '/themes/backend/main/admin', 'active' => isActive('themes'), 'visible' => issetModule('themes'), 'visible' => Yii::app()->user->checkAccess('all_settings_admin')),

						array('label' => tc('Languages and currency'), 'visible' => Yii::app()->user->checkAccess('all_lang_and_currency_admin')),
						array('label' => tc('Languages'), 'icon' => 'icon-globe', 'url' => $baseUrl . '/lang/backend/main/admin', 'active' => isActive('lang'), 'visible' => !isFree() && Yii::app()->user->checkAccess('all_lang_and_currency_admin')),
						array('label' => tc('Translations'), 'icon' => 'icon-wrench', 'url' => $baseUrl . '/translateMessage/backend/main/admin', 'active' => isActive('translateMessage'), 'visible' => Yii::app()->user->checkAccess('all_lang_and_currency_admin')),
						array('label' => tc('Currencies'), 'icon' => 'icon-wrench', 'url' => $baseUrl . '/currency/backend/main/admin', 'active' => isActive('currency'), 'visible' => issetModule('currency') && Yii::app()->user->checkAccess('all_lang_and_currency_admin')),

						array('label' => tc('Modules'), 'visible' => Yii::app()->user->checkAccess('all_modules_admin') && (issetModule('notifier') || issetModule('slider') || issetModule('advertising') || issetModule('iecsv') || issetModule('formdesigner') || issetModule('socialposting'))),
						array('label' => tc('Mail editor'), 'icon' => 'icon-circle-arrow-right', 'url' => $baseUrl . '/notifier/backend/main/admin', 'active' => isActive('notifier'), 'visible' => issetModule('notifier') && Yii::app()->user->checkAccess('all_modules_admin')),
						array('label' => tc('Slide-show on the Home page'), 'icon' => 'icon-circle-arrow-right', 'url' => $baseUrl . '/slider/backend/main/admin', 'active' => isActive('slider'), 'visible' => issetModule('slider') && Yii::app()->user->checkAccess('all_modules_admin')),
						array('label' => tc('Import / Export'), 'icon' => 'icon-circle-arrow-right', 'url' => $baseUrl . '/iecsv/backend/main/admin', 'active' => isActive('iecsv'), 'visible' => issetModule('iecsv') && Yii::app()->user->checkAccess('all_modules_admin')),
						array('label' => tc('Advertising banners'), 'icon' => 'icon-circle-arrow-right', 'url' => $baseUrl . '/advertising/backend/advert/admin', 'active' => isActive('advertising'), 'visible' => issetModule('advertising') && Yii::app()->user->checkAccess('all_modules_admin')),
						array('label' => tc('The forms designer'), 'icon' => 'icon-circle-arrow-right', 'url' => $baseUrl . '/formdesigner/backend/main/admin', 'active' => (isActive('formdesigner') || isActive('formeditor')), 'visible' => issetModule('formdesigner') && Yii::app()->user->checkAccess('all_modules_admin')),
						array('label' => tt('Services of automatic posting', 'socialposting'), 'icon' => 'icon-circle-arrow-right', 'url' => $baseUrl . '/socialposting/backend/main/admin', 'active' => isActive('socialposting'), 'visible' => issetModule('socialposting') && Yii::app()->user->checkAccess('all_modules_admin')),

						array('label' => tc('Location module'), 'visible' => (issetModule('location') && Yii::app()->user->checkAccess('all_reference_admin'))),
						array('label' => tc('Countries'), 'icon' => 'icon-globe', 'url' => $baseUrl . '/location/backend/country/admin', 'visible' => (issetModule('location') && Yii::app()->user->checkAccess('all_reference_admin')), 'active' => isActive('location.country')),
						array('label' => tc('Regions'), 'icon' => 'icon-globe', 'url' => $baseUrl . '/location/backend/region/admin', 'visible' => (issetModule('location') && Yii::app()->user->checkAccess('all_reference_admin')), 'active' => isActive('location.region')),
						array('label' => tc('Cities'), 'icon' => 'icon-globe', 'url' => $baseUrl . '/location/backend/city/admin', 'visible' => (issetModule('location') && Yii::app()->user->checkAccess('all_reference_admin')), 'active' => isActive('location.city')),

						array('label' => tc('Other'), 'visible' => Yii::app()->user->checkAccess('news_admin')),
						array('label' => tc('News about Open Real Estate CMS'), 'icon' => 'icon-home', 'url' => $baseUrl . '/news/backend/main/product', 'active' => isActive('news.product'), 'visible' => Yii::app()->user->checkAccess('news_admin')),
					),
				));
				?>
            </div>
            <!--/.well -->
        </div>
        <!--/span-->
        <div class="span9">
			<?php echo $content; ?>
        </div>
        <!--/span-->
    </div>
    <!--/row-->

    <hr>

    <footer>
        <p>&copy;&nbsp;<?php echo ORE_VERSION_NAME . ' ' . ORE_VERSION . ', ' . date('Y'); ?></p>
    </footer>

    <div id="loading" style="display:none;"><?php echo Yii::t('common', 'Loading content...'); ?></div>
	<?php
	Yii::app()->clientScript->registerCoreScript('jquery');
	Yii::app()->clientScript->registerScriptFile($baseThemeUrl . '/js/jquery.dropdownPlain.js', CClientScript::POS_HEAD);
	Yii::app()->clientScript->registerScriptFile($baseThemeUrl . '/js/adminCommon.js', CClientScript::POS_HEAD);
	Yii::app()->clientScript->registerScriptFile($baseThemeUrl . '/js/habra_alert.js', CClientScript::POS_END);

	$this->widget('application.modules.fancybox.EFancyBox', array(
			'target' => 'a.fancy',
			'config' => array(
				'ajax' => array('data' => "isFancy=true"),
				'titlePosition' => 'inside',
			),
		)
	);
	?>
</div>
<!--/.fluid-container-->

<?php $this->beginWidget('bootstrap.widgets.TbModal', array('id'=>'temp_modal')); ?>
<div class="modal-header">
    <a class="close" data-dismiss="modal">&times;</a>
    <h3 id="temp_modal_title">&nbsp;&nbsp;</h3>
</div>
<div class="modal-body">
	<div id="temp_modal_content"><?php echo tc('Loading content...');?></div>
</div>
<?php $this->endWidget(); ?>

<script type="text/javascript">
	var tempModal = {
		setContent: function(content){
			$('#temp_modal_content').html(content);
		},
		open: function(){
			$("#temp_modal").modal("show");
		},
		close: function(){
			tempModal.setTitle('');
			tempModal.setContent('<?php echo tc('Loading content...');?>');
			$("#temp_modal").modal("hide");
		},
		init: function(){
			$('a.tempModal').each(function(el){
				var objUrl = $(this).attr('href');
				if(objUrl != ''){
					$(this).on('click', function(event){
						$('#temp_modal_content').load(objUrl);
						var title = $(this).attr('data-original-title');

						if (!title || title.length < 1)
							title = $(this).attr('title');

						if(title){
							tempModal.setTitle(title);
						}
						tempModal.open();
						event.preventDefault();
					})
				}
			});
		},
		setTitle: function(title){
			$('#temp_modal_title').html(title);
		}
	}

	$(function(){
		tempModal.init();
	});
</script>

</body>
</html>
