<?php
/**
 * @noinspection PhpMissingReturnTypeInspection
 */

namespace SV\ThreadReplyBanTeeth\XF\Poll;

use SV\ThreadReplyBanTeeth\XF\Entity\Thread as ExtendedThreadEntity;
use XF\Entity\Poll as PollEntity;
use XF\Mvc\Entity\Entity;

/**
 * @extends \XF\Poll\Thread
 */
class Thread extends XFCP_Thread
{
    public function canVote(Entity $content, PollEntity $poll, &$error = null)
    {
        /** @var ExtendedThreadEntity $content */
        return parent::canVote($content, $poll, $error) && $content->svExtraReplyBanCheck(ExtendedThreadEntity::$svReplyBanOptionThread);
    }

    public function canEdit(Entity $content, PollEntity $poll, &$error = null)
    {
        /** @var ExtendedThreadEntity $content */
        return parent::canEdit($content, $poll, $error) && $content->svExtraReplyBanCheck(ExtendedThreadEntity::$svReplyBanOptionThread);
    }

    public function canDelete(Entity $content, PollEntity $poll, &$error = null)
    {
        /** @var ExtendedThreadEntity $content */
        return parent::canDelete($content, $poll, $error) && $content->svExtraReplyBanCheck(ExtendedThreadEntity::$svReplyBanOptionThread);
    }

    public function canAlwaysEditDetails(Entity $content, PollEntity $poll, &$error = null)
    {
        /** @var ExtendedThreadEntity $content */
        return parent::canAlwaysEditDetails($content, $poll, $error) && $content->svExtraReplyBanCheck(ExtendedThreadEntity::$svReplyBanOptionThread);
    }
}