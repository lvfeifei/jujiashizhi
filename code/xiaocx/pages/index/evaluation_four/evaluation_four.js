// pages/index/article_detail/article_detail.js
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
    question: wx.getStorageSync('evaluation_four_data') || []
  },

  // 设置其它选项的内容
  set_auther_content(e){
    let {question} = this.data
    let auther_id = app.getMyItem(e,'auther_id')
    let value = app.getMyValue(e)

    question.forEach(i => {    
      if(i.options[0] == auther_id){  
        i.option_content = value 
      }  
    });
    this.setData({
      question
    }) 
  },

  // 选项点击
  click_item(e){   
    let {question} = this.data
    let item = app.getMyItem(e,'item')  
    let pid = app.getMyItem(e,'pid')  
    let classify_id = app.getMyItem(e,'classify_id')   
    console.log(classify_id)
    if(question.length){   
      let flag = false
      question.forEach(i => {     
        if(i.name != '其它'){
          i.option_content = '' 
        } 
        if(i.id == item.capability_id){ 
          flag = true 
          i.options = [item.id]   
        }  
      });

      if(!flag){
        let  itemArr = {}
        itemArr.id = pid
        itemArr.evaluation_class_id = classify_id
        itemArr.option_content = ''
        itemArr.type = item.type
        itemArr.options = []
        itemArr.options.push(item.id)
        question.push(itemArr) 
      } 
     
    }else{ 
      let itemArr = {}
      itemArr.id = pid
      itemArr.evaluation_class_id = classify_id
      itemArr.option_content = ''
      itemArr.type = item.type
      itemArr.options = []
      itemArr.options.push(item.id)
      question.push(itemArr) 
    }
 
    let new_question = question.filter(i => i.options.length) 
    this.setData({
      question:new_question
    })
   
  },

  // 返回上一页
  go_back(){
    wx.setStorageSync('evaluation_four_data', this.data.question || [])
    wx.navigateBack({
      delta: 1,
    })
  },

  // 跳转页面
  go_next(){  
    wx.requestSubscribeMessage({
      tmplIds:['cHvEcSyo84oy7o1iW60Ij09EfELCgBoLSIb6UvNDsLc', 'adOB9ynl2QxVOrdHKi5Esqdi5mchfH312Jt3VEoI5vs'],
      complete:()=>{
        let {question,question_list} = this.data   
        wx.showLoading({
            title: '请求中...',
            mask: true
        })
        // 判断是否存在没选的 选项
        if(!question.length || question.length < question_list.length){
          return app.toast_none('请完善测评问题');
        }  
        wx.setStorageSync('evaluation_four_data',question)
        let evaluation_one_data = wx.getStorageSync('evaluation_one_data')
        let evaluation_two_data = wx.getStorageSync('evaluation_two_data')
        let evaluation_three_data = wx.getStorageSync('evaluation_three_data') 
        let evaluation_four_data = wx.getStorageSync('evaluation_four_data') 

        let all_data = [
          ...evaluation_one_data,
          ...evaluation_two_data,
          ...evaluation_three_data,
          ...evaluation_four_data
        ] 
           // console.log(all_data) 
     
      app.post_ajax('/order/ordercreate',{question:all_data}, function(res, code){ 
        // console.log(res)
        let {data,msg,status} = res 
        wx.hideLoading(); 
          if (status == 1) {
 
            // 清空缓存
            wx.setStorageSync('evaluation_one_data',[])
            wx.setStorageSync('evaluation_two_data',[])
            wx.setStorageSync('evaluation_three_data',[])
            wx.setStorageSync('evaluation_four_data',[])

            // 1是需要专家审核 2是直接发
            wx.setStorageSync('zhuajia_img',data.expert_avatar )
            wx.setStorageSync('sendtime',data.sendtime )
            wx.setStorageSync('ceping_id',data.id)
            if(data.careplan == 1){
               // 需要等待
            //   wx.navigateTo({
            //     url: '../evaluation_wait/evaluation_wait',
            //   })
            if (data.sendtime == null) {
              wx.reLaunch({
                url: '../evaluation_wait_expert/evaluation_wait_expert',
              })
            } else {
              wx.reLaunch({
                url: '../evaluation_wait/evaluation_wait',
              })
            }
              
            }else{
              // 不需要等待
            //   wx.navigateTo({
            //     url: '../evaluation_success/evaluation_success',
            //   }) 
              wx.reLaunch({
                url: '../evaluation_success/evaluation_success',
              })
            } 
          }else {
            app.toast_none(msg);
          } 
      })  
      }
    } 
    ) 
   
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
      title: '请求中...',
    }) 
      let that = this;   
      let params = {  
        evaluation_class_id:4
      }

      app.post_ajax('/evaluation_capability/question',params, function(res, code){ 
        console.log(res)
        let {data,msg,status} = res
          wx.hideLoading(); 
          if (status == 1) {
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
    this.setData({
        question: wx.getStorageSync('evaluation_four_data') || []
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