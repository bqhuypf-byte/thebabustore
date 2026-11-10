# ATK Store — Storefront Clone Spec (English, condensed)

**Reference:** https://www.atk.store/ (ATK Gear — gaming mice, keyboards, mouse pads). **Surveyed:** 10 Nov 2026, read-only. No sign-in, no order, no form submitted. One cart item added twice only to inspect drawer/cart/checkout, then removed; cart verified empty.
**Scope:** storefront ONLY (no admin access provided; admin out of scope, §9). **Platform:** Shopify; custom theme, signatures match Maestrooo **Impact** (theme asset `flickity.js`, `.slideshow`, `.drawer`, `--section-autoplay-duration`).
**Target:** the owner's shop (thebabustore, Laravel 11 / Livewire / Filament 3). Rebuild the look/motion below under the owner's own brand/products/prices/contacts.
**Sources:** live browser observation + the site's own files, downloaded and grepped (`theme.css` 302 KB, `theme.js` 226 KB, `vendor.js`, `flickity.js`, homepage HTML theme settings). Values the browser only estimated visually are marked "visual est." — never treat them as measured.

## §1 Ground rules

1. **Owner's brand only.** Never copy the reference's products, prices, article text, images, logos, people, quotes, videos, or contact/payment details. Every reference value here is an example to replace.
2. **Real data wins.** Products (CSV: name, slug, category, price, originalPrice, image, sku, stock, rating, reviewCount, description, highlights), contacts, payment details, policies — from the owner's data. Missing data = marked placeholder, never invented.
3. **UI in Vietnamese; money as integer VND.** Reference is English/USD.
4. **Do not reproduce the reference's bugs** (§7) — each is a must-fix.
5. **Order-code format is the owner's decision** (proposal pending: RVX + 6 digits). Never decide silently.
6. **No demo-login button, no test products in production.**
7. **Sales receipt:** reference has none (§5). Build the self-created "Phiếu bán hàng" — NOT a tax e-invoice (no tax provider/code/signature).
8. **Safety:** coupons validated server-side with row locks; order codes from a DB sequence in a transaction, unique index, never reuse cancelled codes; revenue = paid or delivered only; web restore forbidden (CLI-only with typed confirmation).

## §2 Design system — MEASURED tokens

### 2.1 Fonts (homepage HTML theme settings + Shopify font files)

| Token | Value |
|---|---|
| Body/UI | `Rubik, sans-serif` — woff2 `rubik_n4/n6/i4/i6` on `fonts.shopifycdn.com` (400/600, normal + italic) |
| Headings | `"New York", Iowan Old Style, Apple Garamond, Baskerville, Times New Roman, Droid Serif, Times, Source Serif Pro, serif` — **system serif stack, NO webfont**. Use exactly; do NOT substitute Playfair Display or any serif webfont |
| Review stars | `JudgemeStar` (Judge.me app) |
| Base size | `15px` (`--base-font-size`). Body ~15–16px, line-height ~1.6 (visual est.). Nav ~15–16px medium, black (visual est.). Page titles large serif ~48px (visual est.) |

### 2.2 Colours

| Token | Value | Source |
|---|---|---|
| Page bg / text / heading | #FFFFFF; text rgb(17,17,17) (sections vary 0/17/40); heading rgb(0,0,0)/(17,17,17) | `--background`, `--text-color`, `--heading-color` |
| Borders | rgb(217,217,217) / rgb(223,223,223); darker rgb(169,169,169) | `--border-color(-darker)` |
| Primary button | bg rgb(0,0,0)/(17,17,17)/(40,40,40) by section; text rgb(255,255,255) | `--primary-button-background/text-color` |
| Secondary button | bg rgb(243,255,52) = #F3FF34 (lime) in ≥1 section; text rgb(40,40,40) | `--secondary-button-background/text-color` |
| Sale accent | rgb(222,42,42) = **#DE2A2A** (sale price + % OFF badge); old price opacity .7 + line-through | `--product-on-sale-accent` |
| Loyalty teal | **#6BD5DA** (streak widget, gift button, "Login to receive") | pixel-sampled |
| Off-white sections | #F9F7F4 / #F8F8F8 (homepage below hero) | pixel-sampled |
| Header | white #FFFFFF after scroll; transparent over dark hero at top, white text/logo | pixel-sampled |
| Announcement bar | white bg, black bold centred text; "Learn More" black pill, white text, fully rounded | visual est. |
| Footer | white bg, black uppercase small headings, black/grey links | visual est. |
| Checkout left panel | dark navy (near-black blue) — hex NOT sampled, do not guess | visual est. |
| Stars | gold/orange (visual est.). In-page links blue/purple underlined (visual est.) | visual est. |

