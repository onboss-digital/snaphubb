# 📊 Banco Oficial SnapHubb - Produção & Desenvolvimento

## ⚠️ IMPORTANTE: Este é o banco OFICIAL de Produção!

**Data de criação:** 2 de janeiro de 2026  
**Status:** Pronto para Produção  
**Versão do Laravel:** 11.41.3  
**Database:** MySQL

---

## 🚀 AMBIENTE DE DESENVOLVIMENTO

### Inicialização Rápida (Retomando o trabalho)

```bash
# Terminal 1: Compilar Assets
npm run dev

# Terminal 2: Iniciar Servidor Laravel
php artisan serve --host=127.0.0.1 --port=8002
```

**Acesso:** http://127.0.0.1:8002

### Credenciais de Teste
- **Email:** admin@snaphubb.com
- **Senha:** Meta10k@@

---

## 🏭 AMBIENTE DE PRODUÇÃO

### Pré-requisitos
- PHP 8.1+ com extensões: MySQL, cURL, JSON, OpenSSL
- MySQL 8.0+
- Node.js 16+
- Composer 2.0+

### 1️⃣ Deploy Inicial (Primeira vez)

```bash
# Clone o repositório
git clone <seu-repo> /var/www/snaphubb
cd /var/www/snaphubb

# Instale dependências
composer install --no-dev --optimize-autoloader
npm install --legacy-peer-deps
npm run production

# Configure o ambiente
cp .env.example .env
# Edite .env com credenciais de produção

# Gere a APP_KEY
php artisan key:generate

# Execute as migrations
php artisan migrate --force

# Permissões corretas
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data /var/www/snaphubb

# Cache otimizado
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 2️⃣ Atualizações Futuras

```bash
cd /var/www/snaphubb

# Pull das mudanças
git pull origin main

# Instale dependências (apenas se alterou composer.json ou package.json)
composer install --no-dev --optimize-autoloader
npm install --legacy-peer-deps

# Execute migrations (se houver novas)
php artisan migrate --force

# Compile assets
npm run production

# Limpe caches
php artisan cache:clear
php artisan config:cache
php artisan route:cache
```

---

## 💾 BACKUP DO BANCO (CRÍTICO!)

### Backup Manual
```bash
# Backup completo
mysqldump -u root -p snaphubb > backup_$(date +%Y%m%d_%H%M%S).sql

# Com compressão (economiza espaço)
mysqldump -u root -p snaphubb | gzip > backup_$(date +%Y%m%d_%H%M%S).sql.gz
```

### Restaurar Backup
```bash
# Se arquivo descompactado
mysql -u root -p snaphubb < backup_20260102_120000.sql

# Se arquivo compactado
gunzip < backup_20260102_120000.sql.gz | mysql -u root -p snaphubb
```

### Backup Automático (Cron Job)
Adicione ao crontab:
```bash
# Fazer backup diariamente às 2 da manhã
0 2 * * * mysqldump -u root -pSUA_SENHA snaphubb | gzip > /backups/snaphubb_$(date +\%Y\%m\%d).sql.gz
```

---

## 📝 ESTRUTURA DO BANCO ATUAL

| Tabela | Registros | Função |
|--------|-----------|--------|
| `users` | 1+ | Usuários do sistema |
| `roles` | Admin | Papéis/Permissões |
| `plan_limitation` | 5 | Limites de Planos |
| `plans` | N | Planos de Assinatura |
| `subscriptions` | N | Assinaturas de Usuários |
| ... | ... | [Ver migrations] |

**Total de Migrations:** 76 executadas

---

## 🔐 Variáveis de Ambiente - Produção

Crie um `.env` em produção com:

```env
APP_NAME=SnapHubb
APP_ENV=production
APP_DEBUG=false
APP_URL=https://seu-dominio.com

