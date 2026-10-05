# Golden Cloth House — Commerce + Social Publishing

Laravel 13 application for a production clothing storefront with Cash on Delivery, product management, orders, responsive product galleries, SEO, and multi-channel social publishing.

The Laravel project is the website. There is no separate website project and there is no social-commerce catalogue/checkout integration. Products are managed internally and can be published as normal social posts.

## Commerce features

- Responsive public clothing storefront
- Homepage hero slider with admin management
- Product catalogue with search and category filtering
- Product detail pages with multi-image gallery
- Local image uploads plus optional external image URLs
- Inventory / stock quantities
- Featured products and product sorting
- Compare-at pricing
- Session shopping cart
- Cash on Delivery checkout for Pakistan
- Shipping fee and free-shipping threshold configuration
- Atomic stock reservation/decrement during checkout
- Customer order confirmation page
- Optional queued customer confirmation email
- Public order tracking using order number + phone
- Admin order list, search and status workflow
- Automatic stock restoration when an order is cancelled
- SEO titles and descriptions
- Canonical URLs
- Open Graph and Twitter metadata
- Product JSON-LD structured data
- XML sitemap
- robots.txt
- Responsive mobile/tablet/desktop layouts
- Privacy and terms pages

## Social publishing features

- Facebook Page publishing
- Instagram Professional publishing
- TikTok photo/video Direct Post flow with asynchronous status checks
- YouTube video publishing with OAuth and token refresh
- Encrypted social access/refresh tokens
- One-click multi-channel normal social posts
- Per-channel publication records, errors and retries
- Scheduled publishing every minute
- Provider readiness endpoint

Social posts are normal posts containing captions, media and platform-specific metadata. The system does not depend on Facebook/Instagram/TikTok/YouTube Shops, product catalogues or social checkout.

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

For uploaded images to display correctly, the `public/storage` link must exist. For social publishing, `APP_URL` must be a real HTTPS public URL so Meta, TikTok and other providers can fetch media.

## Production / cPanel deployment

The Laravel `public` directory is the web document root.

1. Configure the production `.env`.
2. Use `APP_ENV=production` and `APP_DEBUG=false`.
3. Set a real HTTPS `APP_URL`.
4. Configure MySQL and persistent sessions/cache/queue as appropriate for the server.
5. Run:

```bash
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan storage:link
php artisan optimize
```

6. Run a persistent queue worker. Prefer Supervisor where available:

```bash
php artisan queue:work --tries=3 --timeout=120
```

7. Add a cron job every minute:

```bash
cd /home/USER/golden-cloth-house && /usr/local/bin/php artisan schedule:run >> /dev/null 2>&1
```

8. Ensure PHP has file uploads enabled and the server allows image uploads up to the application's 10 MB per-image limit.
9. Ensure `storage/` and `bootstrap/cache/` are writable by the web/queue user.
10. Configure SMTP if customer order confirmation emails are required.

## Store configuration

The `.env.example` contains:

- `STORE_NAME`
- `STORE_CURRENCY`
- `STORE_SHIPPING_FEE`
- `STORE_FREE_SHIPPING_THRESHOLD`
- `STORE_COD_ENABLED`
- `STORE_CONTACT_PHONE`
- `STORE_CONTACT_EMAIL`
- `STORE_WHATSAPP`
- `STORE_ADDRESS`
- social profile URLs

The default commerce configuration is PKR with Cash on Delivery enabled.

## Customer workflow

1. Customer visits `/`.
2. Customer browses `/shop` or searches by product/category/SKU.
3. Customer opens a product detail page and browses the photo gallery.
4. Customer selects quantity and adds the product to the cart.
5. Customer reviews the cart.
6. Customer enters Pakistani delivery information at checkout.
7. Customer places a Cash on Delivery order.
8. Stock is decremented inside a database transaction.
9. The customer receives an order confirmation page and, when email is configured, a queued confirmation email.
10. Admin manages the order from `/admin/orders`.
11. Customers can track orders from `/track-order` using their order number and phone number.

## Admin workflow

- `/admin` — dashboard
- `/admin/products` — products and inventory
- `/admin/slides` — homepage slider
- `/admin/orders` — Cash on Delivery orders
- `/admin/connections` — social account connections
- `/admin/posts` — normal social posts

## Product images

The product form accepts up to 20 local images per save, with JPG, JPEG, PNG and WebP support and a maximum of 10 MB per image.

Uploaded files are stored on Laravel's `public` disk under `products/` and the relative paths are stored in `products.media_paths`.

If an image is uploaded successfully but is not visible in the browser, run:

```bash
php artisan storage:link
```

and verify that `public/storage` points to `storage/app/public`.

## Meta: Facebook + Instagram

The application uses Meta/Facebook Login for a Facebook Page and a linked Instagram Professional account.

1. Create an app in Meta for Developers.
2. Configure the required Facebook Login functionality.
3. Add:
   - `https://YOUR-DOMAIN.com/admin/connections/facebook/callback`
   - `https://YOUR-DOMAIN.com/admin/connections/instagram/callback`
4. Set `META_APP_ID` and `META_APP_SECRET`.
5. The Facebook account must manage the target Page.
6. The Instagram account must be a Professional account linked to the Page.

The application requests the provider permissions defined in `config/social.php`. Meta may require App Review and Business Verification for production publishing.

## TikTok

1. Create a TikTok developer app.
2. Add Content Posting API.
3. Enable Direct Post.
4. Configure:
   - `https://YOUR-DOMAIN.com/admin/connections/tiktok/callback`
5. Set `TIKTOK_CLIENT_KEY` and `TIKTOK_CLIENT_SECRET`.
6. Complete TikTok's required approval/audit for public publishing.
7. Verify the production media domain/URL prefix when required by TikTok.

The implementation supports TikTok photo/video Direct Post and asynchronous status polling.

## YouTube

1. Create a Google Cloud project.
2. Enable YouTube Data API v3.
3. Configure OAuth consent.
4. Create a Web Application OAuth client.
5. Configure:
   - `https://YOUR-DOMAIN.com/admin/connections/youtube/callback`
6. Set `GOOGLE_CLIENT_ID` and `GOOGLE_CLIENT_SECRET`.
7. Connect the desired YouTube channel from the admin panel.

YouTube publishing is video-only and uses the YouTube Data API upload flow.

## SEO

The storefront includes:

- unique page titles
- meta descriptions
- canonical URLs
- Open Graph metadata
- Twitter cards
- Product JSON-LD
- ClothingStore JSON-LD on the homepage
- XML sitemap at `/sitemap.xml`
- robots.txt at `/robots.txt`
- clean product slugs
- crawlable product detail pages
- responsive image handling and lazy loading on product cards

## Testing

Run locally:

```bash
php artisan test
```

GitHub Actions runs the test suite on pushes to `master` and pull requests using PHP 8.3.

Commerce tests cover product image uploads, cart/checkout, Cash on Delivery order creation, inventory decrement and empty-cart protection.

## Security checklist

- Never commit `.env`.
- Use HTTPS in production.
- Use a strong `APP_KEY`.
- Keep provider secrets server-side only.
- Keep registration closed after the first administrator is created.
- Use a persistent queue backend and supervised worker in production.
- Restrict access to `/admin` through the application's admin session middleware.
- Review social provider permissions and audit requirements before enabling production publishing.
- Configure production mail credentials only on the server.
