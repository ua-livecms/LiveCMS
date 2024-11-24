<?php

/**
 * Константи та псевдо функції для скорочення змінних та функцій
 */

# Функція `remove_script` видаляє потенційно небезпечні скрипти та елементи з текстового рядка.
function remove_script($string = null)
{
    # Видаляє спеціальні символи ASCII, які можуть використовуватися для прихованих маніпуляцій.
    $string = preg_replace('/[\\x00-\\x08\\x0B\\x0C\\x0E-\\x1F\\x7F]+/S', '', $string);

    # Містить ключові слова, які можуть використовуватися для виконання скриптів (наприклад, vbscript, embed).
    $parm1 = array('vbscript', 'expression', 'applet', 'xml', 'blink', 'embed', 'object', 'frameset', 'ilayer', 'layer', 'bgsound');

    # Містить події браузера, які можуть викликати виконання шкідливого коду (наприклад, onload, onclick).
    $parm2 = array(
        'onabort', 'onactivate', 'onafterprint', 'onafterupdate', 'onbeforeactivate', 'onbeforecopy', 'onbeforecut',
        'onbeforedeactivate', 'onbeforeeditfocus', 'onbeforepaste', 'onbeforeprint', 'onbeforeunload', 'onbeforeupdate',
        'onblur', 'onbounce', 'oncellchange', 'onchange', 'oncontextmenu', 'oncontrolselect', 'oncopy', 'oncut',
        'ondataavailable', 'ondatasetchanged', 'ondatasetcomplete', 'ondblclick', 'ondeactivate', 'ondrag', 'ondragend',
        'ondragenter', 'ondragleave', 'ondragover', 'ondragstart', 'ondrop', 'onerror', 'onerrorupdate', 'onfilterchange',
        'onfinish', 'onfocus', 'onfocusin', 'onfocusout', 'onhelp', 'onkeydown', 'onkeypress', 'onkeyup',
        'onlayoutcomplete', 'onload', 'onlosecapture', 'onmousedown', 'onmouseenter', 'onmouseleave', 'onmousemove',
        'onmouseout', 'onmouseover', 'onmouseup', 'onmousewheel', 'onmove', 'onmoveend', 'onmovestart', 'onpaste',
        'onpropertychange', 'onreadystatechange', 'onreset', 'onresize', 'onresizeend', 'onresizestart', 'onrowenter',
        'onrowexit', 'onrowsdelete', 'onrowsinserted', 'onscroll', 'onselect', 'onselectionchange', 'onselectstart',
        'onstart', 'onstop', 'onsubmit', 'onunload'
    );

    # Об'єднує всі заборонені елементи в один список.
    $parm = array_merge($parm1, $parm2);

    # Перебирає всі заборонені слова для їх видалення.
    for ($i = 0; $i < sizeof($parm); $i++) {
        # Формує регулярний вираз для пошуку заборонених слів з урахуванням можливих маніпуляцій (наприклад, вставка символів).
        $pattern = '/';
        for ($j = 0; $j < strlen($parm[$i]); $j++) {
            if (0 < $j) {
                $pattern .= '(';
                $pattern .= '(&#[x|X]0([9][a][b]);?)?'; # Юнікод-посилання.
                $pattern .= '|(&#0([9][10][13]);?)?';   # Десяткові посилання.
                $pattern .= ')?';
            }
            $pattern .= $parm[$i][$j];
        }
        $pattern .= '/i'; # Незалежність від регістру.

        # Видаляє знайдені збіги з рядка.
        $string = preg_replace($pattern, ' ', $string);
    }

    # Повертає очищений рядок.
    return $string;
}

# Функція `_filter` виконує додаткову обробку даних для захисту.
function _filter($data)
{
    # 1. Викликає функцію `remove_script` для видалення небезпечних елементів.
    # 2. Екранує спеціальні символи (addslashes).
    # 3. Перетворює спеціальні HTML-символи у текстову форму (htmlspecialchars).
    return remove_script(addslashes(htmlspecialchars($data)));
}

# Шлях від кореневої директорії (поточна коренева директорія на сервері).
define('ROOT', $_SERVER['DOCUMENT_ROOT']);

# Поточний системний час (мітка часу UNIX).
define('TM', time());

# Ім'я файлу, до якого виконується звернення (наприклад, `index.php`).
define('PHP_SELF', _filter($_SERVER['PHP_SELF']));

# Домен сайту (наприклад, `example.com`).
define('HTTP_HOST', _filter($_SERVER['HTTP_HOST']));

