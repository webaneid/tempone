# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

**Tempone** adalah WordPress theme untuk news/online magazine dengan desain monochrome yang elegan. Theme ini menggunakan Tailwind CSS Play CDN untuk styling dan memiliki sistem loop/archive yang sophisticated dengan featured posts section dan responsive layout.

**Current Version:** 0.1.9 (January 2025)
**WordPress Version:** 6.0+
**PHP Version:** 7.4+
**Author:** Webane Indonesia (https://webane.com)

## Architecture

### Core Structure

Theme ini menggunakan arsitektur modular dengan separation of concerns:

- `functions.php` - Bootstrap utama yang mendefinisikan 3 konstanta: `TEMPONE_PATH`, `TEMPONE_URI`, `TEMPONE_VERSION`, kemudian me-load semua file dari `/inc`
- `/inc` - Berisi semua file fungsi PHP yang dipisah berdasarkan concern:
  - `setup.php` - Theme supports, navigation menus, image sizes, text domain
  - `assets.php` - Enqueue CSS/JS, Tailwind Play CDN loading
  - `template-tags.php` - Helper functions untuk templating
  - `share.php` - Generator URL untuk social media sharing
  - `security.php` - Sanitization/escaping helpers
  - `editor.php` - Gutenberg editor styling & Google Fonts
  - `footer.php` - Footer-related functions
  - `post.php` - Post-related utilities
  - `image.php` - Image processing utilities
  - `seo.php` - SEO & Google News optimization
  - `widget.php` - Custom widgets
  - `acf-layouts.php` - ACF Flexible Content layouts
  - `wordpress.php` - WordPress core customizations
  - `updater.php` - GitHub-based auto-update system
  - `admin.php` - Main admin page (Tempone Setup)
  - `/inc/admin/` - Modular admin customizations (v0.1.6+):
    - `dashboard.php` - Custom dashboard dengan analytics
    - `customizer.php` - Login page glassmorphism design
    - `user.php` - Enhanced user profile dengan Chart.js
    - `design-tokens.php` - ACF color system integration
    - `menu.php` - Admin menu customizations
    - `header.php` - Admin header modifications
    - `navigation.php` - Admin navigation tweaks
    - `content.php` - Admin content area customizations
    - `footer-mobile-menu.php` - Mobile bottom navigation (v0.1.7+)
- `/tp` - Template parts (partials) untuk komponen UI yang reusable
- `/scss` - SCSS files dengan struktur modular (tokens, setup, header, footer, post)
- `/css` - Compiled CSS output
- `/js` - JavaScript files (`main.js` untuk carousel & interactivity)

### Template Hierarchy & Loop System

Theme mengikuti WordPress template hierarchy dengan custom archive system:

**Main Templates:**
- `index.php` - Blog index (homepage jika tidak ada static front page)
- `archive.php` - Universal archive template yang handle category, tag, author, dan date archives
- `category.php` - Require `archive.php` (smart filtering by category)
- `tag.php` - Require `archive.php` (smart filtering by tag)
- `author.php` - Require `archive.php` (smart filtering by author)
- `single.php` - Single post (news article)
- `page.php` - Static pages
- `search.php` - Search results
- `404.php` - Error page

**IMPORTANT: Archive System Logic**

Semua archive pages (`category.php`, `tag.php`, `author.php`) require `archive.php` yang memiliki smart filtering logic untuk featured posts:

```php
// archive.php automatically filters featured posts by context
if ( is_category() ) {
    $featured_args['cat'] = get_queried_object_id();
} elseif ( is_tag() ) {
    $featured_args['tag_id'] = get_queried_object_id();
} elseif ( is_author() ) {
    $featured_args['author'] = get_queried_object_id();
} elseif ( is_date() ) {
    // Filter by year/month/day
}
```

Ini berarti:
- Category page hanya tampilkan featured posts dari category tersebut
- Author page hanya tampilkan featured posts dari author tersebut
- Tag page hanya tampilkan featured posts dengan tag tersebut

### Post Loop Templates (Content Parts)

Theme memiliki 5 variasi content template di `/tp/content-*.php`:

1. **`content-overlay.php`** - Image dengan gradient overlay, text di atas gambar
   - Digunakan untuk: Featured carousel mobile, posts #6 dan #11 di main loop
   - Mobile full-width: Wrapped dengan `<div class="-mx-4">` untuk edge-to-edge
   - Struktur: Title (H3) + Meta (kategori • date) dalam 1 baris
   - Font mobile: 0.875rem title, 0.7rem meta
   - Font desktop: 1.25rem title, 0.75rem meta

2. **`content-classic.php`** - Vertical card (image top, content bottom)
   - Digunakan untuk: Desktop carousel slides (4 posts pertama di featured)
   - Meta dalam 1 baris: kategori • separator • date
   - Font responsive: 1.125rem mobile → 1.5rem desktop

3. **`content-title.php`** - Title only (no thumbnail)
   - Digunakan untuk: 3 posts horizontal di bawah desktop carousel
   - Minimalist, hanya title + time
   - Grid 3 kolom horizontal di desktop

4. **`content.php`** - Default horizontal (thumbnail left, content right)
   - Digunakan untuk: Main posts list di desktop (non-overlay)
   - Wrapper flex dengan thumbnail 12rem x 9rem
   - Meta 1 baris di atas title

5. **`content-image-side.php`** - Compact side layout dengan square thumbnail
   - Digunakan untuk: Main posts list di mobile (non-overlay)
   - Thumbnail 6rem x 6rem (square)
   - Title line-clamp 3 baris
   - Border-bottom separator antar posts

### Featured Posts Section Structure

**Desktop Layout:**
```
┌─────────────────────────────────────────┐
│  Carousel (4 posts) - content-classic   │
│  [< Prev] [Slide 1] [Next >]            │
│  ● ○ ○ ○  (indicators)                  │
└─────────────────────────────────────────┘
┌───────────┬───────────┬───────────┐
│  Title 1  │  Title 2  │  Title 3  │  (content-title)
└───────────┴───────────┴───────────┘
```

**Mobile Layout:**
```
┌──────────────────────────────────┐
│ [Swipe horizontally →]           │
│ ┌─────────┐ ┌─────────┐ ┌─────  │
│ │ Post 1  │ │ Post 2  │ │ Post  │  (7 posts, content-overlay)
│ │ Overlay │ │ Overlay │ │ Over  │
│ └─────────┘ └─────────┘ └─────  │
└──────────────────────────────────┘
```

**Featured Section Rules:**
- Hanya muncul di **page 1** (hidden di pagination page 2+)
- Query: `meta_key: ane_news_utama`, `meta_value: 1` (ACF checkbox)
- Di archive pages: Auto-filtered by category/tag/author/date context
- Mobile: Swipeable horizontal scroll, no arrows, 7 posts as overlay
- Desktop: Carousel 4 posts + 3 title posts, auto-play 5s, dengan controls

### Main Posts Loop Pattern

**Desktop (index.php & archive.php):**
- Posts 1-5: `content.php` (horizontal default)
- Post 6: `content-overlay.php` (full-width overlay)
- Posts 7-10: `content.php`
- Post 11: `content-overlay.php`
- Posts 12-15: `content.php`

**Mobile (index.php & archive.php):**
- Posts 1-5: `content-image-side.php` (compact square)
- Post 6: `content-overlay.php` dengan wrapper `-mx-4` (full-width)
- Posts 7-10: `content-image-side.php`
- Post 11: `content-overlay.php` dengan wrapper `-mx-4`
- Posts 12-15: `content-image-side.php`

```php
// Pattern di semua archive templates
if ( 6 === $post_counter || 11 === $post_counter ) {
    if ( $is_mobile ) {
        echo '<div class="-mx-4 mb-6">';  // Full-width mobile
        get_template_part( 'tp/content', 'overlay' );
        echo '</div>';
    } else {
        get_template_part( 'tp/content', 'overlay' );
    }
} elseif ( $is_mobile ) {
    get_template_part( 'tp/content', 'image-side' );
} else {
    get_template_part( 'tp/content' );
}
```

### Grid Layout System

**Desktop (lg breakpoint 1024px+):**
```
┌────────────────────────────┬──────────┐
│  Main Content (2fr)        │ Sidebar  │
│  Featured section          │  (1fr)   │
│  Posts list                │          │
│  lg:pr-8                   │ lg:pl-8  │
└────────────────────────────┴──────────┘
```

**Mobile:**
```
┌──────────────────────────────────┐
│ Featured carousel (outside grid) │
│ -mx-4 untuk full-width          │
└──────────────────────────────────┘
┌──────────────────────────────────┐
│  Main Content (stacked)          │
│  Posts list                      │
└──────────────────────────────────┘
┌──────────────────────────────────┐
│  Sidebar (stacked below)         │
└──────────────────────────────────┘
```

**Key Classes:**
- Container: `mx-auto px-4 py-8 max-w-7xl`
- Grid: `grid lg:grid-cols-[2fr_1fr] gap-8 lg:divide-x divide-gray-200`
- Main column: `main-content lg:pr-8 min-w-0` (min-w-0 prevents overflow!)
- Mobile featured: `featured-section-mobile` (hidden lg:hidden via CSS)

### Styling System

**Tailwind CSS Play CDN:**
- Loaded as script tag (NOT stylesheet): `wp_enqueue_script('tempone-tailwind', 'https://cdn.tailwindcss.com')`
- No build process, instant utility classes
- Production: Consider local build dengan purge untuk optimasi

**Custom SCSS (_post.scss):**
Semua post layout styles ada di `scss/_post.scss`:
- `.post-overlay` - Overlay dengan gradient, aspect-ratio 16/9, max-height controls
- `.post-classic` - Vertical card dengan hover effects
- `.post-title-only` - Title-only minimalist
- `.post-default` - Horizontal layout dengan flex wrapper
- `.post-image-side` - Square thumbnail compact layout
- `.featured-mobile-carousel` - Scrollbar hidden, snap-scroll behavior

**CSS Variables (`scss/_tokens.scss`):**
- `--tempone-color-primary`: #2d232e
- `--tempone-color-secondary`: #474448
- `--tempone-color-body`: #1e2d2f
- `--tempone-color-light`: #f1f0ea
- `--tempone-color-accent`: #e0ddcf

**Typography:**
- Headings: Poppins
- Body: Inter

### Carousel Implementation

**Desktop Carousel (`js/main.js`):**
- Manual controls: Prev/Next buttons
- Indicators: Dots dengan active state
- Auto-play: 5 seconds interval
- Selector: `.carousel-indicator` (bukan `.indicator`!)
- Transform: `translateX(-${currentSlide * 100}%)`

**Mobile Carousel (CSS-only):**
- Native scroll: `overflow-x-scroll`
- Snap points: `scroll-snap-type: x mandatory`, `snap-start`
- Smooth scrolling: `-webkit-overflow-scrolling: touch`
- Hidden scrollbar: `scrollbar-width: none` + `::-webkit-scrollbar { display: none }`

### Responsive Strategy

**Mobile Detection:**
```php
$is_mobile = wp_is_mobile();  // Server-side detection
```

**Usage Rules:**
- `wp_is_mobile()` untuk **template switching** (content-overlay vs content-image-side)
- Tailwind classes (`lg:`, `md:`) untuk **layout responsiveness**
- CSS media queries untuk **font sizes** dan **spacing adjustments**

**Breakpoints:**
- Mobile: default (< 1024px)
- Desktop: `@media (min-width: 1024px)` atau `lg:` di Tailwind

### Pagination

**Function:** `ane_post_pagination($query)`
- Custom pagination function (defined di `inc/template-tags.php`)
- Konsisten di semua templates: `index.php`, `archive.php`, `search.php`
- Accepts WP_Query object atau global $wp_query

**Usage:**
```php
// index.php - custom query
ane_post_pagination( $main_query );

// archive.php - main query
global $wp_query;
ane_post_pagination( $wp_query );
```

## Development Commands

### SCSS Compilation

SCSS files **WAJIB** di-compile ke CSS setelah perubahan:

```bash
# Main theme styles
npx sass scss/tempone.scss css/tempone.css

# Production (minified) - recommended for production
npx sass scss/tempone.scss css/tempone.css --style=compressed

# Admin styles
npx sass scss/admin.scss css/admin.css
npx sass scss/admin.scss css/admin.css --style=compressed

# Editor styles
npx sass scss/editor-style.scss css/editor-style.css

# Watch mode (auto-compile on save)
npx sass --watch scss/tempone.scss:css/tempone.css
```

**File structure:**
- `scss/tempone.scss` → Main import file
- `scss/_tokens.scss` → CSS variables
- `scss/_post.scss` → All post loop styles
- `scss/_header.scss` → Header styles
- `scss/_footer.scss` → Footer styles
- `scss/admin.scss` → Admin styles import
- `scss/_admin-*.scss` → Admin component styles
- `scss/editor-style.scss` → Gutenberg editor styles

**IMPORTANT:** GitHub Actions automatically compiles all SCSS on release, but for local development you must compile manually.

### Auto-Update System & Release Process

**GitHub-Based Auto-Updater** (`inc/updater.php`):
- Automatically checks for new releases from GitHub every 24 hours
- Compares current version (`TEMPONE_VERSION`) with latest GitHub release
- Shows update notification in WordPress admin
- Users can click "Update now" to install latest version

**Manual Update Check:**
```
wp-admin/themes.php?tempone_force_check=1
```

**Creating a New Release:**

1. **Update version** in `style.css` (line 7):
   ```css
   Version: 0.2.0
   ```

2. **Update CHANGELOG.md** with new version info

3. **Commit changes:**
   ```bash
   git add style.css CHANGELOG.md
   git commit -m "Release v0.2.0 - Description"
   git push
   ```

4. **Create and push tag:**
   ```bash
   git tag -a v0.2.0 -m "Release version 0.2.0"
   git push origin v0.2.0
   ```

5. **GitHub Actions automatically:**
   - Compiles all SCSS to CSS (compressed)
   - Compiles translations (.po → .mo)
   - Creates ZIP package (excludes: .git, .github, node_modules, scss, .gitignore)
   - Creates GitHub Release with CHANGELOG.md content
   - Uploads ZIP as release asset

**Release Workflow File:** `.github/workflows/release.yml`
- Triggers on: `push` to tags matching `v*.*.*`
- Requires: Node.js 18, Sass, gettext
- Output: `tempone-{version}.zip`

**IMPORTANT:**
- Always use semantic versioning: `v{MAJOR}.{MINOR}.{PATCH}`
- Test thoroughly before creating release tag
- GitHub repository must be accessible for updater to work
- Repository settings in `inc/updater.php`: `$github_owner` and `$github_repo`

### Testing Theme

1. MAMP/XAMPP/Local WP running
2. WordPress admin: Appearance > Themes → Activate Tempone
3. Set "Posts page" di Settings > Reading untuk testing index.php
4. Test different archives: category, tag, author
5. Test mobile dengan browser DevTools responsive mode

### Navigation Menus

5 menu locations (`inc/setup.php`):
- `primary` - Primary Menu
- `secondary` - Secondary Menu
- `footer` - Footer Menu
- `media_network` - Jaringan Media Menu
- `footer_bottom` - Footer Bottom Menu

### Image Sizes

Custom sizes:
- `tempone-card` - 640x360 (16:9, hard crop)
- `tempone-card-large` - 960x540 (16:9, hard crop)
- `tempone-news-md` - For overlay backgrounds

## SEO & HTML Tag Rules

**Strict heading hierarchy:**

**Archive Pages (index, category, tag, author):**
- H1: Archive title (`the_archive_title()` atau "Blog")
- H2: Judul post di content-default.php
- H3: Judul post di content-overlay.php, content-classic.php, content-title.php, content-image-side.php

**Single Post:**
- H1: Post title
- H2/H3: Dalam content

**NEVER:**
- Multiple H1 per page
- H1 di footer
- Skip heading levels

## Coding Standards

### WordPress Coding Standards

- PHP 7.4+ type hints
- Docblocks dengan `@package tempone`
- Escaping: `esc_html()`, `esc_attr()`, `esc_url()`, `wp_kses_post()`
- i18n: `__()`, `_e()` dengan text domain `tempone`
- Tabs untuk indentation
- No trailing whitespace

### Template Parts Naming

- `content-*.php` - Post loop variations (overlay, classic, title, default, image-side)
- `section-*.php` - Homepage ACF sections (jika ada)
- `single-*.php` - Single post components
- `header-site.php`, `footer-site.php` - Global layout

### File Organization Rules

1. **NEVER** create new files without user confirmation
2. **ALWAYS** use existing template parts
3. Function additions go to appropriate `/inc/*.php` file
4. New concern = new `/inc` file, require in `functions.php`
5. Template changes = modify existing `/tp/*.php`

## Key Implementation Patterns

### Archive Query Filtering

**Pattern untuk context-aware featured posts:**
```php
if ( is_category() ) {
    $args['cat'] = get_queried_object_id();
} elseif ( is_tag() ) {
    $args['tag_id'] = get_queried_object_id();
} elseif ( is_author() ) {
    $args['author'] = get_queried_object_id();
}
```

### Post Counter Loop

**Pattern untuk varied layout (post 6 & 11 overlay):**
```php
$post_counter = 1;
while ( have_posts() ) : the_post();
    if ( 6 === $post_counter || 11 === $post_counter ) {
        // Overlay
    } else {
        // Regular
    }
    $post_counter++;
endwhile;
```

### Mobile Full-Width Wrapper

**Pattern untuk full-width content di dalam container:**
```php
if ( $is_mobile ) {
    echo '<div class="-mx-4 mb-6">';  // Negative margin cancels parent padding
    get_template_part(...);
    echo '</div>';
}
```

### Featured Section Conditional

**Pattern untuk page 1 only:**
```php
$paged_check = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
$show_featured = ( 1 === $paged_check );

if ( $featured_query->have_posts() && $show_featured ) {
    // Render featured section
}
```

## Common Issues & Solutions

### Issue: Grid content overflow di mobile
**Solution:** Add `min-w-0` ke grid child untuk force proper shrinking

### Issue: Carousel tidak jalan
**Solution:** Check selector `.carousel-indicator` (bukan `.indicator`), pastikan totalSlides > 0

### Issue: Mobile carousel scroll tidak smooth
**Solution:** Pastikan `scroll-snap-type: x mandatory` dan `snap-start` di child elements

### Issue: Overlay text terlalu besar
**Solution:** Font size mobile 0.875rem, desktop 1.25rem, padding 1rem mobile / 1.5rem desktop

### Issue: Featured section muncul di page 2
**Solution:** Check `$show_featured = ( 1 === $paged_check )` conditional

## Important Notes

- Tailwind Play CDN loaded as **script**, not stylesheet
- Theme requires ACF Pro untuk homepage (optional)
- No jQuery - pure vanilla JavaScript
- No border-radius - sharp vertical design aesthetic
- Mobile-first: base styles mobile, desktop via media queries
- Text domain: `tempone`
- All posts pagination via `ane_post_pagination()`
- Featured posts: ACF field `ane_news_utama` (checkbox meta)

## SEO & News Optimization System

### Overview

Theme ini dilengkapi dengan sistem SEO dan News optimization yang comprehensive untuk:
- Google News indexing
- AI crawler optimization (ChatGPT, Claude, Perplexity)
- Social media sharing optimization
- News website best practices

### File Structure

```
inc/
├── seo.php           # SEO & News optimization functions
├── share.php         # Social media share system
├── template-tags.php # Open Graph meta tags (lines 114-208)
└── widget.php        # Tempone Posts Widget
```

### Google News Integration

**NewsArticle Schema** (`inc/seo.php:16-82`)
- Auto-generated JSON-LD schema untuk setiap single post
- Includes: headline, description, image, dates, author, publisher
- Critical untuk Google News eligibility

```php
function tempone_news_article_schema() {
    // Outputs NewsArticle JSON-LD
    // Hooked to wp_head with priority 20
}
```

**Google News Sitemap** (`inc/seo.php:272-332`)
- URL: `/?tempone_news_sitemap=1`
- Only includes posts from **last 2 days** (Google News requirement)
- XML format dengan news namespace
- Submit URL ini ke Google News Publisher Center

```php
// Get sitemap URL
$sitemap_url = tempone_get_news_sitemap_url();
// Returns: https://yoursite.com/?tempone_news_sitemap=1
```

**Breadcrumb Schema** (`inc/seo.php:89-128`)
- Content hierarchy untuk Google News
- Home → Category → Article structure

### AI Crawler Optimization

**Dublin Core Metadata** (`inc/seo.php:135-158`)
- Academic/news citation standard
- Used by ChatGPT, Claude, Perplexity untuk citations
- Fields: DC.title, DC.creator, DC.subject, DC.description, etc.

**Citation Metadata** (`inc/seo.php:165-181`)
- Helps AI models properly cite content as source
- citation_title, citation_author, citation_publication_date, etc.

**Content Freshness Signals** (`inc/seo.php:188-198`)
- Google News prioritizes frequently updated content
- last-modified header
- revisit-after meta tag

**Enhanced Robots Meta** (`inc/seo.php:205-216`)
- Max snippet/preview untuk news
- max-snippet:-1, max-image-preview:large, max-video-preview:-1
- Priority 1 untuk early loading

**Enhanced RSS Feed** (`inc/seo.php:226-264`)
- AI crawlers use RSS to discover content
- Includes featured image, categories, tags
- Full content formatting

### Social Media Share System

**Share Buttons** (`inc/share.php`)

Platform-specific formatting:
- **WhatsApp**: Bold title dengan `*asterisks*`, custom message, uses `api.whatsapp.com/send`
- **Facebook**: Relies on Open Graph meta tags (can't use custom text)
- **X (Twitter)**: Title + excerpt + link
- **Telegram**: HTML formatting dengan `<b>` tags

```php
// Display share buttons in single.php
tempone_share_buttons( $post_id );

// Get platform-specific share text
$text = tempone_get_share_text( $post_id, 'whatsapp' );

// Get share URLs
$links = tempone_get_share_links( $post_id );
```

**Share Button Styling** (`scss/_post.scss:679-764`)
- Mobile: Icon only (padding 0.625rem)
- Desktop: Icon + text (padding 0.625rem 1rem)
- Platform-specific hover colors:
  - Facebook: #1877f2
  - X: #000000
  - WhatsApp: #25d366
  - Telegram: #0088cc

**Open Graph Meta Tags** (`inc/template-tags.php:114-208`)

Complete OG implementation untuk Facebook/social sharing:
- og:title, og:description, og:image, og:url
- og:type: article
- article:published_time, article:modified_time
- article:author, article:section, article:tag
- Twitter Card tags (summary_large_image)

Hooked to `wp_head` in `inc/setup.php:85` dengan priority 5.

### Footer Social Media Integration

**WhatsApp Link dengan Custom Message** (`inc/footer.php:68-81`)

```php
function tempone_get_whatsapp_link( string $number, string $message = '' ) : string {
    // Uses api.whatsapp.com/send for app opening
    // Includes custom message from ACF field: ane_whatsapp_message
}
```

**Social Links Function** (`inc/footer.php:86-140`)
- Retrieves WhatsApp message from ACF: `ane_whatsapp_message`
- Two WhatsApp options:
  - `ane_whatsapp_chanel` - WhatsApp Channel (broadcast)
  - `ane_whatsapp` - WhatsApp Chat (dengan custom message)
- Conditional rendering: Icons only show if field is filled

**Supported Platforms:**
- Facebook
- Threads
- TikTok
- Instagram
- WhatsApp Channel
- WhatsApp Chat (with custom message)
- X (Twitter)

### Widget System

**Tempone Posts Widget** (`inc/widget.php`)

Custom widget untuk sidebar dengan features:
- **Types:** Recent Post, Popular Post, Most Comments, Post Format (Gallery/Video)
- **Filtering:** By category, tag, or both
- **Sorting:** By ID, title, date, rand, comment_count, or view count
- **Template:** Uses `tp/content-image-side.php`
- **Security:** Full sanitization, escaping, proper WordPress coding standards
- **i18n:** Translation-ready dengan text domain `tempone`

**Widget Configuration:**
```php
// Widget ID: tempone-post-widget
// Widget Name: "Tempone: Posts"
// Location: Appearance → Widgets → Main Sidebar
```

**Popular Post Implementation:**
```php
// Uses view count meta key
'meta_key' => 'tempone_views'  // NOT 'post_views_count'
'orderby'  => 'meta_value_num'
```

**View Tracking System** (`inc/post.php:453-671`)
- Meta key: `tempone_views`
- Function: `tempone_set_views( $post_id )`
- AJAX support for cached sites
- Admin column display
- Helper: `tempone_get_views( $post_id )` returns formatted count

### Sidebar System

**Registered Sidebars** (`inc/setup.php:55-78`)
- `sidebar-main` - Main Sidebar (blog, archive, single, search)
- `sidebar-landingpage` - Landing Page Sidebar

**Sidebar Usage:**
- `single.php:205` - Uses `sidebar-main`
- `archive.php:186` - Uses `sidebar-main`
- `index.php:183` - Uses `sidebar-main`
- `search.php:81` - Uses `sidebar-main`
- `tp/section-flexible.php:63` - Uses `sidebar-landingpage`

**Conditional Rendering:**
```php
<?php if ( is_active_sidebar( 'sidebar-main' ) ) : ?>
    <?php dynamic_sidebar( 'sidebar-main' ); ?>
<?php else : ?>
    <!-- Placeholder message -->
<?php endif; ?>
```

### Admin Panel: SEO & News Setup

**Location:** Tempone Setup → SEO & News (`inc/admin.php:465-758`)

**Features:**
- Google News Sitemap URL display
- 6-step Publisher Center submission guide
- SEO Features grid (4 columns)
- AI Crawler Optimization checklist
- Testing tools links (Facebook Debugger, Twitter Validator, Schema Validator, Rich Results Test)
- Additional resources section

**Access:**
- URL: `wp-admin/admin.php?page=tempone-seo-news`
- Capability: `manage_options`
- Hook: `admin_menu` dengan priority 999

### Typography Consistency

**Single Footer Title Standardization** (`scss/_post.scss:746-752`)

All post titles dalam single-footer menggunakan ukuran konsisten:
```scss
.single-footer {
  .post-classic__title,
  .post-title-only__title {
    font-size: 1.125rem !important;
    line-height: 1.75rem !important;
    font-weight: 500 !important;
  }
}
```

Matches landing page typography untuk visual consistency.

### ACF Fields Required

**Social Media Group** (`ane_social_media`):
- `ane_facebook` - Facebook page URL
- `url_threads` - Threads profile URL
- `ane_tiktok` - TikTok profile URL
- `ane_instagram` - Instagram profile URL
- `ane_whatsapp_chanel` - WhatsApp Channel URL (broadcast)
- `ane_x` - X (Twitter) profile URL
- `ane_whatsapp` - WhatsApp number for chat
- `ane_whatsapp_message` - Custom message untuk WhatsApp chat

**Company Info** (`ane_company_name`):
- Used in NewsArticle schema publisher field

### Testing Checklist

**SEO & Schema:**
1. View source single post → Check NewsArticle JSON-LD di `<head>`
2. Test Google Rich Results: https://search.google.com/test/rich-results
3. Validate schema: https://validator.schema.org/
4. Check sitemap: `/?tempone_news_sitemap=1`

**Social Sharing:**
1. Facebook Debugger: https://developers.facebook.com/tools/debug/
2. Twitter Card Validator: https://cards-dev.twitter.com/validator
3. Test WhatsApp share button → Verify message format
4. Test Telegram share → Check HTML formatting

**Widget:**
1. Add "Tempone: Posts" to Main Sidebar
2. Configure type: Popular Post
3. Verify posts ordered by view count
4. Check mobile layout uses `content-image-side.php`

### Important Implementation Notes

**Share System:**
- WhatsApp message limited to 150 characters excerpt
- Facebook cannot use custom text (relies on OG tags)
- WhatsApp uses `api.whatsapp.com/send` for app opening
- Share buttons: Lines 165-168 in `single.php`

**SEO System:**
- Google News sitemap only includes last 2 days
- NewsArticle schema auto-outputs on single posts
- All SEO functions hooked to `wp_head` with priorities 1-21
- RSS feed enhancement auto-active

**Widget System:**
- Must use meta key `tempone_views` (not `post_views_count`)
- Template uses `content-image-side.php` for consistency
- Security: No `extract()`, full sanitization
- Widget appears in all sidebars using `sidebar-main`

## Constants & Configuration

### Theme Constants (defined in `functions.php`)

```php
TEMPONE_PATH    // get_template_directory() - Full filesystem path
TEMPONE_URI     // get_template_directory_uri() - Theme URL
TEMPONE_VERSION // wp_get_theme()->get('Version') - Current version from style.css
```

**Usage:**
```php
// Correct
require_once TEMPONE_PATH . '/inc/setup.php';
wp_enqueue_style( 'tempone-style', TEMPONE_URI . '/css/tempone.css', [], TEMPONE_VERSION );

// Wrong - don't hardcode paths
require_once get_template_directory() . '/inc/setup.php';  // Use TEMPONE_PATH instead
```

### GitHub Repository Configuration (`inc/updater.php`)

```php
private $github_owner = 'webaneid';     // GitHub username/organization
private $github_repo  = 'tempone';      // Repository name
```

**IMPORTANT:** Update these values if you fork/clone the repository to enable auto-updates.

### ACF Field Groups (Options Pages)

**Main Options Page:** `ane_konfigurasi` (Tempone Setup)
- Customization: Color palette (6 colors)
- Company Info: Name, address, phone, email
- Social Media: Facebook, Instagram, X, TikTok, Threads, WhatsApp
- Contact Page: Tagline, description

**Featured Post Meta:** `ane_news_utama` (checkbox on post edit screen)
- Value: `1` = Featured, `0` or empty = Not featured
- Used in archive.php featured query

**User Profile:** `gravatar_ane` (user profile image field)
- Used in admin user profile page
- Fallback to WordPress default avatar if not set

## DO NOT

- ❌ Create `the_posts_pagination()` wrappers - use `ane_post_pagination()` directly
- ❌ Add new functions without user approval
- ❌ Change navigation/pagination structure
- ❌ Use different content templates than specified
- ❌ Add border-radius to post elements
- ❌ Create separate category.php/tag.php logic (use archive.php)
- ❌ Skip SCSS compilation after changes
- ❌ Use `overflow-x-auto` for desktop carousel (breaks flex)
- ❌ Hardcode featured query without context filtering
- ❌ Use `post_views_count` meta key - always use `tempone_views`
- ❌ Change WhatsApp URL format from `api.whatsapp.com/send`
- ❌ Modify Google News sitemap to include more than 2 days
- ❌ Remove Open Graph meta tags - Facebook sharing relies on them
- ❌ Use `extract()` in widget code - security risk
- ❌ Remove mobile menu icon rules from `_admin-menu-icon.scss` - both desktop and mobile need separate selectors
- ❌ Skip `.wp-responsive-open` selector for mobile admin menu - critical for hamburger drawer icons
- ❌ Create new files in root `/inc` for admin functionality - use `/inc/admin/` folder (v0.1.6+ modular structure)
- ❌ Modify `TEMPONE_VERSION` manually - it's auto-loaded from style.css
- ❌ Change GitHub repo settings in `inc/updater.php` without testing - breaks auto-updates

## Translation System

### Overview

Theme is **translation-ready** dengan English sebagai default language. Semua user-facing text menggunakan WordPress i18n functions.

### Configuration

- **Text Domain**: `tempone`
- **Domain Path**: `/languages`
- **Default Language**: English (en_US)
- **Translation Functions**: `__()`, `_e()`, `esc_html__()`, `esc_html_e()`, `esc_attr__()`, `esc_attr_e()`

### File Structure

```
languages/
├── tempone.pot           # Translation template (manually created)
├── tempone-id_ID.po      # Indonesian translation source (sample included)
├── tempone-id_ID.mo      # Indonesian compiled (needs compilation)
├── README.md             # Quick start guide
└── COMPILE.md            # Compilation instructions
```

### Creating Translations

**Method 1: Loco Translate Plugin** (Recommended)
- Install from WordPress.org
- Auto-detects all translatable strings
- Built-in editor and compiler
- No technical knowledge needed

**Method 2: Poedit** (For developers)
```bash
# Open tempone.pot in Poedit
# Create new translation
# Save as tempone-{locale}.po
# .mo file generated automatically
```

**Method 3: WP-CLI**
```bash
# Regenerate POT file
wp i18n make-pot . languages/tempone.pot --domain=tempone

# Compile PO to MO
wp i18n make-mo languages
```

### Compiling Translations

PO files must be compiled to MO for WordPress to use them:

```bash
# Using msgfmt
msgfmt -o languages/tempone-id_ID.mo languages/tempone-id_ID.po

# Using WP-CLI
wp i18n make-mo languages

# Using Poedit
# Just click Save - auto-compiles to .mo
```

### Sample Translation Included

`tempone-id_ID.po` - Complete Indonesian translation:
- 290+ translated strings
- All admin panel text
- All ACF field labels
- All template strings
- All widget text
- All error messages

To use:
1. Compile to `.mo` using one of the methods above
2. Go to: Settings → General → Site Language: Bahasa Indonesia
3. Save and visit site

### Translation Coverage

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

### Documentation

- Full guide: `/TRANSLATION.md`
- Quick start: `/languages/README.md`
- Compilation: `/languages/COMPILE.md`

### Testing Translations

1. Change WordPress language: Settings → General → Site Language
2. Verify frontend (header, footer, sidebar)
3. Check single post page
4. Check archive pages
5. Clear cache and test in incognito

### Important Notes

- All hardcoded Indonesian text has been converted to English with translation functions
- Text domain `tempone` registered in `inc/setup.php`
- **Translation loading**: Uses `load_textdomain()` instead of `load_theme_textdomain()`
  - Bypasses WordPress translation cache for reliable loading
  - Direct .mo file detection via `determine_locale()`
  - Prevents cache interference issues
- WordPress only reads `.mo` files, not `.po` files
- Always edit `.po` files, then recompile to `.mo`

### Translation Loading Implementation

**IMPORTANT:** Theme uses direct `.mo` file loading instead of standard `load_theme_textdomain()` to bypass WordPress translation cache issues.

```php
// inc/setup.php
function tempone_setup() : void {
    /**
     * Load theme translations.
     * Using load_textdomain() instead of load_theme_textdomain() to bypass WordPress
     * translation caching mechanism that can prevent new translations from loading.
     * determine_locale() gets fresh locale without cache interference.
     */
    $locale = determine_locale();
    $mofile = get_template_directory() . "/languages/tempone-{$locale}.mo";

    if ( file_exists( $mofile ) ) {
        load_textdomain( 'tempone', $mofile );
    }
    // ... rest of setup
}
add_action( 'after_setup_theme', 'tempone_setup' );
```

### Why Direct Loading (NOT load_theme_textdomain)?

**Problem with Standard Approach:**
- `load_theme_textdomain()` relies on WordPress translation cache
- Object cache (Redis/Memcached) can cache old translations
- `get_locale()` result can be cached
- When compiling new `.mo` files, WordPress continues using cached version
- Deactivating/reactivating theme doesn't always clear cache

**Solution Benefits:**

**1. `determine_locale()` instead of `get_locale()`**
- Fresh locale detection every time
- Checks filters and options without cache
- More reliable for dynamic language switching

**2. `load_textdomain()` instead of `load_theme_textdomain()`**
- Direct file loading, no search path
- Bypasses WordPress translation cache
- No object cache interference
- Immediate reflection of new translations

**3. `file_exists()` validation**
- Prevents errors if translation file missing
- Graceful fallback to English
- No PHP warnings

**Development Benefits:**
- ✅ New translations load immediately after compilation
- ✅ No need to deactivate/reactivate theme
- ✅ No need to clear cache after each change
- ✅ Faster iteration when developing translations

**Production Benefits:**
- ✅ More reliable translation loading
- ✅ Less affected by caching plugins
- ✅ Works with Redis/Memcached object cache
- ✅ Predictable behavior across all environments

## Admin Customization System

### Overview

Theme memiliki sistem admin customization yang comprehensive meliputi custom dashboard, login page branding, dan editor styling.

### Custom Dashboard (`inc/admin/dashboard.php`)

**Features:**
- Custom dashboard page yang menggantikan default WordPress dashboard
- Real-time analytics dengan Chart.js
- Stats cards untuk visitors, posts, comments, authors
- Interactive charts untuk posts per month
- Recent posts widget dengan "Create New Post" button
- Popular posts ranking
- Author performance tracking
- Content distribution breakdown

**Location:** WP Admin → Dashboard (automatically redirects from default dashboard)

**Key Functions:**
- `tempone_render_dashboard_page()` - Main dashboard renderer
- `tempone_dashboard_stats_cards()` - Stats cards widget
- `tempone_dashboard_posts_chart()` - Posts analytics chart
- `tempone_dashboard_popular_posts()` - Popular posts list
- `tempone_dashboard_recent_posts()` - Recent posts dengan create button
- `tempone_dashboard_top_authors()` - Author leaderboard
- `tempone_get_dashboard_stats()` - Stats data retrieval

**Chart.js Integration:**
```javascript
// Localized data for Chart.js
temponeDashboard.postsData // Posts per month data
temponeDashboard.colors    // Theme color palette
```

**Styling:** `scss/_admin-dashboard.scss` → `css/admin.css`

**Stats Tracked:**
- Total visitors (based on post views)
- Posts this month
- Total comments
- Active authors
- 12-month content trend

### Login Page Customization (`inc/admin/customizer.php`)

**Modern Glassmorphism Design:**
- Dark background (#0a0a0a)
- Gradient blur circles (Webane green theme)
- Glassmorphism card dengan backdrop-filter
- Custom logo dari `https://webane.com/aset/webane-login.svg`
- Responsive design

**Webane Color Palette:**
```scss
--webane-green: #73ab01;        // Primary green
--webane-green-dark: #264113;   // Dark green
--webane-green-darker: #0c1606; // Darkest green
--webane-green-light: #e3eecc;  // Light green
```

**Design Elements:**
- **Background Circles:**
  - Top right: Green gradient blur (400px, opacity 0.6)
  - Bottom left: Dark green gradient blur (350px, opacity 0.7)
  - Filter: `blur(120px)`

- **Login Card:**
  - Background: `rgba(255, 255, 255, 0.08)`
  - Backdrop filter: `blur(20px)`
  - Border: `1px solid rgba(255, 255, 255, 0.1)`
  - Border radius: `20px`
  - Box shadow: `0 8px 32px rgba(0, 0, 0, 0.3)`

- **Input Fields:**
  - Background: `rgba(255, 255, 255, 0.1)`
  - Border: `1px solid rgba(255, 255, 255, 0.2)`
  - Focus: Green border + glow effect
  - Border radius: `10px`

- **Login Button:**
  - Gradient: `#73ab01` → `#264113`
  - Uppercase text dengan letter-spacing
  - Hover: Transform translateY(-2px) + shadow
  - Box shadow: `0 4px 15px rgba(115, 171, 1, 0.3)`

- **Messages/Notices:**
  - Glassmorphism style matching card
  - Success: Green border-left
  - Error: Red border-left dengan tinted background
  - Backdrop filter: `blur(15px)`

**Custom Elements:**
- Title: "Login" (translatable)
- Subtitle: "Welcome Back! Please Login To Your Account"
- Logo: Webane Indonesia SVG
- Signup link: "Don't have an account? Signup"

**Functions:**
- `tempone_login_enqueue_scripts()` - Enqueue inline CSS
- `tempone_login_form_top()` - Add title & subtitle
- `tempone_login_form_bottom()` - Add signup link
- `tempone_login_logo_url()` - Change logo URL to home
- `tempone_login_logo_title()` - Change logo title
- Removes shake effect on error

### WordPress Editor Customization (`inc/editor.php`)

**Typography System untuk Gutenberg:**

**Google Fonts Loading:**
- Poppins (weights: 600, 700, 800) - untuk headings
- Inter (weights: 400, 500, 600) - untuk body text
- Loaded via `add_editor_style()` hook

**Heading Styles (Poppins):**
```scss
H1: 2.5rem, weight 800, line-height 1.2
H2: 2rem, weight 700, line-height 1.3
H3: 1.75rem, weight 700
H4: 1.5rem, weight 600
H5: 1.25rem, weight 600
H6: 1.125rem, weight 600
```

**Body Text (Inter):**
- Font size: **17px** (optimal reading size)
- Line height: 1.75
- Font smoothing: antialiased
- Color: `var(--tempone-color-body)`

**Block Styling:**
- Paragraphs: Inter 17px
- Lists: Inter 17px dengan proper spacing
- Blockquotes: Poppins italic, 1.25rem, dengan border-left
- Code blocks: Monospace dengan dark background
- Tables: Inter 0.95rem dengan styled headers
- Buttons: Poppins 1rem weight 600
- Images: Max-width 100%, auto height
- Figcaptions: Inter 0.875rem italic

**Post Title:**
- Font: Poppins 2.5rem weight 800
- Matching dengan frontend single post title

**Files:**
- `scss/editor-style.scss` - Editor SCSS source
- `css/editor-style.css` - Compiled CSS (7.4KB)
- Auto-enqueued untuk Gutenberg editor

**Responsive:**
- Mobile (< 768px): Reduced font sizes
- H1: 2rem, H2: 1.75rem, H3: 1.5rem
- Body: 16px (dari 17px)

### User Profile Enhancement (`inc/admin/user.php`)

**Modern User Profile Page:**
- Enhanced WordPress user profile page (`wp-admin/profile.php`)
- Shows comprehensive user information and performance analytics
- Reuses Chart.js integration from dashboard
- Custom avatar support via ACF field `gravatar_ane`

**Features:**

**1. Header Card:**
- Large avatar (120px, from ACF `gravatar_ane` field)
- Display name, username, email
- Role badge with color coding
- Registration date
- User bio (description field)

**2. Performance Stats:**
- 5 stats cards: Total Posts, Total Views, Avg Views/Post, Total Comments, Posts This Month
- Each card dengan icon dan hover effect
- Gradient icon backgrounds
- Real-time data from WordPress database

**3. Posts Per Month Chart:**
- Line chart showing last 12 months
- Dual Y-axis: Posts count + Views count
- Interactive tooltips
- Uses same Chart.js setup as dashboard

**4. Recent Activity:**
- **Recent Posts** (5 latest):
  - Post title (clickable to edit)
  - Time ago, views count, comments count
  - Post status badge (publish/draft/pending)
- **Recent Comments** (5 latest):
  - Comment excerpt (20 words)
  - Post link
  - Time ago

**Key Functions:**
- `tempone_user_profile_header()` - Main header card renderer
- `tempone_user_profile_performance()` - Stats cards & chart
- `tempone_user_profile_recent_activity()` - Posts & comments lists
- `tempone_get_user_stats()` - Stats calculation
- `tempone_get_user_posts_data()` - Chart data for specific user
- `tempone_user_profile_enqueue_scripts()` - Load Chart.js & custom JS

**Hooks:**
- `show_user_profile` - Own profile page
- `edit_user_profile` - Editing other user profile
- `admin_enqueue_scripts` - Conditional script loading (profile/user-edit screens only)

**JavaScript:**
- File: `js/admin-user.js`
- Creates dual-axis line chart
- Localized data via `temponeUserProfile` object
- Auto-responsive canvas

**Styling:** `scss/_admin-user.scss` → `css/admin.css`

**Database Queries:**
- Uses `tempone_views` meta key (consistent dengan widget)
- Filters by user ID for all stats
- Optimized queries dengan proper indexes
- 12-month date range untuk chart

**ACF Integration:**
- Field name: `gravatar_ane`
- Fallback to WordPress default avatar if not set
- Uses existing `get_avatar()` filter hook

**Translation Coverage:**
- All UI strings translatable
- Stats labels: "Total Posts", "Total Views", etc.
- Activity labels: "Recent Posts", "Recent Comments"
- Time labels: "ago", "views", "comments"
- Empty states: "No posts yet", "No comments yet"

### Mobile Footer Navigation (`inc/admin/footer-mobile-menu.php`)

**Bottom Navigation Bar untuk Mobile Admin:**
- Fixed bottom navigation bar yang hanya muncul di mobile (≤782px)
- 5 menu items dengan SVG icons untuk quick access
- Active state detection berdasarkan current screen
- Center "Create Post" button dengan primary color highlight
- Safe area inset support untuk notched devices (iPhone X+)

**Menu Items:**
1. **Dashboard** → `admin.php?page=tempone-dashboard`
   - Icon: Tempone Dashboard icon (speedometer with gauge) - synced from `_admin-menu-icon.scss`
   - Stroke-based SVG dengan viewBox 24x24
   - Active: Dashboard page atau Tempone Dashboard
2. **Pages** → `edit.php?post_type=page`
   - Icon: Paper plane/send icon - minimalist design
   - Fill-based SVG dengan viewBox 16x16
   - Active: Pages list atau edit page
3. **Create Post** (CENTER) → `post-new.php`
   - Icon: Plus symbol dalam circular background
   - Styling: Primary color (#2d232e), larger (28px vs 24px)
   - Active: Post creation page
4. **Settings** → `admin.php?page=tempone-setup`
   - Icon: Tempone Setup icon (dashboard with settings) - synced from `_admin-menu-icon.scss`
   - Stroke-based SVG dengan browser window + settings elements
   - Active: Any tempone-* admin page
5. **Plugins** → `plugins.php`
   - Icon: Plug icon dengan circular design
   - Stroke-based SVG dengan viewBox 24x24
   - Active: Plugins page

**Icon Consistency:**
- All icons (except Create Post) synced dengan admin menu icons dari `_admin-menu-icon.scss`
- Dashboard, Settings, Plugins menggunakan same SVG paths as desktop/mobile admin menu
- Ensures visual consistency across all admin navigation elements
- Icons use `currentColor` untuk automatic color adaptation based on active state

**Design Specifications:**
- **Layout:** Fixed bottom, flexbox dengan space-around
- **Background:** `var(--tempone-color-light)` dengan subtle border-top
- **Icons:** 24px standard, 28px untuk center button
- **Text:** 10px (small but readable), weight 500, letter-spacing 0.01em
- **Icon Opacity:** 0.7 default, 1.0 when active
- **Z-index:** 99999 (above admin bar)
- **Animation:** Slide-up on load (0.3s ease)
- **Content Padding:** Auto-adds 70px bottom padding ke #wpwrap

**Center Button Special Styling:**
- Circular background: `border-radius: 50%`
- Background color: `var(--tempone-color-primary)`
- Icon color: `var(--tempone-color-light)`
- Box shadow: `0 2px 8px rgba(45, 35, 46, 0.2)`
- Hover: Scale 1.05 + enhanced shadow
- Padding: 6px

**Active State Detection Logic:**
```php
$current_screen = get_current_screen();

if ( 'dashboard' === $current_screen->id || 'tempone-dashboard' === $current_screen->id ) {
    $current_page = 'dashboard';
} elseif ( 'edit-page' === $current_screen->id || 'page' === $current_screen->id ) {
    $current_page = 'pages';
} elseif ( 'post-new' === $current_screen->id ) {
    $current_page = 'create';
} elseif ( strpos( $current_screen->id, 'tempone' ) !== false ) {
    $current_page = 'settings';
} elseif ( 'plugins' === $current_screen->id ) {
    $current_page = 'plugins';
}
```

**Safe Area Inset:**
```scss
padding-bottom: env(safe-area-inset-bottom, 0);
```
Automatically adjusts untuk iPhone dengan notch/Dynamic Island.

**Function:** `tempone_admin_footer_mobile_menu()`
- Hook: `admin_footer`
- Conditional: Only renders on admin pages (`is_admin()`)
- Returns early if not admin

**Styling:** `scss/_admin-footer-mobile-menu.scss` → `css/admin.css`

**Files:**
- PHP: `inc/admin/footer-mobile-menu.php` (98 lines)
- SCSS: `scss/_admin-footer-mobile-menu.scss` (127 lines)
- Registered in: `functions.php` line 46

### Footer Branding

**"Designed by Webane" Credit:**
- Location: Site footer after copyright
- Function: `tempone_load_designed_by()`
- Format: "Designed with love by Webane Indonesia"
- Link: https://webane.com
- Styling: Separator pipe, hover effect

**Admin Footer:**
- Function: `tempone_remove_footer_admin()`
- Text: "Designed with love by Webane Indonesia. Powered by WordPress."
- Replaces default "Thank you for creating with WordPress"

### Admin Menu Icon System

**Custom SVG Icons for Admin Menu:**
- File: `scss/_admin-menu-icon.scss` (182 lines)
- Replaces default WordPress Dashicons dengan custom SVG icons
- Uses data URI format untuk inline SVG (no external files)
- Icons untuk: Dashboard, Posts, Media, Pages, Comments, Appearance, Plugins, Users, Tools, Settings, Tempone Setup

**Desktop Implementation:**
```scss
#adminmenu .wp-menu-image:before {
    content: '' !important;
    background-image: url("data:image/svg+xml,...") !important;
    background-size: contain !important;
    width: 24px !important;
    height: 24px !important;
}
```

**Mobile Implementation (≤782px):**
```scss
@media screen and (max-width: 782px) {
    /* Force custom icons in mobile hamburger drawer */
    .wp-responsive-open #adminmenu .wp-menu-image:before {
        content: '' !important;
        background-size: contain !important;
        background-position: center !important;
        width: 24px !important;
        height: 24px !important;
        display: block !important;
    }

    /* Hide WordPress Dashicons font */
    .wp-responsive-open #adminmenu .wp-menu-image {
        font-size: 0 !important;
    }
}
```

**Icon States:**
- Default: `rgba(255,255,255,0.7)` opacity
- Hover/Active/Current: `%23ffffff` (white, URL-encoded `#ffffff`)
- Active classes: `.current`, `.wp-has-current-submenu`, `.opensub`

**Key Details:**
- WordPress mobile drawer uses `.wp-responsive-open` class when hamburger menu is opened
- Custom icons MUST override Dashicons font dengan `font-size: 0 !important`
- SVG colors in data URI must be URL-encoded (`#` becomes `%23`)
- Mobile drawer has same HTML structure as desktop but needs explicit forcing with higher specificity

### SCSS Structure for Admin

```
scss/
├── admin.scss                      # Main admin import
├── _admin-style.scss               # General admin styling
├── _admin-menu-icon.scss           # Custom SVG icons (desktop + mobile)
├── _admin-dashboard.scss           # Dashboard components
├── _admin-user.scss                # User profile enhancement
├── _admin-footer-mobile-menu.scss  # Mobile footer navigation
└── editor-style.scss               # Editor standalone (with tokens)
```

Compile commands:
```bash
# Admin styles
npx sass scss/admin.scss css/admin.css

# Minified admin styles
npx sass scss/admin.scss css/admin.min.css --style=compressed

# Editor styles
npx sass scss/editor-style.scss css/editor-style.css
```

### Translation Coverage

**Admin strings yang translatable:**
- Login page: "Login", "Welcome Back! Please Login To Your Account"
- Dashboard: "Dashboard", "Content Analytics", "Popular Posts", "Recent Posts"
- Actions: "Create New Post", "Signup"
- Stats: "Total Visitors", "Total Articles", "Active Authors"
- Footer: "Designed with love by %s"

**Indonesian translations included:**
- "Login" → "Masuk"
- "Welcome Back! Please Login To Your Account" → "Selamat Datang Kembali! Silakan Masuk Ke Akun Anda"
- "Don't have an account?" → "Belum punya akun?"
- "Signup" → "Daftar"
- "Designed with love by %s" → "Dirancang sepenuh hati oleh %s"
- "Webane Indonesia" → "Webane"

### Testing Admin Customization

**Login Page:**
1. Logout dari WordPress
2. Visit `/wp-login.php`
3. Verify glassmorphism card dengan Webane logo
4. Check green gradient circles background
5. Test responsive di mobile

**Dashboard:**
1. Login ke WP Admin
2. Automatically redirected ke custom dashboard
3. Verify stats cards dengan real data
4. Check Chart.js rendering
5. Test "Create New Post" button functionality

**User Profile:**
1. Go to Users → Profile (or edit any user)
2. Verify header card dengan avatar dari ACF `gravatar_ane`
3. Check 5 stats cards dengan real data
4. Verify posts per month chart (12 months, dual Y-axis)
5. Check recent posts list (5 items dengan views/comments)
6. Check recent comments list (5 items dengan post links)
7. Test responsive di mobile (grid stacking)
8. Verify role badge color coding

**Editor:**
1. Create/Edit post
2. Verify Poppins font untuk headings
3. Verify Inter 17px untuk paragraphs
4. Test different heading levels (H1-H6)
5. Check blockquotes, lists, code blocks

**Mobile Footer Navigation:**
1. Resize browser ke mobile width (≤782px) atau gunakan DevTools
2. Verify footer menu muncul di bottom
3. Check 5 menu items dengan icons
4. Verify center "Create Post" button dengan circular background
5. Test active state (click different menu items)
6. Check safe area padding di iPhone dengan notch
7. Verify slide-up animation on page load
8. Test content tidak tertutup footer (70px padding)
9. Verify desktop tidak menampilkan footer menu

**Mobile Admin Menu Icons:**
1. Resize browser ke mobile width (≤782px) atau gunakan DevTools
2. Click hamburger menu icon (top left)
3. Verify mobile drawer slides in from left
4. Check custom SVG icons display for all menu items (not default Dashicons)
5. Icons should be: Dashboard (speedometer), Posts (document), Media (image), Pages (page), Comments (chat), Appearance (palette), Plugins (puzzle), Users (people), Tools (wrench), Settings (gear), Tempone Setup (dashboard with settings)
6. Test icon opacity: default 0.7, active 1.0
7. Verify icons remain visible when scrolling drawer
8. Test desktop admin menu also shows same custom icons
9. Close drawer and verify icons reset properly

### Important Notes

**Admin Customization:**
- All inline CSS untuk login page (no separate file)
- Chart.js loaded from CDN (4.4.0)
- Dashboard uses WordPress postbox structure
- Editor styles auto-loaded untuk Gutenberg
- Admin menu icons: SVG data URIs in SCSS (no external image files)
- Mobile menu icons require `.wp-responsive-open` selector for hamburger drawer

**Color Consistency:**
- Login: Webane green palette (#73ab01 family)
- Dashboard: Tempone monochrome palette
- Editor: Matches frontend color variables

**Performance:**
- Login CSS: Inline (no HTTP request)
- Dashboard: Single admin.css file
- Editor: Separate editor-style.css (only loads in editor)
- Chart.js: CDN with version pinning

**DO NOT:**
- ❌ Remove glassmorphism effects from login
- ❌ Change Webane logo URL
- ❌ Modify editor font sizes without updating frontend
- ❌ Skip SCSS compilation after admin changes
- ❌ Change Chart.js version without testing
- ❌ Remove translation functions from admin strings
- ❌ Modify mobile footer menu z-index (must be 99999 to stay above admin bar)
- ❌ Remove safe area inset support from mobile footer (breaks on notched devices)
- ❌ Change center button circular design (it's a key visual element)
- ❌ Remove mobile icon selectors from `_admin-menu-icon.scss` - hamburger menu won't show custom icons
- ❌ Change icon SVG colors without URL encoding (`#` must be `%23`)
