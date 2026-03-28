import { pptxToHtml } from './pptx-to-html/index.js';

function getViewportSize(root) {
    const styles = window.getComputedStyle(root);
    const paddingLeft = parseFloat(styles.paddingLeft || '0');
    const paddingRight = parseFloat(styles.paddingRight || '0');
    const availableWidth = Math.max(320, Math.floor(root.clientWidth - paddingLeft - paddingRight - 4));
    const width = Math.max(320, Math.min(availableWidth, 1120));

    return {
        width: width,
        height: Math.round(width * 9 / 16)
    };
}

async function renderPptx(root) {
    const streamUrl = root.getAttribute('data-pptx-stream');
    if (!streamUrl) {
        return;
    }

    const status = root.querySelector('[data-pptx-status]');
    if (status) {
        status.textContent = 'Loading PPTX slides...';
    }

    const controller = new AbortController();
    const timeoutId = window.setTimeout(() => controller.abort(), 20000);

    const response = await fetch(streamUrl, {
        credentials: 'same-origin',
        signal: controller.signal,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    });
    window.clearTimeout(timeoutId);

    if (!response.ok) {
        throw new Error('Unable to load PPTX file.');
    }

    const arrayBuffer = await response.arrayBuffer();
    const viewport = getViewportSize(root);
    const slidesHtml = await pptxToHtml(arrayBuffer, {
        width: viewport.width,
        height: viewport.height,
        scaleToFit: true,
        letterbox: true
    });

    root.classList.add('is-ready');
    root.innerHTML = slidesHtml.map((slideHtml, index) => {
        return `
            <article class="pptx-renderer__slide-card">
                <div class="pptx-renderer__slide-meta">Slide ${index + 1}</div>
                <div class="pptx-renderer__slide-stage">${slideHtml}</div>
            </article>
        `;
    }).join('');
}

function boot() {
    const roots = document.querySelectorAll('[data-pptx-stream]');
    if (!roots.length) {
        return;
    }

    roots.forEach((root) => {
        renderPptx(root).catch(() => {
            root.classList.add('is-error');
            root.innerHTML = '<div class="pptx-renderer__status">PPTX preview could not be loaded inside the project. Upload the file again as `.pptx` or convert it to PDF for a stable in-project preview.</div>';
        });
    });
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', boot, { once: true });
} else {
    boot();
}
