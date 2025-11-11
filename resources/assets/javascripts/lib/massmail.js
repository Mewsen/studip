import axios from 'axios';

const MassMail = {
    exportRecipients: (evt) => {
        evt.preventDefault();

        const values = evt.target.closest('div.studipform.vueified').__vue_app__._instance.proxy.getFormValues();

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
            window.location.href = response.data;
        }).catch(error => {
            STUDIP.Report.error(error.message);
        });
    }
}

export default MassMail;
