---
name: testing-admin-ui
description: Test admin layout changes (sidebar, header, breakpoints) end-to-end by driving the local dev server's Chrome via Playwright/CDP. Use when verifying admin/* CSS or layout changes, especially anything involving scrolling, overflow, fixed/sticky elements, or media-query breakpoints.
---

# Testing the admin UI (sidebar, header, breakpoints)

## When to use this skill

- A PR touches `public/assets/backend/css/admin-styles.css`, `resources/views/admin/layouts/*`, or anything else that affects the admin shell.
- You need to verify scroll/overflow behaviour, sticky/fixed positioning, or responsive breakpoints.
- Manual UI clicking through the admin would be too slow or non-deterministic.

## Why Playwright/CDP, not just screenshots or `computer` tool

Screenshot-only tests have a critical blind spot: a sidebar that looks identical in both broken and fixed states (because the viewport happens to be tall enough that content doesn't overflow) will pass either way. For overflow/scroll work the assertion has to be **programmatic**: shrink the viewport to *force* overflow, then attempt to scroll via `scrollTop = N` and verify the property actually changed. If the container is non-scrollable, the write is silently ignored.

The `computer` / `browser_console` tools sporadically fail with "Chrome is not in the foreground" in this sandbox. Playwright connecting via CDP at `http://localhost:29229` works reliably regardless of window focus.

## Setup checklist

1. **Dev server.** From the repo root:
   ```bash
   php artisan serve --host 127.0.0.1 --port 8000
   ```
   Run it in a background shell (`run_in_background: true`).

2. **Vite assets must exist** or `/login` returns HTTP 500 (`ViteManifestNotFoundException`). The repo blueprint already runs `npm install && npm run build` in maintenance.

3. **Login.** Seeded super-admin is `superadmin@gmail.com` / `12345678`. The session cookie persists in the Chrome profile at `/home/ubuntu/.browser_data_dir` so subsequent test runs can reuse it; if the session has expired, the Playwright script can do the login itself before the assertions.

4. **Playwright.** Already installed in the snapshot. Connect with:
   ```python
   from playwright.sync_api import sync_playwright
   with sync_playwright() as pw:
       browser = pw.chromium.connect_over_cdp("http://localhost:29229")
       page = browser.contexts[0].pages[0]   # reuse existing tab
   ```

## Assertion patterns that catch real regressions

### Pattern 1 — Computed-style assertion (cheap, deterministic)

For any CSS rule the PR claims to change, read the *computed* value, not the source CSS. Example:

```python
page.evaluate("""() => {
  const sb = document.getElementById('sidebar');
  const cs = getComputedStyle(sb);
  return { display: cs.display, overflowY: cs.overflowY, position: cs.position };
}""")
```

Good for verifying the rule was actually applied (not overridden by a more specific selector).

### Pattern 2 — Force overflow, then check `scrollTop` actually advances

This is the *only* reliable way to verify scroll containers. A non-scrolling element silently ignores `scrollTop` writes.

```python
page.set_viewport_size({"width": 1280, "height": 400})  # force overflow
page.evaluate("""() => {
  const el = document.querySelector('.sidebar-menu');
  el.scrollTop = 200;
  return { didScroll: el.scrollTop > 0, scrollHeight: el.scrollHeight, clientHeight: el.clientHeight };
}""")
```

Pass criteria: `didScroll === true` AND `scrollHeight > clientHeight`. If `didScroll === false`, the scroll container isn't actually scrollable — that's a real fail you'd never catch from a screenshot.

### Pattern 3 — Pinned-element invariance

For sticky/fixed headers inside a scrolling container: record `getBoundingClientRect().top` before and after scrolling. If the header is genuinely pinned, both reads should be identical AND the scroll position should have advanced (combine with Pattern 2 — alone it's a vacuous truth).

### Pattern 4 — Collapsed / mobile state

Don't trust hover via `dispatchEvent('mouseover')` — synthetic events don't trigger CSS `:hover`. Use `page.mouse.move(x, y)` to physically move the cursor.

For mobile breakpoint testing: `page.set_viewport_size({"width": 480, "height": 640})` and check the media-query rules apply (e.g. `getComputedStyle(sidebar).transform` should include the translate).

## Common pitfalls in this app

- **`.sidebar-menu` is the scroll container, not `.sidebar`.** Any new layout rule that puts overflow back on `.sidebar` itself will regress the pinned-header behaviour.
- **Submenu expand uses metismenu** — it adds `.mm-show` and toggles `aria-expanded`. To programmatically force a submenu open without a click:
  ```javascript
  $('#metismenu > li > a').each(function(){
    const $a = $(this); const $ul = $a.next('ul');
    if ($ul.length) { $a.attr('aria-expanded','true'); $ul.addClass('mm-show'); }
  });
  ```
- **`transition: all` was deliberately narrowed** to `width, transform, box-shadow` in PR #12 — be careful if you re-add `all`, you'll re-introduce the layout-property transitions that interfered with scroll behaviour.
- **Mobile sidebar uses `transform: translateX(-100%)`** with `.show` to toggle. Don't rely on `display: none`.

## Regression sweep (cheap, run on every PR touching the admin)

```python
for route in ("/admin", "/admin/users", "/admin/roles", "/admin/permissions", "/admin/media"):
    resp = page.goto(f"http://127.0.0.1:8000{route}", wait_until="networkidle")
    assert resp.ok, f"{route} returned {resp.status}"
```

Attach a `pageerror` listener on the page before navigating to catch JS errors.

## Devin secrets needed

None — the seeded super-admin credentials are non-secret development creds documented in `database/seeders/UserTableSeeder.php`.
