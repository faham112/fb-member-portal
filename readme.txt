=== FB Member Portal ===
Contributors: fahambaloch
Requires at least: 5.8
Tested up to: 6.6
Stable tag: 1.6.4
License: GPLv2 or later

Code by Faham Baloch

Fully custom front-end user environment for WordPress: registration, login,
Free/Premium roles, member dashboard (Profile, Orders/Payments, Content,
Referrals), a real Stripe subscription upgrade, and referral link tracking.

== What's new in 1.1.0 ==
- Fixed a bug where an administrator account could get stuck being
  treated like a Free/Premium member (redirected out of wp-admin) if a
  member role was ever accidentally attached to their account. Admin
  capability now always takes priority everywhere in the plugin, and
  the plugin auto-repairs this on the affected account's next admin
  page load.
- Real Stripe Checkout subscription for the Premium tier (recurring
  billing via Stripe — no investment returns, no promised payouts).

== What's new in 1.1.1 ==
- Fixed ERR_TOO_MANY_REDIRECTS: when WordPress itself needs a fresh
  login (wp-login.php?reauth=1, usually from an expired/invalid auth
  cookie), the plugin no longer intercepts that and bounces you back
  to wp-admin — it lets WordPress's own re-login flow complete first.
- Referral link tracking: members get a personal referral link and can
  see who signed up through it. This is reporting only — the plugin
  does NOT calculate or auto-pay commissions. Any payout is a manual
  decision you make and handle outside the plugin.
- New admin "Referrals" report page.
- wp-admin stays the normal, generic WordPress Dashboard. The plugin's
  own admin screens live under the "FB Member Portal" sidebar menu.

== Installation ==
1. Upload the `fb-member-portal` folder to `wp-content/plugins/`.
2. Activate from Plugins > Installed Plugins.
3. Three pages are auto-created: Login, Register, Dashboard.
4. In FB Member Portal > Settings, add your Stripe Secret Key, Price ID,
   and Webhook Signing Secret if you want the Premium upgrade button to work.

== Shortcodes ==
[fbmp_login]       - Login form
[fbmp_register]    - Registration form (Free/Premium selector, ?ref= aware)
[fbmp_dashboard]   - Member dashboard (Profile / Orders / Content / Referrals)

== How roles work ==
Two custom roles: `free_member` and `premium_member`. Members never see
wp-admin — they're redirected to the front-end Dashboard page. Admins and
editors use wp-admin normally; the plugin adds one extra sidebar menu.

== Stripe subscription ==
1. In Stripe Dashboard, create a recurring Price for your Premium plan.
2. Paste the Secret Key and Price ID into FB Member Portal > Settings.
3. Add a webhook endpoint in Stripe pointing to:
     https://yoursite.com/?fbmp_stripe_webhook=1
   listening for `checkout.session.completed`. Paste the signing secret
   into Settings too.
4. Free members will see an "Upgrade to Premium" button on their
   dashboard, which opens Stripe's hosted Checkout page. On successful
   payment, their role is automatically upgraded to premium_member and
   the payment is logged in Orders & Payments.

== Referral tracking (reporting only) ==
Each member has a referral link (their register page + ?ref=username).
New sign-ups through that link are recorded against the referrer, visible
on their dashboard and in the admin Referrals report. This plugin does
NOT move any money or calculate commissions automatically — it is a
lightweight "who brought whom" log, nothing more.

== Orders / Payments table ==
`{prefix}fbmp_orders` (id, user_id, item, amount, currency, status,
created_at) — populated by the Stripe webhook on real payments, and
manually if you add an order from the admin Orders page.

== Premium-gated content ==
Posts in the category set under Settings > "Premium content category
slug" are locked for Free members on the Content tab.

== Security notes ==
- All front-end AJAX requests are nonce-protected (`fbmp_nonce`).
- Stripe webhook signatures are verified with HMAC-SHA256 before any
  role change happens.
- Passwords are handled entirely via core WP functions.

== Credits ==
Built and coded by Faham Baloch.

== What's new in 1.1.2 ==
- Login now explicitly re-sets the auth cookie right after sign-in
  (belt-and-suspenders) to help on hosts where cookies don't stick
  reliably on the very first request.
- NOTE: if you still get bounced back to the login page after landing
  on /wp-admin, this is almost always a cookie/domain issue on the
  hosting side (common on free hosts that mask/proxy your real site
  URL under another domain), not something a plugin can fully fix.
  Check Settings > General: "WordPress Address (URL)" and "Site
  Address (URL)" must both be the exact same https URL you actually
  browse to.

