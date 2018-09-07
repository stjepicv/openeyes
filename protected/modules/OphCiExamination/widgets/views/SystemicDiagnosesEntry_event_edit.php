<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2017
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2017, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

use OEModule\OphCiExamination\models\SystemicDiagnoses_Diagnosis;
?>

<?php
if (!isset($values)) {
    $values = array(
        'id' => $diagnosis->id,
        'disorder_id' => $diagnosis->disorder_id,
        'disorder_display' => $diagnosis->disorder ? $diagnosis->disorder->term : '',
        'has_disorder' => $diagnosis->has_disorder,
        'side_id' => $diagnosis->side_id,
        'side_display' => $diagnosis->side ? $diagnosis->side->adjective : 'N/A',
        'date' => $diagnosis->date,
        'date_display' => $diagnosis->getDisplayDate(),
    );
}

    if (isset($values['date']) && strtotime($values['date'])) {
        list($start_sel_year, $start_sel_month, $start_sel_day) = explode('-', $values['date']);
    } else {
        $start_sel_day = $start_sel_month = null;
        $start_sel_year = date('Y');
        $values['date'] = $start_sel_year . '-00-00'; // default to the year displayed in the select dropdowns
    }

    $is_new_record = isset($diagnosis) && $diagnosis->isNewRecord ? true : false;

    $mandatory = !$removable;
?>

    <tr data-key="<?=$row_count;?>">
        <td>
            <?=$values['disorder_display'];?>
            <input type="hidden" name="<?= $field_prefix ?>[id]" value="<?=$values['id'] ?>" />
            <input type="hidden" name="<?= $field_prefix ?>[disorder_id]" value="<?=$values['disorder_id'] ?>" />
        </td>

        <td id="<?="{$model_name}_{$row_count}_checked_status"?>">
            <?php
                if ($removable) {
                    if ($values['has_disorder'] === SystemicDiagnoses_Diagnosis::$NOT_PRESENT) { ?>
                        <label class="inline highlight">
                            <?php echo \CHtml::radioButton(
                                $field_prefix . '[has_disorder]',
                                $values['has_disorder'] === SystemicDiagnoses_Diagnosis::$PRESENT,
                                array('value' => SystemicDiagnoses_Diagnosis::$PRESENT)
                            ); ?>
                            yes
                        </label>
                        <label class="inline highlight">
                            <?php echo \CHtml::radioButton(
                                $field_prefix . '[has_disorder]',
                                $values['has_disorder'] === SystemicDiagnoses_Diagnosis::$NOT_PRESENT,
                                array('value' => SystemicDiagnoses_Diagnosis::$NOT_PRESENT)
                            ); ?>
                            no
                        </label>
                    <?php } else {
                        echo CHtml::hiddenField($field_prefix . '[has_disorder]', (string)SystemicDiagnoses_Diagnosis::$PRESENT);
                    }
                } else {
                    ?>
                    <label class="inline highlight">
                        <?php echo \CHtml::radioButton(
                            $field_prefix . '[has_disorder]',
                            $posted_not_checked,
                            array('value' => SystemicDiagnoses_Diagnosis::$NOT_CHECKED)
                        ); ?>
                        Not checked
                    </label>
                    <label class="inline highlight">
                        <?php echo \CHtml::radioButton(
                            $field_prefix . '[has_disorder]',
                            $values['has_disorder'] === SystemicDiagnoses_Diagnosis::$PRESENT,
                            array('value' => SystemicDiagnoses_Diagnosis::$PRESENT)
                        ); ?>
                        yes
                    </label>
                    <label class="inline highlight">
                        <?php echo \CHtml::radioButton(
                            $field_prefix . '[has_disorder]',
                            $values['has_disorder'] === SystemicDiagnoses_Diagnosis::$NOT_PRESENT,
                            array('value' => SystemicDiagnoses_Diagnosis::$NOT_PRESENT)
                        ); ?>
                        no
                    </label>
                    <?php
                }
            ?>
        </td>

        <?php $this->widget('application.widgets.EyeSelector', [
                'inputNamePrefix' => $field_prefix,
                'selectedEyeId' => $values['side_id'] ? $values['side_id'] : EyeSelector::$NOT_CHECKED
        ]); ?>

        <td>
            <input id="systemic-diagnoses-datepicker-<?= $row_count; ?>"
                   class="date" placeholder="yyyy-mm-dd"
                   name="<?= $field_prefix ?>[date]"
                   value="<?=$values['date'] ?>"
                   style="width: 90px"
                   autocomplete="off">
        </td>
        <td>
            <i class="js-has-tooltip oe-i info small pad right" data-tooltip-content="You can enter date format as yyyy-mm-dd, or yyyy-mm or yyyy."></i>
        </td>
        <?php if ($removable) : ?>
            <td>
                <i class="oe-i trash"></i>
            </td>
        <?php else: ?>
            <td>read only</td>
        <?php endif; ?>
    </tr>

<?php
$assetManager = Yii::app()->getAssetManager();
$widgetPath = $assetManager->publish('protected/widgets/js');
Yii::app()->clientScript->registerScriptFile($widgetPath . '/EyeSelector.js');
?>