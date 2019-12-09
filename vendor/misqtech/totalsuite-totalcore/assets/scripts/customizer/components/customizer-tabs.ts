namespace TotalCore.Customizer.Components {
    import Component = TotalCore.Common.Component;

    @Component('components.customizer', {
        templateUrl: 'customizer-tabs-component-template',
        bindings: {
            target: '@',
        },
        require: {
            $customizer: '^customizer',
            $content: '?^^customizerTabContent',
        },
        transclude: true,
    })
    class CustomizerTabs {
        private $content;
        private target;

        getTarget() {
            return this.$content ? this.$content.getTarget() : null;
        }
    }
}