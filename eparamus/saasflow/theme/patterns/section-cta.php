<?php
/**
 * Title: Section — CTA Band
 * Slug: eparamus/section-cta
 * Categories: eparamus-sections
 * Description: Full-width blue CTA section with headline, supporting copy, dual buttons, and a 3-step panel. Used as the final section on most pages.
 * Keywords: cta, call to action, start, conversion
 * Viewport Width: 1280
 */
?>

<!-- wp:group {
  "align":"full",
  "className":"ep-section ep-section--blue",
  "backgroundColor":"primary",
  "style":{"spacing":{"padding":{"top":"0","bottom":"0"}}},
  "layout":{"type":"constrained"}
} -->
<div class="wp-block-group alignfull ep-section ep-section--blue has-primary-background-color has-background">

  <!-- wp:columns {
    "isStackedOnMobile":true,
    "style":{
      "spacing":{
        "blockGap":{"left":"var:preset|spacing|32"}
      }
    }
  } -->
  <div class="wp-block-columns">

    <!-- Left: headline + copy + buttons -->
    <!-- wp:column {"verticalAlignment":"center"} -->
    <div class="wp-block-column is-vertically-aligned-center">

      <!-- wp:paragraph {
        "className":"ep-label",
        "style":{"color":{"text":"rgba(255,255,255,0.6)"}}
      } -->
      <p class="ep-label" style="color:rgba(255,255,255,0.6);">Getting Started</p>
      <!-- /wp:paragraph -->

      <!-- wp:heading {
        "level":2,
        "style":{
          "typography":{"letterSpacing":"-0.8px"},
          "color":{"text":"#ffffff"}
        }
      } -->
      <h2 class="wp-block-heading" style="letter-spacing:-0.8px;color:#ffffff;">
        CTA Headline Goes Here
      </h2>
      <!-- /wp:heading -->

      <!-- wp:paragraph {
        "style":{
          "typography":{"fontSize":"clamp(16px, 2vw, 20px)","lineHeight":"1.6"},
          "color":{"text":"rgba(255,255,255,0.75)"}
        }
      } -->
      <p style="font-size:clamp(16px, 2vw, 20px);line-height:1.6;color:rgba(255,255,255,0.75);">
        Supporting copy for the CTA. One or two sentences that reinforce the offer and reduce friction.
      </p>
      <!-- /wp:paragraph -->

      <!-- wp:buttons {
        "style":{"spacing":{"blockGap":"var:preset|spacing|8"}}
      } -->
      <div class="wp-block-buttons">

        <!-- wp:button {
          "className":"is-style-white",
          "style":{
            "typography":{"fontSize":"16px"},
            "spacing":{
              "padding":{"top":"16px","bottom":"16px","left":"32px","right":"32px"}
            }
          }
        } -->
        <div class="wp-block-button is-style-white">
          <a class="wp-block-button__link wp-element-button"
             href="#"
             style="font-size:16px;padding:16px 32px;">
            Primary CTA
          </a>
        </div>
        <!-- /wp:button -->

        <!-- wp:button {"className":"is-style-ghost"} -->
        <div class="wp-block-button is-style-ghost">
          <a class="wp-block-button__link wp-element-button" href="#">Schedule a Conversation</a>
        </div>
        <!-- /wp:button -->

      </div>
      <!-- /wp:buttons -->

      <!-- wp:paragraph {
        "style":{
          "typography":{"fontSize":"13px"},
          "color":{"text":"rgba(255,255,255,0.45)"}
        }
      } -->
      <p style="font-size:13px;color:rgba(255,255,255,0.45);">
        Start small. Build confidence. Scale over time.
      </p>
      <!-- /wp:paragraph -->

    </div>
    <!-- /wp:column -->

    <!-- Right: 3-step panel -->
    <!-- wp:column {"verticalAlignment":"center"} -->
    <div class="wp-block-column is-vertically-aligned-center">

      <!-- wp:html -->
      <div style="background:rgba(255,255,255,0.1);border:1px solid rgba(255,255,255,0.2);border-radius:28px;padding:24px;display:flex;flex-direction:column;gap:12px;">

        <div style="display:flex;gap:16px;align-items:flex-start;background:rgba(255,255,255,0.08);border-radius:12px;padding:16px;">
          <div style="width:32px;height:32px;border-radius:50%;background:rgba(255,255,255,0.15);display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:13px;font-weight:700;color:white;">1</div>
          <div>
            <p style="font-weight:600;color:white;font-size:15px;margin:0 0 4px;">Step One Title</p>
            <p style="font-size:13px;color:rgba(255,255,255,0.65);margin:0;">Step one supporting copy.</p>
          </div>
        </div>

        <div style="display:flex;gap:16px;align-items:flex-start;background:rgba(255,255,255,0.08);border-radius:12px;padding:16px;">
          <div style="width:32px;height:32px;border-radius:50%;background:rgba(255,255,255,0.15);display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:13px;font-weight:700;color:white;">2</div>
          <div>
            <p style="font-weight:600;color:white;font-size:15px;margin:0 0 4px;">Step Two Title</p>
            <p style="font-size:13px;color:rgba(255,255,255,0.65);margin:0;">Step two supporting copy.</p>
          </div>
        </div>

        <div style="display:flex;gap:16px;align-items:flex-start;background:rgba(255,255,255,0.08);border-radius:12px;padding:16px;">
          <div style="width:32px;height:32px;border-radius:50%;background:rgba(255,255,255,0.15);display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:13px;font-weight:700;color:white;">3</div>
          <div>
            <p style="font-weight:600;color:white;font-size:15px;margin:0 0 4px;">Step Three Title</p>
            <p style="font-size:13px;color:rgba(255,255,255,0.65);margin:0;">Step three supporting copy.</p>
          </div>
        </div>

      </div>
      <!-- /wp:html -->

    </div>
    <!-- /wp:column -->

  </div>
  <!-- /wp:columns -->

</div>
<!-- /wp:group -->
