<?php
namespace App\Services;

class ValidateOrderData
{
    private array $data;
    private array $errors = [];

    /**
     * Запускает валидацию всех полей
     * @return bool
     */
    public function validate(array $data): bool
    {
        $this->data = $data;
        $this->errors = [];

        $this->validateFio();
        $this->validateAddress();
        $this->validatePhone();
        $this->validateEmail();

        return empty($this->errors);
    }

    /**
     * Валидация ФИО
     */
    private function validateFio(): void
    {
        $field = 'fio';
        $value = $this->getValue($field);

        if (empty($value)) {
            $this->addError($field, 'ФИО обязательно для заполнения');
            return;
        }

        // Разрешаем кириллицу, латиницу, пробелы, дефисы и точки
        if (!preg_match('/^[\p{Cyrillic}\p{Latin}\s\.\-]+$/u', trim($value))) {
            $this->addError($field, 'ФИО содержит недопустимые символы');
            return;
        }

        // Минимальная длина - 2 символа, максимальная - 100
        $length = mb_strlen(trim($value), 'UTF-8');
        if ($length < 2 || $length > 100) {
            $this->addError($field, 'ФИО должно быть от 2 до 100 символов');
        }
    }

    /**
     * Валидация адреса
     */
    private function validateAddress(): void
    {
        $field = 'address';
        $value = $this->getValue($field);

        if (empty($value)) {
            $this->addError($field, 'Адрес обязательно для заполнения');
            return;
        }

        $length = mb_strlen(trim($value), 'UTF-8');
        if ($length < 10 || $length > 255) {
            $this->addError($field, 'Адрес должен быть от 10 до 255 символов');
            return;
        }

        // Базовая проверка на наличие города и улицы (опционально)
        if (!preg_match('/[а-яА-ЯёЁ]{2,}.*[уулУул\.]{0,3}/u', $value)) {
            // Это предупреждение, а не ошибка - можно закомментировать при необходимости
            // $this->addError($field, 'Адрес выглядит неполным');
        }
    }

    /**
     * Валидация телефона
     */
    private function validatePhone(): void
    {
        $field = 'phone';
        $value = $this->getValue($field);

        if (empty($value)) {
            $this->addError($field, 'Телефон обязательно для заполнения');
            return;
        }

        // Очищаем телефон от лишних символов
        $cleanPhone = preg_replace('/[^\d+]/', '', $value);

        // Проверка российских номеров: +7 или 8, затем 10 цифр
        if (!preg_match('/^(\+7|8)\d{10}$/', $cleanPhone)) {
            $this->addError($field, 'Неверный формат телефона. Пример: +79007009911 или 89007009911');
            return;
        }
    }

    /**
     * Валидация email
     */
    private function validateEmail(): void
    {
        $field = 'email';
        $value = $this->getValue($field);

        if (empty($value)) {
            $this->addError($field, 'Email обязательно для заполнения');
            return;
        }

        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->addError($field, 'Неверный формат email');
            return;
        }

        // Дополнительная проверка длины
        if (mb_strlen($value) > 254) {
            $this->addError($field, 'Email слишком длинный');
        }
    }

    /**
     * Получает значение поля с предварительной обработкой
     */
    private function getValue(string $field): string
    {
        return isset($this->data[$field]) ? trim((string)$this->data[$field]) : '';
    }

    /**
     * Добавляет ошибку валидации
     */
    private function addError(string $field, string $message): void
    {
        if (!isset($this->errors[$field])) {
            $this->errors[$field] = [];
        }
        $this->errors[$field][] = $message;
    }

    /**
     * Возвращает массив ошибок
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Возвращает первую ошибку для поля
     * @param string $field
     * @return string|null
     */
    public function getError(string $field): ?string
    {
        return $this->errors[$field][0] ?? null;
    }

    /**
     * Возвращает очищенные и валидные данные
     * @return array
     */
    public function getValidatedData(): array
    {
        return [
            'fio' => trim($this->data['fio'] ?? ''),
            'address' => trim($this->data['address'] ?? ''),
            'phone' => preg_replace('/[^\d+]/', '', $this->data['phone'] ?? ''),
            'email' => strtolower(trim($this->data['email'] ?? '')),
        ];
    }

    /**
     * Статический метод для быстрой валидации
     * @param array $data
     * @return array [bool $isValid, array $errors]
     */
    public static function quickValidate(array $data): array
    {
        $validator = new self($data);
        $isValid = $validator->validate();
        return [$isValid, $validator->getErrors()];
    }
}