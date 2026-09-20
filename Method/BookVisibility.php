<?php
declare(strict_types=1);
namespace GDO\LinkUUp\Method;

use GDO\Core\GDT;
use GDO\Core\GDT_Float;
use GDO\Core\GDT_Object;
use GDO\Core\Javascript;
use GDO\DB\Database;
use GDO\Form\GDT_AntiCSRF;
use GDO\Form\GDT_Form;
use GDO\Form\GDT_Submit;
use GDO\Form\MethodForm;
use GDO\LinkUUp\LUP_Room;
use GDO\LinkUUp\Module_LinkUUp;
use GDO\User\GDO_User;
use GDO\User\GDO_UserSetting;

/** Increases a room's catalogue visibility for credits; reducing it remains free. */
final class BookVisibility extends MethodForm
{
	private ?LUP_Room $roomCache = null;
	public function isTrivial(): bool { return false; }
	public function gdoParameters(): array { return [GDT_Object::make('room')->table(LUP_Room::table())->notNull()]; }
	private function room(): LUP_Room
	{
		if ($this->roomCache === null)
		{
			$room = LUP_Room::paramFrom($this, 'room');
			$this->roomCache = LUP_Room::table()->getById($room->getID()) ?: $room;
		}
		return $this->roomCache;
	}

	public function hasPermission(GDO_User $user, string &$error, array &$args): bool
	{
		if ($this->room()->canEdit($user)) { return true; }
		$error = 'err_not_allowed';
		return false;
	}

	public function getMethodTitle(): string { return t('mt_linkuup_bookvisibility', [$this->room()->gdoDisplay('room_name')]); }

	public function renderPage(): GDT
	{
		$this->getModule()->addCSS('css/lup-visibility-booking.css');
		$this->getModule()->addJS('js/lup-visibility-booking.js?rev=1');
		$room = $this->room();
		Javascript::addJSPreInline('window.LUP_VISIBILITY_BOOKING=' . json_encode([
			'lat' => $room->getLat(), 'lng' => $room->getLng(), 'view' => $room->getView(),
			'creditsPerKm' => Module_LinkUUp::instance()->cfgCreditsViewKM(),
		], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) . ';');
		return $this->templatePHP('page/book_visibility.php', ['form' => $this->getForm(), 'room' => $room]);
	}

	protected function createForm(GDT_Form $form): void
	{
		$form->addFields(
			GDT_Float::make('room_view')->min(0.010)->max(42000.0)->step(0.001)->initial($this->room()->gdoVar('room_view')),
			GDT_AntiCSRF::make(),
		);
		$form->actions()->addField(GDT_Submit::make()->label('btn_book_visibility')->icon('visibility'));
	}

	public function formValidated(GDT_Form $form): GDT
	{
		$room = $this->room();
		$newView = (float)$form->getFormVar('room_view');
		$cost = $this->cost(max(0.0, $newView - $room->getView()));
		$user = GDO_User::current();
		if ($cost && !$this->chargeCredits($user, $cost))
		{
			$form->error('err_lup_visibility_credits', [$cost, $this->creditBalance($user)]);
			return $this->renderPage();
		}
		$room->saveVar('room_view', (string)$newView);
		return $this->redirectMessage('msg_lup_visibility_booked', [$room->gdoDisplay('room_name'), $newView, $cost], $room->href_edit());
	}

	private function cost(float $increase): int
	{
		$module = Module_LinkUUp::instance();
		return $increase > 0 ? (int)ceil($increase * $module->cfgCreditsViewKM()) : 0;
	}

	private function chargeCredits(GDO_User $user, int $cost): bool
	{
		$table = GDO_UserSetting::table()->gdoTableIdentifier();
		$id = (int)$user->getID();
		Database::instance()->queryWrite("UPDATE {$table} SET uset_var=uset_var-{$cost} WHERE uset_user={$id} AND uset_name='credits' AND uset_var+0>={$cost}");
		return Database::instance()->affectedRows() === 1;
	}

	private function creditBalance(GDO_User $user): int
	{
		$setting = GDO_UserSetting::getById($user->getID(), 'credits');
		return $setting ? (int)$setting->gdoVar('uset_var') : 0;
	}
}
