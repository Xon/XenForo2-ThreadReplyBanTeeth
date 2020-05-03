<?php

namespace SV\ThreadReplyBanTeeth\XF\Entity;

class Post extends XFCP_Post
{
    /**
     * @param null $error
     * @return bool
     */
    public function canEdit(&$error = null)
    {
        $hasPermission = parent::canEdit($error);

        if (!$hasPermission)
        {
            return false;
        }

        if (\XF::app()->options()->svEditReplyBan)
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
     * XF2.0 support
     *
     * @param null $error
     * @return bool
     */
    public function canLike(&$error = null)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $hasPermission = parent::canLike($error);

        if (!$hasPermission)
        {
            return false;
        }

        if (\XF::app()->options()->svLikeReplyBan)
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
     * @return bool
     */
    public function canReact(&$error = null)
    {
        $hasPermission = parent::canReact($error);

        if (!$hasPermission)
        {
            return false;
        }

        if (\XF::app()->options()->svLikeReplyBan)
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
     * @param null   $error
     * @return bool
     */
    public function canDelete($type = 'soft', &$error = null)
    {
        $hasPermission = parent::canDelete($type, $error);

        if (!$hasPermission)
        {
            return false;
        }

        if (\XF::app()->options()->svDeleteReplyBan)
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
