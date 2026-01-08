# 🔐 Credenciais e Configuração de Acesso ao BD em Produção

## 1️⃣ Configuração Padrão do .env (Seu Projeto)

Baseado no seu `.env` atual, aqui está como ficará em produção:

```env
# ============================================
# DATABASE CONFIGURATION (Produção)
# ============================================

# Local onde MySQL está rodando
DB_HOST=127.0.0.1           # ou seu-servidor.com se for remoto
DB_PORT=3306                 # Porta padrão do MySQL
DB_DATABASE=snaphubb         # Nome do banco (definido por você)
DB_USERNAME=root             # Usuário (MUDE para algo mais seguro!)
DB_PASSWORD=sua-senha-aqui   # Senha (ALTAMENTE CONFIDENCIAL)

# ============================================
# Credenciais do Stripe (Pagamentos)
# ============================================

STRIPE_API_PUBLIC_KEY=pk_live_seu_token_publico_aqui
STRIPE_API_SECRET_KEY=sk_live_seu_token_secreto_aqui
STRIPE_WEBHOOK_SECRET=whsec_seu_webhook_secret_aqui

# ============================================
# Credenciais Mercado Pago (PIX)
# ============================================

MERCADOPAGO_ENV=production
MERCADOPAGO_ACCESS_TOKEN=seu_token_producao_aqui
MERCADOPAGO_PUBLIC_KEY=seu_public_key_aqui

# ============================================
# URLs de Produção
# ============================================

APP_URL=https://seu-site.com
STREAMIT_API_URL=https://seu-site.com/api
```

---

## 2️⃣ Estrutura de Pastas em Produção (VPS)

```
/var/www/
├── snaphubb/                    ← Seu projeto Laravel
│   ├── .env                     ← ARQUIVO CRÍTICO (credenciais)
│   ├── .env.production.backup   ← BACKUP do .env (seguro)
│   ├── storage/
│   │   ├── logs/
│   │   │   └── laravel.log      ← Logs da aplicação
│   │   └── backups/
│   │       └── snaphubb-2025-01-07.sql  ← Backup do banco
│   ├── database/
│   │   └── migrations/          ← Scripts de criação de tabelas
│   └── ...outros arquivos
│
└── backups/                     ← Pasta segura para backups
    └── snaphubb-2025-01-07.sql
    └── snaphubb-2025-01-06.sql
    └── snaphubb-2025-01-05.sql
```

---

## 3️⃣ Como Proteger Suas Credenciais

### **❌ NUNCA faça isso:**
```env
# Expor no Git
git add .env
git commit -m "adicionei credenciais"

# Enviar por email
"Segue credenciais: usuario=root password=123456"

# Deixar em arquivo público
/public/config.php

# Compartilhar via Slack/Teams desprotegido
```

### **✅ FAÇA isso:**
```bash
# 1. Arquivo .env NÃO vai para Git
#    (já está em .gitignore)

# 2. Fazer backup seguro do .env
#    (senha protegida, 7z encryption)
7z a -p sua-senha-super-segura .env.7z .env

# 3. Guardar senhas em gerenciador:
#    - 1Password
#    - LastPass
#    - Bitwarden
#    - Vault do servidor

# 4. Ao fazer deploy:
#    - SSH em seu servidor
#    - Fazer upload do .env via SCP
#    - NUNCA copiar-colar no terminal
scp .env usuario@seu-vps.com:/var/www/snaphubb/.env

# 5. Verificar permissões
ssh usuario@seu-vps.com
chmod 600 /var/www/snaphubb/.env  # Só você pode ler
ls -la /var/www/snaphubb/.env     # Verificar
```

---

## 4️⃣ Comandos Práticos para Acesso

### **Conectar à VPS e Verificar BD**

```bash
# Conectar via SSH
ssh usuario@seu-ip-vps.com

# Depois de conectado:

# Ver versão do MySQL
mysql --version

# Conectar ao banco
mysql -u root -p
# Digite a senha

# Dentro do MySQL:
SHOW DATABASES;        -- Ver todos os bancos
USE snaphubb;          -- Entrar no banco
SHOW TABLES;           -- Ver todas as tabelas
SELECT COUNT(*) FROM users;  -- Contar usuários
DESC users;            -- Ver estrutura da tabela

# Sair
EXIT;
```

### **Verificar Saúde do BD via Laravel**

```bash
# SSH na VPS
ssh usuario@seu-ip-vps.com
cd /var/www/snaphubb

# Usar Laravel para checar
php artisan tinker

# Dentro do Tinker:
>>> User::count()
=> 10

>>> Subscription::where('status', 'active')->count()
=> 8

>>> Order::where('payment_status', 'succeeded')->sum('price')
=> 599.00

>>> exit
```

---

## 5️⃣ Criar Backup Automático

### **Script Cron (Roda Diariamente)**

