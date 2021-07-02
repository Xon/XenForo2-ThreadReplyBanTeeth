<?php

namespace SV\ThreadReplyBanTeeth\XF\Entity;

use XF\Mvc\Entity\Structure;
use XF\Phrase;

class Post extends XFCP_Post
{
    /**
     * @param Phrase|string|null $error
     * @return bool
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
     * @noinspection PhpMissingReturnTypeInspection
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
            if ($this->isReplyBanned())
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
     * @noinspection PhpMissingReturnTypeInspection
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
     * @noinspection PhpMissingReturnTypeInspection
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
            if ($this->isReplyBanned())
            {
                return false;
            }
        }

        return true;
    }

    public function isReplyBanned(): bool
    {
        $isReplyBanned = $this->_getterCache['isReplyBanned'] ?? null;

        if ($isReplyBanned === null)
        {
            /** @var Thread $thread */
            $thread = $this->Thread;
            $this->_getterCache['isReplyBanned'] = $isReplyBanned = $thread->isReplyBanned();
        }

        return $isReplyBanned;
    }

    public function setIsReplyBanned(bool $isReplyBanned = null)
    {
        if ($isReplyBanned === null)
        {
            unset($this->_getterCache['isReplyBanned']);
        }
        else
        {
            $this->_getterCache['isReplyBanned'] = $isReplyBanned;
        }
    }

    /** @noinspection PhpMissingReturnTypeInspection */
    public static function getStructure(Structure $structure)
    {
        $structure = parent::getStructure($structure);

        $structure->options['svHasReplyBanned'] = \XF::options()->svReplyBanBanner ?? false;

        return $structure;
    }
}
