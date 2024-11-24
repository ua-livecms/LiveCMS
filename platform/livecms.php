<?php

/**
 * Инициализация сессии с дополнительными проверками и безопасной обработкой.
 */

// Запускаем буферизацию вывода, чтобы предотвратить отправку контента до завершения выполнения.
ob_start();

/**
 * Назначаем имя сессии.
 * Имя сессии устанавливается как 'SID'. Это может быть полезно для настройки сессий с уникальными именами.
 */

session_name('SID');

/**
 * Проверяем статус сессии.
 * Если сессия не была запущена, запускаем её.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Получаем текущий идентификатор сессии.
 * Идентификатор сессии хранится в переменной $sessID.
 */

$sessID = session_id();

/**
 * Проверяем, соответствует ли текущий идентификатор сессии шаблону.
 * Шаблон проверяет, что идентификатор состоит только из букв (A-Z, a-z) и цифр (0-9) и имеет длину 32 символа.
 */

if (!preg_match('#^[A-Za-z0-9]{32}$#', $sessID)) {

    /**
     * Если идентификатор не соответствует шаблону, генерируем новый идентификатор с помощью MD5-хеширования.
     * В данном случае используется случайное число для генерации уникального идентификатора.
     */

    $sessID = md5(mt_rand(0, 999999));
}

/**
 * Функція для видалення потенційно небезпечних скриптів та елементів з текстового рядка.
 * Видаляє спеціальні символи та заборонені теги/події, що можуть бути використані для атак.
 *
 * @param string|null $string Текст, з якого потрібно видалити небезпечні елементи.
 * @return string Очищений текст.
 */

function remove_script($string = null)
{
    // Видаляємо спеціальні символи ASCII
    $string = preg_replace('/[\\x00-\\x08\\x0B\\x0C\\x0E-\\x1F\\x7F]+/S', '', $string);

    // Множина заборонених тегів
    $forbidden_tags = array(
        'vbscript',
        'expression',
        'applet',
        'xml',
        'blink',
        'embed',
        'object',
        'frameset',
        'ilayer',
        'layer',
        'bgsound',
        'onabort',
        'onactivate',
        'onafterprint',
        'onafterupdate',
        'onbeforeactivate',
        'onbeforecopy',
        'onbeforecut',
        'onbeforedeactivate',
        'onbeforeeditfocus',
        'onbeforepaste',
        'onbeforeprint',
        'onbeforeunload',
        'onbeforeupdate',
        'onblur',
        'onbounce',
        'oncellchange',
        'onchange',
        'oncontextmenu',
        'oncontrolselect',
        'oncopy',
        'oncut',
        'ondataavailable',
        'ondatasetchanged',
        'ondatasetcomplete',
        'ondblclick',
        'ondeactivate',
        'ondrag',
        'ondragend',
        'ondragenter',
        'ondragleave',
        'ondragover',
        'ondragstart',
        'ondrop',
        'onerror',
        'onerrorupdate',
        'onfilterchange',
        'onfinish',
        'onfocus',
        'onfocusin',
        'onfocusout',
        'onhelp',
        'onkeydown',
        'onkeypress',
        'onkeyup',
        'onlayoutcomplete',
        'onload',
        'onlosecapture',
        'onmousedown',
        'onmouseenter',
        'onmouseleave',
        'onmousemove',
        'onmouseout',
        'onmouseover',
        'onmouseup',
        'onmousewheel',
        'onmove',
        'onmoveend',
        'onmovestart',
        'onpaste',
        'onpropertychange',
        'onreadystatechange',
        'onreset',
        'onresize',
        'onresizeend',
        'onresizestart',
        'onrowenter',
        'onrowexit',
        'onrowsdelete',
        'onrowsinserted',
        'onscroll',
        'onselect',
        'onselectionchange',
        'onselectstart',
        'onstart',
        'onstop',
        'onsubmit',
        'onunload'
    );

    // Перебір заборонених тегів
    foreach ($forbidden_tags as $tag) {
        $pattern = '/' . preg_quote($tag, '/') . '/i';
        $string = preg_replace($pattern, ' ', $string);
    }

    return $string;
}

/**
 * Фільтрація текстових даних для захисту від XSS та SQL ін'єкцій.
 *
 * @param string $data Вхідні дані для фільтрації.
 * @return string Очищені дані.
 */

