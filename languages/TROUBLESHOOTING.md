# Translation Troubleshooting Guide

## ⚠️ Common Issue: Translation Not Showing Despite Correct Setup

If you see **"Related Posts"**, **"Newest Posts"**, **"Comments"** in **English** even after:
- ✅ Setting WordPress language to Bahasa Indonesia
- ✅ Compiling .mo file successfully
- ✅ Verifying .mo file exists (11KB)

**The problem is: WordPress Translation Cache**

## 🔧 Solution (Step by Step)

### Step 1: Verify WordPress Language Setting

1. Go to: **Settings → General**
2. Check: **Site Language**
3. Must show: **Bahasa Indonesia** (not English)
4. Click: **Save Changes**

### Step 2: Force Translation Reload (CRITICAL!)

WordPress caches translations in memory (object cache). You MUST force reload:

**Method A: Deactivate/Reactivate Theme** (EASIEST)

1. Go to: **Appearance → Themes**
2. Click **Activate** on any other theme (e.g., Twenty Twenty-Three)
3. Wait 2 seconds
4. Click **Activate** on **Tempone** again

This forces WordPress to:
- Clear old translation cache
- Reload .mo file from disk
- Rebuild translation cache

**Method B: Flush Object Cache** (if using caching)

If you have Redis, Memcached, or object caching plugin:

1. Redis Object Cache plugin:
   - Settings → Redis → **Flush Cache**

2. W3 Total Cache:
   - Performance → Dashboard → **Empty All Caches**

3. WP Super Cache:
   - Settings → WP Super Cache → **Delete Cache**

4. WP Rocket:
   - Settings → **Clear Cache**

**Method C: Delete WordPress Transients** (advanced)

```bash
# Via WP-CLI
wp transient delete --all

# Or via plugin
# Install "Transients Manager" plugin and delete all transients
```

### Step 3: Clear Browser Cache

1. **Chrome/Edge**: Ctrl+Shift+Del (Windows) or Cmd+Shift+Del (Mac)
2. Select: **Cached images and files**
3. Click: **Clear data**
4. Or test in **Incognito/Private window**

### Step 4: Force Page Reload

1. **Hard refresh**: Ctrl+F5 (Windows) or Cmd+Shift+R (Mac)
2. Or: Ctrl+Shift+R (Cmd+Shift+R on Mac)

### Step 5: Verify Translation

Visit your website and check:
- Single post page → Should show **"Berita Terkait"** (not "Related Posts")
- Single post page → Should show **"Berita Terbaru"** (not "Newest Posts")
- Comments section → Should show **"Komentar"** (not "Comments")

## 🔍 Diagnostic Checklist

Run through this checklist:

### 1. WordPress Language Setting
```
✅ Settings → General → Site Language = Bahasa Indonesia
```

### 2. Translation Files Exist
```bash
ls -la wp-content/themes/tempone/languages/
```

Should show:
```
-rw-r--r--  tempone-id_ID.mo  (11KB)
-rw-r--r--  tempone-id_ID.po  (14KB)
-rw-r--r--  tempone.pot       (10KB)
```

### 3. File Permissions
```bash
chmod 644 wp-content/themes/tempone/languages/*.mo
```

### 4. MO File Is Not Corrupt
```bash
# Verify MO file contains translations
msgunfmt wp-content/themes/tempone/languages/tempone-id_ID.mo | grep "Related Posts"
```

Should output:
```
msgid "Related Posts"
msgstr "Berita Terkait"
```

### 5. WordPress Is Using Correct Locale

Add this temporary code to theme's `functions.php`:

```php
// TEMPORARY DEBUG CODE - DELETE AFTER TESTING
add_action( 'wp_footer', function() {
    if ( current_user_can( 'manage_options' ) ) {
        echo '<div style="position:fixed;bottom:0;left:0;background:#000;color:#fff;padding:10px;z-index:999999;">';
        echo 'Locale: ' . get_locale() . '<br>';
        echo 'Translation: ' . __( 'Related Posts', 'tempone' ) . '<br>';
        echo 'Text Domain Loaded: ' . ( is_textdomain_loaded( 'tempone' ) ? 'YES' : 'NO' );
        echo '</div>';
    }
} );
```

