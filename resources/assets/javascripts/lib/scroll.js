/**
 * Provides means to hook into the scroll event. Registered callbacks are
 * called with the current scroll top and scroll left position so both
 * vertical and horizontal scroll events can be handled.
 *
 * Updates/calls to the callback are synchronized to screen refresh by using
 * the animation frame method (which will fallback to a timer based solution).
 */
const handlers = {};
let animId = false;

let lastTop = null;
let lastLeft = null;

function refresh() {
    const hasHandlers = Object.keys(handlers).length > 0;
    if (!hasHandlers && animId !== false) {
        window.cancelAnimationFrame(animId);
        animId = false;
    } else if (hasHandlers && animId === false) {
        animId = window.requestAnimationFrame(() => Scroll.executeHandlers());
    }
}

function engageScrollTrigger() {
    window.removeEventListener('scroll', refresh);
    window.addEventListener('scroll', refresh, {once: true});
}

const Scroll = {
    executeHandlers(only_these = []) {
        const scrollTop = document.scrollingElement.scrollTop;
        const scrollLeft = document.scrollingElement.scrollLeft;

        if (scrollTop !== lastTop || scrollLeft !== lastLeft) {
            for (const [index, handler] of Object.entries(handlers)) {
                if (only_these.length === 0 || only_these.includes(index)) {
                    handler(scrollTop, scrollLeft);
                }
            }

            lastTop  = scrollTop;
            lastLeft = scrollLeft;
        }

        animId = false;

        engageScrollTrigger();
    },
    addHandler(index, handler, immediate = false) {
        handlers[index] = handler;
        engageScrollTrigger();

        if (immediate) {
            Scroll.executeHandlers([index]);
        }
    },
    removeHandler(index) {
        delete handlers[index];
        engageScrollTrigger();
    }
};

export default Scroll;
