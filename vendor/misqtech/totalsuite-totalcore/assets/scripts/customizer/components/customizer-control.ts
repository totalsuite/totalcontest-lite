namespace TotalCore.Customizer.Components {
    import Component = TotalCore.Common.Component;

    @Component('components.customizer', {
        templateUrl: 'customizer-control-component-template',
        bindings: {
            type: '@',
            label: '@',
            help: '@',
            options: '<',
            ngModel: '=',
        },
        transclude: true,
    })
    class CustomizerControl {
        private help;
        private label;
        private ngModel;
        private options;
        private type = 'text';

        getTemplate() {
            return `customizer-control-${this.type}-template`;
        }
    }
}