<?php
declare(strict_types=1);
namespace GDO\LinkUUp;

use GDO\Core\GDT_Enum;

/** The response-time tier booked for a room's Minion. */
final class GDT_MinionSubscription extends GDT_Enum
{
	public const DELAY_1M = 'delay_1m';
	public const DELAY_2M = 'delay_2m';
	public const DELAY_3M = 'delay_3m';
	public const DELAY_4M = 'delay_4m';
	public const DELAY_5M = 'delay_5m';

	public function gdtDefaultLabel(): ?string
	{
		return 'minion_subscription';
	}

	protected function __construct()
	{
		parent::__construct();
		$this->enumValues(self::DELAY_1M, self::DELAY_2M, self::DELAY_3M, self::DELAY_4M, self::DELAY_5M);
		$this->emptyLabel('minion_subscription_none');
	}

	public static function delayMinutes(?string $subscription): ?int
	{
		return match ($subscription)
		{
			self::DELAY_1M => 1,
			self::DELAY_2M => 2,
			self::DELAY_3M => 3,
			self::DELAY_4M => 4,
			self::DELAY_5M => 5,
			default => null,
		};
	}

	/** Five minutes cost 200 credits; each minute faster adds 200 credits. */
	public static function credits(?string $subscription): int
	{
		$minutes = self::delayMinutes($subscription);
		return $minutes === null ? 0 : (6 - $minutes) * 200;
	}
}
