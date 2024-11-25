<?php

declare(strict_types=1);

namespace SV\ThreadReplyBanTeeth\XF\Finder;

/**
 * @extends \XF\Finder\Thread
 */
class Thread extends XFCP_Thread
{
    /** @var bool */
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

        $options = \XF::options();
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
