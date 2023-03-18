<?php
/**
 * @noinspection PhpMultipleClassDeclarationsInspection
 * @noinspection PhpIllegalPsrClassPathInspection
 */

namespace SV\ThreadReplyBanTeeth\XF\Entity
{
    /**
     * @mixin \SV\ModToolsImprovements\XF\Entity\Post
     */
    class XFCP_Post extends \XF\Entity\Post {}

    /**
     * @mixin \SV\Threadmarks\XF\Entity\Thread
     * @mixin \SV\PostFriction\XF\Entity\Thread
     * @mixin \SV\CollaborativeThreads\XF\Entity\Thread
     * @mixin \SV\ModToolsImprovements\XF\Entity\Thread
     */
	class XFCP_Thread extends \XF\Entity\Thread {}
}
