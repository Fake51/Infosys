<?php

class SignupApiModel extends Model {

  /**
   * Get general configuration related to signup
   */
  public function getConfig($module) {
    $config_file = SIGNUP_FOLDER."config/$module.json";
    if(!is_file($config_file)) die("Config data not found");
    $content = file_get_contents($config_file);

    if ($module == 'main') {
      $config = json_decode($content);
      $config->con_start = $this->config->get('con.start');
      $config->autocomplete = [
        'organizer_categories' => $this->loadOrganizerCategories(),
      ];

      return json_encode($config, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK);
    }

    return $content;
  }

  public function getAllPages() {
    $pages = [];

    $page_files = glob(SIGNUP_FOLDER."pages/*");
    foreach($page_files as $page_file){ // iterate files
      if(!is_file($page_file)) continue;
      $name = basename($page_file, ".json");
      $pages[$name] = json_decode(file_get_contents($page_file));
    }

    return $pages;
  }

  public function getPage($page_id) {
    $page_file = SIGNUP_FOLDER."pages/$page_id.json";
    if(!is_file($page_file)) die("Signup page not found");

    return file_get_contents($page_file);
  }


  /**
   * Get available food items for signup
   */
  public function getFood() {
    $result = (object)[];
    
    // TODO make this a setting
    $category = [
      5 => "breakfast",
      12 => "dinner",
      18 => "dinner",
    ];
    $result->categories = [
      'breakfast' => [
        'en' => 'breakfast',
        'da' => 'morgenmad',
      ],
      'dinner' => [
        'en' => 'dinner',
        'da' => 'aftensmad',
      ]
    ];

    $food =  $this->createEntity('Madtider')->findAll();
    usort($food, function($a, $b) {
      return strtotime($a->dato) - strtotime($b->dato);
    });
    
    foreach($food as $fooditem) {
      $resultitem = (object) [];
      $resultitem->id = $fooditem->id;
      $resultitem->text = [
        'da' => $fooditem->description_da,
        'en' => $fooditem->description_en,
      ];
      $cat = $category[$fooditem->mad_id];
      $day = date('N', strtotime($fooditem->dato));
      $result->days[$day][$cat][] = $resultitem;
    }
    return $result;
  }