function _filter($data)
{
    // Пропускає через функцію remove_script та додає додаткову екранізацію символів.
    return remove_script(addslashes(htmlspecialchars($data)));
}

// Визначення констант для роботи з серверними змінними
define('ROOT', $_SERVER['DOCUMENT_ROOT']);
define('TM', time());
define('PHP_SELF', _filter($_SERVER['PHP_SELF']));
define('HTTP_HOST', _filter($_SERVER['HTTP_HOST']));
define('SERVER_NAME', _filter($_SERVER['SERVER_NAME']));

define('HTTP_REFERER', isset($_SERVER['HTTP_REFERER']) ? _filter($_SERVER['HTTP_REFERER']) : 'none');
define('BROWSER', isset($_SERVER['HTTP_USER_AGENT']) ? _filter($_SERVER['HTTP_USER_AGENT']) : 'none');
define('IP', _filter(filter_var($_SERVER["REMOTE_ADDR"], FILTER_VALIDATE_IP)));

// Визначення протоколу (HTTP/HTTPS)
define('SCHEME', isset($_SERVER['HTTPS']) ? 'https://' : 'http://');
define('REQUEST_URI', isset($_SERVER["REQUEST_URI"]) ? _filter($_SERVER["REQUEST_URI"]) : '/');

/**
 * Функція для отримання значення з масиву $_GET з опційною фільтрацією.
 *
 * @param string $data Ключ масиву $_GET.
 * @param int $d Якщо 0, значення буде відфільтровано.
 * @return mixed Значення з масиву $_GET або false.
 */

function get($data, $d = 0)
{
    return isset($_GET[$data]) ? ($d == 0 ? remove_script($_GET[$data]) : $_GET[$data]) : false;
}

/**
 * Функція для отримання значення з масиву $_POST з опційною фільтрацією.
 *
 * @param string $data Ключ масиву $_POST.
 * @param int $d Якщо 0, значення буде відфільтровано.
 * @return mixed Значення з масиву $_POST або false.
 */

function post($data, $d = 0)
{
    return isset($_POST[$data]) ? ($d == 0 ? remove_script($_POST[$data]) : $_POST[$data]) : false;
}

/**
 * Функція для роботи з даними з масиву $_COOKIE.
 *
 * @param string $name Ключ масиву $_COOKIE.
 * @return mixed Значення з масиву $_COOKIE або false.
 */

function cookie($name)
{
    return isset($_COOKIE[$name]) ? remove_script($_COOKIE[$name]) : false;
}

/**
 * Функція для роботи зі сесією.
 *
 * @param string $data Ключ масиву $_SESSION.
 * @param mixed $param Значення для запису, або 'no_data' для отримання значення.
 * @return mixed Значення з масиву $_SESSION або false.
 */

function session($data, $param = 'no_data')
{
    if ($param === 'no_data') {
        return isset($_SESSION[$data]) ? (is_array($_SESSION[$data]) ? $_SESSION[$data] : remove_script($_SESSION[$data])) : false;
    }
    return $_SESSION[$data] = $param;
}

/**
 * Функція для отримання або запису параметрів налаштувань.
 *
 * @param string $data Ключ налаштування.
 * @param mixed|null $param Значення для запису налаштування або null для отримання.
 * @return mixed Значення налаштування.
 */

function config($data, $param = null)
{
    global $config;
    return $param === null ? _filter($config[$data]) : $config[$data] = $param;
}

/**
 * Функція для визначення версії сайту (мобільна чи десктопна).
 *
 * @return bool true, якщо мобільний пристрій, false, якщо десктоп.
 */

function type_version()
{
    $mobile_array = array(
        'ipad',
        'iphone',
        'android',
        'pocket',
        'palm',
        'windows ce',
        'windowsce',
        'cellphone',
        'opera mobi',
        'ipod',
        'small',
        'sharp',
        'sonyericsson',
        'symbian',
        'opera mini',
        'nokia',
        'htc_',
        'samsung',
        'motorola',
        'smartphone',
        'blackberry',
        'playstation portable',
        'tablet browser'
    );

    foreach ($mobile_array as $value) {
        if (strpos(strtolower(BROWSER), $value) !== false) {
            return true;
        }
    }
    return false;
}

/**
 * Функція для перенаправлення користувача на інший URL.
 *
 * @param string $url URL для перенаправлення.
 * @param int $refresh Час затримки в секундах перед перенаправленням.
 */

