# 🎯 GUIA RÁPIDO - Testando a Feature em 5 Minutos

## ⚡ Quick Start

### 1️⃣ Acessar a página
```
http://127.0.0.1:8002/voting
```

### 2️⃣ O que você verá:

**Se NÃO tem acesso (recomendado testar primeiro):**
- 🔒 Modal com explicação
- 💰 Valor do plano
- 3 benefícios listados
- Botão "Comprar Acesso"

**Se TEM acesso:**
- 👑 Top 3 da semana com rankings
- 💜 Grid de atrizes para votar
- ⭐ Contador de votos

---

## 🧪 Teste Rápido (SEM ACESSO)

1. **Abra em incógnito** para testar sem acesso
2. **Acesse:** `/voting`
3. **Veja:** Modal explicativo
4. **Clique:** "Comprar Acesso" (vai para checkout)

---

## 🧪 Teste com Acesso

### Requisito: Ter um plano "Community Voting" comprado

1. **Crie o plano:**
   - Acesse: `/app/subscriptions/plans`
   - Clique: "Add New"
   - Preencha:
     ```
     Name:      Community Voting
     Slug:      community-voting
     Price:     R$ 5,00 (exemplo)
     Status:    Active
     ```

2. **Compre o plano:**
   - Acesse: `/subscriptions`
   - Clique em "Community Voting"
   - Complete o checkout

3. **Acesse a votação:**
   - Vá para: `/voting`
   - Veja o Top 3
   - Clique em qualquer atriz
   - Voto será registrado!

---

## 📍 Locais Importantes

| Página | URL | O que faz |
|--------|-----|----------|
| Votação | `/voting` | Página principal |
| Planos | `/app/subscriptions/plans` | Criar plano voting |
| Minhas Compras | `/subscription-plan` | Comprar acesso |
| Admin Atrizes | `/app/cast-crew` | Gerenciar candidatas |

---

## 🎨 Itens do Menu

No menu superior você verá:

```
Início | Filmes | Personalidades | Vídeos | Em Breve | Votação | Live TV
                                                       ↑
                                             NOVO ITEM ADICIONADO
```

- ✅ Com acesso: ❤️ Votação
- ❌ Sem acesso: 🔒 Votação

---

## 🖼️ Screenshots Esperados

### Modal (Sem Acesso)
```
┌─────────────────────────────┐
│ 🔒 Acesso Restrito          │
│                             │
│ Desbloqueie a Votação...    │
│                             │
│ ✓ Vote Ilimitadamente       │
│ ✓ Veja o Top 3 da Semana    │
│ ✓ Influencie a Comunidade   │
│                             │
│ [Cancelar] [Comprar Acesso] │
└─────────────────────────────┘
```

### Página (Com Acesso)
```
┌────────────────────────────┐
│ ❤️ Votação da Comunidade     │
│                            │
│ 👑 Top 3 da Semana        │
│ ┌──────┐ ┌──────┐ ┌──────┐│
│ │ 1º   │ │ 2º   │ │ 3º   ││
│ │ 500v │ │ 350v │ │ 200v ││
│ └──────┘ └──────┘ └──────┘│
│                            │
│ 💜 Vote Agora             │
│ ┌──┐ ┌──┐ ┌──┐ ┌──┐      │
│ │  │ │  │ │  │ │  │      │
│ │  │ │  │ │  │ │  │      │
│ │🗳│ │🗳│ │🗳│ │🗳│      │
│ └──┘ └──┘ └──┘ └──┘      │
└────────────────────────────┘
```

---

## 🐛 Se algo não funcionar

### Menu não mostra "Votação"?
```bash
php artisan optimize:clear
```
Recarregue a página (F5)

### Erro ao clicar em "Votação"?
```bash
# Verifique as rotas
php artisan route:list | grep voting
```
Esperado: 6 rotas listadas

### API não responde?
```bash
# Teste manualmente
curl http://127.0.0.1:8002/api/v1/voting/top-3
```
Deve retornar JSON

### Banco de dados vazio?
```bash
# Verifique as tabelas
php artisan tinker
>>> \DB::table('votes')->count()
>>> \DB::table('weekly_rankings')->count()
```

---

## 🎬 Demo Completa (30 segundos)

```
1. Abra incógnito
2. Vá para /voting
3. Veja modal
4. Clique "Comprar Acesso"
5. Volte (sem comprar)
6. Faça login com USER que TEM acesso
7. Vá para /voting novamente
8. Clique em "Votar"
9. Veja card mudar de cor
10. Pronto! Feature funciona! ✅
```

---

## 💡 Dicas

- 🎨 Cores mudam ao passar mouse
- 📱 Teste em mobile (F12 → Device Toggle)
- 🔄 Top 3 atualiza em tempo real
- 📊 Estatísticas em `weekly_rankings`
- 🗳️ Um voto por usuário por atriz por semana

---

## 📞 Próximos Passos

1. ✅ Feature está pronta
2. ⏳ Criar plano "Community Voting"
3. ⏳ Testar votação
4. ⏳ Ajustar preço/benefícios
5. ⏳ Deploy em produção

---

**Qualquer dúvida, veja os arquivos de documentação! 📚**

- `VOTING_FEATURE_GUIDE.md` - Documentação completa
- `VOTING_IMPLEMENTATION_COMPLETE.md` - Resumo técnico
- `VOTING_MENU_INTEGRATION.md` - Como foi adicionado ao menu
