const app = getApp();
Page({

  /**
   * 页面的初始数据
   */
  data: {
    fixs:0,
    top: 0,
    nav_height: 0,
    question_list:[],
    question:[]
  },

  
  // 选项点击
  click_item(e){   
    let {question} = this.data
    let item = app.getMyItem(e,'item') 
    let options = app.getMyItem(e,'options')  
    let   question_id = item.id ,sn= item.sn ,option_text  = options,option = 0  
    for(let i in item.options){
       if(options == item.options[i]){
        option = i 
       } 
     }  
 
    if(question.length){   
      let flag = false
      question.forEach(i => {     
        if(i.id == question_id){ 
          flag = true 
          i.option = option
          i.option_text = option_text
        }  
      });

      if(!flag){
        let  itemArr = {}
        itemArr.id = question_id 
        itemArr.sn = sn 
        itemArr.option =option
        itemArr.option_text =option_text 
        question.push(itemArr) 
      } 
     
    }else{ 
      let  itemArr = {}
      itemArr.id = question_id 
      itemArr.sn = sn 
      itemArr.option =option
      itemArr.option_text =option_text 
      question.push(itemArr) 
    }
 
    this.setData({
      question
    })
   
  },
   // 返回上一页
   go_back(){
    wx.navigateBack({
      delta: 1,
    })
  },

  go_next(){  
    let {question} = this.data 
    if(!question.length){
      return app.toast_none('请选择问题答案')
    } 
    wx.showLoading({
      title: '请求中...',
      mask: true
  })
    app.post_ajax('/user/evaluate_save',{
      id:wx.getStorageSync('gongdan_id'),
      program:question
    }, function(res, code){  
      let {msg,status} = res  
      wx.hideLoading(); 
        if (status == 1) { 
            wx.reLaunch({
                url: '../pingjia_success/pingjia_success',
            }) 
        }else{
          app.toast_none(msg);
        }   
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

  // 获取列表
  this.get_list()
},


// 获取列表
get_list(){ 
wx.showLoading({
  title: '加载中...',
}) 
  let that = this;     
  app.post_ajax('/evaluate/index',{
    id:wx.getStorageSync('gongdan_id')
  }, function(res, code){  
    let {data,msg,status} = res
      wx.hideLoading(); 
      if (data.length) {
          that.setData({ 
            question_list:data
          })
      }else {
         app.toast_none(msg);
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