/**
 * @package    APP
 * @subpackage Template JS core
 * @since      1.0.0
 * @version    1.0.0
 * @author     Ehsan Zare <darksider.legend@gmail.com>
 * @link       https://code-path.com
 * @copyright  (c) 2021 Code-path web developing team
 */

let App = (function()
{
    // THIS object
    let self = {};

    /**
     * Handle sticky navbar
     * @return void
     */
    self.stickyNav = function()
    {
        const body       = document.body;
        const scrollUp   = 'scroll-up';
        const scrollDown = 'scroll-down';

        let lastScroll = 0;

        window.addEventListener('scroll', () => {
            const currentScroll = window.pageYOffset;

            if(currentScroll <= 0)
            {
                body.classList.remove(scrollUp);
                return;
            }

            // Down
            if(currentScroll > lastScroll && !body.classList.contains(scrollDown))
            {
                body.classList.remove(scrollUp);
                body.classList.add(scrollDown);
            }
            // Up
            else if(currentScroll < lastScroll && body.classList.contains(scrollDown))
            {
                body.classList.remove(scrollDown);
                body.classList.add(scrollUp);
            }

            lastScroll = currentScroll;
        });
    }

    /**
     * Initialize owlCarousel
     * @return void
     */
    self.owlCarousel = function()
    {
        $('[owl-carousel="main"]').owlCarousel({
            nav: false,
            loop: true,
            margin: 16,
            responsive: {
                0: { items: 1 }
            }
        });
    }

    /**
     * Toggler
     * @return void
     */
    self.toggler = function()
    {
        $(document.body).on('click', '[data-toggle]', function()
        {
            let $this   = $(this),
                $panel  = $($(this).data('toggle') + ':visible'),
                $target = $($(this).data('target') + ':hidden');

            if(!$panel.length || !$target.length || $this.hasClass('active'))
                return;

            $panel.not($target).stop(true, true).fadeOut('fast', function() {
                $target.stop(true, true).fadeIn('fast');
                $('[data-toggle="' + $this.data('toggle') + '"]').removeClass('active');
                $this.addClass('active');
            });
        });
    }

    /**
     * Initialize actions
     * @return void
     */
    self.init = function()
    {
        self.toggler();
        self.stickyNav();
        self.owlCarousel();
    }

    /**
     * Fire whole thing
     * @return void
     */
    self.__start__ = function()
    {
        // Call APP
        self.init();
    }

    return self;
}());

// Call APP when content is loaded
(document.readyState !== 'loading') ? App.__start__() : document.addEventListener('DOMContentLoaded', function() { App.__start__(); });
