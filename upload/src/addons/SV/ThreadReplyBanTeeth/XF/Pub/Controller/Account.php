<?php
/**
 * @noinspection PhpUnusedParameterInspection
 */

declare(strict_types=1);

namespace SV\ThreadReplyBanTeeth\XF\Pub\Controller;

use XF\Entity\ThreadReplyBan as ThreadReplyBanEntity;
use XF\Mvc\Entity\ArrayCollection;
use XF\Mvc\ParameterBag;
use XF\Mvc\Reply\AbstractReply;
use XF\Repository\ThreadReplyBan as ThreadReplyBanRepo;
use XF\Repository\Node as NodeRepo;
use function assert;

/**
 * Extends \XF\Pub\Controller\Account
 */
class Account extends XFCP_Account
{
    public function actionThreadBans(ParameterBag $params): AbstractReply
    {
        if (!(\XF::options()->svReplyBanList ?? true))
        {
            return $this->notFound();
        }
        $visitor = \XF::visitor();
        $userId = (int)$visitor->user_id;
        assert($userId !== 0);

        $page = (int)$this->filterPage();
        $perPage = (int)$this->options()->messagesPerPage;
        $this->assertCanonicalUrl($this->buildLink('account/thread-bans', null, ['page' => $page]));

        $filters = [];

        $replyBanRepo = $this->repository('XF:ThreadReplyBan');
        assert($replyBanRepo instanceof ThreadReplyBanRepo);

        $finder = $replyBanRepo->findReplyBansForList()
                               ->where('user_id', $userId)
                               ->limitByPage($page, $perPage);

        // only fetch for visible forums
        $nodeRepo = $this->repository('XF:Node');
        assert($nodeRepo instanceof NodeRepo);
        $nodes = $nodeRepo->getNodeList();

        if ($nodes->count() !== 0)
        {
            $finder->where('Thread.node_id', $nodes->keys())
                   ->where('Thread.discussion_state', 'visible')
                   ->with('Thread.full')
            ;
        }
        else
        {
            $finder->whereImpossible();
        }

        $total = $finder->total();

        $this->assertValidPage($page, $perPage, $total, 'account/thread-bans', null);

        $replyBans = $total !== 0 ? $finder->fetch() : new ArrayCollection([]);
        if (\XF::isAddOnActive('SV/ExtendedIgnore'))
        {
            foreach ($replyBans as $replyBan)
            {
                /** @var ThreadReplyBanEntity $replyBan */
                $thread = $replyBan->Thread;
                assert($thread !== null);
                $thread->setOption('svForceIgnore', false);
            }
        }

        //$this->getOption('svForceIgnore')

        $params = [
            'filters' => $filters,
            'page'    => $page,
            'perPage' => $perPage,
            'total'   => $total,
            'replyBans' => $replyBans,
        ];

        return $this->view('', 'svThreadReplyBanTeeth_banned_threads', $params);
    }

}