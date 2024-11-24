<?php

# Це використовується для перевірки, чи правильно запущено додаток.
define("LiveCMS", true);

# Встановлюємо HTTP-заголовок, який інформує, що сервер працює під управлінням "LiveCMS".
header('Powered: LiveCMS - Creative Resource Management');

# Вказуємо браузеру, що кешування дозволено для загального доступу.
header("Cache-control: public");

# Константи та псевдо функції для скорочення змінних.
require $_SERVER['DOCUMENT_ROOT'] . '/platform/livecms.php';

# Плагін для тестування
require ROOT . '/modules/example/index.php';

