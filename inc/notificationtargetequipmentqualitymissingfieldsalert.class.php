<?php
class PluginAdditionalalertsNotificationTargetEquipmentQualityMissingFieldsAlert extends PluginAdditionalalertsNotificationTarget {
    public function getEvents() {
        return ['equipmentqualitymissingfields' => __('Missing or inconsistent required fields', 'additionalalerts')];
    }
    public function getDatasForTemplate($event, $options = []) {
        $list = PluginAdditionalalertsEquipmentQualityMissingFieldsAlert::getEquipmentsWithMissingFields();
        return [
            'equipments' => $list,
            'count' => count($list)
        ];
    }
    public function getSubject($event, $options = []) {
        return __('Missing or inconsistent required fields', 'additionalalerts');
    }
    public function getBody($event, $options = []) {
        $data = $this->getDatasForTemplate($event, $options);
        if ($data['count'] == 0) {
            return __('No equipment with missing or inconsistent required fields', 'additionalalerts');
        }
        $body = __('Equipments with missing or inconsistent required fields:', 'additionalalerts') . "\n";
        foreach ($data['equipments'] as $eq) {
            $body .= $eq['type'] . ': ' . $eq['name'] . ' (ID: ' . $eq['id'] . ")\n";
        }
        return $body;
    }
}
