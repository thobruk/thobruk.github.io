<?php
/**
 * Title: Section — 4 Cards
 * Slug: eparamus/section-4-cards
 * Categories: eparamus-sections
 * Description: Centred section header followed by a 4-column card grid. Each card has an icon area, heading, body, and optional link.
 * Keywords: cards, grid, features
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

  <!-- Section header -->
  <!-- wp:group {
    "style":{"spacing":{"blockGap":"var:preset|spacing|4"}},
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

    <!-- wp:paragraph {
      "textAlign":"center",
      "style":{"typography":{"fontSize":"clamp(16px, 2vw, 20px)","lineHeight":"1.6"}}
    } -->
    <p class="has-text-align-center" style="font-size:clamp(16px, 2vw, 20px);line-height:1.6;">
      Supporting paragraph that introduces the cards below. One or two sentences.
    </p>
    <!-- /wp:paragraph -->

  </div>
  <!-- /wp:group -->

  <!-- 4-column card grid -->
  <!-- wp:columns {
    "className":"ep-grid-4",
    "isStackedOnMobile":true,
    "style":{"spacing":{"blockGap":"var:preset|spacing|4"}}
  } -->
  <div class="wp-block-columns ep-grid-4">

    <!-- Card 1 -->
    <!-- wp:column -->
    <div class="wp-block-column">
      <!-- wp:group {"className":"ep-card","layout":{"type":"flex","orientation":"vertical","justifyContent":"left"}} -->
      <div class="wp-block-group ep-card">

        <!-- wp:html -->
        <div class="ep-card__icon">
          <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" width="20" height="20"><rect x="2" y="3" width="16" height="14" rx="2"/><path d="M6 7h8M6 11h5"/></svg>
        </div>
        <!-- /wp:html -->

        <!-- wp:heading {"level":5} -->
        <h5 class="wp-block-heading">Card Title</h5>
        <!-- /wp:heading -->

        <!-- wp:paragraph {"style":{"typography":{"fontSize":"15px","lineHeight":"1.6"}}} -->
        <p style="font-size:15px;line-height:1.6;">Card body copy goes here. Keep it to two or three sentences.</p>
        <!-- /wp:paragraph -->

        <!-- wp:paragraph {"className":"ep-card__link"} -->
        <p class="ep-card__link"><a href="#">Explore →</a></p>
        <!-- /wp:paragraph -->

      </div>
      <!-- /wp:group -->
    </div>
    <!-- /wp:column -->

    <!-- Card 2 -->
    <!-- wp:column -->
    <div class="wp-block-column">
      <!-- wp:group {"className":"ep-card","layout":{"type":"flex","orientation":"vertical","justifyContent":"left"}} -->
      <div class="wp-block-group ep-card">

        <!-- wp:html -->
        <div class="ep-card__icon">
          <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" width="20" height="20"><path d="M10 2l2.4 4.8 5.6.8-4 3.9.9 5.5L10 14.5l-4.9 2.5.9-5.5L2 7.6l5.6-.8z"/></svg>
        </div>
        <!-- /wp:html -->

        <!-- wp:heading {"level":5} -->
        <h5 class="wp-block-heading">Card Title</h5>
        <!-- /wp:heading -->

        <!-- wp:paragraph {"style":{"typography":{"fontSize":"15px","lineHeight":"1.6"}}} -->
        <p style="font-size:15px;line-height:1.6;">Card body copy goes here. Keep it to two or three sentences.</p>
        <!-- /wp:paragraph -->

        <!-- wp:paragraph {"className":"ep-card__link"} -->
        <p class="ep-card__link"><a href="#">Explore →</a></p>
        <!-- /wp:paragraph -->

      </div>
      <!-- /wp:group -->
    </div>
    <!-- /wp:column -->

    <!-- Card 3 -->
    <!-- wp:column -->
    <div class="wp-block-column">
      <!-- wp:group {"className":"ep-card","layout":{"type":"flex","orientation":"vertical","justifyContent":"left"}} -->
      <div class="wp-block-group ep-card">

        <!-- wp:html -->
        <div class="ep-card__icon">
          <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" width="20" height="20"><circle cx="10" cy="10" r="8"/><path d="M10 6v4l3 2"/></svg>
        </div>
        <!-- /wp:html -->

        <!-- wp:heading {"level":5} -->
        <h5 class="wp-block-heading">Card Title</h5>
        <!-- /wp:heading -->

        <!-- wp:paragraph {"style":{"typography":{"fontSize":"15px","lineHeight":"1.6"}}} -->
        <p style="font-size:15px;line-height:1.6;">Card body copy goes here. Keep it to two or three sentences.</p>
        <!-- /wp:paragraph -->

        <!-- wp:paragraph {"className":"ep-card__link"} -->
        <p class="ep-card__link"><a href="#">Explore →</a></p>
        <!-- /wp:paragraph -->

      </div>
      <!-- /wp:group -->
    </div>
    <!-- /wp:column -->

    <!-- Card 4 -->
    <!-- wp:column -->
    <div class="wp-block-column">
      <!-- wp:group {"className":"ep-card","layout":{"type":"flex","orientation":"vertical","justifyContent":"left"}} -->
      <div class="wp-block-group ep-card">

        <!-- wp:html -->
        <div class="ep-card__icon">
          <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" width="20" height="20"><path d="M2 14l5-5 4 4 7-8"/></svg>
        </div>
        <!-- /wp:html -->

        <!-- wp:heading {"level":5} -->
        <h5 class="wp-block-heading">Card Title</h5>
        <!-- /wp:heading -->

        <!-- wp:paragraph {"style":{"typography":{"fontSize":"15px","lineHeight":"1.6"}}} -->
        <p style="font-size:15px;line-height:1.6;">Card body copy goes here. Keep it to two or three sentences.</p>
        <!-- /wp:paragraph -->

        <!-- wp:paragraph {"className":"ep-card__link"} -->
        <p class="ep-card__link"><a href="#">Explore →</a></p>
        <!-- /wp:paragraph -->

      </div>
      <!-- /wp:group -->
    </div>
    <!-- /wp:column -->

  </div>
  <!-- /wp:columns -->

</div>
<!-- /wp:group -->
