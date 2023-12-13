<div class="row">
    <form id="edit_address" action="{$smarty.server.SCRIPT_NAME}?controller={$smarty.get.controller}&amp;tabs=address&amp;action={if !$edit}add{else}edit{/if}" method="post" class="validate_form{if !$edit} add_form collapse in{else} edit_form{/if} col-ph-12">
        <div id="drop-zone"{if !isset($address.img_address) || empty($address.img_address)} class="no-img"{/if}>
            <div id="drop-buttons" class="form-group">
                <label id="clickHere" class="btn btn-default">
                    ou cliquez ici.. <span class="fa fa-upload"></span>
                    <input type="hidden" name="MAX_FILE_SIZE" value="4048576" />
                    <input type="file" id="img" name="img" />
                    {*<input type="hidden" id="id_product" name="id" value="{$address.id_address}">*}
                </label>
            </div>
            <div class="preview-img">
                {if isset($address.img_address) && !empty($address.img_address)}
                    <img id="preview" src="/upload/geoloc/{$address.id_address}/{$address.img_address}" alt="address" class="preview img-responsive" />
                {else}
                    <img id="preview" src="#" alt="Déposez votre images ici..." class="no-img img-responsive" />
                {/if}
            </div>
        </div>
        {include file="language/brick/dropdown-lang.tpl"}
        <div class="row">
            <div class="col-ph-12">
                <div class="tab-content">
                    {foreach $langs as $id => $iso}
                        <div role="tabpanel" class="tab-pane{if $iso@first} active{/if}" id="lang-{$id}">
                            <fieldset>
                                <legend>Adresse</legend>
                                <div class="row">
                                    <div class="col-xs-12 col-sm-8 col-md-9 col-lg-10">
                                        <div class="form-group">
                                            <label for="company_address_{$id}">{#company_address#|ucfirst} :</label>
                                            <input type="text" class="form-control" id="company_address_{$id}" name="address[content][{$id}][company_address]" value="{$address.content[{$id}].company_address}" size="50" />
                                        </div>
                                        <div class="form-group">
                                            <label for="address_address_{$id}">{#address_address#|ucfirst} :</label>
                                            <input type="text" class="form-control address" id="address_address_{$id}" name="address[content][{$id}][address_address]" value="{$address.content[{$id}].address_address}" size="50" />
                                        </div>
                                        <div class="row">
                                            <div class="col-xs-12 col-sm-6">
                                                <div class="form-group">
                                                    <label for="postcode_address_{$id}">{#postcode_address#|ucfirst} :</label>
                                                    <input type="text" class="form-control postcode" id="postcode_address_{$id}" name="address[content][{$id}][postcode_address]" value="{$address.content[{$id}].postcode_address}" size="50" />
                                                </div>
                                            </div>
                                            <div class="col-xs-12 col-sm-6">
                                                <div class="form-group">
                                                    <label for="city_address_{$id}">{#city_address#|ucfirst} :</label>
                                                    <input type="text" class="form-control city" id="city_address_{$id}" name="address[content][{$id}][city_address]" value="{$address.content[{$id}].city_address}" size="50" />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="country_address_{$id}">{#country#}&nbsp;:</label>
                                            <select id="country_address_{$id}" class="form-control country" name="address[content][{$id}][country_address]">
                                                <option value="">{#select_country#}</option>
                                                {foreach $countries as $key => $val}
                                                    <option value="{$val}" data-iso="{$key}" {if {$address.content[{$id}].country_address} === $val} selected{/if}>{$val|ucfirst}</option>
                                                {/foreach}
                                            </select>
                                        </div>
                                        <div class="row">
                                            <div class="col-xs-12 col-sm-6">
                                                <div class="form-group">
                                                    <label for="lat_address_{$id}">{#lat_address#|ucfirst} :</label>
                                                    <input type="text" class="form-control lat" id="lat_address_{$id}" name="address[content][{$id}][lat_address]" value="{$address.content[{$id}].lat_address}" size="50" />
                                                </div>
                                            </div>
                                            <div class="col-xs-12 col-sm-6">
                                                <div class="form-group">
                                                    <label for="lng_address_{$id}">{#lng_address#|ucfirst} :</label>
                                                    <input type="text" class="form-control lng" id="lng_address_{$id}" name="address[content][{$id}][lng_address]" value="{$address.content[{$id}].lng_address}" size="50" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-sm-4 col-md-3 col-lg-2">
                                        <div class="form-group">
                                            <label for="published_address_{$id}">Statut</label>
                                            <input id="published_address_{$id}" data-toggle="toggle" type="checkbox" name="address[content][{$id}][published_address]" data-on="Publiée" data-off="Brouillon" data-onstyle="success" data-offstyle="danger"{if (!isset($address) && $iso@first) || $address.content[{$id}].published_address} checked{/if}>
                                        </div>
                                    </div>
                                </div>
                                <div class="map-col"></div>
                            </fieldset>
                            <fieldset>
                                <legend>Informations Complémentaires</legend>
                                <div class="row">
                                    <div class="col-xs-12 col-md-4">
                                        <div class="form-group">
                                            <label for="phone_address_{$id}">{#phone_address#}</label>
                                            <input id="phone_address_{$id}" type="text" class="form-control" name="address[content][{$id}][phone_address]" placeholder="{#ph_phone_address#}" {if isset($address)} value="{$address.content[{$id}].phone_address}"{/if} />
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-md-4">
                                        <div class="form-group">
                                            <label for="mobile_address_{$id}">{#mobile_address#}</label>
                                            <input id="mobile_address_{$id}" type="text" class="form-control" name="address[content][{$id}][mobile_address]" placeholder="{#ph_mobile_address#}" {if isset($address)} value="{$address.content[{$id}].mobile_address}"{/if} />
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-md-4">
                                        <div class="form-group">
                                            <label for="fax_address_{$id}">{#fax_address#}</label>
                                            <input id="fax_address_{$id}" type="text" class="form-control" name="address[content][{$id}][fax_address]" placeholder="{#ph_fax_address#}" {if isset($address)} value="{$address.content[{$id}].fax_address}"{/if} />
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-xs-12 col-md-8">
                                        <div class="form-group">
                                            <label for="email_address_{$id}">{#email_address#}</label>
                                            <input id="email_address_{$id}" type="text" class="form-control" name="address[content][{$id}][email_address]" placeholder="{#ph_email_address#}" {if isset($address)} value="{$address.content[{$id}].email_address}"{/if} />
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-md-4">
                                        <div class="form-group">
                                            <label for="vat_address_{$id}">{#vat_address#}</label>
                                            <input id="vat_address_{$id}" type="text" class="form-control" name="address[content][{$id}][vat_address]" placeholder="{#ph_vat_address#}" {if isset($address)} value="{$address.content[{$id}].vat_address}"{/if} />
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-xs-12">
                                        <div class="form-group">
                                            <label for="content_address_{$id}">{#content#|ucfirst} :</label>
                                            <textarea name="address[content][{$id}][content_address]" id="content_address_{$id}" class="form-control mceEditor">{call name=cleantextarea field=$address.content[{$id}].content_address}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </fieldset>
                            <fieldset>
                                <legend>Réseaux</legend>
                                <div class="row">
                                    <div class="col-xs-12 col-md-4">
                                        <div class="form-group">
                                            <label for="facebook_address_{$id}">{#facebook_address#}</label>
                                            <input id="facebook_address_{$id}" type="text" class="form-control" name="address[content][{$id}][facebook_address]" placeholder="{#ph_facebook_address#}" {if isset($address)} value="{$address.content[{$id}].facebook_address}"{/if} />
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-md-4">
                                        <div class="form-group">
                                            <label for="instagram_address_{$id}">{#instagram_address#}</label>
                                            <input id="instagram_address_{$id}" type="text" class="form-control" name="address[content][{$id}][instagram_address]" placeholder="{#ph_instagram_address#}" {if isset($address)} value="{$address.content[{$id}].instagram_address}"{/if} />
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-md-4">
                                        <div class="form-group">
                                            <label for="linkedin_address_{$id}">{#linkedin_address#}</label>
                                            <input id="linkedin_address_{$id}" type="text" class="form-control" name="address[content][{$id}][linkedin_address]" placeholder="{#ph_linkedin_address#}" {if isset($address)} value="{$address.content[{$id}].linkedin_address}"{/if} />
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-xs-12 col-md-6">
                                        <div class="form-group">
                                            <label for="website_address_{$id}">{#website_address#}</label>
                                            <input id="website_address_{$id}" type="text" class="form-control" name="address[content][{$id}][website_address]" placeholder="{#ph_website_address#}" {if isset($address)} value="{$address.content[{$id}].website_address}"{/if} />
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-md-6">
                                        <div class="form-group">
                                            <label for="suppl_address_{$id}">{#suppl_address#}</label>
                                            <input id="suppl_address_{$id}" type="text" class="form-control" name="address[content][{$id}][suppl_address]" placeholder="{#ph_suppl_address#}" {if isset($address)} value="{$address.content[{$id}].suppl_address}"{/if} />
                                        </div>
                                    </div>
                                </div>
                            </fieldset>
                            {if $edit}
                                <div class="row">
                                    <div class="col-ph-12 col-sm-8">
                                        <div class="form-group">
                                            <label for="public-url[{$id}]">URL de la page</label>
                                            <input type="text" class="form-control public-url" data-lang="{$id}" id="public_url[{$id}]" readonly="readonly" size="50" value="{$address.content[{$id}].public_url}" />
                                        </div>
                                    </div>
                                </div>
                            {/if}
                            {if $edit}
                                <fieldset>
                                    <legend>{#category#}</legend>
                                    <div class="row">
                                        <div class="col-ph-12 col-sm-12">
                                            <div class="form-group">
                                                <label for="address[content][{$id}][tag_address]">{#address_tag#|ucfirst}Tags :</label>
                                                <input type="text" class="tags-input" value="{$address.content[{$id}].tags_address}" data-lang="{$id}" {*data-role="tagsinput"*} name="address[content][{$id}][tag_address]" id="tag-address-{$id}"/>
                                                <input type="hidden" id="auto-tag-{$id}" disabled="disabled" value="{$address.content[{$id}].tags}" />
                                            </div>
                                        </div>
                                    </div>
                                </fieldset>
                            {/if}
                            {*<fieldset>
                                <legend>Options</legend>
                                <div class="form-group">
                                    <label for="link_address_{$id}">{#link_address#|ucfirst} :</label>
                                    <input type="text" class="form-control" id="link_address_{$id}" name="address[content][{$id}][link_address]" value="{$address.content[{$id}].link_address}" size="50" />
                                </div>
                                <div class="form-group">
                                    <label for="blank_address_{$id}">{#blank_address#|ucfirst}</label>
                                    <div class="switch">
                                        <input type="checkbox" id="blank_address_{$id}" name="address[content][{$id}][blank_address]" class="switch-native-control"{if $address.content[{$id}].blank_address} checked{/if} />
                                        <div class="switch-bg">
                                            <div class="switch-knob"></div>
                                        </div>
                                    </div>
                                </div>
                            </fieldset>*}
                        </div>
                    {/foreach}
                </div>
            </div>
        </div>
        <fieldset>
            <legend>Enregistrer</legend>
            {if $edit}
            <input type="hidden" name="id" id="id_address" value="{$address.id_address}" />
            {/if}
            <button class="btn btn-main-theme" type="submit">{#save#|ucfirst}</button>
        </fieldset>
    </form>
</div>