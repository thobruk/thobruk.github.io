<?php
/**
 * Title: Section — 4-Step Process
 * Slug: eparamus/section-steps
 * Categories: eparamus-sections
 * Description: Dark section with a numbered 4-step horizontal flow and connecting line. Used for process/how-it-works sections.
 * Keywords: steps, process, numbered, flow, how it works
 * Viewport Width: 1280
 */
?>

<!-- wp:group {
  "align":"full",
  "className":"ep-section ep-section--dark",
  "backgroundColor":"dark-800",
  "style":{"spacing":{"padding":{"top":"0","bottom":"0"}}},
  "layout":{"type":"constrained"}
} -->
<div class="wp-block-group alignfull ep-section ep-section--dark has-dark-800-background-color has-background">

  <!-- Section header -->
  <!-- wp:group {
    "layout":{
      "type":"constrained",
      "contentSize":"680px",
      "justifyContent":"center"
    }
  } -->
  <div class="wp-block-group">

    <!-- wp:paragraph {
      "className":"ep-label",
      "style":{"text":{"textAlign":"center"}}
    } -->
    <p class="ep-label" style="text-align:center;">Section label</p>
    <!-- /wp:paragraph -->

    <!-- wp:heading {
      "level":2,
      "textAlign":"center",
      "style":{
        "typography":{"letterSpacing":"-0.8px"},
        "color":{"text":"#ffffff"}
      }
    } -->
    <h2 class="wp-block-heading has-text-align-center" style="letter-spacing:-0.8px;color:#ffffff;">
      Section Headline Goes Here
    </h2>
    <!-- /wp:heading -->

    <!-- wp:paragraph {
      "textAlign":"center",
      "style":{
        "typography":{"fontSize":"clamp(16px, 2vw, 20px)","lineHeight":"1.6"},
        "color":{"text":"rgba(255,255,255,0.65)"}
      }
    } -->
    <p class="has-text-align-center" style="font-size:clamp(16px, 2vw, 20px);line-height:1.6;color:rgba(255,255,255,0.65);">
      Intro copy goes here. One or two sentences introducing the process.
    </p>
    <!-- /wp:paragraph -->

  </div>
  <!-- /wp:group -->

  <!-- Steps grid — uses ep-steps CSS for connecting line and number circles -->
  <!-- wp:html -->
  <div class="ep-steps">

    <div class="ep-step">
      <div class="ep-step__number">1</div>
      <div class="ep-step__content">
        <h5>Step One</h5>
        <p>Brief description of this step. One or two sentences.</p>
      </div>
    </div>

    <div class="ep-step">
      <div class="ep-step__number">2</div>
      <div class="ep-step__content">
        <h5>Step Two</h5>
        <p>Brief description of this step. One or two sentences.</p>
      </div>
    </div>

    <div class="ep-step">
      <div class="ep-step__number">3</div>
      <div class="ep-step__content">
        <h5>Step Three</h5>
        <p>Brief description of this step. One or two sentences.</p>
      </div>
    </div>

    <div class="ep-step">
      <div class="ep-step__number">4</div>
      <div class="ep-step__content">
        <h5>Step Four</h5>
        <p>Brief description of this step. One or two sentences.</p>
      </div>
    </div>

  </div>
  <!-- /wp:html -->

  <!-- Closing statement -->
  <!-- wp:paragraph {
    "textAlign":"center",
    "style":{
      "typography":{"fontSize":"15px"},
      "color":{"text":"rgba(255,255,255,0.45)"},
      "dimensions":{"maxWidth":"600px"},
      "layout":{"selfStretch":"fit"}
    }
  } -->
  <p class="has-text-align-center" style="font-size:15px;color:rgba(255,255,255,0.45);max-width:600px;margin-inline:auto;">
    Closing statement goes here — ties the steps together.
  </p>
  <!-- /wp:paragraph -->

</div>
<!-- /wp:group -->
