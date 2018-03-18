<template>
    <ul class="pagination" v-if="shouldPaginate">
        <li class="page-item" v-show="prevUrl">
            <a class="page-link" href="#" aria-label="Previous" rel="prev" @click.prevent="page--">
                <span aria-hidden="true">&laquo; Previous</span>
            </a>
        </li>
        <li class="page-item" v-show="nextUrl">
            <a class="page-link" href="#" aria-label="Next" rel="next" @click.prevent="page++">
                <span aria-hidden="true">Next &raquo;</span>
            </a>
        </li>
    </ul>
</template>

<script>
    import CommonCode from '../mixins/commonCode';

    export default {
        props: ['dataSet'],
        mixins: [CommonCode],
        data() {
            return {
                page: 1,
                prevUrl: false,
                nextUrl: false
            }
        },
        watch: {
            dataSet() {
                console.log('-- in paginator.vue');
                this.page = this.currPage();
                this.prevUrl = this.prevPageUrl();
                this.nextUrl = this.nextPageUrl();
            },
            page() {
                this.broadcast().updateUrl();
            }
        },
        computed: {
            shouldPaginate() {
                return !! this.prevUrl || !! this.nextUrl;
            }
        },
        methods: {
            broadcast() {
                this.$emit('updated', this.page);
                this.$emit('currentPageIs', this.page);

                return this;
            },
            updateUrl() {
                history.pushState(null, null, `?page=${this.page}`);
            },
            currPage() {
                return this.dataSet.current_page;
            },
            prevPageUrl() {
                return this.convertNullToFalse(this.dataSet.prev_page_url);
            },
            nextPageUrl() {
                return this.convertNullToFalse(this.dataSet.next_page_url);
            }
        }
    }
</script>
