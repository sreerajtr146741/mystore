# How to Configure Gmail SMTP on Render

Since your local `.env` file is NOT uploaded to Render (for security), you must manually add your email settings to Render's Environment Variables.

## Step 1: Get a Google App Password (If using Gmail)
You cannot use your normal Gmail password. You must generate an App Password:
1. Go to your **Google Account Settings**.
2. Search for **"App Passwords"** (You must have 2-Step Verification enabled).
3. Create a new App Password named "Render".
4. Copy the 16-character password generated (e.g., `abcd efgh ijkl mnop`).

## Step 2: Add Variables to Render
1. Go to your [Render Dashboard](https://dashboard.render.com/).
2. Click on your Web Service (e.g., `mystore-backend` or `mystore-frontend`).
3. Click on the **"Environment"** tab on the left sidebar.
4. Click **"Add Environment Variable"** for each of the following keys:

| Key | Value (Example) |
|-----|-----------------|
| `MAIL_MAILER` | `smtp` |
| `MAIL_HOST` | `smtp.gmail.com` |
| `MAIL_PORT` | `587` |
| `MAIL_USERNAME` | `your-email@gmail.com` |
| `MAIL_PASSWORD` | `your-16-char-app-password` |
| `MAIL_ENCRYPTION` | `tls` |
| `MAIL_FROM_ADDRESS` | `your-email@gmail.com` |
| `MAIL_FROM_NAME` | `MyStore Support` |

## Step 3: Save & Redeploy
1. Click **"Save Changes"**.
2. Render might auto-deploy. If not, go to the **"Events"** tab and click **"Manual Deploy"** -> **"Deploy latest commit"**.

---
**Why is this necessary?**
Your `.env` file is in your `.gitignore`, so it never gets uploaded to GitHub or Render. This is a security best practice. Any configuration that was in your `.env` must be manually re-entered in Render's "Environment" tab.
