var $jscomp=$jscomp||{};$jscomp.scope={};$jscomp.findInternal=function(a,e,c){a instanceof String&&(a=String(a));for(var h=a.length,g=0;g<h;g++){var d=a[g];if(e.call(c,d,g,a))return{i:g,v:d}}return{i:-1,v:void 0}};$jscomp.ASSUME_ES5=!1;$jscomp.ASSUME_NO_NATIVE_MAP=!1;$jscomp.ASSUME_NO_NATIVE_SET=!1;$jscomp.SIMPLE_FROUND_POLYFILL=!1;
$jscomp.defineProperty=$jscomp.ASSUME_ES5||"function"==typeof Object.defineProperties?Object.defineProperty:function(a,e,c){a!=Array.prototype&&a!=Object.prototype&&(a[e]=c.value)};$jscomp.getGlobal=function(a){return"undefined"!=typeof window&&window===a?a:"undefined"!=typeof global&&null!=global?global:a};$jscomp.global=$jscomp.getGlobal(this);
$jscomp.polyfill=function(a,e,c,h){if(e){c=$jscomp.global;a=a.split(".");for(h=0;h<a.length-1;h++){var g=a[h];g in c||(c[g]={});c=c[g]}a=a[a.length-1];h=c[a];e=e(h);e!=h&&null!=e&&$jscomp.defineProperty(c,a,{configurable:!0,writable:!0,value:e})}};$jscomp.polyfill("Array.prototype.find",function(a){return a?a:function(a,c){return $jscomp.findInternal(this,a,c).v}},"es6","es3");
(function(){var a=[],e=0;window.generate_the_wcmp=function(c){function h(b,l){if(b+1<e||l){var f=b+1;!l||f!=e&&0!=d('[playernumber="'+f+'"]').closest("[data-loop]").length&&d('[playernumber="'+f+'"]').closest("[data-loop]")[0]==d('[playernumber="'+b+'"]').closest("[data-loop]")[0]||(f=d('[playernumber="'+b+'"]').closest("[data-loop]").find("[playernumber]:first").attr("playernumber"));a[f]instanceof d&&a[f].is("a")?a[f].is(":visible")?a[f].click():h(b+1,l):d(a[f].container).is(":visible")?a[f].play():
h(b+1,l)}}function g(b){var a=b.data("product"),f=d("img.product-"+a);f.length&&1==d('[data-product="'+a+'"]').length&&(a=f.offset(),b=b.closest("div.wcmp-player"),b.css({position:"absolute","z-index":999999}).offset({left:a.left+(f.width()-b.width())/2,top:a.top+(f.height()-b.height())/2}))}if(!("boolean"!==typeof c&&"undefined"!=typeof wcmp_global_settings&&1*wcmp_global_settings.onload)&&"undefined"===typeof generated_the_wcmp){generated_the_wcmp=!0;var d=jQuery;d(".wcmp-player-container").on("click",
"*",function(b){b.preventDefault();b.stopPropagation();return!1}).parent().removeAttr("title");d.expr[":"].regex=function(b,a,f){a=f[3].split(",");var c=/^(data|css):/;f=a[0].match(c)?a[0].split(":")[0]:"attr";c=a.shift().replace(c,"");return(new RegExp(a.join("").replace(/^\s+|\s+$/g,""),"ig")).test(d(b)[f](c))};var q="undefined"!=typeof wcmp_global_settings?wcmp_global_settings.play_all:!0,k="undefined"!=typeof wcmp_global_settings?!(1*wcmp_global_settings.play_simultaneously):!0,n="undefined"!=
typeof wcmp_global_settings?1*wcmp_global_settings.fade_out:!0,p="undefined"!=typeof wcmp_global_settings&&"ios_controls"in wcmp_global_settings&&1*wcmp_global_settings.ios_controls?!0:!1;c=d("audio.wcmp-player:not(.track):not([playernumber])");var r=d("audio.wcmp-player.track:not([playernumber])"),m={pauseOtherPlayers:k,iPadUseNativeControls:p,iPhoneUseNativeControls:p,success:function(b,c){var f=d(c).data("duration"),e=d(c).data("estimated_duration"),g=d(c).attr("playernumber");"undefined"!=typeof e&&
(b.getDuration=function(){return e});"undefined"!=typeof f&&setTimeout(function(b,c){return function(){a[b].updateDuration=function(){d(this.media).closest(".wcmp-player").find(".mejs-duration").html(c)};a[b].updateDuration()}}(g,f),50);d(c).attr("volume")&&(b.setVolume(parseFloat(d(c).attr("volume"))),0==b.volume&&b.setMuted(!0));b.addEventListener("timeupdate",function(a){a=b.getDuration();isNaN(b.currentTime)||isNaN(a)||(n&&4>a-b.currentTime?b.setVolume(b.volume-b.volume/3):b.currentTime&&("undefined"==
typeof b.bkVolume&&(b.bkVolume=parseFloat(d(b).find("audio,video").attr("volume")||b.volume)),b.setVolume(b.bkVolume),0==b.bkVolume&&b.setMuted(!0)))});b.addEventListener("volumechange",function(a){a=b.getDuration();isNaN(b.currentTime)||isNaN(a)||!(4<a-b.currentTime)&&n||!b.currentTime||(b.bkVolume=b.volume)});b.addEventListener("ended",function(a){a=d(b).closest('[data-loop="1"]');b.currentTime=0;if(1*q||a.length){var c=1*d(b).attr("playernumber");isNaN(c)&&(c=1*d(b).find("[playernumber]").attr("playernumber"));
h(c,a.length)}})}};k=".product-type-grouped :regex(name,quantity\\[\\d+\\])";c.each(function(){var b=d(this);b.find("source").attr("src");b.attr("playernumber",e);m.audioVolume="vertical";try{a[e]=new MediaElementPlayer(b[0],m)}catch(l){"console"in window&&console.log(l)}e++});r.each(function(){var b=d(this);b.find("source").attr("src");b.attr("playernumber",e);m.features=["playpause"];try{a[e]=new MediaElementPlayer(b[0],m)}catch(l){"console"in window&&console.log(l)}e++;g(b);d(window).resize(function(){g(b)})});
d(k).length||(k=".product-type-grouped [data-product_id]");d(k).length||(k=".woocommerce-grouped-product-list [data-product_id]");d(k).length||(k='.woocommerce-grouped-product-list [id*="product-"]');d(k).each(function(){try{var b=d(this),a=(b.data("product_id")||b.attr("name")||b.attr("id")).replace(/[^\d]/g,""),c=d(".wcmp-player-list.merge_in_grouped_products .product-"+a+":first .wcmp-player-title"),e=d("<table></table>");c.length&&!c.closest(".wcmp-first-in-product").length&&(c.closest("tr").addClass("wcmp-first-in-product"),
0==c.closest("form").length&&c.closest(".wcmp-player-list").prependTo(b.closest("form")),e.append(b.closest("tr").prepend("<td>"+c.html()+"</td>")),c.html("").append(e))}catch(t){}})}};window.wcmp_force_init=function(){delete window.generated_the_wcmp;generate_the_wcmp(!0)};jQuery(generate_the_wcmp);jQuery(window).on("load",function(){generate_the_wcmp(!0);var a=jQuery,e=window.navigator.userAgent;a("[data-lazyloading]").each(function(){var c=a(this);c.attr("preload",c.data("lazyloading"))});if(e.match(/iPad/i)||
e.match(/iPhone/i))if("undefined"!=typeof wcmp_global_settings?wcmp_global_settings.play_all:1)a(".wcmp-player .mejs-play button").one("click",function(){if("undefined"==typeof wcmp_preprocessed_players){wcmp_preprocessed_players=!0;var c=a(this);a(".wcmp-player audio").each(function(){this.play();this.pause()});setTimeout(function(){c.click()},500)}})}).on("popstate",function(){jQuery("audio[data-product]:not([playernumber])").length&&wcmp_force_init()});jQuery(document).on("scroll wpfAjaxSuccess woof_ajax_done yith-wcan-ajax-filtered wpf_ajax_success berocket_ajax_products_loaded berocket_ajax_products_infinite_loaded",
wcmp_force_init)})();