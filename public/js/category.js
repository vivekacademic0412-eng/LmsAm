document.addEventListener("DOMContentLoaded", function () {

    function openModal(id) {
        const modal = document.getElementById(id);
        if (!modal) return;
        modal.classList.add("open");
        modal.setAttribute("aria-hidden", "false");
    }

    function closeModal(id) {
        const modal = document.getElementById(id);
        if (!modal) return;
        modal.classList.remove("open");
        modal.setAttribute("aria-hidden", "true");
    }

    document.addEventListener("click", function (e) {

        const openBtn = e.target.closest("[data-modal-open]");
        if (openBtn) {
            const id = openBtn.getAttribute("data-modal-open");
            openModal(id);
            return;
        }

        const closeBtn = e.target.closest("[data-modal-close]");
        if (closeBtn) {
            const id = closeBtn.getAttribute("data-modal-close");
            closeModal(id);
            return;
        }

        const overlay = e.target.classList.contains("modal-overlay") ? e.target : null;
        if (overlay && overlay.classList.contains("open")) {
            overlay.classList.remove("open");
            overlay.setAttribute("aria-hidden", "true");
        }

    });

    document.addEventListener("keydown", function (e) {
        if (e.key === "Escape") {
            const openModal = document.querySelector(".modal-overlay.open");
            if (openModal) {
                openModal.classList.remove("open");
                openModal.setAttribute("aria-hidden", "true");
            }
        }
    });

});