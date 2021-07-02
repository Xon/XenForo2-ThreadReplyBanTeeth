<?php
/**
 * @noinspection PhpMissingReturnTypeInspection
 */

namespace SV\ThreadReplyBanTeeth\SV\Threadmarks\Entity;

use SV\Threadmarks\Entity\ThreadmarkIndexInterface;
use XF\Phrase;

/**
 * Extends \SV\Threadmarks\Entity\ThreadmarkIndex
 */
class ThreadmarkIndex extends XFCP_ThreadmarkIndex
{
    /**
     * @param string $type
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

        if ($this->app()->options()->svThreadmarkReplyBan ?? true)
        {
            if ($this->isReplyBanned())
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
    public function canEdit(&$error = null)
    {
        $hasPermission = parent::canEdit($error);

        if (!$hasPermission)
        {
            return false;
        }

        if ($this->app()->options()->svThreadmarkReplyBan ?? true)
        {
            if ($this->isReplyBanned())
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
    public function canSortContent(&$error = null)
    {
        $hasPermission = parent::canSortContent($error);

        if (!$hasPermission)
        {
            return false;
        }

        if ($this->app()->options()->svThreadmarkReplyBan ?? true)
        {
            if ($this->isReplyBanned())
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
    public function canAddContent(&$error = null)
    {
        $hasPermission = parent::canAddContent($error);

        if (!$hasPermission)
        {
            return false;
        }

        if ($this->app()->options()->svThreadmarkReplyBan ?? true)
        {
            if ($this->isReplyBanned())
            {
                return false;
            }
        }

        return true;
    }

    /**
     * @param ThreadmarkIndexInterface $content
     * @param Phrase|string|null       $error
     * @return bool
     */
    public function canDeleteContent(ThreadmarkIndexInterface $content, &$error = null)
    {
        $hasPermission = parent::canDeleteContent($content, $error);

        if (!$hasPermission)
        {
            return false;
        }

        if ($this->app()->options()->svThreadmarkReplyBan ?? true)
        {
            if ($this->isReplyBanned())
            {
                return false;
            }
        }

        return true;
    }

    public function isReplyBanned() : bool
    {
        $visitor = \XF::visitor();
        if (!$visitor->user_id)
        {
            return false;
        }

        $container = $this->getIndexContent();
        $replyBans = $container instanceof \XF\Entity\Thread
            ? $container->ReplyBans
            : null;

        if ($replyBans && isset($replyBans[$visitor->user_id]))
        {
            $replyBan = $replyBans[$visitor->user_id];

            return ($replyBan && (!$replyBan->expiry_date || $replyBan->expiry_date > \XF::$time));
        }

        return false;
    }
}