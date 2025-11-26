# Tempone - Premium WordPress News Theme

**Version:** 0.1.5
**Author:** [Webane Indonesia](https://webane.com)
**License:** Proprietary (Premium Theme)
**Requires WordPress:** 6.0+
**Tested up to:** 6.4
**PHP Version:** 7.4+

---

## 📖 About

Tempone is a sophisticated WordPress theme for news websites and online magazines. Built with modern web technologies following WordPress best practices.

### Key Features

- 🎨 Modern Glassmorphism Design
- 📊 Advanced Analytics Dashboard
- ✍️ Custom Gutenberg Editor
- 🚀 Performance Optimized
- 🌐 Fully Translatable (English, Indonesian included)
- 📱 Mobile-First Responsive
- 🔍 SEO & Google News Ready
- 👤 Enhanced User Profiles with Analytics
- 🎨 Dynamic Color Customization via ACF

---

## 🚀 Installation

### Quick Install

1. Download latest release from [GitHub Releases](https://github.com/webane/tempone/releases)
2. Upload ZIP to **Appearance → Themes → Add New → Upload**
3. Activate theme
4. Auto-updates enabled!

### Manual Install

1. Upload `tempone` folder to `/wp-content/themes/`
2. Activate in **Appearance → Themes**
3. Install **Advanced Custom Fields Pro** (required)
4. Configure theme settings in **Tempone Setup**

---

## 📋 Required Plugins

- **Advanced Custom Fields Pro** (required)
- **Contact Form 7** (optional, for contact pages)

---

## ⚙️ Initial Setup

### 1. Theme Settings (Tempone Setup)

Navigate to **WP Admin → Tempone Setup** and configure:

**Customization Tab:**
- Primary Color (`ane-warna-utama`)
- Secondary Color (`ane-warna-utama-2`)
- Body Text Color (`ane-warna-text`)
- Light Background (`ane-warna-terang`)
- Dark Elements (`ane-warna-gelap`)
- Accent Color (`ane-warna-alternatif`)

**Company Info:**
- Company Name (`ane_company_name`)
- Address (`ane_company_address`)
- Phone (`ane_company_phone`)
- Email (`ane_company_email`)

**Social Media:**
- Facebook (`ane_facebook`)
- Instagram (`ane_instagram`)
- X/Twitter (`ane_x`)
- TikTok (`ane_tiktok`)
- Threads (`url_threads`)
- WhatsApp Channel (`ane_whatsapp_chanel`)
- WhatsApp Chat (`ane_whatsapp`)
- WhatsApp Message (`ane_whatsapp_message`)

### 2. Create Menus

**Appearance → Menus** - Create 5 menus:
1. **Primary Menu** - Main navigation
2. **Secondary Menu** - Top header links
3. **Footer Menu** - Footer links
4. **Media Network** - Network links
5. **Footer Bottom** - Bottom footer links

### 3. Configure Widgets

**Appearance → Widgets:**
- **Main Sidebar** - Blog/archive sidebar
- **Landing Page Sidebar** - Landing page sidebar

**Recommended Widgets:**
- Tempone: Posts (Popular, Recent, By Category)
- Search
- Categories
- Recent Comments

### 4. Create Homepage

**Pages → Add New:**
1. Title: "Homepage" atau "Home"
2. Template: **Landing Page**
3. Add ACF flexible content layouts
4. Set as homepage: **Settings → Reading → Static Front Page**

### 5. Set Blog Page

**Settings → Reading:**
- Homepage: Static page (select your landing page)
- Posts page: Select blog page

---

## 📧 Contact Form Setup

### Install Contact Form 7

1. Install **Contact Form 7** plugin
2. Activate plugin

### Create Contact Form

**Contact → Contact Forms → Add New**

Use this form code:
```
<p>
    <label>Name
        [text* your-name placeholder "Enter Your Full Name"]
    </label>
</p>

<p>
    <label>Email
        [email* your-email placeholder "Enter Your Email"]
    </label>
</p>

<p>
    <label>Phone Number
        [tel* your-phone placeholder "Enter Your Phone Number"]
    </label>
</p>

<p>
    <label>Message
        [textarea your-message placeholder "Your Message"]
    </label>
</p>

<p>
    [submit "Submit"]
</p>
```

### Email Settings (Mail Tab)

- **To:** `admin@yoursite.com`
- **From:** `[your-email] <wordpress@yoursite.com>`
- **Subject:** `[your-name] - Contact Form Submission`

**Message Body:**
```
From: [your-name] <[your-email]>
Phone: [your-phone]

Message:
[your-message]

--
This e-mail was sent from [_site_title] ([_site_url])
```

### Create Contact Page

1. **Pages → Add New**
2. Title: "Contact" or "Kontak"
3. Insert Contact Form 7 shortcode: `[contact-form-7 id="123"]`
4. Publish

### Configure Contact Page ACF

**Tempone Setup → Contact Page Content:**
- **Tagline** (`ane_title`): "We're Ready to Help You!"
- **Description** (`ane_description`): Full description text

Company info from Tempone Setup will auto-display on contact page.

---

## 🔄 Auto-Update System

Theme automatically checks for updates every 24 hours from GitHub releases.

### Manual Update Check

Add `?tempone_force_check=1` to themes page URL:
```
wp-admin/themes.php?tempone_force_check=1
```

### How Updates Work

1. Theme checks GitHub releases API
2. Compares current version with latest release
3. Shows update notification in **Appearance → Themes**
4. Click "Update Now" to install
5. All settings and content preserved

---

## 🚢 Deployment & Distribution

### For Theme Developers

#### 1. Setup GitHub Repository

```bash
cd /path/to/tempone
git init
git add .
git commit -m "Initial commit: Tempone v0.1.0"
git remote add origin git@github.com:yourusername/tempone.git
git push -u origin main
```

#### 2. Configure Repository

- Set repository to **Private** (for premium themes)
- Enable **Releases** feature
- Add collaborators if needed

#### 3. Update Repository URLs

Edit `inc/updater.php`:
```php
private $github_owner = 'yourusername';
private $github_repo = 'tempone';
```

#### 4. Create Release

```bash
# Update version in style.css
# Update CHANGELOG.md

# Create tag
git tag -a v0.1.0 -m "Release version 0.1.0"
git push origin v0.1.0
```

GitHub Actions will automatically:
- ✅ Compile SCSS files
- ✅ Compile translations
- ✅ Create ZIP package
- ✅ Create GitHub Release
- ✅ Upload distribution file

#### 5. Release Workflow

**For each new release:**

1. Make changes and commit
2. Update version in `style.css`
3. Update `CHANGELOG.md`
4. Create release tag: `git tag -a v0.2.0 -m "Release 0.2.0"`
5. Push tag: `git push origin v0.2.0`
6. GitHub Actions creates release automatically!

### Distribution Best Practices

- Use semantic versioning (MAJOR.MINOR.PATCH)
- Test on clean WordPress install before release
- Compile all SCSS to CSS
- Compile all translations (.po → .mo)
- No PHP errors or warnings
- No JavaScript console errors
- Test mobile responsive
- Cross-browser compatibility

---

## 🎨 Customization

### Theme Colors

All colors can be customized via **Tempone Setup → Customization**. Colors use CSS variables that automatically update across entire theme (frontend + admin).

### Custom Layouts (ACF Flexible Content)

Create custom layouts in `tp/` folder:
1. Create PHP file: `tp/your-layout.php`
2. Add layout to ACF Flexible Content field
3. Use ACF fields within layout

### Custom Widgets

Theme includes custom widget: **Tempone: Posts**
- Recent posts
- Popular posts (by views)
- Most commented
- Filter by category/tag

---

## 🛠️ Development

### SCSS Compilation

**Required after ANY CSS changes:**

```bash
# Main theme styles
npx sass scss/tempone.scss css/tempone.css

# Admin styles
npx sass scss/admin.scss css/admin.css

# Editor styles
npx sass scss/editor-style.scss css/editor-style.css

# Watch mode (auto-compile)
npx sass scss/tempone.scss css/tempone.css --watch
```

### Translation System

**Text Domain:** `tempone`
**Domain Path:** `/languages`
**Default Language:** English (en_US)

Theme is fully translation-ready with complete Indonesian translation included.

#### Creating New Translation

**Method 1: Loco Translate Plugin (Recommended)**

1. Install Loco Translate plugin
2. Go to: **Loco Translate → Themes → Tempone**
3. Click "New language" and select your language
4. Translate strings in the interface
5. Click "Save" - .mo file compiled automatically

**Method 2: Poedit (For Developers)**

1. Generate POT template:
   ```bash
   wp i18n make-pot . languages/tempone.pot --domain=tempone
   ```

2. Open `tempone.pot` in Poedit
3. Create new translation and save as: `tempone-{locale}.po`
   - Indonesian: `tempone-id_ID.po`
   - Spanish: `tempone-es_ES.po`
   - French: `tempone-fr_FR.po`

4. Poedit auto-generates `.mo` file on save

#### Compile Translations Manually

**Required after .po file changes:**

```bash
# Compile Indonesian translation
msgfmt -o languages/tempone-id_ID.mo languages/tempone-id_ID.po

# Or use WP-CLI
wp i18n make-mo languages

# Regenerate POT file
wp i18n make-pot . languages/tempone.pot --domain=tempone
```

#### Common Locale Codes

| Language            | Locale Code |
|---------------------|-------------|
| English (US)        | en_US       |
| Indonesian          | id_ID       |
| Spanish (Spain)     | es_ES       |
| French (France)     | fr_FR       |
| German              | de_DE       |
| Portuguese (Brazil) | pt_BR       |
| Chinese (Simplified)| zh_CN       |
| Japanese            | ja          |
| Korean              | ko_KR       |

#### Translation Coverage

✅ **Fully Translatable:**
- Admin panel strings
- ACF field labels and instructions
- Template parts (header, footer, sidebar)
- Widget labels
- Pagination text
- Comment forms
- Archive titles
- Error messages
- Button labels

#### Testing Translations

1. **Change WordPress Language:**
   - Go to: **Settings → General**
   - Site Language: Select your language
   - Save Changes

2. **Verify Translation:**
   - Check frontend (header, footer, sidebar)
   - Check single post page
   - Check archive pages
   - Clear cache and test in incognito

### Directory Structure

```
tempone/
├── css/              # Compiled CSS
├── scss/             # SCSS source files
├── js/               # JavaScript files
├── inc/              # PHP functions
│   └── admin/        # Admin customizations
├── tp/               # Template parts
├── languages/        # Translations
└── README.md         # This file
```

---

## 🐛 Troubleshooting

### Theme Colors Not Applying

1. Check ACF fields exist in **Tempone Setup → Customization**
2. Clear browser cache
3. Check `admin_head` hook priority (should be 999)
4. Inspect HTML `<head>` for `<style id="tempone-custom-colors">`

### Contact Form Not Styled

1. Ensure SCSS compiled: `npx sass scss/tempone.scss css/tempone.css`
2. Clear browser cache
3. Check Contact Form 7 shortcode is correct

### Update Notification Not Showing

1. Force check: `wp-admin/themes.php?tempone_force_check=1`
2. Delete transient: `delete_transient('tempone_update_check')`
3. Verify repository URLs in `inc/updater.php`
4. Check GitHub release exists and is public

### Dashboard/User Profile Not Loading

1. Check JavaScript console for errors
2. Verify Chart.js CDN loaded: `https://cdn.jsdelivr.net/npm/chart.js@4.4.0`
3. Clear browser cache
4. Deactivate conflicting plugins

### Translations Not Showing

1. **Check file names:**
   - Must be: `tempone-{locale}.mo` (e.g., `tempone-id_ID.mo`)
   - NOT just: `id_ID.mo`

2. **Check file location:**
   - Must be in: `/wp-content/themes/tempone/languages/`
   - NOT in: `/wp-content/languages/themes/`

3. **Check WordPress language:**
   - Settings → General → Site Language

4. **Regenerate translations:**
   ```bash
   # Update PO file in Poedit
   # Click "Update from POT file"
   # Save (will regenerate .mo)
   ```

5. **Force reload:**
   - Deactivate and reactivate theme
   - Clear all caches

---

## 📚 Documentation

### Complete Documentation Files

- **README.md** - This file (installation, setup, deployment, translations)
- **CLAUDE.md** - Complete developer guide for AI assistants
- **CHANGELOG.md** - Version history
- **inc/admin/README.md** - Admin customization system
- **languages/README.md** - Translation quick start
- **languages/COMPILE.md** - Translation compilation guide

### Support Resources

- **GitHub Issues:** https://github.com/webane/tempone/issues
- **Website:** https://webane.com
- **Email:** support@webane.com

---

## 📝 Changelog

See [CHANGELOG.md](CHANGELOG.md) for complete version history.

### Latest (v0.1.5)

- Enhanced user profile with analytics
- Performance optimizations
- Translation system improvements
- Admin UI enhancements

---

## 📄 License

**Proprietary Premium Theme License**

- Single site license per purchase
- Can be used on client projects
- Cannot be resold or redistributed
- GitHub repository: Private

---

**Made with ❤️ by [Webane Indonesia](https://webane.com)**
**Version:** 0.1.5 | **Last Updated:** 2025-01-25
