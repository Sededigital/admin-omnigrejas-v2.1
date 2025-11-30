document.addEventListener('livewire:loaded', () => {
    // Escutar evento de refresh
    Livewire.on('refreshUsers', () => {
        // Refresh logic if needed
    });

    // Resetar formulário quando modal for fechado
    document.getElementById('userModal')?.addEventListener('hidden.bs.modal', function () {
        $wire.call('closeModal');
    });
});
