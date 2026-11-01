# Estudyantipid (Laravel / Blade, no Breeze)

Copy everything into a fresh Laravel project, keeping the same paths.
`routes/web.php` and `database/seeders/DatabaseSeeder.php` replace the defaults.

## Setup
1. **bootstrap/app.php** — replace `->withMiddleware(...)` with the block in `bootstrap/app.snippet.php`
   (registers the `role:` middleware and the login redirects).
2. **app/Models/User.php** — add the new columns to `$fillable`:

       protected $fillable = ['name', 'email', 'password', 'role', 'college'];

3. **Logos** (run from the project root; links expire ~7 days after creation):

       curl -L -o public/images/et-logo.png   "https://www.figma.com/api/mcp/asset/9bf0f37c-bb53-4b78-b643-136e94de874b.png"
       curl -L -o public/images/et-logo-1.png "https://www.figma.com/api/mcp/asset/e871cc84-cc95-45e5-b9b1-05d7f5ff0a87.png"

4. **Database + run**

       php artisan migrate
       php artisan db:seed
       php artisan serve

## Dev logins (from the seeder — change before deploying)
- Student: student@snsu.edu.ph / password  → /login
- Admin:   admin@snsu.edu.ph  / password  → /admin/login

## Pages
| URL | What |
|---|---|
| /login | Sign in (Student tab) |
| /admin/login | Sign in (Admin tab) |
| /register | Create student account |
| /student/dashboard | Student dashboard (needs student login) |
| /admin/overview | Placeholder (needs admin login) |

Add your other colleges to `config/estudyantipid.php`.
