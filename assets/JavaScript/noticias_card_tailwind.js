// Renderizador de notícias em Tailwind
(() => {
  "use strict";
  const GRID_ID = "noticias-container";

  function criarCard(n) {
    const art = document.createElement("article");
    art.className = "bg-white rounded-2xl overflow-hidden border shadow-sm hover:shadow-md transition";

    if (n.imagem) {
      const img = document.createElement("img");
      img.src = n.imagem;
      img.alt = n.titulo || "Notícia";
      img.className = "w-full h-44 object-cover";
      art.appendChild(img);
    }

    const box = document.createElement("div");
    box.className = "p-4";

    if (n.data) {
      const small = document.createElement("small");
      small.className = "text-neutral-500";
      small.textContent = n.data;
      box.appendChild(small);
    }

    const h3 = document.createElement("h3");
    h3.className = "mt-1 font-semibold";
    h3.textContent = n.titulo || "";
    box.appendChild(h3);

    if (n.subtitulo) {
      const sub = document.createElement("p");
      sub.className = "text-sm text-neutral-600";
      sub.textContent = n.subtitulo;
      box.appendChild(sub);
    }

    const p = document.createElement("p");
    p.className = "mt-2 text-sm";
    p.textContent = n.texto || "";
    box.appendChild(p);

    const actions = document.createElement("div");
    actions.className = "mt-3";
    if (n.link) {
      const a = document.createElement("a");
      a.href = n.link;
      a.className = "inline-flex items-center text-primary hover:underline";
      a.textContent = "Ler mais";
      actions.appendChild(a);
    }
    box.appendChild(actions);

    art.appendChild(box);
    return art;
  }

  function getGrid() {
    return document.getElementById(GRID_ID);
  }

  window.adicionarNoticia = function (n) {
    const grid = getGrid();
    if (!grid) return;
    grid.appendChild(criarCard(n));
  };

  window.adicionarNoticiasEmLote = function (lista) {
    const grid = getGrid();
    if (!grid) return;
    const frag = document.createDocumentFragment();
    (lista || []).forEach(n => frag.appendChild(criarCard(n)));
    grid.appendChild(frag);
  };
})();