### 2.3 Shape/size

Button radius `0px` (square, `--button-border-radius`; visual est. said ~4–6px — follow the variable). Primary button ~48–56px tall, full-width on PDP (visual est.). Header ~125px incl. announcement ~45px + nav ~80px at 1549px viewport (visual est.). Cart drawer `width:89vw; max-width:400px; height:100%` (`.drawer`). Large price = base + 7px = 22px (`.price--large`). Price-list items separated by `margin-inline-end:10px`.
Product card: white, no border/shadow, image on white, red % OFF badge top-right, centred title ~15px medium, red sale price + grey struck-through old price, star row, colour swatches (black ring on selected). **No Add-to-Cart button on cards** — hover-only "Quick view" pill.

### 2.4 Motion — MEASURED (theme.css / theme.js)

**Timings:** micro-transitions `opacity/transform/color/background .2s–.25s ease-in-out` (default); drawer `transform .6s cubic-bezier(.75,0,.175,1)` + visibility (.7s variant exists); header `background .2s ease-in-out, color .2s ease-in-out, box-shadow .2s ease-in-out`; slideshow autoplay `5s` (`--section-autoplay-duration`); progress bar `slideshowProgressBarAnimation var(--section-autoplay-duration) linear`; nav item `navigationItemAnimation var(--section-autoplay-duration) linear`; prev/next `.3s ease-in-out prevNextButtonKeyframe`; spinner `2s linear infinite spinnerRotate` + `1.5s ease-in-out infinite spinnerDash` + `6s ease-in-out infinite spinnerColor`; play ripple `1.4s ease-in-out infinite playButtonRipple`; look-dot `2s ease-in-out infinite shopTheLookDotKeyframe`; underlined text `.6s textUnderlinedAnimatedKeyframes`.
**Keyframes (9, by name):** `playButtonRipple`, `textUnderlinedAnimatedKeyframes`, `spinnerRotate`, `spinnerDash`, `spinnerColor`, `slideshowProgressBarAnimation`, `shopTheLookDotKeyframe`, `prevNextButtonKeyframe`, `navigationItemAnimation`. Reproduce by name/timing; invent no others.
**Carousel library:** Flickity (theme asset `flickity.js`).

