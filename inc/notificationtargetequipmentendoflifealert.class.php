<?php
// Cible de notification pour l'alerte de fin de vie
class PluginAdditionalalertsNotificationTargetEquipmentEndOfLifeAlert extends PluginAdditionalalertsNotificationTarget {
    public function getEvents() {
        return ['equipmentendoflife' => __('End of life alert', 'additionalalerts')];
    }
    public function getDatasForTemplate($event, $options = []) {
        $expired = PluginAdditionalalertsEquipmentEndOfLifeAlert::getExpiredEndOfLifeEquipments();
        return [
            'expired_equipments' => $expired,
            'count' => count($expired)
        ];
    }
    public function getSubject($event, $options = []) {
        return __('End of life alert', 'additionalalerts');
    }
    public function getBody($event, $options = []) {
        $data = $this->getDatasForTemplate($event, $options);
        if ($data['count'] == 0) {
            return __('No equipment with end of life reached', 'additionalalerts');
        }
        $body = __('Equipments with end of life reached:', 'additionalalerts') . "\n";
        foreach ($data['expired_equipments'] as $eq) {
            $body .= $eq['type'] . ': ' . $eq['name'] . ' (' . $eq['endoflife_date'] . ")\n";
        }
        return $body;
    }
}
