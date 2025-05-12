/**
 * @package    App
 * @subpackage Template JS core
 * @since      1.0.0
 * @version    1.0.0
 * @author     Ehsan Zare <darksider.legend@gmail.com>
 * @link       https://code-path.com
 * @copyright  (c) 2023 Code-path web developing team
 */

const App = (() => {
    // THIS object
    const self = {};

    // Define App selector
    self.Selector = {
        // Input
        input: 'input',

        // Slider
        slider: '#slider',

        // Selectbox
        sbHolder: '.sbHolder',
        selectbox: 'select:not([sb])',
        selectboxLazy: 'select[sb=\'lazy\']'
    };

    // Define App attribute
    self.Attribute = {};

    /**
     * Initialize slider
     * @return void
     */
    self.initSlider = () => {
        // Failed to load flux plugin
        if(typeof flux === 'undefined')
            return;

        // Get elements
        const $slider = $(self.Selector.slider);

        // No need to go any further
        if(!$slider.length)
            return;

        // Prepare slider config
        const sliderCFG = {
            delay: Config.Slider.interval,
            autoplay: true,
            captions: true,
            pagination: true,
            transitions: new Array('dissolve')
        };

        // Call flux
        window.myFlux = new flux.slider(self.Selector.slider, sliderCFG)
    };

    /**
     * Initialize iCheck
     * @param  boolean refresh
     * @return void
     */
    self.initIcheck = (refresh) => {
        // Failed to load iCheck plugin
        if(typeof $.fn.iCheck === 'undefined')
            return;

        // Old browsers compatibility
        refresh = refresh || false;

        // Call iCheck
        $(self.Selector.input).iCheck({radioClass: 'iradio_futurico', checkboxClass: 'icheckbox_futurico'}).on('ifChecked ifUnchecked', () => {$(this).change();});
    };

    /**
     * Initialize selectbox
     * @param  boolean refresh
     * @return void
     */
    self.initSelectbox = (refresh) => {
        // Failed to load selectbox plugin
        if(typeof $.fn.selectbox === 'undefined')
            return;

        // Old browsers compatibility
        refresh = refresh || false;

        // Loop through selects
        ((refresh) ? $(self.Selector.selectboxLazy) : $(self.Selector.selectbox)).each(function()
        {
            const visibility = $(this).css('display') !== 'none';

            // Call selectbox
            $(this).selectbox({onChange: (value) => {$(this).val(value).change();}}).next(self.Selector.sbHolder).attr('style', $(this).attr('style'))[visibility ? 'show' : 'hide']();
        });
    };

    /**
     * Initialize actions
     * @return void
     */
    self.init = () => {
        self.initSlider();
        self.initIcheck();
        self.initSelectbox();

        // [DataTables init] Call iCheck and selectbox
        $('body').on('init.dt', (e, ctx) => {
            self.initIcheck();
            self.initSelectbox();
        });
    };

    return self;
})();

// Call APP when content is loaded
(document.readyState !== 'loading') ? App.init() : document.addEventListener('DOMContentLoaded', () => { App.init(); });
