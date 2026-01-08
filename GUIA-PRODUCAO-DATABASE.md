# 📊 Guia Completo: Acessando o Banco de Dados em Produção

---

## 🎯 Visão Rápida: Onde Estão Seus Dados?

```
VPS (Hosting próprio)
├── Aplicação Laravel (pasta /var/www/snaphubb)
│   └── Conecta a →
├── MySQL Server (localhost:3306)
│   └── Database: snaphubb
│       ├── users (👥 usuários cadastrados)
│       ├── subscriptions (🔄 assinantes ativos)
│       ├── orders (💳 pagamentos/transações)
│       └── ... 90+ outras tabelas
└── Arquivo .env (credenciais DB)
```

---

## 🔐 Parte 1: Acessar o Banco em Produção

### **Método 1: Via SSH + MySQL CLI (Recomendado)**

```bash
# 1. Conectar à VPS via SSH
ssh usuario@seu-ip-vps.com

# 2. Acessar MySQL (sem senha = pressiona Enter)
mysql -u root snaphubb

# Ou com senha:
mysql -h 127.0.0.1 -u root -p snaphubb
# Digite a senha quando solicitado
```

**Dentro do MySQL:**
```sql
-- Ver quantos usuários têm
SELECT COUNT(*) as total_usuarios FROM users;

-- Ver quantos assinantes ativos têm
SELECT COUNT(*) as assinantes_ativos FROM subscriptions WHERE status = 'active';

-- Sair
exit;
```

---

### **Método 2: Via Laravel Artisan (Mais Seguro)**

Se tiver acesso SSH à VPS:

```bash
# 1. Conectar à VPS
ssh usuario@seu-ip-vps.com

# 2. Ir para pasta do projeto
cd /var/www/snaphubb

# 3. Abrir Tinker (Laravel console)
php artisan tinker

# 4. Rodar comandos PHP
>>> User::count()  // Retorna: 10
>>> Subscription::where('status', 'active')->count()  // Retorna: 8
>>> exit
```

---

### **Método 3: Via PHPMyAdmin (Interface Visual)**

Se tiver instalado:
```
URL: https://seu-site.com/phpmyadmin
Usuário: root
Senha: (conforme .env)
Database: snaphubb
```

---

### **Método 4: Via DBeaver (Recomendado para Desktop)**

Instalação grátis: https://dbeaver.io/

**Passos:**
1. Abrir DBeaver
2. New Connection → MySQL
3. Preencher:
   - Server: `seu-ip-vps.com`
   - Port: `3306`
   - Database: `snaphubb`
   - User: `root`
   - Password: (do .env)
4. Test Connection
5. Browse visual das tabelas

---

## 📋 Parte 2: Estrutura de Dados (As Tabelas Importantes)

### **Tabela 1: `users` (Usuários Cadastrados)**

```sql
-- Ver TODOS os usuários cadastrados
SELECT 
    id,
    email,
    first_name,
    last_name,
    created_at,
    is_banned,
    status,
    deleted_at
FROM users
WHERE deleted_at IS NULL  -- Usuários NÃO deletados
ORDER BY created_at DESC;

-- Resultado esperado com 10 usuários:
| id | email              | first_name | last_name | created_at          | is_banned | status | deleted_at |
|----|-------------------|-----------|-----------|-------------------|-----------|--------|------------|
| 1  | user1@mail.com    | João      | Silva     | 2025-01-01...     | 0         | 1      | NULL       |
| 2  | user2@mail.com    | Maria     | Santos    | 2025-01-02...     | 0         | 1      | NULL       |
| 3  | deleted@mail.com  | Pedro     | Costa     | 2025-01-03...     | 0         | 1      | 2025-01-07 |
| ...
```

**Campos importantes:**
- `id` = ID único do usuário
- `email` = Email do cadastro
- `is_banned` = 1 se banido, 0 se ativo
- `status` = 1 se ativo, 0 se inativo
- `deleted_at` = NULL se ativo, data se deletado (soft delete)
- `created_at` = Quando se cadastrou

---

### **Tabela 2: `subscriptions` (Assinantes Ativos)**

