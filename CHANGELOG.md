# Changelog

All notable changes to Tempone theme will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Fixed
- **Admin animations** - Removed page transition animations that caused unnecessary page refreshes
  - Disabled `initPageTransitions()` - was intercepting all admin links and forcing refresh with fade animation
  - Removed unused animation functions for better performance
  - Kept minimal animations: page fade-in and progress bars only
  - File size reduced from 220 to 75 lines in `js/admin-animations.js`

## [0.1.9] - 2025-01-27

### Fixed
- **Color system** - White color added to Tempone color palette for better consistency
- **User profile avatar** - Gravatar now uses square image size instead of circular for modern design
- **Mobile footer menu icons** - Footer navigation icons now consistent with Tempone custom admin icons
  - Dashboard: Speedometer icon matching admin menu
  - Pages: Paper plane icon
  - Create: Plus symbol (unchanged)
  - Posts: Edit/pencil icon matching admin menu
  - Settings: Browser with settings icon matching Tempone Setup
- **Translations** - Footer menu labels now match WordPress standards
  - Pages: "Laman" (not "Halaman")
  - Posts: "Pos" (not "Tulisan")
- **Mobile admin header logo** - Removed hardcoded "tempone" text, now uses dynamic logo
  - Uses WordPress custom logo if set in Customizer
  - Falls back to `/img/logo-tempone.svg` if no custom logo
  - Function: `tempone_admin_bar_logo()` in `inc/admin.php`
- **Mobile dashboard** - Enhanced mobile dashboard layout for better dynamics and modern appearance
  - Improved responsive grid stacking
  - Better card spacing and typography
- **Mobile single post** - Cleaner mobile layout for single post pages
  - Enhanced readability
  - Optimized spacing and typography
- **Page template** - Added responsive design improvements for static pages
  - Better mobile layout handling
  - Consistent spacing with other templates

### Changed
- Mobile footer menu structure: Removed "Plugins", added "Posts" for better content workflow
  - New order: Dashboard, Pages, Create, Posts, Settings
  - Posts menu more relevant for news/magazine sites
- Icon opacity system: SVG elements now properly inherit parent opacity for consistent appearance

### Technical
- Footer menu file: `inc/admin/footer-mobile-menu.php`
- Admin menu icons: `scss/_admin-menu-icon.scss` (mobile hamburger drawer support)
- Admin bar logo: `scss/_admin-header.scss` line 455-469
- Translation file: `languages/tempone-id_ID.po` and `.mo` compiled
- All changes tested on mobile (≤782px) and desktop viewports

## [0.1.8] - 2025-01-27

### Fixed
- **Mobile admin menu icons** - Custom SVG icons now display correctly in hamburger menu drawer
  - Added mobile-specific CSS rules with `.wp-responsive-open` selector
  - Forces custom icons to override default WordPress Dashicons
  - Maintains consistent icon design between desktop and mobile admin
- Code synchronization and stability improvements
- Ensured all admin components properly registered
- Verified SCSS compilation pipeline

### Technical
- Stable release following v0.1.7
- All features from v0.1.7 tested and verified
- Ready for auto-update testing
- Mobile menu icon system: `scss/_admin-menu-icon.scss` lines 156-182

## [0.1.7] - 2025-01-26

### Added
- **Mobile Footer Navigation Bar** - Bottom navigation for mobile admin (≤782px)
  - 5 menu items: Dashboard, Pages, Create Post (center), Settings, Plugins
  - Active state detection based on current screen
  - Center "Create Post" button with primary color and circular background
  - Fixed bottom positioning with safe area inset support for notched devices
  - Slide-up animation on load
  - SVG icons: 24px standard, 28px for center button
  - Small readable text (10px) with dominant icons
  - Auto-adds 70px bottom padding to prevent content overlap
  - File: `inc/admin/footer-mobile-menu.php`
  - Styles: `scss/_admin-footer-mobile-menu.scss`

### Changed
- Enhanced mobile admin experience with bottom navigation
- Improved mobile usability with quick access to common actions
- SCSS structure updated with footer mobile menu import

### Technical
- Function: `tempone_admin_footer_mobile_menu()` hooked to `admin_footer`
- Active detection: Dashboard, Pages, Create Post, Settings (tempone-setup), Plugins
- Mobile-only display with `@media (max-width: 782px)`
- Responsive design with flexbox layout
- Safe area support: `padding-bottom: env(safe-area-inset-bottom, 0)`

## [0.1.6] - 2025-01-25

### Added
- Enhanced user profile page (wp-admin/profile.php) with comprehensive analytics
  - User header card with avatar from ACF field `gravatar_ane`
  - Performance stats: Total Posts, Total Views, Avg Views/Post, Total Comments, Posts This Month
  - Dual-axis Chart.js chart: Posts vs Views over 12 months (filtered by user)
  - Recent posts section with views, comments, and status badges
  - Recent comments section with post links
  - Role badge display (Administrator, Editor, Author, Contributor, Subscriber)
  - User registration date and bio display
- Complete Indonesian translation for admin panel (288 total strings)
  - inc/admin/user.php: 19 strings translated
  - inc/admin/dashboard.php: 20 strings translated
  - inc/admin.php: 67 strings translated (Tempone Setup pages)
  - SEO & News, Google News submission guide, testing tools
- Admin system documentation in inc/admin/README.md
  - Complete file structure guide for AI assistants
  - SCSS compilation instructions
  - ACF color system integration details
  - Chart.js implementation patterns
  - Integration guide for new admin pages

