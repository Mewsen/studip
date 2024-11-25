<div data-admission-rule="ParticipantRestrictedAdmission">
    <participant-restricted-admission :distribution="<?= $rule->getDistributionTime() ?>"
                                      :fcfs="<?= $rule->isFCFSAllowed() ? 'true' : 'false'?>"
                                      :hasPrios="false"></participant-restricted-admission>
</div>
