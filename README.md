# comments-api-challenge
### Esta é uma API Rest desenvolvida com a finalidade de gerenciar comentarios em uma rede social, os enpoints foram criados para atender as demandas do desafio proposto. O arquivo PDF do desafio pode ser encontrado em `/docs/Teste prático - Back-end.pdf`


### O ponta pé inicial foi a idealização dos Enpoints, representados abaixo
### `$this-> Comentar em uma postagem`
```
{
            "method":"POST",
            "uri":"/comments",
            "description":"Create an comment on a post.",
            "header":[],
            "query_string":[],
            "request_body":[
                {
                    "key":"id_post",
                    "type":"integer",
                    "required":"true",
                    "descripition":"Post identification code."
                },
                {
                    "key":"id_user",
                    "type":"integer",
                    "required":"true",
                    "descripition":"Post identification code."
                },
                {
                    "key":"message",
                    "type":"string",
                    "required":"true",
                    "descripition":"Message body of comment."
                },
                {
                    "key":"highlight_minutes",
                    "type":"integer",
                    "required":"false",
                    "descripition":"Amount in minutes comment must be highligthed."
                }
            ]
        }
```
### `$this-> Listar todos os comentários de uma Postagem ->`
### `$this-> Listar todos os comentários de um Usuário ->`
```
{
            "method":"GET",
            "uri":"/comments",
            "description":"Return a list of comments.",
            "header":[
                {
                    "key":"Authorization",
                    "type":"string",
                    "required":"true",
                    "descripition":"Authorization code."
                }
            ],
            "query_string":[
                {
                    "key":"id_post",
                    "type":"integer",
                    "required":"false",
                    "descripition":"Post identification code."
                },
                {
                    "key":"id_user",
                    "type":"integer",
                    "required":"false",
                    "descripition":"User identification code."
                }
            ]
            
        }
```

### `$this-> Consultar notificações de um usuário ->`
```
{
            "method":"GET",
            "uri":"/notifications",
            "description":"Return a list of notifications.",
            "header":[
                {
                    "key":"Authorization",
                    "type":"string",
                    "required":"true",
                    "descripition":"Authorization code."
                }
            ],
            "query_string":[
                {
                    "key":"id_user",
                    "type":"integer",
                    "required":"false",
                    "descripition":"User identification code."
                }
            ]
        }
```
### `$this-> Exclusão de comentário ->`
```
{
            "method":"DELETE",
            "uri":"/comments/{id_comment}",
            "description":"Delete an comment.",
            "header":[
                {
                    "key":"Authorization",
                    "type":"string",
                    "required":"true",
                    "descripition":"Authorization code."
                },
                {
                    "key":"id_user",
                    "type":"integer",
                    "required":"true",
                    "descripition":"User identification code."
                }
            ],
            "query_string":[]
        }
```

### A modelagem do banco passou por algumas alterações ao longo do desenvolvimento como pode ser visto nos arquivos de migrations mas segue abaixo a versão final 
![Modelagem Conceitual](/docs/mer_conceitual.png?raw=true "Modelagem Conceitual")

### Pilha tecnologica respeitando a base proposta no desafio que seria em PHP.
1. Microframework [Lumen](https://lumen.laravel.com/) : Escolhi usar o Lumen por ter uma documentação muito completa e ser focado na criação de APIs.
2. Cache [Redis](https://redis.io/) : Como o desafio apresentou um ponto sensível a sobrecarga que é a listagem de comentarios, decidi usar o Redis em memoria para otimizar o endpoint.
3. Banco Mysql : Praticidade e open source.

### [Fluxo](/docs/fluxo_postagem.txt) desenvolvido para implementar as regras da postagem de comentario
```
Legendas : [
	'Comentarista':'Usuario realizando o comentario',
	'Postador':'Usuario dono do post'
]
Antes de inserir o comentario
-> Buscar todos os dados do usuario que esta comentando;
-> Buscar todos os dados da postagem;
-> Buscar todos os dados do usuario dono da postagem;
-> Buscar parametros da api_settings para limite de postagens por intervalo.

-> Comentarista fez X postagens nos ultimos Y segundos ?
	Sim: Impedir comentario.
	Não: Proxima validação.
-> Comentarista comprou destaque ?
	Sim: Comentarista tem salo suficiente ?
		Sim: Efetuar comentario.
			* Registrar Transação da compra e da retenção do sistema.
			* Enviar moedas para o Postador.
		Não: Impedir comentario.
	Não: Proxima validação.
-> Comentarista é assinante ?
	Sim: Efetuar comentario.
	Não: Proxima validação.
-> Postador é Assinante ?
	Sim: Efetuar comentario.
	Não: Impedir comentario.
```

### Listagem de comentarios
Na listagem de comentarios não encontrei nenhuma forma de resolver toda a regra de negocio de uma só vez no SELECT, portanto segui uma estrategia mesclando a busca no banco com manipulação logica em memoria.
```
Fluxo na classe CommentService.php -> getAll() 
``` 

### Exclusão de comentarios
Segui a linha de raciocinio de nunca deletar efetivamente os comentarios mas sim tornalos inviveis, pensando em manter um historico completo da postagem.


## Instalação do projeto usando Docker
##### Repositorio do [Laradock](https://github.com/laradock/laradock) <- Canivete suíço para desenvolvimento PHP com docker.

1. Clone o projeto
```
$ git clone https://github.com/thejaum/comments-api-challenge.git
```
2. Acesse a pasta do projeto e execute o composer install para baixar as dependencias.
```
$ composer install
```
3. Dentro da pasta raiz, altere o nome do '.env' para '.env.local' e o '.env.docker' para '.env'
```
$ cp .env .env.local
$ cp .env.docker .env
```
4. E ainda na raiz de um git clone no repositorio do Laradock 
```
$ git clone https://github.com/laradock/laradock.git
```
5. Acesse a pasta /laradock, altere o arquivo env-example para .env
```
$ cp env-example .env
```
6. Ainda na pasta do laradock execute o seguinte comando para subir os containers.
```
$ docker-compose up -d nginx mysql redis	
```
7. Agora acesse a linha de comando do container servidor php
```
$ docker exec -it laradock_workspace_1 /bin/sh
```
8. No workspace execute o migration
```
$ php artisan migrate
```
9. E agora o seeder
```
$ php artisan db:seeder
```


## Como utilizar

```
Na pasta /docs/ deixei uma collection do postman que pode auxiliar no consumo do serviço.
```
