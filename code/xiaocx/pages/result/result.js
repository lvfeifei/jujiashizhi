var app = getApp();
var timer;
Page({

    /**
     * 页面的初始数据
     */
    data: {
        fixs: 0,
        top: 0,
        nav_height: 0,
        texts_num: 0,
        avatar_url: '', //用户头像,
        one_keys_list: [],
        chat_list: [],
        is_loading: false,
        loading: false,
        page: 1,
        limit: 10,
        content: '',
        con_len: 0, // 长度

        disabled: true, // 是否禁用
        is_focus: false, // 是否出发
        chat_num: '', // 消息内容
        user_info: {}, // 用户信息
        status: 1, // 判断当前是输入状态还是语音状态   1 输入 2 切换语音 3 开始讲话 
        voice_time: 0, // 录音的时间
        is_auth_voice: false, // 判断是否开启录音
        is_play: false, // 是否有播放的音频
        zj_message: {},
        last_url: '', // 上一条播放的音频

        iosSABottom: 0,

        is_record: false,                   // 录音中
    },

    /**
     * 生命周期函数--监听页面加载
     */
    onLoad(options) {
        let that = this;
        let res = app.systemInfo
        let bottomLift = (res.screenHeight - res.safeArea.bottom - 5) * 2 + 150;
        that.setData({
            bottomLift
        })
        //  自定义导航
        let menuButtonObject = wx.getMenuButtonBoundingClientRect();
        wx.getSystemInfo({
            success: res => {
                let statusBarHeight = res.statusBarHeight,
                    navHeight = statusBarHeight + menuButtonObject.height + (menuButtonObject.top - statusBarHeight) * 2; //导航高度  
                    that.setData({
                    top: statusBarHeight,
                    nav_height: navHeight,
                })
            },
            fail(err) {
                console.log(err);
            }
        })

        // 上传录音 
        app.recorderManager.onStop((res) => {
            console.log(res)
            if (res.duration > 1000) {
                that.uploadServe(1, res.tempFilePath) // 1 语音 2 图片
            } else {
                app.toast_none('录音时长过短！')
            }
        })

        app.backgroundAudioManager.onEnded(() => {
            that.setData({
                is_play: false
            })
        })
    },

    // 播放录音
    play_audio(e) {
        let url = app.getMyItem(e, 'audio')
        let {
            is_play,
            last_url
        } = this.data

        if (url == last_url) {
            this.setData({
                last_url: ''
            })
            return app.backgroundAudioManager.stop();
        } else {
            if (is_play) {
                app.backgroundAudioManager.stop();
                this.setData({
                    is_play: false
                })
            }
            app.backgroundAudioManager.src = url,
                app.backgroundAudioManager.play();
            this.setData({
                is_play: true,
                last_url: url
            })
        }



    },

    // 开始 录音
    start_record() {
        let that = this;
        if (that.data.is_record) {
            this.setData({
                status: 3
            })
            let {
                voice_time
            } = this.data
            // 开始录音
            const options = {
                duration: 60000, //指定录音的时长，单位 ms，最大为10分钟（600000），默认为1分钟（60000） 
                sampleRate: 16000, //采样率
                numberOfChannels: 1, //录音通道数
                encodeBitRate: 96000, //编码码率
                format: 'mp3', //音频格式，有效值 aac/mp3
                frameSize: 50, //指定帧大小，单位 KB
            }
            app.recorderManager.start(options);
            // 清除定时器 
            clearInterval(timer);
            timer = setInterval(function () {
                // 判断大于 60 秒就关闭 
                if (voice_time >= 60) {
                    clearInterval(timer);
                    // 发送录音
                    that.send_audio()
                } else {
                    voice_time += 1;
                    that.setData({
                        voice_time
                    })
                }
            }, 1000);
        }
        
    },

    // 录制中
    voice_ing(){
        var that = this
        console.log(111);
        that.setData({
            status: 2,
            is_auth_voice: true
        })
    },

    // 按下事件
    touchstart() {
        var that = this;
        app.backgroundAudioManager.stop();
        that.data.is_record = true;
        wx.authorize({
            scope: 'scope.record',
            success() {
                that.start_record()
            },
            fail() {
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
                                                that.voice_ing()
                                            },
                                        })
                                    } else {
                                        //第二次才成功授权
                                        console.log("设置录音授权成功");
                                        that.voice_ing()
                                    }
                                },
                            })
                        }
                    },

                })
            }
        })

    },

    // 上传到服务器
    uploadServe(type, file) {
        let that = this;
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
                let res = JSON.parse(data.data)
                if (res.status != 1) {
                    app.toast_none(res.msg)
                } else {
                    // 返回的 录音url   直接发送数据 res.data.imgurl  
                    that.send_message(res.data.imgurl, 3);
                }
            }
        })
    },


    // 点击播放录音
    play_music(e) {
        let audio = app.getMyItem(e, 'audio')
        app.backgroundAudioManager.src = audio
        app.backgroundAudioManager.play();
    },

    // 抬起事件
    touchend() {
        let {
            voice_time
        } = this.data
        console.log('抬起事件');
        this.data.is_record = false;
        this.send_audio()
        this.setData({
            status: 2
        })
    },

    // 发送录音
    send_audio() {
        app.recorderManager.stop();
        // 清除定时器 
        clearInterval(timer);
    },

    // 跳转授权登录页面
    go_auth() {
        wx.navigateTo({
            url: '../my/hzxx/hzxx',
        })
    },

    change_status(e) {
        let status = app.getMyItem(e, 'status')
        this.setData({
            status
        })
    },

    // 监听页面
    // onPageScroll(e) { 
    //     if(e.scrollTop > 10){
    //     this.setData({
    //         fixs: 1
    //     });
    //     } else {
    //     this.setData({
    //         fixs: 0
    //     });
    //     }
    // }, 


    /**
     * 生命周期函数--监听页面显示
     */
    async onShow() {
        clearInterval(app.count_unread_timer)
        app.is_show_tip = false
        if (typeof this.getTabBar === 'function' &&
            this.getTabBar()) {
            this.getTabBar().setData({
                selected: 1,
                show: true
            })
        }

        var that = this;
        let user_info = await app.get_user_info()
        //获取屏幕高度
        that.setData({
            height: wx.getSystemInfoSync().windowHeight * 2 - 129,
            user_info,
            status: 1
        })

        // 获取专家信息
        that.get_zj_message()
        that.getDetail();


    },

    // 获取专家信息
    get_zj_message() {
        let that = this
        app.get_ajax('/user_chat/expert_default', '', function (res, code) {
            that.setData({
                zj_message: res
            })
        })
    },


    /**
     * 生命周期函数--监听页面隐藏
     */
    onHide: function () {
        clearInterval(timer);
        let {
            is_play
        } = this.data
        if (is_play) {
            app.backgroundAudioManager.stop();
            this.setData({
                is_play: false
            })
        }

        app.get_user_count_unread()
    },

    /**
     * 生命周期函数--监听页面卸载
     */
    onUnload: function () {
        clearInterval(timer);
        let {
            is_play
        } = this.data
        if (is_play) {
            app.backgroundAudioManager.stop();
            this.setData({
                is_play: false
            })
        }
    },


    /**
     * 获取全部消息
     */
    getDetail: function () {
        var that = this;
        that.data.loading = true;
        wx.showLoading({
            title: '请求中...',
            mask: true
        })
        app.post_ajax('/User_chat/chart_list', {
            user_id: app.user_id,
            // page: that.data.page,
            // limit: that.data.limit
        }, function (res, status) {
            wx.hideLoading();
            if (status == 200) {
                that.setData({
                    chat_list: res,
                }, function () {
                    that.data.loading = false;
                    that.data.chat_num = 'chat' + (res.length - 1);
                    that.setData({
                        chat_num: that.data.chat_num
                    })

                    // 获取最新消息
                    that.get_new_chat()
                    app.hideLoading();
                });
            } else {
                app.hideLoading();
            }
        });
    },

    /**
     * 发消息
     * name:内容
     * chat_type 1文本 2图片 3 录音
     */
    send_message: function (name, chat_type) {


        var that = this;
        app.post_ajax('/User_chat/reply_user', {
            // user_id: app.user_id,
            content: name,
            msg_type: chat_type,
            voice_time: that.data.voice_time
        }, function (res, status) {
            if (status == 200) {
                if (res.length > 0) {
                    for (var i in res) {
                        that.data.chat_list.push(res[i]);
                    }
                    that.data.chat_num = 'chat' + (that.data.chat_list.length - 1);
                    that.setData({
                        voice_time: 0,
                        chat_list: that.data.chat_list,
                        chat_num: that.data.chat_num
                    })
                }
                wx.hideKeyboard();
            } else {
                app.toast_none(res)
                app.hideLoading();
            }
        });
    },

    /**
     * 上拉加载更多
     */
    loadMore: function () {
        var that = this;
        if (that.data.loading == true) {
            return false;
        }
        that.data.page += 1;
        app.showLoading('加载中');
        that.getDetail();
    },

    /**
     * 设置内容
     */
    set_content: function (e) {
        var that = this;
        that.data.content = e.detail.value;
        that.setData({
            con_len: that.data.content.length
        })
    },

    /**
     * 发送
     */
    send_write: function () {
        var that = this;
        wx.requestSubscribeMessage({
            tmplIds: ['KYeiLmBXUM0SIo9Hf1IW_8PMA3uzjVNEnDQ0CDnLMTA'],  // 咨询回复通知
            complete: () => {
                if (!that.data.content) {
                    return app.toast_none('发送内容不能为空')
                }
                if (!that.data.content.trim()) {
                    return app.toast_none('发送内容不能为空')
                }
                that.send_message(that.data.content, 1);
                that.setData({
                    content: '',
                    con_len: 0
                })
            }
        })

    },

    /**
     * 预览图片
     */
    yu: function (e) {
        var url = e.currentTarget.dataset.e;
        console.log(e);
        wx.previewImage({
            current: url, // 当前显示图片的http链接
            urls: [url] // 需要预览的图片http链接列表
        })
    },


    /**
     * 上传-照片
     */
    upload_photo: function () {
        let that = this;
        if (app.user.is_register == 2) {
            wx.navigateTo({
                url: '../index/register',
            })
            return false;
        }
        wx.requestSubscribeMessage({
            tmplIds: ['NcL2W_n8SRzSzAcVjdOMh6D-2SVYQ1C5okEW8XL67p0'],
            success: function (res) {
                console.log(res)
            },
            fail: function (e) {
                console.log(e)
            }
        })

        initQiniu();
        wx.chooseImage({
            count: 1, // 默认9
            sizeType: ['original', 'compressed'], // 可以指定是原图还是压缩图，默认二者都有
            sourceType: ['album', 'camera'], // 可以指定来源是相册还是相机，默认二者都有
            success: function (res) {

                // 返回选定照片的本地文件路径列表，tempFilePath可以作为img标签的src属性显示图片
                var tempFilePaths = res.tempFilePaths;
                app.showLoading('发送中...');
                that.data.is_upload = true;
                that.setData({
                    is_upload: that.data.is_upload,
                }, function () {
                    // 交给七牛上传
                    qiniuUploader.upload(tempFilePaths[0], (res) => {
                            that.send_message(res.imageURL, 2);
                        }, (error) => {

                            app.hideLoading();
                            console.error('error: ' + JSON.stringify(error));
                        }, null, // 可以使用上述参数，或者使用 null 作为参数占位符
                        (progress) => {
                            if (that.data.upload_percent != progress.progress) {
                                that.setData({
                                    upload_percent: progress.progress
                                }, function () {
                                    if (progress.progress == 100) {
                                        that.data.is_upload = false;
                                        that.setData({
                                            is_upload: that.data.is_upload
                                        }, function () {
                                            app.hideLoading();
                                        });
                                    }
                                });
                                console.log('上传进度', progress.progress);
                            } else {
                                app.hideLoading();
                            }
                        }, cancelTask => that.setData({
                            cancelTask
                        })
                    );
                });
            }
        })
    },

    /**
     * 去处理结果
     */
    go_detail: function (e) {
        var that = this;
        console.log(e);
        var problem_id = e.currentTarget.dataset.id;
        wx.navigateTo({
            url: '../index/problem_detail?id=' + problem_id,
        })
    },



    /**
     * 获取最新消息
     */
    get_new_chat: function () {
        var that = this;
        if (!this.data.user_info.authorization) {
            return false;
        }
        clearInterval(timer);
        timer = setInterval(function () {
            app.get_ajax('/User_chat/monitor_news', {
                user_id: app.user_id,
            }, function (res, status) {
                if (res.length > 0) {
                    for (var i in res) {
                        that.data.chat_list.push(res[i]);
                    }
                    that.setData({
                        chat_list: that.data.chat_list,
                    }, function () {
                        that.setData({
                            chat_num: 'chat' + that.data.chat_list.length - 1
                        })
                    });
                }
            });
        }, 3000);
    }
})