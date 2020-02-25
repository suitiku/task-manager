
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
Vue.component('content-wrapper', require('./components/ContentWrapper.vue'));
Vue.component('dead-line', require('./components/DeadLine.vue'));
Vue.component('tag-cloud', require('./components/TagCloud.vue'));
Vue.component('array-filter', require('./components/ArrayFilter.vue'));
Vue.component('tag', require('./components/Tag.vue'));
Vue.component('tag-list', require('./components/TagList.vue'));
Vue.component('date-selecter', require('./components/DateSelecter.vue'));
Vue.component('notice', require('./components/Notice.vue'));
Vue.component('toggle-switch', require('./components/ToggleSwitch.vue'));
Vue.component('date-picker', require('./components/DatePicker.vue'));
Vue.component('content-frame', require('./components/ContentFrame.vue'));
Vue.component('progress-circle', require('./components/ProgressCircle.vue'));
Vue.component('custom-button', require('./components/CustomButton.vue'));
Vue.component('navi-bar', require('./components/NaviBar.vue'));
Vue.component('event-bus-test', require('./components/EventBusTest.vue'));
Vue.component('home', require('./components/Home.vue'));
Vue.component('text-spliter', require('./components/TextSpliter.vue'));
Vue.component('filter-array', require('./components/FilterArray.vue'));
Vue.component('filter-tag-box', require('./components/FilterTagBox.vue'));
Vue.component('filter-box', require('./components/FilterBox.vue'));
Vue.component('tool-tip', require('./components/ToolTip.vue'));


const app = new Vue({
    el: '#app'
});