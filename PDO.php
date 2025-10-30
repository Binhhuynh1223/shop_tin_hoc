<?php
require 'vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$servername = $_ENV['DB_HOST'];
$dbname = $_ENV['DB_NAME'];
$username = $_ENV['DB_USER'];
$password = $_ENV['DB_PASS'];

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    error_log("Connected successfully");
} catch (PDOException $e) {
    error_log("Connection failed: " . $e->getMessage());
}