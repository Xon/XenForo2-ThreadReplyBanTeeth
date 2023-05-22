<?php
/**
 * @noinspection PhpMissingReturnTypeInspection
 */

declare(strict_types=1);

namespace SV\ThreadReplyBanTeeth\SV\Threadmarks\Entity;

use SV\Threadmarks\Entity\ThreadmarkIndexInterface;
use XF\Entity\Thread;
use XF\Phrase;

/**
 * Extends \SV\Threadmarks\Entity\ThreadmarkIndex
 */
class ThreadmarkIndex extends XFCP_ThreadmarkIndex
{
    /**
     * @param string $type
     * @param Phrase|string|null $error
     * @return bool
     */
    public function canDelete($type = 'soft', &$error = null)
    {
        return parent::canDelete($type, $error) && $this->svExtraReplyBanCheck();
    }

    public function canEdit(&$error = null)
    {
        return parent::canEdit($error) && $this->svExtraReplyBanCheck();
    }

    public function canSortContent(&$error = null)
    {
        return parent::canSortContent($error) && $this->svExtraReplyBanCheck();
    }

    public function canAddContent(&$error = null)
    {
        return parent::canAddContent($error) && $this->svExtraReplyBanCheck();
    }

    public function canDeleteContent(ThreadmarkIndexInterface $content, &$error = null)
    {
        return parent::canDeleteContent($content, $error) && $this->svExtraReplyBanCheck();
    }

    //*********

    protected function svExtraReplyBanCheck(): bool
    {
        if ($this->app()->options()->svThreadmarkReplyBan ?? true)
        {
            if ($this->isReplyBanned())
            {
                return false;
            }
        }

        return true;
    }

    public function isReplyBanned() : bool
    {
        $visitor = \XF::visitor();
        $userId = (int)$visitor->user_id;
        if ($userId === 0)
        {
            return false;
        }

        $container = $this->getIndexContent();
        if ($container instanceof Thread)
        {
            /** @var \SV\ThreadReplyBanTeeth\XF\Entity\Thread $container */
            return $container->isUserReplyBanned($userId);
        }

        return false;
    }
}