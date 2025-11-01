#!/bin/bash
# Script para executar o container localmente para testes

set -e

# Cores
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

CONTAINER_NAME="cozinha-sabore"
IMAGE_NAME="leonardorennerdev/cozinha-sabore:latest"
PORT="8080"

echo -e "${BLUE}╔════════════════════════════════════════╗${NC}"
echo -e "${BLUE}║   🚀 Run Local Container              ║${NC}"
echo -e "${BLUE}║   Cozinha Sabore                      ║${NC}"
echo -e "${BLUE}╚════════════════════════════════════════╝${NC}"
echo ""

# Parar e remover container existente
if [ "$(docker ps -aq -f name=${CONTAINER_NAME})" ]; then
    echo -e "${YELLOW}⚠️  Removendo container existente...${NC}"
    docker stop ${CONTAINER_NAME} 2>/dev/null || true
    docker rm ${CONTAINER_NAME} 2>/dev/null || true
fi

echo -e "${GREEN}🚀 Iniciando container...${NC}"
echo ""

docker run -d \
  --name ${CONTAINER_NAME} \
  -p ${PORT}:80 \
  -e APP_ENV=production \
  -e APP_DEBUG=false \
  -e APP_URL=http://localhost:${PORT} \
  -e BACKEND_API_URL=http://3.90.155.156:8080 \
  -e USE_BACKEND_API=true \
  ${IMAGE_NAME}

if [ $? -eq 0 ]; then
    echo ""
    echo -e "${GREEN}╔════════════════════════════════════════╗${NC}"
    echo -e "${GREEN}║   ✅ Container iniciado com sucesso!  ║${NC}"
    echo -e "${GREEN}╚════════════════════════════════════════╝${NC}"
    echo ""
    echo -e "${BLUE}📍 Acesse em:${NC} ${GREEN}http://localhost:${PORT}${NC}"
    echo ""
    echo -e "${BLUE}📋 Comandos úteis:${NC}"
    echo -e "   ${YELLOW}docker logs -f ${CONTAINER_NAME}${NC}     # Ver logs"
    echo -e "   ${YELLOW}docker exec -it ${CONTAINER_NAME} sh${NC}  # Entrar no container"
    echo -e "   ${YELLOW}docker stop ${CONTAINER_NAME}${NC}         # Parar container"
    echo ""
else
    echo -e "${RED}❌ Erro ao iniciar container!${NC}"
    exit 1
fi

