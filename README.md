# Short

Encurtador de URLs **sem cadastro**, feito em Laravel 12. O visitante cola uma URL, clica em **Encurtar** e recebe um link curto (ex.: `https://short.dominio.com/aB3xYz`) ou um código próprio (ex.: `/meu-link-1-compartilhado`).

Cada link recebe um **PIN de gerenciamento**, exibido **uma única vez**. Com o código + PIN, qualquer pessoa que tenha criado o link consegue voltar e consultar os dados dele, sem precisar de login.

## Objetivos

- Encurtar URLs em um clique, numa interface mínima e responsiva (tema escuro com destaque laranja).
- Permitir códigos personalizados.
- Dar acesso às informações do link via PIN, sem cadastro.
- Registrar cada acesso (data/hora, sistema, navegador e tipo de dispositivo).
- Rodar em **subdomínios de hospedagens compartilhadas**, sem precisar de serviços extras (banco SQLite, sem filas obrigatórias).

## Stack

| Camada | Tecnologia |
| --- | --- |
| Backend | PHP 8.2+ · Laravel 12 |
| Banco | SQLite |
| Front-end | Blade · Tailwind CSS 4 · Vite · JavaScript puro |
| Testes | Pest 4 (unitários, feature e navegação com Playwright) |
| Análise estática | Larastan (PHPStan) nível 6 · Laravel Pint |

## Funcionalidades (etapa 01)

- **Tela inicial**: input centralizado (apenas linha inferior) + botão laranja **Encurtar** com animação de carregamento.
- **Validação no front e no backend**: só aceita `http`/`https` com domínio válido. Sem protocolo, adiciona `https://`.
- **Código personalizado** (opcional, em "Personalizar link"): letras minúsculas, números e hífens, de 3 a 50 caracteres. Palavras reservadas do sistema são bloqueadas.
- **Tela do link** (`/gerenciar/{codigo}`): data/hora de criação, URL original, URL encurtada (com botão copiar), total de acessos e o **PIN** (somente logo após a criação).
- **Acesso com PIN** (`/gerenciar`): código (ou URL curta) + PIN, com limite de tentativas por minuto.
- **Redirecionamento** (`/{codigo}`): HTTP 302 para a URL original, registrando o acesso em `visualizations`.

## Decisões de segurança

- O PIN **não é salvo em texto puro**: guardamos um HMAC-SHA256 dele com a `APP_KEY`. Isso mantém o índice único e a busca, mas um vazamento do banco não expõe os PINs. **Trocar a `APP_KEY` invalida todos os PINs.**
- PIN de 8 caracteres sem símbolos ambíguos (`0/O`, `1/I/L`). Não diferencia maiúsculas.
- Limite de requisições por IP: 20 links/min e 5 tentativas de PIN/min (configurável em `config/short.php`).
- Códigos gerados (base62, 6 caracteres) diferenciam maiúsculas. Os personalizados são salvos em minúsculas.

## Banco de dados

```
urls
├── url_id          PK
├── original_link   text
├── short_url       unique (código curto)
├── pin_manager     unique (HMAC do PIN)
└── created_at · updated_at · deleted_at

visualizations
├── visualization_id  PK
├── url_id            FK → urls.url_id (cascade)
├── access_date_hour  datetime (indexado)
├── device_info       json {os, browser, device}
└── created_at · updated_at · deleted_at
```

A mesma `original_link` pode existir várias vezes. Cada registro tem um código e um PIN próprios.

## Estrutura

```
app/
├── Http/Controllers/
│   ├── ShortUrlController.php   # tela inicial e criação
│   ├── ManageController.php     # acesso por PIN e tela do link
│   └── RedirectController.php   # /{codigo} → URL original
├── Http/Requests/               # validação (StoreShortUrl, UnlockShortUrl)
├── Models/                      # ShortUrl (tabela urls), Visualization
└── Services/
    ├── ShortUrlCreator.php      # cria link + PIN, trata colisões
    ├── ShortCodeGenerator.php   # código base62 / palavras reservadas
    ├── PinManager.php           # gera, faz hash e confere o PIN
    ├── ManagedUrls.php          # links desbloqueados na sessão
    └── UserAgentParser.php      # sistema, navegador e dispositivo
config/short.php                 # tamanhos, alfabetos, limites, reservados
resources/views/                 # layout, home, manage/{unlock,show}
resources/js/app.js              # validação, loading, copiar
tests/{Unit,Feature,Browser}/    # Pest 4
.agents/docs/                    # especificações por etapa
```

## Rodando localmente

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate
composer run dev          # servidor + Vite + logs
```

Com Docker: `docker compose up --build` (a aplicação fica em `http://localhost:8000`).

## Testes e qualidade

```bash
composer test             # unitários + feature
composer test:browser     # navegação (Playwright/Chromium)
composer analyse          # Larastan nível 6
./vendor/bin/pint         # padronização de código
```

Os testes de navegação precisam do Chromium do Playwright:

```bash
npx playwright install chromium
sudo npx playwright install-deps chromium   # bibliotecas do sistema (Linux/WSL)
```

A imagem Docker já traz as duas coisas.

## Deploy em hospedagem compartilhada (subdomínio)

1. Localmente, rode `composer install --no-dev --optimize-autoloader` e `npm run build`.
2. Envie o projeto (incluindo `vendor/` e `public/build/`) para uma pasta, ex.: `/public_html/short`.
3. No painel, crie o subdomínio apontando para:
   - **`/public_html/short/public`** (recomendado), ou
   - `/public_html/short`: o `.htaccess` da raiz encaminha tudo para `public/` e bloqueia o resto.
4. Configure o `.env` (`APP_ENV=production`, `APP_DEBUG=false`, `APP_URL=https://short.seudominio.com`, `APP_KEY`).
5. Crie `database/database.sqlite`, garanta permissão de escrita nele, em `database/` e em `storage/`, e rode `php artisan migrate --force`.

## Roadmap

- [x] 00 · Estrutura do projeto
- [x] 01 · Tela inicial, criação do link, PIN e redirecionamento
- [ ] Detalhamento das informações de acesso na tela do link
