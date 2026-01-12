const app = getApp();
Page({

    /**
     * 页面的初始数据
     */
    data: {
        fixs: 0,
        top: 0,
        nav_height: 0,
        user_info: {},  // 用户㤈
        history_list: [] // 历史照护记录列表
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

    },


    // 获取历史记录列表
    async get_history_list(type) {
        wx.showLoading({
            title: '请求中...',
        })
        let history_list = await app.get_user_history()
        //停止下拉刷新
        if (type) {
            wx.stopPullDownRefresh();
        }
        wx.hideLoading();

        // 循环放一个二维数据
        let date = []
        let all_data = []
        if (history_list.length) {
            history_list.forEach(item => {
                if (!date.includes(item.create_time)) {
                    date.push(item.create_time)
                }
            });

            date.forEach(item => {
                let obj = {
                    create_time: item,
                    data_list: []
                }
                all_data.push(obj)
            });

            history_list.forEach(item => {
                all_data.forEach((i, index) => {
                    if (item.create_time == i.create_time) {
                        all_data[index].data_list.push(item)
                    }
                })
            })
        }

        this.setData({
            history_list: all_data
        })
    },

    // 跳转评价
    go_pingjia(e) {
        let id = app.getMyItem(e, 'id')
        wx.setStorageSync('gongdan_id', id)
        wx.navigateTo({
            url: '../index/pingjia/pingjia',
        })
    },

    // 跳转评价
    go_guanai(e) {
        let id = app.getMyItem(e, 'id')
        wx.setStorageSync('gongdan_id', id)
        wx.navigateTo({
            url: './diaocha/diaocha',
        })
    },

    // 跳转-照护详情
    go_detail(e) {
        let item = app.getMyItem(e, 'id')
        if (item.status == 3) {
            wx.navigateTo({
                url: './zhfa/zhfa?id=' + item.id,
            })
        } else {
            wx.navigateTo({
                url: './jlxx/jlxx?id=' + item.id,
            })
        }
    },

    // 进入工单详情页面
    go_order(e){
        let item = app.getMyItem(e, 'id')
        wx.navigateTo({
            url: './jlxx/jlxx?id=' + item.id,
        })
    },

    async get_user_desc() {
        wx.showLoading({
            title: '请求中...',
        })
        let user_info = await app.get_user_info()
        if (user_info.age > 0) {
            this.setData({
                user_info
            })
        } else {
            // wx.navigateTo({
            //     url: './hzxx/hzxx',
            // }) 
            wx.reLaunch({
                url: './hzxx/hzxx',
            }) 
        }

        wx.hideLoading()
    },

    // 跳转患者信息
    go_hzxx(e) {
        let type = app.getMyItem(e, 'type') || 0
        wx.navigateTo({
            url: './hzxx/hzxx?is_show_info=1&type=' + type,
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

        if (typeof this.getTabBar === 'function' &&
            this.getTabBar()) {
            this.getTabBar().setData({
                selected: 2,
                show:true
            })
        }

        // 获取用户信息
        this.get_user_desc()
        this.get_history_list()
    },


    /**
     * 页面相关事件处理函数--监听用户下拉动作
     */
    onPullDownRefresh() {
        this.get_history_list(true)
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