# Tempone Theme - Deployment Guide

Complete guide untuk setup GitHub repository dan auto-update system untuk premium theme distribution.

---

## 🚀 Step-by-Step Setup

### 1. Create GitHub Repository

```bash
# Navigate ke theme directory
cd /Applications/MAMP/htdocs/tempone/wp-content/themes/tempone

# Initialize git
git init

# Add all files
git add .

# Initial commit
git commit -m "Initial commit: Tempone v0.1.0"

# Create GitHub repository (via web atau gh CLI)
# Repository: https://github.com/webane/tempone

# Add remote
git remote add origin git@github.com:webane/tempone.git

# Push to GitHub
git push -u origin main
```

### 2. Configure Repository Settings

**GitHub Repository Settings:**
1. Go to repository Settings
2. Enable "Releases" feature
3. Set repository to Private (untuk premium theme)
4. Add collaborators if needed

**GitHub Secrets (for Actions):**
- `GITHUB_TOKEN` - Already available automatically
- No additional secrets needed for basic setup

### 3. Update Repository URLs in Code

Edit `inc/updater.php`:
```php
private $github_owner = 'webane';  // Your GitHub username
private $github_repo = 'tempone';   // Your repository name
```

### 4. Create First Release

```bash
# Tag version
git tag -a v0.1.0 -m "Release version 0.1.0 - Initial Release"

# Push tag (triggers GitHub Action)
git push origin v0.1.0
```

GitHub Actions will automatically:
- ✅ Compile SCSS files
- ✅ Compile translations
- ✅ Create ZIP package
- ✅ Create GitHub Release
- ✅ Upload distribution file

### 5. Verify Release

1. Go to: `https://github.com/webane/tempone/releases`
2. Check `tempone-0.1.0.zip` uploaded
3. Download and test installation

---

## 📦 Distribution Workflow

### For Each New Release:

1. **Make changes & commit:**
```bash
git add .
git commit -m "Add new feature"
git push origin main
```

2. **Update version in style.css:**
```css
/*
Version: 0.2.0
*/
```

3. **Update CHANGELOG.md:**
```markdown
## [0.2.0] - 2025-01-24
### Added
- New feature description
```

4. **Create release tag:**
```bash
git tag -a v0.2.0 -m "Release version 0.2.0"
git push origin v0.2.0
```

5. **GitHub Actions auto-creates release!**

---

## 🔄 How Auto-Update Works

### For End Users:

1. **Install theme** dari ZIP file atau upload manual
2. **Theme checks for updates** setiap 24 jam
3. **Update notification** muncul di Appearance → Themes
4. **Click "Update Now"** untuk install versi terbaru
5. **Settings preserved** - semua konfigurasi tetap aman

### Update Check Locations:

- Automatic: Every 24 hours (via transient cache)
- Manual: Visit `wp-admin/themes.php?tempone_force_check=1`
- Dashboard: Appearance → Themes (notification badge)

---

## 🛠️ Development Workflow

### Local Development:

```bash
# Create feature branch
git checkout -b feature/new-feature

# Make changes
# ... edit files ...

# Compile SCSS
sass scss/tempone.scss css/tempone.css

# Test changes
# ... test in browser ...

# Commit
git add .
git commit -m "Add new feature"

# Push to GitHub
git push origin feature/new-feature

# Create Pull Request on GitHub
# Merge to main when ready
```

### Pre-Release Checklist:

- [ ] All SCSS compiled to CSS
- [ ] All translations compiled (.po → .mo)
- [ ] Version updated in `style.css`
- [ ] `CHANGELOG.md` updated
- [ ] Tested on clean WordPress install
- [ ] No PHP errors or warnings
- [ ] No JavaScript console errors
- [ ] Mobile responsive tested
- [ ] Cross-browser tested

---

## 📊 GitHub Actions Workflow

File: `.github/workflows/release.yml`

**Triggers:** Push tags matching `v*.*.*`

**Steps:**
1. Checkout code
2. Setup Node.js
3. Install Sass
4. Compile SCSS → CSS
5. Compile PO → MO
6. Create distribution ZIP
7. Create GitHub Release
8. Upload ZIP asset

**Build Excludes:**
- `.git/` directory
- `.github/` workflows
- `node_modules/`
- `scss/` source files
- `.gitignore`, `.DS_Store`

---

## 🔐 License & Distribution

### Premium Theme License:

- Single site license per purchase
- Can be used on client projects
- Cannot be resold or redistributed
- GitHub repository: Private

### License Validation (Optional Enhancement):

For future versions, consider adding:
- License key system
- Domain validation
- Activation API
- Usage tracking

---

## 🎯 Recommended Enhancements

### Phase 2: License System

Add license validation:
```php
// inc/license.php
class Tempone_License {
    // Validate license key
    // Check domain activation
    // Enable/disable updates
}
```

### Phase 3: Analytics

Track installations:
- Number of active installations
- WordPress versions
- PHP versions
- Popular features

### Phase 4: Premium Support

- Ticketing system
- Documentation site
- Video tutorials
- Priority support for license holders

---

## 📞 Support

**Developer Contact:**
- Email: dev@webane.com
- GitHub Issues: https://github.com/webane/tempone/issues

**End-User Support:**
- Email: support@webane.com
- Documentation: https://webane.com/docs/tempone

---

## 🔄 Update History

### v0.1.0 (2025-01-23)
- ✅ Initial release
- ✅ Auto-update system implemented
- ✅ GitHub Actions configured
- ✅ Distribution workflow ready

---

**Deployment Guide v1.0**
Last updated: 2025-01-23
By: Webane Indonesia
