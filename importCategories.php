<?php

include('database.php');

function importToDb(array $categories, ?string $parent) {
    foreach ($categories as $item) {
        insert($item, $parent);
        if (array_key_exists("childrens", $item)) {
            $childrens = $item["childrens"];
            importToDb($childrens, $item["alias"]);
        }
    }
}

function getDataFromJson(string $file) {
    $handle = @fopen($file, "r");

    if ($handle !== false) {
        $json = fgets($handle);
    } else {
        print("Ошибка чтения файла" . "\n");
        return null;
    }

    fclose($handle);

    if ($json === false) {
        print("Ошибка чтения с файла" . "\n");
    }
    return json_decode($json, true);
}

$decodedJson = getDataFromJson("categories.json");
importToDb($decodedJson, null);