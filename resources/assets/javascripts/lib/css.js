/**
 * Add methods to dynamically insert and remove css styles.
 *
 * @author Jan-Hendrik Willms <tleilax+studip@gmail.com>
 * @copyright Stud.IP Core Group 2014
 * @license GPL2 or any later version
 * @since Stud.IP 3.1
 */

// "Private" stylesheet rules are applied to, generated from a dynamically
// inserted style tag in the document's header
let sheet = null;


// Expose functions to global STUDIP object, namespaced under CSS
class CSS {
    /**
     * Dynamically add a ruleset for a given selector to the current site
     *
     * @param {string} selector - CSS selector to add rules for
     * @param {object} css - Actual css rules as hash object
     * @param {array} vendors - Optional array of vendor prefixes to apply
     */
    static addRule(selector, css, vendors) {
        vendors = vendors ?? [];
        vendors.push('');

        if (sheet === null) {
            let style = document.createElement('style');
            sheet = document.head.appendChild(style).sheet;
        }

        let propText = Object.keys(css)
            .map(p => {
                let result = [];
                for (let i = 0; i < vendors.length; i += 1) {
                    result.push(vendors[i] + p + ':' + css[p]);
                }
                return result.join(';');
            })
            .join(';');

        sheet.insertRule(selector + '{' + propText + '}', sheet.cssRules.length);
    }

    /**
     * Removes a currently added, dynamic ruleset.
     *
     * @param {string} selector - CSS selector to remove rules for
     */
    static removeRule(selector) {
        if (sheet === null) {
            return;
        }

        for (let i = sheet.cssRules.length - 1; i >= 0; i -= 1) {
            if (sheet.cssRules[i].selectorText === selector) {
                sheet.deleteRule(i);
            }
        }
    }
}

export default CSS;
