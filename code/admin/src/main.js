// The Vue build version to load with the `import` command
// (runtime-only or standalone) has been set in webpack.base.conf with an alias.
import Vue from 'vue'
import App from './App'

import router from './router'

import 'element-ui/lib/theme-chalk/index.css';
import '../static/css/global.css' /*引入公共样式*/
import '../static/css/global_all.css' /*引入公共样式*/
import 'element-ui/lib/theme-chalk/display.css';
import adminApi from './util/common.js';

import ElementUI from 'element-ui'

import '../static/ueditor/ueditor.config.js'
import '../static/ueditor/ueditor.parse.js'
import '../static/ueditor/ueditor.all.js'

// 富文本样式
import 'quill/dist/quill.core.css'
import 'quill/dist/quill.snow.css'
import 'quill/dist/quill.bubble.css'
  
import jsCookie from 'js-cookie';
import axios from 'axios';
  
// 请求基准路径
axios.defaults.baseURL = '/admin';


import CKEditor from 'ckeditor4-vue';  
Vue.use( CKEditor );

var that = this;
// 请求拦截（配置发送请求的信息）-------------------
axios.interceptors.request.use(function (config) {
    // 处理请求之前的配置
    const token = jsCookie.get('token') || '';
    if (token) {
        config.headers['token'] = token;
    }

    return config;
}, function (error) {
    // 请求失败的处理
    return Promise.reject(error);
});


// 响应拦截（配置请求回来的信息）-------------------
axios.interceptors.response.use(function (response, next) {
    if (response.data) {
        return response.data
    } else {
        return response;
    }
}, function (error, next) { 
    if (error.response) {
        if (error.response.status == 403) {
            jsCookie.remove('token');
            router.push({path:'/login'})
        }
    }
    return Promise.reject(error.response.data.msg);
});
Vue.prototype.axios = axios;
Vue.prototype.cookie = jsCookie;

Vue.use(ElementUI);


// 路由守卫
var token;
router.beforeEach((to, from, next) => {
    if (to.path === '/Login') return next()
    // 获取token
    token = jsCookie.get('token');
    // console.log('路由', token);
    // 判断token的状态,取反则没有token，让用户去登录
    if (!token) return next('/Login')
    next()
})

// Vue.http.interceptors.push((request, next) => {
//   // 请求发送前的处理逻辑
//   request.headers.set('token', token)
//   next((response) => {
//         // 请求发送后的处理逻辑
//         // 根据请求的状态，response参数会返回给successCallback或errorCallback
//         return response
//   })
// })

//定义全局变量
Vue.prototype.adminApi = adminApi;




Vue.config.productionTip = false

/* eslint-disable no-new */
new Vue({
    el: '#app',
    router,
    components: {
        App
    },
    template: '<App/>'
})
