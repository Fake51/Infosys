<?php
exit;
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

require_once __DIR__ . '/../lib/swift/lib/swift_required.php';

putenv('ENVIRONMENT=live');
require __DIR__  . '/../bootstrap.php';

$infosys = createInfosys()
    ->setup();
$dic = $infosys->getDIC();

$log    = $dic->get('Log');
$db     = $dic->get('DB');

$factory = $dic->get('EntityFactory');

$sent = 0;

$emails_seen = array();

$select = $factory->create('Deltagere')->getSelect();
$select->setWhere('annulled', '=', 'nej')
    ->setWhere('signed_up', '>', '0000-00-00')
    ->setWhere('email', '!=', '')
//    ->setWhere('id', '=', '100')
    ->setField('deltagere.*', false);

$participants = $factory->create('Deltagere')->findBySelectMany($select);

foreach ($participants as $participant) {

    if (isset($emails_seen[$participant->email])) {
        continue;
    }

    $emails_seen[$participant->email] = true;

    if ($participant->speaksDanish()) {
        $text = <<<TXT
<p>Kære {$participant->fornavn},</p>
<p></p>
<p>Der er nogle få ting, vi gerne vil oplyse dig om, nu hvor Fastaval nærmer sig.</p>

<p>Du kan altid komme i kontakt med vagthavende general på <strong>40 38 82 87</strong>.</p>

<p>Du kan komme i kontakt med en tryghedsvært på <strong>50 54 20 26</strong>. Tryghedsværterne er tilgængelige fra kl. 10 til baren lukker. Læs mere her, om hvad tryghedsværterne kan hjælpe med: <a href="https://www.fastaval.dk/tryghedsvaerter-2/">https://www.fastaval.dk/tryghedsvaerter-2/</a>.</p>

<p>Vi har opdateret vores alkoholregler og sanktioner, læs dem her: <a href="https://www.fastaval.dk/regler-for-alkohol/">https://www.fastaval.dk/regler-for-alkohol/</a>.</p>

<p><strong>Onsdag kl. 19</strong> afholdes Introtur for nye deltagere på Fastaval. Her kan du stille alle dine spørgsmål, samt lære andre deltagere at kende. Duk op kl. 19 i Fællesrummet.</p>

<p>Holdlæggerne er tæt på at være færdige med at få aktiviteterne på plads, og du kan nu se nogle af de ting, du kommer til at lave på årets val. Vær opmærksom på, at der stadig kan forekomme ændringer.</p>

<p>Du får adgang til at se dine detaljer (din tilmelding plus de forskellige aktiviteter du er kommet på, spil som GDS) ved at gå ind på: <a href="http://infosys.fastaval.dk/deltager/showsignup/{$participant->id}">http://infosys.fastaval.dk/deltager/showsignup/{$participant->id}</a></p>

<p>Du skal bruge din personlige kode for at få adgang - den er: <strong>{$participant->password}</strong></p>

<p>Samme kode bruges også til vores Android og iPhone apps - søg på Fastaval eller Fastavappen hvor du henter apps. Så gem e-mailen.</p>

<p>GDS-puslespillet er blevet lagt men husk at der stadig kan ske ændringer så tjek din deltagerseddel og opdater din app når du ankommer på Fastaval.</p>

<p>Vi vil bede dig tjekke om du har fået en tjans på et tidspunkt hvor du ikke endnu er ankommet på Fastaval - eller i den anden ende, er taget afsted igen. Hvis du står i en situation hvor du allerede nu kan se at du ikke kan tage din tjans vil vi bede dig om at skrive til os på <a href="mailto:gds@fastaval.dk">gds@fastaval.dk</a> - så finder vi en løsning! Skulle det under Fastaval vise sig at du ikke er i stand til at tage din tjans, det kan f.eks. være ved sygdom, så kom og fortæl os det så snart det er muligt for så har vi nemlig længere tid til at finde en til at tage over. Jo mere tid vi har til at finde et par ekstra hænder jo mere overskud og det kan vi godt li’ det der overskud. </p>

<p>Puslespillet har været nemmere at ligge i år, men vi har stadig nogle udfordringer, og vi tænkte at vi ville prøve en anden metode: Nu hvor du kan se dit program, og måske ved om du har tid og overskud til at tage én af tjanserne på denne liste vil Fastaval blive virkeligt taknemmelig, hvis du springer til. Det vil fungere som en almindelig GDS-tjans: Du ved hvornår den er og du får en sms inden. Skriv til os på <a href="mailto:gds@fastaval.dk">gds@fastaval.dk</a> om hvilke tjans du har for øje. Tjanserne vi mangler folk til er:</p>

<ul>
<li>Torsdag: 3 x Opvask, kl. 14-16</li>
<li>Fredag: 3 x Opvask, kl. 14-16</li>
<li>Søndag: 3 x Fest-opvask, kl. 20.30 - 23.30</li>
<li>Søndag: 2 x Fest-barhjælp (alkoholfri bar), kl. 23-01</li>
<li>Søndag: 2 x Caféhjælp, kl. 23-01</li>
<li>Søndag nat: 2 x Caféhjælp, kl. 01-03</li>
</ul>

<p>Hvis du har spørgsmål ang. din tilmelding eller andet, så kontakt os på <a href="mailto:info@fastaval.dk">info@fastaval.dk</a></p>
<p></p>

<p>Vi glæder os til at se dig på Fastaval!</p>

TXT;
        } else {
            $text = <<<TXT
Dear {$participant->fornavn},

<p>There is a few things we would like you to know now that Fastaval is coming up.</p>

<p>You can always call the General at <strong>+45 40 38 82 87</strong>.</p>

<p>You can get in contact with a Tryghedsvært (“Safety Host”) at <strong>+45 50 54 20 26</strong>. The Safety Hosts are available between 10 am and until the bar closes. Read more about what they can help you with here: <a href="https://www.fastaval.dk/tryghedsvaerter/?lang=en">https://www.fastaval.dk/tryghedsvaerter/?lang=en</a>.</p>

<p>We have updated our alcohol policy, read it here: <a href="https://www.fastaval.dk/rules-on-alcohol/?lang=en">https://www.fastaval.dk/rules-on-alcohol/?lang=en</a>.</p>

<p><strong>Wednesday at 19</strong> there will be an Introtour for new participants at Fastaval, where you can ask any questions. Just show up at 19 at the Common Area.</p>

<p>The people assigning players to teams are very busy and in fact you can already see some of the activities you will be participating in at this years convention.  Please note that nothing is set in stone - there may still be last-minute changes.</p>

<p>You can see your details (signup info plus activities you will be participating in) by browsing to: <a href="http://infosys.fastaval.dk/deltager/showsignup/{$participant->id}">http://infosys.fastaval.dk/deltager/showsignup/{$participant->id}</a></p>

<p>You need your personal code to get access - it is: <strong>{$participant->password}</strong></p>

<p>The same code is used for the Android and iPhone apps - search for Fastaval or Fastavappen where you get your apps. So don't delete the email.</p>

<p>The DIY-puzzle is finished but remember that changes can still happen, so check your participant details and update your app when you arrive at Fastaval.</p>

<p>We would like to ask you to check if you have received a shift for a time when you have not yet arrived at Fastaval - or when you have already left. If you know already that you cannot do a shift then please write us at <a href="mailto:gds@fastaval.dk">gds@fastaval.dk</a> - and we will find a solution! If during Fastaval it should happen that you will not be capable of doing your shift, for illness or other reasons, please tell us as soon as possible, as that gives us much better chances of finding someone to take over. The more time we have to find helpers the easier the job for us, the better for everyone.</p>

<p>The DIY puzxle has been easier this year but it did not come without a few challenges, and we thought to try a different tack for the last issues: now that you have access to your programmed activities and might know that you will have some surplus energy for a DIY shift, we would be very grateful if you grab one from the list below. It is a DIY shift as any other - you know when and where and will be reminded as per usual. Write us at <a href="mailto:gds@fastaval.dk">gds@fastaval.dk</a> with details about which shift you will help us out with. Those we need help with are:</p>

<ul>
<li>Thursday: 3 x Dish washing, 14-16</li>
<li>Friday: 3 x Dish washing, 14-16</li>
<li>Sunday: 3 x Party dish washing, 20.30 - 23.30</li>
<li>Sunday: 2 x Party barhelp (alcoholfree bar), 23-01</li>
<li>Sunday: 2 x Caféhelp, 23-01</li>
<li>Sunday night: 2 x Caféhelp, 01-03</li>
</ul>

<p>If you have any questions regarding your signup or other matters, please contact us at: <a href="mailto:info@fastaval.dk">info@fastaval.dk</a></p>
<p></p>

<p>We are looking forward to seeing you at Fastaval!</p>

TXT;
    }

    try {

        $text_body = strip_tags($text);

        $e = new Mail();
        $e->setFrom('info@fastaval.dk', 'Fastaval')
            ->setRecipient($participant->email)
            ->setSubject('Fastaval 2018: almost there ...')
            ->setPlainTextBody($text_body)
            ->setHtmlBody($text);

        $e->send();
        $log->logToDB("Email script har sendt email til " . $participant->email, "Script", 0);

    } catch (Exception $e) {
        echo "Failed to email participant {$deltager['email']}." . PHP_EOL;
        echo $e->getMessage() . PHP_EOL;
    }

    $sent++;
}

$log->logToDB("Email script har sendt emails ud til " . $sent . " deltagere.", "Script", 0);
