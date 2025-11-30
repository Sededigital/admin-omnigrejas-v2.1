function initLogsScripts() {
    const detalhesModal = document.getElementById('detalhesModal');

    if (detalhesModal) {
        detalhesModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const detalhes = button.getAttribute('data-detalhes');
            const content = document.getElementById('detalhes-content');

            if (!detalhes) {
                content.innerHTML = '<p class="text-muted">Nenhum detalhe disponível.</p>';
                return;
            }

            try {
                const detalhesObj = JSON.parse(detalhes);

                if (Object.keys(detalhesObj).length === 0) {
                    content.innerHTML = '<p class="text-muted">Nenhum detalhe disponível.</p>';
                    return;
                }

                let html = '<div class="list-group list-group-flush">';
                Object.entries(detalhesObj).forEach(([key, value]) => {
                    html += `<div class="list-group-item px-0">
                        <strong class="text-capitalize">${key.replace(/_/g, ' ')}:</strong>
                        <span class="ms-2">${value || 'N/A'}</span>
                    </div>`;
                });
                html += '</div>';

                content.innerHTML = html;
            } catch (e) {
                content.innerHTML = '<p class="text-danger">Erro ao carregar detalhes.</p>';
            }
        });
    }

    // Tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
}

// Eventos Livewire 3
document.addEventListener("livewire:initialized", () => {
    initLogsScripts();
});
document.addEventListener("livewire:navigated", () => {
    initLogsScripts();
});
