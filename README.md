# Locadora de Carros API

API RESTful para gerenciamento completo de uma locadora de veículos, construída com Laravel 12. Oferece controle de frota, clientes, locações com máquina de estados, pagamentos, vistorias, multas, manutenções, alertas automatizados e dashboard com indicadores de negócio.

## Funcionalidades

- Autenticação via JWT (login, logout, refresh, usuário autenticado)
- CRUD completo de marcas, modelos, carros, clientes e locações
- Gestão de frota com combustível, câmbio, categoria e diária padrão
- Máquina de estados para locações (reservada → ativa → finalizada/cancelada)
- Vistorias de retirada e devolução com nível de combustível e km
- Controle de multas de trânsito por locação e cliente
- Gestão de manutenções preventivas e corretivas com controle de disponibilidade
- Pagamentos com tipos (diária, multa atraso, km extra, dano, desconto) e status
- Automação financeira via Jobs (cobranças, monitoramento de atrasos, alertas)
- Sistema de alertas com notificações automáticas (atrasos, manutenções, inadimplência)
- Dashboard com 12+ indicadores de negócio (faturamento, taxa de ocupação, inadimplência)
- Bloqueio de clientes com motivo e validação em novas locações
- Mascaramento de dados sensíveis (CPF, CNH) nas respostas da API
- Rate limiting (login: 5/min, API: 60/min)
- Validação de dados via DTOs com Spatie Laravel Data

## Stack

| Camada         | Tecnologia                                 |
|----------------|--------------------------------------------|
| Linguagem      | PHP 8.5                                    |
| Framework      | Laravel 12                                 |
| Banco de Dados | MySQL (latest)                             |
| Cache / Fila   | Redis                                      |
| Autenticação   | JWT (php-open-source-saver/jwt-auth)       |
| DTOs           | Spatie Laravel Data 4                      |
| Containers     | Docker Compose                             |
| Code Style     | Laravel Pint (PSR-12 strict)               |
| Análise        | PHPStan / Larastan (nível 5)               |
| Testes         | PHPUnit + Mockery (unitários + integração) |

## Requisitos

- Docker e Docker Compose

## Instalação

```bash
git clone <repo-url>
cd app_locadora_carros

cp .env.example .env

# Criar a rede compartilhada com o frontend
docker network create locadora_network

# Subir os containers
docker compose up -d

# Instalar dependências e configurar o banco
docker compose exec app composer install
docker compose exec app php artisan key:generate
docker compose exec app php artisan jwt:secret
docker compose exec app php artisan migrate --seed

# Gerar IDE Helper (opcional)
docker compose exec app php artisan ide-helper:generate
docker compose exec app php artisan ide-helper:models -N
```

Para recriar o banco do zero:

```bash
docker compose exec app php artisan migrate:fresh --seed
```

Após o setup, a API estará disponível em `http://localhost:8000`.

## Serviços

| Serviço    | URL / Porta          | Descrição                              |
|------------|----------------------|----------------------------------------|
| API        | http://localhost:8000 | Aplicação Laravel (Nginx + PHP-FPM)    |
| MySQL      | localhost:3306       | Banco de dados                         |
| Redis      | localhost:6379       | Cache, sessões e filas                 |
| Queue      | —                    | Worker de fila (financeiro, monitoramento) |

## Dados Iniciais (Seeder)

| Dado               | Detalhe                                              |
|--------------------|------------------------------------------------------|
| Usuário Admin      | admin@locadora.com / password                        |
| Usuários           | 3 usuários aleatórios                                |
| Marcas             | 10 (Toyota, VW, Chevrolet, Fiat, Honda, Hyundai, Ford, Renault, Nissan, Jeep) |
| Modelos            | Múltiplos por marca (Corolla, Gol, Onix, Uno, etc.) |
| Carros             | 1–3 por modelo (com placa, cor, combustível, câmbio) |
| Clientes           | 12 clientes (3 bloqueados)                           |
| Locações           | 5 finalizadas, 3 ativas, 2 reservadas               |
| Pagamentos         | 1–3 por locação finalizada                           |