```bash
# Conectar à VPS
ssh usuario@seu-ip-vps.com

# Criar pasta para backups
mkdir -p /var/www/snaphubb/storage/backups

# Editar crontab
crontab -e

# Adicionar essa linha (backup todo dia às 3 AM):
0 3 * * * cd /var/www/snaphubb && php artisan backup:run

# Ou criar backup manual assim:
0 3 * * * mysqldump -u root -p'sua-senha' snaphubb > /var/www/snaphubb/storage/backups/snaphubb-$(date +\%Y-\%m-\%d).sql
```

---

## 6️⃣ Exemplo: Acessar Dados de Um Usuário Específico

### **Cenário: Cliente "João Silva" quer saber status da assinatura**

```bash
# 1. SSH na VPS
ssh usuario@seu-ip-vps.com

# 2. Entrar no MySQL
mysql -u root -p snaphubb

# 3. Executar query:
SELECT 
    u.id,
    u.email,
    u.first_name,
    u.created_at,
    s.status as subscription_status,
    s.start_date,
    s.end_date,
    p.name as plan_name,
    o.price,
    o.payment_status,
    o.created_at as payment_date
FROM users u
LEFT JOIN subscriptions s ON u.id = s.user_id AND s.deleted_at IS NULL
LEFT JOIN plan p ON s.plan_id = p.id
LEFT JOIN orders o ON u.id = o.user_id
WHERE u.email = 'joao@example.com'
ORDER BY o.created_at DESC;

# 4. Resultado:
# id=1, email=joao@example.com, created_at=2025-01-01
# subscription_status=active, start_date=2025-01-01, end_date=2025-02-01
# plan_name=Premium, price=49.90, payment_status=succeeded
```

---

## 7️⃣ Recuperação de Desastres

### **Cenário: Algo deu errado, preciso restaurar backup**

```bash
# SSH na VPS
ssh usuario@seu-ip-vps.com

# 1. Fazer backup do estado atual (antes de restaurar)
mysqldump -u root -p snaphubb > /var/www/snaphubb/storage/backups/snaphubb-ANTES-RESTAURACAO.sql

# 2. Restaurar do backup anterior
mysql -u root -p snaphubb < /var/www/snaphubb/storage/backups/snaphubb-2025-01-06.sql

# 3. Verificar se restaurou corretamente
mysql -u root -p snaphubb
SELECT COUNT(*) FROM users;
EXIT;
```

---

## 8️⃣ Monitoramento em Tempo Real

### **Ver Logs de Erro**

```bash
# SSH na VPS
ssh usuario@seu-ip-vps.com

# Ver últimas linhas do log
tail -f /var/www/snaphubb/storage/logs/laravel.log

# Ver últimas 100 linhas
tail -100 /var/www/snaphubb/storage/logs/laravel.log

# Procurar erro específico
grep "error" /var/www/snaphubb/storage/logs/laravel.log
grep "Exception" /var/www/snaphubb/storage/logs/laravel.log
```

### **Ver Espaço em Disco**

```bash
# Ver quanto espaço está usando
du -sh /var/www/snaphubb

# Ver espaço disponível
df -h

# Ver tabelas maiores do MySQL
mysql -u root -p snaphubb -e "
SELECT 
    table_name,
    ROUND(((data_length + index_length) / 1024 / 1024), 2) AS 'Size_MB'
FROM information_schema.TABLES
WHERE table_schema = 'snaphubb'
ORDER BY (data_length + index_length) DESC;
"
```

---

## 9️⃣ Checklist Pré-Deploy

Antes de colocar seu site ao vivo:

```
☑ .env configurado corretamente
☑ Senhas do MySQL alteradas (não padrão)
☑ SSH com chave pública funcionando
☑ Backup automático configurado
☑ Firewall bloqueia acesso ao MySQL (porta 3306)
☑ Teste de conexão funciona
☑ Logs estão sendo gravados
☑ Espaço em disco suficiente (mín 5GB)
☑ HTTPS configurado
☑ Email do admin funciona
☑ Stripe/PIX credenciais em produção
☑ Backup inicial feito e testado
☑ Plano de disaster recovery documentado
```

---

## 🔟 Suporte Rápido

### **Se der erro de conexão:**

```bash
# Verificar se MySQL está rodando
ps aux | grep mysql

# Se não estiver:
sudo systemctl start mysql

# Verificar credenciais
mysql -h 127.0.0.1 -u root -p'sua-senha' -e "SELECT 1;"

# Se retornar "1" = tudo OK
# Se der erro = credenciais erradas
```

### **Se performance cair:**

```bash
# Ver queries lentas
mysql -u root -p snaphubb -e "
SELECT 
    query_time,
    lock_time,
    sql_text
FROM mysql.slow_log
ORDER BY query_time DESC
LIMIT 10;
"

# Ou otimizar índices
php artisan optimize
php artisan view:clear
php artisan cache:clear
```

---

**Tudo configurado? Próximo passo é fazer o deploy! 🚀**
