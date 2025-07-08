(function (wp) {
    const { addFilter } = wp.hooks;
    const { createHigherOrderComponent } = wp.compose;
    const { InspectorControls } = wp.blockEditor;
    const { PanelBody, SelectControl, ComboboxControl } = wp.components;
    const { Fragment, useState } = wp.element;

    // Static arrays for roles and devices
    const rolesOptions = [
        { label: 'Logged-out', value: 'logged_out' },
        { label: 'Logged-in', value: 'logged_in' },
        { label: 'Admin', value: 'administrator' },
        { label: 'Editor', value: 'editor' },
        { label: 'Subscriber', value: 'subscriber' },
    ];

    const devicesOptions = [
        { label: 'Computer', value: 'desktop' },
        // { label: 'Tablet', value: 'tablet' },
        { label: 'Mobile', value: 'mobile' },
    ];

    const sections = [
        'pages',
        'posts types',
        'posts',
        'archives',
        'roles',
        'devices',
    ];

    const sectionLabels = {
        'pages': 'Pages',
        'posts types': 'Posts Types',
        'posts': 'Posts',
        'archives': 'Archives',
        'roles': 'User Roles',
        'devices': 'Devices',
    };

    // The main panel component
    function TinyWidgetManagerPanel({ attributes, setAttributes }) {
        const options = window.cwmWidgetOptions || {};

        // State for posts autocomplete
        const [postResults, setPostResults] = useState([]);
        const [isLoadingPosts, setIsLoadingPosts] = useState(false);

        // State for accordion panels
        const [openPanels, setOpenPanels] = useState(
            sections.reduce((acc, section) => ({ ...acc, [section]: false }), {})
        );

        const value = attributes.twmVisibility || {};

        // Only the posts section
        const [postOptions, setPostOptions] = useState([]);
        const [selectedPosts, setSelectedPosts] = useState([]); // [{label, value}, ...]

        const fetchPosts = (search) => {
            setIsLoadingPosts(true);
            wp.apiFetch({
                path: `/wp/v2/posts?search=${encodeURIComponent(search)}&per_page=10`,
            }).then((posts) => {
                setPostOptions(
                    posts.map((post) => ({
                        label: post.title.rendered,
                        value: String(post.id),
                    }))
                );
                setIsLoadingPosts(false);
            });
        };

        const handlePanelToggle = (section) => {
            setOpenPanels((prev) => ({
                ...prev,
                [section]: !prev[section],
            }));
        };

        const handleChange = (newValue) => {
            setAttributes({ twmVisibility: newValue });
        };

        return (
            <InspectorControls>
                <PanelBody title="Tiny Widget Manager" initialOpen={false}>
                    <div className="twim-widget-controls">
                        {sections.map((section) => (
                            <PanelBody
                                key={section}
                                title={sectionLabels[section] || section}
                                initialOpen={!!openPanels[section]}
                                onToggle={() => handlePanelToggle(section)}
                                opened={!!openPanels[section]}
                            >
                                {section === 'posts' ? (
                                    <ComboboxControl
                                        label="Test Posts"
                                        value=""
                                        options={[]}
                                        onInputChange={(input) => {
                                            console.log('Input changed:', input);
                                        }}
                                        onChange={() => { }}
                                    />
                                    // <ComboboxControl
                                    //     multiple
                                    //     label="Select Posts"
                                    //     value={selectedPosts}
                                    //     options={postOptions}
                                    //     onInputChange={(input) => {
                                    //         if (input && input.length > 2) {
                                    //             fetchPosts(input);
                                    //         } else {
                                    //             setPostOptions([]);
                                    //         }
                                    //     }}
                                    //     onChange={(selected) => {
                                    //         setSelectedPosts(selected);
                                    //         // Save to attributes or state as needed
                                    //     }}
                                    //     isLoading={isLoadingPosts}
                                    //     help="Type to search posts"
                                    // />
                                ) : section === 'roles' ? (
                                    <SelectControl
                                        multiple
                                        label="Select User Roles"
                                        value={value.roles || []}
                                        options={rolesOptions}
                                        onChange={(selected) => {
                                            handleChange({
                                                ...value,
                                                roles: Array.isArray(selected) ? selected : [selected],
                                            });
                                        }}
                                    />
                                ) : section === 'devices' ? (
                                    <SelectControl
                                        multiple
                                        label="Select Devices"
                                        value={value.devices || []}
                                        options={devicesOptions}
                                        onChange={(selected) => {
                                            handleChange({
                                                ...value,
                                                devices: Array.isArray(selected) ? selected : [selected],
                                            });
                                        }}
                                    />
                                ) : (
                                    <SelectControl
                                        multiple
                                        label={`Select ${sectionLabels[section] || section}`}
                                        value={value[section] || []}
                                        options={Object.entries(options[section]?.items || {}).map(([val, label]) => ({
                                            label,
                                            value: val,
                                        }))}
                                        onChange={(selected) => {
                                            handleChange({
                                                ...value,
                                                [section]: Array.isArray(selected) ? selected : [selected],
                                            });
                                        }}
                                    />
                                )}
                            </PanelBody>
                        ))}
                    </div>
                </PanelBody>
            </InspectorControls>
        );
    }

    // HOC to add the panel to all widgets
    const withTinyWidgetManagerPanel = createHigherOrderComponent((BlockEdit) => {
        return (props) => (
            <Fragment>
                <BlockEdit {...props} />
                <TinyWidgetManagerPanel {...props} />
            </Fragment>
        );
    }, 'withTinyWidgetManagerPanel');

    // Apply to all widgets (or restrict to widget blocks if you want)
    addFilter(
        'editor.BlockEdit',
        'tiny-widget-manager/with-tiny-widget-manager-panel',
        withTinyWidgetManagerPanel
    );
})(window.wp);