```sql
-- Ver TODOS os assinantes com seus planos
SELECT 
    s.id,
    s.user_id,
    u.email,
    u.first_name,
    s.plan_id,
    p.name as plan_name,
    s.status,
    s.start_date,
    s.end_date,
    s.total_amount,
    s.created_at,
    s.deleted_at
FROM subscriptions s
JOIN users u ON s.user_id = u.id
LEFT JOIN plan p ON s.plan_id = p.id
WHERE s.deleted_at IS NULL  -- Assinanturas NÃO deletadas
AND s.status = 'active'     -- Apenas ATIVAS
ORDER BY s.created_at DESC;

-- Resultado esperado:
| id | user_id | email              | first_name | plan_id | plan_name | status | start_date | end_date   | total_amount |
|----|---------|-------------------|-----------|---------|-----------|--------|-----------|-----------|--------------|
| 1  | 1       | user1@mail.com    | João      | 1       | Premium   | active | 2025-01-01| 2025-02-01| 49.90      |
| 2  | 2       | user2@mail.com    | Maria     | 2       | Gold      | active | 2025-01-02| 2025-04-02| 99.90      |
| ...
```

**Campos importantes:**
- `status` = 'active' (ativo), 'cancelled' (cancelado), 'expired' (expirado)
- `start_date` = Quando a assinatura começou
- `end_date` = Quando expira
- `total_amount` = Valor pago
- `deleted_at` = NULL se ativo, data se deletado

---

### **Tabela 3: `orders` (Transações de Pagamento)**

```sql
-- Ver TODOS os pagamentos (Stripe + PIX)
SELECT 
    o.id,
    o.user_id,
    u.email,
    o.plan,
    o.currency,
    o.price,
    o.payment_status,
    o.external_payment_id,  -- ID do Stripe
    o.pix_id,               -- ID do PIX/Mercado Pago
    o.created_at
FROM orders o
JOIN users u ON o.user_id = u.id
ORDER BY o.created_at DESC;

-- Resultado esperado:
| id | user_id | email              | plan    | currency | price | payment_status | external_payment_id | pix_id | created_at |
|----|---------|-------------------|---------|----------|-------|----------------|-------------------|--------|-----------|
| 1  | 1       | user1@mail.com    | monthly | BRL      | 49.90 | succeeded      | ch_stripe_123     | NULL   | 2025-01-01|
| 2  | 2       | user2@mail.com    | monthly | BRL      | 49.90 | succeeded      | NULL              | pix_456| 2025-01-02|
| 3  | 1       | user1@mail.com    | monthly | BRL      | 49.90 | failed         | ch_stripe_789     | NULL   | 2025-01-04|
| ...
```

**Campos importantes:**
- `payment_status` = 'pending', 'succeeded', 'failed', 'refunded'
- `external_payment_id` = ID do Stripe (para rastrear lá)
- `pix_id` = ID do Mercado Pago (para rastrear lá)
- `created_at` = Quando a transação foi feita

---

### **Tabela 4: `plan` (Planos Disponíveis)**

```sql
-- Ver todos os planos cadastrados
SELECT 
    id,
    name,
    currency,
    language,
    price,
    discount_percentage,
    duration,
    duration_value,
    pages_product_external_id,  -- ID do Stripe
    status,
    created_at
FROM plan
WHERE deleted_at IS NULL
ORDER BY created_at DESC;

-- Resultado esperado:
| id | name     | currency | language | price | discount_% | duration | duration_value | pages_product_external_id | status |
|----|---------|----------|----------|-------|-----------|----------|----------------|------------------------|--------|
| 1  | Premium | BRL      | br       | 49.90 | 20        | month    | 1              | prod_stripe_123       | 1      |
| 2  | Premium | USD      | en       | 9.99  | 20        | month    | 1              | prod_stripe_123       | 1      |
| 3  | Gold    | BRL      | br       | 99.90 | 30        | month    | 1              | prod_stripe_456       | 1      |
| ...
```

---

## 📊 Parte 3: Queries Práticas para Monitorar

### **1️⃣ Dashboard Rápido: Ver Tudo em Uma Consulta**

```sql
SELECT 
    (SELECT COUNT(*) FROM users WHERE deleted_at IS NULL) as total_usuarios,
    (SELECT COUNT(*) FROM subscriptions WHERE status = 'active' AND deleted_at IS NULL) as assinantes_ativos,
    (SELECT COUNT(*) FROM subscriptions WHERE status = 'cancelled' AND deleted_at IS NULL) as assinantes_cancelados,
    (SELECT COUNT(*) FROM orders WHERE payment_status = 'succeeded') as pagamentos_sucesso,
    (SELECT COUNT(*) FROM orders WHERE payment_status = 'failed') as pagamentos_falhados,
    (SELECT SUM(price) FROM orders WHERE payment_status = 'succeeded') as receita_total;
```

