const app = getApp();
Component({
    data: {
        show: false,
        is_show_tip: false,
        selected: 0,
        color: "#000",
        selectedColor: "#DF105E",
        list: [
            {
                pagePath: "/pages/index/index",
                text: "首页",
                iconPath: "/static/tabbar/home.png",
                selectedIconPath: "/static/tabbar/home-on.png"
            },
            {
                pagePath: "/pages/result/result",
                text: "专家互动",
                iconPath: "/static/tabbar/result.png",
                selectedIconPath: "/static/tabbar/result-on.png"
            },
            {
                pagePath: "/pages/my/my",
                text: "我的记录",
                iconPath: "/static/tabbar/mine.png",
                selectedIconPath: "/static/tabbar/mine-on.png"
            }
        ]
    },
    attached() {
        let that = this
        // 定时器发送请求 判断是否有消息回复
        //   app.count_unread_timer = setInterval(function(){
        //       app.post_ajax('/user_chat/count_unread','', function(res, code){   
        //         that.setData({
        //           is_show_tip:res > 0 ? true : false  
        //         }) 
        //       })      
        //   },5000)

        setInterval(function () {
            that.setData({
                is_show_tip: app.is_show_tip
            })
        }, 2000)
    },
    methods: {
        switchTab(e) {
            const data = e.currentTarget.dataset
            const url = data.path
            wx.switchTab({ url })
            this.setData({
                selected: data.index
            })
        }
    }
})