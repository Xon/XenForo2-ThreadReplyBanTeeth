<?php

namespace SV\ThreadReplyBanTeeth\XF\Entity;

use SV\StandardLib\Helper;
use XF\Mvc\Entity\Structure;
use XF\Repository\ThreadReplyBan as ThreadReplyBanRepository;

/**
 * @extends \XF\Entity\User
 */
class User extends XFCP_User
{
    public function canViewThreadBans(): bool
    {
        // todo; implement permission
        return \XF::visitor()->is_moderator;
    }

    protected function getThreadBanCount(): int
    {
        $replyBanRepo = Helper::repository(ThreadReplyBanRepository::class);

        return $replyBanRepo->findReplyBansForList()
                            ->where('user_id', $this->user_id)
                            ->total();
    }

    /**
     * @param Structure $structure
     * @return Structure
     */
    public static function getStructure(Structure $structure)
    {
        $structure = parent::getStructure($structure);

        $structure->getters['thread_bans_count'] = ['getter' => 'getThreadBanCount', 'cache' => true];
    
        return $structure;
    }
}