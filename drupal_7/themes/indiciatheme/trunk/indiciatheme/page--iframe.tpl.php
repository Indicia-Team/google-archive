<!-- Messages and Help -->
<?php print $messages; ?>
<?php print render($page['help']); ?>

<!-- Breadcrumbs -->
<?php if ($breadcrumb): print $breadcrumb; endif; ?>

<?php print render($page['secondary_content']); // Secondary content region (Secondary) ?>

<!-- Three column 3x33 Gpanel -->
<?php if (
  $page['three_33_top'] ||
  $page['three_33_first'] ||
  $page['three_33_second'] ||
  $page['three_33_third'] ||
  $page['three_33_bottom']
  ): ?>
  <div class="at-panel gpanel panel-display three-3x33 clearfix">
    <?php print render($page['three_33_top']); ?>
    <?php print render($page['three_33_first']); ?>
    <?php print render($page['three_33_second']); ?>
    <?php print render($page['three_33_third']); ?>
    <?php print render($page['three_33_bottom']); ?>
  </div>
<?php endif; ?>

<div id="columns">
  <div class="columns-inner clearfix">

    <div id="content-column">
      <div class="content-inner">

        <?php print render($page['highlight']); // Highlighted region ?>

        <<?php print $tag; ?> id="main-content" role="main">

          <?php print render($title_prefix); ?>
          <?php if ($title || $primary_local_tasks || $secondary_local_tasks || $action_links = render($action_links)): ?>
            <header id="main-content-header" class="clearfix">

              <?php if ($title): ?>
                <h1 id="page-title"><?php print $title; ?></h1>
              <?php endif; ?>

              <?php if ($primary_local_tasks || $secondary_local_tasks || $action_links): ?>
                <div id="tasks" class="clearfix">

                  <?php if ($primary_local_tasks): ?>
                    <ul class="tabs primary clearfix"><?php print render($primary_local_tasks); ?></ul>
                  <?php endif; ?>

                  <?php if ($secondary_local_tasks): ?>
                    <ul class="tabs secondary clearfix"><?php print render($secondary_local_tasks); ?></ul>
                  <?php endif; ?>

                  <?php if ($action_links = render($action_links)): ?>
                    <ul class="action-links clearfix"><?php print $action_links; ?></ul>
                  <?php endif; ?>

                </div>
              <?php endif; ?>

            </header>
          <?php endif; ?>
          <?php print render($title_suffix); ?>

          <?php if ($content = render($page['content'])): ?>
            <div id="content">
              <?php print $content; // Main content region ?>
            </div>
          <?php endif; ?>

          <!-- Feed icons (RSS, Atom icons etc -->
          <?php print $feed_icons; ?>

        </<?php print $tag; ?>> <!-- /main-content -->

        <?php print render($page['content_aside']); // Aside region ?>

      </div>
    </div> <!-- /content-column -->

    <?php print render($page['sidebar_first']); // Sidebar first region ?>
    <?php print render($page['sidebar_second']); // Sidebar second region ?>

  </div>
</div> <!-- /columns -->

<?php print render($page['tertiary_content']); // Tertiary content region (Tertiary) ?>

<!-- four-4x25 Gpanel -->
<?php if (
  $page['four_first'] ||
  $page['four_second'] ||
  $page['four_third'] ||
  $page['four_fourth']
  ): ?>
  <div class="at-panel gpanel panel-display four-4x25 clearfix">
    <div class="panel-row row-1 clearfix">
      <?php print render($page['four_first']); ?>
      <?php print render($page['four_second']); ?>
    </div>
    <div class="panel-row row-2 clearfix">
      <?php print render($page['four_third']); ?>
      <?php print render($page['four_fourth']); ?>
    </div>
  </div>
<?php endif; ?>

