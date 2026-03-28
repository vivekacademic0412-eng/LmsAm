(function () {
    document.addEventListener('DOMContentLoaded', function () {
        var pairs = [
            {
                toggle: document.getElementById('notificationToggle'),
                popup: document.getElementById('notificationPopup')
            },
            {
                toggle: document.getElementById('profileToggle'),
                popup: document.getElementById('profilePopup')
            }
        ].filter(function (pair) {
            return pair.toggle && pair.popup;
        });

        if (!pairs.length) {
            return;
        }

        function closeAll() {
            pairs.forEach(function (pair) {
                pair.popup.classList.remove('open');
            });
        }

        pairs.forEach(function (pair) {
            pair.toggle.addEventListener('click', function (event) {
                var shouldOpen = !pair.popup.classList.contains('open');

                event.stopPropagation();
                closeAll();

                if (shouldOpen) {
                    pair.popup.classList.add('open');
                }
            });

            pair.popup.addEventListener('click', function (event) {
                event.stopPropagation();
            });
        });

        document.addEventListener('click', function () {
            closeAll();
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                closeAll();
            }
        });
    });
})();
