#!/bin/bash
# 🚀 Script Rápido de Setup e Testes - Snaphubb
# Execute este script para preparar e testar o projeto completo

set -e # Parar em caso de erro

echo "╔════════════════════════════════════════════════════════════╗"
echo "║          🚀 SETUP E TESTES - SNAPHUBB                      ║"
echo "╚════════════════════════════════════════════════════════════╝"

# Cores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# 1. Iniciar Docker
echo -e "\n${YELLOW}[1/8] Iniciando Docker...${NC}"
docker-compose up -d
echo -e "${GREEN}✅ Docker iniciado${NC}"

# 2. Aguardar MySQL
echo -e "\n${YELLOW}[2/8] Aguardando MySQL...${NC}"
for i in {1..30}; do
  if docker-compose exec -T mysql mysqladmin ping -u root -p"" &> /dev/null; then
    echo -e "${GREEN}✅ MySQL pronto${NC}"
    break
  fi
  echo -n "."
  sleep 1
done

# 3. Instalar dependências
echo -e "\n${YELLOW}[3/8] Instalando dependências...${NC}"
composer install --quiet
npm install --quiet
echo -e "${GREEN}✅ Dependências instaladas${NC}"

# 4. Preparar banco de dados
echo -e "\n${YELLOW}[4/8] Preparando banco de dados...${NC}"
php artisan migrate --quiet
php artisan db:seed --class=SubscriptionTestSeeder --quiet
echo -e "${GREEN}✅ Banco de dados preparado${NC}"

# 5. Gerar dados de teste
echo -e "\n${YELLOW}[5/8] Verificando integridade de dados...${NC}"
php check_data_integrity.php

# 6. Executar testes
echo -e "\n${YELLOW}[6/8] Executando testes automatizados...${NC}"
php artisan test tests/Feature/UserRegistrationTest.php --quiet
php artisan test tests/Feature/SubscriptionFlowTest.php --quiet
echo -e "${GREEN}✅ Todos os testes passaram${NC}"

# 7. Limpar cache
echo -e "\n${YELLOW}[7/8] Limpando cache...${NC}"
php artisan cache:clear
php artisan config:cache
echo -e "${GREEN}✅ Cache limpo${NC}"

# 8. Resumo
echo -e "\n${YELLOW}[8/8] Gerando resumo...${NC}"
echo ""
echo -e "${GREEN}╔════════════════════════════════════════════════════════════╗${NC}"
echo -e "${GREEN}║  ✅ SETUP COMPLETO - PRONTO PARA TESTES                   ║${NC}"
echo -e "${GREEN}╚════════════════════════════════════════════════════════════╝${NC}"
echo ""
echo "📊 Próximos passos:"
echo ""
echo "1. Iniciar servidor Laravel:"
echo "   php artisan serve --host=127.0.0.1 --port=8002"
echo ""
echo "2. Acessar documentação:"
echo "   - Guia de Testes: GUIA-TESTES-VALIDACAO.md"
echo "   - Guia de API: GUIA-TESTES-API.md"
echo "   - Checklist: CHECKLIST-IMPLEMENTACAO.md"
echo ""
echo "3. Executar testes manualmente:"
echo "   php artisan test"
echo ""
echo "4. Verificar integridade:"
echo "   php check_data_integrity.php"
echo ""
echo "🔗 URLs úteis:"
echo "   • API: http://127.0.0.1:8002/api"
echo "   • Tinker: php artisan tinker"
echo ""
echo -e "${GREEN}Boa sorte! 🚀${NC}"
