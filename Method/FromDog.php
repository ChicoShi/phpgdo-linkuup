<?php
declare(strict_types=1);
namespace GDO\LinkUUp\Method;

use GDO\Core\GDT;
use GDO\Core\GDT_Object;
use GDO\Core\GDT_Secret;
use GDO\Core\GDT_String;
use GDO\Core\GDO_Exception;
use GDO\Core\GDO_Hook;
use GDO\Core\GDT_Response;
use GDO\Core\Method;
use GDO\LinkUUp\LUP_Room;
use GDO\LinkUUp\Module_LinkUUp;
use GDO\User\GDO_User;

/** Receive one Dog/Mira response and broadcast it into a LinkUUp room. */
final class FromDog extends Method
{
	/** This machine-to-machine endpoint authenticates with its shared secret. */
	public function isGuestAllowed(): bool
	{
		return true;
	}

	public function gdoParameters(): array
	{
		return [
			GDT_Secret::make('secret')->notNull(),
			GDT_Object::make('room')->notNull()->table(LUP_Room::table()),
			GDT_String::make('message')->notNull()->max(4096),
		];
	}

	public function beforeExecute(): void
	{
		if (!Module_LinkUUp::instance()->isSecretCorrect($this->gdoParameterVar('secret')))
		{
			throw new GDO_Exception('err_lup_connector_secret');
		}
	}

	public function execute(): GDT
	{
		$room = $this->gdoParameterValue('room');
		$minion = GDO_User::getByName('minion');
		GDO_Hook::blank(['hook_message' => json_encode([
			'event' => 'LUPDogMessage',
			'args' => [(string)$room->getID(), (string)$minion->getID(), $this->gdoParameterVar('message')],
		], JSON_THROW_ON_ERROR)])->insert();
		return GDT_Response::make();
	}
}
