# 🔗 Integração com Backend Java Spring Boot

Este documento descreve como o sistema Laravel está integrado com o backend Java Spring Boot.

## 📋 Configuração

### Variáveis de Ambiente

Adicione as seguintes variáveis ao arquivo `.env`:

```env
# URL do backend Java
BACKEND_API_URL=http://52.201.117.189:8080

# Usar backend API (true) ou arquivo local (false)
USE_BACKEND_API=true
```

## 🔐 Autenticação

O backend Java utiliza Spring Security com `@AuthenticationPrincipal UserDetails`. 

**Login implementado**: O sistema já está totalmente integrado com o endpoint de login do backend!

### Endpoint de Login

**POST /restaurantes/login**

O sistema faz login automaticamente usando email e senha. Suporta duas formas de autenticação:

1. **JWT Token** (se o backend retornar token no header `Authorization` ou no corpo da resposta)
2. **Basic Auth** (se o backend não retornar token, usa email/senha para próximas requisições)

### Fluxo de Autenticação

1. Usuário faz login no Laravel
2. Laravel envia credenciais para `POST /restaurantes/login`
3. Backend retorna objeto `Restaurante` (200 OK) ou erro (401 Unauthorized)
4. Se sucesso, Laravel:
   - Armazena dados do restaurante na sessão
   - Configura token JWT (se disponível) OU Basic Auth (senão)
   - Todas as próximas requisições usam essa autenticação automaticamente

### Exemplo de Uso

O login já está integrado na tela de login do Laravel (`/login`). Basta usar email e senha cadastrados no backend.

## 📡 Endpoints Utilizados

### POST /restaurantes/login
Faz login no sistema.

**Body (LoginRequest):**
```json
{
  "email": "restaurante@email.com",
  "password": "senha123"
}
```

**Resposta de sucesso (200):**
```json
{
  "id": 1,
  "nome": "Restaurante Exemplo",
  "cnpj": "12.345.678/0001-90",
  "email": "restaurante@email.com",
  "telefone": "(11) 1234-5678",
  ...
}
```

**Resposta de erro (401):**
```json
{
  "message": "Email ou senha incorretos."
}
```

### GET /pedidos
Lista todos os pedidos do **cliente** autenticado.

**Resposta esperada:**
```json
[
  {
    "id": 1,
    "cliente": { ... },
    "restaurante": { ... },
    "itens": [ ... ],
    "status": "NOVO",
    "criadoEm": "2024-01-01T10:00:00"
  }
]
```

### GET /pedidos/restaurante
Lista todos os pedidos do **restaurante** autenticado.

**Resposta esperada:**
```json
[
  {
    "id": 1,
    "cliente": { ... },
    "restaurante": { ... },
    "itens": [ ... ],
    "status": "NOVO",
    "criadoEm": "2024-01-01T10:00:00"
  }
]
```

**Nota:** Este endpoint é usado pelo sistema Laravel quando um restaurante está logado.

### POST /pedidos
Cria um novo pedido.

**Body esperado (PedidoRequest):**
```json
{
  "restauranteId": 1,
  "itens": [
    {
      "itemRestauranteId": 1,
      "quantidade": 2,
      "observacoes": "..."
    }
  ],
  "observacoesGerais": "..."
}
```

### PUT /pedidos/{id}/status?status={status}
Atualiza o status de um pedido (para **clientes**).

**Status permitidos:**
- `NOVO`
- `EM_PREPARO`
- `CONCLUIDO`
- `CANCELADO`

### PUT /pedidos/{id}/status-restaurante?status={status}
Atualiza o status de um pedido (para **restaurantes**).

**Status permitidos:**
- `NOVO`
- `EM_PREPARO`
- `CONCLUIDO`
- `CANCELADO`

**Nota:** Este endpoint é usado pelo sistema Laravel quando um restaurante atualiza o status de um pedido.

## 🏗️ Arquitetura

### Serviços Criados

1. **ApiPedidoService** (`app/Services/ApiPedidoService.php`)
   - Gerencia comunicação HTTP com o backend
   - Trata autenticação via tokens
   - Trata erros e exceções

2. **PedidoService** (atualizado)
   - Mantém compatibilidade com sistema antigo (arquivo JSON)
   - Usa `ApiPedidoService` quando `USE_BACKEND_API=true`
   - Faz fallback automático em caso de erros

### Fluxo de Dados

```
View (Blade) 
  → Controller 
    → PedidoService 
      → ApiPedidoService 
        → Backend Java (HTTP)
```

## 🔄 Funcionalidades Implementadas

- ✅ **Login automático** com backend Java
- ✅ Listagem de pedidos do restaurante autenticado (GET /pedidos/restaurante)
- ✅ Atualização de status por restaurante (PUT /pedidos/{id}/status-restaurante)
- ✅ Todos os status suportados: NOVO, EM_PREPARO, CONCLUIDO, CANCELADO
- ✅ Tratamento de erros robusto
- ✅ Suporte a múltiplos formatos de resposta
- ✅ Gerenciamento de tokens de autenticação (JWT ou Basic Auth)
- ✅ Interface visual para todos os status
- ✅ Logout com limpeza de credenciais
- ✅ Filtragem de segurança adicional no frontend

## ⚠️ Notas Importantes

1. **Autenticação**: ✅ **JÁ IMPLEMENTADA!**
   - Login automático no endpoint `/restaurantes/login`
   - Suporte a JWT Token (se o backend fornecer)
   - Suporte a Basic Auth (fallback automático)
   - Gerenciamento automático de sessão

2. **Estrutura de Dados**: O sistema é flexível e aceita diferentes formatos de resposta do backend, mas é recomendado seguir o padrão mostrado nos endpoints.

3. **Tratamento de Erros**: Todos os erros são logados e exibidos ao usuário de forma amigável.

4. **Fallback**: Se `USE_BACKEND_API=false`, o sistema usa o arquivo `pedidos.json` como antes.

## 🧪 Testando a Integração

1. Configure a URL do backend no `.env`:
   ```env
   BACKEND_API_URL=http://52.201.117.189:8080
   USE_BACKEND_API=true
   ```

2. Certifique-se de que o backend está acessível

3. Faça login usando credenciais do backend:
   - Acesse `/login`
   - Use email e senha de um restaurante cadastrado no backend
   - O sistema fará login automaticamente no backend Java

4. Teste as funcionalidades:
   - Visualize pedidos (GET /pedidos)
   - Atualize status dos pedidos (PUT /pedidos/{id}/status)
   - Verifique histórico de pedidos

## 📝 Próximos Passos

- [x] ✅ Implementar autenticação completa com backend
- [ ] Adicionar cache para melhorar performance
- [ ] Implementar retry automático em caso de falhas
- [ ] Adicionar testes automatizados
- [ ] Implementar refresh de token (se usar JWT)

