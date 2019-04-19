<?php

namespace SV\ThreadReplyBanTeeth\SV\Threadmarks\ControllerPlugin;

class Threadmark extends XFCP_Threadmark
{
    protected function getContentTypeWith($contentType, array $with = [])
    {
        $with = parent::getContentTypeWith($contentType, $with);

        switch ($contentType)
        {
            case 'post':
                $with[] = 'Thread.ReplyBans|' . \XF::visitor()->user_id;
                break;
        }

        return $with;
    }
}