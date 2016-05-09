<h3>Print, sign and send to: (you can reopen the page and print later)</h3>
<?php echo UtilMarkdown::transform($address) ?>
<h3>Privacy agreement</h3>
<?php echo UtilMarkdown::transform($privacy_policy) ?>
<h3>Contact address</h3>
<table class="t_contact">
  <tr>
    <th>Widget ID</th>
    <td><?php echo $widget_id ?></td>
    <th>Campaign ID</th>
    <td><?php echo $campaign_id ?></td>
  </tr>
  <tr>
    <th>first name</th>
    <td><?php echo Util::enc($owner->getFirstname()) ?></td>
    <th>phone</th>
    <td><?php echo Util::enc($owner->getPhone()) ?></td>
  </tr>
  <tr>
    <th>last name</th>
    <td><?php echo Util::enc($owner->getLastname()) ?></td>
    <th>e-mail</th>
    <td><?php echo Util::enc($owner->getEmail()) ?></td>
  </tr>
  <tr>
    <th>function</th>
    <td><?php echo Util::enc($owner->getFunction()) ?></td>
    <th>address</th>
    <td><?php echo Util::enc($owner->getAddress()) ?></td>
  </tr>
  <tr>
    <th>organisation</th>
    <td><?php echo Util::enc($owner->getOrganisation()) ?></td>
    <th>country</th>
    <td><?php echo Util::enc($owner->getCountry()) ?></td>
  </tr>
</table>

<p style="margin-bottom: 0; font-size: 180%; line-height: 150%"><br /><br />&#x2717;</p>
<p style="border-top: 1px solid black; width: 40%;text-align: center">sign</p>
