export default {
    methods: {
        getCurrentPageNumber() {
            let query = location.search.match(/page=(\d+)/);

            return  query ? query[1] : 1;
        }
    }
}
