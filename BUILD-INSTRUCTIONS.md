# 🐳 Instruções de Build e Deploy - Docker

## 📋 Pré-requisitos

- Docker instalado e em execução
- Conta no Docker Hub (username: `leonardorennerdev`)
- Estar logado no Docker Hub

---

## 🚀 Método 1: Build e Push Manual (Recomendado)

### 1️⃣ Build da Imagem

```bash
docker build -t leonardorennerdev/cozinha-sabore:latest .
```

### 2️⃣ Login no Docker Hub

```bash
docker login
# Username: leonardorennerdev
# Password: [sua senha]
```

### 3️⃣ Push para Docker Hub

```bash
docker push leonardorennerdev/cozinha-sabore:latest
```

### 4️⃣ (Opcional) Push com Versão Específica

```bash
docker tag leonardorennerdev/cozinha-sabore:latest leonardorennerdev/cozinha-sabore:v1.0.0
docker push leonardorennerdev/cozinha-sabore:v1.0.0
```

---

## 🎯 Método 2: Script Automatizado (Linux/Mac)

### Dar permissão de execução
```bash
chmod +x docker/build-and-push.sh
```

### Executar script
```bash
./docker/build-and-push.sh
```

O script irá:
- ✅ Verificar se Docker está instalado
- ✅ Fazer build da imagem
- ✅ Perguntar qual versão/tag usar
- ✅ Perguntar se deseja fazer push
- ✅ Fazer login se necessário
- ✅ Push para Docker Hub

---

## 🧪 Testar Localmente

### Opção 1: Comando direto
```bash
docker run -d \
  --name cozinha-sabore \
  -p 8080:80 \
  -e APP_ENV=production \
  -e APP_DEBUG=false \
  -e BACKEND_API_URL=http://3.90.155.156:8080 \
  leonardorennerdev/cozinha-sabore:latest
```

### Opção 2: Script automatizado (Linux/Mac)
```bash
chmod +x docker/run-local.sh
./docker/run-local.sh
```

### Acessar
```
http://localhost:8080
```

---

## 🌐 Deploy em Servidor de Produção

### 1. Conectar ao servidor via SSH

```bash
ssh user@seu-servidor.com
```

### 2. Pull da imagem

```bash
docker pull leonardorennerdev/cozinha-sabore:latest
```

### 3. Executar container

```bash
docker run -d \
  --name cozinha-sabore \
  --restart unless-stopped \
  -p 80:80 \
  -e APP_ENV=production \
  -e APP_DEBUG=false \
  -e APP_URL=http://seu-dominio.com \
  -e BACKEND_API_URL=http://3.90.155.156:8080 \
  -e USE_BACKEND_API=true \
  leonardorennerdev/cozinha-sabore:latest
```

### 4. Verificar logs

```bash
docker logs -f cozinha-sabore
```

---

## 🔄 Atualizar Aplicação em Produção

```bash
# 1. Pull da nova versão
docker pull leonardorennerdev/cozinha-sabore:latest

# 2. Parar container atual
docker stop cozinha-sabore

# 3. Remover container antigo
docker rm cozinha-sabore

# 4. Executar nova versão
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

## 📊 Comandos Úteis

### Ver logs em tempo real
```bash
docker logs -f cozinha-sabore
```

### Entrar no container
```bash
docker exec -it cozinha-sabore sh
```

### Ver status do container
```bash
docker ps -a
```

### Parar container
```bash
docker stop cozinha-sabore
```

### Remover container
```bash
docker rm cozinha-sabore
```

### Ver imagens locais
```bash
docker images
```

### Limpar imagens não utilizadas
```bash
docker image prune -a
```

### Verificar uso de recursos
```bash
docker stats cozinha-sabore
```

---

## 🔧 Variáveis de Ambiente Disponíveis

| Variável | Descrição | Valor Padrão |
|----------|-----------|--------------|
| `APP_NAME` | Nome da aplicação | Cozinha Sabore |
| `APP_ENV` | Ambiente (local/production) | production |
| `APP_DEBUG` | Modo debug | false |
| `APP_URL` | URL da aplicação | http://localhost |
| `BACKEND_API_URL` | URL do backend Java | http://3.90.155.156:8080 |
| `USE_BACKEND_API` | Usar API backend | true |

---

## 🐛 Troubleshooting

### Container não inicia
```bash
# Ver logs
docker logs cozinha-sabore

# Ver detalhes do container
docker inspect cozinha-sabore
```

### Erro de permissão
```bash
# Entrar no container e verificar
docker exec -it cozinha-sabore sh
ls -la /var/www/html/storage
```

### Rebuild sem cache
```bash
docker build --no-cache -t leonardorennerdev/cozinha-sabore:latest .
```

### Limpar tudo e começar de novo
```bash
docker stop cozinha-sabore
docker rm cozinha-sabore
docker rmi leonardorennerdev/cozinha-sabore:latest
docker build -t leonardorennerdev/cozinha-sabore:latest .
```

---

## 📦 Informações da Imagem

- **Base**: PHP 8.2 FPM Alpine
- **Web Server**: Nginx
- **Process Manager**: Supervisor
- **Porta**: 80
- **Tamanho**: ~150-200MB
- **Multi-stage**: Não (single stage otimizado)

---

## 🎯 Workflow Completo (Build → Push → Deploy)

```bash
# 1. Build
docker build -t leonardorennerdev/cozinha-sabore:latest .

# 2. Testar localmente
docker run -d --name test -p 8080:80 leonardorennerdev/cozinha-sabore:latest
# Testar em http://localhost:8080
docker stop test && docker rm test

# 3. Login Docker Hub
docker login

# 4. Push
docker push leonardorennerdev/cozinha-sabore:latest

# 5. Deploy no servidor
ssh user@servidor
docker pull leonardorennerdev/cozinha-sabore:latest
docker stop cozinha-sabore && docker rm cozinha-sabore
docker run -d --name cozinha-sabore --restart unless-stopped -p 80:80 leonardorennerdev/cozinha-sabore:latest
```

---

## ✅ Checklist de Deploy

- [ ] Build da imagem local bem-sucedido
- [ ] Teste local funcionando (http://localhost:8080)
- [ ] Login no Docker Hub realizado
- [ ] Push para Docker Hub concluído
- [ ] Verificar imagem no Docker Hub: https://hub.docker.com/r/leonardorennerdev/cozinha-sabore
- [ ] Pull no servidor de produção
- [ ] Container rodando em produção
- [ ] Aplicação acessível via navegador
- [ ] Logs sem erros críticos

---

## 🔗 Links Úteis

- Docker Hub: https://hub.docker.com/r/leonardorennerdev/cozinha-sabore
- Documentação Docker: https://docs.docker.com
- Laravel Docker: https://laravel.com/docs/deployment

