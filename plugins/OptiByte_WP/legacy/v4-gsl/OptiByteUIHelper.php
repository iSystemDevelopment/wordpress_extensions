<?php

class OptiByteUIHelper {
    public static function statusBadge($status) {
        $colors = ['pending' => 'orange', 'processing' => 'blue', 'done' => 'green', 'error' => 'red'];
        return '<span style="color:' . ($colors[$status] ?? 'black') . '; font-weight:bold;">' . strtoupper($status) . '</span>';
    }

    public static function formatSize($bytes) {
        return round($bytes / 1024, 1) . ' KB';
    }

    public static function formatTime($iso) {
        return $iso ? date("Y-m-d H:i", strtotime($iso)) : '-';
    }
}
