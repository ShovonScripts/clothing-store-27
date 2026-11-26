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
  - waiting for locator('[x-trap]') to be visible

```

# Page snapshot

```yaml
- main [ref=e2]:
  - generic [ref=e4]:
    - heading "404" [level=1] [ref=e5]
    - generic [ref=e6]: Not Found
```

# Test source

```ts
  1  | import { test, expect } from '@playwright/test';
  2  | 
  3  | test.describe('Cart Drawer Focus Trap', () => {
  4  |   test.beforeEach(async ({ page }) => {
  5  |     // Go to a product page and add to cart to open the drawer
  6  |     await page.goto('/product');
  7  |     await page.waitForLoadState('networkidle');
  8  |     
  9  |     // Find first product link and click it
  10 |     const firstProduct = page.locator('a[href*="/product/"]').first();
  11 |     if (await firstProduct.isVisible({ timeout: 5000 })) {
  12 |       await firstProduct.click();
  13 |       await page.waitForLoadState('networkidle');
  14 |     }
  15 |   });
  16 | 
  17 |   test('opens drawer, Tab cycles inside, Escape closes, focus returns to cart button', async ({ page }) => {
  18 |     // Click Add to Cart to open the drawer
  19 |     const addToCartButton = page.locator('button[wire\\:click*="addToCart"]').first();
  20 |     if (await addToCartButton.isVisible({ timeout: 5000 })) {
  21 |       await addToCartButton.click();
  22 |     } else {
  23 |       // Fallback: click the first add-to-cart form button
  24 |       const addToCartForm = page.locator('form[action="/cart/add"] button[type="submit"]').first();
  25 |       if (await addToCartForm.isVisible({ timeout: 3000 })) {
  26 |         await addToCartForm.click();
  27 |       }
  28 |     }
  29 |     
  30 |     await page.waitForLoadState('networkidle');
  31 |     await page.waitForTimeout(500);
  32 | 
  33 |     // Wait for drawer to open - the drawer is a Livewire component that re-renders when opened
> 34 |     await page.waitForSelector('[x-trap]', { state: 'visible', timeout: 10000 });
     |                ^ TimeoutError: page.waitForSelector: Timeout 10000ms exceeded.
  35 |     
  36 |     const cartDrawer = page.locator('[x-trap]').first();
  37 |     await expect(cartDrawer).toBeVisible({ timeout: 5000 });
  38 | 
  39 |     // Get focusable elements inside drawer
  40 |     const focusableElements = cartDrawer.locator('button, a, input, select, textarea, [tabindex]:not([tabindex="-1"])');
  41 |     const count = await focusableElements.count();
  42 |     expect(count).toBeGreaterThan(0);
  43 | 
  44 |     // Tab through elements - should cycle inside drawer
  45 |     await page.keyboard.press('Tab');
  46 |     let focused = await page.evaluate(() => document.activeElement?.tagName);
  47 |     expect(['BUTTON', 'A', 'INPUT', 'SELECT', 'TEXTAREA']).toContain(focused);
  48 | 
  49 |     await page.keyboard.press('Tab');
  50 |     focused = await page.evaluate(() => document.activeElement?.tagName);
  51 |     expect(['BUTTON', 'A', 'INPUT', 'SELECT', 'TEXTAREA']).toContain(focused);
  52 | 
  53 |     // Shift+Tab goes backwards
  54 |     await page.keyboard.press('Shift+Tab');
  55 |     focused = await page.evaluate(() => document.activeElement?.tagName);
  56 |     expect(['BUTTON', 'A', 'INPUT', 'SELECT', 'TEXTAREA']).toContain(focused);
  57 | 
  58 |     // Escape closes drawer
  59 |     await page.keyboard.press('Escape');
  60 |     await expect(cartDrawer).toBeHidden({ timeout: 3000 });
  61 | 
  62 |     // Focus returns to the cart button (or the add to cart button)
  63 |     const cartTrigger = page.locator('a[href*="/cart"][aria-label="Cart"]').first();
  64 |     await expect(cartTrigger).toBeFocused();
  65 |   });
  66 | });
```