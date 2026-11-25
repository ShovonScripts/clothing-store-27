import { chromium } from 'playwright';
import fs from 'fs';

async function measurePage(url, name) {
  const browser = await chromium.launch({ headless: true });
  const page = await browser.newPage();
  
  const resources = [];
  page.on('response', response => {
    const url = response.url();
    if (url.match(/\.js($|\?)/) || url.includes('livewire') || url.includes('alpine')) {
      const headers = response.headers();
      resources.push({
        url,
        contentType: headers['content-type'],
        contentEncoding: headers['content-encoding'],
        contentLength: headers['content-length'],
        status: response.status()
      });
    }
  });

  const consoleMessages = [];
  page.on('console', msg => {
    if (msg.text().toLowerCase().includes('alpine')) {
      consoleMessages.push({ type: msg.type(), text: msg.text() });
    }
  });

  await page.goto(url, { waitUntil: 'networkidle' });
  
  // Get performance entries for JS resources
  const entries = await page.evaluate(() => {
    return performance.getEntriesByType('resource')
      .filter(r => r.initiatorType === 'script' || r.name.match(/\.js($|\?)/) || r.name.includes('livewire') || r.name.includes('alpine'))
      .map(r => ({
        name: r.name,
        transferSize: r.transferSize,
        encodedBodySize: r.encodedBodySize,
        decodedBodySize: r.decodedBodySize,
        initiatorType: r.initiatorType
      }));
  });

  // Check Alpine init count
  const alpineInitCount = await page.evaluate(() => window.__alpineInitCount || 0);

  await browser.close();

  return { name, url, entries, consoleMessages, alpineInitCount };
}

async function main() {
  const baseUrl = 'http://127.0.0.1:8123';
  const pages = [
    { url: `${baseUrl}/`, name: 'Home' },
    { url: `${baseUrl}/shop`, name: 'Shop' },
    { url: `${baseUrl}/product/classic-cotton-crew-neck-t-shirt`, name: 'Product' },
    { url: `${baseUrl}/search?q=shirt`, name: 'Search' },
    { url: `${baseUrl}/categories`, name: 'Categories' },
    { url: `${baseUrl}/page/about`, name: 'Page' },
  ];

  const results = [];
  for (const p of pages) {
    console.log(`Measuring ${p.name}...`);
    try {
      const r = await measurePage(p.url, p.name);
      results.push(r);
      console.log(`  Alpine init count: ${r.alpineInitCount}`);
      console.log(`  JS resources: ${r.entries.length}`);
      r.entries.forEach(e => {
        console.log(`    ${e.name}: transfer=${e.transferSize} encoded=${e.encodedBodySize} decoded=${e.decodedBodySize}`);
      });
      if (r.consoleMessages.length) {
        console.log('  Console:');
        r.consoleMessages.forEach(c => console.log(`    [${c.type}] ${c.text}`));
      }
    } catch (e) {
      console.error(`  ERROR: ${e.message}`);
    }
  }

  // Write results
  fs.writeFileSync('performance-results.json', JSON.stringify(results, null, 2));
  console.log('\nResults written to performance-results.json');
}

main().catch(console.error);