<?php
/** Shared, server-rendered navigation for LinkUUp location management. */
global $me;
$current = strtolower($me?->getMethodName() ?? '');
$items = [
    ['Main', 'th-large', 'Übersicht', ['main']],
    ['Rooms', 'map-marker-alt', 'Locations', ['rooms', 'ownerrooms', 'editroom', 'coworkers']],
    ['AddRoom', 'plus', 'Neuer Ort', ['addroom']],
    ['CategoryList', 'shapes', 'Kategorien', ['categorylist', 'addcategory', 'editcategory']],
    ['Statistics', 'chart-line', 'Auswertung', ['statistics']],
];
?>
<nav id="tabs" class="lup-admin-nav" aria-label="Team-Bereiche">
    <?php foreach ($items as [$method, $icon, $label, $active]): ?>
    <a href="<?=href('LinkUUp', $method)?>"<?=in_array($current, $active, true) ? ' aria-current="page"' : ''?>>
        <i class="fas fa-<?=$icon?>" aria-hidden="true"></i><span><?=$label?></span>
    </a>
    <?php endforeach; ?>
</nav>
