<?php

use GDO\JPGraph\GDT_GraphDateselect;
use GDO\LinkUUp\GDT_RoomGraph;
use GDO\LinkUUp\LUP_Room;
use GDO\LinkUUp\Method\GraphMessagecount;
use GDO\LinkUUp\Method\GraphUsercount;
use GDO\UI\GDT_Link;
use GDO\User\GDO_User;

/**
 * @var LUP_Room $room
 * @var GDO\Form\GDT_Form $locationForm
 */
$canPrintFlyers = GDO_User::current()->isStaff();

	$inputs = [
		'room' => $room->getID(),
	];
	$categoryVisuals = [
		'1' => ['fas fa-globe-europe', 'category-country'], '2' => ['fas fa-city', 'category-city'],
		'3' => ['fas fa-cocktail', 'category-bar'], '4' => ['fas fa-beer', 'category-pub'],
		'5' => ['fas fa-coffee', 'category-cafe'], '6' => ['fas fa-briefcase', 'category-business'],
		'7' => ['fas fa-shopping-bag', 'category-shop'], '8' => ['fas fa-place-of-worship', 'category-religion'],
		'9' => ['fas fa-cut', 'category-salon'], '10' => ['fas fa-map-marked-alt', 'category-town'],
		'11' => ['fas fa-compact-disc', 'category-club'], '12' => ['fas fa-theater-masks', 'category-culture'],
		'13' => ['fas fa-futbol', 'category-sport'], '14' => ['fas fa-utensils', 'category-food'],
		'15' => ['fas fa-tree', 'category-outdoors'], '16' => ['fas fa-school', 'category-education'],
		'17' => ['fas fa-university', 'category-education'], '18' => ['fas fa-hospital', 'category-health'],
		'19' => ['fas fa-hotel', 'category-hotel'],
	];
	$categoryVisual = $categoryVisuals[$room->getCategory()] ?? ['fas fa-map-marker-alt', 'category-default'];

	?>
<header class="lup-statistics-heading"><h1>Deine Location im Überblick</h1><p>Besuche und Gespräche – nachvollziehbar für den gewählten Zeitraum.</p></header>
<div class="lup-statistics-room-select">
	<?=$locationForm->renderForm()?>
</div>
    <div class="lup-room-statistics">
        <div class="statistics-room col-xs-12 col-sm-3">
			<div class="lup-stat-category-icon <?=$categoryVisual[1]?>" title="Kategorie"><i class="<?=$categoryVisual[0]?>"></i></div>
			<span class="lup-stat-category-name">Kategorie</span>
            <h2><?php
				echo $room->gdoDisplay('room_name'); ?></h2>
			<?php if ($info = $room->gdoDisplay('room_info')): ?>
				<p class="lup-stat-room-description"><?=$info?></p>
			<?php endif; ?>
            <a class="lup-stat-qrcode" href="<?=$room->href_qrcode()?>" title="QR-Code für <?=$room->gdoDisplay('room_name')?>"><i class="fas fa-qrcode"></i><span>QR</span></a>
            <div><?=GDT_Link::make()->href($room->url_chat())->render()?></div>
			<?php if ($room->canEdit(GDO_User::current())): ?>
				<a class="lup-stat-edit-room" href="<?=$room->href_edit()?>"><i class="fas fa-edit"></i><span><?=t('btn_edit')?></span></a>
			<?php endif; ?>
			<?php if ($canPrintFlyers): ?>
				<a class="lup-stat-edit-map" href="<?=href('LinkUUp', 'LocationMap', '&room=' . $room->getID())?>"><i class="fas fa-draw-polygon"></i><span><?=t('mt_linkuup_locationmap')?></span></a>
			<?php endif; ?>
			<?php if ($canPrintFlyers): ?>
				<a class="lup-stat-flyer" href="<?=href('LinkUUp', 'RoomFlyer', '&room=' . $room->getID() . '&_ajax=1')?>"><i class="fas fa-print"></i><span><?=t('room_flyer')?></span></a>
			<?php endif; ?>
        </div>
        <div class="col-xs-12 col-sm-9 grapics"><h2>Aktivität im Zeitraum</h2>
			<?=GDT_GraphDateselect::make('date')->initial('this_year')->addClass('lup-graph-select')->withToday(false)->withYesterday(false)->render()?>
			<input class="lup-graph-custom-date" type="date" name="start" aria-label="Zeitraum von" disabled="disabled" hidden/>
			<input class="lup-graph-custom-date" type="date" name="end" aria-label="Zeitraum bis" disabled="disabled" hidden/>
            <div class="lup-room-graph-container">
                <div class="statistics-usercount col-xs-12 col-sm-6"><h3>Besuche</h3>
					<?=GDT_RoomGraph::make()->room($room)->graphMethod(GraphUsercount::make()->appliedInputs($inputs))->withoutDateInput()->render()?>
                </div>
                <div class="statistics-messagecount col-xs-12 col-sm-6"><h3>Nachrichten</h3>
					<?=GDT_RoomGraph::make()->room($room)->graphMethod(GraphMessagecount::make()->appliedInputs($inputs))->withoutDateInput()->render()?>
                </div>
            </div>
        </div>
    </div>
