<?php
include('categoriesManager.php');

$handle = fopen("type_a.txt", "w+");
categoriesManager::exportFromDb(null, null, 0, $handle);
fclose($handle);

$handle = fopen("type_b.txt", "w+");
categoriesManager::exportFromDbUntilNestingLevel(null, 0, 1, $handle);
fclose($handle);
