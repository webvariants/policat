<?php echo '<?xml version="1.0" encoding="UTF-8" ?>' ?>
<rss version="2.0">
  <channel>
    <title>policat.org - recent petitions</title>
    <link><?php echo url_for('homepage', array(), true) ?></link>
    <?php
    use_helper('Text');
    foreach ($petitions as $petition):
      $text = $petition['PetitionText'][0];
      $widget = $text['DefaultWidget'];
      $title = Util::enc($widget['title'] ? $widget['title'] : $text['title']);
      if (in_array($petition['kind'], Petition::$EMAIL_KINDS, true))
        $body = Util::enc(
            ($widget['email_subject'] ? $widget['email_subject'] : $text['email_subject']) .
            ($widget['email_body'] ? $widget['email_body'] : $text['email_body'])
        );
      else
        $body =
          UtilMarkdown::transform(($widget['intro'] ? $widget['intro'] . " \n\n" : '') . $text['body'] . ($widget['footer'] ? " \n\n" . $widget['footer'] : ''));
    ?>
      <item>
        <title><?php echo $title ?></title>
        <description><?php echo strip_tags($body) ?></description>
        <link><?php echo $petition['read_more_url'] ?></link>
      </item>
<?php endforeach ?>
  </channel>
</rss>
