<?php

namespace SV\ThreadReplyBanTeeth\SV\Threadmarks\ControllerPlugin;

class Threadmark extends XFCP_Threadmark
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
            case 'post':
                $with[] = 'Thread.ReplyBans|' . \XF::visitor()->user_id;
                break;
        }

        return $with;
    }
}