const app = getApp();
Page({

  /**
   * 页面的初始数据
   */
  data: {
    authorization:0, // 是否授权
    fixs:0,
    top: 0,
    nav_height: 0,
    hz_sex: '',  // 患者性别 1 男 2 女  患者L患者性别：[L1:男] [L2:女]
    hz_age:'', // 患者年龄
    hz_xueli:[ 
      {
        id:'N1',
        zhz_id:'U1',
        name:'未上过学/不识字',
      },
      {
        id:'N2',
        zhz_id:'U2',
        name:'小学',
      },
      {
        id:'N3',
        zhz_id:'U3',
        name: '初中', 
      },
      {
        id:'N4',
        zhz_id:'U4',
        name:'高中/中专'
      },
      {
        id:'N5',
        zhz_id:'U5',
        name:'本科(大专)及以上'
      }
    
      ], // 患者N教育程度 [N1:未上过学/不识字][N2:小学][N3:初中][N4:高中/中专][N5:本科及以上]
    hz_xueli_index:999,
    hz_jibing:[
      {
        id:'O1',
        name:'阿尔茨海默病',
      },
       {
        id:'O2',
        name:'血管性痴呆',
      } ,
      {
       id:'O3',
       name:  '混合性痴呆',
     }  ,
     {
        id:'O4',
        name:  '其他'
      } 
       
    ], // 患者O患者疾病类型：[O1:阿尔茨海默病][O2:血管性痴呆][O3:混合性痴呆][O4:其他]
    hz_jibing_index: 999,
    hz_yzcd:'' ,// 严重程度 1 轻度 2 中度 3 重度  // 患者P患者病情严重程度：[P1:轻度] [P2:中度] [P3:重度]
    hz_hobby_list:[
      { id:'Q1',   name:'无'  }, { id:'Q2',    name:'唱歌/唱戏/听音乐/听戏/演奏乐器' },
      {  id:'Q3', name:'跳舞/健美操/八段锦/打太极/练气功' }, { id:'Q4', name:'散步/慢跑/爬山/打球/游泳/旅游功' },
      {  id:'Q5',   name:'绘画/书法/写作/阅读'   },{  id:'Q6', name:'养花草植物' },
      {id:'Q7', name:'养宠物' } 
    ],   // 爱好列表  患者Q患者确诊前的兴趣爱好（可多选）：[Q1:无][Q2:唱歌/唱戏/听音乐/听戏/演奏乐器][Q3:跳舞/健美操/八段锦/打太极/练气功][Q4:散步/慢跑/爬山/打球/游泳/旅游][Q5:绘画/书法/写作/阅读][Q6:养花草植物][Q7:养宠物][Q8:其他（请列出________）]
    hz_hobby:[], // 患者爱好 多选
    hz_auther_hobby:'',// 患者其它爱好, 患者确诊前的兴趣爱好如果选这Q8 自定义兴趣爱好
    hz_xznl:'', // 患者行走能力    // 	 患者R患者行走能力：[R1:可以正常行走][R2:自行使用拐杖、助步器、轮椅][R3:使用轮椅且需帮助][R4:完全卧床] 
    zhz_sex: '', // 照护者性别   照护者S性别：[S1:男] [S2:女]
    zhz_age:'', // 照护者年纪
    zhz_xueli_index:999, // 照护者学历 照护者U教育程度 [U1:未上过学/不识字][U2:小学][U3:初中][U4:高中/中专][U5:本科及以上]
    zhz_year:'',  // 照护者年限 照护者V照护年限：[V1<1年][V2:1-2年][V3:2–4年][V4:>4年]
    zhz_gx:'',     // 照护者关系 照护者W与患者关系：[W1:配偶][W2:子女][W3:媳婿][W4:其他]
    zhz_room:'', // 照护者在否与患者同住 照护者X与患者同住：[X1:是][X2:否]'
    canIUseGetUserProfile:false,        // 获取用户信息
    is_show_info:0  // 是否展示 修改内容
  }, 

  // 保存表单内容
  save_form_content(){
    let {
      hz_sex, hz_age,hz_xueli_index,hz_jibing_index,hz_yzcd,hz_hobby,hz_auther_hobby,hz_xznl,hz_xueli,hz_jibing,
      zhz_sex,zhz_age,zhz_xueli_index,zhz_year,zhz_gx,zhz_room
     } = this.data

   if(!hz_sex){ 
    return app.toast_none('请选择患者性别');
   }

   if(!hz_age){ 
    return app.toast_none('请输入患者年龄');
   }

   if(hz_age > 150 || hz_age < 0 ){ 
    return app.toast_none('请输入正确的患者年龄');
   }

   if(hz_xueli_index == 999){
    return app.toast_none('请选择患者教育程度');
   }

  if(!hz_jibing_index == 999){
    return app.toast_none('请选择患者疾病类型');
   }

   if(!hz_yzcd){ 
    return app.toast_none('请选择患者严重程度');
   }

   if(hz_hobby.length == 0  && !hz_auther_hobby){
    return app.toast_none('请选择患者兴趣爱好');
   }
  
  if(!hz_xznl){ 
    return app.toast_none('请选择患者行走能力');
  }
 
  if(!zhz_sex){ 
    return app.toast_none('请选择照护者性别');
   }

   if(!zhz_age){ 
    return app.toast_none('请输入照护者年龄');
   }

   if(zhz_age > 99 ){ 
    return app.toast_none('请输入正确的照护者年龄');
   }

   if(zhz_xueli_index == 999 ){
    return app.toast_none('请选择照护教育程度');
   }

   if(!zhz_year){
    return app.toast_none('请选择照护年限');
   }

   if(!zhz_gx){
    return app.toast_none('请选择照护者关系');
   }

   if(!zhz_room){
    return app.toast_none('请选择是否患者同住');
   }

   let hz_xueli_text = hz_xueli[hz_xueli_index].id
   let hz_jibing_text = hz_jibing[hz_jibing_index].id
   let zhz_xueli_text = hz_xueli[zhz_xueli_index].zhz_id
 
    console.log('患者信息',hz_sex, hz_age,hz_xueli_text,hz_jibing_text,hz_yzcd,hz_hobby,hz_auther_hobby,hz_xznl)
    console.log('照护者信息', zhz_sex,zhz_age,zhz_xueli_text,zhz_year,zhz_gx,zhz_room)
  
    if(hz_auther_hobby && !hz_hobby.includes('Q8')){
      hz_hobby.push('Q8')
    }
    let params = { 
      gender: zhz_sex,
      age:zhz_age,
      education:zhz_xueli_text,
      care_years:zhz_year,
      relation:zhz_gx,
      live:zhz_room, 
      patient_gender:hz_sex,
      patient_age:hz_age, 
      patient_education:hz_xueli_text,
      patient_disease_type:hz_jibing_text,
      patient_illness:hz_yzcd,   
      // patient_hobby:hz_hobby.join(','),
      patient_hobby: hz_hobby,
      patient_walk:hz_xznl,
      patient_hobby_content:hz_auther_hobby,
    }

    app.post_ajax('/user/userSave',params, function(res, code){
      console.log(res) 
      let {msg,status} = res
        wx.hideLoading(); 
        if (status == 1) { 
            app.set_time_out(msg, function () {
              wx.switchTab({
                url: '../../my/my',
              })
            }); 
        }else {
          app.toast_none(msg);
        }
    })  

  },

  // 患者疾病切换 
  bind_hz_jibing_change(e){ 
    let hz_jibing_index = app.getMyValue(e)   
    this.setData({
      hz_jibing_index
    })
  },

  // 患者学历切换 
  bind_hz_xueli_change(e){  
    let hz_xueli_index = app.getMyValue(e)   
    this.setData({
      hz_xueli_index
    })
  },

  // 照护者学历切换 
  bind_zhz_xueli_change(e){  
    let zhz_xueli_index = app.getMyValue(e)  
    console.log(zhz_xueli_index)
    this.setData({
      zhz_xueli_index
    })
  },

  // 爱好点击
  hoddy_click(e){
    let {hz_hobby} = this.data
    let item = app.getMyItem(e,'item')    
  
    if(hz_hobby.indexOf(item) == -1){
      if(hz_hobby.length && hz_hobby.includes('Q1')){
        hz_hobby.splice(hz_hobby.indexOf('Q1'),1)
      }  
     
      if(item == 'Q1'){
        hz_hobby = []
        hz_hobby.push(item) 
      }else{
        hz_hobby.push(item) 
      }
       
    }else{
      hz_hobby.splice(hz_hobby.indexOf(item),1)
    } 
 
    this.setData({
      hz_hobby
    }) 
  },

  // 切换照护者性别
  change_zhz_sex(e){
    let zhz_sex = app.getMyItem(e,'zhz_sex')   
    this.setData({
      zhz_sex
    })   
  },

  //   切换照护者 是否同住
  change_zhz_room(e){
    let zhz_room = app.getMyItem(e,'room')   
    this.setData({
      zhz_room
    })   
  },
  // 切换照护者年限
  change_zhz_year(e){
    let zhz_year = app.getMyItem(e,'year')   
    this.setData({
      zhz_year
    })  
  },

  // 切换照护者关系
  change_zhz_gx(e){
    let zhz_gx = app.getMyItem(e,'gx')   
    this.setData({
      zhz_gx
    })  
  },
  // 切换患者性别
  change_hz_sex(e){
    let hz_sex = app.getMyItem(e,'hz_sex')   
    this.setData({
      hz_sex
    })   
  },

  // 切换患者行走能力
  change_hz_xznl(e){
    let hz_xznl = app.getMyItem(e,'hz_xznl')   
    this.setData({
      hz_xznl
    }) 
  },

  // 切换 病情严重程度
  change_hz_yzcd(e){
    let hz_yzcd = app.getMyItem(e,'hz_yzcd')   
    this.setData({
      hz_yzcd
    })  
  },

  // 患者年龄
  change_hz_age(e){ 
    let hz_age = app.getMyValue(e)   
    this.setData({
      hz_age
    })   
  },

  // 照护者年纪
  change_zhz_age(e){ 
    let zhz_age = app.getMyValue(e)   
    this.setData({
      zhz_age
    })   
  },

  // 患者其它爱好
  change_hz_auther_hobby(e){
    let hz_auther_hobby = app.getMyValue(e)   
    this.setData({
      hz_auther_hobby
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

  // 返回上一页
  go_back(){
    let {is_show_info} = this.data
    let url = is_show_info ? '../../my/my' :  '../../index/index'; 
     wx.switchTab({
      url
    })
  },
  /**
   * 生命周期函数--监听页面加载
   */
  onLoad(options) {
    let {is_show_info,type} = options

    if(type == 2) {
      console.log(2)
      wx.createSelectorQuery().select('.zhz_message').boundingClientRect(res => {
        // 2.使用wx.getSysTemInfo()获取设备及页面高度windowHeight（px）
           wx.getSystemInfo({
               success(ress) {
                console.log('res.top',res.top)
                console.log('ress.windowHeight/2',ress.windowHeight/2) 
                 wx.pageScrollTo({ 
                   // 3. 滚动的距离根据设备的页面高度进行微调（px）
                   scrollTop: res.top - ress.windowHeight/2 + 260, 
                   duration: 0
                 })
               }
         })
      }).exec() 
    }

    if(is_show_info){
      this.setData({
        is_show_info
      })
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

    if (wx.getUserProfile) {
      this.setData({
          canIUseGetUserProfile: true
      })
  } 
    // 获取用户信息
    this.get_user_desc()
  },
 
  async get_user_desc(){
    let {hz_xueli,hz_jibing,is_show_info} = this.data
     let user_info = await app.get_user_info()

     if(is_show_info == 1){
      this.setData({
        authorization:user_info.authorization,
        zhz_sex:user_info.gender,
        zhz_age:user_info.age || '' , 
        zhz_year:user_info.care_years,
        zhz_gx:user_info.relation,
        zhz_room:user_info.live,
        hz_sex:user_info.patient_gender,
        hz_age:user_info.patient_age || '' , 
        hz_xueli_index:this.get_index(hz_xueli,user_info.patient_education,1),
        hz_jibing_index:this.get_index(hz_jibing,user_info.patient_disease_type,1),
        zhz_xueli_index:this.get_index(hz_xueli,user_info.education,2), 
        hz_yzcd:user_info.patient_illness,
        hz_hobby:user_info.patient_hobby || [],
        hz_xznl:user_info.patient_walk,
        hz_auther_hobby:user_info.patient_hobby_content,
       }) 
     }  else{
      this.setData({
        authorization:user_info.authorization,
      })
     }
  
     
  },

  // 查询索引
  get_index(arr,key,type){ 
    let index = ''
    arr.forEach((item,idx) => { 
      if(type == 1){
        if(item.id == key ){
          index = idx
         } 
      }else if(type == 2){
        if(item.zhz_id == key ){
          index = idx
         } 
      }
       
    }); 
    return index
  },

  /**
     * 弹窗登陆
     */
    login: function (e) {
      var that = this;
      app.set_user_info(e.detail.userInfo);
      if (e.detail.errMsg == "getUserInfo:ok") { 
          that.get_user_desc(); 
      }
  },

  
  /**
   * 新授权接口
   * @param {*} e 
   */
  new_login: function (e) {
    var that = this;
    wx.showLoading({
      title: '请求中...',
      mask: true
  })
  
    wx.getUserProfile({
        desc: '用于完善会员资料', // 声明获取用户个人信息后的用途，后续会展示在弹窗中，请谨慎填写
        success: (res) => { 
            app.set_user_info(res.userInfo);  
            that.setData({
              authorization:1
            }) 
            that.save_form_content() 
        },
        complete:()=>{
          wx.hideLoading(); 
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