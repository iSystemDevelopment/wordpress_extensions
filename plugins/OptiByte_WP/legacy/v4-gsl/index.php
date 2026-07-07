<?php
require_once '/var/www/shared/lib/OptiByteConfig.php';
require_once '/var/www/shared/lib/OptiByteQueue.php';
require_once '/var/www/shared/lib/OptiByteLog.php';
require_once '/var/www/shared/lib/OptiByteUIHelper.php';

$token = $_GET['token'] ?? $_POST['token'] ?? '';
if ($token !== OptiByteConfig::TOKEN) {
    http_response_code(403);
    echo "<h3 style='color:red;'>403 – Access Denied</h3>";
    exit;
}

$queue = OptiByteQueue::load();
$logs = OptiByteLog::load();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['scan'])) {
        shell_exec("php /var/www/api.[retired-host]/public/cron/optibyte-scanner.php");
    }
    if (isset($_POST['optimize'])) {
        shell_exec("php /var/www/api.[retired-host]/public/cron/optibyte-optimizer.php");
    }
    if (isset($_POST['clear_queue'])) {
        OptiByteQueue::clear();
    }
    if (isset($_POST['clear_log'])) {
        OptiByteLog::clear();
    }
    header("Location: ?token=" . urlencode($token));
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>OptiByte PRO Dashboard</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        h1 { margin-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 40px; }
        th, td { padding: 8px 10px; border: 1px solid #ccc; text-align: left; }
        th { background: #f8f8f8; }
        button { padding: 6px 12px; margin-right: 10px; }
    </style>
</head>
<body>
    <h1>📊 OptiByte PRO v4 Dashboard</h1>

    <div>
        <form method="post" action="?token=<?= urlencode($token) ?>">
            <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
            <button name="scan">🔁 Scan Queue</button>
            <button name="optimize">⚙️ Run Optimizer</button>
            <button name="clear_queue">🗑️ Clear Queue</button>
            <button name="clear_log">🧹 Clear Logs</button>
        </form>
    </div>

    <h2>🧺 Job Queue (<?= count($queue) ?>)</h2>
    <table>
        <tr><th>File</th><th>Status</th><th>Started</th><th>Completed</th></tr>
        <?php foreach ($queue as $job): ?>
        <tr>
            <td><?= htmlspecialchars($job['file']) ?></td>
            <td><?= OptiByteUIHelper::statusBadge($job['status']) ?></td>
            <td><?= OptiByteUIHelper::formatTime($job['started'] ?? null) ?></td>
            <td><?= OptiByteUIHelper::formatTime($job['completed'] ?? null) ?></td>
        </tr>
        <?php endforeach; ?>
    </table>

    <h2>📜 Optimization Log (<?= count($logs) ?>)</h2>
    <table>
        <tr><th>File</th><th>Status</th><th>WebP</th><th>AVIF</th><th>Duration</th></tr>
        <?php foreach (array_reverse($logs) as $entry): ?>
        <tr>
            <td><?= htmlspecialchars($entry['file']) ?></td>
            <td><?= OptiByteUIHelper::statusBadge($entry['status']) ?></td>
            <td><?= OptiByteUIHelper::formatSize($entry['webp_size'] ?? 0) ?></td>
            <td><?= OptiByteUIHelper::formatSize($entry['avif_size'] ?? 0) ?></td>
            <td><?= ($entry['duration_ms'] ?? 0) . ' ms' ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
