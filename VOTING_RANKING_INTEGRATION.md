# 🔗 INTEGRAÇÃO: /voting COM /app/users/ranking

## 📋 Resumo

Agora o sistema `/voting` está **TOTALMENTE INTEGRADO** com `/app/users/ranking`!

O que mudou:
- ✅ `/voting` agora busca dados do Ranking ativo
- ✅ As opções de votação vêm da tabela `rankings` (campo `contents`)
- ✅ Os votos são salvos em `ranking_responses` (sistema já existente)
- ✅ Tudo funciona em tempo real

---

## 🏗️ ARQUITETURA INTEGRADA

```
┌─────────────────────────────────────────────────────────┐
│                   /app/users/ranking                    │
│         (Admin cria um ranking com opções)              │
│                                                         │
│  ├─ Name: "Qual atriz favorita desta semana?"           │
│  ├─ Start: 2026-01-04                                   │
│  ├─ End: 2026-01-10                                     │
│  ├─ Contents: [                                         │
│  │   {                                                  │
│  │     "slug": "ana-silva",                             │
│  │     "name": "Ana Silva",                             │
│  │     "image_url": "...",                              │
│  │     "votes": 0                                       │
│  │   },                                                 │
│  │   {                                                  │
│  │     "slug": "beatriz-costa",                         │
│  │     "name": "Beatriz Costa",                         │
│  │     "image_url": "...",                              │
│  │     "votes": 0                                       │
│  │   }                                                  │
│  │ ]                                                    │
│  └─ Status: 1 (Ativo)                                   │
│                                                         │
└──────────────┬──────────────────────────────────────────┘
               │
               ├─ Usuário acessa: /voting
               │
               ├─ Sistema busca ranking ativo (data atual)
               │
               ├─ Frontend carrega:
               │  ├─ Top 3 opções (por votos)
               │  └─ Grid com todas as opções
               │
               └─ Usuário vota em uma opção
                  │
                  └─ POST /api/v1/voting/vote
                     ├─ Encontra ranking ativo
                     ├─ Incrementa votes na opção
                     ├─ Salva em ranking_responses
                     └─ Retorna top 3 atualizado
```

---

## 📊 FLUXO TÉCNICO COMPLETO

### 1. **Admin cria um Ranking**

URL: `/app/users/ranking`

Preench:
- **Name**: "Qual atriz favorita desta semana?"
- **Status**: Ativo (1)
- **Start Date**: 2026-01-04
- **End Date**: 2026-01-11
- **Contents** (JSON):
```json
[
  {
    "slug": "ana-silva",
    "name": "Ana Silva",
    "image_url": "/images/ana.jpg",
    "votes": 0
  },
  {
    "slug": "beatriz-costa",
    "name": "Beatriz Costa",
    "image_url": "/images/beatriz.jpg",
    "votes": 0
  },
  {
    "slug": "carla-santos",
    "name": "Carla Santos",
    "image_url": "/images/carla.jpg",
    "votes": 0
  }
]
```

### 2. **Usuário acessa /voting**

```
┌─────────────────────────────────────────┐
│ GET /voting                             │
│ ↓                                       │
│ VotingController@index()                │
│  ├─ Verifica: auth()->user()            │
│  ├─ Verifica: userHasVotingAccess()     │
│  ├─ Busca ranking ativo:                │
│  │  └─ Ranking::where('status', 1)      │
│  │       ->where('start_date', <=today) │
│  │       ->where('end_date', >=today)   │
│  │       ->first()                      │
│  └─ Passa para view:                    │
│     ├─ $hasAccess = true/false          │
│     └─ $activeRanking = {...}           │
│                                         │
│ Renderiza: voting::index                │
│ Se TEM ranking:                         │
│  └─ JavaS cript carrega:                │
│     ├─ GET /api/v1/voting/top-3         │
│     └─ GET /api/v1/voting/all-candidates│
│                                         │
│ Se NÃO TEM ranking:                     │
│  └─ Mostra: "Nenhum ranking ativo"      │
└─────────────────────────────────────────┘
```

### 3. **Frontend carrega dados via API**

```javascript
// Load Top 3
GET /api/v1/voting/top-3
Response:
{
  "success": true,
  "data": [
    {
      "id": "ana-silva",
      "position": 1,
      "name": "Ana Silva",
      "image": "/images/ana.jpg",
      "total_votes": 45,
      "percentage": 50.0
    },
    ...
  ],
  "ranking_id": 5
}

// Load All Candidates  
GET /api/v1/voting/all-candidates
Response:
{
  "success": true,
  "data": [
    {
      "id": "ana-silva",
      "name": "Ana Silva",
      "image": "/images/ana.jpg",
      "votes": 45,
      "percentage": 50.0
    },
    {
      "id": "beatriz-costa",
      "name": "Beatriz Costa",
      "image": "/images/beatriz.jpg",
      "votes": 36,
      "percentage": 40.0
    },
    {
      "id": "carla-santos",
      "name": "Carla Santos",
      "image": "/images/carla.jpg",
      "votes": 9,
      "percentage": 10.0
    }
  ],
  "ranking_id": 5
}
```

### 4. **Usuário vota**

