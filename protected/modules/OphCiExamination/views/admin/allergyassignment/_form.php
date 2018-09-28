<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2017
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public
 * License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later
 * version. OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the
 * implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more
 * details. You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled
 * COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2017, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>

<div class="cols-7">
    <table class="standard cols-full">
        <h2><?php echo $title ?></h2>
        <hr class="divider">
        <colgroup>
            <col class="cols-3">
            <col class="cols-5">
        </colgroup>
        <tbody>
        <tr>
            <td>Name</td>
            <td class="cols-full">
                <?php echo CHtml::activeTelField(
                    $model,
                    'name',
                    ['class' => 'cols-full']
                ); ?>
            </td>
        </tr>
        <tr>
            <td class="fade">Subspecialty:</td>
            <td>
                <?php echo CHtml::dropDownList(
                    'subspecialty-id',
                    $model->subspecialty_id,
                    Subspecialty::model()->getList(),
                    ['empty' => '- Please select -', 'class' => 'cols-full']
                ) ?>
            </td>
        </tr>
        <tr>
            <td class="fade">Context</td>
            <td>
                <?php if (!$model->subspecialty_id) { ?>
                    <?php echo CHtml::dropDownList(
                        'firm-id',
                        '',
                        array(),
                        [
                            'class' => 'cols-full',
                            'empty' => 'All ' . Firm::contextLabel() . 's',
                            'disabled' => 'disabled',
                        ]
                    ) ?>
                <?php } else { ?>
                    <?php echo CHtml::dropDownList(
                        'firm-id',
                        $model->firm_id,
                        Firm::model()->getList($model->subspecialty_id),
                        array(
                            'class' => 'cols-full',
                            'empty' => 'All ' . Firm::contextLabel() . 's',
                            'disabled' => (@$_POST['emergency_list'] == 1 ? 'disabled' : ''),
                        )
                    ) ?>
                <?php } ?>
            </td>
        </tr>
        </tbody>
    </table>

    <?php
    $examination_allergy_listdata = CHtml::listData(
        OEModule\OphCiExamination\models\OphCiExaminationAllergy::model()->findAll(),
        'id',
        'name'
    );
    $gender_models = Gender::model()->findAll();
    $gender_options = CHtml::listData($gender_models, function ($gender_model) {
        return CHtml::encode($gender_model->name)[0];
    }, 'name');
    ?>

    <div id="allergy" class="data-group">
        <?php
        $columns = array(
            array(
                'header' => 'Allergy Name',
                'name' => 'Allergy Name',
                'type' => 'raw',
                'value' => function ($data, $row) use ($examination_allergy_listdata) {
                    return
                        CHtml::hiddenField(
                            "OEModule_OphCiExamination_models_OphCiExaminationAllergySetEntry[$row][id]",
                            $data->id
                        ) .
                        CHtml::dropDownList(
                            "OEModule_OphCiExamination_models_OphCiExaminationAllergySetEntry[$row][ophciexamination_allergy_id]",
                            $data->ophciexamination_allergy_id,
                            $examination_allergy_listdata,
                            array('empty' => '- select --')
                        );
                }
            ),
            array(
                'header' => 'Sex Specific',
                'name' => 'gender',
                'type' => 'raw',
                'value' => function ($data, $row) use ($gender_options) {
                    echo CHtml::dropDownList(
                        "OEModule_OphCiExamination_models_OphCiExaminationAllergySetEntry[$row][gender]",
                        $data->gender,
                        $gender_options,
                        array('empty' => '-- select --')
                    );
                }
            ),
            array(
                'header' => 'Age Specific (Min)',
                'name' => 'age_min',
                'type' => 'raw',
                'value' => function ($data, $row) {
                    return CHtml::numberField(
                        "OEModule_OphCiExamination_models_OphCiExaminationAllergySetEntry[$row][age_min]",
                        $data->age_min,
                        array("style" => "width:55px;")
                    );
                }
            ),
            array(
                'header' => 'Age Specific (Max)',
                'name' => 'age_max',
                'type' => 'raw',
                'value' => function ($data, $row) {
                    return CHtml::numberField(
                        "OEModule_OphCiExamination_models_OphCiExaminationAllergySetEntry[$row][age_max]",
                        $data->age_max,
                        array("style" => "width:55px;")
                    );
                }
            ),
            array(
                'header' => '',
                'type' => 'raw',
                'value' => function ($data, $row) {
                    return CHtml::link('remove', '#', array('class' => 'remove_allergy_entry'));
                }
            ),

        );
        $dataProvider = new \CActiveDataProvider('OEModule\OphCiExamination\models\OphCiExaminationAllergySetEntry');
        $dataProvider->setData($model->allergy_set_entries);

        ?>
        <?php $this->widget('zii.widgets.grid.CGridView', array(
            'dataProvider' => $dataProvider,
            'itemsCssClass' => 'generic-admin standard',
            //'template' => '{items}',
            "emptyTagName" => 'span',
            'summaryText' => false,
            'rowHtmlOptionsExpression' => 'array("data-row"=>$row)',
            'enableSorting' => false,
            'enablePagination' => false,
            'columns' => $columns,
            'rowHtmlOptionsExpression' => 'array("data-row" => $row)',
        )); ?>
    </div>
    <?php echo CHtml::button(
        'Add Allergy',
        [
            'class' => 'button large',
            'type' => 'button',
            'id' => 'add_new_allergy'
        ]
    ); ?>


    <?php echo CHtml::button(
        'Save',
        [
            'class' => 'button large',
            'type' => 'submit',
            'name' => 'save',
            'id' => 'et_save'
        ]
    ); ?>

    <?php echo CHtml::button(
        'Cancel',
        [
            'class' => 'button large',
            'type' => 'button',
            'name' => 'cancel',
            'id' => 'et_cancel'
        ]
    ); ?>
