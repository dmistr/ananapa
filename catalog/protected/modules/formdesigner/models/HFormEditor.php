<?php

/* * ********************************************************************************************
 *								Open Real Estate
 *								----------------
 * 	version				:	V1.12.0
 * 	copyright			:	(c) 2015 Monoray
 * 							http://monoray.net
 *							http://monoray.ru
 *
 * 	website				:	http://open-real-estate.info/en
 *
 * 	contact us			:	http://open-real-estate.info/en/contact-us
 *
 * 	license:			:	http://open-real-estate.info/en/license
 * 							http://open-real-estate.info/ru/license
 *
 * This file is part of Open Real Estate
 *
 * ********************************************************************************************* */
class HFormEditor
{
    private static $_cache;

    public static function getReferencesList()
    {
        Yii::import('application.modules.referencecategories.models.ReferenceCategories');
        $ref = ReferenceCategories::model()->findAllByAttributes(array(
            'type' => ReferenceCategories::TYPE_FOR_EDITOR,
        ));

        return $ref ? CHtml::listData($ref, 'id', 'title') : array();
    }

    public static function getGeneralFields()
    {
        $cache = FormDesigner::getCacheByView();

        return isset($cache[FormDesigner::VIEW_IN_GENERAL]) ? $cache[FormDesigner::VIEW_IN_GENERAL] : array();
    }

    public static function getExtendedFields()
    {
        $cache = FormDesigner::getCacheByView();

        return isset($cache[FormDesigner::VIEW_IN_EXTENDED]) ? $cache[FormDesigner::VIEW_IN_EXTENDED] : array();
    }

    public static function getRulesForModel()
    {
        $all = self::getAllFields();

        $rules = array();
        $fieldsSafe = array();
        foreach ($all as $row) {
            if ($row['rules'] == FormDesigner::RULE_REQUIRED || $row['rules'] == FormDesigner::RULE_REQUIRED_NUMERICAL) {
                $rules[] = array($row['field'], 'requiredAdvanced');
            }

            if ($row['rules'] == FormDesigner::RULE_NUMERICAL || $row['rules'] == FormDesigner::RULE_REQUIRED_NUMERICAL) {
                $rules[] = array($row['field'], 'numerical');
            }

            $fieldsSafe[] = $row['field'];
        }

        if ($fieldsSafe) {
            $rules[] = array(implode(', ', $fieldsSafe), 'safe');
        }

        return $rules;
    }

    public static function getAllFields()
    {
        if (!isset(self::$_cache['all'])) {
            $general = self::getGeneralFields();
            $extended = self::getExtendedFields();

            self::$_cache['all'] = CMap::mergeArray($general, $extended);
        }

        return self::$_cache['all'];
    }

	public static function renderViewRows($rows, Apartment $model) {
		if (!$rows) {
			return '';
		}

		foreach ($rows as $row) {
			if (!$model->canShowInView($row['field'])) {
				continue;
			}

			if ($row['standard_type'] != FormDesigner::STANDARD_TYPE_NEW && file_exists(ROOT_PATH . '/protected/views/common/apartments/fields/' . $row['field'] . '.php')) { //
				Yii::app()->controller->renderPartial('//../views/common/apartments/fields/' . $row['field'], array('data' => $model));
				continue;
			}

			if ($row->type == FormDesigner::TYPE_REFERENCE) {
				$sql = "SELECT title_" . Yii::app()->language . " FROM {{apartment_reference_values}} WHERE id=" . $model->$row['field'];
				$value = CHtml::encode(Yii::app()->db->createCommand($sql)->queryScalar());
			}
			else {
				$value = is_string($model->$row['field']) ? CHtml::encode($model->$row['field']) : '???';
				if($row->type == FormDesigner::TYPE_TEXT_AREA_WS){
					$value = purify($model->$row['field']);
				}
			}

			if ($row->type == FormDesigner::TYPE_INT && $row->measure_unit) {
				$value .= '&nbsp;' . CHtml::encode($row->measure_unit);
			}

			if ($value) {
				if ($row['standard_type'] > 0) {
					echo '<dt>' . CHtml::encode($model->getAttributeLabel($row['field'])) . ':</dt>';
				}
				else {
					echo '<dt>' . CHtml::encode($row['label_' . Yii::app()->language]) . ':</dt>';
				}

				if ($row->field == 'phone') {
                    if (issetModule('tariffPlans') && issetModule('paidservices') && ($model->owner_id != Yii::app()->user->id)) {
                        if (Yii::app()->user->isGuest) {
                            $defaultTariffInfo = TariffPlans::getFullTariffInfoById(TariffPlans::DEFAULT_TARIFF_PLAN_ID);

                            if (!$defaultTariffInfo['showPhones']) {
                                echo '<dd>'.Yii::t('module_tariffPlans', 'Please <a href="{n}">login</a> to view', Yii::app()->controller->createUrl('/site/login')).'</dd>';
                            }
                            else {
                                echo '<dd><span id="owner-phone">' . CHtml::link(tc('Show phone'), 'javascript: void(0);', array('onclick' => 'generatePhone();')) . '</span>' . '</dd>';
                            }
                        }
                        else {
                            if (TariffPlans::checkAllowShowPhone())
                                echo '<dd>' . $value . '</dd>';
                            else
                                echo '<dd>'.Yii::t('module_tariffPlans', 'Please <a href="{n}">change the tariff plan</a> to view', Yii::app()->controller->createUrl('/tariffPlans/main/index')).'</dd>';
                        }
                    }
                    else {
                        echo '<dd><span id="owner-phone">' . CHtml::link(tc('Show phone'), 'javascript: void(0);', array('onclick' => 'generatePhone();')) . '</span>' . '</dd>';
                    }
				}
				else {
					echo '<dd>' . $value . '</dd>';
				}
			}
		}
	}

