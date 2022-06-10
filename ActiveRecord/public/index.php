<?php

use Dasha\application\controller\Chat_Controller;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Dasha\application\Archive;

require_once dirname(__DIR__) . '/vendor/autoload.php';

$connection = new PDO('mysql:dbname=MyBase;host=127.0.0.1','dasha','param12345');
$File_Logs = "/var/www/html/ActiveRecord/logs/logs_archive.log";

$loader = new FilesystemLoader(dirname(__DIR__) . '/templates/');
$messageHandler = new StreamHandler($File_Logs, Logger::INFO);

$twig = new Environment($loader);
$log = new Logger('action');
$chat = new Chat_Controller($twig, $log, $messageHandler);

$log->pushHandler($messageHandler);

$chat->__invoke();
$chat->__invokeClear();

echo "История сообщений:</p>";
$chat->print_message($connection);
$chat->__invokeButtons();

$login = $_GET['login'];
$password = $_GET['password'];

if ((!empty($login)) || (!empty($password))) {
    $sql = 'SELECT * from user where username = :login';
    $stmt = $connection->prepare($sql);
    $stmt->bindParam('login', $login, PDO::PARAM_STR);
    $stmt->execute();
    $table = $stmt->fetchAll();

    if ($table[0]['password'] == $password) {
        setcookie('global_login', $login, time() + 180);
        $chat->__invokeMesseng($login);
    } else if (empty($table)) {
        $log->error('This user is not registered');
        echo "<script> alert('Такого пользователь незарегестрирован.') </script>";
    } else {
        $log->error('Incorrect password entered');
        echo "<script> alert('Введен неверный пароль.') </script>";
    }
}

$message = $_GET['message'];
if (isset($message) && $message !== '') {
    $chat->add_message($connection, $_COOKIE['global_login'], $message);
    header('Refresh: 0; url=index.php');
}

//Удаление всех сообщений
if (isset($_GET['delete'])) {
    $chat->delete($connection);
    header('Refresh: 0; url=index.php');
}

/*  Active Record   */

$information = new Archive();
$DB_results = $information->getAll();
$personById = null;

function FilingInTheTable($connection, $chat) {
    $chat->deleteArchive($connection);
    $sql = 'INSERT INTO messArchive_ActiveRecord(data_mes, username, messages) SELECT data_mes, username, messages FROM message_archive';
    $stmt = $connection->prepare($sql);
    $stmt->execute();
}

// Получение всех записей
if (isset($_GET['getAllRecords'])) {
    //FilingInTheTable($connection, $chat);
    $chat->getAllRecords($DB_results);
}

// Получение записи по ID
if (isset($_GET['getByID'])) {
    //FilingInTheTable($connection, $chat);
    $chat->__invokeID();
}

$getId = $_GET['ID'];
if ($getId != '') $chat->getByID($getId);

// Удаление записи
if (isset($_GET['deleteRecord'])) {
    //FilingInTheTable($connection, $chat);
    $chat->__invokeDelete();
}

$ID_del = $_GET['ID_del'];
if ($ID_del != '') $chat->deleteRecords($ID_del);

// Сохранение записи (добавление, обновление)
if (isset($_GET['saveRecord'])) {
    //FilingInTheTable($connection, $chat);
    $chat->__invokeSave();
}

$ID_save = $_GET['ID_save'];

if ($ID_save != '')
    $chat->sendInfo($ID_save);

// Получению записей по значению поля из таблицы
if (isset($_GET['getByFieldValue'])) {
    //FilingInTheTable($connection, $chat);
    $chat->__invokeFieldValue();
}

$input = $_GET['input'];
$date_field = str_replace('T', ' ', $_GET['date_field']);

if ($input != '' || $date_field != '')
    $chat->fieldInfo($input, $date_field, $connection);

?>