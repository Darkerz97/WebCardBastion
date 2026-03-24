import './bootstrap';

const restoreInteractionLayer = () => {
    document.body.style.pointerEvents = 'auto';
    document.body.style.overflow = '';
    document.documentElement.style.overflow = '';

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
};

document.addEventListener('DOMContentLoaded', restoreInteractionLayer);
window.addEventListener('pageshow', restoreInteractionLayer);
