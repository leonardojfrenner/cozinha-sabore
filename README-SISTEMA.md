# 🍽️ Sistema Cozinha Sabore

Sistema de gerenciamento de pedidos para restaurantes desenvolvido em Laravel.

## 🚀 Funcionalidades

- **Login de Restaurantes**: Autenticação usando CNPJ e senha
- **Visualização de Pedidos**: Lista todos os pedidos do restaurante
- **Alteração de Status**: Marcar pedidos como concluídos
- **Histórico**: Visualizar todos os pedidos processados
- **Logout**: Sair do sistema

## 📋 Dados de Teste

### Credenciais de Login

**Restaurante 1 - Cozinha da Mãe:**
- Email: `cozinha@email.com`
- Senha: `123456`

**Restaurante 2 - Temakeria:**
- Email: `temaki@email.com`
- Senha: `123456`

## 🏗️ Estrutura do Sistema

### Arquivos Principais

- `app/Services/PedidoService.php` - Serviço para gerenciar dados dos pedidos
- `app/Http/Controllers/PedidoController.php` - Controlador dos pedidos
- `app/Http/Controllers/Auth/RestauranteLoginController.php` - Controlador de autenticação
- `pedidos.json` - Arquivo com dados de teste dos pedidos
- `resources/views/` - Views do sistema

### Rotas

- `/` - Redireciona para login
- `/login` - Tela de login
- `/pedidos` - Lista de pedidos (requer autenticação)
- `/pedidos/historico` - Histórico de pedidos (requer autenticação)

## 🔧 Como Usar

1. **Acesse o sistema**: Navegue para a URL do projeto
2. **Faça login**: Use uma das credenciais de teste
3. **Visualize pedidos**: Veja todos os pedidos do restaurante
4. **Altere status**: Marque pedidos como concluídos
5. **Veja histórico**: Acesse o histórico de pedidos
6. **Logout**: Saia do sistema

## 📊 Dados dos Pedidos

Os pedidos contêm:

- **Informações do Cliente**: Nome, telefone, email, CPF, endereço
- **Itens do Pedido**: Nome, descrição, preço, quantidade
- **Modificações**: Ingredientes removidos/adicionados
- **Observações**: Observações específicas do pedido
- **Status**: NOVO ou CONCLUIDO
- **Data**: Data e hora de criação

## 🎨 Interface

- Design responsivo com Tailwind CSS
- Cores temáticas (laranja/âmbar)
- Interface intuitiva e moderna
- Feedback visual para ações

## 🔄 Integração com API

O sistema está preparado para integração com API externa:

- O `PedidoService` pode ser facilmente adaptado para consumir APIs
- Estrutura de dados compatível com formato JSON
- Separação clara entre lógica de negócio e apresentação

## 📝 Próximos Passos

- Integração com API real
- Implementação de banco de dados
- Adição de mais funcionalidades
- Melhorias na interface
- Sistema de notificações

---

**Desenvolvido com ❤️ usando Laravel e Tailwind CSS**