# Database
DB_CONNECTION=mysql
DB_HOST=seu-host-mysql
DB_PORT=3306
DB_DATABASE=snaphubb_prod
DB_USERNAME=snap_user
DB_PASSWORD=SENHA_MUITO_FORTE_AQUI

# Cache
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

# Mail
MAIL_MAILER=smtp
MAIL_HOST=seu-smtp.com
MAIL_PORT=587
MAIL_USERNAME=seu-email@com
MAIL_PASSWORD=sua-senha
MAIL_FROM_ADDRESS=noreply@snaphubb.com

# Storage
FILESYSTEM_DISK=s3
AWS_ACCESS_KEY_ID=xxx
AWS_SECRET_ACCESS_KEY=xxx
AWS_BUCKET=seu-bucket

# MeiliSearch
MEILISEARCH_HOST=http://seu-meilisearch:7700
```

---

## ⚡ Checklist Pré-Produção

- [ ] `.env` configurado corretamente
- [ ] `APP_KEY` gerado (`php artisan key:generate`)
- [ ] Database migrations executadas
- [ ] Permissões admin sincronizadas
- [ ] Assets compilados (`npm run production`)
- [ ] Caches limpos e recompilados
- [ ] Logs configurados
- [ ] Backup do banco feito
- [ ] SSL/HTTPS instalado
- [ ] Firewall configurado
- [ ] Monitoramento ativo

---

## 🚨 Recuperação de Desastres

### Banco corrompido
```bash
# Restaure do backup mais recente
gunzip < backup_recente.sql.gz | mysql -u root -p snaphubb

# Verifique integridade
php artisan migrate:status
```

### Perdeu dados crítico
1. Verifique backup anterior
2. Restaure database backup
3. Execute `php artisan cache:clear`
4. Teste em staging antes de ir para prod

### Servidor fora do ar
```bash
# Verifique status
php artisan tinker
# Se conectar, o servidor está ok

# Reinicie serviços
sudo systemctl restart php-fpm
sudo systemctl restart mysql
sudo systemctl restart nginx
```

---

## 📊 Status Atual do Banco (2 jan 2026)

```
✅ Banco: snaphubb
✅ Usuários: 1 admin criado
✅ Permissões: 229 sincronizadas
✅ Plan Limitations: 5 criadas
✅ Migrations: 76/76 executadas
✅ Seeders: Executadas
✅ Assets: Compilados
```

---

## 📞 Suporte & Troubleshooting

| Problema | Solução |
|----------|---------|
| Migrations não rodam | `php artisan migrate --force` |
| Permissões perdidas | `php artisan db:seed --class=FixAdminPermissionsSeeder` |
| Assets não aparecem | `npm run production && php artisan cache:clear` |
| Banco lento | Cheque índices: `ANALYZE TABLE users;` |
| Login não funciona | Verifique `.env` DB_* credenciais |
| Erro 500 | Cheque `storage/logs/laravel.log` |

---

## 🔄 Workflow de Desenvolvimento → Produção

```
1. Desenvolvimento Local
   ├─ npm run dev
   └─ php artisan serve

2. Testes
   ├─ Teste todas features
   └─ Verifique banco

3. Staging/Pré-Prod
   ├─ Deploy código
   ├─ Rode migrations
   └─ Teste novamente

4. Produção
   ├─ Backup banco
   ├─ Deploy código
   ├─ Rode migrations --force
   ├─ Compile assets
   └─ Limpe caches
```

---

## 💡 Melhores Práticas

✅ **Sempre faça backup antes de qualquer alteração**  
✅ **Nunca execute `migrate:fresh` em produção**  
✅ **Use git para controlar todas as mudanças**  
✅ **Teste em staging antes de ir para prod**  
✅ **Mantenha logs e monitore erros**  
✅ **Documente alterações no banco**  
✅ **Versione seus backups**  
✅ **Teste restauração de backups regularmente**

---

**Documento válido a partir de:** 2 de janeiro de 2026  
**Última revisão:** Hoje  
**Próxima revisão:** A cada release
