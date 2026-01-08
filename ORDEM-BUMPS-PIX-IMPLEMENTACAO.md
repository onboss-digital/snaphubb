# 🚀 Order Bumps para PIX - Implementação Completa

## ✅ O que foi implementado

### **Frontend (snaphubb-pages)**
✅ Remover restrição de PIX - bumps agora aparecem para AMBOS os métodos  
✅ O mesmo design de cards funciona para cartão E PIX

### **Backend (snaphubb)**
✅ PixController: Adicionar bumps na requisição PIX  
✅ WebHookController: Registrar bumps quando PIX é confirmado  
✅ Tabela nova: `user_purchased_bumps` para rastrear compras

---

## 🔧 PRÓXIMOS PASSOS

### **Passo 1: Rodar as Migrations**

No **backend (snaphubb)**:

```bash
php artisan migrate
```

Isso vai criar a tabela `user_purchased_bumps` que armazena quais bumps cada usuário comprou.

---

### **Passo 2: Entender o Fluxo Novo**

#### **Antes (apenas Cartão):**
```
Frontend:
1. Seleciona plano + bumps
2. Envia para Stripe (valor total)
3. Stripe processa tudo

Backend:
- Cria assinatura do plano
- (bumps não eram rastreados)
```

#### **Depois (Cartão + PIX):**
```
Frontend:
1. Seleciona plano + bumps
2. Envia para Stripe OU PIX (valor total + ids dos bumps)

Backend:
- Cria assinatura do plano
- Registra na tabela user_purchased_bumps quais bumps foram comprados
- Email pode informar quais add-ons foram adquiridos
```

---

## 📊 ESTRUTURA DOS DADOS

### **Enviado do Frontend para PIX:**

```json
{
  "amount": 39.98,
  "currency_code": "BRL",
  "plan_key": "premium-monthly",
  "bumps": [4],  // IDs dos bumps selecionados
  "customer": {
    "name": "João Silva",
    "email": "joao@example.com",
    ...
  }
}
```

### **Metadata do PIX (armazenada):**

```json
{
  "bumps": [4]  // Quais bumps foram adquiridos
}
```

### **Webhook quando PIX é pago:**

A metadata vem de volta no webhook com os IDs dos bumps, e o backend:
1. Cria assinatura do plano
2. Registra cada bump em `user_purchased_bumps`

---

## 🗄️ TABELA: user_purchased_bumps

```sql
CREATE TABLE user_purchased_bumps (
  id BIGINT PRIMARY KEY AUTO_INCREMENT,
  user_id BIGINT NOT NULL FOREIGN KEY,
  bump_id BIGINT NOT NULL FOREIGN KEY,
  plan_id BIGINT NULLABLE FOREIGN KEY,
  purchased_at TIMESTAMP,
  created_at TIMESTAMP,
  updated_at TIMESTAMP,
  
  UNIQUE(user_id, bump_id),
  INDEX(user_id),
  INDEX(bump_id)
);
```

**Exemplos de dados:**
```
| id | user_id | bump_id | plan_id | purchased_at        |
|----|---------|---------|---------|-------------------|
| 1  | 123     | 4       | 1       | 2025-01-07 10:00  |
| 2  | 124     | 4       | 1       | 2025-01-07 10:15  |
| 3  | 124     | 5       | 1       | 2025-01-07 10:15  |
```

---

## 🔍 COMO RASTREAR BUMPS DO USUÁRIO

### **Verificar quais bumps um usuário comprou:**

```php
// Obter todos os bumps de um usuário
$user = User::find(123);
$bumps = DB::table('user_purchased_bumps')
    ->where('user_id', $user->id)
    ->get();

// Ou com o modelo OrderBump
$bumps = \Modules\Subscriptions\Models\OrderBump::whereIn('id', [4, 5])->get();
```

### **No Dashboard/Admin:**

```php
// Quantos bumps foram vendidos?
$bumpSales = DB::table('user_purchased_bumps')
    ->selectRaw('bump_id, COUNT(*) as total_sold')
    ->groupBy('bump_id')
    ->get();

// Exemplo resultado:
// bump_id: 4, total_sold: 125
// bump_id: 5, total_sold: 87
```

---

## 📝 FICHEIRO DE EXEMPLO

Se quiser testar o fluxo completo, aqui está o que seria enviado:

### **Frontend envia para POST /api/pix/create:**

```javascript
fetch('/api/pix/create', {
  method: 'POST',
  headers: { 'Content-Type': 'application/json' },
  body: JSON.stringify({
    amount: 3999,  // R$ 39,99 em centavos
    currency_code: 'BRL',
    plan_key: 'premium-monthly',
    offer_hash: 'abc123',
    bumps: [4],    // NOVO: Bump selecionado
    customer: {
      name: 'João Silva',
      email: 'joao@example.com',
      phone_number: '11999999999',
      document: '12345678901'
    }
  })
});
```

### **Backend responde com QR Code:**

```json
{
  "status": "success",
  "data": {
    "payment_id": "pix123",
    "qr_code": "00020.126360000000001234567...",
    "amount": 3999,
    "expiration_date": "2025-01-07 23:00:00"
  }
}
```

### **Webhook quando paga (Pushing Pay envia):**

```json
{
  "event": "payment.approved",
  "payment_id": "pix123",
  "amount": 3999,
  "status": "paid",
  "metadata": {
    "bumps": [4]  // ← Vem do que foi enviado
  }
}
```

### **Backend processa (WebHookController):**

1. Cria `Subscription` (plano)
2. Insere em `user_purchased_bumps`: (user_id, bump_id=4)
3. Envia email com confirmação de plano + add-on

---

## 🧪 COMO TESTAR

### **1. Pré-requisitos:**
- ✅ Migrations rodadas
- ✅ Bumps populados com dados (do exemplo anterior)

### **2. Teste Frontend:**

```
1. Abra http://localhost:8000
2. Selecione método PIX (não cartão)
3. Verifique se bumps aparecem ✓
4. Selecione um bump ✓
5. Total deve incluir o bump ✓
```

### **3. Teste Backend (Simulate PIX Payment):**

```bash
# Terminal 1: Verificar dados antes
php artisan tinker
> DB::table('user_purchased_bumps')->count();
=> 0

# Terminal 2: Simular webhook (copie curl do Pushing Pay ou use Postman)
curl -X POST http://localhost:8000/api/webhook/pushing-pay \
  -H "Content-Type: application/json" \
  -d '{
    "event": "payment.approved",
    "payment_id": "test123",
    "amount": 3999,
    "status": "paid",
    "metadata": {
      "bumps": [4]
    }
  }'

# Terminal 1: Verificar dados depois
> DB::table('user_purchased_bumps')->count();
=> 1  ← Registrado!
> DB::table('user_purchased_bumps')->first();
```

---

## 📋 CHECKLIST

- [ ] Rodar migration: `php artisan migrate`
- [ ] Verificar que tabela `user_purchased_bumps` foi criada
- [ ] Testar frontend - bumps aparecem para PIX?
- [ ] Simular webhook de PIX pagamento
- [ ] Verificar que `user_purchased_bumps` tem registro
- [ ] (Opcional) Criar email que mostra bumps adquiridos

---

## 🚀 PRÓXIMAS FUNCIONALIDADES (Fase 3)

- [ ] Dashboard: Mostrar vendas de bumps por período
- [ ] Email: Incluir lista de bumps adquiridos
- [ ] Admin: Interface para ver quem comprou qual bump
- [ ] Analytics: Relatório de bump mais vendido
- [ ] Entrega: Sistema para acessar conteúdo do bump (após compra)

---

## 🔗 ARQUIVOS MODIFICADOS

| Arquivo | O que mudou |
|---------|-----------|
| `snaphubb-pages/resources/views/livewire/page-pay.blade.php` | Removido `!== 'pix'` |
| `snaphubb-pages/app/Http/Controllers/PixController.php` | Adicionar `bumps` na validação e metadata |
| `snaphubb/app/Http/Controllers/WebHookController.php` | Registrar bumps em nova tabela |
| `snaphubb/database/migrations/...create_user_purchased_bumps_table.php` | Nova migração |

---

## ✨ RESUMO

| Antes | Depois |
|-------|--------|
| Bumps só no cartão | Bumps em cartão E PIX |
| Bumps não rastreados | Bumps registrados em tabela |
| Sem dados de venda | Dashboard pode mostrar vendas |

**Status:** ✅ **IMPLEMENTADO E PRONTO**