  /**
   * Get all activities available for signup
   */
  public function getActivities() {
    // TODO Multiblock
    // TODO Signup Maximum
    $result = (object)[];

    // TODO make this a setting
    $result->categories = [
      'all' => [
        'da' => 'Alle Aktiviteter',
        'en' => 'All Activities',
      ],
      'rolle' => [
        'da' => 'Rollespil',
        'en' => 'Roleplaying',
        'color' => 'lightgreen',
        'include' => ['live'],
      ],
      'live' => [
        'da' => 'Live Rollespil',
        'en' => 'Live Roleplaying',
        'color' => 'mediumpurple',
        'nobutton' => true,
      ],
      'braet' => [
        'da' => 'Brætspil',
        'en' => 'Board games',
        'color' => 'lightblue',
      ],
      'magic' => [
        'da' => 'Magic',
        'en' => 'Magic',
        'color' => 'darkgreen',
      ],
      'junior' => [
        'da' => 'Junior',
        'en' => 'Junior',
        'color' => 'lightgreen',
      ],
      'default' => [
        'da' => 'Øvrige aktiviteter',
        'en' => 'Other activities',
        'color' => 'yellow'
      ],
    ];
    $result->table_headline = [
      'da' => 'Aktiviteter',
      'en' => 'Activities',
    ];
    $result->day_cutoff = 4;
    $result->link_text = [
      'en' => "Read more on the website",
      'da' => "Læs mere på hjemmesiden",
    ];


    $activitities = $this->createEntity('Aktiviteter')->findAll();
    foreach ($activitities as $activity) {
      if ($activity->hidden == 'ja' || $activity->kan_tilmeldes == 'nej' || $activity->type == 'system') continue;
      $activity_info = (object)[];
      $activity_info->exclusive = $activity->tids_eksklusiv == 'ja';
      $activity_info->gm = $activity->spilledere_per_hold > 0;
      $activity_info->min_age = $activity->getMinAge();
      $activity_info->max_age = $activity->getMaxAge();
      $activity_info->type = $activity->type;
      $activity_info->wp_id = $activity->wp_link;
      $activity_info->lang = [
        'en' => str_contains($activity->sprog, 'engelsk'),
        'da' => str_contains($activity->sprog, 'dansk'),
      ];
      $activity_info->desc = [
        'en' => $activity->description_en,
        'da' => $activity->foromtale,
      ];
      $activity_info->title = [
        'da' => $activity->navn,
        'en' => $activity->title_en
      ];

      $result->activities[$activity->id] = $activity_info;
    }
    $runs = $this->createEntity('Afviklinger')->findAll();
    foreach ($runs as $run) {
      if (!isset($result->activities[$run->aktivitet_id])) continue;
      $run_info = (object)[];
      $run_info->id = $run->id;
      $run_info->activity = $run->aktivitet_id;
      $run_info->start = [
        'day' => date('N', strtotime($run->start)),
        'hour' => intval(date('H', strtotime($run->start))),
        'min' => intval(date('i', strtotime($run->start))),
        'stamp' => strtotime($run->start),
      ];
      $run_info->end = [
        'day' => date('N', strtotime($run->slut)),
        'hour' => intval(date('H', strtotime($run->slut))),
        'min' => intval(date('i', strtotime($run->slut))),
        'stamp' => strtotime($run->slut),
      ];
      $day = $run_info->start['day'];
      if($run_info->start['hour'] < $result->day_cutoff) $day--; // Put runs that start late together with the day before
      $result->runs[$day][] = $run_info;
    }
    
    foreach($result->runs as $day => $runs) {
      usort($result->runs[$day], function($a, $b) {
        return $a->start['stamp'] - $b->start['stamp'];
      });
    }


    return $result;
  }

  /**
   * Get available wear for signup
   */
  public function getWear() {
    $result = (object) [];
    $result->wear = [];

    $wear = $this->createEntity('Wear');
    $wear_list = $wear->findAll();
    foreach($wear_list as $item) {
      $result_item = [
        'id' => $item->id,
        'name' => [
          'en' => $item->title_en,
          'da' => $item->navn,
        ],
        'desc' => [
          'en' => $item->description_en,
          'da' => $item->beskrivelse,
        ],
        'position' => $item->position,
        'prices' => [],
        'max_order' => $item->max_order,
        'variants' => $item->getVariants(),
        'images' => $item->getImages(),
      ];
      $prices = $item->getWearpriser();
      foreach($prices as $price) {
        $result_item['prices'][] = [
          'user_category' => $price->brugerkategori_id,
          'price' => $price->pris,
        ];
      }
      $result->wear[] = $result_item;
    }

    return $result;
  }

  public function submitSignup($data) {
    $participant = $this->createEntity('DummyParticipant');
    return $this->applySignup($data['signup'], $participant, $data['lang']);
  }

  public function confirmSignup($data) {
    if (!isset($data['info'])) {
      // Create barebone participant
      $participant = $this->createEntity('Deltagere');
      $participant->password = sprintf('%06d', mt_rand(0, 999999));
      $participant->fornavn = "";
      $participant->efternavn = "";
      $participant->adresse1 = "";
      $participant->postnummer = "";
      $participant->by = "";
      $participant->brugerkategori_id = 1;
      $participant->birthdate = '0000-00-00 00:00:00';
      $participant->medical_note = '';
      $participant->gcm_id = '';
      $participant->insert();
    } else {
      $participant = $this->createEntity('Deltagere')->findById($data['info']['id']);
      if (!$participant || $participant->password != $data['info']['pass']) {
        return [$data['info'],['errors' => ['confirm' => [['type' => 'wrong_info']]]]];
      }
    }
    $result = $this->applySignup($data['signup'], $participant, $data['lang']);
    
    if(count($result['errors']) == 0) {
      // Update participant
      if(!$participant->update()) {
        $result['errors']['confirm'][] = ['type' => 'database'];
      }
    }

    return [
      $result,
      $participant,
    ];
  }

