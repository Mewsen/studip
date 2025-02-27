import { test, expect } from '@playwright/test';

const autorFile = 'tests/e2e/.auth/autor.json';

test.describe('Visiting my Arbeitsplatz @autor @courseware', () => {
    test.use({ storageState: autorFile });

    test('should let us create a new Lernmaterial', async ({ page }) => {
        await page.goto('dispatch.php/contents/courseware/index');
        await page.getByRole('button', { name: 'Lernmaterial hinzufügen' }).click();
        await page.getByRole('button', { name: 'Neu erstellen' }).click();
        await page.getByLabel('Titel des Lernmaterials*').fill('Ein Titel');
        await page.getByLabel('Beschreibung*').fill('Eine Beschreibung');
        await page.getByRole('button', { name: 'Erstellen', exact: true }).click();
        const lernmaterial = await page.getByRole('link', { name: 'Ein Titel Eine Beschreibung' }).last();
        await expect(lernmaterial).toBeVisible();
    });
});
