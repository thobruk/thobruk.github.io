/**
 * fix-blocks.js
 * Applies targeted regex fixes to content/*.html block markup so Gutenberg
 * does not show "unexpected or invalid content" errors.
 *
 * Fixes applied:
 *   1. Remove plain HTML comments inside block HTML (cause container-block validation failures)
 *   2. Groups with padding:{top:"0",bottom:"0"} — remove spacing attr from block comment
 *      so getSaveContent() generates no inline style (CSS provides section padding instead)
 *   3. ep-card groups — fix margin-bottom:8px → var(--wp--preset--spacing--4)
 *   4. Buttons — move font-size + has-custom-font-size to outer <div>, expand padding shorthand
 *   5. Headings — add has-text-color class, sort style properties alphabetically
 *
 * Usage (from tools/):
 *   node fix-blocks.js [../content]
 */

const fs   = require('fs');
const path = require('path');

const contentDir = process.argv[2] || '../content';
const DRY_RUN    = process.argv.includes('--dry-run');

// ── Helpers ───────────────────────────────────────────────────

function sortStyleProps(styleStr) {
  return styleStr
    .split(';')
    .map(p => p.trim())
    .filter(Boolean)
    .sort((a, b) => a.localeCompare(b))
    .join(';');
}

// ── Fix functions ─────────────────────────────────────────────

/**
 * 1. Remove plain HTML comments (not block comments) from inside block HTML.
 * Block comments look like:  <!-- wp:name ... --> or <!-- /wp:name -->
 * Everything else is a code-organization comment that breaks container validation.
 */
function removePlainHtmlComments(html) {
  return html.replace(/<!--(?!\s*\/?wp:)[^>]*-->/g, '');
}

/**
 * 2. Remove "style":{"spacing":{"padding":{"top":"0","bottom":"0"}}} from
 * group/columns block comment JSON when both top and bottom are "0".
 * This prevents getSaveContent() from generating inline padding:0 which
 * would override the CSS ep-section padding.
 */
function removeZeroPaddingFromBlockComments(html) {
  return html.replace(
    /(<!-- wp:(?:group|columns|column)\s+)(\{.*?\})\s*(\/-->|-->)/gs,
    (match, prefix, attrsJson, suffix) => {
      let attrs;
      try { attrs = JSON.parse(attrsJson); } catch { return match; }

      const padding = attrs?.style?.spacing?.padding;
      if (padding && padding.top === '0' && padding.bottom === '0') {
        delete attrs.style.spacing.padding;
        if (!Object.keys(attrs.style.spacing).length) delete attrs.style.spacing;
        if (!Object.keys(attrs.style).length)         delete attrs.style;
      }

      return `${prefix}${JSON.stringify(attrs)}${suffix === '/-->' ? ' /-->' : ' -->'}`;
    }
  );
}

/**
 * 3. Fix ep-card group inline style: margin-bottom:8px → CSS custom property.
 */
function fixEpCardMargin(html) {
  return html.replace(
    /(<div class="wp-block-group ep-card" style=")margin-bottom:8px;?(")/g,
    '$1margin-bottom:var(--wp--preset--spacing--4)$2'
  );
}

/**
 * 4. Fix button blocks.
 *
 * Before (what we authored):
 *   <div class="wp-block-button [style?]">
 *     <a class="wp-block-button__link wp-element-button [color-classes]"
 *        href="..."
 *        style="font-size:16px;padding:16px 32px;">
 *
 * After (what WP 6.5 save() generates):
 *   <div class="wp-block-button [style?] has-custom-font-size" style="font-size:16px">
 *     <a class="wp-block-button__link [color-classes] wp-element-button"
 *        href="..."
 *        style="padding-top:16px;padding-right:32px;padding-bottom:16px;padding-left:32px">
 */
