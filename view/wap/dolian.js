(function (global, factory) {
    typeof exports === 'object' && typeof module !== 'undefined' ? module.exports = factory() :
        typeof define === 'function' && define.amd ? define(factory) :
            (global.dolina_i18n = factory());
}(this, function () { 'use strict';

    var _typeof = typeof Symbol === "function" && typeof Symbol.iterator === "symbol" ? function (obj) {
        return typeof obj;
    } : function (obj) {
        return obj && typeof Symbol === "function" && obj.constructor === Symbol ? "symbol" : typeof obj;
    };

    var classCallCheck = function (instance, Constructor) {
        if (!(instance instanceof Constructor)) {
            throw new TypeError("Cannot call a class as a function");
        }
    };

    var _extends = Object.assign || function (target) {
            for (var i = 1; i < arguments.length; i++) {
                var source = arguments[i];

                for (var key in source) {
                    if (Object.prototype.hasOwnProperty.call(source, key)) {
                        target[key] = source[key];
                    }
                }
            }

            return target;
        };

    var inherits = function (subClass, superClass) {
        if (typeof superClass !== "function" && superClass !== null) {
            throw new TypeError("Super expression must either be null or a function, not " + typeof superClass);
        }

        subClass.prototype = Object.create(superClass && superClass.prototype, {
            constructor: {
                value: subClass,
                enumerable: false,
                writable: true,
                configurable: true
            }
        });
        if (superClass) Object.setPrototypeOf ? Object.setPrototypeOf(subClass, superClass) : subClass.__proto__ = superClass;
    };

    var possibleConstructorReturn = function (self, call) {
        if (!self) {
            throw new ReferenceError("this hasn't been initialised - super() hasn't been called");
        }

        return call && (typeof call === "object" || typeof call === "function") ? call : self;
    };

    var slicedToArray = function () {
        function sliceIterator(arr, i) {
            var _arr = [];
            var _n = true;
            var _d = false;
            var _e = undefined;

            try {
                for (var _i = arr[Symbol.iterator](), _s; !(_n = (_s = _i.next()).done); _n = true) {
                    _arr.push(_s.value);

                    if (i && _arr.length === i) break;
                }
            } catch (err) {
                _d = true;
                _e = err;
            } finally {
                try {
                    if (!_n && _i["return"]) _i["return"]();
                } finally {
                    if (_d) throw _e;
                }
            }

            return _arr;
        }

        return function (arr, i) {
            if (Array.isArray(arr)) {
                return arr;
            } else if (Symbol.iterator in Object(arr)) {
                return sliceIterator(arr, i);
            } else {
                throw new TypeError("Invalid attempt to destructure non-iterable instance");
            }
        };
    }();

    var consoleLogger = {
        type: 'logger',

        log: function log(args) {
            this._output('log', args);
        },
        warn: function warn(args) {
            this._output('warn', args);
        },
        error: function error(args) {
            this._output('error', args);
        },
        _output: function _output(type, args) {
            if (console && console[type]) console[type].apply(console, Array.prototype.slice.call(args));
        }
    };

    var Logger = function () {
        function Logger(concreteLogger) {
            var options = arguments.length <= 1 || arguments[1] === undefined ? {} : arguments[1];
            classCallCheck(this, Logger);

            this.subs = [];
            this.init(concreteLogger, options);
        }

        Logger.prototype.init = function init(concreteLogger) {
            var options = arguments.length <= 1 || arguments[1] === undefined ? {} : arguments[1];

            this.prefix = options.prefix || 'i18next:';
            this.logger = concreteLogger || consoleLogger;
            this.options = options;
            this.debug = options.debug === false ? false : true;
        };

        Logger.prototype.setDebug = function setDebug(bool) {
            this.debug = bool;
            this.subs.forEach(function (sub) {
                sub.setDebug(bool);
            });
        };

        Logger.prototype.log = function log() {
            this.forward(arguments, 'log', '', true);
        };

        Logger.prototype.warn = function warn() {
            this.forward(arguments, 'warn', '', true);
        };

        Logger.prototype.error = function error() {
            this.forward(arguments, 'error', '');
        };

        Logger.prototype.deprecate = function deprecate() {
            this.forward(arguments, 'warn', 'WARNING DEPRECATED: ', true);
        };

        Logger.prototype.forward = function forward(args, lvl, prefix, debugOnly) {
            if (debugOnly && !this.debug) return;
            if (typeof args[0] === 'string') args[0] = prefix + this.prefix + ' ' + args[0];
            this.logger[lvl](args);
        };

        Logger.prototype.create = function create(moduleName) {
            var sub = new Logger(this.logger, _extends({ prefix: this.prefix + ':' + moduleName + ':' }, this.options));
            this.subs.push(sub);

            return sub;
        };

        // createInstance(options = {}) {
        //   return new Logger(options, callback);
        // }

        return Logger;
    }();

    ;

    var baseLogger = new Logger();

    var EventEmitter = function () {
        function EventEmitter() {
            classCallCheck(this, EventEmitter);

            this.observers = {};
        }

        EventEmitter.prototype.on = function on(events, listener) {
            var _this = this;

            events.split(' ').forEach(function (event) {
                _this.observers[event] = _this.observers[event] || [];
                _this.observers[event].push(listener);
            });
        };

        EventEmitter.prototype.off = function off(event, listener) {
            var _this2 = this;

            if (!this.observers[event]) {
                return;
            }

            this.observers[event].forEach(function () {
                if (!listener) {
                    delete _this2.observers[event];
                } else {
                    var index = _this2.observers[event].indexOf(listener);
                    if (index > -1) {
                        _this2.observers[event].splice(index, 1);
                    }
                }
            });
        };

        EventEmitter.prototype.emit = function emit(event) {
            for (var _len = arguments.length, args = Array(_len > 1 ? _len - 1 : 0), _key = 1; _key < _len; _key++) {
                args[_key - 1] = arguments[_key];
            }

            if (this.observers[event]) {
                this.observers[event].forEach(function (observer) {
                    observer.apply(undefined, args);
                });
            }

            if (this.observers['*']) {
                this.observers['*'].forEach(function (observer) {
                    var _ref;

                    observer.apply(observer, (_ref = [event]).concat.apply(_ref, args));
                });
            }
        };

        return EventEmitter;
    }();

    function makeString(object) {
        if (object == null) return '';
        return '' + object;
    }

    function copy(a, s, t) {
        a.forEach(function (m) {
            if (s[m]) t[m] = s[m];
        });
    }

    function getLastOfPath(object, path, Empty) {
        function cleanKey(key) {
            return key && key.indexOf('###') > -1 ? key.replace(/###/g, '.') : key;
        }

        var stack = typeof path !== 'string' ? [].concat(path) : path.split('.');
        while (stack.length > 1) {
            if (!object) return {};

            var key = cleanKey(stack.shift());
            if (!object[key] && Empty) object[key] = new Empty();
            object = object[key];
        }

        if (!object) return {};
        return {
            obj: object,
            k: cleanKey(stack.shift())
        };
    }

    function setPath(object, path, newValue) {
        var _getLastOfPath = getLastOfPath(object, path, Object);

        var obj = _getLastOfPath.obj;
        var k = _getLastOfPath.k;


        obj[k] = newValue;
    }

    function pushPath(object, path, newValue, concat) {
        var _getLastOfPath2 = getLastOfPath(object, path, Object);

        var obj = _getLastOfPath2.obj;
        var k = _getLastOfPath2.k;


        obj[k] = obj[k] || [];
        if (concat) obj[k] = obj[k].concat(newValue);
        if (!concat) obj[k].push(newValue);
    }

    function getPath(object, path) {
        var _getLastOfPath3 = getLastOfPath(object, path);

        var obj = _getLastOfPath3.obj;
        var k = _getLastOfPath3.k;


        if (!obj) return undefined;
        return obj[k];
    }

    function deepExtend(target, source, overwrite) {
        for (var prop in source) {
            if (prop in target) {
                // If we reached a leaf string in target or source then replace with source or skip depending on the 'overwrite' switch
                if (typeof target[prop] === 'string' || target[prop] instanceof String || typeof source[prop] === 'string' || source[prop] instanceof String) {
                    if (overwrite) target[prop] = source[prop];
                } else {
                    deepExtend(target[prop], source[prop], overwrite);
                }
            } else {
                target[prop] = source[prop];
            }
        }return target;
    }

    function regexEscape(str) {
        return str.replace(/[\-\[\]\/\{\}\(\)\*\+\?\.\\\^\$\|]/g, '\\$&');
    }

    /* eslint-disable */
    var _entityMap = {
        "&": "&amp;",
        "<": "&lt;",
        ">": "&gt;",
        '"': '&quot;',
        "'": '&#39;',
        "/": '&#x2F;'
    };
    /* eslint-enable */

    function escape(data) {
        if (typeof data === 'string') {
            return data.replace(/[&<>"'\/]/g, function (s) {
                return _entityMap[s];
            });
        } else {
            return data;
        }
    }

    var ResourceStore = function (_EventEmitter) {
        inherits(ResourceStore, _EventEmitter);

        function ResourceStore() {
            var data = arguments.length <= 0 || arguments[0] === undefined ? {} : arguments[0];
            var options = arguments.length <= 1 || arguments[1] === undefined ? { ns: ['translation'], defaultNS: 'translation' } : arguments[1];
            classCallCheck(this, ResourceStore);

            var _this = possibleConstructorReturn(this, _EventEmitter.call(this));

            _this.data = data;
            _this.options = options;
            return _this;
        }

        ResourceStore.prototype.addNamespaces = function addNamespaces(ns) {
            if (this.options.ns.indexOf(ns) < 0) {
                this.options.ns.push(ns);
            }
        };

        ResourceStore.prototype.removeNamespaces = function removeNamespaces(ns) {
            var index = this.options.ns.indexOf(ns);
            if (index > -1) {
                this.options.ns.splice(index, 1);
            }
        };

        ResourceStore.prototype.getResource = function getResource(lng, ns, key) {
            var options = arguments.length <= 3 || arguments[3] === undefined ? {} : arguments[3];

            var keySeparator = options.keySeparator || this.options.keySeparator;
            if (keySeparator === undefined) keySeparator = '.';

            var path = [lng, ns];
            if (key && typeof key !== 'string') path = path.concat(key);
            if (key && typeof key === 'string') path = path.concat(keySeparator ? key.split(keySeparator) : key);

            if (lng.indexOf('.') > -1) {
                path = lng.split('.');
            }

            return getPath(this.data, path);
        };

        ResourceStore.prototype.addResource = function addResource(lng, ns, key, value) {
            var options = arguments.length <= 4 || arguments[4] === undefined ? { silent: false } : arguments[4];

            var keySeparator = this.options.keySeparator;
            if (keySeparator === undefined) keySeparator = '.';

            var path = [lng, ns];
            if (key) path = path.concat(keySeparator ? key.split(keySeparator) : key);

            if (lng.indexOf('.') > -1) {
                path = lng.split('.');
                value = ns;
                ns = path[1];
            }

            this.addNamespaces(ns);

            setPath(this.data, path, value);

            if (!options.silent) this.emit('added', lng, ns, key, value);
        };

        ResourceStore.prototype.addResources = function addResources(lng, ns, resources) {
            for (var m in resources) {
                if (typeof resources[m] === 'string') this.addResource(lng, ns, m, resources[m], { silent: true });
            }
            this.emit('added', lng, ns, resources);
        };

        ResourceStore.prototype.addResourceBundle = function addResourceBundle(lng, ns, resources, deep, overwrite) {
            var path = [lng, ns];
            if (lng.indexOf('.') > -1) {
                path = lng.split('.');
                deep = resources;
                resources = ns;
                ns = path[1];
            }

            this.addNamespaces(ns);

            var pack = getPath(this.data, path) || {};

            if (deep) {
                deepExtend(pack, resources, overwrite);
            } else {
                pack = _extends({}, pack, resources);
            }

            setPath(this.data, path, pack);

            this.emit('added', lng, ns, resources);
        };

        ResourceStore.prototype.removeResourceBundle = function removeResourceBundle(lng, ns) {
            if (this.hasResourceBundle(lng, ns)) {
                delete this.data[lng][ns];
            }
            this.removeNamespaces(ns);

            this.emit('removed', lng, ns);
        };

        ResourceStore.prototype.hasResourceBundle = function hasResourceBundle(lng, ns) {
            return this.getResource(lng, ns) !== undefined;
        };

        ResourceStore.prototype.getResourceBundle = function getResourceBundle(lng, ns) {
            if (!ns) ns = this.options.defaultNS;

            // TODO: COMPATIBILITY remove extend in v2.1.0
            if (this.options.compatibilityAPI === 'v1') return _extends({}, this.getResource(lng, ns));

            return this.getResource(lng, ns);
        };

        ResourceStore.prototype.toJSON = function toJSON() {
            return this.data;
        };

        return ResourceStore;
    }(EventEmitter);

    var postProcessor = {

        processors: {},

        addPostProcessor: function addPostProcessor(module) {
            this.processors[module.name] = module;
        },
        handle: function handle(processors, value, key, options, translator) {
            var _this = this;

            processors.forEach(function (processor) {
                if (_this.processors[processor]) value = _this.processors[processor].process(value, key, options, translator);
            });

            return value;
        }
    };

    function convertInterpolation(options) {

        options.interpolation = {
            unescapeSuffix: 'HTML'
        };

        options.interpolation.prefix = options.interpolationPrefix || '__';
        options.interpolation.suffix = options.interpolationSuffix || '__';
        options.interpolation.escapeValue = options.escapeInterpolation || false;

        options.interpolation.nestingPrefix = options.reusePrefix || '$t(';
        options.interpolation.nestingSuffix = options.reuseSuffix || ')';

        return options;
    }

    function convertAPIOptions(options) {
        if (options.resStore) options.resources = options.resStore;

        if (options.ns && options.ns.defaultNs) {
            options.defaultNS = options.ns.defaultNs;
            options.ns = options.ns.namespaces;
        } else {
            options.defaultNS = options.ns || 'translation';
        }

        if (options.fallbackToDefaultNS && options.defaultNS) options.fallbackNS = options.defaultNS;

        options.saveMissing = options.sendMissing;
        options.saveMissingTo = options.sendMissingTo || 'current';
        options.returnNull = options.fallbackOnNull ? false : true;
        options.returnEmptyString = options.fallbackOnEmpty ? false : true;
        options.returnObjects = options.returnObjectTrees;
        options.joinArrays = '\n';

        options.returnedObjectHandler = options.objectTreeKeyHandler;
        options.parseMissingKeyHandler = options.parseMissingKey;
        options.appendNamespaceToMissingKey = true;

        options.nsSeparator = options.nsseparator;
        options.keySeparator = options.keyseparator;

        if (options.shortcutFunction === 'sprintf') {
            options.overloadTranslationOptionHandler = function (args) {
                var values = [];

                for (var i = 1; i < args.length; i++) {
                    values.push(args[i]);
                }

                return {
                    postProcess: 'sprintf',
                    sprintf: values
                };
            };
        }

        options.whitelist = options.lngWhitelist;
        options.preload = options.preload;
        if (options.load === 'current') options.load = 'currentOnly';
        if (options.load === 'unspecific') options.load = 'languageOnly';

        // backend
        options.backend = options.backend || {};
        options.backend.loadPath = options.resGetPath || 'locales/__lng__/__ns__.json';
        options.backend.addPath = options.resPostPath || 'locales/add/__lng__/__ns__';
        options.backend.allowMultiLoading = options.dynamicLoad;

        // cache
        options.cache = options.cache || {};
        options.cache.prefix = 'res_';
        options.cache.expirationTime = 7 * 24 * 60 * 60 * 1000;
        options.cache.enabled = options.useLocalStorage ? true : false;

        options = convertInterpolation(options);
        if (options.defaultVariables) options.interpolation.defaultVariables = options.defaultVariables;

        // TODO: deprecation
        // if (options.getAsync === false) throw deprecation error

        return options;
    }

    function convertJSONOptions(options) {
        options = convertInterpolation(options);
        options.joinArrays = '\n';

        return options;
    }

    function convertTOptions(options) {
        if (options.interpolationPrefix || options.interpolationSuffix || options.escapeInterpolation) {
            options = convertInterpolation(options);
        }

        options.nsSeparator = options.nsseparator;
        options.keySeparator = options.keyseparator;

        options.returnObjects = options.returnObjectTrees;

        return options;
    }

    function appendBackwardsAPI(i18n) {
        i18n.lng = function () {
            baseLogger.deprecate('i18next.lng() can be replaced by i18next.language for detected language or i18next.languages for languages ordered by translation lookup.');
            return i18n.services.languageUtils.toResolveHierarchy(i18n.language)[0];
        };

        i18n.preload = function (lngs, cb) {
            baseLogger.deprecate('i18next.preload() can be replaced with i18next.loadLanguages()');
            i18n.loadLanguages(lngs, cb);
        };

        i18n.setLng = function (lng, options, callback) {
            baseLogger.deprecate('i18next.setLng() can be replaced with i18next.changeLanguage() or i18next.getFixedT() to get a translation function with fixed language or namespace.');
            if (typeof options === 'function') {
                callback = options;
                options = {};
            }
            if (!options) options = {};

            if (options.fixLng === true) {
                if (callback) return callback(null, i18n.getFixedT(lng));
            }

            i18n.changeLanguage(lng, callback);
        };

        i18n.addPostProcessor = function (name, fc) {
            baseLogger.deprecate('i18next.addPostProcessor() can be replaced by i18next.use({ type: \'postProcessor\', name: \'name\', process: fc })');
            i18n.use({
                type: 'postProcessor',
                name: name,
                process: fc
            });
        };
    }

    var Translator = function (_EventEmitter) {
        inherits(Translator, _EventEmitter);

        function Translator(services) {
            var options = arguments.length <= 1 || arguments[1] === undefined ? {} : arguments[1];
            classCallCheck(this, Translator);

            var _this = possibleConstructorReturn(this, _EventEmitter.call(this));

            copy(['resourceStore', 'languageUtils', 'pluralResolver', 'interpolator', 'backendConnector'], services, _this);

            _this.options = options;
            _this.logger = baseLogger.create('translator');
            return _this;
        }

        Translator.prototype.changeLanguage = function changeLanguage(lng) {
            if (lng) this.language = lng;
        };

        Translator.prototype.exists = function exists(key) {
            var options = arguments.length <= 1 || arguments[1] === undefined ? { interpolation: {} } : arguments[1];

            if (this.options.compatibilityAPI === 'v1') {
                options = convertTOptions(options);
            }

            return this.resolve(key, options) !== undefined;
        };

        Translator.prototype.extractFromKey = function extractFromKey(key, options) {
            var nsSeparator = options.nsSeparator || this.options.nsSeparator;
            if (nsSeparator === undefined) nsSeparator = ':';

            var namespaces = options.ns || this.options.defaultNS;
            if (nsSeparator && key.indexOf(nsSeparator) > -1) {
                var parts = key.split(nsSeparator);
                namespaces = parts[0];
                key = parts[1];
            }
            if (typeof namespaces === 'string') namespaces = [namespaces];

            return {
                key: key,
                namespaces: namespaces
            };
        };

        Translator.prototype.translate = function translate(keys) {
            var options = arguments.length <= 1 || arguments[1] === undefined ? {} : arguments[1];

            if ((typeof options === 'undefined' ? 'undefined' : _typeof(options)) !== 'object') {
                options = this.options.overloadTranslationOptionHandler(arguments);
            } else if (this.options.compatibilityAPI === 'v1') {
                options = convertTOptions(options);
            }

            // non valid keys handling
            if (keys === undefined || keys === null || keys === '') return '';
            if (typeof keys === 'number') keys = String(keys);
            if (typeof keys === 'string') keys = [keys];

            // return key on CIMode
            var lng = options.lng || this.language;
            if (lng && lng.toLowerCase() === 'cimode') return keys[keys.length - 1];

            // separators
            var keySeparator = options.keySeparator || this.options.keySeparator || '.';

            // get namespace(s)

            var _extractFromKey = this.extractFromKey(keys[keys.length - 1], options);

            var key = _extractFromKey.key;
            var namespaces = _extractFromKey.namespaces;

            var namespace = namespaces[namespaces.length - 1];

            // resolve from store
            var res = this.resolve(keys, options);

            var resType = Object.prototype.toString.apply(res);
            var noObject = ['[object Number]', '[object Function]', '[object RegExp]'];
            var joinArrays = options.joinArrays !== undefined ? options.joinArrays : this.options.joinArrays;

            // object
            if (res && typeof res !== 'string' && noObject.indexOf(resType) < 0 && !(joinArrays && resType === '[object Array]')) {
                if (!options.returnObjects && !this.options.returnObjects) {
                    this.logger.warn('accessing an object - but returnObjects options is not enabled!');
                    return this.options.returnedObjectHandler ? this.options.returnedObjectHandler(key, res, options) : 'key \'' + key + ' (' + this.language + ')\' returned an object instead of string.';
                }

                var copy = resType === '[object Array]' ? [] : {}; // apply child translation on a copy

                for (var m in res) {
                    copy[m] = this.translate('' + key + keySeparator + m, _extends({ joinArrays: false, ns: namespaces }, options));
                }
                res = copy;
            }
            // array special treatment
            else if (joinArrays && resType === '[object Array]') {
                res = res.join(joinArrays);
                if (res) res = this.extendTranslation(res, key, options);
            }
            // string, empty or null
            else {
                var usedDefault = false,
                    usedKey = false;

                // fallback value
                if (!this.isValidLookup(res) && options.defaultValue !== undefined) {
                    usedDefault = true;
                    res = options.defaultValue;
                }
                if (!this.isValidLookup(res)) {
                    usedKey = true;
                    res = key;
                }

                // save missing
                if (usedKey || usedDefault) {
                    this.logger.log('missingKey', lng, namespace, key, res);

                    var lngs = [];
                    if (this.options.saveMissingTo === 'fallback' && this.options.fallbackLng && this.options.fallbackLng[0]) {
                        for (var i = 0; i < this.options.fallbackLng.length; i++) {
                            lngs.push(this.options.fallbackLng[i]);
                        }
                    } else if (this.options.saveMissingTo === 'all') {
                        lngs = this.languageUtils.toResolveHierarchy(options.lng || this.language);
                    } else {
                        //(this.options.saveMissingTo === 'current' || (this.options.saveMissingTo === 'fallback' && this.options.fallbackLng[0] === false) ) {
                        lngs.push(options.lng || this.language);
                    }

                    if (this.options.saveMissing) {
                        if (this.options.missingKeyHandler) {
                            this.options.missingKeyHandler(lngs, namespace, key, res);
                        } else if (this.backendConnector && this.backendConnector.saveMissing) {
                            this.backendConnector.saveMissing(lngs, namespace, key, res);
                        }
                    }

                    this.emit('missingKey', lngs, namespace, key, res);
                }

                // extend
                res = this.extendTranslation(res, key, options);

                // append namespace if still key
                if (usedKey && res === key && this.options.appendNamespaceToMissingKey) res = namespace + ':' + key;

                // parseMissingKeyHandler
                if (usedKey && this.options.parseMissingKeyHandler) res = this.options.parseMissingKeyHandler(res);
            }

            // return
            return res;
        };

        Translator.prototype.extendTranslation = function extendTranslation(res, key, options) {
            var _this2 = this;

            if (options.interpolation) this.interpolator.init(options);

            // interpolate
            var data = options.replace && typeof options.replace !== 'string' ? options.replace : options;
            if (this.options.interpolation.defaultVariables) data = _extends({}, this.options.interpolation.defaultVariables, data);
            res = this.interpolator.interpolate(res, data);

            // nesting
            res = this.interpolator.nest(res, function () {
                for (var _len = arguments.length, args = Array(_len), _key = 0; _key < _len; _key++) {
                    args[_key] = arguments[_key];
                }

                return _this2.translate.apply(_this2, args);
            }, options);

            if (options.interpolation) this.interpolator.reset();

            // post process
            var postProcess = options.postProcess || this.options.postProcess;
            var postProcessorNames = typeof postProcess === 'string' ? [postProcess] : postProcess;

            if (res !== undefined && postProcessorNames && postProcessorNames.length && options.applyPostProcessor !== false) {
                res = postProcessor.handle(postProcessorNames, res, key, options, this);
            }

            return res;
        };

        Translator.prototype.resolve = function resolve(keys) {
            var _this3 = this;

            var options = arguments.length <= 1 || arguments[1] === undefined ? {} : arguments[1];

            var found = void 0;

            if (typeof keys === 'string') keys = [keys];

            // forEach possible key
            keys.forEach(function (k) {
                if (_this3.isValidLookup(found)) return;

                var _extractFromKey2 = _this3.extractFromKey(k, options);

                var key = _extractFromKey2.key;
                var namespaces = _extractFromKey2.namespaces;

                if (_this3.options.fallbackNS) namespaces = namespaces.concat(_this3.options.fallbackNS);

                var needsPluralHandling = options.count !== undefined && typeof options.count !== 'string';
                var needsContextHandling = options.context !== undefined && typeof options.context === 'string' && options.context !== '';

                var codes = options.lngs ? options.lngs : _this3.languageUtils.toResolveHierarchy(options.lng || _this3.language);

                namespaces.forEach(function (ns) {
                    if (_this3.isValidLookup(found)) return;

                    codes.forEach(function (code) {
                        if (_this3.isValidLookup(found)) return;

                        var finalKey = key;
                        var finalKeys = [finalKey];

                        var pluralSuffix = void 0;
                        if (needsPluralHandling) pluralSuffix = _this3.pluralResolver.getSuffix(code, options.count);

                        // fallback for plural if context not found
                        if (needsPluralHandling && needsContextHandling) finalKeys.push(finalKey + pluralSuffix);

                        // get key for context if needed
                        if (needsContextHandling) finalKeys.push(finalKey += '' + _this3.options.contextSeparator + options.context);

                        // get key for plural if needed
                        if (needsPluralHandling) finalKeys.push(finalKey += pluralSuffix);

                        // iterate over finalKeys starting with most specific pluralkey (-> contextkey only) -> singularkey only
                        var possibleKey = void 0;
                        while (possibleKey = finalKeys.pop()) {
                            if (_this3.isValidLookup(found)) continue;
                            found = _this3.getResource(code, ns, possibleKey, options);
                        }
                    });
                });
            });

            return found;
        };

        Translator.prototype.isValidLookup = function isValidLookup(res) {
            return res !== undefined && !(!this.options.returnNull && res === null) && !(!this.options.returnEmptyString && res === '');
        };

        Translator.prototype.getResource = function getResource(code, ns, key) {
            var options = arguments.length <= 3 || arguments[3] === undefined ? {} : arguments[3];

            return this.resourceStore.getResource(code, ns, key, options);
        };

        return Translator;
    }(EventEmitter);

    function capitalize(string) {
        return string.charAt(0).toUpperCase() + string.slice(1);
    }

    var LanguageUtil = function () {
        function LanguageUtil(options) {
            classCallCheck(this, LanguageUtil);

            this.options = options;

            this.whitelist = this.options.whitelist || false;
            this.logger = baseLogger.create('languageUtils');
        }

        LanguageUtil.prototype.getLanguagePartFromCode = function getLanguagePartFromCode(code) {
            if (code.indexOf('-') < 0) return code;

            var specialCases = ['NB-NO', 'NN-NO', 'nb-NO', 'nn-NO', 'nb-no', 'nn-no'];
            var p = code.split('-');
            return this.formatLanguageCode(specialCases.indexOf(code) > -1 ? p[1].toLowerCase() : p[0]);
        };

        LanguageUtil.prototype.formatLanguageCode = function formatLanguageCode(code) {
            // http://www.iana.org/assignments/language-tags/language-tags.xhtml
            if (typeof code === 'string' && code.indexOf('-') > -1) {
                var specialCases = ['hans', 'hant', 'latn', 'cyrl', 'cans', 'mong', 'arab'];
                var p = code.split('-');

                if (this.options.lowerCaseLng) {
                    p = p.map(function (part) {
                        return part.toLowerCase();
                    });
                } else if (p.length === 2) {
                    p[0] = p[0].toLowerCase();
                    p[1] = p[1].toUpperCase();

                    if (specialCases.indexOf(p[1].toLowerCase()) > -1) p[1] = capitalize(p[1].toLowerCase());
                } else if (p.length === 3) {
                    p[0] = p[0].toLowerCase();

                    // if lenght 2 guess it's a country
                    if (p[1].length === 2) p[1] = p[1].toUpperCase();
                    if (p[0] !== 'sgn' && p[2].length === 2) p[2] = p[2].toUpperCase();

                    if (specialCases.indexOf(p[1].toLowerCase()) > -1) p[1] = capitalize(p[1].toLowerCase());
                    if (specialCases.indexOf(p[2].toLowerCase()) > -1) p[2] = capitalize(p[2].toLowerCase());
                }

                return p.join('-');
            } else {
                return this.options.cleanCode || this.options.lowerCaseLng ? code.toLowerCase() : code;
            }
        };

        LanguageUtil.prototype.isWhitelisted = function isWhitelisted(code, exactMatch) {
            if (this.options.load === 'languageOnly' || this.options.nonExplicitWhitelist && !exactMatch) {
                code = this.getLanguagePartFromCode(code);
            }
            return !this.whitelist || !this.whitelist.length || this.whitelist.indexOf(code) > -1 ? true : false;
        };

        LanguageUtil.prototype.toResolveHierarchy = function toResolveHierarchy(code, fallbackCode) {
            var _this = this;

            fallbackCode = fallbackCode || this.options.fallbackLng || [];
            if (typeof fallbackCode === 'string') fallbackCode = [fallbackCode];

            var codes = [];
            var addCode = function addCode(code) {
                var exactMatch = arguments.length <= 1 || arguments[1] === undefined ? false : arguments[1];

                if (_this.isWhitelisted(code, exactMatch)) {
                    codes.push(code);
                } else {
                    _this.logger.warn('rejecting non-whitelisted language code: ' + code);
                }
            };

            if (typeof code === 'string' && code.indexOf('-') > -1) {
                if (this.options.load !== 'languageOnly') addCode(this.formatLanguageCode(code), true);
                if (this.options.load !== 'currentOnly') addCode(this.getLanguagePartFromCode(code));
            } else if (typeof code === 'string') {
                addCode(this.formatLanguageCode(code));
            }

            fallbackCode.forEach(function (fc) {
                if (codes.indexOf(fc) < 0) addCode(_this.formatLanguageCode(fc));
            });

            return codes;
        };

        return LanguageUtil;
    }();

    ;

    // definition http://translate.sourceforge.net/wiki/l10n/pluralforms
    /* eslint-disable */
    var sets = [{ lngs: ['ach', 'ak', 'am', 'arn', 'br', 'fil', 'gun', 'ln', 'mfe', 'mg', 'mi', 'oc', 'tg', 'ti', 'tr', 'uz', 'wa'], nr: [1, 2], fc: 1 }, { lngs: ['af', 'an', 'ast', 'az', 'bg', 'bn', 'ca', 'da', 'de', 'dev', 'el', 'en', 'eo', 'es', 'es_ar', 'et', 'eu', 'fi', 'fo', 'fur', 'fy', 'gl', 'gu', 'ha', 'he', 'hi', 'hu', 'hy', 'ia', 'it', 'kn', 'ku', 'lb', 'mai', 'ml', 'mn', 'mr', 'nah', 'nap', 'nb', 'ne', 'nl', 'nn', 'no', 'nso', 'pa', 'pap', 'pms', 'ps', 'pt', 'pt_br', 'rm', 'sco', 'se', 'si', 'so', 'son', 'sq', 'sv', 'sw', 'ta', 'te', 'tk', 'ur', 'yo'], nr: [1, 2], fc: 2 }, { lngs: ['ay', 'bo', 'cgg', 'fa', 'id', 'ja', 'jbo', 'ka', 'kk', 'km', 'ko', 'ky', 'lo', 'ms', 'sah', 'su', 'th', 'tt', 'ug', 'vi', 'wo', 'zh'], nr: [1], fc: 3 }, { lngs: ['be', 'bs', 'dz', 'hr', 'ru', 'sr', 'uk'], nr: [1, 2, 5], fc: 4 }, { lngs: ['ar'], nr: [0, 1, 2, 3, 11, 100], fc: 5 }, { lngs: ['cs', 'sk'], nr: [1, 2, 5], fc: 6 }, { lngs: ['csb', 'pl'], nr: [1, 2, 5], fc: 7 }, { lngs: ['cy'], nr: [1, 2, 3, 8], fc: 8 }, { lngs: ['fr'], nr: [1, 2], fc: 9 }, { lngs: ['ga'], nr: [1, 2, 3, 7, 11], fc: 10 }, { lngs: ['gd'], nr: [1, 2, 3, 20], fc: 11 }, { lngs: ['is'], nr: [1, 2], fc: 12 }, { lngs: ['jv'], nr: [0, 1], fc: 13 }, { lngs: ['kw'], nr: [1, 2, 3, 4], fc: 14 }, { lngs: ['lt'], nr: [1, 2, 10], fc: 15 }, { lngs: ['lv'], nr: [1, 2, 0], fc: 16 }, { lngs: ['mk'], nr: [1, 2], fc: 17 }, { lngs: ['mnk'], nr: [0, 1, 2], fc: 18 }, { lngs: ['mt'], nr: [1, 2, 11, 20], fc: 19 }, { lngs: ['or'], nr: [2, 1], fc: 2 }, { lngs: ['ro'], nr: [1, 2, 20], fc: 20 }, { lngs: ['sl'], nr: [5, 1, 2, 3], fc: 21 }];

    var _rulesPluralsTypes = {
        1: function _(n) {
            return Number(n > 1);
        },
        2: function _(n) {
            return Number(n != 1);
        },
        3: function _(n) {
            return 0;
        },
        4: function _(n) {
            return Number(n % 10 == 1 && n % 100 != 11 ? 0 : n % 10 >= 2 && n % 10 <= 4 && (n % 100 < 10 || n % 100 >= 20) ? 1 : 2);
        },
        5: function _(n) {
            return Number(n === 0 ? 0 : n == 1 ? 1 : n == 2 ? 2 : n % 100 >= 3 && n % 100 <= 10 ? 3 : n % 100 >= 11 ? 4 : 5);
        },
        6: function _(n) {
            return Number(n == 1 ? 0 : n >= 2 && n <= 4 ? 1 : 2);
        },
        7: function _(n) {
            return Number(n == 1 ? 0 : n % 10 >= 2 && n % 10 <= 4 && (n % 100 < 10 || n % 100 >= 20) ? 1 : 2);
        },
        8: function _(n) {
            return Number(n == 1 ? 0 : n == 2 ? 1 : n != 8 && n != 11 ? 2 : 3);
        },
        9: function _(n) {
            return Number(n >= 2);
        },
        10: function _(n) {
            return Number(n == 1 ? 0 : n == 2 ? 1 : n < 7 ? 2 : n < 11 ? 3 : 4);
        },
        11: function _(n) {
            return Number(n == 1 || n == 11 ? 0 : n == 2 || n == 12 ? 1 : n > 2 && n < 20 ? 2 : 3);
        },
        12: function _(n) {
            return Number(n % 10 != 1 || n % 100 == 11);
        },
        13: function _(n) {
            return Number(n !== 0);
        },
        14: function _(n) {
            return Number(n == 1 ? 0 : n == 2 ? 1 : n == 3 ? 2 : 3);
        },
        15: function _(n) {
            return Number(n % 10 == 1 && n % 100 != 11 ? 0 : n % 10 >= 2 && (n % 100 < 10 || n % 100 >= 20) ? 1 : 2);
        },
        16: function _(n) {
            return Number(n % 10 == 1 && n % 100 != 11 ? 0 : n !== 0 ? 1 : 2);
        },
        17: function _(n) {
            return Number(n == 1 || n % 10 == 1 ? 0 : 1);
        },
        18: function _(n) {
            return Number(n == 0 ? 0 : n == 1 ? 1 : 2);
        },
        19: function _(n) {
            return Number(n == 1 ? 0 : n === 0 || n % 100 > 1 && n % 100 < 11 ? 1 : n % 100 > 10 && n % 100 < 20 ? 2 : 3);
        },
        20: function _(n) {
            return Number(n == 1 ? 0 : n === 0 || n % 100 > 0 && n % 100 < 20 ? 1 : 2);
        },
        21: function _(n) {
            return Number(n % 100 == 1 ? 1 : n % 100 == 2 ? 2 : n % 100 == 3 || n % 100 == 4 ? 3 : 0);
        }
    };
    /* eslint-enable */

    function createRules() {
        var l,
            rules = {};
        sets.forEach(function (set) {
            set.lngs.forEach(function (l) {
                return rules[l] = {
                    numbers: set.nr,
                    plurals: _rulesPluralsTypes[set.fc]
                };
            });
        });
        return rules;
    }

    var PluralResolver = function () {
        function PluralResolver(languageUtils) {
            var options = arguments.length <= 1 || arguments[1] === undefined ? {} : arguments[1];
            classCallCheck(this, PluralResolver);

            this.languageUtils = languageUtils;
            this.options = options;

            this.logger = baseLogger.create('pluralResolver');

            this.rules = createRules();
        }

        PluralResolver.prototype.addRule = function addRule(lng, obj) {
            this.rules[lng] = obj;
        };

        PluralResolver.prototype.getRule = function getRule(code) {
            return this.rules[this.languageUtils.getLanguagePartFromCode(code)];
        };

        PluralResolver.prototype.needsPlural = function needsPlural(code) {
            var rule = this.getRule(code);

            return rule && rule.numbers.length <= 1 ? false : true;
        };

        PluralResolver.prototype.getSuffix = function getSuffix(code, count) {
            var _this = this;

            var rule = this.getRule(code);

            if (rule) {
                var _ret = function () {
                    if (rule.numbers.length === 1) return {
                        v: ''
                    }; // only singular

                    var idx = rule.noAbs ? rule.plurals(count) : rule.plurals(Math.abs(count));
                    var suffix = rule.numbers[idx];

                    // special treatment for lngs only having singular and plural
                    if (rule.numbers.length === 2 && rule.numbers[0] === 1) {
                        if (suffix === 2) {
                            suffix = 'plural';
                        } else if (suffix === 1) {
                            suffix = '';
                        }
                    }

                    var returnSuffix = function returnSuffix() {
                        return _this.options.prepend && suffix.toString() ? _this.options.prepend + suffix.toString() : suffix.toString();
                    };

                    // COMPATIBILITY JSON
                    // v1
                    if (_this.options.compatibilityJSON === 'v1') {
                        if (suffix === 1) return {
                            v: ''
                        };
                        if (typeof suffix === 'number') return {
                            v: '_plural_' + suffix.toString()
                        };
                        return {
                            v: returnSuffix()
                        };
                    }
                    // v2
                    else if (_this.options.compatibilityJSON === 'v2' || rule.numbers.length === 2 && rule.numbers[0] === 1) {
                        return {
                            v: returnSuffix()
                        };
                    }
                    // v3 - gettext index
                    else if (rule.numbers.length === 2 && rule.numbers[0] === 1) {
                        return {
                            v: returnSuffix()
                        };
                    }
                    return {
                        v: _this.options.prepend && idx.toString() ? _this.options.prepend + idx.toString() : idx.toString()
                    };
                }();

                if ((typeof _ret === 'undefined' ? 'undefined' : _typeof(_ret)) === "object") return _ret.v;
            } else {
                this.logger.warn('no plural rule found for: ' + code);
                return '';
            }
        };

        return PluralResolver;
    }();

    ;

    var Interpolator = function () {
        function Interpolator() {
            var options = arguments.length <= 0 || arguments[0] === undefined ? {} : arguments[0];
            classCallCheck(this, Interpolator);

            this.logger = baseLogger.create('interpolator');

            this.init(options, true);
        }

        Interpolator.prototype.init = function init() {
            var options = arguments.length <= 0 || arguments[0] === undefined ? {} : arguments[0];
            var reset = arguments[1];

            if (reset) this.options = options;
            if (!options.interpolation) options.interpolation = { escapeValue: true };

            var iOpts = options.interpolation;

            this.escapeValue = iOpts.escapeValue;

            this.prefix = iOpts.prefix ? regexEscape(iOpts.prefix) : iOpts.prefixEscaped || '{{';
            this.suffix = iOpts.suffix ? regexEscape(iOpts.suffix) : iOpts.suffixEscaped || '}}';

            this.unescapePrefix = iOpts.unescapeSuffix ? '' : iOpts.unescapePrefix || '-';
            this.unescapeSuffix = this.unescapePrefix ? '' : iOpts.unescapeSuffix || '';

            this.nestingPrefix = iOpts.nestingPrefix ? regexEscape(iOpts.nestingPrefix) : iOpts.nestingPrefixEscaped || regexEscape('$t(');
            this.nestingSuffix = iOpts.nestingSuffix ? regexEscape(iOpts.nestingSuffix) : iOpts.nestingSuffixEscaped || regexEscape(')');

            // the regexp
            var regexpStr = this.prefix + '(.+?)' + this.suffix;
            this.regexp = new RegExp(regexpStr, 'g');

            var regexpUnescapeStr = this.prefix + this.unescapePrefix + '(.+?)' + this.unescapeSuffix + this.suffix;
            this.regexpUnescape = new RegExp(regexpUnescapeStr, 'g');

            var nestingRegexpStr = this.nestingPrefix + '(.+?)' + this.nestingSuffix;
            this.nestingRegexp = new RegExp(nestingRegexpStr, 'g');
        };

        Interpolator.prototype.reset = function reset() {
            if (this.options) this.init(this.options);
        };

        Interpolator.prototype.interpolate = function interpolate(str, data) {
            var match = void 0,
                value = void 0;

            function regexSafe(val) {
                return val.replace(/\$/g, '$$$$');
            }

            // unescape if has unescapePrefix/Suffix
            while (match = this.regexpUnescape.exec(str)) {
                var _value = getPath(data, match[1].trim());
                str = str.replace(match[0], _value);
                this.regexpUnescape.lastIndex = 0;
            }

            // regular escape on demand
            while (match = this.regexp.exec(str)) {
                value = getPath(data, match[1].trim());
                if (typeof value !== 'string') value = makeString(value);
                if (!value) {
                    this.logger.warn('missed to pass in variable ' + match[1] + ' for interpolating ' + str);
                    value = '';
                }
                value = this.escapeValue ? regexSafe(escape(value)) : regexSafe(value);
                str = str.replace(match[0], value);
                this.regexp.lastIndex = 0;
            }
            return str;
        };

        Interpolator.prototype.nest = function nest(str, fc) {
            var options = arguments.length <= 2 || arguments[2] === undefined ? {} : arguments[2];

            var match = void 0,
                value = void 0;

            var clonedOptions = JSON.parse(JSON.stringify(options));
            clonedOptions.applyPostProcessor = false; // avoid post processing on nested lookup

            function regexSafe(val) {
                return val.replace(/\$/g, '$$$$');
            }

            // if value is something like "myKey": "lorem $(anotherKey, { "count": {{aValueInOptions}} })"
            function handleHasOptions(key) {
                if (key.indexOf(',') < 0) return key;

                var p = key.split(',');
                key = p.shift();
                var optionsString = p.join(',');
                optionsString = this.interpolate(optionsString, clonedOptions);

                try {
                    clonedOptions = JSON.parse(optionsString);
                } catch (e) {
                    this.logger.error('failed parsing options string in nesting for key ' + key, e);
                }

                return key;
            }

            // regular escape on demand
            while (match = this.nestingRegexp.exec(str)) {
                value = fc(handleHasOptions.call(this, match[1].trim()), clonedOptions);
                if (typeof value !== 'string') value = makeString(value);
                if (!value) {
                    this.logger.warn('missed to pass in variable ' + match[1] + ' for interpolating ' + str);
                    value = '';
                }
                value = this.escapeValue ? regexSafe(escape(value)) : regexSafe(value);
                str = str.replace(match[0], value);
                this.regexp.lastIndex = 0;
            }
            return str;
        };

        return Interpolator;
    }();

    function remove(arr, what) {
        var found = arr.indexOf(what);

        while (found !== -1) {
            arr.splice(found, 1);
            found = arr.indexOf(what);
        }
    }

    var Connector = function (_EventEmitter) {
        inherits(Connector, _EventEmitter);

        function Connector(backend, store, services) {
            var options = arguments.length <= 3 || arguments[3] === undefined ? {} : arguments[3];
            classCallCheck(this, Connector);

            var _this = possibleConstructorReturn(this, _EventEmitter.call(this));

            _this.backend = backend;
            _this.store = store;
            _this.services = services;
            _this.options = options;
            _this.logger = baseLogger.create('backendConnector');

            _this.state = {};
            _this.queue = [];

            _this.backend && _this.backend.init && _this.backend.init(services, options.backend, options);
            return _this;
        }

        Connector.prototype.queueLoad = function queueLoad(languages, namespaces, callback) {
            var _this2 = this;

            // find what needs to be loaded
            var toLoad = [],
                pending = [],
                toLoadLanguages = [],
                toLoadNamespaces = [];

            languages.forEach(function (lng) {
                var hasAllNamespaces = true;

                namespaces.forEach(function (ns) {
                    var name = lng + '|' + ns;

                    if (_this2.store.hasResourceBundle(lng, ns)) {
                        _this2.state[name] = 2; // loaded
                    } else if (_this2.state[name] < 0) {
                        // nothing to do for err
                    } else if (_this2.state[name] === 1) {
                        if (pending.indexOf(name) < 0) pending.push(name);
                    } else {
                        _this2.state[name] = 1; // pending

                        hasAllNamespaces = false;

                        if (pending.indexOf(name) < 0) pending.push(name);
                        if (toLoad.indexOf(name) < 0) toLoad.push(name);
                        if (toLoadNamespaces.indexOf(ns) < 0) toLoadNamespaces.push(ns);
                    }
                });

                if (!hasAllNamespaces) toLoadLanguages.push(lng);
            });

            if (toLoad.length || pending.length) {
                this.queue.push({
                    pending: pending,
                    loaded: {},
                    errors: [],
                    callback: callback
                });
            }

            return {
                toLoad: toLoad,
                pending: pending,
                toLoadLanguages: toLoadLanguages,
                toLoadNamespaces: toLoadNamespaces
            };
        };

        Connector.prototype.loaded = function loaded(name, err, data) {
            var _this3 = this;

            var _name$split = name.split('|');

            var _name$split2 = slicedToArray(_name$split, 2);

            var lng = _name$split2[0];
            var ns = _name$split2[1];


            if (err) this.emit('failedLoading', lng, ns, err);

            if (data) {
                this.store.addResourceBundle(lng, ns, data);
            }

            // set loaded
            this.state[name] = err ? -1 : 2;
            // callback if ready
            this.queue.forEach(function (q) {
                pushPath(q.loaded, [lng], ns);
                remove(q.pending, name);

                if (err) q.errors.push(err);

                if (q.pending.length === 0 && !q.done) {
                    q.errors.length ? q.callback(q.errors) : q.callback();
                    _this3.emit('loaded', q.loaded);
                    q.done = true;
                }
            });

            // remove done load requests
            this.queue = this.queue.filter(function (q) {
                return !q.done;
            });
        };

        Connector.prototype.read = function read(lng, ns, fcName, tried, wait, callback) {
            var _this4 = this;

            if (!tried) tried = 0;
            if (!wait) wait = 250;

            if (!lng.length) return callback(null, {}); // noting to load

            this.backend[fcName](lng, ns, function (err, data) {
                if (err && data /* = retryFlag */ && tried < 5) {
                    setTimeout(function () {
                        _this4.read.call(_this4, lng, ns, fcName, ++tried, wait * 2, callback);
                    }, wait);
                    return;
                }
                callback(err, data);
            });
        };

        Connector.prototype.load = function load(languages, namespaces, callback) {
            var _this5 = this;

            if (!this.backend) {
                this.logger.warn('No backend was added via i18next.use. Will not load resources.');
                return callback && callback();
            }
            var options = _extends({}, this.backend.options, this.options.backend);

            if (typeof languages === 'string') languages = this.services.languageUtils.toResolveHierarchy(languages);
            if (typeof namespaces === 'string') namespaces = [namespaces];

            var toLoad = this.queueLoad(languages, namespaces, callback);
            if (!toLoad.toLoad.length) {
                if (!toLoad.pending.length) callback(); // nothing to load and no pendings...callback now
                return; // pendings will trigger callback
            }

            // load with multi-load
            if (options.allowMultiLoading && this.backend.readMulti) {
                this.read(toLoad.toLoadLanguages, toLoad.toLoadNamespaces, 'readMulti', null, null, function (err, data) {
                    if (err) _this5.logger.warn('loading namespaces ' + toLoad.toLoadNamespaces.join(', ') + ' for languages ' + toLoad.toLoadLanguages.join(', ') + ' via multiloading failed', err);
                    if (!err && data) _this5.logger.log('loaded namespaces ' + toLoad.toLoadNamespaces.join(', ') + ' for languages ' + toLoad.toLoadLanguages.join(', ') + ' via multiloading', data);

                    toLoad.toLoad.forEach(function (name) {
                        var _name$split3 = name.split('|');

                        var _name$split4 = slicedToArray(_name$split3, 2);

                        var l = _name$split4[0];
                        var n = _name$split4[1];


                        var bundle = getPath(data, [l, n]);
                        if (bundle) {
                            _this5.loaded(name, err, bundle);
                        } else {
                            var _err = 'loading namespace ' + n + ' for language ' + l + ' via multiloading failed';
                            _this5.loaded(name, _err);
                            _this5.logger.error(_err);
                        }
                    });
                });
            }

            // load one by one
            else {
                (function () {
                    var readOne = function readOne(name) {
                        var _this6 = this;

                        var _name$split5 = name.split('|');

                        var _name$split6 = slicedToArray(_name$split5, 2);

                        var lng = _name$split6[0];
                        var ns = _name$split6[1];


                        this.read(lng, ns, 'read', null, null, function (err, data) {
                            if (err) _this6.logger.warn('loading namespace ' + ns + ' for language ' + lng + ' failed', err);
                            if (!err && data) _this6.logger.log('loaded namespace ' + ns + ' for language ' + lng, data);

                            _this6.loaded(name, err, data);
                        });
                    };

                    ;

                    toLoad.toLoad.forEach(function (name) {
                        readOne.call(_this5, name);
                    });
                })();
            }
        };

        Connector.prototype.reload = function reload(languages, namespaces) {
            var _this7 = this;

            if (!this.backend) {
                this.logger.warn('No backend was added via i18next.use. Will not load resources.');
            }
            var options = _extends({}, this.backend.options, this.options.backend);

            if (typeof languages === 'string') languages = this.services.languageUtils.toResolveHierarchy(languages);
            if (typeof namespaces === 'string') namespaces = [namespaces];

            // load with multi-load
            if (options.allowMultiLoading && this.backend.readMulti) {
                this.read(languages, namespaces, 'readMulti', null, null, function (err, data) {
                    if (err) _this7.logger.warn('reloading namespaces ' + namespaces.join(', ') + ' for languages ' + languages.join(', ') + ' via multiloading failed', err);
                    if (!err && data) _this7.logger.log('reloaded namespaces ' + namespaces.join(', ') + ' for languages ' + languages.join(', ') + ' via multiloading', data);

                    languages.forEach(function (l) {
                        namespaces.forEach(function (n) {
                            var bundle = getPath(data, [l, n]);
                            if (bundle) {
                                _this7.loaded(l + '|' + n, err, bundle);
                            } else {
                                var _err2 = 'reloading namespace ' + n + ' for language ' + l + ' via multiloading failed';
                                _this7.loaded(l + '|' + n, _err2);
                                _this7.logger.error(_err2);
                            }
                        });
                    });
                });
            }

            // load one by one
            else {
                (function () {
                    var readOne = function readOne(name) {
                        var _this8 = this;

                        var _name$split7 = name.split('|');

                        var _name$split8 = slicedToArray(_name$split7, 2);

                        var lng = _name$split8[0];
                        var ns = _name$split8[1];


                        this.read(lng, ns, 'read', null, null, function (err, data) {
                            if (err) _this8.logger.warn('reloading namespace ' + ns + ' for language ' + lng + ' failed', err);
                            if (!err && data) _this8.logger.log('reloaded namespace ' + ns + ' for language ' + lng, data);

                            _this8.loaded(name, err, data);
                        });
                    };

                    ;

                    languages.forEach(function (l) {
                        namespaces.forEach(function (n) {
                            readOne.call(_this7, l + '|' + n);
                        });
                    });
                })();
            }
        };

        Connector.prototype.saveMissing = function saveMissing(languages, namespace, key, fallbackValue) {
            if (this.backend && this.backend.create) this.backend.create(languages, namespace, key, fallbackValue);

            // write to store to avoid resending
            if (!languages || !languages[0]) return;
            this.store.addResource(languages[0], namespace, key, fallbackValue);
        };

        return Connector;
    }(EventEmitter);

    var Connector$1 = function (_EventEmitter) {
        inherits(Connector, _EventEmitter);

        function Connector(cache, store, services) {
            var options = arguments.length <= 3 || arguments[3] === undefined ? {} : arguments[3];
            classCallCheck(this, Connector);

            var _this = possibleConstructorReturn(this, _EventEmitter.call(this));

            _this.cache = cache;
            _this.store = store;
            _this.services = services;
            _this.options = options;
            _this.logger = baseLogger.create('cacheConnector');

            _this.cache && _this.cache.init && _this.cache.init(services, options.cache, options);
            return _this;
        }

        Connector.prototype.load = function load(languages, namespaces, callback) {
            var _this2 = this;

            if (!this.cache) return callback && callback();
            var options = _extends({}, this.cache.options, this.options.cache);

            if (typeof languages === 'string') languages = this.services.languageUtils.toResolveHierarchy(languages);
            if (typeof namespaces === 'string') namespaces = [namespaces];

            if (options.enabled) {
                this.cache.load(languages, function (err, data) {
                    if (err) _this2.logger.error('loading languages ' + languages.join(', ') + ' from cache failed', err);
                    if (data) {
                        for (var l in data) {
                            for (var n in data[l]) {
                                if (n === 'i18nStamp') continue;
                                var bundle = data[l][n];
                                if (bundle) _this2.store.addResourceBundle(l, n, bundle);
                            }
                        }
                    }
                    if (callback) callback();
                });
            } else {
                if (callback) callback();
            }
        };

        Connector.prototype.save = function save() {
            if (this.cache && this.options.cache && this.options.cache.enabled) this.cache.save(this.store.data);
        };

        return Connector;
    }(EventEmitter);

    function get$1() {
        return {
            debug: false,
            initImmediate: true,

            ns: ['translation'],
            defaultNS: ['translation'],
            fallbackLng: ['dev'],
            fallbackNS: false, // string or array of namespaces

            whitelist: false, // array with whitelisted languages
            nonExplicitWhitelist: false,
            load: 'all', // | currentOnly | languageOnly
            preload: false, // array with preload languages

            keySeparator: '.',
            nsSeparator: ':',
            pluralSeparator: '_',
            contextSeparator: '_',

            saveMissing: false, // enable to send missing values
            saveMissingTo: 'fallback', // 'current' || 'all'
            missingKeyHandler: false, // function(lng, ns, key, fallbackValue) -> override if prefer on handling

            postProcess: false, // string or array of postProcessor names
            returnNull: true, // allows null value as valid translation
            returnEmptyString: true, // allows empty string value as valid translation
            returnObjects: false,
            joinArrays: false, // or string to join array
            returnedObjectHandler: function returnedObjectHandler() {}, // function(key, value, options) triggered if key returns object but returnObjects is set to false
            parseMissingKeyHandler: false, // function(key) parsed a key that was not found in t() before returning
            appendNamespaceToMissingKey: false,
            overloadTranslationOptionHandler: function overloadTranslationOptionHandler(args) {
                return { defaultValue: args[1] };
            },

            interpolation: {
                escapeValue: true,
                prefix: '{{',
                suffix: '}}',
                // prefixEscaped: '{{',
                // suffixEscaped: '}}',
                // unescapeSuffix: '',
                unescapePrefix: '-',

                nestingPrefix: '$t(',
                nestingSuffix: ')',
                // nestingPrefixEscaped: '$t(',
                // nestingSuffixEscaped: ')',
                defaultVariables: undefined // object that can have values to interpolate on - extends passed in interpolation data
            }
        };
    }

    function transformOptions(options) {
        // create namespace object if namespace is passed in as string
        if (typeof options.ns === 'string') options.ns = [options.ns];
        if (typeof options.fallbackLng === 'string') options.fallbackLng = [options.fallbackLng];
        if (typeof options.fallbackNS === 'string') options.fallbackNS = [options.fallbackNS];

        // extend whitelist with cimode
        if (options.whitelist && options.whitelist.indexOf('cimode') < 0) options.whitelist.push('cimode');

        return options;
    }

    var dolinaI18n = function (_EventEmitter) {
        inherits(dolinaI18n, _EventEmitter);

        function dolinaI18n() {
            var options = arguments.length <= 0 || arguments[0] === undefined ? {} : arguments[0];
            var callback = arguments[1];
            classCallCheck(this, dolinaI18n);

            var _this = possibleConstructorReturn(this, _EventEmitter.call(this));

            _this.options = transformOptions(options);
            _this.services = {};
            _this.logger = baseLogger;
            _this.modules = {};

            if (callback && !_this.isInitialized) _this.init(options, callback);
            return _this;
        }

        dolinaI18n.prototype.init = function init(options, callback) {
            var _this2 = this;

            if (typeof options === 'function') {
                callback = options;
                options = {};
            }
            if (!options) options = {};

            if (options.compatibilityAPI === 'v1') {
                this.options = _extends({}, get$1(), transformOptions(convertAPIOptions(options)), {});
            } else if (options.compatibilityJSON === 'v1') {
                this.options = _extends({}, get$1(), transformOptions(convertJSONOptions(options)), {});
            } else {
                this.options = _extends({}, get$1(), this.options, transformOptions(options));
            }
            if (!callback) callback = function callback() {};

            function createClassOnDemand(ClassOrObject) {
                if (!ClassOrObject) return;
                if (typeof ClassOrObject === 'function') return new ClassOrObject();
                return ClassOrObject;
            }

            // init services
            if (!this.options.isClone) {
                if (this.modules.logger) {
                    baseLogger.init(createClassOnDemand(this.modules.logger), this.options);
                } else {
                    baseLogger.init(null, this.options);
                }

                var lu = new LanguageUtil(this.options);
                this.store = new ResourceStore(this.options.resources, this.options);

                var s = this.services;
                s.logger = baseLogger;
                s.resourceStore = this.store;
                s.resourceStore.on('added removed', function (lng, ns) {
                    s.cacheConnector.save();
                });
                s.languageUtils = lu;
                s.pluralResolver = new PluralResolver(lu, { prepend: this.options.pluralSeparator, compatibilityJSON: this.options.compatibilityJSON });
                s.interpolator = new Interpolator(this.options);

                s.backendConnector = new Connector(createClassOnDemand(this.modules.backend), s.resourceStore, s, this.options);
                // pipe events from backendConnector
                s.backendConnector.on('*', function (event) {
                    for (var _len = arguments.length, args = Array(_len > 1 ? _len - 1 : 0), _key = 1; _key < _len; _key++) {
                        args[_key - 1] = arguments[_key];
                    }

                    _this2.emit.apply(_this2, [event].concat(args));
                });

                s.backendConnector.on('loaded', function (loaded) {
                    s.cacheConnector.save();
                });

                s.cacheConnector = new Connector$1(createClassOnDemand(this.modules.cache), s.resourceStore, s, this.options);
                // pipe events from backendConnector
                s.cacheConnector.on('*', function (event) {
                    for (var _len2 = arguments.length, args = Array(_len2 > 1 ? _len2 - 1 : 0), _key2 = 1; _key2 < _len2; _key2++) {
                        args[_key2 - 1] = arguments[_key2];
                    }

                    _this2.emit.apply(_this2, [event].concat(args));
                });

                if (this.modules.languageDetector) {
                    s.languageDetector = createClassOnDemand(this.modules.languageDetector);
                    s.languageDetector.init(s, this.options.detection, this.options);
                }

                this.translator = new Translator(this.services, this.options);
                // pipe events from translator
                this.translator.on('*', function (event) {
                    for (var _len3 = arguments.length, args = Array(_len3 > 1 ? _len3 - 1 : 0), _key3 = 1; _key3 < _len3; _key3++) {
                        args[_key3 - 1] = arguments[_key3];
                    }

                    _this2.emit.apply(_this2, [event].concat(args));
                });
            }

            // append api
            var storeApi = ['getResource', 'addResource', 'addResources', 'addResourceBundle', 'removeResourceBundle', 'hasResourceBundle', 'getResourceBundle'];
            storeApi.forEach(function (fcName) {
                _this2[fcName] = function () {
                    return this.store[fcName].apply(this.store, arguments);
                };
            });

            // TODO: COMPATIBILITY remove this
            if (this.options.compatibilityAPI === 'v1') appendBackwardsAPI(this);

            var load = function load() {
                _this2.changeLanguage(_this2.options.lng, function (err, t) {
                    _this2.emit('initialized', _this2.options);
                    _this2.logger.log('initialized', _this2.options);

                    callback(err, t);
                });
            };

            if (this.options.resources || !this.options.initImmediate) {
                load();
            } else {
                setTimeout(load, 0);
            }

            return this;
        };

        dolinaI18n.prototype.loadResources = function loadResources(callback) {
            var _this3 = this;

            if (!callback) callback = function callback() {};

            if (!this.options.resources) {
                var _ret = function () {
                    if (_this3.language && _this3.language.toLowerCase() === 'cimode') return {
                        v: callback()
                    }; // avoid loading resources for cimode

                    var toLoad = [];

                    var append = function append(lng) {
                        var lngs = _this3.services.languageUtils.toResolveHierarchy(lng);
                        lngs.forEach(function (l) {
                            if (toLoad.indexOf(l) < 0) toLoad.push(l);
                        });
                    };

                    append(_this3.language);

                    if (_this3.options.preload) {
                        _this3.options.preload.forEach(function (l) {
                            append(l);
                        });
                    }

                    _this3.services.cacheConnector.load(toLoad, _this3.options.ns, function () {
                        _this3.services.backendConnector.load(toLoad, _this3.options.ns, callback);
                    });
                }();

                if ((typeof _ret === 'undefined' ? 'undefined' : _typeof(_ret)) === "object") return _ret.v;
            } else {
                callback(null);
            }
        };

        dolinaI18n.prototype.reloadResources = function reloadResources(lngs, ns) {
            if (!lngs) lngs = this.languages;
            if (!ns) ns = this.options.ns;
            this.services.backendConnector.reload(lngs, ns);
        };

        dolinaI18n.prototype.use = function use(module) {
            if (module.type === 'backend') {
                this.modules.backend = module;
            }

            if (module.type === 'cache') {
                this.modules.cache = module;
            }

            if (module.type === 'logger' || module.log && module.warn && module.warn) {
                this.modules.logger = module;
            }

            if (module.type === 'languageDetector') {
                this.modules.languageDetector = module;
            }

            if (module.type === 'postProcessor') {
                postProcessor.addPostProcessor(module);
            }

            return this;
        };

        dolinaI18n.prototype.changeLanguage = function changeLanguage(lng, callback) {
            var _this4 = this;

            var done = function done(err) {
                if (lng) {
                    _this4.emit('languageChanged', lng);
                    _this4.logger.log('languageChanged', lng);
                }

                if (callback) callback(err, function () {
                    for (var _len4 = arguments.length, args = Array(_len4), _key4 = 0; _key4 < _len4; _key4++) {
                        args[_key4] = arguments[_key4];
                    }

                    return _this4.t.apply(_this4, args);
                });
            };

            if (!lng && this.services.languageDetector) lng = this.services.languageDetector.detect();

            if (lng) {
                this.language = lng;
                this.languages = this.services.languageUtils.toResolveHierarchy(lng);

                this.translator.changeLanguage(lng);

                if (this.services.languageDetector) this.services.languageDetector.cacheUserLanguage(lng);
            }

            this.loadResources(function (err) {
                done(err);
            });
        };

        dolinaI18n.prototype.getFixedT = function getFixedT(lng, ns) {
            var _this5 = this;

            var fixedT = function fixedT(key, options) {
                options = options || {};
                options.lng = options.lng || fixedT.lng;
                options.ns = options.ns || fixedT.ns;
                return _this5.t(key, options);
            };
            fixedT.lng = lng;
            fixedT.ns = ns;
            return fixedT;
        };

        dolinaI18n.prototype.t = function t() {
            return this.translator && this.translator.translate.apply(this.translator, arguments);
        };

        dolinaI18n.prototype.exists = function exists() {
            return this.translator && this.translator.exists.apply(this.translator, arguments);
        };

        dolinaI18n.prototype.setDefaultNamespace = function setDefaultNamespace(ns) {
            this.options.defaultNS = ns;
        };

        dolinaI18n.prototype.loadNamespaces = function loadNamespaces(ns, callback) {
            var _this6 = this;

            if (!this.options.ns) return callback && callback();
            if (typeof ns === 'string') ns = [ns];

            ns.forEach(function (n) {
                if (_this6.options.ns.indexOf(n) < 0) _this6.options.ns.push(n);
            });

            this.loadResources(callback);
        };

        dolinaI18n.prototype.loadLanguages = function loadLanguages(lngs, callback) {
            if (typeof lngs === 'string') lngs = [lngs];
            var preloaded = this.options.preload || [];

            var newLngs = lngs.filter(function (lng) {
                return preloaded.indexOf(lng) < 0;
            });
            // Exit early if all given languages are already preloaded
            if (!newLngs.length) return callback();

            this.options.preload = preloaded.concat(newLngs);
            this.loadResources(callback);
        };

        dolinaI18n.prototype.dir = function dir(lng) {
            if (!lng) lng = this.language;

            var rtlLngs = ['ar', 'shu', 'sqr', 'ssh', 'xaa', 'yhd', 'yud', 'aao', 'abh', 'abv', 'acm', 'acq', 'acw', 'acx', 'acy', 'adf', 'ads', 'aeb', 'aec', 'afb', 'ajp', 'apc', 'apd', 'arb', 'arq', 'ars', 'ary', 'arz', 'auz', 'avl', 'ayh', 'ayl', 'ayn', 'ayp', 'bbz', 'pga', 'he', 'iw', 'ps', 'pbt', 'pbu', 'pst', 'prp', 'prd', 'ur', 'ydd', 'yds', 'yih', 'ji', 'yi', 'hbo', 'men', 'xmn', 'fa', 'jpr', 'peo', 'pes', 'prs', 'dv', 'sam'];

            return rtlLngs.indexOf(this.services.languageUtils.getLanguagePartFromCode(lng)) >= 0 ? 'rtl' : 'ltr';
        };

        dolinaI18n.prototype.createInstance = function createInstance() {
            var options = arguments.length <= 0 || arguments[0] === undefined ? {} : arguments[0];
            var callback = arguments[1];

            return new dolinaI18n(options, callback);
        };

        dolinaI18n.prototype.cloneInstance = function cloneInstance() {
            var _this7 = this;

            var options = arguments.length <= 0 || arguments[0] === undefined ? {} : arguments[0];
            var callback = arguments[1];

            var clone = new dolinaI18n(_extends({}, options, this.options, { isClone: true }), callback);
            var membersToCopy = ['store', 'translator', 'services', 'language'];
            membersToCopy.forEach(function (m) {
                clone[m] = _this7[m];
            });

            return clone;
        };

        return dolinaI18n;
    }(EventEmitter);

    var dolina_i18n = new dolinaI18n();

    dolina_i18n.init({
        lng: 'en',
        fallbackLng: 'en',
        resources: {
            en: {
                translation: {
                    "recent_on_online": "Recently Online",
                    "close":"Close",
                    "leave_msg": "Leave your message here",
                    "sent": "Delivered",
                    "sending": "In sending",
                    "send": "SEND",
                    "read": "Already read",
                    "get_image": "I have an image for your, please check",
                    "submit": "Submit",
                    "send_image": "send an image"
                }
            },
            zh: {
                translation: {
                    "recent_on_online": "最近在线",
                    "close":"关闭",
                    "leave_msg": "在这里留言",
                    "sent": "已发送",
                    "sending": "发送中",
                    "send": "发送",
                    "read": "已阅读",
                    "get_image": "我给您发送了一张图片，打开看看吧",
                    "submit": "提交",
                    "send_image": "发来了一张图片"
                }
            },
            es: {
                translation: {
                    "recent_on_online": "línea reciente",
                    "close":"cerca",
                    "leave_msg": "deja un mensaje",
                    "sent": "entregado",
                    "sending": "la entrega",
                    "read": "leer",
                    "send": "ENVIAR",
                    "get_image": "tiene una imagen",
                    "submit": "enviar"
                }
            }
        }
    });

    return dolina_i18n;

}));
/*******************************************************************************
 * Copyright (c) 2013 IBM Corp.
 *
 * All rights reserved. This program and the accompanying materials
 * are made available under the terms of the Eclipse Public License v1.0
 * and Eclipse Distribution License v1.0 which accompany this distribution. 
 *
 * The Eclipse Public License is available at 
 *    http://www.eclipse.org/legal/epl-v10.html
 * and the Eclipse Distribution License is available at 
 *   http://www.eclipse.org/org/documents/edl-v10.php.
 *
 * Contributors:
 *    Andrew Banks - initial API and implementation and initial documentation
 *******************************************************************************/


// Only expose a single object name in the global namespace.
// Everything must go through this module. Global Paho.MQTT module
// only has a single public function, client, which returns
// a Paho.MQTT client object given connection details.
 
/**
 * Send and receive messages using web browsers.
 * <p> 
 * This programming interface lets a JavaScript client application use the MQTT V3.1 or
 * V3.1.1 protocol to connect to an MQTT-supporting messaging server.
 *  
 * The function supported includes:
 * <ol>
 * <li>Connecting to and disconnecting from a server. The server is identified by its host name and port number. 
 * <li>Specifying options that relate to the communications link with the server, 
 * for example the frequency of keep-alive heartbeats, and whether SSL/TLS is required.
 * <li>Subscribing to and receiving messages from MQTT Topics.
 * <li>Publishing messages to MQTT Topics.
 * </ol>
 * <p>
 * The API consists of two main objects:
 * <dl>
 * <dt><b>{@link Paho.MQTT.Client}</b></dt>
 * <dd>This contains methods that provide the functionality of the API,
 * including provision of callbacks that notify the application when a message
 * arrives from or is delivered to the messaging server,
 * or when the status of its connection to the messaging server changes.</dd>
 * <dt><b>{@link Paho.MQTT.Message}</b></dt>
 * <dd>This encapsulates the payload of the message along with various attributes
 * associated with its delivery, in particular the destination to which it has
 * been (or is about to be) sent.</dd>
 * </dl> 
 * <p>
 * The programming interface validates parameters passed to it, and will throw
 * an Error containing an error message intended for developer use, if it detects
 * an error with any parameter.
 * <p>
 * Example:
 * 
 * <code><pre>
client = new Paho.MQTT.Client(location.hostname, Number(location.port), "clientId");
client.onConnectionLost = onConnectionLost;
client.onMessageArrived = onMessageArrived;
client.connect({onSuccess:onConnect});

function onConnect() {
  // Once a connection has been made, make a subscription and send a message.
  console.log("onConnect");
  client.subscribe("/World");
  message = new Paho.MQTT.Message("Hello");
  message.destinationName = "/World";
  client.send(message); 
};
function onConnectionLost(responseObject) {
  if (responseObject.errorCode !== 0)
	console.log("onConnectionLost:"+responseObject.errorMessage);
};
function onMessageArrived(message) {
  console.log("onMessageArrived:"+message.payloadString);
  client.disconnect(); 
};	
 * </pre></code>
 * @namespace Paho.MQTT 
 */

if (typeof dolinaWS === "undefined") {
	dolinaWS = {};
}

dolinaWS.MQTT = (function (global) {

	// Private variables below, these are only visible inside the function closure
	// which is used to define the module. 

	var version = "@VERSION@";
	var buildLevel = "@BUILDLEVEL@";
	
	/** 
	 * Unique message type identifiers, with associated
	 * associated integer values.
	 * @private 
	 */
	var MESSAGE_TYPE = {
		CONNECT: 1, 
		CONNACK: 2, 
		PUBLISH: 3,
		PUBACK: 4,
		PUBREC: 5, 
		PUBREL: 6,
		PUBCOMP: 7,
		SUBSCRIBE: 8,
		SUBACK: 9,
		UNSUBSCRIBE: 10,
		UNSUBACK: 11,
		PINGREQ: 12,
		PINGRESP: 13,
		DISCONNECT: 14
	};
	
	// Collection of utility methods used to simplify module code 
	// and promote the DRY pattern.  

	/**
	 * Validate an object's parameter names to ensure they 
	 * match a list of expected variables name for this option
	 * type. Used to ensure option object passed into the API don't
	 * contain erroneous parameters.
	 * @param {Object} obj - User options object
	 * @param {Object} keys - valid keys and types that may exist in obj. 
	 * @throws {Error} Invalid option parameter found. 
	 * @private 
	 */
	var validate = function(obj, keys) {
		for (var key in obj) {
			if (obj.hasOwnProperty(key)) {       		
				if (keys.hasOwnProperty(key)) {
					if (typeof obj[key] !== keys[key])
					   throw new Error(format(ERROR.INVALID_TYPE, [typeof obj[key], key]));
				} else {	
					var errorStr = "Unknown property, " + key + ". Valid properties are:";
					for (var key in keys)
						if (keys.hasOwnProperty(key))
							errorStr = errorStr+" "+key;
					throw new Error(errorStr);
				}
			}
		}
	};

	/**
	 * Return a new function which runs the user function bound
	 * to a fixed scope. 
	 * @param {function} User function
	 * @param {object} Function scope  
	 * @return {function} User function bound to another scope
	 * @private 
	 */
	var scope = function (f, scope) {
		return function () {
			return f.apply(scope, arguments);
		};
	};
	
	/** 
	 * Unique message type identifiers, with associated
	 * associated integer values.
	 * @private 
	 */
	var ERROR = {
		OK: {code:0, text:"AMQJSC0000I OK."},
		CONNECT_TIMEOUT: {code:1, text:"AMQJSC0001E Connect timed out."},
		SUBSCRIBE_TIMEOUT: {code:2, text:"AMQJS0002E Subscribe timed out."}, 
		UNSUBSCRIBE_TIMEOUT: {code:3, text:"AMQJS0003E Unsubscribe timed out."},
		PING_TIMEOUT: {code:4, text:"AMQJS0004E Ping timed out."},
		INTERNAL_ERROR: {code:5, text:"AMQJS0005E Internal error. Error Message: {0}, Stack trace: {1}"},
		CONNACK_RETURNCODE: {code:6, text:"AMQJS0006E Bad Connack return code:{0} {1}."},
		SOCKET_ERROR: {code:7, text:"AMQJS0007E Socket error:{0}."},
		SOCKET_CLOSE: {code:8, text:"AMQJS0008I Socket closed."},
		MALFORMED_UTF: {code:9, text:"AMQJS0009E Malformed UTF data:{0} {1} {2}."},
		UNSUPPORTED: {code:10, text:"AMQJS0010E {0} is not supported by this browser."},
		INVALID_STATE: {code:11, text:"AMQJS0011E Invalid state {0}."},
		INVALID_TYPE: {code:12, text:"AMQJS0012E Invalid type {0} for {1}."},
		INVALID_ARGUMENT: {code:13, text:"AMQJS0013E Invalid argument {0} for {1}."},
		UNSUPPORTED_OPERATION: {code:14, text:"AMQJS0014E Unsupported operation."},
		INVALID_STORED_DATA: {code:15, text:"AMQJS0015E Invalid data in local storage key={0} value={1}."},
		INVALID_MQTT_MESSAGE_TYPE: {code:16, text:"AMQJS0016E Invalid MQTT message type {0}."},
		MALFORMED_UNICODE: {code:17, text:"AMQJS0017E Malformed Unicode string:{0} {1}."},
	};
	
	/** CONNACK RC Meaning. */
	var CONNACK_RC = {
		0:"Connection Accepted",
		1:"Connection Refused: unacceptable protocol version",
		2:"Connection Refused: identifier rejected",
		3:"Connection Refused: server unavailable",
		4:"Connection Refused: bad user name or password",
		5:"Connection Refused: not authorized"
	};

	/**
	 * Format an error message text.
	 * @private
	 * @param {error} ERROR.KEY value above.
	 * @param {substitutions} [array] substituted into the text.
	 * @return the text with the substitutions made.
	 */
	var format = function(error, substitutions) {
		var text = error.text;
		if (substitutions) {
		  var field,start;
		  for (var i=0; i<substitutions.length; i++) {
			field = "{"+i+"}";
			start = text.indexOf(field);
			if(start > 0) {
				var part1 = text.substring(0,start);
				var part2 = text.substring(start+field.length);
				text = part1+substitutions[i]+part2;
			}
		  }
		}
		return text;
	};
	
	//MQTT protocol and version          6    M    Q    I    s    d    p    3
	var MqttProtoIdentifierv3 = [0x00,0x06,0x4d,0x51,0x49,0x73,0x64,0x70,0x03];
	//MQTT proto/version for 311         4    M    Q    T    T    4
	var MqttProtoIdentifierv4 = [0x00,0x04,0x4d,0x51,0x54,0x54,0x04];
	
	/**
	 * Construct an MQTT wire protocol message.
	 * @param type MQTT packet type.
	 * @param options optional wire message attributes.
	 * 
	 * Optional properties
	 * 
	 * messageIdentifier: message ID in the range [0..65535]
	 * payloadMessage:	Application Message - PUBLISH only
	 * connectStrings:	array of 0 or more Strings to be put into the CONNECT payload
	 * topics:			array of strings (SUBSCRIBE, UNSUBSCRIBE)
	 * requestQoS:		array of QoS values [0..2]
	 *  
	 * "Flag" properties 
	 * cleanSession:	true if present / false if absent (CONNECT)
	 * willMessage:  	true if present / false if absent (CONNECT)
	 * isRetained:		true if present / false if absent (CONNECT)
	 * userName:		true if present / false if absent (CONNECT)
	 * password:		true if present / false if absent (CONNECT)
	 * keepAliveInterval:	integer [0..65535]  (CONNECT)
	 *
	 * @private
	 * @ignore
	 */
	var WireMessage = function (type, options) { 	
		this.type = type;
		for (var name in options) {
			if (options.hasOwnProperty(name)) {
				this[name] = options[name];
			}
		}
	};
	
	WireMessage.prototype.encode = function() {
		// Compute the first byte of the fixed header
		var first = ((this.type & 0x0f) << 4);
		
		/*
		 * Now calculate the length of the variable header + payload by adding up the lengths
		 * of all the component parts
		 */

		var remLength = 0;
		var topicStrLength = new Array();
		var destinationNameLength = 0;
		
		// if the message contains a messageIdentifier then we need two bytes for that
		if (this.messageIdentifier != undefined)
			remLength += 2;

		switch(this.type) {
			// If this a Connect then we need to include 12 bytes for its header
			case MESSAGE_TYPE.CONNECT:
				switch(this.mqttVersion) {
					case 3:
						remLength += MqttProtoIdentifierv3.length + 3;
						break;
					case 4:
						remLength += MqttProtoIdentifierv4.length + 3;
						break;
				}

				remLength += UTF8Length(this.clientId) + 2;
				if (this.willMessage != undefined) {
					remLength += UTF8Length(this.willMessage.destinationName) + 2;
					// Will message is always a string, sent as UTF-8 characters with a preceding length.
					var willMessagePayloadBytes = this.willMessage.payloadBytes;
					if (!(willMessagePayloadBytes instanceof Uint8Array))
						willMessagePayloadBytes = new Uint8Array(payloadBytes);
					remLength += willMessagePayloadBytes.byteLength +2;
				}
				if (this.userName != undefined)
					remLength += UTF8Length(this.userName) + 2;	
				if (this.password != undefined)
					remLength += UTF8Length(this.password) + 2;
			break;

			// Subscribe, Unsubscribe can both contain topic strings
			case MESSAGE_TYPE.SUBSCRIBE:	        	
				first |= 0x02; // Qos = 1;
				for ( var i = 0; i < this.topics.length; i++) {
					topicStrLength[i] = UTF8Length(this.topics[i]);
					remLength += topicStrLength[i] + 2;
				}
				remLength += this.requestedQos.length; // 1 byte for each topic's Qos
				// QoS on Subscribe only
				break;

			case MESSAGE_TYPE.UNSUBSCRIBE:
				first |= 0x02; // Qos = 1;
				for ( var i = 0; i < this.topics.length; i++) {
					topicStrLength[i] = UTF8Length(this.topics[i]);
					remLength += topicStrLength[i] + 2;
				}
				break;

			case MESSAGE_TYPE.PUBREL:
				first |= 0x02; // Qos = 1;
				break;

			case MESSAGE_TYPE.PUBLISH:
				if (this.payloadMessage.duplicate) first |= 0x08;
				first  = first |= (this.payloadMessage.qos << 1);
				if (this.payloadMessage.retained) first |= 0x01;
				destinationNameLength = UTF8Length(this.payloadMessage.destinationName);
				remLength += destinationNameLength + 2;	   
				var payloadBytes = this.payloadMessage.payloadBytes;
				remLength += payloadBytes.byteLength;  
				if (payloadBytes instanceof ArrayBuffer)
					payloadBytes = new Uint8Array(payloadBytes);
				else if (!(payloadBytes instanceof Uint8Array))
					payloadBytes = new Uint8Array(payloadBytes.buffer);
				break;

			case MESSAGE_TYPE.DISCONNECT:
				break;

			default:
				;
		}

		// Now we can allocate a buffer for the message

		var mbi = encodeMBI(remLength);  // Convert the length to MQTT MBI format
		var pos = mbi.length + 1;        // Offset of start of variable header
		var buffer = new ArrayBuffer(remLength + pos);
		var byteStream = new Uint8Array(buffer);    // view it as a sequence of bytes

		//Write the fixed header into the buffer
		byteStream[0] = first;
		byteStream.set(mbi,1);

		// If this is a PUBLISH then the variable header starts with a topic
		if (this.type == MESSAGE_TYPE.PUBLISH)
			pos = writeString(this.payloadMessage.destinationName, destinationNameLength, byteStream, pos);
		// If this is a CONNECT then the variable header contains the protocol name/version, flags and keepalive time
		
		else if (this.type == MESSAGE_TYPE.CONNECT) {
			switch (this.mqttVersion) {
				case 3:
					byteStream.set(MqttProtoIdentifierv3, pos);
					pos += MqttProtoIdentifierv3.length;
					break;
				case 4:
					byteStream.set(MqttProtoIdentifierv4, pos);
					pos += MqttProtoIdentifierv4.length;
					break;
			}
			var connectFlags = 0;
			if (this.cleanSession) 
				connectFlags = 0x02;
			if (this.willMessage != undefined ) {
				connectFlags |= 0x04;
				connectFlags |= (this.willMessage.qos<<3);
				if (this.willMessage.retained) {
					connectFlags |= 0x20;
				}
			}
			if (this.userName != undefined)
				connectFlags |= 0x80;
			if (this.password != undefined)
				connectFlags |= 0x40;
			byteStream[pos++] = connectFlags; 
			pos = writeUint16 (this.keepAliveInterval, byteStream, pos);
		}

		// Output the messageIdentifier - if there is one
		if (this.messageIdentifier != undefined)
			pos = writeUint16 (this.messageIdentifier, byteStream, pos);

		switch(this.type) {
			case MESSAGE_TYPE.CONNECT:
				pos = writeString(this.clientId, UTF8Length(this.clientId), byteStream, pos); 
				if (this.willMessage != undefined) {
					pos = writeString(this.willMessage.destinationName, UTF8Length(this.willMessage.destinationName), byteStream, pos);
					pos = writeUint16(willMessagePayloadBytes.byteLength, byteStream, pos);
					byteStream.set(willMessagePayloadBytes, pos);
					pos += willMessagePayloadBytes.byteLength;
					
				}
			if (this.userName != undefined)
				pos = writeString(this.userName, UTF8Length(this.userName), byteStream, pos);
			if (this.password != undefined) 
				pos = writeString(this.password, UTF8Length(this.password), byteStream, pos);
			break;

			case MESSAGE_TYPE.PUBLISH:	
				// PUBLISH has a text or binary payload, if text do not add a 2 byte length field, just the UTF characters.	
				byteStream.set(payloadBytes, pos);
					
				break;

//    	    case MESSAGE_TYPE.PUBREC:	
//    	    case MESSAGE_TYPE.PUBREL:	
//    	    case MESSAGE_TYPE.PUBCOMP:	
//    	    	break;

			case MESSAGE_TYPE.SUBSCRIBE:
				// SUBSCRIBE has a list of topic strings and request QoS
				for (var i=0; i<this.topics.length; i++) {
					pos = writeString(this.topics[i], topicStrLength[i], byteStream, pos);
					byteStream[pos++] = this.requestedQos[i];
				}
				break;

			case MESSAGE_TYPE.UNSUBSCRIBE:	
				// UNSUBSCRIBE has a list of topic strings
				for (var i=0; i<this.topics.length; i++)
					pos = writeString(this.topics[i], topicStrLength[i], byteStream, pos);
				break;

			default:
				// Do nothing.
		}

		return buffer;
	}	

	function decodeMessage(input,pos) {
	    var startingPos = pos;
		var first = input[pos];
		var type = first >> 4;
		var messageInfo = first &= 0x0f;
		pos += 1;
		

		// Decode the remaining length (MBI format)

		var digit;
		var remLength = 0;
		var multiplier = 1;
		do {
			if (pos == input.length) {
			    return [null,startingPos];
			}
			digit = input[pos++];
			remLength += ((digit & 0x7F) * multiplier);
			multiplier *= 128;
		} while ((digit & 0x80) != 0);
		
		var endPos = pos+remLength;
		if (endPos > input.length) {
		    return [null,startingPos];
		}

		var wireMessage = new WireMessage(type);
		switch(type) {
			case MESSAGE_TYPE.CONNACK:
				var connectAcknowledgeFlags = input[pos++];
				if (connectAcknowledgeFlags & 0x01)
					wireMessage.sessionPresent = true;
				wireMessage.returnCode = input[pos++];
				break;
			
			case MESSAGE_TYPE.PUBLISH:     	    	
				var qos = (messageInfo >> 1) & 0x03;
							
				var len = readUint16(input, pos);
				pos += 2;
				var topicName = parseUTF8(input, pos, len);
				pos += len;
				// If QoS 1 or 2 there will be a messageIdentifier
				if (qos > 0) {
					wireMessage.messageIdentifier = readUint16(input, pos);
					pos += 2;
				}
				
				var message = new dolinaWS.MQTT.Message(input.subarray(pos, endPos));
				if ((messageInfo & 0x01) == 0x01) 
					message.retained = true;
				if ((messageInfo & 0x08) == 0x08)
					message.duplicate =  true;
				message.qos = qos;
				message.destinationName = topicName;
				wireMessage.payloadMessage = message;	
				break;
			
			case  MESSAGE_TYPE.PUBACK:
			case  MESSAGE_TYPE.PUBREC:	    
			case  MESSAGE_TYPE.PUBREL:    
			case  MESSAGE_TYPE.PUBCOMP:
			case  MESSAGE_TYPE.UNSUBACK:    	    	
				wireMessage.messageIdentifier = readUint16(input, pos);
				break;
				
			case  MESSAGE_TYPE.SUBACK:
				wireMessage.messageIdentifier = readUint16(input, pos);
				pos += 2;
				wireMessage.returnCode = input.subarray(pos, endPos);	
				break;
		
			default:
				;
		}
				
		return [wireMessage,endPos];	
	}

	function writeUint16(input, buffer, offset) {
		buffer[offset++] = input >> 8;      //MSB
		buffer[offset++] = input % 256;     //LSB 
		return offset;
	}	

	function writeString(input, utf8Length, buffer, offset) {
		offset = writeUint16(utf8Length, buffer, offset);
		stringToUTF8(input, buffer, offset);
		return offset + utf8Length;
	}	

	function readUint16(buffer, offset) {
		return 256*buffer[offset] + buffer[offset+1];
	}	

	/**
	 * Encodes an MQTT Multi-Byte Integer
	 * @private 
	 */
	function encodeMBI(number) {
		var output = new Array(1);
		var numBytes = 0;

		do {
			var digit = number % 128;
			number = number >> 7;
			if (number > 0) {
				digit |= 0x80;
			}
			output[numBytes++] = digit;
		} while ( (number > 0) && (numBytes<4) );

		return output;
	}

	/**
	 * Takes a String and calculates its length in bytes when encoded in UTF8.
	 * @private
	 */
	function UTF8Length(input) {
		var output = 0;
		for (var i = 0; i<input.length; i++) 
		{
			var charCode = input.charCodeAt(i);
				if (charCode > 0x7FF)
				   {
					  // Surrogate pair means its a 4 byte character
					  if (0xD800 <= charCode && charCode <= 0xDBFF)
						{
						  i++;
						  output++;
						}
				   output +=3;
				   }
			else if (charCode > 0x7F)
				output +=2;
			else
				output++;
		} 
		return output;
	}
	
	/**
	 * Takes a String and writes it into an array as UTF8 encoded bytes.
	 * @private
	 */
	function stringToUTF8(input, output, start) {
		var pos = start;
		for (var i = 0; i<input.length; i++) {
			var charCode = input.charCodeAt(i);
			
			// Check for a surrogate pair.
			if (0xD800 <= charCode && charCode <= 0xDBFF) {
				var lowCharCode = input.charCodeAt(++i);
				if (isNaN(lowCharCode)) {
					throw new Error(format(ERROR.MALFORMED_UNICODE, [charCode, lowCharCode]));
				}
				charCode = ((charCode - 0xD800)<<10) + (lowCharCode - 0xDC00) + 0x10000;
			
			}
			
			if (charCode <= 0x7F) {
				output[pos++] = charCode;
			} else if (charCode <= 0x7FF) {
				output[pos++] = charCode>>6  & 0x1F | 0xC0;
				output[pos++] = charCode     & 0x3F | 0x80;
			} else if (charCode <= 0xFFFF) {    				    
				output[pos++] = charCode>>12 & 0x0F | 0xE0;
				output[pos++] = charCode>>6  & 0x3F | 0x80;   
				output[pos++] = charCode     & 0x3F | 0x80;   
			} else {
				output[pos++] = charCode>>18 & 0x07 | 0xF0;
				output[pos++] = charCode>>12 & 0x3F | 0x80;
				output[pos++] = charCode>>6  & 0x3F | 0x80;
				output[pos++] = charCode     & 0x3F | 0x80;
			};
		} 
		return output;
	}
	
	function parseUTF8(input, offset, length) {
		var output = "";
		var utf16;
		var pos = offset;

		while (pos < offset+length)
		{
			var byte1 = input[pos++];
			if (byte1 < 128)
				utf16 = byte1;
			else 
			{
				var byte2 = input[pos++]-128;
				if (byte2 < 0) 
					throw new Error(format(ERROR.MALFORMED_UTF, [byte1.toString(16), byte2.toString(16),""]));
				if (byte1 < 0xE0)             // 2 byte character
					utf16 = 64*(byte1-0xC0) + byte2;
				else 
				{ 
					var byte3 = input[pos++]-128;
					if (byte3 < 0) 
						throw new Error(format(ERROR.MALFORMED_UTF, [byte1.toString(16), byte2.toString(16), byte3.toString(16)]));
					if (byte1 < 0xF0)        // 3 byte character
						utf16 = 4096*(byte1-0xE0) + 64*byte2 + byte3;
								else
								{
								   var byte4 = input[pos++]-128;
								   if (byte4 < 0) 
						throw new Error(format(ERROR.MALFORMED_UTF, [byte1.toString(16), byte2.toString(16), byte3.toString(16), byte4.toString(16)]));
								   if (byte1 < 0xF8)        // 4 byte character 
										   utf16 = 262144*(byte1-0xF0) + 4096*byte2 + 64*byte3 + byte4;
					   else                     // longer encodings are not supported  
						throw new Error(format(ERROR.MALFORMED_UTF, [byte1.toString(16), byte2.toString(16), byte3.toString(16), byte4.toString(16)]));
								}
				}
			}  

				if (utf16 > 0xFFFF)   // 4 byte character - express as a surrogate pair
				  {
					 utf16 -= 0x10000;
					 output += String.fromCharCode(0xD800 + (utf16 >> 10)); // lead character
					 utf16 = 0xDC00 + (utf16 & 0x3FF);  // trail character
				  }
			output += String.fromCharCode(utf16);
		}
		return output;
	}
	
	/** 
	 * Repeat keepalive requests, monitor responses.
	 * @ignore
	 */
	var Pinger = function(client, window, keepAliveInterval) { 
		this._client = client;        	
		this._window = window;
		this._keepAliveInterval = keepAliveInterval*1000;     	
		this.isReset = false;
		
		var pingReq = new WireMessage(MESSAGE_TYPE.PINGREQ).encode(); 
		
		var doTimeout = function (pinger) {
			return function () {
				return doPing.apply(pinger);
			};
		};
		
		/** @ignore */
		var doPing = function() { 
			if (!this.isReset) {
				this._client._trace("Pinger.doPing", "Timed out");
				this._client._disconnected( ERROR.PING_TIMEOUT.code , format(ERROR.PING_TIMEOUT));
			} else {
				this.isReset = false;
				this._client._trace("Pinger.doPing", "send PINGREQ");
				this._client.socket.send(pingReq); 
				this.timeout = this._window.setTimeout(doTimeout(this), this._keepAliveInterval);
			}
		}

		this.reset = function() {
			this.isReset = true;
			this._window.clearTimeout(this.timeout);
			if (this._keepAliveInterval > 0)
				this.timeout = setTimeout(doTimeout(this), this._keepAliveInterval);
		}

		this.cancel = function() {
			this._window.clearTimeout(this.timeout);
		}
	 }; 

	/**
	 * Monitor request completion.
	 * @ignore
	 */
	var Timeout = function(client, window, timeoutSeconds, action, args) {
		this._window = window;
		if (!timeoutSeconds)
			timeoutSeconds = 30;
		
		var doTimeout = function (action, client, args) {
			return function () {
				return action.apply(client, args);
			};
		};
		this.timeout = setTimeout(doTimeout(action, client, args), timeoutSeconds * 1000);
		
		this.cancel = function() {
			this._window.clearTimeout(this.timeout);
		}
	}; 
	
	/*
	 * Internal implementation of the Websockets MQTT V3.1 client.
	 * 
	 * @name Paho.MQTT.ClientImpl @constructor 
	 * @param {String} host the DNS nameof the webSocket host. 
	 * @param {Number} port the port number for that host.
	 * @param {String} clientId the MQ client identifier.
	 */
	var ClientImpl = function (uri, host, port, path, clientId) {
		// Check dependencies are satisfied in this browser.
		if (!("WebSocket" in global && global["WebSocket"] !== null)) {
			throw new Error(format(ERROR.UNSUPPORTED, ["WebSocket"]));
		}
		if (!("localStorage" in global && global["localStorage"] !== null)) {
			throw new Error(format(ERROR.UNSUPPORTED, ["localStorage"]));
		}
		if (!("ArrayBuffer" in global && global["ArrayBuffer"] !== null)) {
			throw new Error(format(ERROR.UNSUPPORTED, ["ArrayBuffer"]));
		}
		this._trace("dolinaWS.MQTT.Client", uri, host, port, path, clientId);

		this.host = host;
		this.port = port;
		this.path = path;
		this.uri = uri;
		this.clientId = clientId;

		// Local storagekeys are qualified with the following string.
		// The conditional inclusion of path in the key is for backward
		// compatibility to when the path was not configurable and assumed to
		// be /mqtt
		this._localKey=host+":"+port+(path!="/mqtt"?":"+path:"")+":"+clientId+":";

		// Create private instance-only message queue
		// Internal queue of messages to be sent, in sending order. 
		this._msg_queue = [];

		// Messages we have sent and are expecting a response for, indexed by their respective message ids. 
		this._sentMessages = {};

		// Messages we have received and acknowleged and are expecting a confirm message for
		// indexed by their respective message ids. 
		this._receivedMessages = {};

		// Internal list of callbacks to be executed when messages
		// have been successfully sent over web socket, e.g. disconnect
		// when it doesn't have to wait for ACK, just message is dispatched.
		this._notify_msg_sent = {};

		// Unique identifier for SEND messages, incrementing
		// counter as messages are sent.
		this._message_identifier = 1;
		
		// Used to determine the transmission sequence of stored sent messages.
		this._sequence = 0;
		

		// Load the local state, if any, from the saved version, only restore state relevant to this client.   	
		for (var key in localStorage)
			if (   key.indexOf("Sent:"+this._localKey) == 0  		    
				|| key.indexOf("Received:"+this._localKey) == 0)
			this.restore(key);
	};

	// Messaging Client public instance members. 
	ClientImpl.prototype.host;
	ClientImpl.prototype.port;
	ClientImpl.prototype.path;
	ClientImpl.prototype.uri;
	ClientImpl.prototype.clientId;

	// Messaging Client private instance members.
	ClientImpl.prototype.socket;
	/* true once we have received an acknowledgement to a CONNECT packet. */
	ClientImpl.prototype.connected = false;
	/* The largest message identifier allowed, may not be larger than 2**16 but 
	 * if set smaller reduces the maximum number of outbound messages allowed.
	 */ 
	ClientImpl.prototype.maxMessageIdentifier = 65536;
	ClientImpl.prototype.connectOptions;
	ClientImpl.prototype.hostIndex;
	ClientImpl.prototype.onConnectionLost;
	ClientImpl.prototype.onMessageDelivered;
	ClientImpl.prototype.onMessageArrived;
	ClientImpl.prototype.traceFunction;
	ClientImpl.prototype._msg_queue = null;
	ClientImpl.prototype._connectTimeout;
	/* The sendPinger monitors how long we allow before we send data to prove to the server that we are alive. */
	ClientImpl.prototype.sendPinger = null;
	/* The receivePinger monitors how long we allow before we require evidence that the server is alive. */
	ClientImpl.prototype.receivePinger = null;
	
	ClientImpl.prototype.receiveBuffer = null;
	
	ClientImpl.prototype._traceBuffer = null;
	ClientImpl.prototype._MAX_TRACE_ENTRIES = 100;

	ClientImpl.prototype.connect = function (connectOptions) {
		var connectOptionsMasked = this._traceMask(connectOptions, "password"); 
		this._trace("Client.connect", connectOptionsMasked, this.socket, this.connected);
		
		if (this.connected) 
			throw new Error(format(ERROR.INVALID_STATE, ["already connected"]));
		if (this.socket)
			throw new Error(format(ERROR.INVALID_STATE, ["already connected"]));
		
		this.connectOptions = connectOptions;
		
		if (connectOptions.uris) {
			this.hostIndex = 0;
			this._doConnect(connectOptions.uris[0]);  
		} else {
			this._doConnect(this.uri);  		
		}
		
	};

	ClientImpl.prototype.subscribe = function (filter, subscribeOptions) {
		this._trace("Client.subscribe", filter, subscribeOptions);
			  
		if (!this.connected)
			throw new Error(format(ERROR.INVALID_STATE, ["not connected"]));
		
		var wireMessage = new WireMessage(MESSAGE_TYPE.SUBSCRIBE);
		wireMessage.topics=[filter];
		if (subscribeOptions.qos != undefined)
			wireMessage.requestedQos = [subscribeOptions.qos];
		else 
			wireMessage.requestedQos = [0];
		
		if (subscribeOptions.onSuccess) {
			wireMessage.onSuccess = function(grantedQos) {subscribeOptions.onSuccess({invocationContext:subscribeOptions.invocationContext,grantedQos:grantedQos});};
		}

		if (subscribeOptions.onFailure) {
			wireMessage.onFailure = function(errorCode) {subscribeOptions.onFailure({invocationContext:subscribeOptions.invocationContext,errorCode:errorCode});};
		}

		if (subscribeOptions.timeout) {
			wireMessage.timeOut = new Timeout(this, window, subscribeOptions.timeout, subscribeOptions.onFailure
					, [{invocationContext:subscribeOptions.invocationContext, 
						errorCode:ERROR.SUBSCRIBE_TIMEOUT.code, 
						errorMessage:format(ERROR.SUBSCRIBE_TIMEOUT)}]);
		}
		
		// All subscriptions return a SUBACK. 
		this._requires_ack(wireMessage);
		this._schedule_message(wireMessage);
	};

	/** @ignore */
	ClientImpl.prototype.unsubscribe = function(filter, unsubscribeOptions) {  
		this._trace("Client.unsubscribe", filter, unsubscribeOptions);
		
		if (!this.connected)
		   throw new Error(format(ERROR.INVALID_STATE, ["not connected"]));
		
		var wireMessage = new WireMessage(MESSAGE_TYPE.UNSUBSCRIBE);
		wireMessage.topics = [filter];
		
		if (unsubscribeOptions.onSuccess) {
			wireMessage.callback = function() {unsubscribeOptions.onSuccess({invocationContext:unsubscribeOptions.invocationContext});};
		}
		if (unsubscribeOptions.timeout) {
			wireMessage.timeOut = new Timeout(this, window, unsubscribeOptions.timeout, unsubscribeOptions.onFailure
					, [{invocationContext:unsubscribeOptions.invocationContext,
						errorCode:ERROR.UNSUBSCRIBE_TIMEOUT.code,
						errorMessage:format(ERROR.UNSUBSCRIBE_TIMEOUT)}]);
		}
	 
		// All unsubscribes return a SUBACK.         
		this._requires_ack(wireMessage);
		this._schedule_message(wireMessage);
	};
	 
	ClientImpl.prototype.send = function (message) {
		this._trace("Client.send", message);

		if (!this.connected)
		   throw new Error(format(ERROR.INVALID_STATE, ["not connected"]));
		
		wireMessage = new WireMessage(MESSAGE_TYPE.PUBLISH);
		wireMessage.payloadMessage = message;
		
		if (message.qos > 0)
			this._requires_ack(wireMessage);
		else if (this.onMessageDelivered)
			this._notify_msg_sent[wireMessage] = this.onMessageDelivered(wireMessage.payloadMessage);
		this._schedule_message(wireMessage);
	};
	
	ClientImpl.prototype.disconnect = function () {
		this._trace("Client.disconnect");

		if (!this.socket)
			throw new Error(format(ERROR.INVALID_STATE, ["not connecting or connected"]));
		
		wireMessage = new WireMessage(MESSAGE_TYPE.DISCONNECT);

		// Run the disconnected call back as soon as the message has been sent,
		// in case of a failure later on in the disconnect processing.
		// as a consequence, the _disconected call back may be run several times.
		this._notify_msg_sent[wireMessage] = scope(this._disconnected, this);

		this._schedule_message(wireMessage);
	};
	
	ClientImpl.prototype.getTraceLog = function () {
		if ( this._traceBuffer !== null ) {
			this._trace("Client.getTraceLog", new Date());
			this._trace("Client.getTraceLog in flight messages", this._sentMessages.length);
			for (var key in this._sentMessages)
				this._trace("_sentMessages ",key, this._sentMessages[key]);
			for (var key in this._receivedMessages)
				this._trace("_receivedMessages ",key, this._receivedMessages[key]);
			
			return this._traceBuffer;
		}
	};
	
	ClientImpl.prototype.startTrace = function () {
		if ( this._traceBuffer === null ) {
			this._traceBuffer = [];
		}
		this._trace("Client.startTrace", new Date(), version);
	};
	
	ClientImpl.prototype.stopTrace = function () {
		delete this._traceBuffer;
	};

	ClientImpl.prototype._doConnect = function (wsurl) { 	        
		// When the socket is open, this client will send the CONNECT WireMessage using the saved parameters. 
		if (this.connectOptions.useSSL) {
		    var uriParts = wsurl.split(":");
		    uriParts[0] = "wss";
		    wsurl = uriParts.join(":");
		}
		this.connected = false;
		if (this.connectOptions.mqttVersion < 4) {
			this.socket = new WebSocket(wsurl, ["mqttv3.1"]);
		} else {
			this.socket = new WebSocket(wsurl, ["mqtt"]);
		}
		this.socket.binaryType = 'arraybuffer';
		
		this.socket.onopen = scope(this._on_socket_open, this);
		this.socket.onmessage = scope(this._on_socket_message, this);
		this.socket.onerror = scope(this._on_socket_error, this);
		this.socket.onclose = scope(this._on_socket_close, this);
		
		this.sendPinger = new Pinger(this, window, this.connectOptions.keepAliveInterval);
		this.receivePinger = new Pinger(this, window, this.connectOptions.keepAliveInterval);
		
		this._connectTimeout = new Timeout(this, window, this.connectOptions.timeout, this._disconnected,  [ERROR.CONNECT_TIMEOUT.code, format(ERROR.CONNECT_TIMEOUT)]);
	};

	
	// Schedule a new message to be sent over the WebSockets
	// connection. CONNECT messages cause WebSocket connection
	// to be started. All other messages are queued internally
	// until this has happened. When WS connection starts, process
	// all outstanding messages. 
	ClientImpl.prototype._schedule_message = function (message) {
		this._msg_queue.push(message);
		// Process outstanding messages in the queue if we have an  open socket, and have received CONNACK. 
		if (this.connected) {
			this._process_queue();
		}
	};

	ClientImpl.prototype.store = function(prefix, wireMessage) {
		var storedMessage = {type:wireMessage.type, messageIdentifier:wireMessage.messageIdentifier, version:1};
		
		switch(wireMessage.type) {
		  case MESSAGE_TYPE.PUBLISH:
			  if(wireMessage.pubRecReceived)
				  storedMessage.pubRecReceived = true;
			  
			  // Convert the payload to a hex string.
			  storedMessage.payloadMessage = {};
			  var hex = "";
			  var messageBytes = wireMessage.payloadMessage.payloadBytes;
			  for (var i=0; i<messageBytes.length; i++) {
				if (messageBytes[i] <= 0xF)
				  hex = hex+"0"+messageBytes[i].toString(16);
				else 
				  hex = hex+messageBytes[i].toString(16);
			  }
			  storedMessage.payloadMessage.payloadHex = hex;
			  
			  storedMessage.payloadMessage.qos = wireMessage.payloadMessage.qos;
			  storedMessage.payloadMessage.destinationName = wireMessage.payloadMessage.destinationName;
			  if (wireMessage.payloadMessage.duplicate) 
				  storedMessage.payloadMessage.duplicate = true;
			  if (wireMessage.payloadMessage.retained) 
				  storedMessage.payloadMessage.retained = true;	   
			  
			  // Add a sequence number to sent messages.
			  if ( prefix.indexOf("Sent:") == 0 ) {
				  if ( wireMessage.sequence === undefined )
					  wireMessage.sequence = ++this._sequence;
				  storedMessage.sequence = wireMessage.sequence;
			  }
			  break;    
			  
			default:
				throw Error(format(ERROR.INVALID_STORED_DATA, [key, storedMessage]));
		}
		localStorage.setItem(prefix+this._localKey+wireMessage.messageIdentifier, JSON.stringify(storedMessage));
	};
	
	ClientImpl.prototype.restore = function(key) {    	
		var value = localStorage.getItem(key);
		var storedMessage = JSON.parse(value);
		
		var wireMessage = new WireMessage(storedMessage.type, storedMessage);
		
		switch(storedMessage.type) {
		  case MESSAGE_TYPE.PUBLISH:
			  // Replace the payload message with a Message object.
			  var hex = storedMessage.payloadMessage.payloadHex;
			  var buffer = new ArrayBuffer((hex.length)/2);
			  var byteStream = new Uint8Array(buffer); 
			  var i = 0;
			  while (hex.length >= 2) { 
				  var x = parseInt(hex.substring(0, 2), 16);
				  hex = hex.substring(2, hex.length);
				  byteStream[i++] = x;
			  }
			  var payloadMessage = new dolinaWS.MQTT.Message(byteStream);
			  
			  payloadMessage.qos = storedMessage.payloadMessage.qos;
			  payloadMessage.destinationName = storedMessage.payloadMessage.destinationName;
			  if (storedMessage.payloadMessage.duplicate) 
				  payloadMessage.duplicate = true;
			  if (storedMessage.payloadMessage.retained) 
				  payloadMessage.retained = true;	 
			  wireMessage.payloadMessage = payloadMessage;
			  
			  break;    
			  
			default:
			  throw Error(format(ERROR.INVALID_STORED_DATA, [key, value]));
		}
							
		if (key.indexOf("Sent:"+this._localKey) == 0) {
			wireMessage.payloadMessage.duplicate = true;
			this._sentMessages[wireMessage.messageIdentifier] = wireMessage;    		    
		} else if (key.indexOf("Received:"+this._localKey) == 0) {
			this._receivedMessages[wireMessage.messageIdentifier] = wireMessage;
		}
	};
	
	ClientImpl.prototype._process_queue = function () {
		var message = null;
		// Process messages in order they were added
		var fifo = this._msg_queue.reverse();

		// Send all queued messages down socket connection
		while ((message = fifo.pop())) {
			this._socket_send(message);
			// Notify listeners that message was successfully sent
			if (this._notify_msg_sent[message]) {
				this._notify_msg_sent[message]();
				delete this._notify_msg_sent[message];
			}
		}
	};

	/**
	 * Expect an ACK response for this message. Add message to the set of in progress
	 * messages and set an unused identifier in this message.
	 * @ignore
	 */
	ClientImpl.prototype._requires_ack = function (wireMessage) {
		var messageCount = Object.keys(this._sentMessages).length;
		if (messageCount > this.maxMessageIdentifier)
			throw Error ("Too many messages:"+messageCount);

		while(this._sentMessages[this._message_identifier] !== undefined) {
			this._message_identifier++;
		}
		wireMessage.messageIdentifier = this._message_identifier;
		this._sentMessages[wireMessage.messageIdentifier] = wireMessage;
		if (wireMessage.type === MESSAGE_TYPE.PUBLISH) {
			this.store("Sent:", wireMessage);
		}
		if (this._message_identifier === this.maxMessageIdentifier) {
			this._message_identifier = 1;
		}
	};

	/** 
	 * Called when the underlying websocket has been opened.
	 * @ignore
	 */
	ClientImpl.prototype._on_socket_open = function () {      
		// Create the CONNECT message object.
		var wireMessage = new WireMessage(MESSAGE_TYPE.CONNECT, this.connectOptions); 
		wireMessage.clientId = this.clientId;
		this._socket_send(wireMessage);
	};

	/** 
	 * Called when the underlying websocket has received a complete packet.
	 * @ignore
	 */
	ClientImpl.prototype._on_socket_message = function (event) {
		this._trace("Client._on_socket_message", event.data);
		// Reset the receive ping timer, we now have evidence the server is alive.
		this.receivePinger.reset();
		var messages = this._deframeMessages(event.data);
		for (var i = 0; i < messages.length; i+=1) {
		    this._handleMessage(messages[i]);
		}
	}
	
	ClientImpl.prototype._deframeMessages = function(data) {
		var byteArray = new Uint8Array(data);
	    if (this.receiveBuffer) {
	        var newData = new Uint8Array(this.receiveBuffer.length+byteArray.length);
	        newData.set(this.receiveBuffer);
	        newData.set(byteArray,this.receiveBuffer.length);
	        byteArray = newData;
	        delete this.receiveBuffer;
	    }
		try {
		    var offset = 0;
		    var messages = [];
		    while(offset < byteArray.length) {
		        var result = decodeMessage(byteArray,offset);
		        var wireMessage = result[0];
		        offset = result[1];
		        if (wireMessage !== null) {
		            messages.push(wireMessage);
		        } else {
		            break;
		        }
		    }
		    if (offset < byteArray.length) {
		    	this.receiveBuffer = byteArray.subarray(offset);
		    }
		} catch (error) {
			this._disconnected(ERROR.INTERNAL_ERROR.code , format(ERROR.INTERNAL_ERROR, [error.message,error.stack.toString()]));
			return;
		}
		return messages;
	}
	
	ClientImpl.prototype._handleMessage = function(wireMessage) {
		
		this._trace("Client._handleMessage", wireMessage);

		try {
			switch(wireMessage.type) {
			case MESSAGE_TYPE.CONNACK:
				this._connectTimeout.cancel();
				
				// If we have started using clean session then clear up the local state.
				if (this.connectOptions.cleanSession) {
					for (var key in this._sentMessages) {	    		
						var sentMessage = this._sentMessages[key];
						localStorage.removeItem("Sent:"+this._localKey+sentMessage.messageIdentifier);
					}
					this._sentMessages = {};

					for (var key in this._receivedMessages) {
						var receivedMessage = this._receivedMessages[key];
						localStorage.removeItem("Received:"+this._localKey+receivedMessage.messageIdentifier);
					}
					this._receivedMessages = {};
				}
				// Client connected and ready for business.
				if (wireMessage.returnCode === 0) {
					this.connected = true;
					// Jump to the end of the list of uris and stop looking for a good host.
					if (this.connectOptions.uris)
						this.hostIndex = this.connectOptions.uris.length;
				} else {
					this._disconnected(ERROR.CONNACK_RETURNCODE.code , format(ERROR.CONNACK_RETURNCODE, [wireMessage.returnCode, CONNACK_RC[wireMessage.returnCode]]));
					break;
				}
				
				// Resend messages.
				var sequencedMessages = new Array();
				for (var msgId in this._sentMessages) {
					if (this._sentMessages.hasOwnProperty(msgId))
						sequencedMessages.push(this._sentMessages[msgId]);
				}
		  
				// Sort sentMessages into the original sent order.
				var sequencedMessages = sequencedMessages.sort(function(a,b) {return a.sequence - b.sequence;} );
				for (var i=0, len=sequencedMessages.length; i<len; i++) {
					var sentMessage = sequencedMessages[i];
					if (sentMessage.type == MESSAGE_TYPE.PUBLISH && sentMessage.pubRecReceived) {
						var pubRelMessage = new WireMessage(MESSAGE_TYPE.PUBREL, {messageIdentifier:sentMessage.messageIdentifier});
						this._schedule_message(pubRelMessage);
					} else {
						this._schedule_message(sentMessage);
					};
				}

				// Execute the connectOptions.onSuccess callback if there is one.
				if (this.connectOptions.onSuccess) {
					this.connectOptions.onSuccess({invocationContext:this.connectOptions.invocationContext});
				}

				// Process all queued messages now that the connection is established. 
				this._process_queue();
				break;
		
			case MESSAGE_TYPE.PUBLISH:
				this._receivePublish(wireMessage);
				break;

			case MESSAGE_TYPE.PUBACK:
				var sentMessage = this._sentMessages[wireMessage.messageIdentifier];
				 // If this is a re flow of a PUBACK after we have restarted receivedMessage will not exist.
				if (sentMessage) {
					delete this._sentMessages[wireMessage.messageIdentifier];
					localStorage.removeItem("Sent:"+this._localKey+wireMessage.messageIdentifier);
					if (this.onMessageDelivered)
						this.onMessageDelivered(sentMessage.payloadMessage);
				}
				break;
			
			case MESSAGE_TYPE.PUBREC:
				var sentMessage = this._sentMessages[wireMessage.messageIdentifier];
				// If this is a re flow of a PUBREC after we have restarted receivedMessage will not exist.
				if (sentMessage) {
					sentMessage.pubRecReceived = true;
					var pubRelMessage = new WireMessage(MESSAGE_TYPE.PUBREL, {messageIdentifier:wireMessage.messageIdentifier});
					this.store("Sent:", sentMessage);
					this._schedule_message(pubRelMessage);
				}
				break;
								
			case MESSAGE_TYPE.PUBREL:
				var receivedMessage = this._receivedMessages[wireMessage.messageIdentifier];
				localStorage.removeItem("Received:"+this._localKey+wireMessage.messageIdentifier);
				// If this is a re flow of a PUBREL after we have restarted receivedMessage will not exist.
				if (receivedMessage) {
					this._receiveMessage(receivedMessage);
					delete this._receivedMessages[wireMessage.messageIdentifier];
				}
				// Always flow PubComp, we may have previously flowed PubComp but the server lost it and restarted.
				var pubCompMessage = new WireMessage(MESSAGE_TYPE.PUBCOMP, {messageIdentifier:wireMessage.messageIdentifier});
				this._schedule_message(pubCompMessage);                    
				break;

			case MESSAGE_TYPE.PUBCOMP: 
				var sentMessage = this._sentMessages[wireMessage.messageIdentifier];
				delete this._sentMessages[wireMessage.messageIdentifier];
				localStorage.removeItem("Sent:"+this._localKey+wireMessage.messageIdentifier);
				if (this.onMessageDelivered)
					this.onMessageDelivered(sentMessage.payloadMessage);
				break;
				
			case MESSAGE_TYPE.SUBACK:
				var sentMessage = this._sentMessages[wireMessage.messageIdentifier];
				if (sentMessage) {
					if(sentMessage.timeOut)
						sentMessage.timeOut.cancel();
					// This will need to be fixed when we add multiple topic support
          			if (wireMessage.returnCode[0] === 0x80) {
						if (sentMessage.onFailure) {
							sentMessage.onFailure(wireMessage.returnCode);
						} 
					} else if (sentMessage.onSuccess) {
						sentMessage.onSuccess(wireMessage.returnCode);
					}
					delete this._sentMessages[wireMessage.messageIdentifier];
				}
				break;
				
			case MESSAGE_TYPE.UNSUBACK:
				var sentMessage = this._sentMessages[wireMessage.messageIdentifier];
				if (sentMessage) { 
					if (sentMessage.timeOut)
						sentMessage.timeOut.cancel();
					if (sentMessage.callback) {
						sentMessage.callback();
					}
					delete this._sentMessages[wireMessage.messageIdentifier];
				}

				break;
				
			case MESSAGE_TYPE.PINGRESP:
				/* The sendPinger or receivePinger may have sent a ping, the receivePinger has already been reset. */
				this.sendPinger.reset();
				break;
				
			case MESSAGE_TYPE.DISCONNECT:
				// Clients do not expect to receive disconnect packets.
				this._disconnected(ERROR.INVALID_MQTT_MESSAGE_TYPE.code , format(ERROR.INVALID_MQTT_MESSAGE_TYPE, [wireMessage.type]));
				break;

			default:
				this._disconnected(ERROR.INVALID_MQTT_MESSAGE_TYPE.code , format(ERROR.INVALID_MQTT_MESSAGE_TYPE, [wireMessage.type]));
			};
		} catch (error) {
			this._disconnected(ERROR.INTERNAL_ERROR.code , format(ERROR.INTERNAL_ERROR, [error.message,error.stack.toString()]));
			return;
		}
	};
	
	/** @ignore */
	ClientImpl.prototype._on_socket_error = function (error) {
		this._disconnected(ERROR.SOCKET_ERROR.code , format(ERROR.SOCKET_ERROR, [error.data]));
	};

	/** @ignore */
	ClientImpl.prototype._on_socket_close = function () {
		this._disconnected(ERROR.SOCKET_CLOSE.code , format(ERROR.SOCKET_CLOSE));
	};

	/** @ignore */
	ClientImpl.prototype._socket_send = function (wireMessage) {
		
		if (wireMessage.type == 1) {
			var wireMessageMasked = this._traceMask(wireMessage, "password"); 
			this._trace("Client._socket_send", wireMessageMasked);
		}
		else this._trace("Client._socket_send", wireMessage);
		
		this.socket.send(wireMessage.encode());
		/* We have proved to the server we are alive. */
		this.sendPinger.reset();
	};
	
	/** @ignore */
	ClientImpl.prototype._receivePublish = function (wireMessage) {
		switch(wireMessage.payloadMessage.qos) {
			case "undefined":
			case 0:
				this._receiveMessage(wireMessage);
				break;

			case 1:
				var pubAckMessage = new WireMessage(MESSAGE_TYPE.PUBACK, {messageIdentifier:wireMessage.messageIdentifier});
				this._schedule_message(pubAckMessage);
				this._receiveMessage(wireMessage);
				break;

			case 2:
				this._receivedMessages[wireMessage.messageIdentifier] = wireMessage;
				this.store("Received:", wireMessage);
				var pubRecMessage = new WireMessage(MESSAGE_TYPE.PUBREC, {messageIdentifier:wireMessage.messageIdentifier});
				this._schedule_message(pubRecMessage);

				break;

			default:
				throw Error("Invaild qos="+wireMmessage.payloadMessage.qos);
		};
	};

	/** @ignore */
	ClientImpl.prototype._receiveMessage = function (wireMessage) {
		if (this.onMessageArrived) {
			this.onMessageArrived(wireMessage.payloadMessage);
		}
	};

	/**
	 * Client has disconnected either at its own request or because the server
	 * or network disconnected it. Remove all non-durable state.
	 * @param {errorCode} [number] the error number.
	 * @param {errorText} [string] the error text.
	 * @ignore
	 */
	ClientImpl.prototype._disconnected = function (errorCode, errorText) {
		this._trace("Client._disconnected", errorCode, errorText);
		
		this.sendPinger.cancel();
		this.receivePinger.cancel();
		if (this._connectTimeout)
			this._connectTimeout.cancel();
		// Clear message buffers.
		this._msg_queue = [];
		this._notify_msg_sent = {};
	   
		if (this.socket) {
			// Cancel all socket callbacks so that they cannot be driven again by this socket.
			this.socket.onopen = null;
			this.socket.onmessage = null;
			this.socket.onerror = null;
			this.socket.onclose = null;
			if (this.socket.readyState === 1)
				this.socket.close();
			delete this.socket;           
		}
		
		if (this.connectOptions.uris && this.hostIndex < this.connectOptions.uris.length-1) {
			// Try the next host.
			this.hostIndex++;
			this._doConnect(this.connectOptions.uris[this.hostIndex]);
		
		} else {
		
			if (errorCode === undefined) {
				errorCode = ERROR.OK.code;
				errorText = format(ERROR.OK);
			}
			
			// Run any application callbacks last as they may attempt to reconnect and hence create a new socket.
			if (this.connected) {
				this.connected = false;
				// Execute the connectionLostCallback if there is one, and we were connected.       
				if (this.onConnectionLost)
					this.onConnectionLost({errorCode:errorCode, errorMessage:errorText});      	
			} else {
				// Otherwise we never had a connection, so indicate that the connect has failed.
				if (this.connectOptions.mqttVersion === 4 && this.connectOptions.mqttVersionExplicit === false) {
					this._trace("Failed to connect V4, dropping back to V3")
					this.connectOptions.mqttVersion = 3;
					if (this.connectOptions.uris) {
						this.hostIndex = 0;
						this._doConnect(this.connectOptions.uris[0]);  
					} else {
						this._doConnect(this.uri);
					}	
				} else if(this.connectOptions.onFailure) {
					this.connectOptions.onFailure({invocationContext:this.connectOptions.invocationContext, errorCode:errorCode, errorMessage:errorText});
				}
			}
		}
	};

	/** @ignore */
	ClientImpl.prototype._trace = function () {
		// Pass trace message back to client's callback function
		if (this.traceFunction) {
			for (var i in arguments)
			{	
				if (typeof arguments[i] !== "undefined")
					arguments[i] = JSON.stringify(arguments[i]);
			}
			var record = Array.prototype.slice.call(arguments).join("");
			this.traceFunction ({severity: "Debug", message: record	});
		}

		//buffer style trace
		if ( this._traceBuffer !== null ) {  
			for (var i = 0, max = arguments.length; i < max; i++) {
				if ( this._traceBuffer.length == this._MAX_TRACE_ENTRIES ) {    
					this._traceBuffer.shift();              
				}
				if (i === 0) this._traceBuffer.push(arguments[i]);
				else if (typeof arguments[i] === "undefined" ) this._traceBuffer.push(arguments[i]);
				else this._traceBuffer.push("  "+JSON.stringify(arguments[i]));
		   };
		};
	};
	
	/** @ignore */
	ClientImpl.prototype._traceMask = function (traceObject, masked) {
		var traceObjectMasked = {};
		for (var attr in traceObject) {
			if (traceObject.hasOwnProperty(attr)) {
				if (attr == masked) 
					traceObjectMasked[attr] = "******";
				else
					traceObjectMasked[attr] = traceObject[attr];
			} 
		}
		return traceObjectMasked;
	};

	// ------------------------------------------------------------------------
	// Public Programming interface.
	// ------------------------------------------------------------------------
	
	/** 
	 * The JavaScript application communicates to the server using a {@link Paho.MQTT.Client} object. 
	 * <p>
	 * Most applications will create just one Client object and then call its connect() method,
	 * however applications can create more than one Client object if they wish. 
	 * In this case the combination of host, port and clientId attributes must be different for each Client object.
	 * <p>
	 * The send, subscribe and unsubscribe methods are implemented as asynchronous JavaScript methods 
	 * (even though the underlying protocol exchange might be synchronous in nature). 
	 * This means they signal their completion by calling back to the application, 
	 * via Success or Failure callback functions provided by the application on the method in question. 
	 * Such callbacks are called at most once per method invocation and do not persist beyond the lifetime 
	 * of the script that made the invocation.
	 * <p>
	 * In contrast there are some callback functions, most notably <i>onMessageArrived</i>, 
	 * that are defined on the {@link Paho.MQTT.Client} object.  
	 * These may get called multiple times, and aren't directly related to specific method invocations made by the client. 
	 *
	 * @name Paho.MQTT.Client    
	 * 
	 * @constructor
	 *  
	 * @param {string} host - the address of the messaging server, as a fully qualified WebSocket URI, as a DNS name or dotted decimal IP address.
	 * @param {number} port - the port number to connect to - only required if host is not a URI
	 * @param {string} path - the path on the host to connect to - only used if host is not a URI. Default: '/mqtt'.
	 * @param {string} clientId - the Messaging client identifier, between 1 and 23 characters in length.
	 * 
	 * @property {string} host - <i>read only</i> the server's DNS hostname or dotted decimal IP address.
	 * @property {number} port - <i>read only</i> the server's port.
	 * @property {string} path - <i>read only</i> the server's path.
	 * @property {string} clientId - <i>read only</i> used when connecting to the server.
	 * @property {function} onConnectionLost - called when a connection has been lost. 
	 *                            after a connect() method has succeeded.
	 *                            Establish the call back used when a connection has been lost. The connection may be
	 *                            lost because the client initiates a disconnect or because the server or network 
	 *                            cause the client to be disconnected. The disconnect call back may be called without 
	 *                            the connectionComplete call back being invoked if, for example the client fails to 
	 *                            connect.
	 *                            A single response object parameter is passed to the onConnectionLost callback containing the following fields:
	 *                            <ol>   
	 *                            <li>errorCode
	 *                            <li>errorMessage       
	 *                            </ol>
	 * @property {function} onMessageDelivered called when a message has been delivered. 
	 *                            All processing that this Client will ever do has been completed. So, for example,
	 *                            in the case of a Qos=2 message sent by this client, the PubComp flow has been received from the server
	 *                            and the message has been removed from persistent storage before this callback is invoked. 
	 *                            Parameters passed to the onMessageDelivered callback are:
	 *                            <ol>   
	 *                            <li>{@link Paho.MQTT.Message} that was delivered.
	 *                            </ol>    
	 * @property {function} onMessageArrived called when a message has arrived in this Paho.MQTT.client. 
	 *                            Parameters passed to the onMessageArrived callback are:
	 *                            <ol>   
	 *                            <li>{@link Paho.MQTT.Message} that has arrived.
	 *                            </ol>    
	 */
	var Client = function (host, port, path, clientId) {
	    
	    var uri;
	    
		if (typeof host !== "string")
			throw new Error(format(ERROR.INVALID_TYPE, [typeof host, "host"]));
	    
	    if (arguments.length == 2) {
	        // host: must be full ws:// uri
	        // port: clientId
	        clientId = port;
	        uri = host;
	        var match = uri.match(/^(wss?):\/\/((\[(.+)\])|([^\/]+?))(:(\d+))?(\/.*)$/);
	        if (match) {
	            host = match[4]||match[2];
	            port = parseInt(match[7]);
	            path = match[8];
	        } else {
	            throw new Error(format(ERROR.INVALID_ARGUMENT,[host,"host"]));
	        }
	    } else {
	        if (arguments.length == 3) {
				clientId = path;
				path = "/mqtt";
			}
			if (typeof port !== "number" || port < 0)
				throw new Error(format(ERROR.INVALID_TYPE, [typeof port, "port"]));
			if (typeof path !== "string")
				throw new Error(format(ERROR.INVALID_TYPE, [typeof path, "path"]));
			
			var ipv6AddSBracket = (host.indexOf(":") != -1 && host.slice(0,1) != "[" && host.slice(-1) != "]");
			uri = "ws://"+(ipv6AddSBracket?"["+host+"]":host)+":"+port+path;
		}

		var clientIdLength = 0;
		for (var i = 0; i<clientId.length; i++) {
			var charCode = clientId.charCodeAt(i);                   
			if (0xD800 <= charCode && charCode <= 0xDBFF)  {    			
				 i++; // Surrogate pair.
			}   		   
			clientIdLength++;
		}     	   	
		if (typeof clientId !== "string" || clientIdLength > 65535)
			throw new Error(format(ERROR.INVALID_ARGUMENT, [clientId, "clientId"])); 
		
		var client = new ClientImpl(uri, host, port, path, clientId);
		this._getHost =  function() { return host; };
		this._setHost = function() { throw new Error(format(ERROR.UNSUPPORTED_OPERATION)); };
			
		this._getPort = function() { return port; };
		this._setPort = function() { throw new Error(format(ERROR.UNSUPPORTED_OPERATION)); };

		this._getPath = function() { return path; };
		this._setPath = function() { throw new Error(format(ERROR.UNSUPPORTED_OPERATION)); };

		this._getURI = function() { return uri; };
		this._setURI = function() { throw new Error(format(ERROR.UNSUPPORTED_OPERATION)); };
		
		this._getClientId = function() { return client.clientId; };
		this._setClientId = function() { throw new Error(format(ERROR.UNSUPPORTED_OPERATION)); };
		
		this._getOnConnectionLost = function() { return client.onConnectionLost; };
		this._setOnConnectionLost = function(newOnConnectionLost) { 
			if (typeof newOnConnectionLost === "function")
				client.onConnectionLost = newOnConnectionLost;
			else 
				throw new Error(format(ERROR.INVALID_TYPE, [typeof newOnConnectionLost, "onConnectionLost"]));
		};

		this._getOnMessageDelivered = function() { return client.onMessageDelivered; };
		this._setOnMessageDelivered = function(newOnMessageDelivered) { 
			if (typeof newOnMessageDelivered === "function")
				client.onMessageDelivered = newOnMessageDelivered;
			else 
				throw new Error(format(ERROR.INVALID_TYPE, [typeof newOnMessageDelivered, "onMessageDelivered"]));
		};
	   
		this._getOnMessageArrived = function() { return client.onMessageArrived; };
		this._setOnMessageArrived = function(newOnMessageArrived) { 
			if (typeof newOnMessageArrived === "function")
				client.onMessageArrived = newOnMessageArrived;
			else 
				throw new Error(format(ERROR.INVALID_TYPE, [typeof newOnMessageArrived, "onMessageArrived"]));
		};

		this._getTrace = function() { return client.traceFunction; };
		this._setTrace = function(trace) {
			if(typeof trace === "function"){
				client.traceFunction = trace;
			}else{
				throw new Error(format(ERROR.INVALID_TYPE, [typeof trace, "onTrace"]));
			}
		};
		
		/** 
		 * Connect this Messaging client to its server. 
		 * 
		 * @name Paho.MQTT.Client#connect
		 * @function
		 * @param {Object} connectOptions - attributes used with the connection. 
		 * @param {number} connectOptions.timeout - If the connect has not succeeded within this 
		 *                    number of seconds, it is deemed to have failed.
		 *                    The default is 30 seconds.
		 * @param {string} connectOptions.userName - Authentication username for this connection.
		 * @param {string} connectOptions.password - Authentication password for this connection.
		 * @param {Paho.MQTT.Message} connectOptions.willMessage - sent by the server when the client
		 *                    disconnects abnormally.
		 * @param {Number} connectOptions.keepAliveInterval - the server disconnects this client if
		 *                    there is no activity for this number of seconds.
		 *                    The default value of 60 seconds is assumed if not set.
		 * @param {boolean} connectOptions.cleanSession - if true(default) the client and server 
		 *                    persistent state is deleted on successful connect.
		 * @param {boolean} connectOptions.useSSL - if present and true, use an SSL Websocket connection.
		 * @param {object} connectOptions.invocationContext - passed to the onSuccess callback or onFailure callback.
		 * @param {function} connectOptions.onSuccess - called when the connect acknowledgement 
		 *                    has been received from the server.
		 * A single response object parameter is passed to the onSuccess callback containing the following fields:
		 * <ol>
		 * <li>invocationContext as passed in to the onSuccess method in the connectOptions.       
		 * </ol>
		 * @config {function} [onFailure] called when the connect request has failed or timed out.
		 * A single response object parameter is passed to the onFailure callback containing the following fields:
		 * <ol>
		 * <li>invocationContext as passed in to the onFailure method in the connectOptions.       
		 * <li>errorCode a number indicating the nature of the error.
		 * <li>errorMessage text describing the error.      
		 * </ol>
		 * @config {Array} [hosts] If present this contains either a set of hostnames or fully qualified
		 * WebSocket URIs (ws://example.com:1883/mqtt), that are tried in order in place 
		 * of the host and port paramater on the construtor. The hosts are tried one at at time in order until
		 * one of then succeeds.
		 * @config {Array} [ports] If present the set of ports matching the hosts. If hosts contains URIs, this property
		 * is not used.
		 * @throws {InvalidState} if the client is not in disconnected state. The client must have received connectionLost
		 * or disconnected before calling connect for a second or subsequent time.
		 */
		this.connect = function (connectOptions) {
			connectOptions = connectOptions || {} ;
			validate(connectOptions,  {timeout:"number",
									   userName:"string", 
									   password:"string", 
									   willMessage:"object", 
									   keepAliveInterval:"number", 
									   cleanSession:"boolean", 
									   useSSL:"boolean",
									   invocationContext:"object", 
									   onSuccess:"function", 
									   onFailure:"function",
									   hosts:"object",
									   ports:"object",
									   mqttVersion:"number"});
			
			// If no keep alive interval is set, assume 60 seconds.
			if (connectOptions.keepAliveInterval === undefined)
				connectOptions.keepAliveInterval = 60;

			if (connectOptions.mqttVersion > 4 || connectOptions.mqttVersion < 3) {
				throw new Error(format(ERROR.INVALID_ARGUMENT, [connectOptions.mqttVersion, "connectOptions.mqttVersion"]));
			}

			if (connectOptions.mqttVersion === undefined) {
				connectOptions.mqttVersionExplicit = false;
				connectOptions.mqttVersion = 4;
			} else {
				connectOptions.mqttVersionExplicit = true;
			}

			//Check that if password is set, so is username
			if (connectOptions.password === undefined && connectOptions.userName !== undefined)
				throw new Error(format(ERROR.INVALID_ARGUMENT, [connectOptions.password, "connectOptions.password"]))

			if (connectOptions.willMessage) {
				if (!(connectOptions.willMessage instanceof Message))
					throw new Error(format(ERROR.INVALID_TYPE, [connectOptions.willMessage, "connectOptions.willMessage"]));
				// The will message must have a payload that can be represented as a string.
				// Cause the willMessage to throw an exception if this is not the case.
				connectOptions.willMessage.stringPayload;
				
				if (typeof connectOptions.willMessage.destinationName === "undefined")
					throw new Error(format(ERROR.INVALID_TYPE, [typeof connectOptions.willMessage.destinationName, "connectOptions.willMessage.destinationName"]));
			}
			if (typeof connectOptions.cleanSession === "undefined")
				connectOptions.cleanSession = true;
			if (connectOptions.hosts) {
			    
				if (!(connectOptions.hosts instanceof Array) )
					throw new Error(format(ERROR.INVALID_ARGUMENT, [connectOptions.hosts, "connectOptions.hosts"]));
				if (connectOptions.hosts.length <1 )
					throw new Error(format(ERROR.INVALID_ARGUMENT, [connectOptions.hosts, "connectOptions.hosts"]));
				
				var usingURIs = false;
				for (var i = 0; i<connectOptions.hosts.length; i++) {
					if (typeof connectOptions.hosts[i] !== "string")
						throw new Error(format(ERROR.INVALID_TYPE, [typeof connectOptions.hosts[i], "connectOptions.hosts["+i+"]"]));
					if (/^(wss?):\/\/((\[(.+)\])|([^\/]+?))(:(\d+))?(\/.*)$/.test(connectOptions.hosts[i])) {
						if (i == 0) {
							usingURIs = true;
						} else if (!usingURIs) {
							throw new Error(format(ERROR.INVALID_ARGUMENT, [connectOptions.hosts[i], "connectOptions.hosts["+i+"]"]));
						}
					} else if (usingURIs) {
						throw new Error(format(ERROR.INVALID_ARGUMENT, [connectOptions.hosts[i], "connectOptions.hosts["+i+"]"]));
					}
				}
				
				if (!usingURIs) {
					if (!connectOptions.ports)
						throw new Error(format(ERROR.INVALID_ARGUMENT, [connectOptions.ports, "connectOptions.ports"]));
					if (!(connectOptions.ports instanceof Array) )
						throw new Error(format(ERROR.INVALID_ARGUMENT, [connectOptions.ports, "connectOptions.ports"]));
					if (connectOptions.hosts.length != connectOptions.ports.length)
						throw new Error(format(ERROR.INVALID_ARGUMENT, [connectOptions.ports, "connectOptions.ports"]));
					
					connectOptions.uris = [];
					
					for (var i = 0; i<connectOptions.hosts.length; i++) {
						if (typeof connectOptions.ports[i] !== "number" || connectOptions.ports[i] < 0)
							throw new Error(format(ERROR.INVALID_TYPE, [typeof connectOptions.ports[i], "connectOptions.ports["+i+"]"]));
						var host = connectOptions.hosts[i];
						var port = connectOptions.ports[i];
						
						var ipv6 = (host.indexOf(":") != -1);
						uri = "ws://"+(ipv6?"["+host+"]":host)+":"+port+path;
						connectOptions.uris.push(uri);
					}
				} else {
					connectOptions.uris = connectOptions.hosts;
				}
			}
			
			client.connect(connectOptions);
		};
	 
		/** 
		 * Subscribe for messages, request receipt of a copy of messages sent to the destinations described by the filter.
		 * 
		 * @name Paho.MQTT.Client#subscribe
		 * @function
		 * @param {string} filter describing the destinations to receive messages from.
		 * <br>
		 * @param {object} subscribeOptions - used to control the subscription
		 *
		 * @param {number} subscribeOptions.qos - the maiximum qos of any publications sent 
		 *                                  as a result of making this subscription.
		 * @param {object} subscribeOptions.invocationContext - passed to the onSuccess callback 
		 *                                  or onFailure callback.
		 * @param {function} subscribeOptions.onSuccess - called when the subscribe acknowledgement
		 *                                  has been received from the server.
		 *                                  A single response object parameter is passed to the onSuccess callback containing the following fields:
		 *                                  <ol>
		 *                                  <li>invocationContext if set in the subscribeOptions.       
		 *                                  </ol>
		 * @param {function} subscribeOptions.onFailure - called when the subscribe request has failed or timed out.
		 *                                  A single response object parameter is passed to the onFailure callback containing the following fields:
		 *                                  <ol>
		 *                                  <li>invocationContext - if set in the subscribeOptions.       
		 *                                  <li>errorCode - a number indicating the nature of the error.
		 *                                  <li>errorMessage - text describing the error.      
		 *                                  </ol>
		 * @param {number} subscribeOptions.timeout - which, if present, determines the number of
		 *                                  seconds after which the onFailure calback is called.
		 *                                  The presence of a timeout does not prevent the onSuccess
		 *                                  callback from being called when the subscribe completes.         
		 * @throws {InvalidState} if the client is not in connected state.
		 */
		this.subscribe = function (filter, subscribeOptions) {
			if (typeof filter !== "string")
				throw new Error("Invalid argument:"+filter);
			subscribeOptions = subscribeOptions || {} ;
			validate(subscribeOptions,  {qos:"number", 
										 invocationContext:"object", 
										 onSuccess:"function", 
										 onFailure:"function",
										 timeout:"number"
										});
			if (subscribeOptions.timeout && !subscribeOptions.onFailure)
				throw new Error("subscribeOptions.timeout specified with no onFailure callback.");
			if (typeof subscribeOptions.qos !== "undefined" 
				&& !(subscribeOptions.qos === 0 || subscribeOptions.qos === 1 || subscribeOptions.qos === 2 ))
				throw new Error(format(ERROR.INVALID_ARGUMENT, [subscribeOptions.qos, "subscribeOptions.qos"]));
			client.subscribe(filter, subscribeOptions);
		};

		/**
		 * Unsubscribe for messages, stop receiving messages sent to destinations described by the filter.
		 * 
		 * @name Paho.MQTT.Client#unsubscribe
		 * @function
		 * @param {string} filter - describing the destinations to receive messages from.
		 * @param {object} unsubscribeOptions - used to control the subscription
		 * @param {object} unsubscribeOptions.invocationContext - passed to the onSuccess callback 
		                                      or onFailure callback.
		 * @param {function} unsubscribeOptions.onSuccess - called when the unsubscribe acknowledgement has been received from the server.
		 *                                    A single response object parameter is passed to the 
		 *                                    onSuccess callback containing the following fields:
		 *                                    <ol>
		 *                                    <li>invocationContext - if set in the unsubscribeOptions.     
		 *                                    </ol>
		 * @param {function} unsubscribeOptions.onFailure called when the unsubscribe request has failed or timed out.
		 *                                    A single response object parameter is passed to the onFailure callback containing the following fields:
		 *                                    <ol>
		 *                                    <li>invocationContext - if set in the unsubscribeOptions.       
		 *                                    <li>errorCode - a number indicating the nature of the error.
		 *                                    <li>errorMessage - text describing the error.      
		 *                                    </ol>
		 * @param {number} unsubscribeOptions.timeout - which, if present, determines the number of seconds
		 *                                    after which the onFailure callback is called. The presence of
		 *                                    a timeout does not prevent the onSuccess callback from being
		 *                                    called when the unsubscribe completes
		 * @throws {InvalidState} if the client is not in connected state.
		 */
		this.unsubscribe = function (filter, unsubscribeOptions) {
			if (typeof filter !== "string")
				throw new Error("Invalid argument:"+filter);
			unsubscribeOptions = unsubscribeOptions || {} ;
			validate(unsubscribeOptions,  {invocationContext:"object", 
										   onSuccess:"function", 
										   onFailure:"function",
										   timeout:"number"
										  });
			if (unsubscribeOptions.timeout && !unsubscribeOptions.onFailure)
				throw new Error("unsubscribeOptions.timeout specified with no onFailure callback.");
			client.unsubscribe(filter, unsubscribeOptions);
		};

		/**
		 * Send a message to the consumers of the destination in the Message.
		 * 
		 * @name Paho.MQTT.Client#send
		 * @function 
		 * @param {string|Paho.MQTT.Message} topic - <b>mandatory</b> The name of the destination to which the message is to be sent. 
		 * 					   - If it is the only parameter, used as Paho.MQTT.Message object.
		 * @param {String|ArrayBuffer} payload - The message data to be sent. 
		 * @param {number} qos The Quality of Service used to deliver the message.
		 * 		<dl>
		 * 			<dt>0 Best effort (default).
		 *     			<dt>1 At least once.
		 *     			<dt>2 Exactly once.     
		 * 		</dl>
		 * @param {Boolean} retained If true, the message is to be retained by the server and delivered 
		 *                     to both current and future subscriptions.
		 *                     If false the server only delivers the message to current subscribers, this is the default for new Messages. 
		 *                     A received message has the retained boolean set to true if the message was published 
		 *                     with the retained boolean set to true
		 *                     and the subscrption was made after the message has been published. 
		 * @throws {InvalidState} if the client is not connected.
		 */   
		this.send = function (topic,payload,qos,retained) {   
			var message ;  
			
			if(arguments.length == 0){
				throw new Error("Invalid argument."+"length");

			}else if(arguments.length == 1) {

				if (!(topic instanceof Message) && (typeof topic !== "string"))
					throw new Error("Invalid argument:"+ typeof topic);

				message = topic;
				if (typeof message.destinationName === "undefined")
					throw new Error(format(ERROR.INVALID_ARGUMENT,[message.destinationName,"Message.destinationName"]));
				client.send(message); 

			}else {
				//parameter checking in Message object 
				message = new Message(payload);
				message.destinationName = topic;
				if(arguments.length >= 3)
					message.qos = qos;
				if(arguments.length >= 4)
					message.retained = retained;
				client.send(message); 
			}
		};
		
		/** 
		 * Normal disconnect of this Messaging client from its server.
		 * 
		 * @name Paho.MQTT.Client#disconnect
		 * @function
		 * @throws {InvalidState} if the client is already disconnected.     
		 */
		this.disconnect = function () {
			client.disconnect();
		};
		
		/** 
		 * Get the contents of the trace log.
		 * 
		 * @name Paho.MQTT.Client#getTraceLog
		 * @function
		 * @return {Object[]} tracebuffer containing the time ordered trace records.
		 */
		this.getTraceLog = function () {
			return client.getTraceLog();
		}
		
		/** 
		 * Start tracing.
		 * 
		 * @name Paho.MQTT.Client#startTrace
		 * @function
		 */
		this.startTrace = function () {
			client.startTrace();
		};
		
		/** 
		 * Stop tracing.
		 * 
		 * @name Paho.MQTT.Client#stopTrace
		 * @function
		 */
		this.stopTrace = function () {
			client.stopTrace();
		};

		this.isConnected = function() {
			return client.connected;
		};
	};

	Client.prototype = {
		get host() { return this._getHost(); },
		set host(newHost) { this._setHost(newHost); },
			
		get port() { return this._getPort(); },
		set port(newPort) { this._setPort(newPort); },

		get path() { return this._getPath(); },
		set path(newPath) { this._setPath(newPath); },
			
		get clientId() { return this._getClientId(); },
		set clientId(newClientId) { this._setClientId(newClientId); },

		get onConnectionLost() { return this._getOnConnectionLost(); },
		set onConnectionLost(newOnConnectionLost) { this._setOnConnectionLost(newOnConnectionLost); },

		get onMessageDelivered() { return this._getOnMessageDelivered(); },
		set onMessageDelivered(newOnMessageDelivered) { this._setOnMessageDelivered(newOnMessageDelivered); },
		
		get onMessageArrived() { return this._getOnMessageArrived(); },
		set onMessageArrived(newOnMessageArrived) { this._setOnMessageArrived(newOnMessageArrived); },

		get trace() { return this._getTrace(); },
		set trace(newTraceFunction) { this._setTrace(newTraceFunction); }	

	};
	
	/** 
	 * An application message, sent or received.
	 * <p>
	 * All attributes may be null, which implies the default values.
	 * 
	 * @name Paho.MQTT.Message
	 * @constructor
	 * @param {String|ArrayBuffer} payload The message data to be sent.
	 * <p>
	 * @property {string} payloadString <i>read only</i> The payload as a string if the payload consists of valid UTF-8 characters.
	 * @property {ArrayBuffer} payloadBytes <i>read only</i> The payload as an ArrayBuffer.
	 * <p>
	 * @property {string} destinationName <b>mandatory</b> The name of the destination to which the message is to be sent
	 *                    (for messages about to be sent) or the name of the destination from which the message has been received.
	 *                    (for messages received by the onMessage function).
	 * <p>
	 * @property {number} qos The Quality of Service used to deliver the message.
	 * <dl>
	 *     <dt>0 Best effort (default).
	 *     <dt>1 At least once.
	 *     <dt>2 Exactly once.     
	 * </dl>
	 * <p>
	 * @property {Boolean} retained If true, the message is to be retained by the server and delivered 
	 *                     to both current and future subscriptions.
	 *                     If false the server only delivers the message to current subscribers, this is the default for new Messages. 
	 *                     A received message has the retained boolean set to true if the message was published 
	 *                     with the retained boolean set to true
	 *                     and the subscrption was made after the message has been published. 
	 * <p>
	 * @property {Boolean} duplicate <i>read only</i> If true, this message might be a duplicate of one which has already been received. 
	 *                     This is only set on messages received from the server.
	 *                     
	 */
	var Message = function (newPayload) {  
		var payload;
		if (   typeof newPayload === "string" 
			|| newPayload instanceof ArrayBuffer
			|| newPayload instanceof Int8Array
			|| newPayload instanceof Uint8Array
			|| newPayload instanceof Int16Array
			|| newPayload instanceof Uint16Array
			|| newPayload instanceof Int32Array
			|| newPayload instanceof Uint32Array
			|| newPayload instanceof Float32Array
			|| newPayload instanceof Float64Array
		   ) {
			payload = newPayload;
		} else {
			throw (format(ERROR.INVALID_ARGUMENT, [newPayload, "newPayload"]));
		}

		this._getPayloadString = function () {
			if (typeof payload === "string")
				return payload;
			else
				return parseUTF8(payload, 0, payload.length); 
		};

		this._getPayloadBytes = function() {
			if (typeof payload === "string") {
				var buffer = new ArrayBuffer(UTF8Length(payload));
				var byteStream = new Uint8Array(buffer); 
				stringToUTF8(payload, byteStream, 0);

				return byteStream;
			} else {
				return payload;
			};
		};

		var destinationName = undefined;
		this._getDestinationName = function() { return destinationName; };
		this._setDestinationName = function(newDestinationName) { 
			if (typeof newDestinationName === "string")
				destinationName = newDestinationName;
			else 
				throw new Error(format(ERROR.INVALID_ARGUMENT, [newDestinationName, "newDestinationName"]));
		};
				
		var qos = 0;
		this._getQos = function() { return qos; };
		this._setQos = function(newQos) { 
			if (newQos === 0 || newQos === 1 || newQos === 2 )
				qos = newQos;
			else 
				throw new Error("Invalid argument:"+newQos);
		};

		var retained = false;
		this._getRetained = function() { return retained; };
		this._setRetained = function(newRetained) { 
			if (typeof newRetained === "boolean")
				retained = newRetained;
			else 
				throw new Error(format(ERROR.INVALID_ARGUMENT, [newRetained, "newRetained"]));
		};
		
		var duplicate = false;
		this._getDuplicate = function() { return duplicate; };
		this._setDuplicate = function(newDuplicate) { duplicate = newDuplicate; };
	};
	
	Message.prototype = {
		get payloadString() { return this._getPayloadString(); },
		get payloadBytes() { return this._getPayloadBytes(); },
		
		get destinationName() { return this._getDestinationName(); },
		set destinationName(newDestinationName) { this._setDestinationName(newDestinationName); },
		
		get qos() { return this._getQos(); },
		set qos(newQos) { this._setQos(newQos); },

		get retained() { return this._getRetained(); },
		set retained(newRetained) { this._setRetained(newRetained); },

		get duplicate() { return this._getDuplicate(); },
		set duplicate(newDuplicate) { this._setDuplicate(newDuplicate); }
	};
	   
	// Module contents.
	return {
		Client: Client,
		Message: Message
	};
})(window);

dolinaIM = {
    createNew: function () {
        var self = {};

        //self.url = "121.42.147.169";
        //self.url = "139.129.203.38";
        //self.port = 8083;
        self.connected = false;
        self.ws = null;
        self.robotid = "_robot_protocol";
        self.signalingTopic = "/engyne_signaling";
        self.sendTopic = null;      //发送消息的topic，null表示与监听topic相同

        self.headimgurl = "";       //头像
        self.nickname = "";         //昵称
        self.admin = 0;             //是否为客服发出
        self.keepAliveInterval = 60;    //连接保持时间

        self.onConnectSuccess = null;
        self.onConnectFailure = null;
        self.onSuccessShakeHand = null;
        self.onSuccessSent = null;
        self.onMessageRead = null;
        self.onMessageArrived = null;
        self.onConnectionLost = null;
        self.onSystemMsg = null;
        self.onSubscribed = null;

        //设置参数
        self.setParams = function(params) {
            self.url = params.wsserver.ip;
            self.port = params.wsserver.port;
            self.clientid = params.clientid;
            self.sessionid = params.sessionid;
            self.convid = params.convid;
            self.appid = params.appid;
            self.pageid = params.pageid;
            if(params.stopic)
                self.signalingTopic = params.stopic;
        }

        self.disconnect = function() {
            if(self.ws)
                self.ws.disconnect();
        }

        self.connect = function (convid, topic, clientid, noshakehand, noack, sessionid) {

            if(convid)
                self.clientid = clientid;
            if(clientid){
                self.topic = topic;
                self.convid = convid;
                self.sessionid = sessionid;
            }

            var mqttid = clientid + "-" + self.randomString(4);

            var ssl = true;

            if (!window.dolinaWS)
                return;
            else{
                if (window.location.protocol === "https:") {
                    ssl = true;
                    self.ws = new dolinaWS.MQTT.Client(self.url, parseInt(self.port), mqttid);
                } else {
                    ssl = false;
                    self.ws = new dolinaWS.MQTT.Client(self.url, (parseInt(self.port)-1), mqttid);
                }
            }

            // connect the client

            self.ws.connect({
                useSSL: ssl,
                onSuccess: function () {

                    if (!self.topic)
                        return;
                    else{
                        self.connected = true;
                        self.subscribe(self.topic);
                    }

                    if( typeof(noshakehand)=='undefined' || noshakehand==null || noshakehand==false)
                        self.shakeHand();

                    if(self.onConnectSuccess)
                        self.onConnectSuccess();
                },

                onFailure: function () {
                    if(self.onConnectFailure)
                        self.onConnectFailure();
                },

                keepAliveInterval: self.keepAliveInterval

            });

            // 监听Socket的关闭
            self.ws.onConnectionLost = function (responseObject) {

                if (responseObject.errorCode !== 0) {
                    console.log("onConnectionLost:" + responseObject.errorMessage);
                }

                self.connected = false;

                if(self.onConnectionLost)
                    self.onConnectionLost();
            };

            // 监听消息
            self.ws.onMessageArrived = function (event) {

		        try{
                   var recObj = JSON.parse(event.payloadString);
		        }catch(e){
		            return;
		        }

                var topic = event.destinationName;

                if (recObj.from == self.clientid)
                    return;

                if (recObj.to && recObj.to != self.clientid)
                    return;

                switch (recObj.type) {
                    case 'system':
                        if (recObj.msgCode == 1005) {
                            //握手成功
                            if(recObj.to && recObj.to==self.clientid && self.onSuccessShakeHand)
                                self.onSuccessShakeHand(recObj.data);
                        }else{
                            if(self.onSystemMsg)
                            {
                                self.onSystemMsg(recObj, topic);
                            }
                        }

                        break;
                    case 'ack':
                        if( recObj.session == self.sessionid )
                        {
                            if (recObj.from == self.robotid) {
                                //设置消息已发送状态
                                if (self.onSuccessSent)
                                    self.onSuccessSent(recObj);
                            } else {
                                //设置消息已阅读状态
                                if (self.onMessageRead)
                                    self.onMessageRead(recObj);
                            }
                        }
                        break;
                    case 'text':
                    case 'image':
                    case 'vote':
                    case 'input':
                    case 'tel':
                    case 'news':
                    case 'guide':
                        if( typeof(noack)=='undefined' || noack==null || noack==false)
                            self.ack(recObj);

                        //正常消息到达
                        if (self.onMessageArrived)
                            self.onMessageArrived(recObj, topic);
                        break;
                    default:
                        break;
                }
            };

        }

        //订阅
        self.subscribe = function(topic)
        {
            if(self.connected)
            {
                self.ws.subscribe(topic);
                if(self.onSubscribed)
                {
                    self.onSubscribed();
                }
            }
        }

        //取消订阅
        self.unsubscribe = function(topic)
        {
            if(self.connected)
            {
                self.ws.unsubscribe(topic);
            }
        }

        //消息确认
        self.ack = function (msgObj) {
            var message = new Object;
            message.type = "ack";
            message.from = self.clientid;
            message.session = msgObj.session;
            message.convid = msgObj.convid;
            message.tmpindex = msgObj.tmpindex;
            message.time = Math.round((new Date()).getTime() / 1000);
            message.to = msgObj.from;

            self.sendToWsServer(JSON.stringify(message))
        }

        self.sendTextMessage = function (messageContent) {
            var message = new Object;

            message.type = "text";
            message.from = self.clientid;
            message.session = self.sessionid;
            message.convid = self.convid;
            message.tmpindex = self.getTmpIndex();
            message.time = Math.round((new Date()).getTime() / 1000);
            message.content = {};
            message.content.content = messageContent;
            message.extra = {};
            message.extra.headimgurl = self.headimgurl;
            message.extra.nickname = self.nickname;
            message.extra.admin = self.admin;

            self.sendToWsServer(JSON.stringify(message));

            return message;
        }

        self.sendAttachMessage = function(uploadedAttachUrl, attachInfo){
            var message = new Object;

            message.type = "image";
            message.from = self.clientid;
            message.session = self.sessionid;
            message.convid = self.convid;
            message.tmpindex = self.getTmpIndex();
            message.time = Math.round((new Date()).getTime() / 1000);
            message.content = {};
            message.content.url = uploadedAttachUrl;
            message.extra = {};
            message.extra.headimgurl = self.headimgurl;
            message.extra.nickname = self.nickname;
            message.extra.admin = self.admin;

            message.info = attachInfo;

            self.sendToWsServer(JSON.stringify(message));

            return message;
        }

        self.sendVoteResult = function(templateIndex, result){
            var msg = {};
            msg.type = "vote";
            msg.template = templateIndex;
            msg.result = result;


            self.sendSignaling(msg);
        }

        self.sendInputResult = function(templateIndex, info){
            var msg = {};
            msg.type = "input";
            msg.template = templateIndex;
            msg.result = info;


            self.sendSignaling(msg);
        }

        self.sendTelResult = function(templateIndex, info){
            var msg = {};
            msg.type = "tel";
            msg.template = templateIndex;
            msg.result = info;

            self.sendSignaling(msg);
        }

        self.sendEvent = function(eventName, data){
            var msg = {};
            msg.type = "event";
            msg.event = eventName;
            msg.data = data;
            msg.appid = self.appid;
            msg.pageid = self.pageid;

            self.sendSignaling(msg);
        }

        self.sendSignaling = function(signaling){
            var message = new Object();

            message.type = "signaling";
            message.from = self.clientid;
            message.session = self.sessionid;
            message.convid = self.convid;
            message.tmpindex = self.getTmpIndex();
            message.topic = self.topic;
            message.time = Math.round((new Date()).getTime() / 1000);
            message.content = {};
            message.content.signaling = signaling;

            self.sendToSignalingServer(JSON.stringify(message));
        }

        self.getTmpIndex = function()
        {
            return ((new Date()).getTime()).toString() + self.randomString(3);
        }

        self.shakeHand = function () {
            //发送用户名
            var message = new Object;

            message.type = "shakehand";
            message.from = self.clientid;
            message.session = self.sessionid;
            message.convid = self.convid;
            message.appid = self.appid;
            message.pageid = self.pageid;
            message.admin = self.admin;
            //message.clientinfo = self.getClientInfo();
            self.sendToWsServer(JSON.stringify(message));
        }

        self.sendToWsServer = function (str) {
            if (!window.dolinaWS)
                return;

            var message = new dolinaWS.MQTT.Message(str);

            message.destinationName = self.sendTopic?self.sendTopic:self.topic;

            if (self.ws && self.ws.isConnected())
                self.ws.send(message);
        }

        self.sendToSignalingServer = function (str) {
            if (!window.dolinaWS)
                return;

            var message = new dolinaWS.MQTT.Message(str);
            message.destinationName = self.signalingTopic;
            if ( self.ws && self.ws.isConnected() )
                self.ws.send(message);
        }

        self.randomString = function(len) {
            len = len || 32;
            var $chars = 'ABCDEFGHJKMNPQRSTWXYZabcdefhijkmnprstwxyz2345678';    /****默认去掉了容易混淆的字符oOLl,9gq,Vv,Uu,I1****/
            var maxPos = $chars.length;
            var pwd = '';
            for (i = 0; i < len; i++) {
                pwd += $chars.charAt(Math.floor(Math.random() * maxPos));
            }
            return pwd;
        }

        return self;
    }
}

/**
 * Created by Administrator on 2016/4/11.
 */

dolinaAnalysis = {
    createNew: function () {
        var self = {};
        self.dolinaIM = null;

        self.report = function(event, data)
        {
            if(self.dolinaIM)
            {

                //记录发送次数
                if(self.counter[event])
                {
                    self.counter[event]["counter"] ++;
                }else{
                    self.counter[event] = {};
                    self.counter[event]["counter"] = 1;
                }

                if( typeof(data)=='undefined' || data==null )
                    data = {};
                
                data.counter = self.counter[event]["counter"];
                if(dolina_i18n)
                    data.lang = dolina_i18n.language;

                if(event=='load'){
                    data.clientinfo = self.getClientInfo();
                }

                self.dolinaIM.sendEvent(event, data);
            }
        }

        //获取终端信息
        self.getClientInfo = function () {
            var info = new Object();
            info.channel = "web";
            info.referrer = window.document.referrer;
            info.deviceinfo = new Object();
            info.deviceinfo.os = self.getOsInfo();
            info.deviceinfo.terminal = self.getNavigatorInfo();
            info.deviceinfo.language = self.language;

            return info;
        }

        //判断操作系统
        self.getOsInfo = function () {
            var UserAgent = navigator.userAgent.toLowerCase();
            var os = {
                isIpad: /ipad/.test(UserAgent),
                isIphone: /iphone os/.test(UserAgent),
                isAndroid: /android/.test(UserAgent),
                isWindowsCe: /windows ce/.test(UserAgent),
                isWindowsMobile: /windows mobile/.test(UserAgent),
                isWin2K: /windows nt 5.0/.test(UserAgent),
                isXP: /windows nt 5.1/.test(UserAgent),
                isVista: /windows nt 6.0/.test(UserAgent),
                isWin7: /windows nt 6.1/.test(UserAgent),
                isWin8: /windows nt 6.2/.test(UserAgent),
                isWin81: /windows nt 6.3/.test(UserAgent),
                isWin10: /windows nt 10.0/.test(UserAgent),
                isMac: /mac os x/.test(UserAgent),
                isLinux: /linux/.test(UserAgent)
            };

            if (os.isWin10)
                return "Windows 10";

            if (os.isAndroid)
                return 'Android';

            if (os.isIphone)
                return 'IOS';

            if (os.isWin7)
                return "Windows 7";

            if (os.isWin8)
                return "Windows 8";

            if (os.isWin81)
                return "Windows 8.1";

            if (os.isMac)
                return "Mac OS X";

            if (os.isLinux)
                return "Linux";

            return "Others";
        }

        //判断浏览器版本
        self.getNavigatorInfo = function () {
            var Sys = {};
            var ua = navigator.userAgent.toLowerCase();
            var s;
            (s = ua.match(/edge.([\d.]+)/)) ? Sys.edge = s[1] :
                (s = ua.match(/msie ([\d.]+)/)) ? Sys.ie = s[1] :
                    (s = ua.match(/firefox\/([\d.]+)/)) ? Sys.firefox = s[1] :
                        (s = ua.match(/chrome\/([\d.]+)/)) ? Sys.chrome = s[1] :
                            (s = ua.match(/opera.([\d.]+)/)) ? Sys.opera = s[1] :
                                (s = ua.match(/version\/([\d.]+).*safari/)) ? Sys.safari = s[1] : 0;

            if (Sys.ie)
                return 'IE ' + Sys.ie;

            if (Sys.firefox)
                return 'Firefox ' + Sys.firefox;

            if (Sys.chrome)
                return 'Chrome ' + Sys.chrome;

            if (Sys.opera)
                return 'Opera ' + Sys.opera;

            if (Sys.edge)
                return 'Edge ' + Sys.edge;

            if (Sys.safari)
                return 'Safari ' + Sys.safari;

            if( (ua.toLowerCase().indexOf("trident") > -1 && ua.indexOf("rv") > -1) )
            {
                return 'IE 11';
            }

            return 'Others';
        }

        //获取默认语言
        self.getNavigatorLang = function() {
            var lang = window.navigator.userLanguage || window.navigator.language;
            var res = lang.split("-");
            var prefix = res[0];

            switch(prefix.toLowerCase()){
                case 'zh':
                    return 'zh';
                case 'en':
                    return 'en';
                case 'es':
                    return 'es';
            }

            return "";
        }

        self.language = self.getNavigatorLang();

        self.counter = {};

        return self;
    }
}

/**
 * Created by Administrator on 2016/3/31.
 */

dolina = {
    createNew: function(_baseUrl) {
        var self = {};

        self.msgList = new Array();           //聊条记录列表
        self.sysMsgList = new Array();         //系统消息列表
        self.connected = false;
        self.windowClosed = true;

        self.systemid = "_robot";
        self.dolinaIM = null;
        self.dolinaAnalysis = null;
        self.unreported = new Array();

        self.teamLogoUrl = "";              //对方的公司logo
        self.noreplylimit = 5000;           //无回复报警的时长（毫秒）

    self.updateUILang = function(selectedLng) {

            if(dolina_i18n)
            {
                dolina_i18n.changeLanguage(selectedLng, function(err, t){
                    // resources have been loaded
                    var hint1 = document.getElementById('dolina_hint1');
                    if(hint1)
                        hint1.innerHTML = dolina_i18n.t('recent_on_online');

                    var teamTitle = document.getElementById('dolina_team_title');
                    if(teamTitle)
                        teamTitle.innerHTML = dolina_i18n.t('team_title');

                    var welcomeMsg = document.getElementById('dolina_welcome_msg');
                    if(welcomeMsg)
                        welcomeMsg.innerHTML = dolina_i18n.t('welcome_msg');

                    var sendBtn = document.getElementById('dolina-btn-msg-send');
                    if(sendBtn)
                        sendBtn.innerHTML = dolina_i18n.t('send');

                    //msgList
                    for( x in self.msgList )
                    {
                        var tag = document.getElementById(self.msgList[x].tmpindex);
                        if(tag) {
                            if(self.msgList[x].sent && self.msgList[x].tmpindex)
                            {
                                tag.innerHTML = dolina_i18n.t('sent');
                            }else{
                                tag.innerHTML = dolina_i18n.t('sending');
                            }
                        }
                    }

                    //self.reportEvent("changelanguage", null);
                });
            }
        }

        self.showChatWin = function(){
            var cContainer = document.getElementById("dolina-container");
            self.addClass(cContainer, "dolina-container-active");
            self.removeClass(cContainer, "dolina-container-min");

            self.hideButton = true;
            self.needReconnect = true;

            self.clientid = dolina.clientid;
            self.convid = dolina.convid;
            self.wsserver = {};
            self.wsserver.ip = dolina.wsserverip;
            self.wsserver.port = dolina.wsserverport;

            self.windowClosed = false;

            self.dolinaBtn.style.display = "none";

            //dolina.showSpin();
            //self.connect();

            //将焦点置于输入框内
            var editBox = document.getElementById("dolina-message-box");
            setTimeout(function(){editBox.focus();},0);

            self.offlineMsgNum = 0;     //clear unread message number
            self.reportEvent("openchat", null);
        }

        self.getChatDlgHtml = function() {

            if(self.chatDlgHtml)
                return self.chatDlgHtml;

            if( self.styleOptions.btn_color )
                self.btnColor = self.styleOptions.btn_color;                            //按钮颜色
            else
                self.btnColor = "#72a7fe";


            if( self.styleOptions.no_reply_sec )                   //无应答时间
                self.noreplylimit = parseInt( self.styleOptions.no_reply_sec )*1000;

            self.chatDlgHtml =
                '<div id="dolina-container" class="dolina-container-min">' +
                '<div class="dolina-header">' +
                //'<a href="" class="dolina-header-nav"><i class="dolina-iconfont">&#xe604;</i></a>' +
                '<a href="javascript:;" class="dolina-header-min" id="closeChatBtn"><i class="dolina-iconfont">&#xe603;</i></a>' +
                '<div class="dolina-header-title" id="dolina_team_title"></div>' +
                '</div>' +
                '<div class="dolina-wrap">' +
                '<div class="dolina-body" id="dolina-body">';

            if( parseInt(self.styleOptions.show_lang) )
            {
                self.chatDlgHtml += '<div class="dolina-select-language">' +
                    '<img id="dolina-select-lang-zh" src="' + _baseUrl + 'js/img/CN.png" title="" alt=""/>' +
                    '<img id="dolina-select-lang-en" src="' + _baseUrl + 'js/img/US.png" title="" alt="" class="active"/>' +
                    '<img id="dolina-select-lang-es" src="' + _baseUrl + 'js/img/ES.png" title="" alt=""/>' +
                    '</div>';
            }

            self.chatDlgHtml +=
                '<div class="dolina-welcome">' +
                '<div id="dolina_hint1">' +  dolina_i18n.t('recent_on_online') + '</div>' +
                '<div id="staffPhoto" class="dolina-avatar"></div>' +
                '<div id="dolina_welcome_msg"></div>' +
                '</div>' +
                '<div class="dolina-stream" id="dolina-container-stream"></div>' +
                '</div>' +
                '<div class="dolina-footer">' +
                '<div class="dolina-footer-wrap">' +
                '<div id="dolinaMessageContainer">' +
                '<i class="dolina-iconfont">&#xe600;</i>' +
                '<i class="dolina-iconfont">&#xe601;</i>' +
                '<div contenteditable="true" class="dolina-footer-editor" id="dolina-message-box">' +
                '</div>' +
                '</div>' +
                '</div>' +
                '<button class="dolina-message-send" id="dolina-btn-msg-send">' + dolina_i18n.t('send') + '</button>' +
                '<div class="dolina-footer-brand">Powered by <a target="_blank" href="http://growthengine.co/">GrowthEngine</a></div>' +
                '</div>' +
                '</div>' +
                '</div>';

            if( parseInt(self.styleOptions.btn_style) == 0 ){
                var btnDisplay = ' ';                               //显示聊天按钮
            }else{
                var btnDisplay = 'none';                            //隐藏聊天按钮
            }

            var isMobile = mobileCheck();
            if(isMobile){
                self.styleOptions.pos_bottom = Math.floor(parseInt(self.styleOptions.pos_bottom)/2);
                self.styleOptions.pos_right = Math.floor(parseInt(self.styleOptions.pos_right)/2);
            }

            self.chatDlgHtml +=
                '<div id="dolina-container-btn" style="background-color: ' + self.btnColor
                + '; bottom: ' + self.styleOptions.pos_bottom + 'px; right: ' + self.styleOptions.pos_right
                + 'px; display: ' + btnDisplay + ';">' +
                '<div id="noOfflineMsg">' +
                '<i class="dolina-iconfont"></i>' +
                '</div>' +
                '<div id="offlineMsg" style="display: none">' +
                '<div class="dolina-launcher-badge" id="offlineMsgNum"></div>' +
                '<div id="dolina_lastOfflineMsg"></div>' +
                '</div>' +
                '</div>';

            return self.chatDlgHtml;
        }

        // 变更语言
        function selectLanguage(selected){

            if( typeof selected=='undefined' || selected =='' || selected==null )
                return;

            self.updateUILang(selected);

            var btnId = "dolina-select-lang-" + selected;
            var selectLangBtn = document.getElementById(btnId);
            var selectLangZh = document.getElementById("dolina-select-lang-zh");
            var selectLangEn = document.getElementById("dolina-select-lang-en");
            var selectLangEs = document.getElementById("dolina-select-lang-es");


            if(selectLangBtn) {
                self.removeClass(selectLangZh, 'active');
                self.removeClass(selectLangEn, 'active');
                self.removeClass(selectLangEs, 'active');
                self.addClass(selectLangBtn, 'active');
            }

            return;
        }

        self.show = function() {

            var mainBody = document.getElementsByTagName('body')[0];
            var dialog = document.createElement("div");
            dialog.id = "dolina_dialog_window";
            //dialog.innerHTML = self.chatDlgHtml;
            dialog.innerHTML = self.getChatDlgHtml();

            mainBody.appendChild(dialog);

            self.dolinaBtn = document.getElementById("dolina-container-btn");

            self.dolinaBtn.onclick = function()
            {
               self.showChatWin();
            }

            self.closeChatBtn = document.getElementById("closeChatBtn");

            self.closeChatBtn.onclick = function()
            {
                var cContainer = document.getElementById("dolina-container");
                self.addClass(cContainer, "dolina-container-min");
                self.removeClass(cContainer, "dolina-container-active");

                self.hideButton = false;
                self.windowClosed = true;

                if( dolinaParams && dolinaParams.options && dolinaParams.options.hideBtn ){
                    self.dolinaBtn.style.display ='none';
                }else{
                    self.dolinaBtn.style.display = "";
                }

                self.showNoOfflineMsgIcon();
            }

            self.sendBtn = document.getElementById('dolina-btn-msg-send');
            self.sendBtn.onclick = function(){
                self.send();
            }

            var selectLangZh = document.getElementById("dolina-select-lang-zh");
            var selectLangEn = document.getElementById("dolina-select-lang-en");
            var selectLangEs = document.getElementById("dolina-select-lang-es");

            if(selectLangZh)
                selectLangZh.onclick = function(){
                    selectLanguage('zh');
                };
            if(selectLangEn)
                selectLangEn.onclick = function(){
                    selectLanguage('en');
                };
            if(selectLangEs)
                selectLangEs.onclick = function(){
                    selectLanguage('es');
                };
        };


        // 显示spinner
        self.showSpin = function(){
            var target = document.getElementById("dolina-body");
            self.spinner.spin(target);
        }

        // 隐藏spinner
        self.hideSpin = function(){
            self.spinner.spin();
        }

        self.registerFromServer = function(userid)
        {
            var x = document.getElementsByTagName('script')[0];
            var url = self.baseUrl + "index.php/Conversation/registerForWeb?username="
                + userid + "&appid=" + dolinaParams.appid + "&channel=web"
                + "&language=" + self.dolinaIM.language
                + "&path=" + encodeURI(self.getUrlRelativePath());

            var s1 = document.createElement('script');
            s1.type = 'text/javascript';
            s1.src = url;
            x.parentNode.insertBefore(s1, x);
        }

        self.getUrlRelativePath = function()
        {
            var url = document.location.toString();
            var arrUrl = url.split("//");

            //var start = arrUrl[1].indexOf("/");
            //var relUrl = arrUrl[1].substring(start);//stop省略，截取从start开始到结尾的所有字符
            var start = arrUrl[1].indexOf("www.");
            var relUrl = arrUrl[1];
            if(start==0){
                 relUrl = arrUrl[1].substring(4);
            }

            if(relUrl.indexOf("?") != -1){
                relUrl = relUrl.split("?")[0];
            }
            return relUrl;
        }

        self.setwsparams = function(data)
        {
            if(data.result=='SUCCESS')
            {
                self.clientid = data.clientid;
                self.convid = data.convid;
                self.topic = data.topic;
                self.teamLogoUrl = data.teamlogo;

                self.wsserverip = data.wsserver.ip;
                self.wsserverport = data.wsserver.port;

                self.qiniuUpToken = data.uploadtoken;

                self.styleOptions = getStyleOptions(data.sdk_style);

                //self.getUnreadMsg(self.clientid);
                self.showNoOfflineMsgIcon();

                self.sessionid = data.sessionid;

                if( typeof(data.appid)=='undefined' || data.appid==null || data.appid=='' )
                {
                    data.appid = dolinaParams.appid;
                }

                if( dolinaParams && dolinaParams.pageid ){
                    data.pageid = dolinaParams.pageid;
                }


                self.dolinaIM.setParams(data);

                self.connect();
            }
        }

        function getStyleOptions(styles){
            //复制后端的配置信息。如果前端有配置信息的话，以前端为准

            if( typeof(dolinaParams)=='undefined' || typeof(dolinaParams.styleOptions)=='undefined' ){
                return styles;
            }

            var ret = {};

            for(var k in styles){
                if( dolinaParams.styleOptions[k] ){
                    ret[k] = dolinaParams.styleOptions[k];
                }else{
                    ret[k] = styles[k];
                }
            }

            return ret;
        }
/*
        self.getUnreadMsg = function(clientid)
        {
            //获取未读消息
            var x = document.getElementsByTagName('script')[0];
            var url = self.baseUrl + "index.php/Message/getUnreadForWeb?clientid="
                + self.clientid + "&limit=3";

            var s1 = document.createElement('script');
            s1.type = 'text/javascript';
            s1.src = url;
            x.parentNode.insertBefore(s1, x);
        }

        self.setUnreadMsg = function(data)
        {
            if(data.result=='SUCCESS')
            {
                self.offlineMsgNum = data.list.length;

                if(self.offlineMsgNum>0)
                {
                    self.lastOfflineMsg = data.list[0].content.content;

                    try{
                        self.showOfflineMsg(data.list[0]);
                    }catch(e){
                        console.log("offline message parse error");
                    }
                } else{
                    self.showNoOfflineMsgIcon();
                }
            }

        }*/

        self.initUploader = function(){
            if(window.Qiniu)
            {
                self.uploader = Qiniu.uploader({
                    runtimes: 'html5,flash,html4',
                    browse_button: "attachmentBtn",
                    max_file_size: "1mb",
                    flash_swf_url: 'js/plupload/Moxie.swf',
                    dragdrop: true,
                    chunk_size: '500kb',
                    uptoken: self.qiniuUpToken,
                    domain: self.qiniuUrl,
                    get_new_uptoken: false,
                    unique_names: false,
                    save_key: false,
                    auto_start: true,
                    init: {
                        'FilesAdded': function(up, files) {

                        },
                        'BeforeUpload': function(up, file) {
                            self.showSpin();
                        },
                        'UploadProgress': function(up, file) {

                        },
                        'UploadComplete': function() {
                            self.spinner.spin();
                        },
                        'Key': function(up, file) {
                            // 在前端对每个文件的key进行个性化处理，可以配置该函数
                            // 该配置必须要在unique_names: false，save_key: false时才生效
                            var key = dolinaParams.appid + "/" + file.name.toLowerCase();
                            // do something with key here
                            return key
                        },
                        'FileUploaded': function(up, file, info) {
                            self.spinner.spin();

                            var fileExt = getFileExt(file.name);

                            var fileInfo = {};


                            try{
                                infoObj = JSON.parse(info);
                                //上传成功后

                                if(fileExt=='image'){
                                    var fileUrl = Qiniu.imageView2({
                                        mode: 2,  // 缩略模式，共6种[0-5]
                                        w: 640,   // 宽度最多640
                                        q: 100    // 新图的图像质量，取值范围：1-100
                                    }, infoObj.key);

                                    fileInfo = Qiniu.imageInfo(infoObj.key);
                                }else{

                                    var fileUrl = self.qiniuUrl + infoObj.key;
                                    fileInfo.name = encodeURIComponent(file.name);
                                }


                            }catch(e){
                                console.log('fail to parse file info');
                            }


                            fileInfo.type = fileExt;


                            self.sendAttach(fileUrl, fileInfo);


                        },
                        'Error': function(up, err, errTip) {
                            self.spinner.spin();
                        }
                    }
                });
            }
        }

        function getFileExt(fileName){

            var strArr = fileName.split(".");

            if(strArr.length==0){
                return 'file';
            }else{
                var ext = strArr[strArr.length-1].toLowerCase();
                switch(ext){
                    case 'png':
                    case 'jpg':
                    case 'gif':
                    case 'jpeg':
                        return 'image';
                    case 'doc':
                    case 'docx':
                        return 'word';
                    case 'xls':
                    case 'xlsx':
                    case 'csv':
                        return 'excel';
                    case 'pdf':
                        return 'pdf';
                    case 'ppt':
                    case 'pptx':
                        return 'powerpoint';
                    default:
                        return 'file';
                }
            }

            return 'file';
        }

        self.initSpin = function(){

            var opts = {
                lines: 10, // 花瓣数目
                length: 4, // 花瓣长度
                width: 3, // 花瓣宽度
                radius: 5, // 花瓣距中心半径
                corners: 1, // 花瓣圆滑度 (0-1)
                rotate: 0, // 花瓣旋转角度
                direction: 1, // 花瓣旋转方向 1: 顺时针, -1: 逆时针
                color: '#000', // 花瓣颜色
                speed: 1, // 花瓣旋转速度
                trail: 60, // 花瓣旋转时的拖影(百分比)
                shadow: false, // 花瓣是否显示阴影
                hwaccel: false, //spinner 是否启用硬件加速及高速旋转
                className: 'spinner', // spinner css 样式名称
                zIndex: 2e9, // spinner的z轴 (默认是2000000000)
                top: '50%', // spinner 相对父容器Top定位 单位 px
                left: '50%'// spinner 相对父容器Left定位 单位 px
            };

            self.spinner = new dolinaSpinner(opts);
        }

        self.register = function(){
            //检查cookies
            var uuid = self.getCookie("dolina_userid");
            if( typeof(uuid)=='undefined' || uuid=='' || uuid==null )
            {
                //新用户，创建一个userid
                self.userid = ((new Date()).getTime()).toString() + self.randomString(8);
                self.setCookie("dolina_userid", self.userid);
            }else{
                self.userid = uuid;
            }

            self.dolinaIM = dolinaIM.createNew();

            self.registerFromServer(self.userid);
        };
        
        self.login = function(username){

            var eventData = {};
            eventData.username = username;

            self.reportEvent("login", eventData);
        }

        self.getCookie = function(name)
        {
            var arr,reg=new RegExp("(^| )"+name+"=([^;]*)(;|$)");
            if(arr=document.cookie.match(reg))
                return decodeURIComponent(arr[2]);
            else
                return null;
        }

        self.get_top_domain = function extractDomain(domain) {

            var re = /([^.]+\.)?([^\.]+\..+)/;
            var m = domain.match(re);

            if ( m && m.length > 2)
                return m[2];
            else
                return domain;
        }

        self.setCookie = function(name,value)
        {
            var Days = 360;
            var exp = new Date();
            exp.setTime(exp.getTime() + Days*24*60*60*1000);


            var ip =
                /^(\d{1,2}|1\d\d|2[0-4]\d|25[0-5])\.(\d{1,2}|1\d\d|2[0-4]\d|25[0-5])\.(\d{1,2}|1\d\d|2[0-4]\d|25[0-5])\.(\d{1,2}|1\d\d|2[0-4]\d|25[0-5])$/;

            if (ip.test(document.domain) == true || document.domain == 'localhost')
            {
                document.cookie = name + "="+ encodeURIComponent(value)
                    + ";expires=" + exp.toGMTString() + ";path=/";
            }else{
                var domain = self.get_top_domain(document.domain);
                document.cookie = name + "="+ encodeURIComponent(value)
                    + ";expires=" + exp.toGMTString() + ";domain=." + domain + ";path=/";
            }
        }

        self.showOfflineMsg = function(message)
        {
            var isMobile = mobileCheck();

            if(message.type!='guide')
            {
                var msgBtn = document.getElementById("dolina-container-btn");
                if(message.extra && message.extra.headimgurl)
                    msgBtn.style.background = "url(" + message.extra.headimgurl + ")";
                else{
                    msgBtn.style.background = "url(" + self.teamLogoUrl + ")";
                }

                msgBtn.style.backgroundSize = "48px 48px";

                var msgNum = document.getElementById("offlineMsgNum");
                msgNum.innerHTML = self.offlineMsgNum;
                msgNum.style.display = "";

                var commentIcon = document.getElementById("noOfflineMsg");
                commentIcon.style.display = "none";
            }else{

                var msgBtn = document.getElementById("dolina-container-btn");
                msgBtn.style.background = "";

                var commentIcon = document.getElementById("noOfflineMsg");
                commentIcon.style.display = "";

                var msgNum = document.getElementById("offlineMsgNum");
                msgNum.style.display = "none";
            }

            var msgHint = document.getElementById("offlineMsg");
            msgHint.style.display = "";

            var lastMsg = document.getElementById("dolina_lastOfflineMsg");
            switch(message.type)
            {
                case "text":
                    self.clearClass(lastMsg);
                    self.addClass(lastMsg, "dolina-launcher-preview");
                    lastMsg.innerHTML = '<div>'
                            + decodeURIComponent(self.lastOfflineMsg)
                            + '</div>'
                            + '<svg width="8px" height="11px" viewBox="0 0 8 11" version="1.1" xmlns="http://www.w3.org/2000/svg">'
                                + '<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">'
                                + '<path d="M4.4408921e-16,-0.5 L7,6 L4.4408921e-16,11.5 L-3,11 L-3,0 L4.4408921e-16,-0.5 Z" stroke="#eee" fill="#FFF"></path>'
                                + '</g>'
                            + '</svg>';
                    break;
                case "vote":
                case "input":
                case "news":
                case "tel":
                case "guide":
                    self.clearClass(lastMsg);
                    if( isMobile ){
                        //在移动设备上
                        self.addClass(lastMsg, "dolina-msg-push-m-bottom");
                        self.addClass(lastMsg, "dolina-ani-msgPushBottom");
                        setTimeout(function(){
                            self.removeClass(lastMsg, "dolina-ani-msgPushBottom");
                        }, 5000)
                    }else if( self.styleOptions && parseInt(self.styleOptions.msg_style)==0 ){
                        self.addClass(lastMsg, "dolina-launcher-preview-news");
                    }else{
                        self.addClass(lastMsg, "dolina-msg-push-pc-right");
                        self.addClass(lastMsg, "dolina-ani-msgPushRight");
                        setTimeout(function(){
                            self.removeClass(lastMsg, "dolina-ani-msgPushRight");
                        }, 5000)
                    }

                    if(message.type=="guide"){
                        var staffPhotoList = "";
                        for(var i=0; i<self.staffList.length; i++) {
                            if(self.staffList[i] && self.staffList[i].headimageurl)
                                staffPhotoList += "<img src='" + self.staffList[i].headimageurl + "'/>";
                        }
                        lastMsg.innerHTML = '<div style="overflow: hidden;padding: 20px 15px;box-shadow: 0 1px 1px #f1f1f1;z-index: 1;position: relative;">' +
                            '<div class="admin-ava">' +
                            staffPhotoList +
                            '</div>' +
                            '<div style="overflow: hidden">' +
                            '<h2>' + self.lastOfflineMsg.title + '</h2>' +
                            '<div class="para">' + self.lastOfflineMsg.desc + '</div>' +
                            '</div>' +
                            '</div>' +
                            '<div style="padding: 15px;background: #f9f9f9;border-radius:0 0 5px 5px;">' +
                            '<input placeholder="'+ dolina_i18n.t("leave_msg") + '" style="width: 100%;padding: 8px;border-radius: 3px;border: 1px solid #ddd; font-size: 12px; height: 33px;box-sizing: border-box;">' +
                            '</div>' +
                            '<div class="preview-close" id="dolina-preview-close-btn"><i class="dolina-iconfont">&#xe602;</i></div>';
                    }else{
                        lastMsg.innerHTML = '<div style="overflow:hidden; margin: 15px;max-height:200px;text-align:left;">'
                            + '<h2>' + self.lastOfflineMsg.title + '</h2>'
                            + '<div style="overflow: hidden;">'
                            + '<div class="para">' + self.lastOfflineMsg.desc + '</div></div>'
                            + '<div class="preview-close" id="dolina-preview-close-btn"><i class="dolina-iconfont">&#xe602;</i></div>';
                    }

                    break;
                //case "image":
                //    self.removeClass(lastMsg, "dolina-launcher-preview");
                //    self.removeClass(lastMsg, "dolina-launcher-preview-guide");
                //    self.addClass(lastMsg, "dolina-launcher-preview-news");

                //    lastMsg.innerHTML = '<div style="overflow:hidden; margin: 15px">'
                //        + '<img src="' + message.content.url + '" style="width: 64px;" />'
                //        + '<div class="para"></div></div>'
                //        + '<div class="preview-close" id="dolina-preview-close-btn"><i class="dolina-iconfont">&#xe602;</i></div>';
                //    break;
                /*case "guide":
                    self.clearClass(lastMsg);

                    if( isMobile ) {
                        self.addClass(lastMsg, "dolina-msg-push-m-bottom");
                        self.addClass(lastMsg, "dolina-ani-msgPushBottom");
                        setTimeout(function(){
                            self.removeClass(lastMsg, "dolina-ani-msgPushBottom");
                        }, 5000)
                    }else{
                        self.addClass(lastMsg, "dolina-launcher-preview-news");
                    }

                    var staffPhotoList = "";
                    for(var i=0; i<self.staffList.length; i++) {
                        if(self.staffList[i] && self.staffList[i].headimageurl)
                            staffPhotoList += "<img src='" + self.staffList[i].headimageurl + "'/>";
                    }
                    lastMsg.innerHTML = '<div style="overflow: hidden;padding: 20px 15px;box-shadow: 0 1px 1px #f1f1f1;z-index: 1;position: relative;">' +
                            '<div class="admin-ava">' +
                                staffPhotoList +
                            '</div>' +
                            '<div style="overflow: hidden">' +
                                '<h2>' + self.lastOfflineMsg.title + '</h2>' +
                                '<div class="para">' + self.lastOfflineMsg.desc + '</div>' +
                            '</div>' +
                            '</div>' +
                            '<div style="padding: 15px;background: #f9f9f9;border-radius:0 0 5px 5px;">' +
                                '<input placeholder="'+ dolina_i18n.t("leave_msg") + '" style="width: 100%;padding: 8px;border-radius: 3px;border: 1px solid #ddd; font-size: 12px; height: 33px;box-sizing: border-box;">' +
                            '</div>' +
                            '<div class="preview-close" id="dolina-preview-close-btn"><i class="dolina-iconfont">&#xe602;</i></div>';
                    break;*/
                default:
                    break;
            }

            lastMsg.innerHTML += "</div>";

            self.closeOfflineMsgBtn = document.getElementById("dolina-preview-close-btn");

            if(self.closeOfflineMsgBtn){
                self.closeOfflineMsgBtn.onclick = function(event)
                {
                    self.showNoOfflineMsgIcon();
                    event.stopPropagation();
                }
            }
        }

        self.showNoOfflineMsgIcon = function()
        {
            var msgBtn = document.getElementById("dolina-container-btn");
            if( typeof(msgBtn)!='undefined' && msgBtn )
                msgBtn.style.background = self.btnColor;

            var msgHint = document.getElementById("offlineMsg");
            if( typeof(msgHint)!='undefined' && msgBtn )
                msgHint.style.display = "none";

            var commentIcon = document.getElementById("noOfflineMsg");
            if( typeof(commentIcon)!='undefined' && msgBtn )
                commentIcon.style.display = "";
        }

        self.hasClass = function(obj, cls) {
            return obj.className.match(new RegExp('(\\s|^)' + cls + '(\\s|$)'));
        }

        self.addClass = function(obj, cls) {
            if (!self.hasClass(obj, cls)) obj.className += " " + cls;
        }

        self.removeClass = function(obj, cls) {
            if (self.hasClass(obj, cls)) {
                var reg = new RegExp('(\\s|^)' + cls + '(\\s|$)');
                obj.className = obj.className.replace(reg, ' ');
            }
        }

        //清除所有class
        self.clearClass = function(obj) {
            obj.className = "";
        }

        self.connect = function()
        {
            if(self.dolinaIM && self.convid && self.clientid) {

               /* if(mqttid)
                    self.dolinaIM.connect(self.convid, self.topic, self.clientid, false, false, mqttid);
                else
                    self.dolinaIM.connect(self.convid, self.topic, self.clientid, false, false, self.clientid);*/

                self.dolinaIM.connect(self.convid, self.topic, self.clientid, false, false, self.sessionid);

            }else
                return;

            self.dolinaIM.onSuccessShakeHand = function(data){
                if(typeof(data)=='undefined' || data == null)
                    return;

                //握手成功
                self.hideSpin();

                //窗口已存在
                var dolinaBox = document.getElementById("dolina-container");
                if(dolinaBox!=null)
                {
                    var editBox = document.getElementById("dolina-message-box");
                    editBox.style.display = "";

                    setTimeout(function(){editBox.focus();},0);
                    return;
                }

                self.show();
                self.clearEditBox();
                self.showNoOfflineMsgIcon();
                self.initUploader();

                self.offlineMsgNum = 0;
                self.lastOfflineMsg = "";
                self.staffList = data.stafflist;

                if( data && dolina_i18n )
                {
                    for(var lang in data.team)
                    {
                        dolina_i18n.addResource(lang, "translation", "team_title", data.team[lang]);
                    }
                    for(var lang in data.welcome)
                    {
                        dolina_i18n.addResource(lang, "translation", "welcome_msg", data.welcome[lang]);
                    }
                }

                //设置客服头像
                if( data && data.stafflist && data.stafflist.length>0)
                {
                    var staffPhoto = document.getElementById("staffPhoto");

                    staffPhoto.innerHTML = "";

                    var loopLimit = data.stafflist.length;

                    if( dolinaParams && dolinaParams.options && dolinaParams.options.maxStaffPhoto ){
                        if ( dolinaParams.options.maxStaffPhoto < loopLimit ) {
                            loopLimit = dolinaParams.options.maxStaffPhoto;
                        }
                    }

                    for( var i=0; i<loopLimit; i++)
                    {
                        var container = document.createElement("span");
                        var img = document.createElement("img");
                        
                        if(data.stafflist[i])
                        {
                            img.src = data.stafflist[i].headimageurl;

                            if( dolinaParams && dolinaParams.options && dolinaParams.options.staffName ) {
                                container.innerHTML = img.outerHTML + dolinaParams.options.staffName;
                            }else{
                                container.innerHTML = img.outerHTML + data.stafflist[i].nickname;
                            }
                        }

                        staffPhoto.appendChild(container);
                    }
                }

                //设置其他信息
                if(data)
                {
                    if(data.team){
                        document.getElementById("dolina_team_title").innerHTML = dolina_i18n.t("team_title");
                    }

                    if(data.welcome){
                        document.getElementById("dolina_welcome_msg").innerHTML = dolina_i18n.t("welcome_msg");
                    }

                }

                //创建数据采集模块
                self.dolinaAnalysis = dolinaAnalysis.createNew();
                self.dolinaAnalysis.dolinaIM = self.dolinaIM;
                selectLanguage(self.dolinaAnalysis.language);

                //开始计时
                if( self.styleOptions.long_stay && typeof(self.longStayInterval)=='undefined' )
                {
                    self.longStayInterval = setTimeout(function(){
                        self.reportEvent('dolina_long_stay', null);
                    }, parseInt(self.styleOptions.long_stay) * 1000 )
                }

            };

            self.dolinaIM.onConnectFailure = function(){
                var editBox = document.getElementById("dolina-message-box");
                editBox.style.display = "none";
                self.showSpin();
                setTimeout(dolina.connect, 10000);
            }

            self.dolinaIM.onConnectionLost = function(){
                var editBox = document.getElementById("dolina-message-box");
                if(editBox && editBox.style)
                    editBox.style.display = "none";
                self.showSpin();
                setTimeout(dolina.connect, 10000);
            }

            self.dolinaIM.onSuccessSent = function(recObj){
                //设置消息已发送状态
                var textSpan = document.getElementById(recObj.tmpindex);
                if(textSpan && textSpan.innerHTML!='&nbsp;' + dolina_i18n.t("read") )
                    textSpan.innerHTML = '&nbsp;' + dolina_i18n.t("sent");

                for( x in self.msgList )
                {
                    if( self.msgList[x].tmpindex == recObj.tmpindex )
                    {
                        self.msgList[x].sent = 1;
                        break;
                    }
                }

                //启动定时器
                self.noreply = true;
                setTimeout(dolina.checkReply, self.noreplylimit);
            };

            self.dolinaIM.onMessageRead = function(recObj){
                //设置消息已阅读状态
                var textSpan = document.getElementById(recObj.tmpindex);
                if(textSpan)
                    textSpan.innerHTML = '&nbsp;' + dolina_i18n.t("read");
            };

            self.dolinaIM.onMessageArrived = function(recObj){
                //有消息到达
                self.msgList.push(recObj);

                try{
                    self.showMessage(recObj);
                }catch(e){
                    console.log("message parse error");
                }

                //如果对话窗口处于隐藏状态，消息以离线消息方式显示
                if (self.windowClosed) {
                    self.offlineMsgNum += 1;
                    self.lastOfflineMsg = recObj.content.content;

                    try{
                        self.showOfflineMsg(recObj);
                    }catch(e){
                        console.log("offline message parse error");
                    }
                }

                self.noreply = false;
            }
        }

        //检查是否有消息回复
        self.checkReply = function(){
            if(self.noreply)
            {
                var data = {};
                data.alert = self.lastSentMsgContent;
                self.reportEvent("noreply", data);
            }
        }

        //点击投票消息
        self.voteyes = function(recObj){
            self.dolinaIM.sendVoteResult(recObj.content.content.template, 1);

            var idyes = "dolinavoteyes_" + recObj.tmpindex;
            self.addClass(document.getElementById(idyes), "c-part_voted");
            var idno = "dolinavoteno_" + recObj.tmpindex;
            self.removeClass(document.getElementById(idno), "c-part_voted");
        }

        self.voteno = function(recObj){
            self.dolinaIM.sendVoteResult(recObj.content.content.template, 0);

            var idyes = "dolinavoteyes_" + recObj.tmpindex;
            self.removeClass(document.getElementById(idyes), "c-part_voted");
            var idno = "dolinavoteno_" + recObj.tmpindex;
            self.addClass(document.getElementById(idno), "c-part_voted");
        }

        self.submitinput = function(recObj, text){
            self.dolinaIM.sendInputResult(recObj.content.content.template, text);
            var idsubmit = "dolinasubmit_" + recObj.tmpindex;
            document.getElementById(idsubmit).disabled = "disabled";
            self.addClass(document.getElementById(idsubmit), "btn-disable");
        }
        
        self.submittel = function(recObj, text){
            self.dolinaIM.sendTelResult(recObj.content.content.template, text);
            var idsubmit = "dolinasubmit_" + recObj.tmpindex;
            document.getElementById(idsubmit).disabled = "disabled";
            self.addClass(document.getElementById(idsubmit), "btn-disable");
        }

        self.showMessage = function(recObj)
        {
            var msgList = document.getElementById("dolina-container-stream");
            if(msgList==null)
                return;

            var newMsg = document.createElement('div');
            newMsg.id = "dolinamsg_" + recObj.tmpindex;

            if(recObj.content && recObj.content.content)
                var templateIndex = recObj.content.content.template;
            else
                var templateIndex = null;

            var msgIndex = recObj.tmpindex;

            newMsg.onclick = function(){
                //消息被点击
                if(templateIndex)
                    self.reportEvent("dolina_msg_clicked", {template: templateIndex, msgindex: msgIndex});
            }

            if(recObj.type=='guide'){
                return;
            }else if(recObj.type=='system')
            {
                self.addClass(newMsg, 'c-part-system');
            }else{

                if(recObj.from != self.clientid)
                {

                    //来自客服的发言
                    if(recObj.extra && recObj.extra.headimgurl)
                        newMsg.innerHTML += '<span class="ava"><img src="' + recObj.extra.headimgurl + '" /></span>';
                    else
                        newMsg.innerHTML += '<span class="ava"><img src="' + self.teamLogoUrl + '" /></span>';

                    switch(recObj.type)
                    {
                        case 'text':
                            self.addClass(newMsg, 'dolina-admin-msg');
                            newMsg.innerHTML += '<article><div class="para">' + decodeURIComponent(recObj.content.content)
                                + '</div></article><time>'
                                + self.transformTime(recObj.time)
                                + '</time>';
                            break;
                        case 'image':
                            self.addClass(newMsg, 'dolina-admin-msg');

                            if(recObj.info.type=='image'){
                                newMsg.innerHTML += '<article>'
                                    + '<img src="' + recObj.content.url + '" style="max-width:70px; max-height:70px; margin-left:5px;"/>'
                                    + '</article><time>'
                                    + self.transformTime(recObj.time)
                                    + '</time>';
                            }else{
                                newMsg.innerHTML += '<article>'
                                    + '<a href="' + recObj.content.url + '">'
                                    + '<img src="' + _baseUrl + 'js/img/' + recObj.info.type + '.png" style="max-width:48px; max-height:48px; margin-left:5px;"/>'
                                    + '</article>'
                                    + '<time>' + decodeURIComponent(recObj.info.name) + '</time></a><time>'
                                    + self.transformTime(recObj.time)
                                    + '</time>';
                            }

                            break;
                        case 'vote':
                            self.addClass(newMsg, 'dolina-admin-vote');

                            newMsg.innerHTML  += '<div class="dolina-article">'
                                + '<h2><i class="dolina-iconfont"></i>' + recObj.content.content.title + '</h2>'
                                + '<div class="para">' + recObj.content.content.desc + '</div>'
                                + '<div class="btn-wrap">'
                                + '<a href="javascript:void(0)" id="dolinavoteyes_' + recObj.tmpindex + '"><i class="dolina-iconfont">&#xe607;</i></a>'
                                + '<a href="javascript:void(0)" id="dolinavoteno_' + recObj.tmpindex + '"><i class="dolina-iconfont">&#xe608;</i></a>'
                                + '</div></div>';

                            break;
                        case 'input':
                            self.addClass(newMsg, 'dolina-admin-email');

                            newMsg.innerHTML  += '<div class="dolina-article">'
                                + '<h2><i class="dolina-iconfont"></i>' + recObj.content.content.title + '</h2>'
                                + '<div class="para">' + recObj.content.content.desc + '</div>'
                                + '<div><input type="text" id="dolinainput_' + recObj.tmpindex + '"></div>'
                                + '<button type="button" class="btn" id="dolinasubmit_' + recObj.tmpindex
                                    + '">' + dolina_i18n.t("submit") + '</button>'
                                + '</div>';

                            break;
                        case 'tel':
                            self.addClass(newMsg, 'dolina-admin-tel');

                            newMsg.innerHTML  += '<div class="dolina-article">'
                                + '<h2><i class="dolina-iconfont"></i>' + recObj.content.content.title + '</h2>'
                                + '<div class="para">' + recObj.content.content.desc + '</div>'
                                + '<div><input type="text" id="dolinatel_' + recObj.tmpindex + '" value=""></div>'
                                + '<button type="button" class="btn" id="dolinasubmit_' + recObj.tmpindex
                                + '">' + dolina_i18n.t("submit") + '</button>'
                                + '</div>';

                            break;
                        case 'news':
                            self.addClass(newMsg, 'dolina-admin-news');

                            if(recObj.content.content.photo)
                            {
                                newMsg.innerHTML += '<div class="dolina-article">'
                                    + '<h2><i class="dolina-iconfont"></i>' + recObj.content.content.title + '</h2>'
                                    + '<img src="' + recObj.content.content.photo + '" style="width: 56px;" />'
                                    + '<div class="para">' + recObj.content.content.desc + '</div>'
                                    + '</div>';
                            }else{
                                newMsg.innerHTML += '<div class="dolina-article">'
                                    + '<h2><i class="dolina-iconfont"></i>' + recObj.content.content.title + '</h2>'
                                    + '<div class="para">' + recObj.content.content.desc + '</div>'
                                    + '</div>';
                            }

                            break;
                        default:
                            break;
                    }
                }else{
                    self.addClass(newMsg, 'dolina-user-msg');

                    if(recObj.msgid)
                        var msgIndex = recObj.msgid;
                    else
                        var msgIndex = recObj.tmpindex;

                    if(recObj.type=='text')
                    {
                        newMsg.innerHTML += '<article><div class="para">' + decodeURIComponent(recObj.content.content)
                            + '</div></article><time id="' + msgIndex + '">'
                            + '&nbsp;' + dolina_i18n.t("sending") + '</time>';
                    }else{

                        if(recObj.info.type=='image'){
                            newMsg.innerHTML += '<article>'
                                + '<img src="' + recObj.content.url + '" style="max-width:70px; max-height:70px; margin-left:5px;"/>'
                                + '</article><time id="' + msgIndex + '">'
                                + '&nbsp;' + dolina_i18n.t("sending") + '</time>';
                        }else{
                            newMsg.innerHTML += '<article>'
                                + '<a href="' + recObj.content.url + '">'
                                + '<img src="' + _baseUrl + 'js/img/' + recObj.info.type + '.png" style="max-width:48px; max-height:48px; margin-left:5px;"/>'
                                + '</article>'
                                + '<time>' + decodeURIComponent(recObj.info.name) + '</time></a><time id="' + msgIndex + '">'
                                + '&nbsp;' + dolina_i18n.t("sending") + '</time>';
                        }
                    }

                }
            }

            msgList.appendChild(newMsg);
            self.scrollToBottom();

            //绑定按钮事件
            switch(recObj.type)
            {
                case 'vote':
                    var idyes = "dolinavoteyes_" + recObj.tmpindex;
                    document.getElementById(idyes).onclick = function(){
                        self.voteyes(recObj);
                        document.getElementById(idyes).style.color = "#72a7fe";
                        document.getElementById(idyes).onclick = null;
                        document.getElementById(idno).onclick = null;
                    };
                    var idno = "dolinavoteno_" + recObj.tmpindex;
                    document.getElementById(idno).onclick = function(){
                        self.voteno(recObj);
                        document.getElementById(idno).style.color = "#72a7fe";
                        document.getElementById(idyes).onclick = null;
                        document.getElementById(idno).onclick = null;
                    };
                    break;
                case 'input':
                    var idsubmit = "dolinasubmit_" + recObj.tmpindex;
                    document.getElementById(idsubmit).onclick = function(){
                        var idinput = "dolinainput_" + recObj.tmpindex;
                        var submitted_text = document.getElementById(idinput).value;
                        document.getElementById(idinput).disabled = "disabled";
                        self.submitinput(recObj, submitted_text);
                    };
                    break;
                case 'tel':
                    var idsubmit = "dolinasubmit_" + recObj.tmpindex;
                    document.getElementById(idsubmit).onclick = function(){
                        var idinput = "dolinatel_" + recObj.tmpindex;
                        var submitted_text = document.getElementById(idinput).value;
                        document.getElementById(idinput).disabled = "disabled";
                        self.submittel(recObj, submitted_text);
                    };
                    break;
                default:
                    break;
            }
        }

        self.send = function(){

            var messageBox = document.getElementById("dolina-message-box");
            var messageContent = messageBox.innerHTML;

            messageContent = messageContent.replace(/&nbsp;|&nbsp;$/g,'');      //去掉&nbsp;
            messageContent = messageContent.replace(/^\s+|\s+$/g,'');      //去掉前后空格


            //清除所有内容
            self.clearEditBox();
            self.initUploader();
            
            if(messageContent==""){
                return;
            }

            //去掉首尾的"\n"
            if( messageContent.indexOf('\n') == 0 )
            {
                messageContent = messageContent.substr(1);
            }

            //中文编码
            messageContent = encodeURIComponent(messageContent);

            if(self.dolinaIM) {
                var sentMsg = self.dolinaIM.sendTextMessage(messageContent);
                self.lastSentMsgContent = messageContent;
            }

            self.msgList.push(sentMsg);

            try{
                self.showMessage(sentMsg);
            }catch(e){
                console.log("message parse error");
            }
        }

        //清除输入框内容
        self.clearEditBox = function()
        {
            var messageContainer = document.getElementById("dolinaMessageContainer");

            while(messageContainer.hasChildNodes())
            {
                messageContainer.removeChild(messageContainer.firstChild);
            }

            var icon = document.createElement("i");
            icon.setAttribute("id", "attachmentBtn");
            icon.setAttribute("class", "dolina-iconfont");
            icon.innerHTML = "&#xe600;";
            messageContainer.appendChild(icon);

            var newBox = document.createElement("div");
            newBox.setAttribute("id", "dolina-message-box");
            newBox.setAttribute("class", "dolina-footer-editor");
            newBox.setAttribute("contenteditable", "true");

            //监听paste时间，去除html标签，只paste纯文本
            newBox.addEventListener('paste', function(e){

                // cancel paste
                e.preventDefault();

                var text = '';
                //var that = $(this);

                if (e.clipboardData)
                    text = e.clipboardData.getData('text/plain');
                else if (window.clipboardData)
                    text = window.clipboardData.getData('Text');
                else if (e.originalEvent.clipboardData)
                    text = $('<div></div>').text(e.originalEvent.clipboardData.getData('text'));

                if (document.queryCommandSupported('insertText')) {
                    document.execCommand('insertHTML', false, text);
                    return false;
                }
                /*else { // IE > 7
                    that.find('*').each(function () {
                        $(this).addClass('within');
                    });

                    setTimeout(function () {
                        // nochmal alle durchlaufen
                        that.find('*').each(function () {
                            $(this).not('.within').contents().unwrap();
                        });
                    }, 1);
                }*/
            })

            messageContainer.appendChild(newBox);

            //auto focus
            setTimeout(function(){newBox.focus();},0);
        }

        self.scrollToBottom = function()
        {
            document.getElementById('dolina-body').scrollTop = document.getElementById('dolina-body').scrollHeight;
        }

        self.sendAttach = function(attachUrl, attachInfo) {

            if(self.dolinaIM)
            {
                var sentMsg = self.dolinaIM.sendAttachMessage(attachUrl, attachInfo);
                self.lastSentMsgContent = dolina_i18n.t("send_image");
            }

            self.msgList.push(sentMsg);
            try{
                self.showMessage(sentMsg);
            }catch(e){
                console.log("message parse error");
            }
        }

        Date.prototype.format = function(format) {
            var date = {
                "M+": this.getMonth() + 1,
                "d+": this.getDate(),
                "h+": this.getHours(),
                "m+": this.getMinutes(),
                "s+": this.getSeconds(),
                "q+": Math.floor((this.getMonth() + 3) / 3),
                "S+": this.getMilliseconds()
            };
            if (/(y+)/i.test(format)) {
                format = format.replace(RegExp.$1, (this.getFullYear() + '').substr(4 - RegExp.$1.length));
            }
            for (var k in date) {
                if (new RegExp("(" + k + ")").test(format)) {
                    format = format.replace(RegExp.$1, RegExp.$1.length == 1
                        ? date[k] : ("00" + date[k]).substr(("" + date[k]).length));
                }
            }
            return format;
        }

        self.transformTime = function(timestamp){
            var timeShift = Math.round((new Date()).getTime() / 1000) - timestamp;
            var SECONDS_IN_ONE_DAY = 24*60*60;
            if(timeShift>SECONDS_IN_ONE_DAY)
                return Math.floor(timeShift/SECONDS_IN_ONE_DAY) + "天前";
            else
                return (new Date(timestamp * 1000)).format("hh:mm:ss");
        }

        document.onkeydown = function(event){
            var e = event || window.event || arguments.callee.caller.arguments[0];
            if(e && e.keyCode==13 && !self.windowClosed){ // enter 键
                self.send();
            }
        };


        self.randomString = function(len) {
            len = len || 32;
            var $chars = 'ABCDEFGHJKMNPQRSTWXYZabcdefhijkmnprstwxyz2345678';    /****默认去掉了容易混淆的字符oOLl,9gq,Vv,Uu,I1****/
            var maxPos = $chars.length;
            var pwd = '';
            for (i = 0; i < len; i++) {
                pwd += $chars.charAt(Math.floor(Math.random() * maxPos));
            }
            return pwd;
        }

        self.reportEvent = function(eventName, data)
        {
            if(self.dolinaIM && self.dolinaAnalysis)
            {
                self.dolinaAnalysis.report(eventName, data);
            }else{
                var unreport = {};
                unreport.event = eventName;
                unreport.data = data;
                self.unreported.push(unreport);
                setTimeout(dolina.tryReport, 3000);
            }
        }

        self.tryReport = function()
        {
            if(self.dolinaAnalysis == null || self.dolinaIM == null)
            {
                return;
            }
            for(var i=0; i<self.unreported.length; i++)
            {
                self.reportEvent(self.unreported[i].event, self.unreported[i].data);
            }
            //清空未上报事件列表
            self.unreported = new Array();
        }

        function mobileCheck() {
            var check = false;
            (function(a){if(/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i.test(a)||/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(a.substr(0,4))) check = true;})(navigator.userAgent||navigator.vendor||window.opera);
            return check;
        };

        return self;
    }
};


;(function (root, factory) {

    /* CommonJS */
    if (typeof module == 'object' && module.exports) module.exports = factory()

    /* AMD module */
    else if (typeof define == 'function' && define.amd) define(factory)

    /* Browser global */
    else root.dolinaSpinner = factory()
}(this, function () {
    "use strict"

    var prefixes = ['webkit', 'Moz', 'ms', 'O'] /* Vendor prefixes */
        , animations = {} /* Animation rules keyed by their name */
        , useCssAnimations /* Whether to use CSS animations or setTimeout */
        , sheet /* A stylesheet to hold the @keyframe or VML rules. */

    /**
     * Utility function to create elements. If no tag name is given,
     * a DIV is created. Optionally properties can be passed.
     */
    function createEl (tag, prop) {
        var el = document.createElement(tag || 'div')
            , n

        for (n in prop) el[n] = prop[n]
        return el
    }

    /**
     * Appends children and returns the parent.
     */
    function ins (parent /* child1, child2, ...*/) {
        for (var i = 1, n = arguments.length; i < n; i++) {
            parent.appendChild(arguments[i])
        }

        return parent
    }

    /**
     * Creates an opacity keyframe animation rule and returns its name.
     * Since most mobile Webkits have timing issues with animation-delay,
     * we create separate rules for each line/segment.
     */
    function addAnimation (alpha, trail, i, lines) {
        var name = ['opacity', trail, ~~(alpha * 100), i, lines].join('-')
            , start = 0.01 + i/lines * 100
            , z = Math.max(1 - (1-alpha) / trail * (100-start), alpha)
            , prefix = useCssAnimations.substring(0, useCssAnimations.indexOf('Animation')).toLowerCase()
            , pre = prefix && '-' + prefix + '-' || ''

        if (!animations[name]) {
            sheet.insertRule(
                '@' + pre + 'keyframes ' + name + '{' +
                '0%{opacity:' + z + '}' +
                start + '%{opacity:' + alpha + '}' +
                (start+0.01) + '%{opacity:1}' +
                (start+trail) % 100 + '%{opacity:' + alpha + '}' +
                '100%{opacity:' + z + '}' +
                '}', sheet.cssRules.length)

            animations[name] = 1
        }

        return name
    }

    /**
     * Tries various vendor prefixes and returns the first supported property.
     */
    function vendor (el, prop) {
        var s = el.style
            , pp
            , i

        prop = prop.charAt(0).toUpperCase() + prop.slice(1)
        if (s[prop] !== undefined) return prop
        for (i = 0; i < prefixes.length; i++) {
            pp = prefixes[i]+prop
            if (s[pp] !== undefined) return pp
        }
    }

    /**
     * Sets multiple style properties at once.
     */
    function css (el, prop) {
        for (var n in prop) {
            el.style[vendor(el, n) || n] = prop[n]
        }

        return el
    }

    /**
     * Fills in default values.
     */
    function merge (obj) {
        for (var i = 1; i < arguments.length; i++) {
            var def = arguments[i]
            for (var n in def) {
                if (obj[n] === undefined) obj[n] = def[n]
            }
        }
        return obj
    }

    /**
     * Returns the line color from the given string or array.
     */
    function getColor (color, idx) {
        return typeof color == 'string' ? color : color[idx % color.length]
    }

    // Built-in defaults

    var defaults = {
        lines: 12             // The number of lines to draw
        , length: 7             // The length of each line
        , width: 5              // The line thickness
        , radius: 10            // The radius of the inner circle
        , scale: 1.0            // Scales overall size of the spinner
        , corners: 1            // Roundness (0..1)
        , color: '#000'         // #rgb or #rrggbb
        , opacity: 1/4          // Opacity of the lines
        , rotate: 0             // Rotation offset
        , direction: 1          // 1: clockwise, -1: counterclockwise
        , speed: 1              // Rounds per second
        , trail: 100            // Afterglow percentage
        , fps: 20               // Frames per second when using setTimeout()
        , zIndex: 2e9           // Use a high z-index by default
        , className: 'spinner'  // CSS class to assign to the element
        , top: '50%'            // center vertically
        , left: '50%'           // center horizontally
        , shadow: false         // Whether to render a shadow
        , hwaccel: false        // Whether to use hardware acceleration (might be buggy)
        , position: 'absolute'  // Element positioning
    }

    /** The constructor */
    function dolinaSpinner (o) {
        this.opts = merge(o || {}, dolinaSpinner.defaults, defaults)
    }

    // Global defaults that override the built-ins:
    dolinaSpinner.defaults = {}

    merge(dolinaSpinner.prototype, {
        /**
         * Adds the spinner to the given target element. If this instance is already
         * spinning, it is automatically removed from its previous target b calling
         * stop() internally.
         */
        spin: function (target) {
            this.stop()

            var self = this
                , o = self.opts
                , el = self.el = createEl(null, {className: o.className})

            css(el, {
                position: o.position
                , width: 0
                , zIndex: o.zIndex
                , left: o.left
                , top: o.top
            })

            if (target) {
                target.insertBefore(el, target.firstChild || null)
            }

            el.setAttribute('role', 'progressbar')
            self.lines(el, self.opts)

            if (!useCssAnimations) {
                // No CSS animation support, use setTimeout() instead
                var i = 0
                    , start = (o.lines - 1) * (1 - o.direction) / 2
                    , alpha
                    , fps = o.fps
                    , f = fps / o.speed
                    , ostep = (1 - o.opacity) / (f * o.trail / 100)
                    , astep = f / o.lines

                    ;(function anim () {
                    i++
                    for (var j = 0; j < o.lines; j++) {
                        alpha = Math.max(1 - (i + (o.lines - j) * astep) % f * ostep, o.opacity)

                        self.opacity(el, j * o.direction + start, alpha, o)
                    }
                    self.timeout = self.el && setTimeout(anim, ~~(1000 / fps))
                })()
            }
            return self
        }

        /**
         * Stops and removes the dolinaSpinner.
         */
        , stop: function () {
            var el = this.el
            if (el) {
                clearTimeout(this.timeout)
                if (el.parentNode) el.parentNode.removeChild(el)
                this.el = undefined
            }
            return this
        }

        /**
         * Internal method that draws the individual lines. Will be overwritten
         * in VML fallback mode below.
         */
        , lines: function (el, o) {
            var i = 0
                , start = (o.lines - 1) * (1 - o.direction) / 2
                , seg

            function fill (color, shadow) {
                return css(createEl(), {
                    position: 'absolute'
                    , width: o.scale * (o.length + o.width) + 'px'
                    , height: o.scale * o.width + 'px'
                    , background: color
                    , boxShadow: shadow
                    , transformOrigin: 'left'
                    , transform: 'rotate(' + ~~(360/o.lines*i + o.rotate) + 'deg) translate(' + o.scale*o.radius + 'px' + ',0)'
                    , borderRadius: (o.corners * o.scale * o.width >> 1) + 'px'
                })
            }

            for (; i < o.lines; i++) {
                seg = css(createEl(), {
                    position: 'absolute'
                    , top: 1 + ~(o.scale * o.width / 2) + 'px'
                    , transform: o.hwaccel ? 'translate3d(0,0,0)' : ''
                    , opacity: o.opacity
                    , animation: useCssAnimations && addAnimation(o.opacity, o.trail, start + i * o.direction, o.lines) + ' ' + 1 / o.speed + 's linear infinite'
                })

                if (o.shadow) ins(seg, css(fill('#000', '0 0 4px #000'), {top: '2px'}))
                ins(el, ins(seg, fill(getColor(o.color, i), '0 0 1px rgba(0,0,0,.1)')))
            }
            return el
        }

        /**
         * Internal method that adjusts the opacity of a single line.
         * Will be overwritten in VML fallback mode below.
         */
        , opacity: function (el, i, val) {
            if (i < el.childNodes.length) el.childNodes[i].style.opacity = val
        }

    })


    function initVML () {

        /* Utility function to create a VML tag */
        function vml (tag, attr) {
            return createEl('<' + tag + ' xmlns="urn:schemas-microsoft.com:vml" class="spin-vml">', attr)
        }

        // No CSS transforms but VML support, add a CSS rule for VML elements:
        sheet.addRule('.spin-vml', 'behavior:url(#default#VML)')

        dolinaSpinner.prototype.lines = function (el, o) {
            var r = o.scale * (o.length + o.width)
                , s = o.scale * 2 * r

            function grp () {
                return css(
                    vml('group', {
                        coordsize: s + ' ' + s
                        , coordorigin: -r + ' ' + -r
                    })
                    , { width: s, height: s }
                )
            }

            var margin = -(o.width + o.length) * o.scale * 2 + 'px'
                , g = css(grp(), {position: 'absolute', top: margin, left: margin})
                , i

            function seg (i, dx, filter) {
                ins(
                    g
                    , ins(
                        css(grp(), {rotation: 360 / o.lines * i + 'deg', left: ~~dx})
                        , ins(
                            css(
                                vml('roundrect', {arcsize: o.corners})
                                , { width: r
                                    , height: o.scale * o.width
                                    , left: o.scale * o.radius
                                    , top: -o.scale * o.width >> 1
                                    , filter: filter
                                }
                            )
                            , vml('fill', {color: getColor(o.color, i), opacity: o.opacity})
                            , vml('stroke', {opacity: 0}) // transparent stroke to fix color bleeding upon opacity change
                        )
                    )
                )
            }

            if (o.shadow)
                for (i = 1; i <= o.lines; i++) {
                    seg(i, -2, 'progid:DXImageTransform.Microsoft.Blur(pixelradius=2,makeshadow=1,shadowopacity=.3)')
                }

            for (i = 1; i <= o.lines; i++) seg(i)
            return ins(el, g)
        }

        dolinaSpinner.prototype.opacity = function (el, i, val, o) {
            var c = el.firstChild
            o = o.shadow && o.lines || 0
            if (c && i + o < c.childNodes.length) {
                c = c.childNodes[i + o]; c = c && c.firstChild; c = c && c.firstChild
                if (c) c.opacity = val
            }
        }
    }

    if (typeof document !== 'undefined') {
        sheet = (function () {
            var el = createEl('style', {type : 'text/css'})
            ins(document.getElementsByTagName('head')[0], el)
            return el.sheet || el.styleSheet
        }())

        var probe = css(createEl('group'), {behavior: 'url(#default#VML)'})

        if (!vendor(probe, 'transform') && probe.adj) initVML()
        else useCssAnimations = vendor(probe, 'animation')
    }

    return dolinaSpinner

}));
