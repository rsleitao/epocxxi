# EpocXXI – Configuração e primeiros passos

## O que já está feito

- **Laravel** instalado (v12)
- **Base de dados** configurada para MySQL no `.env`:
  - `DB_DATABASE=epocxxi`
  - `DB_USERNAME=root`
  - `DB_PASSWORD=` (vazio, típico no XAMPP)
- **Laravel Breeze** instalado com stack **Blade** (login, registo, perfil, dashboard)
- **Locale** definido para português (`APP_LOCALE=pt`)

---

## Passos que tens de fazer

### 1. Criar a base de dados MySQL

No **phpMyAdmin** (http://localhost/phpmyadmin) ou na linha de comando MySQL:

```sql
CREATE DATABASE epocxxi CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Ou no XAMPP: abrir MySQL Console e executar o comando acima.

### 2. Correr as migrações

No diretório do projeto:

```bash
php artisan migrate
```

Isto cria as tabelas `users`, `password_reset_tokens`, `sessions`, `cache`, `cache_locks` e `jobs`.

### 3. (Opcional) Frontend com Vite

**Sem Node.js:** As páginas de login, registo e dashboard funcionam sem build: os layouts usam Tailwind e Alpine.js por CDN quando o ficheiro `public/build/manifest.json` não existe (evita o erro `ViteManifestNotFoundException`).

**Com Node.js:** Para compilar os assets localmente (recomendado em produção):

```bash
npm install
npm run build
```

Noutro terminal, para servir a aplicação:

```bash
php artisan serve
```

Aceder a: **http://127.0.0.1:8000**

Se usares o XAMPP com o projeto em `htdocs/epocxxi`, podes aceder por: **http://localhost/epocxxi/public** (e configurar um virtual host se quiseres).

### 4. Registo e login

- **Registo:** http://127.0.0.1:8000/register (ou /epocxxi/public/register)
- **Login:** http://127.0.0.1:8000/login
- **Dashboard:** http://127.0.0.1:8000/dashboard (após login)

---

## Próximas fases (a combinar contigo)

1. **Tabelas e modelos:** Requerente, Gabinete, Tipo de Imóvel, Imóveis, Processos, Subcontratados, Orçamentos, Distritos, Concelhos, Freguesias.
2. **Orçamentos:** formulário de criação (com todos os campos que listaste), estados (ex.: rascunho, enviado, aceite, cancelado) e depois **Kanban** + **listagem** de orçamentos.
3. **Processos:** criação de processo quando o orçamento for aceite e fluxo de trabalhos.

Quando quiseres avançar, diz por onde preferes começar (tabelas, orçamentos ou Kanban) e seguimos a partir daí.
