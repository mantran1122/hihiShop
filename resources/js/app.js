import './bootstrap';

import Alpine from 'alpinejs';
import NProgress from 'nprogress';
import 'nprogress/nprogress.css';

window.Alpine = Alpine;
window.NProgress = NProgress;

// Thanh tiến trình khi chuyển trang / submit form (thư viện NProgress)
NProgress.configure({
    showSpinner: false,
    trickleSpeed: 120,
    minimum: 0.15,
});

Alpine.start();

/* --------------------------------------------------------------------------
 * Phản hồi khi chuyển trang: thanh tiến trình + trạng thái nút submit
 * ------------------------------------------------------------------------ */
const isSameOrigin = (url) => {
    try {
        return new URL(url, window.location.href).origin === window.location.origin;
    } catch {
        return false;
    }
};

const shouldTrackLink = (link, event) => {
    if (!link || event.defaultPrevented) return false;
    if (event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) return false;
    if (link.target === '_blank' || link.hasAttribute('download')) return false;
    if (link.dataset.noProgress !== undefined) return false;

    const href = link.getAttribute('href');

    if (!href || href.startsWith('#') || href.startsWith('mailto:') || href.startsWith('tel:') || href.startsWith('javascript:')) {
        return false;
    }

    return isSameOrigin(link.href);
};

// Bắt ở pha bubble để không cản các handler confirm()/preventDefault() của form
document.addEventListener('click', (event) => {
    const link = event.target.closest('a');

    if (shouldTrackLink(link, event)) {
        NProgress.start();
    }
});

document.addEventListener('submit', (event) => {
    if (event.defaultPrevented) return;

    const form = event.target;

    if (!(form instanceof HTMLFormElement) || form.dataset.noProgress !== undefined) return;

    NProgress.start();

    // Nút submit hiện spinner + tạm khoá để tránh bấm 2 lần
    form.querySelectorAll('button[type="submit"]').forEach((button) => {
        if (button.disabled) return;

        button.dataset.originalHtml = button.innerHTML;
        button.disabled = true;
        button.classList.add('opacity-70', 'pointer-events-none');
        button.innerHTML = '<span class="inline-flex items-center justify-center gap-2"><span class="w-3.5 h-3.5 rounded-full border-2 border-white/40 border-t-white animate-spin"></span> Đang xử lý...</span>';
    });
});

window.addEventListener('pageshow', () => NProgress.done());
window.addEventListener('load', () => NProgress.done());
