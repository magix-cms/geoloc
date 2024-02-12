{extends file="layout.tpl"}
{block name="title"}{if !empty($page.seo_title_geoloc)}{$page.seo_title_geoloc}{else}{#seo_title_geoloc#}{/if}{/block}
{block name="description"}{if !empty($page.seo_desc_geoloc)}{$page.seo_desc_geoloc}{else}{#seo_desc_geoloc#}{/if}{/block}
{block name='body:id'}gmap{/block}
{block name="styleSheet" append}{if $consentedCookies.ggMapCookies}
    {$css_files = ["geoloc","form"]}
{/if}{/block}
{block name="main"}
    {if $consentedCookies.ggMapCookies}
        {include file="geoloc/map.tpl"}
        {*<pre>{print_r($addresses)}</pre>*}
    {else}
        <div class="container">
            <p class="h1">{#accept_gmap_cookies_to_display#}</p>
        </div>
    {/if}
{/block}
{if $consentedCookies.ggMapCookies}
{block name="scripts"}
    <script type="text/javascript">
        let configMap = {$config_gmap};
        /*async function initMap() {
            const { Map } = await google.maps.importLibrary("maps");
            const { Marker } = await google.maps.importLibrary("marker");

            //window.addEventListener('DOMContentLoaded',() => {
            let gMap = new GoogleMap(configMap);
            //});
        }*/
    </script>
    {$jquery = false}
    {$js_files = [
        'group' => [],
        'normal' => [],
        'async' => [
            "/plugins/geoloc/js/{if $setting.mode === 'dev'}src/{/if}gmap{if $setting.mode !== 'dev'}.min{/if}.js"
        ]
    ]}
{/block}
{*{block name="foot"}
    <script src="https://maps.google.com/maps/api/js?language={$lang}{if $config.api_key != '' AND $config.api_key != NULL}&amp;key={$config.api_key}{/if}" async></script>
{/block}*}{/if}