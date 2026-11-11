# ATK header and category cards reference audit

Observed 2026-11-11 at https://www.atk.store/. Scope: public header, product mega menus and four Shop by Gear Type cards. No purchase, signup or account changes were performed.

## Observed behavior

- Desktop: wide white header, logo left, navigation centered, compact action icons right. Reference typography is Rubik for UI, with serif category-card titles.
- All Products hover: a left category list with a vertical separator; active category has an orange marker. Hovering a category replaces the featured product strip on the right. Category labels and View all link navigate to collections.
- Direct Mice/Keyboards hover: a featured strip without the left category list. Product image, bold uppercase name, no prices, horizontal scrolling and previous/next buttons.
- At a 1440px browser viewport, the reference All Products layout measured 220px sidebar, about 58px column gap and 190px product card width; image stage about 162px high with 10px padding.
- Mobile at 390px: compact logo/search/cart/navigation controls replace desktop links. Category cards stack vertically; description visibility is reduced on the reference.
- Category cards: two-column desktop grid, 18px gap, 12px corner radius, cover background, dark horizontal overlay, left text and white Explore button. At 1440px cards measured about 664 by 280px; section heading around 43px and card headings 33px in a serif face.

## BABU adaptation

- Preserve BABU logo, existing category/product records, Vietnamese labels, order lookup, existing account/search/cart functionality, and Bai Jamjuree typography selected earlier by the owner.
- Use the reference's sidebar state for Sản phẩm; direct category links retain the full-width strip. Only active products with an in_stock variant are featured.
- Preserve actual product/gallery assets. Several keyboard images are promotional posters; they cannot look like ATK's product-only packshots without separate clean assets. No new specifications, promotional terms or product images were invented.
- Four category tiles use existing category banners and descriptions, with the observed layout and 12px radius; category headings use Georgia as a locally available serif substitute.
- Announcement sale copy, newsletter, currency, Discord and ATK account behavior were not cloned because they depend on ATK's services and campaign data.

## Verification

Checked desktop menu opening and sidebar switching, direct category mode, horizontal product scrolling, logo assets, mobile layout and search control. Header/catalog integration tests cover active stock filtering and compact order lookup. No admin data or product records were changed for this work.