---

## Endpoints

Todas as rotas (exceto login e refresh) requerem o header:

```
Authorization: Bearer {token}
```

Base URL: `/api`

### Autenticação

#### Login

```
POST /api/login
```

**Body:**

```json
{
  "email": "admin@locadora.com",
  "password": "password"
}
```

**Resposta** `200`:

```json
{
  "access_token": "eyJ0eXAi...",
  "token_type": "bearer",
  "expires_in": 3600
}
```

#### Refresh

```
POST /api/refresh
```

#### Logout

```
POST /api/v1/logout
```

#### Usuário autenticado

```
POST /api/v1/me
```

---

### Dashboard

| Método | URI                                | Descrição                    |
|--------|------------------------------------|------------------------------|
| GET    | `/api/v1/dashboard/resumo`         | Resumo geral com indicadores |
| GET    | `/api/v1/dashboard/locacoes-por-status` | Locações agrupadas por status |
| GET    | `/api/v1/dashboard/faturamento`    | Faturamento por período      |

**Resposta do resumo** `200`:

```json
{
  "data": {
    "total_clientes": 42,
    "total_carros": 35,
    "carros_disponiveis": 18,
    "carros_em_manutencao": 3,
    "locacoes_ativas": 14,
    "locacoes_atrasadas": 2,
    "taxa_ocupacao": 48.57,
    "total_multas_pendentes": 5,
    "valor_multas_pendentes": 3250.00,
    "total_a_receber": 12500.00,
    "total_recebido_mes": 45000.00,
    "manutencoes_proximas": 4,
    "alertas_nao_lidos": 7
  }
}
```

---

### Cliente

| Método      | URI                              | Descrição        |
|-------------|----------------------------------|------------------|
| GET         | `/api/v1/cliente`                | Listar clientes  |
| POST        | `/api/v1/cliente`                | Criar cliente    |
| GET         | `/api/v1/cliente/{id}`           | Buscar cliente   |
| PUT/PATCH   | `/api/v1/cliente/{id}`           | Atualizar cliente|
| DELETE      | `/api/v1/cliente/{id}`           | Deletar cliente  |

**Body (criar):**

```json
{
  "nome": "João Silva",
  "cpf": "123.456.789-00",
  "email": "joao@email.com",
  "telefone": "(11) 99999-9999",
  "data_nascimento": "1990-01-15",
  "cnh": "12345678901",
  "endereco": "Rua das Flores, 123",
  "cidade": "São Paulo",
  "estado": "SP",
  "cep": "01234-567"
}
```

**Validações:**
- `nome`: obrigatório, 3–255 caracteres
- `cpf`: obrigatório, formato XXX.XXX.XXX-XX, único
- `email`: obrigatório, formato de e-mail, único
- `telefone`: obrigatório, formato (XX) XXXXX-XXXX
- `cnh`: obrigatório, 11 dígitos, único
- `data_nascimento`: obrigatório, data válida
- `bloqueado`: boolean (apenas na atualização)
- `motivo_bloqueio`: obrigatório quando `bloqueado = true`

---

### Carro

| Método      | URI                                | Descrição                   |
|-------------|------------------------------------|-----------------------------|
| GET         | `/api/v1/carro`                    | Listar carros               |
| POST        | `/api/v1/carro`                    | Criar carro                 |
| GET         | `/api/v1/carro/{id}`               | Buscar carro                |
| PUT/PATCH   | `/api/v1/carro/{id}`               | Atualizar carro             |
| DELETE      | `/api/v1/carro/{id}`               | Deletar carro               |
| GET         | `/api/v1/carro/{id}/manutencao`    | Manutenções do carro        |

---

### Locação

| Método      | URI                                    | Descrição              |
|-------------|----------------------------------------|------------------------|
| GET         | `/api/v1/locacao`                      | Listar locações        |
| POST        | `/api/v1/locacao`                      | Criar locação          |
| GET         | `/api/v1/locacao/{id}`                 | Buscar locação         |
| PUT/PATCH   | `/api/v1/locacao/{id}`                 | Atualizar locação      |
| DELETE      | `/api/v1/locacao/{id}`                 | Deletar locação        |
| PATCH       | `/api/v1/locacao/{id}/iniciar`         | Iniciar locação        |
| PATCH       | `/api/v1/locacao/{id}/finalizar`       | Finalizar locação      |
| PATCH       | `/api/v1/locacao/{id}/cancelar`        | Cancelar locação       |

