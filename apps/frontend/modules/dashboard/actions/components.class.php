<?php

class dashboardComponents extends policatComponents {

  public function executeTrending() {
    $user = $this->getGuardUser();

    $petition_table = PetitionTable::getInstance();
    $petition_query = $petition_table->queryByUserCampaigns($user, false, false, true);
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
