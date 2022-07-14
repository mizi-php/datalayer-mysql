# datalayer-mysql

Manipula conexões com bancos MYSQL e MARIADB

    composer require mizi/datalayer-mysql

### Inicializar

Para iniciar uma conexão, deve-se adicionanas os dados dentro de variaveis de ambiente.
Cada variavel, deve ser escrita com o prefixo **DB_**+**NOME**

Crie uma conexão Mysql com a seguite configuração em seu **.env**

    DB_MAIN_TYPE = mysql
    DB_MAIN_HOST = 127.0.0.1
    DB_MAIN_DATA = nomeDoBanco
    DB_MAIN_USER = root
    DB_MAIN_PASS = 

Com isso, o sistema criar uma conexão **main** e se conectar com o banco de nome **DB_MAIN_DATA** em **DB_MAIN_HOST**, utilizndo as credenciais **DB_MAIN_USER** e **DB_MAIN_PASS**

### Multiplos bancos
Pode-se criar quantas conexões precisar, com quantos bancos forem nescessarios.

    DB_MAIN_TYPE = mysql
    DB_MAIN_HOST = 127.0.0.1
    DB_MAIN_DATA = dataMain
    DB_MAIN_USER = root
    DB_MAIN_PASS = 

    DB_CACHE_TYPE = mysql
    DB_CACHE_HOST = 127.0.0.2
    DB_CACHE_DATA = dataCache
    DB_CACHE_USER = root
    DB_CACHE_PASS = 

    DB_BLOG_TYPE = mysql
    DB_BLOG_HOST = 127.0.0.1
    DB_BLOG_DATA = dataBlog
    DB_BLOG_USER = root
    DB_BLOG_PASS = 

    Db_LOJA_TYPE = mysql
    DB_LOJA_HOST = 127.0.0.3
    DB_LOJA_DATA = dataLoja
    DB_LOJA_USER = root
    DB_LOJA_PASS = 
    
    ...

## Objeto de conexão

Para utilizar a conexão, utilize a biblioteca [DATALAYER](https://github.com/mizi-php/datalayer/tree/main/.doc/driver.md)

---

### Env

**DB_NAME_TYPE**: Inicialização de um datalayer NAME para uma conexão MYSQL

    DB_NAME_TYPE='mysql'

**DB_NAME_HOST**: Host para um datalayer MYSQL

    DB_NAME_HOST='localhost'

**DB_NAME_DATA**: Nome da base de dados para um datalayer MYSQL

    DB_NAME_DATA='nameData'

**DB_NAME_USER**: Usuário para um datalayer MYSQL

    DB_NAME_USER='root'

**DB_NAME_PASS**: Senha para um datalayer MYSQL

    DB_NAME_PASS=''