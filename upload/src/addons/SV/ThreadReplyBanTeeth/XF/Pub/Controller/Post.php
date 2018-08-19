<?php

namespace SV\ThreadReplyBanTeeth\XF\Pub\Controller;

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
            if ($options->svEditReplyBan ||
                $options->svLikeReplyBan ||
                $options->svThreadmarkReplyBan  ||
                $options->svDeleteReplyBan)
            {
                $extraWith[] = 'Thread.ReplyBans|' . $userId;
            }
        }

        return parent::assertViewablePost($postId, $extraWith);
    }
}
