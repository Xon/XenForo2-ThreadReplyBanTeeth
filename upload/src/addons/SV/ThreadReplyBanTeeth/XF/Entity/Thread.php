<?php
/**
 * @noinspection PhpMissingReturnTypeInspection
 * @noinspection PhpMultipleClassDeclarationsInspection
 */

declare(strict_types=1);

namespace SV\ThreadReplyBanTeeth\XF\Entity;

use SV\ForumBan\XF\Entity\User as ForumBanExtendedUserEntity;
use XF\Entity\ThreadReplyBan as ThreadReplyBanEntity;
use XF\Entity\User as UserEntity;
use XF\Mvc\Entity\Structure;

/**
 * @extends \XF\Entity\Thread
 */
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

    public function canEditTags(&$error = null)
    {
        return parent::canEditTags($error) && $this->svExtraReplyBanCheck(static::$svReplyBanOptionThread);
    }

    //*********

    public function svExtraReplyBanCheck(string $option): bool
    {
        if (\XF::options()->{$option} ?? true)
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
        $visitor = \XF::visitor();
        $userId = (int)$visitor->user_id;
        if ($userId === 0)
        {
            return false;
        }

        return $this->isUserReplyBanned($userId);
    }

    public function isUserReplyBanned(int $userId): bool
    {
        if ($userId === 0)
        {
            return false;
        }

        /** @var array<int,bool>|null $replyBannedUsers */
        $replyBannedUsers = $this->_getterCache['svReplyBannedUserIds'] ?? null;
        /** @var bool|null $userIsReplyBanned */
        $userIsReplyBanned = $replyBannedUsers[$userId] ?? null;

        if ($userIsReplyBanned === null)
        {
            $replyBans = $this->ReplyBans;
            /** @var ThreadReplyBanEntity|null $replyBan */
            $replyBan = $replyBans[$userId] ?? null;

            if ($replyBan !== null)
            {
                $expiryDate = (int)$replyBan->expiry_date;
                $userIsReplyBanned = $expiryDate === 0 || $expiryDate >= \XF::$time;
            }
            $userIsReplyBanned = $userIsReplyBanned ?? false;

            if ($replyBannedUsers !== null)
            {
                $replyBannedUsers[$userId] = $userIsReplyBanned;
                $this->_getterCache['svReplyBannedUserIds'] = $replyBannedUsers;
            }
        }

        if ($userIsReplyBanned)
        {
            return true;
        }

        if (\XF::isAddOnActive('SV/ForumBan'))
        {
            /** @var array<int,bool>|null $forumBannedUsers */
            $forumBannedUsers = $this->_getterCache['svForumBannedUserIds'] ?? null;
            /** @var bool|null $userIsForumBanned */
            $userIsForumBanned = $forumBannedUsers[$userId] ?? null;

            if ($userIsForumBanned === null)
            {
                /** @var ForumBanExtendedUserEntity|null $user */
                $user = \XF::app()->find(UserEntity::class, $userId);
                if ($user !== null && $user->isForumBanned($this->node_id))
                {
                    $userIsForumBanned = true;
                }
                $userIsForumBanned = $userIsForumBanned ?? false;

                if ($forumBannedUsers !== null)
                {
                    $forumBannedUsers[$userId] = $userIsForumBanned;
                    $this->_getterCache['svForumBannedUserIds'] = $forumBannedUsers;
                }
            }

            if ($userIsForumBanned)
            {
                return true;
            }
        }

        return false;
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
     * @param int[]|null $userIds
     */
    public function setUsersAreForumBanned(?array $userIds)
    {
        if ($userIds === null)
        {
            unset($this->_getterCache['svForumBannedUserIds']);
        }
        else
        {
            $this->_getterCache['svForumBannedUserIds'] = $userIds;
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
            $userId = (int)\XF::visitor()->user_id;
            if ($userId !== 0)
            {
                $options = \XF::options();

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
