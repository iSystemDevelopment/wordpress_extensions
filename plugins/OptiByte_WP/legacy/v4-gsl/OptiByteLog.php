<?php
require_once __DIR__ . '/OptiByteConfig.php';

class OptiByteLog {
    const FILE = OptiByteConfig::LOG_FILE;

    public static function load() {
        if (!file_exists(self::FILE)) return [];
        $data = file_get_contents(self::FILE);
        return json_decode($data, true) ?? [];
    }

    public static function add($entry) {
        $log = self::load();
        $log[] = $entry;
        file_put_contents(self::FILE, json_encode($log, JSON_PRETTY_PRINT));
    }

    public static function clear() {
        file_put_contents(self::FILE, json_encode([], JSON_PRETTY_PRINT));
    }
}