**Resultado esperado com 10 usuários:**
```
total_usuarios | assinantes_ativos | assinantes_cancelados | pagamentos_sucesso | pagamentos_falhados | receita_total
8              | 6                 | 2                     | 10                 | 1                   | 599.00
```

---

### **2️⃣ Listar Assinantes com Detalhes Completos**

```sql
SELECT 
    u.id,
    u.email,
    CONCAT(u.first_name, ' ', u.last_name) as nome_completo,
    s.id as subscription_id,
    p.name as plano,
    s.status as status_assinatura,
    s.start_date as data_inicio,
    s.end_date as data_expiracao,
    s.total_amount as valor_pago,
    CASE 
        WHEN s.end_date > NOW() THEN 'ATIVO'
        ELSE 'EXPIRADO'
    END as situacao,
    u.created_at as cadastrado_em
FROM users u
LEFT JOIN subscriptions s ON u.id = s.user_id AND s.deleted_at IS NULL
LEFT JOIN plan p ON s.plan_id = p.id
WHERE u.deleted_at IS NULL
ORDER BY u.created_at DESC;
```

---

### **3️⃣ Ver Usuários que Deletaram Conta**

```sql
SELECT 
    id,
    email,
    first_name,
    last_name,
    created_at as data_cadastro,
    deleted_at as data_delecao,
    DATEDIFF(deleted_at, created_at) as dias_de_uso
FROM users
WHERE deleted_at IS NOT NULL  -- Apenas deletados
ORDER BY deleted_at DESC;
```

---

### **4️⃣ Relatório de Receita por Período**

```sql
SELECT 
    DATE(o.created_at) as data,
    COUNT(o.id) as total_transacoes,
    COUNT(CASE WHEN o.payment_status = 'succeeded' THEN 1 END) as sucesso,
    COUNT(CASE WHEN o.payment_status = 'failed' THEN 1 END) as falhas,
    SUM(CASE WHEN o.payment_status = 'succeeded' THEN o.price ELSE 0 END) as receita,
    o.currency
FROM orders o
GROUP BY DATE(o.created_at), o.currency
ORDER BY o.created_at DESC;
```

---

### **5️⃣ Auditar Planos (Ver Se Tem Duplicatas)**

```sql
SELECT 
    pages_product_external_id,
    currency,
    language,
    COUNT(*) as quantidade
FROM plan
WHERE deleted_at IS NULL
GROUP BY pages_product_external_id, currency, language
HAVING COUNT(*) > 1;

-- Se retornar dados = PROBLEMA! Tem planos duplicados
```

---

## 🔍 Parte 4: Soft Deletes (Histórico de Deleção)

### **O que é Soft Delete?**

Quando um usuário ou assinatura é "deletado", **NÃO é removido do banco**, apenas marcado com data em `deleted_at`:

```
Antes de deletar:
| id | email | deleted_at |
| 1  | user1 | NULL       | ← ATIVO

Depois de deletar:
| id | email | deleted_at      |
| 1  | user1 | 2025-01-07...   | ← DELETADO
```

**Por que?**
- ✅ Auditoria: sabe quem e quando deletou
- ✅ Conformidade: LGPD exige histórico
- ✅ Recuperação: pode restaurar se necessário
- ✅ Relatórios: pode incluir/excluir dados deletados

---

### **Ver Histórico Completo de um Usuário**

```sql
-- Ver TODOS os registros de um usuário (até deletados)
SELECT 
    id,
    email,
    first_name,
    created_at,
    deleted_at,
    CASE 
        WHEN deleted_at IS NULL THEN 'ATIVO'
        ELSE 'DELETADO'
    END as status
FROM users
WHERE email = 'usuario@example.com'
LIMIT 1;
```

---

### **Recuperar Usuário Deletado (Se Necessário)**

```sql
-- Restaurar um usuário que foi deletado
UPDATE users 
SET deleted_at = NULL 
WHERE id = 123;
```

---

## 🔐 Parte 5: Segurança em Produção

### **Checklist de Acesso Seguro:**

```
☑ SSH com chave pública (nunca senha)
☑ Mudar senha root do MySQL na VPS
☑ Criar usuário MySQL específico (não usar root)
☑ Fazer backup automático diário
☑ Logar acessos ao DB (audit log)
☑ Não compartilhar credenciais por email
☑ Usar .env protegido em produção
☑ Firewall: MySQL só aceita de seu IP
```

