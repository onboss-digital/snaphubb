# 🧪 Testes Práticos - API REST Snaphubb

## 📌 Pré-requisitos
- Laravel rodando em `http://127.0.0.1:8002`
- Banco de dados com dados de teste populados
- Postman ou similar para testar APIs

---

## 🔐 Autenticação

### Obter Token (Sanctum)

**POST** `/api/login`

```json
{
  "email": "active@example.com",
  "password": "password123"
}
```

**Response:**
```json
{
  "token": "sua_api_token_aqui"
}
```

Use este token em todas as requisições subsequentes adicionando o header:
```
Authorization: Bearer sua_api_token_aqui
```

---

## 👤 Endpoints de Usuário

### 1. Listar Usuários
**GET** `/api/users`

**Headers:**
```
Authorization: Bearer {token}
Accept: application/json
```

**Response (200 OK):**
```json
{
  "data": [
    {
      "id": 1,
      "first_name": "João",
      "last_name": "Ativo",
      "email": "active@example.com",
      "username": "joao_ativo",
      "is_subscribe": 1,
      "status": 1,
      "created_at": "2024-01-01T10:00:00.000000Z",
      "updated_at": "2024-01-01T10:00:00.000000Z"
    }
  ],
  "links": { "first": "...", "last": "..." },
  "meta": { "current_page": 1, "total": 100 }
}
```

### 2. Obter Usuário Específico
**GET** `/api/users/{id}`

**Headers:**
```
Authorization: Bearer {token}
Accept: application/json
```

**Response (200 OK):**
```json
{
  "data": {
    "id": 1,
    "first_name": "João",
    "last_name": "Ativo",
    "email": "active@example.com",
    "mobile": "5511999999999",
    "gender": "male",
    "status": 1,
    "is_subscribe": 1,
    "subscriptions": [
      {
        "id": 1,
        "status": "active",
        "plan_id": 2,
        "start_date": "2024-12-01",
        "end_date": "2025-01-01",
        "amount": 19.99,
        "total_amount": 23.49
      }
    ]
  }
}
```

### 3. Criar Novo Usuário
**POST** `/api/users`

**Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
```

**Body:**
```json
{
  "first_name": "Novo",
  "last_name": "Usuário",
  "email": "novo@example.com",
  "username": "novo_user",
  "password": "SenhaForte123!",
  "password_confirmation": "SenhaForte123!",
  "mobile": "5511988888888",
  "gender": "male",
  "status": 1
}
```

**Response (201 Created):**
```json
{
  "data": {
    "id": 101,
    "first_name": "Novo",
    "last_name": "Usuário",
    "email": "novo@example.com",
    "message": "Usuário criado com sucesso"
  }
}
```

### 4. Atualizar Usuário
**PUT** `/api/users/{id}`

**Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
```

**Body:**
```json
{
  "first_name": "Novo Atualizado",
  "mobile": "5511977777777",
  "status": 1
}
```

**Response (200 OK):**
```json
{
  "data": {
    "id": 101,
    "first_name": "Novo Atualizado",
    "message": "Usuário atualizado com sucesso"
  }
}
```

### 5. Deletar Usuário
**DELETE** `/api/users/{id}`

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200 OK):**
```json
{
  "message": "Usuário deletado com sucesso"
}
```

---

## 💳 Endpoints de Assinatura

### 1. Listar Assinaturas do Usuário
**GET** `/api/users/{user_id}/subscriptions`

**Headers:**
```
Authorization: Bearer {token}
Accept: application/json
```

**Response (200 OK):**
```json
{
  "data": [
    {
      "id": 1,
      "user_id": 1,
      "plan_id": 2,
      "status": "active",
      "start_date": "2024-12-01T00:00:00.000000Z",
      "end_date": "2025-01-01T00:00:00.000000Z",
      "amount": 19.99,
      "discount_percentage": 0,
      "tax_amount": 3.50,
      "total_amount": 23.49,
      "type": "monthly",
      "duration": 30,
      "plan": {
        "id": 2,
        "name": "Premium",
        "price": 19.99,
        "features": ["ads": false, "hd": true]
      }
    }
  ]
}
```

### 2. Obter Assinatura Específica
**GET** `/api/subscriptions/{id}`

**Headers:**
```
Authorization: Bearer {token}
Accept: application/json
```

**Response (200 OK):**
```json
{
  "data": {
    "id": 1,
    "user_id": 1,
    "plan_id": 2,
    "status": "active",
    "start_date": "2024-12-01",
    "end_date": "2025-01-01",
    "amount": 19.99,
    "total_amount": 23.49,
    "user": {
      "id": 1,
      "full_name": "João Ativo",
      "email": "active@example.com"
    }
  }
}
```

### 3. Criar Nova Assinatura
**POST** `/api/users/{user_id}/subscriptions`

**Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
```

**Body:**
```json
{
  "plan_id": 1,
  "start_date": "2025-01-03",
  "end_date": "2025-02-03",
  "amount": 29.99,
  "discount_percentage": 0,
  "tax_amount": 5.25,
  "total_amount": 35.24,
  "type": "monthly",
  "duration": 30
}
```

**Response (201 Created):**
```json
{
  "data": {
    "id": 102,
    "user_id": 1,
    "status": "active",
    "plan_id": 1,
    "message": "Assinatura criada com sucesso"
  }
}
```

### 4. Atualizar Status da Assinatura
**PUT** `/api/subscriptions/{id}`

**Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
```

**Body (Exemplo 1: Marcar como Expirada):**
```json
{
  "status": "expired"
}
```

**Body (Exemplo 2: Renovar):**
```json
{
  "status": "active",
  "start_date": "2025-01-03",
  "end_date": "2025-02-03"
}
```

