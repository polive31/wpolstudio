(function ($) {
    function initCwmWidgetControls(context) {
        $(context).find('.twim-widget-controls').each(function () {
            const $container = $(this);

            // Tabs
            $container.find('.twim-tab-nav li').off('click').on('click', function () {
                const tab = $(this).data('tab');
                $container.find('.twim-tab-nav li').removeClass('active');
                $(this).addClass('active');
                $container.find('.twim-tab-content').removeClass('active').hide();
                $container.find(`.twim-tab-content[data-tab="${tab}"]`).addClass('active').show();
            });


            // Selectize with dropdown list
            $container.find('.twim-selectize').each(function () {
                if (!this.selectize) {
                    $(this).selectize({
                        plugins: ['remove_button'],
                        delimiter: ',',
                        persist: false,
                        render: {
                            option: function (data, escape) {
                                const level = Number(data.level) || 0;
                                const indent = '&nbsp;'.repeat(level * 4);
                                return `<div class="option">${indent}${escape(data.text)}</div>`;
                            }
                        }
                    });
                }
            });


            // Selectize with autocomplete
            // $container.find('.twim-selectize.autocomplete').each(function () {
            //     if (!this.selectize) {
            //         // Selectize with autocomplete
            //         $(this).selectize({
            //             plugins: ['remove_button'],
            //             delimiter: ',',
            //             persist: false,
            //             valueField: 'id',
            //             labelField: 'title',
            //             searchField: 'title',
            //             load: function (query, callback) {
            //                 if (!query.length) return callback();
            //                 $.ajax({
            //                     url: ajaxurl, // WordPress built-in AJAX handler
            //                     type: 'POST',
            //                     dataType: 'json',
            //                     data: {
            //                         action: 'twim_search_posts',
            //                         nonce: cwmWidget.nonce, // Make sure this is localized
            //                         q: query
            //                     },
            //                     error: function () {
            //                         callback();
            //                     },
            //                     success: function (res) {
            //                         callback(res.data || []);
            //                     }
            //                 });
            //             }
            //         });
            //     }
            // });


            // JSON update
            // $container.find('.twim-mode, .twim-selectize').off('change').on('change', function () {
            //     const data = {};

            //     $container.find('.twim-tab-content').each(function () {
            //         const $tab = $(this);
            //         const type = $tab.data('tab');
            //         const mode = $tab.find('.twim-mode').val();
            //         const items = $tab.find('.twim-selectize').val() || [];
            //         data[type] = { mode, items };
            //     });

            //     $container.find('.twim-json-data').val(JSON.stringify(data));
            // });

            // Active tab on load
            $container.find('.twim-tab-nav li.active').trigger('click');
        });

    }

    $(document).ready(function () {
        initCwmWidgetControls(document);
    });

    $(document).on('widget-updated widget-added', function (event, widget) {
        initCwmWidgetControls(widget);
    });

    /* ----------------------------------------------------------------------------------------------------------------*/
    /*                                                 STORE LAST ACTIVE NAV TAB
    /* ----------------------------------------------------------------------------------------------------------------*/

    let isSavingWidget = false;

    $(document).on('click', '.widget-control-save', function () {
        isSavingWidget = true;

        // Optional: clear flag after short delay, or after widget-updated
        setTimeout(() => {
            isSavingWidget = false;
        }, 2000); // adjust if needed
    });

    // Store selected tab on click
    $(document).on('click', '.twim-tab-nav li', function () {
        // Prevent active tab saving if on widget save event
        if (isSavingWidget) {
            console.log('Tab click ignored during widget save');
            return;
        }

        const $li = $(this);
        const tab = $li.data('tab'); // or use text() or attr()
        const $widget = $li.closest('.widget');
        $widget.data('twim-active-tab', tab);
    });

    function restoreTabState($widget) {
        const activeTab = $widget.data('twim-active-tab');
        if (activeTab) {
            $widget.find('.twim-tab-nav li').removeClass('active');
            $widget.find('.twim-tab-nav li[data-tab="' + activeTab + '"]').addClass('active');
            $widget.find('.twim-tab-content').hide();
            $widget.find('.twim-tab-content[data-tab="' + activeTab + '"]').show();
        }
    }

    $('.widget').each(function () {
        const widget = this;

        const observer = new MutationObserver(function (mutations) {
            mutations.forEach(function (mutation) {
                if (
                    mutation.type === 'attributes' &&
                    mutation.attributeName === 'class'
                ) {
                    const wasDirty = mutation.oldValue.includes('widget-dirty');
                    const isDirtyNow = widget.classList.contains('widget-dirty');

                    if (wasDirty && !isDirtyNow) {
                        restoreTabState($(widget));
                    }
                }
            });
        });

        observer.observe(widget, {
            attributes: true,
            attributeFilter: ['class'],
            attributeOldValue: true
        });
    });


})(jQuery);
