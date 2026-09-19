<?php
declare(strict_types=1);
namespace GDO\LinkUUp\Method;

use GDO\Core\GDT;
use GDO\LinkUUp\GDT_RoomSelect;
use GDO\LinkUUp\LUP_Room;
use GDO\QRCode\Method\Render;

/** Two cut-ready A5 flyers on one A4 landscape page. */
final class RoomFlyer extends \GDO\Core\Method
{
	public function getPermission(): ?string { return 'staff'; }
	public function isTrivial(): bool { return false; }

	public function gdoParameters(): array
	{
		return [GDT_RoomSelect::make('room')->notNull()];
	}

	public function execute(): GDT
	{
		$this->getModule()->addCSS('css/lup-room-flyer.css?rev=20260919_2');
		$room = $this->gdoParameterValue('room');
		$url = $room->url_chat();
		return $this->templatePHP('page/room_flyer.php', [
			'room' => $room,
			'url' => $url,
			'qrCode' => Render::renderBase64($url, 600),
		]);
	}
}
