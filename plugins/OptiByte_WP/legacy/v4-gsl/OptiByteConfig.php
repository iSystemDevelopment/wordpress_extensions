<?php
class OptiByteConfig {
    const TOKEN = 'your_secure_token_here'; // replace with your real token
    const QUEUE_FILE = '/var/www/shared/optibyte.queue.json';
    const LOG_FILE   = '/var/www/shared/optibyte-log.json';
    const LOCK_FILE  = '/var/www/shared/optibyte.lock';
    const INPUT_DIR  = '/var/www/shared/uploads/optibyte-staging/';
    const OUTPUT_DIR = '/var/www/shared/optimized/';
    const WEBP_QUALITY = 85;
    const AVIF_QUALITY = 60;
}
