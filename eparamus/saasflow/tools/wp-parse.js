const { JSDOM } = require('jsdom');
const dom = new JSDOM('<!DOCTYPE html>');
global.window = dom.window;
global.document = dom.window.document;
global.navigator = dom.window.navigator;
global.MutationObserver = dom.window.MutationObserver;
global.requestAnimationFrame = (cb) => setTimeout(cb, 0);
global.cancelAnimationFrame = clearTimeout;
global.requestIdleCallback = (cb) => setTimeout(cb, 0);
global.cancelIdleCallback = clearTimeout;

// Suppress all stdout/stderr during registration
const origStdout = process.stdout.write.bind(process.stdout);
const origStderr = process.stderr.write.bind(process.stderr);
process.stdout.write = () => true;
process.stderr.write = () => true;

const { registerCoreBlocks } = require('@wordpress/block-library');
registerCoreBlocks();

process.stdout.write = origStdout;
process.stderr.write = origStderr;

const { parse, serialize } = require('@wordpress/blocks');
const fs = require('fs');
const path = require('path');

const contentDir = process.argv[2];
const files = fs.readdirSync(contentDir).filter(f => f.endsWith('.html'));

for (const file of files) {
  const html = fs.readFileSync(path.join(contentDir, file), 'utf8');
  const blocks = parse(html);
  const invalid = blocks.filter(b => b.isValid === false);
  if (invalid.length > 0) {
    console.log(`${file}: ${blocks.length} blocks, ${invalid.length} INVALID`);
    invalid.forEach(b => console.log(`  - ${b.name}`));
  } else {
    console.log(`${file}: ${blocks.length} blocks, all valid`);
  }
}
