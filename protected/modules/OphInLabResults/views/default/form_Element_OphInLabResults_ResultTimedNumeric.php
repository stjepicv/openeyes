<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>

<section id="result-output" class="element-fields">
  <div class="element-fields">
    <div class="active-form">
      <table class="cols-11">
        <tbody>
          <tr>
                <?= $form->hiddenInput($element, 'type');?>
                <?php echo $form->textField($element, 'time', $element->getHtmlOptionsForInput('time'), array());?>
          </tr>
          <tr>
                <?php echo $form->textField($element, 'result', $element->getHtmlOptionsForInput('result'), array());?>
          </tr>
          <tr>
                <?php echo $form->textArea($element, 'comment', $element->getHtmlOptionsForInput('comment'), array(), ['maxlength'=>'250']);?>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</section>
