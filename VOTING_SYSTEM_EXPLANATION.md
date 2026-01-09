# 📚 GUIA COMPLETO: Como Funciona o Sistema de Votação (/voting)

## 🎯 Resumo Executivo

Existem **DOIS sistemas de votação/ranking** NO PROJETO:

### 1️⃣ **Sistema ANTIGO: RankingModal** (em `/app/users/ranking`)
- **Onde está**: `Modules/User/Models/Ranking`
- **Como funciona**: Cria rankings TEMPORÁRIOS com datas de início/fim
- **Para quem**: Para usuários com plano específico
- **Dados**: Armazenados em `ranking_responses` (respostas dos usuários)
- **Modal**: Aparece uma vez por ranking (depois some)

### 2️⃣ **Sistema NOVO: Voting** (em `/voting`)
- **Onde está**: `Modules/Voting/Models/Vote` + `WeeklyRanking`
- **Como funciona**: Votação de atrizes que recalcula SEMANALMENTE
- **Para quem**: Usuários com acesso à feature "Community Voting"
- **Dados**: Baseado em `CastCrew` (atrizes do elenco)
- **Interface**: Página dedicada em `/voting`

---

## 🏗️ ARQUITETURA DO SISTEMA DE VOTAÇÃO (NOVO)

### Tabelas Envolvidas:

```
┌─────────────────────────────────────────────────────────────────┐
│                         cast_crew                               │
│  (Atrizes/Atores que podem receber votos)                      │
│  ┌─────────────────────────────────────────────────────────┐  │
│  │ id (PK)                                                 │  │
│  │ name         → Nome da atriz                           │  │
│  │ position     → 'actress', 'actor', etc                 │  │
│  │ status       → 1 (ativa) ou 0 (inativa)                │  │
│  │ created_at, updated_at                                 │  │
│  └─────────────────────────────────────────────────────────┘  │
│                            ↑ FK                                 │
└────────────────┬───────────────────────────┬───────────────────┘
                 │                           │
       ┌─────────↓───────────────┐  ┌────────↓────────────────────┐
       │       votes             │  │   weekly_rankings           │
       │ (Votos individuais)     │  │ (Rankings cacheados)        │
       ├─────────────────────────┤  ├─────────────────────────────┤
       │ id                      │  │ id                          │
       │ user_id (FK)            │  │ week_id (YYYY-WW)           │
       │ cast_crew_id (FK) ──────┤  │ cast_crew_id (FK) ──────────┤
       │ week_id (YYYY-WW)       │  │ rank_position (1/2/3)       │
       │ vote_count              │  │ total_votes (acumulado)     │
       │ created_at              │  │ percentage (% do total)     │
       │ updated_at              │  │ created_at                  │
       │ deleted_at              │  │ updated_at                  │
       └─────────────────────────┘  └─────────────────────────────┘
        Unique: (user_id,           Armazenado para
         cast_crew_id,              performance
         week_id)
```

---

## 🔄 FLUXO DE DADOS

### 1. **Usuário acessa /voting**

```
┌─────────────────────────────────────────────────────────────┐
│ GET /voting                                                 │
│ ↓                                                           │
│ VotingController@index()                                    │
│   ├─ Verifica: auth()->user() existe?                       │
│   ├─ Verifica: userHasVotingAccess()?                       │
│   │   └─ Query: Subscriptions onde status='active'          │
│   │       AND plan.identifier LIKE '%voting%'               │
│   ├─ Se TEM acesso:                                         │
│   │   └─ Renderiza view com hasAccess=true                  │
│   │       └─ Carrega JavaScript que chama APIs              │
│   └─ Se NÃO TEM acesso:                                     │
│       └─ Renderiza modal com "Comprar Acesso"               │
└─────────────────────────────────────────────────────────────┘
```

### 2. **Frontend faz chamadas AJAX para APIs**

```
┌──────────────────────────────────────────────────────────────┐
│ JavaScript do frontend chama:                                │
│                                                              │
│ 1️⃣ GET /api/v1/voting/top-3                                 │
│    └─ WeeklyRanking::currentWeek()->top3()                  │
│       └─ Retorna: [{ranking com fotos, votos, %}, ...]      │
│                                                              │
│ 2️⃣ GET /api/v1/voting/all-candidates                        │
│    └─ CastCrew::where('status', 1)->get()                   │
│       └─ Retorna: [{name, foto, votesCount}, ...]           │
│                                                              │
│ 3️⃣ POST /api/v1/voting/vote                                 │
│    └─ Body: {cast_crew_id: 5}                               │
│    └─ Cria/incrementa Vote::create(...)                     │
│    └─ Recalcula WeeklyRanking::update()                     │
│       └─ Retorna: {success: true}                           │
│                                                              │
│ 4️⃣ GET /api/v1/voting/user-vote/{week_id}                  │
│    └─ Vote::byUser()->currentWeek()                         │
│       └─ Retorna: {cast_crew_id, vote_count}                │
└──────────────────────────────────────────────────────────────┘
```

