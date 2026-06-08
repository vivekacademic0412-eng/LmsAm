(function () {
    /* helpers */
    function $(id) { return document.getElementById(id); }

    var notifToggle  = $('notificationToggle');
    var notifPopup   = $('notificationPopup');
    var profileToggle = $('profileToggle');
    var profilePopup = $('profilePopup');
    var themeBtn     = $('themeBtn');
    var themeBtnIcon = $('themeBtnIcon');

    /* open / close popups */
    function openPopup(popup, trigger) {
        popup.classList.add('open');
        trigger.setAttribute('aria-expanded', 'true');
    }

    function closePopup(popup, trigger) {
        popup.classList.remove('open');
        trigger.setAttribute('aria-expanded', 'false');
    }

    function togglePopup(popup, trigger, other, otherTrigger) {
        var isOpen = popup.classList.contains('open');
        /* close sibling first */
        if (other && other.classList.contains('open')) {
            closePopup(other, otherTrigger);
        }
        if (isOpen) {
            closePopup(popup, trigger);
        } else {
            openPopup(popup, trigger);
        }
    }

    notifToggle.addEventListener('click', function (e) {
        e.stopPropagation();
        togglePopup(notifPopup, notifToggle, profilePopup, profileToggle);
    });

    profileToggle.addEventListener('click', function (e) {
        e.stopPropagation();
        togglePopup(profilePopup, profileToggle, notifPopup, notifToggle);
    });

    /* close on outside click */
    document.addEventListener('click', function (e) {
        if (!notifPopup.contains(e.target) && !notifToggle.contains(e.target)) {
            closePopup(notifPopup, notifToggle);
        }
        if (!profilePopup.contains(e.target) && !profileToggle.contains(e.target)) {
            closePopup(profilePopup, profileToggle);
        }
    });

    /* close on Escape */
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            closePopup(notifPopup, notifToggle);
            closePopup(profilePopup, profileToggle);
        }
    });

    /* Theme toggle */
   // Theme toggle (FIXED)
document.addEventListener("DOMContentLoaded", function () {

    const htmlEl = document.documentElement;
    const themeBtn = document.getElementById("themeBtn");
    const themeBtnIcon = document.getElementById("themeBtnIcon");

    // load saved theme
    const saved = localStorage.getItem("lms-theme") || "dark";
    applyTheme(saved);

    themeBtn.addEventListener("click", function () {
        const current = htmlEl.getAttribute("data-theme") || "dark";
        const next = current === "dark" ? "light" : "dark";

        applyTheme(next);
        localStorage.setItem("lms-theme", next);
    });

    function applyTheme(theme) {
        htmlEl.setAttribute("data-theme", theme);

        if (theme === "dark") {
            themeBtnIcon.className = "fa-solid fa-sun";
            themeBtn.title = "Switch to light mode";
        } else {
            themeBtnIcon.className = "fa-solid fa-moon";
            themeBtn.title = "Switch to dark mode";
        }
    }
});
    /* Search keyboard shortcut: Ctrl/Cmd + K */
    document.addEventListener('keydown', function (e) {
        if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
            e.preventDefault();
            var inp = document.querySelector('.search-bar input');
            if (inp) inp.focus();
        }
    });
})();