<?php

namespace SV\ThreadReplyBanTeeth\XF\Entity;

use XF\Phrase;

class Post extends XFCP_Post
{
    /**
     * @param null $error
     *
     * @return bool
     *
     * @noinspection PhpMissingReturnTypeInspection
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
     * @param null $error
     *
     * @return bool
     *
     * @noinspection PhpMissingReturnTypeInspection
     */
    public function canReact(&$error = null)
    {
        $hasPermission = parent::canReact($error);

        if (!$hasPermission)
        {
            return false;
        }

        if ($this->app()->options()->svLikeReplyBan)
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
     * @param string $type
     * @param Phrase|null   $error
     *
     * @return bool
     *
     * @noinspection PhpMissingReturnTypeInspection
     */
    public function canDelete($type = 'soft', &$error = null)
    {
        $hasPermission = parent::canDelete($type, $error);

        if (!$hasPermission)
        {
            return false;
        }

        if ($this->app()->options()->svDeleteReplyBan)
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
     * @param Phrase|null $error
     *
     * @return bool
     *
     * @noinspection PhpMissingReturnTypeInspection
     */
    public function canWarn(&$error = null)
    {
        $hasPermission = parent::canWarn($error);

        if (!$hasPermission)
        {
            return false;
        }

        if ($this->app()->options()->svWarnReplyBan)
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
}
