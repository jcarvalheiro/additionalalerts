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

if (!defined('GLPI_ROOT')) {
   die("Sorry. You can't access directly to this file");
}

/**
 * Class PluginAdditionalalertsAdditionalalert
 */
class PluginAdditionalalertsAdditionalalert extends CommonDBTM {

   static $rightname = "plugin_additionalalerts";

   /**
    * @param int $nb
    *
    * @return translated
    */
   static function getTypeName($nb = 0) {

      return _n('Other alert', 'Others alerts', $nb, 'additionalalerts');
   }

   static function displayAlerts() {
      global $DB;

      $CronTask = new CronTask();

      $config = PluginAdditionalalertsConfig::getConfig();

      $infocom = new PluginAdditionalalertsInfocomAlert();
      $infocom->getFromDBbyEntity($_SESSION["glpiactive_entity"]);
      if (isset($infocom->fields["use_infocom_alert"])
          && $infocom->fields["use_infocom_alert"] > 0) {
         $use_infocom_alert = $infocom->fields["use_infocom_alert"];
      } else {
         $use_infocom_alert = $config->useInfocomAlert();
      }

      $ticketunresolved = new PluginAdditionalalertsTicketUnresolved();
      $ticketunresolved->getFromDBbyEntity($_SESSION["glpiactive_entity"]);
      if (isset($ticketunresolved->fields["delay_ticket_alert"])
          && $ticketunresolved->fields["delay_ticket_alert"] > 0) {
         $delay_ticket_alert = $ticketunresolved->fields["delay_ticket_alert"];
      } else {
         $delay_ticket_alert = $config->getDelayTicketAlert();
      }

      $inkalert = new PluginAdditionalalertsInkAlert();
      $inkalert->getFromDBbyEntity($_SESSION["glpiactive_entity"]);
      if (isset($inkalert->fields["use_ink_alert"])
          && $inkalert->fields["use_ink_alert"] > 0) {
         $use_ink_alert = $inkalert->fields["use_ink_alert"];
      } else {
         $use_ink_alert = $config->useInkAlert();
      }

      $additionalalerts_ink = 0;
      if ($CronTask->getFromDBbyName("PluginAdditionalalertsInkAlert", "AdditionalalertsInk")) {
         if ($CronTask->fields["state"] != CronTask::STATE_DISABLE && $use_ink_alert > 0) {
            $additionalalerts_ink = 1;
         }
      }

      $additionalalerts_not_infocom = 0;
      if ($CronTask->getFromDBbyName("PluginAdditionalalertsInfocomAlert", "AdditionalalertsNotInfocom")) {
         if ($CronTask->fields["state"] != CronTask::STATE_DISABLE && $use_infocom_alert > 0) {
            $additionalalerts_not_infocom = 1;
         }
      }

      $additionalalerts_ticket_unresolved = 0;
      if ($CronTask->getFromDBbyName("PluginAdditionalalertsTicketUnresolved", "AdditionalalertsTicketUnresolved")) {
         if ($CronTask->fields["state"] != CronTask::STATE_DISABLE && $delay_ticket_alert > 0) {
            $additionalalerts_ticket_unresolved = 1;
         }
      }

      // Affichage des tickets en attente de validation
      $delay_ticket_waiting_validation = $config->getDelayTicketWaitingValidation();
      $additionalalerts_ticket_waiting_validation = 0;
      $CronTask = new CronTask();
      if ($CronTask->getFromDBbyName("PluginAdditionalalertsTicketWaitingValidation", "AdditionalalertsTicketWaitingValidation")) {
         if ($CronTask->fields["state"] != CronTask::STATE_DISABLE && $delay_ticket_waiting_validation > 0) {
            $additionalalerts_ticket_waiting_validation = 1;
         }
      }
      if ($additionalalerts_ticket_waiting_validation != 0) {
         $entities = [$_SESSION["glpiactive_entity"] => $delay_ticket_waiting_validation];
         foreach ($entities as $entity => $delay) {
            $query = PluginAdditionalalertsTicketWaitingValidation::query($delay, $entity);
            $result = $DB->query($query);
            $nbcol = 5;
            if ($DB->numrows($result) > 0) {
               echo "<div align='center'><table class='tab_cadre' cellspacing='2' cellpadding='3'><tr><th colspan='$nbcol'>";
               echo __('Tickets waiting for validation since more', 'additionalalerts') . " " . $delay . " " . _n('Day', 'Days', 2) . ", " . __('Entity') . " : " . Dropdown::getDropdownName("glpi_entities", $entity) . "</th></tr>";
               echo "<tr><th>" . __('Title') . "</th>";
               echo "<th>" . __('Entity') . "</th>";
               echo "<th>" . __('Status') . "</th>";
               echo "<th>" . __('Opening date') . "</th>";
               echo "<th>" . __('Last update') . "</th></tr>";
               while ($data = $DB->fetchArray($result)) {
                  echo PluginAdditionalalertsTicketWaitingValidation::displayBody($data);
               }
               echo "</table></div>";
            } else {
               echo "<br><div align='center'><b>" . __('No tickets waiting for validation since more', 'additionalalerts') . " " . $delay . " " . _n('Day', 'Days', 2) . ", " . __('Entity') . " : " . Dropdown::getDropdownName("glpi_entities", $entity) . "</b></div>";
            }
            echo "<br>";
         }
      }

      // Affichage des tickets en attente de réponse utilisateur
      $delay_ticket_waiting_user = $config->getDelayTicketWaitingUser();
      $additionalalerts_ticket_waiting_user = 0;
      $CronTask = new CronTask();
      if ($CronTask->getFromDBbyName("PluginAdditionalalertsTicketWaitingUser", "AdditionalalertsTicketWaitingUser")) {
         if ($CronTask->fields["state"] != CronTask::STATE_DISABLE && $delay_ticket_waiting_user > 0) {
            $additionalalerts_ticket_waiting_user = 1;
         }
      }
      if ($additionalalerts_ticket_waiting_user != 0) {
         $entities = [$_SESSION["glpiactive_entity"] => $delay_ticket_waiting_user];
         foreach ($entities as $entity => $delay) {
            $query = PluginAdditionalalertsTicketWaitingUser::query($delay, $entity);
            $result = $DB->query($query);
            $nbcol = 5;
            if ($DB->numrows($result) > 0) {
               echo "<div align='center'><table class='tab_cadre' cellspacing='2' cellpadding='3'><tr><th colspan='$nbcol'>";
               echo __('Tickets waiting for user response since more', 'additionalalerts') . " " . $delay . " " . _n('Day', 'Days', 2) . ", " . __('Entity') . " : " . Dropdown::getDropdownName("glpi_entities", $entity) . "</th></tr>";
               echo "<tr><th>" . __('Title') . "</th>";
               echo "<th>" . __('Entity') . "</th>";
               echo "<th>" . __('Status') . "</th>";
               echo "<th>" . __('Opening date') . "</th>";
               echo "<th>" . __('Last update') . "</th></tr>";
               while ($data = $DB->fetchArray($result)) {
                  echo PluginAdditionalalertsTicketWaitingUser::displayBody($data);
               }
               echo "</table></div>";
            } else {
               echo "<br><div align='center'><b>" . __('No tickets waiting for user response since more', 'additionalalerts') . " " . $delay . " " . _n('Day', 'Days', 2) . ", " . __('Entity') . " : " . Dropdown::getDropdownName("glpi_entities", $entity) . "</b></div>";
            }
            echo "<br>";
         }
      }

      // Affichage des techniciens avec trop de tickets ouverts
      $max_open_tickets_tech = $config->getMaxOpenTicketsTech();
      $additionalalerts_ticket_open_tech = 0;
      $CronTask = new CronTask();
      if ($CronTask->getFromDBbyName("PluginAdditionalalertsTicketOpenTech", "AdditionalalertsTicketOpenTech")) {
         if ($CronTask->fields["state"] != CronTask::STATE_DISABLE && $max_open_tickets_tech > 0) {
            $additionalalerts_ticket_open_tech = 1;
         }
      }
      if ($additionalalerts_ticket_open_tech != 0) {
         $entities = [$_SESSION["glpiactive_entity"] => $max_open_tickets_tech];
         foreach ($entities as $entity => $max) {
            $query = PluginAdditionalalertsTicketOpenTech::query($max, $entity);
            $result = $DB->query($query);
            $nbcol = 2;
            if ($DB->numrows($result) > 0) {
               echo "<div align='center'><table class='tab_cadre' cellspacing='2' cellpadding='3'><tr><th colspan='$nbcol'>";
               echo __('Technicians with too many open tickets', 'additionalalerts') . " (" . $max . ") - " . __('Entity') . " : " . Dropdown::getDropdownName("glpi_entities", $entity) . "</th></tr>";
               echo "<tr><th>" . __('Technician') . "</th>";
               echo "<th>" . __('Number of open tickets') . "</th></tr>";
               while ($data = $DB->fetchArray($result)) {
                  echo PluginAdditionalalertsTicketOpenTech::displayBody($data);
               }
               echo "</table></div>";
            } else {
               echo "<br><div align='center'><b>" . __('No technician with too many open tickets', 'additionalalerts') . " (" . $max . ") - " . __('Entity') . " : " . Dropdown::getDropdownName("glpi_entities", $entity) . "</b></div>";
            }
            echo "<br>";
         }
      }

      // Affichage des tickets à priorité élevée non traités
      $delay_ticket_high_priority = $config->getDelayTicketHighPriority();
      $additionalalerts_ticket_high_priority = 0;
      $CronTask = new CronTask();
      if ($CronTask->getFromDBbyName("PluginAdditionalalertsTicketHighPriority", "AdditionalalertsTicketHighPriority")) {
         if ($CronTask->fields["state"] != CronTask::STATE_DISABLE && $delay_ticket_high_priority > 0) {
            $additionalalerts_ticket_high_priority = 1;
         }
      }
      if ($additionalalerts_ticket_high_priority != 0) {
         $entities = [$_SESSION["glpiactive_entity"] => $delay_ticket_high_priority];
         foreach ($entities as $entity => $delay) {
            $query = PluginAdditionalalertsTicketHighPriority::query($delay, $entity);
            $result = $DB->query($query);
            $nbcol = 6;
            if ($DB->numrows($result) > 0) {
               echo "<div align='center'><table class='tab_cadre' cellspacing='2' cellpadding='3'><tr><th colspan='$nbcol'>";
               echo __('High priority tickets not processed since more', 'additionalalerts') . " " . $delay . " " . _n('Day', 'Days', 2) . ", " . __('Entity') . " : " . Dropdown::getDropdownName("glpi_entities", $entity) . "</th></tr>";
               echo "<tr><th>" . __('Title') . "</th>";
               echo "<th>" . __('Entity') . "</th>";
               echo "<th>" . __('Status') . "</th>";
               echo "<th>" . __('Opening date') . "</th>";
               echo "<th>" . __('Last update') . "</th>";
               echo "<th>" . __('Priority') . "</th></tr>";
               while ($data = $DB->fetchArray($result)) {
                  echo PluginAdditionalalertsTicketHighPriority::displayBody($data);
               }
               echo "</table></div>";
            } else {
               echo "<br><div align='center'><b>" . __('No high priority tickets not processed since more', 'additionalalerts') . " " . $delay . " " . _n('Day', 'Days', 2) . ", " . __('Entity') . " : " . Dropdown::getDropdownName("glpi_entities", $entity) . "</b></div>";
            }
            echo "<br>";
         }
      }

      // Affichage des tickets en attente depuis trop longtemps
      $delay_ticket_pending = $config->getDelayTicketPending();
      $additionalalerts_ticket_pending = 0;
      $CronTask = new CronTask();
      if ($CronTask->getFromDBbyName("PluginAdditionalalertsTicketPending", "AdditionalalertsTicketPending")) {
         if ($CronTask->fields["state"] != CronTask::STATE_DISABLE && $delay_ticket_pending > 0) {
            $additionalalerts_ticket_pending = 1;
         }
      }
      if ($additionalalerts_ticket_pending != 0) {
         $entities = [$_SESSION["glpiactive_entity"] => $delay_ticket_pending];
         foreach ($entities as $entity => $delay) {
            $query = PluginAdditionalalertsTicketPending::query($delay, $entity);
            $result = $DB->query($query);
            $nbcol = 5;
            if ($DB->numrows($result) > 0) {
               echo "<div align='center'><table class='tab_cadre' cellspacing='2' cellpadding='3'><tr><th colspan='$nbcol'>";
               echo __('Tickets pending too long since', 'additionalalerts') . " " . $delay . " " . _n('Day', 'Days', 2) . ", " . __('Entity') . " : " . Dropdown::getDropdownName("glpi_entities", $entity) . "</th></tr>";
               echo "<tr><th>" . __('Title') . "</th>";
               echo "<th>" . __('Entity') . "</th>";
               echo "<th>" . __('Status') . "</th>";
               echo "<th>" . __('Opening date') . "</th>";
               echo "<th>" . __('Last update') . "</th></tr>";
               while ($data = $DB->fetchArray($result)) {
                  echo PluginAdditionalalertsTicketPending::displayBody($data);
               }
               echo "</table></div>";
            } else {
               echo "<br><div align='center'><b>" . __('No tickets pending too long since', 'additionalalerts') . " " . $delay . " " . _n('Day', 'Days', 2) . ", " . __('Entity') . " : " . Dropdown::getDropdownName("glpi_entities", $entity) . "</b></div>";
            }
            echo "<br>";
         }
      }

      // Affichage des matériels sans emplacement
      $use_equipment_noloc_alert = $config->useEquipmentNoLocAlert();
      $additionalalerts_equipment_noloc = 0;
      $CronTask = new CronTask();
      if ($CronTask->getFromDBbyName("PluginAdditionalalertsEquipmentNoLoc", "AdditionalalertsEquipmentNoLoc")) {
         if ($CronTask->fields["state"] != CronTask::STATE_DISABLE && $use_equipment_noloc_alert > 0) {
            $additionalalerts_equipment_noloc = 1;
         }
      }
      if ($additionalalerts_equipment_noloc != 0) {
         $entities = [$_SESSION["glpiactive_entity"] => 1];
         foreach ($entities as $entity => $dummy) {
            $query = PluginAdditionalalertsEquipmentNoLoc::query($entity);
            $result = $DB->query($query);
            $nbcol = Session::isMultiEntitiesMode() ? 7 : 6;
            if ($DB->numrows($result) > 0) {
               echo "<div align='center'><table class='tab_cadre' cellspacing='2' cellpadding='3'><tr><th colspan='$nbcol'>";
               echo __('Equipments with no location', 'additionalalerts') . " - " . __('Entity') . " : " . Dropdown::getDropdownName("glpi_entities", $entity) . "</th></tr>";
               echo "<tr><th>" . __('Name') . "</th>";
               if (Session::isMultiEntitiesMode()) {
                  echo "<th>" . __('Entity') . "</th>";
               }
               echo "<th>" . __('Type') . "</th>";
               echo "<th>" . __('Operating system') . "</th>";
               echo "<th>" . __('Status') . "</th>";
               echo "<th>" . __('Location') . "</th>";
               echo "<th>" . __('User') . " / " . __('Group') . " / " . __('Alternate username') . "</th></tr>";
               while ($data = $DB->fetchArray($result)) {
                  echo PluginAdditionalalertsEquipmentNoLoc::displayBody($data);
               }
               echo "</table></div>";
            } else {
               echo "<br><div align='center'><b>" . __('No equipment with no location', 'additionalalerts') . " - " . __('Entity') . " : " . Dropdown::getDropdownName("glpi_entities", $entity) . "</b></div>";
            }
            echo "<br>";
         }
      }

      // Affichage des tickets sans catégorie
      $use_ticket_no_category_alert = $config->useTicketNoCategoryAlert();
      $additionalalerts_ticket_no_category = 0;
      $CronTask = new CronTask();
      if ($CronTask->getFromDBbyName("PluginAdditionalalertsTicketNoCategory", "AdditionalalertsTicketNoCategory")) {
         if ($CronTask->fields["state"] != CronTask::STATE_DISABLE && $use_ticket_no_category_alert > 0) {
            $additionalalerts_ticket_no_category = 1;
         }
      }
      if ($additionalalerts_ticket_no_category != 0) {
         $entities = [$_SESSION["glpiactive_entity"] => 1];
         foreach ($entities as $entity => $dummy) {
            $query = PluginAdditionalalertsTicketNoCategory::query($entity);
            $result = $DB->query($query);
            $nbcol = Session::isMultiEntitiesMode() ? 6 : 5;
            if ($DB->numrows($result) > 0) {
               echo "<div align='center'><table class='tab_cadre' cellspacing='2' cellpadding='3'><tr><th colspan='$nbcol'>";
               echo __('Tickets with no category', 'additionalalerts') . " - " . __('Entity') . " : " . Dropdown::getDropdownName("glpi_entities", $entity) . "</th></tr>";
               echo "<tr><th>" . __('Title') . "</th>";
               if (Session::isMultiEntitiesMode()) {
                  echo "<th>" . __('Entity') . "</th>";
               }
               echo "<th>" . __('Status') . "</th>";
               echo "<th>" . __('Opening date') . "</th>";
               echo "<th>" . __('Last update') . "</th>";
               echo "<th>" . __('Technician') . "</th></tr>";
               while ($data = $DB->fetchArray($result)) {
                  echo PluginAdditionalalertsTicketNoCategory::displayBody($data);
               }
               echo "</table></div>";
            } else {
               echo "<br><div align='center'><b>" . __('No tickets with no category', 'additionalalerts') . " - " . __('Entity') . " : " . Dropdown::getDropdownName("glpi_entities", $entity) . "</b></div>";
            }
            echo "<br>";
         }
      }
      if ($additionalalerts_not_infocom == 0
          && $additionalalerts_ink == 0
          && $additionalalerts_ticket_unresolved == 0) {
         echo "<div align='center'><b>" . __('No used alerts', 'additionalalerts') . "</b></div>";
      }
      if ($additionalalerts_not_infocom != 0) {
         if (Session::haveRight("infocom", READ)) {

            $query  = PluginAdditionalalertsInfocomAlert::query($_SESSION["glpiactive_entity"]);
            $result = $DB->query($query);

            if ($DB->numrows($result) > 0) {

               if (Session::isMultiEntitiesMode()) {
                  $nbcol = 7;
               } else {
                  $nbcol = 6;
               }
               echo "<div align='center'><table class='tab_cadre' cellspacing='2' cellpadding='3'><tr><th colspan='$nbcol'>";
               echo PluginAdditionalalertsInfocomAlert::getTypeName(2) . "</th></tr>";
               echo "<tr><th>" . __('Name') . "</th>";
               if (Session::isMultiEntitiesMode()) {
                  echo "<th>" . __('Entity') . "</th>";
               }
               echo "<th>" . __('Type') . "</th>";
               echo "<th>" . __('Operating system') . "</th>";
               echo "<th>" . __('Status') . "</th>";
               echo "<th>" . __('Location') . "</th>";
               echo "<th>" . __('User') . " / " . __('Group') . " / " . __('Alternate username') . "</th></tr>";
               while ($data = $DB->fetchArray($result)) {

                  echo PluginAdditionalalertsInfocomAlert::displayBody($data);
               }
               echo "</table></div>";
            } else {
               echo "<br><div align='center'><b>" . __('No computers with no buy date', 'additionalalerts') . "</b></div>";
            }
            echo "<br>";
         }
      }

      if ($additionalalerts_ink != 0) {

         if (Plugin::isPluginActive("fusioninventory")
            && $DB->tableExists("glpi_plugin_fusioninventory_printercartridges")) {
            if (Session::haveRight("cartridge", READ)) {
               $query  = PluginAdditionalalertsInkAlert::query($_SESSION["glpiactiveentities_string"]);
               $result = $DB->query($query);

               if ($DB->numrows($result) > 0) {
                  if (Session::isMultiEntitiesMode()) {
                     $nbcol = 4;
                  } else {
                     $nbcol = 3;
                  }
                  echo "<div align='center'><table class='tab_cadre' cellspacing='2' cellpadding='3'>";
                  echo "<tr><th colspan='$nbcol'>" . __('Cartridges whose level is low', 'additionalalerts') . "</th></tr>";
                  echo "<tr>";
                  echo "<th>" . __('Printer') . "</th>";
                  if (Session::isMultiEntitiesMode()) {
                     echo "<th>" . __('Entity') . "</th>";
                  }
                  echo "<th>" . __('Cartridge') . "</th>";
                  echo "<th>" . __('Ink level', 'additionalalerts') . "</th></tr>";

                  while ($data = $DB->fetchArray($result)) {
                     echo PluginAdditionalalertsInkAlert::displayBody($data);
                  }
                  echo "</table></div>";
               } else {
                  echo "<br><div align='center'><b>" . __('No cartridge is below the threshold', 'additionalalerts') . "</b></div>";
               }
            }
         } else {
            echo "<br><div align='center'><b>" . __('Ink level alerts', 'additionalalerts') . " : " . __('Fusioninventory plugin is not installed', 'additionalalerts') . "</b></div>";
         }
      }

      if ($additionalalerts_ticket_unresolved != 0) {
         $entities = PluginAdditionalalertsTicketUnresolved::getEntitiesToNotify('delay_ticket_alert');

         foreach ($entities as $entity => $delay_ticket_alert) {
            $query  = PluginAdditionalalertsTicketUnresolved::query($delay_ticket_alert, $entity);
            $result = $DB->query($query);
            $nbcol  = 7;


            if ($DB->numrows($result) > 0) {

               echo "<div align='center'><table class='tab_cadre' cellspacing='2' cellpadding='3'><tr><th colspan='$nbcol'>";
               echo __('Tickets unresolved since more', 'additionalalerts') . " " . $delay_ticket_alert . " " . _n('Day', 'Days', 2) . ", " . __('Entity') . " : " . Dropdown::getDropdownName("glpi_entities", $entity) . "</th></tr>";
               echo "<tr><th>" . __('Title') . "</th>";
               echo "<th>" . __('Entity') . "</th>";
               echo "<th>" . __('Status') . "</th>";
               echo "<th>" . __('Opening date') . "</th>";
               echo "<th>" . __('Last update') . "</th>";
               echo "<th>" . __('Technician') . "</th>";
               echo "<th>" . __('Manager') . "</th>";

               while ($data = $DB->fetchArray($result)) {
                  echo PluginAdditionalalertsTicketUnresolved::displayBody($data);
               }


               echo "</table></div>";
            } else {
               echo "<br><div align='center'><b>" . __('No tickets unresolved since more', 'additionalalerts') . " " .
                    $delay_ticket_alert . " " . _n('Day', 'Days', 2) . ", " . __('Entity') . " : " . Dropdown::getDropdownName("glpi_entities", $entity) . "</b></div>";
            }

            echo "<br>";
         }
      }

      if (PluginAdditionalalertsConfig::getConfig()->useEquipmentWarrantyAlert()) {
         echo '<h2>' . __('Warranty expired alert', 'additionalalerts') . '</h2>';
         PluginAdditionalalertsEquipmentWarrantyAlert::displayAlerts();
      }
      if (PluginAdditionalalertsConfig::getConfig()->useEquipmentEndOfLifeAlert()) {
         echo '<h2>' . __('End of life alert', 'additionalalerts') . '</h2>';
         PluginAdditionalalertsEquipmentEndOfLifeAlert::displayAlerts();
      }
      if (PluginAdditionalalertsConfig::getConfig()->useEquipmentNotInventoriedAlert()) {
         echo '<h2>' . __('Not inventoried since X days', 'additionalalerts') . '</h2>';
         PluginAdditionalalertsEquipmentNotInventoriedAlert::displayAlerts();
      }
      if (PluginAdditionalalertsConfig::getConfig()->useEquipmentNoAssignmentAlert()) {
         echo '<h2>' . __('No assignment alert', 'additionalalerts') . '</h2>';
         PluginAdditionalalertsEquipmentNoAssignmentAlert::displayAlerts();
      }
      if (PluginAdditionalalertsConfig::getConfig()->useEquipmentMissingInfoAlert()) {
         echo '<h2>' . __('Missing info alert', 'additionalalerts') . '</h2>';
         PluginAdditionalalertsEquipmentMissingInfoAlert::displayAlerts();
      }
      if (PluginAdditionalalertsConfig::getConfig()->useComputerNotUsedAlert()) {
         echo '<h2>' . __('Computer not used since X days', 'additionalalerts') . '</h2>';
         PluginAdditionalalertsComputerNotUsedAlert::displayAlerts();
      }
      if (PluginAdditionalalertsConfig::getConfig()->usePeripheralNotLinkedAlert()) {
         echo '<h2>' . __('Peripheral not linked alert', 'additionalalerts') . '</h2>';
         PluginAdditionalalertsPeripheralNotLinkedAlert::displayAlerts();
      }
      if (PluginAdditionalalertsConfig::getConfig()->useEquipmentBadLocationAlert()) {
         echo '<h2>' . __('Bad location alert', 'additionalalerts') . '</h2>';
         PluginAdditionalalertsEquipmentBadLocationAlert::displayAlerts();
      }
      if (PluginAdditionalalertsConfig::getConfig()->useEquipmentMaintenanceAlert()) {
         echo '<h2>' . __('Maintenance alert', 'additionalalerts') . '</h2>';
         PluginAdditionalalertsEquipmentMaintenanceAlert::displayAlerts();
      }
      if (PluginAdditionalalertsConfig::getConfig()->useEquipmentHighIncidentAlert()) {
         echo '<h2>' . __('High incident alert', 'additionalalerts') . '</h2>';
         PluginAdditionalalertsEquipmentHighIncidentAlert::displayAlerts();
      }
      if (PluginAdditionalalertsConfig::getConfig()->useEquipmentQualityMissingFieldsAlert()) {
            echo '<h2>' . __('Missing or inconsistent required fields', 'additionalalerts') . '</h2>';
            PluginAdditionalalertsEquipmentQualityMissingFieldsAlert::displayAlerts();
        }
        if (PluginAdditionalalertsConfig::getConfig()->useEquipmentQualityDuplicatesAlert()) {
            echo '<h2>' . __('Detected duplicates', 'additionalalerts') . '</h2>';
            PluginAdditionalalertsEquipmentQualityDuplicatesAlert::displayAlerts();
        }
        if (PluginAdditionalalertsConfig::getConfig()->useEquipmentQualityBadAssignmentAlert()) {
            echo '<h2>' . __('Assignment to disabled/nonexistent user or service', 'additionalalerts') . '</h2>';
            PluginAdditionalalertsEquipmentQualityBadAssignmentAlert::displayAlerts();
        }
        if (PluginAdditionalalertsConfig::getConfig()->useEquipmentQualityDateCoherenceAlert()) {
            echo '<h2>' . __('Incoherent dates (buy > warranty/commissioning)', 'additionalalerts') . '</h2>';
            PluginAdditionalalertsEquipmentQualityDateCoherenceAlert::displayAlerts();
        }
        if (PluginAdditionalalertsConfig::getConfig()->useEquipmentQualityObsoleteInfoAlert()) {
            echo '<h2>' . __('Obsolete information (unsupported OS/firmware/version)', 'additionalalerts') . '</h2>';
            PluginAdditionalalertsEquipmentQualityObsoleteInfoAlert::displayAlerts();
        }
        if (PluginAdditionalalertsConfig::getConfig()->useEquipmentQualityNoMoveHistoryAlert()) {
            echo '<h2>' . __('No move or maintenance history', 'additionalalerts') . '</h2>';
            PluginAdditionalalertsEquipmentQualityNoMoveHistoryAlert::displayAlerts();
        }
        if (PluginAdditionalalertsConfig::getConfig()->useEquipmentQualityBadLocationRefAlert()) {
            echo '<h2>' . __('Deleted or unreferenced location', 'additionalalerts') . '</h2>';
            PluginAdditionalalertsEquipmentQualityBadLocationRefAlert::displayAlerts();
        }
        if (PluginAdditionalalertsConfig::getConfig()->useEquipmentQualityIncompleteRelationAlert()) {
            echo '<h2>' . __('Incomplete relations (e.g. computer without monitor)', 'additionalalerts') . '</h2>';
            PluginAdditionalalertsEquipmentQualityIncompleteRelationAlert::displayAlerts();
        }
        if (PluginAdditionalalertsConfig::getConfig()->useEquipmentQualityBadStatusAlert()) {
            echo '<h2>' . __('Inconsistent status (e.g. in stock but assigned)', 'additionalalerts') . '</h2>';
            PluginAdditionalalertsEquipmentQualityBadStatusAlert::displayAlerts();
        }
        if (PluginAdditionalalertsConfig::getConfig()->useEquipmentQualityOldModifAlert()) {
            echo '<h2>' . __('Not modified for over a year', 'additionalalerts') . '</h2>';
            PluginAdditionalalertsEquipmentQualityOldModifAlert::displayAlerts();
        }
    }

