<?php

    //Получаем HTTP от recaptcha
    $recaptcha = $_REQUEST['g-recaptcha-response'];
    //Сюда пишем СЕКРЕТНЫЙ КЛЮЧ, который нам присвоил гугл
    $secret = '6Lfc6qUUAAAAAMEIq3qUYmtkpMs82X6DfQCL5Zo7';
    //Формируем utl адрес для запроса на сервер гугла
    $url = "https://www.google.com/recaptcha/api/siteverify?secret=".$secret ."&response=".$recaptcha."&remoteip=".$_SERVER['REMOTE_ADDR'];

    //Инициализация и настройка запроса
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_TIMEOUT, 10);
    curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.2.16) Gecko/20110319 Firefox/3.6.16");
    //Выполняем запрос и получается ответ от сервера гугл
    $curlData = curl_exec($curl);

    curl_close($curl);
    //Ответ приходит в виде json строки, декодируем ее

    return json_decode($curlData, true);
?>