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

$json = null;
$handle = @fopen("categories.json", "r");

if ($handle !== false) {
    $json = fgets($handle);
} else {
    print("Ошибка чтения файла" . "\n");
    return;
}

@fclose($handle);

if ($json === false) {
    print("Ошибка чтения с файла" . "\n");
}
$decodedJson = json_decode($json, true);

importToDb($decodedJson, null);