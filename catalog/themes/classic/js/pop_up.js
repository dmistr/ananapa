function showPopup( promoCode, turnOff )
{
	if ( turnOff )
	{
		jQuery.cookie('popup_' + promoCode, 1000, { expires: 1, path: '/' });
	}
	else
	{
		if ( jQuery.cookie('popup_' + promoCode) < 1 ) // это значит показать 1 раз
		{

			jQuery('#popup_' + promoCode + '_window').show();
			jQuery('#popup_' + promoCode + '_no-thanks').show();

			var currentPromoHit = jQuery.cookie('popup_' + promoCode);

			if ( currentPromoHit > 0)
			{
				++currentPromoHit;
			}
			else
			{
				currentPromoHit = 1;
			}

			jQuery.cookie('popup_' + promoCode, currentPromoHit, { expires: 1, path: '/' });

		}

		jQuery('#popup_' + promoCode + '_window a').click(function() {
			jQuery('#popup_' + promoCode + '_window').hide();
			jQuery('#popup_' + promoCode + '_no-thanks').hide();
		});
		jQuery('#popup_' + promoCode + '_overlay').click(function() {
			jQuery('#popup_' + promoCode + '_window').hide();
			jQuery('#popup_' + promoCode + '_no-thanks').hide();
		});
		jQuery('#popup_' + promoCode + '_no-thanks').click(function() {
			jQuery('#popup_' + promoCode + '_window').hide();
			jQuery('#popup_' + promoCode + '_no-thanks').hide();
		});
	}
}
