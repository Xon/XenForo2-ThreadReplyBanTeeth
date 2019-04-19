<?php

namespace SV\ThreadReplyBanTeeth\SV\Threadmarks\ControllerPlugin;

class Threadmark extends XFCP_Threadmark
{
    protected function getContentTypeWith($contentType)
    {
        $with = parent::getContentTypeWith($contentType);

        switch ($contentType)
        {
            case 'post':
                $with[] = 'Thread.ReplyBans|' . \XF::visitor()->user_id;
                break;
        }

        return $with;
    }
}