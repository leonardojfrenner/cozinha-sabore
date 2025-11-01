# Docker - Cozinha Sabore

## 🐳 Build e Push para Docker Hub

### 1. Build da Imagem
```bash
docker build -t leonardorennerdev/cozinha-sabore:latest .
```

### 2. Login no Docker Hub
```bash
docker login
# Username: leonardorennerdev
# Password: [sua senha]
```

### 3. Push da Imagem
```bash
docker push leonardorennerdev/cozinha-sabore:latest
```

### 4. Push com Tag de Versão (Opcional)
```bash
docker tag leonardorennerdev/cozinha-sabore:latest leonardorennerdev/cozinha-sabore:v1.0.0
docker push leonardorennerdev/cozinha-sabore:v1.0.0
```

---

## 🚀 Executar Container

### Executar Localmente
```bash
docker run -d \
  --name cozinha-sabore \
  -p 8080:80 \
  -e APP_ENV=production \
  -e APP_DEBUG=false \
  -e BACKEND_API_URL=http://3.90.155.156:8080 \
  leonardorennerdev/cozinha-sabore:latest
```

### Com Variáveis de Ambiente Customizadas
```bash
docker run -d \
  --name cozinha-sabore \
  -p 8080:80 \
  -e APP_NAME="Cozinha Sabore" \
  -e APP_ENV=production \
  -e APP_DEBUG=false \
  -e APP_URL=http://localhost:8080 \
  -e BACKEND_API_URL=http://3.90.155.156:8080 \
  -e USE_BACKEND_API=true \
  leonardorennerdev/cozinha-sabore:latest
```

### Verificar Logs
```bash
docker logs -f cozinha-sabore
```

### Parar Container
```bash
docker stop cozinha-sabore
docker rm cozinha-sabore
```

---

## 🌐 Deploy em Servidor

### Baixar e Executar
```bash
# Pull da imagem
docker pull leonardorennerdev/cozinha-sabore:latest

# Executar
docker run -d \
  --name cozinha-sabore \
  --restart unless-stopped \
  -p 80:80 \
  -e APP_ENV=production \
  -e APP_DEBUG=false \
  -e BACKEND_API_URL=http://3.90.155.156:8080 \
  leonardorennerdev/cozinha-sabore:latest
```

---

## 📋 Variáveis de Ambiente Importantes

| Variável | Descrição | Padrão |
|----------|-----------|--------|
| `APP_NAME` | Nome da aplicação | Cozinha Sabore |
| `APP_ENV` | Ambiente (local/production) | production |
| `APP_DEBUG` | Debug mode | false |
| `APP_URL` | URL da aplicação | http://localhost |
| `BACKEND_API_URL` | URL do backend Java | http://3.90.155.156:8080 |
| `USE_BACKEND_API` | Usar API backend | true |

---

## 🔧 Troubleshooting

### Ver logs em tempo real
```bash
docker logs -f cozinha-sabore
```

### Entrar no container
```bash
docker exec -it cozinha-sabore sh
```

### Recriar cache
```bash
docker exec cozinha-sabore php artisan config:cache
docker exec cozinha-sabore php artisan route:cache
docker exec cozinha-sabore php artisan view:cache
```

### Rebuild completo
```bash
docker build --no-cache -t leonardorennerdev/cozinha-sabore:latest .
```

---

## 📦 Estrutura da Imagem

- **Base**: PHP 8.2 FPM Alpine (leve)
- **Web Server**: Nginx
- **Process Manager**: Supervisor
- **Portas**: 80 (HTTP)
- **Tamanho**: ~150MB (aproximado)

---

## 🎯 Comandos Rápidos

```bash
# Build + Push
docker build -t leonardorennerdev/cozinha-sabore:latest . && docker push leonardorennerdev/cozinha-sabore:latest

# Stop + Remove + Run
docker stop cozinha-sabore && docker rm cozinha-sabore && docker run -d --name cozinha-sabore -p 8080:80 leonardorennerdev/cozinha-sabore:latest

# Limpar imagens antigas
docker image prune -a
```

