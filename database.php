<?php
$dbname = "testDB";
$servername = "localhost";
$username = null;
$password = null;
$connection = false;

function getUsernameAndPasswordFromFile(?string &$username, ?string &$password) {
    $handle = @fopen("MySQL.txt", "r");
    if ($handle !== false) {
        $username = rtrim(fgets($handle), "\n");
        $password = fgets($handle);
    } else {
        print("Ошибка чтения файла" . "\n");
    }
    fclose($handle);
    if ($username === false || $password === false) {
        print("Ошибка чтения с файла");
    }
}

function createTable() {
    $table = "CREATE TABLE categories (
        Id INT PRIMARY KEY NOT NULL,
        Name CHAR(100) NOT NULL,
        Alias CHAR(30) NOT NULL,
        ParentId INT,
        FOREIGN KEY (ParentId) REFERENCES categories(Id)
    )";

    if ($GLOBALS["connection"]->query($table) === false) {
        print("Ошибка создания БД " . $GLOBALS["connection"]->error . "\n");
    } else {
        print("Успешное создание БД" . "\n");
    }
}

function insert(array $categoryArray, ?string $categoryParent) {
    $connection = new mysqli($GLOBALS["servername"], $GLOBALS["username"],
                            $GLOBALS["password"], $GLOBALS["dbname"]);
    $categoryId = $categoryArray["id"];
    $categoryName = $categoryArray["name"];
    $categoryAlias = $categoryArray["alias"];
    if ($categoryParent) {
        $sql = "SELECT * FROM categories WHERE Alias = ?";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param('s', $categoryParent);
        $stmt->execute();
        $result = $stmt->get_result();
        $categoryParentId = $result->fetch_row()[0];
    } else {
        $categoryParentId = null;
    }

    $sql = "INSERT INTO categories (Id, Name, Alias, ParentId) VALUES (?, ?, ?, ?)";
    $stmt = $connection->prepare($sql);
    $stmt->bind_param('issi', $categoryId, $categoryName, $categoryAlias, $categoryParentId);
    $stmt->execute();

    $connection->close();
}

function getDbData(?int $ParentId) {
    $connection = new mysqli($GLOBALS["servername"], $GLOBALS["username"],
                            $GLOBALS["password"], $GLOBALS["dbname"]);
    if ($ParentId === null) {
        $sql = "SELECT * FROM categories WHERE ParentId IS NULL";
        $stmt = $connection->prepare($sql);
    } else {
        $sql = "SELECT * FROM categories WHERE ParentId = ?";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param('i', $ParentId);
    }
    $stmt->execute();
    return $stmt->get_result();
}

getUsernameAndPasswordFromFile($username, $password);

//$connection = new mysqli($servername, $username, $password, $dbname);
//print($connection->host_info . "\n");
//
////createTable();
//
//$connection->close();
//print("close connection" . "\n");