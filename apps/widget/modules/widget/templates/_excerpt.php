<div id="widget-action-id-<?php echo $excerpt['petition_id'] ?>" class="excerpt span6 top_line" onclick="<?php echo UtilWidget::getWidgetHereJs($excerpt['widget_id'], true) ?>" style="cursor: pointer">
    <div class="row">
        <div class="span1"><?php if ($excerpt['key_visual']): ?><img class="home_teaser" src="<?php echo image_path('keyvisual/' . $excerpt['key_visual']) ?>" alt="" /><?php else: ?><div class="visible-desktop" style="background: #f5f5f5; height: 70px">&nbsp;</div><?php endif ?></div>
        <div class="span5">
            <div class="petition_stats_container">
                <p><b><?php echo  Util::enc($excerpt['title']) ?></b> <?php echo Util::enc($excerpt['text']) ?></p>
                <div class="petition_stats">
                    <dl class="well well-small">
                        <dt>Total</dt>
                        <dd title="<?php echo number_format($excerpt['signings'], 0, '.', ',') ?>"><?php echo Util::readable_number($excerpt['signings']) ?></dd>
                        <dt>Today</dt>
                        <dd title="<?php echo number_format($excerpt['signings24'], 0, '.', ',') ?>"><?php echo Util::readable_number($excerpt['signings24']) ?></dd>
                    </dl>
                    <a class="btn btn-mini show">sign!</a>
                </div>
            </div>
        </div>
    </div>
</div>