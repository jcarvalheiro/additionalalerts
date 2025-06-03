<?php
// Cible de notification pour l'alerte maintenance
class PluginAdditionalalertsNotificationTargetEquipmentMaintenanceAlert extends PluginAdditionalalertsNotificationTarget {
    public function getEvents() {
        return ['equipmentmaintenance' => __('Maintenance alert', 'additionalalerts')];
    }
    public function getDatasForTemplate($event, $options = []) {
        $list = PluginAdditionalalertsEquipmentMaintenanceAlert::getMaintenanceEquipments();
        return [
            'maintenance_equipments' => $list,
            'count' => count($list)
        ];
    }
    public function getSubject($event, $options = []) {
        return __('Maintenance alert', 'additionalalerts');
    }
    public function getBody($event, $options = []) {
        $data = $this->getDatasForTemplate($event, $options);
        if ($data['count'] == 0) {
            return __('No equipment with maintenance issue', 'additionalalerts');
        }
        $body = __('Equipments with maintenance issue:', 'additionalalerts') . "\n";
        foreach ($data['maintenance_equipments'] as $eq) {
            $body .= $eq['type'] . ': ' . $eq['name'] . "\n";
        }
        return $body;
    }
}
