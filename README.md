# Sistema Estoque

O Sistema de Estoque é um CRUD construído em PHP 8 e Laravel, estruturado em arquitetura hexagonal. O projeto implementa injeção de dependência, autenticação JWT e gerenciamento de perfis e permissões para controle de acesso.

## Tecnologias e Requisitos:
* Linguagem: PHP 8+
* Gerenciador de pacotes: Composer
* Framework: Laravel
* Banco de Dados: PostgreSQL
* Autenticação: JWT (JSON Web Token)

## Instalação
Para instalar o projeto, siga os passos abaixo:

1. Clone o repositório
```bash
https://github.com/SaorCampos/Sistema-estoque.git
cd Sistema-estoque
```
2. Instale as dependências:
```bash
composer install
```
3. Crie o arquivo de ambiente:
```bash
cp .env.example .env
```
4. Configure as variáveis de ambiente: Defina as credenciais de banco de dados, informações JWT e outras variáveis em ``.env`` exemplo:
```
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=sistema_estoque
DB_USERNAME=root
DB_PASSWORD=password
```
5. Após configurar o .env, execute os comandos abaixo para criar as chaves do projeto e JWT:
```bash
php artisan key:generate
php artisan jwt:secret
```
6. Após criar as chaves, execute o comando abaixo para criar as tabelas no banco de dados:
```bash
php artisan migrate --seed
```
Assim estara criado um usuario com 
```
login: Admin
senha: 123456
```
## Executando o servidor
Inicie o servidor com o comando:
```bash
php artisan serve
```
A aplicação estará disponível em ``http://localhost:8000``.

Assim você podera usar progamas como Insomnia ou Postman para testar a Api do projeto.

## Autenticação e Controle de Acesso
O sistema usa JWT para autenticação. Após o login, um token JWT é gerado, garantindo o acesso de acordo com o perfil e as permissões do usuário.
## Estrutura Hexagonal
A arquitetura hexagonal permite fácil manutenção e testes isolados, organizando as responsabilidades em domínios específicos e facilitando a injeção de dependências.
## Testes
Execute os testes para validar as funcionalidades:

* Testes dos enpoints:
```bash
php artisan test tests/Feature/
```
* Testes das camadas de Repository:
```bash
php artisan test tests/Unit/Repositories
```
* Testes das camadas de Service:
```bash
php artisan test tests/Unit/Services
```
# Funcionalidades
1. Gestão de Movimentações de Estoque:
* Entradas: Registrar a entrada de itens no estoque com quantidade, nota fiscal, fornecedor, e data de movimentação.
* Saídas: Registrar a saída de itens, incluindo número de controle, destino e data.
2. Autenticação e Controle de Acesso:
* JWT: Controle de autenticação com token JWT.
* Perfis e Permissões: Controle granular para diferentes níveis de acesso.
3. Histórico e Auditoria:
* Registra atualizações de cada entrada e saída para garantir a rastreabilidade.

# Regras de Negócio
1. Unicidade da Nota Fiscal e Número de Controle de Saída: Cada entrada de estoque deve ter uma nota fiscal única e cada saída seu número de controle.

2. Validação de Quantidade: Verifica se há quantidade suficiente antes de permitir uma saída.

3. Controle de Acesso: Usuários precisam ter as permissões adequadas para manipular dados de estoque.

# Estrutura da Arquitetura Hexagonal
A arquitetura do projeto é dividida em camadas, garantindo separação de responsabilidades e facilitando testes e manutenção:

1. Camada de Aplicação:
* Gerencia regras de negócio e lógica de controle.
* Inclui ``controllers``, ``services``, e ``use cases`` que interagem com as demais camadas.

2. Camada de Domínio:
* Contém as entidades e lógica de domínio central do sistema.
* Define as regras de negócio e contratos de interface ``Services``.

3. Camada de Infraestrutura:
* Concentra as interações com o banco de dados e recursos externos.
* Inclui repositórios e implementações de interfaces para persistência, como o ``MovimentosRepository``.
