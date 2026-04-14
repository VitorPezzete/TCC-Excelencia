#  TCC Excelencia

Plataforma de **delivery** com cardápio integrado. Sistema completo para gerenciamento de produtos, pedidos, endereços de entrega e autenticação de usuários.

**Repositório**: [github.com/VitorPezzete/TCC-Excelencia](https://github.com/VitorPezzete/TCC-Excelencia)

---

##  Análise do Projeto

### Descrição Geral
O projeto Excelencia é uma plataforma de delivery desenvolvida como Trabalho de Conclusão de Curso (TCC), construída com as tecnologias mais modernas para web.

### Stack Tecnológico

#### Backend
- **Framework**: Laravel 12 (PHP 8.2+)
- **Banco de Dados**: Suporte a múltiplos bancos (SQLite, MySQL, PostgreSQL, etc)
- **ORM**: Eloquent
- **Autenticação**: Laravel Authentication
- **Queue**: Redis/Database (para processamento assíncrono)

#### Frontend
- **Build Tool**: Vite 7
- **Styling**: Tailwind CSS 4
- **JS Framework**: Vanilla JS com Axios
- **Transpilação**: Autoprefixer e PostCSS

#### Ferramentas de Desenvolvimento
- **Testing**: PHPUnit 11
- **Code Quality**: Laravel Pint
- **Logging**: Laravel Pail
- **Development Server**: Laravel Sail (Docker)

---

##  Funcionalidades Principais

### 1. **Autenticação & Autorização**
-  Registro de novos usuários
-  Sistema de login/logout
-  Middleware de autenticação
-  Suporte a perfil com dados de contato (email, telefone)

### 2. **Catálogo de Produtos**
-  Exibição de cardápio/menu
-  Produtos organizados por categoria
-  Informações detalhadas: nome, descrição, preço, imagem
-  Produtos em destaque
-  Status de ativo/inativo

### 3. **Gerenciamento de Perfil**
-  Atualização de dados pessoais
-  Alteração de senha
-  Múltiplos endereços de entrega
-  Definição de endereço padrão

### 4. **Carrinho de Compras**
-  Adição/remoção de itens (tabela: `itens_carrinho`)

### 5. **Sistema de Pedidos**
-  Criação e processamento de pedidos
-  Histórico de status do pedido
-  Itens do pedido com rastreamento

### 6. **Pagamentos**
-  Integração de sistema de pagamentos (estrutura criada)

### 7. **Promoções**
-  Sistema de cupons/descontos (tabela: `cupons`)

---

## Estrutura do Banco de Dados

### Modelos Implementados
| Modelo | Descrição | Atributos Principais |
|--------|-----------|---------------------|
| **User** | Usuários do sistema | name, email, phone, password |
| **Categoria** | Categorias de produtos | nome, descrição |
| **Produto** | Items do cardápio | nome, descricao, preco, imagem, destaque, ativo |
| **Endereco** | Endereços de entrega | user_id, rua, cidade, estado, cep |

### Tabelas de Negócio
- `cupons` - Códigos de desconto
- `pedidos` - Pedidos realizados
- `historico_status_pedido` - Rastreamento de status
- `itens_pedido` - Produtos em cada pedido
- `itens_carrinho` - Items do carrinho do usuário
- `pagamentos` - Registros de transações

---

## Rotas da Aplicação

### Públicas (sem autenticação)
```
GET  /                    → Página inicial
GET  /cardapio            → Visualizar cardápio
GET  /login               → Formulário de login
POST /cadastro            → Registrar novo usuário
POST /login               → Autenticar usuário
```

### Protegidas (requer autenticação)
```
POST /logout              → Fazer logout
GET  /perfil              → Visualizar perfil
POST /perfil/dados        → Atualizar dados pessoais
POST /perfil/senha        → Alterar senha
POST /perfil/enderecos    → Adicionar novo endereço
PUT  /perfil/enderecos/:id → Editar endereço
DELETE /perfil/enderecos/:id → Remover endereço
POST /perfil/enderecos/:id/padrao → Definir endereço padrão
```

---

## Estrutura de Pastas

```
excelencia/
├── app/
│   ├── Http/
│   │   └── Controllers/
│   │       ├── AuthController.php      # Autenticação (register, login, logout)
│   │       ├── CardapioController.php  # Exibição do cardápio
│   │       └── ProfileController.php   # Perfil e endereços do usuário
│   ├── Models/
│   │   ├── User.php                    # Modelo de usuário
│   │   ├── Produto.php                 # Modelo de produto/item
│   │   ├── Categoria.php               # Categorias de produtos
│   │   └── Endereco.php                # Endereços de entrega
│   └── Providers/
├── database/
│   ├── migrations/                     # Estrutura do banco de dados
│   ├── factories/                      # Factories para testes
│   └── seeders/                        # Dados iniciais (categorias, produtos)
├── resources/
│   ├── views/                          # Templates Blade
│   │   ├── welcome.blade.php          # Página inicial
│   │   ├── cardapio.blade.php         # Cardápio/Menu
│   │   ├── login.blade.php            # Login e cadastro
│   │   ├── perfil.blade.php           # Perfil do usuário
│   │   ├── header.blade.php           # Componente de cabeçalho
│   │   └── footer.blade.php           # Componente de rodapé
│   ├── js/                             # JavaScript
│   │   ├── app.js                     # App principal
│   │   ├── cardapio.js                # Lógica do cardápio
│   │   ├── login.js                   # Lógica de autenticação
│   │   └── perfil.js                  # Lógica do perfil
│   ├── css/
│   │   └── app.css                    # Estilos Tailwind CSS
│   └── images/
├── routes/
│   └── web.php                        # Definição de todas as rotas
├── public/
│   └── images/
│       └── products/                  # Imagens dos produtos
├── config/                            # Arquivos de configuração
├── storage/                           # Arquivos gerados/uploads
├── bootstrap/                         # Bootstrap da aplicação
├── tests/                             # Testes automatizados
├── vendor/                            # Dependências PHP (Composer)
├── node_modules/                      # Dependências JavaScript (npm)
├── .env.example                       # Exemplo de variáveis de ambiente
├── composer.json                      # Dependências PHP e scripts
├── package.json                       # Dependências JavaScript e scripts
├── vite.config.js                     # Configuração do Vite
├── phpunit.xml                        # Configuração de testes
└── README.md                          # Este arquivo
```

---

## Como Instalar e Executar

### Pré-requisitos
- PHP 8.2+
- Node.js 18+
- Composer
- Git

### Passo 1: Clonar o Repositório
```bash
git clone https://github.com/VitorPezzete/TCC-Excelencia.git
cd TCC-Excelencia
```

### Passo 2: Instalar Dependências
```bash
# Dependências PHP
composer install

# Dependências JavaScript
npm install
```

### Passo 3: Configurar Ambiente
```bash
# Copiar arquivo de exemplo
cp .env.example .env

# Gerar chave de aplicação
php artisan key:generate
```

### Passo 4: Preparar Banco de Dados
```bash
# Executar migrações
php artisan migrate

# (Opcional) Popular banco com dados iniciais
php artisan db:seed
```

### Passo 5: Executar em Desenvolvimento
```bash
# Opção 1: Usando o script de desenvolvimento (recomendado)
composer run dev

# Opção 2: Rodar separadamente
# Terminal 1:
php artisan serve

# Terminal 2:
npm run dev
```

A aplicação estará disponível em: **http://localhost:8000**

---

## Controllers

### AuthController
- `register()` - Registra novo usuário com validação
- `login()` - Autentica usuário
- `logout()` - Faz logout e redefine sessão

### CardapioController
- `index()` - Exibe todos os produtos com categorias

### ProfileController
- `index()` - Exibe dados do perfil
- `updateData()` - Atualiza nome, email, telefone
- `updatePassword()` - Altera senha com validação
- `storeAddress()` - Adiciona novo endereço
- `updateAddress()` - Edita endereço existente
- `destroyAddress()` - Remove endereço
- `setDefaultAddress()` - Define endereço padrão

---

## Migrations (Tabelas)

- `users` - Usuários do sistema
- `categorias` - Categorias de produtos
- `produtos` - Items do catálogo
- `enderecos` - Endereços de entrega dos usuários
- `cupons` - Códigos de desconto/promoções
- `pedidos` - Pedidos realizados
- `itens_pedido` - Produtos em cada pedido
- `itens_carrinho` - Itens no carrinho do usuário
- `pagamentos` - Transações de pagamento
- `historico_status_pedido` - Rastreamento do status

---

## Scripts Disponíveis

### Desenvolvimento
```bash
# Iniciar servidor e build em tempo real
composer run dev

# Apenas servidor Laravel
php artisan serve

# Apenas build Vite
npm run dev

# Build para produção
npm run build
```

### Testes
```bash
# Rodar todos os testes
composer run test

# Rodar testes com verbosidade
php artisan test --verbose
```

### Code Quality
```bash
# Formatar código com Pint
./vendor/bin/pint

# Verificar erros sem corrigir
./vendor/bin/pint --test
```

### Banco de Dados
```bash
# Executar migrações
php artisan migrate

# Reverter última migração
php artisan migrate:rollback

# Limpar e recriar banco
php artisan migrate:fresh

# Popular banco com seeds
php artisan db:seed
```

---

## Tecnologias & Padrões

### Design Patterns
- **MVC** - Model-View-Controller
- **Repository Pattern** - Integrado via Eloquent
- **Service Container** - Dependency Injection
- **Middleware Pattern** - Para autenticação e autorização

### Convenções
- **PSR-12** - PHP Code Style Guide
- **Blade** - Template engine Laravel
- **RESTful Routing** - Rotas seguem convenções REST
- **Eloquent Relations** - Relacionamentos entre modelos

---

## Funcionalidades em Desenvolvimento

- [ ] Dashboard administrativo
- [ ] Sistema de avaliações/comentários
- [ ] Histórico de pedidos com detalhes
- [ ] Notificações por email
- [ ] Relatórios de vendas

---

## Contribuindo

Contribuições são bem-vindas! Por favor:
1. Fork o projeto
2. Crie uma branch para sua feature (`git checkout -b feature/AmazingFeature`)
3. Commit suas mudanças (`git commit -m 'Add some AmazingFeature'`)
4. Push para a branch (`git push origin feature/AmazingFeature`)
5. Abra um Pull Request

---

## Contato

**Desenvolvedores**: Vitor Pezzete, Lucas Meduneckas, Léticia Duarte e Leonardo Alencar.  

---

## Licença

Este projeto é um TCC e está disponível para fins educacionais. Todos os direitos reservados.

---

**Última atualização**: Abril de 2026
# TCC Excelencia

Plataforma de **e-commerce e delivery** com cardápio integrado. Sistema completo para gerenciamento de produtos, pedidos, endereços de entrega e autenticação de usuários.

**Repositório**: [github.com/VitorPezzete/TCC-Excelencia](https://github.com/VitorPezzete/TCC-Excelencia)

---

## Análise do Projeto

### Descrição Geral
O projeto Excelencia é uma plataforma de delivery desenvolvida como Trabalho de Conclusão de Curso (TCC), construída com as tecnologias mais modernas para web.

### Stack Tecnológico

#### Backend
- **Framework**: Laravel 12 (PHP 8.2+)
- **Banco de Dados**: Suporte a múltiplos bancos (SQLite, MySQL, PostgreSQL, etc)
- **ORM**: Eloquent
- **Autenticação**: Laravel Authentication
- **Queue**: Redis/Database (para processamento assíncrono)

#### Frontend
- **Build Tool**: Vite 7
- **Styling**: Tailwind CSS 4
- **JS Framework**: Vanilla JS com Axios
- **Transpilação**: Autoprefixer e PostCSS

#### Ferramentas de Desenvolvimento
- **Testing**: PHPUnit 11
- **Code Quality**: Laravel Pint
- **Logging**: Laravel Pail
- **Development Server**: Laravel Sail (Docker)

---

## Funcionalidades Principais

### 1. **Autenticação & Autorização**
- Registro de novos usuários
- Sistema de login/logout
- Middleware de autenticação
- Suporte a perfil com dados de contato (email, telefone)

### 2. **Catálogo de Produtos**
- Exibição de cardápio/menu
- Produtos organizados por categoria
- Informações detalhadas: nome, descrição, preço, imagem
- Produtos em destaque
- Status de ativo/inativo

### 3. **Gerenciamento de Perfil**
- Atualização de dados pessoais
- Alteração de senha
- Múltiplos endereços de entrega
- Definição de endereço padrão

### 4. **Carrinho de Compras**
- Adição/remoção de itens (tabela: `itens_carrinho`)

### 5. **Sistema de Pedidos**
- Criação e processamento de pedidos
- Histórico de status do pedido
- Itens do pedido com rastreamento

### 6. **Pagamentos**
- Integração de sistema de pagamentos (estrutura criada)

### 7. **Promoções**
- Sistema de cupons/descontos (tabela: `cupons`)

---

## Estrutura do Banco de Dados

### Modelos Implementados
| Modelo | Descrição | Atributos Principais |
|--------|-----------|---------------------|
| **User** | Usuários do sistema | name, email, phone, password |
| **Categoria** | Categorias de produtos | nome, descrição |
| **Produto** | Items do cardápio | nome, descricao, preco, imagem, destaque, ativo |
| **Endereco** | Endereços de entrega | user_id, rua, cidade, estado, cep |

### Tabelas de Negócio
- `cupons` - Códigos de desconto
- `pedidos` - Pedidos realizados
- `historico_status_pedido` - Rastreamento de status
- `itens_pedido` - Produtos em cada pedido
- `itens_carrinho` - Items do carrinho do usuário
- `pagamentos` - Registros de transações

---

## Rotas da Aplicação

### Públicas (sem autenticação)
```
GET  /                    → Página inicial
GET  /cardapio            → Visualizar cardápio
GET  /login               → Formulário de login
POST /cadastro            → Registrar novo usuário
POST /login               → Autenticar usuário
```

### Protegidas (requer autenticação)
```
POST /logout              → Fazer logout
GET  /perfil              → Visualizar perfil
POST /perfil/dados        → Atualizar dados pessoais
POST /perfil/senha        → Alterar senha
POST /perfil/enderecos    → Adicionar novo endereço
PUT  /perfil/enderecos/:id → Editar endereço
DELETE /perfil/enderecos/:id → Remover endereço
POST /perfil/enderecos/:id/padrao → Definir padr

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
