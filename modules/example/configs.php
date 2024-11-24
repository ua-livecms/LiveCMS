<?php
// Масив обов'язкових ключів для кожного розділу
$requiredKeysGlobal = ['LIVECMS_VERSION', 'LIVECMS_NAME', 'LIVECMS_TYPE', 'LIVECMS_UPDATE'];
$requiredKeysDebug = ['DEBUGGING', 'INTERPRETATOR'];

// Перевірка обов'язкових ключів для розділу 'GLOBAL'
foreach ($requiredKeysGlobal as $key) {
    if (!isset($configs['GLOBAL'][$key])) {
        // Якщо ключ не існує в розділі 'GLOBAL', виводимо помилку
        echo "Помилка: Відсутній обов'язковий ключ конфігурації в GLOBAL: <strong>$key</strong><br>";
    } else {
        // Якщо ключ існує, виводимо його значення
        echo "Ключ <strong>'$key'</strong> знайдено в GLOBAL, і його значення: <strong>" . htmlspecialchars($configs['GLOBAL'][$key]) . "</strong><br>";
    }
}

// Перевірка обов'язкових ключів для розділу 'DEBUG'
foreach ($requiredKeysDebug as $key) {
    if (!isset($configs['DEBUG'][$key])) {
        // Якщо ключ не існує в розділі 'DEBUG', виводимо помилку
        echo "Помилка: Відсутній обов'язковий ключ конфігурації в DEBUG: <strong>$key</strong><br>";
    } else {
        // Якщо ключ існує, виводимо його значення
        echo "Ключ <strong>'$key'</strong> знайдено в DEBUG, і його значення: <strong>" . htmlspecialchars($configs['DEBUG'][$key]) . "</strong><br>";
    }
}

// Виведення конфігураційних даних для кожного розділу
echo '<h3>Конфігурація GLOBAL:</h3>';
foreach ($configs['GLOBAL'] as $key => $data) {
    echo '<pre>' . htmlspecialchars($key) . ' => ' . htmlspecialchars($data) . '</pre>';
}

echo '<h3>Конфігурація DEBUG:</h3>';
foreach ($configs['DEBUG'] as $key => $data) {
    echo '<pre>' . htmlspecialchars($key) . ' => ' . htmlspecialchars($data) . '</pre>';
}
