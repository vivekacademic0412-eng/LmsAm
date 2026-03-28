document.addEventListener('DOMContentLoaded', function () {
    var toggles = document.querySelectorAll('[data-filter-toggle]');
    toggles.forEach(function (btn) {
        btn.addEventListener('click', function () {
            var id = btn.getAttribute('data-filter-toggle');
            var panel = document.getElementById(id);
            if (!panel) return;
            var isOpen = panel.classList.toggle('open');
            btn.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
            panel.setAttribute('aria-hidden', isOpen ? 'false' : 'true');
        });
    });

    document.addEventListener('click', function (e) {
        toggles.forEach(function (btn) {
            var id = btn.getAttribute('data-filter-toggle');
            var panel = document.getElementById(id);
            if (!panel) return;
            if (btn.contains(e.target) || panel.contains(e.target)) {
                return;
            }
            panel.classList.remove('open');
            btn.setAttribute('aria-expanded', 'false');
            panel.setAttribute('aria-hidden', 'true');
        });
    });

    document.addEventListener('keydown', function (e) {
        if (e.key !== 'Escape') return;
        toggles.forEach(function (btn) {
            var id = btn.getAttribute('data-filter-toggle');
            var panel = document.getElementById(id);
            if (!panel) return;
            panel.classList.remove('open');
            btn.setAttribute('aria-expanded', 'false');
            panel.setAttribute('aria-hidden', 'true');
        });
    });

    var courseForm = document.getElementById('courseFilterForm');
    var courseCategory = document.getElementById('courseCategoryFilter');
    var courseSubcategory = document.getElementById('courseSubcategoryFilter');

    if (courseForm && courseCategory && courseSubcategory) {
        var subOptions = Array.from(courseSubcategory.querySelectorAll('option[data-parent]'));

        function syncCourseSubcategories() {
            var parentId = courseCategory.value;
            subOptions.forEach(function (opt) {
                opt.hidden = parentId === '' || opt.getAttribute('data-parent') !== parentId;
            });
            courseSubcategory.disabled = parentId === '';
            if (courseSubcategory.value) {
                var selected = courseSubcategory.options[courseSubcategory.selectedIndex];
                if (selected && selected.getAttribute('data-parent') !== parentId) {
                    courseSubcategory.value = '';
                }
            }
        }

        courseCategory.addEventListener('change', function () {
            courseSubcategory.value = '';
            syncCourseSubcategories();
        });

        syncCourseSubcategories();
    }

    var userForm = document.getElementById('userFilterForm');
    var userRole = document.getElementById('userRoleFilter');
    if (userForm && userRole) {
        userRole.addEventListener('change', function () {});
    }

    var enrollmentForm = document.getElementById('enrollmentFilterForm');
    var enrollmentCategory = document.getElementById('enrollmentCategoryFilter');
    var enrollmentSubcategory = document.getElementById('enrollmentSubcategoryFilter');
    var enrollmentTrainer = document.getElementById('enrollmentTrainerFilter');

    if (enrollmentForm && enrollmentCategory && enrollmentSubcategory) {
        var enrollSubOptions = Array.from(enrollmentSubcategory.querySelectorAll('option[data-parent]'));

        function syncEnrollmentSubcategories() {
            var parentId = enrollmentCategory.value;
            enrollSubOptions.forEach(function (opt) {
                opt.hidden = parentId === '' || opt.getAttribute('data-parent') !== parentId;
            });
            enrollmentSubcategory.disabled = parentId === '';
            if (enrollmentSubcategory.value) {
                var selected = enrollmentSubcategory.options[enrollmentSubcategory.selectedIndex];
                if (selected && selected.getAttribute('data-parent') !== parentId) {
                    enrollmentSubcategory.value = '';
                }
            }
        }

        enrollmentCategory.addEventListener('change', function () {
            enrollmentSubcategory.value = '';
            syncEnrollmentSubcategories();
        });

        syncEnrollmentSubcategories();
    }
});
