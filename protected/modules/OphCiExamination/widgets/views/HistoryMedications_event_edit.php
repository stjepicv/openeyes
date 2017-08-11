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
?>

<?php

$model_name = CHtml::modelName($element);
?>

<script type="text/javascript" src="<?= $this->getJsPublishedPath('HistoryMedications.js') ?>"></script>
<div class="element-fields" id="<?= $model_name ?>_element">

    <input type="hidden" name="<?= $model_name ?>[present]" value="1" />

    <table class="<?= $model_name ?>_entry_table">
        <thead>
        <tr>
            <th>Start Date</th>
            <th>End Date</th>
            <th>Medication</th>
            <th>Adminstration</th>
            <th>Action(s)</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $row_count = 0;
        foreach ($element->entries as $entry) {
            $this->render(
                'HistoryRisksEntry_event_edit',
                array(
                    'entry' => $entry,
                    'form' => $form,
                    'model_name' => $model_name,
                    'field_prefix' => $model_name . '[entries][' . $row_count . ']',
                    'row_count' => $row_count,
                    'removable' => !in_array($entry->risk_id, $required_risk_ids),
                )
            );
            $row_count++;
        }
        ?>
        </tbody>
        <tfoot>
        <tr>
            <td colspan="4"></td>
            <td><button class="button small primary add-entry">Add</button></td>
        </tr>
        </tfoot>
    </table>
    <script type="text/template" class="entry-template hidden">
        <?php
        $empty_entry = new \OEModule\OphCiExamination\models\HistoryMedicationsEntry();
        $this->render(
            'HistoryMedicationsEntry_event_edit',
            array(
                'entry' => $empty_entry,
                'form' => $form,
                'model_name' => $model_name,
                'field_prefix' => $model_name . '[entries][{{row_count}}]',
                'row_count' => '{{row_count}}',
                'removable' => true
            )
        );
        ?>
    </script>
</div>


<script type="text/javascript">
  $(document).ready(function() {
    new OpenEyes.OphCiExamination.HistoryMedicationsController({
      element: $('#<?=$model_name?>_element')
    });
  });
</script>
