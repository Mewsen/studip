// Registrations URLS:
export const showRegistrationURL = (id, role = 'tool') => STUDIP.URLHelper.getURL(`dispatch.php/admin/lti/registrations/show/${id}?role=${role}`);
export const createRegistrationURL = (role = 'tool') => STUDIP.URLHelper.getURL(`dispatch.php/admin/lti/registrations/create?role=${role}`);
export const editRegistrationURL = (id, role = 'tool') => STUDIP.URLHelper.getURL(`dispatch.php/admin/lti/registrations/edit/${id}?role=${role}`);
export const storeRegistrationURL = () => STUDIP.URLHelper.getURL(`dispatch.php/admin/lti/registrations/store`);
export const updateRegistrationURL = id => STUDIP.URLHelper.getURL(`dispatch.php/admin/lti/registrations/update/${id}`);
export const deleteRegistrationURL = id => STUDIP.URLHelper.getURL(`dispatch.php/admin/lti/registrations/delete/${id}`);


// Deployments URLS:
export const deploymentsIndexURL = (registrationId, role = 'tool') => STUDIP.URLHelper.getURL(`dispatch.php/admin/lti/deployments?registration_id=${registrationId}&role=${role}`);
export const addDeploymentURL = registrationId => STUDIP.URLHelper.getURL(`dispatch.php/admin/lti/deployments/create?registration_id=${registrationId}`);
export const storeDeploymentURL = () => STUDIP.URLHelper.getURL(`dispatch.php/admin/lti/deployments/store`);
export const editDeploymentURL = id => STUDIP.URLHelper.getURL(`dispatch.php/admin/lti/deployments/edit/${id}`);
export const updateDeploymentURL = id => STUDIP.URLHelper.getURL(`dispatch.php/admin/lti/deployments/update/${id}`);
export const deleteDeploymentURL = id => STUDIP.URLHelper.getURL(`dispatch.php/admin/lti/deployments/delete/${id}`);


// Resource Links URLS:
export const createResourceURL = () => STUDIP.URLHelper.getURL(`dispatch.php/admin/lti/resources/create`);

export const editResourceURL = id => STUDIP.URLHelper.getURL(`dispatch.php/admin/lti/resources/edit/${id}`);

export const editResourceConsentURL = registrationId => STUDIP.URLHelper.getURL(`dispatch.php/lti/consent/edit/${registrationId}`);

export const storeResourceURL = () => STUDIP.URLHelper.getURL(`dispatch.php/admin/lti/resources/store`);
export const updateResourceURL = id => STUDIP.URLHelper.getURL(`dispatch.php/admin/lti/resources/update/${id}`);
export const deleteResourceURL = id => STUDIP.URLHelper.getURL(`dispatch.php/admin/lti/resources/delete/${id}`);


// Publication URLS:
export const showPublicationURL = id => STUDIP.URLHelper.getURL(`dispatch.php/admin/lti/publications/show/${id}`);
export const createPublicationURL = () => STUDIP.URLHelper.getURL(`dispatch.php/admin/lti/publications/create`);
export const editPublicationURL = (id) => STUDIP.URLHelper.getURL(`dispatch.php/admin/lti/publications/edit/${id}`);
export const storePublicationURL = () => STUDIP.URLHelper.getURL(`dispatch.php/admin/lti/publications/store`);
export const updatePublicationURL = id => STUDIP.URLHelper.getURL(`dispatch.php/admin/lti/publications/update/${id}`);
export const deletePublicationURL = id => STUDIP.URLHelper.getURL(`dispatch.php/admin/lti/publications/delete/${id}`);


// Others:
export const launchResourceURL = (id, version = '1.1') => {
    switch (version) {
        case '1.1':
            return STUDIP.URLHelper.getURL(`dispatch.php/lti/1p1/index/launch/${id}`);
        case '1.3a':
            return STUDIP.URLHelper.getURL(`dispatch.php/lti/1p3/index/launch/${id}`);
    }

    console.error(`Unknown LTI version ${version}`);
};
export const selectContentURL = id => STUDIP.URLHelper.getURL(`dispatch.php/lti/1p3/index/select_contents/${id}`);
export const showRangeURL = rangeId => STUDIP.URLHelper.getURL(`dispatch.php/course/details/index/${rangeId}`);

export const userProfileURL = username => STUDIP.URLHelper.getURL('dispatch.php/profile', {username});