</div>

<script type="text/template" id="new_allergy_entry" class="hidden">
    <tr data-row="{{row}}">
        <td>
            <?php
            echo CHtml::dropDownList("OEModule_OphCiExamination_models_OphCiExaminationAllergySetEntry[{{row}}][ophciexamination_allergy_id]",
                null, $examination_allergy_listdata, array("empty" => '-- select --'));
            ?>
        </td>
        <td>
            <?php
            echo CHtml::dropDownList("OEModule_OphCiExamination_models_OphCiExaminationAllergySetEntry[{{row}}][gender]", null, $gender_options, array('empty' => '-- select --'));
            ?>
        </td>
        <td>
            <input style="width:55px;" type="number"
                   name="OEModule_OphCiExamination_models_OphCiExaminationAllergySetEntry[{{row}}][age_min]"
                   id="OEModule_OphCiExamination_models_OphCiExaminationAllergySetEntry_{{row}}_age_min">
        </td>
        <td>
            <input style="width:55px;" type="number"
                   name="OEModule_OphCiExamination_models_OphCiExaminationAllergySetEntry[{{row}}][age_max]"
                   id="OEModule_OphCiExamination_models_OphCiExaminationAllergySetEntry_{{row}}_age_max">
        </td>
        <td>
            <a href="javascript:void(0)" class="remove_allergy_entry">remove</a>
        </td>
    </tr>
</script>

<script>
    $(document).ready(function () {
        var $table = $('table.generic-admin');

        $('#add_new_allergy').on('click', function (e) {
            var data = {}, $row
            $table = $('table.generic-admin');

            data['row'] = OpenEyes.Util.getNextDataKey($table.find('tbody tr'), 'row');
            $row = Mustache.render(
                $('#new_allergy_entry').text(),
                data
            );
            $table.find('tbody').append($row);
            $table.find('td.empty').closest('tr').hide();
        });

        $($table).on('click', '.remove_allergy_entry', function (e) {
            $(this).closest('tr').remove();
            if ($table.find('tbody tr').length <= 1) {
                $table.find('td.empty').closest('tr').show();
            }
        });

        $(this).on('change', '#subspecialty-id', function (e) {
            var subspecialty_id = $(this).val();

            if (subspecialty_id === '') {
                $('#firm-id option').remove();
                $('#firm-id').append($('<option>').text("All Contexts"));
                $('#firm-id').attr('disabled', 'disabled');
            } else {
                $.ajax({
                    'type': 'GET',
                    'url': baseUrl + '/PatientTicketing/default/getFirmsForSubspecialty?subspecialty_id=' + subspecialty_id,
                    'success': function (html) {
                        $('#firm-id').replaceWith(html);
                        $('#firm-id').addClass('cols-full');
                    }
                });
            }
        });
    });

</script>

<script>
    $(document).ready(function () {
        $('#et_cancel').click(function () {
            window.location.href = '/OphCiExamination/oeadmin/AllergyAssignment/';
        });
    });
</script>