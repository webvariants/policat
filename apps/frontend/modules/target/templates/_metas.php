<table id="metas" class="table table-bordered table-striped">
  <thead>
    <tr><th>Name</th><th>Kind</th><th>Keyword</th><th></th></tr>
  </thead>
  <tbody>
    <?php foreach ($metas as $meta): /* @var $meta MailingListMeta */ ?>
      <tr id="meta_<?php echo $meta->getId() ?>">
        <td><?php echo $meta->getName() ?></td>
        <td><?php echo $meta->getKindName() ?>
          <?php if ($meta->getKind() == MailingListMeta::KIND_MAPPING): $meta2 = $meta->getMeta();
            $mapping = $meta->getMapping() ?>
          (<?php echo $mapping ? $mapping->getName() : '' ?>: <?php echo $meta2 ? $meta2->getName() : '' ?>)
          <?php endif ?>
        </td>
        <td><?php echo $meta->getSubst() ?></td>
        <td>
          <a class="ajax_link btn btn-primary btn-sm" href="<?php echo url_for('target_meta', array('id' => $meta->getId())) ?>">Edit</a>
          <a class="ajax_link btn btn-sm btn-danger" href="<?php echo url_for('target_meta_delete', array('id' => $meta->getId())) ?>">Delete</a>
        </td>
      </tr>
<?php endforeach ?>
  </tbody>
</table>