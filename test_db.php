<?php
try {
    new PDO('mysql:host=127.0.0.1;port=3306', 'root', '');
    echo "✓ Kết nối DB thành công!
";
} catch (PDOException $e) {
    echo "✗ DB ERROR: " . $e->getMessage() . "
";
}
