# Quick Compile Guide - Generate .mo File

File `tempone-id_ID.po` sudah siap, tinggal di-compile menjadi `.mo` file agar bisa dibaca WordPress.

## ⚡ Method 1: Loco Translate Plugin (TERMUDAH - RECOMMENDED)

### Step 1: Install Plugin

1. Login ke WordPress Admin
2. Go to: **Plugins → Add New**
3. Search: `Loco Translate`
4. Click **Install Now**
5. Click **Activate**

### Step 2: Sync Translation File

1. Go to: **Loco Translate → Themes**
2. Click **Tempone**
3. Anda akan lihat: `Indonesian (Indonesia)` - **100% complete**
4. Click **Indonesian (Indonesia)**
5. Click **Sync** button (top right)
6. Click **Save**

✅ **DONE!** File `.mo` sudah otomatis ter-generate di folder `languages/`

### Step 3: Activate Indonesian

1. Go to: **Settings → General**
2. **Site Language**: Pilih **Bahasa Indonesia**
3. Click **Save Changes**

🎉 Website sekarang dalam Bahasa Indonesia!

---

## Method 2: Using Poedit (Untuk Developer)

### Step 1: Download Poedit

- Download dari: https://poedit.net/download
- Install di computer Anda

### Step 2: Compile

1. Open Poedit
2. File → Open
3. Pilih file: `tempone/languages/tempone-id_ID.po`
4. Click **Save** (Ctrl+S / Cmd+S)

✅ File `tempone-id_ID.mo` otomatis ter-generate di folder yang sama!

### Step 3: Upload ke Server

1. Upload file `tempone-id_ID.mo` ke:
   ```
   /wp-content/themes/tempone/languages/tempone-id_ID.mo
   ```

2. Activate di WordPress:
   - Settings → General → Site Language: Bahasa Indonesia

---

## Method 3: Command Line (macOS/Linux)

### Install gettext (jika belum ada)

```bash
# macOS
brew install gettext

# Ubuntu/Debian
sudo apt-get install gettext
```

### Compile PO to MO

```bash
# Navigate ke theme folder
cd /Applications/MAMP/htdocs/tempone/wp-content/themes/tempone

# Compile
msgfmt -o languages/tempone-id_ID.mo languages/tempone-id_ID.po

# Verify
ls -la languages/
```

Anda akan lihat file baru: `tempone-id_ID.mo`

### Activate

Settings → General → Site Language: Bahasa Indonesia

---

## Verification

Setelah compile dan activate, verify translation:

### Check Files Exist

```bash
ls -la wp-content/themes/tempone/languages/
```

Harus ada:
- ✅ tempone.pot
- ✅ tempone-id_ID.po
- ✅ tempone-id_ID.mo  ← **File ini yang dibaca WordPress!**

### Check Frontend

Visit website, lihat di:
- Header menu
- Footer
- Single post page
- Archive pages
- Sidebar widgets

Semua text seharusnya dalam **Bahasa Indonesia**.

### Clear Cache

Jika masih English:
1. Clear WordPress cache (jika pakai caching plugin)
2. Clear browser cache (Ctrl+Shift+Del)
3. Test di Incognito/Private window

---

## Troubleshooting

### Translation tidak muncul?

**Check 1: File .mo ada?**
```bash
ls wp-content/themes/tempone/languages/tempone-id_ID.mo
```

**Check 2: WordPress language setting?**
- Settings → General → Site Language = **Bahasa Indonesia** ✅

**Check 3: File permissions?**
```bash
chmod 644 wp-content/themes/tempone/languages/*.mo
```

**Check 4: Clear cache**
- Deactivate & reactivate theme
- Clear all caches
- Test in incognito

### Masih ada text dalam English?

Kemungkinan string tersebut dari:
- WordPress core (bukan dari theme)
- Plugin lain
- User content (post title, content, dll)

Theme Tempone hanya mentranslate **theme strings**, bukan WordPress core atau plugin.

---

## Recommendation

🏆 **GUNAKAN METHOD 1 (Loco Translate)** karena:
- ✅ Paling mudah - no command line
- ✅ Auto-compile setiap kali save
- ✅ Visual editor untuk edit translation
- ✅ Bisa langsung test di WordPress admin

---

## File Structure (After Compilation)

```
tempone/languages/
├── README.md              # Documentation
├── COMPILE.md             # Detailed guide
├── QUICK-COMPILE.md       # This file
├── tempone.pot            # Template (DO NOT DELETE)
├── tempone-id_ID.po       # Indonesian source (edit this)
└── tempone-id_ID.mo       # Indonesian compiled (auto-generated)
```

**IMPORTANT:**
- Edit: `.po` file
- WordPress reads: `.mo` file
- Keep both files
