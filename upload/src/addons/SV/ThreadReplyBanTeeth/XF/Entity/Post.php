<?php
/**
 * @noinspection PhpMissingReturnTypeInspection
 */

namespace SV\ThreadReplyBanTeeth\XF\Entity;

use XF\Phrase;

class Post extends XFCP_Post
{
    //********* XF support

    /**
     * @param Phrase|string|null $error
     * @return bool
     */
    public function canEdit(&$error = null)
    {
        $hasPermission = parent::canEdit($error);

        if (!$hasPermission)
        {
            return false;
        }

        if ($this->app()->options()->svEditReplyBan ?? true)
        {
            /** @var Thread $thread */
            $thread = $this->Thread;
            if ($thread->isReplyBanned())
            {
                return false;
            }
        }

        return true;
    }

    /**
     * @param Phrase|string|null $error
     * @return bool
     */
    public function canReact(&$error = null)
    {
        $hasPermission = parent::canReact($error);

        if (!$hasPermission)
        {
            return false;
        }

        if ($this->app()->options()->svLikeReplyBan ?? true)
        {
            /** @var Thread $thread */
            $thread = $this->Thread;
            if ($thread->isReplyBanned())
            {
                return false;
            }
        }

        return true;
    }

    /**
     * @param string             $type
     * @param Phrase|string|null $error
     * @return bool
     */
    public function canDelete($type = 'soft', &$error = null)
    {
        $hasPermission = parent::canDelete($type, $error);

        if (!$hasPermission)
        {
            return false;
        }

        if ($this->app()->options()->svDeleteReplyBan ?? true)
        {
            /** @var Thread $thread */
            $thread = $this->Thread;
            if ($thread->isReplyBanned())
            {
                return false;
            }
        }

        return true;
    }

    /**
     * @param Phrase|string|null $error
     * @return bool
     */
    public function canWarn(&$error = null)
    {
        $hasPermission = parent::canWarn($error);

        if (!$hasPermission)
        {
            return false;
        }

        if ($this->app()->options()->svWarnReplyBan ?? true)
        {
            /** @var Thread $thread */
            $thread = $this->Thread;
            if ($thread->isReplyBanned())
            {
                return false;
            }
        }

        return true;
    }

    //*********
}
