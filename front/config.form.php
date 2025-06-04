<?php
/*
 * @version $Id: HEADER 15930 2011-10-30 15:47:55Z tsmr $
 -------------------------------------------------------------------------
 additionalalerts plugin for GLPI
 Copyright (C) 2009-2022 by the additionalalerts Development Team.

 https://github.com/InfotelGLPI/additionalalerts
 -------------------------------------------------------------------------

 LICENSE

 This file is part of additionalalerts.

 additionalalerts is free software; you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation; either version 2 of the License, or
 (at your option) any later version.

 additionalalerts is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with additionalalerts. If not, see <http://www.gnu.org/licenses/>.
 --------------------------------------------------------------------------
 */

include('../../../inc/includes.php');

if (Plugin::isPluginActive("additionalalerts")) {

   $config = new PluginAdditionalalertsConfig();
   if (isset($_POST["update"])) {
      $config->update($_POST);
      Html::back();
   } else {
      Html::header(PluginAdditionalalertsAdditionalalert::getTypeName(2), '', "admin", "pluginadditionalalertsmenu");
      $config = new PluginAdditionalalertsConfig();
      $config->showConfigForm();
      Html::footer();
   }
} else {
   Html::header(__('Setup'), '', "config", "plugins");
   echo "<div class='alert alert-important alert-warning d-flex'>";
   echo "<b>" . __('Please activate the plugin', 'additionalalerts') . "</b></div>";
   Html::footer();
}
   echo '<tr><th colspan="2">'.__('Equipment alerts', 'additionalalerts').'</th></tr>';
   echo '<tr><td>' . __('Warranty expired alert', 'additionalalerts') . '</td><td>';
   Html::showCheckbox(['name' => 'use_equipment_warranty_alert', 'checked' => $this->fields['use_equipment_warranty_alert']]);
   echo '</td></tr>';
   echo '<tr><td>' . __('End of life alert', 'additionalalerts') . '</td><td>';
   Html::showCheckbox(['name' => 'use_equipment_endoflife_alert', 'checked' => $this->fields['use_equipment_endoflife_alert']]);
   echo '</td></tr>';
   echo '<tr><td>' . __('Not inventoried since X days', 'additionalalerts') . '</td><td>';
   Html::showCheckbox(['name' => 'use_equipment_notinventoried_alert', 'checked' => $this->fields['use_equipment_notinventoried_alert']]);
   echo '</td></tr>';
   echo '<tr><td>' . __('No assignment alert', 'additionalalerts') . '</td><td>';
   Html::showCheckbox(['name' => 'use_equipment_noassignment_alert', 'checked' => $this->fields['use_equipment_noassignment_alert']]);
   echo '</td></tr>';
   echo '<tr><td>' . __('Missing info alert', 'additionalalerts') . '</td><td>';
   Html::showCheckbox(['name' => 'use_equipment_missinginfo_alert', 'checked' => $this->fields['use_equipment_missinginfo_alert']]);
   echo '</td></tr>';
   echo '<tr><td>' . __('Computer not used since X days', 'additionalalerts') . '</td><td>';
   Html::showCheckbox(['name' => 'use_computer_notused_alert', 'checked' => $this->fields['use_computer_notused_alert']]);
   echo '</td></tr>';
   echo '<tr><td>' . __('Peripheral not linked alert', 'additionalalerts') . '</td><td>';
   Html::showCheckbox(['name' => 'use_peripheral_notlinked_alert', 'checked' => $this->fields['use_peripheral_notlinked_alert']]);
   echo '</td></tr>';
   echo '<tr><td>' . __('Bad location alert', 'additionalalerts') . '</td><td>';
   Html::showCheckbox(['name' => 'use_equipment_badlocation_alert', 'checked' => $this->fields['use_equipment_badlocation_alert']]);
   echo '</td></tr>';
   echo '<tr><td>' . __('Maintenance alert', 'additionalalerts') . '</td><td>';
   Html::showCheckbox(['name' => 'use_equipment_maintenance_alert', 'checked' => $this->fields['use_equipment_maintenance_alert']]);
   echo '</td></tr>';
   echo '<tr><td>' . __('High incident alert', 'additionalalerts') . '</td><td>';
   Html::showCheckbox(['name' => 'use_equipment_highincident_alert', 'checked' => $this->fields['use_equipment_highincident_alert']]);
   echo '</td></tr>';
   echo '<tr><th colspan="2">'.__('Quality control alerts', 'additionalalerts').'</th></tr>';
   echo '<tr><td>' . __('Missing or inconsistent required fields', 'additionalalerts') . '</td><td>';
   Html::showCheckbox(['name' => 'use_equipment_quality_missingfields_alert', 'checked' => $this->fields['use_equipment_quality_missingfields_alert']]);
   echo '</td></tr>';
   echo '<tr><td>' . __('Detected duplicates', 'additionalalerts') . '</td><td>';
   Html::showCheckbox(['name' => 'use_equipment_quality_duplicates_alert', 'checked' => $this->fields['use_equipment_quality_duplicates_alert']]);
   echo '</td></tr>';
   echo '<tr><td>' . __('Assignment to disabled/nonexistent user or service', 'additionalalerts') . '</td><td>';
   Html::showCheckbox(['name' => 'use_equipment_quality_badassignment_alert', 'checked' => $this->fields['use_equipment_quality_badassignment_alert']]);
   echo '</td></tr>';
   echo '<tr><td>' . __('Incoherent dates (buy > warranty/commissioning)', 'additionalalerts') . '</td><td>';
   Html::showCheckbox(['name' => 'use_equipment_quality_datecoherence_alert', 'checked' => $this->fields['use_equipment_quality_datecoherence_alert']]);
   echo '</td></tr>';
   echo '<tr><td>' . __('Obsolete information (unsupported OS/firmware/version)', 'additionalalerts') . '</td><td>';
   Html::showCheckbox(['name' => 'use_equipment_quality_obsoleteinfo_alert', 'checked' => $this->fields['use_equipment_quality_obsoleteinfo_alert']]);
   echo '</td></tr>';
   echo '<tr><td>' . __('No move or maintenance history', 'additionalalerts') . '</td><td>';
   Html::showCheckbox(['name' => 'use_equipment_quality_nomovehistory_alert', 'checked' => $this->fields['use_equipment_quality_nomovehistory_alert']]);
   echo '</td></tr>';
   echo '<tr><td>' . __('Deleted or unreferenced location', 'additionalalerts') . '</td><td>';
   Html::showCheckbox(['name' => 'use_equipment_quality_badlocationref_alert', 'checked' => $this->fields['use_equipment_quality_badlocationref_alert']]);
   echo '</td></tr>';
   echo '<tr><td>' . __('Incomplete relations (e.g. computer without monitor)', 'additionalalerts') . '</td><td>';
   Html::showCheckbox(['name' => 'use_equipment_quality_incompleterelation_alert', 'checked' => $this->fields['use_equipment_quality_incompleterelation_alert']]);
   echo '</td></tr>';
   echo '<tr><td>' . __('Inconsistent status (e.g. in stock but assigned)', 'additionalalerts') . '</td><td>';
   Html::showCheckbox(['name' => 'use_equipment_quality_badstatus_alert', 'checked' => $this->fields['use_equipment_quality_badstatus_alert']]);
   echo '</td></tr>';
   echo '<tr><td>' . __('Not modified for over a year', 'additionalalerts') . '</td><td>';
   Html::showCheckbox(['name' => 'use_equipment_quality_oldmodif_alert', 'checked' => $this->fields['use_equipment_quality_oldmodif_alert']]);
   echo '</td></tr>';
