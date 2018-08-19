<?php

namespace SV\ThreadReplyBanTeeth;

use XF\AddOn\AbstractSetup;
use XF\AddOn\StepRunnerInstallTrait;
use XF\AddOn\StepRunnerUninstallTrait;
use XF\AddOn\StepRunnerUpgradeTrait;

class Setup extends AbstractSetup
{
    use StepRunnerInstallTrait;
    use StepRunnerUpgradeTrait;
    use StepRunnerUninstallTrait;

    public function upgrade2010000Step1()
    {
        $this->renameOption('SV_ThreadReplyBanTeeth_EditBan', 'svEditReplyBan');
        $this->renameOption('SV_ThreadReplyBanTeeth_DeleteBan', 'svDeleteReplyBan');
        $this->renameOption('SV_ThreadReplyBanTeeth_LikeBan', 'svLikeReplyBan');
    }

    /**
     * @param $old
     * @param $new
     * @throws \XF\PrintableException
     */
    protected function renameOption($old, $new)
    {
        /** @var \XF\Entity\Option $optionOld */
        $optionOld = \XF::finder('XF:Option')->whereId($old)->fetchOne();
        $optionNew = \XF::finder('XF:Option')->whereId($new)->fetchOne();
        if ($optionOld && !$optionNew)
        {
            $optionOld->option_id = $new;
            $optionOld->save();
        }
    }
}
