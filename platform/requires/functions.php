<?php

# Відкриваємо директорію, яка містить файли з функціями.
$data = opendir(ROOT . '/platform/functions');

# Перебираємо всі файли в директорії.
while ($function = readdir($data)) {
    # Перевіряємо, чи файл має розширення `.php`.
    if (preg_match('#\.php$#i', $function)) {
        # Підключаємо файл, що відповідає умові, до основного скрипту.
        require_once(ROOT . '/platform/functions/' . $function);
    }
}
