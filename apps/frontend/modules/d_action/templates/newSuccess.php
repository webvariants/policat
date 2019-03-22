<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="<?php echo url_for('dashboard') ?>">Dashboard</a></li>
    <li class="breadcrumb-item active">Start a new e-action</li>
  </ol>
</nav>
<div class="page-header">
  <h1>Start a new e-action</h1>
</div>
    <form class="ajax_form form-horizontal" action="<?php echo url_for('petition_new_') ?>" method="post">
      <?php echo $form->renderHiddenFields() ?>
      <?php echo $form->renderRows('name', 'campaign_id', '*new_campaign', 'kind') ?>
      <fieldset>
        <legend>Sign-up data</legend>
        <div class="global_error">
          <span id="new_petition_customise"></span>
        </div>
        <?php echo $form->renderRows('titletype', 'nametype', 'with_address', 'with_country', 'default_country', 'country_collection_id', 'with_comments', 'with_extra1') ?>
      </fieldset>
      <div class="form-actions">
        <button accesskey="s" title="[Accesskey] + S" class="btn btn-primary" type="submit">Save &amp; go to optional settings</button>
        <a class="btn btn-secondary submit" data-submit='{"go_translation":1}'>Save &amp; go to actions texts and translations</a>
        <a class="btn btn-secondary" href="<?php echo url_for('homepage') ?>">Cancel</a>
      </div>
    </form>
