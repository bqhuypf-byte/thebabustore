# Public repair-service layout audit — 24 November 2026

## Scope and authorization

The owner explicitly requested a direct visit and layout adaptation after the earlier Babu Care feature was approved. This supersedes the earlier restriction against adapting the reference layout, but keeps Babu identity, Roboto, #182ABB, existing form delivery and unconfirmed prices/warranty as contact-only. No new public route, account, service checkout or tracking system was requested.

## Observed pages

| Page | URL | Observed |
| --- | --- | --- |
| Home | https://rightfix.com.vn/ | 1440px desktop and emulated 390px mobile. Roboto. Large cleaning banner spanning two rows at left; wide upper-right media banner and two lower-right service tiles. Four-column image-led service product grid on desktop; source mobile stacks banners and uses two product columns. |
| Service listing | https://rightfix.com.vn/dich-vu-sua-chua-gia-tot | Public service cards with square images, title/price and a left category/product sidebar. Main heading approximately 27px. No purchase or external form submitted. |
| Mouse repair | https://rightfix.com.vn/sua-chua-chuot-may-tinh-tai-rightfix | Explanatory service article, common-fault headings and an eight-row price table. Content and prices are source business claims, not confirmed Babu terms. |

No source login attempted. Public pages required no credentials. Private/admin screens, full checkout, mail submission, and every source service detail were outside coverage. No source photos, logos, contacts, widgets, tracking scripts or unrelated footer links were copied.

## Babu adaptation

- Existing /sua-chua-gear route and global shop header/footer retained.
- Replace abstract SVG hero with a four-panel image mosaic using actual existing Babu category images, not photographs of completed repair work. Keep owner-approved hero copy and contact CTA.
- Add internal navigation, eight representative service cards, working group filters and all 26 approved service items grouped into four expandable directories. No unconfirmed prices or fixed warranty claims introduced.
- Keep approved four-step process, assurance note, nine native FAQ disclosures and the existing validated repair form.
- Mobile remains one column as previously approved; this intentionally adapts the source two-column product-grid behavior.
- CSS changes are scoped to Babu Care. No product, warranty or home template changes in this iteration.

## Interaction checks and validation

- Local 1440px: four-panel mosaic, eight service cards, four full service groups, 26 items, nine FAQs; all media loaded; no horizontal document overflow. Roboto and #182ABB verified from computed styles.
- Local 390px: mosaic, service catalogue and form stack; no horizontal document overflow. A legacy input-width conflict was corrected so all fields align.
- Selecting mouse filter shows two mouse cards. Selecting the mouse mosaic tile opens the matching directory and navigates to its anchor. FAQ disclosure opens. Form transport/validation logic unchanged.
- 20 tests passed, 249 assertions: RepairServicesTest, InformationPagesTest, WarrantyWorkflowTest, ProductDetailExperienceTest. Includes fake mail only; no real customer request or email was sent.

## Data relationship

Service names and policies remain in the existing structured RepairServicesContent module. Category images come from current active category records without changing those records. Requests use the configured shop email through the existing dedicated Livewire component and mail class. No new dependencies or database migrations.
