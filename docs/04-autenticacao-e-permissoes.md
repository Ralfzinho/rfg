# Autenticação e Permissões

O sistema possui um painel de administração com acesso restrito, protegido por um sistema de login.

## Arquivos Relevantes

*   **`admin-login/login.php`**: Página com o formulário de login.
*   **`admin-login/login_process.php`**: Script que processa os dados do formulário de login.
*   **`admin-login/logout.php`**: Script que encerra a sessão do usuário.
*   **`includes/funcoes.php`**: Contém todas as funções de `auth_*` para controle de acesso.

## Lógica de Autenticação

A lógica de autenticação está centralizada no arquivo `includes/funcoes.php`. As principais funções são:

*   **`auth_login(string $email, string $password): bool`**: Verifica as credenciais do usuário no banco de dados. Se forem válidas, armazena os dados do usuário na sessão `$_SESSION['user']`.
*   **`auth_check(): bool`**: Retorna `true` se o usuário estiver logado (ou seja, se `$_SESSION['user']` existir).
*   **`auth_user(): ?array`**: Retorna os dados do usuário logado ou `null` se não houver ninguém logado.
*   **`auth_logout(): void`**: Encerra a sessão do usuário e o redireciona para a página de login.

## Controle de Acesso e Permissões

O sistema utiliza "guardas" para proteger páginas que exigem autenticação ou um nível de permissão específico.

*   **`auth_require_login(): void`**: Se o usuário não estiver logado, esta função o redireciona para a página de login. É usada no topo de todas as páginas do painel de administração.
*   **`auth_require_role(array $roles): void`**: Além de exigir login, esta função verifica se o papel (`role`) do usuário está na lista de `$roles` permitidas. Caso contrário, o usuário é redirecionado para uma página de "sem permissão".

Os papéis de usuário (`roles`) estão definidos na tabela `usuarios` e podem ser:

*   **`admin`**: Acesso total ao painel de administração.
*   **`editor`**: Acesso limitado, pode criar e editar conteúdo, mas não pode gerenciar usuários.
*   **`viewer`**: Acesso apenas para visualização.
