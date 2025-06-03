<?php
// Cible de notification pour l'alerte ordinateur non utilisé
class PluginAdditionalalertsNotificationTargetComputerNotUsedAlert extends PluginAdditionalalertsNotificationTarget {
    public function getEvents() {
        return ['computernotused' => __('Computer not used since X days', 'additionalalerts')];
    }
    public function getDatasForTemplate($event, $options = []) {
        $list = PluginAdditionalalertsComputerNotUsedAlert::getNotUsedComputers();
        return [
            'notused_computers' => $list,
            'count' => count($list)
        ];
    }
    public function getSubject($event, $options = []) {
        return __('Computer not used since X days', 'additionalalerts');
    }
    public function getBody($event, $options = []) {
        $data = $this->getDatasForTemplate($event, $options);
        if ($data['count'] == 0) {
            return __('No computer not used since X days', 'additionalalerts');
        }
        $body = __('Computers not used since X days:', 'additionalalerts') . "\n";
        foreach ($data['notused_computers'] as $eq) {
            $body .= $eq['name'] . ' (' . $eq['last_login'] . ")\n";
        }
        return $body;
    }
}
