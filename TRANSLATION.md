# Tempone Theme Translation Guide

## Overview

Tempone theme is **translation-ready** with English as the default language. All user-facing text uses WordPress i18n functions with the text domain `tempone`.

## Translation System

### Text Domain

- **Text Domain**: `tempone`
- **Domain Path**: `/languages`
- **Default Language**: English (en_US)

### Translation Functions Used

The theme uses standard WordPress translation functions:

```php
__( 'Text', 'tempone' )              // Returns translated string
_e( 'Text', 'tempone' )              // Echoes translated string
esc_html__( 'Text', 'tempone' )      // Returns escaped translated string
esc_html_e( 'Text', 'tempone' )      // Echoes escaped translated string
esc_attr__( 'Text', 'tempone' )      // Returns escaped attribute translated string
esc_attr_e( 'Text', 'tempone' )      // Echoes escaped attribute translated string
```

## How to Translate Tempone

### Method 1: Using Loco Translate Plugin (Recommended for Non-Developers)

1. **Install Loco Translate**:
   - Go to: Plugins → Add New
   - Search: "Loco Translate"
   - Install and Activate

2. **Create Translation**:
   - Go to: Loco Translate → Themes → Tempone
   - Click "New language"
   - Select your language (e.g., Indonesian - Bahasa Indonesia)
   - Choose location: /wp-content/themes/tempone/languages/
   - Click "Start translating"

3. **Translate Strings**:
   - Find English strings in the list
   - Enter translations in your language
   - Click "Save" when done

4. **Sync Translations**:
   - Click "Sync" button if you add new translatable strings to theme

### Method 2: Using Poedit (Recommended for Developers)

1. **Generate POT File** (Template):
   ```bash
   # Using WP-CLI
   wp i18n make-pot . languages/tempone.pot --domain=tempone

   # Or using wp-pot-cli
   wppot --domain=tempone --src=. --dest-file=languages/tempone.pot
   ```

2. **Create PO File**:
   - Open Poedit
   - Click "Create new translation"
   - Select `languages/tempone.pot`
   - Choose your language
   - Save as: `languages/tempone-{locale}.po`
     - Indonesian: `tempone-id_ID.po`
     - Spanish: `tempone-es_ES.po`
     - French: `tempone-fr_FR.po`

3. **Translate**:
   - Translate each string in Poedit
   - Poedit will automatically generate `.mo` file when you save

4. **Upload**:
   - Upload `.po` and `.mo` files to: `/wp-content/themes/tempone/languages/`

### Method 3: Using WPML / Polylang

1. **Install Plugin**:
   - WPML: https://wpml.org/
   - Polylang: https://wordpress.org/plugins/polylang/

2. **Configure**:
   - Add languages in plugin settings
   - Plugin will detect theme strings automatically

3. **Translate**:
   - Use plugin's string translation interface
   - Translate theme strings, posts, pages, menus

## File Structure

```
tempone/
├── languages/
│   ├── tempone.pot           # Template file (to be generated)
│   ├── tempone-id_ID.po      # Indonesian translation source
│   ├── tempone-id_ID.mo      # Indonesian compiled translation
│   ├── tempone-es_ES.po      # Spanish translation source
│   └── tempone-es_ES.mo      # Spanish compiled translation
```

## Common Locale Codes

| Language              | Locale Code |
|-----------------------|-------------|
| English (US)          | en_US       |
| Indonesian            | id_ID       |
| Spanish (Spain)       | es_ES       |
| French (France)       | fr_FR       |
| German                | de_DE       |
| Portuguese (Brazil)   | pt_BR       |
| Chinese (Simplified)  | zh_CN       |
| Japanese              | ja          |
| Korean                | ko_KR       |
| Arabic                | ar          |
| Dutch                 | nl_NL       |
| Italian               | it_IT       |
| Russian               | ru_RU       |

## Generating POT File

### Using WP-CLI (Recommended)

```bash
cd /path/to/tempone
wp i18n make-pot . languages/tempone.pot --domain=tempone
```

### Using Poedit

1. Open Poedit
2. File → New from source code
3. Select theme folder
4. Save as `languages/tempone.pot`

