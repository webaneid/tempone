# Tempone Theme Translation Files

This directory contains translation files for the Tempone theme.

## Quick Start

### For Users (Using Loco Translate)

1. Install **Loco Translate** plugin from WordPress.org
2. Go to: **Loco Translate → Themes → Tempone**
3. Click "New language" and select your language
4. Start translating!

### For Developers (Using WP-CLI)

Generate POT file:
```bash
wp i18n make-pot /path/to/tempone languages/tempone.pot --domain=tempone
```

## File Structure

```
languages/
├── README.md              # This file
├── tempone.pot            # Translation template (to be generated)
├── tempone-id_ID.po       # Indonesian translation source
├── tempone-id_ID.mo       # Indonesian compiled translation
├── tempone-{locale}.po    # Your language translation source
└── tempone-{locale}.mo    # Your language compiled translation
```

## Generating POT File

### Method 1: WP-CLI (Recommended)

```bash
cd /path/to/tempone-theme
wp i18n make-pot . languages/tempone.pot --domain=tempone
```

### Method 2: Grunt/Gulp

If theme has `Gruntfile.js` or `gulpfile.js`:

```bash
npm install
npm run build  # or grunt/gulp makepot
```

### Method 3: wp-pot-cli (Node.js)

```bash
npm install -g wp-pot-cli
cd /path/to/tempone-theme
wppot --domain=tempone --src=. --dest-file=languages/tempone.pot
```

### Method 4: Poedit

1. Open Poedit
2. File → New from source code
3. Select tempone theme folder
4. Click "Extract"
5. Save as `languages/tempone.pot`

## Creating Translations

### Using Poedit

1. Open Poedit
2. File → New from POT/PO file
3. Select `tempone.pot`
4. Choose your language
5. Save as `tempone-{locale}.po` (e.g., `tempone-id_ID.po`)
6. Translate strings
7. Save (`.mo` file generated automatically)

### Using Loco Translate

1. WordPress Admin → Loco Translate → Themes
2. Select "Tempone"
3. Click "New language"
4. Choose language and location
5. Start translating
6. Click "Save"

## Common Locale Codes

| Language           | Code    | Filename             |
|--------------------|---------|----------------------|
| Indonesian         | id_ID   | tempone-id_ID.po/mo  |
| English (UK)       | en_GB   | tempone-en_GB.po/mo  |
| Spanish (Spain)    | es_ES   | tempone-es_ES.po/mo  |
| French             | fr_FR   | tempone-fr_FR.po/mo  |
| German             | de_DE   | tempone-de_DE.po/mo  |
| Portuguese (BR)    | pt_BR   | tempone-pt_BR.po/mo  |
| Chinese (Simp.)    | zh_CN   | tempone-zh_CN.po/mo  |
| Japanese           | ja      | tempone-ja.po/mo     |
| Arabic             | ar      | tempone-ar.po/mo     |

## Theme Info

- **Text Domain**: `tempone`
- **Domain Path**: `/languages`
- **Default Language**: English (en_US)

## Troubleshooting

### Translation not showing?

1. Check filename: Must be `tempone-{locale}.mo`
2. Check location: Must be in this directory
3. Check WordPress language: Settings → General → Site Language
4. Clear cache and reload

### Need to update translations?

1. Regenerate POT file using methods above
2. Open your `.po` file in Poedit
3. Click "Update from POT file"
4. Select `tempone.pot`
5. Translate new strings
6. Save

## Resources

- Full documentation: See `/TRANSLATION.md` in theme root
- WordPress i18n: https://developer.wordpress.org/apis/handbook/internationalization/
- Poedit: https://poedit.net/
- Loco Translate: https://wordpress.org/plugins/loco-translate/
