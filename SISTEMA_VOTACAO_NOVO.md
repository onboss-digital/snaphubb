# Sistema de Votação Atualizado - Documentação

## 🎯 Mudanças Implementadas

### 1. **Modal CTA (Call To Action)**
- **Arquivo**: `Modules/Frontend/Resources/views/components/partials/modals/ranking-cta-modal.blade.php`
- **Componente**: `app/View/Components/RankingModal.php`
- **Funcionalidade**: 
  - Modal simples que incentiva o usuário a participar da votação
  - Mensagem: "Parece que você ainda não votou esta semana na creator mais requisitada da comunidade"
  - Mostra quantos votos o usuário tem disponíveis (0-3)
  - Botão CTA claro redirecionando para `/voting`
  - Aparece automaticamente quando usuário entra no site e ainda não votou

### 2. **Limite de 3 Votos por Período**
- **Arquivo**: `Modules/Voting/Http/Controllers/VotingController.php`
- **Método**: `storeVote()`
- **Regras**:
  - Cada usuário pode votar até 3 vezes por período de votação
  - Período definido pela data início/fim do ranking
  - Após atingir 3 votos, retorna erro 429 (Too Many Requests)
  - Novo período = nova votação = reseta o contador

**Exemplo**:
- Votação iniciada em 04/01/2026
- Encerrada em 06/01/2026
- Usuário pode votar 3 vezes neste período
- Em 07/01/2026, novo ranking = 3 votos novos

### 3. **Sistema de Sugestões (Separado)**
- **Endpoint**: `POST /api/v1/voting/suggest`
- **Método**: `storeSuggestion()` no VotingController
- **Campos**:
  ```json
  {
    "ranking_id": 1,
    "sugestion_name": "Nome da Creator",
    "sugestion_link": "https://link-do-conteudo.com"
  }
  ```
- **Validações**:
  - Nome obrigatório (max 255 caracteres)
  - Link obrigatório (deve ser URL válida)
  - Ranking deve estar ativo
  - Usuário deve ter acesso à feature

- **Resposta de sucesso**:
  ```json
  {
    "success": true,
    "message": "Sugestão registrada com sucesso!"
  }
  ```

### 4. **Visualização de Sugestões (Backend)**
- **Local**: `/app/users/ranking/edit/{id}`
- **Arquivo**: `Modules/User/Resources/views/backend/rankings/edit.blade.php`
- **Função**: Lista todas as sugestões da comunidade para análise
- **Campos exibidos**:
  - Nome da creator sugerida
  - Link/site do conteúdo
  - Usuário que sugeriu
  - Data da sugestão

## 📊 Fluxo do Sistema

### Usuário Não Votou Ainda
1. Acessa o site
2. Modal CTA aparece (automático)
3. Clica em "Votar Agora" → redireciona para `/voting`
4. Vota em 1-3 candidatos
5. Modal de sugestões aparece se criou RankingResponse mas sem vote
6. Pode sugerir creator usando o formulário
7. Dados salvos em `ranking_responses` (sugestion_name, sugestion_link)

### Usuário Já Votou 3x
1. Modal CTA não aparece (limite atingido)
2. Acesso a `/voting` ainda funciona
3. Vê o Top 3 e todos os candidatos
4. Não pode clicar em "Votar" novamente (botão desabilitado)
5. Pode ver sugestões da comunidade

### Novo Período de Votação
1. Admin cria novo ranking em `/app/users/ranking`
2. Define novas datas (start_date, end_date)
3. Todos os usuários podem votar 3 vezes novamente
4. Contador de votos reseta automaticamente

## 🗄️ Banco de Dados

### Tabela: ranking_responses
```sql
- id (bigint)
- user_id (int) - Quem votou/sugeriu
- ranking_id (int) - Em qual ranking
- content_slug (varchar) - O que foi votado (null se apenas sugestão)
- response_date (date) - Quando votou
- sugestion_name (text) - Nome da creator sugerida
- sugestion_link (varchar) - Link do conteúdo
- created_at, updated_at
```

## 📱 Frontend Integration

### Adicionar formulário de sugestões em `/voting`

```javascript
// JavaScript para enviar sugestão
async function submitSuggestion() {
    const name = document.getElementById('suggestion-name').value;
    const link = document.getElementById('suggestion-link').value;
    const rankingId = document.getElementById('ranking-id').value;

    const response = await fetch('/api/v1/voting/suggest', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            ranking_id: rankingId,
            sugestion_name: name,
            sugestion_link: link
        })
    });

    const data = await response.json();
    if (data.success) {
        alert(data.message);
        // Limpar formulário
    } else {
        alert('Erro: ' + data.message);
    }
}
```

## ✅ Checklist de Testes

- [ ] Modal CTA aparece quando usuário entra
- [ ] Modal redireciona para `/voting`
- [ ] Primeiro voto funciona e salva
- [ ] Segundo voto funciona
- [ ] Terceiro voto funciona
- [ ] Quarto voto mostra erro "Limite atingido"
- [ ] Sugestão pode ser enviada via API
- [ ] Sugestão aparece em `/app/users/ranking/edit/1`
- [ ] Novo ranking reseta o contador de votos
- [ ] Usuário pode votar 3x no novo ranking

## 🔗 Rotas Relacionadas

- `GET /voting` - Página de votação
- `POST /api/v1/voting/vote` - Votar em candidato
- `POST /api/v1/voting/suggest` - Sugerir creator
- `GET /api/v1/voting/top-3` - Obter top 3 candidatos
- `GET /api/v1/voting/all-candidates` - Obter todos os candidatos
- `GET /app/users/ranking` - Listar rankings (admin)
- `POST /app/users/ranking` - Criar novo ranking (admin)
- `GET /app/users/ranking/edit/{id}` - Editar ranking e ver sugestões (admin)
