# Instructions

- Following Playwright test failed.
- Explain why, be concise, respect Playwright best practices.
- Provide a snippet of code with the fix, if possible.

# Test info

- Name: cart-drawer-focus-trap.spec.ts >> Cart Drawer Focus Trap >> opens drawer, Tab cycles inside, Escape closes, focus returns to cart button
- Location: tests\cart-drawer-focus-trap.spec.ts:17:3

# Error details

```
TimeoutError: page.waitForSelector: Timeout 10000ms exceeded.
Call log:
  - waiting for locator('[role="dialog"][aria-modal="true"]') to be visible

```

# Page snapshot

```yaml
- generic [active] [ref=f2e1]:
  - banner [ref=f2e2]:
    - generic [ref=f2e4]:
      - 'link "{BRAND} - Home" [ref=f2e6] [cursor=pointer]':
        - /url: http://127.0.0.1:8123
        - generic [ref=f2e10]: "{BRAND}"
      - navigation "Main navigation" [ref=f2e11]:
        - link "Home" [ref=f2e12] [cursor=pointer]:
          - /url: http://127.0.0.1:8123
        - link "Shop" [ref=f2e13] [cursor=pointer]:
          - /url: http://127.0.0.1:8123/shop
        - link "Categories" [ref=f2e14] [cursor=pointer]:
          - /url: http://127.0.0.1:8123/categories
        - link "Size Guide" [ref=f2e15] [cursor=pointer]:
          - /url: http://127.0.0.1:8123/page/size-guide
        - link "Delivery" [ref=f2e16] [cursor=pointer]:
          - /url: http://127.0.0.1:8123/page/delivery-info
      - generic [ref=f2e17]:
        - generic [ref=f2e18]:
          - button "বাংলা" [ref=f2e19]
          - button "English" [ref=f2e20]
        - link "Cart" [ref=f2e21] [cursor=pointer]:
          - /url: http://127.0.0.1:8123/cart
  - main [ref=f2e24]:
    - generic [ref=f2e25]:
      - text: "@livewireLazy('storefront.cart-drawer')"
      - main [ref=f2e26]:
        - generic [ref=f2e27]:
          - heading "Shopping Cart (0)" [level=1] [ref=f2e28]
          - paragraph [ref=f2e29]: Review your items before checkout
        - generic [ref=f2e30]:
          - heading "Your cart is empty" [level=2] [ref=f2e33]
          - paragraph [ref=f2e34]: Looks like you haven't added any items yet.
          - link "Continue Shopping" [ref=f2e35] [cursor=pointer]:
            - /url: http://127.0.0.1:8123/shop
  - contentinfo [ref=f2e38]:
    - generic [ref=f2e39]:
      - generic [ref=f2e40]:
        - generic [ref=f2e41]:
          - 'link "{BRAND} - Home" [ref=f2e42] [cursor=pointer]':
            - /url: http://127.0.0.1:8123
            - generic [ref=f2e46]: "{BRAND}"
          - paragraph [ref=f2e47]: Men's fashion for the modern Bangladeshi gentleman. Quality clothing, fair prices, delivered to your door.
        - generic [ref=f2e48]:
          - heading "Quick Links" [level=3] [ref=f2e49]
          - list [ref=f2e50]:
            - listitem [ref=f2e51]:
              - link "Shop All" [ref=f2e52] [cursor=pointer]:
                - /url: http://127.0.0.1:8123/shop
            - listitem [ref=f2e53]:
              - link "Categories" [ref=f2e54] [cursor=pointer]:
                - /url: http://127.0.0.1:8123/categories
            - listitem [ref=f2e55]:
              - link "About Us" [ref=f2e56] [cursor=pointer]:
                - /url: http://127.0.0.1:8123/page/about
            - listitem [ref=f2e57]:
              - link "Contact" [ref=f2e58] [cursor=pointer]:
                - /url: http://127.0.0.1:8123/page/contact
        - generic [ref=f2e59]:
          - heading "Support" [level=3] [ref=f2e60]
          - list [ref=f2e61]:
            - listitem [ref=f2e62]:
              - link "Delivery Information" [ref=f2e63] [cursor=pointer]:
                - /url: http://127.0.0.1:8123/page/delivery-info
            - listitem [ref=f2e64]:
              - link "Returns & Exchanges" [ref=f2e65] [cursor=pointer]:
                - /url: http://127.0.0.1:8123/page/return-exchange
            - listitem [ref=f2e66]:
              - link "Size Guide" [ref=f2e67] [cursor=pointer]:
                - /url: http://127.0.0.1:8123/page/size-guide
            - listitem [ref=f2e68]:
              - link "Privacy Policy" [ref=f2e69] [cursor=pointer]:
                - /url: http://127.0.0.1:8123/page/privacy-policy
            - listitem [ref=f2e70]:
              - link "Terms of Service" [ref=f2e71] [cursor=pointer]:
                - /url: http://127.0.0.1:8123/page/terms
        - generic [ref=f2e72]:
          - heading "Contact Us" [level=3] [ref=f2e73]
          - list [ref=f2e74]:
            - listitem [ref=f2e75]:
              - link "01XXXXXXXXX" [ref=f2e78] [cursor=pointer]:
                - /url: tel:01XXXXXXXXX
            - listitem [ref=f2e79]:
              - link "WhatsApp" [ref=f2e82] [cursor=pointer]:
                - /url: https://wa.me/8801XXXXXXXXX
      - generic [ref=f2e84]:
        - paragraph [ref=f2e85]: "© 2026 {BRAND}. All rights reserved."
        - generic [ref=f2e86]:
          - link "Privacy Policy" [ref=f2e87] [cursor=pointer]:
            - /url: http://127.0.0.1:8123/page/privacy-policy
          - link "Terms of Service" [ref=f2e88] [cursor=pointer]:
            - /url: http://127.0.0.1:8123/page/terms
  - link "Chat on WhatsApp" [ref=f2e89] [cursor=pointer]:
    - /url: https://wa.me/8801XXXXXXXXX?text=Hi%2C+I+have+a+question+about+your+products.
```

