// ========== ANIMATIONS & INTERACTIONS ==========

document.addEventListener('DOMContentLoaded', function() {
    
    // Animations au scroll
    const animateOnScroll = () => {
        const elements = document.querySelectorAll('.card, .stat-card, .service-card, .demand-card');
        
        elements.forEach(el => {
            const rect = el.getBoundingClientRect();
            const isVisible = rect.top < window.innerHeight - 100;
            
            if (isVisible && !el.classList.contains('animated')) {
                el.classList.add('scale-in', 'animated');
            }
        });
    };
    
    // Initial animation
    animateOnScroll();
    window.addEventListener('scroll', animateOnScroll);
    
    // Confirmation modale stylisée pour suppression
    const deleteButtons = document.querySelectorAll('.btn-delete, [onclick*="confirm"]');
    
    deleteButtons.forEach(btn => {
        const originalOnclick = btn.getAttribute('onclick');
        if (originalOnclick) {
            btn.removeAttribute('onclick');
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                if (confirm('⚠️ Êtes-vous sûr de vouloir supprimer cet élément ? Cette action est irréversible.')) {
                    eval(originalOnclick);
                }
            });
        }
    });
    
    // Tooltips personnalisés
    const tooltips = document.querySelectorAll('[data-tooltip]');
    tooltips.forEach(el => {
        el.addEventListener('mouseenter', function(e) {
            const tooltip = document.createElement('div');
            tooltip.className = 'custom-tooltip';
            tooltip.textContent = this.dataset.tooltip;
            tooltip.style.cssText = `
                position: fixed;
                background: #1a1a2e;
                color: white;
                padding: 6px 12px;
                border-radius: 8px;
                font-size: 0.75rem;
                z-index: 10000;
                pointer-events: none;
                white-space: nowrap;
                font-weight: 500;
            `;
            document.body.appendChild(tooltip);
            
            const updatePosition = (x, y) => {
                tooltip.style.left = (x + 15) + 'px';
                tooltip.style.top = (y - 30) + 'px';
            };
            
            updatePosition(e.clientX, e.clientY);
            
            const moveHandler = (moveEvent) => {
                updatePosition(moveEvent.clientX, moveEvent.clientY);
            };
            
            document.addEventListener('mousemove', moveHandler);
            
            el.addEventListener('mouseleave', function() {
                tooltip.remove();
                document.removeEventListener('mousemove', moveHandler);
            }, { once: true });
        });
    });
    
    // Smooth scroll pour les ancres
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                e.preventDefault();
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    });
    
    // Loading state pour les formulaires
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const submitBtn = this.querySelector('button[type="submit"]');
            if (submitBtn && !submitBtn.classList.contains('no-loading')) {
                const originalText = submitBtn.innerHTML;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Chargement...';
                submitBtn.disabled = true;
                
                setTimeout(() => {
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                }, 3000);
            }
        });
    });
    
    // Compteur animé pour les statistiques
    const animateNumbers = () => {
        const counters = document.querySelectorAll('.stat-card h3, .counter');
        counters.forEach(counter => {
            const updateCounter = () => {
                const target = parseInt(counter.innerText);
                const current = parseInt(counter.innerText);
                const increment = target / 50;
                
                if (current < target) {
                    counter.innerText = Math.ceil(current + increment);
                    setTimeout(updateCounter, 20);
                } else {
                    counter.innerText = target;
                }
            };
            
            const rect = counter.getBoundingClientRect();
            if (rect.top < window.innerHeight - 100 && !counter.classList.contains('counted')) {
                counter.classList.add('counted');
                updateCounter();
            }
        });
    };
    
    window.addEventListener('scroll', animateNumbers);
    animateNumbers();
    
    // Dark mode toggle (optionnel - gardé simple)
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)');
    if (prefersDark.matches) {
        document.body.classList.add('dark-mode');
    }
    
    // Notification toast (si présent)
    const showToast = (message, type = 'success') => {
        const toast = document.createElement('div');
        toast.className = `toast-notification toast-${type}`;
        toast.innerHTML = `
            <div class="toast-content">
                <i class="fas ${type === 'success' ? 'fa-check-circle' : type === 'error' ? 'fa-exclamation-circle' : 'fa-info-circle'}"></i>
                <span>${message}</span>
            </div>
        `;
        toast.style.cssText = `
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: white;
            border-radius: 12px;
            padding: 12px 20px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
            z-index: 10000;
            animation: slideIn 0.3s ease;
            border-left: 4px solid ${type === 'success' ? '#2a9d8f' : type === 'error' ? '#e63946' : '#1a4a6f'};
        `;
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 3000);
    };
    
    // Exposer globalement
    window.showToast = showToast;
    
    // Animation de chargement des graphiques
    const charts = document.querySelectorAll('canvas');
    charts.forEach(chart => {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    observer.unobserve(entry.target);
                }
            });
        });
        chart.style.opacity = '0';
        chart.style.transition = 'opacity 0.5s ease';
        observer.observe(chart);
    });
    
});

// Ajout des styles d'animation pour les toasts
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    .animated {
        animation: scaleIn 0.4s ease-out forwards;
    }
    
    @keyframes scaleIn {
        from {
            opacity: 0;
            transform: scale(0.95);
        }
        to {
            opacity: 1;
            transform: scale(1);
        }
    }
    
    .dark-mode {
        --gray-50: #1a1a2e;
        --gray-100: #16213e;
        --gray-200: #1f2a44;
        background: #0f0f1a;
    }
`;
document.head.appendChild(style);