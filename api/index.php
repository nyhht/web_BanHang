<?php

// 1. Ép Laravel lưu file cấu hình và giao diện tạm vào thư mục /tmp của Vercel
$storagePath = '/tmp/storage/framework';
foreach (['/meta', '/sessions', '/views', '/cache/data'] as $dir) {
    if (!is_dir($storagePath . $dir)) {
        mkdir($storagePath . $dir, 0755, true);
    }
}

putenv("VIEW_COMPILED_PATH=/tmp/storage/framework/views");
putenv("APP_CONFIG_CACHE=/tmp/storage/framework/cache/config.php");

// 2. Gọi lõi Laravel chạy bình thường
require __DIR__ . '/../public/index.php';