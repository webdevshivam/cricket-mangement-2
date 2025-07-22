/**
 * Components page JavaScript functionality
 * Handles component showcase, code copying, and demonstrations
 */

/**
 * Initialize components page functionality
 */
function initializeComponents() {
    initializeComponentCategories();
    initializeCodeCopying();
    initializeDemoChart();
    initializeSelect2Demo();
    
    console.log('Components page initialized');
}

/**
 * Initialize component categories
 */
function initializeComponentCategories() {
    const categoryButtons = document.querySelectorAll('.category-btn');
    const componentSections = document.querySelectorAll('.component-section');
    
    categoryButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const category = this.dataset.category;
            
            // Update active button
            categoryButtons.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            
            // Show/hide sections
            componentSections.forEach(section => {
                if (category === 'all' || section.dataset.category === category) {
                    section.style.display = 'block';
                } else {
                    section.style.display = 'none';
                }
            });
        });
    });
}

/**
 * Initialize code copying functionality
 */
function initializeCodeCopying() {
    // This function will be called from the HTML
    window.copyCode = function(codeId) {
        const codeElement = document.getElementById(codeId);
        if (!codeElement) return;
        
        const code = codeElement.textContent;
        
        // Copy to clipboard
        navigator.clipboard.writeText(code).then(() => {
            showToast('Code copied to clipboard!', 'success');
        }).catch(err => {
            console.error('Failed to copy code:', err);
            showToast('Failed to copy code', 'error');
        });
    };
}

/**
 * Initialize demo chart
 */
function initializeDemoChart() {
    const ctx = document.getElementById('demo-chart');
    if (!ctx) return;
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            datasets: [{
                label: 'Performance',
                data: [12, 19, 3, 5, 2, 3],
                borderColor: '#ffd700',
                backgroundColor: 'rgba(255, 215, 0, 0.1)',
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    labels: {
                        color: '#ffffff'
                    }
                }
            },
            scales: {
                y: {
                    ticks: {
                        color: '#ffffff'
                    },
                    grid: {
                        color: 'rgba(255, 255, 255, 0.1)'
                    }
                },
                x: {
                    ticks: {
                        color: '#ffffff'
                    },
                    grid: {
                        color: 'rgba(255, 255, 255, 0.1)'
                    }
                }
            }
        }
    });
}

/**
 * Initialize Select2 demo
 */
function initializeSelect2Demo() {
    const select2Demo = document.querySelector('.select2-demo');
    if (select2Demo && typeof $ !== 'undefined') {
        $(select2Demo).select2({
            theme: 'bootstrap-5',
            width: '100%'
        });
    }
}

/**
 * Show confirmation demo
 */
function showConfirmationDemo() {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ffd700',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete it!',
            background: '#2a2a2a',
            color: '#ffffff'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Deleted!',
                    text: 'Your file has been deleted.',
                    icon: 'success',
                    background: '#2a2a2a',
                    color: '#ffffff',
                    confirmButtonColor: '#ffd700'
                });
            }
        });
    }
}

/**
 * Show toast notification
 */
function showToast(message, type = 'info') {
    // Create toast element
    const toast = document.createElement('div');
    toast.className = `toast align-items-center text-white bg-${type === 'success' ? 'success' : 'warning'} border-0`;
    toast.setAttribute('role', 'alert');
    toast.setAttribute('aria-live', 'assertive');
    toast.setAttribute('aria-atomic', 'true');
    
    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">
                <i class="fas fa-${type === 'success' ? 'check' : 'info'}-circle me-2"></i>
                ${message}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    `;
    
    // Add to toast container or create one
    let toastContainer = document.querySelector('.toast-container');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3';
        document.body.appendChild(toastContainer);
    }
    
    toastContainer.appendChild(toast);
    
    // Show toast
    const bsToast = new bootstrap.Toast(toast);
    bsToast.show();
    
    // Remove toast after it's hidden
    toast.addEventListener('hidden.bs.toast', () => {
        toast.remove();
    });
}

/**
 * Refresh components
 */
function refreshComponents() {
    showToast('Components refreshed!', 'success');
}

// Make functions available globally
window.initializeComponents = initializeComponents;
window.showConfirmationDemo = showConfirmationDemo;
window.refreshComponents = refreshComponents;

// Refresh button event listener
document.addEventListener('DOMContentLoaded', function() {
    const refreshBtn = document.getElementById('refresh-components');
    if (refreshBtn) {
        refreshBtn.addEventListener('click', refreshComponents);
    }
});

console.log('Components JavaScript loaded successfully');