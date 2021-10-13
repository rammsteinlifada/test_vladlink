<?php
class databaseClient
{
    private string $dbname;
    private string $servername;
    private string $username;
    private string $password;

    public function __construct()
    {
        $this->setDbname("testDB");
        $this->setServername("localhost");
        $this->getUsernameAndPasswordFromFile();
    }

    public function setDbname(string $dbname): void
    {
        $this->dbname = $dbname;
    }

    public function getDbname(): string
    {
        return $this->dbname;
    }

    public function setServername(string $servername): void
    {
        $this->servername = $servername;
    }

    public function getServername(): string
    {
        return $this->servername;
    }

    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getUsernameAndPasswordFromFile()
    {
        $handle = @fopen("MySQL.txt", "r");
        if ($handle !== false) {
            $username = fgets($handle);
            $password = fgets($handle);
        } else {
            throw new Exception("Ошибка чтения файла");
            return;
        }
        fclose($handle);
        if ($username === false || $password === false) {
            throw new Exception("Ошибка чтения с файла");
        } else {
            self::setUsername(rtrim($username));
            self::setPassword($password);
        }
    }

    public function createTable()
    {
        $connection = new mysqli(self::getServername(),
                                self::getUsername(),
                                self::getPassword(),
                                self::getDbname());
        $table = "CREATE TABLE categories (
            Id INT PRIMARY KEY NOT NULL,
            Name CHAR(100) NOT NULL,
            Alias CHAR(30) NOT NULL,
            ParentId INT,
            FOREIGN KEY (ParentId) REFERENCES categories(Id)
        )";

        if ($connection->query($table) === false) {
            throw new Exception('Ошибка создания БД ' . $connection->error);
        } else {
            print("Успешное создание БД" . "\n");
        }

        $connection->close();
    }

    public function insert(array $categoryArray, ?string $categoryParent)
    {
        $connection = new mysqli(self::getServername(),
                                self::getUsername(),
                                self::getPassword(),
                                self::getDbname());
        $categoryId = $categoryArray["id"];
        $categoryName = $categoryArray["name"];
        $categoryAlias = $categoryArray["alias"];
        if ($categoryParent) {
            $sql = "SELECT * FROM categories WHERE Alias = ?";
            $stmt = $connection->prepare($sql);
            $stmt->bind_param('s', $categoryParent);
            $executeResult = $stmt->execute();
            if ($executeResult === false) {
                throw new Exception('Ошибка. Не удалось найти категорию в таблице');
            }
            $result = $stmt->get_result();
            $categoryParentId = $result->fetch_row()[0];
        } else {
            $categoryParentId = null;
        }

        $sql = "INSERT INTO categories (Id, Name, Alias, ParentId) VALUES (?, ?, ?, ?)";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param('issi',
                          $categoryId,
                           $categoryName,
                                $categoryAlias,
                                $categoryParentId);
        $executeResult = $stmt->execute();
        if ($executeResult === false) {
            throw new Exception('Ошибка. Не удалось вставить в таблицу');
        }

        $connection->close();
    }

    public function getDbDataByParentId(?int $ParentId)
    {
        $connection = new mysqli(self::getServername(),
                                self::getUsername(),
                                self::getPassword(),
                                self::getDbname());
        if ($ParentId === null) {
            $sql = "SELECT * FROM categories WHERE ParentId IS NULL";
            $stmt = $connection->prepare($sql);
        } else {
            $sql = "SELECT * FROM categories WHERE ParentId = ?";
            $stmt = $connection->prepare($sql);
            $stmt->bind_param('i', $ParentId);
        }
        $executeResult = $stmt->execute();
        if ($executeResult === false) {
            throw new Exception('Ошибка. Не удалось найти категорию в таблице');
        }
        return $stmt->get_result();
    }

}