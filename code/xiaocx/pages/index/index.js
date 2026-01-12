
const app = getApp();

Page({

    /**
     * 页面的初始数据
     */
    data: { 
        fixs: 0,
        top: 0,
        nav_height: 0,
        change_menu_active: 1,
        show_dialog: false,
        show_type: 1,                           //    1大图2小图 
        totalPages: 1,
        page: 1,
        count: 0,
        result_list: [],

        page2: 1,
        result_list2: [],

        user_info: {},  // 用户信息
        is_show_pj_box: false,  // 是否显示评价模块
        pj_id: '2',
        is_show_xieyi_dialog:false  // 是否展示协议弹窗
    },

    // 关闭弹窗
    close() {
        wx.setStorageSync('dialog_time', Date.parse(new Date()) / 1000)
        this.setData({
            show_dialog: false
        })
    },

    // 跳转测评
    go_ceping() {

        let { user_info } = this.data
        // 判断用户是否授权 
        if (user_info.authorization != 1) {
            wx.navigateTo({
                url: '../my/hzxx/hzxx'
            })
        } else {
            // 判断是否可以点击提交工单 
            // app.post_ajax('/order/is_pending','', function(res, code){  
            //     let {data,status} = res  
            //       if (status == 1) { 
            //         if(data.status){
            //           app.toast_none(data.tips)
            //         }else{
            //           wx.navigateTo({
            //             url: './evaluation/evaluation',
            //           })
            //         } 
            //       } 
            // })    

            wx.navigateTo({
                url: './evaluation/evaluation',
            })
        }

    },

    // 跳转照护方案
    go_zhaohufangan() {

        let { user_info } = this.data
        // 判断用户是否授权 
        if (user_info.authorization != 1) {
            wx.navigateTo({
                url: '../my/hzxx/hzxx'
            })

        } else {
            // 判断是否可以点击提交工单 
            app.post_ajax('/order/program_info', '', function (res, code) {
                let { data, status, msg } = res
                if (status == 1) {
                    wx.navigateTo({
                        url: '../my/zhfa/zhfa?id=' + data.id,
                    })
                } else {
                    app.toast_none(msg)
                }
            })
        }

    },

    // 判断是否显示评价弹窗
    is_pop_up() {
        let that = this;
        app.post_ajax('/evaluate/is_pop_up', '', function (res, code) {
            let { data, status } = res
            if (status == 1 && data.status) {
                wx.setStorageSync('gongdan_id', data.id)
                // 判断是否需要 展示弹窗
                let show_dialog_status = false
                let dialog_time = wx.getStorageSync('dialog_time')
                if (dialog_time) {
                    // 判断是否 超过 1天  
                    let current_time = Date.parse(new Date()) / 1000;
                    let hour = Math.ceil((dialog_time - current_time) / 60 / 60)
                    if (hour >= 24) {
                        show_dialog_status = true
                    }
                } else {
                    show_dialog_status = true
                }
                that.setData({
                    is_show_pj_box: true,
                    show_dialog: show_dialog_status
                })
            } else {
                wx.setStorageSync('gongdan_id', '')
                that.setData({
                    is_show_pj_box: false,
                    show_dialog: false
                })
            }
        })
    },

    // 跳转评价页面
    go_pj_page() {
        this.setData({
            show_dialog: false,
        })
        wx.navigateTo({
            url: './pingjia/pingjia',
        })
    },

    // 获取列表
    get_list() {
        wx.showLoading({
            title: '请求中...',
        })
        let that = this; 
        let params = {
            page: that.data.page,
            limit: 10,
            help_class_id:  1
        }
        app.post_ajax('/help/index', params, (res, code) => {
            let { data, msg, status } = res
            wx.hideLoading();
            if (status == 1) {
                if (data.count == 0) {
                    that.data.page --;
                } else {
                    that.setData({
                        result_list: that.data.page==1?data.list: that.data.result_list.concat(data.list),
                        show_type: data.show_type
                    })
                }
            } else {
                app.toast_none(msg);
            }
        })
    },

    get_list2() {
        wx.showLoading({
            title: '请求中...',
        })
        let that = this; 
        let params = {
            page: that.data.page2,
            limit: 10,
            help_class_id:  2
        }
        app.post_ajax('/help/index', params, (res, code) => {
            let { data, msg, status } = res
            wx.hideLoading();
            if (status == 1) {
                if (data.count == 0) {
                    that.data.page2 --;
                } else {
                    that.setData({
                        result_list2: that.data.page2==1?data.list: that.data.result_list2.concat(data.list),
                        show_type: data.show_type
                    })
                }
            } else {
                app.toast_none(msg);
            }
        })
    },

    // 跳转协议详情
    go_xieyi(e){
        let key = e.currentTarget.dataset.key; 
        wx.navigateTo({
            url: './xieyi/xieyi?key='+key,
        })
    },

    // 不同意协议
    no_agree_btn(){
        app.toast_none('请关闭小程序并退出')
    },

    // 同意协议
    agree_btn(){   
        let that = this
        app.post_ajax('/user/agreement', {}, (res, code) => {
            console.log(res)
            let { msg, status } = res 
            if (status == 1) {
                // 关闭弹窗
                that.setData({ 
                    is_show_xieyi_dialog:false
                }) 
                that.get_user_desc()
            } else {
                app.toast_none(msg);
            }
        })

       
    },

    // 切换菜单
    change_menu(e) {
        let that = this;
        let change_menu_active = e.currentTarget.dataset.menu;
        wx.setStorageSync('menu_active', change_menu_active)
        // if (change_menu_active == 1) {
        //     that.data.page = 1;
        //     this.get_list()
        // } else {
        //     that.data.page2 = 1;
        //     this.get_list2()
        // }
        that.setData({
            change_menu_active
        })
    },

    // 监听页面
    onPageScroll(e) {
        // console.log(e.scrollTop)
        if (e.scrollTop > 10) {
            this.setData({
                fixs: e.scrollTop
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
        //  自定义导航
        let menuButtonObject = wx.getMenuButtonBoundingClientRect();
        wx.getSystemInfo({
            success: res => {
                let statusBarHeight = res.statusBarHeight,
                    navHeight = statusBarHeight + menuButtonObject.height + (menuButtonObject.top - statusBarHeight) * 2;//导航高度  
                this.setData({
                    top: statusBarHeight,
                    nav_height: navHeight
                })
            },
            fail(err) {
                console.log(err);
            }
        })
 
        let change_menu_active = wx.getStorageSync('menu_active') || 1
        this.setData({
            change_menu_active
        })

        // 获取列表
        this.get_list();
        this.get_list2();
    },


    /**
     * 生命周期函数--监听页面初次渲染完成
     */
    onReady() {
        
    },

    // 跳转文章详情
    go_detail(e) {
        const id = app.getMyItem(e, 'ids')
        wx.navigateTo({
            url: '../index/article_detail/article_detail?id=' + id,
        })
    },

    // 跳转页面
    go_jump(e) {
        let url = app.getMyItem(e, 'url')
        let { user_info } = this.data
        // 判断用户是否授权 
        if (user_info.authorization != 1) {
            url = '../my/hzxx/hzxx'
        }
        wx.navigateTo({
            url
        })
    },
    /**
     * 生命周期函数--监听页面显示
     */
    onShow() {
        
        // 获取用户信息
        this.get_user_desc()
    },

    // 获取用户信息
    async get_user_desc() {
        let that = this;
        let user_info = await app.get_user_info()

        if(user_info.is_agree == 1){
            that.setData({
                is_show_xieyi_dialog: true
            })
        } else {
            // 判断是否显示评价弹窗
            that.is_pop_up()
        }
        if (typeof that.getTabBar === 'function' && that.getTabBar()) {
            that.getTabBar().setData({
                selected: 0,
                show: !that.data.is_show_xieyi_dialog
            })
        }
        this.setData({
            user_info
        })
    },

    /**
     * 生命周期函数--监听页面隐藏
     */
    onHide() {
        
    },

    /**
     * 生命周期函数--监听页面卸载
     */
    onUnload() {
        // wx.setStorageSync('menu_active',1)
    },

    /**
     * 页面相关事件处理函数--监听用户下拉动作
     */
    onPullDownRefresh() {
    },

    /**
     * 页面上拉触底事件的处理函数
     */
    async onReachBottom() {
        let that = this;

        if (that.data.change_menu_active == 1) {
            that.data.page ++;
            await this.get_list();
        } else {
            that.data.page2 ++;
            await this.get_list2();
        }
    },

    /**
     * 用户点击右上角分享
     */
    onShareAppMessage() {

    }
})