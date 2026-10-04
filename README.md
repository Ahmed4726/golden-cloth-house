# Golden Cloth House — Social Commerce

Laravel 13 application for a clothing shop catalogue and multi-channel social publishing.

## Included
- Admin session authentication
- Product catalogue with reusable image URLs
- Product-aware social composer
- Facebook Page and Instagram Professional connection flow
- TikTok Login Kit / Content Posting connection flow
- Google OAuth / YouTube channel connection flow
- Encrypted access/refresh tokens
- One-click multi-channel publishing
- Per-channel publication records, errors and retry attempts
- Queue-based publishing and scheduled publishing
- Drafts, scheduling, publish/retry and history
- Platform readiness endpoint
- Feature tests and GitHub Actions CI

## Setup
1. Copy .env.example to .env.
2. Run composer install.
3. Run php artisan key:generate.
4. Configure the database.
5. Run php artisan migrate.
6. Create the first administrator at /register.
7. Configure provider credentials in .env.
8. Run php artisan queue:work.
9. Run php artisan schedule:work locally, or configure production cron to run php artisan schedule:run every minute.

## OAuth callback paths
- /connections/facebook/callback
- /connections/instagram/callback
- /connections/tiktok/callback
- /connections/youtube/callback

## Provider requirements

### Meta
Configure a Meta app with the required Page and Instagram permissions. The application exchanges authorization for a user token, discovers managed Pages, stores Page access tokens, and stores linked Instagram Professional accounts when available.

### TikTok
Configure TikTok Login Kit and Content Posting API. Direct Post requires the video.publish authorization/approval. TikTok states that unaudited clients are restricted to private viewing, and public media pulled from URLs must satisfy its verification requirements.

### YouTube
Configure Google OAuth and YouTube Data API. The application requests upload and channel-read access, stores refresh tokens, refreshes expired access tokens, and uses a resumable upload session for video publishing.

## Production checklist
- Use HTTPS and a strong APP_KEY.
- Never commit .env.
- Use Redis or a persistent queue backend and supervise the queue worker.
- Run the scheduler every minute.
- Restrict the initial admin registration route after creating the administrator.
- Configure provider redirect URLs exactly.
- Review each platform's permissions and approval/audit requirements before public production publishing.
- Monitor social_post_publications errors and queue failures.

## Testing
Run php artisan test. CI is configured in .github/workflows/tests.yml.
