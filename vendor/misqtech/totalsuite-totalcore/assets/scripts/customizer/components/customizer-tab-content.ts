namespace TotalCore.Customizer.Components {
    import Component = TotalCore.Common.Component;

    @Component('components.customizer', {
        templateUrl: 'customizer-tab-content-component-template',
        bindings: {
            name: '@',
        },
        require: {
            $customizer: '^customizer',
            $content: '?^^customizerTabContent',
        },
        transclude: true,
    })
    class CustomizerTabContent {
        private $content;
        private name;

        getTarget() {
            return [this.$content ? this.$content.getTarget() : null, this.name].filter(Boolean).join('.');
        }
    }
}