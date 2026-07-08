<!--Start of Tawk.to Script-->
<script type="text/javascript">
window.Tawk_API = window.Tawk_API || {};
window.Tawk_LoadStart = new Date();

(function () {
    function fireTawkEvent(name) {
        var event;

        if (typeof window.CustomEvent === 'function') {
            event = new CustomEvent(name);
        } else {
            event = document.createEvent('Event');
            event.initEvent(name, true, true);
        }

        document.dispatchEvent(event);
    }

    var previousOnLoad = window.Tawk_API.onLoad;

    window.Tawk_API.onLoad = function () {
        if (typeof previousOnLoad === 'function') {
            previousOnLoad();
        }

        if (typeof window.Tawk_API.showWidget === 'function') {
            window.Tawk_API.showWidget();
        }

        fireTawkEvent('tawk:ready');
    };

    if (document.getElementById('tawk-embed-script')) {
        return;
    }

    var script = document.createElement('script');
    var firstScript = document.getElementsByTagName('script')[0];

    script.id = 'tawk-embed-script';
    script.async = true;
    script.src = 'https://embed.tawk.to/6a37aea6b40d591d46abb94e/1jrko4f8h';
    script.charset = 'UTF-8';
    script.setAttribute('crossorigin', '*');
    script.onerror = function () {
        fireTawkEvent('tawk:error');
    };

    firstScript.parentNode.insertBefore(script, firstScript);
})();
</script>
<!--End of Tawk.to Script-->
