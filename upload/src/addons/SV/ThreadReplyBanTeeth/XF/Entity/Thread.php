<?php

namespace SV\ThreadReplyBanTeeth\XF\Entity;

use XF\Mvc\Entity\Structure;
use XF\Phrase;

class Thread extends XFCP_Thread
{
    /**
     * @param Phrase|string|null $error
     * @return bool
     * @noinspection PhpMissingReturnTypeInspection
     */
    public function canAddThreadmark(&$error = null)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $hasPermission = parent::canAddThreadmark($error);

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
     * @noinspection PhpMissingReturnTypeInspection
     */
    public function canEditThreadmark(&$error = null)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $hasPermission = parent::canEditThreadmark($error);

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
     * @param string             $type
     * @param Phrase|string|null $error
     * @return bool
     * @noinspection PhpMissingParamTypeInspection
     * @noinspection PhpMissingReturnTypeInspection
     */
    public function canDeleteThreadmark($type, &$error = null)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $hasPermission = parent::canDeleteThreadmark($type, $error);

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
     * @noinspection PhpMissingReturnTypeInspection
     */
    public function canAddThreadmarkIndex(&$error = null)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $hasPermission = parent::canAddThreadmarkIndex($error);

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
     * @noinspection PhpMissingReturnTypeInspection
     */
    public function canLinkContentToIndex(&$error = null)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $hasPermission = parent::canLinkContentToIndex($error);

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
     * Title Edit History support
     *
     * @param Phrase|string|null $error
     * @return bool
     * @noinspection PhpMissingReturnTypeInspection
     */
    public function canEditThreadTitle(&$error = null)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $hasPermission = parent::canEditThreadTitle($error);

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
    public function canReplyBan(&$error = null)
    {
        $hasPermission = parent::canReplyBan($error);

        if (!$hasPermission)
        {
            return false;
        }

        if ($this->app()->options()->svReplyBanReplyBan ?? true)
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
        $visitor = \XF::visitor();
        if (!$visitor->user_id)
        {
            return false;
        }

        $replyBans = $this->ReplyBans;

        if (isset($replyBans[$visitor->user_id]))
        {
            $replyBan = $replyBans[$visitor->user_id];

            return ($replyBan && (!$replyBan->expiry_date || $replyBan->expiry_date > \XF::$time));
        }

        return false;
    }


    /**
     * @param Structure $structure
     * @return Structure
     * @noinspection PhpMissingReturnTypeInspection
     */
    public static function getStructure(Structure $structure)
    {
        $structure = parent::getStructure($structure);

        $structure->withAliases['full'][] = function () {
            $userId = \XF::visitor()->user_id;
            if ($userId)
            {
                $options = \XF::app()->options();

                if (($options->svEditReplyBan ?? true) ||
                    ($options->svLikeReplyBan ?? true) ||
                    ($options->svDeleteReplyBan ?? true))
                {
                    return ['ReplyBans|' . $userId];
                }
            }

            return null;
        };

        return $structure;
    }
}
