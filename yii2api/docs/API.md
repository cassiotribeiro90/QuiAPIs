# API

Documentacao da API HTTP do projeto. A base da URL depende do ambiente, por exemplo `http://localhost:8000`.
As rotas de producao usam URLs amigaveis sem `index.php` e o prefixo `/api`.

> Este documento descreve o codigo atualmente publicado em `config/web.php` e nos controllers de `controllers/api`.
> Rotas marcadas como legadas estao configuradas, mas apontam para actions que nao foram encontradas no codigo.

## Convencoes

### Requisicao

- Envie JSON com `Content-Type: application/json` quando houver corpo.
- Rotas protegidas usam `Authorization: Bearer <access_token>`.
- A API app usa o usuario autenticado da aplicacao; a API lojista usa um token de lojista.
- Rotas do lojista que operam uma loja exigem `X-Store-Id` (tambem aceitam `store_id` no body/query em alguns endpoints).
- CORS esta habilitado pela aplicacao e `OPTIONS` e tratado para preflight.
- Paginacao usa `page` (inicia em 1) e `per_page`.

### Resposta

Sucesso:

```json
{
  "success": true,
  "code": 200,
  "message": "Operacao realizada com sucesso",
  "data": {}
}
```

Erro:

```json
{
  "success": false,
  "code": 400,
  "message": "Mensagem do erro",
  "status": "invalid_credentials",
  "errors": {}
}
```

`data`, `status` e `errors` sao opcionais. O campo `code` acompanha o status HTTP.
Respostas de listagem normalmente retornam `data.items` e `data.pagination`, com `total`, `page`, `per_page` e `total_pages`.

### Codigos HTTP

`200` sucesso, `201` criacao quando aplicavel, `400` entrada invalida, `401` nao autenticado,
`403` acesso negado, `404` recurso inexistente e `500` erro interno.

## App / cliente

Base: `/api/app`

### Autenticacao

| Metodo | Rota | Auth | Entrada principal |
|---|---|---:|---|
| POST | `/auth/login` | Nao | `email`, `senha`, opcionalmente `device_id`, `device_token` |
| POST | `/auth/cadastro` | Nao | Dados cadastrais do cliente |
| POST | `/auth/social` | Nao | Provedor e identificador/dados da conta social |
| POST | `/auth/convidado` | Nao | `device_id` ou header `X-Device-Id` |
| POST | `/auth/phone` | Nao | `phone`; pode usar `X-Device-Id` |
| POST | `/auth/verify-otp` | Nao | `phone`, `code`, opcionalmente `device_id`, `device_token` |
| POST | `/auth/refresh-token` | Nao | `refresh_token` |
| POST | `/auth/logout` | Sim | Token no header |
| GET | `/auth/me` | Sim | Nenhum |
| POST | `/auth/me` | Nao no filtro; usa dados do usuario quando disponivel | Campos do perfil |
| POST | `/auth/update-telefone` | Sim | Novo telefone |
| POST | `/auth/confirm-update-telefone` | Sim | Telefone e codigo OTP |

Login, OTP e convidado retornam tokens no formato:

```json
{
  "access_token": "...",
  "refresh_token": "...",
  "expires_in": 7200,
  "token_type": "Bearer",
  "usuario": {},
  "enderecos": []
}
```

O access token e emitido normalmente para 2 horas e o refresh token para 30 dias. O controller de OTP atualmente aceita qualquer codigo numerico de seis digitos; isso e comportamento de desenvolvimento e deve ser corrigido antes de producao.

### Cadastro e localizacao

| Metodo | Rota | Auth | Query/body |
|---|---|---:|---|
| GET | `/cadastro/buscar-cep` | Nao | `cep` |
| POST | `/cadastro/validar-etapa1` | Nao | Dados da primeira etapa |
| POST | `/cadastro/cadastrar` | Nao | Dados completos do cadastro |
| GET | `/localizacao/geocodificar` | Nao | Endereco para geocodificacao |
| GET | `/localizacao/buscar-endereco` | Nao | Coordenadas ou endereco |
| POST | `/localizacao/confirmar-endereco` | Nao | Endereco confirmado |

### Lojas e catalogo

| Metodo | Rota | Auth | Query/body |
|---|---|---:|---|
| GET | `/lojas` e `/loja` | Conforme controller | Filtros de loja e localizacao |
| GET | `/lojas/proximas` e `/loja/proximas` | Conforme controller | `latitude`, `longitude`, filtros e paginacao |
| GET | `/loja-home` ou `/loja-home/{id}` | Nao | `id` ou `loja_id` obrigatorio; `categoria_id`, `search`, `order_by`, `page`, `per_page`, `latitude`, `longitude` |
| GET | `/produto-detail` ou `/produto-detail/{id}` | Conforme controller | `id` ou `produto_id` |
| GET | `/produtos` | Conforme controller | Filtros e paginacao |
| GET | `/produtos/{id}` | Conforme controller | ID do produto |
| POST | `/produtos` | Conforme controller | Dados do produto/carrinho conforme action |
| GET | `/loja-home/categorias` | Nao | `loja_id`, obrigatorio |
| GET | `/loja-home/avaliacoes` | Nao | `loja_id`, `page`, `per_page` |
| GET | `/loja-home/secoes` | Nao | Filtros da loja e paginacao |

