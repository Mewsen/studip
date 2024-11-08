<section class="contentbox">
    <header>
        <h1>Changes of visibility in Stud.IP</h1>
    </header>
    <section>
        <table width="100%" border="0" cellspacing="10" cellpadding="10">
            <colgroup>
                <col style="width: 47%">
                <col style="width: 6%">
                <col style="width: 47%">
            </colgroup>
            <tr>
                <td colspan="3">
                    <p>You now have the option to decide whether you want to be visible or invisible in Stud.IP. This setting can be changed at any time under Profile->Settings->Privacy.</p>
                    <p> In principle and regardless of being visible or invisible, some things are valid:</p>
                    <ul>
                        <li>you can use Stud.IP actively and take part in courses, forum discussions, etc.</li>
                        <li>participant-lists of courses are only accessible for the participants if everyone of them is in
                            agreement with it</li>
                        <li>as soon as you become active in the system - which means creating articles in forum discussions,
                            taking part in non-invisible polls (votings), sending mails, etc. - your name is indicated and it
                            isn't possible to avoid that other users can see directly whether you are visible or invisible.</li>
                    </ul>
                </td>
            </tr>

            <tr>
                <td style="background:#ddffdd; border:1px solid #d0d7e3;" valign="top">
                    <p><b>If you are visible you</b></p>
                    <ul>
                        <li>will appear in the public "who-is-online"-list with the time of your last activity</li>
                        <li>can use any opportunity of communication</li>
                        <li>can decide most extensively what information other users are able to see on your personal homepage</li>
                        <li>can be found by other users and come into contact with them</li>
                        <li>make a contribution to help making Stud.IP an active and communicative platform</li>
                    </ul>
                    <?= \Studip\LinkButton::create('Become visible', URLHelper::getURL('?vis_state=yes&vis_cmd=apply')) ?>
                </td>
                <td></td>
                <td style="background:#ffdddd; border:1px solid #d0d7e3;" valign="top">
                    <p><b>If you are invisible you</b>
                    <ul>
                        <li>cannot be found via "user search"</li>
                        <li>don't appear in the "who is online"-list</li>
                        <li>cannot use your Stud.IP-homepage anymore</li>
                        <li>don't give the opportunity to other users for reaching your e-mail address, your Stud.IP-score, etc.</li>
                        <li>cannot be listed into the address-books of other users</li>
                    </ul>
                    <?= \Studip\LinkButton::create('Become invisible', URLHelper::getURL('?vis_state=no&vis_cmd=apply')) ?>
                </td>
            </tr>
            <? if (Config::get()->PRIVACY_URL): ?>
            <tr>
                <td colspan="3">
                    <p>
                        For further information, please refer to the
                        <a href="<?=URLHelper::getURL(Config::get()->PRIVACY_URL, ['cancel_login' => 1], true) ?>" target="_bank">privacy policy</a>.
                    </p>
                </td>
            </tr>
            <? endif; ?>
        </table>
    </section>
</section>
