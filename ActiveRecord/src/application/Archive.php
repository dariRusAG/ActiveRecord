<?php

namespace Dasha\application;

use PDO;

class Archive
{
    private $id;
    private $data;
    private $name;
    private $message;
    private $connection;

    public function __construct() {
        $this->connection = new PDO('mysql:dbname=MyBase;host=127.0.0.1','dasha','param12345');
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getData()
    {
        return $this->data;
    }

    public function setData($data)
    {
        $this->data = $data;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function setMessage($message)
    {
        $this->message = $message;
    }

    // Получить все данные с таблицы
    public function getAll() {
        $sql = 'SELECT * from messArchive_ActiveRecord';
        $stmt = $this->connection->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Поиск записи по ID
    public function findById($id) {
        $sql = 'SELECT * from messArchive_ActiveRecord WHERE ID_mes = :id LIMIT 1';
        $stmt = $this->connection->prepare($sql);

        $stmt->bindParam('id', $id, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetchAll()[0];
        $foundParameter = null;

        if (isset($result)) {
            $foundParameter = new Archive();
            $foundParameter->setId($result['ID_mes']);
            $foundParameter->setName($result['username']);
            $foundParameter->setData($result['data_mes']);
            $foundParameter->setMessage($result['messages']);
        }
        return $foundParameter;
    }

    // Поиск записи по любому полю
    public function findByValue($stmt)
    {
        $i = 0;
        $result = $stmt->fetchAll();
        foreach ($result as $row) $i = $i + 1;

        if ($i == 0) echo 'Записей не найдено';
        else {
            $foundParameter = new Archive();
            foreach ($result as $row) {
                $foundParameter->setId($row['ID_mes']);
                $foundParameter->setName($row['username']);
                $foundParameter->setData($row['data_mes']);
                $foundParameter->setMessage($row['messages']);

                $id = $foundParameter->getId();
                $data = $foundParameter->getData();
                $name = $foundParameter->getName();
                $message = $foundParameter->getMessage();

                echo "$id $data $name: $message</p>";
            }
        }
    }

    // Удаление записи
    public function delete($id) {
        $sql = 'DELETE FROM messArchive_ActiveRecord WHERE ID_mes = :id';
        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam('id', $id, PDO::PARAM_STR);
        $stmt->execute();

        echo "Запись: $id успешно удалена.";
    }

    // Добавление записи
    public function save() {
        $id = $this->id;
        $data = $this->data;
        $name = $this->name;
        $message = $this->message;

        if ($id != '' && $data != '' && $name != '' &&  $message != '')
            {
                $sql = 'INSERT INTO messArchive_ActiveRecord values(:id, :name, :data, :message)';
                $stmt = $this->connection->prepare($sql);

                $stmt->bindParam('id', $id);
                $stmt->bindParam('name', $name);
                $stmt->bindParam('data', $data);
                $stmt->bindParam('message', $message);

                $stmt->execute();

                echo "Запись: $id успешно добавлена.";
            }
        else echo "Введены не все поля";
    }

    // Обновление записи
    public function update() {
        $id = $this->id;
        $data = $this->data;
        $name = $this->name;
        $message = $this->message;

        if ($id != '' && $data != '' && $name != '' &&  $message != '')
        {
            $sql = 'UPDATE messArchive_ActiveRecord SET data_mes = :data, username = :name, messages = :message WHERE ID_mes = :id';
            $stmt = $this->connection->prepare($sql);

            $stmt->bindParam('id', $id);
            $stmt->bindParam('name', $name);
            $stmt->bindParam('data', $data);
            $stmt->bindParam('message', $message);
            $stmt->execute();

            echo "Запись: $id успешно обновлена.";
        }
        else echo "Введены не все поля";
    }
}