   public static function getNotificationTargets() {
        return [
            'equipmentwarrantyexpired' => 'PluginAdditionalalertsNotificationTargetEquipmentWarrantyAlert',
            'equipmentendoflife' => 'PluginAdditionalalertsNotificationTargetEquipmentEndOfLifeAlert',
            'equipmentnotinventoried' => 'PluginAdditionalalertsNotificationTargetEquipmentNotInventoriedAlert',
            'equipmentnoassignment' => 'PluginAdditionalalertsNotificationTargetEquipmentNoAssignmentAlert',
            'equipmentmissinginfo' => 'PluginAdditionalalertsNotificationTargetEquipmentMissingInfoAlert',
            'computernotused' => 'PluginAdditionalalertsNotificationTargetComputerNotUsedAlert',
            'peripheralnotlinked' => 'PluginAdditionalalertsNotificationTargetPeripheralNotLinkedAlert',
            'equipmentbadlocation' => 'PluginAdditionalalertsNotificationTargetEquipmentBadLocationAlert',
            'equipmentmaintenance' => 'PluginAdditionalalertsNotificationTargetEquipmentMaintenanceAlert',
            'equipmenthighincident' => 'PluginAdditionalalertsNotificationTargetEquipmentHighIncidentAlert',
            'equipmentqualitymissingfields' => 'PluginAdditionalalertsNotificationTargetEquipmentQualityMissingFieldsAlert',
            'equipmentqualityduplicates' => 'PluginAdditionalalertsNotificationTargetEquipmentQualityDuplicatesAlert',
            'equipmentqualitybadassignment' => 'PluginAdditionalalertsNotificationTargetEquipmentQualityBadAssignmentAlert',
            'equipmentqualitydatecoherence' => 'PluginAdditionalalertsNotificationTargetEquipmentQualityDateCoherenceAlert',
            'equipmentqualityobsoleteinfo' => 'PluginAdditionalalertsNotificationTargetEquipmentQualityObsoleteInfoAlert',
            'equipmentqualitynomovehistory' => 'PluginAdditionalalertsNotificationTargetEquipmentQualityNoMoveHistoryAlert',
            'equipmentqualitybadlocationref' => 'PluginAdditionalalertsNotificationTargetEquipmentQualityBadLocationRefAlert',
            'equipmentqualityincompleterelation' => 'PluginAdditionalalertsNotificationTargetEquipmentQualityIncompleteRelationAlert',
            'equipmentqualitybadstatus' => 'PluginAdditionalalertsNotificationTargetEquipmentQualityBadStatusAlert',
            'equipmentqualityoldmodif' => 'PluginAdditionalalertsNotificationTargetEquipmentQualityOldModifAlert',
        ];
    }

}
