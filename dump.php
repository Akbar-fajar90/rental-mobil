<?php
require 'public/index.php';
$db = \Config\Database::connect();
$row = $db->table('t_sewa')->where('id_sewa', 9)->get()->getRowArray();
file_put_contents('db_dump.txt', print_r($row, true));
