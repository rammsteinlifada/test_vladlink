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

function exportFromDbUntilNestingLevel(?string $parentId,
                                       int $nestingLevel,
                                       int $lastNestingLevel) {
    if ($lastNestingLevel < 0) {
        return;
    }
    if ($nestingLevel > $lastNestingLevel) {
        return;
    }
    $data = getDbData($parentId);
    while ($row = $data->fetch_row()) {
        $placeholder = str_repeat("\t", $nestingLevel);
        $placeholder .= $row[1] . "\n";
        fwrite($GLOBALS["handle"], $placeholder);
        exportFromDbUntilNestingLevel($row[0],$nestingLevel + 1, $lastNestingLevel);
    }
}

$handle = @fopen("type_a.txt", "w+");
exportFromDb(null, null, 0);
@fclose($handle);

$handle = @fopen("type_b.txt", "w+");
exportFromDbUntilNestingLevel(null, 0, 1);
@fclose($handle);