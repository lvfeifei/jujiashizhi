const app = getApp();
Page({

  /**
   * 页面的初始数据
   */
  data: {
    fixs:0,
    top: 0,
    nav_height: 0,
  },
 
  go_back(){
    wx.navigateBack({
      delta: 1,
    })
  },
  /**
   * 新授权接口
   * @param {*} e 
   */
  new_login: function (e) { 
    wx.getUserProfile({
        desc: '用于完善会员资料', // 声明获取用户个人信息后的用途，后续会展示在弹窗中，请谨慎填写
        success: (res) => {
          console.log(res)
            app.set_user_info(res.userInfo); 
            wx.navigateBack({
              delta: 1,
            })
        }
    })
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