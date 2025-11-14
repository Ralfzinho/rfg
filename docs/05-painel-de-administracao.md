# Painel de Administração

O painel de administração é a área restrita do site onde os administradores e editores podem gerenciar o conteúdo do campeonato.

## Acesso

O acesso ao painel é feito através da página de login em `/admin-login/login.php`. Após o login, o usuário é redirecionado para o dashboard principal em `/admin/dashboard.php`.

## Funcionalidades

O painel é dividido nas seguintes seções:

### Dashboard (`admin/dashboard.php`)

Página inicial do painel, com links para as principais áreas de gerenciamento.

### Gerenciamento de Pilotos

*   **Listagem (`admin/pilotos/listar.php`)**: Exibe todos os pilotos cadastrados, permitindo editar ou excluir cada um.
*   **Cadastro (`admin/pilotos/cadastrar.php`)**: Formulário para adicionar um novo piloto.
*   **Edição (`admin/pilotos/editar.php`)**: Formulário para atualizar os dados de um piloto existente.

### Gerenciamento de Equipes

*   **Listagem (`admin/equipes/listar.php`)**: Exibe todas as equipes cadastradas.
*   **Cadastro (`admin/equipes/cadastrar.php`)**: Formulário para adicionar uma nova equipe.
*   **Edição (`admin/equipes/editar.php`)**: Formulário para atualizar os dados de uma equipe existente.

### Gerenciamento de Corridas

*   **Listagem (`admin/corridas/listar.php`)**: Exibe o calendário de corridas da temporada.
*   **Cadastro (`admin/corridas/cadastrar.php`)**: Formulário para agendar uma nova corrida.
*   **Edição (`admin/corridas/editar.php`)**: Formulário para atualizar as informações de uma corrida.

### Lançamento de Resultados

*   **`admin/corridas/resultados.php`**: Página para inserir os resultados de uma corrida finalizada, definindo a posição de chegada, pontos, etc., para cada piloto.

### Visualização da Classificação

*   **`admin/classificacao.php`**: Exibe a tabela de classificação de pilotos e equipes, calculada com base nos resultados lançados.

## Estado Atual (Desenvolvimento)

Atualmente, grande parte do painel de administração está em modo **"mock"**. Isso significa que as operações de criação, edição e exclusão de dados são salvas na sessão do PHP (`$_SESSION`) em vez de no banco de dados. O código para a interação com o banco de dados está presente nos arquivos, mas comentado, indicando a implementação futura.
