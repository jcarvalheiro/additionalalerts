<?php
// Cible de notification pour l'alerte d'équipement sans affectation
class PluginAdditionalalertsNotificationTargetEquipmentNoAssignmentAlert extends PluginAdditionalalertsNotificationTarget {
    public function getEvents() {
        return ['equipmentnoassignment' => __('No assignment alert', 'additionalalerts')];
    }
    public function getDatasForTemplate($event, $options = []) {
        $list = PluginAdditionalalertsEquipmentNoAssignmentAlert::getNoAssignmentEquipments();
        return [
            'noassignment_equipments' => $list,
            'count' => count($list)
        ];
    }
    public function getSubject($event, $options = []) {
        return __('No assignment alert', 'additionalalerts');
    }
    public function getBody($event, $options = []) {
        $data = $this->getDatasForTemplate($event, $options);
        if ($data['count'] == 0) {
            return __('No equipment without assignment', 'additionalalerts');
        }
        $body = __('Equipments without assignment:', 'additionalalerts') . "\n";
        foreach ($data['noassignment_equipments'] as $eq) {
            $body .= $eq['type'] . ': ' . $eq['name'] . "\n";
        }
        return $body;
    }
}
