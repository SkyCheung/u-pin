function dolinaLoadJS(n, a, e) {
    if (a) e();
    else {
        var o = document.createElement("script");
        o.src = n,
        e = e ||
        function() {},
        o.onload = o.onreadystatechange = function() {
            this.readyState && "loaded" !== this.readyState && "complete" !== this.readyState || (e(), this.onload = this.onreadystatechange = null, this.parentNode.removeChild(this))
        },
        document.getElementsByTagName("script")[0].appendChild(o)
    }
} !
function() {
    console.log(8888);

    function n() {
        if (dolina = dolina.createNew(a), dolina.qiniuUrl = e, dolina.baseUrl = a + "core/", dolina.register(), dolina.initSpin(), dolina) {
            var n = {};
            dolinaParams && dolinaParams.options && dolinaParams.options.loadEventName ? dolina.reportEvent(dolinaParams.options.loadEventName, n) : dolina.reportEvent("load", n)
        }

        console.dir(dolina);

        window.onbeforeunload = function() {
            dolina.dolinaIM && dolina.dolinaIM.disconnect()
        },
        dolinaParams && (dolinaParams.dolina = dolina)
    }
    return false;
    var a = "https://sdk.engyne.net/",
    e = "https://cdn.engyne.net/";
    dolinaLoadJS(e + "SDK/js/dolina081702.js", window.dolinaIM,
    function() {
        n()
    }),
    dolinaLoadJS(e + "js/plupload.full.min.js", window.Qiniu,
    function() {
        dolinaLoadJS(e + "js/qiniu.min.js", window.Qiniu,
        function() {})
    });
    var o = document.getElementsByTagName("head")[0],
    i = document.createElement("link");
    i.rel = "stylesheet",
    i.type = "text/css",
    i.href = e + "SDK/js/css/msg.css",
    o.appendChild(i)
} ();