{{-- Stamp <html data-theme> before first paint (no FOUC). Include in <head>
     BEFORE the stylesheet. Reads the persisted choice, else the OS preference. --}}
<script>
    (function () {
        try {
            var stored = localStorage.getItem('nexo-theme');
            var mode = stored || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
            document.documentElement.setAttribute('data-theme', mode);
        } catch (e) { /* private mode: fall back to CSS prefers-color-scheme */ }
    })();
</script>
