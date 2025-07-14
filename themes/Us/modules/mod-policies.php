<?php
/* WORDPRESS SETUP AFTER switching to Us child theme */
add_action('after_switch_theme', 'wordpress_setup');

function wordpress_setup() {
	// Run only when "Us child" theme is activated
	$current_theme = wp_get_theme();
	if ( $current_theme->get('Name') !== 'Us child' ) {
		return;
	}
	$option = array(
		'blogdescription'               => '',
		'default_comment_status'        => 'closed',
		'use_trackback'                 => '',
		'default_ping_status'           => 'closed',
		'default_pingback_flag'         => '',
		'permalink_structure'           => '/%postname%/',
		'use_smilies'                   => ''
	);
	foreach ( $option as $key => $value ) {
		update_option( $key, $value );
	}
	global $wp_rewrite;
	$wp_rewrite->flush_rules();

	// Custom pages
	$privacypage = array(
		'ID'           => 3,
		'post_content' => '[vc_row][vc_column][vc_column_text]
      <h1>Privacy Policy</h1>
      <ol>
      <li><strong>Wie wij zijn</strong>
      <ol>
      <li>"Wij", "ons" of "onze" betekent <mark>BEDRIJFSNAAM</mark>, met maatschappelijke zetel te <mark>ADRES</mark> en met ondernemingsnummer (<mark>NUMMER</mark>). Wij zullen beschouwd worden als verwerkingsverantwoordelijke voor wat betreft de persoonsgegevens die wij verzamelen in het kader van uw gebruik van onze website.</li>
      <li>Deze privacy policy is er enkel op gericht om u informatie te verschaffen met betrekking tot de verwerking van persoonsgegevens in het kader van onze website. Voor onze privacy praktijken met betrekking tot onze dienstverlening verwijzen wij naar de overeenkomst die u met ons gesloten heeft of de contactpersoon zoals hieronder aangeduid. Onze website, <mark>WEBSITEURL</mark>, is eigendom van en wordt beheerd door <mark>BEDRIJFSNAAM</mark>. De website wordt gehost door <mark>BEDRIJFSNAAM</mark>. Voor verdere informatie, verwijzen wij naar artikel 5 van deze Privacy Policy.</li>
      <li>Uw privacy is belangrijk voor ons. Vandaar hebben wij deze privacy policy ontwikkeld om u meer informatie te verschaffen betreffende de verzameling, mededeling, overdracht en gebruik (“verwerking”) van de persoonsgegevens die u met ons deelt, alsook om u meer informatie te verschaffen betreffende uw rechten. Wij verzoeken u dan ook om deze privacy policy door te nemen.</li>
      <li>Indien u vragen, opmerkingen of klachten heeft met betrekking tot deze Privacy Policy of de verwerking van uw persoonsgegevens of u wenst een verzoek in te dienen in overeenstemming met artikel 4, gelieve dan contact op te nemen met ons via één van volgende manieren:
      <ol>
      <li>Per e-mail: <mark>INFOMAILADRES</mark>, ter attentie van <mark>ZAAKVOERDER</mark></li>
      <li>Per post: <mark>ADRESGEGEVENS</mark> + <mark>NAAM</mark></li>
      </ol>
      </li>
      </ol>
      <em>Deze Privacy Policy werd laatst geüpdatet op 22/05/2018.</em></li>
      </ol>
      <ol start="2">
      <li><strong>Hoe wij uw persoonsgegevens gebruiken en verzamelen</strong>
      <ol>
      <li style="list-style-type: none;">
      <ol>
      <li>Persoonsgegevens worden gedefinieerd als alle informatie over een geïdentificeerde of identificeerbare natuurlijke persoon. Identificeerbaar verwijst naar identificators (zoals naam, identificatienummer, locatiedata, etc.) die kunnen gebruikt worden om een natuurlijke persoon rechtstreeks of onrechtstreeks te identificeren.</li>
      <li>De persoonsgegevens die wij verzamelen, worden verzameld voor de volgende doeleinden:
      <ol>
      <li>Indien u gebruik maakt van het contactformulier op onze website, dan gebruiken wij uw persoonsgegevens om te kunnen antwoorden op uw verzoek, via e-mail, dan wel via telefoon. Uw e-mailadres zal ook worden opgenomen in onze database voor het versturen van onze nieuwsbrief en het verstrekken van informatie betreffende evenementen, seminaries e.d.m.</li>
      <li>Indien u zich inschrijft voor de nieuwsbrief, zal uw e-mailadres gebruikt worden om u onze nieuwsbrieven te verzenden. Bijkomend kunnen wij u op de hoogte houden van evenementen, seminaries, e.d.m. die wij organiseren en die voor u relevant kunnen zijn.</li>
      <li>Wij verwerken uw persoonsgegevens met als doel het supporteren van de website en om uw gebruikservaring te verbeteren. Dit doel strekt zich uit tot het monitoren van de veiligheid, beschikbaarheid, (performance), vermogen, en gezondheid van onze website.</li>
      <li>Wij verwerken uw persoonsgegevens om de toegekende rechten op basis van de toepasselijke wetgeving af te dwingen of na te leven (zoals het verdedigen tegen juridische aanspraken) indien nodig. Wij kunnen uw persoonsgegevens ook gebruiken om onze verplichtingen op basis van de toepasselijke wetgeving na te komen.</li>
      </ol>
      </li>
      <li>Wij verzamelen de volgende categorieën van persoonsgegevens:
      <ol>
      <li>Contactgegevens: Indien u gebruikt maakt van het contactformulier, dan wordt u gevraagd volgende informatie te verstrekken: naam, e-mailadres, telefoonnummer, tewerkstellingsplaats. Dit is informatie die rechtstreeks door u verstrekt wordt.</li>
      <li>Nieuwsbrief: Indien u zich inschrijft voor onze nieuwsbrief, wordt u gevraagd om uw e-mailadres te verstrekken. Dit is informatie die rechtstreeks door u verstrekt wordt.</li>
      <li>Gebruiksinformatie: Wij verwerken persoonsgegevens betreffende uw gebruik van onze website: IP-adres, toestel ID en type, verwijzingsbron, taalinstellingen, browser type, operating system, geografische locatie, duur van het bezoek, bezochte pagina, of informatie betreffende de timing, frequentie en patroon van uw bezoek. Deze informatie kan geaggregeerd worden en kan ons helpen om nuttige informatie te verzamelen betreffende het gebruik van de website. In het geval dat dergelijke gebruiksinformatie geanonimiseerd is (en dus niet herleidbaar is tot u als natuurlijk persoon), dan valt dergelijke informatie niet onder deze Privacy Policy. Deze informatie wordt automatisch verzameld door uw gebruik van de website.</li>
      <li>Log In gegevens: Wij verzamelen persoonsgegevens betreffende uw digitale-marketing-strategie: login gegevens (accountnaam, wachtwoord, e-mailadres). Deze persoonsgegevens worden door u ter beschikking gesteld. Creditcard gegevens worden uitsluitend door onze klant ingevoerd en zonder toestemming onmogelijk te gebruiken.</li>
      </ol>
      </li>
      <li>De rechtsgronden voor het gebruik van uw persoonsgegevens zijn
      <ol>
      <li>Contractuele grond: de verwerking is noodzakelijk voor de uitvoering van de afgemaakte afspraken in de arbeidsovereenkomst.</li>
      <li>Toestemming. <mark>BEDRIJFSNAAM</mark> heeft ondubbelzinnige en expliciete toestemming verkregen van de persoon. We hebben deze toestemming gekregen wanneer de arbeidsovereenkomst werd getekend. U heeft te allen tijde het recht uw toestemming in te trekken. Dit zal geen invloed hebben op de rechtmatigheid van de verwerking die gebeurde voor de intrekking van uw toestemming.</li>
      <li>Gerechtvaardigd belang: de verwerking is noodzakelijk voor de behartiging van de gerechtvaardigde belangen, met name de belofte tot de beste <mark>BEDRIJFSNAAM</mark>-klantervaring.  (bv. het aanbieden van nieuwe diensten). Zonder de verwerking wordt bovengenoemde streef niet uitvoerbaar.</li>
      </ol>
      </li>
      <li>Uw persoonsgegevens zullen enkel worden gebruikt volgens de doeleinden zoals uiteengezet in artikel 2.2.</li>
      </ol>
      </li>
      </ol>
      </li>
      </ol>
      <ol start="3">
      <li><strong>Bijhouden van uw persoonsgegevens en verwijdering</strong>
      <ol>
      <li>Uw persoonsgegevens zullen niet langer worden bijgehouden dan noodzakelijk is om een specifiek doeleinde te behartigen. Omdat het evenwel niet mogelijk is om op voorhand een periode aan te duiden, zal de periode als volgt worden beslist:
      <ol>
      <li>De periode is afhankelijk van welke diensten <mark>BEDRIJFSNAAM</mark> verleent. Deze worden contractueel afgesproken en vastgelegd. Wanneer er gebruikt wordt gemaakt van onze Hosting dienst wordt standaard een overeenkomst van onbepaalde duur opgesteld. Deze overeenkomst is te allen tijde opzegbaar. Termijnen betreffende digitale strategie worden evenals bij het sluiten van de overeenkomst vastgelegd. De duur van deze overeenkomsten zijn afhankelijk van de vraag en contractueel overeengekomen.</li>
      <li>Indien u uw toestemming intrekt of indien u zich verzet zich tegen de verwerking van persoonsgegevens, en dergelijk verzet wordt weerhouden, dan zullen wij uw persoonsgegevens verwijderen. Wij zullen evenwel die persoonsgegevens noodzakelijk om uw voorkeur naar de toekomst toe te respecteren, bijhouden.</li>
      <li>Wij zijn evenwel gerechtigd om uw persoonsgegevens bij te houden indien dit nodig is om te voldoen aan onze wettelijke verplichtingen, om een juridische aanspraak in te stellen of ons te verdedigen tegen dergelijke aanspraak of voor bewijsredenen.</li>
      </ol>
      </li>
      </ol>
      </li>
      </ol>
      <ol start="4">
      <li><strong>Uw rechten als individu</strong>
      <ol>
      <li>Dit artikel bevat een overzicht van uw belangrijkste rechten overeenkomstig de toepasselijke wetgeving bescherming persoonsgegevens. We hebben getracht ze op een duidelijke en leesbare manier voor u samen te vatten.</li>
      <li>Indien u één van uw rechten wenst uit te oefenen, stuurt u ons een schriftelijk verzoek in overeenstemming met artikel 1 van deze Privacy Policy. We trachten zonder onredelijke vertraging, maar in elk geval binnen een termijn van één maand na ontvangst van uw verzoek, op uw verzoek te reageren. Indien wij in de onmogelijkheid verkeren om binnen voornoemde termijn van één maand te reageren en de termijn wensen te verlengen, of in geval wij geen gevolg zullen geven aan uw verzoek, zullen wij u daarvan in kennis stellen.</li>
      <li>Recht op inzage</li>
      <li>In het geval dat wij uw persoonsgegevens verwerken, heeft u recht op toegang tot uw persoonsgegevens, alsook tot bepaalde aanvullende informatie zoals omschreven in deze Privacy Policy.</li>
      <li>U heeft het recht van ons een kopie te ontvangen van uw persoonsgegevens die wij in ons bezit hebben, op voorwaarde dat dit geen nadelige invloed heeft op de rechten en vrijheden van anderen. Het eerste exemplaar wordt u kosteloos bezorgd, maar wij behouden ons het recht voor om een redelijke vergoeding in rekening te brengen wanneer u om meerdere kopieën verzoekt.</li>
      <li>Recht op verbetering</li>
      <li>Als de persoonsgegevens die wij over u bijhouden onjuist of onvolledig zijn, heeft u het recht om ons te verzoeken deze informatie te corrigeren, of om ons te verzoeken – rekening houdend met de doeleinden van de verwerking – te voltooien.</li>
      <li>Recht op gegevensverwijdering / vergetelheid</li>
      <li>Wanneer een van de volgende gevallen van toepassing is, heeft u het recht om – zonder onredelijke vertraging – verwijdering van uw persoonsgegevens te verkrijgen:
      <ol>
      <li>De persoonsgegevens zijn niet langer nodig voor de doeleinden waarvoor zij zijn verzameld of anderszins verwerkt;</li>
      <li>U trekt uw toestemming waarop de verwerking berust in, en er is geen andere rechtsgrond voor de verwerking van uw persoonsgegevens;</li>
      <li>Uw persoonsgegevens zijn onrechtmatig verwerkt;</li>
      <li>Verwijdering van uw persoonsgegevens is noodzakelijk om in overeenstemming te zijn met EU-recht of Belgisch recht;</li>
      </ol>
      </li>
      <li>Er zijn bepaalde uitsluitingen op het recht op gegevenswissing. Deze uitsluitingen omvatten waar verwerking nodig is,
      <ol>
      <li>Voor het uitoefenen van het recht op vrijheid van meningsuiting en informatie;</li>
      <li>Om redenen van algemeen belang op het gebied van volksgezondheid;</li>
      <li>Met het oog op archivering in het algemeen belang, of statistische doeleinden;</li>
      <li>Voor het nakomen van een wettelijke verplichting; of,</li>
      <li>Voor de instelling, uitoefening of onderbouwing van een rechtsvordering.</li>
      <li>Recht op beperking van de verwerking</li>
      </ol>
      </li>
      <li>U heeft het recht de beperking van de verwerking van uw persoonsgegevens te verkrijgen (hetgeen betekent dat de persoonsgegevens alleen door ons mogen worden opgeslagen en alleen voor beperkte doeleinden mogen worden gebruikt), indien één van de volgende elementen van toepassing is:
      <ol>
      <li>U betwist de juistheid van de persoonsgegevens, gedurende een periode die ons in staat stelt de juistheid van de persoonsgegevens te controleren;</li>
      <li>De verwerking is onrechtmatig en u verzet zich tegen het wissen van de persoonsgegevens en verzoekt in de plaats daarvan om beperking van het gebruik ervan;</li>
      <li>Wij hebben uw persoonsgegevens niet meer nodig voor de verwerkingsdoeleinden, maar u heeft deze nodig voor de instelling, uitoefening of onderbouwing van een rechtsvordering; of,</li>
      <li>U heeft bezwaar gemaakt tegen de verwerking, in afwachting van het antwoord op de vraag of de gerechtvaardigde gronden van ons zwaarder wegen dan die van u.</li>
      </ol>
      </li>
      <li>Naast ons recht om uw persoonsgegevens op te slaan, kunnen we deze nog steeds verwerken, maar alleen:
      <ol>
      <li>Met uw toestemming;</li>
      <li>Voor het instellen, uitoefenen of verdedigen van een rechtsvordering;</li>
      <li>Ter bescherming van de rechten van een andere natuurlijke of rechtspersoon; of,</li>
      <li>Om redenen van openbaar belang.</li>
      </ol>
      </li>
      <li>Vooraleer we de beperking van de verwerking van uw persoonsgegevens opheffen, wordt u geïnformeerd.
      Recht op overdraagbaarheid van uw persoonsgegevens / dataportabiliteit.</li>
      <li>Indien de verwerking van uw persoonsgegevens berust op uw toestemming, en de verwerking via geautomatiseerde procedés wordt verricht, heeft u het recht om uw persoonsgegevens te ontvangen in een gestructureerde, gangbare en machine leesbare vorm. Dit recht is echter niet van toepassing, in zoverre dit afbreuk zou doen aan de rechten en vrijheden van anderen.</li>
      <li>U heeft ook het recht om uw persoonsgegevens, indien dit technisch mogelijk is, rechtstreeks door ons naar een ander bedrijf te laten doorzenden.</li>
      <li>Recht van bezwaar. U heeft te allen tijde het recht om – vanwege met uw specifieke situatie verband houdende redenen – bezwaar te maken tegen de verwerking van uw persoonsgegevens, maar alleen in de mate dat de wettelijke basis voor de verwerking is dat de verwerking noodzakelijk is voor:
      <ol>
      <li>De uitvoering van een taak van algemeen belang of bij de uitoefening van een taak in het kader van de uitoefening van het openbaar gezag dat aan ons is verleend;</li>
      <li>De behartiging van onze gerechtvaardigde belangen of die van een derde.</li>
      </ol>
      </li>
      <li>Indien u bezwaar maakt tegen de verwerking van uw persoonsgegevens, zullen wij de persoonsgegevens niet meer verwerken, tenzij wij aantoonbare gerechtvaardigde belangen voor de verwerking kunnen aantonen die zwaarder wegen dan de belangen of de grondrechten en de fundamentele vrijheden van u.</li>
      <li>Wanneer uw persoonsgegevens worden verwerkt ten behoeve van direct marketing, ongeacht of het een aanvankelijke dan wel een verdere verwerking betreft, heeft u het recht te allen tijde en kosteloos bezwaar te maken tegen deze verwerking, ook in het geval van profilering voor zover deze betrekking heeft op de direct marketing. Indien u zo’n bezwaar maakt, zullen wij stoppen met het verwerken van uw persoonsgegevens voor dit doeleinde.</li>
      <li>Recht om klacht in te dienen bij een toezichthoudende autoriteit. Indien u van mening bent dat de door ons uitgevoerde verwerking van uw persoonsgegevens in strijd is met de wetgeving inzake gegevensbescherming, heeft u het recht om een klacht in te dienen bij een toezichthoudende autoriteit die verantwoordelijk is voor de gegevensbescherming. U kunt dit doen in de EU-lidstaat van uw gewone verblijfplaats, van de plaats waar u werkt of van de plaats waar de vermeende inbreuk heeft plaatsgevonden. In België kan je een klacht indienen bij de Privacy Commissie, Drukpersstraat 35, 1000 Brussel (commission@privacycommission.be), <a href="https://www.privacycommission.be/nl/contact">https://www.privacycommission.be/nl/contact</a>.</li>
      </ol>
      </li>
      </ol>
      <ol start="5">
      <li><strong>Het verstrekken van uw persoonsgegevens aan derden</strong>
      <ol>
      <li>Om onze website aan te bieden, werken wij met dienstverleners om uw persoonsgegevens te verwerken en op te slagen. Wij gebruiken de volgende dienstverleners:
      <ol>
      <li><mark>DATABASE PLATFORM NAAM</mark></li>
      <li>Indien overeengekomen een derde partij die toegang heeft tot het website dashboard</li>
      <li>Indien contractueel overeengekomen onder de noemer SEA werken wij samen met Facebook business, Google adwords, LinkedIn business.</li>
      <li>(…)</li>
      </ol>
      </li>
      <li>Het kan zijn dat toegang verstrekken tot uw gegevens nodig is voor wettelijke doeleinden. In dergelijk geval zullen wij genoodzaakt zijn om hieraan te voldoen. Wij mogen uw persoonsgegevens ook verstrekken indien dit nodig is om de vitale belangen van een andere natuurlijke persoon te beschermen.</li>
      <li>Wij verstrekken geen persoonsgegevens aan derde partijen zonder dat dit eerder werd overeengekomen. Onze website maakt gebruik van sociale media plug-ins die het mogelijk maken u te linken naar onze sociale media kanalen of die u in staat stellen om content te delen op uw sociale media kanalen. Deze sociale media kanalen zijn Facebook, Instagram, LinkedIn, Twitter, Google+, Youtube en Pinterest. Indien op u dergelijke link klikt, kan het zijn dat de hiervoor vermelde sociale media partners persoonsgegevens verzamelen, zoals persoonsgegevens betreffende uw profiel.</li>
      <li>Wij staan niet in voor hoe deze sociale media partners uw persoonsgegevens gebruiken. In dergelijk geval zullen zij optreden als verwerkingsverantwoordelijke. Ter uwe informatie sommen we hieronder de relevante links op (deze kunnen evenwel van tijd tot tijd veranderen):
      <ol>
      <li>Facebook: <a href="http://facebook.com/about/privacy">http://facebook.com/about/privacy</a>;</li>
      <li>Instagram: <a href="https://help.instagram.com/155833707900388">https://help.instagram.com/155833707900388</a>;</li>
      <li>LinkedIn: <a href="http://linkedin.com/legal/privacy-policy">http://linkedin.com/legal/privacy-policy</a>;</li>
      <li>Twitter: <a href="http://twitter.com/privacy">http://twitter.com/privacy</a>;</li>
      <li>Google+: <a href="https://www.google.com/intl/en/policies/privacy/">https://www.google.com/intl/en/policies/privacy/</a>;</li>
      </ol>
      </li>
      </ol>
      </li>
      </ol>
      <ol start="6">
      <li><strong>Doorgifte van persoonsgegevens</strong>
      <ol>
      <li>Er gebeurt geen doorgifte van persoonsgegevens buiten de Europese Economische Ruimte.</li>
      <li>Wij verzekeren dat een doorgifte van persoonsgegevens naar een derde land zal gebeuren met inachtneming van de nodige garanties.</li>
      <li>U gaat akkoord met de doorgifte van persoonsgegevens naar een derde land.</li>
      </ol>
      </li>
      </ol>
      <ol start="7">
      <li><strong> Cookies</strong>
      <ol>
      <li>Onze website maakt gebruik van cookies. Voor verdere informatie verwijzen wij u door naar onze <mark><a href="/?page_id=8&preview=true">cookie policy</a></mark>.</li>
      </ol>
      </li>
      </ol>
      <ol start="8">
      <li><strong>Aanpassingen aan de privacy policy </strong>
      <ol>
      <li>Van tijd tot tijd kunnen wij wijzigingen aanbrengen aan deze privacy policy. De meest recente versie van de privacy policy kan altijd geconsulteerd worden op de website.</li>
      </ol>
      </li>
      </ol>
      [/vc_column_text][/vc_column][/vc_row]',
	);
	wp_update_post( $privacypage );


	$homepage = array(
		'ID'           => 2,
		'post_title'   => 'Home',
		'post_content' => '',
		'post_name'    => 'home',
	);
	wp_update_post( $homepage );
	update_option( 'page_on_front', 2 );
	update_option( 'show_on_front', 'page' );


	$cookiepolicy = array(
		'post_title'    => 'Cookie policy',
		'post_status'   => 'draft',
		'post_author'   => 1,
		'post_type'     => 'page',
		'post_content'  => '[vc_row][vc_column][vc_column_text]
      <h1>Cookie policy</h1>
      <ol>
      <li><strong>Wat zijn cookies?</strong>
      <ol>
      <li>Cookies zijn kleine databestanden waarmee een website aan uw browser vraagt om die op uw computer of mobiel apparaat te bewaren wanneer u de website of bepaalde pagina’s bezoekt. De cookies laten de website toe om uw acties of voorkeuren na verloop van tijd te ‘onthouden’. De meeste browsers aanvaarden cookies, maar gebruikers kunnen hun browsers zo instellen dat deze cookies worden geblokkeerd of verwijderd telkens wanneer gewenst.</li>
      <li>Cookies bevatten gewoonlijk de naam van de website waar het cookie vandaan komt, hoe lang het cookie op uw apparaat zal blijven, en een waarde, wat meestal een willekeurig gegeneerd uniek nummer is.</li>
      <li>Sommige cookies zullen worden verwijderd zodra u de website verlaat (de zogenaamde ‘sessiecookies’), andere cookies zullen op uw computer of mobiel apparaat bewaard blijven en zullen ons helpen om u te identificeren als een bezoeker van onze website (de zogenaamde ‘permanente cookies’).</li>
      </ol>
      </li>
      </ol>
      <ol start="2">
      <li><strong>Waarom gebruiken wij cookies?</strong>
      Op onze Website worden cookies voor diverse doeleinden gebruikt.
      <ol>
      <li>Wij gebruiken cookies om de gebruikerservaring op de Website te verbeteren en om uw surfgedrag in kaart te brengen (bv. de pagina’s die u heeft bezocht en de tijd die u op die pagina doorbracht). Cookies maken onze Website gebruiksvriendelijker en staan ons toe om onze Website beter af te stemmen op uw interesses en behoeften. Cookies worden ook gebruikt om de snelheid van uw toekomstige activiteiten en ervaringen op de Website op te drijven. Wij gebruiken bijvoorbeeld cookies om uw taalvoorkeuren te onthouden.</li>
      <li>Wij gebruiken ook cookies om op anonieme wijze samengevoegde statistieken te verzamelen die ons toelaten om te begrijpen hoe onze Website wordt gebruikt en hoe wij onze diensten kunnen verbeteren.</li>
      </ol>
      </li>
      </ol>
      <ol start="3">
      <li><strong>Welke cookies gebruiken wij?</strong>
      <ol>
      <li>Wij gebruiken first party cookies en third party cookies :
      <ol>
      <li>First party cookies zijn cookies die door de Website zelf worden gecreëerd. Deze cookies worden gebruikt om uw gebruikerservaring te optimaliseren.</li>
      <li>Third party cookies zijn cookies die gecreëerd zijn door andere partijen (en dus niet door de Website). Third party cookies op onze Website zijn Facebook, Twitter, LinkedIn en Google Analytics. Google Analytics is Google’s analytische tool die ons helpt te begrijpen hoe u met onze Website omgaat. De tool kan een reeks cookies gebruiken om informatie te verzamelen en om gebruikersstatistieken over de Website te rapporteren zonder individuele bezoekers aan Google persoonlijk kenbaar te maken. Het voornaamste cookie dat door Google Analytics wordt gebruikt, is het ‘___ga’ cookie</li>
      </ol>
      </li>
      <li>Er kan een verder onderscheid gemaakt worden tussen de volgende types cookies:
      <ol>
      <li>Noodzakelijke cookies: Deze zijn noodzakelijk voor de werking van onze Website. Deze omvatten bijvoorbeeld cookies die het u mogelijk maken om in te loggen.
      Onze noodzakelijke cookies worden hier vermeld:
      <table>
      <tbody>
      <tr>
      <td>__ga</td>
      <td>...</td>
      </tr>
      <tr>
      <td>__gid</td>
      <td>...</td>
      </tr>
      </tbody>
      </table>
      </li>
      <li>Analytische cookies/prestatiecookies: Deze cookies laten ons toe om ons webverkeer te analyseren, het aantal gebruikers van onze Website te bekijken en te zien hoe bezoekers op onze Website navigeren.</li>
      <li>Functionele cookies: Deze cookies ‘onthouden’ de keuzes die u maakte op onze Website (bv. taalvoorkeur), wat de Website gebruikersvriendelijker maakt en de gebruikerservaring bevordert.</li>
      <li>Gerichte cookies: Deze tonen ons de pagina’s die u heeft bezocht en de links die u heeft gevolgd zodat de advertenties meer op uw interesses kunnen worden afgestemd.</li>
      </ol>
      </li>
      </ol>
      </li>
      <li><strong>Hoe kan u cookies beheren of verwijderen?</strong>
      <ol>
      <li>U kan op ieder moment cookies beheren of verwijderen via de instellingen van uw internetbrowser, wat u toelaat bepaalde of alle cookies te blokkeren. Het uitschakelen van cookies zal de diensten die wij kunnen aanbieden beperken en kan uw gebruikerservaring beïnvloeden. Het verwijderen van cookies kan tot gevolg hebben dat u handmatig uw voorkeuren moet aanpassen telkens wanneer u onze Website bezoekt. Ga voor meer informatie over het beheren en/of verwijderen van cookies naar de pagina die relevant is voor uw browser:
      <a href="https://support.apple.com/kb/ph19214?locale=en_US">Safari </a>
      <a href="https://support.google.com/chrome/answer/95647?co=GENIE.Platform%3DDesktop&amp;hl=en"> Google Chrome </a>
      <a href="https://support.mozilla.org/en-US/kb/cookies-information-websites-store-on-your-computer"> Mozilla Firefox</a>
      <a href="https://support.microsoft.com/en-us/kb/260971">Internet Explorer</a>
      Raadpleeg voor andere browsers de documentatie van de browseroperator.</li>
      </ol>
      </li>
      </ol>
      [/vc_column_text][/vc_column][/vc_row]',
	);
	wp_insert_post( $cookiepolicy );


	$footer = array(
		'post_title'    => 'Footer',
		'post_status'   => 'publish',
		'post_type'     => 'us_page_block',
		'post_content'  => '[vc_row][vc_column][vc_column_text]
      <p style="text-align: center;">Built with pride and caffeine by <a href="https://sidekick.be/" target="_blank">Sidekick</a></p>
      [/vc_column_text][/vc_column][/vc_row]',
	);
	wp_insert_post( $footer );


	$header = array(
		'post_title'    => 'Header',
		'post_status'   => 'publish',
		'post_type'     => 'us_header',
		'post_content'  => '{"default":{"options":{"breakpoint":"900px","orientation":"hor","sticky":true,"scroll_breakpoint":"100px","transparent":false,"width":"300px","elm_align":"center","shadow":"thin","top_show":false,"top_height":"40px","top_sticky_height":"40px","top_fullwidth":false,"top_centering":false,"middle_height":"100px","middle_sticky_height":"60px","middle_fullwidth":false,"middle_centering":false,"elm_valign":"top","bg_img":"","bg_img_wrapper_start":"","bg_img_size":"cover","bg_img_repeat":"repeat","bg_img_position":"top left","bg_img_attachment":true,"bg_img_wrapper_end":"","bottom_show":false,"bottom_height":"50px","bottom_sticky_height":"50px","bottom_fullwidth":false,"bottom_centering":false},"layout":{"top_left":[],"top_center":[],"top_right":[],"middle_left":["image:1"],"middle_center":[],"middle_right":["menu:1","search:1","cart:1"],"bottom_left":[],"bottom_center":[],"bottom_right":[],"hidden":[]}},"tablets":{"options":{"breakpoint":"900px","orientation":"hor","sticky":true,"scroll_breakpoint":"100px","transparent":false,"width":"300px","elm_align":"center","shadow":"thin","top_show":false,"top_height":"40px","top_sticky_height":"40px","top_fullwidth":false,"top_centering":false,"middle_height":"80px","middle_sticky_height":"50px","middle_fullwidth":false,"middle_centering":false,"elm_valign":"top","bg_img":"","bg_img_wrapper_start":"","bg_img_size":"cover","bg_img_repeat":"repeat","bg_img_position":"top left","bg_img_attachment":true,"bg_img_wrapper_end":"","bottom_show":false,"bottom_height":"50px","bottom_sticky_height":"50px","bottom_fullwidth":false,"bottom_centering":false},"layout":{"top_left":[],"top_center":[],"top_right":[],"middle_left":["image:1"],"middle_center":[],"middle_right":["menu:1","search:1","cart:1"],"bottom_left":[],"bottom_center":[],"bottom_right":[],"hidden":[]}},"mobiles":{"options":{"breakpoint":"600px","orientation":"hor","sticky":true,"scroll_breakpoint":"50px","transparent":false,"width":"300px","elm_align":"center","shadow":"thin","top_show":false,"top_height":"40px","top_sticky_height":"40px","top_fullwidth":false,"top_centering":false,"middle_height":"50px","middle_sticky_height":"50px","middle_fullwidth":false,"middle_centering":false,"elm_valign":"top","bg_img":"","bg_img_wrapper_start":"","bg_img_size":"cover","bg_img_repeat":"repeat","bg_img_position":"top left","bg_img_attachment":true,"bg_img_wrapper_end":"","bottom_show":false,"bottom_height":"50px","bottom_sticky_height":"50px","bottom_fullwidth":false,"bottom_centering":false},"layout":{"top_left":[],"top_center":[],"top_right":[],"middle_left":["image:1"],"middle_center":[],"middle_right":["menu:1","search:1","cart:1"],"bottom_left":[],"bottom_center":[],"bottom_right":[],"hidden":[]}},"data":{"image:1":{"img":"http://demo.sidekick.be/stefanie/wp-content/themes/Impreza/img/us-logo.png","image":"","size":"large","align":"none","style":"","meta":false,"meta_style":"simple","onclick":"none","link":"/","img_transparent":"","animate":"","animate_delay":"","heading_1":"","height":"35px","height_tablets":"30px","height_mobiles":"20px","heading_2":"","height_sticky":"35px","height_sticky_tablets":"30px","height_sticky_mobiles":"20px","hide_for_sticky":false,"hide_for_not_sticky":false,"el_class":"","el_id":"","css":"","design_options":"","color_bg":"","color_border":"","color_text":"","hide_below":0,"width":"","border_radius":""},"menu:1":{"source":"header-menu","font":"body","font_weight":"","text_transform":"","font_style":"","font_size":"1rem","indents":"20px","vstretch":true,"hover_effect":"simple","dropdown_arrow":false,"dropdown_effect":"height","dropdown_font_size":"1rem","dropdown_width":false,"mobile_width":"900px","mobile_layout":"dropdown","mobile_effect_p":"afl","mobile_effect_f":"aft","mobile_font_size":"1.1rem","mobile_dropdown_font_size":"0.9rem","mobile_align":"left","mobile_behavior":true,"mobile_icon_size":"20px","mobile_icon_size_tablets":"20px","mobile_icon_size_mobiles":"20px","hide_for_sticky":false,"hide_for_not_sticky":false,"el_class":"","el_id":"","css":"","design_options":"","color_bg":"","color_border":"","color_text":"","hide_below":0,"width":"","border_radius":""},"search:1":{"text":"Search","layout":"fullwidth","field_width":"240px","field_width_tablets":"200px","product_search":false,"icon":"fas|search","icon_size":"18px","icon_size_tablets":"18px","icon_size_mobiles":"18px","hide_for_sticky":false,"hide_for_not_sticky":false,"el_class":"","el_id":"","css":"","design_options":"","color_bg":"","color_border":"","color_text":"","hide_below":0,"width":"","border_radius":""},"cart:1":{"icon":"fas|shopping-cart","size":"20px","size_tablets":"20px","size_mobiles":"20px","quantity_color_bg":"#e95095","quantity_color_text":"#fff","vstretch":true,"dropdown_effect":"height","hide_for_sticky":false,"hide_for_not_sticky":false,"el_class":"","el_id":"","css":"","design_options":"","color_bg":"","color_border":"","color_text":"","hide_below":0,"width":"","border_radius":""}}}',
	);
	wp_insert_post( $header );
}
wordpress_setup();