---

### Marca

| Método      | URI                        | Descrição        |
|-------------|----------------------------|------------------|
| GET         | `/api/v1/marca`            | Listar marcas    |
| POST        | `/api/v1/marca`            | Criar marca      |
| GET         | `/api/v1/marca/{id}`       | Buscar marca     |
| PUT/PATCH   | `/api/v1/marca/{id}`       | Atualizar marca  |
| DELETE      | `/api/v1/marca/{id}`       | Deletar marca    |

---

### Modelo

| Método      | URI                          | Descrição        |
|-------------|------------------------------|------------------|
| GET         | `/api/v1/modelo`             | Listar modelos   |
| POST        | `/api/v1/modelo`             | Criar modelo     |
| GET         | `/api/v1/modelo/{id}`        | Buscar modelo    |
| PUT/PATCH   | `/api/v1/modelo/{id}`        | Atualizar modelo |
| DELETE      | `/api/v1/modelo/{id}`        | Deletar modelo   |

---

### Pagamento

| Método      | URI                                    | Descrição                    |
|-------------|----------------------------------------|------------------------------|
| GET         | `/api/v1/pagamento`                    | Listar pagamentos            |
| POST        | `/api/v1/pagamento`                    | Criar pagamento              |
| GET         | `/api/v1/pagamento/{id}`               | Buscar pagamento             |
| PUT/PATCH   | `/api/v1/pagamento/{id}`               | Atualizar pagamento          |
| DELETE      | `/api/v1/pagamento/{id}`               | Deletar pagamento            |
| GET         | `/api/v1/locacao/{id}/pagamento`       | Pagamentos por locação       |

---

### Vistoria

| Método | URI                                    | Descrição                    |
|--------|----------------------------------------|------------------------------|
| POST   | `/api/v1/locacao/{id}/vistoria`        | Criar vistoria               |
| GET    | `/api/v1/locacao/{id}/vistoria`        | Vistorias da locação         |

---

### Multa

| Método      | URI                                    | Descrição                    |
|-------------|----------------------------------------|------------------------------|
| GET         | `/api/v1/multa`                        | Listar multas                |
| POST        | `/api/v1/multa`                        | Criar multa                  |
| GET         | `/api/v1/multa/{id}`                   | Buscar multa                 |
| PUT/PATCH   | `/api/v1/multa/{id}`                   | Atualizar multa              |
| DELETE      | `/api/v1/multa/{id}`                   | Deletar multa                |
| GET         | `/api/v1/locacao/{id}/multa`           | Multas por locação           |
| GET         | `/api/v1/cliente/{id}/multa`           | Multas por cliente           |

---

### Manutenção

| Método      | URI                                    | Descrição                    |
|-------------|----------------------------------------|------------------------------|
| GET         | `/api/v1/manutencao`                   | Listar manutenções           |
| POST        | `/api/v1/manutencao`                   | Criar manutenção             |
| GET         | `/api/v1/manutencao/{id}`              | Buscar manutenção            |
| PUT/PATCH   | `/api/v1/manutencao/{id}`              | Atualizar manutenção         |
| DELETE      | `/api/v1/manutencao/{id}`              | Deletar manutenção           |
| GET         | `/api/v1/manutencao/proximas`          | Manutenções próximas         |
| GET         | `/api/v1/carro/{id}/manutencao`        | Manutenções por carro        |

---

### Alerta

| Método | URI                                | Descrição                    |
|--------|------------------------------------|------------------------------|
| GET    | `/api/v1/alerta`                   | Listar alertas               |
| GET    | `/api/v1/alerta/count`             | Contagem de não lidos        |
| PATCH  | `/api/v1/alerta/{id}/lido`         | Marcar como lido             |
| PATCH  | `/api/v1/alerta/lidos`             | Marcar todos como lidos      |