| # | Element | Trigger | Effect | Timing |
|---|---|---|---|---|
| A1 | Announcement bar | Auto-rotate + arrows | Fade between ≥3 messages; countdown ticks 1s (custom element, `countdown-part="days/hours/minutes/seconds"`, restarts at next end time) | 1s tick; fade .2s |
| A2 | Hero slider (7 slides) | Autoplay 5s + dot bars + arrows | Slide change; progress bar on active dot | 5s linear |
| A3 | Sticky header | Scroll | Announcement bar scrolls away; header transparent (white text) → solid white + black text | .2s ease-in-out |
| A4 | Product-card hover | Hover | "Quick view" white pill appears (hover-only); image swaps to 2nd angle | .2–.25s |
| A5 | Add to cart | Click | Button turns ORANGE + spinner → drawer slides in from right over dimmed page → badge increments | spinner 2s linear; drawer .6s cubic-bezier(.75,0,.175,1) |
| A6 | Cart drawer | Opens/closes | Slides in from right; threshold progress bar (ref tiers $5@$50, $10@$100, $15@$150, $20@$200 — owner's VND tiers are a decision); blue gift toast at top; qty steppers, Remove, "Clear all" | .6s cubic-bezier(.75,0,.175,1) |
| A7 | Newsletter popup | Scroll/time on home | "Subscribe to Us", serif "REGISTER AND RECEIVE 8% OFF", email field, "Send Code" disabled until email entered, "OK", X, "No thanks" | not measured — default .2–.25s |
| A8 | Streak widget | Always visible, fixed bottom-left | Teal gift button + white card "3-day streak!…" + teal "Login to receive" | static |
| A9 | PDP gallery | Click thumbnail/arrows | Main image swaps; variant pills update price/images; selected pill black-filled, white text | .2s |
| A10 | FAQ accordion / filters | Click | Answer/drawer expands; pills filter FAQ list | .2–.25s |
| A11 | Checkout express buttons | Load | PayPal/Google Pay render as grey skeleton rectangles; "Pay now" disabled until form complete | until loaded |
| A12 | Scroll-reveal | — | **NONE observed — do NOT add** | — |
| A13 | Back-to-top | — | **NONE observed — do NOT add** | — |
| A14 | Marquee/ticker | — | **NONE observed — do NOT add** | — |
| A15 | Mobile menu animation | — | **NOT VERIFIED** (desktop only) — verify on a real phone; do not invent | — |

## §3 Storefront

### 3.0 Routes

Home `/`; collections `/collections/all-products, deals, best-sellers, new-arrival, loved-by-pros, esports-verified, mice, keyboard, mouse-pads, atk-x-aspas, velota, velota-mice, velota-keyboards, velota-mouse-pads, velota-accessories, atk-x-qk` + ~15 more (30 in sitemap); products `/products/<slug>`; `/cart`; `/checkout` (→ `/` when empty; with items → `/checkouts/cn/…`); `/search?q=`; pages `/pages/about-us, contact, terms-conditions, warranty, shipping-policy, return-policy, payment-policy, privacy-policy, faqs, product-manual, atk-hub, track-your-order, product-care, help-center, deals-rewards, sign-up-gift-offers, register, product-authentication, free-gift-mystery-box, free-shipping, signature, black-friday-2025, esports-verified-players, velota-brand-story` (all verified live except affiliate-program destination — its footer link did not navigate, §7.8); `/blogs/news` (18 pages; tags All/Event-Promotion/Product Introduction/Review/Technical Guide); `/account/login`, `/account/register` (both → Shopify new customer accounts, email-first, no password on step 1); `/policies/terms-of-service` (Shopify default, unstyled).
**Sitemap-dead slugs (404 — do NOT reproduce):** `/pages/help-center, track-your-order, product-care, affiliate-program, deals-rewards, sign-up-gift-offers, terms-of-service, warranty-policy, contact-us`.

### 3.1 Homepage (top → bottom)

1. Announcement bar (A1) + "Learn More" black pill. 2. Header (logo; nav All Products / Mice / Keyboards / Mouse Pads / Deals & Rewards / VELOTA / ATK HUB; EN/USD selector, Discord, mail, Search, Account, Cart badge; sticky, A3). 3. Hero slider, 7 slides, dark gaming imagery, headline + CTA (A2). 4. "Featured Products" slider (~3.5 cards visible, Flickity). 5. "Shop by Gear Type" tiles (Mice/Keyboards/Mouse Pads). 6. Feature blocks (Mouse Pads/Keyboards full-bleed). 7. "Make It Yours" setup builder (Mouse → Keyboard → Mouse Pad). 8. "From the Pros" quotes. 9. "Esports-Verified" badges. 10. "Reviews From Influencers" (12 videos; All/Mouse pills; Desktop/Mobile tabs). 11. "Best Sellers" slider ("Save Up to 60% OFF"). 12. "DESIGNED FOR SPEED. BUILT TO WIN." feature sections. 13. "ATK SUPPORT" (driver/warranty/update cards). 14. "Check Warranty / Blogs & Guides / Learn More". 15. Subscribe (email + Register). 16. Footer: columns SUBSCRIBE TO US / EXPLORE / SUPPORT / OTHER STORE / ATK GEAR GLOBAL LIMITED; socials (Facebook, X, Instagram, Pinterest, YouTube, TikTok, Discord); payment icons (Amex, Apple Pay, Bancontact, Google Pay, Mastercard, MB WAY, PayPal, Shop Pay, Union Pay, Visa); company block + contact (reference values are examples only). Newsletter popup (A7) + streak widget (A8) float above.

### 3.2 Collection page (mice 23 products; keyboards 29)

Breadcrumb Home / Mice; serif title; SEO paragraph + "Read more" toggle. Filters: Availability (In stock/Out of stock + counts), Price, Product type — collapsible drawers (A10). Sort dropdown (Featured/Most relevant/…). Count label, 4-col grid, pagination. Cards per §2.3; hover Quick view + image swap (A4). Keyboards adds a "Gaming Keyboard FAQ" accordion article below the grid. **Counts must count products, not variants** (§7.2).

### 3.3 Product detail (mouse Phantom $89.98/$119.98, 4.69★ 127 reviews; keyboard RS7 Air $69.98/$99.98, 30% OFF, 4.79★ 112 reviews — reference currency)

Breadcrumb; 2-col (gallery left, buy box right). Gallery: thumbnail column (28 on mouse) + main image + arrows; click swaps image (A9). Buy box: red "XX% OFF" badge; title; stars + review-count link; sale price red large + regular struck through; gift panel (bundle coupons $10 OFF $150+ / $20 OFF $250+; FREE gifts over $50 / $120); Model pills; Colour pills; qty stepper; ADD TO CART (black, full-width) + "Buy with Shop Pay" + "Our Trusted Online Stores"; "Our Promise" (Ships in 24h, 12-Month Limited Warranty, 30-Day Trial); feature icons (30-Day Satisfaction Guarantee, 12-Month Quality Assurance, Lightning-Fast Delivery). Below: highlights (e.g. carbon-fiber hollow shell, 46 g, PAW3950, 8KHz); review summary (Write a Review, 5★ filter, Sort newest); Reviews; "Check Warranty"; "Read More/Less" spec tables; embedded video; "Complete Your Setup" add-ons; "Our Best Offers"; FAQ accordion; "You May Also Like" slider. Variant pills update price/images; selected = black-filled white text; out-of-stock variant = PRE-ORDER ("ships as soon as inventory is available"). Keyboard extras: "Hear the Sound" button; "Striking Color Expression" (5 colours, Select buttons). **Single source of truth per spec value; colour names identical in selector and spec table; buy-box colours match the showcase** (§7.3–7.4).

### 3.4 Cart + checkout

**Drawer (A5/A6):** slides from right over dimmed page; threshold progress bar + gift toast + steppers + Remove + "Clear all". **/cart:** empty state ("Your cart is empty" + black START SHOPPING → all products); with items: line items, stepper, Remove, clear-all, Order summary, Subtotal, black "Check Out" + "or continue shopping". **Checkout (look only):** left dark-navy panel with line-art; Express checkout (PayPal/Google Pay, skeleton while loading, A11); Contact (email); Delivery (Country, name, address, apt, city, state, ZIP, phone optional); Shipping method ("Enter your shipping address…"); Payment (card, billing = same as shipping); "Pay now" disabled until complete. Right white summary (items, discount code + Apply, Subtotal, Shipping, Total).
**Free gifts — rebuild CORRECTLY (§7.1):** gift lines computed server-side in the same response as the cart change; gift is free (0) only while its threshold holds; removing the qualifying product removes the gift in the same operation — never repriced at full price. Discounts: automatic (reference: Black Friday Threshold, greeting-card, AOVAI codes); owner's coupons: 3 types (%, fixed, freeship), server-side recheck + row locks.

### 3.5 Search

`/search?q=`: "Search: N results found for '<query>'", same card layout + journal entries, Sort by Relevance. **Match meaningfully** (name/SKU/category) — not the reference's loose matching (§7.9).

### 3.6 Footer pages

- **About Us:** black hero, "Who We Are" serif, timeline 2023–2026 + year filter; no duplicated events (§7.7).
- **Contact:** form (Full Name optional / Email* / Topic* Support–Cooperation–Complaints / Message* / Send); SUPPORT + COOPERATION cards (reference emails are examples only).
- **FAQs:** keyword search, 11 category pills, ~70 accordion Q&As (7-day return / 1-year warranty).
- **Shipping/Return/Payment/Warranty/Privacy/Terms:** serif H1, letterspaced caps; Shipping: standard 1–4 days & express 1–3 days, free over $80 (reference threshold — owner's decision), taxes/duties on express; Return: free form, 30-day replacement, refund in 2 days; Warranty: 12-month limited + 30-day satisfaction guarantee; Privacy: last-updated date; Terms: 20 sections. **No "[LINK TO …]" placeholders** (§7.10).
- **User Manual:** MICE/Keyboards/Accessories grids, Download + View manual. **ATK HUB:** render real content — not a blank iframe (§7.8). **Blog/News:** tag filters, search, 9/page, pagination.
- **Login/Register:** email-first page (logo, "Sign in or create an account", Email, Continue, pre-checked news checkbox, Terms + Privacy links). Owner's auth is existing Laravel auth — do not rebuild Shopify accounts.

### 3.7 Builder / Pros / Influencers

Setup builder: 3 steps (Mouse → Keyboard → Mouse Pad), compatible products per step, combined price. Pros quotes, Esports-Verified badges, Influencer videos (All/Mouse pills, Desktop/Mobile tabs): rebuild with the owner's own testimonials/videos, or omit if none — never copy the reference's people, quotes or videos.

## §4 Admin

Not surveyed — no admin access provided. Do not infer an admin spec. (Reference is Shopify; owner's admin is the existing Filament admin — add missing screens there, do not clone Shopify's admin.)

## §5 Sales receipt (NEW — reference has none)

Self-created "Phiếu bán hàng" (NOT a tax e-invoice: no tax provider/code/signature). Content: order date/time, payment info, customer name, order code, line items, totals, shop contact. Trigger (owner's decision — proposal pending): auto-PDF on order created/confirmed/paid. Delivery: email via SMTP (SPF/DKIM/DMARC); A5 + 80mm print in admin. Customer side: download link on the order-tracking page for the customer's own order only.

## §6 Data model

Product, ProductVariant (sku, status, name, price, discount_price, image, stock, product_id, warranty_days — flat list; display groups derived from variant names), Category, Subcategory, Brand, Order, OrderItem, Review, Coupon, Setting, Slide, Blog, Address, Transaction, User, Activity. New: Announcement (rotating messages + countdown end time), GiftThreshold (threshold → gift product), SetupBundle (builder steps), InfluencerReview (owner-supplied only).

## §7 Bugs — must-fix, do NOT copy

1. **Free-gift race (most serious):** gifts auto-add async seconds after add-to-cart; drawer/cart/checkout disagree; removing the product left gifts repriced at full price ($49.98+$29.98) then vanished on re-render. Build §3.4's synchronous rule.
2. **Filter counts wrong:** Mice 22+12=34 for 23 products; Keyboards 23+20=43 for 29 (counts variants). Count products.
3. **Spec contradictions:** DPI 1–50000 (table) vs 10–40000 (sensor section); colour names differ selector vs spec table ("Verge (Blue)" vs "Verge (Gray Blue)").
4. **Colour mismatch + typos:** buy box White/Black only vs 5 showcase colours; typos "AdiustableBult", "tacticle", "fasttttt", "ive"; low-effort reviews ("aasd", "good").
5. Duplicate colour radios on cards (Black/White twice). Deduplicate.
6. Sale price with no compare price on several products. Every sale needs a compare price or no badge.
7. About timeline duplicates events (GSCES ×5, CES ×10, ChinaJoy ×5, CITE ×5).
8. Stale sitemap: 9 pages 404 at sitemap slugs; Affiliate Program footer link did not navigate; ATK HUB blank iframe. Keep sitemap synced; no blank iframes.
9. Loose search: "phantom" returned 15 products incl. unrelated models.
10. Checkout quirks: brand "ATK Gaming Gear" vs "ATK Gear" (pick one name); 3 auto-discount codes in URL not listed in UI; Terms "[LINK TO REFUND POLICY]" placeholder.
11. Transient image failures (logo/flag/product images failed or pending). Add loading/error states.
12. Accessibility/UX: 28 thumbnails with no accessible name; countdown/announcement rotate on every page incl. 404s; "3-day streak" claimed on a first visit. Fix names; scope the bar to the storefront; show only real streaks.

## §8 Acceptance checklist

- [ ] Tokens per §2: Rubik 400/600, exact system-serif heading stack, base 15px, white bg, black square buttons (radius 0), sale #DE2A2A, teal #6BD5DA, borders #D9D9D9/#DFDFDF.
- [ ] Motion per §2.4: 5s autoplay + progress bar; drawer .6s cubic-bezier(.75,0,.175,1); header .2s; micro .2–.25s; 9 keyframes; Flickity sliders; 1s countdown. NO scroll-reveal, NO back-to-top, NO marquee, NO dark mode.
- [ ] Visual match: reference vs build screenshots, same viewport, side by side; list + fix every visible difference, re-check.
- [ ] All §3.0 routes resolve; no dead sitemap entries.
- [ ] Drawer + /cart + checkout always agree on lines/gifts/totals; gifts never reprice at full price.
- [ ] Filter counts count products; search matches meaningfully; variant pills update price/images; out-of-stock = PRE-ORDER.
- [ ] "Pay now" disabled until form complete; express buttons skeleton while loading.
- [ ] Newsletter popup, streak widget, FAQ accordion, setup builder work per §3.
- [ ] Receipt (§5): PDF, SMTP email, A5/80mm print.
- [ ] Owner's brand/products/prices/contacts throughout; nothing copied; no test products, no demo login, no placeholder links.

## §9 Out of scope

Admin (see §4). Shopify apps (Judge.me, Avada Joy, AOVAI, goaffpro): rebuild equivalents in the owner's stack only where the owner asks. Multi-currency/language (reference EN/USD; owner's shop Vietnamese/VND). Reference's external stores (VXE, Amazon, AliExpress, eBay, Ozon, TikTok US, Shopee). Mobile menu animation (not verified — verify on a real phone first).
