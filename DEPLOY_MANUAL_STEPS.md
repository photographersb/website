# 🚀 DEPLOY TO CPANEL - MANUAL STEPS

## Step 1: Connect to Server via SSH

**Open PowerShell and type:**

```powershell
ssh photogra@premium.bd101.svlogins.com
```

**When asked "Are you sure you want to continue?" → Type `yes` and press Enter**

You should now be connected to the server (prompt will change)

---

## Step 2: Generate SSH Key

**On the server terminal, paste:**

```bash
ssh-keygen -t rsa -b 4096 -f ~/.ssh/id_rsa -N ""
```

**Press Enter (it will generate the key)**

---

## Step 3: Display Your Public Key

**On the server terminal, paste:**

```bash
cat ~/.ssh/id_rsa.pub
```

**Copy the entire output (starts with `ssh-rsa`)**

---

## Step 4: Add SSH Key to GitHub

1. **Go to:** https://github.com/settings/keys
2. **Click:** "New SSH key"
3. **Title:** `cPanel Server`
4. **Key:** Paste the key you copied from Step 3
5. **Click:** "Add SSH key"

---

## Step 5: Deploy Repository

**Still on the server terminal, paste this entire block:**

```bash
cd /home/photogra
mv public_html public_html_backup
git clone git@github.com:mahidulislamnakib/photographer-sb.git public_html
cd public_html
composer install --optimize-autoloader --no-dev
npm install
npm run build
php artisan config:clear
php artisan cache:clear
php artisan route:clear
chmod -R 755 storage bootstrap/cache
chown -R photogra:photogra storage bootstrap/cache
```

**Wait for it to complete (5-10 minutes)**

---

## Step 6: Verify Deployment

When done, check:
- **Your site:** https://photographersb.com
- **Should be live!** ✅

---

## 🎉 DONE!

Your site is now deployed from GitHub! 

**Future updates are now super easy:**

### Local PC:
```powershell
cd "c:\xampp\htdocs\Photographar SB"
git add .
git commit -m "Your changes"
git push origin main
```

### Server Update:
```bash
ssh photogra@premium.bd101.svlogins.com
cd /home/photogra/public_html
git pull origin main
php artisan config:clear
php artisan cache:clear
```

---

**Ready to start?** Just follow the steps above in order! 🚀
