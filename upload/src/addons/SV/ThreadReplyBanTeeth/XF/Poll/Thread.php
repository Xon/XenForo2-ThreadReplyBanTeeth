<?php

namespace SV\ThreadReplyBanTeeth\XF\Poll;



use XF\Entity\Poll;
use XF\Mvc\Entity\Entity;
use SV\ThreadReplyBanTeeth\XF\Entity\Thread as ThreadEntity;

/**
 * Extends \XF\Poll\Thread
 */
class Thread extends XFCP_Thread
{
    public function canEdit(Entity $content, Poll $poll, &$error = null)
    {
        /** @var ThreadEntity $content */
        return parent::canEdit($content, $poll, $error) && $content->svExtraReplyBanCheck(ThreadEntity::$svReplyBanOptionThread);
    }

    public function canDelete(Entity $content, Poll $poll, &$error = null)
    {
        /** @var ThreadEntity $content */
        return parent::canDelete($content, $poll, $error) && $content->svExtraReplyBanCheck(ThreadEntity::$svReplyBanOptionThread);
    }

    public function canAlwaysEditDetails(Entity $content, Poll $poll, &$error = null)
    {
        /** @var ThreadEntity $content */
        return parent::canAlwaysEditDetails($content, $poll, $error) && $content->svExtraReplyBanCheck(ThreadEntity::$svReplyBanOptionThread);
    }
}