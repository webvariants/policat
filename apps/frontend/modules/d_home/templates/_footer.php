<footer class="top_line">
  <ul>
    <?php if ($footer_title): ?>
      <?php if ($footer_link): ?>
        <li><a href="<?php echo $footer_link ?>"><?php echo $footer_title ?></a></li>
      <?php else: ?>
        <li><?php echo $footer_title ?></li>
      <?php endif ?>
    <?php endif ?>
    <?php if ($terms): ?><li><a href="<?php echo url_for('terms') ?>"><?php echo $terms_title ?></a></li><?php endif ?>
    <?php if ($privacy): ?><li><a href="<?php echo url_for('privacy') ?>"><?php echo $privacy_title ?></a></li><?php endif ?>
    <?php if ($contact): ?><li><a href="<?php echo url_for('contact') ?>"><?php echo $contact_title ?></a></li><?php endif ?>
    <?php if ($imprint): ?><li><a href="<?php echo url_for('imprint') ?>"><?php echo $imprint_title ?></a></li><?php endif ?>
  </ul>
</footer>
