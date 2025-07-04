import { Deserializer } from 'jsonapi-serializer';

const deserializer = new Deserializer({
    keyForAttribute: 'snake_case',
    typeAsAttribute: true
});

export const deserializeJSONAPIResponse = async response => {
    try {
        return await deserializer.deserialize(response);
    } catch (error) {
        console.error('Failed to deserialize JSON:API response', error);
        throw error;
    }
};
