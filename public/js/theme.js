(function () {
    var root = document.documentElement;
    var storageKey = 'lms-theme';

    function setTheme(theme) {
        root.setAttribute('data-theme', theme);
        try {
            localStorage.setItem(storageKey, theme);
        } catch (e) {
            // no-op
        }
    }

    function getInitialTheme() {
        try {
            var saved = localStorage.getItem(storageKey);
            if (saved === 'light' || saved === 'dark') {
                return saved;
            }
        } catch (e) {
            // no-op
        }

        return 'light';
    }

    var currentTheme = getInitialTheme();
    setTheme(currentTheme);

    document.addEventListener('DOMContentLoaded', function () {
        var toggle = document.getElementById('themeToggle');
        if (!toggle) {
            return;
        }

        toggle.addEventListener('click', function () {
            var next = root.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
            setTheme(next);
        });
    });
})();
