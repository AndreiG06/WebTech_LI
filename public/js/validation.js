/**
 * Проверка форм на стороне клиента.
 *
 * Используются встроенные HTML5-правила (required, minlength, min, max, pattern),
 * а этот скрипт:
 *  - не даёт отправить форму с ошибками и подсвечивает неверные поля (стили Bootstrap);
 *  - проверяет, что «Повторите пароль» совпадает с паролем (атрибут data-match).
 *
 * Даже если JavaScript отключён, все данные всё равно проверяются на сервере.
 */
document.addEventListener('DOMContentLoaded', function () {
    var forms = document.querySelectorAll('.needs-validation');

    forms.forEach(function (form) {
        // Проверка совпадения паролей
        var confirmFields = form.querySelectorAll('[data-match]');
        confirmFields.forEach(function (confirmField) {
            var original = form.querySelector('#' + confirmField.dataset.match);

            function checkMatch() {
                if (confirmField.value !== original.value) {
                    confirmField.setCustomValidity('Пароли не совпадают');
                } else {
                    confirmField.setCustomValidity('');
                }
            }

            confirmField.addEventListener('input', checkMatch);
            original.addEventListener('input', checkMatch);
        });

        // Когда пользователь исправляет поле, убираем серверную подсветку ошибки
        form.querySelectorAll('.is-invalid').forEach(function (field) {
            field.addEventListener('input', function () {
                field.classList.remove('is-invalid');
            });
        });

        form.addEventListener('submit', function (event) {
            confirmFields.forEach(function (field) {
                field.dispatchEvent(new Event('input'));
            });

            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        });
    });
});
