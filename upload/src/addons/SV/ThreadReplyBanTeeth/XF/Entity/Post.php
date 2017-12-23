<?php

namespace SV\ThreadReplyBanTeeth\XF\Entity;

class Post extends XFCP_Post {
    /**
     * @param null $error
     * @return bool
     */
    public function canEdit(&$error = null) {
        $hasPermission = parent::canEdit($error);

        if(!$hasPermission) {
            return false;
        }

        $thread = $this->Thread;
        $visitor = \XF::visitor();

        if (\XF::app()->options()->SV_ThreadReplyBanTeeth_EditBan)
        {
            {
                if($this->isReplyBanned()) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * @param null $error
     * @return bool
     */
    public function canLike(&$error = null) {
        $hasPermission = parent::canLike($error);

        if(!$hasPermission) {
            return false;
        }

        $thread = $this->Thread;
        $visitor = \XF::visitor();

        if (\XF::app()->options()->SV_ThreadReplyBanTeeth_LikeBan)
        {
            if($this->isReplyBanned()) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param null $error
     * @return bool
     */
    public function canDelete($type = 'soft', &$error = null) {
        $hasPermission = parent::canDelete($type, $error);

        if(!$hasPermission) {
            return false;
        }

        $thread = $this->Thread;
        $visitor = \XF::visitor();

        if (\XF::app()->options()->SV_ThreadReplyBanTeeth_DeleteBan)
        {
            if($this->isReplyBanned()) {
                return false;
            }
        }

        return true;
    }

    protected function isReplyBanned() {
        /** @var \XF\Mvc\Entity\AbstractCollection $replyBans */
        $replyBans = $this->Thread->ReplyBans;
        $visitor = \XF::visitor();

        return $replyBans->offsetExists($visitor->user_id);
    }
}