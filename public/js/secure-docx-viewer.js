(function () {
    function renderWithMammoth(root, arrayBuffer) {
        if (typeof mammoth === 'undefined' || !mammoth.convertToHtml) {
            return Promise.reject(new Error('Mammoth is unavailable.'));
        }

        return mammoth.convertToHtml({ arrayBuffer: arrayBuffer })
            .then(function (result) {
                root.classList.add('is-ready', 'is-mammoth');
                root.innerHTML = '<div class="docx-renderer__body">' + (result.value || '<p>No document content found.</p>') + '</div>';
            });
    }

    function renderWithDocxPreview(root, arrayBuffer) {
        if (typeof docx === 'undefined' || !docx.renderAsync) {
            return Promise.reject(new Error('docx-preview is unavailable.'));
        }

        root.innerHTML = '';
        root.classList.add('is-rendering');

        return docx.renderAsync(arrayBuffer, root, root, {
            className: 'docx-viewer',
            inWrapper: true,
            hideWrapperOnPrint: false,
            ignoreWidth: false,
            ignoreHeight: false,
            ignoreFonts: false,
            breakPages: true,
            ignoreLastRenderedPageBreak: false,
            useBase64URL: true,
            renderHeaders: true,
            renderFooters: true,
            renderFootnotes: true,
            renderEndnotes: true
        }).then(function () {
            root.classList.add('is-ready');
            root.classList.remove('is-rendering');
        });
    }

    function renderDocx(root) {
        var streamUrl = root.getAttribute('data-docx-stream');
        if (!streamUrl) {
            return;
        }

        var status = root.querySelector('[data-docx-status]');
        if (status) {
            status.textContent = 'Loading DOCX preview...';
        }

        var hasDocxPreview = typeof docx !== 'undefined' && !!docx.renderAsync;
        var hasMammoth = typeof mammoth !== 'undefined' && !!mammoth.convertToHtml;

        if (!hasDocxPreview && !hasMammoth) {
            if (status) {
                status.textContent = 'DOCX preview library is unavailable.';
            }
            return;
        }

        fetch(streamUrl, {
            credentials: 'same-origin',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
            .then(function (response) {
                if (!response.ok) {
                    throw new Error('Unable to load DOCX file.');
                }

                return response.arrayBuffer();
            })
            .then(function (arrayBuffer) {
                return renderWithDocxPreview(root, arrayBuffer).catch(function () {
                    return renderWithMammoth(root, arrayBuffer);
                });
            })
            .catch(function () {
                root.classList.remove('is-rendering');
                root.classList.add('is-error');
                root.innerHTML = '<div class="docx-renderer__status">DOCX preview could not be loaded here. Use the Google Docs or original file button.</div>';
            });
    }

    function boot() {
        var roots = document.querySelectorAll('[data-docx-stream]');
        if (!roots.length) {
            return;
        }

        roots.forEach(renderDocx);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', boot, { once: true });
        return;
    }

    boot();
})();