### Using wp-pot-cli (Node.js)

```bash
npm install -g wp-pot-cli
cd /path/to/tempone
wppot --domain=tempone --src=. --dest-file=languages/tempone.pot
```

## Translation Coverage

### Translatable Areas

✅ **Fully Translatable**:
- Admin panel strings
- ACF field labels and instructions
- Template parts (header, footer, sidebar)
- Widget labels
- Pagination text
- Comment forms
- Archive titles
- Error messages
- Button labels
- Meta information

✅ **Already in English**:
- All admin panel text
- All ACF field labels
- All template strings
- All widget text
- All error messages

### Non-Translatable Content

❌ **User Content** (Translate via WordPress admin or WPML/Polylang):
- Post titles and content
- Page titles and content
- Custom field values
- Menu items
- Widget content (titles, text)

## Testing Translations

1. **Change WordPress Language**:
   - Go to: Settings → General
   - Site Language: Select your language
   - Save Changes

2. **Verify Translation**:
   - Check frontend (header, footer, sidebar)
   - Check single post page
   - Check archive pages
   - Check admin panel (if admin language same as site)

3. **Clear Cache**:
   - Clear WordPress cache
   - Clear browser cache
   - Check in incognito mode

## Troubleshooting

### Translations Not Showing

1. **Check file names**:
   - Must be: `tempone-{locale}.mo`
   - Example: `tempone-id_ID.mo` (NOT `id_ID.mo`)

2. **Check file location**:
   - Must be in: `/wp-content/themes/tempone/languages/`
   - NOT in: `/wp-content/languages/themes/`

3. **Check WordPress language**:
   - Settings → General → Site Language

4. **Check file permissions**:
   ```bash
   chmod 644 languages/*.mo
   chmod 644 languages/*.po
   ```

5. **Force reload**:
   - Deactivate and reactivate theme
   - Or add to `functions.php` temporarily:
   ```php
   add_action( 'after_setup_theme', function() {
       load_theme_textdomain( 'tempone', get_template_directory() . '/languages' );
   }, 1 );
   ```

### Missing Strings

1. **Regenerate POT file**:
   ```bash
   wp i18n make-pot . languages/tempone.pot --domain=tempone
   ```

2. **Update PO file**:
   - Open `.po` file in Poedit
   - Click "Update from POT file"
   - Select `tempone.pot`
   - Translate new strings
   - Save (will regenerate `.mo`)

### ACF Fields Not Translated

ACF field labels are hardcoded in PHP. To translate:

1. **Using Loco Translate**:
   - Strings will appear automatically
   - Just translate them

2. **Using WPML**:
   - WPML → String Translation
   - Find and translate ACF strings

## Indonesian Translation Quick Start

For Indonesian translation, key strings to translate:

### Common UI Elements

| English                    | Indonesian                |
|----------------------------|---------------------------|
| Read more                  | Baca selengkapnya        |
| Categories                 | Kategori                 |
| Tags                       | Tag                      |
| Search                     | Cari                     |
| Home                       | Beranda                  |
| Comments                   | Komentar                 |
| Leave a comment            | Tinggalkan komentar      |
| Post navigation            | Navigasi pos             |
| Previous post              | Pos sebelumnya           |
| Next post                  | Pos selanjutnya          |
| Related posts              | Berita terkait           |
| No posts found             | Tidak ada postingan      |

## Contributing Translations

If you create a translation, consider contributing it back:

1. Fork the theme repository
2. Add your `.po` and `.mo` files to `/languages/`
3. Submit a pull request
4. Or email files to: support@tempone.com

## Resources

- WordPress i18n: https://developer.wordpress.org/apis/handbook/internationalization/
- Poedit: https://poedit.net/
- Loco Translate: https://wordpress.org/plugins/loco-translate/
- WPML: https://wpml.org/
- Polylang: https://polylang.pro/

## Notes

- Always translate `.po` files, NOT `.mo` files
- `.mo` files are compiled automatically from `.po` files
- Keep `.pot` file as reference template
- Text domain `tempone` is registered in `inc/setup.php`
- Load text domain happens in `after_setup_theme` hook
