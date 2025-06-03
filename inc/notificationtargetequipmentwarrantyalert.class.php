<?php
// Cible de notification pour l'alerte de garantie expirée
class PluginAdditionalalertsNotificationTargetEquipmentWarrantyAlert extends PluginAdditionalalertsNotificationTarget {
    public function getEvents() {
        return ['equipmentwarrantyexpired' => __('Warranty expired alert', 'additionalalerts')];
    }

    public function getDatasForTemplate($event, $options = []) {
        $expired = PluginAdditionalalertsEquipmentWarrantyAlert::getExpiredWarrantyEquipments();
        return [
            'expired_equipments' => $expired,
            'count' => count($expired)
        ];
    }

    public function getSubject($event, $options = []) {
        return __('Warranty expired alert', 'additionalalerts');
    }

    public function getBody($event, $options = []) {
        $data = $this->getDatasForTemplate($event, $options);
        if ($data['count'] == 0) {
            return __('No equipment with expired warranty', 'additionalalerts');
        }
        $body = __('Equipments with expired warranty:', 'additionalalerts') . "\n";
        foreach ($data['expired_equipments'] as $eq) {
            $body .= $eq['type'] . ': ' . $eq['name'] . ' (' . $eq['warranty_date'] . ")\n";
        }
        return $body;
    }
}
