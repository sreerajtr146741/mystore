# 🐘 PostgreSQL Setup Guide for Render

## ✅ What's Been Configured

Your application is now configured to use **PostgreSQL** instead of MySQL/SQLite:

1. ✅ **PHP Extensions**: Added `pdo_pgsql` and `pgsql` to Dockerfile
2. ✅ **Default Connection**: Changed to `pgsql` in render.yaml
3. ✅ **Default Port**: Changed to `5432` (PostgreSQL standard)
4. ✅ **Default Username**: Changed to `postgres`

## 📋 Next Steps: Create PostgreSQL Database on Render

### Option 1: Using Render's PostgreSQL (Recommended - Free Tier Available)

#### Step 1: Create PostgreSQL Database
1. Go to your Render Dashboard: https://dashboard.render.com
2. Click **"New +"** → **"PostgreSQL"**
3. Fill in the details:
   - **Name**: `mystore-db` (or any name you prefer)
   - **Database**: `mystore` (or any name)
   - **User**: Will be auto-generated
   - **Region**: Choose same region as your web service
   - **Plan**: **Free** (or paid if you need more resources)
4. Click **"Create Database"**

#### Step 2: Wait for Database to be Created
- This takes about 1-2 minutes
- You'll see the status change to "Available"

#### Step 3: Get Database Connection Details
Once created, you'll see:
- **Internal Database URL** (use this for Render services)
- **External Database URL** (for external connections)
- **Hostname**
- **Port** (usually 5432)
- **Database Name**
- **Username**
- **Password**

#### Step 4: Configure Your Web Service
1. Go to your **Web Service** (buyorix-backend)
2. Click **"Environment"** tab
3. Add/Update these variables:

```
DB_CONNECTION=pgsql
DB_HOST=<your-postgres-hostname>
DB_PORT=5432
DB_DATABASE=<your-database-name>
DB_USERNAME=<your-postgres-username>
DB_PASSWORD=<your-postgres-password>
```

**💡 Pro Tip**: Render provides an **Internal Database URL** that looks like:
```
postgres://user:password@hostname:5432/database
```

You can parse this or use individual values.

#### Step 5: Redeploy
- After setting environment variables, click **"Manual Deploy"** → **"Deploy latest commit"**
- Or just wait - Render auto-deploys when you push to GitHub

### Option 2: Using Render's Database URL (Easier)

If Render provides a `DATABASE_URL` environment variable automatically:

1. In your web service environment, you might see `DATABASE_URL` already set
2. Laravel can use this directly if you add to your `.env` (already handled in Dockerfile):
   ```
   DATABASE_URL=${DATABASE_URL}
   ```

### Option 3: External PostgreSQL Provider

If you prefer an external provider:

**Free Options:**
- **Supabase**: https://supabase.com (Free tier: 500MB)
- **Neon**: https://neon.tech (Free tier: 3GB)
- **ElephantSQL**: https://www.elephantsql.com (Free tier: 20MB)

Then just set the connection details in Render environment variables.

## 🔍 Verify Database Connection

### After Deployment:

1. **Check Build Logs** for:
   ```
   Running migrations...
   Migration table created successfully.
   Migrating: 2014_10_12_000000_create_users_table
   Migrated:  2014_10_12_000000_create_users_table
   ...
   ```

2. **Visit Your App**: https://buyorix-backend.onrender.com/
   - Should now work without database errors!

3. **Check Debug Page**: https://buyorix-backend.onrender.com/debug.php
   - Should show `DB_CONNECTION: pgsql`

## ⚠️ Common Issues

### Issue: "SQLSTATE[08006] Connection refused"
**Solution**: 
- Verify `DB_HOST` is correct
- Ensure your web service and database are in the same region
- Use the **Internal Database URL** (not external) for Render services

### Issue: "SQLSTATE[08006] could not translate host name"
**Solution**:
- Check that `DB_HOST` doesn't include `postgres://` prefix
- Should be just the hostname, e.g., `dpg-xxxxx-a.oregon-postgres.render.com`

### Issue: "SQLSTATE[28000] password authentication failed"
**Solution**:
- Double-check `DB_USERNAME` and `DB_PASSWORD`
- Make sure there are no extra spaces
- Password might contain special characters - ensure they're properly set

## 📊 Database Management

### Access PostgreSQL via Render Dashboard:
1. Go to your PostgreSQL service
2. Click **"Connect"** → **"External Connection"**
3. Use tools like:
   - **pgAdmin** (GUI)
   - **psql** (CLI)
   - **DBeaver** (GUI)
   - **TablePlus** (GUI)

### Run Migrations Manually:
If migrations fail during deployment, you can run them via Render Shell:

1. Go to your Web Service
2. Click **"Shell"** tab
3. Run:
   ```bash
   php artisan migrate --force
   ```

## 🎯 Expected Environment Variables

After setup, your Render environment should have:

```env
# App
APP_NAME=MyStore
APP_ENV=production
APP_DEBUG=true  # Set to false after testing
APP_URL=https://buyorix-backend.onrender.com
APP_KEY=base64:... (auto-generated)

# Database (PostgreSQL)
DB_CONNECTION=pgsql
DB_HOST=dpg-xxxxx-a.oregon-postgres.render.com
DB_PORT=5432
DB_DATABASE=mystore
DB_USERNAME=mystore_user
DB_PASSWORD=your-secure-password

# Mail (Optional)
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@gmail.com
MAIL_FROM_NAME=MyStore
```

## ✅ Checklist

- [ ] PostgreSQL database created on Render
- [ ] Database connection details copied
- [ ] Environment variables set in web service
- [ ] Web service redeployed
- [ ] Build logs show successful migrations
- [ ] Application loads without database errors
- [ ] Debug mode disabled (`APP_DEBUG=false`) after testing

---

**Status**: PostgreSQL support configured ✅
**Next**: Create PostgreSQL database and set environment variables
**Time**: ~5 minutes to set up database
