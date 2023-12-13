{extends file="layout.tpl"}
{block name="title"}{seo_rewrite conf=['level'=>'root','type'=>'title','default'=>{$geoloc.company}]}{/block}
{block name="description"}{seo_rewrite conf=['level'=>'root','type'=>'description','default'=>{$geoloc.company}]}{/block}
{block name="styleSheet" append}
    {$css_files = ["cms","geoloc"]}
{/block}
{block name='body:id'}gmap{/block}
{block name='article'}
<article class="container cms" id="article" itemprop="mainContentOfPage" itemscope itemtype="http://schema.org/WebPageElement">
{block name='article:content' nocache}
{*<pre>{print_r($geoloc)}</pre>*}
    <div class="row">
        <div class="col-12 col-md-6 col-lg-7 col-xl-8">
            <h1 itemprop="name">{$geoloc.company}</h1>
            <div itemprop="text clearfix">
                {$geoloc.content}
            </div>
        </div>
        <div class="col-12 col-md-6 col-lg-5 col-xl-4" itemscope itemtype="https://schema.org/LocalBusiness">
            {include file="img/img.tpl" img=$geoloc.img lazy=true}
            <table class="table table-geoloc">
                {if $geoloc.location.address}
                <tr>
                    <td><i class="material-icons ico ico-location"></i></td>
                    <td>
                        <meta itemprop="name" content="{$geoloc.company}">
                        <div itemprop="address" itemscope itemtype="https://schema.org/PostalAddress">
                            <span itemprop="streetAddress">{$geoloc.location.address},</span>
                            <span itemprop="addressLocality">{$geoloc.location.city}</span>
                            <span itemprop="postalCode">{$geoloc.location.postcode}</span>
                            <span itemprop="addressCountry">{$geoloc.location.isocountry}</span>
                            <span>{$geoloc.location.country}</span>
                        </div>
                        <div itemprop="geo" itemscope itemtype="https://schema.org/GeoCoordinates">
                            <meta itemprop="latitude" content="{$geoloc.geo.lat}" />
                            <meta itemprop="longitude" content="{$geoloc.geo.lng}" />
                        </div>
                        <meta itemprop="priceRange" content="€€€"/>
                    </td>
                </tr>
                {/if}
                {if $geoloc.contact.mobile}
                <tr>
                    <td><i class="material-icons ico ico-smartphone"></i></td>
                    <td>
                        <meta itemprop="telephone" content="{$geoloc.contact.mobile}"/>
                        <a href="tel:{$geoloc.contact.mobile|replace:'(0)':''|replace:' ':''|replace:'.':''}">
                            {$geoloc.contact.mobile}
                        </a>
                    </td>
                </tr>
                {/if}
                {if $geoloc.contact.phone}
                <tr>
                    <td><i class="material-icons ico ico-phone"></i></td>
                    <td>
                        <a href="tel:{$geoloc.contact.phone|replace:'(0)':''|replace:' ':''|replace:'.':''}">
                            {$geoloc.contact.phone}
                        </a>
                    </td>
                </tr>
                {/if}
                {if $geoloc.contact.email}
                <tr>
                    <td><i class="material-icons ico ico-email"></i></td>
                    <td>{mailto address={$geoloc.contact.email} encode="hex"}</td>
                </tr>
                {/if}
                {if $geoloc.contact.fax}
                    <tr>
                        <td><i class="material-icons ico ico-print"></i></td>
                        <td>{$geoloc.contact.fax}</td>
                    </tr>
                {/if}
                {if $geoloc.social.facebook}
                    <tr>
                        <td><i class="material-icons ico ico-facebook"></i></td>
                        <td>
                            <a class="targetblank" href="{$geoloc.social.facebook}">{$geoloc.social.facebook}</a>
                        </td>
                    </tr>
                {/if}
                {if $geoloc.social.instagram}
                    <tr>
                        <td><i class="material-icons ico ico-instagram"></i></td>
                        <td>
                            <a class="targetblank" href="{$geoloc.social.instagram}">{$geoloc.social.instagram}</a>
                        </td>
                    </tr>
                {/if}
                {if $geoloc.social.linkedin}
                    <tr>
                        <td><i class="material-icons ico ico-linkedin"></i></td>
                        <td>
                            <a class="targetblank" href="{$geoloc.social.linkedin}">{$geoloc.social.linkedin}</a>
                        </td>
                    </tr>
                {/if}
                {if $geoloc.social.website}
                    <tr>
                        <td><i class="material-icons ico ico-tools"></i></td>
                        <td>
                            <a class="targetblank" href="{$geoloc.social.website}">{$geoloc.social.website}</a>
                        </td>
                    </tr>
                {/if}
                {if $geoloc.social.suppl}
                    <tr>
                        <td><i class="material-icons ico ico-tools"></i></td>
                        <td>
                            <a class="targetblank" href="{$geoloc.social.suppl}">{$geoloc.social.suppl}</a>
                        </td>
                    </tr>
                {/if}
            </table>
        </div>
    </div>
{/block}
</article>
{/block}