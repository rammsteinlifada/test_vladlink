<?php
include('databaseClient.php');

class categoriesManager
{
    public static function exportFromDb(?int $parentId,
                                        ?string $route,
                                        int $nestingLevel,
                                        $resource) {
        $data = (new databaseClient())->getDbDataByParentId($parentId);
        while ($row = $data->fetch_row()) {
            $nextRoute = "/" . $row[2];
            $placeholder = str_repeat("\t", $nestingLevel);
            $placeholder .= $row[1] . " " . $route . $nextRoute . "\n";
            fwrite($resource, $placeholder);
            self::exportFromDb($row[0], $nextRoute, $nestingLevel + 1, $resource);
        }
    }

    public static function exportFromDbUntilNestingLevel(?int $parentId,
                                                         int $nestingLevel,
                                                         int $lastNestingLevel,
                                                              $resource) {
        if ($lastNestingLevel < 0) {
            return;
        }
        if ($nestingLevel > $lastNestingLevel) {
            return;
        }
        $data = (new databaseClient())->getDbDataByParentId($parentId);
        while ($row = $data->fetch_row()) {
            $placeholder = str_repeat("\t", $nestingLevel);
            $placeholder .= $row[1] . "\n";
            fwrite($resource, $placeholder);
            self::exportFromDbUntilNestingLevel($row[0],
                $nestingLevel + 1,
                $lastNestingLevel,
                $resource);
        }
    }

    public static function importToDb(array $categories, ?string $parent) {
        foreach ($categories as $item) {
            (new databaseClient())->insert($item, $parent);
            if (array_key_exists("childrens", $item)) {
                $childrens = $item["childrens"];
                self::importToDb($childrens, $item["alias"]);
            }
        }
    }

    public static function getDataFromJson(string $file) {
        $handle = @fopen($file, "r");

        if ($handle !== false) {
            $json = fgets($handle);
        } else {
            throw new Exception('Ошибка чтения файла');
        }

        fclose($handle);

        if ($json === false) {
            throw new Exception('Ошибка чтения с файла');
        }
        return json_decode($json, true);
    }

}