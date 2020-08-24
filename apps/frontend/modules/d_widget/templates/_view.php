<div class="modal hide hidden_remove" id="widget_view" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Widget view</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p><b>HTML-Code:</b><code> &lt;div id="policat_<?php echo $id ?>"&gt;&lt;div class="policat-loading"&gt;&lt;/div&gt;&lt;/div&gt;
                        &lt;script type="text/javascript"&gt;window.policat_target_id_<?php echo $id ?> = "policat_<?php echo $id ?>";&lt;/script&gt;
                        &lt;script type="text/javascript" src="<?php echo url_for('api_js_widget', array('id' => $id), true) ?>" async="true"&gt;&lt;/script&gt;
                        &lt;style&gt;.policat-loading{position:relative;height:1em;text-align:center}@keyframes policatspin{to{transform:rotate(360deg)}}
                        .policat-loading::after{content:'';box-sizing:border-box;position:absolute;top:0.33em;width:1em;height:1em;margin-left:1em;border-radius:50%;border-top:2px solid #333;border-right:2px solid transparent;animation:policatspin 0.8s linear infinite}&lt;/style&gt;</code></p>
                <p><b>Shareable widget page for testing or standalone use (without embedding):</b> <a target="_blank" href="<?php echo url_for('widget_page', ['id' => $id], true) ?>"><?php echo url_for('widget_page', ['id' => $id], true) ?></a></p>
                <?php if ($follows): ?>
                <p><strong style="color:red">Attention: The action is following the action
                        "<?php echo $follows ?>".</strong></p>
                <?php endif ?>
                <?php if ($petition_off): ?>
                <div class="alert">Note: Action status is not 'active'. To preview/activate widget, set action status to
                    'active' <a class="btn btn-secondary btn-sm"
                        href="<?php echo url_for('petition_edit_', array('id' => $petition_off)) ?>">edit action</a>
                </div>
                <?php endif ?>
                <?php if ($petition_text_off): ?>
                <div class="alert">Note: Translation status for this widget's language is not 'active'. To
                    preview/activate widget, set translation status to 'active' <a class="btn btn-secondary btn-sm"
                        href="<?php echo url_for('translation_edit', array('id' => $petition_text_off)) ?>">edit
                        translation</a></div>
                <?php endif ?>
                <?php if ($widget_off): ?>
                <div class="alert">Note: The widget is not 'active'. To preview/activate widget, set widget status to
                    'active'</div>
                <?php endif ?>
                <?php if ($petition_off || $petition_text_off || $widget_off): ?>
                <?php else: ?>
                <script type="text/javascript">
                var policat_target_id_<?php echo $id ?> = 'widget_target';
                </script>
                <script type="text/javascript"
                    src="<?php echo url_for('api_js_widget_no_redirect', array('id' => $id)) ?>"></script>
                <div id="widget_target"></div>
                <?php endif ?>
            </div>
            <div class="modal-footer">
                <a class="btn btn-secondary" href="<?php echo url_for('widget_edit', array('id' => $id)) ?>">Edit</a>
                <a class="btn btn-secondary" data-dismiss="modal">Close</a>
            </div>
        </div>
    </div>
</div>