---

## Enums

| Enum                 | Valores                                                                         |
|----------------------|---------------------------------------------------------------------------------|
| LocacaoStatusEnum    | `reservada`, `ativa`, `finalizada`, `cancelada`                                 |
| CombustivelEnum      | `flex`, `gasolina`, `etanol`, `diesel`, `eletrico`, `hibrido`                   |
| CambioEnum           | `manual`, `automatico`, `cvt`                                                   |
| CategoriaCarroEnum   | `economico`, `compacto`, `sedan`, `suv`, `pickup`, `luxo`, `van`                |
| PagamentoTipoEnum    | `diaria`, `multa_atraso`, `km_extra`, `dano`, `desconto`                        |
| PagamentoStatusEnum  | `pendente`, `pago`, `cancelado`                                                 |
| MetodoPagamentoEnum  | `dinheiro`, `credito`, `debito`, `pix`                                          |
| MultaStatusEnum      | `pendente`, `paga`, `contestada`, `cancelada`                                   |
| ManutencaoTipoEnum   | `preventiva`, `corretiva`, `revisao`                                            |
| ManutencaoStatusEnum | `agendada`, `em_andamento`, `concluida`, `cancelada`                            |
| VistoriaTipoEnum     | `retirada`, `devolucao`                                                         |
| CombustivelNivelEnum | `vazio`, `1_4`, `metade`, `3_4`, `cheio`                                        |
| AlertaTipoEnum       | `locacao_atrasada`, `manutencao_proxima`, `manutencao_vencida`, `multa_pendente`, `inadimplencia` |

## Jobs Automáticos

| Job                              | Schedule      | Descrição                                                              |
|----------------------------------|---------------|------------------------------------------------------------------------|
| VerificarLocacoesAtrasadasJob    | Diário 08:00  | Identifica locações ativas vencidas, marca como atrasada, cria alerta  |
| VerificarManutencoesProximasJob  | Diário 07:00  | Detecta manutenções nos próximos 7 dias, cria alerta                   |
| GerarCobrancasFinalizacaoJob     | Sob demanda   | Ao finalizar locação: gera pagamentos de diária, multa atraso, km extra |

## Variáveis de Ambiente

| Variável                         | Descrição                           | Valor Padrão              |
|----------------------------------|-------------------------------------|---------------------------|
| `APP_ENV`                        | Ambiente                            | `local`                   |
| `APP_URL`                        | URL base da aplicação               | `http://localhost:8000`   |
| `DB_CONNECTION`                  | Driver do banco                     | `mysql`                   |
| `DB_HOST`                        | Host do MySQL                       | `mysql`                   |
| `DB_PORT`                        | Porta do MySQL                      | `3306`                    |
| `DB_DATABASE`                    | Nome do banco                       | `locadora`                |
| `DB_USERNAME`                    | Usuário do banco                    | `laravel`                 |
| `DB_PASSWORD`                    | Senha do banco                      | `secret`                  |
| `REDIS_HOST`                     | Host do Redis                       | `redis`                   |
| `CACHE_DRIVER`                   | Driver de cache                     | `redis`                   |
| `QUEUE_CONNECTION`               | Driver da fila                      | `redis`                   |
| `SESSION_DRIVER`                 | Driver de sessão                    | `redis`                   |
| `CORS_ALLOWED_ORIGINS`           | Origens permitidas (separado por vírgula) | `http://localhost:5173` |
| `JWT_SECRET`                     | Chave secreta do JWT                | —                         |
| `JWT_TTL`                        | Tempo de vida do token (minutos)    | `60`                      |
| `JWT_REFRESH_TTL`                | Tempo para refresh (minutos)        | `20160`                   |
| `JWT_BLACKLIST_ENABLED`          | Habilitar blacklist de tokens       | `true`                    |
| `LOCADORA_MULTA_ATRASO_PERCENTUAL` | % de multa por atraso            | `2`                       |
| `LOCADORA_CUSTO_KM_EXTRA`       | Custo por km excedente              | `0.50`                    |
| `LOCADORA_KM_LIVRE_POR_DIA`     | Km livre por dia de locação         | `100`                     |

