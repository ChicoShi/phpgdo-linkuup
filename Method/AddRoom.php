<?php
namespace GDO\LinkUUp\Method;

use GDO\Core\GDO;
use GDO\Core\GDT;
use GDO\Core\GDT_Hook;
use GDO\Form\GDT_AntiCSRF;
use GDO\Form\GDT_Form;
use GDO\Form\GDT_Submit;
use GDO\Form\MethodCrud;
use GDO\LinkUUp\LUP_Room;
use GDO\LinkUUp\LUP_Global;
use GDO\LinkUUp\LUP_Workers;
use GDO\User\GDO_User;
use GDO\UI\GDT_Redirect;

final class AddRoom extends MethodCrud
{

	public function isTrivial(): bool { return false; }

	/**
	 * Rooms are a VIP feature. Keep this check on the server as well as in the
	 * app menu, so that a copied backend URL cannot bypass it.
	 */
	public function hasPermission(GDO_User $user, string &$error, array &$args): bool
	{
		if (!parent::hasPermission($user, $error, $args))
		{
			return false;
		}
		if ($user->isAdmin() || LUP_Global::isVIP($user))
		{
			return true;
		}
		$error = 'err_lup_vip_only';
		return false;
	}

	public function hrefList(): string { return href('LinkUUp', 'Rooms'); }

	public function featureRead(): bool { return false; }
	public function featureUpdate(): bool { return false; }
	public function featureDelete(): bool { return false; }

	public function renderPage(): GDT
	{
		return $this->templatePHP('page/add_room.php', [
			'form' => $this->getForm(),
		]);
	}

	public function gdoTable(): GDO { return LUP_Room::table(); }

	/**
	 * A draft only needs its name and title. Everything spatial belongs in the
	 * room editor and the map editor after the room exists.
	 */
	protected function createForm(GDT_Form $form): void
	{
		$room = LUP_Room::table();
		$form->addFields(
			$room->gdoColumn('room_name')->label('name'),
			$room->gdoColumn('room_info')->label('title'),
			GDT_AntiCSRF::make(),
		);
		$form->actions()->addField(GDT_Submit::make('create')->label('btn_create')->icon('add')->onclick([$this, 'onCreate']));
	}

	/** A draft always continues straight to its detailed editor. */
	public function onCreate(GDT_Form $form): GDT
	{
		$gdo = LUP_Room::blank($form->getFormVars());
		$gdo->setVar('room_owner', GDO_User::current()->getID());
		$gdo->setVar('room_enabled', '0');
		$this->beforeCreate($form, $gdo);
		$gdo->insert();
		$this->afterCreate($form, $gdo);
		return GDT_Redirect::to($gdo->href_edit());
	}

	public function afterCreate(GDT_Form $form, GDO $gdo): void
	{
		$this->updateOwnerPermissions($gdo);
		GDT_Hook::callWithIPC('LUPRoomAdded', $gdo);
	}

	public function updateOwnerPermissions(LUP_Room $room)
	{
		if ($owner = $room->getOwner())
		{
			LUP_Workers::addWorker($room, $owner);
		}
	}

	public function afterUpdate(GDT_Form $form, GDO $gdo): void
	{
		$this->updateOwnerPermissions($gdo);
	}

}
