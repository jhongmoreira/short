const SCHEME = /^[a-z][a-z0-9+.-]*:\/\//i;

/** Normaliza e valida uma URL (mesma regra do backend). */
export function normalizeUrl(raw) {
    const value = raw.trim();
    if (value === '') return { ok: false, message: 'Informe a URL que deseja encurtar.' };

    const withScheme = SCHEME.test(value) ? value : `https://${value}`;

    try {
        const url = new URL(withScheme);
        const validHost = url.hostname === 'localhost' || url.hostname.includes('.');
        if (!['http:', 'https:'].includes(url.protocol) || !validHost || /\s/.test(value)) {
            throw new Error();
        }
        return { ok: true, value: withScheme };
    } catch {
        return { ok: false, message: 'Informe uma URL válida.' };
    }
}

const CODE_PATTERN = /^[a-z0-9]+(?:-[a-z0-9]+)*$/;

export function validateCode(raw) {
    const value = raw.trim().toLowerCase();
    if (value === '') return { ok: true, value };
    if (value.length < 3) return { ok: false, message: 'O código precisa ter pelo menos 3 caracteres.' };
    if (value.length > 50) return { ok: false, message: 'O código pode ter no máximo 50 caracteres.' };
    if (!CODE_PATTERN.test(value)) {
        return { ok: false, message: 'Use apenas letras minúsculas, números e hífens (sem hífen no início ou fim).' };
    }
    return { ok: true, value };
}

function setError(input, message) {
    const target = document.getElementById(input.getAttribute('aria-describedby'));
    input.setAttribute('aria-invalid', message ? 'true' : 'false');
    if (target) {
        target.textContent = message ?? '';
        target.hidden = !message;
    }
}

function initShortenForm(form) {
    const urlInput = form.querySelector('[name="url"]');
    const codeInput = form.querySelector('[name="code"]');
    const button = form.querySelector('[data-submit]');
    const toggle = form.querySelector('[data-toggle-custom]');
    const customBox = form.querySelector('[data-custom]');

    toggle?.addEventListener('click', () => {
        const open = customBox.hidden;
        customBox.hidden = !open;
        toggle.setAttribute('aria-expanded', String(open));
        if (open) codeInput.focus();
        else codeInput.value = '';
    });

    codeInput?.addEventListener('input', () => {
        codeInput.value = codeInput.value.toLowerCase().replace(/\s+/g, '-');
        setError(codeInput, null);
    });

    urlInput.addEventListener('input', () => setError(urlInput, null));

    form.addEventListener('submit', (event) => {
        if (form.dataset.submitting) {
            event.preventDefault();
            return;
        }

        const url = normalizeUrl(urlInput.value);
        const code = codeInput && !customBox.hidden ? validateCode(codeInput.value) : { ok: true, value: '' };

        setError(urlInput, url.ok ? null : url.message);
        if (codeInput) setError(codeInput, code.ok ? null : code.message);

        if (!url.ok || !code.ok) {
            event.preventDefault();
            (!url.ok ? urlInput : codeInput).focus();
            return;
        }

        urlInput.value = url.value;
        if (codeInput) codeInput.value = code.value;

        form.dataset.submitting = '1';
        button.disabled = true;
        button.setAttribute('aria-busy', 'true');
        button.querySelector('[data-label]').textContent = 'Encurtando';
        button.querySelector('[data-spinner]').hidden = false;
    });

    // Ao voltar pelo histórico (bfcache), reativa o botão.
    window.addEventListener('pageshow', () => {
        delete form.dataset.submitting;
        button.disabled = false;
        button.removeAttribute('aria-busy');
        button.querySelector('[data-label]').textContent = 'Encurtar';
        button.querySelector('[data-spinner]').hidden = true;
    });
}

function initCopyButtons() {
    document.querySelectorAll('[data-copy]').forEach((btn) => {
        btn.addEventListener('click', async () => {
            const text = btn.dataset.copy;
            try {
                await navigator.clipboard.writeText(text);
            } catch {
                const tmp = Object.assign(document.createElement('textarea'), { value: text });
                document.body.append(tmp);
                tmp.select();
                document.execCommand('copy');
                tmp.remove();
            }
            const label = btn.querySelector('[data-copy-label]');
            const original = label.textContent;
            label.textContent = 'Copiado!';
            btn.dataset.copied = 'true';
            setTimeout(() => {
                label.textContent = original;
                delete btn.dataset.copied;
            }, 1800);
        });
    });
}

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-shorten-form]').forEach(initShortenForm);
    initCopyButtons();
});
