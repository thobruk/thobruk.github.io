<?php

add_action( 'after_setup_theme', function () {
    add_theme_support( 'wp-block-styles' );
    add_theme_support( 'editor-styles' );
    add_editor_style( 'style.css' );
} );

// Enqueue Google Fonts + theme stylesheet
add_action( 'wp_enqueue_scripts', function () {
    wp_enqueue_style(
        'eparamus-fonts',
        'https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap',
        [],
        null
    );

    wp_enqueue_style(
        'eparamus-style',
        get_stylesheet_uri(),
        [ 'eparamus-fonts' ],
        wp_get_theme()->get( 'Version' )
    );
} );

// Enqueue Google Fonts in the block editor too
add_action( 'enqueue_block_editor_assets', function () {
    wp_enqueue_style(
        'eparamus-fonts-editor',
        'https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap',
        [],
        null
    );
} );

// Register custom block pattern categories
add_action( 'init', function () {
    register_block_pattern_category(
        'eparamus-sections',
        [ 'label' => __( 'eParamus Sections', 'eparamus' ) ]
    );

    register_block_pattern_category(
        'eparamus-pages',
        [ 'label' => __( 'eParamus Pages', 'eparamus' ) ]
    );
} );

// Register custom button styles for the editor
add_action( 'init', function () {
    register_block_style( 'core/button', [
        'name'  => 'secondary',
        'label' => __( 'Secondary', 'eparamus' ),
    ] );

    register_block_style( 'core/button', [
        'name'  => 'ghost',
        'label' => __( 'Ghost', 'eparamus' ),
    ] );

    register_block_style( 'core/button', [
        'name'  => 'white',
        'label' => __( 'White', 'eparamus' ),
    ] );

    register_block_style( 'core/button', [
        'name'  => 'large',
        'label' => __( 'Large', 'eparamus' ),
    ] );
} );

// Scroll animation JS — injected in footer, lightweight inline
add_action( 'wp_footer', function () { ?>
<script>
(function () {
  var observer = new IntersectionObserver(
    function (entries) {
      entries.forEach(function (e) {
        if (e.isIntersecting) {
          e.target.classList.add('is-visible');
          observer.unobserve(e.target);
        }
      });
    },
    { threshold: 0.08, rootMargin: '0px 0px -48px 0px' }
  );

  // Explicitly marked elements
  document.querySelectorAll('.ep-fade-up').forEach(function (el) {
    observer.observe(el);
  });

  // Auto-animate key elements inside sections
  var selectors = [
    '.ep-section .ep-label',
    '.ep-section h1, .ep-section h2, .ep-section h3',
    '.ep-section > * > .wp-block-group > p',
    '.ep-section .wp-block-columns > .wp-block-column',
    '.ep-section .ep-card',
    '.ep-section .ep-step',
    '.ep-section .ep-contrast',
    '.ep-section .wp-block-buttons',
    '.ep-section .ep-ladder__item',
  ].join(', ');

  document.querySelectorAll(selectors).forEach(function (el, i) {
    if (el.classList.contains('is-visible')) return;
    el.classList.add('ep-fade-up');
    // Stagger siblings
    var siblings = el.parentElement ? el.parentElement.children : [];
    var idx = Array.prototype.indexOf.call(siblings, el);
    if (idx > 0 && idx < 4) {
      el.style.transitionDelay = (idx * 0.1) + 's';
    }
    observer.observe(el);
  });
})();
</script>
<?php } );