  /**
   * Create reverse lookup from infosys_id to page item
   */
  private function createLookup($page) {
    $lookup = [];
    foreach($page->sections as $skey => $section) {
      if(!isset($section->items)) continue;
      foreach($section->items as $ikey => $item) {
        if (isset($item->infosys_id)) {
          $lookup[$item->infosys_id] = [
            'item' => $item,
            'disabled' => ($item->disabled ?? false) || ($section->disabled ?? false),
            'required' => $item->required ?? false,
          ];
        }
      }
    }
    return $lookup;
  }

  /**
   * Apply signup data to a participant
   */
  public function applySignup($data, $participant, $lang) {
    $errors = $categories = [];
    $is_alea = $is_organizer = $junior_plus = false;
    $total = 0;
    $junior_note = "";
    $sprog = [];
    $sleeping_areas = $author = [];
    $config = [
      'main' => json_decode($this->getConfig('main')),
      'activities' => json_decode($this->getConfig('activities')),
    ];

    $participant->signed_up = date('Y-m-d H:i:s');
    // Reset orders
    $participant->removeEntrance();
    $participant->removeFood();
    $participant->removeActivitySignups();
    $participant->removeAllWear();
    $participant->removeDiySignup();

    $column_info = $this->createEntity('Deltagere')->getColumnInfo();

    foreach($data as $category => $items) {
      $entries = [];
      $category_total = 0;
      
      // Load file for the category/page
      $page_file = SIGNUP_FOLDER."pages/$category.json";
      if(!is_file($page_file)) {
        $errors[$category][] = [
          'type' => 'missing_page',
          'info' => $category,
        ];
        continue;
      }
      $page = json_decode(file_get_contents($page_file));
      $lookup = $this->createLookup($page);

      // Check for missing
      foreach($lookup as $key => $value) {
        if ($value['required'] && !$value['disabled'] && !isset($items[$key])) {
          if ($value['item']->no_submit) continue;
          $errors[$category][] = [
            'type' => 'required',
            'info' => $category." ".$key,
            'id' => $key,
          ];
        }
      }

      foreach($items as $key => $value) {
        $price = 0;
        $extra = [];

        if (isset($lookup[$key]) && $lookup[$key]['disabled']) {
          $errors[$category][] = [
            'type' => 'disabled',
            'info' => $category." ".$key,
            'id' => $key,
          ];
          continue;
        }

        $key_parts = explode(":", $key, 2);
        if (count($key_parts) == 1) {
          switch($key) {
            case 'participant':
              $bk  = $this->createEntity('BrugerKategorier');
              $participant->brugerkategori_id = $bk->findByname($value)->id;
              $is_organizer = $value == 'Arrangør';
              break;

            case 'author':
              if ($value == 'off') continue 2;
              $author[] = 'role';
              break;
 
            case 'designer':
              if ($value == 'off') continue 2;
              $author[] = 'board';
              break;

            case 'organizer':
              if ($value == 'off') continue 2;
              break;

            case 'wear_orders':
              // We assume the user category has been set at this point
              $user_category = $this->createEntity('BrugerKategorier')->findById($participant->brugerkategori_id);
              if(!$user_category) {
                $errors[$category][] = [
                  'type' => 'no_user_category',
                  'info' => "$key $value",
                ];
                continue 2;
              }

              foreach($value as $wear_order) {
                $wear = $this->createEntity('Wear')->findById($wear_order['wear_id']);
                $wear_prices = $wear->getWearpriser($user_category);
  
                // If there is no price for junior, check regular participant prices
                if (count($wear_prices) == 0 && $junior_plus) {
                  $user_category = $this->createEntity('BrugerKategorier')->getDeltager();
                  $wear_prices = $wear->getWearpriser($user_category);
                }

                if (count($wear_prices) == 0) {
                  $errors[$category][] = [
                    'type' => 'no_wear_price',
                    'info' => "wear_order",
                    'user_category_id' => $user_category->id,
                    'wear_id' => $wear->id,
                  ];
                  continue 3;
                }
                
                if(!$participant->setWearOrder($wear_prices[0], $wear_order['amount'], $wear_order['attributes'] ?? [])) {
                  $errors[$category][] = [
                    'type' => 'wear_order_fail',
                    'info' => "wear_order",
                    'user_category_id' => $user_category->id,
                    'wear_id' => $wear->id,
                    'amount' => $wear_order['amount'],
                    'attributes' => $wear_order['attributes'],
                  ];
                  continue 3;
                }

                $price = $wear_prices[0]->pris * $wear_order['amount'];
                $entries[] = [
                  'special_module' => 'wear',
                  'key' => $key,
                  'value' => $wear_order,
                  'wear_id' => $wear->id,
                  'price' => $price,
                  'amount' => $wear_order['amount'],
                  'attributes' => $wear_order['attributes'] ?? [],
                  'single_price' => $wear_prices[0]->pris,
                ];
        
                $category_total += $price;
              }
              continue 2;

            default:
              if (!isset($column_info[$key])) {
                $errors[$category][] = [
                  'type' => 'no_field',
                  'info' => "participant $key",
                ];
                continue 2;
              }

              if ($value == 'on') {
                if ($key == 'sober_sleeping') $sleeping_areas[] = 'sober';
                if ($key == 'sovesal') $sleeping_areas[] = 'organizer';
              }

              $do_value = $value;
              if (!$participant->is_dummy) {
                $valid = $participant->getValidColumnValues($key);
                if (isset($valid['type']) && $valid['type'] == 'enum') {
                  $do_value = $value == 'on' ? 'ja' : $do_value;
                  $do_value = $value == 'off' ? 'nej' : $do_value;
                  if (!in_array($do_value, $valid['values'])) {
                    $errors[$category][] = [
                      'type' => 'wrong_enum',
                      'id' => $key,
                      'value' => $value,
                      'valid' => $valid,
                    ];
                    continue 2;
                  }
                }
                if (isset($valid['type']) && $valid['type'] == 'int' && $value == "") $do_value = 0;
              }

              $participant->$key = $do_value;
              if ($value == "" || $value == 0 || $value == 'off') {
                // No need to show empty fields on breakdown
                continue 2;
              }
          }
        } else {
          $key_cat = $key_parts[0];
          $key_item = $key_parts[1];

          if ($value === 'off') continue;
          
          switch($key_cat) {
            case 'junior':

              if ($key_item == 'entrance') {
                // Add entry for junior participants
                $entry = $this->createEntity('Indgang');
                $select = $entry->getSelect();
                $select->setWhere('type', 'like', '%Junior%');
                $entry = $entry->findBySelect($select);
                $participant->setIndgang($entry);
                $price = $entry->pris;
                break;
              }

              if ($key_item == 'plus') {
                $junior_plus = true;
                break;
              }

              $labels = [
                'contact_name' => 'Navn',
                'contact_number' => 'Telefon',
                'contact_mail' => 'Email',
              ];
              if (!isset($labels[$key_item])) {
                $errors[$category][] = [
                  'type' => 'no_field',
                  'info' => "$key_cat $key_item",
                ];
                continue 2;
              }
              $junior_note .= "$labels[$key_item]: $value\n";
              break;

            case 'entry':
              $entry = $this->createEntity('Indgang');
              $select = $entry->getSelect();
              if ($key_item == 'partout') {
                $age = $participant->getAge(new DateTime($config['main']->con_start));
                $select->setWhere('type', 'like', '%Indgang - Partout%');
                if ($age < $config['main']->age_young) {
                  if ($age < $config['main']->age_kid) {
                    $select->setWhere('type', 'like', '%Barn%');  
                  } else {
                    $select->setWhere('type', 'like', '%Ung%');
                  }
                }
                // NB! We assume Alea signup is earlier or same page
                if ($age >= $config['main']->age_kid && ($is_alea || $items->{'misc:alea'})) {
                  $select->setWhere('type', 'like', '%Alea%');
                }
                // NB! We assume organizer setting is before this
                if ($age >= $config['main']->age_kid && $is_organizer) {
                  $select->setWhere('type', 'like', '%Arrangør%');
                }
              } else {
                $day = intval($key_item) -1;
                $date = new DateTime($this->config->get('con.start'));
                $date->add(new DateInterval("P{$day}D"));
                $select->setWhereDate('start', '=', $date->format('Y-m-d'));
                $select->setWhere('type', '=', 'Indgang - Enkelt');
              }
              $entry = $entry->findBySelect($select);
              if (!$entry) {
                $errors[$category][] = [
                  'type' => 'no_entry',
                  'info' => "$key_cat:$key_item $value",
                  'age'  => $age,
                  'alea' => ($is_alea || $items->{'misc:alea'}),
                  'organizer' => $is_organizer,
                ];
                continue 2;
              }
              $participant->setIndgang($entry);
              $price = $entry->pris;
              break;

            case 'sleeping':
              $entry = $this->createEntity('Indgang');
              $select = $entry->getSelect();
              if ($key_item == 'partout') {
                $select->setWhere('type', 'like', 'Overnatning - Partout%');
                // NB! We assume organizer setting is before this
                if ($age >= $config['main']->age_kid && $is_organizer) {
                  $select->setWhere('type', 'like', '%Arrangør%');
                }
              } else {
                $day = intval($key_item) -1;
                $date = new DateTime($this->config->get('con.start'));
                $date->add(new DateInterval("P{$day}D"));
                $select->setWhereDate('start', '=', $date->format('Y-m-d'));
                $select->setWhere('type', '=', 'Overnatning - Enkelt');
              }
              $entry = $entry->findBySelect($select);
              if (!$entry) {
                $errors[$category][] = [
                  'type' => 'no_entry',
                  'info' => "$key_cat:$key_item $value",
                  'organizer' => $is_organizer,
                ];
                continue 2;
              }
              $participant->setIndgang($entry);
              $price = $entry->pris;
              break;

            case 'sleeping_area':
              $sleeping_areas[] = $key_item;
              break;

            case 'misc':
              $entry = $this->createEntity('Indgang');
              $select = $entry->getSelect();
          
              switch($key_item) {
                case 'mattres':
                  $select->setWhere('type', '=', 'Leje af madras');
                  break;

                case 'party':
                  $select->setWhere('type', '=', 'Ottofest');
                  break;
  
                case 'bubbles':
                  $select->setWhere('type', '=', 'Ottofest - Champagne');
                  break;

                case 'alea':
                  $is_alea = true;
                  $select->setWhere('type', '=', 'Alea medlemskab');
                  break;
                
                case 'ticket_fee':
                  $select->setWhere('type', '=', 'Billetgebyr');
                  break;

                case 'extra_support':
                  $amount = min(9, floor(intval($value) / 100));
                  $select->setWhere('type', '=', "Rig onkel - {$amount}00");
                  $participant->rig_onkel = 'ja';
                  $value = 'on';
                  break;
  
                case 'secret_support':
                  $amount = min(9, floor(intval($value) / 100));
                  $select->setWhere('type', '=', "Hemmelig onkel - {$amount}00");
                  $participant->hemmelig_onkel = 'ja';
                  $value = 'on';
                  break;

                default:
                  $errors[$category][] = [
                    'type' => 'no_field',
                    'info' => "$key_cat $key_item",
                  ];
                  continue 3;
              }

              $entry = $entry->findBySelect($select);
              $participant->setIndgang($entry);
              $price = $entry->pris;
              break;

            case 'food':
              if (intval($value) != 0) {
                $food = $this->createEntity('Madtider')->findById($value);  
              } else {
                $food = $this->createEntity('Madtider')->findById($key_item);
              }
              if (!$food) {
                $errors[$category][] = [
                  'type' => 'no_food',
                  'info' => "$key_cat:$key_item $value",
                ];
                continue 2;
              }
              $participant->setMad($food);
              $price = $food->getMad()->pris;
              break;

            case 'hero':
              [$day, $time] = explode("-", $key_item, 2);
              $day = intval($day) -3;
              $date = new DateTime($this->config->get('con.start'));
              $date->add(new DateInterval("P{$day}D"));
              $date_string = $date->format('Y-m-d');
              switch($time) {
                case 'morning':
                  $period = "7-12";
                  break;

                case 'afternoon':
                  $period = "12-17";
                  break;

                case 'evening':
                  $period = "17-24";
                  break;

                default:
                $errors[$category][] = [
                  'type' => 'unknown_time',
                  'info' => "$key_cat:$key_item $value",
                ];
                continue 3;
              }

              $participant->setGDSTilmelding(null, "$date_string $period");
              break;

            case 'activity':
              // TODO check for alder
              // TODO check for max tilmeldinger 
              $run = $this->createEntity('Afviklinger')->findbyId($key_item);
              if (!$run) {
                $errors[$category][] = [
                  'type' => 'no_run',
                  'info' => "$key_cat:$key_item $value",
                ];
                continue 2;
              }
              $value = intval($value);
              $choice_count = count($config['activities']->choices->prio->$lang);
              if ($value <= $choice_count) {
                $participant->setAktivitetTilmelding($run, $value, 'spiller');
              } else {
                $participant->setAktivitetTilmelding($run, 0, 'spilleder');
                if ($value == $choice_count + 2) {// SL + 1st prio
                  $participant->setAktivitetTilmelding($run, 1, 'spiller');
                }
              }
              $price = $run->getAktivitet()->pris;
              break;

            case 'activity_language':
              $sprog[] = $key_item;
              break;

            case 'note':
              $participant->setNote($key_item, $value);
              if ($value == "") continue 2; // Don't show empty values on breakdown
              break;

            default:
              $errors[$category][] = [
                'type' => 'no_field',
                'info' => "$key_cat $key_item",
              ];
          }
        }
        $entries[] = array_merge([
          'key' => $key,
          'value' => $value,
          'price' => $price,
        ], $extra);

        $category_total += $price;
      }

      if (count($entries) > 0) {
        $entries[] = [
          'key' => 'sub_total',
          'value' => $category_total,
        ];
        $categories[$category] = $entries;
        $total += $category_total;
      }
    }

    // Languages
    $participant->setCollection('sprog', $sprog);

    // Sleeping area
    $participant->setCollection('sleeping_area', $sleeping_areas);

    // Author of rpg or designer of board game
    $participant->forfatter = count($author) > 0 ? 'ja' : 'nej';
    if (count($author)) $participant->scenarie = "";
    $participant->setCollection('author', $author);

    // Check for actual organizer selection
    if ($is_organizer && $participant->forfatter == 'nej' && !$participant->work_area) {
      $errors['organizer'][] = [
        'type' => 'no_organizer_selection',
        'id' => 'participant',
      ];
    }

    // Notes
    if ($junior_note) $participant->setNote('junior_ward', $junior_note);

    return [
      'errors' => $errors,
      'categories' => $categories,
      'total' => $total,
    ];
  }

