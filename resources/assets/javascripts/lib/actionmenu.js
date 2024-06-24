/**
 * Determine whether the menu should be opened in dialog or regular layout.
 * @type {[type]}
 */
function determineBreakpoint(element) {
    return $(element).closest('.ui-dialog-content').length > 0 ? '.ui-dialog-content' : '#content';
}

/**
 * Obtain all parents of the given element that have scrollable content.
 */
function getScrollableParents(element, menu_width, menu_height) {
    const offset     = $(element).offset();
    const breakpoint = determineBreakpoint(element);

    let elements = [];
    $(element).parents().each(function () {
        // Stop at breakpoint
        if ($(this).is(breakpoint)) {
            return false;
        }

        // Exit early if overflow is visible
        const overflow = $(this).css('overflow');
        if (overflow === 'visible' || overflow === 'inherit') {
            return;
        }

        // Check whether element is overflown
        const overflown = this.scrollHeight > this.clientHeight || this.scrollWidth > this.clientWidth;
        if (overflow === 'hidden' && overflown) {
            elements.push(this);
            return;
        }

        // Check if menu fits inside element
        const offs = $(this).offset();
        const w    = $(this).width();
        const h    = $(this).height();

        if (offset.left + menu_width > offs.left + w) {
            elements.push(this);
        } else if (offset.top + menu_height > offs.top + h) {
            elements.push(this);
        }
    });

    return elements;
}

class ActionMenu
{
    static stash = new Map();
    static openMenus = [];
    static #secret = Symbol();
    static scrollHandlerState = false;


