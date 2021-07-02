<?php

namespace SV\ThreadReplyBanTeeth\XF\Pub\Controller;

use XF\Mvc\Entity\AbstractCollection;
use XF\Mvc\ParameterBag;
use XF\Mvc\Reply\View as ViewReply;

/**
 * Extends \XF\Pub\Controller\Thread
 */
class Thread extends XFCP_Thread
{
    public function actionIndex(ParameterBag $params)
    {
        $reply = parent::actionIndex($params);

        if ($reply instanceof ViewReply && (\XF::options()->svReplyBanBanner ?? true))
        {
            // cache thread reply ban status into posts to apply a styling banner
            /** @var \XF\Entity\Thread $thread */
            $thread = $reply->getParam('thread');
            if ($thread && (!$thread->hasOption('threadmark_category_id') || !$thread->getOption('threadmark_category_id')))
            {
                /** @var AbstractCollection|array $posts */
                $posts = $reply->getParam('posts') ?? [];
                $postsByUserIds = [];

                /** @var \SV\ThreadReplyBanTeeth\XF\Entity\Post $post */
                foreach ($posts as $post)
                {
                    $userId = $post->user_id;
                    $postsByUserIds[$userId] = $userId;
                }
                if ($postsByUserIds)
                {
                    $replyBannedUserIds = [];
                    $isReplyBannedRaw = \XF::finder('XF:ThreadReplyBan')
                                           ->where('thread_id', $thread->thread_id)
                                           ->where('user_id', \array_keys($postsByUserIds))
                                           ->where('expiry_date', '<', \XF::$time)
                                           ->fetchRaw(['fetchOnly' => ['user_id']]);
                    foreach ($isReplyBannedRaw as $row)
                    {
                        $replyBannedUserIds[$row['user_id']] = true;
                    }
                    // update the post cache to avoid additional queries
                    foreach ($posts as $post)
                    {
                        $post->setIsReplyBanned($replyBannedUserIds[$post->user_id] ?? false);
                    }
                }
            }
        }

        return $reply;
    }
}