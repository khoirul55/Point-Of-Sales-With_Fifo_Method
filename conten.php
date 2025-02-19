<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$root = realpath($_SERVER["DOCUMENT_ROOT"]);
$base_path = $root . '/POSFIFO_ADEKELAPA/';

if (!empty($_GET["page"])) {
    $page = $_GET["page"];
    $file_path = __DIR__ . "/{$page}.php";
    if (file_exists($file_path)) {
        include_once($file_path);
    } else {
        echo "File not found: {$file_path}";
    }
} else {
    include "home.php";
}
?>

