<?php
declare(strict_types=1);
namespace GDO\LinkUUp;

use GDO\Core\GDT_Enum;

/** The response-time tier booked for a room's Minion. */
final class GDT_MinionSubscription extends GDT_Enum
{
	public const DELAY_30S = 'delay_30s';
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
		// Present the plans from relaxed to immediate response time.
		$this->enumValues(self::DELAY_5M, self::DELAY_4M, self::DELAY_3M, self::DELAY_2M, self::DELAY_1M, self::DELAY_30S);
		$this->emptyLabel('minion_subscription_none');
	}

	public static function delaySeconds(?string $subscription): ?int
	{
		return match ($subscription)
		{
			self::DELAY_5M => 300,
			self::DELAY_4M => 240,
			self::DELAY_3M => 180,
			self::DELAY_2M => 120,
			self::DELAY_1M => 60,
			self::DELAY_30S => 30,
			default => null,
		};
	}

	/** Five minutes cost 200 credits; 30 seconds is the premium 1,500-credit tier. */
	public static function credits(?string $subscription): int
	{
		return match ($subscription)
		{
			self::DELAY_5M => 200,
			self::DELAY_4M => 400,
			self::DELAY_3M => 600,
			self::DELAY_2M => 800,
			self::DELAY_1M => 1000,
			self::DELAY_30S => 1500,
			default => 0,
		};
	}
}