# Test source

```ts
  1  | import { test, expect } from '@playwright/test';
  2  | 
  3  | test.describe('Cart Drawer Focus Trap', () => {
  4  |   test.beforeEach(async ({ page }) => {
  5  |     // Add an item to cart via API
  6  |     await page.goto('/shop');
  7  |     await page.waitForLoadState('networkidle');
  8  |     
  9  |     const firstAddToCart = page.locator('form[action="/cart/add"] button[type="submit"]').first();
  10 |     if (await firstAddToCart.isVisible({ timeout: 5000 })) {
  11 |       await firstAddToCart.click();
  12 |       await page.waitForLoadState('networkidle');
  13 |       await page.waitForTimeout(500);
  14 |     }
  15 |   });
  16 | 
  17 |   test('opens drawer, Tab cycles inside, Escape closes, focus returns to cart button', async ({ page }) => {
  18 |     // Go to cart page which should now have items and load the drawer
  19 |     await page.goto('/cart');
  20 |     await page.waitForLoadState('networkidle');
  21 | 
  22 |     // Open cart drawer via header cart button
  23 |     const cartButton = page.locator('a[href*="/cart"][aria-label="Cart"]').first();
  24 |     await expect(cartButton).toBeVisible();
  25 |     await cartButton.click();
  26 | 
  27 |     // Wait for drawer to open
> 28 |     await page.waitForSelector('[role="dialog"][aria-modal="true"]', { state: 'visible', timeout: 10000 });
     |                ^ TimeoutError: page.waitForSelector: Timeout 10000ms exceeded.
  29 |     
  30 |     const cartDrawer = page.locator('[role="dialog"][aria-modal="true"]').first();
  31 |     await expect(cartDrawer).toBeVisible({ timeout: 5000 });
  32 | 
  33 |     // Get all focusable elements inside drawer
  34 |     const focusableElements = cartDrawer.locator('button, a, input, select, textarea, [tabindex]:not([tabindex="-1"])');
  35 |     const count = await focusableElements.count();
  36 |     expect(count).toBeGreaterThan(0);
  37 | 
  38 |     // Tab through elements - should cycle inside drawer
  39 |     await page.keyboard.press('Tab');
  40 |     let focused = await page.evaluate(() => document.activeElement?.tagName);
  41 |     expect(['BUTTON', 'A', 'INPUT', 'SELECT', 'TEXTAREA']).toContain(focused);
  42 | 
  43 |     // Tab again
  44 |     await page.keyboard.press('Tab');
  45 |     focused = await page.evaluate(() => document.activeElement?.tagName);
  46 |     expect(['BUTTON', 'A', 'INPUT', 'SELECT', 'TEXTAREA']).toContain(focused);
  47 | 
  48 |     // Shift+Tab goes backwards
  49 |     await page.keyboard.press('Shift+Tab');
  50 |     focused = await page.evaluate(() => document.activeElement?.tagName);
  51 |     expect(['BUTTON', 'A', 'INPUT', 'SELECT', 'TEXTAREA']).toContain(focused);
  52 | 
  53 |     // Escape closes drawer
  54 |     await page.keyboard.press('Escape');
  55 |     await expect(page.locator('[role="dialog"][aria-modal="true"]')).toBeHidden({ timeout: 3000 });
  56 | 
  57 |     // Focus returns to the cart button in header
  58 |     const cartTrigger = page.locator('a[href*="/cart"][aria-label="Cart"]').first();
  59 |     await expect(cartTrigger).toBeFocused();
  60 |   });
  61 | });
```