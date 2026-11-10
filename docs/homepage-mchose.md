# Homepage layout: MCHOSE reference

The homepage uses the existing BABU catalog, Slide records, blog posts, and store settings. No product records are modified.

- First viewport: full-width Slide carousel, with height calculated from the actual header size. Falls back to Banner Web when no Slides exist.
- Categories: two-column image cards; products and sale sections retain their existing behavior.
- Editorial content: introduction, HUB, community, image cards and FAQ layouts; blog section uses existing posts.
- Admin: Cài đặt Website → Trang chủ. Sections are editable, reorderable and can be disabled. Null content uses defaults derived from existing store settings; an empty saved list intentionally hides optional sections.
- No fabricated reviewers, customer photos, product claims or policies are inserted.

Validation: PHP syntax, Blade cache compilation, homepage HTTP 200, four Slide assets HTTP 200, and four tests with 17 assertions passed. Visual browser verification could not run because the browser tool rejected authorization.

## Rollback

Use `git revert <homepage-commit>` to restore the previous frontend. The nullable homepage_sections column may remain without affecting the old frontend. To remove it, before reverting the code run:

    php artisan migrate:rollback --path=database/migrations/2026_11_10_000002_add_homepage_sections_to_settings_table.php

Removing the column also removes any editorial content saved there. Back it up first if needed. Existing descriptions, images and product records are unaffected.