<?php
/*
 * Copyright (c) 2016, webvariants GmbH <?php Co. KG, http://www.webvariants.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */

class dashboardComponents extends policatComponents {

  public function executeTrending() {
    $user = $this->getGuardUser();

    $petition_table = PetitionTable::getInstance();
    $petition_query = $petition_table->queryByUserCampaigns($user, false, true);
    $petition_query = $petition_table->filter($petition_query, new poilcatFilterArray(array(
        PetitionTable::FILTER_ORDER => PetitionTable::ORDER_TRENDING
    )));
    $petition_query->limit(5);

    $this->petitions = $petition_query->execute();

    $widget_table = WidgetTable::getInstance();
    $widget_query = $widget_table->queryByUser($user);
    $widget_table->filter($widget_query, new poilcatFilterArray(array(
        WidgetTable::FILTER_ORDER => WidgetTable::ORDER_TRENDING
    )));
    $widget_query->limit(5);

    $this->widgets = $widget_query->execute();
  }

}
