# Sistema_Estoque

O Sistema de Estoque é um CRUD construído em PHP 8 e Laravel, estruturado em arquitetura hexagonal. O projeto implementa injeção de dependência, autenticação JWT e gerenciamento de perfis e permissões para controle de acesso.

## Tecnologias e Requisitos:
* Linguagem: PHP 8+
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
4. Configure as variáveis de ambiente: Defina as credenciais de banco de dados, informações JWT e outras variáveis em ``.env``:
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
