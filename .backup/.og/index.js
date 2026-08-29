import puppeteer from 'puppeteer';
import yargs from 'yargs';
import { hideBin } from 'yargs/helpers';
import path from 'path';
import template from "./template.js";

const argv = yargs(hideBin(process.argv)).argv;
const filepath = path.resolve(argv.path);
const confetti = argv.confetti || false;
const content = argv._[0];

(async () => {
  const browser = await puppeteer.launch({
    headless: "new",
  });
  const page = await browser.newPage();

  await page.setViewport({ width: 2048, height: 1170 });
  await page.setContent(template(content, confetti));
  await page.waitForNetworkIdle();

  await page.screenshot({
    type: 'png',
    path: filepath,
  });

  await browser.close();
})();
