const puppeteer = require('puppeteer');

(async () => {
    try {
        const browserURL = 'http://127.0.0.1:9222';
        const browser = await puppeteer.connect({ browserURL });
        const pages = await browser.pages();
        let targetPage = null;
        for (const page of pages) {
            const url = page.url();
            if (url.includes('travelsuite.evaneos.com')) {
                targetPage = page;
                break;
            }
        }
        
        if (!targetPage) {
            console.log("Could not find Evaneos page");
            process.exit(1);
        }

        console.log("Connected to Evaneos page:", targetPage.url());
        
        // Ensure Accommodations is selected
        // Evaluate and extract all hotel cards
        const hotels = await targetPage.evaluate(() => {
            const items = [];
            // This selector depends on the UI, let's just grab all images and nearby text
            // Or look for typical card structures
            const cards = document.querySelectorAll('div[data-testid="service-card"], div.MuiPaper-root, div[class*="Card"]'); // Try generic
            if (cards.length === 0) {
                // let's try to get all images and their nearest text
                const images = document.querySelectorAll('img');
                for (const img of images) {
                    items.push({
                        src: img.src,
                        alt: img.alt,
                        parentText: img.parentElement ? img.parentElement.innerText.substring(0, 100).replace(/\n/g, ' ') : ''
                    });
                }
                return { type: 'images', items };
            }
            return { type: 'cards', length: cards.length };
        });
        
        console.log(JSON.stringify(hotels, null, 2));
        
        // Wait, maybe we can just get the HTML and parse it?
        const html = await targetPage.content();
        const fs = require('fs');
        fs.writeFileSync('page.html', html);
        console.log("Saved page HTML to page.html");
        
        browser.disconnect();
    } catch (e) {
        console.error(e);
    }
})();