### Changed
- Reorganized admin files into inc/admin/ folder structure
  - dashboard.php: Custom dashboard (replaces wp-admin/index.php)
  - user.php: Enhanced user profile page
  - customizer.php: Glassmorphism login page
  - menu.php, header.php, navigation.php: Admin UI components
- Improved translation system reliability
  - Use `load_textdomain()` instead of `load_theme_textdomain()`
  - Direct .mo file loading with `determine_locale()`
  - Bypasses WordPress translation cache for immediate loading
- Updated documentation structure
  - Consolidated contact form setup into README.md
  - Merged deployment guide into README.md
  - Merged translation guide into README.md
  - Extracted SOLUTION.md content to CLAUDE.md
  - Single admin documentation in inc/admin/README.md

### Fixed
- Translation file duplicates removed using msguniq
- File organization cleanup (removed 18 unused files)
  - Deleted unused template parts: section-custom_html.php, section-hero_main.php, section-hero_top_banner.php, section-photo_feature.php
  - Removed backup files and redundant documentation
  - Cleaned up languages folder (kept only .pot, .po, .mo files)

### Developer
- Chart.js integration pattern documented for future admin pages
- ACF color system fully documented with CSS variable usage
- SCSS file structure standardized with proper import order
- Translation workflow documented (Loco Translate, Poedit, msgfmt)

## [0.1.5] - 2025-01-24

### Fixed
- Remove zipball fallback in updater (prevents wrong folder names like webaneid-tempone-ba50689)
- Add clickable "Update now" link to update notification
- Add "Update Available" action link to theme card (visible on hover)
- Fix theme update button not appearing in WordPress admin

### Performance
- Use minified CSS (tempone.min.css) for 10KB size reduction
- Move Tailwind CDN loading to footer (eliminate render-blocking, save ~250ms)
- Add preconnect hints for external resources (save ~200-400ms DNS/connection time)
- Add font-display swap to Google Fonts (prevent invisible text during font loading)

## [0.1.1] - 2025-01-24

### Fixed
- Test auto-update system
- Minor bug fixes

## [0.1.0] - 2025-01-23

### Added
- Initial release of Tempone theme
- Custom dashboard with analytics and Chart.js integration
- Glassmorphism login page with Webane branding
- WordPress Gutenberg editor customization (Poppins + Inter fonts)
- Featured posts carousel (desktop manual + mobile swipe)
- 5 content template variations (overlay, classic, title, default, image-side)
- Google News optimization (NewsArticle schema, sitemap, Dublin Core)
- Social media share system (WhatsApp, Facebook, X, Telegram)
- SEO & AI crawler optimization
- Custom widgets (Tempone Posts Widget)
- View tracking system with AJAX support
- Full Indonesian translation (tempone-id_ID)
- ACF Flexible Content layouts
- Responsive design (mobile-first)
- Tailwind CSS Play CDN integration

### Theme Features
- Custom dashboard replacing default WordPress dashboard
- Real-time analytics (visitors, posts, comments, authors)
- Posts per month chart with Chart.js
- Popular posts ranking
- Recent posts with "Create New Post" button
- Author performance tracking
- Content distribution breakdown

### Admin Customization
- Modern glassmorphism login page
- Webane green color palette
- Custom logo from webane.com
- Gradient blur background circles
- Responsive login design
- Custom footer branding

### Typography
- Headings: Poppins (weights 600, 700, 800)
- Body: Inter (weights 400, 500, 600)
- Editor: 17px body text for optimal reading
- Matching frontend and editor styling

### SEO & News
- Google News sitemap (last 2 days)
- NewsArticle JSON-LD schema
- Breadcrumb schema
- Dublin Core metadata for AI crawlers
- Citation metadata
- Open Graph tags for social sharing
- Enhanced RSS feed

### Performance
- Tailwind CSS Play CDN (script tag, not stylesheet)
- SCSS compilation to optimized CSS
- No jQuery - pure vanilla JavaScript
- Lazy loading support
- Mobile-first responsive design

### Security
- Full escaping and sanitization
- No `extract()` usage
- Proper nonce verification
- WordPress coding standards compliant

### Browser Support
- Modern browsers (Chrome, Firefox, Safari, Edge)
- Mobile Safari
- Chrome Mobile
- Responsive breakpoints: 768px, 1024px

### Developer Features
- Modular architecture with `/inc` separation
- Comprehensive CLAUDE.md documentation
- Translation-ready (text domain: tempone)
- ACF Pro support (optional)
- Custom template tags
- WordPress template hierarchy

---

## Release Notes

### Version 0.1.0 - "Foundation Release"

This is the initial release of Tempone, a sophisticated WordPress theme for news and magazine websites. Built with modern web technologies and following WordPress best practices.

**Highlights:**
- 🎨 Modern glassmorphism design
- 📊 Advanced analytics dashboard
- 🚀 Performance optimized
- 🌐 Fully translatable
- 📱 Mobile-first responsive
- 🔍 SEO & Google News ready
- ✍️ Custom Gutenberg editor styling

**What's Next:**
- v0.2.0: Theme customizer options
- v0.3.0: WooCommerce integration
- v0.4.0: Additional ACF layouts
- v1.0.0: WordPress.org submission (optional)

---

[Unreleased]: https://github.com/webaneid/tempone/compare/v0.1.5...HEAD
[0.1.5]: https://github.com/webaneid/tempone/compare/v0.1.1...v0.1.5
[0.1.1]: https://github.com/webaneid/tempone/compare/v0.1.0...v0.1.1
[0.1.0]: https://github.com/webaneid/tempone/releases/tag/v0.1.0
