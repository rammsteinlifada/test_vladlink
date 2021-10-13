<?php

include('database.php');

function exportFromDb(?int $parentId, ?string $route, int $nestingLevel, $resource) {
    $data = getDbDataByParentId($parentId);
    while ($row = $data->fetch_row()) {
        $nextRoute = "/" . $row[2];
        $placeholder = str_repeat("\t", $nestingLevel);
        $placeholder .= $row[1] . " " . $route . $nextRoute . "\n";
        fwrite($resource, $placeholder);
        exportFromDb($row[0], $nextRoute, $nestingLevel + 1, $resource);
    }
}

function exportFromDbUntilNestingLevel(?int $parentId,
                                       int $nestingLevel,
                                       int $lastNestingLevel,
                                       $resource) {
    if ($lastNestingLevel < 0) {
        return;
    }
    if ($nestingLevel > $lastNestingLevel) {
        return;
    }
    $data = getDbDataByParentId($parentId);
    while ($row = $data->fetch_row()) {
        $placeholder = str_repeat("\t", $nestingLevel);
        $placeholder .= $row[1] . "\n";
        fwrite($resource, $placeholder);
        exportFromDbUntilNestingLevel($row[0],
                           $nestingLevel + 1,
                                      $lastNestingLevel,
                                      $resource);
    }
}

$handle = fopen("type_a.txt", "w+");
exportFromDb(null, null, 0, $handle);
fclose($handle);

$handle = fopen("type_b.txt", "w+");
exportFromDbUntilNestingLevel(null, 0, 1, $handle);
fclose($handle);
