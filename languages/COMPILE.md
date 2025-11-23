# Compiling Translation Files

The `.po` file (Portable Object) needs to be compiled into `.mo` file (Machine Object) to be used by WordPress.

## Method 1: Using Loco Translate (Easiest)

1. Install **Loco Translate** plugin
2. The plugin will automatically compile `.po` to `.mo` when you save
3. No manual compilation needed!

## Method 2: Using Poedit (Desktop App)

1. Download and install Poedit: https://poedit.net/
2. Open the `.po` file in Poedit
3. Click "Save" or "Compile to MO"
4. Poedit will automatically create the `.mo` file

## Method 3: Using msgfmt Command (Terminal)

### macOS / Linux:

```bash
# Install gettext if not installed
brew install gettext  # macOS
sudo apt-get install gettext  # Ubuntu/Debian

# Compile PO to MO
msgfmt -o languages/tempone-id_ID.mo languages/tempone-id_ID.po
```

### Windows:

1. Download gettext for Windows: https://mlocati.github.io/articles/gettext-iconv-windows.html
2. Open Command Prompt in theme directory
3. Run:
```cmd
msgfmt -o languages\tempone-id_ID.mo languages\tempone-id_ID.po
```

## Method 4: Using WP-CLI

```bash
wp i18n make-mo languages
```

This will compile all `.po` files in the languages directory.

## Verify Compilation

After compilation, you should have:

```
languages/
├── tempone.pot            # Template
├── tempone-id_ID.po       # Indonesian translation source
└── tempone-id_ID.mo       # Indonesian compiled (REQUIRED for WordPress)
```

## Testing

1. Go to: Settings → General → Site Language
2. Select: **Bahasa Indonesia**
3. Save Changes
4. Visit your site - all strings should now be in Indonesian!

## Notes

- WordPress only reads `.mo` files, not `.po` files
- Always edit `.po` files, then recompile to `.mo`
- `.mo` files are binary and cannot be edited directly
- Keep `.po` files for future edits