### 3. **Voter em uma atriz**

```
┌──────────────────────────────────────────────────────────────┐
│ Usuário clica em "Votar" para "Ana Silva"                   │
│ ↓                                                            │
│ JavaScript dispara:                                          │
│   POST /api/v1/voting/vote                                  │
│   {cast_crew_id: 1}                                          │
│ ↓                                                            │
│ VotingController@storeVote()                                │
│   ├─ Verifica acesso: checkUserAccess()                      │
│   ├─ Valida: cast_crew_id existe?                            │
│   ├─ Cria/atualiza: Vote::create() ou ::update()             │
│   ├─ Recalcula: updateWeeklyRanking()                        │
│   │   ├─ SELECT SUM(vote_count) FROM votes                   │
│   │   │         WHERE week_id = YYYY-WW                      │
│   │   ├─ Calcula: percentage = (votos_atriz / total) * 100   │
│   │   └─ Atualiza WeeklyRanking com novo rank               │
│   └─ Retorna: {success: true, ranking: {...}}               │
│ ↓                                                            │
│ Frontend mostra:                                            │
│   ✅ Toast: "Voto registrado com sucesso!"                  │
│   ✅ Card muda cor para VERDE                               │
│   ✅ Top 3 atualiza (GET /api/v1/voting/top-3 novamente)   │
└──────────────────────────────────────────────────────────────┘
```

---

## 📊 RELAÇÃO COM /app/users/ranking

### ❓ Pergunta: Há integração entre /voting e /app/users/ranking?

**RESPOSTA: NÃO, são sistemas INDEPENDENTES.**

```
┌────────────────────────────────────────────────────────────────┐
│  /app/users/ranking                                            │
│  ├─ Model: Modules\User\Models\Ranking                         │
│  ├─ Tabela: rankings                                           │
│  ├─ Dados: Conteúdo JSON customizado (contents column)         │
│  ├─ Características:                                           │
│  │  ├─ Datas: start_date até end_date (TEMPORÁRIO)            │
│  │  ├─ Status: Ativo ou inativo                               │
│  │  ├─ Ligado a planos específicos                            │
│  │  ├─ Aparece como MODAL na página                           │
│  │  └─ Cada usuário responde UMA VEZ                          │
│  │                                                            │
│  └─ USO: Enquetes/rankings customizados TEMPORÁRIOS            │
│                                                                │
│     (Ex: "Qual será o episódio mais assistido?")              │
│                                                                │
└────────────────────────────────────────────────────────────────┘
              VERSUS
┌────────────────────────────────────────────────────────────────┐
│  /voting (NOVO SISTEMA)                                        │
│  ├─ Model: Modules\Voting\Models\Vote                          │
│  ├─ Tabelas: votes, weekly_rankings                            │
│  ├─ Dados: Baseado em CastCrew (atrizes reais)                │
│  ├─ Características:                                           │
│  │  ├─ Datas: SEMANAL (YYYY-WW)                               │
│  │  ├─ Permanente: Recalcula toda semana                      │
│  │  ├─ Ligado a plano "community-voting"                      │
│  │  ├─ Aparece como PÁGINA DEDICADA                           │
│  │  └─ Cada usuário pode votar MÚLTIPLAS VEZES                │
│  │                                                            │
│  └─ USO: Votação de ATRIZES FAVORITAS (contínua)             │
│                                                                │
│     (Ex: "Qual atriz é sua favorita desta semana?")           │
│                                                                │
└────────────────────────────────────────────────────────────────┘
```

---

## ⚙️ COMO OS DADOS SÃO ALIMENTADOS

### Opção 1: AUTOMÁTICO (Recomendado)
O sistema usa atrizes já cadastradas em:
- **Localização**: `/app/cast-crew`
- **Modelo**: `Modules\CastCrew\Models\CastCrew`
- **Campos**: `name`, `position`, `status`, `bio`, etc
- **Como funciona**: 
  - Qualquer atriz com `status=1` aparece automaticamente em `/voting`
  - Quando usuário vota, é criada entrada em `votes` table
  - Sistema recalcula `weekly_rankings` automaticamente

