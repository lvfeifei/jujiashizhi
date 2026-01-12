const app = getApp();
Page({

  /**
   * 页面的初始数据
   */
  data: {
    fixs:0,
    top: 0,
    nav_height: 0,
    zhuajia_img: '',
    sendtime:'',
  },

  // 获取历史记录列表
  async get_history_list(){
    let history_list = await app.get_user_history() 
    if(history_list.length){
      wx.switchTab({
        url: '../../my/my',
      })
    }else{
      app.toast_none('暂无记录')
    }
     
  },

  go_index(){
    wx.switchTab({
      url: '../../index/index',
    })
  },
  /**
   * 生命周期函数--监听页面加载
   */
  onLoad(options) {
    // 获取专家信息
    this.get_zj_message()
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

    // 获取专家信息
    get_zj_message(){
      wx.showLoading({
        title: '请求中...',
        mask: true
    })
    
      let that = this
      app.get_ajax('/user_chat/expert_default','', function(res, code){  
        that.setData({
          zhuajia_img:res.avatar_url,
          sendtime:res.sendtime
        })
        wx.hideLoading(); 
      })
    },

  // 监听页面
 onPageScroll(e) {
  // console.log(e.scrollTop)
  if(e.scrollTop > 10){
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