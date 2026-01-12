const app = getApp();
var timer = 0
Page({

    /**
     * 页面的初始数据
     */
    data: {
        show_dialog_status: 2,
        fixs: 0,
        top: 0,
        nav_height: 0,
        show_dialog: false,
        show_dialog_title: '',
        scenes_id: '', // 当前打开的scenes_id
        scenes_src: '', // 保存录音的临时目录
        step: 1, // 当前状态  默认未录制
        play_status: 1, // 播放状态  1 暂停 2 播放
        recording_time: '00:00', // 录音的时间展示 
        status: "",
        play_time: '00:00', // 播放的时间
        play_sec: 0, // 播放秒数
        total_time: 0, // 播放总时长 
        scenes_one: wx.getStorageSync('scenes_one') || '',
        scenes_two: wx.getStorageSync('scenes_two') || '',
        scenes_three: wx.getStorageSync('scenes_three') || '',
        scenes_four: wx.getStorageSync('scenes_four') || '',
        scenes_five: wx.getStorageSync('scenes_five') || '',
        scenes_one_total_time: wx.getStorageSync('scenes_one_total_time') || '',
        scenes_two_total_time: wx.getStorageSync('scenes_two_total_time') || '',
        scenes_three_total_time: wx.getStorageSync('scenes_three_total_time') || '',
        scenes_four_total_time: wx.getStorageSync('scenes_four_total_time') || '',
        scenes_five_total_time: wx.getStorageSync('scenes_five_total_time') || '',
        scenes_data: {
            1: 'scenes_one',
            2: 'scenes_two',
            3: 'scenes_three',
            4: 'scenes_four',
            5: 'scenes_five'
        },
        scenes_list: [],
        success_url_item: '' // 点击的否为完成的状态
    },

    // 跳转下一步
    go_next() {

        let {
            scenes_one,
            scenes_two,
            scenes_three,
            scenes_four,
            scenes_five,
            scenes_one_total_time,
            scenes_two_total_time,
            scenes_three_total_time,
            scenes_four_total_time,
            scenes_five_total_time
        } = this.data
        if (!scenes_one && !scenes_two && !scenes_three && !scenes_four && !scenes_five) {
            return app.toast_none('请最少完成一个场景的录音')
        }

        //  组合提交数据
        let form_data = {
            family_relation: wx.getStorageSync('taidu_data'),
            order_id: wx.getStorageSync('gongdan_id'),
            scenes_one,
            scenes_two,
            scenes_three,
            scenes_four,
            scenes_five,
            scenes_one_time: scenes_one_total_time,
            scenes_two_time: scenes_two_total_time,
            scenes_three_time: scenes_three_total_time,
            scenes_four_time: scenes_four_total_time,
            scenes_five_time: scenes_five_total_time
        }
        wx.showLoading({
            title: '请求中...',
            mask: true
        })
        app.post_ajax('/order/orderresearchcreate', form_data, function (res, code) {
            let {
                msg,
                status
            } = res
            wx.hideLoading();
            if (status == 1) {
                wx.setStorageSync('taidu_data', '')
                wx.setStorageSync('scenes_one', '')
                wx.setStorageSync('scenes_two', '')
                wx.setStorageSync('scenes_three', '')
                wx.setStorageSync('scenes_four', '')
                wx.setStorageSync('scenes_five', '')

                wx.setStorageSync('scenes_one_total_time')
                wx.setStorageSync('scenes_two_total_time')
                wx.setStorageSync('scenes_three_total_time')
                wx.setStorageSync('scenes_four_total_time')
                wx.setStorageSync('scenes_five_total_time')

                wx.navigateTo({
                    url: '../diaocha_wancheng/diaocha_wancheng',
                })
            } else {
                app.toast_none(msg);
            }

        })

    },

    //  返回上一页
    go_back() {
        wx.navigateBack({
            delta: 1,
        })
    },


    // 开始录制音频
    start_luzhi_time() {
        let total_time = 0
        let that = this; 
        // 清除定时器 
        clearInterval(timer);
        timer = setInterval(function () {
            total_time += 1; 
            // console.log(total_time) 
            var recording_time = app.get_minte_scond(total_time);
            that.setData({
                recording_time,
                total_time
            })
            
            if(total_time >= 600){
                that.success_luyin()
            }
        }, 1000);
    },

    success_luyin(){
        clearInterval(timer) 
        this.wancheng_luyin()
    },

    // 关闭 dialog 
    close() {
        this.setData({
            show_dialog: false,
            step: 1,
            recording_time: '00:00',
            play_time: '00:00',
            play_status: 1,
            play_sec: 0,
            scenes_id: '',
            scenes_src: "",
            total_time: 0,
            success_url_item: ""
        })
        app.recorderManager.stop();
        app.backgroundAudioManager.stop();
        clearInterval(timer)
    },

    // 开始播放音频
    start_play_time(play_sec) {
        let that = this
        let {
            total_time
        } = this.data
        // 清除定时器 
        clearInterval(timer);
        timer = setInterval(function () {
            play_sec += 1;
            if (play_sec >= total_time) {
                clearInterval(timer);
                that.setData({
                    play_time: '00:00',
                    play_sec: 0,
                    play_status: 1
                })
            } else {
                var play_time = app.get_minte_scond(play_sec);
                that.setData({
                    play_time,
                    play_sec
                })
            }

        }, 1000);
    },

    // 点击播放录音
    play_music() {
        let {
            play_sec,
            play_time,
            success_url_item
        } = this.data
        this.setData({
            play_status: 2,
            play_sec,
            play_time
        })
        let play_url = success_url_item ? this.data[success_url_item] : this.data.scenes_src
        app.backgroundAudioManager.src = play_url
        app.backgroundAudioManager.play();
        this.start_play_time(play_sec);
    },

    // 暂停播放录音
    parse_music() {
        this.setData({
            play_status: 1,
        })
        clearInterval(timer);
        app.backgroundAudioManager.pause();
    },

    // 点击重新录制
    reset_luzhi() {

        let {
            success_url_item
        } = this.data
        if (success_url_item) {
            wx.setStorageSync(success_url_item, '')
            wx.setStorageSync(success_url_item + '_total_time', '')
            this.setData({
                success_url_item: '',
                [success_url_item]: '',
                [success_url_item + '_total_time']: 0
            })
        }

        this.setData({
            step: 1,
            recording_time: '00:00',
            play_time: '00:00',
            play_status: 1,
            play_sec: 0,
            total_time: 0
        })
        // 清除定时器
        clearInterval(timer);
        // 有播放的就暂停播放
        app.backgroundAudioManager.stop();
    },

    // 打开录音弹窗
    open_dialog(e) {
        let {
            scenes_data,
            scenes_list
        } = this.data
        let scenes_id_x = app.getMyItem(e, 'scenes_id')
        let scenes_id = scenes_data[scenes_id_x]
        if (this.data[scenes_id]) {
            //    return  app.toast_none('该场景已完成录制') 
            let recording_time = app.get_minte_scond(this.data[scenes_id + '_total_time'])
            let total_time = this.data[scenes_id + '_total_time']
            this.setData({
                step: 3, // 当前状态  默认未录制
                play_status: 1, // 播放状态  1 暂停 2 播放
                recording_time,
                total_time,
                success_url_item: scenes_id
            })
        }
        this.setData({
            scenes_id,
            show_dialog: true,
            show_dialog_title: scenes_list[scenes_id_x - 1].scenes_title
        })
    },

    // 开始录制
    start_record(){
        var that = this
        const options = {
            duration: 6000020, //指定录音的时长，单位 ms，最大为10分钟（600000），默认为1分钟（60000） 
            sampleRate: 16000, //采样率
            numberOfChannels: 1, //录音通道数
            encodeBitRate: 96000, //编码码率
            format: 'mp3', //音频格式，有效值 aac/mp3
            frameSize: 40, //指定帧大小，单位 KB
        }
        that.setData({
            status: 2,
            step: 2
        })
        app.recorderManager.start(options);
    },

    // 点击开始录制
    start_luzhi() {
        var that = this;
        app.backgroundAudioManager.stop();

        //开始录音
        wx.authorize({
            scope: 'scope.record',
            success() {
                console.log("录音授权成功");
                //第一次成功授权后 状态切换为2
                that.start_record();
            },
            fail() {
                console.log("第一次录音授权失败");
                wx.showModal({
                    title: '提示',
                    content: '您未授权录音，功能将无法使用',
                    showCancel: true,
                    confirmText: "授权",
                    confirmColor: "#52a2d8",
                    success: function (res) {
                        if (res.confirm) {
                            //确认则打开设置页面（重点）
                            wx.openSetting({
                                success: (res) => {
                                    console.log(res.authSetting);
                                    if (!res.authSetting['scope.record']) {
                                        //未设置录音授权
                                        console.log("未设置录音授权");
                                        wx.showModal({
                                            title: '提示',
                                            content: '您未授权录音，功能将无法使用',
                                            showCancel: false,
                                            success: function (res) {
                                                that.setData({
                                                    step: 1,
                                                })
                                            },
                                        })
                                    } else {
                                        //第二次才成功授权
                                        app.toast_none('授权成功，可以开始录音啦！')
                                    }
                                },
                            })
                        }
                    },

                })
            }
        })

    },

    // 点击完成录制 上传录音 保存链接
    confirm_btn() {
        let {
            scenes_id,
            scenes_src
        } = this.data
        console.log(scenes_id, scenes_src)
        wx.showLoading({
            title: '提交中...',
            mask: true
        }) 
         this.uploadServe(1, scenes_src, scenes_id) // 1 语音 2 图片 
    },

    // 上传到服务器
    uploadServe(type, file, scenes_id) {

        console.log(type, file, scenes_id)
        let that = this;
        let {
            total_time
        } = this.data
        wx.uploadFile({
            url: app.api_url + '/upload/upload_img',
            filePath: file,
            name: 'file',
            header: {
                "Content-Type": "multipart/form-data",
                'codeid': wx.getStorageSync('codeid') || ''
            },
            formData: {
                type,
                folder: "audio"
            },
            success: function (data) {
                wx.hideLoading();
                let res = JSON.parse(data.data)
                if (res.status != 1) {
                    app.toast_none(res.msg)
                } else {
                    that.setData({
                        [scenes_id]: res.data.imgurl,
                        [scenes_id + '_total_time']: total_time
                    })
                    app.toast_none(res.msg)
                    wx.setStorageSync(scenes_id, res.data.imgurl)
                    wx.setStorageSync(scenes_id + '_total_time', total_time)
                    that.close()
                }
            },
            fail: function (err) {
                console.log(err);
            }
        })
    },

    // 完成录音
    wancheng_luyin() {
        if (this.data.total_time <= 120) {
            return app.toast_none('录音不能低于2分钟');
        }
        app.recorderManager.stop();
        console.log('完成录制')
    },

    // 监听页面
    onPageScroll(e) {
        // console.log(e.scrollTop)
        if (e.scrollTop > 10) {
            this.setData({
                fixs: 1
            });
        } else {
            this.setData({
                fixs: 0
            });
        }
    },


    /**
     * 生命周期函数--监听页面加载
     */
    onLoad(options) {
        let that = this 
        //  自定义导航
        let menuButtonObject = wx.getMenuButtonBoundingClientRect();
        wx.getSystemInfo({
            success: res => {
                let statusBarHeight = res.statusBarHeight,
                    navHeight = statusBarHeight + menuButtonObject.height + (menuButtonObject.top - statusBarHeight) * 2; //导航高度  
                this.setData({
                    top: statusBarHeight,
                    nav_height: navHeight
                })
            },
            fail(err) {
                console.log(err);
            }
        })
        // 获取列表
        that.get_list()

        app.recorderManager.onStart(() => {
            console.log('开始录音了')
            that.start_luzhi_time()
        });

        app.recorderManager.onStop((res) => {
            console.log('停止录音', res)
            const {
                tempFilePath
            } = res
            // 停止计时功能
            clearInterval(timer); 
            that.setData({
                scenes_src: tempFilePath,
                step: 3
            }) 
        })

    },


    // 获取列表
    get_list() {
        wx.showLoading({
            title: '加载中...',
        })
        let that = this;
        app.post_ajax('/family_relation/scenes', '', function (res, code) {
            let {
                data,
                msg,
                status
            } = res
            wx.hideLoading();
            if (status == 1) {
                that.setData({
                    scenes_list: data
                })
            } else {
                app.toast_none(msg);
            }
        })
    },

    /**
     * 生命周期函数--监听页面初次渲染完成
     */
    onReady() {

    },

    /**
     * 生命周期函数--监听页面显示
     */
    onShow() {

    },

    /**
     * 生命周期函数--监听页面隐藏
     */
    onHide() {
        app.recorderManager.stop();
        this.close();
    },

    /**
     * 生命周期函数--监听页面卸载
     */
    onUnload() {

    },

    /**
     * 页面相关事件处理函数--监听用户下拉动作
     */
    onPullDownRefresh() {

    },

    /**
     * 页面上拉触底事件的处理函数
     */
    onReachBottom() {

    },

    /**
     * 用户点击右上角分享
     */
    onShareAppMessage() {

    }
})