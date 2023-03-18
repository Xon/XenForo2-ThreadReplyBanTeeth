<?php
/**
 * @noinspection PhpMissingReturnTypeInspection
 */

namespace SV\ThreadReplyBanTeeth\XF\Entity;

use XF\Phrase;

/**
 * @property-read Thread $Thread
 */
class Post extends XFCP_Post
{
    //********* XF support

    public function canEdit(&$error = null)
    {
        return parent::canEdit($error) && $this->svExtraReplyBanCheck('svEditReplyBan');
    }

    public function canReact(&$error = null)
    {
        return parent::canReact($error) && $this->svExtraReplyBanCheck('svLikeReplyBan');
    }

    public function canDelete($type = 'soft', &$error = null)
    {
        return parent::canDelete($type, $error) && $this->svExtraReplyBanCheck('svDeleteReplyBan');
    }

    public function canWarn(&$error = null)
    {
        return parent::canWarn($error) && $this->svExtraReplyBanCheck('svWarnReplyBan');
    }

    //*********

    protected function svExtraReplyBanCheck(string $option): bool
    {
        if ($this->app()->options()->{$option} ?? true)
        {
            $thread = $this->Thread;
            if ($thread !== null && $thread->isReplyBanned())
            {
                return false;
            }
        }

        return true;
    }
}
