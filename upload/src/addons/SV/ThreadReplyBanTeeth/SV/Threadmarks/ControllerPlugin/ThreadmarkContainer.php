<?php

namespace SV\ThreadReplyBanTeeth\SV\Threadmarks\ControllerPlugin;

class ThreadmarkContainer extends XFCP_ThreadmarkContainer
{
    /**
     * @param string $contentType
     *
     * @return array
     *
     * @noinspection PhpMissingReturnTypeInspection
     * @noinspection PhpMissingParamTypeInspection
     */
    protected function getContentTypeWith($contentType)
    {
        $with = parent::getContentTypeWith($contentType);

        switch ($contentType)
        {
            case 'thread':
                $with[] = 'ReplyBans|' . \XF::visitor()->user_id;
                break;
        }

        return $with;
    }
}