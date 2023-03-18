<?php
/**
 * @noinspection PhpMissingReturnTypeInspection
 * @noinspection PhpMultipleClassDeclarationsInspection
 */

namespace SV\ThreadReplyBanTeeth\XF\Entity;

use XF\Entity\User;
use XF\Mvc\Entity\Structure;
use XF\Phrase;

class Thread extends XFCP_Thread
{
    public static $svReplyBanOptionThreadmark = 'svThreadmarkReplyBan';
    public static $svReplyBanOptionReplyBan = 'svReplyBanReplyBan';
    public static $svReplyBanOptionThread   = 'svThreadReplyBan';

    //********* SV/CollaborativeThreads support

    public function canEditCollaboration(&$error = null)
    {
        return parent::canEditCollaboration($error) && $this->svExtraReplyBanCheck(static::$svReplyBanOptionThread);
    }

    //********* SV/Threadmarks support

    public function canAddThreadmark(&$error = null)
    {
        return parent::canAddThreadmark( $error) && $this->svExtraReplyBanCheck(static::$svReplyBanOptionThreadmark);
    }

    public function canEditThreadmark(&$error = null)
    {
        return parent::canEditThreadmark( $error) && $this->svExtraReplyBanCheck(static::$svReplyBanOptionThreadmark);
    }

    public function canDeleteThreadmark($type, &$error = null)
    {
        return parent::canDeleteThreadmark($type, $error) && $this->svExtraReplyBanCheck(static::$svReplyBanOptionThreadmark);
    }

    public function canAddThreadmarkIndex(&$error = null)
    {
        return parent::canAddThreadmarkIndex($error) && $this->svExtraReplyBanCheck(static::$svReplyBanOptionThreadmark);
    }

    public function canLinkContentToIndex(&$error = null)
    {
        return parent::canLinkContentToIndex($error) && $this->svExtraReplyBanCheck(static::$svReplyBanOptionThreadmark);
    }

    //********* SV/PostFriction support

    public function canEditPostFriction(&$error = null): bool
    {
        return parent::canEditPostFriction($error) && $this->svExtraReplyBanCheck(static::$svReplyBanOptionThread);
    }

    //********* XF support

    public function canEdit(&$error = null)
    {
        return parent::canEdit($error) && $this->svExtraReplyBanCheck(static::$svReplyBanOptionThread);
    }

    public function canDelete($type = 'soft', &$error = null)
    {
        return parent::canDelete($type, $error) && $this->svExtraReplyBanCheck(static::$svReplyBanOptionThread);
    }

    public function canReplyBan(&$error = null)
    {
        return parent::canReplyBan($error) && $this->svExtraReplyBanCheck(static::$svReplyBanOptionReplyBan);
    }

    public function canLockUnlock(&$error = null)
    {
        return parent::canLockUnlock($error) && $this->svExtraReplyBanCheck(static::$svReplyBanOptionThread);
    }

    public function canStickUnstick(&$error = null)
    {
        return parent::canStickUnstick($error) && $this->svExtraReplyBanCheck(static::$svReplyBanOptionThread);
    }

    public function canCreatePoll(&$error = null)
    {
        return parent::canCreatePoll($error) && $this->svExtraReplyBanCheck(static::$svReplyBanOptionThread);
    }

    public function canChangeType(&$error = null): bool
    {
        return parent::canChangeType($error) && $this->svExtraReplyBanCheck(static::$svReplyBanOptionThread);
    }

    public function canMove(&$error = null): bool
    {
        return parent::canMove($error) && $this->svExtraReplyBanCheck(static::$svReplyBanOptionThread);
    }

    public function canMerge(&$error = null): bool
    {
        return parent::canMerge($error) && $this->svExtraReplyBanCheck(static::$svReplyBanOptionThread);
    }

    //*********

    protected function svExtraReplyBanCheck(string $option): bool
    {
        if ($this->app()->options()->{$option} ?? true)
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
        return $this->isUserReplyBanned(\XF::visitor()->user_id);
    }

    public function isUserReplyBanned(int $userId): bool
    {
        if (!$userId)
        {
            return false;
        }

        /** @var User|bool $user */
        $user = $this->em()->findCached('XF:User', $userId);
        if (($user instanceof User) && $user->is_banned)
        {
            return true;
        }

        /** @var bool[]|null $replyBannedUsers */
        $replyBannedUsers = $this->_getterCache['svReplyBannedUserIds'] ?? null;
        $userIsReplyBanned = $replyBannedUsers[$userId] ?? null;

        if ($userIsReplyBanned === null)
        {
            $replyBans = $this->ReplyBans;

            if (isset($replyBans[$userId]))
            {
                $replyBan = $replyBans[$userId];

                $userIsReplyBanned = ($replyBan && (!$replyBan->expiry_date || $replyBan->expiry_date >= \XF::$time));
            }
            $userIsReplyBanned = $userIsReplyBanned ?? false;

            if ($replyBannedUsers !== null)
            {
                $replyBannedUsers[$userId] = $userIsReplyBanned;
                $this->_getterCache['svReplyBannedUserIds'] = $replyBannedUsers;
            }
        }

        return $userIsReplyBanned;
    }

    /**
     * @param int[]|null $userIds
     */
    public function setUsersAreReplyBanned(?array $userIds)
    {
        if ($userIds === null)
        {
            unset($this->_getterCache['svReplyBannedUserIds']);
        }
        else
        {
            $this->_getterCache['svReplyBannedUserIds'] = $userIds;
        }
    }

    /**
     * @param Structure $structure
     * @return Structure
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

        $structure->options['svHasReplyBanned'] = \XF::options()->svReplyBanBanner ?? false;

        return $structure;
    }
}
