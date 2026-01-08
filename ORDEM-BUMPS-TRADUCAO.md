# 🌍 Implementação: Order Bumps com Tradução Multilíngue

## 📋 Passo a Passo de Implementação

### **Passo 1: Rodar a Migration no Backend (snaphubb)**

```bash
cd /caminho/para/snaphubb
php artisan migrate
```

A migration `2025_01_07_000001_add_translations_to_order_bumps_table.php` irá adicionar os campos:
- `title_en`, `description_en`, `text_button_en` (Inglês)
- `title_es`, `description_es`, `text_button_es` (Espanhol)

**Nota:** O `title`, `description` e `text_button` originais permanecerão como **Português (padrão)**.

---

### **Passo 2: Popular os dados de tradução**

Você pode fazer isso de 3 formas:

#### **Opção A: Via Tinker (Rápido)**
```bash
php artisan tinker
```

```php
use Modules\Subscriptions\Models\OrderBump;

// Atualizar primeiro bump
OrderBump::find(1)->update([
    'title' => 'Criptografía anónima',
    'description' => 'Acesso a conteúdos ao vivo e eventos',
    'title_en' => 'Anonymous Encryption',
    'description_en' => 'Access to live content and events',
    'title_es' => 'Cifrado anónimo',
    'description_es' => 'Acceso a contenidos en vivo y eventos',
]);

// Atualizar segundo bump
OrderBump::find(2)->update([
    'title' => 'Guia Premium',
    'description' => 'Acesso ao guia completo de estratégias',
    'title_en' => 'Premium Guide',
    'description_en' => 'Access to the complete strategies guide',
    'title_es' => 'Guía Premium',
    'description_es' => 'Acceso a la guía completa de estrategias',
]);
```

#### **Opção B: Via Script (Arquivo incluído)**
```bash
php artisan tinker
> include('exemplo-update-order-bumps.php');
```

#### **Opção C: Via Admin Panel / API**
Adicionar endpoint admin para editar os bumps com as traduções.

---

### **Passo 3: Entender a Estrutura de Resposta da API**

Agora quando você chamar `/api/get-plans`, a resposta será:

```json
{
  "status": true,
  "data": [
    {
      "id": 1,
      "name": "Premium Monthly",
      "price": 29.99,
      "orderBumps": [
        {
          "id": 4,
          "external_id": "3nidg2uzc0",
          "title": "Criptografía anónima",
          "title_en": "Anonymous Encryption",
          "title_es": "Cifrado anónimo",
          "description": "Acesso a conteúdos ao vivo e eventos",
          "description_en": "Access to live content and events",
          "description_es": "Acceso a contenidos en vivo y eventos",
          "text_button": null,
          "text_button_en": null,
          "text_button_es": null,
          "price": 9.99,
          "plan_id": 1
        }
      ]
    }
  ]
}
```

---

### **Passo 4: Frontend (snaphubb-pages) já está pronto!**

A view `resources/views/livewire/page-pay.blade.php` foi atualizada para:

1. ✅ Detectar o idioma selecionado (`$selectedLanguage`)
2. ✅ Buscar o campo correto baseado no idioma:
   - Se idioma = `en` → usa `title_en`, `description_en`
   - Se idioma = `es` → usa `title_es`, `description_es`
   - Caso contrário → usa `title`, `description` (Português)
3. ✅ Usar fallback caso o campo traduzido não exista

---

## 🧪 Como Testar

### **1. Verificar dados no banco:**
```sql
SELECT id, title, title_en, title_es, description, description_en, description_es 
FROM order_bumps;
```

### **2. Testar no frontend:**

#### **Teste 1: Português**
- Abra `http://localhost:8000`
- Selecione idioma **Português**
- Veja os bumps aparecerem em **Português**

#### **Teste 2: Inglês**
- Mude para idioma **English**
- Veja os bumps aparecerem em **Inglês**

#### **Teste 3: Espanhol**
- Mude para idioma **Español**
- Veja os bumps aparecerem em **Espanhol**

---

## 📊 Mapeamento de Campos

| Campo | Português | Inglês | Espanhol |
|-------|-----------|--------|----------|
| **Título** | `title` | `title_en` | `title_es` |
| **Descrição** | `description` | `description_en` | `description_es` |
| **Botão** | `text_button` | `text_button_en` | `text_button_es` |

---

## ✨ Próximos Passos (Opcional)

1. **Admin Panel**: Criar interface para editar bumps com as 3 traduções
2. **Validation**: Validar que pelo menos `title` está preenchido
3. **Icons**: Adicionar campo `icon_url` aos bumps
4. **Original Price**: Adicionar `original_price` para mostrar desconto
5. **A/B Testing**: Adicionar campos de analytics aos bumps

---

## 🔧 Troubleshooting

### "Erro: Column 'title_en' not found"
**Solução:** A migration ainda não foi rodada. Execute:
```bash
php artisan migrate
```

### "Bumps não aparecem em inglês mesmo com title_en preenchido"
**Solução:** Limpe o cache do navegador (Ctrl+Shift+Delete) e recarregue.

### "Como alterar a ordem de prioridade de idiomas?"
**Código atual:**
```php
$langCode = match($selectedLanguage) {
    'en' => 'title_en',
    'es' => 'title_es',
    default => 'title'  // ← Português é o fallback padrão
};
```

Mude o `default` se necessário.

---

## 📝 Arquivo de Exemplos

Veja `exemplo-update-order-bumps.php` para exemplos de como atualizar os dados.

---

**Status:** ✅ Implementação Completa
