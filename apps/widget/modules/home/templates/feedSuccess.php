<?php echo '<?xml version="1.0" encoding="UTF-8" ?>' ?>
<rss version="2.0">
    <channel>
        <title>policat.org - recent petitions</title>
        <link><?php echo url_for('homepage', array(), true) ?></link>
        <?php foreach ($excerpts as $excerpt): ?>
          <item>
              <title><?php echo $excerpt['title'] ?></title>
              <description><?php echo $excerpt['text'] ?></description>
              <link><?php echo $excerpt['read_more_url'] ?></link>
          </item>
        <?php endforeach ?>
    </channel>
</rss>
