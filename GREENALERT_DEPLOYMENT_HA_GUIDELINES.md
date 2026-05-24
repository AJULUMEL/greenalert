# GreenAlert — Deployment & High-Availability (HA) Guidelines

This document contains step-by-step deployment instructions, security hardening, scaling and high-availability options for GreenAlert. Docker-related instructions have been removed and archived.

## 1. Prerequisites

**System Requirements:**
```
OS:          Ubuntu 22.04 LTS
Architecture: x86_64
RAM:         Minimum 2GB (4GB recommended)
Disk Space:  Minimum 20GB
```

**Required Software:**
```
PHP:      8.2 or higher
MySQL:    8.0 or higher
Nginx:    1.18 or higher
Node.js:  18 LTS or higher
Composer: Latest version
```

**Optional but Recommended:**
```
Redis:    6.0 or higher (for caching)
```

---

## 2. Installation Steps

Below are concise, copy‑paste friendly instructions to get the application running from a cloned GitHub repository. Two workflows are provided:

- Quick development (Linux) — suitable for servers and developer machines
- Quick development (Windows with Laragon) — Windows-friendly steps

Choose the one that matches your environment.

2.1 Quick clone & run — Linux (development)

```bash
# 1. Clone repository
git clone <repository-url> greenalert
cd greenalert

# 2. Install PHP dependencies
composer install

# 3. Copy environment and generate key
cp .env.example .env
php artisan key:generate

# 4. Configure DB in .env (DB_DATABASE, DB_USERNAME, DB_PASSWORD)

# 5. Run migrations and seeders
php artisan migrate --seed

# 6. Create storage link and set permissions
php artisan storage:link
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache

# 7. Install Node assets (optional for front-end changes)
npm install
npm run dev   # for development
# or for production assets:
npm run build

# 8. Start dev server (quick)
php artisan serve --host=127.0.0.1 --port=8000

# Visit: http://127.0.0.1:8000
```

2.2 Quick clone & run — Windows (Laragon)

1. Place the repository inside Laragon's www folder (e.g., `C:\laragon\www\greenalert`) or clone there:

```powershell
cd C:\laragon\www
git clone <repository-url> greenalert
cd greenalert
```

2. In Laragon Terminal (or PowerShell with PHP/Composer on PATH):

```powershell
composer install
copy .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
npm install
npm run dev
```

3. Use Laragon's Apache/Nginx integration or run `php artisan serve` and open `http://localhost:8000`.

2.3 Production basics (Nginx + PHP-FPM)

These are concise production commands you can run on an Ubuntu server after cloning and configuring `.env`.

```bash
# Install dependencies (system packages should be preinstalled)
composer install --no-dev --optimize-autoloader
php artisan key:generate
php artisan migrate --force
php artisan db:seed --force   # optional
php artisan storage:link
npm ci && npm run build

# Cache optimizations
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Ensure correct permissions
sudo chown -R www-data:www-data /var/www/greenalert
sudo chmod -R 775 /var/www/greenalert/storage /var/www/greenalert/bootstrap/cache

# Start/ensure PHP-FPM and Nginx are running (systemd)
# sudo systemctl restart php8.2-fpm nginx
```

2.4 Production process supervision & scheduled tasks

- Run queue workers with Supervisor or systemd services. Example Supervisor program for `queue:work`:

```
[program:greenalert-queue]
command=php /var/www/greenalert/artisan queue:work --sleep=3 --tries=3 --memory=512
process_name=%(program_name)s_%(process_num)02d
numprocs=1
user=www-data
autostart=true
autorestart=true
stdout_logfile=/var/log/greenalert/queue.log
stderr_logfile=/var/log/greenalert/queue_err.log
```

- Laravel scheduler: add to root crontab:

```cron
* * * * * cd /var/www/greenalert && php artisan schedule:run >> /dev/null 2>&1
```

2.5 Notes

- Always store secrets (DB passwords, mail credentials) in `.env` and never commit them.
- Use HTTPS in production and obtain certificates from Let's Encrypt or your CA.

(Further HA/scale instructions are in section 6.)

(Further HA/scale instructions are in section 6.)

(See also `GREENALERT_PROFESSIONAL_DOCUMENTATION_v2.md` for architecture/diagrams.)

2.6 Nginx configuration and systemd examples (production-ready)

Below are ready-to-use configuration snippets and commands for a production Ubuntu server running Nginx + PHP-FPM. Adjust paths and domain names as needed.

2.6.1 Nginx site configuration

Create `/etc/nginx/sites-available/greenalert` with the following content (replace `greenalert.example.com`):

