<?php

namespace SV\ThreadReplyBanTeeth\XF\Entity;

use XF\Mvc\Entity\Structure;

class Thread extends XFCP_Thread
{
    /**
     * @param null $error
     * @return bool
     */
    public function canAddThreadmark(&$error = null)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $hasPermission = parent::canAddThreadmark($error);

        if (!$hasPermission)
        {
            return false;
        }

        if (\XF::app()->options()->svThreadmarkReplyBan)
        {
            if ($this->isReplyBanned())
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
    public function canEditThreadmark(&$error = null)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $hasPermission = parent::canEditThreadmark($error);

        if (!$hasPermission)
        {
            return false;
        }

        if (\XF::app()->options()->svThreadmarkReplyBan)
        {
            if ($this->isReplyBanned())
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
    public function canDeleteThreadmark($type, &$error = null)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $hasPermission = parent::canDeleteThreadmark($type, $error);

        if (!$hasPermission)
        {
            return false;
        }

        if (\XF::app()->options()->svThreadmarkReplyBan)
        {
            if ($this->isReplyBanned())
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
    public function canAddThreadmarkIndex(&$error = null)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $hasPermission = parent::canAddThreadmarkIndex($error);

        if (!$hasPermission)
        {
            return false;
        }

        if (\XF::app()->options()->svThreadmarkReplyBan)
        {
            if ($this->isReplyBanned())
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
    public function canDeleteFromThreadmarkIndex(&$error = null)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $hasPermission = parent::canDeleteFromThreadmarkIndex($error);

        if (!$hasPermission)
        {
            return false;
        }

        if (\XF::app()->options()->svThreadmarkReplyBan)
        {
            if ($this->isReplyBanned())
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
    public function canEdit(&$error = null)
    {
        $hasPermission = parent::canEdit($error);

        if (!$hasPermission)
        {
            return false;
        }

        if (\XF::app()->options()->svEditReplyBan)
        {
            if ($this->isReplyBanned())
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
     * @param null $error
     * @return bool
     */
    public function canEditThreadTitle(&$error = null)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $hasPermission = parent::canEditThreadTitle($error);

        if (!$hasPermission)
        {
            return false;
        }

        if (\XF::app()->options()->svEditReplyBan)
        {
            if ($this->isReplyBanned())
            {
                return false;
            }
        }

        return true;
    }

    /**
     * @return bool
     */
    public function isReplyBanned()
    {
        $visitor = \XF::visitor();
        if (!$visitor->user_id)
        {
            return false;
        }

        /** @var \XF\Mvc\Entity\AbstractCollection $replyBans */
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
     *
     * @return Structure
     */
    public static function getStructure(Structure $structure)
    {
        $structure = parent::getStructure($structure);

        // XF2.1 support
        if (isset($structure->withAliases))
        {
            $structure->withAliases['full'][] = function () {
                $userId = \XF::visitor()->user_id;
                if ($userId)
                {
                    $options = \XF::app()->options();

                    if ($options->svEditReplyBan ||
                        $options->svLikeReplyBan ||
                        $options->svDeleteReplyBan)
                    {
                        return ['ReplyBans|' . $userId];
                    }
                }

                return null;
            };
        }

        return $structure;
    }
}
