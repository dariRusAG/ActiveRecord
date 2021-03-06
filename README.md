# ActiveRecord
## Практическая реализации DAL (Data Access Layer) через паттерн Active Record

*Задача* Реализовать простое приложение PHP с доступом в одну из таблиц БД через паттерн Active Record.
Порядок выполнения:
1. Анализ задачи.
2. Исследование источников.
3. С помощью паттерна Active Record реализовать функционал над одной таблицей БД по:
 * получению всех записей
 * получению записи по id
 * получению записей по значению поля из таблицы (фильтрация по полю)
 * сохранению записи _cохранениие записи делится на обновление и добавление_
 * удалению записи

Форма отчета: репозиторий на GitHub с php-приложением, работоспособное приложение доступное по сети, в котором в качестве DAL используется паттерн Active Record.

### Результат
[Чат](http://143.198.70.213:4444/)

### Конечная таблица в MySql по которой испытывался функционал:
![Таблица](https://user-images.githubusercontent.com/91362737/173147398-41546186-886e-4fbc-b743-aa8f503d0a64.png)

### Архитектура папок:
![Структура](https://user-images.githubusercontent.com/91362737/173147797-024ec1ad-2580-4249-8c42-84d72fdd73a1.png)

#### P.S: Сайт в идеале должен был работать совместно с чатом (при добавлении сообщений - добавляется и в нашу базу), но как и все в этом мире он не идеален. 
#### P.P.S: Это реализовано, но с некоторыми ошибками, да и суть в Active Record, так что не стала ломать голову.
