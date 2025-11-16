// /rfg/assets/js/admin-panel.js

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

    // Remove qualquer .sidebar-active (botão, span, etc.)
    document.querySelectorAll('.sidebar-active').forEach(el => {
        el.classList.remove('sidebar-active');
    });

    // Pega o botão correto (mesmo se o clique foi no span/svg)
    const btn = btnEl ? btnEl.closest('.sidebar-item') : null;
    if (btn) {
        btn.classList.add('sidebar-active');
    }
};

// ===============================
// Marca item ativo na sidebar baseado na URL atual
// ===============================
function setActiveSidebarItem() {
    const currentPath = window.location.pathname;
    
    // Remove todos os ativos
    document.querySelectorAll('.sidebar-active').forEach(el => {
        el.classList.remove('sidebar-active');
    });
    
    // Mapeia URLs para os itens da sidebar
    const pathMap = {
        '/admin/index.php': 'dashboard',
        '/admin/dashboard.php': 'dashboard',
        '/admin/pilotos/listar.php': 'pilotos',
        '/admin/pilotos/cadastrar.php': 'pilotos',
        '/admin/pilotos/editar.php': 'pilotos',
        '/admin/equipes/listar.php': 'equipes',
        '/admin/equipes/cadastrar.php': 'equipes',
        '/admin/equipes/editar.php': 'equipes',
        '/admin/corridas/listar.php': 'corridas',
        '/admin/corridas/cadastrar.php': 'corridas',
        '/admin/corridas/editar.php': 'corridas',
        '/admin/resultados/listar.php': 'resultados',
        '/admin/resultados/cadastrar.php': 'resultados',
        '/admin/classificacoes/pilotos.php': 'classificacoes',
        '/admin/classificacoes/equipes.php': 'classificacoes',
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
            activeItem.classList.add('sidebar-active');
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