<?php
/**
 * Title: Section — Contrast Block
 * Slug: eparamus/section-contrast-block
 * Categories: eparamus-sections
 * Description: Side-by-side comparison panel: muted left column (traditional/old) vs active right column (IMPACT/new).
 * Keywords: contrast, comparison, traditional, vs
 * Viewport Width: 1280
 */
?>

<!-- wp:group {
  "align":"full",
  "className":"ep-section ep-section--gray",
  "style":{"spacing":{"padding":{"top":"0","bottom":"0"}}},
  "layout":{"type":"constrained"}
} -->
<div class="wp-block-group alignfull ep-section ep-section--gray">

  <!-- Section header -->
  <!-- wp:group {
    "layout":{
      "type":"constrained",
      "contentSize":"680px",
      "justifyContent":"center"
    }
  } -->
  <div class="wp-block-group">

    <!-- wp:paragraph {"className":"ep-label","style":{"text":{"textAlign":"center"}}} -->
    <p class="ep-label" style="text-align:center;">Section label</p>
    <!-- /wp:paragraph -->

    <!-- wp:heading {
      "level":2,
      "textAlign":"center",
      "style":{"typography":{"letterSpacing":"-0.8px"}}
    } -->
    <h2 class="wp-block-heading has-text-align-center" style="letter-spacing:-0.8px;">
      Section Headline Goes Here
    </h2>
    <!-- /wp:heading -->

  </div>
  <!-- /wp:group -->

  <!-- Contrast block -->
  <!-- wp:columns {
    "className":"ep-contrast",
    "isStackedOnMobile":true,
    "style":{"spacing":{"blockGap":"0"}}
  } -->
  <div class="wp-block-columns ep-contrast">

    <!-- Left: muted (traditional) -->
    <!-- wp:column {"className":"ep-contrast__side ep-contrast__side--left"} -->
    <div class="wp-block-column ep-contrast__side ep-contrast__side--left">

      <!-- wp:heading {"level":6,"className":"ep-contrast__heading"} -->
      <h6 class="wp-block-heading ep-contrast__heading">Traditional</h6>
      <!-- /wp:heading -->

      <!-- wp:html -->
      <div class="ep-contrast__item ep-contrast__item--muted"><span class="ep-contrast__dot"></span>Item one</div>
      <div class="ep-contrast__item ep-contrast__item--muted"><span class="ep-contrast__dot"></span>Item two</div>
      <div class="ep-contrast__item ep-contrast__item--muted"><span class="ep-contrast__dot"></span>Item three</div>
      <div class="ep-contrast__item ep-contrast__item--muted"><span class="ep-contrast__dot"></span>Item four</div>
      <!-- /wp:html -->

    </div>
    <!-- /wp:column -->

    <!-- Right: active (IMPACT) -->
    <!-- wp:column {"className":"ep-contrast__side ep-contrast__side--right"} -->
    <div class="wp-block-column ep-contrast__side ep-contrast__side--right">

      <!-- wp:heading {"level":6,"className":"ep-contrast__heading"} -->
      <h6 class="wp-block-heading ep-contrast__heading">IMPACT</h6>
      <!-- /wp:heading -->

      <!-- wp:html -->
      <div class="ep-contrast__item ep-contrast__item--active"><span class="ep-contrast__dot"></span>Item one</div>
      <div class="ep-contrast__item ep-contrast__item--active"><span class="ep-contrast__dot"></span>Item two</div>
      <div class="ep-contrast__item ep-contrast__item--active"><span class="ep-contrast__dot"></span>Item three</div>
      <div class="ep-contrast__item ep-contrast__item--active"><span class="ep-contrast__dot"></span>Item four</div>
      <!-- /wp:html -->

    </div>
    <!-- /wp:column -->

  </div>
  <!-- /wp:columns -->

  <!-- Closing statement -->
  <!-- wp:paragraph {
    "textAlign":"center",
    "style":{
      "typography":{"fontSize":"15px","fontWeight":"500"},
      "color":{"text":"#40454f"}
    }
  } -->
  <p class="has-text-align-center" style="font-size:15px;font-weight:500;color:#40454f;">
    Closing statement goes here.
  </p>
  <!-- /wp:paragraph -->

</div>
<!-- /wp:group -->
