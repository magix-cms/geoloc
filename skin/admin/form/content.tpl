{include file="language/brick/dropdown-lang.tpl"}
<div class="row">
    <form id="edit_geoloc" action="{$smarty.server.SCRIPT_NAME}?controller={$smarty.get.controller}&amp;tabs=content&amp;action=edit" method="post" class="validate_form edit_form col-ph-12 col-md-8">
        <div class="row">
            <div class="col-ph-12">
                <div class="tab-content">
                    {foreach $langs as $id => $iso}
                        <fieldset role="tabpanel" class="tab-pane{if $iso@first} active{/if}" id="lang-{$id}">
                            <div class="row">
                                <div class="col-xs-12 col-sm-8">
                                    <div class="form-group">
                                        <label for="content[{$id}][name_geoloc]">{#title#|ucfirst} *:</label>
                                        <input type="text" class="form-control" id="content[{$id}][name_geoloc]" name="content[{$id}][name_geoloc]" value="{$pages.content[{$id}].name_geoloc}" size="50" />
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-4">
                                    <div class="form-group">
                                        <label for="content[{$id}][published_geoloc]">Statut</label>
                                        <input id="content[{$id}][published_geoloc]" data-toggle="toggle" type="checkbox" name="content[{$id}][published_geoloc]" data-on="Publiée" data-off="Brouillon" data-onstyle="success" data-offstyle="danger"{if (!isset($pages) && $iso@first) || $pages.content[{$id}].published_geoloc} checked{/if}>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-12 col-sm-12">
                                    <div class="form-group">
                                        <label for="content[{$id}][content_geoloc]">{#content#|ucfirst} :</label>
                                        <textarea name="content[{$id}][content_geoloc]" id="content[{$id}][content_geoloc]" class="form-control mceEditor">{call name=cleantextarea field=$pages.content[{$id}].content_geoloc}</textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <button class="btn collapsed btn-collapse" role="button" data-toggle="collapse" href="#metas-{$id}" aria-expanded="true" aria-controls="metas-{$id}">
                                    <span class="fa"></span> {#display_metas#|ucfirst}
                                </button>
                            </div>
                            <div id="metas-{$id}" class="collapse">
                                <div class="row">
                                    <div class="col-ph-12 col-sm-8">
                                        <div class="form-group">
                                            <label for="content[{$id}][seo_title_geoloc]">{#title#|ucfirst} :</label>
                                            <textarea class="form-control" id="content[{$id}][seo_title_geoloc]" name="content[{$id}][seo_title_geoloc]" cols="70" rows="3">{$pages.content[{$id}].seo_title_geoloc}</textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-ph-12 col-sm-8">
                                        <div class="form-group">
                                            <label for="content[{$id}][seo_desc_geoloc]">Description :</label>
                                            <textarea class="form-control" id="content[{$id}][seo_desc_geoloc]" name="content[{$id}][seo_desc_geoloc]" cols="70" rows="3">{$pages.content[{$id}].seo_desc_geoloc}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                    {/foreach}
                </div>
            </div>
        </div>
        {*<input type="hidden" id="id_geoloc" name="id" value="{$pages.id_geoloc}">*}
        <button class="btn btn-main-theme pull-right" type="submit" name="action" value="edit">{#save#|ucfirst}</button>
    </form>
</div>