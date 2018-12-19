<?php

namespace SV\ThreadReplyBanTeeth\XF\Finder;

class Thread extends XFCP_Thread
{
    protected $appliedReplyBan = false;

    /**
     * Mostly XF2.0 support
     *
     * @param null $userId
     */
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

        if ($options->svEditReplyBan ||
            $options->svLikeReplyBan ||
            $options->svDeleteReplyBan)
        {
            $this->with('ReplyBans|' . $userId);
        }
    }

    /**
     * XF2.0 support
     *
     * @param bool $includeForum
     * @return XFCP_Thread
     */
    public function forFullView($includeForum = false)
    {
        if (\XF::$versionId < 2010000)
        {
            $this->includeReplyBan();
        }

        /** @noinspection PhpDeprecationInspection */
        return parent::forFullView($includeForum);
    }


    public function withReadData($userId = null)
    {
        $this->includeReplyBan($userId);

        return parent::withReadData($userId);
    }
}
