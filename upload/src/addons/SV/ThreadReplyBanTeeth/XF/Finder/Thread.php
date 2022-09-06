<?php

namespace SV\ThreadReplyBanTeeth\XF\Finder;

class Thread extends XFCP_Thread
{
    protected $appliedReplyBan = false;

    protected function includeReplyBan(int $userId): void
    {
        if ($this->appliedReplyBan)
        {
            return;
        }
        $this->appliedReplyBan = true;

        if ($userId === 0)
        {
            return;
        }

        $options = $this->app()->options();
        if (($options->svEditReplyBan ?? true) ||
            ($options->svLikeReplyBan ?? true) ||
            ($options->svDeleteReplyBan ?? true))
        {
            $this->with('ReplyBans|' . $userId);
        }
    }

    /**
     * @param int|null $userId
     * @return Thread
     * @noinspection PhpMissingReturnTypeInspection
     */
    public function withReadData($userId = null)
    {
        $this->includeReplyBan((int)($userId ?? \XF::visitor()->user_id));

        return parent::withReadData($userId);
    }
}
