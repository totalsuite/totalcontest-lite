namespace TotalCore.Common.Models {
    export class Model {
        protected attributes;

        constructor(attributes) {
            this.attributes = attributes;
        }

        get(prop, defaultValue = null) {
            try {
                let path = this.attributes;

                prop.split('.').forEach((part) => {
                    path = path[part];
                });

                return typeof path === 'undefined' ? defaultValue : path;
            } catch (ex) {
                return defaultValue;
            }
        }

        getFlatten() {
            var result = {};

            function recurse(cur, prop) {
                if (Object(cur) !== cur) {
                    result[prop] = cur;
                } else if (Array.isArray(cur)) {
                    for (var i = 0, l = cur.length; i < l; i++)
                        recurse(cur[i], prop + "[" + i + "]");
                    if (l == 0)
                        result[prop] = [];
                } else {
                    var isEmpty = true;
                    for (var p in cur) {
                        isEmpty = false;
                        recurse(cur[p], prop ? prop + "." + p : p);
                    }
                    if (isEmpty && prop)
                        result[prop] = {};
                }
            }

            recurse(this.getRaw(), "");
            return result;
        }

        getId() {
            return this.get('id');
        }

        getRaw() {
            return this.attributes;
        }

        set(prop, value) {
            let path = this.attributes;
            let parts = prop.split('.');
            parts.forEach((part, index) => {
                if (!path[part]) {
                    path[part] = {};
                }

                if (index == (parts.length - 1)) {
                    path[part] = value;
                } else {
                    path = path[part];
                }
            });


            return path;
        }
    }
}