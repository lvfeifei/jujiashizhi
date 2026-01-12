// pages/index/article_detail/article_detail.js
Page({

  /**
   * 页面的初始数据
   */
  data: {
    fixs:0,
    top: 0,
    nav_height: 0,
  },

  go_next(){
    wx.navigateTo({
      url: '../pingjia_one/pingjia_one',
    })
  },
  
  // 返回上一页
  go_back(){
    wx.navigateBack({
      delta: 1,
      fail:()=>{ 
        wx.reLaunch({
          url: '/pages/my/my',
        })
    }
    })
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad(options) { 
    if(options.id){
        wx.setStorageSync('gongdan_id',options.id) 
    }
    
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