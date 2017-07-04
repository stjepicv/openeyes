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

class PatientLevelElementBehaviour extends CActiveRecordBehavior
{
    /**
     * @param \Patient|null $patient
     * @return \BaseEventTypeElement|null
     */
    public function getTipElement(\Patient $patient = null)
    {
        if (!$patient) {
            $patient = $this->owner->event->getPatient();
        }
        return $this->owner->getModuleApi()->getLatestElement(get_class($this->owner), $patient);
    }

    /**
     * Base check method. Note this method should not be called directly - use isAtTip. This allows
     * isAtTip to be overridden in class utilising this behavior.
     *
     * @return bool
     */
    public function tipCheck()
    {
        if ($this->owner->getIsNewRecord()) {
            if (!$this->owner->event || $this->owner->event->getIsNewRecord()) {
                return true;
            }
            else {
                $tip = $this->getTipElement();
                return $this->owner->event->isAfterEvent($tip->event);
            }
        }
        $tip = $this->owner->getTipElement();
        return $tip && $tip->id === $this->owner->id;
    }

    /**
     * @return bool
     */
    public function isAtTip()
    {
        return $this->tipCheck();
    }
}