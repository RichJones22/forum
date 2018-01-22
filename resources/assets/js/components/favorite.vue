<template>
    <button type="submit" :class="classes" @click="toggle">
        <span class="glyphicon glyphicon-heart"></span>
        <span v-text="favoritesCount"></span>
    </button>
</template>

<script>
    export default {
        props: ['reply'],
        data() {
            return {
                favoritesCount: this.reply.favoritesCount,
                isFavorited: this.reply.isFavorited
            }
        },
        computed: {
            classes() {
                return ['btn', this.isFavorited ? 'btn-primary' : 'btn-default']
            },
            addRemoveFavoriteEndpoint() {
                return '/replies/' + this.reply.id + '/favorites';
            }
        },
        methods: {
            toggle() {
                console.log('inside toggle method');

                if (this.isFavorited) {
                    this.addFavorite();
                } else {
                    this.removeFavorite();
                }
            },
            addFavorite() {
                axios.delete(this.addRemoveFavoriteEndpoint);

                this.isFavorited = false;
                this.favoritesCount--;
            },
            removeFavorite() {
                axios.post(this.addRemoveFavoriteEndpoint);

                this.isFavorited = true;
                this.favoritesCount++;
            }
        }
    }
</script>