// src/mixins/utilMixin.js

export default {
    methods: {
        deleteProperties(obj, properties) {
            // If properties is not an array, convert it to one (assuming it's a single string)
            if (!Array.isArray(properties)) {
                if (typeof properties === 'string') {
                properties = [properties]; // If it's a string, convert it to an array with a single item
                } else {
                throw new Error('Second argument must be an array or a string of property names');
                }
            }

            // Loop through the properties and delete them if they exist in the object
            properties.forEach(prop => {
                if (obj.hasOwnProperty(prop)) {
                delete obj[prop];
                }
            });

            return obj;
            }

    },

};
