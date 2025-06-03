<?php
// Cible de notification pour l'alerte localisation obsolète
class PluginAdditionalalertsNotificationTargetEquipmentBadLocationAlert extends PluginAdditionalalertsNotificationTarget {
    public function getEvents() {
        return ['equipmentbadlocation' => __('Bad location alert', 'additionalalerts')];
    }
    public function getDatasForTemplate($event, $options = []) {
        $list = PluginAdditionalalertsEquipmentBadLocationAlert::getBadLocationEquipments();
        return [
            'badlocation_equipments' => $list,
            'count' => count($list)
        ];
    }
    public function getSubject($event, $options = []) {
        return __('Bad location alert', 'additionalalerts');
    }
    public function getBody($event, $options = []) {
        $data = $this->getDatasForTemplate($event, $options);
        if ($data['count'] == 0) {
            return __('No equipment with bad location', 'additionalalerts');
        }
        $body = __('Equipments with bad location:', 'additionalalerts') . "\n";
        foreach ($data['badlocation_equipments'] as $eq) {
            $body .= $eq['type'] . ': ' . $eq['name'] . "\n";
        }
        return $body;
    }
}
