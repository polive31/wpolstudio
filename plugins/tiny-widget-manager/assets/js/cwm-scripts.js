(function($) {
    function initCwmWidgetControls(context) {
        $(context).find('.cwm-widget-controls').each(function () {
            const $container = $(this);

            // Onglets
            $container.find('.cwm-tab-nav li').off('click').on('click', function () {
                const tab = $(this).data('tab');
                $container.find('.cwm-tab-nav li').removeClass('active');
                $(this).addClass('active');
                $container.find('.cwm-tab-content').removeClass('active').hide();
                $container.find(`.cwm-tab-content[data-tab="${tab}"]`).addClass('active').show();
            });

            // Selectize
            $container.find('.cwm-selectize').each(function () {
                if (!this.selectize) {
                    $(this).selectize({
                        plugins: ['remove_button'],
                        delimiter: ',',
                        persist: false
                    });
                }
            });

            // Mise à jour JSON
            $container.find('.cwm-mode, .cwm-selectize').off('change').on('change', function () {
                const data = {};

                $container.find('.cwm-tab-content').each(function () {
                    const $tab = $(this);
                    const type = $tab.data('tab');
                    const mode = $tab.find('.cwm-mode').val();
                    const items = $tab.find('.cwm-selectize').val() || [];
                    data[type] = { mode, items };
                });

                $container.find('.cwm-json-data').val(JSON.stringify(data));
            });

            // Onglet actif au chargement
            $container.find('.cwm-tab-nav li.active').trigger('click');
        });
    }

    $(document).ready(function () {
        initCwmWidgetControls(document);
    });

    $(document).on('widget-updated widget-added', function (event, widget) {
        initCwmWidgetControls(widget);
    });
})(jQuery);
