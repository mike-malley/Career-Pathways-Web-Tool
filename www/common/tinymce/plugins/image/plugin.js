! function() {
    "use strict";
    var i, e = tinymce.util.Tools.resolve("tinymce.PluginManager"),
        d = function(e) {
            return !1 !== e.settings.image_dimensions
        },
        l = function(e) {
            return !0 === e.settings.image_advtab
        },
        m = function(e) {
            return e.getParam("image_prepend_url", "")
        },
        n = function(e) {
            return e.getParam("image_class_list")
        },
        r = function(e) {
            return !1 !== e.settings.image_description
        },
        a = function(e) {
            return !0 === e.settings.image_title
        },
        o = function(e) {
            return !0 === e.settings.image_caption
        },
        u = function(e) {
            return e.getParam("image_list", !1)
        },
        c = function(e) {
            return e.getParam("images_upload_url", !1)
        },
        s = function(e) {
            return e.getParam("images_upload_handler", !1)
        },
        g = function(e) {
            return e.getParam("images_upload_url")
        },
        f = function(e) {
            return e.getParam("images_upload_handler")
        },
        p = function(e) {
            return e.getParam("images_upload_base_path")
        },
        h = function(e) {
            return e.getParam("images_upload_credentials")
        },
        v = "undefined" != typeof window ? window : Function("return this;")(),
        b = function(e, t) {
            return function(e, t) {
                for (var n = t !== undefined && null !== t ? t : v, r = 0; r < e.length && n !== undefined && null !== n; ++r) n = n[e[r]];
                return n
            }(e.split("."), t)
        },
        y = {
            getOrDie: function(e, t) {
                var n = b(e, t);
                if (n === undefined || null === n) throw e + " not available on this browser";
                return n
            }
        },
        x = tinymce.util.Tools.resolve("tinymce.util.Promise"),
        w = tinymce.util.Tools.resolve("tinymce.util.Tools"),
        C = tinymce.util.Tools.resolve("tinymce.util.XHR"),
        S = function(e, t) {
            return Math.max(parseInt(e, 10), parseInt(t, 10))
        },
        N = function(e, n) {
            var r = document.createElement("img");

            function t(e, t) {
                r.parentNode && r.parentNode.removeChild(r), n({
                    width: e,
                    height: t
                })
            }
            r.onload = function() {
                t(S(r.width, r.clientWidth), S(r.height, r.clientHeight))
            }, r.onerror = function() {
                t(0, 0)
            };
            var a = r.style;
            a.visibility = "hidden", a.position = "fixed", a.bottom = a.left = "0px", a.width = a.height = "auto", document.body.appendChild(r), r.src = e
        },
        _ = function(e, a, t) {
            return function n(e, r) {
                return r = r || [], w.each(e, function(e) {
                    var t = {
                        text: e.text || e.title
                    };
                    e.menu ? t.menu = n(e.menu) : (t.value = e.value, a(t)), r.push(t)
                }), r
            }(e, t || [])
        },
        T = function(e) {
            return e && (e = e.replace(/px$/, "")), e
        },
        A = function(e) {
            return 0 < e.length && /^[0-9]+$/.test(e) && (e += "px"), e
        },
        R = function(e) {
            if (e.margin) {
                var t = e.margin.split(" ");
                switch (t.length) {
                    case 1:
                        e["margin-top"] = e["margin-top"] || t[0], e["margin-right"] = e["margin-right"] || t[0], e["margin-bottom"] = e["margin-bottom"] || t[0], e["margin-left"] = e["margin-left"] || t[0];
                        break;
                    case 2:
                        e["margin-top"] = e["margin-top"] || t[0], e["margin-right"] = e["margin-right"] || t[1], e["margin-bottom"] = e["margin-bottom"] || t[0], e["margin-left"] = e["margin-left"] || t[1];
                        break;
                    case 3:
                        e["margin-top"] = e["margin-top"] || t[0], e["margin-right"] = e["margin-right"] || t[1], e["margin-bottom"] = e["margin-bottom"] || t[2], e["margin-left"] = e["margin-left"] || t[1];
                        break;
                    case 4:
                        e["margin-top"] = e["margin-top"] || t[0], e["margin-right"] = e["margin-right"] || t[1], e["margin-bottom"] = e["margin-bottom"] || t[2], e["margin-left"] = e["margin-left"] || t[3]
                }
                delete e.margin
            }
            return e
        },
        t = function(e, t) {
            var n = u(e);
            "string" == typeof n ? C.send({
                url: n,
                success: function(e) {
                    t(JSON.parse(e))
                }
            }) : "function" == typeof n ? n(t) : t(n)
        },
        I = function(e, t, n) {
            function r() {
                n.onload = n.onerror = null, e.selection && (e.selection.select(n), e.nodeChanged())
            }
            n.onload = function() {
                t.width || t.height || !d(e) || e.dom.setAttribs(n, {
                    width: n.clientWidth,
                    height: n.clientHeight
                }), r()
            }, n.onerror = r
        },
        O = function(r) {
            return new x(function(e, t) {
                var n = new(y.getOrDie("FileReader"));
                n.onload = function() {
                    e(n.result)
                }, n.onerror = function() {
                    t(n.error.message)
                }, n.readAsDataURL(r)
            })
        },
        L = tinymce.util.Tools.resolve("tinymce.dom.DOMUtils"),
        P = Object.prototype.hasOwnProperty,
        U = (i = function(e, t) {
            return t
        }, function() {
            for (var e = new Array(arguments.length), t = 0; t < e.length; t++) e[t] = arguments[t];
            if (0 === e.length) throw new Error("Can't merge zero objects");
            for (var n = {}, r = 0; r < e.length; r++) {
                var a = e[r];
                for (var o in a) P.call(a, o) && (n[o] = i(n[o], a[o]))
            }
            return n
        }),
        E = L.DOM,
        k = function(e) {
            return e.style.marginLeft && e.style.marginRight && e.style.marginLeft === e.style.marginRight ? T(e.style.marginLeft) : ""
        },
        M = function(e) {
            return e.style.marginTop && e.style.marginBottom && e.style.marginTop === e.style.marginBottom ? T(e.style.marginTop) : ""
        },
        D = function(e) {
            return e.style.borderWidth ? T(e.style.borderWidth) : ""
        },
        z = function(e, t) {
            return e.hasAttribute(t) ? e.getAttribute(t) : ""
        },
        B = function(e, t) {
            return e.style[t] ? e.style[t] : ""
        },
        H = function(e) {
            return null !== e.parentNode && "FIGURE" === e.parentNode.nodeName
        },
        j = function(e, t, n) {
            e.setAttribute(t, n)
        },
        F = function(e) {
            var t, n, r, a;
            H(e) ? (a = (r = e).parentNode, E.insertAfter(r, a), E.remove(a)) : (t = e, n = E.create("figure", {
                "class": "image"
            }), E.insertAfter(n, t), n.appendChild(t), n.appendChild(E.create("figcaption", {
                contentEditable: !0
            }, "Caption")), n.contentEditable = "false")
        },
        W = function(e, t) {
            var n = e.getAttribute("style"),
                r = t(null !== n ? n : "");
            0 < r.length ? (e.setAttribute("style", r), e.setAttribute("data-mce-style", r)) : e.removeAttribute("style")
        },
        J = function(e, r) {
            return function(e, t, n) {
                e.style[t] ? (e.style[t] = A(n), W(e, r)) : j(e, t, n)
            }
        },
        V = function(e, t) {
            return e.style[t] ? T(e.style[t]) : z(e, t)
        },
        G = function(e, t) {
            var n = A(t);
            e.style.marginLeft = n, e.style.marginRight = n
        },
        $ = function(e, t) {
            var n = A(t);
            e.style.marginTop = n, e.style.marginBottom = n
        },
        X = function(e, t) {
            var n = A(t);
            e.style.borderWidth = n
        },
        q = function(e, t) {
            e.style.borderStyle = t
        },
        K = function(e) {
            return "FIGURE" === e.nodeName
        },
        Q = function(e, t) {
            var n = document.createElement("img");
            return j(n, "style", t.style), (k(n) || "" !== t.hspace) && G(n, t.hspace), (M(n) || "" !== t.vspace) && $(n, t.vspace), (D(n) || "" !== t.border) && X(n, t.border), (B(n, "borderStyle") || "" !== t.borderStyle) && q(n, t.borderStyle), e(n.getAttribute("style"))
        },
        Y = function(e, t) {
            return {
                src: z(t, "src"),
                alt: z(t, "alt"),
                title: z(t, "title"),
                width: V(t, "width"),
                height: V(t, "height"),
                "class": z(t, "class"),
                style: e(z(t, "style")),
                caption: H(t),
                hspace: k(t),
                vspace: M(t),
                border: D(t),
                borderStyle: B(t, "borderStyle")
            }
        },
        Z = function(e, t, n, r, a) {
            n[r] !== t[r] && a(e, r, n[r])
        },
        ee = function(r, a) {
            return function(e, t, n) {
                r(e, n), W(e, a)
            }
        },
        te = function(e, t, n) {
            var r = Y(e, n);
            Z(n, r, t, "caption", function(e, t, n) {
                return F(e)
            }), Z(n, r, t, "src", j), Z(n, r, t, "alt", j), Z(n, r, t, "title", j), Z(n, r, t, "width", J(0, e)), Z(n, r, t, "height", J(0, e)), Z(n, r, t, "class", j), Z(n, r, t, "style", ee(function(e, t) {
                return j(e, "style", t)
            }, e)), Z(n, r, t, "hspace", ee(G, e)), Z(n, r, t, "vspace", ee($, e)), Z(n, r, t, "border", ee(X, e)), Z(n, r, t, "borderStyle", ee(q, e))
        },
        ne = function(e, t) {
            var n = e.dom.styles.parse(t),
                r = R(n),
                a = e.dom.styles.parse(e.dom.styles.serialize(r));
            return e.dom.styles.serialize(a)
        },
        re = function(e) {
            var t = e.selection.getNode(),
                n = e.dom.getParent(t, "figure.image");
            return n ? e.dom.select("img", n)[0] : t && ("IMG" !== t.nodeName || t.getAttribute("data-mce-object") || t.getAttribute("data-mce-placeholder")) ? null : t
        },
        ae = function(t, e) {
            var n = t.dom,
                r = n.getParent(e.parentNode, function(e) {
                    return t.schema.getTextBlockElements()[e.nodeName]
                }, t.getBody());
            return r ? n.split(r, e) : e
        },
        oe = function(t) {
            var e = re(t);
            return e ? Y(function(e) {
                return ne(t, e)
            }, e) : {
                src: "",
                alt: "",
                title: "",
                width: "",
                height: "",
                "class": "",
                style: "",
                caption: !1,
                hspace: "",
                vspace: "",
                border: "",
                borderStyle: ""
            }
        },
        ie = function(t, e) {
            var n = function(e, t) {
                var n = document.createElement("img");
                if (te(e, U(t, {
                        caption: !1
                    }), n), j(n, "alt", t.alt), t.caption) {
                    var r = E.create("figure", {
                        "class": "image"
                    });
                    return r.appendChild(n), r.appendChild(E.create("figcaption", {
                        contentEditable: !0
                    }, "Caption")), r.contentEditable = "false", r
                }
                return n
            }(function(e) {
                return ne(t, e)
            }, e);
            t.dom.setAttrib(n, "data-mce-id", "__mcenew"), t.focus(), t.selection.setContent(n.outerHTML);
            var r = t.dom.select('*[data-mce-id="__mcenew"]')[0];
            if (t.dom.setAttrib(r, "data-mce-id", null), K(r)) {
                var a = ae(t, r);
                t.selection.select(a)
            } else t.selection.select(r)
        },
        le = function(e, t) {
            var n = re(e);
            n ? t.src ? function(t, e) {
                var n, r = re(t);
                if (te(function(e) {
                        return ne(t, e)
                    }, e, r), n = r, t.dom.setAttrib(n, "src", n.getAttribute("src")), K(r.parentNode)) {
                    var a = r.parentNode;
                    ae(t, a), t.selection.select(r.parentNode)
                } else t.selection.select(r), I(t, e, r)
            }(e, t) : function(e, t) {
                if (t) {
                    var n = e.dom.is(t.parentNode, "figure.image") ? t.parentNode : t;
                    e.dom.remove(n), e.focus(), e.nodeChanged(), e.dom.isEmpty(e.getBody()) && (e.setContent(""), e.selection.setCursorLocation())
                }
            }(e, n) : t.src && ie(e, t)
        },
        ue = function(n, r) {
            r.find("#style").each(function(e) {
                var t = Q(function(e) {
                    return ne(n, e)
                }, U({
                    src: "",
                    alt: "",
                    title: "",
                    width: "",
                    height: "",
                    "class": "",
                    style: "",
                    caption: !1,
                    hspace: "",
                    vspace: "",
                    border: "",
                    borderStyle: ""
                }, r.toJSON()));
                e.value(t)
            })
        },
        ce = function(t) {
            return {
                title: "Advanced",
                type: "form",
                pack: "start",
                items: [{
                    label: "Style",
                    name: "style",
                    type: "textbox",
                    onchange: (o = t, function(e) {
                        var t = o.dom,
                            n = e.control.rootControl;
                        if (l(o)) {
                            var r = n.toJSON(),
                                a = t.parseStyle(r.style);
                            n.find("#vspace").value(""), n.find("#hspace").value(""), ((a = R(a))["margin-top"] && a["margin-bottom"] || a["margin-right"] && a["margin-left"]) && (a["margin-top"] === a["margin-bottom"] ? n.find("#vspace").value(T(a["margin-top"])) : n.find("#vspace").value(""), a["margin-right"] === a["margin-left"] ? n.find("#hspace").value(T(a["margin-right"])) : n.find("#hspace").value("")), a["border-width"] ? n.find("#border").value(T(a["border-width"])) : n.find("#border").value(""), a["border-style"] ? n.find("#borderStyle").value(a["border-style"]) : n.find("#borderStyle").value(""), n.find("#style").value(t.serializeStyle(t.parseStyle(t.serializeStyle(a))))
                        }
                    })
                }, {
                    type: "form",
                    layout: "grid",
                    packV: "start",
                    columns: 2,
                    padding: 0,
                    defaults: {
                        type: "textbox",
                        maxWidth: 50,
                        onchange: function(e) {
                            ue(t, e.control.rootControl)
                        }
                    },
                    items: [{
                        label: "Vertical space",
                        name: "vspace"
                    }, {
                        label: "Border width",
                        name: "border"
                    }, {
                        label: "Horizontal space",
                        name: "hspace"
                    }, {
                        label: "Border style",
                        type: "listbox",
                        name: "borderStyle",
                        width: 90,
                        maxWidth: 90,
                        onselect: function(e) {
                            ue(t, e.control.rootControl)
                        },
                        values: [{
                            text: "Select...",
                            value: ""
                        }, {
                            text: "Solid",
                            value: "solid"
                        }, {
                            text: "Dotted",
                            value: "dotted"
                        }, {
                            text: "Dashed",
                            value: "dashed"
                        }, {
                            text: "Double",
                            value: "double"
                        }, {
                            text: "Groove",
                            value: "groove"
                        }, {
                            text: "Ridge",
                            value: "ridge"
                        }, {
                            text: "Inset",
                            value: "inset"
                        }, {
                            text: "Outset",
                            value: "outset"
                        }, {
                            text: "None",
                            value: "none"
                        }, {
                            text: "Hidden",
                            value: "hidden"
                        }]
                    }]
                }]
            };
            var o
        },
        se = function(e, t) {
            e.state.set("oldVal", e.value()), t.state.set("oldVal", t.value())
        },
        de = function(e, t) {
            var n = e.find("#width")[0],
                r = e.find("#height")[0],
                a = e.find("#constrain")[0];
            n && r && a && t(n, r, a.checked())
        },
        me = function(e, t, n) {
            var r = e.state.get("oldVal"),
                a = t.state.get("oldVal"),
                o = e.value(),
                i = t.value();
            n && r && a && o && i && (o !== r ? (i = Math.round(o / r * i), isNaN(i) || t.value(i)) : (o = Math.round(i / a * o), isNaN(o) || e.value(o))), se(e, t)
        },
        ge = function(e) {
            de(e, me)
        },
        fe = function() {
            var e = function(e) {
                ge(e.control.rootControl)
            };
            return {
                type: "container",
                label: "Dimensions",
                layout: "flex",
                align: "center",
                spacing: 5,
                items: [{
                    name: "width",
                    type: "textbox",
                    maxLength: 5,
                    size: 5,
                    onchange: e,
                    ariaLabel: "Width"
                }, {
                    type: "label",
                    text: "x"
                }, {
                    name: "height",
                    type: "textbox",
                    maxLength: 5,
                    size: 5,
                    onchange: e,
                    ariaLabel: "Height"
                }, {
                    name: "constrain",
                    type: "checkbox",
                    checked: !0,
                    text: "Constrain proportions"
                }]
            }
        },
        pe = function(e) {
            de(e, se)
        },
        he = ge,
        ve = function(e) {
            e.meta = e.control.rootControl.toJSON()
        },
        be = function(s, e) {
            var t = [{
                name: "src",
                type: "filepicker",
                filetype: "image",
                label: "Source",
                autofocus: !0,
                onchange: function(e) {
                    var t, n, r, a, o, i, l, u, c;
                    n = s, i = (t = e).meta || {}, l = t.control, u = l.rootControl, (c = u.find("#image-list")[0]) && c.value(n.convertURL(l.value(), "src")), w.each(i, function(e, t) {
                        u.find("#" + t).value(e)
                    }), i.width || i.height || (r = n.convertURL(l.value(), "src"), a = m(n), o = new RegExp("^(?:[a-z]+:)?//", "i"), a && !o.test(r) && r.substring(0, a.length) !== a && (r = a + r), l.value(r), N(n.documentBaseURI.toAbsolute(l.value()), function(e) {
                        e.width && e.height && d(n) && (u.find("#width").value(e.width), u.find("#height").value(e.height), pe(u))
                    }))
                },
                onbeforecall: ve
            }, e];
            return r(s) && t.push({
                name: "alt",
                type: "textbox",
                label: "Image description"
            }), a(s) && t.push({
                name: "title",
                type: "textbox",
                label: "Image Title"
            }), d(s) && t.push(fe()), n(s) && t.push({
                name: "class",
                type: "listbox",
                label: "Class",
                values: _(n(s), function(e) {
                    e.value && (e.textStyle = function() {
                        return s.formatter.getCssText({
                            inline: "img",
                            classes: [e.value]
                        })
                    })
                })
            }), o(s) && t.push({
                name: "caption",
                type: "checkbox",
                label: "Caption"
            }), t
        },
        ye = function(e, t) {
            return {
                title: "General",
                type: "form",
                items: be(e, t)
            }
        },
        xe = be,
        we = function() {
            return y.getOrDie("URL")
        },
        Ce = function(e) {
            return we().createObjectURL(e)
        },
        Se = function(e) {
            we().revokeObjectURL(e)
        },
        Ne = tinymce.util.Tools.resolve("tinymce.ui.Factory"),
        _e = function() {};

    function Te(i) {
        var t = function(e, r, a, t) {
            var o, n;
            (o = new(y.getOrDie("XMLHttpRequest"))).open("POST", i.url), o.withCredentials = i.credentials, o.upload.onprogress = function(e) {
                t(e.loaded / e.total * 100)
            }, o.onerror = function() {
                a("Image upload failed due to a XHR Transport error. Code: " + o.status)
            }, o.onload = function() {
                var e, t, n;
                o.status < 200 || 300 <= o.status ? a("HTTP Error: " + o.status) : (e = JSON.parse(o.responseText)) && "string" == typeof e.location ? r((t = i.basePath, n = e.location, t ? t.replace(/\/$/, "") + "/" + n.replace(/^\//, "") : n)) : a("Invalid JSON: " + o.responseText)
            }, (n = new FormData).append("file", e.blob(), e.filename()), o.send(n)
        };
        return i = w.extend({
            credentials: !1,
            handler: t
        }, i), {
            upload: function(e) {
                return i.url || i.handler !== t ? (r = e, a = i.handler, new x(function(e, t) {
                    try {
                        a(r, e, t, _e)
                    } catch (n) {
                        t(n.message)
                    }
                })) : x.reject("Upload url missing from the settings.");
                var r, a
            }
        }
    }
    var Ae = function(u) {
            return function(e) {
                var t = Ne.get("Throbber"),
                    n = e.control.rootControl,
                    r = new t(n.getEl()),
                    a = e.control.value(),
                    o = Ce(a),
                    i = Te({
                        url: g(u),
                        basePath: p(u),
                        credentials: h(u),
                        handler: f(u)
                    }),
                    l = function() {
                        r.hide(), Se(o)
                    };
                return r.show(), O(a).then(function(e) {
                    var t = u.editorUpload.blobCache.create({
                        blob: a,
                        blobUri: o,
                        name: a.name ? a.name.replace(/\.[^\.]+$/, "") : null,
                        base64: e.split(",")[1]
                    });
                    return i.upload(t).then(function(e) {
                        var t = n.find("#src");
                        return t.value(e), n.find("tabpanel")[0].activateTab(0), t.fire("change"), l(), e
                    })
                })["catch"](function(e) {
                    u.windowManager.alert(e), l()
                })
            }
        },
        Re = ".jpg,.jpeg,.png,.gif",
        Ie = function(e) {
            return {
                title: "Upload",
                type: "form",
                layout: "flex",
                direction: "column",
                align: "stretch",
                padding: "20 20 20 20",
                items: [{
                    type: "container",
                    layout: "flex",
                    direction: "column",
                    align: "center",
                    spacing: 10,
                    items: [{
                        text: "Browse for an image",
                        type: "browsebutton",
                        accept: Re,
                        onchange: Ae(e)
                    }, {
                        text: "OR",
                        type: "label"
                    }]
                }, {
                    text: "Drop an image here",
                    type: "dropzone",
                    accept: Re,
                    height: 100,
                    onchange: Ae(e)
                }]
            }
        };

    function Oe(r) {
        for (var a = [], e = 1; e < arguments.length; e++) a[e - 1] = arguments[e];
        return function() {
            for (var e = [], t = 0; t < arguments.length; t++) e[t] = arguments[t];
            var n = a.concat(e);
            return r.apply(null, n)
        }
    }
    var Le = function(t, e) {
        var n = e.control.getRoot();
        he(n), t.undoManager.transact(function() {
            var e = U(oe(t), n.toJSON());
            le(t, e)
        }), t.editorUpload.uploadImagesAuto()
    };

    function Pe(o) {
        function e(e) {
            var n, t, r = oe(o);
            if (e && (t = {
                    type: "listbox",
                    label: "Image list",
                    name: "image-list",
                    values: _(e, function(e) {
                        e.value = o.convertURL(e.value || e.url, "src")
                    }, [{
                        text: "None",
                        value: ""
                    }]),
                    value: r.src && o.convertURL(r.src, "src"),
                    onselect: function(e) {
                        var t = n.find("#alt");
                        (!t.value() || e.lastControl && t.value() === e.lastControl.text()) && t.value(e.control.text()), n.find("#src").value(e.control.value()).fire("change")
                    },
                    onPostRender: function() {
                        t = this
                    }
                }), l(o) || c(o) || s(o)) {
                var a = [ye(o, t)];
                l(o) && a.push(ce(o)), (c(o) || s(o)) && a.push(Ie(o)), n = o.windowManager.open({
                    title: "Insert/edit image",
                    data: r,
                    bodyType: "tabpanel",
                    body: a,
                    onSubmit: Oe(Le, o)
                })
            } else n = o.windowManager.open({
                title: "Insert/edit image",
                data: r,
                body: xe(o, t),
                onSubmit: Oe(Le, o)
            });
            pe(n)
        }
        return {
            open: function() {
                t(o, e)
            }
        }
    }
    var Ue = function(e) {
            e.addCommand("mceImage", Pe(e).open)
        },
        Ee = function(o) {
            return function(e) {
                for (var t, n, r = e.length, a = function(e) {
                        e.attr("contenteditable", o ? "true" : null)
                    }; r--;) t = e[r], (n = t.attr("class")) && /\bimage\b/.test(n) && (t.attr("contenteditable", o ? "false" : null), w.each(t.getAll("figcaption"), a))
            }
        },
        ke = function(e) {
            e.on("preInit", function() {
                e.parser.addNodeFilter("figure", Ee(!0)), e.serializer.addNodeFilter("figure", Ee(!1))
            })
        },
        Me = function(e) {
            e.addButton("image", {
                icon: "image",
                tooltip: "Insert/edit image",
                onclick: Pe(e).open,
                stateSelector: "img:not([data-mce-object],[data-mce-placeholder]),figure.image"
            }), e.addMenuItem("image", {
                icon: "image",
                text: "Image",
                onclick: Pe(e).open,
                context: "insert",
                prependToContext: !0
            })
        };
    e.add("image", function(e) {
        ke(e), Me(e), Ue(e)
    })
}();