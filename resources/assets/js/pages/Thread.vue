<script>
    import Replies from '../components/replies.vue';

    export default {
        props: ['initRepliesCount'],
        components: {Replies},

        data() {
            return {
                repliesCount: this.initRepliesCount
            }
        },
        mounted() {
            console.log('-- Thread.vue --');

            this.$emit('repliesCountIs', this.repliesCount);
        },
    }
</script>