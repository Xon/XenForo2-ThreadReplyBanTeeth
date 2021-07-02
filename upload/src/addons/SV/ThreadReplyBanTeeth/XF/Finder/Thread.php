<?php

namespace SV\ThreadReplyBanTeeth\XF\Finder;

class Thread extends XFCP_Thread
{
    protected $appliedReplyBan = false;

    /**
     * Mostly XF2.0 support
     */
    protected function includeReplyBan(int $userId = null)
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
        $this->includeReplyBan($userId);

        return parent::withReadData($userId);
    }
}
