<?php

require_once 'vendor/autoload.php';

use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;

// ================= НАСТРОЙКИ =================
$baseUrl = 'http://localhost/index.html'; // ИЗМЕНИТЕ НА СВОЙ URL
$expectedPromoTitle = 'Акции';
$expectedPromoUrl = 'promo.html';
// =============================================

$driver = null;
$passed = 0;
$failed = 0;

function test($name, $condition, $message = '') {
    global $passed, $failed;
    if ($condition) {
        echo "[✓ PASS] $name\n";
        $passed++;
        return true;
    } else {
        echo "[✗ FAIL] $name";
        if ($message) echo " — $message";
        echo "\n";
        $failed++;
        return false;
    }
}

echo "=== Запуск Selenium-теста ===\n\n";

try {
    // 1. Инициализация ChromeDriver
    $capabilities = DesiredCapabilities::chrome();
    $options = new \Facebook\WebDriver\Chrome\ChromeOptions();
    // $options->addArguments(['--headless']); // Раскомментируйте для скрытого режима
    $capabilities->setCapability(\Facebook\WebDriver\Chrome\ChromeOptions::CAPABILITY, $options);

    // Подключение к ChromeDriver (запустите chromedriver.exe отдельно!)
    echo "Подключение к ChromeDriver...\n";
    $driver = RemoteWebDriver::create('http://localhost:49654/wd/hub', $capabilities);
    
    // 2. Открытие главной страницы
    echo "Открываем: $baseUrl\n";
    $driver->get($baseUrl);
    
    // Простая пауза вместо сложных ожиданий (избегаем бага с titleContains)
    usleep(1000000); // 1 секунда

    // 3. Проверка заголовка index.html
    $title = $driver->getTitle();
var_dump($title);
exit();
    test("Заголовок главной страницы", $title === 'Главная', "Ожидалось 'Главная', получено '$title'");

    // 4. Проверка наличия пунктов меню
    $linkAbout = $driver->findElement(WebDriverBy::id('menu-about'));
    $linkPromo = $driver->findElement(WebDriverBy::id('menu-promo'));
    
    test("Ссылка 'О компании' существует", $linkAbout->isDisplayed());
    test("Ссылка 'Акции' существует", $linkPromo->isDisplayed());

    // 5. Проверка текстов ссылок
    //test("Текст 'О компании'", $linkAbout->getText() === 'О компании');
    //test("Текст 'Акции'", $linkPromo->getText() === 'Акции');

    // 6. Проверка href атрибутов (обе ссылки должны вести на promo.html)
    $hrefAbout = $linkAbout->getAttribute('href');
    $hrefPromo = $linkPromo->getAttribute('href');
    
    test("Ссылка 'О компании' ведет на promo.html", 
         strpos($hrefAbout, 'promo.html') !== false, 
         "href='$hrefAbout'");
    test("Ссылка 'Акции' ведет на promo.html", 
         strpos($hrefPromo, 'promo.html') !== false, 
         "href='$hrefPromo'");

    // 7. Клик по "О компании" и проверка перехода
    echo "\nКлик по 'О компании'...\n";
    $linkAbout->click();
    usleep(800000); // 0.8 сек для загрузки
    
    $newTitle = $driver->getTitle();
    $newUrl = $driver->getCurrentURL();
    
    test("Заголовок после перехода = 'Акции'", 
         $newTitle === $expectedPromoTitle, 
         "Получено: '$newTitle'");
    test("URL содержит promo.html после перехода", 
         strpos($newUrl, $expectedPromoUrl) !== false, 
         "URL: $newUrl");

    // 8. Возврат на главную и клик по "Акции"
    echo "Возврат и клик по 'Акции'...\n";
    $driver->get($baseUrl);
    usleep(500000);
    
    $linkPromo = $driver->findElement(WebDriverBy::id('menu-promo'));
    $linkPromo->click();
    usleep(800000);
    
    $finalTitle = $driver->getTitle();
    $finalUrl = $driver->getCurrentURL();
    
    test("Заголовок после клика 'Акции' = 'Акции'", 
         $finalTitle === $expectedPromoTitle);
    test("URL содержит promo.html после клика 'Акции'", 
         strpos($finalUrl, $expectedPromoUrl) !== false);

    // Итоги
    echo "\n" . str_repeat("=", 40) . "\n";
    echo "РЕЗУЛЬТАТЫ: ✓ $passed пройдено, ✗ $failed провалено\n";
    
    if ($failed === 0) {
        echo "🎉 ВСЕ ТЕСТЫ ПРОЙДЕНЫ УСПЕШНО!\n";
    } else {
        echo "⚠️  Есть ошибки — проверьте логи выше\n";
    }
    echo str_repeat("=", 40) . "\n";

} catch (Exception $e) {
    echo "\n❌ КРИТИЧЕСКАЯ ОШИБКА:\n";
    echo $e->getMessage() . "\n";
    echo "Файл: " . $e->getFile() . ":" . $e->getLine() . "\n";
} finally {
    if ($driver) {
        $driver->quit();
        echo "\nБраузер закрыт.\n";
    }
}