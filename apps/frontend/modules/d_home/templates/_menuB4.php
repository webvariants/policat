<nav class="navbar navbar-toggleable-md navbar-toggleable-md-strech navbar-inverse fixed-top bg-inverse no-print">
    <button class="navbar-toggler navbar-toggler-right collapsed" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <?php $logo = StoreTable::value(StoreTable::PORTAL_LOGO); ?>
    <?php if ($logo): ?>
        <a class="navbar-brand navbar-brand-img" href="<?php echo url_for('homepage') ?>">
            <img src="<?php echo image_path('store/' . $logo) ?>?<?php echo StoreTable::version(StoreTable::PORTAL_LOGO) ?>" alt="<?php echo Util::enc(StoreTable::value(StoreTable::PORTAL_NAME)) ?>" />
        </a>
    <?php else: ?>
        <a class="navbar-brand" href="<?php echo url_for('homepage') ?>">
            <?php echo Util::enc(StoreTable::value(StoreTable::PORTAL_NAME)) ?>
        </a>
    <?php endif ?>
    <div class="collapse navbar-collapse" id="navbarCollapse">
        <ul class="navbar-nav mr-auto">
            <?php if ($menu_home): ?><li class="nav-item"><a class="nav-link" href="<?php echo url_for('homepage') ?>">Home</a></li><?php endif ?>
            <?php if (sfContext::getInstance()->getModuleName() == 'd_home'): ?>
                  <?php if ($sf_user->isAuthenticated()): ?><li class="nav-item"><a class="nav-link" href="<?php echo url_for('dashboard') ?>">Dashboard</a></li><?php endif ?>
                  <?php if ($menu_start): ?>
                <li class="nav-item">
                    <?php if ($sf_user->isAuthenticated()): ?>
                      <a class="nav-link" href="<?php echo url_for('petition_new_') ?>">Start an e-action</a>
                    <?php else: ?>
                      <a class="nav-link" rel="nofollow" data-toggle="modal" href="#login_modal" href="<?php echo url_for('ajax_signin') ?>">Start new e-action</a>
                    <?php endif ?>
                </li>
              <?php endif ?>
              <?php if ($tips): ?><li class="nav-item"><a class="nav-link" href="<?php echo url_for('tips') ?>"><?php echo $tips_title ?></a></li><?php endif ?>
              <?php if ($faq): ?><li class="nav-item"><a class="nav-link" href="<?php echo url_for('faq') ?>"><?php echo $faq_title ?></a></li><?php endif ?>
              <?php if ($pricing): ?><li class="nav-item"><a class="nav-link" href="<?php echo url_for('pricing') ?>">Pricing</a></li><?php endif ?>
              <?php if ($sf_user->isAuthenticated()): ?>
                    <?php if ($help): ?><li class="nav-item"><a class="nav-link" href="<?php echo url_for('help') ?>"><?php echo $help_title ?></a></li><?php endif ?>
                  <?php endif ?>
                <?php else: ?>
              <li class="nav-item"><a class="nav-link" href="<?php echo url_for('dashboard') ?>">Dashboard</a></li>
              <li class="nav-item"><a class="nav-link" href="<?php echo url_for('action_index') ?>">Actions</a></li>
              <li class="nav-item"><a class="nav-link" href="<?php echo url_for('widget_index') ?>">Widgets</a></li>
              <?php if ($pricing): ?><li class="nav-item"><a class="nav-link" href="<?php echo url_for('pricing') ?>">Pricing</a></li><?php endif ?>
              <?php if ($tips): ?><li class="nav-item"><a class="nav-link" href="<?php echo url_for('tips') ?>"><?php echo $tips_title ?></a></li><?php endif ?>
              <li class="nav-item"><a class="nav-link" href="<?php echo url_for('help') ?>"><?php echo $help_title ?></a></li>
            <?php endif ?>
        </ul>
        <ul class="navbar-nav mt-2 mt-md-0">
            <?php if ($sf_user->hasCredential(myUser::CREDENTIAL_ADMIN)): ?>
              <li class="nav-item"><a class="nav-link" href="<?php echo url_for('admin') ?>">Admin</a></li>
            <?php endif ?>
            <?php if ($sf_user->isAuthenticated()): ?>
              <li class="nav-item"><a class="nav-link" href="<?php echo url_for('profile') ?>">Welcome <?php echo $sf_user->getFirstName() ?>!</a></li>
              <li class="nav-item"><a class="nav-link" href="<?php echo url_for('sf_guard_signout') ?>">Logout</a></li>
            <?php else: ?>
              <?php if ($menu_login): ?><li class="nav-item"><a class="nav-link" data-toggle="modal" data-target="#login_modal" rel="nofollow" href="<?php echo url_for('homepage') ?>" href="<?php echo url_for('ajax_signin') ?>">Login<?php if ($menu_join): ?> | Join<?php endif ?></a></li><?php endif ?>
            <?php endif ?>
        </ul>

    </div>
</nav>
