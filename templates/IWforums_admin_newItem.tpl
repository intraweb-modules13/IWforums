<script language="javascript">
    function send(){
        var error=false;
        if(document.new_forum.nom_forum.value==""){
			//for gt detection
            alert("{{gt text="You haven't write the name of the forum"}}");
            var error=true;
        }
        if(error){return false;}
        document.forms['new_forum'].submit();
    }
    
    function cancel() {
        window.location = "index.php?module=IWforums&type=admin&func=main";
    }
</script>
{pageaddvar name='stylesheet' value='vendor/bootstrap/css/bootstrap.css'}
{pageaddvar name='stylesheet' value='modules/IWforums/style/bsRewrite.css'}
{include file="IWforums_admin_menu.htm"}
<div class="z-admincontainer">
    {if $m eq "c"}
        <div>{img modname=$this->name src='dupForuml.png' style="float:right"}</div>
    {else}
        <div>{img modname='core' src='filenew.png' set='icons/large' style="float:right"}</div>
    {/if}
    <h2>
        {if isset($forum.fid) AND $forum.fid gt 0}
            {if $m eq "c"}
                {gt text="Create a new forum copying '%s' values" tag1=$forum.nom_forum}
            {else}
                {gt text="Edit forum"} {$forum.nom_forum}
            {/if}
        {else}
            {gt text="Create a new forum"}
        {/if}
    </h2>
    <form  class="form-horizontal" role="form" method="post" name="new_forum" id="new_forum" action="{modurl modname='IWforums' type='admin' func='create'}" enctype="application/x-www-form-urlencoded">
        <input type="hidden" name="csrftoken" value="{insert name='csrftoken'}" />
        <input type="hidden" name="m" value="{$m}" />
        <input type="hidden" name="grup" value="{$forum.grup}" />
        <input type="hidden" name="mod" value="{$forum.mod}" />
        <input type="hidden" name="fid" value="{$forum.fid}" />
        <div class="form-group">
            <label class="col-xs-2 control-label"for="nom_forum">{gt text="Forum name"}</label>
            <div class="col-xs-9">
                <input id="nom_forum" name="nom_forum" type="text" maxlength="50" class="form-control" value="{$forum.nom_forum}">
            </div>
        </div>
        <div class="form-group">
            <label class="col-xs-2 control-label" for="descriu">{gt text="Brief description"}</label>
            <div class="col-xs-9">
                <input id="descriu" class="form-control" name="descriu" type="text" maxlength="100" value="{$forum.descriu|safehtml}"/>
            </div>
        </div>
        <div class="form-group">
            <label class="col-xs-2 control-label">{gt text='Introduction'} ({gt text="optional"})</label>
            <div class="col-xs-9">
                <textarea class="form-control" id="longDescriu" name="longDescriu">{$forum.longDescriu}</textarea>
            </div>
        </div>
        <div class="form-group">
            <label class="col-xs-2 control-label">{gt text='Observations'} ({gt text="optional"})</label>
            <div class="col-xs-9">
                <input type="text" class="form-control" id="observacions" name="observacions" value="{$forum.observacions}">
            </div>
        </div>
        
        <div class="form-group">
            <div class="col-sm-offset-1" >
            <label class="control-label" for="msgEditTime">{gt text="Minutes, after the submission, during which the messages can be edited by theirs authors"}</label>
            <input style="width:70px; display:inline" class="form-control" id="msgEditTime" name="msgEditTime" type="number" min="0" max="999" {if isset($forum.msgEditTime) && $forum.msgEditTime neq ""} value="{$forum.msgEditTime}" {else} value="15" {/if}/>
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-offset-1 col-sx-10">
            <label for="msgDelTime">{gt text="Minutes, after the submission, during which the messages can be deleted by theirs authors"}</label>
            <input style="width:70px; display:inline" class="form-control" id="msgDelTime" name="msgDelTime" type="number" min="0" max="999" {if isset($forum.msgDelTime) && $forum.msgDelTime neq ""} value="{$forum.msgDelTime}" {else} value='15' {/if}/>
            </div>
        </div>
        {if $modvars.IWmain.crAc_UserReports && $modvars.IWmain.crAc_UR_IWforums} {* If reports are enabled *}
        <div class="form-group">
            <label class="control-label col-xs-3"  for="subscrType">{gt text="Forum subscription type"} </label>  
                <select class="form-control" id="subscrMode" name="subscrMode" style="width:50%">      
                  <option value="{'IWforums_Constant::VOLUNTARY'|constant}" {if isset($forum.subscriptionMode) && $forum.subscriptionMode eq 'IWforums_Constant::VOLUNTARY'|constant}selected{/if}>{gt text = "Users must subscribe to the forum (Voluntary)"}</option>
                  <option value="{'IWforums_Constant::NOT_ALLOWED'|constant}" {if isset($forum.subscriptionMode) && $forum.subscriptionMode eq 'IWforums_Constant::NOT_ALLOWED'|constant }selected{/if}>{gt text = "Nobody can subscribe to this forum (No subscription)"}</option>
                  <option value="{'IWforums_Constant::OPTIONAL'|constant}" {if isset($forum.subscriptionMode) && $forum.subscriptionMode eq 'IWforums_Constant::OPTIONAL'|constant}selected{/if}>{gt text = "All users are subscribed by default and may unsubscribe (Optional)"}</option> 
                  <option value="{'IWforums_Constant::COMPULSORY'|constant}" {if isset($forum.subscriptionMode) && $forum.subscriptionMode eq 'IWforums_Constant::COMPULSORY'|constant}selected{/if}>{gt text = "All users are subscribed by default but can't unsubscribe (Compulsory)"}</option> 
                </select> 
        </div>
        {/if}
        <div class="form-group">
            <div class="col-sm-offset-2 col-sx-10" >
            <label class="control-label" for="adjunts">
                <input id="adjunts" name="adjunts" type="checkbox" {if $forum.adjunts eq 1}checked{/if} value="1"/>&nbsp;{gt text="Attached files are allowed"}</label>
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-offset-2 col-sx-10" >
            <label class="control-label"  for="actiu">            
                <input id="actiu" name="actiu" type="checkbox" {if $forum.actiu eq 1}checked{/if} value="1" />&nbsp;{gt text="Active?"}
            </label>
            </div>
        </div>
        
        <div class="form-group z-center">

                {if isset($forum.fid) AND $forum.fid gt 0}
                    {if $m eq "c"}
                        <button type="button" class="btn btn-success" onclick="javascript: send();">
                            <span class="glyphicon glyphicon-ok"></span>&nbsp;{gt text="Create"}
                        </button>
                    {else}
                        <button type="button" class="btn btn-success" onclick="javascript: send();">
                            <span class="glyphicon glyphicon-ok"></span>&nbsp;{gt text="Modify"}
                        </button>
                    {/if}
                {else}
                   <button type="button" class="btn btn-success" onclick="javascript: send();">
                        <span class="glyphicon glyphicon-ok"></span>&nbsp;{gt text="Create"}
                    </button>
                {/if}
                <button type="button" class="btn btn-danger" onclick="javascript:cancel()">
                    <span class="glyphicon glyphicon-remove"></span>&nbsp;{gt text="Cancel"}
                </button>
        </div>
        {notifydisplayhooks eventname='IWforums.ui_hooks.IWforums.form_edit' id=null}
    </form>
</div>