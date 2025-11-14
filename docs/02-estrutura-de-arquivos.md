# Estrutura de Arquivos

O projeto está organizado da seguinte forma:

```
/
|-- admin/
|   |-- corridas/
|   |-- equipes/
|   |-- pilotos/
|   |-- classificacao.php
|   `-- dashboard.php
|-- admin-login/
|   |-- criar_admin.php
|   |-- login.php
|   |-- login_process.php
|   `-- logout.php
|-- assets/
|   |-- css/
|   |-- img/
|   `-- js/
|-- docs/
|   |-- 01-visao-geral.md
|   |-- ...
|-- includes/
|   |-- db.php
|   |-- funcoes.php
|   |-- layout_footer.php
|   |-- layout_head.php
|   `-- layout_nav.php
|-- pages/
|   |-- classificacao.php
|   |-- corrida.php
|   |-- corrida_detalhe.php
|   |-- equipes.php
|   `-- pilotos.php
|-- index.php
`-- README.md
```

## Descrição dos Diretórios

*   **`admin/`**: Contém as páginas do painel de administração para gerenciar o conteúdo do site.
*   **`admin-login/`**: Contém os arquivos relacionados à autenticação dos usuários administradores.
*   **`assets/`**: Contém os arquivos estáticos como CSS, imagens e JavaScript.
*   **`docs/`**: Contém os arquivos de documentação do projeto.
*   **`includes/`**: Contém arquivos PHP reutilizáveis, como a conexão com o banco de dados, funções de autenticação e partes do layout (cabeçalho, rodapé, navegação).
*   **`pages/`**: Contém as páginas públicas do site, que exibem as informações do campeonato para os visitantes.

## Arquivos Principais

*   **`index.php`**: A página inicial do site.
*   **`passos.txt`**: Um arquivo de texto com anotações sobre as funcionalidades planejadas para o projeto.
*   **`includes/db.php`**: Arquivo de configuração e conexão com o banco de dados.
*   **`includes/funcoes.php`**: Contém as principais funções de autenticação e outras funções auxiliares.
*   **`admin/dashboard.php`**: A página principal do painel de administração.
