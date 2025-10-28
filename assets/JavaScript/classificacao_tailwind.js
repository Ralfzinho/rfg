// Renderizador de classificação em Tailwind (sem quebras + logo)
(() => {
  "use strict";
  const LIST_ID = "classificacao-list";

  function getList() {
    const el = document.getElementById(LIST_ID);
    if (!el) console.warn(`[classificacao] Container #${LIST_ID} não encontrado.`);
    return el;
  }

  window.renderClassificacaoTailwind = function (dados) {
    const list = getList();
    if (!list) return;
    list.innerHTML = "";

    const ordenado = [...dados].sort((a, b) => b.pontos - a.pontos);

    ordenado.forEach((row, idx) => {
      const item = document.createElement("div");
      item.className = "flex items-center justify-between py-3";

      if (idx === 0) item.classList.add("bg-emerald-50");

      // Esquerda (rank + avatar + nome)
      const left = document.createElement("div");
      // min-w-0 permite que o nome trunque dentro do flex
      left.className = "flex items-center gap-3 min-w-0";

      const rank = document.createElement("span");
      rank.className = "w-5 text-sm font-semibold text-neutral-700";
      rank.textContent = idx + 1;

      let avatar;
      if (row.logo) {
        avatar = document.createElement("img");
        avatar.src = row.logo;
        avatar.alt = row.equipe || "Logo";
        avatar.className = "w-7 h-7 rounded-full object-cover ring-1 ring-neutral-200 flex-none";
      } else {
        avatar = document.createElement("span");
        avatar.className = "w-7 h-7 rounded-full bg-neutral-200 inline-block ring-1 ring-neutral-200 flex-none";
      }

      const name = document.createElement("span");
      // truncate = sem quebra + reticências; flex-1 para ocupar o espaço restante
      name.className = "font-medium truncate flex-1";
      name.textContent = row.equipe;

      left.append(rank, avatar, name);

      // Direita (pontos) – nunca quebrar linha
      const points = document.createElement("span");
      points.className = "font-semibold text-emerald-600 whitespace-nowrap";
      points.textContent = `${row.pontos} pts`;

      item.append(left, points);
      list.appendChild(item);
    });
  };
})();