  /**
   * Retrieve participant data in format used by signup plugin
   */
  public function loadSignup($id, $pass) {
    // Find by ID
    $participant = $this->createEntity('Deltagere')->findById($id);
    // Find by email instead
    if(!$participant) {
      $select = $this->createEntity('Deltagere')->getSelect();
      $select->setWhere('email', '=', $id);
      $select->setWhere('password', '=', $pass);

      $participant = $this->createEntity('Deltagere')->findBySelect($select);
    }
    if (!$participant || $participant->password != $pass) {
      return ['signup' => [],'errors' => [['type' => 'wrong_info']]];
    }

    $column_info = $participant->getColumnInfo();
    $signup = ['id' => $participant->id]; // We return participant id in case they used email for loading.
    $errors = [];
    $has_entrance = false;
    $config = [
      'activities' => json_decode($this->getConfig('activities')),
    ];

    foreach($this->getAllPages() as $page_id => $page_data) {
      $lookup = $this->createLookup($page_data);
      // Collect simple properties
      foreach($lookup as $key => $info) {
        $value = '';
        $key_parts = explode(":", $key, 2);
        if (count($key_parts) == 1) {
          switch($key) {
            case 'participant':
              $bid = $participant->brugerkategori_id;
              $bk  = $this->createEntity('BrugerKategorier')->findById($bid);
              if ($bk->isArrangoer()) {
                $value = 'Arrangør';
              } else {
                $value = $bk->navn;
              }
              break;

            case 'organizer':
              $value = $participant->arbejdsomraade ? 'on' : '';
              break;

            case 'author':
              $value = in_array('role', $participant->getCollection('author'));
              break;

            case 'designer':
              $value = in_array('board', $participant->getCollection('author'));
              break;
              
            case 'birthdate':
              $value = substr($participant->birthdate, 0, 10);
              break;

            case 'ready_mandag':
            case 'ready_tirsdag':
              $signup['together:prepare'] = 'on';
              // Notice no break here
            default:
              if (!isset($column_info[$key])) {
                
                // Value is the same as a different field (like email confirm)
                if($equals = $lookup[$key]['item']->equals) {
                  $value = $participant->$equals;
                  break;
                }
                
                // Value isn't submitted
                if($lookup[$key]['item']->no_submit) continue 2;
                
                $errors[] = [
                  'type' => 'no_field',
                  'info' => "participant $key",
                  'item' => $info['item'],
                ];
                continue 2;
              }

              $value = $participant->$key;
              if ($value == 'ja') $value = 'on';
              if ($value == 'nej') $value = '';
          }
        }
        $signup[$key] = $value;
      }
    }

    // Collect entrance items
    foreach($participant->getIndgang() as $entrance) {
      switch(true) {
        case $entrance->isPartout():
          if ($entrance->isEntrance()) {
            $signup['entry:partout'] = 'on';
            $has_entrance = true;
          } else {
            $signup['sleeping:partout'] = 'on';
          }
          break;

        case  $entrance->isDayTicket() || $entrance->isSleepTicket():
          $type = $entrance->isDayTicket() ? 'entry' : 'sleeping';
          $entrance = $type == 'entry' ? true : $entrance;
          $start = new DateTime($entrance->start);
          $day = intval($start->format('d')) -2;
          $signup["$type:$day"] = 'on';
          break;

        case $entrance->type == 'Indgang - Junior':
          $signup['junior:entrance'] = 'on';
          break;

        case $entrance->type == 'Leje af madras':
          $signup['misc:mattres'] = 'on';
          break;

        case $entrance->type == 'Ottofest':
          $signup['misc:party'] = 'on';
          break;

        case $entrance->type == 'Ottofest - Champagne':
          $signup['misc:bubbles'] = 'on';
          break;

        case $entrance->type == 'Alea medlemskab':
          $signup['misc:alea'] = 'on';
          break;

        case $entrance->type == 'Billetgebyr':
          $signup['misc:ticket_fee'] = 'on';
          break;

        case preg_match("/Rig onkel - (\d+)/", $entrance->type, $matches):
          $signup['misc:extra_support'] = $matches[1];
          break;

        case preg_match("/Hemmelig onkel - (\d+)/", $entrance->type, $matches):
          $signup['misc:secret_support'] = $matches[1];
          break;

        default:
          $errors[] = [
            'type' => 'unknown_entry',
            'info' => $entrance->type,
          ];
      }
    }

    // Collect food order
    foreach($participant->getMadtider() as $food) {
      if ($food->isDinner()) {
        $day = date('N', strtotime($food->dato));
        $signup['food:dinner'.$day] = $food->id;
      } else {
        $signup['food:'.$food->id] = $food->id;
      }
    }

    // Collect hero-task signup
    foreach($participant->getGDSTilmeldinger() as $hero_signup) {
      $period = $hero_signup->period;
      preg_match("/(\d{4}-\d{2}-\d{2}) (\d{2}-\d{2})/", $period, $match);
      $day = date('N', strtotime($match[1]));
      switch($match[2]) {
        case "7-12":
          $time = 'morning';
          break;

        case "12-17":
          $time = 'afternoon';
          break;

        case "17-24":
          $time = 'evening';
          break;

        default:
          $errors[] = [
            'type' => 'unknown_time',
            'period' => $period,
          ];
          continue 2;
      }
      $signup["hero:$day-$time"] = 'on';
      $signup['together:hero'] = 'on';
    }

    // Collect activity signup
    foreach ($participant->getTilmeldinger() as $run_signup) {
      $run_id = $run_signup->afvikling_id;
      $prio = $run_signup->prioritet;
      $type = $run_signup->tilmeldingstype;
      if ($type == 'spilleder') {
        $prio = count($config['activities']->choices->prio->en) +1;
        if($run_signup->getAktivitet()->type == 'braet') {
          $signup['together:gm'] = 'on';
        } else {
          $signup['together:rules'] = 'on';
        }
      }
      if (isset($signup["activity:$run_id"])) {
        // We have a combination of player and GM signup
        $signup["activity:$run_id"] += $prio;
      } else {
        $signup["activity:$run_id"] = $prio;
      }
    }

    // Activity languages
    foreach($participant->getCollection('sprog') as $sprog) {
      $signup['activity_language:'.$sprog] = 'on';
    }

    // Activity languages
    foreach($participant->getCollection('sleeping_area') as $area) {
      $signup['sleeping_area:'.$area] = 'on';
    }

    // Collect wear orders
    $signup["wear_orders"] = [];
    foreach($participant->getWear() as $wear_order) {
      $wearprice = $wear_order->getWearpris();
      
      $order = [
        'wear_id' => $wear_order->getWear()->id,
        'amount' => $wear_order->antal,
        'price' => $wearprice->pris,
        'price_category' => $wearprice->brugerkategori_id,
        'attributes' => [],
      ];
      
      foreach($wear_order->getAttributes() as $type => $att) {
        $order['attributes'][$type] = $att['id'];
      }

      $signup["wear_orders"][$wear_order->id] = $order;
    }

    // Get notes
    foreach($participant->note as $key => $note) {
      if ($key == 'junior_ward') {
        $ids = [
          'Navn' => 'contact_name',
          'Telefon' => 'contact_number',
          'Email' => 'contact_mail',
        ];
        foreach(explode("\n", $note->content) as $line) {
          [$label, $value] = explode(":", $line);
          $signup['junior:'.$ids[$label]] = trim($value);
        }
      } else {
        $signup['note:'.$key] = $note->content;
      }
    }

    // Check for junior:plus
    if ($has_entrance && $signup['participant'] == 'Juniordeltager') {
      $signup['junior:plus'] = 'on';
    }

    return [
      'signup' => $signup,
      'errors' => $errors,
    ];
  }

  private function loadOrganizerCategories() {
    $query = "SELECT id, name_da, name_en FROM organizer_categories";
    $result = $this->db->query($query);
    $categories = [];
    foreach($result as $row) {
      $categories[$row['id']] = [
        'en' => $row['name_en'],
        'da' => $row['name_da'],
        'id' => $row['id'],
      ];
    }
  
    return $categories;
  }
}