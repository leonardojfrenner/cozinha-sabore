#!/bin/bash
# Script para Build e Push da Imagem Docker

set -e

# Cores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Variáveis
DOCKER_USERNAME="leonardorennerdev"
IMAGE_NAME="cozinha-sabore"
FULL_IMAGE_NAME="${DOCKER_USERNAME}/${IMAGE_NAME}"

echo -e "${BLUE}╔════════════════════════════════════════╗${NC}"
echo -e "${BLUE}║   🐳 Docker Build & Push Script      ║${NC}"
echo -e "${BLUE}║   Cozinha Sabore                      ║${NC}"
echo -e "${BLUE}╚════════════════════════════════════════╝${NC}"
echo ""

# Verificar se Docker está instalado
if ! command -v docker &> /dev/null; then
    echo -e "${RED}❌ Docker não está instalado!${NC}"
    exit 1
fi

echo -e "${GREEN}✅ Docker encontrado!${NC}"
echo ""

# Pedir versão (opcional)
read -p "$(echo -e ${YELLOW}Digite a versão da tag [default: latest]:${NC} )" VERSION
VERSION=${VERSION:-latest}

echo ""
echo -e "${BLUE}📦 Iniciando build da imagem...${NC}"
echo -e "${YELLOW}   Tag: ${FULL_IMAGE_NAME}:${VERSION}${NC}"
echo ""

# Build da imagem
docker build -t ${FULL_IMAGE_NAME}:${VERSION} .

if [ $? -eq 0 ]; then
    echo ""
    echo -e "${GREEN}✅ Build concluído com sucesso!${NC}"
else
    echo ""
    echo -e "${RED}❌ Erro no build!${NC}"
    exit 1
fi

# Se não for 'latest', também tagear como latest
if [ "$VERSION" != "latest" ]; then
    echo ""
    echo -e "${BLUE}🏷️  Criando tag 'latest'...${NC}"
    docker tag ${FULL_IMAGE_NAME}:${VERSION} ${FULL_IMAGE_NAME}:latest
fi

# Perguntar se deseja fazer push
echo ""
read -p "$(echo -e ${YELLOW}Deseja fazer push para Docker Hub? [s/N]:${NC} )" PUSH_CONFIRM
PUSH_CONFIRM=${PUSH_CONFIRM:-N}

if [[ "$PUSH_CONFIRM" =~ ^[Ss]$ ]]; then
    echo ""
    echo -e "${BLUE}🔐 Verificando login no Docker Hub...${NC}"
    
    # Verificar se está logado
    if ! docker info 2>/dev/null | grep -q "Username: ${DOCKER_USERNAME}"; then
        echo -e "${YELLOW}⚠️  Você precisa fazer login no Docker Hub${NC}"
        docker login
    fi
    
    echo ""
    echo -e "${BLUE}📤 Fazendo push da imagem...${NC}"
    docker push ${FULL_IMAGE_NAME}:${VERSION}
    
    if [ "$VERSION" != "latest" ]; then
        docker push ${FULL_IMAGE_NAME}:latest
    fi
    
    if [ $? -eq 0 ]; then
        echo ""
        echo -e "${GREEN}╔════════════════════════════════════════╗${NC}"
        echo -e "${GREEN}║   ✅ Processo concluído com sucesso!  ║${NC}"
        echo -e "${GREEN}╚════════════════════════════════════════╝${NC}"
        echo ""
        echo -e "${BLUE}📦 Imagens disponíveis:${NC}"
        echo -e "   ${GREEN}${FULL_IMAGE_NAME}:${VERSION}${NC}"
        if [ "$VERSION" != "latest" ]; then
            echo -e "   ${GREEN}${FULL_IMAGE_NAME}:latest${NC}"
        fi
        echo ""
        echo -e "${BLUE}🚀 Para executar:${NC}"
        echo -e "   ${YELLOW}docker pull ${FULL_IMAGE_NAME}:${VERSION}${NC}"
        echo -e "   ${YELLOW}docker run -d --name cozinha-sabore -p 8080:80 ${FULL_IMAGE_NAME}:${VERSION}${NC}"
        echo ""
    else
        echo ""
        echo -e "${RED}❌ Erro ao fazer push!${NC}"
        exit 1
    fi
else
    echo ""
    echo -e "${GREEN}✅ Build concluído!${NC}"
    echo -e "${BLUE}Para fazer push manualmente:${NC}"
    echo -e "   ${YELLOW}docker push ${FULL_IMAGE_NAME}:${VERSION}${NC}"
    echo ""
fi

