#!/bin/bash
# Script para limpar branches desnecessárias no repositório remoto
# Execute este script quando o repositório remoto estiver acessível

cd "e:\ONBOSS DIGITAL\SNAPHUBB\snaphubb"

echo "Deletando branches remotas desnecessárias..."

# Deletar branches remotas antigas
git push origin --delete boss
git push origin --delete deploy/payment-webhooks-multilang
git push origin --delete feature/check-user-exists-endpoint
git push origin --delete feature/update-core-version
git push origin --delete feature/update-core-version-backup
git push origin --delete feature/v1.2.6

echo "✓ Limpeza remota concluída!"
echo ""
echo "Estado final das branches:"
git branch -a