**Body (Exemplo 3: Cancelar):**
```json
{
  "status": "cancelled"
}
```

**Response (200 OK):**
```json
{
  "data": {
    "id": 102,
    "status": "expired",
    "message": "Assinatura atualizada com sucesso"
  }
}
```

### 5. Listar Assinaturas por Status
**GET** `/api/subscriptions?status=active`

**Headers:**
```
Authorization: Bearer {token}
Accept: application/json
```

**Query Parameters:**
```
status=active|expired|cancelled
plan_id=1
user_id=1
sort=-created_at
```

**Response (200 OK):**
```json
{
  "data": [
    { "id": 1, "status": "active", "user_id": 1, ... },
    { "id": 2, "status": "active", "user_id": 3, ... },
    { "id": 4, "status": "active", "user_id": 5, ... }
  ],
  "meta": {
    "total": 45,
    "count": 10,
    "per_page": 10,
    "current_page": 1
  }
}
```

---

## 📊 Endpoints de Relatórios

### 1. Estatísticas de Assinaturas
**GET** `/api/subscriptions/statistics`

**Headers:**
```
Authorization: Bearer {token}
Accept: application/json
```

**Response (200 OK):**
```json
{
  "data": {
    "total_subscriptions": 45,
    "active_subscriptions": 30,
    "expired_subscriptions": 10,
    "cancelled_subscriptions": 5,
    "total_revenue": 1250.50,
    "active_revenue": 950.00,
    "average_subscription_value": 27.78,
    "subscriptions_by_plan": {
      "1": { "name": "Basic", "count": 20 },
      "2": { "name": "Premium", "count": 18 },
      "3": { "name": "Pro", "count": 7 }
    }
  }
}
```

### 2. Usuários Próximos de Expirar
**GET** `/api/subscriptions/expiring-soon?days=7`

**Headers:**
```
Authorization: Bearer {token}
Accept: application/json
```

**Response (200 OK):**
```json
{
  "data": [
    {
      "user_id": 1,
      "user_name": "João Ativo",
      "email": "active@example.com",
      "subscription_id": 1,
      "days_remaining": 5,
      "end_date": "2025-01-08",
      "plan_name": "Premium"
    },
    {
      "user_id": 3,
      "user_name": "Ana Multi",
      "email": "multi@example.com",
      "subscription_id": 3,
      "days_remaining": 3,
      "end_date": "2025-01-06",
      "plan_name": "Premium"
    }
  ]
}
```

---

## 🧪 Cenários de Teste Completos

### Cenário 1: Fluxo Completo de Um Novo Usuário

```bash
# 1. Autenticar como admin
curl -X POST http://127.0.0.1:8002/api/login \
  -H "Content-Type: application/json" \
  -d '{"email": "admin@example.com", "password": "password123"}'

# 2. Criar novo usuário
curl -X POST http://127.0.0.1:8002/api/users \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "first_name": "Teste",
    "last_name": "Fluxo",
    "email": "teste.fluxo@example.com",
    "password": "password123",
    "mobile": "5511999999999"
  }'

# 3. Criar assinatura para o novo usuário
curl -X POST http://127.0.0.1:8002/api/users/102/subscriptions \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "plan_id": 2,
    "start_date": "2025-01-03",
    "end_date": "2025-02-03",
    "amount": 19.99,
    "tax_amount": 3.50,
    "total_amount": 23.49
  }'

# 4. Verificar assinatura
curl -X GET http://127.0.0.1:8002/api/subscriptions/102 \
  -H "Authorization: Bearer {token}"

# 5. Atualizar para expirada
curl -X PUT http://127.0.0.1:8002/api/subscriptions/102 \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{"status": "expired"}'

# 6. Renovar assinatura
curl -X PUT http://127.0.0.1:8002/api/subscriptions/102 \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "status": "active",
    "start_date": "2025-01-03",
    "end_date": "2025-02-03"
  }'
```

### Cenário 2: Validar Integridade de Dados

```bash
# Obter estatísticas
curl -X GET http://127.0.0.1:8002/api/subscriptions/statistics \
  -H "Authorization: Bearer {token}"

# Listar assinaturas ativas
curl -X GET "http://127.0.0.1:8002/api/subscriptions?status=active" \
  -H "Authorization: Bearer {token}"

# Listar assinaturas próximas de expirar
curl -X GET "http://127.0.0.1:8002/api/subscriptions/expiring-soon?days=7" \
  -H "Authorization: Bearer {token}"
```

---

## ✅ Códigos de Status HTTP Esperados

| Código | Significado | Ação |
|--------|------------|------|
| 200 | OK | Requisição bem-sucedida |
| 201 | Created | Recurso criado com sucesso |
| 400 | Bad Request | Dados inválidos - verificar body |
| 401 | Unauthorized | Token inválido ou expirado |
| 403 | Forbidden | Sem permissão para acessar |
| 404 | Not Found | Recurso não encontrado |
| 422 | Unprocessable Entity | Erro de validação |
| 500 | Server Error | Erro no servidor |

---

## 🔍 Dicas de Debugging

### 1. Ativar Query Logging

No arquivo `.env`:
```
DB_LOG=true
```

Depois, no Tinker:
```php
php artisan tinker

DB::enableQueryLog();
// ... executa as queries ...
dd(DB::getQueryLog());
```

### 2. Verificar Relacionamentos

```php
$user = User::with('subscriptions')->find(1);
$subscriptions = $user->subscriptions;
dd($subscriptions);
```

### 3. Validar Timestamps

```php
$sub = Subscription::find(1);
echo "Criada: " . $sub->created_at;
echo "Atualizada: " . $sub->updated_at;
echo "Expirou: " . $sub->end_date;
```

---

**Última atualização:** Janeiro 2026
