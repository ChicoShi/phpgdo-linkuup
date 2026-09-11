<?php
namespace GDO\LinkUUp;

use GDO\Votes\GDO_VoteTable;

final class LUP_RoomVote extends GDO_VoteTable
{

	public function gdoVoteObjectTable() { return LUP_Room::table(); }

	/** The LinkUUp UI deliberately uses a ten-star location scale. */
	public function gdoVoteMax() { return 10; }

}
