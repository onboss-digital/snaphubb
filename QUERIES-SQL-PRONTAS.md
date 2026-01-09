# 📋 Queries SQL Prontas (Copy-Paste)

Arquivo com todas as queries úteis para monitorar seu banco em produção.

---

## 🎯 DASHBOARD: Visão Geral em 1 Query

```sql
-- Cole isso no MySQL para ver tudo de uma vez
SELECT 
    'TOTAL USUÁRIOS CADASTRADOS' as metrica,
    COUNT(*) as valor
FROM users
WHERE deleted_at IS NULL
UNION ALL
SELECT 
    'ASSINANTES ATIVOS',
    COUNT(*)
FROM subscriptions
WHERE status = 'active' AND deleted_at IS NULL
UNION ALL
SELECT 
    'ASSINANTES CANCELADOS',
    COUNT(*)
FROM subscriptions
WHERE status = 'cancelled' AND deleted_at IS NULL
UNION ALL
SELECT 
    'PAGAMENTOS SUCESSO',
    COUNT(*)
FROM orders
WHERE payment_status = 'succeeded'
UNION ALL
SELECT 
    'PAGAMENTOS FALHADOS',
    COUNT(*)
FROM orders
WHERE payment_status = 'failed'
UNION ALL
SELECT 
    'RECEITA TOTAL (R$)',
    ROUND(SUM(price), 2)
FROM orders
WHERE payment_status = 'succeeded';
```

---

## 👥 USUÁRIOS: Listar Todos com Detalhes

```sql
-- Todos os usuários cadastrados
SELECT 
    id,
    email,
    CONCAT(first_name, ' ', last_name) as nome,
    status,
    is_banned,
    created_at,
    DATEDIFF(NOW(), created_at) as dias_cadastrado
FROM users
WHERE deleted_at IS NULL
ORDER BY created_at DESC;
```

```sql
-- Usuários que deletaram conta
SELECT 
    id,
    email,
    CONCAT(first_name, ' ', last_name) as nome,
    created_at as data_cadastro,
    deleted_at as data_delecao,
    DATEDIFF(deleted_at, created_at) as dias_de_uso
FROM users
WHERE deleted_at IS NOT NULL
ORDER BY deleted_at DESC;
```

```sql
-- Usuários banidos
SELECT 
    id,
    email,
    first_name,
    is_banned,
    created_at
FROM users
WHERE is_banned = 1 AND deleted_at IS NULL;
```

```sql
-- Procurar usuário específico
SELECT *
FROM users
WHERE email = 'seu-email@example.com'
   OR CONCAT(first_name, ' ', last_name) LIKE '%João%'
LIMIT 1;
```

---

## 🔄 ASSINANTES: Acompanhar Status

```sql
-- Visão completa de assinantes
SELECT 
    s.id as subscription_id,
    u.id as user_id,
    u.email,
    CONCAT(u.first_name, ' ', u.last_name) as cliente,
    p.name as plano,
    s.status,
    s.start_date as inicio,
    s.end_date as expiracao,
    DATEDIFF(s.end_date, NOW()) as dias_restantes,
    s.total_amount as valor,
    CASE 
        WHEN s.end_date > NOW() THEN '✓ ATIVO'
        WHEN s.end_date <= NOW() THEN '✗ EXPIRADO'
        ELSE 'SEM INFO'
    END as situacao,
    s.created_at
FROM subscriptions s
JOIN users u ON s.user_id = u.id
LEFT JOIN plan p ON s.plan_id = p.id
WHERE s.deleted_at IS NULL
ORDER BY s.end_date DESC;
```

```sql
-- Apenas assinantes que vão expirar em 7 dias
SELECT 
    u.email,
    CONCAT(u.first_name, ' ', u.last_name) as cliente,
    p.name as plano,
    s.end_date,
    DATEDIFF(s.end_date, NOW()) as dias_restantes
FROM subscriptions s
JOIN users u ON s.user_id = u.id
LEFT JOIN plan p ON s.plan_id = p.id
WHERE s.status = 'active'
  AND s.deleted_at IS NULL
  AND s.end_date BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL 7 DAY)
ORDER BY s.end_date ASC;
```

```sql
-- Assinantes cancelados
SELECT 
    u.id,
    u.email,
    CONCAT(u.first_name, ' ', u.last_name) as cliente,
    p.name as plano,
    s.start_date,
    s.end_date,
    DATEDIFF(s.end_date, s.start_date) as dias_de_assinatura,
    s.total_amount
FROM subscriptions s
JOIN users u ON s.user_id = u.id
LEFT JOIN plan p ON s.plan_id = p.id
WHERE s.status = 'cancelled' AND s.deleted_at IS NULL
ORDER BY s.end_date DESC;
```

---

## 💳 PAGAMENTOS: Acompanhar Transações

