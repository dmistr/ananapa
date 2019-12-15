
        var updateText = 'Загрузка ...';
        var resultBlock = 'appartment_box';
        var indicator = '/catalog/themes/classic/images/pages/indicator.gif';
        var bg_img = '/catalog/themes/classic/images/pages/opacity.png';

        var useGoogleMap = 0;
        var useYandexMap = 0;
        var useOSMap = 1;

        var modeListShow = 'block';

        $('div.appartment_item').live('mouseover mouseout', function(event){
            if (event.type == 'mouseover') {
             $(this).find('div.apartment_item_edit').show();
            } else {
             $(this).find('div.apartment_item_edit').hide();
            }
        });

        function setListShow(mode){
            modeListShow = mode;
            reloadApartmentList(urlsSwitching[mode]);
        };


        $(function () {
            if(modeListShow == 'map'){
                list.apply();
            }
        });
    