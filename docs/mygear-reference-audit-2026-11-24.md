# MYGEAR storefront reference audit

Date: 2026-11-24 (Asia/Bangkok). Destination: existing local Babu Store Laravel checkout.

## Observed coverage

| Page | URL | Evidence / observed states |
|---|---|---|
| Home | https://mygear.com.vn/ | Desktop 1440px, header search, category navigation, marketing carousel, four banners, dense product sections, footer. Narrow 500px viewport inspected: source has horizontal overflow; do not reproduce that defect. |
| Category | https://mygear.com.vn/man-hinh-viewsonic | Desktop: left filter rail (brand, price, attributes), category tiles, featured products, white bordered cards and sort controls. |
| Product | https://mygear.com.vn/man-hinh-may-tinh-viewsonic-va2215h-100hz | Title/breadcrumb across the page, left gallery with bottom thumbnails, middle banner/specification/price/actions, right information panels. Lower description/reviews column and technical/news sidebar; related products below. |
| Cart | https://mygear.com.vn/cart | Empty cart, continue-shopping action and multi-column store/support footer. No cart mutation or purchase submitted. |
| Warranty | https://mygear.com.vn/tra-bao-hanh | Phone lookup field, Kiểm tra action, warranty reception/brand location sections. No personal data entered. |
| Account | https://mygear.com.vn/taikhoan -> https://mygear.com.vn/dang-nhap | Public redirect and visible username/password/login controls. No authentication attempted. |

## Reproduction specification

- Retain Babu logo, Roboto, brand purple #3d2bd6, original product/media/admin data and existing purchase validation.
- Shared header: thin promotional strip, wide rectangular search field, icon actions, category button on the navigation row, bordered category links. Bind to Babu routes and actual categories.
- Home: wide carousel, category marketing tiles, campaign/product grids in bordered panels; use actual slides and stock-qualified products.
- Category: filter rail on desktop with existing category/price/brand bindings; four-column product grid beside it, horizontal sort row; two columns and compact filters on mobile.
- Product: three-column desktop information hierarchy, bottom gallery thumbnails, price/action emphasis, verified technical data in a sidebar. Lower content and specification/news sidebar; review interactions retained. Two-column tablet and stacked mobile layouts. No independent sticky columns.
- Forms/cart/footer: common light bordered panels, purple headings/actions and consistent spacing. No invented stores, contact details, policies, installment programs or reviews.
- Inventory remains real: nullable exact counts; original status-only fallback and validator stay unchanged. SKU remains hidden.

## Limitations

This is representative public-template coverage, not an exhaustive audit of every MYGEAR route. Filled-source-cart, checkout submission, login success, private/admin screens, warranty lookup results, paid actions and real review submission were not tested. No third-party private data or assets were downloaded. Source shipping/promotional claims are not Babu business facts and must not be copied as such.


## Local implementation and validation

Implemented in `public/css/retail-reference.css` and the existing shared Blade layouts: masthead/menu, homepage merchandise positions, category filter rail, three-column product purchase area, horizontal gallery thumbnails, lower specifications/news rail, visible review block, login/warranty/cart panel styling and footer. Existing Babu assets and purple branding are retained. No product, price, inventory or policy records were changed.

Local checks: 54 tests passed (406 assertions), covering purchase validation, product details, listing prices/carousel, warranty workflows, customer activation, information pages and account center. Representative public endpoints return HTTP 200 after final Blade correction. Desktop 1440px and narrow 500px checks cover overflow, grid changes, mobile navigation and form layout. Browser interaction checks cover category sorting and explicit variant selection; no real purchase or external form submission was made.

The current database has no articles marked published. The news rail therefore links to the existing Blog page instead of exposing draft previews. Private pages and every possible source category/brand combination remain outside the observed audit coverage.
