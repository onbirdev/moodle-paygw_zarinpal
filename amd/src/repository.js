// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

import Ajax from 'core/ajax';

/**
 * Return the ZarinPal payment URL.
 *
 * @param {string} component Name of the component that the itemId belongs to
 * @param {string} paymentArea The area of the component that the itemId belongs to
 * @param {number} itemId An internal identifier that is used by the component
 * @param {string} description Payment description
 * @returns {Promise<{clientid: string, brandname: string, cost: number, currency: string}>}
 */
export const payment = (component, paymentArea, itemId, description) => {
    const request = {
        methodname: 'paygw_zarinpal_payment',
        args: {
            component,
            paymentarea: paymentArea,
            itemid: itemId,
            description,
        },
    };

    return Ajax.call([request])[0];
};
