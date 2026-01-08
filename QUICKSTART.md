# 🚀 Guia de Inicialização Rápida - SnapHubb

## ⚡ Na Próxima Vez que Abrir o Projeto

Se **NÃO** alterou o banco de dados, rode apenas:

```bash
# 1. Compilar assets (CSS/JS)
npm run dev

# 2. Iniciar servidor Laravel (em outro terminal)
php artisan serve
```

Depois acesse: **http://127.0.0.1:8000**

---

## 🔄 Se Precisa Resetar ou Atualizar o Banco

Se houve mudanças nas migrations:

```bash
# 1. Instalar dependências (execute uma única vez)
composer install
npm install --legacy-peer-deps

# 2. Executar migrations
php artisan migrate

# 3. (OPCIONAL) Se precisar resetar TUDO:
php artisan migrate:fresh --seed

# 4. Sincronizar permissões do admin
php artisan db:seed --class=FixAdminPermissionsSeeder

# 5. Compilar assets
npm run dev

# 6. Iniciar servidor
php artisan serve
```

---

## 📋 Checklist de Status Atual (janeiro 2, 2026)

✅ Banco de dados: **Criado e populado**
✅ Permissões do admin: **Sincronizadas (229 permissões)**
✅ Plan Limitations: **Criadas (5 tipos)**
✅ Dependências PHP: **Instaladas**
✅ Dependências JS: **Instaladas**
✅ Assets compilados: **Prontos**
✅ Servidor Laravel: **Rodando na porta 8002**

**Login de teste:**
- Email: `admin@snaphubb.com`
- Senha: `Meta10k@@`

---

## 🛠️ Troubleshooting Rápido

| Problema | Solução |
|----------|---------|
| Assets não atualizando | `npm run dev` |
| Cache desatualizado | `php artisan cache:clear` |
| Rotas não aparecem | `php artisan cache:clear && php artisan route:cache` |
| Permissões perdidas | `php artisan db:seed --class=FixAdminPermissionsSeeder` |
| Banco com erro | `php artisan migrate:fresh --seed` |

---

## 📌 Arquivo .env Atual

```env
APP_URL=http://127.0.0.1:8002
DB_HOST=127.0.0.1
DB_DATABASE=snaphubb
DB_USERNAME=root
DB_PASSWORD= (vazio - padrão local)
```

Se precisar de senha no MySQL, atualize `.env`:
```env
DB_PASSWORD=sua_senha
```

---

## 💡 Dica Importante

Para evitar problemas na próxima inicialização:
1. Não delete a pasta `database/` 
2. Não delete `.env`
3. Se trocar de máquina, copie a pasta inteira do projeto
4. Antes de fazer `migrate:fresh`, faça backup do banco!

---

**Última atualização:** 2 de janeiro de 2026
**Desenvolvedor:** SnapHubb Team
