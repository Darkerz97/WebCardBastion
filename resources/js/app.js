import './bootstrap';

const restoreInteractionLayer = () => {
    document.body.style.pointerEvents = 'auto';
    document.body.style.opacity = '1';
    document.body.style.filter = 'none';
    document.body.style.overflow = '';
    document.documentElement.style.overflow = '';
    document.documentElement.style.opacity = '1';
    document.documentElement.style.filter = 'none';

    document.querySelectorAll('dialog[open]').forEach((dialog) => {
        if (typeof dialog.close === 'function') {
            dialog.close();
        } else {
            dialog.removeAttribute('open');
        }
    });

    document.querySelectorAll('[popover]').forEach((element) => {
        if (typeof element.hidePopover === 'function') {
            try {
                element.hidePopover();
            } catch {
                element.removeAttribute('popover');
            }
        }
    });

    document.querySelectorAll('[inert]').forEach((element) => {
        element.removeAttribute('inert');
    });

    document.querySelectorAll('[aria-hidden="true"]').forEach((element) => {
        if (element.matches('[data-auth-page], [data-auth-form], main, body')) {
            element.removeAttribute('aria-hidden');
        }
    });
};

const setupAuthForms = () => {
    document.querySelectorAll('form[data-auth-form="true"]').forEach((form) => {
        if (form.dataset.authBound === 'true') {
            return;
        }

        form.dataset.authBound = 'true';

        form.addEventListener('submit', () => {
            const submitButton = form.querySelector('button[type="submit"]');

            if (!(submitButton instanceof HTMLButtonElement)) {
                return;
            }

            submitButton.disabled = true;
            submitButton.dataset.originalText = submitButton.textContent ?? '';
            submitButton.textContent = 'Procesando...';
            submitButton.classList.add('is-submitting');
        });
    });
};

document.addEventListener('DOMContentLoaded', restoreInteractionLayer);
document.addEventListener('DOMContentLoaded', setupAuthForms);
window.addEventListener('pageshow', restoreInteractionLayer);
window.addEventListener('pageshow', setupAuthForms);
