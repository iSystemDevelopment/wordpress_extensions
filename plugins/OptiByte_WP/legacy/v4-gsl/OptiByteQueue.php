<?php
require_once __DIR__ . '/OptiByteConfig.php';

class OptiByteQueue {
    const FILE = OptiByteConfig::QUEUE_FILE;
    const LOCK = OptiByteConfig::LOCK_FILE;

    public static function load() {
        if (!file_exists(self::FILE)) return [];
        $data = file_get_contents(self::FILE);
        return json_decode($data, true) ?? [];
    }

    public static function save($queue) {
        file_put_contents(self::FILE, json_encode($queue, JSON_PRETTY_PRINT));
    }

    public static function add($job) {
        $queue = self::load();
        $queue[] = $job;
        self::save($queue);
    }

    public static function clear() {
        self::save([]);
    }

    public static function lock() {
        $lock = fopen(self::LOCK, 'c+');
        if (!flock($lock, LOCK_EX | LOCK_NB)) return false;
        return $lock;
    }

    public static function unlock($lock) {
        if ($lock) {
            flock($lock, LOCK_UN);
            fclose($lock);
        }
    }
}