## Testes

```bash
# Todos os testes
docker compose exec app php artisan test

# Testes de um módulo específico
docker compose exec app php artisan test app/Api/Modules/Locacao

# Testes por grupo
docker compose exec app php artisan test --group=locacao
```

## Qualidade de Código

```bash
# Formatação (Laravel Pint — PSR-12 strict)
docker compose exec app ./vendor/bin/pint

# Análise estática (PHPStan nível 5 + Larastan)
docker compose exec app ./vendor/bin/phpstan analyse

# IDE helper
docker compose exec app php artisan ide-helper:generate
docker compose exec app php artisan ide-helper:models -N
```

## Arquitetura

O projeto segue uma **arquitetura modular** onde cada domínio é isolado em seu próprio módulo dentro de `app/Api/Modules/`. Cada módulo possui suas camadas:

```
app/Api/Modules/
├── Alerta/
│   ├── Controllers/        → AlertaController
│   ├── Data/               → AlertaQueryData (DTO)
│   ├── Enums/              → AlertaTipoEnum
│   ├── UseCases/           → GetAlertas, Count, MarcarLido, MarcarTodosLidos
│   ├── Repositories/       → AlertaRepository
│   ├── Resources/          → AlertaResource
│   └── Tests/              → Assertables, Enums, Integrations
├── Auth/
│   ├── Controllers/        → Login, Logout, Refresh, Me
│   ├── Data/               → LoginData
│   ├── UseCases/           → Login, Logout, Refresh, GetAuthenticatedUser
│   ├── Resources/          → AuthResource
│   └── Tests/              → Integrations
├── Carro/
│   ├── Controllers/        → CRUD
│   ├── Data/               → CreateCarroData, UpdateCarroData, CarroQueryData
│   ├── Enums/              → CombustivelEnum, CambioEnum, CategoriaCarroEnum
│   ├── UseCases/           → Create, Get, GetAll, Update, Delete
│   ├── Repositories/       → CarroRepository
│   ├── Resources/          → CarroResource
│   └── Tests/              → Data, UseCases, Integrations
├── Cliente/
│   ├── Controllers/        → CRUD
│   ├── Data/               → CreateClienteData, UpdateClienteData, ClienteQueryData
│   ├── UseCases/           → Create, Get, GetAll, Update, Delete
│   ├── Repositories/       → ClienteRepository (mascaramento CPF/CNH)
│   ├── Resources/          → ClienteResource
│   └── Tests/              → Data, UseCases, Integrations
├── Dashboard/
│   ├── Controllers/        → Resumo, LocacoesPorStatus, Faturamento
│   ├── UseCases/           → GetResumo, GetLocacoesPorStatus, GetFaturamento
│   ├── Repositories/       → DashboardRepository (12+ indicadores)
│   ├── Resources/          → ResumoResource, LocacoesPorStatusResource, FaturamentoResource
│   └── Tests/              → Assertables, UseCases, Integrations
├── Locacao/
│   ├── Controllers/        → CRUD + Iniciar, Finalizar, Cancelar
│   ├── Data/               → Create, Update, Finalizar, QueryData
│   ├── Enums/              → LocacaoStatusEnum (máquina de estados)
│   ├── Services/           → LocacaoService (cálculos, transições, dispatch Jobs)
│   ├── UseCases/           → Create, Get, GetAll, Update, Delete, Iniciar, Finalizar, Cancelar
│   ├── Repositories/       → LocacaoRepository
│   ├── Resources/          → LocacaoResource
│   └── Tests/              → Data, UseCases, Services, Integrations
├── Manutencao/
│   ├── Controllers/        → CRUD + ByCarro, Proximas
│   ├── Data/               → Create, Update, QueryData
│   ├── Enums/              → ManutencaoTipoEnum, ManutencaoStatusEnum
│   ├── Services/           → ManutencaoService (controle disponibilidade carro)
│   ├── UseCases/           → Create, Get, GetAll, ByCarro, Proximas, Update, Delete
│   ├── Repositories/       → ManutencaoRepository
│   ├── Resources/          → ManutencaoResource
│   └── Tests/              → Data, UseCases, Services, Integrations, Enums
├── Marca/
│   ├── Controllers/        → CRUD
│   ├── Data/               → Create, Update, QueryData
│   ├── UseCases/           → Create, Get, GetAll, Update, Delete
│   ├── Repositories/       → MarcaRepository
│   ├── Resources/          → MarcaResource
│   └── Tests/              → Data, UseCases, Integrations
├── Modelo/
│   ├── Controllers/        → CRUD
│   ├── Data/               → Create, Update, QueryData
│   ├── UseCases/           → Create, Get, GetAll, Update, Delete
│   ├── Repositories/       → ModeloRepository
│   ├── Resources/          → ModeloResource
│   └── Tests/              → Data, UseCases, Integrations
├── Multa/
│   ├── Controllers/        → CRUD + ByLocacao, ByCliente
│   ├── Data/               → Create, Update, QueryData
│   ├── Enums/              → MultaStatusEnum
│   ├── UseCases/           → Create, Get, GetAll, ByLocacao, ByCliente, Update, Delete
│   ├── Repositories/       → MultaRepository
│   ├── Resources/          → MultaResource
│   └── Tests/              → Data, UseCases, Integrations, Enums
├── Pagamento/
│   ├── Controllers/        → CRUD + ByLocacao
│   ├── Data/               → Create, Update, QueryData
│   ├── Enums/              → PagamentoStatusEnum, PagamentoTipoEnum, MetodoPagamentoEnum
│   ├── UseCases/           → Create, Get, GetAll, ByLocacao, Update, Delete
│   ├── Repositories/       → PagamentoRepository
│   ├── Resources/          → PagamentoResource
│   └── Tests/              → Data, UseCases, Integrations, Enums
└── Vistoria/
    ├── Controllers/        → Create, ListByLocacao
    ├── Data/               → CreateVistoriaData
    ├── Enums/              → VistoriaTipoEnum, CombustivelNivelEnum
    ├── Services/           → VistoriaService (regras: 1 retirada + 1 devolução por locação)
    ├── UseCases/           → Create, GetByLocacao
    ├── Repositories/       → VistoriaRepository
    ├── Resources/          → VistoriaResource
    └── Tests/              → Data, UseCases, Integrations, Enums
```

