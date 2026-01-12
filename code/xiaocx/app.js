//app.js
const qiniuUploader = require("/utils/qiniuUploader");
const QQMapWX = require('/utils/qqmap-wx-jssdk.min.js'); 
App({
    // parse: WxParse 
    api_url: 'https://jujiashizhi-api.syncsmart.cn/api',                      // 正式地址 
    upload_percent: 0,                                          // 七牛云上传进度
    is_upload: false,                                           // 上传状态
    call_back_fun: '', // 回调方法
    upload_url: '',
    user_id: '',            // 微信用户ID    MDAwMDAwMDAwMIGEbXY        MDAwMDAwMDAwMIGHgXY
    plan_user_id: '',       // 平台用户id
    user: '',               // 用户信息
    scene: 0,               // 进入小程序的方式
    url: '',                // 进入的页面
    is_ios: false,
    phone_model: '',        // 手机型号
    is_share: false,        // 是否分享
    images: '',
    to_loading: false,

    boundTop: 0,
    navBarHeight: 0, // 导航栏高度
    // menuBottom: 0, // 胶囊距底部间距（顶部间距也是这个）
    // menuHeight: 0, // 胶囊高度

    register_url: '',           // 注册完成后跳转的地址

    cropper_url: '',            // 被裁切后的图片

    qqmapsdk: '',
    qq_map_key: 'VVWBZ-53BKP-X5NDL-VDUCG-ULSEQ-MPBY5',

    systemInfo: {},             // 系统信息
    temp_upload: [''],            // 上传临时的图片

    // 初始化录音管理器
    recorderManager: wx.getRecorderManager(),

    // 背景音频播放器
    backgroundAudioManager: wx.createInnerAudioContext(),

    count_unread_timer: null,
    is_show_tip: false,

    // 获取自定义对象
    getMyItem(e, item) {
        return e.currentTarget.dataset[item]
    },

    // 获取 value
    getMyValue(e) {
        return e.detail.value
    },

    // 初始化
    async onLaunch(options) {

        // 专家互动未读数量
        this.get_user_count_unread()

        // 获取设备信息
        this.getSystemInfo();
        // 设置开屏广告缓存 
        // this.calcNavBarInfo(); 
    },

    // 判断用户是否有 未读的数量
    async get_user_count_unread() {
        let that = this;
        that.count_unread_timer = setInterval(function () {
            that.post_ajax('/user_chat/count_unread', '', function (res, code) {
                that.is_show_tip = res > 0 ? true : false
            })
        }, 5000)
    },

    calcNavBarInfo() {
        // 获取系统信息
        const systemInfo = wx.getSystemInfoSync();
        // 胶囊按钮位置信息
        const menuButtonInfo = wx.getMenuButtonBoundingClientRect();
        // 导航栏高度 = 状态栏到胶囊的间距（胶囊上坐标位置-状态栏高度） * 2 + 胶囊高度 + 状态栏高度
        this.navBarHeight = (menuButtonInfo.top - systemInfo.statusBarHeight) * 2 + menuButtonInfo.height + systemInfo.statusBarHeight;
        // 状态栏和菜单按钮(标题栏)之间的间距
        // 等同于菜单按钮(标题栏)到正文之间的间距（胶囊上坐标位置-状态栏高度）
        this.menuBottom = menuButtonInfo.top - systemInfo.statusBarHeight;
        // 菜单按钮栏(标题栏)的高度
        this.boundTop = systemInfo.statusBarHeight;
        this.menuHeight = menuButtonInfo.height;
    },

    // 设置分享用户ID
    set_share_user_id(options) {
        let share_user_id = 0;
        if (options.share_user_id != undefined) {
            share_user_id = parseInt(options.share_user_id);
            wx.setStorageSync('share_user_id', share_user_id);
        } else {
            share_user_id = wx.getStorageSync('share_user_id') || 0;
        }
        return share_user_id;
    },

    async onShow(options) {
        let that = this;
        // 获取场景值
        that.scene = options.scene;
        let share_user_id = decodeURIComponent(options.query.scene).split('=')[1]
        if (share_user_id) {
            wx.setStorageSync('share_user_id', share_user_id);
        }
        // 初始化 - 腾讯地图
        that.qqmapsdk = new QQMapWX({
            key: that.qq_map_key // 必填
        });
    },

    // 初始化七牛云
    async initQiniu() {
        let that = this
        var options = {
            region: 'NCN', // 【华南区=SCN】【NCN=华北】【ECN=华东】【NA=北美】
            uptokenURL: that.api_url + '/Qiniu/getToken', //七牛云token
            domain: that.cdn_url, //图片前缀地址
            shouldUseQiniuFileName: true
        };
        await qiniuUploader.init(options);
    },


    /**
     * 离开小程序清除
     */
    onHide: function () {
        var that = this;
    },

    // 获取分享用户ID
    get_share_user_id() {
        return wx.getStorageSync('share_user_id') || 0;
    },

    // 获取系统信息
    getSystemInfo: function () {
        let t = this;
        wx.getSystemInfo({
            success: function (res) {
                t.systemInfo = res;
            }
        });
    },


    /**
     * 打开地理位置
     */
    open_location: function (address, name, lat, lng) {
        // 通过地址获取经纬度
        wx.openLocation({
            latitude: lat,
            longitude: lng,
            name: name,
            address: address
        });
    },

    // 通过 - 地址解析 经纬度
    async get_geocoder(address) {
        let that = this;
        return new Promise((resolve, reject) => {
            that.qqmapsdk.geocoder({
                //获取表单传入地址
                address: address,
                success: function (res) {
                    resolve(res.result)
                },
                fail: function (err) {
                    reject(err)
                }
            })
        })
    },

    // 处理 - 活动列表的状态数据
    handler_activity_data(res, activity_list = []) {
        res.map((activity) => {
            activity_list.push(activity)
        })
        return activity_list;
    },

    // 预览资源
    async preview_media(sources, current = 0) {
        wx.previewMedia({
            sources: sources,
            current: current,
            showmenu: true
        })
    },

    // 上传视频
    async choose_video(count = 1, sourceType) {
        let that = this;
        if (that.is_upload == true) {
            return false;
        }
        that.showLoading()
        that.is_upload = true;

        // 初始化七牛云
        await that.initQiniu();

        if (sourceType) {
            sourceType = [sourceType];
        } else {
            sourceType = ['album', 'camera'];
        }

        return new Promise((resolve, reject) => {
            wx.chooseVideo({
                sourceType: sourceType,     // 可以指定来源是相册还是相机，默认二者都有
                compressed: true,          // 不需要压缩
                maxDuration: 60,            // 判断时长
                async success(res) {
                    if (res.errMsg == "chooseVideo:ok") {
                        that.is_upload = false;
                        that.hideLoading();
                        if (res.duration > 30) {
                            that.toast_none('超出最大视频上传时长')
                        } else {
                            let video = await that.upload_picture(res.tempFilePath);
                            resolve(video);
                        }

                        // 压缩视频
                        // wx.compressVideo({
                        //     src: res.tempFilePath,
                        //     quality: 'low',
                        //     bitrate: 720,
                        //     fps: 60,
                        //     resolution: 1,
                        //     async success(res2){
                        //         let video = await that.upload_picture(res2.tempFilePath);
                        //         resolve(video);
                        //     },
                        //     fail(err){
                        //         reject(err)
                        //     }
                        // })
                    } else {
                        that.is_upload = false;
                        that.hideLoading()
                    }
                },
                fail() {
                    that.is_upload = false;
                    that.hideLoading()
                }
            })
        })
    },

    // 选择多张或单张照片
    async choose_image(count = 1, sourceType) {
        let that = this;
        if (that.is_upload == true) {
            return false;
        }
        that.showLoading()
        that.is_upload = true;

        // 初始化七牛云
        await that.initQiniu();

        if (sourceType) {
            sourceType = [sourceType];
        } else {
            sourceType = ['album', 'camera'];
        }

        return new Promise((resolve, reject) => {
            wx.chooseImage({
                count: count,
                sizeType: ['compressed'],           // 可以指定是原图还是压缩图，默认二者都有
                sourceType: sourceType,             // 可以指定来源是相册还是相机，默认二者都有
                async success(res) {
                    if (res.errMsg == "chooseImage:ok") {
                        var tempFilePaths = res.tempFilePaths;
                        let img = [];
                        for (var i in tempFilePaths) {
                            let pic = await that.upload_picture(tempFilePaths[i]);
                            img.push(pic);
                        }
                        that.is_upload = false;
                        that.hideLoading()
                        resolve(img);
                    } else {
                        that.is_upload = false;
                        that.hideLoading()
                    }
                },
                fail() {
                    that.is_upload = false;
                    that.hideLoading()
                }
            })
        })
    },

    // 删除文件
    async chooseMessageFile(count = 1, type = 'all', extension = []) {
        let that = this;
        if (that.is_upload == true) {
            return false;
        }
        that.showLoading()
        that.is_upload = true;

        // 初始化七牛云
        await that.initQiniu();

        return new Promise((resolve, reject) => {
            wx.chooseMessageFile({
                count: count,
                type: type,
                async success(res) {
                    // tempFilePath可以作为img标签的src属性显示图片
                    if (res.errMsg == "chooseMessageFile:ok") {
                        var tempFilePaths = res.tempFiles;
                        for (var i in tempFilePaths) {
                            if (tempFilePaths[i].size / 1024 / 1024 > 30) {
                                that.is_upload = false;
                                that.hideLoading()
                                reject('上传文件不能超过30M')
                                return false;
                            }
                            tempFilePaths[i].url = await that.upload_picture(tempFilePaths[i].path);
                        }
                        that.is_upload = false;
                        that.hideLoading()
                        resolve(tempFilePaths);
                    } else {
                        that.is_upload = false;
                        that.hideLoading()
                        reject('未选择文件')
                    }
                },
                async fail(err) {
                    that.is_upload = false;
                    that.hideLoading()
                    reject(err)
                }
            })
        })
    },

    // 上传到七牛云图片
    async upload_picture(files) {
        let that = this;

        // 交给七牛上传
        return new Promise((resolve, reject) => {
            qiniuUploader.upload(files, (res) => {
                resolve(res.imageURL);
            }, (error) => {
                console.error('error: ' + JSON.stringify(error));
                reject(error)
            }, null);
        })
    },

    /***
     * 检查用户是否登陆
     * jihaichuan
     */
    check_user_login(fn) {
        var that = this;
        if (that.user.authorization == 2) {
            that.toast_none('请先授权个人信息', () => {
                // 获取当前页面路径 
                wx.navigateTo({
                    url: '/pages/auth/auth',
                });
            });
            return false;
        }
        fn();
    },


    /**
     * 用户登录
     */
    async user_login() {
        var that = this;
        let share_user_id = this.get_share_user_id();
        return new Promise(function (resolve, reject) {
            wx.login({
                async success(res) {
                    // console.log(res)
                    await that.ajax('/Login/login', {
                        code: res.code,
                        scene: that.scene,
                        share_user_id
                    }, 'GET').then((res, code) => {
                        //   console.log(res)
                        that.user = res
                        // that.user_id = res.wx_user_id; 
                        that.user_id = res.data.codeid;
                        wx.setStorageSync('codeid', that.user_id)
                        resolve(res);
                    });
                }
            });
        })
    },


    /**
     * 弹窗授权
     */
    dialog_authorize: function () {
        var that = this;
        // 判断是否授权
        wx.getUserInfo({
            withCredentials: true,
            lang: 'zh_CN',
            success: function (res) {
                that.set_user_info(res.userInfo);
            },
            fail: function () {
                wx.getSetting({
                    success: (res) => {
                        if (res.authSetting["scope.userInfo"] == false) {
                            // wx.navigateTo({ url: 'authorize' });
                        }
                    }
                });
            }
        });
    },

    // 获取用户 照护记录
    async get_user_history() {
        let that = this;
        let history_list = []
        // 获取用户数据
        await that.post_ajax('/user/my_history', '', function (res, status) {
            if (status == 200) {
                history_list = res.data;
            }
        });
        return history_list;
    },

    /**
     * 获取用户信息
     * jihaichuan
     */
    async get_user_info() {
        let that = this;
        // 获取用户数据
        await that.post_ajax('/user/index', '', function (res, status) {
            if (status == 200) {
                // 存储本地信息
                that.user = res.data;
                that.set_user_data(res.data);
            }
        });
        return that.user;
    },

    /**
     * 设置当前用户数据
     * jihaichuan
     */
    set_user_data: function (res) {
        let that = this;
        // 存储全部数据
        that.user = res;
        wx.setStorageSync('user', res);
    },


    /**
     * 微信用户授权
     */
    set_user_info: function (user_info) {
        var that = this;
        user_info['wx_user_id'] = that.user_id;
        that.post_ajax('/Login/getwechatuserdetail', user_info, function (data, status) {
            if (status == 200) {
                // 设置app.js的数据值
                that.set_user_data(data.data);
            }
        });
    },

    /**
     * 设置强制授权
     */
    set_wechat_setting: function () {
        var that = this;
        wx.openSetting({
            success: (res) => {
                if (res.authSetting["scope.userInfo"] == true) {
                    that.user_login();
                } else {
                    wx.navigateBack({
                        delta: 1
                    });
                }
            }
        });
    },

    /**
     * get请求
     */
    async get_ajax(url, data, fn) {
        let that = this;
        data = data || {}

        // 获取用户ID
        await that.get_user_id();
        // data.user_id = that.user_id
        // data.codeid = that.user_id

        // data.user_id = 'MDAwMDAwMDAwMIGEbXY'  //  1

        return await this.ajax(url, data, 'GET').then((res) => {
            fn(res, 200);
        }).catch((err) => {
            fn(err, 500);
        });
    },

    /**
     * post请求
     */
    async post_ajax(url, data, fn) {
        let that = this;
        data = data || {}

        // 获取用户ID
        await that.get_user_id();
        data.user_id = that.user_id
        // data.codeid = that.user_id
        // 1) ---MDAwMDAwMDAwMIGEbXY
        // (2) ---MDAwMDAwMDAwMIGabXY
        // (3) ---MDAwMDAwMDAwMIGqbXY
        // (4) ---MDAwMDAwMDAwMIJ0bXY
        // (5) ---MDAwMDAwMDAwMIKEbXY



        //  data.user_id = 'MDAwMDAwMDAwMIGEbXY'  //  1

        return await this.ajax(url, data, 'POST').then((res) => {
            fn(res, 200);
        }).catch((err) => {
            console.log(err)
            fn(err, 500);
        });
    },

    // 请求数据
    async ajax(url, data, type) {
        let that = this;
        return new Promise((resolve, reject) => {
            wx.request({
                url: that.api_url + url,
                method: type,
                data: data,
                header: {
                    // 'content-type': 'application/x-www-form-urlencoded',
                    'content-type': 'application/json',
                    // 'codeid' :   'MDAwMDAwMDAwMIGabXY' || wx.getStorageSync('codeid') || '' // 
                    // 'codeid' :   'MDAwMDAwMDAwMIJ0bXY' || wx.getStorageSync('codeid') || '' // 春
                    'codeid': wx.getStorageSync('codeid') || ''

                },
                success: (res) => {
                    if (res.statusCode == 200) {
                        resolve(res.data, res.statusCode)
                    } else {
                        reject(res.data, res.statusCode)
                    }
                },
                fail: (err) => {
                    reject(err)
                }
            });
        })
    },


    /**
     * 简单版-弹出层
     */
    basic_dialog: function (content, title) {
        title = title ? title : '提示';
        wx.showModal({
            title: title,
            content: content,
            showCancel: false
        });
        return false;
    },

    /**
     * 弹窗提醒定时做什么操作
     */
    set_time_out: function (title, fn, timer) {
        timer = timer ? timer : 1000;
        wx.showToast({
            title: title,
            success: function () {
                setTimeout(function () {
                    if (fn) {
                        (fn)();
                    }
                    // 隐藏toast
                    wx.hideToast();
                }, timer);
            }
        })
    },


    /**
     * 吐司弹窗
     * @param string  title
     * @param function fn (可写)
     * @timer integral timer  毫秒（ps:多少秒执行fn方法）
     * jihaichaun
     */
    toast_none: function (title, fn, timer, image) {
        title = title ? title : '操作提示';
        timer = timer ? timer : 1000;
        wx.showToast({
            title: title,
            icon: 'none',
            image: image ? image : '',
            success: function () {
                setTimeout(function () {
                    if (fn) {
                        (fn)();
                    }

                    // 隐藏toast
                    wx.hideToast();
                }, timer);
            }
        })
    },


    /**
     * 把时间戳转换成 分|秒
     */
    get_minte_scond: function (time) {
        time = parseInt(time);
        var minte = '00';
        var scond = '00';
        if (time >= 60) {
            minte = parseInt(time / 60);
            time = time - minte * 60;

            minte = minte > 9 ? minte : '0' + minte;
        }
        scond = time > 9 ? time : '0' + time;
        return minte + ':' + scond;
    },


    /**
     * 截取字符串
     */
    substring: function (str, start, end) {
        return (str.length > end) ? str.substring(start, end) + '...' : str;
    },
    /**
     * 截取时间
     */
    substring_time: function (str, start, end) {
        return str.substring(start, end);
    },



    /**
     * 验证数字
     */
    validate_number: function (num) {
        var reg = /^1[3|4|5|7|8|9][0-9]\d{8}$/;
        return reg.test(num);
    },

    /**
     * 确认提醒框
     */
    confirm_dialog: function (title, content, fn) {
        title = title ? title : '警告';
        wx.showModal({
            title: title,
            content: content,
            showCancel: false,
            success: fn
        });
    },

    /**
     * 查询在数组中的下标
     */
    indexOf: function (arr, value) {
        for (var i = 0, vlen = arr.length; i < vlen; i++) {
            if (arr[i] == value) {
                return i;
            }
        }
    },

    /**
     * 删除数组中的某一项
     */
    delArray: function (arr, value) {
        var index = this.indexOf(arr, value); //调用上面函数获取下标

        if (index != -1) {
            arr.splice(index, 1); //删除元素
            return arr; //已经剔除的原数组
        }
    },

    /**
     * 显示加载状态
     */
    showLoading: function (title) {
        if (wx.canIUse('showNavigationBarLoading') == true) {
            // 线上导航栏加载状态
            wx.showNavigationBarLoading();
        }
    },

    /**
     * 隐藏加载
     */
    hideLoading: function () {
        if (wx.canIUse('hideLoading') == true) {
            wx.hideNavigationBarLoading();
        }
    },

    /**
     * 弹出层,确定后做什么
     */
    dialog: function (content, fn) {
        wx.showModal({
            title: '提醒',
            content: content,
            showCancel: true,
            success: function (res) {
                if (res.confirm) {
                    fn()
                } else {

                }
            },
            fail: function () {

            }
        });
        return false;
    },


    /**
     * 去除字符串两边空格
     */
    trim: function (str) {
        return str.replace(/(^\s*)|(\s*$)/g, "");
    },

    /**
     * 获取用户ID
     * jihaichuan
     */
    async get_user_id() {
        let that = this;
        if (!that.user_id) {
            await that.user_login();
        }
        return that.user_id;
    },

    /**
     * 返回用户信息
     * jihaichuan
     */
    get_user: function () {
        let that = this;
        return that.user || wx.getStorageSync('user') || {};
    },

    /**
     * 使用循环的方式判断一个元素是否存在于一个数组中
     * @param {Object} arr 数组
     * @param {Object} value 元素值
     */
    isInArray: function (arr, value) {
        for (var i = 0; i < arr.length; i++) {
            if (value === arr[i]) {
                return true;
            }
        }
        return false;
    },

    /**
     * 数据转化  
     */
    formatNumber: function (n) {
        n = n.toString()
        return n[1] ? n : '0' + n
    },

    /** 
     * 时间戳转化为年 月 日 时 分 秒 
     * number: 传入时间戳 
     * format：返回格式，支持自定义，但参数必须与formateArr里保持一致 
     */
    formatTime: function (number, format) {
        var that = this;
        var formateArr = ['Y', 'M', 'D', 'h', 'm', 's'];
        var returnArr = [];

        var date = new Date(number * 1000);
        returnArr.push(date.getFullYear());
        returnArr.push(that.formatNumber(date.getMonth() + 1));
        returnArr.push(that.formatNumber(date.getDate()));

        returnArr.push(that.formatNumber(date.getHours()));
        returnArr.push(that.formatNumber(date.getMinutes()));
        returnArr.push(that.formatNumber(date.getSeconds()));

        for (var i in returnArr) {
            format = format.replace(formateArr[i], returnArr[i]);
        }
        return format;
    },


    /**
     * 计算n天后的日期
     * initDate：开始日期，默认为当天日期， 格式：yyyymmdd/yyyy-mm-dd
     * days:天数
     */
    getthedate: function (initDate, days, falg) {
        //可以加上错误处理
        var a = new Date(initDate)
        a = a.valueOf()
        a = a + days * 24 * 60 * 60 * 1000
        a = new Date(a);
        var m = a.getMonth() + 1;
        if (m.toString().length == 1) {
            m = '0' + m;
        }
        var d = a.getDate();
        if (d.toString().length == 1) {
            d = '0' + d;
        }
        if (falg == 1) {
            return a.getFullYear() + "年" + m + "月" + d + "日";
        } else {
            return a.getFullYear() + "-" + m + "-" + d;
        }
    },

    /**
     * 把时间戳格式成 [时,分,秒](到期时间)
     */
    format_seconds: function (time) {
        var time = parseInt(time); // 到期时间 秒

        //当前时间秒
        var tmp = Date.parse(new Date()).toString();
        tmp = parseInt(tmp.substr(0, 10));
        //剩余时间
        time -= tmp;
        var result = {
            hour: '00',
            minte: '00',
            second: '00'
        };
        if (time <= 0) {
            return result;
        }

        // 返回资源

        var minte = 0; // 分
        var hour = 0; // 小时

        // 小时
        // if (time >= 3600) {
        //     hour = parseInt(time / 3600);
        //     time = time - (hour * 3600);
        //     result.hour = (hour > 9) ? hour : '0' + hour;
        // }

        // 分钟   
        if (time >= 60) {
            minte = parseInt(time / 60);
            time = time - (minte * 60);
            result.minte = (minte > 9) ? minte : '0' + minte;
        }
        // 秒
        result.second = (time > 9) ? time : '0' + time;

        // 返回资源
        return result;
    },

    /**
     * 倒计时时间格式
     */
    // 时间格式化输出，如03:25:19 86。每10ms都会调用一次
    date_format: function (micro_second) {
        var that = this;
        // 秒数
        var second = Math.floor(micro_second / 1000);
        // 小时位
        var hr = Math.floor(second / 3600);
        // 分钟位
        var min = that.fill_zero_prefix(Math.floor((second - hr * 3600) / 60));
        // 秒位
        var sec = that.fill_zero_prefix((second - hr * 3600 - min * 60)); // equal to => var sec = second % 60;
        // 毫秒位，保留2位
        var micro_sec = that.fill_zero_prefix(Math.floor((micro_second % 1000) / 10));

        return min + ":" + sec;
    },

    /**
     * 位数不足补零
     */
    fill_zero_prefix: function (num) {
        return num < 10 ? "0" + num : num
    },


    /**
     * 跳转链接地址
     */
    go_url: function (url) {
        let that = this;
        if (!url) {
            wx.switchTab({
                url: '../index/index',
            });
            return;
        }
        // 跳转链接
        wx.navigateTo({
            url: url,
            fail: function (e) {
                if (e.errMsg == "navigateTo:fail can not navigateTo a tabbar page") {
                    wx.switchTab({
                        url: url
                    });
                } else {
                    wx.navigateTo({
                        url: url
                    });
                }
            }
        });
    },

    /**
     * 根据id返回在二维数组中的下标
     * array目标二维数组
     * v 要查询的id
     */
    get_index: function (array, v) {
        if (array) {
            for (var i = 0; i < array.length; i++) {
                if (array[i].id == v) {
                    return i;
                }
            }
        } else {
            return 0;
        }

    },

    /**
     * 根据id返回在二维数组中的下标
     * array目标二维数组
     * v 要查询的值
     */
    get_item: function (array, v) {
        for (var i = 0; i < array.length; i++) {
            if (array[i] == v) {
                return i;
            }
        }
    },


    /**
     * 保存图片
     */
    download_img: function (share_img, fn) {
        var that = this;
        if (share_img == '') {
            that.toast_none('图片加载失败');
            return false;
        }
        that.dialog('您确定保存这张图片吗?', function () {
            that.downloadFile(share_img).then((res) => {
                that.saveImageToPhotosAlbum(res.tempFilePath).then((res) => {
                    that.toast_none('下载成功', function () {
                        if (fn) {
                            fn();
                        }
                    });
                }).catch((res) => {
                    that.toast_none(res);
                })
            })
        })

    },

    // 将下载的图片，保存到系统相册中
    async saveImageToPhotosAlbum(filepath) {
        return new Promise((resolve, reject) => {
            wx.saveImageToPhotosAlbum({
                filePath: filepath,
                async success(response) {
                    if (response.errMsg == "saveImageToPhotosAlbum:ok") {
                        resolve(response)
                    } else {
                        reject('未确认下载到相册')
                    }
                },
                async fail(err) {
                    reject(err)
                }
            });
        })
    },


    // 下载 - 文件
    async downloadFile(url) {
        return new Promise((resolve, reject) => {
            let file = wx.getStorageSync('file' + url) || '';
            if (file) {
                resolve(file)
            } else {
                wx.downloadFile({
                    url: url,
                    async success(res) {
                        let tempFilePath = res.tempFilePath;
                        wx.setStorageSync('file' + url, tempFilePath)
                        resolve(tempFilePath)
                    },
                    fail(err) {
                        reject(err)
                    }
                });
            }
        })
    },


    /**
     * 微信支付
     */
    weChatPay: function (order_id) {
        var that = this;
        wx.showLoading({
            title: '支付中..',
        });
        that.post_ajax('/pay/order_data', {
            user_id: that.user_id,
            order_id: order_id,
        }, function (response, status) {
            if (status == 200) {
                wx.requestPayment({
                    'timeStamp': response.timeStamp,
                    'nonceStr': response.nonceStr,
                    'package': response.package,
                    'signType': response.signType,
                    'paySign': response.paySign,
                    'success': function (res) {
                        that.set_time_out('支付成功', function () {
                            wx.switchTab({
                                url: '/pages/index/index',
                            })
                        });
                    },
                    'fail': function (res) {
                        that.basic_dialog('支付失败，请重新请求');
                    }
                });

            } else {
                that.basic_dialog('支付失败，请重新请求');
            }
            wx.hideLoading();
        });
    },


    // 判断是否为 正确的金额
    checkAmt(dPrice) {

        if (!dPrice || !dPrice.trim()) {
            this.toast_none('金额不能为空~');
            return false;
        }

        // '''第一步：判断是否有非法字符'''
        for (var i = 0; i < dPrice.length; i++) {
            if (isNaN(parseInt(dPrice.charAt(i))) && dPrice.charAt(i) != "." && dPrice.charAt(i) != ",") {
                this.toast_none('请输入正确的金额！');
                return false;
            }
        }
        // '''第二步：如果存在小数点，判断是否仅有一个小数点'''
        if (dPrice.indexOf(".") != dPrice.lastIndexOf(".")) {
            this.toast_none('请输入正确的金额！');
            return false;
        }
        // '''第三步：判断金额是否为零'''
        var re = /,/g;
        var amt1 = dPrice.replace(re, "");
        var amt2 = parseFloat(amt1);
        if (amt2 <= 0) {
            this.toast_none('输入的金额小于或等于零，请重新输入！');
            return false;
        } else {
            // '''第四步：判断金额小数点后是否超过两位'''
            if (amt1.indexOf(".") != -1) {
                var str = amt1.substr(amt1.indexOf(".") + 1);
                if (str.length > 2) {
                    this.toast_none('金额小数点不能超过两位');
                    return false;
                }
            }
            // '''第五步：判断以零开头的金额，小数点是否在第一位'''
            if (amt1.charAt(0) == "0" && amt1.indexOf(".") != 1) {
                this.toast_none('请输入正确的金额！');
                return false;
            }
        }
        return true;
    },

    /**
     * 验证手机
     */
    checkPhone: function (phone) {
        var that = this;
        if (!(/^1(3|4|5|7|8|9)\d{9}$/.test(phone))) {
            that.toast_none('请输入正确手机号');
            return false;
        } else {
            return true;
        }
    },

    /**
     * 获取用户解密后的手机号码
     */
    async get_wechat_phone(code, encrypt_data, iv, fn) {
        var that = this;
        iv = encodeURIComponent(iv);
        encrypt_data = encodeURIComponent(encrypt_data);
        await that.post_ajax('/Login/get_user_wechat_phone', {
            code: code,
            encrypt_data: encrypt_data,
            iv: iv,
        }, function (res, status) {
            console.log(res, status)
            fn(res, status)
        });
    },

    // 获取-省份列表
    async get_province_list(fn) {
        let that = this;
        return await that.get_ajax('/City/get_city_list', '', fn);
    },

    // 获取-城市列表
    async get_city_list(province_id, fn) {
        let that = this;
        return await that.get_ajax('/City/get_city', {
            province_id: province_id
        }, fn);
    },

    /**
     * 获取今日的日期
     */
    getNowFormatDate: function () {
        var date = new Date();
        var seperator1 = "-";
        var year = date.getFullYear();
        var month = date.getMonth() + 1;
        var strDate = date.getDate();
        if (month >= 1 && month <= 9) {
            month = "0" + month;
        }
        if (strDate >= 0 && strDate <= 9) {
            strDate = "0" + strDate;
        }
        var currentdate = year + seperator1 + month + seperator1 + strDate;
        return currentdate;
    },


    /**
     * 打开用户小程序设置
     */
    open_user_seeting(fn) {
        var that = this;
        wx.getSetting({
            success: function (res) {
                var statu = res.authSetting;
                if (!statu['scope.userLocation']) {
                    wx.openSetting({
                        success: function (data) {
                            if (data.authSetting["scope.userLocation"] === true) {
                                //授权成功之后，再调用chooseLocation选择地方
                                that.get_distance(fn);
                            } else {
                                that.toast_none('授权失败');
                            }
                        },
                        fail: function (res) {
                            that.basic_dialog('当前微信版本过低，无法使用该功能，请升级到最新微信版本后重试')
                        }
                    })
                }
            },
            fail: function (res) {
                that.toast_none('调用授权窗口失败');
            }
        })
    },



    /**
     * 分享卡片回调
     */
    share_callback() {
        var that = this;
        if (that.is_share) {
            that.is_share = false;
            that.get_share_integral();
        }
    },


    /**
     * 获取用户信息
     */
    get_user_detail: function (fn) {
        let that = this;
        // 增加form id
        that.post_ajax('/user/index', {
            user_id: that.user_id,
        }, function (res, status) {
            if (status == 200) {
                if (fn) {
                    fn(res);
                }
            } else {
                that.toast_none('获取用户信息失败');
            }
        });
    },


    /**
     * 地理位置授权
     * @param {*} callback 
     */
    getUserLocation: function (callback) {
        let vm = this
        wx.getSetting({
            success: (res) => {
                // res.authSetting['scope.userLocation'] == undefined    表示 初始化进入该页面
                // res.authSetting['scope.userLocation'] == false    表示 非初始化进入该页面,且未授权
                // res.authSetting['scope.userLocation'] == true    表示 地理位置授权
                // 拒绝授权后再次进入重新授权
                if (res.authSetting['scope.userLocation'] != undefined && res.authSetting['scope.userLocation'] != true) {
                    // console.log('authSetting:status:拒绝授权后再次进入重新授权', res.authSetting['scope.userLocation'])
                    wx.showModal({
                        title: '',
                        content: '需要获取你的地理位置，请确认授权',
                        success: function (res) {
                            if (res.cancel) {
                                vm.to_loading = false;
                                wx.showToast({
                                    title: '拒绝授权',
                                    icon: 'none'
                                })
                            } else if (res.confirm) {
                                wx.openSetting({
                                    success: function (dataAu) {
                                        // console.log('dataAu:success', dataAu)
                                        if (dataAu.authSetting["scope.userLocation"] == true) {
                                            //再次授权，调用wx.getLocation的API
                                            vm.getLocation(dataAu, callback)
                                        } else {
                                            vm.to_loading = false;
                                            wx.showToast({
                                                title: '授权失败',
                                                icon: 'none'
                                            })
                                        }
                                    }
                                })
                            }
                        }
                    })
                }
                // 初始化进入，未授权
                else if (res.authSetting['scope.userLocation'] == undefined) {
                    // console.log('authSetting:status:初始化进入，未授权', res.authSetting['scope.userLocation'])
                    //调用wx.getLocation的API
                    vm.getLocation(res, callback)
                }
                // 已授权
                else if (res.authSetting['scope.userLocation']) {
                    // console.log('authSetting:status:已授权', res.authSetting['scope.userLocation'])
                    //调用wx.getLocation的API
                    vm.getLocation(res, callback)
                }
                vm.hideLoading();
            }
        })
    },

    /**
     * 微信获得经纬度
     * @param {*} userLocation 
     * @param {*} callback 
     */
    getLocation: function (userLocation, callback) {
        let vm = this
        wx.getLocation({
            type: "wgs84",
            success: function (res) {
                callback(res);
            },
            fail: function (res) {
                if (res.errMsg === 'getLocation:fail:auth denied') {
                    vm.to_loading = false;
                    wx.showToast({
                        title: '拒绝授权',
                        icon: 'none'
                    })
                }
                if (!userLocation || !userLocation.authSetting['scope.userLocation']) {
                    vm.getUserLocation(callback)
                } else if (userLocation.authSetting['scope.userLocation']) {
                    vm.to_loading = false;
                    wx.showModal({
                        title: '',
                        content: '请在系统设置中打开定位服务',
                        showCancel: false,
                        success: result => {

                        }
                    })
                } else {
                    that.to_loading = false;
                    wx.showToast({
                        title: '授权失败',
                        icon: 'none'
                    })
                }
                vm.hideLoading();
            }
        })
    },

    /**
     * 根据经纬度回去城市
     * longitude经度
     * latitude纬度
     */
    loadCity: function (longitude, latitude, fn) {
        var that = this
        wx.request({
            url: 'https://apis.map.qq.com/ws/geocoder/v1/?location=' + latitude + ',' + longitude + '&key=' + that.qq_map_key,
            data: {},
            header: {
                'Content-Type': 'application/json'
            },
            success: function (res) {
                var data = res.data.result;
                if (fn) {
                    fn(data);
                }
            },
            fail: function () {
                that.to_loading = false;
                that.toast_none('获取定位失败');
            },

        })
    },

    // 判断标签
    isEmojiCharacter(substring) {
        for (var i = 0; i < substring.length; i++) {
            var hs = substring.charCodeAt(i);
            if (0xd800 <= hs && hs <= 0xdbff) {
                if (substring.length > 1) {
                    var ls = substring.charCodeAt(i + 1);
                    var uc = ((hs - 0xd800) * 0x400) + (ls - 0xdc00) + 0x10000;
                    if (0x1d000 <= uc && uc <= 0x1f77f) {
                        return true;
                    }
                }
            } else if (substring.length > 1) {
                var ls = substring.charCodeAt(i + 1);
                if (ls == 0x20e3) {
                    return true;
                }
            } else {
                if (0x2100 <= hs && hs <= 0x27ff) {
                    return true;
                } else if (0x2B05 <= hs && hs <= 0x2b07) {
                    return true;
                } else if (0x2934 <= hs && hs <= 0x2935) {
                    return true;
                } else if (0x3297 <= hs && hs <= 0x3299) {
                    return true;
                } else if (hs == 0xa9 || hs == 0xae || hs == 0x303d || hs == 0x3030
                    || hs == 0x2b55 || hs == 0x2b1c || hs == 0x2b1b
                    || hs == 0x2b50) {
                    return true;
                }
            }
        }
    }
})