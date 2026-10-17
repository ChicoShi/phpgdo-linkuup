<?php /** @var \GDO\Form\GDT_Form $form */ ?>
<section class="lup-visibility-booking">
	<h2><?=t('mt_linkuup_bookvisibility', [html($room->getName())])?></h2>
	<div id="lup-visibility-map" class="lup-visibility-map"></div>
	<p id="lup-visibility-cost" class="lup-visibility-cost" aria-live="polite"></p>
	<?=$form->render()?>
</section>