### Opção 2: MANUAL (Via Script)

Se o banco estiver vazio, crie dados de teste:

```bash
php create_test_castcrew.php
```

Ou via `tinker`:

```bash
php artisan tinker

$cast = \Modules\CastCrew\Models\CastCrew::create([
    'name' => 'Ana Silva',
    'position' => 'actress',
    'bio' => 'Atriz premiada',
    'status' => 1
]);
```

---

## 🔧 FLUXO TÉCNICO COMPLETO

### Step 1: Preparar dados base

```bash
# Verificar se existem atrizes
php artisan tinker
> \Modules\CastCrew\Models\CastCrew::count()
=> 0  # ❌ Precisa criar

# Criar 5 atrizes de teste
> for($i = 1; $i <= 5; $i++) {
    \Modules\CastCrew\Models\CastCrew::create([
        'name' => 'Atriz #' . $i,
        'position' => 'actress',
        'status' => 1,
        'bio' => 'Bio teste'
    ]);
}
```

### Step 2: Garantir acesso do usuário

```bash
# Verificar subscription do usuário
php artisan tinker
> $user = \App\Models\User::where('email', 'assinante@test.com')->first();
> $user->subscriptions()->where('status', 'active')->get();

# Se não tem, criar
> \Modules\Subscriptions\Models\Subscription::create([
    'user_id' => $user->id,
    'plan_id' => 3,  # ID do plano "Community Voting"
    'status' => 'active',
    'amount' => 9.90,
    'name' => 'Community Voting',
    'identifier' => 'community-voting',
    'type' => 'monthly'
]);
```

### Step 3: Testar acesso

```bash
# Acessar /voting
# Verificar: Vê a interface (não o modal)?

# Via console do navegador:
fetch('/api/v1/voting/all-candidates')
  .then(r => r.json())
  .then(d => console.log(d.data))
```

### Step 4: Votar

```javascript
// No console do navegador:
fetch('/api/v1/voting/vote', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
  },
  body: JSON.stringify({cast_crew_id: 1})
})
.then(r => r.json())
.then(d => console.log(d))
```

---

## 🎬 Resumo: Como tudo funciona junto

```
┌─────────────────────────────────────────────────────────────────┐
│                        USUÁRIO FINAL                            │
└────────────────┬────────────────────────────────────────────────┘
                 │
                 ├─ Acessa /voting
                 │  ↓
                 │  ✅ Autenticado + tem acesso? → Vê interface
                 │  ❌ Não tem acesso? → Vê modal "Comprar"
                 │
                 ├─ Clica em atriz para votar
                 │  ↓
                 │  POST /api/v1/voting/vote {cast_crew_id: X}
                 │  ↓
                 │  Backend:
                 │    1. Verifica acesso
                 │    2. Cria/atualiza Vote
                 │    3. Recalcula WeeklyRanking
                 │  ↓
                 │  ✅ "Voto registrado!"
                 │  ✅ Top 3 atualiza
                 │  ✅ Card muda cor
                 │
                 └─ Semana termina
                    ↓
                    Cron job (ou manual):
                    updateWeeklyRanking() roda
                    ↓
                    Próxima semana começa com novo ranking
```

---

## 📋 CHECKLIST DE INTEGRAÇÃO

- ✅ Atrizes cadastradas em `/app/cast-crew`
- ✅ Plano "Community Voting" criado
- ✅ Usuário tem subscription ativa
- ✅ Rotas registradas: `/voting`, `/api/v1/voting/*`
- ✅ Helper `userHasVotingAccess()` funcionando
- ✅ Menu item "Votação" aparecendo

---

## 🚀 PRÓXIMOS PASSOS (OPCIONAIS)

Se quiser expandir o sistema:

1. **Adicionar fotos de atrizes**: Integrar com `Spatie\MediaLibrary`
2. **Enviar notificação**: Email quando atriz fica #1
3. **Prêmios/Pontos**: Dar pontos para quem votou no #1
4. **Histórico**: Permitir ver rankings das semanas passadas
5. **Leaderboard**: Top 10 votadores

---

## 📞 DÚVIDAS?

Se tiver dúvidas sobre qualquer parte, é só me avisar!
