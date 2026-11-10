# atk_store_frontend — rebuild the ATK Store frontend look and motion in the owner's shop

## When to use

The owner wants their shop (thebabustore, Laravel 11 / Livewire / Filament 3) to look and move like https://www.atk.store/ — same typography, colours, buttons, cards, header, hero slider, cart drawer and animations — under their own brand, products, prices and contacts.

Full spec: `references/atk-store-clone-spec-storefront-english.md` (surveyed 10 Nov 2026, read-only, storefront only). Read it before writing code. This file is the short version: tokens, motion rules, ground rules.

## Measured tokens (from the reference's own CSS + theme settings — never guess)

- **Body/UI:** `Rubik, sans-serif` (Shopify CDN woff2, 400/600, normal + italic).
- **Headings:** `"New York", Iowan Old Style, Apple Garamond, Baskerville, Times New Roman, Droid Serif, Times, Source Serif Pro, serif` — **system serif stack, no webfont**. Use exactly; do NOT substitute Playfair Display or any serif webfont.
- **Base size:** 15px. Page titles large serif (~48px).
- **Colours:** bg #FFFFFF; text rgb(17,17,17); heading rgb(0,0,0); borders rgb(217,217,217)/rgb(223,223,223), darker rgb(169,169,169).
- **Primary button:** black bg (rgb(0,0,0)/(17,17,17)/(40,40,40) by section), white text, **radius 0px (square)**, ~48–56px tall, full-width on PDP.
- **Sale accent:** rgb(222,42,42) = **#DE2A2A** (sale price + % OFF badge). Old price opacity .7 + line-through. Large price = base + 7px (22px).
- **Loyalty teal:** #6BD5DA (pixel-sampled). **Lime button accent:** #F3FF34 (≥1 section). **Off-white sections:** #F9F7F4/#F8F8F8 (pixel-sampled).
- **Product card:** white, no border/shadow, image on white, red % OFF badge top-right, centred title ~15px medium, red sale price + grey struck-through old price, star row, colour swatches (black ring on selected). **No Add-to-Cart on cards** — hover-only "Quick view" pill.
- **Cart drawer:** width 89vw, max 400px, from the right. **Header:** ~125px (announcement ~45px + nav ~80px); transparent over hero at top (white text), solid white + black text after scroll.

## Motion (measured from theme.css / theme.js — reproduce exactly)

- Micro-transitions `.2s–.25s ease-in-out`; drawer `.6s cubic-bezier(.75,0,.175,1)`; header `.2s ease-in-out`; slideshow autoplay `5s` with progress-bar animation; prev/next `.3s ease-in-out`.
- **9 keyframes by name:** `playButtonRipple` (1.4s), `textUnderlinedAnimatedKeyframes` (.6s), `spinnerRotate` (2s linear), `spinnerDash` (1.5s), `spinnerColor` (6s), `slideshowProgressBarAnimation`, `shopTheLookDotKeyframe` (2s), `prevNextButtonKeyframe` (.3s), `navigationItemAnimation`.
- **Carousels:** Flickity (theme asset `flickity.js`). **Countdown:** ticks 1s (custom element, `countdown-part="days/hours/minutes/seconds"`).
- **Add to cart:** button turns ORANGE + spinner → drawer slides in from right over dimmed page → badge increments.
- **Card hover:** "Quick view" pill appears (hover-only); image swaps to 2nd angle. **PDP:** thumbnail click swaps main image; variant pills update price/images; selected pill black-filled, white text.
- **Do NOT add:** scroll-reveal (none observed), back-to-top (none), marquee (none), auto dark mode (light-only).

## Ground rules (always)

1. **Owner's brand/products/prices/contacts.** Never copy the reference's products, prices, text, images, logos, people, quotes, videos, or contact/payment details.
2. **Vietnamese UI; integer VND.** Reference is English/USD.
3. **Do not reproduce the reference's bugs** (spec §7): free-gift async race (gifts computed server-side in the same response; removing the qualifying product removes the gift, never reprices it at full price), filter counts counting variants (count products), spec contradictions, duplicate colour radios, sale prices without compare prices, stale sitemap 404s, loose search, placeholder links.
4. **Sales receipt:** reference has none. Build the self-created "Phiếu bán hàng" (NOT a tax e-invoice: no tax provider/code/signature) — auto-PDF, SMTP email, A5/80mm print.
5. **Safety:** coupons server-side + row locks; order codes from a DB sequence in a transaction, unique index, never reuse cancelled codes; revenue = paid or delivered only.
6. **Visual QA:** screenshot reference vs build at the same viewport, side by side; list + fix every visible difference, re-check.

## How to apply (Codex / Gemini on the owner's machine)

1. Read `references/atk-store-clone-spec-storefront-english.md` in full.
2. Run the Part 1 feature filter (KEEP / ADAPT / DROP / ASK) and **STOP for the owner's approval before coding** — the task file `codex-task-atk-design.md` (delivered alongside this skill) has the exact opening prompt.
3. Source-of-truth order: **database > real website (thebabustore) > task file > demo**. Prices, SKUs, stock, warranty, contacts come only from the shop's own data.
4. Local site http://127.0.0.1:8000/ is reachable only from the owner's machine (chat Muse cannot reach it). Verify the redesign by opening local pages in the browser and comparing screenshots side by side with the reference.
