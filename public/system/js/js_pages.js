document.addEventListener('livewire:navigate', () => {
    const loader = document.getElementById('spa-loader');
    if (loader) {
        loader.classList.remove('d-none');
        setTimeout(() => loader.classList.add('show'), 50);
    }
});

document.addEventListener('livewire:navigated', () => {
    const loader = document.getElementById('spa-loader');
    if (loader) {
        loader.classList.remove('show');
        setTimeout(() => loader.classList.add('d-none'), 300);
    }
});

// Enhanced Toast System with Progress Bar
window.showToast = function(message, type = 'success') {
    // console.log('showToast called with:', message, type);
    const toastContainer = document.getElementById('global-toast-container');
    if (!toastContainer) {
     //   console.error('Toast container not found');
        return;
    }
    const toastId = 'toast-' + Date.now();

    // Define icon based on type
    const iconClass = type === 'success' ? 'fa-check-circle' :
                    type === 'error' || type === 'danger' ? 'fa-exclamation-triangle' :
                    type === 'warning' ? 'fa-exclamation-circle' :
                    'fa-info-circle';

    const toastHTML = `
        <div id="${toastId}" class="toast align-items-center text-white bg-${type} border-0 toast-slide-in" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex w-100">
                <div class="toast-body flex-grow-1">
                    <i class="fas ${iconClass} me-2 toast-icon"></i>
                    <span class="toast-message">${message}</span>
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto toast-close-btn" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-progress">
                <div class="toast-progress-bar" style="animation-duration: 5s;"></div>
            </div>
        </div>
    `;

    toastContainer.insertAdjacentHTML('beforeend', toastHTML);

    const toastElement = document.getElementById(toastId);

    // Add slide-in animation with bounce effect
    setTimeout(() => {
        toastElement.classList.add('toast-visible');
    }, 10);

    const toast = new bootstrap.Toast(toastElement, {
        autohide: true,
        delay: 5000
    });

    // Show toast with animation
    setTimeout(() => {
        toast.show();
    }, 100);

    // Remove toast from DOM after hiding with slide-out animation
    toastElement.addEventListener('hidden.bs.toast', () => {
        toastElement.classList.remove('toast-visible');
        toastElement.classList.add('toast-slide-out');
        setTimeout(() => {
            if (toastElement.parentNode) {
                toastElement.remove();
            }
        }, 300);
    });

    // Add click handler for close button with animation
    const closeBtn = toastElement.querySelector('.toast-close-btn');
    if (closeBtn) {
        closeBtn.addEventListener('click', () => {
            toastElement.classList.remove('toast-visible');
            toastElement.classList.add('toast-slide-out');
        });
    }
};

// Livewire 3 Compatible Toast System
let toastListenersInitialized = false;

function setupToastListeners() {
    if (toastListenersInitialized) {
        // console.log('Toast listeners already initialized');
        return;
    }

    // console.log('Setting up Livewire 3 toast event listeners');

    // Listen for toast events
    Livewire.on('toast', (data) => {
        // console.log('Livewire 3 toast event received:', data);

        // Handle both array and object formats (Livewire 3 compatibility)
        let toastData = data;
        if (Array.isArray(data) && data.length > 0) {
            toastData = data[0];
        }

        if (toastData && toastData.message && toastData.type) {
            // console.log('Calling showToast with:', toastData.message, toastData.type);
            window.showToast(toastData.message, toastData.type);
        } else {
           // console.error('Invalid toast data:', data);
        }
    });

    // Listen for test-toast events
    Livewire.on('test-toast', (data) => {
        // console.log('Livewire 3 test-toast event received:', data);

        // Handle both array and object formats
        let toastData = data;
        if (Array.isArray(data) && data.length > 0) {
            toastData = data[0];
        }

        if (toastData && toastData.message && toastData.type) {
            // console.log('Calling showToast for test-toast with:', toastData.message, toastData.type);
            window.showToast(toastData.message, toastData.type);
        } else {
          //  console.error('Invalid test-toast data:', data);
        }
    });

    toastListenersInitialized = true;
    // console.log('Livewire 3 toast listeners setup complete');
}

// Initialize on DOM ready
document.addEventListener('DOMContentLoaded', () => {
    // console.log('DOM loaded, initializing Livewire 3 toast system');

    // Setup immediately if Livewire is available
    if (typeof Livewire !== 'undefined') {
        setupToastListeners();
    }
});

// Re-initialize on Livewire navigation (SPA support)
document.addEventListener('livewire:navigated', () => {
    // console.log('Livewire navigated, re-initializing toast system');
    setupToastListeners();
});

// Fallback for older Livewire versions
document.addEventListener('livewire:loaded', () => {
    // console.log('Livewire loaded (fallback), setting up toast listeners');
    setupToastListeners();
});


