<?php
include('categoriesManager.php');

try {
    $decodedJson = categoriesManager::getDataFromJson("categories.json");
    categoriesManager::importToDb($decodedJson, null);
} catch (Exception $e) {
    print("Ошибка декодирования JSON " . $e->getMessage());
}