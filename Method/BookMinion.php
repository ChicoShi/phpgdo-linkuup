<?php
declare(strict_types=1);
namespace GDO\LinkUUp\Method;

use DateTimeImmutable;
use DateTimeZone;
use GDO\Core\GDT;
use GDO\Core\GDT_Object;
use GDO\DB\Database;
use GDO\Form\GDT_AntiCSRF;
use GDO\Form\GDT_Form;
use GDO\Form\GDT_Submit;
use GDO\Form\MethodForm;
use GDO\LinkUUp\GDT_MinionSubscription;
use GDO\LinkUUp\LUP_Room;
use GDO\User\GDO_User;
use GDO\User\GDO_UserSetting;

/** Book or change the response-time tier for a location's Minion. */
final class BookMinion extends MethodForm
{
	public function isTrivial(): bool { return false; }

	public function gdoParameters(): array
	{
		return [GDT_Object::make('room')->table(LUP_Room::table())->notNull()];
	}

	private function room(): LUP_Room
	{
		return LUP_Room::paramFrom($this, 'room');
	}

	public function hasPermission(GDO_User $user, string &$error, array &$args): bool
	{
		if ($this->room()->canEdit($user))
		{
			return true;
		}
		$error = 'err_not_allowed';
		return false;
	}

	public function getMethodTitle(): string
	{
		return t('mt_linkuup_bookminion', [$this->room()->gdoDisplay('room_name')]);
	}

	protected function createForm(GDT_Form $form): void
	{
		$current = $this->room()->gdoVar('room_minion_subscription');
		$form->addFields(
			GDT_MinionSubscription::make('subscription')->notNull()->initial($current ?: GDT_MinionSubscription::DELAY_5M),
			GDT_AntiCSRF::make(),
		);
		$form->actions()->addField(GDT_Submit::make('book')->label('btn_book_minion')->icon('smart_toy'));
	}

	public function formValidated(GDT_Form $form): GDT
	{
		$room = $this->room();
		$user = GDO_User::current();
		$newSubscription = $form->getFormVar('subscription');

		// Every booking pays the selected monthly rate in advance. Renewing early
		// adds a calendar month after the already paid expiry instead of losing it.
		$cost = GDT_MinionSubscription::credits($newSubscription);
		if (!$this->chargeCredits($user, $cost))
		{
			return $this->error('err_lup_minion_credits', [$cost, $this->creditBalance($user)]);
		}

		$now = new DateTimeImmutable('now', new DateTimeZone('UTC'));
		$currentExpiry = $room->gdoVar('room_minion_expire');
		$start = $currentExpiry ? new DateTimeImmutable($currentExpiry, new DateTimeZone('UTC')) : $now;
		if ($start < $now)
		{
			$start = $now;
		}
		$expires = $start->modify('+1 month')->format('Y-m-d H:i:s');
		$room->saveVars([
			'room_minion_subscription' => $newSubscription,
			'room_minion_expire' => $expires,
		]);
		return $this->redirectMessage('msg_lup_minion_booked', [
			$room->gdoDisplay('room_name'),
			t('enum_' . $newSubscription),
			$room->gdoDisplay('room_minion_expire'),
			$cost,
		], $room->href_edit());
	}

	/** One conditional update prevents concurrent bookings from overspending credits. */
	private function chargeCredits(GDO_User $user, int $cost): bool
	{
		if ($cost <= 0)
		{
			return true;
		}
		$table = GDO_UserSetting::table()->gdoTableIdentifier();
		$userId = (int)$user->getID();
		Database::instance()->queryWrite("UPDATE {$table} SET uset_var=uset_var-{$cost} WHERE uset_user={$userId} AND uset_name='credits' AND uset_var+0>={$cost}");
		return Database::instance()->affectedRows() === 1;
	}

	private function creditBalance(GDO_User $user): int
	{
		$setting = GDO_UserSetting::getById($user->getID(), 'credits');
		return $setting ? (int)$setting->gdoVar('uset_var') : 0;
	}
}
