<?php
namespace GDO\LinkUUp\Method;

use GDO\Core\GDT;
use GDO\Core\Method;
use GDO\Form\GDT_Form;
use GDO\LinkUUp\GDT_LocationSelect;
use GDO\LinkUUp\LUP_Room;
use GDO\User\GDO_User;

/**
 * Statistics overview for room owners and coworkers.
 *
 * @author gizmore
 */
final class Statistics extends Method
{
	public function gdoParameters(): array
	{
		$rooms = LUP_Room::getEditableRooms(GDO_User::current());
        $room = array_shift($rooms);
		return [
			GDT_LocationSelect::make('room')
				->editableRooms()
				->notNull()
				->initial($room ? $room->getID() : '0'),
		];
	}

	public function hasPermission(GDO_User $user, string &$error, array &$args): bool
	{
		$room = $this->gdoParameterValue('room');
		if ($room instanceof LUP_Room && $room->canEdit($user))
		{
			return true;
		}
		$error = 'err_not_allowed';
		return false;
	}

	public function execute(): GDT
	{
		$locationSelect = $this->gdoParameter('room');
		$locationForm = GDT_Form::make('lup-statistics-location')
			->verb(GDT_Form::GET)
			->action(href('LinkUUp', 'Statistics'))
			->slim()
			->noFocus();
		$locationForm->addField($locationSelect);

		$tVars = [
			'room' => $this->gdoParameterValue('room'),
			'locationForm' => $locationForm,
		];
		return $this->templatePHP('page/statistics.php', $tVars);
	}

}
