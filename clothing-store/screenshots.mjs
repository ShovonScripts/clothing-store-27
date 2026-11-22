import { chromium } from 'playwright';
import fs from 'fs';
import zlib from 'zlib';

(async () => {
    const browser = await chromium.launch({ headless: true });
    const results = [];

    const viewports = [
        { name: 'mobile', width: 360, height: 640 },
        { name: 'desktop', width: 1280, height: 720 }
    ];

    const pages = [
        { name: 'home', url: 'http://127.0.0.1:8000' },
        { name: 'category', url: 'http://127.0.0.1:8000/shop' },
        { name: 'product', url: 'http://127.0.0.1:8000/product/classic-cotton-crew-neck-t-shirt' },
        { name: 'search', url: 'http://127.0.0.1:8000/search?q=shirt' }
    ];

    for (const pageInfo of pages) {
        for (const viewport of viewports) {
            const context = await browser.newContext({
                viewport: { width: viewport.width, height: viewport.height },
                userAgent: 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
            });
            const page = await context.newPage();

            // Capture full HTML after page load
            await page.goto(pageInfo.url, { waitUntil: 'networkidle', timeout: 30000 });
            await page.waitForTimeout(2000);

            // Get the full HTML content
            const htmlContent = await page.content();

            // Measure gzipped HTML size
            const gzippedHtml = zlib.gzipSync(Buffer.from(htmlContent));
            const htmlSizeGzipped = gzippedHtml.length;

            // Find LCP candidate (largest image in viewport)
            const lcpInfo = await page.evaluate(() => {
                const images = Array.from(document.querySelectorAll('img'));
                let lcpImage = null;
                let maxArea = 0;
                
                for (const img of images) {
                    const rect = img.getBoundingClientRect();
                    const area = rect.width * rect.height;
                    if (area > maxArea && rect.width > 0 && rect.height > 0) {
                        maxArea = area;
                        lcpImage = {
                            src: img.src,
                            width: rect.width,
                            height: rect.height,
                            naturalWidth: img.naturalWidth,
                            naturalHeight: img.naturalHeight
                        };
                    }
                }
                return lcpImage;
            });

            // Take screenshot
            const screenshotPath = `storage/app/public/screenshots/${pageInfo.name}-${viewport.name}.png`;
            await page.screenshot({ path: screenshotPath, fullPage: true });

            results.push({
                page: pageInfo.name,
                viewport: viewport.name,
                width: viewport.width,
                height: viewport.height,
                htmlSizeGzipped,
                screenshot: screenshotPath,
                lcp: lcpInfo
            });

            console.log(`${pageInfo.name} (${viewport.name}): HTML gzipped = ${htmlSizeGzipped} bytes, LCP = ${lcpInfo ? lcpInfo.src : 'none'}`);

            await context.close();
        }
    }

    await browser.close();

    // Save results
    fs.writeFileSync('storage/app/public/screenshots/results.json', JSON.stringify(results, null, 2));
    console.log('\nResults saved to storage/app/public/screenshots/results.json');
})();