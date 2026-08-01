<?php

namespace App\Services;

use App\Models\PixelSetting;

class PixelManager
{
    public static function head(): string
    {
        $html = '';
        foreach (PixelSetting::active() as $setting) {
            $html .= match ($setting->platform) {
                'facebook' => self::facebook($setting),
                'tiktok'   => self::tiktok($setting),
                'google'   => self::google($setting),
                default    => '',
            };
        }
        return $html;
    }

    protected static function facebook(PixelSetting $s): string
    {
        return "<script>
            !function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?
            n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;
            n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;
            t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,
            document,'script','https://connect.facebook.net/en_US/fbevents.js');
            fbq('init', '{$s->pixel_id}');
            fbq('track', 'PageView');
        </script>";
    }

    protected static function tiktok(PixelSetting $s): string
    {
        return "<script>
            !function (w, d, t) {w.TiktokAnalyticsObject=t;var ttq=w[t]=w[t]||[];ttq.methods=['page','track','identify','instances','debug','on','off','once','ready','alias','group','enableCookie','disableCookie'],ttq.setAndDefer=function(t,e){t[e]=function(){t.push([e].concat(Array.prototype.slice.call(arguments,0)))}};for(var i=0;i<ttq.methods.length;i++)ttq.setAndDefer(ttq,ttq.methods[i]);ttq.instance=function(t){for(var e=ttq._i[t]||[],n=0;n<e.length;n++)ttq.setAndDefer(e,ttq.methods[n]);return e},ttq.load=function(e,n){var i='https://analytics.tiktok.com/i18n/pixel/events.js';ttq._i=ttq._i||{},ttq._i[e]=[],ttq._i[e]._u=i,ttq._t=ttq._t||{},ttq._t[e]=+new Date,ttq._o=ttq._o||{},ttq._o[e]=n||{};var o=document.createElement('script');o.type='text/javascript',o.async=!0,o.src=i+'?sdkid='+e+'&lib='+t;var a=document.getElementsByTagName('script')[0];a.parentNode.insertBefore(o,a)};
            ttq.load('{$s->pixel_id}');
            ttq.page();
            }(window, document, 'ttq');
        </script>";
    }

    protected static function google(PixelSetting $s): string
    {
        return "<script async src=\"https://www.googletagmanager.com/gtag/js?id={$s->pixel_id}\"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            gtag('config', '{$s->pixel_id}');
        </script>";
    }
}