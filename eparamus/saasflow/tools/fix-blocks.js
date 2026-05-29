/**
 * fix-blocks.js
 * Reads content/*.html files, finds invalid blocks, and repairs them by:
 *  - Leaf blocks (heading, paragraph, button, buttons): replace innerHTML with
 *    what getSaveContent() generates from the stored block attributes.
 *  - Container blocks (group, columns, column): patch only the outer element's
 *    attributes (class + style) from save(), leaving inner content untouched.
 *
 * Usage (from tools/):
 *   node fix-blocks.js ../content > /dev/null 2>&1
 */

const { JSDOM } = require('jsdom');
const fs   = require('fs');
const path = require('path');

// --- Browser globals ---
const dom = new JSDOM('<!DOCTYPE html>');
global.window            = dom.window;
global.document          = dom.window.document;
global.MutationObserver  = dom.window.MutationObserver;
global.requestAnimationFrame  = cb => setTimeout(cb, 0);
global.cancelAnimationFrame   = clearTimeout;
global.requestIdleCallback    = cb => setTimeout(cb, 0);
global.cancelIdleCallback     = clearTimeout;

// Suppress registration noise
const noop = () => true;
const origStdout = process.stdout.write.bind(process.stdout);
const origStderr = process.stderr.write.bind(process.stderr);
const origCL = { log: console.log, warn: console.warn, error: console.error, info: console.info };
process.stdout.write = noop;
process.stderr.write = noop;
Object.assign(console, { log: noop, warn: noop, error: noop, info: noop });

const { registerCoreBlocks } = require('@wordpress/block-library');
registerCoreBlocks();

process.stdout.write = origStdout;
process.stderr.write = origStderr;
Object.assign(console, origCL);

const { parse, serialize, getBlockType, getSaveContent } = require('@wordpress/blocks');

// Blocks where we replace the FULL html from save() (no inner blocks)
const LEAF_BLOCKS = new Set([
  'core/heading', 'core/paragraph', 'core/button', 'core/buttons',
  'core/image', 'core/separator',
]);

// Blocks where we patch only the OUTER element attributes, not inner content
const CONTAINER_BLOCKS = new Set([
  'core/group', 'core/columns', 'core/column', 'core/cover',
]);

/**
 * Parse outer tag attributes from an HTML string like:
 *   <div class="foo" style="bar">...</div>
 * Returns { tag, attrs: Map<string,string>, selfClosing }
 */
function parseOuterTag(html) {
  const m = html.match(/^<(\w+)([^>]*)>/s);
  if (!m) return null;
  const tag   = m[1];
  const attrs = new Map();
  const attrStr = m[2];
  for (const a of attrStr.matchAll(/(\w[\w-]*)(?:="([^"]*)")?/g)) {
    attrs.set(a[1], a[2] ?? '');
  }
  return { tag, attrs };
}

/**
 * Rebuild outer opening tag with attributes from save() output but keep
 * whatever is INSIDE the original innerHTML.
 */
function patchOuterElement(originalHtml, saveHtml) {
  const savedTag = parseOuterTag(saveHtml.trim());
  if (!savedTag) return originalHtml;

  // Build new attribute string from save() version
  const attrParts = [];
  for (const [k, v] of savedTag.attrs) {
    attrParts.push(v ? `${k}="${v}"` : k);
  }
  const newOpen = `<${savedTag.tag}${attrParts.length ? ' ' + attrParts.join(' ') : ''}>`;

  // Find the matching close tag in original and keep everything in between
  const origMatch = originalHtml.match(/^<[^>]+>([\s\S]*)<\/\w+>\s*$/s);
  const innerContent = origMatch ? origMatch[1] : '';

  return `${newOpen}${innerContent}</${savedTag.tag}>`;
}

/**
 * Walk a block tree and fix invalid blocks in-place.
 * Returns { html, fixed } where html is the corrected block HTML.
 */
function fixBlock(block) {
  if (!block.name) return { html: block.originalContent || '', fixed: 0 };

  const blockType = getBlockType(block.name);
  let fixed = 0;
  let html  = block.originalContent || '';

  // Fix inner blocks first (recurse)
  if (block.innerBlocks && block.innerBlocks.length) {
    for (const inner of block.innerBlocks) {
      const { html: innerFixed, fixed: innerCount } = fixBlock(inner);
      if (innerCount > 0) {
        html = html.replace(inner.originalContent || '', innerFixed);
        fixed += innerCount;
      }
    }
  }

  // Fix this block if invalid
  if (block.isValid === false && blockType) {
    try {
      const saveHtml = getSaveContent(blockType, block.attributes).trim();

      if (LEAF_BLOCKS.has(block.name)) {
        // Replace entirely with save() output
        html = saveHtml;
        fixed++;
      } else if (CONTAINER_BLOCKS.has(block.name)) {
        // Patch only the outer element's attributes
        html = patchOuterElement(html, saveHtml);
        fixed++;
      }
    } catch (e) {
      // Can't fix — leave as-is
    }
  }

  return { html, fixed };
}

// ── Main ──────────────────────────────────────────────────────

const contentDir = process.argv[2] || '../content';
const files = fs.readdirSync(contentDir).filter(f => f.endsWith('.html')).sort();
const results = [];

for (const file of files) {
  const filePath = path.join(contentDir, file);
  const original = fs.readFileSync(filePath, 'utf8');
  const blocks   = parse(original);

  let output    = original;
  let totalFixed = 0;

  for (const block of blocks) {
    const { html: fixedHtml, fixed } = fixBlock(block);
    if (fixed > 0) {
      output = output.replace(block.originalContent || '', fixedHtml);
      totalFixed += fixed;
    }
  }

  if (totalFixed > 0) {
    fs.writeFileSync(filePath, output);
  }

  results.push({ file, fixed: totalFixed });
  console.log(`${file}: fixed ${totalFixed} block(s)`);
}

// Verify by re-parsing
console.log('\n--- Verification ---');
let totalRemaining = 0;
for (const { file } of results) {
  const html = fs.readFileSync(path.join(contentDir, file), 'utf8');
  const blocks = parse(html);
  const invalid = [];
  function countInvalid(bs) {
    for (const b of bs) {
      if (b.name && b.isValid === false) invalid.push(b.name);
      if (b.innerBlocks) countInvalid(b.innerBlocks);
    }
  }
  countInvalid(blocks);
  totalRemaining += invalid.length;
  if (invalid.length) {
    console.log(`${file}: ${invalid.length} still invalid — ${[...new Set(invalid)].join(', ')}`);
  } else {
    console.log(`${file}: OK`);
  }
}
console.log(`\nTotal remaining invalid: ${totalRemaining}`);
