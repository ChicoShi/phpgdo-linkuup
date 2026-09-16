<?php
namespace GDO\LinkUUp\Websocket;

use GDO\LinkUUp\LUP_HelpRead;
use GDO\Core\GDO;
use GDO\Websocket\Server\GWS_Command;
use GDO\Websocket\Server\GWS_Commands;
use GDO\Websocket\Server\GWS_Message;

/**
 * Mark a help key as read.
 *
 * @author gizmore
 */
final class LUPWS_HelpRead extends GWS_Command
{

	public function execute(GWS_Message $msg)
	{
        $uid = $msg->user()->getID();
        $key = $msg->readString();
        if (!preg_match('/^[a-zA-Z0-9_:-]{1,64}$/D', $key))
        {
            return $msg->rplyError('err_parameter');
        }
        if (!LUP_HelpRead::table()->countWhere("lhr_user=$uid AND lhr_key=" . GDO::quoteS($key)))
        {
            LUP_HelpRead::blank(['lhr_user' => $uid, 'lhr_key' => $key])->insert();
        }
		$msg->replyBinary($msg->cmd());
	}

}

GWS_Commands::register(0x1191, new LUPWS_HelpRead());
