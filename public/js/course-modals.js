(function () {
    function openModal(id) {
        var modal = document.getElementById(id);
        if (modal) {
            modal.classList.add('open');
        }
    }

    function closeModal(id) {
        var modal = document.getElementById(id);
        if (modal) {
            modal.classList.remove('open');
        }
    }

    document.addEventListener('click', function (event) {
        var openBtn = event.target.closest('[data-modal-open]');
        if (openBtn) {
            event.preventDefault();
            openModal(openBtn.getAttribute('data-modal-open'));
            return;
        }

        var closeBtn = event.target.closest('[data-modal-close]');
        if (closeBtn) {
            event.preventDefault();
            closeModal(closeBtn.getAttribute('data-modal-close'));
            return;
        }

        var overlay = event.target.classList.contains('modal-overlay') ? event.target : null;
        if (overlay && overlay.id) {
            closeModal(overlay.id);
        }
    });

    document.addEventListener('keydown', function (event) {
        if (event.key !== 'Escape') {
            return;
        }
        document.querySelectorAll('.modal-overlay.open').forEach(function (modal) {
            modal.classList.remove('open');
        });
    });
})();
