# Golden Cloth House — Social Commerce

Laravel 13 application for a clothing shop catalogue, public storefront, and multi-channel social publishing.

## What is included

- Public Golden Cloth House storefront at `/`
- Product catalogue at `/shop`
- Admin dashboard at `/admin`
- Product CRUD with price, SKU, category and description
- Local product-image uploads with public storage
- Product images automatically reused by the social composer
- Facebook Page publishing
- Instagram Professional publishing
- TikTok photo/video Direct Post flow with asynchronous status checks
- YouTube video publishing with OAuth and token refresh
- Encrypted social access/refresh tokens
- One-click multi-channel publishing
- Per-channel publication records, errors and retries
- Scheduled publishing every minute
- Provider readiness endpoint
- Feature tests and GitHub Actions CI

## Local setup

1. Copy `.env.example` to `.env`.
2. Run `composer install`.
3. Run `php artisan key:generate`.
4. Configure MySQL or SQLite.
5. Run `php artisan migrate`.
6. Run `php artisan storage:link`.
7. Create the first administrator at `/register`. Registration automatically closes after the first user exists.
8. Run `php artisan queue:work`.
9. Run `php artisan schedule:work` locally.

For social media publishing, `APP_URL` must be a real HTTPS public URL in production because Meta and TikTok need to fetch product media from the public internet.

## Website / domain deployment

This project is the website; no separate website project is required.

For cPanel:
- Point the domain/subdomain document root to the Laravel `public` directory.
- Configure the production `.env` with the real `APP_URL=https://your-domain.com`.
- Run `composer install --no-dev --optimize-autoloader`.
- Run `php artisan migrate --force`.
- Run `php artisan storage:link`.
- Run `php artisan optimize`.
- Keep the queue worker running. If Supervisor is unavailable, a cron job can run `php artisan queue:work --stop-when-empty --tries=3` frequently.
- Add a cron entry that runs `php artisan schedule:run` every minute.

Example cPanel cron:
```
cd /home/USER/golden-cloth-house && /usr/local/bin/php artisan schedule:run >> /dev/null 2>&1
```

The public site is `/`, while administration is `/admin`.

## Meta: Facebook + Instagram

The application currently uses the Meta/Facebook Login flow for a Facebook Page and a linked Instagram Professional account.

1. Create an app in Meta for Developers.
2. Add the Facebook Login product/configuration required by the app.
3. Configure the app's OAuth redirect URL to:
   - `https://YOUR-DOMAIN.com/admin/connections/facebook/callback`
4. Also register:
   - `https://YOUR-DOMAIN.com/admin/connections/instagram/callback`
5. Copy the Meta App ID into `META_APP_ID`.
6. Copy the Meta App Secret into `META_APP_SECRET`.
7. Keep the graph/authorize/token URLs from `.env.example` unless Meta gives you a different current endpoint.
8. The Facebook account used to connect must manage the Page.
9. For Instagram publishing, use an Instagram Professional account and link it to the Facebook Page. The app discovers the linked Instagram account during connection.

For the current Facebook Login publishing flow, the project requests:
- Facebook: `pages_show_list`, `pages_read_engagement`, `pages_manage_posts`
- Instagram: `instagram_basic`, `instagram_content_publish`, `pages_show_list`

Meta may require App Review, Business Verification, or additional approval before an app can publish for accounts outside its development/test roles. Do not assume a production account will work until Meta approves the requested permissions.

## TikTok

1. Create a developer app at TikTok for Developers.
2. Add the Content Posting API product.
3. Enable Direct Post.
4. Configure the web OAuth redirect URL:
   - `https://YOUR-DOMAIN.com/admin/connections/tiktok/callback`
5. Copy the Client Key to `TIKTOK_CLIENT_KEY`.
6. Copy the Client Secret to `TIKTOK_CLIENT_SECRET`.
7. Request/enable the required Content Posting permissions.
8. Connect the TikTok account from Admin → Social Connections.

TikTok's current Direct Post flow requires `video.publish` authorization/approval. Unaudited clients are restricted to private viewing. TikTok also requires public media URLs to come from a verified domain or URL prefix when using URL-based transfer. The application polls the returned `publish_id` until TikTok reports completion/failure.

## YouTube

1. Open Google Cloud Console and create a project.
2. Enable YouTube Data API v3.
3. Configure the OAuth consent screen.
4. Create OAuth 2.0 credentials for a Web application.
5. Add this authorized redirect URI:
   - `https://YOUR-DOMAIN.com/admin/connections/youtube/callback`
6. Copy the OAuth Client ID to `GOOGLE_CLIENT_ID`.
7. Copy the OAuth Client Secret to `GOOGLE_CLIENT_SECRET`.
8. Connect the desired YouTube channel from Admin → Social Connections.

The application requests:
- `https://www.googleapis.com/auth/youtube.upload`
- `https://www.googleapis.com/auth/youtube.readonly`

YouTube uploads from unverified API projects can be restricted to private viewing until the project passes Google's required audit.

## Posting workflow

1. Admin creates a product.
2. Upload product images in the product form.
3. Product appears automatically on the public storefront.
4. Open Admin → Create Post.
5. Select the product.
6. The title, description and stored product images are prefilled.
7. Select the connected social accounts.
8. Choose Publish Now or Schedule.
9. Laravel queues each platform publication independently.
10. The dashboard/publication history records success or the exact platform error.

### Platform media rules

- Facebook: current implementation publishes image posts.
- Instagram: current implementation publishes image posts through the Professional account flow.
- TikTok: supports photo and video Direct Post, subject to TikTok approval/audit.
- YouTube: requires a video URL; image-only products cannot be uploaded as YouTube videos.

## Important production requirement

Uploaded product images are stored under Laravel public storage and converted to absolute URLs for social publishing. Your production domain must be reachable from the public internet over HTTPS and `APP_URL` must be correct.

## OAuth callback paths

- `/admin/connections/facebook/callback`
- `/admin/connections/instagram/callback`
- `/admin/connections/tiktok/callback`
- `/admin/connections/youtube/callback`

## Testing

Run:
```
php artisan test
```

CI is configured in `.github/workflows/tests.yml`.

## Security checklist

- Never commit `.env`.
- Use HTTPS in production.
- Use a strong `APP_KEY`.
- Keep provider secrets server-side only.
- Registration closes after the first administrator is created.
- Use a persistent queue backend and a supervised worker in production.
- Review provider permissions and audit requirements before enabling customer-facing publishing.
