# Páginas Públicas

As páginas públicas são as áreas do site acessíveis a todos os visitantes, sem a necessidade de login.

## Arquivos

Todas as páginas públicas estão localizadas no diretório `/pages/`.

### `index.php` (Página Inicial)

É a porta de entrada do site. Apresenta um design moderno com:
*   Um "hero" com vídeo de fundo.
*   Seções sobre a história da equipe "Race for Glory".
*   Chamada para ação para seguir a equipe no Instagram.
*   Destaques e notícias (atualmente com conteúdo estático).

### `pages/classificacao.php`

Exibe a tabela de classificação da temporada, tanto para pilotos quanto para equipes. Os dados são obtidos dinamicamente do banco de dados, somando os pontos da tabela `resultados`.

### `pages/pilotos.php`

Lista todos os pilotos ativos na temporada, exibindo o nome e a equipe de cada um.

### `pages/equipes.php`

Mostra a lista de equipes participantes, com a pontuação total de cada uma.

### `pages/corrida.php`

Apresenta o calendário completo da temporada em formato de timeline. Permite filtrar para ver apenas as corridas futuras. Cada item da lista leva para a página de detalhes da corrida.

### `pages/corrida_detalhe.php`

Exibe informações detalhadas sobre uma corrida específica, incluindo:
*   Nome do GP e do circuito.
*   Data e número de voltas.
*   A tabela com os resultados finais da corrida (posição, piloto, equipe, pontos).
