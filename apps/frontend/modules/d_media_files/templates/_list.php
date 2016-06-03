<?php use_helper('Text', 'Number', 'Date','Template');?>

<div id="media_files_list">    
    
    <div> <?php echo formatSize($totalSize); ?> of <?php echo formatSize(MediaFiles::SPACE_LIMIT); ?> used. You have <?php echo formatSize($leftSize); ?> left for uploads.</div>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th> </th>        
                <th>Date </th>        
                <th>Filename</th>          
                <th>Size </th>                
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($files as $file):?>
                <tr>                    
                    <td><a href="<?php echo $file->getUrl(); ?>" title="<?php echo $file->getName() ?>" target="_blank"><img src="<?php echo $file->getUrl(); ?>"  width="100px" height="100px"/></a></td>
                    <td><?php echo format_date($file->getCreatedAt(), 'yyyy-MM-dd') ?></td>
                    <td><?php echo $file->getFilename() ?></td>                                                        
                    <td><?php echo formatSize($file->getSize()) ?></td>                                    
                    <td>                                          
                            <a class="btn btn-mini" href="<?php echo url_for('media_files_rename', array('petition_id' => $petition, "id"=>$file->getId()))  ?>">Rename</a>
                            <a class="btn btn-mini" href="<?php echo url_for('media_files_delete', array('petition_id' => $petition, "id"=>$file->getId()))  ?>">Delete</a>
                    </td>
            </tr>
        <?php endforeach ?>
        </tbody>
    </table>
    <?php include_partial('dashboard/pager', array('pager' => $files)) ?>
</div>
<div>
<form method="POST" class="form-inline" action="<?php echo url_for('media_files_upload', array('petition_id' => $petition)) ?>"   <?php $form->isMultipart() and print 'enctype="multipart/form-data" ' ?>>    
   <div class="field">            
            <div><?php echo $form->renderHiddenFields() ?></div>
            <div><?php echo $form['file']?></div>
            <?php if ($errors): ?>                           
            <div><?php foreach ($errors as $key => $value): ?>
                      <div>  <?php echo $value; ?> </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
    </div>     
    <div> <input class="filter_reset btn btn-small top15" type="submit" value="Upload"></div>
</form>
</div>