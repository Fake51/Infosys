<?php

class MailModel extends Model {
  public function getRecipients($type, $filter_recent = 1) {

    if (intval($type) !== 0) {
      return [$this->createEntity('Deltagere')->findById(intval($type))];
    }

    $participants = $this->createEntity('Deltagere')->findAll();

    if ($filter_recent) {
      $this->filterOutRecentMails($participants, $type, $filter_recent);
    }

    switch ($type) {
      case 'setup':
        foreach ($participants as $id => $participant) {
          if ($participant->ready_mandag !== 'ja' && $participant->ready_tirsdag !== 'ja') {
            unset($participants[$id]);
          }
        }
        break;
    }

    return $participants;
  }

  private function filterOutRecentMails(array &$participants, $type, $days_ago = 1) {
    $select = $this->createEntity('LogItem')->getSelect();
    $select->setWhere('message', 'LIKE', "%System sent $type mail to participant%")
        ->setRawWhere('DATE(created) > ADDDATE(NOW(), INTERVAL -'.intval($days_ago).' day)', 'AND');

    $filter_ids = [];
    foreach ($this->createEntity('LogItem')->findBySelectMany($select) as $log_item) {
      if (preg_match('/\(ID: (\d+) \)/', $log_item->message, $matches)) {
        $filter_ids[$matches[1]] = true;
      }
    }

    foreach ($participants as $index => $participant) {
      if (isset($filter_ids[$participant->id])) unset($participants[$index]);
    }
  }

}