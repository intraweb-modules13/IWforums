<script language="javascript">
    function send(){
    var error = false;
            if (document.new_forum.nom_forum.value == ""){
    alert("{{gt text="You haven't writte the name of the forum "}}");
            var error = true;
    }
    if (error){return false; }
    document.forms['new_forum'].submit();
    }
</script>
{include file="IWforums_admin_menu.tpl"}
<div class="z-admincontainer">
    <div class="z-adminpageicon">{img modname='core' src='filenew.png' set='icons/large'}</div>
    <h2>
        <!--[if isset($forum.fid) AND $forum.fid gt 0]-->
        {gt text="Edit fòrum"} {$forum.nom_forum}
        <!--[else]-->

        {gt text="Create a new forum"}
        <!--[/if]-->
    </h2>
    <form  class="z-form" method="post" name="new_forum" id="new_forum" action="{modurl modname='IWforums' type='admin' func='create'}" enctype="application/x-www-form-urlencoded" onSubmit="return send()">
        <input type="hidden" name="csrftoken" value="{insert name='csrftoken'}" />
        <input type="hidden" name="m" value="{$m}" />
        <input type="hidden" name="fid" value="{$forum.fid}" />
        <div class="z-formrow">
            <label for="nom_forum">{gt text="Forum name"}</label>
            <input id="nom_forum" name="nom_forum" type="text" size="50" maxlength="200" value="{$forum.nom_forum}"/>
        </div>
        <div class="z-formrow">
            <label for="descriu">{gt text="Description"}</label>
            <input id="descriu" name="descriu" type="text" size="50" maxlength="200" value="{$forum.descriu}"/>
        </div>
        <div class="z-formrow">
            <label for="adjunts">{gt text="Attached files are allowed"}</label>
            <input id="adjunts" name="adjunts" type="checkbox" {if $forum.adjunts eq 1}checked{/if} value="1"/>
        </div>
        <div class="z-formrow">
            <label for="msgEditTime">{gt text="Minutes, after the submission, during which the messages can be edited by theirs authors"}</label>
            <input id="msgEditTime" name="msgEditTime" type="text" size="5" maxlength="3" value="{$forum.msgEditTime}"/>
        </div>
        <div class="z-formrow">
            <label for="msgDelTime">{gt text="Minutes, after the submission, during which the messages can be deleted by theirs authors"}</label>
            <input id="msgDelTime" name="msgDelTime" type="text" size="5" maxlength="3" value="{$forum.msgDelTime}"/>
        </div>

        <div class="z-formrow">
            <label for="subscriptions">{gt text="Subscriptions to this forum"}</label>
            <select name="subscriptions" id="subscriptions">
                <option {if $forum.subscriptions eq 0}selected="selected"{/if} value="0">{gt text="Subscriptions are not available"}</option>
                <option {if $forum.subscriptions eq 1}selected="selected"{/if} value="1">{gt text="Everybody who can access to this forum is subscribed to it by default"}</option>
                <option {if $forum.subscriptions eq 2}selected="selected"{/if} value="2">{gt text="Users that have access to this forum can subscribe to it but they are not subscribed by default"}</option>
                <option {if $forum.subscriptions eq 3}selected="selected"{/if} value="3">{gt text="The subscription to this forum is forced"}</option>
            </select>
        </div>
        <div class="z-formrow">
            <label for="sendByCron">{gt text="Can send notification because the subscription by cron"}</label>
            <input id="sendByCron" name="sendByCron" type="checkbox" {if $forum.sendByCron eq 1}checked="checked"{/if} value="1" />
                   <div class="z-formnote z-informationmsg">
                {gt text="Check this option only if you will activate a cron that will send the messages to forum's subscriptors. In this case users will have the possibility to choose between receive an email for each new message in the forum or receive only a sumari every day. You can send messages calling the function called sendMails."}
            </div>
        </div>
        <div class="z-formrow">
            <label for="observacions">{gt text="Observations"}</label>
            <textarea type="text" name="observacions" cols="42" rows="5" style="width: 300px">{$forum.observacions}</textarea>
        </div>
        <div class="z-formrow">
            <label for="actiu">{gt text="Active?"}</label>
            <input id="actiu" name="actiu" type="checkbox" {if $forum.actiu eq 1}checked{/if} value="1"/>
        </div>
        <div class="z-center">
            <span class="z-buttons">
                <!--[if isset($forum.fid) AND $forum.fid gt 0]-->
                <a onclick="javascript: send();">
                    {img modname='core' src='button_ok.png' set='icons/small' __alt="Modify" __title="Modify"}
                    {gt text="Modify"}
                </a>
                <!--[else]-->
                <a onclick="javascript: send();">
                    {img modname='core' src='button_ok.png' set='icons/small' __alt="Create" __title="Create"}
                    {gt text="Create"}
                </a>
                <!--[/if]-->
            </span>
            <span class="z-buttons">
                <a href="{modurl modname='IWforums' type='admin' func='main'}">
                    {img modname='core' src='button_cancel.png' set='icons/small'   __alt="Cancel" __title="Cancel"}
                    {gt text="Cancel"}
                </a>
            </span>
        </div>
    </form>
</div>