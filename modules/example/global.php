<?php

echo "<pre>";
echo '<br>Проверка для констант:<br>';
echo 'ROOT: ' . (ROOT ? ROOT : 'Не задано') . '<br>';
echo 'TM: ' . (TM ? TM : 'Не задано') . '<br>';

echo '<br>Проверка для переменных, которые могут быть доступны в глобальном контексте:<br>';
echo 'PHP_SELF: ' . (PHP_SELF ? PHP_SELF : 'Не задано') . '<br>';
echo 'HTTP_HOST: ' . (HTTP_HOST ? HTTP_HOST : 'Не задано') . '<br>';
echo 'SERVER_NAME: ' . (SERVER_NAME ? SERVER_NAME : 'Не задано') . '<br>';
echo 'HTTP_REFERER: ' . (HTTP_REFERER ? HTTP_REFERER : 'Не задано') . '<br>';
echo 'BROWSER: ' . (BROWSER ? BROWSER : 'Не задано') . '<br>';
echo 'IP: ' . (IP ? IP : 'Не задано') . '<br>';
echo 'SCHEME: ' . (SCHEME ? SCHEME : 'Не задано') . '<br>';
echo 'REQUEST_URI: ' . (REQUEST_URI ? REQUEST_URI : 'Не задано') . '<br>';

echo '</pre>';