`/loja-home/{id}` retorna os dados da loja, secoes de produtos, `pagination`, `filter_options`, `configuracoes` e `meio_a_meio`. A resposta pode incluir `distancia` e `distancia_texto` quando latitude e longitude sao fornecidas.

As seguintes rotas tambem estao configuradas para busca, categorias e avaliacoes, mas apontam para actions sem controller correspondente no inventario atual:

- `GET /loja/{id}/search` e `GET /loja/search`
- `GET /loja/{id}/categorias` e `GET /loja/categorias`
- `GET /loja/{id}/avaliacoes` e `GET /loja/avaliacoes`
- `GET /categorias`
- `GET /avaliacoes/loja/{lojaId}`
- `GET /avaliacoes/produto/{produtoId}`
- `POST /avaliacoes`
- `PUT /avaliacoes/{id}`

### Enderecos

| Metodo | Rota | Auth | Entrada principal |
|---|---|---:|---|
| GET | `/enderecos` | Sim | Nenhuma |
| GET | `/enderecos/{id}` | Sim | ID |
| POST | `/enderecos` | Sim | Dados do endereco |
| PUT | `/enderecos/{id}` | Sim | Campos alterados |
| DELETE | `/enderecos/{id}` | Sim | ID |
| PUT | `/enderecos/{id}/set-padrao` | Sim | ID |
| POST | `/enderecos/buscar-cep` | Sim | `cep` |

### Carrinho

| Metodo | Rota | Auth | Entrada principal |
|---|---|---:|---|
| GET | `/carrinho` | Sim | `endereco_id` opcional |
| PUT | `/carrinho/atualizar` | Sim | `item_id` ou `produto_id`, `quantidade`, `opcoes`, `observacao`, `endereco_id` |
| POST | `/carrinho/limpar` | Sim | Nenhuma |
| POST | `/carrinho/calcular` | Nao no filtro | Itens, loja e endereco para simulacao |
| GET | `/carrinho/resumo` | Sim | Parametros do resumo |
| GET | `/carrinho/verificar-loja` | Sim | Identificador da loja |

Quantidade zero em `/carrinho/atualizar` remove o item. O resumo inclui subtotal, frete, total, loja e formas de pagamento quando disponiveis.

### Pedidos do cliente

| Metodo | Rota | Auth | Entrada principal |
|---|---|---:|---|
| POST | `/pedido/calcular-frete` | Sim | Itens/endereco |
| POST | `/pedido/criar` | Sim | Itens, endereco e forma de pagamento |
| GET | `/pedido/historico` | Sim | Filtros e paginacao |
| GET | `/pedido/view` ou `/pedido/view/{id}` | Sim | `id` |
| POST | `/pedido/cancelar` | Sim | `id` e motivo opcional |

## Lojista

Base oficial: `/api/lojista`

### Autenticacao e conta

| Metodo | Rota | Auth | Entrada principal |
|---|---|---:|---|
| POST | `/auth-lojista/phone` | Nao | Telefone |
| POST | `/auth-lojista/verify-otp` | Nao | Telefone e codigo |
| POST | `/auth-lojista/login` | Nao | Credenciais |
| POST | `/auth-lojista/refresh-token` | Nao | `refresh_token` |
| POST | `/auth-lojista/logout` | Sim | Token |
| POST | `/auth-lojista/create` | Nao | Dados do lojista |

Rotas antigas sem o prefixo `/api/` (`/lojista/pedido`, `/auth-lojista/login` e `/auth-lojista/create`) permanecem configuradas, mas apontam para controllers/actions que nao correspondem a superficie oficial atual.

### Pedidos da loja

Todas as rotas abaixo exigem `Authorization` e `X-Store-Id`.

| Metodo | Rota | Entrada principal |
|---|---|---|
| GET | `/lojista-pedido` | `status`, `data_inicio`, `data_fim`, `page`, `per_page` |
| GET | `/lojista-pedido/ativos` | Nenhuma |
| GET | `/lojista-pedido/status-count` | Nenhuma |
| GET | `/lojista-pedido/historico/{id}` | ID do pedido |
| GET | `/lojista-pedido/{id}` | ID do pedido |
| POST | `/lojista-pedido/{id}/aceitar` | ID |
| POST | `/lojista-pedido/{id}/recusar` | ID e motivo opcional |
| POST | `/lojista-pedido/{id}/status` | Novo status |
| POST | `/lojista-pedido/{id}/cancelar` | Motivo opcional |

Status usados pelo fluxo incluem `novo`, `aguardando`, `confirmado`, `preparando`, `pronto`, `saiu`, `entregue` e `cancelado`. `/lojista-pedido/ativos` agrupa pedidos por `novo`, `preparando`, `pronto` e `saiu`.

