<?php
require_once __DIR__ . '/OptiByteConfig.php';
require_once __DIR__ . '/OptiByteQueue.php';

class OptiByteScanner {
    public static function scan() {
        $imageDir = OptiByteConfig::INPUT_DIR;

        if (!is_dir($imageDir)) {
            mkdir($imageDir, 0755, true);
        }

        $images = glob($imageDir . '*.{jpg,jpeg,png}', GLOB_BRACE);
        if (!$images) {
            echo "No images found.\n";
            return 0;
        }

        $lock = OptiByteQueue::lock();
        if (!$lock) {
            echo "Queue is locked. Try again later.\n";
            return 0;
        }

        $queue = OptiByteQueue::load();
        $existing = array_column($queue, 'file');
        $newJobs = 0;

        foreach ($images as $imgPath) {
            $basename = basename($imgPath);
            if (in_array($basename, $existing)) continue;

            $queue[] = [
                'file' => $basename,
                'path' => $imgPath,
                'status' => 'pending',
                'created' => date('c')
            ];
            $newJobs++;
        }

        OptiByteQueue::save($queue);
        OptiByteQueue::unlock($lock);

        echo "$newJobs new job(s) added to queue.\n";
        return $newJobs;
    }
}
