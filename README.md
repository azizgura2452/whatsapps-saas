## 🚀 Project Setup
**Clone and Go Project**
```console
git clone https://github.com/MAK-PHP/Varsity_whatsapp.git
cd Varsity_whatsapp
```

**Install Composer & Node Dependencies**
```console
composer install
npm install
```

**Database & env creation**
- Create database called - `varsity_whatsapp`
- Create `.env` file by copying `.env` file

**Generate Artisan Key or necessary linkings**
```console
php artisan key:generate
php artisan storage:link
```

**Migrate Database with seeder**
```console
php artisan migrate:fresh --seed && php artisan module:seed
```

**Run Project**
```php
php artisan serve
npm run dev
```

So, You've got the project of Laravel Role & Permission Management on your http://localhost:8000


## ⚙️ How it works
1. Login using Super Admin Credential -
    1. Email - `superadmin@example.com`
    1. Password - `12345678`
1. Forget password - Password forget and reset will work if email is set up properly
1. Create User
1. Create Role
1. Assign Permission to Roles
1. Assign Multiple Role to an User
1. Check by login with the new credentials.
1. If you've not enough permission to do any task, you'll get a warning message.
1. Dashboard with Beautiful chart integrated
1. Module Based Development - Custom Module Add/Enable/Disable/Delete
1. Monitoring - Logging of every action of your application
1. Monitoring - Laravel Pulse
