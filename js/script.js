document.addEventListener('DOMContentLoaded', function () {
    var forms = document.querySelectorAll('form[data-confirm]');

    forms.forEach(function (form) {
        form.addEventListener('submit', function (event) {
            var message = form.getAttribute('data-confirm') || 'Are you sure?';

            if (!window.confirm(message)) {
                event.preventDefault();
            }
        });
    });
});
