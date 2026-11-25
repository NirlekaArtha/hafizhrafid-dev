const themes = ['system', 'light', 'dark'];

function applyTheme(theme) {
    const root = document.documentElement;
    const dark = theme === 'dark' || (theme === 'system' && matchMedia('(prefers-color-scheme: dark)').matches);

    root.dataset.theme = theme;
    root.classList.toggle('dark', dark);

    document.querySelectorAll('[data-theme-toggle]').forEach((button) => {
        button.setAttribute('aria-label', button.dataset['label' + theme[0].toUpperCase() + theme.slice(1)]);
    });
}

function storedTheme() {
    try {
        const value = localStorage.getItem('theme');

        return themes.includes(value) ? value : 'system';
    } catch (e) {
        return 'system';
    }
}

document.addEventListener('click', (event) => {
    if (!event.target.closest('[data-theme-toggle]')) {
        return;
    }

    const next = themes[(themes.indexOf(storedTheme()) + 1) % themes.length];

    try {
        localStorage.setItem('theme', next);
    } catch (e) {
        // Storage can be blocked; the choice then only lasts for this page view.
    }

    applyTheme(next);
});

// Follow the OS setting live while the visitor has not picked a theme.
matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
    if (storedTheme() === 'system') {
        applyTheme('system');
    }
});

applyTheme(storedTheme());
