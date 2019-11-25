
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

Vue.component('example-component', require('./components/ExampleComponent.vue'));
Vue.component('project-list', require('./components/ProjectList.vue'));
Vue.component('project', require('./components/Project.vue'));
Vue.component('task-list', require('./components/TaskList.vue'));
Vue.component('task', require('./components/Task.vue'));
Vue.component('versatile-form', require('./components/VersatileForm.vue'));
Vue.component('test', require('./components/Test.vue'));
Vue.component('modal', require('./components/Modal.vue'));
Vue.component('star-range', require('./components/StarRange.vue'));
Vue.component('check-box', require('./components/CheckBox.vue')); //未実装
Vue.component('progress-bar', require('./components/ProgressBar.vue'));
Vue.component('list-box', require('./components/ListBox.vue'));
Vue.component('lists', require('./components/Lists.vue'));
Vue.component('dead-line', require('./components/DeadLine.vue'));
Vue.component('tag-cloud', require('./components/TagCloud.vue'));
Vue.component('array-filter', require('./components/ArrayFilter.vue'));
Vue.component('tag', require('./components/Tag.vue'));
Vue.component('tag-list', require('./components/TagList.vue'));
Vue.component('date-selecter', require('./components/DateSelecter.vue'));

const app = new Vue({
    el: '#app'
});
