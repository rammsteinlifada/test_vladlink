<?php
$dbname = "testDB";
$servername = "localhost";
$username = null;
$password = null;

$handle = @fopen("MySQL.txt", "r");
if ($handle) {
    $username = fgets($handle, 1024);
    $username = rtrim($username, "\n");
    $password = fgets($handle, 1024);
} else {
    print("Ошибка чтения файла" . "\n");
}
@fclose($handle);

if (!$username || !$password) {
    print("Ошибка чтения с файла");
}

// CREATE DATABASE testDB
//$connection = mysqli_connect($servername, $username, $password);

//$db = "CREATE DATABASE testDB";
//
//if ($connection->query($db) == false) {
//    print("Ошибка создания БД " . $connection->error . "\n");
//} else {
//    print("Успешное создание БД" . "\n");
//}

//CREATE TABLE categories
$connection = mysqli_connect($servername, $username, $password, $dbname);

if ($connection == false) {
    print("Ошибка подключения к серверу " . mysqli_connect_error() . "\n");
} else {
    print("Успешное соединение" . "\n");
}

//$table = "CREATE TABLE categories (
//    Id INT AUTO_INCREMENT PRIMARY KEY,
//    Name CHAR(100) NOT NULL,
//    Alias CHAR(30) NOT NULL,
//    ParentId INT,
//    FOREIGN KEY (ParentId) REFERENCES categories(Id)
//)";
//
//if ($connection->query($table) == false) {
//    print("Ошибка создания БД " . $connection->error . "\n");
//} else {
//    print("Успешное создание БД" . "\n");
//}

$connection->close();