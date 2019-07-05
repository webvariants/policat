<div id="widget-action-id-<?php echo $excerpt['petition_id'] ?>" class="excerpt col-md-6 top_line" onclick="<?php echo UtilWidget::getWidgetHereJs($excerpt['widget_id'], true) ?>" style="cursor: pointer">
    <div class="row">
        <div class="col-md-3"><?php if ($excerpt['key_visual']): ?><img class="home_teaser" src="<?php echo image_path('keyvisual/' . $excerpt['key_visual']) ?>" alt="" style="max-width:100%" /><?php endif ?></div>
        <div class="col-md-7">
            <div class="petition_stats_container">
                <p><b><?php echo  Util::enc($excerpt['title']) ?></b> <?php echo Util::enc($excerpt['text']) ?></p>
                <a class="btn btn-sm btn-secondary d-block show">sign!</a>
            </div>
        </div>
    </div>
</div>
