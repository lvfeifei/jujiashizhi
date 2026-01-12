const app = getApp();
Page({

    /**
     * 页面的初始数据
     */
    data: {
       id:0,
       logo:'',
       title:'',
        fixs: 0,
        top: 0,
        nav_height: 0,
        user_info: {},  // 用户㤈
        history_list: [], // 历史照护记录列表
        type:0,   // 判断是否显示取消绑定
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

       this.setData({ 
          id: decodeURIComponent(options.scene).split('=')[1]  
        }) 
        this.detail(); 
    },
 
    // 跳转评价
    pre() { 
        wx.reLaunch({ 
          url: '/pages/index/index',
        }) 
    },
 
    bind_click(){
      wx.showLoading({
        title: '请求中...',
    }) 
    app.post_ajax('/user/bind_bead_house', {
      id:this.data.id
    },  (res, code)=> {
      wx.hideLoading() 
      app.toast_none(res.msg)
      if (res.status == 1) { 
        setTimeout(()=>{
          this.pre()
        },1500) 
      }  
  })
    },

    remove_bind_click(){
        wx.showLoading({
            title: '请求中...',
        }) 
        app.post_ajax('/user/unbind_bead_house', {
          id:this.data.id
        },  (res, code)=> {
          wx.hideLoading() 
          app.toast_none(res.msg)
          if (res.status == 1) { 
            setTimeout(()=>{
              this.pre()
            },1500) 
          } 
        }) 
    }, 

    detail() {
        wx.showLoading({
            title: '请求中...',
        }) 
        app.post_ajax('/bead_house/bead_house_info', {
          id:this.data.id
        },  (res, code)=> {
          wx.hideLoading() 
          if (res.status == 1) {
              this.setData({
                logo: res.data.logo,
                title: res.data.title,
                type:res.data.type 
              })
          } else {
              app.toast_none(msg)
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
        
        if (typeof this.getTabBar === 'function' &&
            this.getTabBar()) {
            this.getTabBar().setData({
                selected: 2,
                show:true
            })
        }
 
    },


    /**
     * 页面相关事件处理函数--监听用户下拉动作
     */
  
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