/**
 * validate-blocks.js
 * Parses all content/*.html files, runs each block through @wordpress/blocks
 * isValid(), and writes a report to /tmp/block-validation-report.json
 *
 * Usage: node validate-blocks.js ../content
 * stdout/stderr are suppressed during registration — redirect output to /dev/null:
 *   node validate-blocks.js ../content > /dev/null 2>&1
 */

const { JSDOM } = require('jsdom');
const fs = require('fs');
const path = require('path');

// --- Browser globals needed by @wordpress packages ---
const dom = new JSDOM('<!DOCTYPE html>');
global.window            = dom.window;
global.document          = dom.window.document;
global.MutationObserver  = dom.window.MutationObserver;
global.requestAnimationFrame  = cb => setTimeout(cb, 0);
global.cancelAnimationFrame   = clearTimeout;
global.requestIdleCallback    = cb => setTimeout(cb, 0);
global.cancelIdleCallback     = clearTimeout;

// Suppress all output during block registration (the packages log heavily)
const origStdoutWrite = process.stdout.write.bind(process.stdout);
const origStderrWrite = process.stderr.write.bind(process.stderr);
const origConsole = { log: console.log, warn: console.warn, error: console.error, info: console.info };
process.stdout.write = () => true;
process.stderr.write = () => true;
Object.assign(console, { log: ()=>{}, warn: ()=>{}, error: ()=>{}, info: ()=>{} });

const { registerCoreBlocks } = require('@wordpress/block-library');
registerCoreBlocks();

// Restore output
process.stdout.write = origStdoutWrite;
process.stderr.write = origStderrWrite;
Object.assign(console, origConsole);

const { parse, getBlockType, getSaveContent, isValid } = require('@wordpress/blocks');

// --- Parse and validate ---
const contentDir = process.argv[2] || '../content';
const files = fs.readdirSync(contentDir).filter(f => f.endsWith('.html')).sort();

const report = {};

function walkBlocks(blocks, results, depth = 0) {
  for (const block of blocks) {
    if (!block.name) continue; // whitespace freeform nodes

    const blockType = getBlockType(block.name);
    const entry = {
      name:       block.name,
      depth,
      isValid:    block.isValid,
      clientId:   block.clientId,
    };

    if (block.isValid === false && blockType) {
      // Generate what save() would produce vs what's stored
      try {
        const expected = getSaveContent(blockType, block.attributes);
        entry.expected = expected.trim();
        entry.actual   = block.originalContent ? block.originalContent.trim() : '(none)';
      } catch (e) {
        entry.saveError = e.message;
      }
    }

    if (!block.isValid) {
      results.push(entry);
    }

    // Recurse into inner blocks
    if (block.innerBlocks && block.innerBlocks.length) {
      walkBlocks(block.innerBlocks, results, depth + 1);
    }
  }
}

for (const file of files) {
  const html = fs.readFileSync(path.join(contentDir, file), 'utf8');
  const blocks = parse(html);

  const invalid = [];
  walkBlocks(blocks, invalid);

  report[file] = {
    totalTopLevel: blocks.length,
    invalidCount:  invalid.length,
    invalid,
  };
}

fs.writeFileSync('/tmp/block-validation-report.json', JSON.stringify(report, null, 2));
console.log('Report written to /tmp/block-validation-report.json');
