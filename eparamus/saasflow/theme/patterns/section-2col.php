<?php
/**
 * Title: Section — 2 Column
 * Slug: eparamus/section-2col
 * Categories: eparamus-sections
 * Description: Two-column section: label + heading + copy on the left, content (copy or stacked cards) on the right.
 * Keywords: two column, 2col, split, content
 * Viewport Width: 1280
 */
?>

<!-- wp:group {
  "align":"full",
  "className":"ep-section",
  "style":{"spacing":{"padding":{"top":"0","bottom":"0"}}},
  "layout":{"type":"constrained"}
} -->
<div class="wp-block-group alignfull ep-section">

  <!-- wp:columns {
    "isStackedOnMobile":true,
    "style":{
      "spacing":{
        "blockGap":{"left":"var:preset|spacing|32"}
      }
    }
  } -->
  <div class="wp-block-columns">

    <!-- Left column: heading side -->
    <!-- wp:column {"verticalAlignment":"center"} -->
    <div class="wp-block-column is-vertically-aligned-center">

      <!-- wp:paragraph {"className":"ep-label"} -->
      <p class="ep-label">Section label</p>
      <!-- /wp:paragraph -->

      <!-- wp:heading {
        "level":2,
        "style":{"typography":{"letterSpacing":"-0.8px"}}
      } -->
      <h2 class="wp-block-heading" style="letter-spacing:-0.8px;">
        Section Headline<br>Goes Here
      </h2>
      <!-- /wp:heading -->

      <!-- wp:paragraph -->
      <p>Supporting copy for the left column. One or two paragraphs that expand on the headline.</p>
      <!-- /wp:paragraph -->

    </div>
    <!-- /wp:column -->

    <!-- Right column: content side (copy or stacked cards) -->
    <!-- wp:column {"verticalAlignment":"top"} -->
    <div class="wp-block-column is-vertically-aligned-top">

      <!-- wp:group {"className":"ep-card","style":{"spacing":{"margin":{"bottom":"var:preset|spacing|4"}}}} -->
      <div class="wp-block-group ep-card" style="margin-bottom:8px;">
        <!-- wp:heading {"level":5} -->
        <h5 class="wp-block-heading">Card Title</h5>
        <!-- /wp:heading -->
        <!-- wp:paragraph {"style":{"typography":{"fontSize":"15px"}}} -->
        <p style="font-size:15px;">Card body copy. One or two sentences.</p>
        <!-- /wp:paragraph -->
      </div>
      <!-- /wp:group -->

      <!-- wp:group {"className":"ep-card","style":{"spacing":{"margin":{"bottom":"var:preset|spacing|4"}}}} -->
      <div class="wp-block-group ep-card" style="margin-bottom:8px;">
        <!-- wp:heading {"level":5} -->
        <h5 class="wp-block-heading">Card Title</h5>
        <!-- /wp:heading -->
        <!-- wp:paragraph {"style":{"typography":{"fontSize":"15px"}}} -->
        <p style="font-size:15px;">Card body copy. One or two sentences.</p>
        <!-- /wp:paragraph -->
      </div>
      <!-- /wp:group -->

      <!-- wp:group {"className":"ep-card","style":{"spacing":{"margin":{"bottom":"var:preset|spacing|4"}}}} -->
      <div class="wp-block-group ep-card" style="margin-bottom:8px;">
        <!-- wp:heading {"level":5} -->
        <h5 class="wp-block-heading">Card Title</h5>
        <!-- /wp:heading -->
        <!-- wp:paragraph {"style":{"typography":{"fontSize":"15px"}}} -->
        <p style="font-size:15px;">Card body copy. One or two sentences.</p>
        <!-- /wp:paragraph -->
      </div>
      <!-- /wp:group -->

      <!-- wp:group {"className":"ep-card"} -->
      <div class="wp-block-group ep-card">
        <!-- wp:heading {"level":5} -->
        <h5 class="wp-block-heading">Card Title</h5>
        <!-- /wp:heading -->
        <!-- wp:paragraph {"style":{"typography":{"fontSize":"15px"}}} -->
        <p style="font-size:15px;">Card body copy. One or two sentences.</p>
        <!-- /wp:paragraph -->
      </div>
      <!-- /wp:group -->

    </div>
    <!-- /wp:column -->

  </div>
  <!-- /wp:columns -->

</div>
<!-- /wp:group -->