function fixButtons(html) {
  return html.replace(
    /<div class="(wp-block-button(?!s)[^"]*)">([\s\S]*?)<\/div>/g,
    (match, divClasses, inner) => {
      // Only process buttons where the <a> has both font-size and a padding shorthand
      if (!inner.includes('font-size') || !inner.includes('padding:')) return match;

      const aMatch = inner.match(/<a([\s\S]*?)style="([^"]*)"([\s\S]*?)>([\s\S]*?)<\/a>/);
      if (!aMatch) return match;

      const [, aPreStyle, aStyleStr, aPostStyle, aContent] = aMatch;

      // Parse style props
      const props = {};
      aStyleStr.split(';').map(p => p.trim()).filter(Boolean).forEach(p => {
        const i = p.indexOf(':');
        if (i > 0) props[p.slice(0, i).trim()] = p.slice(i + 1).trim();
      });

      const fontSize = props['font-size'];
      const padding  = props['padding'];
      if (!fontSize && !padding) return match;

      // Expand padding shorthand
      let paddingStyle = '';
      if (padding) {
        const parts = padding.trim().split(/\s+/);
        const t = parts[0], r = parts[1] ?? parts[0],
              b = parts[2] ?? parts[0], l = parts[3] ?? r;
        paddingStyle = `padding-top:${t};padding-right:${r};padding-bottom:${b};padding-left:${l}`;
      }

      // Build new <a> style (padding only, no font-size)
      const remainingProps = Object.entries(props)
        .filter(([k]) => k !== 'font-size' && k !== 'padding')
        .map(([k, v]) => `${k}:${v}`)
        .join(';');
      const newAStyle = [paddingStyle, remainingProps].filter(Boolean).join(';');

      // Promote font-size to outer <div>
      const newDivClasses = fontSize
        ? divClasses.replace(/\s*has-custom-font-size\b/, '').trimEnd() + ' has-custom-font-size'
        : divClasses;
      const divStyleAttr = fontSize ? ` style="font-size:${fontSize}"` : '';
      const aStyleAttr   = newAStyle ? ` style="${newAStyle}"` : '';

      const newA = `<a${aPreStyle}style="${newAStyle}"${aPostStyle}>${aContent.trim()}</a>`;
      return `<div class="${newDivClasses}"${divStyleAttr}>${newA}</div>`;
    }
  );
}

/**
 * 5. Fix heading elements:
 *   - Add has-text-color class when style contains a color: property
 *   - Sort style properties alphabetically, remove trailing semicolons
 */
