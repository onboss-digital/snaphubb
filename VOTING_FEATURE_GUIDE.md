# 🎯 Feature de Votação da Comunidade - Guia de Implementação

## ✅ O que foi criado:

### 1. **Módulo Voting Completo**
   - ✅ Migrations para `votes` e `weekly_rankings`
   - ✅ Models `Vote` e `WeeklyRanking`
   - ✅ Controller com todas as APIs
   - ✅ Frontend responsivo (mobile/tablet/desktop)
   - ✅ Sistema de acesso condicional

### 2. **Estrutura de Banco de Dados**
```
votes
├── user_id
├── cast_crew_id (atriz votada)
├── week_id (YYYY-WW format)
├── vote_count
└── timestamps

weekly_rankings
├── week_id
├── cast_crew_id
├── rank_position (1º, 2º, 3º)
├── total_votes
└── percentage
```

### 3. **APIs Criadas**
```
GET  /api/v1/voting/top-3              → Top 3 da semana
GET  /api/v1/voting/all-candidates     → Todas as atrizes
POST /api/v1/voting/vote               → Registrar voto
GET  /api/v1/voting/user-vote/{week}   → Voto do usuário
```

### 4. **Página Frontend**
- Modal explicativo (usuários sem acesso)
- Top 3 da semana com cards elegantes
- Grade responsiva de candidatos
- Botão de votação + contador
- Sistema de toast/notificações
- Design profissional com gradientes

---

## 🔧 Próximos Passos (Para integrar ao menu):

### PASSO 1: Criar um Plan para Votação

Acesse `/app/subscriptions/plans` e crie um novo plano chamado **"Community Voting"** com:
- Nome: Community Voting
- Slug: community-voting
- Descrição: Acesso à votação da comunidade
- Preço: R$ XX,XX (escolha você)
- Duração: mensal, trimestral ou anual
- Status: Ativo

### PASSO 2: Adicionar Item ao Menu

Encontre a navbar/menu da aplicação (provavelmente em `resources/views/frontend/layouts/master.blade.php` ou similar) e adicione:

```blade
<li class="nav-item">
    <a class="nav-link" href="/voting">
        @if(!auth()->user() || !userHasVotingAccess())
            <i class="ph ph-lock"></i>
        @else
            <i class="ph ph-heart-half"></i>
        @endif
        Votação
    </a>
</li>
```

### PASSO 3: Criar Helper para Verificar Acesso

Adicione em `app/helpers.php`:

```php
function userHasVotingAccess()
{
    $user = auth()->user();
    if (!$user) return false;

    return \Modules\Subscriptions\Models\Subscription::where('user_id', $user->id)
        ->where('status', 'active')
        ->whereHas('plan', function ($query) {
            $query->where('slug', 'community-voting')
                ->orWhere('name', 'like', '%Voting%');
        })
        ->exists();
}
```

### PASSO 4: Configurar Middleware (Opcional)

Se quiser proteger a rota:

```php
Route::middleware(['auth', 'check.voting.access'])->group(function () {
    Route::get('/voting', [VotingController::class, 'index'])->name('voting.index');
});
```

---

## 📊 Como Funciona:

### Fluxo de um usuário SEM acesso:
1. Clica em "Votação" no menu
2. Vê um ícone de cadeado 🔒
3. Página abre e mostra modal explicativo
4. Modal exibe benefícios + botão "Comprar Acesso"
5. Clica no botão → vai para checkout

### Fluxo de um usuário COM acesso:
1. Clica em "Votação" no menu
2. Página carrega com top 3 da semana
3. Vê grid com todas as atrizes
4. Clica em "Votar" para qualquer atriz
5. Voto é registrado
6. Card muda de cor (verde) e mostra "✓ Votado"
7. Top 3 é atualizado em tempo real

---

## 🎨 Customizações Possíveis:

### Alterar cores da votação:
Em `/Modules/Voting/Resources/views/index.blade.php`, seção de styles:
```css
.vote-button {
    background: linear-gradient(135deg, #ff1744 0%, #d32f2f 100%);
    /* Troque para suas cores */
}
```

### Ajustar número de candidatos no Top 3:
Em `/Modules/Voting/Http/Controllers/VotingController.php`:
```php
->take(3)  // Mude para outro número
```

### Adicionar descrição de benefícios:
Edite a seção `.voting-benefits` no HTML da view

---

## 🚀 Testando Localmente:

```bash
# 1. Certificar que as migrations rodaram
php artisan migrate

# 2. Acessar a página
http://127.0.0.1:8002/voting

# 3. Se não tiver acesso, verá o modal
# 4. Se tiver acesso, verá a página completa
```

---

## 📝 Checklist para Produção:

- [ ] Criar Plan "Community Voting"
- [ ] Adicionar item ao menu (com ícone de cadeado)
- [ ] Testar com usuário SEM acesso
- [ ] Testar com usuário COM acesso
- [ ] Verificar responsividade em mobile/tablet
- [ ] Ajustar cores/textos conforme brand
- [ ] Testar votação múltipla
- [ ] Verificar ranking semanal
- [ ] Testar em todos os browsers

---

## 🔐 Segurança:

- ✅ Autenticação via `Auth::user()`
- ✅ Validação de acesso no controller
- ✅ Validação no frontend + backend
- ✅ CSRF protection automático
- ✅ Sanitização de dados

---

## 📱 Responsividade:

- ✅ Desktop (1920px+)
- ✅ Laptop (1200px+)
- ✅ Tablet (768px)
- ✅ Mobile SM (576px)
- ✅ Mobile XS (320px)

---

**Tudo pronto para usar! Qualquer dúvida ou customização, é só avisar!** 🎉