    public static function renderFormRows($rows, Apartment $model) {
		if (!$rows) {
			return '';
		}

		$isShowTip = true;

		foreach ($rows as $row) {
			if (!$model->canShowInForm($row['field'])) {
				continue;
			}

            // если есть файл отображения для формы
			if ($row['standard_type'] == FormDesigner::STANDARD_TYPE_ORIGINAL_VIEW) {
				Yii::app()->controller->renderPartial('//../views/common/apartments/backend/fields/' . $row['field'], array('model' => $model));
				continue;
			}

			$required = ($row->rules == FormDesigner::RULE_REQUIRED || $row->rules == FormDesigner::RULE_REQUIRED_NUMERICAL) ? array('required' => true) : array();

			echo '<div class="rowold">';
			if ($row['standard_type'] == FormDesigner::STANDARD_TYPE_NEW) {
				echo CHtml::label($row['label_' . Yii::app()->language], get_class($model) . '_' . $row['field'], $required);
			} else {
				echo $row['is_i18n'] ? '' : CHtml::activeLabel($model, $row['field']);
			}

			if ($row['is_i18n'])
				$isShowTip = false;

			if ($isShowTip)
				echo Apartment::getTip($row['field']);

			switch ($row['type']) {
				case FormDesigner::TYPE_TEXT:
					if ($row['is_i18n']) {
						Yii::app()->controller->widget('application.modules.lang.components.langFieldWidget', array(
							'model' => $model,
							'field' => $row['field'],
							'type' => 'string'
						));
					} else {
						echo CHtml::activeTextField($model, $row['field'], array('class' => 'width500', 'maxlength' => 255));
					}
					break;

				case FormDesigner::TYPE_INT:
					echo CHtml::activeTextField($model, $row['field'], array('class' => 'width70', 'maxlength' => 255));
					if ($row->measure_unit) {
						echo '&nbsp;' . $row->measure_unit;
					}
					break;

				case FormDesigner::TYPE_TEXT_AREA:
					if ($row['is_i18n']) {
						Yii::app()->controller->widget('application.modules.lang.components.langFieldWidget', array(
							'model' => $model,
							'field' => $row['field'],
							'type' => 'text'
						));
					} else {
						echo CHtml::activeTextArea($model, $row['field'], array('class' => 'width500 height200'));
					}
					break;

				case FormDesigner::TYPE_TEXT_AREA_WS:
                    if ($row['is_i18n']) {
                        Yii::app()->controller->widget('application.modules.lang.components.langFieldWidget', array(
                            'model' => $model,
                            'field' => $row['field'],
                            'type' => 'text-editor'
                        ));
                    } else {
                        // даем пользователям ограниченый набор форматирования
                        $toolbar = array(
                            array('Source', '-', 'Bold', 'Italic', 'Underline', 'Strike'),
                           // array('Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo'),
                            array('NumberedList', 'BulletedList', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock'),
                            //array('Styles', 'Format', 'Font', 'FontSize', 'TextColor', 'BGColor'),
                            //array('Image', 'Link', 'Unlink', 'SpecialChar'),
                        );

						$filebrowserImageUploadUrl = '';

                        if (Yii::app()->user->checkAccess('backend_access')) { // if admin - enable upload image
                            $filebrowserImageUploadUrl = Yii::app()->createAbsoluteUrl('/site/uploadimage', array('type' => 'imageUpload', Yii::app()->request->csrfTokenName => Yii::app()->request->csrfToken));

                            $toolbar = array(
                                array('Source', '-', 'Bold', 'Italic', 'Underline', 'Strike'),
                                array('Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo'),
                                array('NumberedList', 'BulletedList', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock'),
                                array('Styles', 'Format', 'Font', 'FontSize', 'TextColor', 'BGColor'),
                                array('Image', 'Link', 'Unlink', 'SpecialChar'),
                            );
                        }

                        Yii::app()->controller->widget('application.extensions.editMe.widgets.ExtEditMe', array(
                            'model' => $model,
                            'attribute' => $row['field'],
                            'toolbar' => $toolbar,
                            'filebrowserImageUploadUrl' => $filebrowserImageUploadUrl,
                            'htmlOptions' => array('id' => $model->id)
                        ));
                    }

					break;

				case FormDesigner::TYPE_REFERENCE:
					echo CHtml::activeDropDownList($model, $row['field'], CMap::mergeArray(array("" => Yii::t('common', 'Please select')), FormDesigner::getListByCategoryID($row->reference_id)));
					break;
			}
			echo '</div>';
		}
    }

    public static function existValueInRows($rows, Apartment $model){
        foreach($rows as $row){
            if(!$model->canShowInView($row['field'])){
                continue;
            }
            return true;
        }
        return false;
    }
}