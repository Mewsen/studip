import Scroll from './scroll.js';

let fold;
let was_below_the_fold = false;

const back_to_top = function(scrolltop) {
    let is_below_the_fold = scrolltop > fold;
    if (is_below_the_fold !== was_below_the_fold) {
        document.getElementById('scroll-to-top').classList.toggle('hide', !is_below_the_fold);
        was_below_the_fold = is_below_the_fold;
    }
};

const ScrollToTop = {
    enable() {
        var minScrollHeight = Math.min(
            document.body.scrollHeight, document.documentElement.scrollHeight,
            document.body.offsetHeight, document.documentElement.offsetHeight,
            document.body.clientHeight, document.documentElement.clientHeight
        );
        fold = minScrollHeight - (minScrollHeight / 5); // top of fifth portion!
        Scroll.addHandler('back-to-top', back_to_top);
    },
    disable() {
        Scroll.removeHandler('header');
        document.getElementById('scroll-to-top').classList.add('hide');
    },
    moveBack() {
        document.getElementById('scroll-to-top').addEventListener('click', (evt)  => {
            evt.preventDefault();
            this.toTop();
        });
        document.getElementById('scroll-to-top').addEventListener('keypress', (evt) => {
            if (evt.code === 'Space') {
                this.toTop();
            }
        });
    },
    toTop() {
        window.scroll({top: 0, left: 0, behavior: 'smooth'});
    }
};

export default ScrollToTop;
