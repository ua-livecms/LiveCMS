<?php

# Це використовується для перевірки, чи правильно запущено додаток.
define("LiveCMS", true);

# Встановлюємо HTTP-заголовок, який інформує, що сервер працює під управлінням "LiveCMS".
header('Powered: LiveCMS - Creative Resource Management');

# Вказуємо браузеру, що кешування дозволено для загального доступу.
header("Cache-control: public");

# Буферизація та сесії.
require './platform/requires/session.php';

# Константи та псевдо функції для скорочення змінних.
require './platform/requires/redefinition.php';

# Перевіряємо значення конфігурації 'INTERPRETATOR'.
require './platform/requires/interpretator.php';

# Підключення до бази даних.
require './platform/requires/database.php';

# Подгрузка функций из папки /platform/functions/.
require './platform/requires/functions.php';

# Подгрузка классов из папки /platform/classes/.
require './platform/requires/classes.php';

# Підключення файлу конфігурації сайту.
require './platform/requires/configs.php';

# Плагін для тестування
require './modules/example/redefinition.php';