function fixHeadings(html) {
  return html.replace(
    /<h([1-6])(\s+class=")(wp-block-heading[^"]*)(")\s+style="([^"]*)"/g,
    (match, level, classOpen, classes, classClose, style) => {
      let newClasses = classes;
      if (style.includes('color:') && !classes.includes('has-text-color')) {
        newClasses += ' has-text-color';
      }
      const sortedStyle = sortStyleProps(style);
      return `<h${level}${classOpen}${newClasses}${classClose} style="${sortedStyle}"`;
    }
  );
}

/**
 * 6. Restore layout:constrained on alignfull ep-section group blocks.
 * WP 6.9 drops the layout attribute on save, causing is-layout-flow
 * instead of is-layout-constrained, which leaves content at x=0.
 */
function restoreConstrainedLayout(html) {
  return html.replace(
    /(<!-- wp:group\s+)(\{.*?\})\s*(\/-->|-->)/gs,
    (match, prefix, attrsJson, suffix) => {
      let attrs;
      try { attrs = JSON.parse(attrsJson); } catch { return match; }

      // Only fix alignfull ep-section groups missing layout
      if (
        attrs.align === 'full' &&
        typeof attrs.className === 'string' &&
        attrs.className.includes('ep-section') &&
        !attrs.layout
      ) {
        attrs.layout = { type: 'constrained' };
        const end = suffix.trim().startsWith('/') ? ' /-->' : ' -->';
        return `${prefix}${JSON.stringify(attrs)}${end}`;
      }
      return match;
    }
  );
}

/**
 * 7. Remove dimensions.maxWidth from paragraph block comments.
 * In WP 6.9, this attribute causes a double <p> wrapper every time
 * the page is saved. The max-width is kept in the HTML inline style.
 */
function removeParagraphMaxWidth(html) {
  return html.replace(
    /(<!-- wp:paragraph\s+)(\{.*?\})\s*(\/-->|-->)/gs,
    (match, prefix, attrsJson, suffix) => {
      let attrs;
      try { attrs = JSON.parse(attrsJson); } catch { return match; }

      if (!attrs?.style?.dimensions?.maxWidth) return match;

      delete attrs.style.dimensions.maxWidth;
      if (!Object.keys(attrs.style.dimensions).length) delete attrs.style.dimensions;
      if (!Object.keys(attrs.style).length)            delete attrs.style;

      const end = suffix.trim() === '/->' || suffix.trim() === '/-->' ? ' /-->' : ' -->';
      return `${prefix}${JSON.stringify(attrs)}${end}`;
    }
  );
}

/**
 * 8. Collapse double <p> tags inside paragraph blocks.
 * WP 6.9 wraps <p> content with another <p> from block typography styles,
 * producing <p style="..."><p style="...;max-width:...">text</p></p>.
 * Merge style and class attributes from both <p> tags into one.
 */
function collapseDoubleParagraph(html) {
  const parseStyle = s => {
    const obj = {};
    s.split(';').map(p => p.trim()).filter(Boolean).forEach(p => {
      const i = p.indexOf(':');
      if (i > 0) obj[p.slice(0, i).trim()] = p.slice(i + 1).trim();
    });
    return obj;
  };
  const getAttr  = (attrs, name) => (attrs.match(new RegExp(`${name}="([^"]*)"`) ) || [])[1] || '';
  const dropAttr = (attrs, name) => attrs.replace(new RegExp(`\\s*${name}="[^"]*"`), '').trim();

  return html.replace(
    /<p([^>]*)><p([^>]*)>([\s\S]*?)<\/p><\/p>/g,
    (match, outerAttrs, innerAttrs, content) => {
      // Merge styles — inner wins on conflict
      const merged = { ...parseStyle(getAttr(outerAttrs, 'style')), ...parseStyle(getAttr(innerAttrs, 'style')) };
      const mergedStyle = Object.entries(merged).map(([k, v]) => `${k}:${v}`).join(';');

      // Merge classes — deduplicate
      const classes = [...new Set([
        ...getAttr(outerAttrs, 'class').split(/\s+/),
        ...getAttr(innerAttrs, 'class').split(/\s+/),
      ].filter(Boolean))].join(' ');

      // Remaining attributes (drop style + class from both, join)
      const rest = [
        dropAttr(dropAttr(outerAttrs, 'style'), 'class'),
        dropAttr(dropAttr(innerAttrs, 'style'), 'class'),
      ].filter(Boolean).join(' ').trim();

      const classAttr = classes    ? ` class="${classes}"`   : '';
      const styleAttr = mergedStyle ? ` style="${mergedStyle}"` : '';
      const restStr   = rest        ? ` ${rest}`               : '';
      return `<p${classAttr}${styleAttr}${restStr}>${content}</p>`;
    }
  );
}

/**
 * 9. Collapse duplicate class="..." attributes on the same element.
 * A prior buggy merge produced <p class="a" class="b"> — merge into one.
 */
function fixDuplicateClassAttrs(html) {
  return html.replace(
    /<(\w+)(\s[^>]*?)>/g,
    (match, tag, attrs) => {
      const classes = [];
      const stripped = attrs.replace(/\s*class="([^"]*)"/g, (_, c) => {
        classes.push(...c.split(/\s+/).filter(Boolean));
        return '';
      });
      if (classes.length === 0) return match;
      const merged = [...new Set(classes)].join(' ');
      return `<${tag} class="${merged}"${stripped}>`;
    }
  );
}

// ── Pipeline ──────────────────────────────────────────────────

function fixFile(html) {
  let out = html;
  out = removePlainHtmlComments(out);
  out = removeZeroPaddingFromBlockComments(out);
  out = restoreConstrainedLayout(out);
  out = removeParagraphMaxWidth(out);
  out = collapseDoubleParagraph(out);
  out = fixDuplicateClassAttrs(out);
  out = fixEpCardMargin(out);
  out = fixButtons(out);
  out = fixHeadings(out);
  return out;
}

// ── Main ──────────────────────────────────────────────────────

const files = fs.readdirSync(contentDir).filter(f => f.endsWith('.html')).sort();

for (const file of files) {
  const filePath = path.join(contentDir, file);
  const original = fs.readFileSync(filePath, 'utf8');
  const fixed    = fixFile(original);

  if (fixed === original) {
    console.log(`${file}: no changes`);
    continue;
  }

  if (DRY_RUN) {
    console.log(`${file}: would change (dry-run)`);
  } else {
    fs.writeFileSync(filePath, fixed);
    console.log(`${file}: updated`);
  }
}