Alias de pedidos tambem estao configurados em `/api/lojista/pedidos...`, apontando para o mesmo controller.

### Cardapio, loja e categorias

| Metodo | Rota | Auth | Entrada principal |
|---|---|---:|---|
| GET | `/cardapio` | Sim | Filtros e paginacao |
| GET | `/cardapio/{id}` | Sim | ID |
| POST | `/cardapio/create` | Sim | Dados do produto |
| PUT/POST | `/cardapio/update/{id}` | Sim | Dados alterados |
| DELETE | `/cardapio/delete/{id}` | Sim | ID |
| POST | `/cardapio/toggle/{id}` | Sim | ID e estado opcional |
| POST | `/cardapio/estoque/{id}` | Sim | Disponibilidade/estoque |
| GET | `/cardapio/options` e `/cardapio/options/{id}` | Sim | Opcoes do cardapio |
| GET | `/categorias` e `/categorias/options` | Sim | Filtros/opcoes |
| GET | `/subcategoria/por-categoria` | Sim | `categoria_id` |
| GET | `/loja` | Sim | Lojas do lojista |
| PUT/POST | `/loja` | Sim | Dados da loja |
| GET | `/loja/minhas-lojas` | Sim | Nenhuma |
| GET | `/loja/{id}` | Sim | ID |

### Dashboard e notificacoes

| Metodo | Rota | Auth | Entrada principal |
|---|---|---:|---|
| GET | `/dashboard` | Sim | Nenhuma |
| GET | `/device-token` | Sim | Nenhuma |
| DELETE | `/device-token` | Sim | Token/device |
| GET | `/teste-firebase` | Sim | Endpoint de teste |
| POST | `/teste-push` | Sim | Dados da notificacao |

## Gestor / painel administrativo

Base: `/api/gestor`

> O `ControllerBase` do gestor nao instala `HttpBearerAuth` globalmente. As actions devem ser verificadas individualmente, pois algumas validam o bearer token manualmente. Trate todas as rotas abaixo como protegidas no cliente.

### Usuarios do gestor

| Metodo | Rota | Entrada principal |
|---|---|---|
| GET | `/gestor-usuarios` | Filtros e paginacao |
| GET | `/gestor-usuarios/{id}` | ID |
| POST | `/gestor-usuarios/login` | Credenciais |
| POST | `/gestor-usuarios/logout` | Token |
| GET | `/gestor-usuarios/me` | Token |
| POST | `/gestor-usuarios/create` | Dados do gestor |
| PUT/POST | `/gestor-usuarios/update/{id}` | Dados alterados |
| DELETE/POST | `/gestor-usuarios/delete/{id}` | ID |
| POST | `/gestor-usuarios/refresh-token` | `refresh_token` |
| GET | `/gestor-usuarios/check-token` | Token |
| GET | `/gestor-usuarios/options` | Opcoes |
| POST | `/gestor-usuarios/device-token` | Token do dispositivo |
| DELETE | `/gestor-usuarios/device-token` | Token do dispositivo |

### Recursos administrativos

| Recurso | Rotas |
|---|---|
| Dashboard | `GET /dashboard` |
| Lojas | `GET /lojas`, `GET /lojas/{id}`, `POST /lojas/create`, `PUT/POST /lojas/update/{id}`, `DELETE/POST /lojas/delete/{id}`, `GET /lojas/options` |
| Produtos | `GET /produto/view`, `GET /produto/view/{id}`, `POST /loja/produtos` |
| Categorias | `GET /categorias`, `GET /categorias/{id}`, `POST /categorias/create`, `PUT/POST /categorias/update/{id}`, `DELETE/POST /categorias/delete/{id}`, `GET /categorias/options` |
| Subcategorias | `GET /subcategorias`, `GET /subcategorias/{id}`, `POST /subcategorias/create`, `PUT/POST /subcategorias/update/{id}`, `DELETE/POST /subcategorias/delete/{id}`, `GET /subcategorias/options`, `GET /subcategorias/por-categoria/{id}` |
| Usuarios lojistas | `GET /store-usuarios`, `GET /store-usuarios/{id}`, `POST /store-usuarios/create`, `PUT/POST /store-usuarios/update/{id}`, `DELETE/POST /store-usuarios/delete/{id}`, `GET /store-usuarios/options`, `GET /store-usuarios/lojas-options` |

`GET /dashboard/graficos` esta configurada, mas `DashboardController` atualmente expoe apenas `index`; a rota precisa ser removida ou implementada.

## Erros e pontos pendentes

- A especificacao deve ser revisada junto com a normalizacao das rotas legadas e dos aliases.
- OTP de cliente e lojista nao deve aceitar qualquer codigo em producao.
- Os endpoints `/teste-firebase` e `/teste-push` devem ser bloqueados ou removidos em producao.
- O gestor deve adotar um filtro de autenticacao uniforme ou documentar formalmente quais actions fazem a validacao manual.
- Para gerar uma especificacao OpenAPI automatica, use este catalogo como contrato inicial e adicione os schemas especificos retornados pelos models.
