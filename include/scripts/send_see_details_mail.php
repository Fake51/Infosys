<?php
/**
 * Copyright (C) 2010-2012 Peter Lind
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/gpl.html>.
 *
 * PHP version 5
 *
 * this file inits the framework. It needs a couple of set definitions (both paths should end with '/':
 * - INCLUDE_PATH: the full path to the include folder
 * - PUBLIC_PATH: the full path to the public folder
 * this one is optional, but as parts of the framework use it, best to init it
 * - PUBLIC_URI: the base part of the uri for the public folder
 *
 * @category  Infosys
 * @package   Scripts
 * @author    Peter Lind <peter.e.lind@gmail.com>
 * @copyright 2010-2012 Peter Lind
 * @license   http://www.gnu.org/licenses/gpl.html GPL 3
 * @link      http://www.github.com/Fake51/Infosys
 */

set_time_limit(0);

require_once '../lib/swift/lib/swift_required.php';

putenv('ENVIRONMENT=live');
require '../bootstrap.php';

$infosys->setup();
$dic = $infosys->getDIC();

$where = 'WHERE annulled = "nej" AND signed_up > "0000-00-00"';
$where = 'WHERE id = 532';

$log    = $dic->get('Log');
$db     = $dic->get('DB');
$emails = $db->query('SELECT id, fornavn, international, password, email FROM deltagere ' . $where . ' ORDER BY id');

$sent = 0;

$emails_seen = array();

foreach ($emails as $deltager) {
    if (isset($emails_seen[$deltager['email']])) {
        continue;
    }

    $emails_seen[$deltager['email']] = true;

    if ($deltager['international'] == 'nej') {
        $text = <<<TXT
<p>Kære {$deltager['fornavn']},</p>
<p></p>
<p>Der er nogle få ting, vi gerne vil oplyse dig om, nu hvor Fastaval nærmer sig.</p>

<p>Du kan altid komme i kontakt med vagthavende general på <strong>40 38 82 87</strong>.</p>

<p>Du kan komme i kontakt med en tryghedsvært på <strong>50 54 20 26</strong>. Tryghedsværterne er tilgængelige fra kl. 10 til baren lukker. Læs mere her, om hvad tryghedsværterne kan hjælpe med: <a href="https://www.fastaval.dk/tryghedsvaerter-2/">https://www.fastaval.dk/tryghedsvaerter-2/</a>.</p>

<p>Vi har opdateret vores alkoholregler og sanktioner, læs dem her: <a href="https://www.fastaval.dk/regler-for-alkohol/">https://www.fastaval.dk/regler-for-alkohol/</a>.</p>

<p><strong>Onsdag kl. 19</strong> afholdes Introtur for nye deltagere på Fastaval. Her kan du stille alle dine spørgsmål, samt lære andre deltagere at kende. Duk op kl. 19 i Fællesrummet.</p>

<p>Holdlæggerne er tæt på at være færdige med at få aktiviteterne på plads, og du kan nu se nogle af de ting, du kommer til at lave på årets val. Vær opmærksom på, at der stadig kan forekomme ændringer.</p>

<p>Du får adgang til at se dine detaljer (din tilmelding plus de forskellige aktiviteter du er kommet på, spil som GDS) ved at gå ind på: <a href="http://infosys.fastaval.dk/deltager/showsignup/{$deltager['id']}">http://infosys.fastaval.dk/deltager/showsignup/{$deltager['id']}</a></p>

<p>Du skal bruge din personlige kode for at få adgang - den er: <strong>{$deltager['password']}</strong></p>

<p>Samme kode bruges også til vores Android og iPhone apps og til Fastavals mobilsite. Så gem e-mailen.</p>

<p>Hvis du har spørgsmål ang. din tilmelding eller andet, så kontakt os på <a href="mailto:info@fastaval.dk">info@fastaval.dk</a></p>
<p></p>

<p>Vi glæder os til at se dig på Fastaval!</p>

TXT;
        } else {
            $text = <<<TXT
Dear {$deltager['fornavn']},

<p>There is a few things we would like you to know now that Fastaval is coming up.</p>

<p>You can always call the General at <strong>+45 40 38 82 87</strong>.</p>

<p>You can get in contact with a Tryghedsvært (“Safety Host”) at <strong>+45 50 54 20 26</strong>. The Safety Hosts are available between 10 am and until the bar closes. Read more about what they can help you with here: <a href="https://www.fastaval.dk/tryghedsvaerter/?lang=en">https://www.fastaval.dk/tryghedsvaerter/?lang=en</a>.</p>

<p>We have updated our alcohol policy, read it here: <a href="https://www.fastaval.dk/rules-on-alcohol/?lang=en">https://www.fastaval.dk/rules-on-alcohol/?lang=en</a>.</p>

<p><strong>Wednesday at 19</strong> there will be an Introtour for new participants at Fastaval, where you can ask any questions. Just show up at 19 at the Common Area.</p>

<p>The people assigning players to teams are very busy and in fact you can already see some of the activities you will be participating in at this years convention.  Please note that nothing is set in stone - there may still be last-minute changes.</p>

<p>You can see your details (signup info plus activities you will be participating in) by browsing to: <a href="http://infosys.fastaval.dk/deltager/showsignup/{$deltager['id']}">http://infosys.fastaval.dk/deltager/showsignup/{$deltager['id']}</a></p>

<p>You need your personal code to get access - it is: <strong>{$deltager['password']}</strong></p>

<p>The same code is used for the Android and iPhone apps, as well as for the mobile site. So don't delete the email.</p>

<p>If you have any questions regarding your signup or other matters, please contact us at: <a href="mailto:info@fastaval.dk">info@fastaval.dk</a></p>
<p></p>

<p>We are looking forward to seeing you at Fastaval!</p>

TXT;
    }

    try {

        $text_body = strip_tags($text);

        $e = new Mail(array('info@fastaval.dk' => 'Fastaval'), $deltager['email'], "Fastaval 2018: almost there ...", $text_body);
        $e->addHtmlBody($text);
        $e->send();
        $log->logToDB("Email script har sendt email til " .$deltager['email'], "Script", 0);

    } catch (Exception $e) {
        echo "Failed to email participant {$deltager['email']}." . PHP_EOL;
        echo $e->getMessage() . PHP_EOL;
    }

    $sent++;
}

$log->logToDB("Email script har sendt emails ud til " . $sent . " deltagere.", "Script", 0);
