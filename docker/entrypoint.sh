#!/bin/bash
set -e

echo "🚀 Iniciando Cozinha Sabore..."

# Aguardar alguns segundos para garantir que tudo está pronto
sleep 2

# Criar .env se não existir
if [ ! -f .env ]; then
    echo "📝 Criando arquivo .env..."
    cp .env.example .env 2>/dev/null || echo "⚠️  .env.example não encontrado"
fi

# Gerar chave da aplicação se não existir
if ! grep -q "APP_KEY=base64:" .env; then
    echo "🔑 Gerando APP_KEY..."
    php artisan key:generate --force
fi

# Criar diretórios necessários
echo "📁 Criando diretórios de storage..."
mkdir -p storage/framework/{sessions,views,cache}
mkdir -p storage/logs
mkdir -p bootstrap/cache

# Configurar permissões
echo "🔐 Configurando permissões..."
chown -R www-data:www-data /var/www/html/storage
chown -R www-data:www-data /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage
chmod -R 775 /var/www/html/bootstrap/cache

# Limpar e otimizar cache
echo "🧹 Otimizando aplicação..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "✅ Aplicação pronta!"
echo "🌐 Acesse em: http://localhost"
echo ""

# Executar comando passado como argumento
exec "$@"

