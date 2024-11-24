<?php

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
        'vbscript', 'expression', 'applet', 'xml', 'blink', 'embed', 'object', 'frameset', 'ilayer', 'layer', 'bgsound',
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

    // Перебір заборонених тегів
    foreach ($forbidden_tags as $tag) {
        $pattern = '/'.preg_quote($tag, '/').'/i';
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

function get($data, $d = 0) {
    return isset($_GET[$data]) ? ($d == 0 ? remove_script($_GET[$data]) : $_GET[$data]) : false;
}

/**
 * Функція для отримання значення з масиву $_POST з опційною фільтрацією.
 *
 * @param string $data Ключ масиву $_POST.
 * @param int $d Якщо 0, значення буде відфільтровано.
 * @return mixed Значення з масиву $_POST або false.
 */

function post($data, $d = 0) {
    return isset($_POST[$data]) ? ($d == 0 ? remove_script($_POST[$data]) : $_POST[$data]) : false;
}

/**
 * Функція для роботи з даними з масиву $_COOKIE.
 *
 * @param string $name Ключ масиву $_COOKIE.
 * @return mixed Значення з масиву $_COOKIE або false.
 */

function cookie($name) {
    return isset($_COOKIE[$name]) ? remove_script($_COOKIE[$name]) : false;
}

/**
 * Функція для роботи зі сесією.
 *
 * @param string $data Ключ масиву $_SESSION.
 * @param mixed $param Значення для запису, або 'no_data' для отримання значення.
 * @return mixed Значення з масиву $_SESSION або false.
 */

function session($data, $param = 'no_data') {
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

function config($data, $param = null) {
    global $config;
    return $param === null ? _filter($config[$data]) : $config[$data] = $param;
}

/**
 * Функція для визначення версії сайту (мобільна чи десктопна).
 *
 * @return bool true, якщо мобільний пристрій, false, якщо десктоп.
 */

function type_version(){
    $mobile_array = array(
        'ipad', 'iphone', 'android', 'pocket', 'palm', 'windows ce', 'windowsce', 'cellphone', 'opera mobi', 'ipod',
        'small', 'sharp', 'sonyericsson', 'symbian', 'opera mini', 'nokia', 'htc_', 'samsung', 'motorola', 'smartphone',
        'blackberry', 'playstation portable', 'tablet browser'
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

function redirect($url, $refresh = 0) {
    if ($refresh <= 0) {
        header('Location: ' . $url);
    } else {
        header('Refresh: ' . $refresh . '; url=' . $url);
    }
    exit();
}
