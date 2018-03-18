export default {
    methods: {
        getCurrentPageNumber() {
            let query = location.search.match(/page=(\d+)/);

            let page =  query ? query[1] : 1;

            return parseInt(page);
        },
        convertNullToFalse(value) {
            if (value === null) {
                return false;
            }

            return value;
        }
    }
}
