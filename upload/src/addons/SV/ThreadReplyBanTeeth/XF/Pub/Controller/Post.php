<?php

namespace SV\ThreadReplyBanTeeth\XF\Pub\Controller;

use XF\Mvc\Reply\Exception as ExceptionReply;

class Post extends XFCP_Post
{
    /**
     * @param int   $postId
     * @param array $extraWith
     * @return \XF\Entity\Post
     * @throws ExceptionReply
     * @noinspection PhpMissingReturnTypeInspection
     */
    protected function assertViewablePost($postId, array $extraWith = [])
    {
        $userId = \XF::visitor()->user_id;
        if ($userId)
        {
            $options = $this->options();
            if (($options->svEditReplyBan ?? true) ||
                ($options->svLikeReplyBan ?? true) ||
                ($options->svThreadmarkReplyBan ?? true) ||
                ($options->svDeleteReplyBan ?? true))
            {
                $extraWith[] = 'Thread.ReplyBans|' . $userId;
            }
        }

        return parent::assertViewablePost($postId, $extraWith);
    }
}
