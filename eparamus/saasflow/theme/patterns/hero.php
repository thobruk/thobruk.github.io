<?php
/**
 * Title: Hero Section
 * Slug: eparamus/hero
 * Categories: eparamus-sections
 * Description: Full-width hero with display headline, subheadline, dual CTAs, and dot-grid background.
 * Keywords: hero, headline, cta
 * Viewport Width: 1280
 */
?>

<!-- wp:group {
  "align":"full",
  "className":"ep-section ep-hero",
  "style":{
    "spacing":{
      "padding":{
        "top":"clamp(64px, 10vw, 120px)",
        "bottom":"clamp(56px, 8vw, 96px)"
      }
    }
  },
  "layout":{"type":"constrained"}
} -->
<div class="wp-block-group alignfull ep-section ep-hero">

  <!-- wp:group {
    "layout":{
      "type":"constrained",
      "contentSize":"760px",
      "justifyContent":"left"
    }
  } -->
  <div class="wp-block-group">

    <!-- wp:paragraph {"className":"ep-label"} -->
    <p class="ep-label">Section label</p>
    <!-- /wp:paragraph -->

    <!-- wp:heading {
      "level":1,
      "style":{
        "typography":{
          "fontWeight":"800",
          "lineHeight":"1.05",
          "letterSpacing":"-1.6px"
        },
        "fontSize":"display"
      }
    } -->
    <h1 class="has-display-font-size" style="font-weight:800;line-height:1.05;letter-spacing:-1.6px;">
      Display Headline<br>Goes Here
    </h1>
    <!-- /wp:heading -->

    <!-- wp:paragraph {
      "style":{
        "typography":{
          "fontSize":"clamp(16px, 2vw, 20px)",
          "lineHeight":"1.6"
        },
        "dimensions":{"maxWidth":"540px"}
      }
    } -->
    <p style="font-size:clamp(16px, 2vw, 20px);line-height:1.6;max-width:540px;">
      Subheadline copy goes here. Keep it to one or two sentences that support the headline.
    </p>
    <!-- /wp:paragraph -->

    <!-- wp:buttons {
      "style":{"spacing":{"blockGap":"var:preset|spacing|8"}}
    } -->
    <div class="wp-block-buttons">

      <!-- wp:button {
        "backgroundColor":"primary",
        "style":{
          "typography":{"fontSize":"16px"},
          "spacing":{
            "padding":{"top":"16px","bottom":"16px","left":"32px","right":"32px"}
          }
        }
      } -->
      <div class="wp-block-button">
        <a class="wp-block-button__link wp-element-button has-primary-background-color has-background"
           href="#"
           style="font-size:16px;padding:16px 32px;">
          Primary CTA
        </a>
      </div>
      <!-- /wp:button -->

      <!-- wp:button {"className":"is-style-secondary"} -->
      <div class="wp-block-button is-style-secondary">
        <a class="wp-block-button__link wp-element-button" href="#">Secondary CTA</a>
      </div>
      <!-- /wp:button -->

    </div>
    <!-- /wp:buttons -->

    <!-- wp:paragraph {
      "style":{
        "typography":{"fontSize":"13px"},
        "color":{"text":"#c7cad1"}
      }
    } -->
    <p style="font-size:13px;color:#c7cad1;">Supporting microcopy goes here.</p>
    <!-- /wp:paragraph -->

  </div>
  <!-- /wp:group -->

</div>
<!-- /wp:group -->
