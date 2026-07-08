<?php
$mysqli = new mysqli('127.0.0.1', 'root', '', 'mealkit');
if ($mysqli->connect_error) {
    echo 'Connection ERROR: ' . $mysqli->connect_error;
    exit;
}

echo "=== MySQL Processlist ===\n";
$result = $mysqli->query('SHOW PROCESSLIST');
if ($result) {
    while ($row = $result->fetch_assoc()) {
        echo $row['ID'].' | '.$row['User'].' | '.$row['Command'].' | '.$row['Time'].'s | '.substr($row['Info'] ?? '', 0, 50)."\n";
    }
}

echo "\n=== MySQL Status ===\n";
$status = $mysqli->query('SHOW STATUS');
if ($status) {
    $rows = [];
    while ($row = $status->fetch_assoc()) {
        if (in_array($row['Variable_name'], ['Threads_connected', 'Threads_running', 'Questions', 'Slow_queries'])) {
            echo $row['Variable_name'].' = '.$row['Value']."\n";
        }
    }
}

echo "\n=== Database Size ===\n";
$db_size = $mysqli->query("SELECT table_name, ROUND(((data_length + index_length) / 1024 / 1024), 2) size_mb FROM information_schema.TABLES WHERE table_schema = 'mealkit' ORDER BY size_mb DESC LIMIT 5");
if ($db_size) {
    while ($row = $db_size->fetch_assoc()) {
        echo $row['table_name'].' = '.$row['size_mb'].' MB'."\n";
    }
}

$mysqli->close();
