<?php

include('database.php');

function exportFromDb(?string $parentId, ?string $route, int $nestingLevel) {
    $data = getDbData($parentId);
    while ($row = $data->fetch_row()) {
        $nextRoute = "/" . $row[2];
        $placeholder = str_repeat("\t", $nestingLevel);
        $placeholder .= $row[1] . " " . $route . $nextRoute . "\n";
        fwrite($GLOBALS["handle"], $placeholder);
        exportFromDb($row[0], $nextRoute, $nestingLevel + 1);
    }
}

$handle = @fopen("type_a.txt", "w+");

exportFromDb(null, null, 0);

@fclose($handle);