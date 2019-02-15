<br />
<footer class="mt-4 footer no-print">
    <div class="container">
        <span class="text-muted">
            <?php if ($footer_title): ?>
              <?php if ($footer_link): ?>
                <a href="<?php echo $footer_link ?>"><?php echo $footer_title ?></a>
              <?php else: ?>
                <?php echo $footer_title ?>
              <?php endif ?>
            <?php endif ?>
            <?php if ($terms): ?><a href="<?php echo url_for('terms') ?>"><?php echo $terms_title ?></a><?php endif ?>
            <?php if ($privacy): ?><a href="<?php echo url_for('privacy') ?>"><?php echo $privacy_title ?></a><?php endif ?>
            <?php if ($contact): ?><a href="<?php echo url_for('contact') ?>"><?php echo $contact_title ?></a><?php endif ?>
            <?php if ($imprint): ?><a href="<?php echo url_for('imprint') ?>"><?php echo $imprint_title ?></a><?php endif ?>
        </span>
    </div>
</footer>
