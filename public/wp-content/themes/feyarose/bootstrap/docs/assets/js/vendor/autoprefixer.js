!function (e) {
    if ("object" == typeof exports && "undefined" != typeof module)module.exports = e(); else if ("function" == typeof define && define.amd)define([], e); else {
        var f;
        "undefined" != typeof window ? f = window : "undefined" != typeof global ? f = global : "undefined" != typeof self && (f = self), f.autoprefixer = e()
    }
}(function () {
    var define, module, exports;
    return (function e(t, n, r) {
        function s(o, u) {
            if (!n[o]) {
                if (!t[o]) {
                    var a = typeof require == "function" && require;
                    if (!u && a)return a(o, !0);
                    if (i)return i(o, !0);
                    var f = new Error("Cannot find module '" + o + "'");
                    throw f.code = "MODULE_NOT_FOUND", f
                }
                var l = n[o] = {exports: {}};
                t[o][0].call(l.exports, function (e) {
                    var n = t[o][1][e];
                    return s(n ? n : e)
                }, l, l.exports, e, t, n, r)
            }
            return n[o].exports
        }

        var i = typeof require == "function" && require;
        for (var o = 0; o < r.length; o++)s(r[o]);
        return s
    })({
        1: [function (require, module, exports) {
            (function () {
                var Autoprefixer, Browsers, Prefixes, autoprefixer, infoCache, isPlainObject, postcss,
                    __slice = [].slice,
                    __bind = function (fn, me) {
                        return function () {
                            return fn.apply(me, arguments);
                        };
                    };

                postcss = require('postcss');

                Browsers = require('./browsers');

                Prefixes = require('./prefixes');

                infoCache = null;

                isPlainObject = function (obj) {
                    return Object.prototype.toString.apply(obj) === '[object Object]';
                };

                autoprefixer = function () {
                    var browsers, options, prefixes, reqs;
                    reqs = 1 <= arguments.length ? __slice.call(arguments, 0) : [];
                    if (reqs.length === 1 && isPlainObject(reqs[0])) {
                        options = reqs[0];
                        reqs = void 0;
                    } else if (reqs.length === 0 || (reqs.length === 1 && (reqs[0] == null))) {
                        reqs = void 0;
                    } else if (reqs.length <= 2 && (reqs[0] instanceof Array || (reqs[0] == null))) {
                        options = reqs[1];
                        reqs = reqs[0];
                    } else if (typeof reqs[reqs.length - 1] === 'object') {
                        options = reqs.pop();
                    }
                    if ((options != null ? options.browsers : void 0) != null) {
                        reqs = options.browsers;
                    } else if (reqs) {
                        if (typeof console !== "undefined" && console !== null) {
                            console.warn('autoprefixer: autoprefixer(browsers) is deprecated ' + 'and will be removed in 3.1. ' + 'Use autoprefixer({ browsers: browsers }).');
                        }
                    }
                    if (reqs == null) {
                        reqs = autoprefixer["default"];
                    }
                    browsers = new Browsers(autoprefixer.data.browsers, reqs);
                    prefixes = new Prefixes(autoprefixer.data.prefixes, browsers, options);
                    return new Autoprefixer(prefixes, autoprefixer.data, options);
                };

                autoprefixer.data = {
                    browsers: require('../data/browsers'),
                    prefixes: require('../data/prefixes')
                };

                Autoprefixer = (function () {
                    function Autoprefixer(prefixes, data, options) {
                        this.prefixes = prefixes;
                        this.data = data;
                        this.options = options != null ? options : {};
                        this.postcss = __bind(this.postcss, this);
                        this.browsers = this.prefixes.browsers.selected;
                    }

                    Autoprefixer.prototype.process = function (str, options) {
                        if (options == null) {
                            options = {};
                        }
                        return this.processor().process(str, options);
                    };

                    Autoprefixer.prototype.postcss = function (css) {
                        if (this.options.remove !== false) {
                            this.prefixes.processor.remove(css);
                        }
                        return this.prefixes.processor.add(css);
                    };

                    Autoprefixer.prototype.info = function () {
                        infoCache || (infoCache = require('./info'));
                        return infoCache(this.prefixes);
                    };

                    Autoprefixer.prototype.processor = function () {
                        return this.processorCache || (this.processorCache = postcss(this.postcss));
                    };

                    return Autoprefixer;

                })();

                autoprefixer["default"] = ['> 1%', 'last 2 versions', 'Firefox ESR', 'Opera 12.1'];

                autoprefixer.loadDefault = function () {
                    return this.defaultCache || (this.defaultCache = autoprefixer({
                            browsers: this["default"]
                        }));
                };

                autoprefixer.process = function (str, options) {
                    if (options == null) {
                        options = {};
                    }
                    return this.loadDefault().process(str, options);
                };

                autoprefixer.postcss = function (css) {
                    return autoprefixer.loadDefault().postcss(css);
                };

                autoprefixer.info = function () {
                    return this.loadDefault().info();
                };

                module.exports = autoprefixer;

            }).call(this);

        }, {
            "../data/browsers": 2,
            "../data/prefixes": 3,
            "./browsers": 4,
            "./info": 33,
            "./prefixes": 38,
            "postcss": 92
        }],
        2: [function (require, module, exports) {
            (function () {
                var convert, data, intervals, major, name, names, normalize, _ref;

                names = ['firefox', 'chrome', 'safari', 'ios_saf', 'opera', 'ie', 'bb', 'android'];

                major = ['firefox', 'chrome', 'safari', 'ios_saf', 'opera', 'android', 'ie', 'ie_mob'];

                normalize = function (array) {
                    return array.reverse().filter(function (i) {
                        return i;
                    });
                };

                intervals = function (array) {
                    var i, interval, result, splited, sub, _i, _len;
                    result = [];
                    for (_i = 0, _len = array.length; _i < _len; _i++) {
                        interval = array[_i];
                        splited = interval.split('-');
                        splited = splited.sort().reverse();
                        sub = (function () {
                            var _j, _len1, _results;
                            _results = [];
                            for (_j = 0, _len1 = splited.length; _j < _len1; _j++) {
                                i = splited[_j];
                                _results.push([i, interval, splited.length]);
                            }
                            return _results;
                        })();
                        result = result.concat(sub);
                    }
                    return result;
                };

                convert = function (name, data) {
                    var future, result, versions;
                    future = normalize(data.versions.slice(-3));
                    versions = intervals(normalize(data.versions.slice(0, -3)));
                    result = {};
                    result.prefix = name === 'opera' ? '-o-' : "-" + data.prefix + "-";
                    if (major.indexOf(name) === -1) {
                        result.minor = true;
                    }
                    if (future.length) {
                        result.future = future;
                    }
                    result.versions = versions.map(function (i) {
                        return i[0];
                    });
                    result.popularity = versions.map(function (i) {
                        return data.usage_global[i[1]] / i[2];
                    });
                    return result;
                };

                module.exports = {};

                _ref = require('caniuse-db/data').agents;
                for (name in _ref) {
                    data = _ref[name];
                    module.exports[name] = convert(name, data);
                }

            }).call(this);

        }, {"caniuse-db/data": 51}],
        3: [function (require, module, exports) {
            (function () {
                var browsers, feature, map, prefix, textDecoration,
                    __slice = [].slice;

                browsers = require('./browsers');

                feature = function (data, opts, callback) {
                    var browser, interval, match, need, sorted, support, version, versions, _i, _len, _ref, _ref1, _ref2;
                    if (!callback) {
                        _ref = [opts, {}], callback = _ref[0], opts = _ref[1];
                    }
                    match = opts.full ? /y\sx($|\s)/ : /\sx($|\s)/;
                    need = [];
                    _ref1 = data.stats;
                    for (browser in _ref1) {
                        versions = _ref1[browser];
                        for (interval in versions) {
                            support = versions[interval];
                            _ref2 = interval.split('-');
                            for (_i = 0, _len = _ref2.length; _i < _len; _i++) {
                                version = _ref2[_i];
                                if (browsers[browser] && support.match(match)) {
                                    version = version.replace(/\.0$/, '');
                                    need.push(browser + ' ' + version);
                                }
                            }
                        }
                    }
                    sorted = need.sort(function (a, b) {
                        a = a.split(' ');
                        b = b.split(' ');
                        if (a[0] > b[0]) {
                            return 1;
                        } else if (a[0] < b[0]) {
                            return -1;
                        } else {
                            return parseFloat(a[1]) - parseFloat(b[1]);
                        }
                    });
                    return callback(sorted);
                };

                map = function (browsers, callback) {
                    var browser, name, version, _i, _len, _ref, _results;
                    _results = [];
                    for (_i = 0, _len = browsers.length; _i < _len; _i++) {
                        browser = browsers[_i];
                        _ref = browser.split(' '), name = _ref[0], version = _ref[1];
                        version = parseFloat(version);
                        _results.push(callback(browser, name, version));
                    }
                    return _results;
                };

                prefix = function () {
                    var data, name, names, _i, _j, _len, _results;
                    names = 2 <= arguments.length ? __slice.call(arguments, 0, _i = arguments.length - 1) : (_i = 0, []), data = arguments[_i++];
                    _results = [];
                    for (_j = 0, _len = names.length; _j < _len; _j++) {
                        name = names[_j];
                        _results.push(module.exports[name] = data);
                    }
                    return _results;
                };

                module.exports = {};

                feature(require('caniuse-db/features-json/border-radius'), function (browsers) {
                    return prefix('border-radius', 'border-top-left-radius', 'border-top-right-radius', 'border-bottom-right-radius', 'border-bottom-left-radius', {
                        mistakes: ['-ms-'],
                        browsers: browsers,
                        transition: true
                    });
                });

                feature(require('caniuse-db/features-json/css-boxshadow'), function (browsers) {
                    return prefix('box-shadow', {
                        browsers: browsers,
                        transition: true
                    });
                });

                feature(require('caniuse-db/features-json/css-animation'), function (browsers) {
                    return prefix('animation', 'animation-name', 'animation-duration', 'animation-delay', 'animation-direction', 'animation-fill-mode', 'animation-iteration-count', 'animation-play-state', 'animation-timing-function', '@keyframes', {
                        browsers: browsers
                    });
                });

                feature(require('caniuse-db/features-json/css-transitions'), function (browsers) {
                    return prefix('transition', 'transition-property', 'transition-duration', 'transition-delay', 'transition-timing-function', {
                        mistakes: ['-ms-'],
                        browsers: browsers
                    });
                });

                feature(require('caniuse-db/features-json/transforms2d'), function (browsers) {
                    return prefix('transform', 'transform-origin', {
                        browsers: browsers,
                        transition: true
                    });
                });

                feature(require('caniuse-db/features-json/transforms3d'), function (browsers) {
                    prefix('perspective', 'perspective-origin', {
                        browsers: browsers,
                        transition: true
                    });
                    return prefix('transform-style', 'backface-visibility', {
                        browsers: browsers
                    });
                });

                feature(require('caniuse-db/features-json/css-gradients'), function (browsers) {
                    browsers = map(browsers, function (browser, name, version) {
                        if (name === 'android' && version < 4 || name === 'ios_saf' && version < 5 || name === 'safari' && version < 5.1) {
                            return browser + ' old';
                        } else {
                            return browser;
                        }
                    });
                    return prefix('linear-gradient', 'repeating-linear-gradient', 'radial-gradient', 'repeating-radial-gradient', {
                        props: ['background', 'background-image', 'border-image'],
                        mistakes: ['-ms-'],
                        browsers: browsers
                    });
                });

                feature(require('caniuse-db/features-json/css3-boxsizing'), function (browsers) {
                    return prefix('box-sizing', {
                        browsers: browsers
                    });
                });

                feature(require('caniuse-db/features-json/css-filters'), function (browsers) {
                    return prefix('filter', {
                        browsers: browsers,
                        transition: true
                    });
                });

                feature(require('caniuse-db/features-json/multicolumn'), function (browsers) {
                    prefix('columns', 'column-width', 'column-gap', 'column-rule', 'column-rule-color', 'column-rule-width', {
                        browsers: browsers,
                        transition: true
                    });
                    return prefix('column-count', 'column-rule-style', 'column-span', 'column-fill', 'break-before', 'break-after', 'break-inside', {
                        browsers: browsers
                    });
                });

                feature(require('caniuse-db/features-json/user-select-none'), function (browsers) {
                    return prefix('user-select', {
                        browsers: browsers
                    });
                });

                feature(require('caniuse-db/features-json/flexbox'), function (browsers) {
                    browsers = map(browsers, function (browser, name, version) {
                        if (name === 'safari' && version < 6.1) {
                            return browser + ' 2009';
                        } else if (name === 'ios_saf' && version < 7) {
                            return browser + ' 2009';
                        } else if (name === 'chrome' && version < 21) {
                            return browser + ' 2009';
                        } else if (name === 'android' && version < 4.4) {
                            return browser + ' 2009';
                        } else {
                            return browser;
                        }
                    });
                    prefix('display-flex', 'inline-flex', {
                        props: ['display'],
                        browsers: browsers
                    });
                    prefix('flex', 'flex-grow', 'flex-shrink', 'flex-basis', {
                        transition: true,
                        browsers: browsers
                    });
                    return prefix('flex-direction', 'flex-wrap', 'flex-flow', 'justify-content', 'order', 'align-items', 'align-self', 'align-content', {
                        browsers: browsers
                    });
                });

                feature(require('caniuse-db/features-json/calc'), function (browsers) {
                    return prefix('calc', {
                        props: ['*'],
                        browsers: browsers
                    });
                });

                feature(require('caniuse-db/features-json/background-img-opts'), function (browsers) {
                    return prefix('background-clip', 'background-origin', 'background-size', {
                        browsers: browsers
                    });
                });

                feature(require('caniuse-db/features-json/font-feature'), function (browsers) {
                    return prefix('font-feature-settings', 'font-variant-ligatures', 'font-language-override', 'font-kerning', {
                        browsers: browsers
                    });
                });

                feature(require('caniuse-db/features-json/border-image'), function (browsers) {
                    return prefix('border-image', {
                        browsers: browsers
                    });
                });

                feature(require('caniuse-db/features-json/css-selection'), function (browsers) {
                    return prefix('::selection', {
                        selector: true,
                        browsers: browsers
                    });
                });

                feature(require('caniuse-db/features-json/css-placeholder'), function (browsers) {
                    browsers = map(browsers, function (browser, name, version) {
                        if (name === 'firefox' && version <= 18) {
                            return browser + ' old';
                        } else {
                            return browser;
                        }
                    });
                    return prefix('::placeholder', {
                        selector: true,
                        browsers: browsers
                    });
                });

                feature(require('caniuse-db/features-json/css-hyphens'), function (browsers) {
                    return prefix('hyphens', {
                        browsers: browsers
                    });
                });

                feature(require('caniuse-db/features-json/fullscreen'), function (browsers) {
                    return prefix(':fullscreen', {
                        selector: true,
                        browsers: browsers
                    });
                });

                feature(require('caniuse-db/features-json/css3-tabsize'), function (browsers) {
                    return prefix('tab-size', {
                        browsers: browsers
                    });
                });

                feature(require('caniuse-db/features-json/intrinsic-width'), function (browsers) {
                    return prefix('max-content', 'min-content', 'fit-content', 'fill-available', {
                        props: ['width', 'min-width', 'max-width', 'height', 'min-height', 'max-height'],
                        browsers: browsers
                    });
                });

                feature(require('caniuse-db/features-json/css3-cursors-newer'), function (browsers) {
                    prefix('zoom-in', 'zoom-out', {
                        props: ['cursor'],
                        browsers: browsers.concat(['chrome 3'])
                    });
                    return prefix('grab', 'grabbing', {
                        props: ['cursor'],
                        browsers: browsers.concat(['firefox 24', 'firefox 25', 'firefox 26'])
                    });
                });

                feature(require('caniuse-db/features-json/css-sticky'), function (browsers) {
                    return prefix('sticky', {
                        props: ['position'],
                        browsers: browsers
                    });
                });

                feature(require('caniuse-db/features-json/pointer'), function (browsers) {
                    return prefix('touch-action', {
                        browsers: browsers
                    });
                });

                textDecoration = require('caniuse-db/features-json/text-decoration');

                feature(textDecoration, function (browsers) {
                    return prefix('text-decoration-style', {
                        browsers: browsers
                    });
                });

                feature(textDecoration, {
                    full: true
                }, function (browsers) {
                    return prefix('text-decoration-line', 'text-decoration-color', {
                        browsers: browsers
                    });
                });

                feature(require('caniuse-db/features-json/text-size-adjust'), function (browsers) {
                    return prefix('text-size-adjust', {
                        browsers: browsers
                    });
                });

                feature(require('caniuse-db/features-json/css-masks'), function (browsers) {
                    return prefix('clip-path', 'mask', 'mask-clip', 'mask-composite', 'mask-image', 'mask-origin', 'mask-position', 'mask-repeat', 'mask-size', {
                        browsers: browsers
                    });
                });

                feature(require('caniuse-db/features-json/css-boxdecorationbreak'), function (brwsrs) {
                    return prefix('box-decoration-break', {
                        browsers: brwsrs
                    });
                });

            }).call(this);

        }, {
            "./browsers": 2,
            "caniuse-db/features-json/background-img-opts": 52,
            "caniuse-db/features-json/border-image": 53,
            "caniuse-db/features-json/border-radius": 54,
            "caniuse-db/features-json/calc": 55,
            "caniuse-db/features-json/css-animation": 56,
            "caniuse-db/features-json/css-boxdecorationbreak": 57,
            "caniuse-db/features-json/css-boxshadow": 58,
            "caniuse-db/features-json/css-filters": 59,
            "caniuse-db/features-json/css-gradients": 60,
            "caniuse-db/features-json/css-hyphens": 61,
            "caniuse-db/features-json/css-masks": 62,
            "caniuse-db/features-json/css-placeholder": 63,
            "caniuse-db/features-json/css-selection": 64,
            "caniuse-db/features-json/css-sticky": 65,
            "caniuse-db/features-json/css-transitions": 66,
            "caniuse-db/features-json/css3-boxsizing": 67,
            "caniuse-db/features-json/css3-cursors-newer": 68,
            "caniuse-db/features-json/css3-tabsize": 69,
            "caniuse-db/features-json/flexbox": 70,
            "caniuse-db/features-json/font-feature": 71,
            "caniuse-db/features-json/fullscreen": 72,
            "caniuse-db/features-json/intrinsic-width": 73,
            "caniuse-db/features-json/multicolumn": 74,
            "caniuse-db/features-json/pointer": 75,
            "caniuse-db/features-json/text-decoration": 76,
            "caniuse-db/features-json/text-size-adjust": 77,
            "caniuse-db/features-json/transforms2d": 78,
            "caniuse-db/features-json/transforms3d": 79,
            "caniuse-db/features-json/user-select-none": 80
        }],
        4: [function (require, module, exports) {
            (function () {
                var Browsers, utils;

                utils = require('./utils');

                Browsers = (function () {
                    Browsers.prefixes = function () {
                        var data, i, name;
                        if (this.prefixesCache) {
                            return this.prefixesCache;
                        }
                        data = require('../data/browsers');
                        return this.prefixesCache = utils.uniq((function () {
                            var _results;
                            _results = [];
                            for (name in data) {
                                i = data[name];
                                _results.push(i.prefix);
                            }
                            return _results;
                        })()).sort(function (a, b) {
                            return b.length - a.length;
                        });
                    };

                    Browsers.withPrefix = function (value) {
                        if (!this.prefixesRegexp) {
                            this.prefixesRegexp = RegExp("" + (this.prefixes().join('|')));
                        }
                        return this.prefixesRegexp.test(value);
                    };

                    function Browsers(data, requirements) {
                        this.data = data;
                        this.selected = this.parse(requirements);
                    }

                    Browsers.prototype.parse = function (requirements) {
                        var selected;
                        if (!(requirements instanceof Array)) {
                            requirements = [requirements];
                        }
                        selected = [];
                        requirements.map((function (_this) {
                            return function (req) {
                                var i, match, name, _ref;
                                _ref = _this.requirements;
                                for (name in _ref) {
                                    i = _ref[name];
                                    if (match = req.match(i.regexp)) {
                                        selected = selected.concat(i.select.apply(_this, match.slice(1)));
                                        return;
                                    }
                                }
                                return utils.error("Unknown browser requirement `" + req + "`");
                            };
                        })(this));
                        return utils.uniq(selected);
                    };

                    Browsers.prototype.aliases = {
                        fx: 'firefox',
                        ff: 'firefox',
                        ios: 'ios_saf',
                        explorer: 'ie',
                        blackberry: 'bb',
                        explorermobile: 'ie_mob',
                        operamini: 'op_mini',
                        operamobile: 'op_mob',
                        chromeandroid: 'and_chr',
                        firefoxandroid: 'and_ff'
                    };

                    Browsers.prototype.requirements = {
                        none: {
                            regexp: /^none$/i,
                            select: function () {
                                if (typeof console !== "undefined" && console !== null) {
                                    console.warn("autoprefixer(\'none\') is deprecated and will be " + 'removed in 3.1. ' + 'Use autoprefixer({ browsers: [] })');
                                }
                                return [];
                            }
                        },
                        lastVersions: {
                            regexp: /^last (\d+) versions?$/i,
                            select: function (versions) {
                                return this.browsers(function (data) {
                                    if (data.minor) {
                                        return [];
                                    } else {
                                        return data.versions.slice(0, versions);
                                    }
                                });
                            }
                        },
                        lastByBrowser: {
                            regexp: /^last (\d+) (\w+) versions?$/i,
                            select: function (versions, browser) {
                                var data;
                                data = this.byName(browser);
                                return data.versions.slice(0, versions).map(function (v) {
                                    return "" + data.name + " " + v;
                                });
                            }
                        },
                        globalStatistics: {
                            regexp: /^> (\d+(\.\d+)?)%$/,
                            select: function (popularity) {
                                return this.browsers(function (data) {
                                    if (data.minor) {
                                        return [];
                                    } else {
                                        return data.versions.filter(function (version, i) {
                                            return data.popularity[i] > popularity;
                                        });
                                    }
                                });
                            }
                        },
                        newerThan: {
                            regexp: /^(\w+) (>=?)\s*([\d\.]+)/,
                            select: function (browser, sign, version) {
                                var data, filter;
                                data = this.byName(browser);
                                version = parseFloat(version);
                                if (sign === '>') {
                                    filter = function (v) {
                                        return v > version;
                                    };
                                } else if (sign === '>=') {
                                    filter = function (v) {
                                        return v >= version;
                                    };
                                }
                                return data.versions.filter(filter).map(function (v) {
                                    return "" + data.name + " " + v;
                                });
                            }
                        },
                        olderThan: {
                            regexp: /^(\w+) (<=?)\s*([\d\.]+)/,
                            select: function (browser, sign, version) {
                                var data, filter;
                                data = this.byName(browser);
                                version = parseFloat(version);
                                if (sign === '<') {
                                    filter = function (v) {
    