### Fluxo de uma requisição

```
Request → Controller → DTO (validação Spatie Data) → UseCase → [Service] → Repository → Resource
```

### Fluxo de uma locação

```
Criar (reservada) → Iniciar (ativa) → Finalizar (finalizada) → GerarCobrancasFinalizacaoJob
                                    → Cancelar (cancelada)
```

### Fluxo de automação financeira

```
Finalizar Locação → Dispatch GerarCobrancasFinalizacaoJob
  └─> Calcula diárias (dias × valor_diaria)
  └─> Calcula multa atraso (se devolveu atrasado)
  └─> Calcula km extra (se excedeu km livre)
  └─> Cria Pagamentos com status "pendente"

Scheduler (diário)
  └─> VerificarLocacoesAtrasadasJob → marca locações vencidas → cria Alerta
  └─> VerificarManutencoesProximasJob → detecta manutenções em 7 dias → cria Alerta
```

## Estrutura Docker

```
docker/
├── nginx/
│   └── default.conf        → Server block Laravel (PHP-FPM + headers de segurança)
└── php/
    └── php.ini              → Configuração PHP (upload, memória)
```

| Container        | Descrição                                          |
|------------------|----------------------------------------------------|
| `locadora_app`   | PHP-FPM — executa a aplicação Laravel              |
| `locadora_nginx` | Nginx — proxy reverso para o PHP-FPM               |
| `locadora_mysql` | MySQL — banco de dados com volume persistente      |
| `locadora_redis` | Redis Alpine — cache, sessões e fila               |
| `locadora_queue` | Worker de fila — processa jobs assíncronos          |
