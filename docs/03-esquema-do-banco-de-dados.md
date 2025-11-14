# Esquema do Banco de Dados

O banco de dados, nomeado `race_for_glory`, é o coração do sistema. Ele armazena todas as informações sobre pilotos, equipes, corridas e resultados.

O esquema SQL completo para a criação das tabelas e inserção de dados iniciais pode ser encontrado no arquivo [`includes/seeds.txt`](../includes/seeds.txt).

## Tabelas Principais

Abaixo está uma descrição das principais tabelas do banco de dados:

### `usuarios`

Armazena os dados dos usuários que podem acessar o painel de administração.

| Coluna | Tipo | Descrição |
| --- | --- | --- |
| `id` | `INT` (PK) | Identificador único do usuário. |
| `name` | `VARCHAR(100)` | Nome do usuário. |
| `email` | `VARCHAR(120)` | E-mail de login (único). |
| `password` | `VARCHAR(255)` | Senha criptografada. |
| `role` | `ENUM('admin','editor','viewer')` | Nível de permissão do usuário. |

### `equipes`

Armazena as informações das equipes que participam do campeonato.

| Coluna | Tipo | Descrição |
| --- | --- | --- |
| `id` | `INT` (PK) | Identificador único da equipe. |
| `nome` | `VARCHAR(100)` | Nome da equipe. |
| `pais` | `VARCHAR(80)` | País de origem da equipe. |
| `...` | | (e outros campos) |

### `pilotos`

Armazena os dados dos pilotos. Cada piloto pertence a uma equipe.

| Coluna | Tipo | Descrição |
| --- | --- | --- |
| `id` | `INT` (PK) | Identificador único do piloto. |
| `nome` | `VARCHAR(100)` | Nome do piloto. |
| `numero` | `INT` | Número do carro do piloto. |
| `equipe_id` | `INT` (FK) | Chave estrangeira para a tabela `equipes`. |
| `...` | | (e outros campos) |

### `circuitos`

Armazena as informações sobre os circuitos onde as corridas acontecem.

| Coluna | Tipo | Descrição |
| --- | --- | --- |
| `id` | `INT` (PK) | Identificador único do circuito. |
| `nome` | `VARCHAR(100)` | Nome do circuito. |
| `localizacao`| `VARCHAR(100)`| Cidade/Região do circuito. |
| `...` | | (e outros campos) |

### `corridas`

Agenda as corridas da temporada, associando um circuito a uma data.

| Coluna | Tipo | Descrição |
| --- | --- | --- |
| `id` | `INT` (PK) | Identificador único da corrida. |
| `nome_gp` | `VARCHAR(100)` | Nome oficial do Grande Prêmio. |
| `circuito_id`| `INT` (FK) | Chave estrangeira para a tabela `circuitos`. |
| `data` | `DATE` | Data da corrida. |
| `status` | `ENUM(...)` | Status da corrida (agendada, finalizada, etc.). |

### `resultados`

Registra o resultado final de cada piloto em uma determinada corrida. É a partir desta tabela que a classificação geral é calculada.

| Coluna | Tipo | Descrição |
| --- | --- | --- |
| `id` | `INT` (PK) | Identificador único do registro de resultado. |
| `corrida_id` | `INT` (FK) | Chave estrangeira para `corridas`. |
| `piloto_id` | `INT` (FK) | Chave estrangeira para `pilotos`. |
| `equipe_id` | `INT` (FK) | Chave estrangeira para `equipes`. |
| `posicao` | `INT` | Posição de chegada do piloto. |
| `pontos` | `INT` | Pontos conquistados na corrida. |
| `...` | | (e outros campos) |
