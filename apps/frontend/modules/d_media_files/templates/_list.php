<?php use_helper('Text', 'Number', 'Date', 'Template'); ?>
<div id="media_files_list">
    <div> <?php echo formatSize($totalSize); ?> of <?php echo formatSize(MediaFile::SPACE_LIMIT); ?> used. You have <?php echo formatSize($leftSize); ?> left for uploads.</div>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th> </th>
                <th>Date </th>
                <th>Title</th>
                <th>Size </th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($files as $file): ?>
              <tr>
                  <td><a href="<?php echo $file->getUrl(); ?>" target="_blank"><img src="<?php echo $file->getUrl() ?>"  width="100px" height="100px"/></a></td>
                  <td><?php echo format_date($file->getCreatedAt(), 'yyyy-MM-dd') ?></td>
                  <td><?php echo $file->getTitle() ?></td>
                  <td><?php echo formatSize($file->getSize()) ?></td>
                  <td>
                      <a class="btn btn-sm ajax_link" href="<?php echo url_for('media_files_rename', array('id' => $file->getId())) ?>">Rename</a>
                      <a class="btn btn-sm ajax_link" href="<?php echo url_for('media_files_delete', array('id' => $file->getId())) ?>">Delete</a>
                  </td>
              </tr>
            <?php endforeach ?>
        </tbody>
    </table>
    <?php include_partial('dashboard/pager', array('pager' => $files)) ?>
</div>