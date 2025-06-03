<?php
// Cible de notification pour l'alerte d'informations manquantes
class PluginAdditionalalertsNotificationTargetEquipmentMissingInfoAlert extends PluginAdditionalalertsNotificationTarget {
    public function getEvents() {
        return ['equipmentmissinginfo' => __('Missing info alert', 'additionalalerts')];
    }
    public function getDatasForTemplate($event, $options = []) {
        $list = PluginAdditionalalertsEquipmentMissingInfoAlert::getMissingInfoEquipments();
        return [
            'missinginfo_equipments' => $list,
            'count' => count($list)
        ];
    }
    public function getSubject($event, $options = []) {
        return __('Missing info alert', 'additionalalerts');
    }
    public function getBody($event, $options = []) {
        $data = $this->getDatasForTemplate($event, $options);
        if ($data['count'] == 0) {
            return __('No equipment with missing info', 'additionalalerts');
        }
        $body = __('Equipments with missing info:', 'additionalalerts') . "\n";
        foreach ($data['missinginfo_equipments'] as $eq) {
            $body .= $eq['type'] . ': ' . $eq['name'] . "\n";
        }
        return $body;
    }
}
