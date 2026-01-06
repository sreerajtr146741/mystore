# 🔍 Troubleshooting HTTP 500 Errors on Render

## Current Status
Your application is deployed but returning **HTTP 500 Internal Server Error**.

## Immediate Actions

### 1. Push Latest Changes
```bash
git push origin main
```

This will deploy:
- ✅ Enhanced entrypoint script with detailed logging
- ✅ Debug mode enabled (APP_DEBUG=true)
- ✅ Debug script at `/debug.php`

### 2. Check Build Logs
After pushing, watch the Render deployment logs for:
```
=== Starting Laravel Application ===
Creating .env file from environment variables...
Generating APP_KEY...
Clearing cache...
Caching configuration...
Running migrations...
Laravel version: Laravel Framework X.X.X
Starting Apache...
```

### 3. Access Debug Script
Once deployed, visit:
```
https://buyorix-backend.onrender.com/debug.php
```

This will show:
- PHP version
- File structure
- Environment variables status
- Laravel bootstrap status
- Detailed error messages if any

### 4. Check Detailed Error Page
Visit the main URL:
```
https://buyorix-backend.onrender.com/
```

With `APP_DEBUG=true`, you'll now see the **actual Laravel error** instead of a generic 500 page.

## Common Causes of HTTP 500 Errors

### ❌ Missing APP_KEY
**Symptom**: "No application encryption key has been specified"

**Solution**: 
- Check Render environment variables
- Ensure `APP_KEY` is set (should auto-generate)
- Or manually set: `base64:YOUR_32_CHAR_KEY`

### ❌ Database Connection Failed
**Symptom**: "SQLSTATE[HY000] [2002] Connection refused"

**Solution**:
- Verify `DB_HOST`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` in Render
- If no database yet, temporarily set:
  ```
  DB_CONNECTION=sqlite
  ```

### ❌ Missing Storage Permissions
**Symptom**: "The stream or file could not be opened"

**Solution**: Already handled in entrypoint script, but check logs for permission errors

### ❌ Composer Dependencies Missing
**Symptom**: "Class 'X' not found"

**Solution**: Check build logs for `composer install` errors

### ❌ .env File Issues
**Symptom**: Various configuration errors

**Solution**: Entrypoint now creates `.env` from environment variables

## Step-by-Step Diagnosis

### Step 1: Check Environment Variables in Render
Go to Render Dashboard → Your Service → Environment

**Required Variables:**
- ✅ `APP_KEY` - Should be auto-generated
- ✅ `APP_URL` - Set to `https://buyorix-backend.onrender.com`
- ⚠️ `DB_HOST` - **MUST BE SET** (or use SQLite)
- ⚠️ `DB_DATABASE` - **MUST BE SET**
- ⚠️ `DB_USERNAME` - **MUST BE SET**
- ⚠️ `DB_PASSWORD` - **MUST BE SET**

### Step 2: Temporary SQLite Workaround
If you don't have a database yet, add this to Render environment:
```
DB_CONNECTION=sqlite
```

Then redeploy. This will let the app start without MySQL.

### Step 3: Check Build Logs
Look for these sections in Render logs:

**✅ Good Signs:**
```
Successfully built [image-id]
Successfully tagged [image-name]
=== Starting Laravel Application ===
Laravel version: Laravel Framework 12.x
Apache/2.4.65 configured -- resuming normal operations
```

**❌ Bad Signs:**
```
ERROR: composer install failed
ERROR: npm run build failed
Fatal error: Class not found
SQLSTATE[HY000]: Connection refused
```

### Step 4: Access Debug Information
Visit these URLs:

1. **Main App**: https://buyorix-backend.onrender.com/
   - Should now show detailed Laravel error page

2. **Debug Script**: https://buyorix-backend.onrender.com/debug.php
   - Shows environment and file structure

3. **API Test**: https://buyorix-backend.onrender.com/api/login
   - Should return JSON (404 or method not allowed is OK)

## Quick Fixes

### Fix 1: Use SQLite Temporarily
Add to Render environment variables:
```
DB_CONNECTION=sqlite
```
Remove or comment out: `DB_HOST`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`

### Fix 2: Ensure APP_KEY is Set
In Render Dashboard → Environment:
```
APP_KEY=base64:your-generated-key-here
```

Or let the entrypoint auto-generate it (already configured).

### Fix 3: Set APP_URL Correctly
```
APP_URL=https://buyorix-backend.onrender.com
```

### Fix 4: Disable Problematic Features Temporarily
Add to Render environment:
```
QUEUE_CONNECTION=sync
CACHE_DRIVER=file
SESSION_DRIVER=file
```

## What to Report Back

After pushing and redeploying, please share:

1. **Build Log Output** (especially the "=== Starting Laravel Application ===" section)
2. **What you see at** `/debug.php`
3. **The detailed error message** from the main page (with APP_DEBUG=true)
4. **Environment variables** you have set in Render (hide sensitive values)

## Next Steps After Diagnosis

Once we see the actual error:
1. Fix the specific issue
2. Set `APP_DEBUG=false` in render.yaml
3. Remove or secure `debug.php`
4. Redeploy

---

**Created**: January 6, 2026
**Status**: Awaiting deployment with debug mode enabled