# Ім'я сервера (аналогічне до `HTTP_HOST`, але іноді може відрізнятися).
define('SERVER_NAME', _filter($_SERVER['SERVER_NAME']));

# URL сторінки, з якої прийшов користувач (реферер). Якщо дані відсутні, встановлюється значення `'none'`.
if (isset($_SERVER['HTTP_REFERER'])) {
    define('HTTP_REFERER', _filter($_SERVER['HTTP_REFERER']));
} else {
    define('HTTP_REFERER', 'none');
}

# Інформація про браузер користувача (заголовок `User-Agent`). Якщо дані відсутні, встановлюється значення `'none'`.
if (isset($_SERVER['HTTP_USER_AGENT'])) {
    define('BROWSER', _filter($_SERVER["HTTP_USER_AGENT"]));
} else {
    define('BROWSER', 'none');
}

# IP-адреса користувача (перевіряється через фільтр `FILTER_VALIDATE_IP` для забезпечення коректності).
define('IP', _filter(filter_var($_SERVER["REMOTE_ADDR"], FILTER_VALIDATE_IP)));

# Визначення протоколу
if (isset($_SERVER['HTTPS'])) { 
    define('SCHEME', 'https://');                       // Якщо встановлено HTTPS, задаємо протокол "https://".
    $scheme = _filter($_SERVER['HTTPS']);               // Отримуємо значення протоколу з параметра сервера.
} else { 
    $scheme = null;                                     // Якщо HTTPS не встановлено, ініціалізуємо змінну $scheme як null.
    if ($scheme && $scheme != 'off') {                  // Перевіряємо, чи існує $scheme і чи воно не дорівнює "off".
        define('SCHEME', 'https://');                   // Якщо протокол активний, задаємо "https://".
    } else { 
        define('SCHEME', 'http://');                    // У всіх інших випадках використовуємо протокол "http://".
    }
}

# Повний URL-адрес запитуваної сторінки
if (isset($_SERVER["REQUEST_URI"])) {
    define('REQUEST_URI', _filter($_SERVER["REQUEST_URI"]));    // Якщо параметр "REQUEST_URI" встановлено, фільтруємо його та зберігаємо в константу.
} else {
    define('REQUEST_URI', '/');                                 // Якщо параметр "REQUEST_URI" відсутній або недоступний, встановлюємо значення за замовчуванням "/".
}

# Функція для роботи з змінною $_GET.
function get($data, $d = 0) {
    // Якщо ключ $data відсутній у масиві $_GET, повертається false
    if (!isset($_GET[$data])) {
        return isset($_GET[$data]);
    } else {
        // Якщо $d дорівнює 0, значення проходить через фільтрацію за допомогою remove_script()
        // Інакше повертається значення без змін
        return ($d == 0 ? remove_script($_GET[$data]) : $_GET[$data]);
    }
}

# Функція для роботи з змінною $_POST.
function post($data, $d = 0) {
    // Якщо ключ $data відсутній у масиві $_POST, повертається false
    if (!isset($_POST[$data])) {
        return isset($_POST[$data]);
    } else {
        // Якщо $d дорівнює 0, значення проходить через фільтрацію за допомогою remove_script()
        // Інакше повертається значення без змін
        return ($d == 0 ? remove_script($_POST[$data]) : $_POST[$data]);
    }
}

# Функція для роботи зі змінною $_COOKIE
function cookie($name) {
    // Перевірка, чи існує змінна з ключем $name у масиві $_COOKIE
    if (!isset($_COOKIE[$name])) {
        // Якщо змінної немає, повертається false
        return isset($_COOKIE[$name]);
    } else {
        // Якщо змінна існує, її значення очищується за допомогою remove_script()
        return remove_script($_COOKIE[$name]);
    }
}

# Функція для перенаправлення (редиректу).
function redirect($url, $refresh = 0) {
    
    /**
     * $url - посилання, на яке потрібно перенаправити користувача.
     * $refresh - час затримки (у секундах) перед перенаправленням.
    */

    if ($refresh <= 0) { 
        // Якщо час затримки не вказаний або дорівнює 0:
        // Використовуємо HTTP-заголовок "Location" для негайного перенаправлення.
        return header('Location: ' . $url) . exit();
    } else { 
        // Якщо вказана затримка:
        // Використовуємо HTTP-заголовок "Refresh" для затримки перед перенаправленням.
        return header('Refresh: ' . $refresh . '; url=' . $url) . exit();
    }
}