== What's new in 1.2.0 ==
- Admin Rescue (Magic Link): a "Lost Administrator access?" link on the
  Login page lets you request a secure, time-limited (15 min), one-time
  email link that restores your Administrator role — no database access
  needed if you ever get locked out again.
- Content Restriction shortcode: [fbmp_restrict role="premium_member"]
  wrap any content in a post/page to lock it by role (free_member,
  premium_member, or any). Admins always see everything.
- Private Site mode: optional Settings toggle that requires login to
  view any page on the site (Login/Register stay open so people can
  still sign in).

== What's new in 1.3.0 ==
- Configurable "After Registration" redirect: send new users to the
  Dashboard (default) or any custom URL (e.g. a "check your email"
  page) — set in FB Member Portal > Settings.
- Configurable welcome email sent to new users, with placeholders
  ({name}, {username}, {email}, {login_url}, {site_name}).
- Optional admin notification email on every new registration.

== What's new in 1.3.1 ==
- Fixed "Security check failed" login/register errors caused by cached
  pages (browser cache, or CDN/edge caching on hosts like InfinityFree)
  serving a stale nonce. The front-end now fetches a brand-new nonce
  via admin-ajax.php (never cached) right before every login,
  register, profile update, rescue request, or checkout submission.
- Login/Register pages now send no-cache headers to further discourage
  proxies from caching them.

== What's new in 1.4.0 ==
- New "Design Presets" library (FB Member Portal > Design Presets):
  ready-made sections you can preview and drop into any page via a
  shortcode — Navbar, Header, Hero, Pricing (Free vs Premium), Footer.
  Copy the shortcode shown under each preview and paste it into any
  Page/Post (e.g. [fbmp_preset key="hero-1"]).

== What's new in 1.5.0 ==
- Design Presets is now fully automated: pick a page from a dropdown,
  choose top/bottom, and click Insert — the section is added to that
  page's content automatically (no manual copy/paste needed).
- Category filter added to Design Presets.
- 5 new preset sections: Features grid, Testimonials, FAQ accordion,
  About section, and a full-width CTA banner — 10 presets total across
  10 categories (Navbar, Header, Hero, Features, Testimonials,
  Pricing, FAQ, About, Call To Action, Footer).

== What's new in 1.6.0 ==
- New "Site-Wide Sections" settings: assign a Navbar preset and/or
  Footer preset to show automatically on EVERY page (via the theme's
  wp_body_open / wp_footer hooks) — instead of only appearing inside
  one page's content. This is what makes a Navbar preset act like a
  real site navbar, and a Footer preset act like a real site footer.
- Tailwind now loads site-wide automatically whenever a site-wide
  section is active, even on pages with no FB Member Portal shortcode.

== What's new in 1.6.1 ==
- Navbar preset (navbar-1) is now login-aware:
  - Guests see: Home, About Us, Pricing + a Login button.
  - Logged-in members see: Dashboard, Home + an account menu (avatar,
    name, Free/Premium badge, Dashboard link, Log out) — no page
    reload needed, it's automatic based on who's viewing.

== What's new in 1.6.2 ==
- Elementor compatibility: Tailwind/CSS now loads correctly even when
  our shortcodes are placed inside an Elementor "Shortcode" widget
  (previously only detected shortcodes in normal post_content).
- The Design Presets "Insert" button now detects Elementor-built pages
  and warns you to use Elementor's own Shortcode widget instead of
  silently inserting into post_content (which Elementor ignores).

== Elementor usage ==
All shortcodes ([fbmp_login], [fbmp_register], [fbmp_dashboard],
[fbmp_preset key="..."]) work inside Elementor via Elementor's built-in
"Shortcode" widget — drag it onto your page and paste the shortcode.
The plugin's own "Insert" automation only works on normal
Gutenberg/Classic pages, not Elementor-built ones.

== What's new in 1.6.3 ==
- New Settings > "Force CSS Everywhere" toggle. Turn this on if you
  insert [fbmp_preset ...] shortcodes yourself via a code-snippets
  plugin, a theme file, or a hook (instead of Design Presets → Insert,
  or the Site Navbar/Footer options) — the plugin has no way to detect
  those placements automatically, so without this toggle the section
  renders unstyled (plain text, no CSS). This fixes the common case of
  a navbar shortcode showing correctly on the plugin's own pages but
  appearing as plain unstyled links on your homepage or other pages
  where it was added via a snippet.

== What's new in 1.6.4 ==
- Navbar preset (navbar-1) is now fully responsive from 320px phones
  up to desktop. Below the "md" breakpoint, links collapse into a
  hamburger icon that opens a full-width dropdown menu (works the same
  for guest and logged-in views). No extra JS needed — uses a native
  <details> toggle, so it works even with JS-blocking setups.
