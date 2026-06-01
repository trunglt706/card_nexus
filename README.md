# Card Nexus

Ứng dụng web quản lý thẻ/ví xây dựng trên **Laravel 10**, **Inertia.js**, **React** và **Vite**.

## Yêu cầu

| Thành phần | Phiên bản |
|------------|-----------|
| PHP | ^8.1 (khuyến nghị 8.2) |
| Composer | 2.x |
| Node.js | 20.x |
| MySQL | 8.0 |
| Redis | 7.x |

**PHP extensions:** `pdo_mysql`, `mbstring`, `bcmath`, `pcntl`, `zip`, `redis` (hoặc dùng `predis` qua Composer).

## Cài đặt local (không Docker)

```bash
cp .env.example .env
composer install
php artisan key:generate
php artisan migrate

npm ci
npm run build   # hoặc npm run dev khi phát triển frontend
php artisan serve
```

- Ứng dụng: http://127.0.0.1:8000  
- Vite dev server: http://127.0.0.1:5173 (chạy `npm run dev` song song)

Cấu hình mặc định trong `.env.example`: timezone `Asia/Ho_Chi_Minh`, locale `vi`, cache/queue qua Redis.

---

## Docker

Stack gồm **PHP-FPM**, **Nginx**, **MySQL**, **Redis** và **queue worker** (`php artisan queue:work redis`).

### Cấu trúc file

```
Dockerfile              # Multi-stage build (frontend, vendor, production, web, development)
docker-compose.yml      # Production
docker-compose.dev.yml  # Overlay cho môi trường dev
.env.docker.example     # Biến môi trường gợi ý cho Docker
docker/
  nginx/default.conf
  php/conf.d/laravel.ini
  entrypoint.sh
```

### Production

```bash
cp .env.docker.example .env
# Tạo APP_KEY: php artisan key:generate (trên máy host) hoặc để entrypoint tạo khi khởi động

docker compose up -d --build
```

| Biến | Mặc định | Ghi chú |
|------|----------|---------|
| `APP_PORT` | `8080` | Cổng Nginx |
| `APP_URL` | `http://localhost:8080` | Khớp với cổng truy cập |
| `DB_DATABASE` | `card_nexus` | |
| `DB_USERNAME` / `DB_PASSWORD` | `card_nexus` / `secret` | |
| `RUN_MIGRATIONS` | `true` | Tự chạy migrate khi container `app` start |

Truy cập: **http://localhost:8080**

Chỉ build lại image ứng dụng (tránh lỗi tag trùng khi build song song):

```bash
docker compose build app
docker compose up -d
```

### Development (Docker)

Mount mã nguồn, bật debug, chạy Vite HMR:

```bash
docker compose -f docker-compose.yml -f docker-compose.dev.yml up --build
```

| Dịch vụ | URL / ghi chú |
|---------|----------------|
| Ứng dụng (Nginx) | http://localhost:8080 |
| Vite | http://localhost:5173 |

Trong container `app`, cài dependency lần đầu (nếu cần):

```bash
docker compose exec app composer install
docker compose exec app php artisan key:generate
```

### Dịch vụ Docker

| Service | Image / build | Vai trò |
|---------|---------------|---------|
| `app` | `card-nexus-app:latest` | PHP 8.2-FPM, Laravel |
| `nginx` | `card-nexus-web:latest` | Reverse proxy, static `public/` |
| `queue` | Dùng chung image `app` | Xử lý hàng đợi Redis |
| `mysql` | `mysql:8.0` | Cơ sở dữ liệu |
| `redis` | `redis:7-alpine` | Cache, session, queue |

> **Lưu ý:** Chỉ service `app` có `build:`; `queue` tái sử dụng image `card-nexus-app:latest` để tránh xung đột tag khi `docker compose build`.

### Stage trong Dockerfile

| Stage | Mô tả |
|-------|--------|
| `frontend` | `npm ci` + `npm run build` (Vite/React) |
| `vendor` | `composer install --no-dev` (PHP 8.2) |
| `production` | PHP-FPM + extensions |
| `web` | Nginx + thư mục `public` đã build |
| `development` | Thêm Node.js và Composer cho dev |

### Lệnh thường dùng

```bash
# Xem log
docker compose logs -f app

# Artisan
docker compose exec app php artisan migrate
docker compose exec app php artisan tinker

# Dừng và xóa container (giữ volume DB)
docker compose down

# Dừng và xóa cả volume (mất dữ liệu DB)
docker compose down -v
```

### Xử lý sự cố

**`image "card-nexus-app:latest": already exists`**

Hai service cùng build một tag. Đã cấu hình chỉ `app` build; chạy `docker compose build app` rồi `docker compose up -d`.

**Đổi code production**

Image production nhúng sẵn assets và `vendor`. Sau khi sửa code hoặc frontend:

```bash
docker compose build app nginx
docker compose up -d
```

---

## Kiểm thử

```bash
php artisan test
# hoặc trong Docker:
docker compose exec app php artisan test
```

## Giấy phép

MIT