---

### **Criar Usuário MySQL Específico (Recomendado)**

```bash
# Conectar como root
mysql -u root -p

# Executar:
CREATE USER 'snaphubb'@'localhost' IDENTIFIED BY 'senha-super-segura-123!@#';
GRANT ALL PRIVILEGES ON snaphubb.* TO 'snaphubb'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

**Depois atualizar .env:**
```env
DB_USERNAME=snaphubb
DB_PASSWORD=senha-super-segura-123!@#
```

---

## 📅 Parte 6: Backup e Disaster Recovery

### **Fazer Backup Manual**

```bash
# Via SSH na VPS
mysqldump -u root -p snaphubb > backup-`date +%Y-%m-%d`.sql

# Ou com comando Laravel
php artisan backup:run
```

---

### **Restaurar do Backup**

```bash
mysql -u root -p snaphubb < backup-2025-01-07.sql
```

---

## 🚀 Parte 7: Monitoramento Contínuo (Recomendado)

### **Script para Monitorar Diariamente**

Criar arquivo `check-database.sh`:

```bash
#!/bin/bash

# Conectar ao MySQL e gerar relatório
mysql -u root -p"$DB_PASSWORD" snaphubb << EOF

-- Relatório diário
SELECT 'RELATÓRIO DIÁRIO' as secao;
SELECT CONCAT('Total Usuários: ', COUNT(*)) FROM users WHERE deleted_at IS NULL;
SELECT CONCAT('Assinantes Ativos: ', COUNT(*)) FROM subscriptions WHERE status = 'active' AND deleted_at IS NULL;
SELECT CONCAT('Receita Hoje: R$ ', ROUND(SUM(price), 2)) FROM orders WHERE DATE(created_at) = CURDATE() AND payment_status = 'succeeded';
SELECT CONCAT('Pagamentos Falhados Hoje: ', COUNT(*)) FROM orders WHERE DATE(created_at) = CURDATE() AND payment_status = 'failed';

EOF
```

---

## ❓ FAQ Rápido

**P: Onde fica o arquivo .env em produção?**  
R: Normalmente em `/var/www/snaphubb/.env` (não é versionado no Git por segurança)

**P: Posso deletar um usuário diretamente do banco?**  
R: Melhor não. Use `php artisan tinker` → `User::find(1)->delete();` para usar soft delete corretamente

**P: Quanto tempo leva um backup?**  
R: Depende do tamanho. Com 10 usuários: ~1 segundo. Com 10k usuários: ~30 segundos

**P: Se excluir um usuário, ele perde acesso?**  
R: Imediatamente (logout automático). Dados ficam no banco marcados como deletados.

**P: Como saber se um pagamento Stripe foi de fato processado?**  
R: Verificar campo `external_payment_id` na tabela `orders` e depois confirmar no Stripe dashboard

**P: Posso ver histórico de quem cancelou assinatura?**  
R: Sim, tabela `subscriptions` tem `status = 'cancelled'` e `deleted_at` mostra quando foi deletado

---

## 🎓 Resumo Final

```
📊 FONTE DE DADOS:
  └─ MySQL em VPS/Hosting (localhost:3306)
     └─ Database: snaphubb

🔑 TABELAS IMPORTANTES:
  ├─ users → Usuários cadastrados
  ├─ subscriptions → Assinantes e status
  ├─ orders → Pagamentos/transações
  └─ plan → Planos disponíveis

🔐 ACESSAR EM PRODUÇÃO:
  ├─ SSH + MySQL CLI (terminal)
  ├─ Laravel Artisan (php artisan tinker)
  ├─ PHPMyAdmin (web interface)
  └─ DBeaver (desktop GUI)

📋 MONITORAR:
  ├─ Total de usuários
  ├─ Assinantes ativos vs cancelados
  ├─ Receita por período
  ├─ Taxa de falha de pagamento
  └─ Histórico de deleções (soft delete)

🛡️ SEGURANÇA:
  ├─ SSH com chave pública
  ├─ Mudar senha root
  ├─ Criar usuário específico
  ├─ Fazer backups regulares
  └─ Logar acessos
```

---

**Próximos passos:**
1. ✅ Acessar VPS via SSH
2. ✅ Conectar ao MySQL
3. ✅ Rodar queries de monitoring
4. ✅ Fazer primeiro backup
5. ✅ Configurar monitoramento automático
