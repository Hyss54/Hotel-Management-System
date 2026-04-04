# Deployment Guide for Gold Plus Hotel (Railway)

This guide explains how to deploy the Hotel Management System to Railway.

## Prerequisites
1. A **GitHub** account with this repository pushed to it.
2. A **Railway** account (connect it to your GitHub).

## Step-by-Step Deployment

### 1. Create a New Project
- Log in to [Railway](https://railway.app/).
- Click **New Project** > **Deploy from GitHub repo**.
- Select this repository.

### 2. Add MySQL Database
- In your Railway project, click **New** > **Database** > **MySQL**.
- Railway will automatically provision a database and inject variables like `MYSQLHOST`, `MYSQLUSER`, etc.

### 3. Configure Environment Variables
Go to the **Variables** tab of your app service and add:

| Key | Value/Source |
|---|---|
| `APP_ENV` | `production` |
| `APP_DEBUG` | `false` |
| `APP_KEY` | Copy from your local `.env` |
| `APP_URL` | Your Railway domain (e.g., `https://gold-plus-hotel.up.railway.app`) |
| `LOG_CHANNEL` | `errorlog` or `stack` |
| `LOG_LEVEL` | `error` |

*Note: You don't need to set `DB_*` variables manually; the app is configured to use Railway's `MYSQL*` variables automatically.*

### 4. Build and Deployment
Railway will detect the `nixpacks.toml` and automatically:
- Install PHP 8.2 and extensions.
- Install Composer and NPM dependencies.
- Build frontend assets (`npm run build`).
- Run migrations and seed structural data (`php artisan migrate --force`, `php artisan db:seed --force`).
- Start the server.

### 5. Access the System
Once the deployment is finished:
- Go to your Railway URL.
- Log in with:
    - **Email**: `admin@goldplushotel.com`
    - **Password**: `password`
- **IMPORTANT**: Change the admin password and email immediately in the profile settings after the first login.

## Troubleshooting
- **Build Fails**: Check the "Deployments" tab for logs.
- **Database Connection**: Ensure the MySQL plugin was added to the same project.
- **Missing Assets**: Ensure `NIXPACKS_NX_VITE_BUILD` or similar isn't causing issues (default config handles it).
