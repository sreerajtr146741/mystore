# 🚨 CRITICAL FIX: Database Connection Failed

Your backend is crashing because it's trying to connect to PostgreSQL on the MySQL port (**3306**). PostgreSQL runs on port **5432**.

## 🛑 IMMEDIATE ACTION REQUIRED

### 1. Update Render Environment Variables
Go to your **Render Dashboard** → **backend service** (buyorix-backend) → **Environment**.

1. Find `DB_PORT`.
2. Change the value from `3306` to `5432`.
3. Click **"Save Changes"**.
4. This will trigger a redeploy automatically.

### 2. Verify Database Host
Ensure `DB_HOST` is set to the **Internal Hostname** of your Render PostgreSQL database.
- It usually looks like: `dpg-d5eimajuibrs738emqh0-a`
- If you are connecting from outside Render, use the External Hostname, but for the backend service itself, use the Internal Hostname.

### 3. Redeploy
Once you've updated `DB_PORT` to `5432` in the dashboard, the service should restart. Check the **Logs** tab.
- You should see "Running migrations..."
- Then "Starting Apache..."

## 🔍 Why this happened
Your `render.yaml` was configured with `DB_PORT: 3306` (MySQL default), but your app is set to use `pgsql` (PostgreSQL). We have fixed `render.yaml` in the codebase, so future deployments will be correct, but you must manually update the running service's environment variable now.