```nginx
server {
	listen 80;
	listen [::]:80;
	server_name greenalert.example.com;

	# Redirect HTTP to HTTPS
	return 301 https://$host$request_uri;
}

server {
	listen 443 ssl http2;
	listen [::]:443 ssl http2;
	server_name greenalert.example.com;

	root /var/www/greenalert/public;
	index index.php index.html;

	access_log /var/log/nginx/greenalert_access.log;
	error_log  /var/log/nginx/greenalert_error.log;

	# SSL certificates (use certbot to obtain)
	ssl_certificate /etc/letsencrypt/live/greenalert.example.com/fullchain.pem;
	ssl_certificate_key /etc/letsencrypt/live/greenalert.example.com/privkey.pem;
	ssl_protocols TLSv1.2 TLSv1.3;
	ssl_prefer_server_ciphers on;

	add_header X-Frame-Options "SAMEORIGIN" always;
	add_header X-Content-Type-Options "nosniff" always;
	add_header Referrer-Policy "no-referrer-when-downgrade" always;

	# Serve static assets directly
	location /assets/ {
		try_files $uri $uri/ =404;
		access_log off;
		expires 30d;
	}

	location / {
		try_files $uri $uri/ /index.php?$query_string;
	}

	location ~ \.php$ {
		include fastcgi_params;
		fastcgi_pass unix:/run/php/php8.2-fpm.sock;
		fastcgi_index index.php;
		fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
		fastcgi_param PATH_INFO $fastcgi_path_info;
	}

	# Deny access to sensitive files
	location ~ /\.(env|git) {
		deny all;
	}
}
```

Enable the site and test:

```bash
sudo ln -s /etc/nginx/sites-available/greenalert /etc/nginx/sites-enabled/greenalert
sudo nginx -t
sudo systemctl reload nginx
```

2.6.2 Obtain SSL with certbot (Let's Encrypt)

```bash
sudo apt update
sudo apt install -y certbot python3-certbot-nginx
sudo certbot --nginx -d greenalert.example.com
```

2.6.3 systemd service for Laravel queue worker

Create `/etc/systemd/system/greenalert-queue.service` with:

```ini
[Unit]
Description=GreenAlert Queue Worker
After=network.target

[Service]
User=www-data
Group=www-data
Restart=always
ExecStart=/usr/bin/php /var/www/greenalert/artisan queue:work --sleep=3 --tries=3 --memory=512
RestartSec=5

[Install]
WantedBy=multi-user.target
```

Enable and start the service:

```bash
sudo systemctl daemon-reload
sudo systemctl enable --now greenalert-queue.service
sudo journalctl -u greenalert-queue -f
```

2.6.4 Optional: systemd timer for `schedule:run` (instead of crontab)

Create `/etc/systemd/system/greenalert-schedule.service`:

```ini
[Unit]
Description=Run Laravel Scheduler

[Service]
Type=oneshot
User=www-data
Group=www-data
ExecStart=/usr/bin/php /var/www/greenalert/artisan schedule:run
```

Create `/etc/systemd/system/greenalert-schedule.timer`:

```ini
[Unit]
Description=Run Laravel schedule every minute

[Timer]
OnCalendar=*-*-* *:*:00
Persistent=true

[Install]
WantedBy=timers.target
```

Enable timer:

```bash
sudo systemctl daemon-reload
sudo systemctl enable --now greenalert-schedule.timer
sudo systemctl status greenalert-schedule.timer
```

2.6.5 Log rotation and directories

Create log directory and ensure permissions:

```bash
sudo mkdir -p /var/log/greenalert
sudo chown -R www-data:www-data /var/log/greenalert
```

Add a logrotate entry `/etc/logrotate.d/greenalert`:

```text
/var/log/nginx/greenalert_error.log /var/log/nginx/greenalert_access.log {
	daily
	rotate 14
	compress
	missingok
	notifempty
	create 0640 www-data adm
	sharedscripts
	postrotate
		systemctl reload nginx >/dev/null 2>&1 || true
	endscript
}
```

---

2.7 Quick troubleshooting

- Check Laravel logs: `tail -f storage/logs/laravel.log`
- Check Nginx: `sudo tail -f /var/log/nginx/greenalert_error.log`
- Check PHP-FPM status: `sudo systemctl status php8.2-fpm`
- Check queue service logs: `sudo journalctl -u greenalert-queue -f`


---

-- Removed: Docker deployment instructions were archived. --

## 4. Post-Installation & Optimization

(Commands for cache clearing, config caching, route caching, view caching, composer autoload optimization, and verification.)

---

## 5. Security Hardening

(Recommended file permissions, firewall rules, fail2ban, regular backups, SSL configuration and renewal.)

---

## 6. Scaling & High Availability

**For larger deployments:**

```
Option 1: Multiple Application Servers
├── Load Balancer (Nginx/HAProxy)
├── App Server 1
├── App Server 2
└── App Server 3 (all connect to shared DB + Redis)

Option 2: Kubernetes Deployment
├── Multiple GreenAlert pods
├── Redis cluster
├── MySQL cluster
└── Ingress controller

Option 3: Cloud Provider
├── Use Azure App Service / AWS ECS
├── Managed MySQL / RDS
├── Managed Redis / ElastiCache
└── CDN for static assets