function redirect($url, $refresh = 0)
{
    if ($refresh <= 0) {
        header('Location: ' . $url);
    } else {
        header('Refresh: ' . $refresh . '; url=' . $url);
    }
    exit();
}

/**
 * Загружает все конфигурационные файлы с настройками из указанной директории.
 * Использует функцию parse_ini_file для чтения INI файлов.
 * Добавлены дополнительные проверки для безопасности и обработки ошибок.
 */

// Путь к директории с конфигурационными файлами
$configDirectoryPath = ROOT . "/platform/configs/";

// Проверяем, существует ли директория
if (!is_dir($configDirectoryPath)) {
    error_log("Ошибка: Директория с конфигурационными файлами не найдена: $configDirectoryPath");
    die("Ошибка: Директория с конфигурационными файлами не найдена.");
}

// Открываем директорию
$configFiles = scandir($configDirectoryPath);

// Фильтруем только .ini файлы из директории
$configFiles = array_filter($configFiles, function ($file) {
    return pathinfo($file, PATHINFO_EXTENSION) === 'ini';
});

// Если нет конфигурационных файлов
if (empty($configFiles)) {
    error_log("Ошибка: Не найдено конфигурационных файлов в директории: $configDirectoryPath");
    die("Ошибка: Не найдено конфигурационных файлов.");
}

// Массив для хранения всех конфигураций
$configs = [];

// Перебираем все конфигурационные файлы
foreach ($configFiles as $file) {
    $filePath = $configDirectoryPath . $file;

    // Проверяем, доступен ли файл для чтения
    if (!is_readable($filePath)) {
        error_log("Ошибка: Нет доступа к файлу конфигурации для чтения: $filePath");
        continue;
    }

    // Загружаем конфигурацию из файла
    $config = @parse_ini_file($filePath, true); // true для сохранения секций

    // Проверка на ошибки при разборе файла
    if ($config === false) {
        error_log("Ошибка: Не удалось разобрать файл конфигурации: $filePath");
        continue;
    }

    // Объединяем конфигурации с уже существующими
    $configs = array_merge_recursive($configs, $config);
}

/**
 * Налаштування рівня відображення помилок залежно від конфігурації INTERPRETATOR.
 * 
 * Якщо INTERPRETATOR = 1, то увімкнено відображення всіх помилок для розробника.
 * Якщо INTERPRETATOR не дорівнює 1, то помилки не відображаються для користувача.
 */

// Проверяем, существует ли ключ "INTERPRETATOR" в конфигурации
if (isset($configs['DEBUG']['INTERPRETATOR']) && $configs['DEBUG']['INTERPRETATOR'] == 1) {
    // Увімкнено відображення всіх помилок
    ini_set('error_reporting', E_ALL);
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
} else {
    // Вимкнено відображення помилок
    ini_set('display_errors', 0);
    ini_set('display_startup_errors', 0);
    error_reporting(0);
}

/**
 * Завантажує і підключає всі PHP-файли з директорії, що містить функції.
 * 
 * Відкриває директорію, перевіряє всі файли на наявність розширення .php і підключає їх до основного скрипту.
 * Цей блок коду дозволяє автоматично підключати всі функції, що містяться в файлах у зазначеній директорії.
 */

// Відкриваємо директорію, яка містить файли з функціями
$data = opendir(ROOT . '/platform/functions');

// Перебираємо всі файли в директорії
while ($function = readdir($data)) {
    // Перевіряємо, чи файл має розширення `.php`
    if (preg_match('#\.php$#i', $function)) {
        // Підключаємо файл, що відповідає умові, до основного скрипту
        require_once(ROOT . '/platform/functions/' . $function);
    }
}

/**
 * Автозавантаження PHP класів.
 * 
 * Використовує функцію spl_autoload_register для автоматичного підключення класів, коли вони потрібні.
 * Клас має бути розміщений у директорії `/platform/PHP-classes/` з розширенням `.class.php`.
 * Якщо файл з класом існує, він підключається автоматично без потреби вказувати шлях до нього.
 */

// Реєструємо автозавантаження класів
spl_autoload_register(function ($class_name) {
    // Перевіряємо, чи існує файл з класом
    if (is_file(ROOT . '/platform/classes/' . $class_name . '.class.php')) {
        // Підключаємо файл класу
        require_once(ROOT . '/platform/classes/' . $class_name . '.class.php');
    }
});
