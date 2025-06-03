<?php
// Cible de notification pour l'alerte périphérique non rattaché
class PluginAdditionalalertsNotificationTargetPeripheralNotLinkedAlert extends PluginAdditionalalertsNotificationTarget {
    public function getEvents() {
        return ['peripheralnotlinked' => __('Peripheral not linked alert', 'additionalalerts')];
    }
    public function getDatasForTemplate($event, $options = []) {
        $list = PluginAdditionalalertsPeripheralNotLinkedAlert::getNotLinkedPeripherals();
        return [
            'notlinked_peripherals' => $list,
            'count' => count($list)
        ];
    }
    public function getSubject($event, $options = []) {
        return __('Peripheral not linked alert', 'additionalalerts');
    }
    public function getBody($event, $options = []) {
        $data = $this->getDatasForTemplate($event, $options);
        if ($data['count'] == 0) {
            return __('No peripheral not linked', 'additionalalerts');
        }
        $body = __('Peripherals not linked:', 'additionalalerts') . "\n";
        foreach ($data['notlinked_peripherals'] as $eq) {
            $body .= $eq['name'] . "\n";
        }
        return $body;
    }
}
