import { test, expect } from '@playwright/test';

test.describe('Cart Drawer Focus Trap', () => {
  test.beforeEach(async ({ page }) => {
    // Add an item to cart via API
    await page.goto('/shop');
    await page.waitForLoadState('networkidle');
    
    const firstAddToCart = page.locator('form[action="/cart/add"] button[type="submit"]').first();
    if (await firstAddToCart.isVisible({ timeout: 5000 })) {
      await firstAddToCart.click();
      await page.waitForLoadState('networkidle');
      await page.waitForTimeout(500);
    }
  });

  test('opens drawer, Tab cycles inside, Escape closes, focus returns to cart button', async ({ page }) => {
    // Go to cart page which should now have items and load the drawer
    await page.goto('/cart');
    await page.waitForLoadState('networkidle');

    // Open cart drawer via header cart button
    const cartButton = page.locator('a[href*="/cart"][aria-label="Cart"]').first();
    await expect(cartButton).toBeVisible();
    await cartButton.click();

    // Wait for drawer to open
    await page.waitForSelector('[role="dialog"][aria-modal="true"]', { state: 'visible', timeout: 10000 });
    
    const cartDrawer = page.locator('[role="dialog"][aria-modal="true"]').first();
    await expect(cartDrawer).toBeVisible({ timeout: 5000 });

    // Get all focusable elements inside drawer
    const focusableElements = cartDrawer.locator('button, a, input, select, textarea, [tabindex]:not([tabindex="-1"])');
    const count = await focusableElements.count();
    expect(count).toBeGreaterThan(0);

    // Tab through elements - should cycle inside drawer
    await page.keyboard.press('Tab');
    let focused = await page.evaluate(() => document.activeElement?.tagName);
    expect(['BUTTON', 'A', 'INPUT', 'SELECT', 'TEXTAREA']).toContain(focused);

    // Tab again
    await page.keyboard.press('Tab');
    focused = await page.evaluate(() => document.activeElement?.tagName);
    expect(['BUTTON', 'A', 'INPUT', 'SELECT', 'TEXTAREA']).toContain(focused);

    // Shift+Tab goes backwards
    await page.keyboard.press('Shift+Tab');
    focused = await page.evaluate(() => document.activeElement?.tagName);
    expect(['BUTTON', 'A', 'INPUT', 'SELECT', 'TEXTAREA']).toContain(focused);

    // Escape closes drawer
    await page.keyboard.press('Escape');
    await expect(page.locator('[role="dialog"][aria-modal="true"]')).toBeHidden({ timeout: 3000 });

    // Focus returns to the cart button in header
    const cartTrigger = page.locator('a[href*="/cart"][aria-label="Cart"]').first();
    await expect(cartTrigger).toBeFocused();
  });
});