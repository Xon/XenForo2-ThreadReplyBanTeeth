<?php

declare(strict_types=1);

namespace SV\ThreadReplyBanTeeth\XF\Pub\Controller;

use XF\Mvc\Reply\Exception as ExceptionReply;

/**
 * @extends \XF\Pub\Controller\Post
 */
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
        $userId = (int)\XF::visitor()->user_id;
        if ($userId !== 0)
        {
            $options = \XF::options();
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
