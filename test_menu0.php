<?php

require 'vendor/autoload.php';

use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Facebook\WebDriver\WebDriverWait;

// НАСТРОЙКИ
// Путь к драйверу (если он не в PATH, укажите полный путь, например 'C:/drivers/chromedriver.exe')
// Если драйвер в PATH или в той же папке, можно оставить пустым или имя файла.
$driverPath = './chromedriver.exe'; // Для Windows
// $driverPath = './chromedriver'; // Для Linux/Mac

// URL вашей страницы
$baseUrl = 'http://localhost/index.html'; 

echo "Запуск теста Selenium...\n";

try {
    // 1. Инициализация драйвера (Chrome)
    $host = 'http://localhost:49654/wd/hub'; // Стандартный адрес, если не используем ServiceBuilder
    // Но для простого запуска без сервера Selenium Standalone используем ChromeDriver напрямую:
    
    $capabilities = DesiredCapabilities::chrome();
    
    // Опции для Chrome (чтобы не открывалось лишнее окно, если нужно, но для наглядности оставим открытым)
    $options = new \Facebook\WebDriver\Chrome\ChromeOptions();
    // $options->addArguments(['--headless']); // Раскомментируйте, если хотите запуск без интерфейса браузера
    
    $capabilities->setCapability(\Facebook\WebDriver\Chrome\ChromeOptions::CAPABILITY, $options);

    // Запуск драйвера (предполагается, что chromedriver запущен или найден в PATH)
    // Примечание: Для работы без отдельного сервера Selenium лучше использовать ServiceBuilder,
    // но для простого примера используем стандартный RemoteWebDriver, указав путь к бинарнику.
    
    // Простой способ запуска локального драйвера:
    $driver = RemoteWebDriver::create($host, $capabilities); 
    // Если у вас не запущен сервер selenium-standalone, код выше упадет.
    // ДЛЯ ЗАПУСКА БЕЗ СЕРВЕРА (Direct connection) нужен такой код:
    
    /* 
       Чтобы код работал "из коробки" без установки Java и Selenium Server,
       нужно использовать библиотеку, которая сама управляет процессом драйвера.
       Однако стандартный пакет php-webdriver требует запущенный драйвер.
       
       Самый простой способ "без докера":
       1. Откройте терминал и запустите: ./chromedriver (или chromedriver.exe)
       2. Затем запустите этот PHP скрипт.
    */

    echo "Браузер открыт. Переход на страницу: $baseUrl\n";
    $driver->get($baseUrl);

    // Ждем загрузки страницы (максимум 10 секунд)
    echo "Ожидание загрузки страницы...\n";
    $driver->wait(10)->until(
        WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::tagName('h1'))
    );

    // ПРОВЕРКА 1: Наличие пунктов меню
    echo "Проверка наличия ссылок...\n";
    
    $linkAbout = $driver->findElement(WebDriverBy::id('menu-about'));
    $linkActions = $driver->findElement(WebDriverBy::id('menu-promo'));

    if ($linkAbout->isDisplayed() && $linkActions->isDisplayed()) {
        echo "[OK] Пункты меню найдены и видимы.\n";
    } else {
        throw new Exception("Пункты меню не найдены!");
    }

    // ПРОВЕРКА 2: Текст ссылок
    $textAbout = $linkAbout->getText();
    $textActions = $linkActions->getText();
    echo $textAbout . PHP_EOL;
    echo $textActions . PHP_EOL;

    if ($textAbout === 'О компании' && $textActions === 'Акции') {
        echo "[OK] Тексты ссылок верны: '$textAbout', '$textActions'.\n";
    } else {
        throw new Exception("Тексты ссылок не совпадают!");
    }

    // ПРОВЕРКА 3: Клик по ссылке "О компании"
    echo "Клик по ссылке 'О компании'...\n";
    $linkAbout->click();
    
    // Проверяем, что мы все еще на странице с Title "Акции" (так как ссылка ведет туда же)
    $currentTitle = $driver->getTitle();
    if ($currentTitle === 'Акции') {
        echo "[OK] После клика 'О компании' заголовок страницы: '$currentTitle'.\n";
    } else {
        throw new Exception("Заголовок страницы изменился неправильно! Ожидалось 'Акции', получено '$currentTitle'");
    }

    // ПРОВЕРКА 4: Клик по ссылке "Акции"
    echo "Клик по ссылке 'Акции'...\n";
    // Перезагружаем элемент, так как DOM мог обновиться (хотя в простом HTML это не обязательно, но это good practice)
    $linkActions = $driver->findElement(WebDriverBy::id('menu-actions'));
    $linkActions->click();

    $currentUrl = $driver->getCurrentURL();
    if (strpos($currentUrl, 'index.php') !== false) {
        echo "[OK] Ссылка 'Акции' ведет на правильный URL.\n";
    }

    echo "\n=== ВСЕ ТЕСТЫ ПРОЙДЕНУ УСПЕШНО ===\n";

} catch (Exception $e) {
    echo "\n=== ТЕСТ ПРОВАЛЕН ===\n";
    echo "Ошибка: " . $e->getMessage() . "\n";
} finally {
    // Закрываем браузер
    if (isset($driver)) {
        $driver->quit();
        echo "Браузер закрыт.\n";
    }
}