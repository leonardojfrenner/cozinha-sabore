# Dockerfile para Laravel - Cozinha Sabore
# Build: docker build -t leonardorennerdev/cozinha-sabore:latest .
# Push: docker push leonardorennerdev/cozinha-sabore:latest

FROM php:8.2-fpm-alpine AS base

# Instalar dependências do sistema
RUN apk add --no-cache \
    bash \
    curl \
    libpng-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    nginx \
    supervisor \
    && docker-php-ext-install pdo pdo_mysql gd zip opcache

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configurar diretório de trabalho
WORKDIR /var/www/html

# Copiar composer files primeiro (cache layer)
COPY composer.json composer.lock ./

# Instalar dependências PHP (production)
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist

# Copiar código da aplicação
COPY . .

# Completar instalação do Composer
RUN composer dump-autoload --optimize

# Criar diretórios necessários
RUN mkdir -p /var/log/supervisor

# Configurar permissões
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/bootstrap/cache

# Copiar configurações do Nginx
COPY docker/nginx.conf /etc/nginx/http.d/default.conf

# Copiar configuração do Supervisor
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Copiar script de entrypoint
COPY docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

# Expor porta 80
EXPOSE 80

# Entrypoint
ENTRYPOINT ["/entrypoint.sh"]

# Comando padrão
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]

