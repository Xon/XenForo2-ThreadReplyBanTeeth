<?php
/**
 * @noinspection PhpUnusedParameterInspection
 */

namespace SV\ThreadReplyBanTeeth\XF\Pub\Controller;

use SV\StandardLib\Helper;
use SV\ThreadReplyBanTeeth\XF\Entity\User as ExtendedUserEntity;
use XF\Mvc\ParameterBag;
use XF\Mvc\Reply\AbstractReply;
use XF\Repository\ThreadReplyBan as ThreadReplyBanRepository;

/**
 * @extends \XF\Pub\Controller\Member
 */
class Member extends XFCP_Member
{
    public function actionThreadBans(ParameterBag $params): AbstractReply
    {
        $user = $this->assertViewableUser($params->get('user_id'));

        /** @var ExtendedUserEntity $visitor */
        $visitor = \XF::visitor();
        if (!$visitor->canViewThreadBans())
        {
            return $this->notFound();
        }

        $page = $this->filterPage();
        $perPage = 25;

        $replyBanRepo = Helper::repository(ThreadReplyBanRepository::class);

        $finder = $replyBanRepo->findReplyBansForList()
                               ->where('user_id', $user->user_id)
                               ->limitByPage($page, $perPage);

        $linkParams = [];


        $bans = $finder->fetch();
        $total = $finder->total();

        $this->assertValidPage($page, $perPage, $total, 'members/thread-bans', $user);

        $viewParams = [
            'bans' => $bans,
            'user' => $user,

            'page'    => $page,
            'perPage' => $perPage,
            'total'   => $total,

            'linkParams' => $linkParams,
        ];

        return $this->view('XF\Member:svThreadBans', 'svThreadReplyBanTeeth_member_thread_ban', $viewParams);
    }
}