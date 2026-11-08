## Backend (Laravel API)

**Frontend Live URL:** [https://your-backend-url.vercel.app](https://your-backend-url.vercel.app)
**Backend Live URL:** [https://your-backend-url.vercel.app](https://your-backend-url.vercel.app)

### Setup Instructions

1. **Install:** `composer install`
2. **Setup:** `cp .env.example .env`
3. **Generate Key:** `php artisan key:generate`
4. **Database:** Create a MySQL database named `kb_ecommerce` and configure `.env`.
5. **Migrate & Seed:** `php artisan migrate --seed`
6. **Storage Link:** `php artisan storage:link`
7. **Run:** `php artisan serve`
8. **Local URL:** http://localhost:8000
9. **API Test:** http://localhost:8000/api/shops