import { test, expect } from '@playwright/test';

const dozentFile = 'tests/e2e/.auth/dozent.json';

test.describe('Logging Out', () => {
    test.use({ storageState: dozentFile });

    test('should take us back to the homepage', async ({ page, baseURL }) => {
        await page.goto(baseURL);
        await expect(page.locator('#notification-wrapper')).toBeVisible();
        await page.getByLabel('Profilmenü').click();
        await page.getByText('Logout').click();
        await expect(page).toHaveURL(/dispatch\.php\/login/);
    });
});
