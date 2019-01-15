<?php
/* @var $petition Petition */
/* @var $petition_text PetitionText */
$widget_texts = $petition->getWidgetIndividualiseText();
if ($petition->isEmailKind()):
  if ($petition->isGeoKind()):
    ?><form action=""><div id="target-selector" class="target-selector"></div>
    <?php
    if ($petition->getKind() == Petition::KIND_PLEDGE):
      $pledge_items = $petition->getPledgeItems();
      $active_plegde_count = 0;
      ob_start();
      ?>
        <li class="pledge_contact">
          <input type="checkbox" name="pledge_contact[]" value="" />
          <i class="pledge_color pledge_done"></i>
          <label class="pledge_contact_name"></label>
          <span class="pledge_icons">
            <?php
            $i = 0;
            foreach ($pledge_items as $pledge_item): /* @var $pledge_item PledgeItem */
              if ($pledge_item->getStatus() == PledgeItemTable::STATUS_ACTIVE): $active_plegde_count++;
                ?><a title="<?php echo __('Read pledge text') ?>" data-for="#pledge_text_<?php echo $pledge_item->getId() ?>" class="nonewwin pledge_item pledge_item_<?php echo $pledge_item->getId() ?> pledge_color_<?php echo $pledge_item->getColor() ?>"><i class="pledge_color pledge_color_<?php echo $pledge_item->getColor() ?>"> </i></a><?php
              endif;
            endforeach
            ?>
          </span>
        </li>
        <?php $template = ob_get_clean(); ?>
        <div id="scroll-pledges" class="scroll-pledges">
          <ul id="pledges" class="pledges" data-pledge-count="<?php echo $active_plegde_count ?>" data-template='<?php echo htmlentities(json_encode(array('data' => trim(preg_replace('/( +)|\n/', ' ', $template)))), ENT_COMPAT, 'UTF-8') ?>' ></ul>
        </div>
        <?php
        foreach ($pledge_items as $pledge_item): /* @var $pledge_item PledgeItem */
          if ($pledge_item->getStatus() == PledgeItemTable::STATUS_ACTIVE):
            ?>
            <div id="pledge_text_<?php echo $pledge_item->getId() ?>" style="display: none" class="pledge-text">
              <?php echo UtilMarkdown::transform($petition_text->getPledgeTextByPledgeItem($pledge_item)) ?>
            </div>
            <?php
          endif;
        endforeach
        ?>
      <?php endif ?>
    </form><?php
  endif;
  if ($petition->getKind() != Petition::KIND_PLEDGE):
    ?>
    <form action="">
      <div>
        <label><?php echo __('Subject') ?></label>
        <input <?php if ($petition->getEditable() == Petition::EDITABLE_NO): ?>disabled="disabled"<?php endif ?> type="text" id="petition_signing_email_subject_copy" />
      </div>
      <div>
        <label><?php echo __('Email body') ?></label>
        <textarea <?php if ($petition->getEditable() == Petition::EDITABLE_NO): ?>disabled="disabled"<?php endif ?> id="petition_signing_email_body_copy" cols="10" rows="10"></textarea>
      </div>
    </form>
    <?php
  endif;
else:
  ?>
  <div class="scroll"><div id="petition_text">
      <?php
      $markdown = array();
      foreach (array('intro', 'body', 'footer') as $field) {
        $value = ($widget_texts && isset($widget[$field])) ? $widget[$field] : $petition_text[$field];
        $markdown[] = $value;
      }
      echo trim(UtilMarkdown::transformMedia(implode("\n\n", $markdown), $petition), "\n") . "\n";
      ?>
    </div>
  </div>
<?php endif;