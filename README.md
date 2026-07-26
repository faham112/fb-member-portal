# FB Member Portal

Fully custom front-end membership environment for WordPress.

**Version:** 1.6.4  
**Author:** Faham Baloch  
**License:** GPLv2 or later

## Modular Structure (split by function type)

```
fb-member-portal/
├── fb-member-portal.php              # Main bootstrap / loader
├── readme.txt                        # WordPress.org style changelog
├── assets/
│   ├── css/fbmp-admin.css
│   └── js/fbmp-scripts.js
├── includes/
│   ├── class-fbmp-activator.php      # Activation / deactivation
│   ├── class-fbmp-roles.php          # Roles + redirects
│   ├── class-fbmp-db.php             # Orders table
│   ├── class-fbmp-ajax.php           # AJAX handlers
│   ├── class-fbmp-access.php         # Private site + restrict + rescue
│   ├── class-fbmp-referral.php       # Referral tracking
│   ├── class-fbmp-stripe.php         # Stripe Checkout
│   ├── class-fbmp-admin.php          # Admin menu + helpers
│   ├── class-fbmp-presets.php        # Preset registry
│   ├── shortcodes/                   # Front-end shortcode views
│   │   ├── class-fbmp-shortcodes.php
│   │   ├── login.php
│   │   ├── register.php
│   │   └── dashboard.php
│   ├── admin/                        # One file per admin screen
│   │   ├── overview.php
│   │   ├── members.php
│   │   ├── orders.php
│   │   ├── referrals.php
│   │   ├── presets.php
│   │   └── settings.php
│   └── presets/                      # Individual design sections
│       ├── navbar_1.php
│       ├── header_1.php
│       ├── hero_1.php
│       ├── features_1.php
│       ├── testimonials_1.php
│       ├── pricing_1.php
│       ├── faq_1.php
│       ├── about_1.php
│       ├── cta_1.php
│       └── footer_1.php
```

## Installation

1. Upload the folder to `wp-content/plugins/`
2. Activate the plugin
3. Three pages are auto-created: Login, Register, Dashboard
4. Go to **FB Member Portal → Settings** for Stripe keys

## Shortcodes

- `[fbmp_login]`
- `[fbmp_register]`
- `[fbmp_dashboard]`
- `[fbmp_preset key="hero-1"]`
- `[fbmp_restrict role="premium_member"]...[/fbmp_restrict]`