    /**
     * Create menu using a singleton pattern for each element.
     */
    static create(element, position = true) {
        const id = $(element).uniqueId().attr('id');
        const breakpoint = determineBreakpoint(element);
        if (!ActionMenu.stash.has(id)) {
            const menu_offset = $(element).offset().top + $('.action-menu-content', element).height();
            const max_offset = $(breakpoint).offset().top + $(breakpoint).height();
            const reversed = menu_offset > max_offset;

            ActionMenu.stash.set(id, new ActionMenu(ActionMenu.#secret, element, reversed, position));
        }

        return ActionMenu.stash.get(id);
    }

    /**
     * Closes all menus.
     * @return {[type]} [description]
     */
    static closeAll() {
        this.stash.forEach((menu) => menu.close());
    }

    /**
     * Private constructor by implementing the secret/passed_secret mechanism.
     */
    constructor(passed_secret, element, reversed, position) {
        // Enforce use of create (would use a private constructor if I could)
        if (ActionMenu.#secret !== passed_secret) {
            throw new Error('Cannot create ActionMenu. Use ActionMenu.create()!');
        }

        this.element = $(element);
        this.menu = this.element;
        this.content = $('.action-menu-content', element);
        this.is_reversed = reversed;
        this.is_open = false;
        this.position = position;

        const additionalClasses = Object.values({ ...this.element[0].classList }).filter((item) => item != 'action-menu');
        const menu_width  = this.content.width();
        const menu_height = this.content.height();
        
        // Reposition the menu?
        if (position) {
            let parents = getScrollableParents(this.element, menu_width, menu_height);
            if (parents.length > 0) {
                const form = this.element.closest('form');
                if (form) {
                    const id = form.uniqueId().attr('id');
                    $('.action-menu-item input[type="image"]:not([form])', this.element).attr('form', id);
                    $('.action-menu-item button:not([form])', this.element).attr('form', id);
                }

                this.menu = $('<div class="action-menu-wrapper">').append(this.content);
                $('.action-menu-icon', element).clone().data('action-menu-element', element).prependTo(this.menu);

                this.menu
                    .addClass(additionalClasses.join(' '))
                    .appendTo(parents[0]);
            } else {
                this.position = false;
            }
        }
        this.update();
    }

    toggleScrollHandler(active) {
        if (ActionMenu.scrollHandlerState === active) {
            return;
        }

        ActionMenu.scrollHandlerState = active;

        if (active) {
            document.addEventListener('scroll', this.repositionAllMenus, true);
            document.addEventListener('scrollend', this.repositionAllMenus, true);

        } else {
            document.removeEventListener('scroll', this.repositionAllMenus, true);
            document.removeEventListener('scrollend', this.repositionAllMenus, true);
        }
    }

    repositionAllMenus() {
        ActionMenu.openMenus.forEach((menu) => menu.reposition());
    }

    /**
     * Adds a class to the menu's element.
     */
    addClass(name) {
        this.menu.addClass(name);
    }

    /**
     * Open the menu.
     */
    open() {
        this.toggle(true);
    }

    /**
     * Close the menu.
     */
    close() {
        this.toggle(false);
    }

    /**
     * Toggle the menus display state. Pass a state to enforce it.
     */
    toggle(state = null) {
        this.is_open = state === null ? !this.is_open : state;

        this.update();

        if (this.is_open) {
            this.reposition();
            this.menu.find('.action-menu-icon').focus();
            ActionMenu.openMenus.push(this);
        } else {
            ActionMenu.openMenus = ActionMenu.openMenus.filter(menu => menu !== this);
        }

        this.toggleScrollHandler(ActionMenu.openMenus.filter(menu => menu.position).length > 0);
    }

    reposition() {
        if (!this.position) {
            return;
        }

        const offset = this.element.offset();
        requestAnimationFrame(() => this.menu.offset(offset));
    }

    /**
     * Update the menu element's attributes.
     */
    update() {
        this.element.toggleClass('is-open', this.is_open);
        this.menu.toggleClass('is-open', this.is_open);
        this.menu.toggleClass('is-reversed', this.is_reversed);
        this.menu.find('.action-menu-icon')
            .attr('aria-expanded', this.is_open ? 'true' : 'false')
    }

    /**
     * Confirms an action in the action menu that calls a JavaScript function
     * instead of linking to another URL.
     */
    static confirmJSAction(element = null) {
        //Show visual hint using a deferred. This way we don't need to
        //duplicate the functionality in the done() handler.
        //(code copied from copyable_link.js and modified)
        (new Promise((resolve, reject) => {
            let confirmation = $('<div class="js-action-confirmation">');
            confirmation.text = jQuery(element).data('confirmation_text');
            confirmation.insertBefore(element);
            jQuery(element).parent().addClass('js-action-confirm-animation');
            let timeout = setTimeout(() => {
                jQuery(element).parent().off('animationend');
                resolve(confirmation);
            }, 1500);
            jQuery(element).parent().one('animationend', () => {
                clearTimeout(timeout);
                resolve(confirmation);
            });
        })).then((confirmation, parent) => {
            confirmation.remove();
            jQuery(element).parent().removeClass('js-action-confirm-animation');
        });
    }

    /**
     * Handles the rotation through the action menu items when the first
     * or last element of the menu has been reached.
     *
     * @param menu The menu whose items shall be rotated through.
     *
     * @param reverse Whether to rotate in reverse (true) or not (false).
     *     Defaults to false.
     */
    static tabThroughItems(menu, reverse = false) {
        if (reverse) {
            //Put the focus on the last link in the menu, if the first link has the focus:
            if (jQuery(menu).find('a:first:focus').length > 0) {
                //Put the focus on the action menu button:
                jQuery(menu).find('button.action-menu-icon').focus();
                return true;
            } else if (jQuery(menu).find('button.action-menu-icon:focus').length > 0) {
                //Put the focus on the last action menu item:
                jQuery(menu).find('a:last').focus();
                return true;
            }
        } else {
            //Put the focus on the first link in the menu, if the last link has the focus:
            if (jQuery(menu).find('a:last:focus').length > 0) {
                //Put the focus on the action menu button:
                jQuery(menu).find('button.action-menu-icon').focus();
                return true;
            }  else if (jQuery(menu).find('button.action-menu-icon:focus').length > 0) {
                //Put the focus on the first action menu item:
                jQuery(menu).find('a:first').focus();
                return true;
            }
        }
        return false;
    }
}

export default ActionMenu;
