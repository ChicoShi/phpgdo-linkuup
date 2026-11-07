<?php
/** Private views: authenticated applicant or freshly checked admin only. */
$escape=static fn($v)=>htmlspecialchars((string)$v,ENT_QUOTES|ENT_SUBSTITUTE,'UTF-8');
$status=['pending'=>'In Prüfung','approved'=>'Freigeschaltet','rejected'=>'Abgelehnt','revoked'=>'Freigabe widerrufen'];
?>
<section class="gdo-operator-applications <?= $review?'':'operator-application-entry' ?>">
<?php if(!$review && !$rows): ?>
<header class="operator-invitation">
 <span class="operator-kicker">FÜR GASTGEBER UND BETREIBER</span>
 <h1>Dein Ort.<br>Digital genauso einladend.</h1>
 <p>Zeig Gästen, was deine Location besonders macht – mit deiner eigenen Karte und einem gemeinsamen Überblick über die Bestellungen.</p>
 <a class="operator-start" href="#operator-application-form">Meine Location anmelden <span aria-hidden="true">→</span></a>
 <small>Zuerst prüfen wir deine Angaben. Angebot und Vertrag klären wir persönlich vor der Freischaltung.</small>
</header>
<section class="operator-benefits" aria-label="Deine Möglichkeiten">
 <article><span aria-hidden="true">01</span><h2>Deine Karte, aktuell.</h2><p>Speisen, Getränke und Preise selbst pflegen. Gäste sehen die veröffentlichte Karte direkt bei deiner Location.</p></article>
 <article><span aria-hidden="true">02</span><h2>Jeder Tisch im Blick.</h2><p>Bestellungen mit Artikeln, Beträgen und Tischnummern bündeln – vom Eingang bis zur Ausgabe.</p></article>
 <article><span aria-hidden="true">03</span><h2>Du gibst den Start frei.</h2><p>Erst Karte und Tische einrichten, dann Bestellungen freigeben. Neue Bestellungen lassen sich bei Bedarf pausieren.</p></article>
</section>
<section class="operator-showcase" aria-labelledby="operator-showcase-title">
 <span class="operator-kicker">EIN BLICK IN DEN BETREIBERBEREICH</span><h2 id="operator-showcase-title">So kann dein Alltag aussehen.</h2>
 <p>Aufnahmen unserer aktuellen Vorschau mit erfundenen Beispieldaten. Zahlungen sind darin simuliert; eine echte Zahlungsanbindung ist noch nicht freigegeben.</p>
 <figure><img src="/GDO/LinkUUp/img/operator/guest-preview.png" alt="Beispiel einer digitalen Karte mit Getränken, Preisen, Suche und Warenkorb" width="1120" height="1100" loading="lazy"><figcaption><strong>Für deine Gäste</strong><span>Karte ansehen, Auswahl treffen. Bestellen wird nach Standortprüfung vor Ort und deiner Freigabe möglich.</span></figcaption></figure>
 <figure><img src="/GDO/LinkUUp/img/operator/operator-preview.png" alt="Beispiel der Betreiberübersicht mit neuen Bestellungen an Tisch 4 und einer Bestellung in Arbeit an Tisch 7" width="1120" height="1100" loading="lazy"><figcaption><strong>Für dein Team</strong><span>Neue, laufende und abgeschlossene Bestellungen an einem Ort.</span></figcaption></figure>
</section>
<section class="operator-next"><h2>Vom Antrag zu deinem Betrieb.</h2><ol><li><strong>Location vorstellen</strong><span>Einrichtung und Kontaktperson angeben.</span></li><li><strong>Gemeinsam klären</strong><span>Wir prüfen die Zuständigkeit und besprechen Angebot und Vertrag.</span></li><li><strong>Einrichten und starten</strong><span>Nach Freischaltung Karte und Tische vorbereiten.</span></li></ol></section>
<?php endif; ?>
<h2 id="operator-application-form"><?= $review?'Betriebsanträge':($rows?'Mein Betrieb':'Erzähl uns von deiner Location') ?></h2>
<?php if($review): ?><p>Nur nach geprüfter Zuständigkeit und abgeschlossener Vertragsklärung freischalten. Die Bestätigung dokumentiert die manuelle Prüfung; sie ist kein juristischer Nachweis. Neue Einrichtungen müssen zuerst durch das Team regulär angelegt und zugeordnet werden; danach ist ein Antrag für diese eigene Location nötig.</p><?php endif; ?>
<?php if(!$review): ?><p class="operator-entry-intro">Location anmelden, Prüfung abwarten, danach Karte und Bestellungen verwalten. Deinen Antragsstatus findest du auf dieser Seite.</p><?php elseif(!$rows): ?><p>Noch keine Anträge.</p><?php endif; ?>
<?php foreach($rows as $row): ?>
<article><h3><?= $escape($row['oa_business']) ?></h3>
<p>Antrag #<?= $escape($row['oa_id']) ?> · <?= $escape($status[$row['oa_status']]??'Unbekannt') ?> · <?= $escape($row['oa_created']) ?></p>
<?php if($review): ?>
<dl><?php foreach(['oa_version'=>'Version','oa_user'=>'Antragsteller-ID','oa_room'=>'Location-ID','oa_kind'=>'Einrichtung','oa_address'=>'Anschrift','oa_contact'=>'Kontaktperson','oa_email'=>'E-Mail','oa_phone'=>'Telefon','oa_details'=>'Rolle und Nutzung','oa_review_note'=>'Letzter Prüfvermerk'] as $key=>$label): ?><dt><?= $escape($label) ?></dt><dd><?= nl2br($escape($row[$key])) ?></dd><?php endforeach; ?></dl>
<?php endif; ?>
</article>
<?php endforeach; ?>
<?= $form->render() ?>
</section>
