<?php

namespace SV\ThreadReplyBanTeeth\SV\Threadmarks\ControllerPlugin;

class ThreadmarkContainer extends XFCP_ThreadmarkContainer
{
    protected function getContentTypeWith($contentType, array $with = [])
    {
        $with = parent::getContentTypeWith($contentType, $with);

        switch ($contentType)
        {
            case 'thread':
                $with[] = 'ReplyBans|' . \XF::visitor()->user_id;
                break;
        }

        return $with;
    }
}