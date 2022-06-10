<?php
namespace Dasha\application\controller;

use Twig\Environment;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use DateTimeImmutable;
use PDO;
use Dasha\application\Archive;

class Chat_Controller
{
    private $twig;
    private $log;
    private $messageHandler; //Handler

    public function __construct(Environment $twig, Logger $log, StreamHandler $messageHandler)
    {
        $this->twig = $twig;
        $this->log = $log;
        $this->messageHandler = $messageHandler;
    }

    public function __invoke()
    {
        echo $this->twig->render('auth.html.twig');
    }

    public function __invokeClear()
    {
        echo $this->twig->render('clear.html.twig');
    }

    public function __invokeMesseng($login)
    {
        echo $this->twig->render('messengs.html.twig',['login' => $login]);
    }

    public function __invokeButtons()
    {
        echo $this->twig->render('buttons.html.twig');
    }

    public function __invokeID()
    {
        echo $this->twig->render('ID.html.twig');
    }

    public function __invokeDelete()
    {
        echo $this->twig->render('deleteR.html.twig');
    }

    public function __invokeSave()
    {
        echo $this->twig->render('saveR.html.twig');
    }

    public function __invokeFieldValue()
    {
        echo $this->twig->render('fieldV.html.twig');
    }

    // Запись сообщений в файл
    function add_message($connection, $login, $message)
    {
        $data = (new DateTimeImmutable())->format('Y-m-d h:i');
        $sql = 'insert into message_archive values (:data , :login, :message)';
        $stmt = $connection->prepare($sql);

        $stmt->bindParam('data', $data, PDO::PARAM_STR);
        $stmt->bindParam('login', $login, PDO::PARAM_STR);
        $stmt->bindParam('message', $message, PDO::PARAM_STR);
        $stmt->execute();

        echo "Загрузка...";

        $this->log->info('New message', ['login' => $login, 'message' => $message]);
    }

    function delete($connection) {
        $sql = 'DELETE FROM message_archive';
        $stmt = $connection->prepare($sql);
        $stmt->execute();
        echo "<script> alert('Все данные удалены!') </script>";

        $this->log->info('Chat was cleared');
    }

    function print_message($connection)
    {
        $sql = 'SELECT * from message_archive ORDER BY data_mes ASC';
        $stmt = $connection->prepare($sql);
        $stmt->execute();

        $result = $connection->query($sql);

        if ($result->rowCount() !== 0) {
            foreach ($result as $row) {
                $data = $row["data_mes"];
                $login = $row["username"];
                $message = $row["messages"];

                echo "<p>$data $login: $message</p>";

            }} else echo "История сообщений пуста :(</p>";
    }

    /*  Active Record   */

    function deleteArchive($connection) {
        $sql = 'DELETE FROM messArchive_ActiveRecord';
        $stmt = $connection->prepare($sql);
        $stmt->execute();
    }

    // Получение всех записей
    function getAllRecords($DB_results) {
        foreach ($DB_results as $row) {
            $ID = $row["ID_mes"];
            $data = $row["data_mes"];
            $login = $row["username"];
            $message = $row["messages"];

            echo "$ID $data $login: $message</p>";
        }
    }

    // Получение записей по ID
    function getByID($getId) {
        $Archive = new Archive();
        $personById = $Archive->findById($getId);

        if (!is_null($personById)) {
            $id = $personById->getId();
            $data = $personById->getData();
            $name = $personById->getName();
            $message = $personById->getMessage();

            echo "$id $data $name: $message</p>";
        }
        else echo "Такой ID не найден</p>";
    }

    // Удаление записи по ID
    function deleteRecords($getId) {
        $Archive = new Archive();
        $personById = $Archive->findById($getId);

        if (!is_null($personById)) {
            $id = $personById->getId();
            $Archive->delete($id);
        }
        else echo "Такой ID не найден</p>";
    }

    function sendInfo($ID_save) {
        $name_save = $_GET['name_save'];
        $date_save = str_replace('T', ' ', $_GET['date_save']);
        $message_save = $_GET['message_save'];
        $action = $_GET['action'];

        $Archive = new Archive();

        $personById = $Archive->findById($ID_save);

        $Archive->setId($ID_save);
        $Archive->setName($name_save);
        $Archive->setData($date_save);
        $Archive->setMessage($message_save);

        switch ($action) {
            case 'addRecords':
                if (!is_null($personById)) echo "Такая запись уже есть. Поменяйте ID.";
                else $Archive->save();
                break;
            case 'updateRecords':
                if (!is_null($personById)) $Archive->update();
                else echo "Такой записи нет. Поменяйте ID.";
                break;
        }
    }

    function fieldInfo($input, $date_field, $connection) {
        $Archive = new Archive();
        $action = $_GET['action'];

        switch ($action) {
            case 'ID_act':
                $this->getByID($input);
                break;
            case 'log_act':
                $sql = 'SELECT * from messArchive_ActiveRecord WHERE username = :input';
                $stmt = $connection->prepare($sql);
                $stmt->execute();
                $stmt->bindParam('input', $input, PDO::PARAM_STR);
                $stmt->execute();

                $Archive->findByValue($stmt);
                break;
            case 'date_act':
                $sql = 'SELECT * from messArchive_ActiveRecord WHERE data_mes = :date_field';
                $stmt = $connection->prepare($sql);
                $stmt->execute();
                $stmt->bindParam('date_field', $date_field, PDO::PARAM_STR);
                $stmt->execute();

                $Archive->findByValue($stmt);
                break;
            case 'mes_act':
                $sql = 'SELECT * from messArchive_ActiveRecord WHERE messages = :input';
                $stmt = $connection->prepare($sql);
                $stmt->execute();
                $stmt->bindParam('input', $input, PDO::PARAM_STR);
                $stmt->execute();

                $Archive->findByValue($stmt);
                break;
        }
    }

}
