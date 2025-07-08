wp.hooks.addFilter(
    'editor.BlockEdit',
    'cwm/with-visibility-controls',
    wp.compose.createHigherOrderComponent((BlockEdit) => {
        return (props) => {
            if (!props || !props.attributes) return <BlockEdit {...props} />;

            return (
                <>
                    <BlockEdit {...props} />
                    {props.isSelected && (
                        wp.element.createElement(
                            wp.blockEditor.InspectorControls,
                            {},
                            wp.element.createElement(
                                wp.components.PanelBody,
                                { title: 'Visibilité', initialOpen: true },
                                wp.element.createElement(
                                    wp.components.SelectControl,
                                    {
                                        label: 'Mode',
                                        value: props.attributes.twim_visibility_post_mode || 'show',
                                        options: [
                                            { label: 'Afficher', value: 'show' },
                                            { label: 'Masquer', value: 'hide' }
                                        ],
                                        onChange: (val) => props.setAttributes({ twim_visibility_post_mode: val })
                                    }
                                )
                            )
                        )
                    )}
                </>
            );
        };
    }, 'withVisibilityControls')
);
