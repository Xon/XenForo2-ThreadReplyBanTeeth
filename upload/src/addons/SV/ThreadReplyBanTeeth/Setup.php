<?php

namespace SV\ThreadReplyBanTeeth;

use SV\StandardLib\InstallerHelper;
use XF\AddOn\AbstractSetup;
use XF\AddOn\StepRunnerInstallTrait;
use XF\AddOn\StepRunnerUninstallTrait;
use XF\AddOn\StepRunnerUpgradeTrait;

class Setup extends AbstractSetup
{
    use InstallerHelper;
    use StepRunnerInstallTrait;
    use StepRunnerUpgradeTrait;
    use StepRunnerUninstallTrait;

    public function upgrade2010000Step1(): void
    {
        $this->renameOption('SV_ThreadReplyBanTeeth_EditBan', 'svEditReplyBan');
        $this->renameOption('SV_ThreadReplyBanTeeth_DeleteBan', 'svDeleteReplyBan');
        $this->renameOption('SV_ThreadReplyBanTeeth_LikeBan', 'svLikeReplyBan');
    }
}
