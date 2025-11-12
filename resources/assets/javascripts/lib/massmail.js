import axios from 'axios';
import {$gettext} from './gettext';

const MassMail = {
    exportRecipients: (evt) => {
        evt.preventDefault();

        const button = evt.target;
        button.disabled = true;
        const text = button.innerText;
        button.innerText = $gettext('Exportiere...');

        const values = window.STUDIP.FormAPI[0].getValues();

        const formData = new FormData();
        Object.keys(values).forEach(i => {
            if (!i.startsWith('STUDIPFORM_')) {
                formData.set(i, values[i]);
            }
        });

        axios.post(
            STUDIP.URLHelper.getURL('dispatch.php/massmail/message/export'),
            formData
        ).then(response => {
            button.disabled = false;
            button.innerText = text;
            window.location.href = response.data;
        }).catch(error => {
            button.disabled = false;
            button.innerText = text;
            STUDIP.Report.error(error.message);
        });
    }
}

export default MassMail;
