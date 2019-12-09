/**
 * Decorators.
 */
namespace TotalCore.Common {
    import IComponentOptions = angular.IComponentOptions;
    import IInjectorService = angular.auto.IInjectorService;

    /**
     * A small helper to inject dependencies dynamically.
     *
     * @param func
     */
    function annotate(func: any) {
        const $injector: IInjectorService = angular.injector(['ng']);
        func.$inject = $injector.annotate(func).map(member => member.replace(/^_/, ''));
    }

    /**
     * Injectable decorator.
     *
     * @returns {(Entity: any) => void}
     * @constructor
     */
    export function Injectable() {
        return (Entity: any) => {
            annotate(Entity);
        }
    }

    /**
     * Service decorator.
     *
     * @param {string} moduleName
     * @returns {(Service: any) => void}
     * @constructor
     */
    export function Service(moduleName: string) {
        return (Service: any) => {
            let module;
            const name = Service.name;
            const isProvider = Service.hasOwnProperty('$get');

            annotate(Service);

            try {
                module = angular.module(moduleName);
            } catch (exception) {
                module = angular.module(moduleName, []);
            }
            module[isProvider ? 'provider' : 'service'](name, Service);
        }
    }

    /**
     * Factory decorator.
     *
     * @param {string} moduleName
     * @param selector
     * @returns {(Factory: any) => void}
     * @constructor
     */
    export function Factory(moduleName: string, selector?) {
        return (Factory: any) => {
            let module;
            const name = selector || `${Factory.name.charAt(0).toLowerCase()}${Factory.name.slice(1)}`.replace('Factory', '');

            annotate(Factory);

            try {
                module = angular.module(moduleName);
            } catch (exception) {
                module = angular.module(moduleName, []);
            }
            module.factory(name, Factory);
        }
    }

    /**
     * Controller decorator.
     *
     * @param {string} moduleName
     * @returns {(Controller: any) => void}
     * @constructor
     */
    export function Controller(moduleName: string) {
        return (Controller: any) => {
            let module;
            const name = Controller.name;

            annotate(Controller);

            try {
                module = angular.module(moduleName);
            } catch (exception) {
                module = angular.module(moduleName, []);
            }
            module.controller(name, Controller);
        }
    }

    /**
     * Filter decorator.
     *
     * @param {string} moduleName
     * @param selector
     * @returns {(Filter: any) => void}
     * @constructor
     */
    export function Filter(moduleName: string, selector?) {
        return (Filter: any) => {
            let module;
            const name = selector || `${Filter.name.charAt(0).toLowerCase()}${Filter.name.slice(1)}`.replace('Filter', '');

            annotate(Filter);

            try {
                module = angular.module(moduleName);
            } catch (exception) {
                module = angular.module(moduleName, []);
            }
            module.filter(name, Filter);
        }
    }

    /**
     * Component decorator.
     *
     * @param moduleName
     * @param {angular.IComponentOptions} options
     * @param {any} selector
     * @returns {(Class: any) => void}
     * @constructor
     */
    export function Component(moduleName, options: IComponentOptions, selector = null) {
        return (Class: any) => {
            let module;
            selector = selector || `${Class.name.charAt(0).toLowerCase()}${Class.name.slice(1)}`.replace('Component', '');

            options.controller = Class;

            annotate(Class);

            try {
                module = angular.module(moduleName);
            } catch (exception) {
                module = angular.module(moduleName, []);
            }
            module.component(selector, options);
        }
    }

    /**
     * Directive decorator.
     *
     * @param moduleName
     * @param {any} selector
     * @returns {(Class: any) => void}
     * @constructor
     */
    export function Directive(moduleName, selector = null) {
        return (Class: any) => {
            let module;
            selector = selector || `${Class.name.charAt(0).toLowerCase()}${Class.name.slice(1)}`.replace('Directive', '');

            annotate(Class);

            try {
                module = angular.module(moduleName);
            } catch (exception) {
                module = angular.module(moduleName, []);
            }

            module.directive(selector, Class);
        }
    }
}