// /rfg/assets/js/admin-panel.js

// ===============================
// Função auxiliar para resetar um item do menu
// ===============================
function resetMenuItem(item) {
    // Remove classes de ativo
    item.classList.remove('bg-yellow-400', 'text-white', 'shadow-lg', 'sidebar-active');
    item.classList.add('text-gray-600');
    
    // Remove estilos inline do item principal
    item.style.backgroundColor = '';
    item.style.color = '';
    item.style.boxShadow = '';
    
    // Reseta o container do ícone
    const iconContainer = item.querySelector('.w-8');
    if (iconContainer) {
        iconContainer.classList.remove('bg-yellow-500');
        iconContainer.classList.add('bg-gray-100');
        iconContainer.style.backgroundColor = '';
    }
    
    // Reseta o SVG para dourado
    const svg = item.querySelector('svg');
    if (svg) {
        svg.classList.remove('text-white');
        svg.classList.add('text-racing-gold');
        svg.style.color = '#facc15'; // Dourado
        svg.style.fill = '#facc15';
    }
    
    // Reseta o texto
    const span = item.querySelector('span');
    if (span) {
        span.style.color = '';
    }
}

// ===============================
// Função auxiliar para ativar um item do menu
// ===============================
function activateMenuItem(item) {
    // Adiciona classes de ativo
    item.classList.add('bg-yellow-400', 'text-white', 'shadow-lg', 'sidebar-active');
    item.classList.remove('text-gray-600');
    
    // Força estilos inline no item principal
    item.style.backgroundColor = '#facc15'; // Amarelo
    item.style.color = 'white';
    item.style.boxShadow = '0 10px 15px -3px rgba(250, 204, 21, 0.3)';
    
    // Atualiza o container do ícone
    const iconContainer = item.querySelector('.w-8');
    if (iconContainer) {
        iconContainer.classList.add('bg-yellow-500');
        iconContainer.classList.remove('bg-gray-100');
        iconContainer.style.backgroundColor = 'rgba(234, 179, 8, 0.4)';
    }
    
    // Força o SVG ficar BRANCO
    const svg = item.querySelector('svg');
    if (svg) {
        svg.classList.add('text-white');
        svg.classList.remove('text-racing-gold');
        svg.style.color = 'white';
        svg.style.fill = 'white';
    }
    
    // Força o texto ficar branco
    const span = item.querySelector('span');
    if (span) {
        span.style.color = 'white';
        span.style.fontWeight = '600';
    }
}

// ===============================
// Troca de seções do painel (para SPA - Single Page)
// ===============================
window.showSection = function (sectionName, btnEl) {
    // Esconde todas as seções
    document.querySelectorAll('.section').forEach(section => {
        section.classList.add('hidden');
    });

    // Mostra a seção escolhida
    const section = document.getElementById(sectionName + '-section');
    if (section) {
        section.classList.remove('hidden');
    }

    // Reseta todos os itens do menu
    document.querySelectorAll('.sidebar-item').forEach(item => {
        resetMenuItem(item);
    });

    // Pega o botão correto (mesmo se o clique foi no span/svg)
    const btn = btnEl ? btnEl.closest('.sidebar-item') : null;
    if (btn) {
        activateMenuItem(btn);
    }
};

// ===============================
// Marca item ativo na sidebar baseado na URL atual
// ===============================
function setActiveSidebarItem() {
    const currentPath = window.location.pathname;
    
    // Reseta todos os itens do menu
    document.querySelectorAll('.sidebar-item').forEach(item => {
        resetMenuItem(item);
    });
    
    // Mapeia URLs para os itens da sidebar
    const pathMap = {
        '/rfg/admin/dashboard_new.php': 'dashboard',
        '/rfg/admin/pilotos': 'pilotos',
        '/rfg/admin/equipes': 'equipes',
        '/rfg/admin/corridas/listar': 'corridas',
        '/rfg/admin/corridas/resultados': 'resultados',
        '/rfg/admin/classificacoes': 'classificacoes',
        '/rfg/admin/usuarios': 'usuarios',
    };
    
    // Encontra qual seção corresponde à URL atual
    let activeSection = null;
    for (const [path, section] of Object.entries(pathMap)) {
        if (currentPath.includes(path)) {
            activeSection = section;
            break;
        }
    }
    
    // Se encontrou, marca como ativo
    if (activeSection) {
        const activeItem = document.querySelector(`[data-section="${activeSection}"]`);
        if (activeItem) {
            activateMenuItem(activeItem);
        }
    }
}

// ===============================
// Modais
// ===============================
window.showModal = function (modalId) {
    const modal = document.getElementById(modalId);
    if (!modal) return;
    modal.classList.remove('hidden');
    modal.classList.add('flex');
};

window.hideModal = function (modalId) {
    const modal = document.getElementById(modalId);
    if (!modal) return;
    modal.classList.add('hidden');
    modal.classList.remove('flex');
};

// Fecha modais clicando fora
document.addEventListener('click', function (e) {
    if (e.target.classList.contains('modal')) {
        hideModal(e.target.id);
    }
});

// ===============================
// Toast / mensagem
// ===============================
window.showMessage = function (message, type = 'success') {
    const container = document.getElementById('messageContainer');
    if (!container) return;

    const bgColor = type === 'success' ? 'bg-green-600' : 'bg-red-600';
    const messageDiv = document.createElement('div');

    messageDiv.className =
        `${bgColor} text-white px-6 py-3 rounded-lg shadow-lg ` +
        `transform transition-all duration-300 translate-x-full`;

    messageDiv.innerHTML = `
    <div class="flex items-center space-x-2">
      <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
        ${type === 'success'
            ? '<path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>'
            : '<path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>'
        }
      </svg>
      <span>${message}</span>
    </div>
  `;

    container.appendChild(messageDiv);

    // entra animando
    setTimeout(() => {
        messageDiv.classList.remove('translate-x-full');
    }, 100);

    // sai depois de 5s
    setTimeout(() => {
        messageDiv.classList.add('translate-x-full');
        setTimeout(() => {
            if (container.contains(messageDiv)) {
                container.removeChild(messageDiv);
            }
        }, 300);
    }, 5000);
};

// ===============================
// Inicialização
// ===============================
document.addEventListener('DOMContentLoaded', function () {
    // Marca o item ativo baseado na URL
    setActiveSidebarItem();
    
    // Se estiver em uma página SPA com seções, mostra a primeira
    const firstSection = document.querySelector('.section');
    if (firstSection) {
        const firstBtn = document.querySelector('.sidebar-item');
        if (firstBtn) {
            window.showSection('dashboard', firstBtn);
        }
    }
});