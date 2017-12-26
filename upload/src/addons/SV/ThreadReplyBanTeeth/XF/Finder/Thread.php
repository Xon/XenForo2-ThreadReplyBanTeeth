<?php

namespace SV\ThreadReplyBanTeeth\XF\Finder;

class Thread extends XFCP_Thread
{
    protected $appliedReplyBan = false;

    protected function includeReplyBan($userId = null)
    {
        if ($this->appliedReplyBan)
        {
            return;
        }
        $this->appliedReplyBan = true;

        if ($userId === null)
        {
            $userId = \XF::visitor()->user_id;
        }
        if (!$userId)
        {
            return;
        }
        $options = \XF::app()->options();

        if ($options->SV_ThreadReplyBanTeeth_EditBan ||
            $options->SV_ThreadReplyBanTeeth_LikeBan ||
            $options->SV_ThreadReplyBanTeeth_DeleteBan)
        {
            $this->with('ReplyBans|' . $userId);
        }
    }

    public function forFullView($includeForum = false)
    {
        $this->includeReplyBan();

        return parent::forFullView($includeForum);
    }


    public function withReadData($userId = null)
    {
        $this->includeReplyBan($userId);

        return parent::withReadData($userId);
    }

    protected function applyFilters(\XF\Finder\Thread $threadFinder, array $filters)
    {
        $this->includeReplyBan();

        return parent::applyFilters($threadFinder, $filters);
    }
}
