<?php
header('Content-Type: application/json');
echo json_encode(['test' => 'works', 'time' => date('Y-m-d H:i:s')]);
?>
