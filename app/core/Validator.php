<?php

/**
 * Простой валидатор данных из форм.
 *
 * Пример:
 *   $v = new Validator();
 *   $v->required('title', $title, 'Название')->maxLength('title', $title, 200, 'Название');
 *   if ($v->fails()) { $errors = $v->errors(); }
 *
 * Для каждого поля сохраняется только первая найденная ошибка.
 */
class Validator
{
    /** @var array<string, string> Ошибки в виде [имя поля => текст ошибки] */
    private array $errors = [];

    /**
     * Поле обязательно для заполнения.
     *
     * @param string $field Имя поля формы.
     * @param mixed  $value Значение поля.
     * @param string $label Название поля для сообщения об ошибке.
     * @return self
     */
    public function required(string $field, $value, string $label): self
    {
        if (trim((string)$value) === '') {
            $this->addError($field, "Поле «{$label}» обязательно для заполнения.");
        }
        return $this;
    }

    /**
     * Минимальная длина строки.
     *
     * @param string $field Имя поля формы.
     * @param string $value Значение поля.
     * @param int    $min   Минимальное количество символов.
     * @param string $label Название поля.
     * @return self
     */
    public function minLength(string $field, string $value, int $min, string $label): self
    {
        if (mb_strlen($value) < $min) {
            $this->addError($field, "Поле «{$label}» должно содержать не меньше {$min} символов.");
        }
        return $this;
    }

    /**
     * Максимальная длина строки.
     *
     * @param string $field Имя поля формы.
     * @param string $value Значение поля.
     * @param int    $max   Максимальное количество символов.
     * @param string $label Название поля.
     * @return self
     */
    public function maxLength(string $field, string $value, int $max, string $label): self
    {
        if (mb_strlen($value) > $max) {
            $this->addError($field, "Поле «{$label}» должно содержать не больше {$max} символов.");
        }
        return $this;
    }

    /**
     * Корректный адрес электронной почты.
     *
     * @param string $field Имя поля формы.
     * @param string $value Значение поля.
     * @return self
     */
    public function email(string $field, string $value): self
    {
        if (filter_var($value, FILTER_VALIDATE_EMAIL) === false) {
            $this->addError($field, 'Введите корректный адрес электронной почты.');
        }
        return $this;
    }

    /**
     * Целое число в заданном диапазоне.
     *
     * @param string $field Имя поля формы.
     * @param mixed  $value Значение поля.
     * @param int    $min   Минимальное значение.
     * @param int    $max   Максимальное значение.
     * @param string $label Название поля.
     * @return self
     */
    public function integerBetween(string $field, $value, int $min, int $max, string $label): self
    {
        $options = ['options' => ['min_range' => $min, 'max_range' => $max]];
        if (filter_var($value, FILTER_VALIDATE_INT, $options) === false) {
            $this->addError($field, "Поле «{$label}» должно быть целым числом от {$min} до {$max}.");
        }
        return $this;
    }

    /**
     * Значение должно быть одним из разрешённых (для списков и переключателей).
     *
     * @param string $field   Имя поля формы.
     * @param mixed  $value   Значение поля.
     * @param array  $allowed Список допустимых значений.
     * @param string $label   Название поля.
     * @return self
     */
    public function inList(string $field, $value, array $allowed, string $label): self
    {
        if (!in_array((string)$value, array_map('strval', $allowed), true)) {
            $this->addError($field, "Выберите значение в поле «{$label}».");
        }
        return $this;
    }

    /**
     * Строка подходит под регулярное выражение.
     *
     * @param string $field   Имя поля формы.
     * @param string $value   Значение поля.
     * @param string $pattern Регулярное выражение.
     * @param string $message Текст ошибки.
     * @return self
     */
    public function matches(string $field, string $value, string $pattern, string $message): self
    {
        if (!preg_match($pattern, $value)) {
            $this->addError($field, $message);
        }
        return $this;
    }

    /**
     * Два значения должны совпадать (например, пароль и его подтверждение).
     *
     * @param string $field   Имя поля формы.
     * @param string $value   Значение поля.
     * @param string $other   Значение, с которым сравниваем.
     * @param string $message Текст ошибки.
     * @return self
     */
    public function same(string $field, string $value, string $other, string $message): self
    {
        if ($value !== $other) {
            $this->addError($field, $message);
        }
        return $this;
    }

    /**
     * Добавляет ошибку для поля (если у него ещё нет ошибки).
     *
     * @param string $field   Имя поля формы.
     * @param string $message Текст ошибки.
     * @return void
     */
    public function addError(string $field, string $message): void
    {
        if (!isset($this->errors[$field])) {
            $this->errors[$field] = $message;
        }
    }

    /**
     * Есть ли ошибки после проверки.
     *
     * @return bool true, если найдена хотя бы одна ошибка.
     */
    public function fails(): bool
    {
        return !empty($this->errors);
    }

    /**
     * Возвращает все найденные ошибки.
     *
     * @return array<string, string> Ошибки в виде [имя поля => текст].
     */
    public function errors(): array
    {
        return $this->errors;
    }
}
