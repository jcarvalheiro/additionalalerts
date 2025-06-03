<?php
// Cible de notification pour l'alerte nombre élevé de pannes
class PluginAdditionalalertsNotificationTargetEquipmentHighIncidentAlert extends PluginAdditionalalertsNotificationTarget {
    public function getEvents() {
        return ['equipmenthighincident' => __('High incident alert', 'additionalalerts')];
    }
    public function getDatasForTemplate($event, $options = []) {
        $list = PluginAdditionalalertsEquipmentHighIncidentAlert::getHighIncidentEquipments();
        return [
            'highincident_equipments' => $list,
            'count' => count($list)
        ];
    }
    public function getSubject($event, $options = []) {
        return __('High incident alert', 'additionalalerts');
    }
    public function getBody($event, $options = []) {
        $data = $this->getDatasForTemplate($event, $options);
        if ($data['count'] == 0) {
            return __('No equipment with high incident count', 'additionalalerts');
        }
        $body = __('Equipments with high incident count:', 'additionalalerts') . "\n";
        foreach ($data['highincident_equipments'] as $eq) {
            $body .= $eq['type'] . ': ' . $eq['name'] . ' (' . $eq['incident_count'] . ")\n";
        }
        return $body;
    }
}
