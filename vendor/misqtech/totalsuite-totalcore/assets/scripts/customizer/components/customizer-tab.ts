namespace TotalCore.Customizer.Components {
    import Component = TotalCore.Common.Component;

    @Component('components.customizer', {
        templateUrl: 'customizer-tab-component-template',
        bindings: {
            target: '@',
        },
        require: {
            $customizer: '^customizer',
            $content: '?^^customizerTabContent',
        },
        transclude: true,
    })
    class CustomizerTab {
        private $content;
        private target;

        getTarget() {
            return [this.$content ? this.$content.getTarget() : null, this.target].filter(Boolean).join('.');
        }
    }
}