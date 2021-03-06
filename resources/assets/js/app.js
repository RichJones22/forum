/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */
// import commonCode from "./mixins/commonCode";


require('./bootstrap');

// global components object
window.Vue = require('vue');

Vue.prototype.authorize = function(handler) {
    // additional admin privileges here...

    let user = window.App.user;

    return user ? handler(user) : false;
};

// events object
window.events = new Vue();

// global helpers
window.flash = function (message) {
    window.events.$emit('flash', message);
};

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

Vue.component('flash', require('./components/flash.vue'));
Vue.component('favorite', require('./components/favorite.vue'));
Vue.component('paginator', require('./components/paginator'));
Vue.component('user-notifications', require('./components/UserNotifications'));

Vue.component('thread-view', require('./pages/Thread.vue'));


const app = new Vue({
    el: '#app',
    // mixins: [commonCode]
});
