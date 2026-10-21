import { expect, test } from '@playwright/test';

/**
 * Smoke test for a freshly set-up project. Changes nothing but the browser session.
 * The sign-in case needs the first administrator's details:
 *   PLAYWRIGHT_ADMIN_EMAIL=... PLAYWRIGHT_ADMIN_PASSWORD=... bin/playwright.sh <path>
 */
test('the sign-in page loads in Malay', async ({ page }) => {
    await page.goto('/login');

    await expect(page.getByRole('button', { name: 'Log Masuk' })).toBeVisible();
});

test('the administrator signs in and sees the admin menu', async ({ page }) => {
    const email = process.env.PLAYWRIGHT_ADMIN_EMAIL;
    const password = process.env.PLAYWRIGHT_ADMIN_PASSWORD;
    test.skip(!email || !password, 'Set PLAYWRIGHT_ADMIN_EMAIL and PLAYWRIGHT_ADMIN_PASSWORD to run this case.');

    await page.goto('/login');
    await page.getByLabel('E-mel').fill(email!);
    await page.getByLabel('Kata laluan').fill(password!);
    await page.getByRole('button', { name: 'Log Masuk' }).click();

    await expect(page).toHaveURL(/\/$/);
    await expect(page.locator('#app-sidebar')).toContainText('Pentadbiran');
});
