document.addEventListener('DOMContentLoaded', () => {

    /* ---------- Theme Toggle ---------- */
    const html = document.documentElement;
    const themeBtn = document.getElementById('themeBtn');

    const savedTheme = localStorage.getItem('am_theme') || 'light';
    html.setAttribute('data-theme', savedTheme);

    if (themeBtn) {
        themeBtn.addEventListener('click', () => {
            const next = html.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
            html.setAttribute('data-theme', next);
            localStorage.setItem('am_theme', next);
        });
    }

    /* ---------- Password Toggle ---------- */
    const passInput = document.getElementById('password');
    const eyeBtn    = document.getElementById('eyeBtn');
    const eyeOpen   = document.getElementById('eyeOpen');
    const eyeClose  = document.getElementById('eyeClosed');

    if (eyeBtn && passInput) {
        eyeBtn.addEventListener('click', () => {
            const visible = passInput.type === 'password';
            passInput.type = visible ? 'text' : 'password';
            if (eyeOpen)  eyeOpen.style.display  = visible ? 'none'  : 'block';
            if (eyeClose) eyeClose.style.display = visible ? 'block' : 'none';
            eyeBtn.setAttribute('aria-label', visible ? 'Hide password' : 'Show password');
            passInput.focus();
        });
    }

    /* ---------- Login Submit ---------- */
    const form = document.getElementById('loginForm');

    if (form) {
        form.addEventListener('submit', async function (e) {
            e.preventDefault();

            const email    = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value.trim();
            const btn      = document.getElementById('submitBtn');
            const btnHtml  = btn.innerHTML;

            /* Client-side validation */
            if (!email || !password) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Fields Required',
                    text: 'Please enter your email and password to continue.',
                    confirmButtonColor: '#0d5dd1',
                });
                return;
            }

            try {
                btn.disabled  = true;
                btn.innerHTML = '<span style="opacity:0.8">Signing in…</span>';

                const response = await fetch(form.dataset.url, {
                    method: 'POST',
                    headers: {
                        'Accept'       : 'application/json',
                        'X-CSRF-TOKEN' : document.querySelector('meta[name="csrf-token"]').content,
                    },
                    credentials: 'same-origin',
                    body: new FormData(form),
                });

                /* ✅ Parse JSON ONCE — this was the bug */
                const data = await response.json();

                if (response.ok) {
                    await Swal.fire({
                        icon             : 'success',
                        title            : 'Welcome back!',
                        text             : data.message || 'Login successful. Redirecting…',
                        timer            : 1500,
                        timerProgressBar : true,
                        showConfirmButton : false,
                        confirmButtonColor: '#0d5dd1',
                    });

                    window.location.href = data.redirect || '/dashboard';

                } else {
                    /* 422 validation / 401 credentials / 429 rate-limit */
                    const message = data.errors?.email?.[0]
                        ?? data.message
                        ?? 'Invalid credentials. Please try again.';

                    Swal.fire({
                        icon : response.status === 429 ? 'warning' : 'error',
                        title: response.status === 429 ? 'Too Many Attempts' : 'Login Failed',
                        text : message,
                        confirmButtonColor: '#0d5dd1',
                    });
                }

            } catch (err) {
                console.error('[Login]', err);

                Swal.fire({
                    icon : 'error',
                    title: 'Server Error',
                    text : 'Something went wrong. Please try again.',
                    confirmButtonColor: '#0d5dd1',
                });

            } finally {
                btn.disabled  = false;
                btn.innerHTML = btnHtml;
            }
        });
    }

});