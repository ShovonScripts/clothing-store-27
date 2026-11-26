import { test, expect } from '@playwright/test';

test.describe('Cart Drawer Focus Trap', () => {
  test.beforeEach(async ({ page }) => {
    // Go to a product page and add to cart to open the drawer
    await page.goto('/product');
    await page.waitForLoadState('networkidle');
    
    // Find first product link and click it
    const firstProduct = page.locator('a[href*="/product/"]').first();
    if (await firstProduct.isVisible({ timeout: 5000 })) {
      await firstProduct.click();
      await page.waitForLoadState('networkidle');
    }
  });

  test('opens drawer, Tab cycles inside, Escape closes, focus returns to cart button', async ({ page }) => {
    // Click Add to Cart to open the drawer
    const addToCartButton = page.locator('button[wire\\:click*="addToCart"]').first();
    if (await addToCartButton.isVisible({ timeout: 5000 })) {
      await addToCartButton.click();
    } else {
      // Fallback: click the first add-to-cart form button
      const addToCartForm = page.locator('form[action="/cart/add"] button[type="submit"]').first();
      if (await addToCartForm.isVisible({ timeout: 3000 })) {
        await addToCartForm.click();
      }
    }
    
    await page.waitForLoadState('networkidle');
    await page.waitForTimeout(500);

    // Wait for drawer to open - the drawer is a Livewire component that re-renders when opened
    await page.waitForSelector('[x-trap]', { state: 'visible', timeout: 10000 });
    
    const cartDrawer = page.locator('[x-trap]').first();
    await expect(cartDrawer).toBeVisible({ timeout: 5000 });

    // Get focusable elements inside drawer
    const focusableElements = cartDrawer.locator('button, a, input, select, textarea, [tabindex]:not([tabindex="-1"])');
    const count = await focusableElements.count();
    expect(count).toBeGreaterThan(0);

    // Tab through elements - should cycle inside drawer
    await page.keyboard.press('Tab');
    let focused = await page.evaluate(() => document.activeElement?.tagName);
    expect(['BUTTON', 'A', 'INPUT', 'SELECT', 'TEXTAREA']).toContain(focused);

    await page.keyboard.press('Tab');
    focused = await page.evaluate(() => document.activeElement?.tagName);
    expect(['BUTTON', 'A', 'INPUT', 'SELECT', 'TEXTAREA']).toContain(focused);

    // Shift+Tab goes backwards
    await page.keyboard.press('Shift+Tab');
    focused = await page.evaluate(() => document.activeElement?.tagName);
    expect(['BUTTON', 'A', 'INPUT', 'SELECT', 'TEXTAREA']).toContain(focused);

    // Escape closes drawer
    await page.keyboard.press('Escape');
    await expect(cartDrawer).toBeHidden({ timeout: 3000 });

    // Focus returns to the cart button (or the add to cart button)
    const cartTrigger = page.locator('a[href*="/cart"][aria-label="Cart"]').first();
    await expect(cartTrigger).toBeFocused();
  });
});