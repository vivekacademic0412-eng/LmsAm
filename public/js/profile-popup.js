(function () {
    document.addEventListener('DOMContentLoaded', function () {
        var toggle = document.getElementById('profileToggle');
        var popup = document.getElementById('profilePopup');

        if (!toggle || !popup) {
            return;
        }

        toggle.addEventListener('click', function (event) {
            event.stopPropagation();
            popup.classList.toggle('open');
        });

        popup.addEventListener('click', function (event) {
            event.stopPropagation();
        });

        document.addEventListener('click', function () {
            popup.classList.remove('open');
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                popup.classList.remove('open');
            }
        });
    });
})();
