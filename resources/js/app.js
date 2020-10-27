
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
Vue.component('template-task', require('./components/TemplateTask.vue'));
Vue.component('versatile-form', require('./components/VersatileForm.vue'));
Vue.component('test', require('./components/Test.vue'));
Vue.component('modal', require('./components/Modal.vue'));
Vue.component('star-range', require('./components/StarRange.vue'));
Vue.component('check-box', require('./components/CheckBox.vue'));
Vue.component('progress-bar', require('./components/ProgressBar.vue'));
Vue.component('list-box', require('./components/ListBox.vue'));
Vue.component('setting', require('./components/Setting.vue'));
Vue.component('content-wrapper', require('./components/ContentWrapper.vue'));
Vue.component('dead-line', require('./components/DeadLine.vue'));
Vue.component('tag-cloud', require('./components/TagCloud.vue'));
Vue.component('array-filter', require('./components/ArrayFilter.vue'));
Vue.component('tag', require('./components/Tag.vue'));
Vue.component('tag-list', require('./components/TagList.vue'));
Vue.component('date-input', require('./components/DateInput.vue'));
Vue.component('notice', require('./components/Notice.vue'));
Vue.component('toggle-switch', require('./components/ToggleSwitch.vue'));
Vue.component('date-picker', require('./components/DatePicker.vue'));
Vue.component('date-slider', require('./components/DateSlider.vue'));
Vue.component('slider', require('./components/Slider.vue'));
Vue.component('content-frame', require('./components/ContentFrame.vue'));
Vue.component('progress-circle', require('./components/ProgressCircle.vue'));
Vue.component('custom-button', require('./components/CustomButton.vue'));
Vue.component('navi-bar', require('./components/NaviBar.vue'));
Vue.component('event-bus-test', require('./components/EventBusTest.vue'));
Vue.component('home', require('./components/Home.vue'));
Vue.component('text-spliter', require('./components/TextSpliter.vue'));
Vue.component('filter-array', require('./components/FilterArray.vue'));
Vue.component('filter-tag', require('./components/FilterTag.vue'));
Vue.component('filter-status', require('./components/FilterStatus.vue'));
Vue.component('filter-stared', require('./components/FilterStared.vue'));
Vue.component('sort-array', require('./components/SortArray.vue'));
Vue.component('sort-box', require('./components/SortBox.vue'));
Vue.component('tool-tip', require('./components/ToolTip.vue'));
Vue.component('template-task-management', require('./components/TemplateTaskManagement.vue'));
Vue.component('tag-management', require('./components/TagManagement.vue'));
Vue.component('color-picker', require('./components/ColorPicker.vue'));
Vue.component('task-log', require('./components/TaskLog.vue'));
Vue.component('waiting', require('./components/Waiting.vue'));
Vue.component('headline-2', require('./components/Headline2.vue'));
Vue.component('congratulation', require('./components/Congratulation.vue'));
Vue.component('circle-meter', require('./components/CircleMeter.vue'));
Vue.component('main-title', require('./components/MainTitle.vue'));
Vue.component('reminders', require('./components/Reminders.vue'));
Vue.component('my-list', require('./components/MyList.vue'));



const app = new Vue({
    el: '#app'
});