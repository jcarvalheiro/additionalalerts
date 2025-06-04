<?php
class PluginAdditionalalertsNotificationTargetEquipmentQualityDuplicatesAlert extends PluginAdditionalalertsNotificationTarget {
    public function getEvents() {
        return ['equipmentqualityduplicates' => __('Detected duplicates', 'additionalalerts')];
    }
    public function getDatasForTemplate($event, $options = []) {
        $list = PluginAdditionalalertsEquipmentQualityDuplicatesAlert::getEquipmentsWithDuplicates();
        return [
            'equipments' => $list,
            'count' => count($list)
        ];
    }
    public function getSubject($event, $options = []) {
        return __('Detected duplicates', 'additionalalerts');
    }
    public function getBody($event, $options = []) {
        $data = $this->getDatasForTemplate($event, $options);
        if ($data['count'] == 0) {
            return __('No equipment with detected duplicates', 'additionalalerts');
        }
        $body = __('Equipments with detected duplicates:', 'additionalalerts') . "\n";
        foreach ($data['equipments'] as $eq) {
            $body .= $eq['type'] . ': ' . $eq['name'] . ' (ID: ' . $eq['id'] . ")\n";
        }
        return $body;
    }
}
