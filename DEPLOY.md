# Deploy EPOC XXI (Ubuntu 22.04 / DigitalOcean)

Depois de clonar o repositório para `/var/www/html`, executar no servidor (SSH).

## 1. Entrar na pasta do projeto

```bash
cd /var/www/html
```

## 2. Instalar dependências PHP (produção)

```bash
composer install --no-dev --optimize-autoloader
```

Se `composer` não existir: `apt update && apt install -y composer` (ou instalar PHP 8.2+ e composer manualmente).

## 3. Ficheiro de ambiente

```bash
cp .env.example .env
php artisan key:generate
```

Editar o `.env` com os valores do servidor:

```bash
nano .env
```

**Não copies o `.env` do teu PC para o servidor** — no servidor usa valores de produção.

Alterar pelo menos:

- `APP_NAME=EPOC`
- `APP_ENV=production`
- `APP_DEBUG=false`
- `APP_URL=https://teu-dominio.com` (ou o IP, ex: `http://188.166.114.165`)
- `APP_KEY=` → deve estar preenchido (corre `php artisan key:generate` se estiver vazio)

**Base de dados (e driver PHP):**

- **Opção A – SQLite** (mais simples, sem MySQL):  
  - No `.env`: `DB_CONNECTION=sqlite` (e deixa comentadas as linhas DB_HOST, DB_DATABASE, etc.).
  - Instalar o driver no servidor: `apt install -y php8.2-sqlite3` (ou `php8.1-sqlite3` conforme `php -v`). Depois: `systemctl restart php8.2-fpm`.
  - Criar o ficheiro da base: `touch database/database.sqlite` e `chown www-data:www-data database/database.sqlite`.

- **Opção B – MySQL**:  
  - No `.env` descomentar e preencher:
  ```
  DB_CONNECTION=mysql
  DB_HOST=127.0.0.1
  DB_PORT=3306
  DB_DATABASE=epocxxi
  DB_USERNAME=epocxxi
  DB_PASSWORD=password_seguro
  ```
  - Instalar o driver: `apt install -y php8.2-mysql` (ou php8.1-mysql). Depois: `systemctl restart php8.2-fpm`.
  - Criar a base e utilizador:
  ```bash
  mysql -u root -p -e "CREATE DATABASE epocxxi; CREATE USER 'epocxxi'@'localhost' IDENTIFIED BY 'password_seguro'; GRANT ALL ON epocxxi.* TO 'epocxxi'@'localhost'; FLUSH PRIVILEGES;"
  ```

## 4. Migrações e cache

```bash
php artisan migrate --force
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## 5. Permissões

O utilizador do servidor web (nginx ou apache) deve ser dono de `storage` e `bootstrap/cache`:

```bash
chown -R www-data:www-data /var/www/html
chmod -R 755 storage bootstrap/cache
chmod -R 775 storage bootstrap/cache
```

(Se usares Apache, o utilizador pode ser `www-data`; confirma com o teu config.)

## 6. Servidor web a apontar para `public`

O document root deve ser a pasta **public** do Laravel.

**Nginx** (ex.: ficheiro em `/etc/nginx/sites-available/default`):

```nginx
root /var/www/html/public;

location / {
    try_files $uri $uri/ /index.php?$query_string;
}

location ~ \.php$ {
    fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
    fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
    include fastcgi_params;
}
```

Depois:

```bash
nginx -t && systemctl reload nginx
```

**Apache**: `DocumentRoot` deve ser `/var/www/html/public` e `AllowOverride All` ativo. Depois:

```bash
systemctl reload apache2
```

## 7. (Opcional) Assets compilados

O projeto usa Tailwind/Alpine por CDN se não existir `public/build`. Para compilar no servidor (Node.js necessário):

```bash
npm ci
npm run build
```

Não é obrigatório para o site funcionar.

## 8. Verificar

Abrir no browser: `http://188.166.114.165` (ou o teu domínio). Deve aparecer o redirect para login.

---

## Atualizar o site no futuro (git pull)

```bash
cd /var/www/html
git pull
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
chown -R www-data:www-data storage bootstrap/cache
```