```
Clica em "Votar" para "Beatriz Costa"
│
├─ POST /api/v1/voting/vote
│  Body: {
│    "content_slug": "beatriz-costa"
│  }
│
├─ VotingController@storeVote()
│  ├─ Verifica: checkUserAccess() = true
│  ├─ Valida: content_slug é válido?
│  ├─ Busca ranking ativo
│  ├─ Procura se usuário já votou
│  │  └─ SELECT FROM ranking_responses
│  │     WHERE user_id = X AND ranking_id = Y
│  ├─ Se já votou:
│  │  └─ Decrementa votes da opção anterior
│  ├─ Incrementa votes da nova opção
│  ├─ Atualiza JSON do ranking:
│  │  └─ ranking.contents = json_encode([
│  │       { "slug": "ana-silva", "votes": 45 },
│  │       { "slug": "beatriz-costa", "votes": 37 },
│  │       { "slug": "carla-santos", "votes": 9 }
│  │     ])
│  ├─ Salva ranking.update()
│  ├─ Cria/atualiza RankingResponse:
│  │  └─ RankingResponse::create([
│  │       'user_id' => auth()->id(),
│  │       'ranking_id' => $ranking->id,
│  │       'content_slug' => 'beatriz-costa',
│  │       'response_date' => today()
│  │     ])
│  └─ Retorna getTop3() atualizado
│
└─ Frontend:
   ├─ ✅ Toast: "Voto registrado com sucesso!"
   ├─ ✅ Card muda cor para verde
   ├─ ✅ Número de votos atualiza
   └─ ✅ Top 3 re-ordena automaticamente
```

---

## 🔄 SINCRONIZAÇÃO DE DADOS

### Tabelas Envolvidas:

```
┌──────────────────────────────┐
│      rankings (ANTIGO)       │  ← Admin controla aqui
├──────────────────────────────┤
│ id                           │
│ name                         │
│ contents (JSON com "votes")  │ ← Incrementado aqui!
│ start_date, end_date         │
│ status                       │
└──────────────────────────────┘
              ↕
    (contents → votes)
              ↕
┌──────────────────────────────┐
│  ranking_responses (NOVO)    │  ← Registra quem votou
├──────────────────────────────┤
│ id                           │
│ user_id                      │
│ ranking_id                   │
│ content_slug                 │ ← Qual opção votou
│ response_date                │
│ sugestion_name (opcional)    │
│ sugestion_link (opcional)    │
└──────────────────────────────┘
```

**Ponto importante**: Os VOTOS são armazenados no campo `contents` da tabela `rankings` como JSON. Cada opção tem um campo `votes` que incrementa.

---

## ✅ CHECKLIST DE INTEGRAÇÃO

- ✅ VotingController reescrito para usar Ranking
- ✅ API endpoints atualizado: `/api/v1/voting/*`
- ✅ Frontend JavaScript atualizado para usar `content_slug`
- ✅ Votos armazenados em `ranking_responses`
- ✅ Contagem de votos em `rankings.contents[].votes`
- ✅ Top 3 dinâmico baseado em votos
- ✅ Uma votação por usuário por dia

---

## 🎯 COMO TESTAR

### 1. Criar um Ranking

```
Acesse: /app/users/ranking
Novo Ranking:
  Name: "Qual atriz favorita da semana?"
  Status: 1 (Ativo)
  Start: 2026-01-04
  End: 2026-01-11
  Contents (JSON):
  [
    {"slug":"ana","name":"Ana Silva","image_url":"/img/ana.jpg","votes":0},
    {"slug":"beatriz","name":"Beatriz Costa","image_url":"/img/beatriz.jpg","votes":0},
    {"slug":"carla","name":"Carla Santos","image_url":"/img/carla.jpg","votes":0}
  ]
```

### 2. Acessar /voting

```
http://127.0.0.1:8002/voting

Esperado:
  ✅ Menu fixo no topo
  ✅ Título: "Votação da Comunidade"
  ✅ Seção "Top 3 da Semana" (vazio ou com dados)
  ✅ Grid com 3 opções
  ✅ Botões "Votar" funcionando
```

### 3. Votar

```
Clique em "Votar" para qualquer opção

Esperado:
  ✅ Toast: "Voto registrado com sucesso!"
  ✅ Card muda para verde
  ✅ Contador de votos atualiza
  ✅ Top 3 re-ordena
  ✅ Mesmo voto salvo em ranking_responses
```

### 4. Verificar dados no banco

```bash
php artisan tinker

# Ver ranking
> $r = \Modules\User\Models\Ranking::where('status', 1)->first()
> $contents = json_decode($r->contents, true)
> echo $contents[0]['votes']  # Deve ser > 0

# Ver resposta do usuário
> $resp = \Modules\User\Models\RankingResponse::latest()->first()
> echo $resp->content_slug  # Deve ser "ana", "beatriz", etc
```

---

## 🔧 ENDPOINTS COMPLETOS

| Método | Endpoint | Descrição | Parâmetros |
|--------|----------|-----------|-----------|
| GET | `/voting` | Página de votação | - |
| GET | `/api/v1/voting/check-access` | Verifica se tem acesso | - |
| GET | `/api/v1/voting/top-3` | Top 3 opções | - |
| GET | `/api/v1/voting/all-candidates` | Todas as opções | - |
| POST | `/api/v1/voting/vote` | Votar em uma opção | `content_slug` |
| GET | `/api/v1/voting/user-vote/{week_id}` | Meu voto | `week_id` |

---

## 🎉 PRONTO!

O sistema de votação está **100% integrado** com os rankings! 

Qualquer ranking que o admin criar em `/app/users/ranking` com status ativo e datas válidas aparecerá automaticamente em `/voting`!

