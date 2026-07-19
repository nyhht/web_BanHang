<?php

// 1. Tự động khởi tạo cấu trúc thư mục tạm bắt buộc cho Laravel trên Vercel
$storageStructures = [
    '/tmp/storage/app/public',
    '/tmp/storage/framework/cache/data',
    '/tmp/storage/framework/sessions',
    '/tmp/storage/framework/views',
    '/tmp/storage/bootstrap/cache'
];

foreach ($storageStructures as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
}

// 2. Định nghĩa các biến môi trường để Laravel ghi cache/views vào thư mục /tmp (được cấp quyền ghi)
putenv('APP_STORAGE=/tmp/storage');
putenv('VIEW_COMPILED_PATH=/tmp/storage/framework/views');

// 3. Gọi file bootstrap chính xác bằng đường dẫn tuyệt đối __DIR__
require __DIR__ . '/../public/index.php';