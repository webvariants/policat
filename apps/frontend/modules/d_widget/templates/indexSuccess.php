<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="<?php echo url_for('dashboard') ?>">Dashboard</a></li>
    <li class="breadcrumb-item active">Widgets</li>
  </ol>
</nav>
<div class="page-header">
  <h1>Widgets</h1>
</div>
<?php include_component('d_widget', 'list') ?>
