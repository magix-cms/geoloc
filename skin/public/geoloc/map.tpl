{if !empty($addresses)}
    {if isset($page) && !empty($page)}
        <article class="container">
            <h1{if empty($page.content_geoloc)} class="sr-only"{/if}>
                {if isset($page.name_geoloc) && !empty($page.name_geoloc)}
                    {$page.name_geoloc}
                {else}
                    {#access_plan#}
                {/if}
            </h1>
            {if isset($page.content_geoloc) && !empty($page.content_geoloc)}
                <div class="gmap-content">
                    {$page.content_geoloc}
                </div>
            {/if}
        </article>
    {/if}
    <div class="row">
        <div class="col-12 col-md-6"></div>
        <div class="col-12 col-md-6">
            <div class="dropdown filter">
                {strip}<button class="btn btn-block btn-box btn-default dropdown-toggle" type="button" data-toggle="dropdown">
                    <span>{if isset($tag)}{$tag.name}{else}{#address_by_type#|ucfirst}{/if}</span>
                    <span class="show-more"><i class="material-icons ico ico-arrow_drop_down"></i></span>
                    <span class="show-less"><i class="material-icons ico ico-arrow_drop_up"></i></span>
                    </button>{/strip}
                <ul class="dropdown-menu">
                    <li><a href="{$url}/{$lang}/geoloc/">{#all_address#}</a></li>
                    {foreach $tags as $tag}
                        <li{if $tag.id_tag eq $smarty.get.tag} class="active"{/if}>{if $tag.id_tag eq $smarty.get.tag}{$tag.name_tag}{else}<a href="{$url}/{$lang}/geoloc/tag/{$tag.id_tag}-{$tag.url_tag}">{$tag.name_tag}</a>{/if}</li>
                    {/foreach}
                </ul>
            </div>
        </div>
    </div>
    {*<pre>{print_r($addresses)}</pre>*}
    <div class="map">
        <div>
            <div id="gmap_map" class="gmap3"></div>
        </div>
        <div id="gmap-address" class="open">
            <div id="searchdir" class="collapse">
                <form class="form-search">
                    <div class="input-group">
                        <input type="text" class="form-control" id="getadress" name="getadress" placeholder="{#geoloc_adress#}" value="" />
                        <div class="input-group-btn">
                            <button class="btn btn-default subdirection" type="submit">
                                <i class="ico ico-search"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="alert alert-primary" itemscope itemtype="http://data-vocabulary.org/Organization">
                <a id="showform" class="btn btn-lg pull-right collapsed hidden-ph hidden-xs" type="button" data-toggle="collapse" data-target="#searchdir" aria-expanded="false" aria-controls="searchdir">
                    <span class="open"><i class="ico ico-directions"></i></span>
                    <span class="close"><i class="ico ico-close"></i></span>
                </a>
                {strip}<a id="openapp" class="btn btn-lg pull-right visible-ph visible-xs"
                   {if $mOS === 'IOS'} href="http://maps.apple.com/maps?ll={$addresses[0].geo.lat},{$addresses[0].geo.lng}&q={$addresses[0].location.address|escape:'url'}%2C%20{$addresses[0].location.city|escape:'url'}%2C%20{$addresses[0].location.country|escape:'url'}"
                   {else} href="geo:{$addresses[0].geo.lat},{$addresses[0].geo.lng}?q={$addresses[0].location.address|escape:'url'}%2C%20{$addresses[0].location.city|escape:'url'}%2C%20{$addresses[0].location.country|escape:'url'}"{/if} target="_blank">
                    <i class="ico ico-directions"></i>
                </a>{/strip}
                    <button class="btn btn-default btn-box hidepanel open">
                        <span class="show-less ver"><i class="ico ico-keyboard_arrow_up"></i></span>
                        <span class="show-more ver"><i class="ico ico-keyboard_arrow_down"></i></span>
                        <span class="show-less hor"><i class="ico ico-keyboard_arrow_left"></i></span>
                        <span class="show-more hor"><i class="ico ico-keyboard_arrow_right"></i></span>
                    </button>
                <meta itemprop="name" content="{$addresses[0].company}" />
                <div id="address" itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">
                    <span class="fa fa-map-marker"></span>
                    <span class="address" itemprop="streetAddress">{$addresses[0].location.address}</span>,
                    <span itemprop="addressLocality">
                        <span class="city">{$addresses[0].location.postcode} {$addresses[0].location.city}</span>, <span class="country">{$addresses[0].location.country}</span>
                    </span>
                    <div itemprop="address" itemscope itemtype="http://schema.org/GeoCoordinates">
                        <meta itemprop="latitude" content="{$addresses[0].geo.lat}" />
                        <meta itemprop="longitude" content="{$addresses[0].geo.lng}" />
                    </div>
                </div>
            </div>
            <div id="r-directions"></div>
        </div>
    </div>

    {if count($addresses) >= 1}
        <div id="addresses" class="container">
        {foreach $addresses as $addr}
        <div class="display">
            <div class="container">
                <div class="row">
                    <div class="col-12 col-sm-4">
                        {if !empty($addr.img)}
                            <div class="figure">
                                {if $addr.public_url}<a href="{$addr.public_url}">
                                {include file="img/img.tpl" img=$addr.img lazy=true}
                                </a>{/if}
                            </div>
                        {/if}
                    </div>
                    <div class="col-12 col-sm-8 content">
                        <h3>{$addr.company}</h3>
                        {if $addr.location.address != ''}<p>{$addr.location.address}, <br />{/if}{$addr.location.postcode} {$addr.location.city}, {$addr.location.country}</p>
                        {if $addr.resume}<p>{$addr.resume|truncate:150:'&hellip;'}</p>{/if}
                        <div class="block-btn">
                            {if $addr.public_url}
                                <a href="{$addr.public_url}" class="btn btn-box btn-main"">{#more_infos#}</a>
                            {/if}
                            <a href="#" class="btn btn-box btn-main select-marker" data-marker="{$addr@index}">{#see_on_map#}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {/foreach}
        </div>
        {*<div id="addresses" class="container">
            {foreach $addresses as $addr}
                {if ($addr@index)%2 == 0}
                    <div class="row">
                {/if}
                <div class="col-12 col-sm-6 col-md-4 col-lg-6">
                    {capture name="content"}
                        <h3>{$addr.company}</h3>
                        <p>{$addr.location.address}, {$addr.location.postcode} {$addr.location.city}, {$addr.location.country}</p>
                        {if $addr.resume}<p class="text-justify">{$addr.resume|truncate:120:'&hellip;'}</p>{/if}
                        <p>
                            {if $addr.public_url}<a href="{$addr.public_url}" class="btn btn-box btn-main"">{#more_infos#}</a>{/if}
                            <a href="#" class="btn btn-box btn-main select-marker" data-marker="{$addr@index}">{#see_on_map#}</a>
                        </p>
                    {/capture}
                    {if !empty($addr.img)}
                        <div class="row">
                            <div class="col-12 col-xs-6 col-sm-12 col-lg-6">
                                {include file="img/img.tpl" img=$addr.img lazy=true}
                            </div>
                            <div class="col-12 col-xs-6 col-sm-12 col-lg-6">
                                {$smarty.capture.content}
                            </div>
                        </div>
                    {else}
                        {$smarty.capture.content}
                    {/if}
                </div>
                {if ($addr@index +1)%2 == 0 || {$addr@last}}
                    </div>
                {/if}
            {/foreach}
        </div>*}
    {/if}
{else}
    <div class="container">
        <div class="mc-message clearfix">
            <p class="alert alert-warning fade in">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <span class="ico ico-warning-sign"></span> {#geoloc_plugin_error#} : {#geoloc_plugin_configured#}
            </p>
        </div>
    </div>
{/if}