Expected output when viewing frontend:
```
Locale: id_ID
Translation: Berita Terkait
Text Domain Loaded: YES
```

If you see:
- ❌ `Locale: en_US` → WordPress language not set to Indonesian
- ❌ `Translation: Related Posts` → Translation not loaded
- ❌ `Text Domain Loaded: NO` → Text domain not loaded

### 6. Theme Translation Loading Code

Check `wp-content/themes/tempone/inc/setup.php`:

Should have:
```php
function tempone_setup() : void {
    // Direct .mo file loading (bypasses WordPress translation cache)
    $locale = determine_locale();
    $mofile = get_template_directory() . "/languages/tempone-{$locale}.mo";

    if ( file_exists( $mofile ) ) {
        load_textdomain( 'tempone', $mofile );
    }
    // ... rest of setup
}
add_action( 'after_setup_theme', 'tempone_setup' );
```

**Note**: Theme uses `load_textdomain()` instead of `load_theme_textdomain()` to bypass WordPress caching issues.

## 🎯 Most Common Causes

### 1. WordPress Object Cache (90% of cases)
**Symptom**: Everything correct, but still showing English

**Fix**: Deactivate/reactivate theme, or flush object cache

**Why**: WordPress caches translations in memory for performance. When you compile new .mo file, WordPress still uses old cached version.

### 2. WordPress Language Not Set
**Symptom**: HTML shows `lang="en-US"` instead of `lang="id"`

**Fix**: Settings → General → Site Language → Bahasa Indonesia → Save

### 3. Browser Cache
**Symptom**: Other pages show Indonesian, but one page still English

**Fix**: Hard refresh (Ctrl+F5) or test in Incognito

### 4. Page Cache Plugin
**Symptom**: First visit shows English, after clearing cache shows Indonesian

**Fix**: Clear page cache in W3 Total Cache / WP Super Cache / WP Rocket

### 5. Wrong Locale Code
**Symptom**: Files named `tempone-id.mo` instead of `tempone-id_ID.mo`

**Fix**: Rename file to exact format: `tempone-id_ID.mo`

## 💡 Quick Test

To quickly test if translations work, add this to any theme file:

```php
echo __( 'Related Posts', 'tempone' );
```

- If shows: **"Berita Terkait"** → ✅ Translations working
- If shows: **"Related Posts"** → ❌ Translations NOT loading

## 📞 Still Not Working?

If after ALL steps above, translation still not working:

### Last Resort Solutions:

**1. Regenerate MO file:**
```bash
cd wp-content/themes/tempone
msgfmt -o languages/tempone-id_ID.mo languages/tempone-id_ID.po
```

**2. Try Loco Translate plugin:**
1. Install "Loco Translate" plugin
2. Loco Translate → Themes → Tempone
3. Click "Indonesian (Indonesia)"
4. Click "Sync"
5. Click "Save"

**3. Manually force translation load:**

Add to `functions.php` (temporary):
```php
add_action( 'init', function() {
    unload_textdomain( 'tempone' );
    load_theme_textdomain( 'tempone', get_template_directory() . '/languages' );
}, 999 );
```

**4. Check for conflicts:**

Deactivate ALL plugins temporarily, then test. If it works, reactivate plugins one by one to find conflict.

## ✅ Success Confirmation

You'll know it's working when you see:

**Frontend:**
- ✅ "Berita Terkait" (not "Related Posts")
- ✅ "Berita Terbaru" (not "Newest Posts")
- ✅ "Komentar" (not "Comments")
- ✅ "Bagikan" (not "Share")
- ✅ "Baca Selengkapnya" (not "Read More")

**HTML Source:**
```html
<html lang="id" ...>
```

Not:
```html
<html lang="en-US" ...>
```

## 📚 Additional Resources

- [WordPress i18n Documentation](https://developer.wordpress.org/apis/handbook/internationalization/)
- [Loco Translate Plugin](https://wordpress.org/plugins/loco-translate/)
- [Poedit - Translation Editor](https://poedit.net/)

---

**Remember**: WordPress translation cache is the #1 cause of "translations not showing" issues. Always deactivate/reactivate theme after compiling new .mo files!
