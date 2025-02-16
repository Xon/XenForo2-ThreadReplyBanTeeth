<?php

declare(strict_types=1);

namespace SV\ThreadReplyBanTeeth\XF\Pub\Controller;

use SV\ForumBan\Finder\ForumBan as ForumBanFinder;
use SV\ForumBan\XF\Entity\User as ForumBanUserEntity;
use SV\StandardLib\Helper;
use XF\Finder\ThreadReplyBan as ThreadReplyBanFinder;
use XF\Mvc\Entity\AbstractCollection;
use XF\Mvc\ParameterBag;
use XF\Mvc\Reply\View as ViewReply;
use function array_key_exists;
use function array_keys;
use function count;

/**
 * @extends \XF\Pub\Controller\Thread
 */
class Thread extends XFCP_Thread
{
    public function actionIndex(ParameterBag $params)
    {
        $reply = parent::actionIndex($params);

        if ($reply instanceof ViewReply && (\XF::options()->svReplyBanBanner ?? false))
        {
            // cache thread reply ban status into posts to apply a styling banner
            /** @var \SV\ThreadReplyBanTeeth\XF\Entity\Thread $thread */
            $thread = $reply->getParam('thread');
            if (!$thread)
            {
                return $reply;
            }
            $visitor = \XF::visitor();
            $visitorUserId = (int)$visitor->user_id;
            if ($visitorUserId !== 0 && (!$thread->hasOption('threadmark_category_id') || !$thread->getOption('threadmark_category_id')))
            {
                /** @var AbstractCollection|array $posts */
                $posts = $reply->getParam('posts') ?? [];
                $postsByUserIds = [];

                /** @var \SV\ThreadReplyBanTeeth\XF\Entity\Post $post */
                foreach ($posts as $post)
                {
                    $postUserId = $post->user_id;
                    $postsByUserIds[$postUserId] = $postUserId;
                }
                unset($postsByUserIds[0]);
                unset($postsByUserIds[$visitorUserId]);
                if (count($postsByUserIds) !== 0)
                {
                    $replyBannedUserIds = [];
                    $isReplyBannedRaw = Helper::finder(ThreadReplyBanFinder::class)
                                           ->where('thread_id', $thread->thread_id)
                                           ->where('user_id', array_keys($postsByUserIds))
                                           ->whereOr(['expiry_date', '=', 0],['expiry_date', '>=', \XF::$time])
                                           ->fetchRaw(['fetchOnly' => ['user_id']]);
                    foreach ($isReplyBannedRaw as $row)
                    {
                        $replyBannedUserIds[$row['user_id']] = true;
                    }
                    // negative cache
                    foreach ($posts as $post)
                    {
                        $postUserId = $post->user_id;
                        if (!array_key_exists($postUserId, $replyBannedUserIds))
                        {
                            $replyBannedUserIds[$postUserId] = false;
                        }
                    }

                    if ($thread->isReplyBanned())
                    {
                        $replyBannedUserIds[$visitorUserId] = true;
                    }
                    unset($replyBannedUserIds[0]);
                    // update the post cache to avoid additional queries
                    $thread->setUsersAreReplyBanned($replyBannedUserIds);

                    if (\XF::isAddOnActive('SV/ForumBan'))
                    {
                        $forumBannedUserIds = [];
                        $isForumBannedRaw = Helper::finder(ForumBanFinder::class)
                            ->where('node_id', $thread->node_id)
                            ->where('user_id', array_keys($postsByUserIds))
                            ->whereOr(['expiry_date', '=', 0], ['expiry_date', '>=', \XF::$time])
                            ->fetchRaw(['fetchOnly' => ['user_id']]);
                        foreach ($isForumBannedRaw as $row)
                        {
                            $forumBannedUserIds[$row['user_id']] = true;
                        }

                        // negative cache
                        foreach ($posts as $post)
                        {
                            $forumBannedUserId = $post->user_id;
                            if (!array_key_exists($forumBannedUserId, $replyBannedUserIds))
                            {
                                $forumBannedUserIds[$forumBannedUserId] = false;
                            }
                        }

                        /** @var ForumBanUserEntity $visitor */
                        if ($visitor->isForumBanned($thread->node_id))
                        {
                            $forumBannedUserIds[$visitorUserId] = true;
                        }
                        unset($forumBannedUserIds[0]);
                        // update the post cache to avoid additional queries
                        $thread->setUsersAreForumBanned($forumBannedUserIds);
                    }
                }
            }
            else
            {
                $thread->setOption('svHasReplyBanned', false);
            }
        }

        return $reply;
    }
}