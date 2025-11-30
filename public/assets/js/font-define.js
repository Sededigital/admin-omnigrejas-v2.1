function initFontSizeToggle() {
    const fontButtons = document.querySelectorAll('.font-size-toggle .btn');

    if (!fontButtons.length) return;

    fontButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            fontButtons.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');

            const size = btn.getAttribute('data-size');
            document.body.classList.remove('font-small', 'font-medium');
            document.body.classList.add(`font-${size}`);
            localStorage.setItem('fontSize', size);
        });
    });

    // Carrega do localStorage
    const savedSize = localStorage.getItem('fontSize');
    if(savedSize){
        document.body.classList.add(`font-${savedSize}`);
        const activeBtn = document.querySelector(`.font-size-toggle .btn[data-size="${savedSize}"]`);
        if(activeBtn) activeBtn.classList.add('active');
    } else {
        const defaultBtn = document.querySelector(`.font-size-toggle .btn[data-size="small"]`);
        if (defaultBtn) defaultBtn.classList.add('active');
        document.body.classList.add('font-small');
    }
}

// Primeira carga
initFontSizeToggle();

// Depois de cada navegação SPA
document.addEventListener('livewire:navigated', initFontSizeToggle);