```sql
-- Todos os pagamentos (últimas 50)
SELECT 
    o.id,
    u.email,
    CONCAT(u.first_name, ' ', u.last_name) as cliente,
    o.plan,
    o.price,
    o.currency,
    o.payment_status,
    o.external_payment_id,
    o.pix_id,
    o.created_at
FROM orders o
JOIN users u ON o.user_id = u.id
ORDER BY o.created_at DESC
LIMIT 50;
```

```sql
-- Pagamentos bem-sucedidos (receita)
SELECT 
    DATE(o.created_at) as data,
    COUNT(*) as quantidade,
    SUM(o.price) as receita,
    o.currency
FROM orders o
WHERE o.payment_status = 'succeeded'
GROUP BY DATE(o.created_at), o.currency
ORDER BY o.created_at DESC;
```

```sql
-- Pagamentos falhados (problema!)
SELECT 
    o.id,
    u.email,
    CONCAT(u.first_name, ' ', u.last_name) as cliente,
    o.plan,
    o.price,
    o.currency,
    o.payment_status,
    o.external_payment_id,
    o.created_at,
    DATEDIFF(NOW(), o.created_at) as horas_atras
FROM orders o
JOIN users u ON o.user_id = u.id
WHERE o.payment_status = 'failed'
ORDER BY o.created_at DESC;
```

```sql
-- Pagamentos de um cliente específico
SELECT 
    o.id,
    o.plan,
    o.price,
    o.payment_status,
    o.external_payment_id,
    o.created_at
FROM orders o
JOIN users u ON o.user_id = u.id
WHERE u.email = 'seu-email@example.com'
ORDER BY o.created_at DESC;
```

```sql
-- Receita por plano
SELECT 
    o.plan,
    COUNT(*) as transacoes,
    SUM(o.price) as receita_total,
    AVG(o.price) as ticket_medio,
    o.currency
FROM orders o
WHERE o.payment_status = 'succeeded'
GROUP BY o.plan, o.currency
ORDER BY receita_total DESC;
```

---

## 📊 PLANOS: Auditoria

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
    pages_product_external_id,
    status,
    created_at
FROM plan
WHERE deleted_at IS NULL
ORDER BY created_at DESC;
```

```sql
-- Encontrar planos duplicados (PROBLEMA!)
SELECT 
    pages_product_external_id,
    currency,
    language,
    COUNT(*) as quantidade,
    GROUP_CONCAT(id) as ids
FROM plan
WHERE deleted_at IS NULL
GROUP BY pages_product_external_id, currency, language
HAVING COUNT(*) > 1;

-- Se retornar dados = você tem duplicatas!
```

```sql
-- Verificar mapeamento Stripe
SELECT 
    id,
    name,
    pages_product_external_id as stripe_product_id,
    currency,
    CASE 
        WHEN pages_product_external_id IS NULL THEN '⚠️ SEM STRIPE'
        ELSE '✓ COM STRIPE'
    END as status
FROM plan
WHERE deleted_at IS NULL;
```

---

## 🔍 AUDITORIA: Histórico de Ações

```sql
-- Ver últimas 100 assinações criadas
SELECT 
    s.id,
    u.email,
    p.name as plano,
    s.start_date,
    s.end_date,
    s.total_amount,
    s.created_at,
    TIMESTAMPDIFF(DAY, s.created_at, NOW()) as dias_atras
FROM subscriptions s
JOIN users u ON s.user_id = u.id
LEFT JOIN plan p ON s.plan_id = p.id
WHERE s.deleted_at IS NULL
ORDER BY s.created_at DESC
LIMIT 100;
```

```sql
-- Ver quem se cadastrou nos últimos 7 dias
SELECT 
    id,
    email,
    CONCAT(first_name, ' ', last_name) as nome,
    created_at,
    TIMESTAMPDIFF(DAY, created_at, NOW()) as dias_atras
FROM users
WHERE deleted_at IS NULL
  AND created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
ORDER BY created_at DESC;
```

```sql
-- Calcular churn (taxa de cancelamento)
SELECT 
    DATE_TRUNC(DATE(created_at), MONTH) as mes,
    COUNT(*) as novas_sub,
    (SELECT COUNT(*) FROM subscriptions 
     WHERE status = 'cancelled' 
     AND DATE_TRUNC(DATE(created_at), MONTH) = DATE_TRUNC(DATE(created_at), MONTH)
    ) as canceladas,
    ROUND(100.0 * (SELECT COUNT(*) FROM subscriptions 
                   WHERE status = 'cancelled' 
                   AND DATE_TRUNC(DATE(created_at), MONTH) = DATE_TRUNC(DATE(created_at), MONTH)
           ) / COUNT(*), 2) as churn_percent
FROM subscriptions
WHERE deleted_at IS NULL
GROUP BY mes
ORDER BY mes DESC;
```

---

## 🔐 SEGURANÇA: Limpeza e Manutenção

```sql
-- Ver tables grandes (que consomem espaço)
SELECT 
    table_name,
    ROUND(((data_length + index_length) / 1024 / 1024), 2) as size_mb
