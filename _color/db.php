<?php
$opt = array(
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
);
    $pdo = new PDO("mysql:dbname=hierarchy;host=localhost", "root", "",$opt);

$sql = "SELECT * FROM `cities`";
$rs = $pdo->query($sql);
$rows = $rs->fetchall();
var_dump($rows);