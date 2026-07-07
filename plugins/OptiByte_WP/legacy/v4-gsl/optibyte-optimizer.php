<?php
require_once '/var/www/shared/lib/OptiByteConfig.php';
require_once '/var/www/shared/lib/OptiByteQueue.php';
require_once '/var/www/shared/lib/OptiByteLog.php';

$outputDir = OptiByteConfig::OUTPUT_DIR;
if (!is_dir($outputDir)) {
    mkdir($outputDir, 0755, true);
}

$queue = OptiByteQueue::load();
$updated = false;
$successCount = 0;

foreach ($queue as &$job) {
    if ($job['status'] !== 'pending') continue;

    $source = $job['path'];
    $filename = pathinfo($source, PATHINFO_FILENAME);
    $webpOut = $outputDir . $filename . '.webp';
    $avifOut = $outputDir . $filename . '.avif';

    $start = microtime(true);
    $job['status'] = 'processing';
    $job['started'] = date('c');
    $updated = true;

    shell_exec("convert "$source" -quality " . OptiByteConfig::WEBP_QUALITY . " "$webpOut" 2>&1");
    shell_exec("magick "$source" -quality " . OptiByteConfig::AVIF_QUALITY . " "$avifOut" 2>&1");

    $webpOK = file_exists($webpOut);
    $avifOK = file_exists($avifOut);

    if ($webpOK && $avifOK) {
        $end = microtime(true);
        $duration = round(($end - $start) * 1000);
        $job['status'] = 'done';
        $job['completed'] = date('c');
        $job['duration_ms'] = $duration;
        $job['outputs'] = ['webp' => $webpOut, 'avif' => $avifOut];

        OptiByteLog::add([
            'file' => basename($source),
            'status' => 'done',
            'webp_size' => filesize($webpOut),
            'avif_size' => filesize($avifOut),
            'duration_ms' => $duration,
            'started' => $job['started'],
            'completed' => $job['completed']
        ]);

        $successCount++;
    } else {
        $job['status'] = 'error';
        $job['error'] = 'Conversion failed';

        OptiByteLog::add([
            'file' => basename($source),
            'status' => 'error',
            'message' => 'Conversion failed',
            'started' => $job['started'],
            'completed' => date('c')
        ]);
    }
}

if ($updated) {
    OptiByteQueue::save($queue);
}

echo "$successCount images optimized.\n";
