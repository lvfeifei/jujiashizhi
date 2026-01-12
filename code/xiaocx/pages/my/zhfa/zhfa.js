const app = getApp();
Page({

  /**
   * 页面的初始数据
   */
  data: {
    fixs:0,
    top: 0,
    nav_height: 0,
    id:'',
    user_data:[],
  
  },
  //  返回上一页
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
   // 跳转照护详情
   go_detail(e){
    // let id = app.getMyItem(e,'id')  
    let program_class = app.getMyItem(e,'program_class') 
    this.setData({
      program_class
    })
    wx.navigateTo({
      url: '../zhaohujianyi/zhaohujianyi?id='+ this.data.id + '&program_class='+program_class,
    }) 
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad(options) {
 
    let {id} = options  
    this.setData({
      id
    })
    this.get_detail(id);
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

  // 获取详情
  get_detail(id){ 
    wx.showLoading({
      title: '请求中...',
      mask: true
  })
    let that = this
    app.post_ajax('/order/patient_program',{id}, function(res, code){  
      let {data,status,msg} = res    
        if (status == 1) {  
          that.setData({
            user_data:data
          })
        }else{
            app.toast_none(msg)
        }  
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