import { defineConfig, devices } from '@playwright/test';

/**
 * Browser checks against a running site. With the docker-webstack:
 *   bin/playwright.sh <path-to-this-project>
 * which picks the Playwright image matching the version installed here.
 */
export default defineConfig({
    testDir: './e2e',
    use: {
        baseURL: process.env.PLAYWRIGHT_BASE_URL ?? 'http://laravel-template.local',
        trace: 'retain-on-failure',
    },
    projects: [{ name: 'chromium', use: { ...devices['Desktop Chrome'] } }],
});
