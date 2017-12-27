<?php

namespace SV\ThreadReplyBanTeeth\XF\Entity;

use XF\Mvc\Entity\Structure;

class Thread extends XFCP_Thread
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

        if (\XF::app()->options()->SV_ThreadReplyBanTeeth_EditBan)
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
    public function canDelete($type = 'soft', &$error = null)
    {
        $hasPermission = parent::canDelete($type, $error);

        if (!$hasPermission)
        {
            return false;
        }

        if (\XF::app()->options()->SV_ThreadReplyBanTeeth_DeleteBan)
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

        if (\XF::app()->options()->SV_ThreadReplyBanTeeth_EditBan)
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
}
