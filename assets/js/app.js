/**
 * Sistema de Control de Firmas - JavaScript Principal
 * Funcionalidades interactivas y utilidades
 */

// Namespace principal de la aplicación
window.App = {
    // Configuración global
    config: {
        toastDuration: 5000,
        animationDuration: 300,
        debounceDelay: 500
    },

    // Inicialización de la aplicación
    init: function() {
        this.initToasts();
        this.initSidebar();
        this.initTables();
        this.initForms();
        this.autoHideMessages();
    },

    // Sistema de notificaciones toast
    showToast: function(message, type = 'info', duration = null) {
        duration = duration || this.config.toastDuration;
        
        const toast = document.createElement('div');
        toast.className = `fixed top-4 right-4 z-50 px-6 py-4 rounded-lg shadow-lg transform translate-x-full transition-transform duration-300`;
        
        // Estilos según el tipo
        const styles = {
            success: 'bg-green-500 text-white',
            error: 'bg-red-500 text-white',
            warning: 'bg-yellow-500 text-white',
            info: 'bg-blue-500 text-white'
        };
        
        toast.className += ` ${styles[type] || styles.info}`;
        toast.innerHTML = `
            <div class="flex items-center">
                <i class="fas fa-${this.getToastIcon(type)} mr-2"></i>
                <span>${message}</span>
                <button class="ml-4 text-white hover:text-gray-200" onclick="this.parentElement.parentElement.remove()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;
        
        document.body.appendChild(toast);
        
        // Animación de entrada
        setTimeout(() => {
            toast.classList.remove('translate-x-full');
        }, 100);
        
        // Auto-remove
        setTimeout(() => {
            toast.classList.add('translate-x-full');
            setTimeout(() => toast.remove(), 300);
        }, duration);
    },

    getToastIcon: function(type) {
        const icons = {
            success: 'check-circle',
            error: 'exclamation-circle',
            warning: 'exclamation-triangle',
            info: 'info-circle'
        };
        return icons[type] || icons.info;
    },

    // Inicializar sistema de toasts
    initToasts: function() {
        // Auto-hide existing messages
        this.autoHideMessages();
    },

    // Auto-ocultar mensajes del sistema (solo mensajes temporales, no estados permanentes)
    autoHideMessages: function() {
        setTimeout(() => {
            const messages = document.querySelectorAll('[class*="bg-green-100"], [class*="bg-red-100"]');
            messages.forEach(message => {
                // Solo ocultar mensajes que no sean de estado de firmas
                const isStatusMessage = message.closest('.signatures-status') || 
                                      message.textContent.includes('Firma registrada') ||
                                      message.textContent.includes('Estado de firma') ||
                                      message.textContent.includes('firmado') ||
                                      message.textContent.includes('pendiente');
                
                if (!isStatusMessage) {
                    message.style.transition = 'opacity 0.5s';
                    message.style.opacity = '0';
                    setTimeout(() => message.remove(), 500);
                }
            });
        }, this.config.toastDuration);
    }

    // Funcionalidades del sidebar
    initSidebar: function() {
        // Restaurar estado del sidebar
        const sidebarCollapsed = localStorage.getItem('sidebar_collapsed') === 'true';
        if (sidebarCollapsed && window.innerWidth < 768) {
            const sidebar = document.getElementById('sidebar');
            if (sidebar) sidebar.classList.add('-translate-x-full');
        }

        // Marcar enlace activo
        this.markActiveLink();
    },

    markActiveLink: function() {
        const currentPath = window.location.search;
        const sidebarLinks = document.querySelectorAll('#sidebar a[href]');
        
        sidebarLinks.forEach(link => {
            const href = link.getAttribute('href');
            if (href && currentPath.includes(href.split('?')[1])) {
                link.classList.add('bg-blue-100', 'text-blue-700');
                link.classList.remove('text-gray-700');
            }
        });
    },

    // Funcionalidades de tablas
    initTables: function() {
        // Hacer tablas responsive
        const tables = document.querySelectorAll('table');
        tables.forEach(table => {
            if (!table.closest('.table-responsive')) {
                const wrapper = document.createElement('div');
                wrapper.className = 'table-responsive overflow-x-auto';
                table.parentNode.insertBefore(wrapper, table);
                wrapper.appendChild(table);
            }
        });
    },

    // Funcionalidades de formularios
    initForms: function() {
        // Validación en tiempo real
        const forms = document.querySelectorAll('form');
        forms.forEach(form => {
            form.addEventListener('submit', this.handleFormSubmit.bind(this));
        });

        // Mejorar inputs
        this.enhanceInputs();
    },

    handleFormSubmit: function(e) {
        const form = e.target;
        const submitBtn = form.querySelector('button[type="submit"]');
        
        if (submitBtn && !submitBtn.disabled) {
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Procesando...';
            submitBtn.disabled = true;
            
            // Restaurar botón si hay error
            setTimeout(() => {
                if (submitBtn.disabled) {
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                }
            }, 10000);
        }
    },

    enhanceInputs: function() {
        // Mejorar campos de número de referencia
        const refInputs = document.querySelectorAll('input[name="numero_referencia"]');
        refInputs.forEach(input => {
            input.addEventListener('input', function(e) {
                const value = e.target.value.toUpperCase();
                e.target.value = value;
                
                // Validación básica
                const regex = /^[A-Z0-9-]*$/;
                if (!regex.test(value)) {
                    e.target.setCustomValidity('Solo letras mayúsculas, números y guiones');
                } else {
                    e.target.setCustomValidity('');
                }
            });
        });
    },

    // Utilidades
    utils: {
        // Debounce function
        debounce: function(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        },

        // Copiar al portapapeles
        copyToClipboard: function(text) {
            if (navigator.clipboard) {
                navigator.clipboard.writeText(text).then(() => {
                    App.showToast('Copiado al portapapeles', 'success', 2000);
                }).catch(() => {
                    this.fallbackCopyTextToClipboard(text);
                });
            } else {
                this.fallbackCopyTextToClipboard(text);
            }
        },

        fallbackCopyTextToClipboard: function(text) {
            const textArea = document.createElement('textarea');
            textArea.value = text;
            textArea.style.position = 'fixed';
            textArea.style.left = '-999999px';
            textArea.style.top = '-999999px';
            document.body.appendChild(textArea);
            textArea.focus();
            textArea.select();
            
            try {
                document.execCommand('copy');
                App.showToast('Copiado al portapapeles', 'success', 2000);
            } catch (err) {
                App.showToast('Error al copiar', 'error', 2000);
            }
            
            document.body.removeChild(textArea);
        },

        // Formatear fecha
        formatDate: function(date) {
            return new Intl.DateTimeFormat('es-ES', {
                year: 'numeric',
                month: '2-digit',
                day: '2-digit'
            }).format(new Date(date));
        },

        // Generar ID único
        generateId: function() {
            return Date.now().toString(36) + Math.random().toString(36).substr(2);
        }
    },

    // Diálogos de confirmación
    confirm: function(message, callback) {
        if (confirm(message)) {
            callback();
        }
    },

    // Loading states
    showLoading: function(element) {
        if (element) {
            element.classList.add('loading');
            element.disabled = true;
        }
    },

    hideLoading: function(element) {
        if (element) {
            element.classList.remove('loading');
            element.disabled = false;
        }
    }
};

// Funciones globales para compatibilidad
function toggleSubmenu(submenuId) {
    const submenu = document.getElementById(submenuId);
    const icon = document.getElementById(submenuId + '-icon');
    
    if (submenu && icon) {
        if (submenu.classList.contains('hidden')) {
            submenu.classList.remove('hidden');
            icon.classList.add('rotate-180');
            localStorage.setItem('submenu_' + submenuId, 'open');
        } else {
            submenu.classList.add('hidden');
            icon.classList.remove('rotate-180');
            localStorage.setItem('submenu_' + submenuId, 'closed');
        }
    }
}

function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    if (sidebar) {
        sidebar.classList.toggle('-translate-x-full');
        const isHidden = sidebar.classList.contains('-translate-x-full');
        localStorage.setItem('sidebar_collapsed', isHidden);
    }
}

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    // App.init();
});

// Exportar para uso global
window.showToast = App.showToast.bind(App);
window.copyToClipboard = App.utils.copyToClipboard;