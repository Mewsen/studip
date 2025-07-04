export const subscriptionTransformer = subscription => {
    // rename object key
    if (subscription.subject.title) {
        subscription.subject.name = subscription.subject.title;
    }

    return {
        ...subscription,
        subject: subscription.subject
    };
};

export const topicTransformer = topic => {
    return {
        ...topic,
        ...topic.category
    }
};
