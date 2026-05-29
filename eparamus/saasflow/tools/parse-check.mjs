import { createRequire } from 'module';
import { JSDOM } from 'jsdom';
import { readFileSync, readdirSync, writeFileSync } from 'fs';
import { join } from 'path';

// Silence everything
const noop = () => true;
process.stdout.write = noop;
process.stderr.write = noop;
const _log = console.log; console.log = noop;
const _warn = console.warn; console.warn = noop;
const _error = console.error; console.error = noop;

const dom = new JSDOM('<!DOCTYPE html>');
Object.assign(global, {
  window: dom.window, document: dom.window.document,
  navigator: dom.window.navigator,
  MutationObserver: dom.window.MutationObserver,
  requestAnimationFrame: cb => setTimeout(cb,0),
  cancelAnimationFrame: clearTimeout,
  requestIdleCallback: cb => setTimeout(cb,0),
  cancelIdleCallback: clearTimeout,
});

const { registerCoreBlocks } = await import('@wordpress/block-library');
registerCoreBlocks();

const { parse, serialize } = await import('@wordpress/blocks');

process.stdout.write = process.stdout.constructor.prototype.write.bind(process.stdout);
process.stderr.write = process.stderr.constructor.prototype.write.bind(process.stderr);
console.log = _log; console.warn = _warn; console.error = _error;

const contentDir = '../content';
const files = readdirSync(contentDir).filter(f => f.endsWith('.html')).sort();

for (const file of files) {
  const html = readFileSync(join(contentDir, file), 'utf8');
  const blocks = parse(html);
  const invalid = blocks.filter(b => b.isValid === false);
  console.log(file + ': ' + blocks.length + ' blocks — ' +
    (invalid.length ? invalid.length + ' INVALID: ' + invalid.map(b=>b.name).join(', ') : 'all valid'));
}
