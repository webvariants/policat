<div class="navbar-inner">
  <div class="container" style="width: auto">
    <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
      <span class="icon-bar"></span>
      <span class="icon-bar"></span>
      <span class="icon-bar"></span>
    </a>
    <div class="nav-collapse collapse">
      <ul class="nav">
        <?php if ($menu_home): ?><li><a href="<?php echo url_for('homepage') ?>">Home</a></li><?php endif ?>
        <?php if (sfContext::getInstance()->getModuleName() == 'd_home'): ?>
            <?php if ($sf_user->isAuthenticated()): ?><li><a href="<?php echo url_for('dashboard') ?>">Dashboard</a></li><?php endif ?>
            <?php if ($menu_start): ?>
            <li>
              <?php if ($sf_user->isAuthenticated()): ?>
                <a href="<?php echo url_for('petition_new_') ?>">Start an e-action</a>
              <?php else: ?>
                <a rel="nofollow" data-toggle="modal" href="#login_modal" href="<?php echo url_for('ajax_signin') ?>">Start new e-action</a>
              <?php endif ?>
            </li>
          <?php endif ?>
          <?php if ($tips): ?><li><a href="<?php echo url_for('tips') ?>"><?php echo $tips_title ?></a></li><?php endif ?>
          <?php if ($faq): ?><li><a href="<?php echo url_for('faq') ?>"><?php echo $faq_title ?></a></li><?php endif ?>
          <?php if ($sf_user->isAuthenticated()): ?>
              <?php if ($help): ?><li><a href="<?php echo url_for('help') ?>"><?php echo $help_title ?></a></li><?php endif ?>
            <?php endif ?>
          <?php else: ?>
          <li><a href="<?php echo url_for('dashboard') ?>">Dashboard</a></li>
          <li><a href="<?php echo url_for('action_index') ?>">Actions</a></li>
          <li><a href="<?php echo url_for('widget_index') ?>">Widgets</a></li>
          <li><a href="<?php echo url_for('help') ?>"><?php echo $help_title ?></a></li>
        <?php endif ?>
      </ul>
      <ul class="nav pull-right">
        <?php if ($sf_user->hasCredential(myUser::CREDENTIAL_ADMIN)): ?>
          <li><a href="<?php echo url_for('admin') ?>">Admin</a></li>
        <?php endif ?>
        <?php if ($sf_user->isAuthenticated()): ?>
          <li><a href="<?php echo url_for('profile') ?>">Welcome <?php echo $sf_user->getFirstName() ?>!</a></li>
          <li><a href="<?php echo url_for('sf_guard_signout') ?>">Logout</a></li>
        <?php else: ?>
            <?php if ($menu_login): ?><li><a rel="nofollow" data-toggle="modal" href="#login_modal" href="<?php echo url_for('ajax_signin') ?>">Login</a></li><?php endif ?>
          <?php if ($menu_join): ?><li><a href="<?php echo url_for('register') ?>">Join</a></li><?php endif ?>
        <?php endif ?>
      </ul>
    </div>
  </div>
</div>