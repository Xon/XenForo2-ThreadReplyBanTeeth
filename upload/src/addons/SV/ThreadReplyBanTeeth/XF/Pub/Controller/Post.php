<?php

namespace SV\ThreadReplyBanTeeth\XF\Pub\Controller;

use XF\Mvc\ParameterBag;

class Post extends XFCP_Post
{
    /**
     * @param int   $postId
     * @param array $extraWith
     * @return \XF\Entity\Post
     */
    protected function assertViewablePost($postId, array $extraWith = [])
    {
        $userId = \XF::visitor()->user_id;
        if ($userId)
        {
            $options = \XF::app()->options();
            if ($options->SV_ThreadReplyBanTeeth_EditBan ||
                $options->SV_ThreadReplyBanTeeth_LikeBan ||
                $options->SV_ThreadReplyBanTeeth_DeleteBan)
            {
                $extraWith[] = 'Thread.ReplyBans|' . $userId;
            }
        }

        return parent::assertViewablePost($postId, $extraWith);
    }
}
