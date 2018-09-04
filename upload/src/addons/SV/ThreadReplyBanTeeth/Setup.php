<?php

namespace SV\ThreadReplyBanTeeth;

use SV\Utils\InstallerHelper;
use XF\AddOn\AbstractSetup;
use XF\AddOn\StepRunnerInstallTrait;
use XF\AddOn\StepRunnerUninstallTrait;
use XF\AddOn\StepRunnerUpgradeTrait;

class Setup extends AbstractSetup
{
    // from https://github.com/Xon/XenForo2-Utils cloned to src/addons/SV/Utils
    use InstallerHelper;
    use StepRunnerInstallTrait;
    use StepRunnerUpgradeTrait;
    use StepRunnerUninstallTrait;

    public function upgrade2010000Step1()
    {
        $this->renameOption('SV_ThreadReplyBanTeeth_EditBan', 'svEditReplyBan');
        $this->renameOption('SV_ThreadReplyBanTeeth_DeleteBan', 'svDeleteReplyBan');
        $this->renameOption('SV_ThreadReplyBanTeeth_LikeBan', 'svLikeReplyBan');
    }
}