FROM information_schema.TABLES
WHERE table_schema = 'snaphubb'
ORDER BY (data_length + index_length) DESC
LIMIT 10;
```

```sql
-- Otimizar todas as tabelas (libera espaço)
OPTIMIZE TABLE users;
OPTIMIZE TABLE subscriptions;
OPTIMIZE TABLE orders;
OPTIMIZE TABLE plan;
-- ... etc para outras tabelas
```

```sql
-- Ver quantos registros deletados existem
SELECT 
    'users deletados' as tipo,
    COUNT(*) as quantidade
FROM users
WHERE deleted_at IS NOT NULL
UNION ALL
SELECT 
    'subscriptions deletadas',
    COUNT(*)
FROM subscriptions
WHERE deleted_at IS NOT NULL
UNION ALL
SELECT 
    'plans deletados',
    COUNT(*)
FROM plan
WHERE deleted_at IS NOT NULL;
```

```sql
-- Limpar dados deletados com mais de 1 ano (CUIDADO!)
-- Apenas se tiver certeza!
DELETE FROM users WHERE deleted_at < DATE_SUB(NOW(), INTERVAL 1 YEAR);
DELETE FROM subscriptions WHERE deleted_at < DATE_SUB(NOW(), INTERVAL 1 YEAR);
-- (Depois fazer backup!)
```

---

## 💰 RELATÓRIOS: Para Seu Chefe

```sql
-- Receita mensal
SELECT 
    DATE_FORMAT(o.created_at, '%Y-%m') as mes,
    COUNT(*) as transacoes,
    ROUND(SUM(o.price), 2) as receita,
    o.currency
FROM orders o
WHERE o.payment_status = 'succeeded'
GROUP BY DATE_FORMAT(o.created_at, '%Y-%m'), o.currency
ORDER BY o.created_at DESC;
```

```sql
-- Crescimento de usuários por semana
SELECT 
    DATE_FORMAT(created_at, '%Y-W%w') as semana,
    COUNT(*) as novos_usuarios
FROM users
WHERE deleted_at IS NULL
GROUP BY DATE_FORMAT(created_at, '%Y-W%w')
ORDER BY semana DESC;
```

```sql
-- Taxa de conversão (usuários → assinantes)
SELECT 
    COUNT(DISTINCT u.id) as total_usuarios,
    COUNT(DISTINCT s.user_id) as usuarios_com_assinatura,
    ROUND(100.0 * COUNT(DISTINCT s.user_id) / COUNT(DISTINCT u.id), 2) as conversao_percent
FROM users u
LEFT JOIN subscriptions s ON u.id = s.user_id AND s.status = 'active'
WHERE u.deleted_at IS NULL;
```

```sql
-- Lifetime Value (LTV) por usuário
SELECT 
    u.id,
    u.email,
    CONCAT(u.first_name, ' ', u.last_name) as cliente,
    ROUND(SUM(o.price), 2) as lifetime_value,
    COUNT(o.id) as total_transacoes,
    COUNT(DISTINCT s.id) as total_assinaturas
FROM users u
LEFT JOIN orders o ON u.id = o.user_id AND o.payment_status = 'succeeded'
LEFT JOIN subscriptions s ON u.id = s.user_id
WHERE u.deleted_at IS NULL
GROUP BY u.id
ORDER BY lifetime_value DESC;
```

---

## 🚨 ALERTAS: Verificar Problemas

```sql
-- Usuários com múltiplos pagamentos falhados (risco!)
SELECT 
    u.email,
    CONCAT(u.first_name, ' ', u.last_name) as cliente,
    COUNT(o.id) as falhas,
    MAX(o.created_at) as ultima_falha
FROM orders o
JOIN users u ON o.user_id = u.id
WHERE o.payment_status = 'failed'
GROUP BY o.user_id
HAVING COUNT(o.id) >= 3
ORDER BY falhas DESC;
```

```sql
-- Assinantes cujos pagamentos recorrentes podem falhar
SELECT 
    u.email,
    CONCAT(u.first_name, ' ', u.last_name) as cliente,
    p.name as plano,
    s.end_date,
    o.payment_status as ultimo_pagamento,
    DATEDIFF(NOW(), o.created_at) as dias_do_ultimo_pagamento
FROM subscriptions s
JOIN users u ON s.user_id = u.id
LEFT JOIN plan p ON s.plan_id = p.id
LEFT JOIN orders o ON s.user_id = o.user_id
WHERE s.status = 'active'
  AND o.payment_status = 'failed'
  AND DATEDIFF(NOW(), o.created_at) <= 30  -- Falha há menos de 30 dias
ORDER BY o.created_at DESC;
```

```sql
-- Verificar se há planos órfãos (sem usuários)
SELECT 
    p.id,
    p.name,
    COUNT(s.id) as assinantes
FROM plan p
LEFT JOIN subscriptions s ON p.id = s.plan_id AND s.status = 'active'
WHERE p.deleted_at IS NULL
GROUP BY p.id
HAVING COUNT(s.id) = 0;
```

---

**Dica:** Salve esse arquivo e customize para suas necessidades!
