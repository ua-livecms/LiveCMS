<?php

/**
 * Функция для вывода заголовка и мета-информации на странице.
 * 
 * @param string|null $title Заголовок страницы.
 * @param string|null $access Доступность страницы (например, для разных групп пользователей).
 * @param string|null $description Описание страницы для мета-тегов.
 * @param string|null $keywords Ключевые слова для мета-тегов.
 * @param string|null $logo Ссылка на логотип, который будет отображаться на странице.
 */

function livecms_header($title = null, $access = null, $description = null, $keywords = null, $logo = null)
{

    // Если title не передан, устанавливаем заголовок по умолчанию
    if ($title) {
        echo "<title>$title</title>";  // Заголовок страницы
    } else {
        echo "<title>Default Title</title>";
    }

    // Если description передан, создаем мета-тег для описания
    if ($description) {
        echo "<meta name='description' content='$description'>";
    } else {
        echo "<meta name='description' content='Default description'>";
    }

    // Если keywords переданы, создаем мета-тег для ключевых слов
    if ($keywords) {
        echo "<meta name='keywords' content='$keywords'>";
    } else {
        echo "<meta name='keywords' content='default, keywords'>";
    }

    // Если доступ передан, можно использовать его для управления доступом к странице
    if ($access) {
        echo "<meta name='access' content='$access'>";
    }

    // Если логотип передан, выводим его как картинку
    if ($logo) {
        echo "<img src='$logo' alt='Logo'>";
    }

    // Пример дополнительных выводов для логирования
    echo "<h1>Page Title: $title</h1>";
}

/**
 * Функция для вывода футера страницы и завершения работы приложения.
 * 
 * @param bool $exit Если параметр $exit равен true, то выполнение скрипта будет завершено.
 */

function livecms_footer($exit = false)
{
    // Закрытие HTML-тегов для футера
    echo "<footer>Footer Content</footer>";

    // Если параметр $exit установлен в true, завершить выполнение
    if ($exit) {
        exit;  // Завершаем выполнение скрипта
    }
}
