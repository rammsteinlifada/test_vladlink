<html>
    <head>
        <title>Тестируем PHP</title>
    </head>
    <body>
        <pre>
<?php
    include('exportCategories.php');
    $handle = @fopen("tmp.txt", "w+");
    exportFromDbUntilNestingLevel(null, 0, 1024);
    rewind($handle);
    if ($handle !== false) {
        $string = fgets($handle);
        while ($string !== false) {
            echo($string . "<br>");
            $string = fgets($handle);
        }
    } else {
        echo("Ошибка чтения файла" . "\n");
    }
    fclose($handle);
    unlink("tmp.txt");
?>
        </pre>
    </body>
</html>